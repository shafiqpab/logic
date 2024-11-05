<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = $_SESSION['logic_erp']['supplier_id'];
$user_comp_location_ids = $_SESSION['logic_erp']['company_location_id'];
//--------------------------------------------------------------------------------------------

// Linking selected report buttons with this page
if($action=="company_wise_report_button_setting"){
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=37 and is_deleted=0 and status_active=1");


	$print_report_format_arr=explode(",",$print_report_format);



	echo "$('#Printt1').hide();\n";
	echo "$('#print_vat1').hide();\n";
	echo "$('#print_vat2').hide();\n";
	echo "$('#print_vat3').hide();\n";
	echo "$('#print_vat4').hide();\n";
	echo "$('#search1').hide();\n";
	echo "$('#without_prog1').hide();\n";
	echo "$('#print_vat8').hide();\n";
	echo "$('#print_vat9').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==109){echo "$('#Printt1').show();\n";}
			if($id==110){echo "$('#print_vat1').show();\n";}
			if($id==111){echo "$('#print_vat2').show();\n";}
			if($id==112){echo "$('#print_vat3').show();\n";}
			if($id==89){echo "$('#print_vat4').show();\n";}
			if($id==113){echo "$('#search1').show();\n";}
			if($id==114){echo "$('#without_prog1').show();\n";}
			if($id==172){echo "$('#print_vat8').show();\n";}
			if($id==129){echo "$('#print_vat9').show();\n";}
			if($id==161){
				echo "$('#print_6').show();\n";
				echo "$('#organic_check').show();\n";
			}
		}
	}
	else
	{
		echo "$('#Printt1').show();\n";
		echo "$('#print_vat1').show();\n";
		echo "$('#print_vat2').show();\n";
		echo "$('#print_vat3').show();\n";
		echo "$('#print_vat4').show();\n";
		echo "$('#search1').show();\n";
		echo "$('#without_prog1').show();\n";
		echo "$('#print_vat8').show();\n";
		echo "$('#print_vat9').show();\n";
	}

	$yarn_rate_match_sql=sql_select("select during_issue from variable_settings_inventory where variable_list=25 and company_name=$data and status_active=1 and is_deleted=0");
	echo "$('#yarn_rate_match').val(".$yarn_rate_match_sql[0][csf("during_issue")].");\n";

	exit();
}

//load drop down supplier
if ($action == "load_drop_down_supplier") {
	if($user_supplier_ids) $user_supplier_cond = " and c.id in ($user_supplier_ids)";else $user_supplier_cond = "";
	echo create_drop_down("cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' $user_supplier_cond and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}
if ($action == "load_drop_down_supplier_loan") {
	echo create_drop_down("cbo_loan_party", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=91 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_purpose") {
	$data=explode("_",$data);
	if($data[0]==1){
		echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "load_supplier();active_inactive();change_basis(this.value)", "", "1,2,4,7,8,15,16,38,46,3,5,6,12,26,29,30,39,40,45","","","");
	}else{
		if($data[0]==2){
			echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "load_supplier();active_inactive();change_basis(this.value)", "", "3,5,6,7,12,26,29,30,39,40,45","","","");
		}else{
			echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "load_supplier();active_inactive();change_basis(this.value)", "", "1,2,4,7,8,15,16,38,46","","","");
		}
	}
	exit();
}

//load drop down company location
if ($action == "load_drop_down_location") {
	$dataArr = explode("_", $data);
	$company_id=$dataArr[0];
	$knitting_source=$dataArr[1];
	if($knitting_source==1)
	{
		if($user_comp_location_ids) $user_comp_location_cond = " and id in ($user_comp_location_ids)"; else $user_comp_location_cond = "";

		echo create_drop_down("cbo_location_id", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$company_id' $user_comp_location_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "", 0);
	}
	else
	{
		echo create_drop_down("cbo_location_id", 170, $blank_array, "", 1, "-- Select Location --", $selected, "", 0);
	}
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/yarn_issue_store_update_controller",$data);
}
//load drop down company Store location
/*if ($action == "load_drop_down_store") {
	if($user_store_ids) $user_store_cond = " and a.id in ($user_store_ids)"; else $user_store_cond = "";
	echo create_drop_down("cbo_store_name", 142, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' $user_store_cond and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "-- Select Store --", $selected, "fn_empty_lot(this.value);", 0);
	exit();
}
*/
//load drop down based on reqsition
if ($action == "load_drop_down_store_req") {
	echo create_drop_down("cbo_store_name", 142, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.id in($data) and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "-- Select Store --", $selected, "fn_empty_lot(this.value);", 0);
	exit();
}

if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	if ($data[1] == 1) $party = "1,3,21,90,30"; else $party = "80";

	echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", $data[1]);
	exit();

}

if ($action == "load_drop_down_dyeing_color") {
	$data = explode("_", $data);
	$booking_id = $data[0];
	$color_id = $data[1];

	echo create_drop_down("cbo_dyeing_color", 142, "select a.id, a.color_name from lib_color a, wo_yarn_dyeing_dtls b where a.id=b.yarn_color and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and b.mst_id='$booking_id' group by a.id, a.color_name order by a.color_name", "id,color_name", 1, "-- Select --", $selected, "", $color_id);
	exit();
}

//load_drop_down_purpose
/*if ($action == "load_drop_down_purpose") {
	if ($data == 1) {
		echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "active_inactive(this.value)", "", "1,4");
	} else if ($data == 2) {
		echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "active_inactive(this.value)", "", "2,5,6,7");
	} else {
		echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "active_inactive(this.value)", "", "");
	}
	exit();
}*/

//load drop down knitting company
if ($action == "load_drop_down_knit_com") {
	$exDataArr = explode("**", $data);
	$knit_source = $exDataArr[0];
	$company = $exDataArr[1];
	$issuePurpose = $exDataArr[2];
	if ($company == "" || $company == 0) $company_cod = ""; else $company_cod = " and id=$company";

	if ($knit_source == 1)
		echo create_drop_down("cbo_knitting_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select --", "", "load_drop_down( 'requires/yarn_issue_store_update_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );");
	else if ($knit_source == 3 && $issuePurpose == 1)
		echo create_drop_down("cbo_knitting_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_issue_store_update_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );", 0);
	else if ($knit_source == 3 && $issuePurpose == 2)
		echo create_drop_down("cbo_knitting_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_issue_store_update_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );", 0);
	else if ($knit_source == 3)
		echo create_drop_down("cbo_knitting_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_issue_store_update_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );", 0);
	else if ($knit_source == 0)
		echo create_drop_down("cbo_knitting_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	exit();
}

// wo/pi popup here----------------------//
if ($action == "fabbook_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>

		function fn_check() {
            /*if(form_validation('cbo_buyer_name','Buyer Name')==false )
             return;
             else*/
             	show_list_view(document.getElementById('cbo_buyer_name').value + '_' + document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' + '<? echo $company; ?>' +
             		'_' + '<? echo $issue_purpose; ?>' +
             		'_' + '<? echo $basis; ?>', 'create_fabbook_search_list_view', 'search_div', 'yarn_issue_store_update_controller', 'setFilterGrid(\'list_view\',-1)'
             		)
             ;
         }

         function js_set_value(booking_dtls) {
         	$("#hidden_booking_number").val(booking_dtls);
         	parent.emailwindow.hide();
         }
     </script>
 </head>
 <body>
 	<div align="center" style="width:100%;">
 		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
 			<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
 				<thead>
 					<tr>
 						<th>Buyer Name</th>
 						<th>Search By</th>
 						<th align="center" id="search_by_td_up">Enter WO/PI Number</th>
 						<th width="200">Date Range</th>
 						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
 							class="formbutton"/></th>
 						</tr>
 					</thead>
 					<tbody>
 						<tr class="general">
 							<td>
 								<?
 								if ($basis == 4) {
 									$search_by = array(1 => "Sales Order No", 2 => "Sales / Booking No", 3 => "Style Ref.");
 									$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../')";
 									$disable = 1;
 								} else {
 									$search_by = array(1 => 'Booking No', 2 => 'Buyer Order', 3 => 'Job No', 4 => "Internal Ref", 5 => "File No");
 									$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../')";
 									$disable = 0;
 								}

 								echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select --", $selected, "", $disable);
 								?>
 							</td>
 							<td>
 								<?
 								echo create_drop_down("cbo_search_by", 130, $search_by, "", 0, "--Select--", "", $dd, 0);
 								?>
 							</td>
 							<td width="180" align="center" id="search_by_td">
 								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
 								id="txt_search_common"/>
 							</td>

 							<td align="center">
 								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
 								placeholder="From Date"/>
 								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
 								placeholder="To Date"/>
 							</td>
 							<td align="center">
 								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check()"
 								style="width:100px;"/>
 							</td>
 						</tr>
 						<tr>
 							<td align="center" height="40" valign="middle" colspan="7">
 								<? echo load_month_buttons(1); ?>
 								<!-- Hidden field here -->
 								<input type="hidden" id="hidden_booking_id" value=""/>
 								<input type="hidden" id="hidden_booking_number" value=""/>
 								<!---END -->
 							</td>
 						</tr>
 					</tbody>
 				</tr>
 			</table>
 			<div align="center" valign="top" id="search_div"></div>
 		</form>
 	</div>
 </body>
 <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
}

if ($action == "create_fabbook_search_list_view") {
	$ex_data = explode("_", $data);
	$buyer = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$company = $ex_data[5];
	$booking_type = $ex_data[6];
	$basis = $ex_data[7];

	if ($basis == 4) {
		$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		$search_field_cond = '';
		if ($txt_search_common != "") {
			if ($txt_search_by == 1) {
				$search_field_cond = " and a.job_no like '%" . $txt_search_common . "'";
			} else if ($txt_search_by == 2) {
				$search_field_cond = " and a.sales_booking_no like '%" . $txt_search_common . "'";
			} else {
				$search_field_cond = " and a.style_ref_no like '" . $txt_search_common . "%'";
			}
		}

		if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
		else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
		else $year_field = "";

		$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.booking_type=4 and a.within_group=1 and a.company_id=$company $search_field_cond order by id";

		$result = sql_select($sql);
		?>
		<div style="margin-top:5px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="70">Job No.</th>
					<th width="60">Year</th>
					<th width="80">Within Group</th>
					<th width="140">Buyer</th>
					<th width="150">Sales/ Booking No</th>
					<th width="80">Booking date</th>
					<th width="140">Style Ref.</th>
					<th>Location</th>
				</thead>
			</table>
			<div style="width:920px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table"
				id="list_view">
				<?
				$i = 1;
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					if ($row[csf('within_group')] == 1)
						$buyer = $company_arr[$row[csf('buyer_id')]];
					else
						$buyer = $buyer_arr[$row[csf('buyer_id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('job_no')]; ?>_<? echo $row[csf('buyer_id')]; ?>_<? echo $buyer; ?>_<? echo $row[csf('style_ref_no')]; ?>');">
						<td width="40"><? echo $i; ?></td>
						<td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
						<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
						<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
						<td width="140"><p><? echo $buyer; ?>&nbsp;</p></td>
						<td width="150"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="140"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
	<?
} else {
	$sql_cond = "";
	if (trim($txt_search_common) != "") {
			if (trim($txt_search_by) == 1) // for Booking No
			{
				$sql_cond .= " and a.booking_no LIKE '%$txt_search_common'";
			} else if (trim($txt_search_by) == 2) // for buyer order
			{
				$sql_cond .= " and b.po_number LIKE '%$txt_search_common%'";    // wo_po_break_down
			} else if (trim($txt_search_by) == 3) // for job no
			{
				$sql_cond .= " and a.job_no LIKE '%$txt_search_common%'";
			} else if (trim($txt_search_by) == 4) // for Internal Ref no
			{
				$sql_cond = " and b.grouping LIKE '%$txt_search_common%'";
			} else if (trim($txt_search_by) == 5) // for File no
			{
				$sql_cond = " and b.file_no LIKE '%$txt_search_common%'";
			}
		}

		if ($txt_date_from != "" && $txt_date_to != "") {
			if ($db_type == 0) {
				$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
			}
		}

		if (trim($company) != 0) $sql_cond .= " and a.company_id='$company'";
		if (trim($buyer) != 0) $sql_cond .= " and a.buyer_id='$buyer'";
		if (trim($booking_type) == 1) $sql_cond .= " and a.booking_type!=4";
		else if (trim($booking_type) == 4) $sql_cond .= " and a.booking_type=4";

		if (trim($booking_type) == 8) {
			$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no as job_no_mst
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and a.fabric_source!=2 group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no";
		} else if (trim($booking_type) == 2 || trim($booking_type) == 15 || trim($booking_type) == 38 || trim($booking_type) == 46 || trim($booking_type) == 7) {
			$sql_cond = '';
			if (trim($txt_search_by) == 1) // for Booking No
			{
				$sql_cond .= " and a.ydw_no LIKE '%$txt_search_common'";
			} else if (trim($txt_search_by) == 3) // for job no
			{
				$sql_cond .= " and b.job_no LIKE '%$txt_search_common%'";
			}

			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if (trim($company) != 0) $sql_cond .= " and a.company_id='$company'";

			$job_arr = array();
			$job_data = sql_select("select a.id as job_id, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
			foreach ($job_data as $row) {
				$job_arr[$row[csf('job_id')]] .= $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('style_ref_no')] . "**" . $row[csf('buyer_name')] . ",";
			}

			if(trim($booking_type) == 2){
				$entry_form_cond = " and a.entry_form in(41,42,114,125,135)";
			}else{
				$entry_form_cond = "  and a.entry_form=94 and a.service_type=$booking_type";
			}

			if ($db_type == 0) {
				if (trim($buyer) == 0) {
					$sql = "select a.id, a.yarn_dyeing_prefix_num as booking_no_prefix_num, a.entry_form, a.ydw_no as booking_no, a.booking_date, a.delivery_date, 1 as item_category, group_concat(distinct(b.job_no)) as job_no_mst, group_concat(distinct(b.job_no_id)) as job_no_id
					from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
					where
					a.id=b.mst_id and
					a.status_active=1 and
					a.is_deleted=0 and
					b.status_active=1 and
					b.is_deleted=0
					$sql_cond $entry_form_cond
					group by a.id, a.entry_form order by a.entry_form";
				} else {
					$sql_cond .= " and c.buyer_name='$buyer'";
					$sql = "select a.id, a.yarn_dyeing_prefix_num as booking_no_prefix_num, a.entry_form, a.ydw_no as booking_no, a.booking_date, a.delivery_date, 1 as item_category, group_concat(distinct(c.job_no)) as job_no_mst, group_concat(distinct(c.id)) as job_no_id
					from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c
					where
					a.id=b.mst_id and
					b.job_no_id=c.id and
					a.status_active=1 and
					a.is_deleted=0 and
					b.status_active=1 and
					b.is_deleted=0
					and a.entry_form in(41,125)
					$sql_cond $entry_form_cond
					group by a.id";
				}
			} else {
				if (trim($buyer) == 0) {
					$sql = "select a.id, a.yarn_dyeing_prefix_num as booking_no_prefix_num, a.entry_form, a.ydw_no as booking_no, a.booking_date, a.delivery_date, 1 as item_category, LISTAGG(cast(b.job_no as varchar(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_mst, LISTAGG(b.job_no_id, ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id,a.supplier_id,a.source,a.service_type,a.pay_mode
					from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
					where
					a.id=b.mst_id and
					a.status_active=1 and
					a.is_deleted=0 and
					b.status_active=1 and
					b.is_deleted=0
					$sql_cond $entry_form_cond
					group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.entry_form, a.booking_date, a.delivery_date,a.supplier_id,a.source,a.service_type,a.pay_mode order by a.entry_form";
				} else {
					$sql_cond .= " and c.buyer_name='$buyer'";
					$sql = "select a.id, a.yarn_dyeing_prefix_num as booking_no_prefix_num, a.entry_form, a.ydw_no as booking_no, a.booking_date, a.delivery_date, 1 as item_category, max(c.buyer_name) as buyer_id, LISTAGG(cast(b.job_no as varchar(4000)), ',') WITHIN GROUP (ORDER BY c.job_no) as job_no_mst, LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as job_no_id,a.supplier_id,a.source,a.service_type,a.pay_mode
					from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c
					where
					a.id=b.mst_id and
					b.job_no_id=c.id and
					a.status_active=1 and
					a.is_deleted=0 and
					b.status_active=1 and
					b.is_deleted=0
					and a.entry_form in(41,125)
					$sql_cond $entry_form_cond
					group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.entry_form, a.booking_date, a.delivery_date,a.supplier_id,a.source,a.service_type,a.pay_mode";
				}
			}
		} else {
			// check variable settings if allocation is available or not
			$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company and variable_list=18 and item_category_id = 1");
			$po_arr = array();
			$po_data = sql_select("select a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.file_no, b.grouping, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
			foreach ($po_data as $row) {
				$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('style_ref_no')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
			}

			$po_arr = array();
			$po_data = sql_select("select a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.file_no, b.grouping, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
			foreach ($po_data as $row) {
				$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('style_ref_no')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
			}
			$sql_cond .= ($variable_set_allocation==1)?" and a.booking_type not in(1,4)":"";
			$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst
			from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b
			where
			a.booking_no=c.booking_no and
			c.po_break_down_id=b.id and
			a.item_category=2 and
			a.fabric_source=1 and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0 and
			c.status_active=1 and
			c.is_deleted=0
			$sql_cond
			group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no";
		}
		$result = sql_select($sql);
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		?>
		<div align="left" style="margin-top:5px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="70">Booking No</th>
					<th width="80">Book. Date</th>
					<th width="100">Buyer</th>
					<th width="90">Item Category</th>
					<th width="90">Job No</th>
					<th width="90">Order Qnty</th>
					<th width="80">Ship. Date</th>
					<th width="150">Order No</th>
					<th width="80">Internal Ref</th>
					<th>File No</th>
				</thead>
			</table>
			<div style="width:990px; max-height:240px; overflow-y:scroll" id="list_container_batch">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table"
				id="list_view">
				<?
				$i = 1;
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					$po_qnty_in_pcs = 0;
					$po_no = '';
					$min_shipment_date = '';
					$style_ref_no = "";
					$buyer_name = "";
					$fileNo = "";
					$internal_ref = "";
					if (trim($booking_type) != 8) {
						if (trim($booking_type) == 2) {
							if ($row[csf('entry_form')] == 41) {
								$job_no_id = array_unique(explode(",", $row[csf('job_no_id')]));
								foreach ($job_no_id as $job_id) {
									$job_data = explode(",", substr($job_arr[$job_id], 0, -1));
									foreach ($job_data as $value) {
										$po_data = explode("**", $value);
										$po_number = $po_data[0];
										$pub_shipment_date = $po_data[1];
										$po_qnty = $po_data[2];
										$poQntyPcs = $po_data[3];
										$style_ref_no = $po_data[4];
										$buyer_id = $po_data[5];
										$supplier_id = $po_data[5];

										if ($po_no == "") {
											$po_no = $po_number;
										} else {
											$po_no .= "," . $po_number;
										}

										if ($min_shipment_date == '') {
											$min_shipment_date = $pub_shipment_date;
										} else {
											if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
										}

										$po_qnty_in_pcs += $poQntyPcs;
										$style_ref_no = $style_ref_no;
										$buyer_name = $buyer_arr[$buyer_id];
									}
								}

								if (trim($buyer) != 0) $buyer_name = $buyer_arr[$row[csf('buyer_id')]];
							} else {
								$buyer_name = "&nbsp;";
								$po_no = "&nbsp;";
								$po_qnty_in_pcs = "&nbsp;";
							}
						} else {
							$po_id = explode(",", $row[csf('po_break_down_id')]);
							foreach ($po_id as $id) {
								$po_data = explode("**", $po_arr[$id]);
								$po_number = $po_data[0];
								$pub_shipment_date = $po_data[1];
								$po_qnty = $po_data[2];
								$poQntyPcs = $po_data[3];
								$style_ref_no = $po_data[4];
								$grouping = $po_data[5];

								if ($po_no == "") $po_no = $po_number; else $po_no .= "," . $po_number;
								if ($internal_ref == "") $internal_ref = $grouping; else $internal_ref .= "," . $grouping;
								if ($fileNo == "") $fileNo = $file_no; else $fileNo .= "," . $file_no;

								if ($min_shipment_date == '') {
									$min_shipment_date = $pub_shipment_date;
								} else {
									if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
								}

								$po_qnty_in_pcs += $poQntyPcs;
								$style_ref_no = $style_ref_no;
							}

							$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
						}
					} else {
						$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
						$po_no = "&nbsp;";
						$po_qnty_in_pcs = "&nbsp;";
					}
					if($row[csf('pay_mode')]!=0){ $pay_mode=$row[csf('pay_mode')];}else{$pay_mode=0;}
					if($row[csf('service_type')]!=0){ $service_type=$row[csf('service_type')];}else{$service_type=0;}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('booking_no')]; ?>_<? echo $row[csf('buyer_id')]; ?>_<? echo trim(implode(",", array_unique(explode(",", $row[csf('job_no_mst')])))); ?>_<? echo $style_ref_no; ?>_<? echo $row[csf('entry_form')]; ?>_<? echo $row[csf('supplier_id')]; ?>_<? echo $row[csf('source')]; ?>_<? echo $pay_mode; ?>_<? echo $service_type; ?>');">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="100"><p><? echo $buyer_name; ?></p></td>
						<td width="90" align="center"><? echo $item_category[$row[csf('item_category')]]; ?></td>
						<td width="90"><? echo implode(",", array_unique(explode(",", $row[csf('job_no_mst')]))); ?></td>
						<td width="90" align="right"><? echo $po_qnty_in_pcs; ?></td>
						<td width="80" align="center"><? if ($min_shipment_date != "") echo change_date_format($min_shipment_date); ?></td>
						<td width="150"><p><? echo $po_no; ?></p></td>
						<td width="80"><? echo implode(",", array_unique(explode(",", $internal_ref))); ?></td>
						<td width=""><? echo implode(",", array_unique(explode(",", $fileNo))); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
	<?
}
exit();
}


if ($action == "check_booking_req") {
	//echo $data;
	//$('#txt_booking_id').val()+"**"+$('#txt_buyer_job_no').val()+"**"+$('#cbo_yarn_count').val()+"**"+$('#txt_composition_id').val()+"**"+$('#txt_composition_percent').val()+"**"+$('#cbo_yarn_type').val()+"**"+$('#cbo_color').val()+"**"+$('#cbo_dyeing_color').val()
	$data_ref = explode("**", $data);
	$booking_id = $data_ref[0];
	$job_no = $data_ref[1];
	$yarn_count = $data_ref[2];
	$composition_id = $data_ref[3];
	$composition_percent = $data_ref[4];
	$yarn_type = $data_ref[5];
	$yarn_color = $data_ref[6];
	$yarn_dyeing_color = $data_ref[7];
	$txt_prod_id = $data_ref[8];
	$txt_issue_qnty = $data_ref[9];
	$update_id = $data_ref[10];

	$booking_req_qnty = 0;
	$job_cond = "";
	if ($job_no != "") $job_cond = " and job_no='$job_no'";
	$comp_cond = "";
	if ($composition_id != "") $comp_cond = " and yarn_comp_type1st=$composition_id";

	$booking_req_sql = sql_select("select sum(yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_dtls where mst_id=$booking_id  and product_id=$txt_prod_id and yarn_color='$yarn_dyeing_color' and entry_form in(41,42) $job_cond
		union all
		select sum(yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_dtls where mst_id=$booking_id  $comp_cond and yarn_type=$yarn_type and yarn_color='$yarn_dyeing_color' and entry_form in(114,125) $job_cond");
	foreach ($booking_req_sql as $row) {
		if ($row[csf("yarn_wo_qty")] > 0) $booking_req_qnty += $row[csf("yarn_wo_qty")];
	}
	$trans_cond = "";
	if ($update_id != "") $trans_cond = " and b.id <> $update_id";

	$prev_issue_qnty = return_field_value("sum(b.cons_quantity) as cons_quantity", "inv_issue_master a, inv_transaction b", "a.id=b.mst_id and a.entry_form = 3 AND a.issue_basis = 1  AND a.issue_purpose = 2 and b.transaction_type=2 and b.item_category=1 and b.prod_id=$txt_prod_id $trans_cond", "cons_quantity");

	$cu_required = $booking_req_qnty - $prev_issue_qnty;
	$over_qnty = 0;
	if (($txt_issue_qnty * 1) > ($cu_required * 1)) {
		$over_qnty = ($txt_issue_qnty * 1) - ($cu_required * 1);
	}
	echo $over_qnty;
}
//yarn LOT POP UP Search Here
if ($action == "yarnLot_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function fn_check_lot(search_type) {
			show_list_view(document.getElementById('cbo_supplier').value + '_' + document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_<? echo $company; ?>' +
				'_<? echo $issue_purpose; ?>' +
				'_<? echo $cbo_store_name; ?>' +
				'_<? echo $txt_composition_id; ?>' + '_<? echo $txt_composition_percent; ?>' + '_<? echo $cbo_yarn_type; ?>' + '_<? echo $cbo_color; ?>' + '_<? echo $cbo_yarn_count; ?>'+ '_<? echo $yarn_rate_match; ?>'+ '_<? echo $txt_booking_no; ?>'+ '_<? echo $cbo_basis; ?>'+ '_<? echo $issue_purpose; ?>'+ '_<? echo $job_no; ?>_'+search_type,'create_lot_search_list_view','search_div','yarn_issue_store_update_controller','setFilterGrid(\'list_view\',-1)');
		}

		function js_set_value(prod_id) {
			$("#hidden_prod_id").val(prod_id);
			var prod_data=prod_id.split("**");
			var prod_id=prod_data[0];
			var yarn_rate=prod_data[2];
			var budge_rate=prod_data[3];
			var budge_cond=prod_data[4];
			var issue_perpose=prod_data[5];
			var job_no=prod_data[6];
			var booking_no=prod_data[7];
			var buyer=prod_data[8];
			if(budge_cond==11 && (yarn_rate*1)>(budge_rate*1) && issue_perpose != 8)
			{
				var page_link = 'yarn_issue_store_update_controller.php?action=budget_yarn_comparision_popup&job_no=' + job_no + '&booking_no=' + booking_no + '&buyer=' + buyer + '&prod_id=' + prod_id + '&yarn_rate=' + yarn_rate;
				var title = "Yarn Info Budget VS Actual";
				emailwindow = dhtmlmodal.open('EmailBox','iframe',page_link,title,'width=450px,height=250px,center=1,resize=0,scrolling=0','');
				return;
			}
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th>Supplier Name</th>
						<th>Search By</th>
						<th align="center" width="200" id="search_by_td_up">Enter Lot Number</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
							class="formbutton"/></th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td>
								<?
								echo create_drop_down("cbo_supplier", 170, "select c.id, c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- Select --", $supplier, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1 => 'Lot No', 2 => 'Yarn Count');
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../')";
								echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot(1)" style="width:100px;"/>
								<?
								$print_report_format=return_field_value("format_id"," lib_report_template","template_name=$company and module_id=6 and report_id=37 and is_deleted=0 and status_active=1");

								$print_report_format_arr=explode(",",$print_report_format);
								if($print_report_format != "")
								{
									foreach($print_report_format_arr as $id)
									{
										if($id==184){
											?>
											<input type="button" id="btnCompWise" value="Composition wise" onClick="fn_check_lot(2)" style="width:120px;" class="formbutton" />
											<?
										}
									}
								}
								?>
							</td>
						</tr>
						<input type="hidden" id="hidden_prod_id" value=""/>
					</tbody>
				</table>
				<div align="center" valign="top" style="margin-top:5px" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "budget_yarn_comparision_popup") {
	echo load_html_head_contents("PO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	ob_start();
	$buyer_arr 			= return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$yarn_count_arr 	= return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$composition_arr 	= return_library_array("select id,composition_name from lib_composition_array", 'id', 'composition_name');
	$product_info 		= sql_select("select lot,yarn_comp_type1st,yarn_count_id,yarn_type from product_details_master where id=$prod_id");

	$sql_budge=sql_select("select id,count_id,copm_one_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and status_active=1");
	$count_arr=$type_arr=$rate_arr=array();
	foreach($sql_budge as $row)
	{
		$count_arr[] = trim($row[csf("count_id")]);
		$type_arr[] = $row[csf("type_id")];
		$rate_arr[] = $row[csf("rate")];
	}
	?>
	<div id="report_container">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="100%" class="rpt_table">
			<thead>
				<tr>
					<td width="60" style="font-weight: bold;">Buyer</td>
					<td width="130"><?php echo $buyer_arr[$buyer];?></td>
					<td width="70" style="font-weight: bold;">Job No</td>
					<td width="80"><?php echo $job_no;?></td>
				</tr>
				<tr>
					<td style="font-weight: bold;">Booking No</td>
					<td><?php echo $booking_no;?></td>
					<td style="font-weight: bold;">Lot</td>
					<td><?php echo $product_info[0][csf("lot")];?></td>
				</tr>
				<tr>
					<th colspan="4" align="center">Yarn info as budget</th>
				</tr>
				<tr>
					<th>Count</th>
					<th>Composition</th>
					<th>Type</th>
					<th>Rate (USD)</th>
				</tr>
				<?php
				foreach($sql_budge as $row)
				{
					?>
					<tr>
						<td><?php echo $yarn_count_arr[$row[csf("count_id")]];?></td>
						<td><?php echo $composition_arr[$row[csf("copm_one_id")]];?></td>
						<td><?php echo $yarn_type[$row[csf("type_id")]];?></td>
						<td align="right"><?php echo $row[csf("rate")];?></td>
					</tr>
					<?
				}
				?>
				<tr>
					<th colspan="4" align="center">Yarn info as issue</th>
				</tr>
				<tr>
					<?php
					$count_bg = (!in_array($product_info[0][csf("yarn_count_id")], $count_arr))?"background-color:red;color:#fff;":"";
					$type_bg = (!in_array($product_info[0][csf("yarn_type")], $type_arr))?"background-color:red;color:#fff;":"";
					$rate_bg = (!in_array($yarn_rate, $rate_arr))?"background-color:red;color:#fff;":"";
					$budget_bg = ($yarn_rate > $budge_data[$product_info[0][csf("yarn_count_id")]][$product_info[0][csf("yarn_type")]]["rate"])?"background-color:red;color:white;":"";
					?>
					<td style="<?php //echo $count_bg;?>"><?php echo $yarn_count_arr[$product_info[0][csf("yarn_count_id")]];?></td>
					<td><?php echo $composition_arr[$product_info[0][csf("yarn_comp_type1st")]];?></td>
					<td style="<?php //echo $type_bg;?>"><?php echo $yarn_type[$product_info[0][csf("yarn_type")]];?></td>
					<td  style="<?php //echo $rate_bg;?>" align="right"><?php echo $yarn_rate;?></td>
				</tr>
			</thead>
		</table>
		<h3 style="color: red;text-align: center;">Selected yarn does not match with budget</h3>
		<input type="button" value="Export To Excel" name="excel" id="excel" class="formbutton" style="width:155px; margin-left: 150px;"  />
	</div>
	<?
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename= $user_name."_".$name.".xls";
	?>
	<script type="text/javascript">
		$("#excel").click(function(e) {
			window.open("<? echo $filename ; ?>", + $('#report_container').html());
			e.preventDefault();
		});
	</script>
	<?
	die;
}

if ($action == "create_lot_search_list_view") {
	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$company = $ex_data[3];
	$issue_purpose = $ex_data[4];
	$store_name = $ex_data[5];
	$txt_composition_id = trim($ex_data[6]);
	$txt_composition_percent = $ex_data[7];
	$cbo_yarn_type = $ex_data[8];
	$cbo_color = $ex_data[9];
	$cbo_yarn_count = $ex_data[10];
	$yarn_rate_match = $ex_data[11];
	$txt_booking_no = $ex_data[12];
	$cbo_basis = $ex_data[13];
	$issue_purpose = $ex_data[14];
	$job_no = $ex_data[15];
	$search_type = $ex_data[16];

	if($yarn_rate_match==1 && $cbo_basis==1)
	{
		$sql_book_job=sql_select("select distinct b.job_no,a.buyer_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_no='$txt_booking_no' and b.status_active=1");
		$book_job_no=$sql_book_job[0][csf("job_no")];
		$buyer_id=$sql_book_job[0][csf("buyer_id")];
		$sql_budge=sql_select("select id, count_id, copm_one_id, type_id, rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$book_job_no' and status_active=1");
		$budge_data=array();
		foreach($sql_budge as $row)
		{
			$budge_data[$row[csf("count_id")]][$row[csf("type_id")]]["id"]=$row[csf("id")];
			$budge_data[$row[csf("count_id")]][$row[csf("type_id")]]["rate"]=$row[csf("rate")];
			$budge_data[$row[csf("count_id")]][$row[csf("type_id")]]["rate"]=$row[csf("rate")];
		}
	}

	$sql_cond = "";
	if (trim($txt_search_common) != "") {
		if (trim($txt_search_by) == 1) // for LOT NO
		{
			$sql_cond .= " and b.lot LIKE '%$txt_search_common%'";
		} else if (trim($txt_search_by) == 2) // for Yarn Count
		{
			$sql_cond .= " and b.yarn_count_id LIKE '%$txt_search_common%'";
		}
	}

	if (trim($supplier) != 0) $sql_cond .= " and b.supplier_id='$supplier'";
	if (trim($company) != 0) $sql_cond .= " and b.company_id='$company'";
	if (trim($store_name) > 0) $sql_cond .= " and a.store_id='$store_name'";
	if($search_type == 1)
	{
		if (trim($txt_composition_id) > 0) $sql_cond .= " and b.yarn_comp_type1st='$txt_composition_id'";
		if (trim($txt_composition_percent) > 0) $sql_cond .= " and b.yarn_comp_percent1st='$txt_composition_percent'";
		if (trim($cbo_yarn_type) > 0) $sql_cond .= " and b.yarn_type='$cbo_yarn_type'";
		if (trim($cbo_yarn_count) > 0) $sql_cond .= " and b.yarn_count_id='$cbo_yarn_count'";
	}else
	{
		if (trim($txt_composition_id) > 0) $sql_cond .= " and b.yarn_comp_type1st='$txt_composition_id'";
	}


	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company and variable_list=18 and item_category_id = 1","allocation");
	if ($variable_set_allocation == 1) { // IF ALLOCATION IS SET TO YES IN VARIABLE SETTINGS
		if(($cbo_basis == 2) || ($cbo_basis == 1 && $issue_purpose == 8)){
			if ($db_type == 0) {
				$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, group_concat( a.weight_per_cone ) AS weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
				from product_details_master b, inv_transaction a
				where
				a.prod_id=b.id and
				a.item_category=1 and
				b.item_category_id=1 and
				b.status_active=1 and
				a.status_active=1 and
				b.current_stock>0
				$sql_cond
				group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";

			} else {
				$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, LISTAGG(a.weight_per_cone, ',') WITHIN GROUP (ORDER BY a.weight_per_cone) as weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
				from product_details_master b, inv_transaction a
				where
				a.prod_id=b.id and
				a.item_category=1 and
				b.item_category_id=1 and
				b.status_active=1 and
				a.status_active=1 and
				b.current_stock>0
				$sql_cond
				group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";
			}
		}else{
			if($job_no!="")
			{
				if ($db_type == 0) {
					$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, group_concat( a.weight_per_cone ) AS weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
					from product_details_master b, inv_transaction a,inv_material_allocation_mst c
					where
					a.prod_id=b.id and a.prod_id=c.item_id and
					a.item_category=1 and
					b.item_category_id=1 and
					b.status_active=1 and
					a.status_active=1 and
					c.status_active=1 and
					b.current_stock>0
					and c.job_no='$job_no'
					$sql_cond
					group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";

				} else {
					$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, LISTAGG(a.weight_per_cone, ',') WITHIN GROUP (ORDER BY a.weight_per_cone) as weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
					from product_details_master b, inv_transaction a,inv_material_allocation_mst c
					where
					a.prod_id=b.id and a.prod_id=c.item_id and
					a.item_category=1 and
					b.item_category_id=1 and
					b.status_active=1 and
					a.status_active=1 and
					c.status_active=1 and
					b.current_stock>0
					and c.job_no='$job_no'
					$sql_cond
					group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";
				}
			}
			else
			{
				if ($db_type == 0) {
					$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, group_concat( a.weight_per_cone ) AS weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
					from product_details_master b, inv_transaction a
					where
					a.prod_id=b.id and
					a.item_category=1 and
					b.item_category_id=1 and
					b.status_active=1 and
					a.status_active=1 and
					b.current_stock>0
					$sql_cond
					group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";

				} else {
					$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, LISTAGG(a.weight_per_cone, ',') WITHIN GROUP (ORDER BY a.weight_per_cone) as weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
					from product_details_master b, inv_transaction a
					where
					a.prod_id=b.id and
					a.item_category=1 and
					b.item_category_id=1 and
					b.status_active=1 and
					a.status_active=1 and
					b.current_stock>0
					$sql_cond
					group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";
				}
			}

		}
	}else{
		if ($db_type == 0) {
			$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, group_concat( a.weight_per_cone ) AS weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
			from product_details_master b, inv_transaction a
			where
			a.prod_id=b.id and
			a.item_category=1 and
			b.item_category_id=1 and
			b.status_active=1 and
			a.status_active=1 and
			b.current_stock>0
			$sql_cond
			group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";

		} else {
			$sql = "select b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty, LISTAGG(a.weight_per_cone, ',') WITHIN GROUP (ORDER BY a.weight_per_cone) as weight_per_cone, sum(case when a.transaction_type=1 then a.order_qnty else 0 end) as rcv_qnty, sum(case when a.transaction_type=1 then a.order_amount else 0 end) as rcv_amt
			from product_details_master b, inv_transaction a
			where
			a.prod_id=b.id and
			a.item_category=1 and
			b.item_category_id=1 and
			b.status_active=1 and
			a.status_active=1 and
			b.current_stock>0
			$sql_cond
			group by b.id, b.company_id, b.supplier_id, b.lot, b.current_stock, b.allocated_qnty, b.available_qnty, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.avg_rate_per_unit,a.store_id order by b.lot";
		}
	}
	//echo $sql;
	$result = sql_select($sql);
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$store_arr = return_library_array("select id,store_name from lib_store_location", 'id', 'store_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');

	$date_arr = array();
	$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
	$result_returnRes_date = sql_select($returnRes_date);
	foreach ($result_returnRes_date as $row)
	{
		$date_arr[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
	}
	?>
	<div align="left" style="margin-left:20px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="130">Supplier</th>
				<th width="70">Yarn Lot</th>
				<th width="80">Yarn Count</th>
				<th width="200">Composition</th>
				<th width="80">Yarn Type</th>
				<th width="110">Color</th>
				<th width="110">Store</th>
				<th>Current Stock</th>
				<th width="70">Rate</th>
				<th width="70">Weight @ Cone</th>
				<th width="70">Age (Days)</th>
			</thead>
		</table>

		<div style="width:1180px; max-height:240px; overflow-y:scroll" id="list_container_batch">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table" id="list_view">
				<?
				$i = 1;
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
					if ($row[csf('yarn_comp_type2nd')] != 0) $composition_string .= " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";

					$store = '';
					$store_id = array_unique(explode(",", $row[csf('store_id')]));
					foreach ($store_id as $val) {
						if ($store == '') $store = $store_arr[$val]; else $store .= "," . $store_arr[$val];
					}

					$yarn_rate=number_format(($row[csf('rcv_amt')]/$row[csf('rcv_qnty')]),4,'.','');
					$budge_rate=number_format($budge_data[$row[csf('yarn_count_id')]][$row[csf('yarn_type')]]["rate"],4,'.','');
					$budge_cond_data=$yarn_rate_match.$cbo_basis;
					$stock_qnty = $row[csf("balance_qnty")];
					$weight_per_cone = implode(",", array_unique(explode(",", $row[csf("weight_per_cone")])));
					$count = $row[csf('yarn_count_id')];
					$lot = $row[csf('lot')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value('<? echo $row[csf('id')] . "**" . $weight_per_cone. "**" . $yarn_rate. "**" . $budge_rate. "**" . $budge_cond_data. "**" . $issue_purpose . "**" . $book_job_no . "**" . $txt_booking_no . "**" . $buyer_id; ?>');">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="70" align="center"><p>&nbsp;<? echo $row[csf('lot')]; ?></p></td>
						<td width="80" align="center"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?>&nbsp;</p></td>
						<td width="200"><p><? echo $composition_string; ?></p></td>
						<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $color_arr[$row[csf('color')]]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $store; ?>&nbsp;</p></td>
						<td align="right"><p><? echo number_format($stock_qnty, 2); ?>&nbsp;</p></td>
						<td width="70" align="right"><? echo $yarn_rate; ?></td>
						<td width="70" align="right"><p><? echo $weight_per_cone; ?></p></td>
						<td width="70" align="center">
							<?
							$ageOfDays = datediff("d", $date_arr[$row[csf("id")]]['min_date'], date("Y-m-d"));
							echo $ageOfDays;
							?>
						</td>
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

// child form data populate after lot pop up search
if ($action == "populate_data_child_from") {
	$data = explode("**", $data);
	$prodID = $data[0];
	$issue_purpose = $data[1];
	$store_name = $data[2];
	$weight_cone = $data[3];
	$weight_cone = explode(",", $weight_cone);
	if (trim($store_name) > 0) $sql_cond = " and a.store_id='$store_name'";

	$sql = "select b.id, b.company_id, b.supplier_id, b.store_id, b.item_category_id, b.detarmination_id, b.sub_group_code, b.sub_group_name, b.item_group_id, b.item_description, b.product_name_details, b.lot, b.item_code, b.unit_of_measure, b.re_order_label, b.minimum_label, b.maximum_label, b.item_account, b.packing_type, b.avg_rate_per_unit, b.last_purchased_qnty, b.current_stock, b.last_issued_qnty, b.stock_value, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.brand, b.allocated_qnty, b.available_qnty, a.store_id as store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_qnty
	from product_details_master b,  inv_transaction a
	where
	a.prod_id=b.id and
	a.item_category=1 and
	b.id='$prodID' and
	b.item_category_id=1 and
	a.status_active=1 and
	a.is_deleted=0 and
	b.status_active=1 and
	b.is_deleted=0 $sql_cond
	group by b.id, b.company_id, b.supplier_id, b.store_id, b.item_category_id, b.detarmination_id, b.sub_group_code, b.sub_group_name, b.item_group_id, b.item_description, b.product_name_details, b.lot, b.item_code, b.unit_of_measure, b.re_order_label, b.minimum_label, b.maximum_label, b.item_account, b.packing_type, b.avg_rate_per_unit, b.last_purchased_qnty, b.current_stock, b.last_issued_qnty, b.stock_value, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.brand, b.allocated_qnty, b.available_qnty, a.store_id";

	$result = sql_select($sql);
	foreach ($result as $row) {
		$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
		if ($row[csf('yarn_comp_type2nd')] != 0) $composition_string .= " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')];

		/*if($db_type==0)
		{
			$invData=sql_select("select group_concat(distinct(store_id)) as store_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where prod_id=".$row[csf('id')]." and item_category=1 and transaction_type=1");

			//$store_id=return_field_value("group_concat(distinct(store_id)) as store_id","inv_transaction","prod_id=".$row[csf('id')]." and item_category=1 and transaction_type=1","store_id");
			$store_id=$invData[0][csf('store_id')];
		}
		else
		{
			$invData=sql_select("select LISTAGG(store_id, ',') WITHIN GROUP (ORDER BY store_id) as store_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where prod_id=".$row[csf('id')]." and item_category=1 and transaction_type=1");

			//$store_id=return_field_value("LISTAGG(store_id, ',') WITHIN GROUP (ORDER BY store_id) as store_id","inv_transaction","prod_id=".$row[csf('id')]." and item_category=1 and transaction_type=1","store_id");
			$store_id=$invData[0][csf('store_id')];
			$store_id=implode(",",array_unique(explode(",",$store_id)));
		}*/

		$stock_qnty = $row[csf("balance_qnty")];

		//echo "$('#cbo_store_name').val('".$store_id."');\n";

		echo "$('#cbo_supplier').val('" . $row[csf("supplier_id")] . "');\n";
		echo "$('#cbo_supplier_lot').val('" . $row[csf("supplier_id")] . "');\n";
		echo "$('#txt_lot_no').val('" . $row[csf("lot")] . "');\n";
		echo "$('#txt_prod_id').val('" . $row[csf("id")] . "');\n";
		echo "$('#txt_current_stock').val(" . $stock_qnty . ");\n";
		echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
		echo "$('#txt_composition').val('" . $composition_string . "');\n";
		echo "$('#cbo_yarn_type').val('" . $row[csf("yarn_type")] . "');\n";
		echo "$('#cbo_uom').val('" . $row[csf("unit_of_measure")] . "');\n";
		echo "$('#cbo_color').val('" . $row[csf("color")] . "');\n";
		echo "$('#cbo_brand').val('" . $row[csf("brand")] . "');\n";
		echo "$('#txt_weight_per_bag').val('" . $invData[0][csf("weight_per_bag")] . "');\n";
		//echo "$('#txt_weight_per_cone').val('".$invData[0][csf("weight_per_cone")]."');\n";
		echo "$('#txt_weight_per_cone').val('" . $weight_cone[0] . "');\n";
	}
	exit();
}


if ($action == "po_popup") {
	echo load_html_head_contents("PO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("_", $data);
	$po_id = $data[0]; //order ID
	if ($data[1]) $type = $data[1]; else $type = 0; //is popup search or not
	$prevQnty = $data[2]; //previous input qnty po wise
	if ($data[3] != "") $prev_method = $data[3];
	else $prev_method = $distribution_method;
	if ($data[4] != "") $issueQnty = $data[4];
	else $issueQnty = $issueQnty;
	if ($data[5] != "") $retnQnty = $data[5];
	if ($update_id=="") {$update_id=0;}
	if ($issue_purpose == 2) {
		$entry_form = return_field_value("entry_form", "wo_yarn_dyeing_mst", "ydw_no='$booking_no'", "entry_form");
	}
	?>
	<script>
		var receive_basis = <? echo $receive_basis; ?>;
		var issue_purpose = <? echo $issue_purpose; ?>;
		var updateIDs = <? echo $update_id; ?>;


		function fn_show_check() {
			if (form_validation('cbo_buyer_name', 'Buyer Name') == false) {
				return;
			}
			show_list_view($('#txt_search_common').val() + '_' + $('#cbo_search_by').val() + '_<? echo $cbo_company_id; ?>' +
				'_' + $('#cbo_buyer_name').val() + '_<? echo $all_po_id; ?>_' +<? echo $receive_basis;?>, 'create_po_search_list_view', 'search_div', 'yarn_issue_store_update_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();'
				)
			;
			set_all();
		}
		var tot_extra_qnty = 0;
		function distribute_qnty(str) {
            if (str == 1) //Proportionate
            {
            	$('#txt_prop_grey_qnty').attr('readonly', false);
            	var tot_po_qnty = $('#tot_po_qnty').val() * 1;
            	var tot_req_qnty = $('#tot_req_qnty').val() * 1;
            	var txt_prop_grey_qnty = $('#txt_prop_grey_qnty').val() * 1;
            	var tblRow = $("#tbl_list_search tr").length;
            	var len = totalGrey = 0;
            	var tot_extra_qnty = 0;
            	$("#tbl_list_search").find('tr').each(function () {
            		var extra_quantity = 0;
            		len = len + 1;
            		var txtOrginal = $(this).find('input[name="txtOrginal[]"]').val() * 1;
            		var txtGreyQnty_placeholder = $(this).find('input[name="txtGreyQnty[]"]').attr('placeholder') * 1;

            		if (txtOrginal == 0) {
            			$(this).remove();
            		}
            		else {
            			if (txt_prop_grey_qnty > 0) {



            				if(receive_basis == 3){
            					var req_qnty = $(this).find('input[name="txtReqQnty[]"]').val() * 1;

            					var perc = req_qnty / tot_req_qnty * 100;
            				}else{
            					var po_qnty = $(this).find('input[name="txtPoQnty[]"]').val() * 1;
            					var perc = po_qnty / tot_po_qnty * 100;
            				}

            				var grey_qnty = (perc * txt_prop_grey_qnty) / 100;
            				totalGrey = totalGrey * 1 + grey_qnty * 1;
            				totalGrey = totalGrey;
            				if (tblRow == len) {
            					var balance = txt_prop_grey_qnty - totalGrey;
            					if(balance > 0){
            						grey_qnty = grey_qnty - balance;
            					}else{
            						grey_qnty = grey_qnty + balance;
            					}
            				}
            				var is_work_order = 0;
            				if(issue_purpose == 2 || issue_purpose == 15 || issue_purpose == 38 || issue_purpose == 46 || issue_purpose == 7){
            					is_work_order = 1;
            				}

            				if ((grey_qnty.toFixed(2) * 1 > txtGreyQnty_placeholder.toFixed(2) * 1) && (receive_basis == 1 && (is_work_order != 1))) {

            					$(this).find('input[name="txtGreyQnty[]"]').val(grey_qnty);
            					extra_quantity = ((grey_qnty.toFixed(2) * 1) - (txtGreyQnty_placeholder.toFixed(2) * 1));
            					tot_extra_qnty += extra_quantity;
            					$(this).find('input[name="txtReturnQnty[]"]').val(extra_quantity.toFixed(2));
            					$(this).find('input[name="txtReturnQnty[]"]').attr('disabled', false).attr('readonly', false);
            					return;
            				}
            				else {

            					$(this).find('input[name="txtReturnQnty[]"]').val("");
            					$(this).find('input[name="txtGreyQnty[]"]').val(grey_qnty);
            					$(this).find('input[name="txtReturnQnty[]"]').attr('disabled', false).attr('readonly', false);
            					if(receive_basis == 3){
            						calculate_returnable_qnty(len,grey_qnty);
            					}
            				}
            			}
            			else {

            				extra_quantity = 0;
            				$('#txt_prop_grey_qnty').val('');
            				$("#tbl_list_search").find('tr').each(function () {
            					$(this).find('input[name="txtGreyQnty[]"]').val('');
            				});
            			}
            			$(this).find('input[name="txtGreyQnty[]"]').attr('readonly', true);
            		}
            	});
            	if (tot_extra_qnty > 0) {
            		$('#extra_quantity').val(tot_extra_qnty.toFixed(2));
            		$('#txt_prop_retn_qnty').val(tot_extra_qnty.toFixed(2));
            		$('#txt_prop_retn_qnty').attr('readonly', false);
            	}
            	else {
            		$('#extra_quantity').val("");
            		$('#txt_prop_retn_qnty').val("");
            		$('#txt_prop_retn_qnty').attr('readonly', false);
            	}

            }
            else {
            	$('#txt_prop_grey_qnty').val('');
            	$('#extra_quantity').val("");
            	$('#txt_prop_grey_qnty').attr('readonly', true);
            	$("#tbl_list_search").find('tr').each(function () {
            		$(this).find('input[name="txtGreyQnty[]"]').val('');
            		$(this).find('input[name="txtGreyQnty[]"]').removeAttr('readonly');
            		$(this).find('input[name="txtReturnQnty[]"]').removeAttr('readonly');
            		$(this).find('input[name="txtReturnQnty[]"]').attr('disabled', false).attr('readonly', false);
            	});
            }
        }


        function fn_bal_check(str) {
        	var tot_extra_qnty = $('#extra_quantity').val();
        	var cbo_distribiution_method = $('#cbo_distribiution_method').val() * 1;
        	var extra_quantity = 0;
        	var txtGreyQnty_placeholder = $('#txtGreyQnty_' + str).attr('placeholder') * 1;
        	var grey_qnty = $('#txtGreyQnty_' + str).val() * 1;

        	var is_work_order = 0;
        	if(issue_purpose == 2 || issue_purpose == 15 || issue_purpose == 38 || issue_purpose == 46 || issue_purpose == 7){
        		is_work_order = 1;
        	}

        	if ((grey_qnty.toFixed(2) * 1 > txtGreyQnty_placeholder.toFixed(2) * 1) && (receive_basis == 1 && is_work_order != 1) && cbo_distribiution_method == 2) {
        		extra_quantity = ((grey_qnty.toFixed(2) * 1) - (txtGreyQnty_placeholder.toFixed(2) * 1));
        		tot_extra_qnty = tot_extra_qnty * 1 + extra_quantity * 1;
        		$('#extra_quantity').val(tot_extra_qnty.toFixed(2));
        		$('#txtReturnQnty_' + str).val(extra_quantity.toFixed(2));
        		$('#txtReturnQnty_' + str).attr('disabled', true);
        		return;
        	}
        }

        function distribute_retn_qnty(str) {
        	if (str == 1) {
        		if(receive_basis != 3){
        			$('#txt_prop_retn_qnty').attr('readonly', false);
        		}else{
        			$('#txt_prop_retn_qnty').attr('readonly', true).attr('disabled', true);
        		}

        		var tot_po_qnty = $('#tot_po_qnty').val() * 1;
        		var txt_prop_retn_qnty = $('#txt_prop_retn_qnty').val() * 1;
        		var tblRow = $("#tbl_list_search tr").length;
        		var len = totalGreyRetn = 0;
        		$("#tbl_list_search").find('tr').each(function () {
        			len = len + 1;
        			var txtOrginal = $(this).find('input[name="txtOrginal[]"]').val() * 1;
        			if (txtOrginal == 0) {
        				$(this).remove();
        			}
        			else {
        				var po_qnty = $(this).find('input[name="txtPoQnty[]"]').val() * 1;
        				var perc = (po_qnty / tot_po_qnty) * 100;
        				var grey_retn_qnty = (perc * txt_prop_retn_qnty) / 100;

        				totalGreyRetn = totalGreyRetn * 1 + grey_retn_qnty * 1;
        				totalGreyRetn = totalGreyRetn.toFixed(2);
        				if (tblRow == len) {
        					var balance = txt_prop_retn_qnty - totalGreyRetn;
        					if (balance != 0) grey_retn_qnty = grey_retn_qnty + (balance);
        				}
        				if(receive_basis == 3){
        					$(this).find('input[name="txtReturnQnty[]"]').val(grey_retn_qnty.toFixed(2)).attr('disabled', true);
        				}else{
        					$(this).find('input[name="txtReturnQnty[]"]').val(grey_retn_qnty.toFixed(2));
        				}
        			}
        		});
        	}
        	else {
        		$('#txt_prop_retn_qnty').val('');
        		$('#txt_prop_retn_qnty').attr('readonly', true).attr('disabled', true);
        		$("#tbl_list_search").find('tr').each(function () {
        			if(receive_basis == 3){
        				$(this).find('input[name="txtReturnQnty[]"]').val('').attr('readonly', true).attr('disabled', true);
        			}else{
        				$(this).find('input[name="txtReturnQnty[]"]').val('');
        			}
        		});
        	}
        }

        var selected_id = new Array();
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
        	var old = document.getElementById('txt_po_row_id').value;
        	if (old != "") {
        		old = old.split(",");
        		for (var i = 0; i < old.length; i++) {
        			js_set_value(old[i])
        		}
        	}
        }

        function js_set_value(str) {
        	toggle(document.getElementById('search' + str), '#FFFFCC');

        	if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
        		selected_id.push($('#txt_individual_id' + str).val());
        	}
        	else {
        		for (var i = 0; i < selected_id.length; i++) {
        			if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
        		}
        		selected_id.splice(i, 1);
        	}
        	var id = '';
        	for (var i = 0; i < selected_id.length; i++) {
        		id += selected_id[i] + ',';
        	}
        	id = id.substr(0, id.length - 1);

        	$('#po_id').val(id);
        }

        function show_grey_prod_recv() {
        	var po_id = $('#po_id').val();
        	var prev_save_string = $('#prev_save_string').val();
        	var prev_method = $('#prev_method').val();
        	var prev_total_qnty = $('#prev_total_qnty').val();
        	var prev_retn_qnty = $('#prev_retn_qnty').val();
        	show_list_view(po_id + '_' + '1' + '_' + prev_save_string + '_' + prev_method + '_' + prev_total_qnty + '_' + prev_retn_qnty, 'po_popup', 'search_div', 'yarn_issue_store_update_controller', '');
        }

        function hidden_field_reset() {
        	$('#po_id').val('');
        	$('#save_string').val('');
        	$('#tot_grey_qnty').val('');
        	selected_id = new Array();
        }

        function fnc_close() {
        	var save_string = '';
        	var tot_grey_qnty = 0;
        	var tot_retn_qnty = '';
        	var no_of_roll = '';
        	var po_id_array = new Array();
        	$("#tbl_list_search").find('tr').each(function () {
        		var txtPoId = $(this).find('input[name="txtPoId[]"]').val();
        		var txtGreyQnty = $(this).find('input[name="txtGreyQnty[]"]').val();
        		var txtReturnQnty = $(this).find('input[name="txtReturnQnty[]"]').val();
        		tot_grey_qnty = tot_grey_qnty * 1 + txtGreyQnty * 1;
        		tot_retn_qnty = tot_retn_qnty * 1 + txtReturnQnty * 1;

        		if (txtGreyQnty * 1 > 0) {
        			if (save_string == "") {
        				save_string = txtPoId + "**" + txtGreyQnty + "**" + txtReturnQnty;
        			}
        			else {
        				save_string += "," + txtPoId + "**" + txtGreyQnty + "**" + txtReturnQnty;
        			}

        			if (jQuery.inArray(txtPoId, po_id_array) == -1) {
        				po_id_array.push(txtPoId);
        			}
        		}
        	});

        	if (tot_grey_qnty.toString().indexOf(".") == -1) {
        		$('#tot_grey_qnty').val(tot_grey_qnty);
        	}
        	else {
        		$('#tot_grey_qnty').val(tot_grey_qnty.toFixed(2));
        	}

        	$('#save_string').val(save_string);
        	$('#tot_retn_qnty').val(tot_retn_qnty);
        	$('#all_po_id').val(po_id_array);
        	$('#distribution_method').val($('#cbo_distribiution_method').val());
        	parent.emailwindow.hide();
        }
        function calculate_returnable_qnty(i,thisValue){

        	if(thisValue != ""){
        		var plan_qnty = $("#txt_plan_qnty_"+i).val() * 1;
        		var cum_issue = $("#txt_cum_issue_"+i).val() * 1;
        		var hdn_grey_qnty = $("#hdn_grey_qnty_"+i).val() * 1;
        		var hdn_grey_qnty_fixed = $("#hdn_grey_qnty_fixed_"+i).val() * 1;
        		var hdn_returnable_qnty = $("#txt_cum_returnable_"+i).val() * 1;
        		var txtReturnQnty = $("#txtReturnQnty_"+i).val() * 1;
        		var hdnReturnQnty = $("#hdnReturnQnty_"+i).val() * 1;

                // Returnable Formula = (Cummulative Issue Qnty + Given Issue Qnty) - Program Quantity
                if(hdn_grey_qnty > 0 || hdn_grey_qnty != ''){
                	var returnable = (cum_issue - (hdn_grey_qnty_fixed - (thisValue*1)) - (hdn_returnable_qnty-hdnReturnQnty)) - plan_qnty;
                }else{
                	var returnable = ((cum_issue - hdn_returnable_qnty) + (thisValue*1)) - plan_qnty;
                }
                $("#txtReturnQnty_"+i).val((returnable > 0) ? returnable.toFixed(2) : '').attr('readonly', false);
            }else{
            	$("#txtReturnQnty_"+i).val('');
            }
        }
	</script>

</head>
<body>
	<? if ($type != 1)
	{
		?>
		<form name="searchdescfrm" id="searchdescfrm">
			<fieldset style="width:950px;margin-left:10px">
				<!-- previous data here -->
				<input type="hidden" name="prev_save_string" id="prev_save_string" class="text_boxes"
				value="<? echo $save_data; ?>">
				<input type="hidden" name="prev_total_qnty" id="prev_total_qnty" class="text_boxes"
				value="<? echo $issueQnty; ?>">
				<input type="hidden" name="prev_retn_qnty" id="prev_retn_qnty" class="text_boxes"
				value="<? echo $retnQnty; ?>">
				<input type="hidden" name="prev_method" id="prev_method" class="text_boxes"
				value="<? echo $distribution_method; ?>">
				<!--- END-->
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
				<input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_retn_qnty" id="tot_retn_qnty" class="text_boxes" value="">
				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
				<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<input type="hidden" name="extra_quantity" id="extra_quantity" class="text_boxes"
				value="<? echo $extra_quantity; ?>">
				<?
			}

			$req_qty_array = array();
			$is_salesOrder = 0;
			$program_qnty_arr=array();
			if ($receive_basis == 2 || ($receive_basis == 1 && $issue_purpose == 2)) {
				$po_cond_req = "";
				$po_cond_iss = "";
				if($entry_form == 135){
					$is_salesOrder = 1;
				}
			} else {
				$po_id_req_iss = '';
				if ($receive_basis == 3) {
					$req_booking_sql = sql_select("select y.po_id,x.requisition_no,x.knit_id,y.program_qnty program_qnty,y.is_sales from(select distinct(a.requisition_no),a.knit_id from ppl_yarn_requisition_entry a where a.requisition_no=$req_no and a.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0) x, ppl_planning_entry_plan_dtls y where x.knit_id = y.dtls_id and y.status_active=1 and y.is_deleted=0");
					$is_salesOrder = $req_booking_sql[0][csf('is_sales')];
					foreach($req_booking_sql as $req_row){
						$program_qnty_arr[$req_row[csf('po_id')]][$req_row[csf('requisition_no')]] += number_format($req_row[csf('program_qnty')], 2, '.', '');
					}
				}else if($entry_form == 135){
					$is_salesOrder = 1;
				}else if($entry_form == 94){
					$yarn_dyeing_info=sql_select("select entry_form,is_sales from wo_yarn_dyeing_mst where status_active=1 and ydw_no='$booking_no' and company_id=$cbo_company_id and entry_form=94");
					//$is_salesOrder = 1;
					$is_salesOrder = $yarn_dyeing_info[0][csf("is_sales")];
				} else {
					$req_booking_sql = sql_select("select po_break_down_id as po_id from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no='$booking_no' group by po_break_down_id");
				}

				foreach ($req_booking_sql as $row) {
					$po_id_req_iss .= $row[csf('po_id')] . ",";
				}
				$po_id_req_iss = chop($po_id_req_iss, ",");

				if ($po_id_req_iss != "") {
					$po_cond_req = " and b.po_break_down_id in($po_id_req_iss)";
					$po_cond_iss = " and po_breakdown_id in($po_id_req_iss)";
				}
			}

			$req_qty_array = array();
			$req_val_array = array();

			if ($is_salesOrder == 1) {
				if($receive_basis==3){
					$req_data_array=sql_select("select b.requisition_no id,sum(b.yarn_qnty) qnty from ppl_yarn_requisition_entry b where b.requisition_no='$req_no' and b.prod_id='$txt_prod_id' and b.is_deleted=0 and b.is_deleted=0 group by b.requisition_no");
				}else{
					if($entry_form == 94){
						$req_data_array=sql_select("select b.job_no_id id,b.product_id,sum(b.yarn_wo_qty) qnty,sum(b.amount*b.dyeing_charge) val from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no='$booking_no' group by b.job_no_id,b.product_id");
					}else{
						$req_data_array = sql_select("select a.id, sum(b.grey_qty) qnty,sum (b.amount*b.avg_rate) val from fabric_sales_order_mst a inner join fabric_sales_order_dtls b on a.id = b.mst_id where a.id in($po_id_req_iss) group by a.id");
					}
				}
			} else {

				if($receive_basis==3){
					$req_data_array=sql_select("select a.po_id id,sum(b.yarn_qnty) qnty from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where  a.dtls_id=b.knit_id and b.requisition_no='$req_no' and b.prod_id='$txt_prod_id' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by a.po_id");

					$req_data_array=sql_select("select b.requisition_no id,sum(b.yarn_qnty) qnty from ppl_yarn_requisition_entry b where b.requisition_no='$req_no' and b.prod_id='$txt_prod_id' and b.is_deleted=0 and b.is_deleted=0 group by b.requisition_no");
				}else{
					$req_data_array = sql_select("select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty, sum(b.amount*a.exchange_rate) as val from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_req group by b.po_break_down_id");
				}
			}

			foreach ($req_data_array as $row) {
				if ($is_salesOrder == 1) {
					if($receive_basis==3){
						$req_qty_array[$row[csf('id')]] = $row[csf('qnty')];
						$req_val_array[$row[csf('id')]] = 0;
					}else{
						if($entry_form == 94){
							$req_qty_array[$row[csf('id')]][$row[csf('product_id')]] = $row[csf('qnty')];
							$req_val_array[$row[csf('id')]][$row[csf('product_id')]] = $row[csf('val')];
						}else{
							$req_qty_array[$row[csf('id')]] = $row[csf('qnty')];
							$req_val_array[$row[csf('id')]] = $row[csf('val')];
						}
					}
				} else {
					if($receive_basis==3){
						$req_qty_array[$row[csf('id')]] = $row[csf('qnty')];
						$req_val_array[$row[csf('id')]] = 0;
					}else{
						$req_qty_array[$row[csf('po_break_down_id')]] = $row[csf('qnty')];
						$req_val_array[$row[csf('po_break_down_id')]] = $row[csf('val')];
					}
				}
			}

			$product_arr = return_library_array("select id, avg_rate_per_unit from product_details_master where item_category_id=1", 'id', 'avg_rate_per_unit');

			$cum_issue_array =$cum_returnable_array= array();

			$sales_order_cond = '';

			if ($is_salesOrder == 1) {
				$sales_order_cond = ' and is_sales=1';
			} else {
				$sales_order_cond = '';
			}

			if($receive_basis == 3){
				$cum_issu_return_sql = "select a.po_breakdown_id, a.prod_id,b.issue_id,sum(b.cons_quantity) return_qnty,c.booking_no req_no from order_wise_pro_details a,inv_transaction b,inv_receive_master c where a.trans_id=b.id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and a.issue_purpose!=2 $po_cond_iss $sales_order_cond and a.prod_id=$txt_prod_id and a.entry_form ='9' and a.trans_type=4 group by a.po_breakdown_id, a.prod_id,b.issue_id,c.booking_no";
				$cum_issu_return_res = sql_select($cum_issu_return_sql);
				$cum_issue_return_array=array();
				foreach ($cum_issu_return_res as $row) {
					$cum_issue_return_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('issue_id')]][$row[csf('req_no')]] += $row[csf('return_qnty')];
				}

				$cum_issu_sql = "select a.po_breakdown_id, a.prod_id,b.requisition_no,b.mst_id issue_id,
				sum(CASE WHEN a.entry_form ='3' and a.trans_type=2 THEN quantity ELSE 0 END) AS issue_qnty,
				sum(case when a.entry_form ='3' and a.trans_type=2 then returnable_qnty else 0 end) as returnable_qnty,
				sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
				sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn
				from order_wise_pro_details a,inv_transaction b
				where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and a.issue_purpose!=2 and b.status_active=1
				$po_cond_iss $sales_order_cond and a.prod_id=$txt_prod_id
				group by a.po_breakdown_id, a.prod_id,b.requisition_no,b.mst_id";
				$cum_issu_sql_res = sql_select($cum_issu_sql);

				$issue_return_qnty=0;
				foreach ($cum_issu_sql_res as $row) {
					$issue_return_qnty = $cum_issue_return_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('issue_id')]][$row[csf('requisition_no')]];
					$cum_issue_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]] += $row[csf('issue_qnty')] + $row[csf('transfer_in_qnty_yarn')] - $issue_return_qnty - $row[csf('transfer_out_qnty_yarn')];
					$cum_returnable_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]] += $row[csf('returnable_qnty')];
					$value = ($row[csf('issue_qnty')] * $product_arr[$row[csf('prod_id')]]) + ($row[csf('transfer_in_qnty_yarn')] * $product_arr[$row[csf('prod_id')]]) - ($issue_return_qnty * $product_arr[$row[csf('prod_id')]]) - ($row[csf('transfer_out_qnty_yarn')] * $product_arr[$row[csf('prod_id')]]);
					$cum_issue_val_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]] += $value;
				}
			}else{
				if($entry_form == 41 || $entry_form == 125){
					$cum_issu_sql = "select a.po_breakdown_id, a.prod_id, sum(case when a.entry_form ='3' and a.trans_type=2 then a.quantity else 0 end) as issue_qnty,
					sum(case when a.entry_form ='3' and a.trans_type=2 then a.returnable_qnty else 0 end) as returnable_qnty,
					sum(case when a.entry_form ='9' and a.trans_type=4 then a.quantity else 0 end) as return_qnty,
					sum(case when a.entry_form ='11' and a.trans_type=5 then a.quantity else 0 end) as transfer_in_qnty_yarn,
					sum(case when a.entry_form ='11' and a.trans_type=6 then a.quantity else 0 end) as transfer_out_qnty_yarn,c.issue_number
					from order_wise_pro_details a,inv_transaction b,inv_issue_master c
					where a.status_active=1 and a.is_deleted=0 and a.trans_id=b.id and b.mst_id=c.id and c.issue_basis=1 and c.issue_purpose=2 $po_cond_iss $sales_order_cond and c.booking_no='$booking_no'
					group by a.po_breakdown_id, a.prod_id,c.issue_number";
				}else{
					$cum_issu_sql = "select po_breakdown_id, prod_id,
					sum(CASE WHEN entry_form ='3' and trans_type=2 THEN quantity ELSE 0 END) AS issue_qnty,
					sum(case when entry_form ='3' and trans_type=2 then returnable_qnty else 0 end) as returnable_qnty,
					sum(CASE WHEN entry_form ='9' and trans_type=4 THEN quantity ELSE 0 END) AS return_qnty,
					sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
					sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn
					from order_wise_pro_details where status_active=1 and is_deleted=0 $po_cond_iss $sales_order_cond group by po_breakdown_id, prod_id";
				}
				/*$cum_issu_sql = "select po_breakdown_id, prod_id,
				sum(CASE WHEN entry_form ='3' and trans_type=2 THEN quantity ELSE 0 END) AS issue_qnty,
				sum(case when entry_form ='3' and trans_type=2 then returnable_qnty else 0 end) as returnable_qnty,
				sum(CASE WHEN entry_form ='9' and trans_type=4 THEN quantity ELSE 0 END) AS return_qnty,
				sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
				sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn
				from order_wise_pro_details where status_active=1 and is_deleted=0 $po_cond_iss $sales_order_cond group by po_breakdown_id, prod_id";*/
				$cum_issu_sql_res = sql_select($cum_issu_sql);
				foreach ($cum_issu_sql_res as $row) {
					if($entry_form == 94){
						$cum_issue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]] += $row[csf('issue_qnty')] + $row[csf('transfer_in_qnty_yarn')] - $row[csf('return_qnty')] - $row[csf('transfer_out_qnty_yarn')];
						$cum_returnable_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]] += $row[csf('returnable_qnty')];
					}else{
						$cum_issue_array[$row[csf('po_breakdown_id')]] += $row[csf('issue_qnty')] + $row[csf('transfer_in_qnty_yarn')] - $row[csf('return_qnty')] - $row[csf('transfer_out_qnty_yarn')];
						$cum_returnable_array[$row[csf('po_breakdown_id')]] += $row[csf('returnable_qnty')];
					}

					$value = ($row[csf('issue_qnty')] * $product_arr[$row[csf('prod_id')]]) + ($row[csf('transfer_in_qnty_yarn')] * $product_arr[$row[csf('prod_id')]]) - ($row[csf('return_qnty')] * $product_arr[$row[csf('prod_id')]]) - ($row[csf('transfer_out_qnty_yarn')] * $product_arr[$row[csf('prod_id')]]);
                    if($entry_form != 135){//yarn dyeing sales order
                    	if($entry_form == 94){
                    		$cum_issue_val_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]] += $value;
                    	}else{
                    		$cum_issue_val_array[$row[csf('po_breakdown_id')]] += $value;
                    	}
                    }else{
                    	$cum_issue_val_array[$row[csf('po_breakdown_id')]] = 0;
                    }
                }
            }

            if ($receive_basis == 2 || $receive_basis == 3) {
            	if ($receive_basis != 3) {
            		?>
            		<table style="margin-left:50px" cellpadding="0" cellspacing="0" width="800" border="1" rules="all"
            		class="rpt_table">
            		<thead>
            			<th>Buyer</th>
            			<th>Search By</th>
            			<th>Search</th>
            			<th>
            				<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
            				class="formbutton"/>
            				<input type="hidden" name="po_id" id="po_id" value="">
            			</th>
            		</thead>
            		<tr class="general">
            			<td align="center">
            				<?
            				echo create_drop_down("cbo_buyer_name", 180, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", "");
            				?>
            			</td>
            			<td align="center">
            				<?
            				$search_by_arr = array(1 => "PO No", 2 => "Job No", 3 => "File No", 4 => "Internal Ref No");
            				echo create_drop_down("cbo_search_by", 170, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
            				?>
            			</td>
            			<td align="center">
            				<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
            				id="txt_search_common"/>
            			</td>
            			<td align="center">
            				<input type="button" name="button2" class="formbutton" value="Show"
            				onClick="fn_show_check()" style="width:100px;"/>
            			</td>
            		</tr>
            	</table>
            	<?
            }

            ?>
            <div id="search_div" style="margin-top:2px">
            	<?
            	if ($all_po_id != "" || $po_id != "") {
            		?>
            		<div style="width:930px; margin-top:2px" align="center">
            			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300"
            			align="center">
            			<thead>
            				<th>Total Issue Qnty</th>
            				<th>Total Returnable Qnty</th>
            				<th>Distribution Method</th>
            			</thead>
            			<tr class="general">
            				<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty"
            					class="text_boxes_numeric"
            					value="<? if ($prev_method == 1) echo $issueQnty; ?>" style="width:120px"
            					onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)">
            				</td>
            				<td><input type="text" name="txt_prop_retn_qnty" id="txt_prop_retn_qnty"
            					class="text_boxes_numeric"
            					value="<? if ($prev_method == 1) echo $retnQnty; ?>" style="width:120px"
            					onBlur="distribute_retn_qnty(document.getElementById('cbo_distribiution_method').value)"
            					<?php echo ($receive_basis == 3)?"disabled='disabled'":'';?> >
            				</td>
            				<td>
            					<?
            					$distribiution_method = array(1 => "Proportionately", 2 => "Manually");
            					echo create_drop_down("cbo_distribiution_method", 160, $distribiution_method, "", 0, "--Select--", $prev_method, "distribute_qnty(this.value);distribute_retn_qnty(this.value);", 0);
            					?>
            				</td>
            			</tr>
            		</table>
            	</div>
            	<div style="margin-left:5px; margin-top:2px">
            		<?
					$distribute_qnty_variable="";
					if(trim($cbo_company_id)!="")
					{
            		$distribute_qnty_variable = return_field_value("distribute_qnty", "variable_settings_production", "company_name='$cbo_company_id' and variable_list=5 and status_active=1 and is_deleted=0", "distribute_qnty");
					}
            		?>
            		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="980">
            			<thead>
            				<th width="120"><?php echo ($is_salesOrder==1)?"Sales Order No":"PO No"?></th>
            				<th width="80">Internal Ref</th>
            				<th width="80">File No</th>
            				<? if($distribute_qnty_variable == 1) {?>
            				<th width="70">Distribution Qty</th>
            				<? } ?>
            				<th width="70">Req. Qty</th>
            				<th width="70">Req. Value</th>
            				<th width="70">PO Qty</th>
            				<th width="70">Cum. Iss. Qty</th>
            				<th width="70">Cum. Iss. Value</th>
            				<th width="70">Balance Value</th>
            				<th width="85">Issue Qty</th>
            				<th>Returnable Qty</th>
            			</thead>
            		</table>
            		<div style="width:1000px; max-height:200px; overflow-y:scroll" id="list_container"
            		align="left">
            		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all"
            		width="970" id="tbl_list_search">
            		<?
            		if ($po_id == "") $po_id = $all_po_id; else $po_id = $po_id;
            		$i = 1;
            		$tot_po_qnty = 0;

            		$allocation_sql = "select a.booking_no,b.po_break_down_id,a.qnty total_allocation_qnty,b.qnty order_wise_allocation from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and a.item_id=$txt_prod_id and b.po_break_down_id in($po_id) and a.status_active=1 and b.status_active=1";
            		$allocation_result = sql_select($allocation_sql);
            		$order_wise_allocation_arr = array();
            		$total_allocation_qnty=0;
            		foreach ($allocation_result as $allocation_row) {
            			$order_wise_allocation_arr[$allocation_row[csf("po_break_down_id")]] = $allocation_row[csf("order_wise_allocation")];
            			$total_allocation_qnty += $allocation_row[csf("order_wise_allocation")];
            		}
            		if ($is_salesOrder == 1) {
            			$po_sql = "select a.id, a.job_no as po_number, sum(b.grey_qty) as po_quantity, 1 as total_set_qnty
            			from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.id in ($po_id) group by a.id, a.job_no";
            		} else {
            			if ($po_id != "") {
            				$po_sql = "select b.id, b.po_number,b.file_no,b.grouping, b.po_quantity, a.total_set_qnty
            				from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id)";
            			}
            		}

            		if($receive_basis == 3){
            			$distribiution_info = sql_select("select requisition_no,po_break_down_id,prod_id,sum(distribution_qnty) distribution_qnty from  ppl_yarn_req_distribution where requisition_no=$req_no and prod_id=$txt_prod_id and status_active=1 group by requisition_no,po_break_down_id,prod_id");
            			foreach ($distribiution_info as $dist_row) {
            				$order_wise_dist[$dist_row[csf("requisition_no")]][$dist_row[csf("po_break_down_id")]] = $dist_row[csf("distribution_qnty")];
            			}
            		}

            		$explSaveData = explode(",", $save_data);
            		$po_dataArray = array();
            		foreach ($explSaveData as $val) {
            			$woQnty = explode("**", $val);
            			$po_dataArray[$woQnty[0]]['qnty'] = $woQnty[1];
            			$po_dataArray[$woQnty[0]]['retQnty'] = $woQnty[2];
            		}
            		$nameArray = sql_select($po_sql);
            		$total_req_qnty=0;
            		foreach ($nameArray as $row) {
            			if ($i % 2 == 0)
            				$bgcolor = "#E9F3FF";
            			else
            				$bgcolor = "#FFFFFF";
            			if ($is_salesOrder != 1) {
            				$po_qnty_in_pcs = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
            				$tot_po_qnty += $po_qnty_in_pcs;
            			}else{
            				$po_qnty_in_pcs = 0;
            				$tot_po_qnty += 0;
            			}

            			$qnty = $po_dataArray[$row[csf('id')]]['qnty'];

            			$returnQnty = $po_dataArray[$row[csf('id')]]['retQnty'];
            			if($receive_basis ==3 ){
            				$balance_qty = '';
            				$balance_val = '';
            				$requisition_qnty = number_format($req_qty_array[$req_no], 2, '.', '');
            				if(count($order_wise_allocation_arr) > 1){
            					$req_qnty = ($requisition_qnty/$total_allocation_qnty)*$order_wise_allocation_arr[$row[csf("id")]];
            				}else{
            					$req_qnty = $requisition_qnty;
            				}
            				$total_req_qnty += $req_qnty;
            			}else{
            				$balance_qty = $req_qty_array[$row[csf('id')]] - $cum_issue_array[$row[csf('id')]];
            				$balance_val = $req_val_array[$row[csf('id')]] - $cum_issue_val_array[$row[csf('id')]];
            				$req_qnty = number_format($req_qty_array[$row[csf('id')]], 2, '.', '');
            			}
            			$allocation_q=$order_wise_allocation_arr[$row[csf("id")]];
            			if($distribute_qnty_variable == 1){
            				$distribiution_qnty = $order_wise_dist[$req_no][$row[csf("id")]];
            			}else{
            				$distribiution_qnty = $program_qnty_arr[$row[csf('id')]][$req_no];
            			}
            			?>
            			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
            				<td width="120" align="center">
            					<? echo $row[csf('po_number')]; ?>
            					<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>"
            					value="<? echo $row[csf('id')]; ?>">
            					<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>"
            					value="1">
            				</td>
            				<td width="80">
            					<p><? echo $row[csf('grouping')]; ?></p>
            				</td>
            				<td width="80"><? echo $row[csf('file_no')]; ?></td>
            				<? if($distribute_qnty_variable == 1) {?>
            				<td width="70" align="right"><? echo $distribiution_qnty; ?></td>
            				<? } ?>
            				<td width="70" align="right" title="(Requisition Quantity[<?php echo $requisition_qnty;?>] &#247; Total Allocation[<?php echo $total_allocation_qnty;?>]) &#215; Order Wise Allocation[<?php echo $allocation_q;?>]">
            					<? echo number_format($req_qnty, 2, '.', ''); ?>
            					<input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo number_format($req_qnty, 2, '.', ''); ?>">
            				</td>
            				<td width="70" align="right">
            					<?php
            					if ($is_salesOrder != 1) {
            						echo number_format($req_val_array[$row[csf('id')]], 2, '.', '');
            					}
            					?>
            				</td>
            				<td width="70" align="right">
            					<? if ($is_salesOrder != 1) {
            						echo number_format($po_qnty_in_pcs, 2, '.', ''); ?>
            						<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>"
            						value="<? echo number_format($po_qnty_in_pcs, 2, '.', ''); ?>">
            						<? }?>
            					</td>
            					<td width="70" align="right">
            						<? if ($receive_basis == 3) {
            							echo number_format($cum_issue_array[$row[csf('id')]][$req_no], 2, '.', ''); ?>
            							<input type="hidden" id="txt_cum_issue_<? echo $i; ?>" value="<? echo $cum_issue_array[$row[csf('id')]][$req_no]; ?>" />
            							<input type="hidden" id="txt_cum_issue_fixed_<? echo $i; ?>" value="<? echo $cum_issue_array[$row[csf('id')]][$req_no]; ?>" />
            							<input type="hidden" id="txt_cum_returnable_<? echo $i; ?>" value="<? echo $cum_returnable_array[$row[csf('id')]][$req_no]; ?>" />
            							<? }else{
            								echo number_format($cum_issue_array[$row[csf('id')]], 2, '.', ''); ?>
            								<input type="hidden" id="txt_cum_issue_<? echo $i; ?>" value="<? echo $cum_issue_array[$row[csf('id')]]; ?>" />
            								<input type="hidden" id="txt_cum_returnable_<? echo $i; ?>" value="<? echo $cum_returnable_array[$row[csf('id')]]; ?>" />
            								<?php }?>
            							</td>
            							<td width="70" align="right">
            								<? echo number_format($cum_issue_val_array[$row[csf('id')]], 2, '.', ''); ?>
            							</td>
            							<td width="70" align="right">
            								<? echo number_format($balance_val, 2, '.', ''); ?>
            							</td>
            							<td width="85" align="center">
            								<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>"
            								class="text_boxes_numeric" style="width:70px"
            								placeholder="<? echo number_format($balance_qty, 2, '.', ''); ?>"
            								value="<? echo $qnty; ?>" onKeyUp="calculate_returnable_qnty(<?php echo $i;?>,this.value)">
            								<input type="hidden" id="txt_plan_qnty_<? echo $i; ?>" value="<?php echo $distribiution_qnty;?>">
            								<?php
            								if($update_id !=""){
            									?>
            									<input type="hidden" id="hdn_grey_qnty_<? echo $i; ?>" value="<? echo $qnty; ?>">
            									<input type="hidden" id="hdn_grey_qnty_fixed_<? echo $i; ?>" value="<? echo $qnty; ?>">
            									<?php }else{
            										?>
            										<input type="hidden" id="hdn_grey_qnty_<? echo $i; ?>" value="">
            										<?
            									} ?>
            								</td>
            								<td align="center">
            									<input type="text" name="txtReturnQnty[]"
            									id="txtReturnQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $returnQnty; ?>">
            									<input type="hidden" id="hdnReturnQnty_<? echo $i; ?>" value="<? echo $returnQnty; ?>">
            								</td>
            							</tr>
            							<?
            							$i++;
            						}
            						?>
            						<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes"
            						value="<? echo $tot_po_qnty; ?>">
            						<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes"
            						value="<? echo $total_req_qnty; ?>">
            					</table>
            				</div>
            				<table width="950" id="table_id">
            					<tr>
            						<td align="center">
            							<input type="button" name="close" class="formbutton" value="Close"
            							id="main_close" onClick="fnc_close();" style="width:100px"/>
            						</td>
            					</tr>
            				</table>
            			</div>
            			<? } ?>
            		</div>
            		<?
            	} else {
            		?>
            		<div style="width:930px; margin-top:2px" align="center">
            			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300"
            			align="center">
            			<thead>
            				<th>Total Issue Qnty</th>
            				<th>Total Returnable Qnty</th>
            				<th>Distribution Method</th>
            			</thead>
            			<tr class="general">
            				<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty"
            					class="text_boxes_numeric" value="<? if ($prev_method == 1) echo $issueQnty; ?>"
            					style="width:120px"
            					onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)">
            				</td>
            				<td><input type="text"
            					name="txt_prop_retn_qnty" <? if ($extra_quantity > 0 && $receive_basis == 1 && $issue_purpose != 2) echo "disabled"; ?>
            					id="txt_prop_retn_qnty" class="text_boxes_numeric"
            					value="<? if ($prev_method == 1) echo $retnQnty; ?>" style="width:120px"
            					onBlur="distribute_retn_qnty(document.getElementById('cbo_distribiution_method').value)">
            				</td>
            				<td>
            					<?
            					$distribiution_method = array(1 => "Proportionately", 2 => "Manually");
            					echo create_drop_down("cbo_distribiution_method", 160, $distribiution_method, "", 0, "--Select--", $prev_method, "distribute_qnty(this.value);distribute_retn_qnty(this.value);", 0);
            					?>
            				</td>
            			</tr>
            		</table>
            	</div>
            	<div style="margin-left:5px; margin-top:2px">
            		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="910">
            			<thead>
            				<th width="120"><?php echo ($is_salesOrder==1)?"Sales Order No":"PO No"?></th>
            				<th width="80">Internal Ref</th>
            				<th width="80">File No</th>
            				<th width="70">Req. Qnty</th>
            				<th width="70">Req. Value</th>
            				<th width="70">PO Qnty</th>
            				<th width="70">Cum. Iss. Qty</th>
            				<th width="70">Cum. Iss. Value</th>
            				<th width="70">Balance Value</th>
            				<th width="85">Issue Qnty</th>
            				<th>Returnable Qnty</th>
            			</thead>
            		</table>
            		<div style="width:930px; max-height:150px; overflow-y:scroll" id="list_container" align="left">
            			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="910" id="tbl_list_search">
            				<?
            				$i = 1;
            				$tot_po_qnty = 0;

            				if ($type == 1 && $po_id != "") {
            					if ($booking_no == "") {
            						$po_sql = "select b.id, b.po_number, b.po_quantity,b.file_no,b.grouping, a.total_set_qnty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id)";
            					} else {
            						$po_sql = "select b.id, b.po_number,b.file_no,b.grouping, b.po_quantity, a.total_set_qnty from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no='$booking_no' and b.id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.po_number,b.file_no,b.grouping, b.po_quantity, a.total_set_qnty";
            					}
            				} else {

            					if ($issue_purpose == 2 || $issue_purpose == 15 || $issue_purpose == 38 || $issue_purpose == 46 || $issue_purpose == 7) {
            						$entry_form = return_field_value("entry_form", "wo_yarn_dyeing_mst", "ydw_no='$booking_no'", "entry_form");
            						$entry_form_cond = ($entry_form != "")?" and entry_form=$entry_form":"";
            						$yarn_dyeing_info=sql_select("select entry_form,is_sales from wo_yarn_dyeing_mst where status_active=1 and ydw_no='$booking_no' and company_id=$cbo_company_id and entry_form=$entry_form");
            						$entry_form = $yarn_dyeing_info[0][csf("entry_form")];
            						$is_sales = $yarn_dyeing_info[0][csf("is_sales")];
            						if($entry_form == 135 && $is_sales==1){
            							$po_sql = "select a.id, a.job_no as po_number, sum(b.grey_qty) as po_quantity, 1 as total_set_qnty
            							from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no ='$job_no' group by a.id, a.job_no";
            						}else if($entry_form == 94 && $is_sales==1){
            							$po_sql = "select a.id, a.job_no as po_number, sum(b.grey_qty) as po_quantity, 1 as total_set_qnty
            							from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no ='$job_no' group by a.id, a.job_no";
            						}else{
            							$po_sql = "select b.id, b.po_number,b.file_no,b.grouping, b.po_quantity, a.total_set_qnty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            						}
            					} else {
            						if ($booking_no == "") {
            							$po_sql = "select b.id, b.po_number,b.file_no,b.grouping, b.po_quantity, a.total_set_qnty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            						} else {
            							$po_sql = "select b.id, b.po_number,b.file_no,b.grouping, b.po_quantity, a.total_set_qnty from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.po_number,b.file_no,b.grouping, b.po_quantity, a.total_set_qnty";
            						}
            					}
            				}

            				if ($save_string == "" && $type == 1) $save_data = $prevQnty;
            				$explSaveData = explode(",", $save_data);

            				$po_dataArray = array();
            				foreach ($explSaveData as $val) {
            					$woQnty = explode("**", $val);
            					$po_dataArray[$woQnty[0]]['qnty'] = $woQnty[1];
            					$po_dataArray[$woQnty[0]]['retQnty'] = $woQnty[2];
            				}

            				$nameArray = sql_select($po_sql);
            				foreach ($nameArray as $row) {
            					if ($i % 2 == 0)
            						$bgcolor = "#E9F3FF";
            					else
            						$bgcolor = "#FFFFFF";

            					$po_qnty_in_pcs = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
            					$tot_po_qnty += $po_qnty_in_pcs;

            					$qnty = $po_dataArray[$row[csf('id')]]['qnty'];
            					$returnQnty = $po_dataArray[$row[csf('id')]]['retQnty'];

            					if($receive_basis ==3 || $entry_form == 135){
            						$balance_qty = '';
            						$balance_val = '';
            					}else{
            						if($entry_form == 94){
            							$balance_qty = $req_qty_array[$row[csf('id')]][$txt_prod_id] - $cum_issue_array[$row[csf('id')]][$txt_prod_id];
            							$balance_val = $req_val_array[$row[csf('id')]][$txt_prod_id] - $cum_issue_val_array[$row[csf('id')]][$txt_prod_id];
            						}else{
            							$balance_qty = $req_qty_array[$row[csf('id')]] - $cum_issue_array[$row[csf('id')]];
            							$balance_val = $req_val_array[$row[csf('id')]] - $cum_issue_val_array[$row[csf('id')]];
            						}
            					}
            					?>
            					<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
            						<td width="120">
            							<p><? echo $row[csf('po_number')]; ?></p>
            							<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>"
            							value="<? echo $row[csf('id')]; ?>">
            							<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>"
            							value="1">
            						</td>
            						<td width="80" align="right">
            							<p><? echo $row[csf('grouping')]; ?>&nbsp;</p>
            						</td>
            						<td width="80" align="right">
            							<p><? echo $row[csf('file_no')]; ?>&nbsp;</p>
            						</td>
            						<td width="70" align="right">
            							<?
            							if($entry_form == 94){
            								echo number_format($req_qty_array[$row[csf('id')]][$txt_prod_id], 2, '.', '');
            							}else{
            								echo number_format($req_qty_array[$row[csf('id')]], 2, '.', '');
            							}
            							?>
            						</td>
            						<td width="70" align="right">
            							<?
            							if($entry_form == 94){
            								echo number_format($req_val_array[$row[csf('id')]][$txt_prod_id], 2, '.', '');
            							}else{
            								echo number_format($req_qty_array[$row[csf('id')]], 2, '.', '');
            							}
            							?>
            						</td>
            						<td width="70" align="right">
            							<? echo number_format($po_qnty_in_pcs, 2, '.', ''); ?>
            							<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>"
            							value="<? echo number_format($po_qnty_in_pcs, 2, '.', ''); ?>">
            						</td>
            						<td width="70" align="right">
            							<?
            							if($entry_form == 94){
            								echo number_format($cum_issue_array[$row[csf('id')]][$txt_prod_id], 2, '.', '');
            							}else{
            								echo number_format($cum_issue_array[$row[csf('id')]], 2, '.', '');
            							}
            							?>
            						</td>
            						<td width="70" align="right">
            							<?
            							if($entry_form == 94){
            								echo number_format($cum_issue_val_array[$row[csf('id')]][$txt_prod_id], 2, '.', '');
            							}else{
            								echo number_format($cum_issue_val_array[$row[csf('id')]], 2, '.', '');
            							}
            							?>
            						</td>
            						<td width="70" align="right">
            							<? echo number_format($balance_val, 2, '.', ''); ?>
            						</td>
            						<td width="85" align="center">
            							<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>"
            							class="text_boxes_numeric" style="width:70px"
            							placeholder="<? echo number_format($balance_qty, 2, '.', ''); ?>"
            							value="<? echo $qnty; ?>" onBlur="fn_bal_check(<? echo $i; ?>)" readonly>
            						</td>
            						<td align="center">
            							<input type="text" name="txtReturnQnty[]" id="txtReturnQnty_<? echo $i; ?>"
            							class="text_boxes_numeric" style="width:70px"
            							value="<? echo $returnQnty; ?>" readonly>
            						</td>
            					</tr>
            					<?
            					$i++;
            				}
            				?>
            				<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes"
            				value="<? echo $tot_po_qnty; ?>">
            			</table>
            		</div>
            		<table width="950" id="table_id">
            			<tr>
            				<td align="center">
            					<input type="button" name="close" class="formbutton" value="Close" id="main_close"
            					onClick="fnc_close();" style="width:100px"/>
            				</td>
            			</tr>
            		</table>
            	</div>
            	<?
            }

            if ($type != 1)
            {
            	?>
            </fieldset>
        </form>
        <?
    }
    ?>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_po_search_list_view") {
	$data = explode("_", $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$search_con = "";
	if ($search_by == 1 && $search_string != "")
		$search_con = " and b.po_number like '%$search_string%'";
	else if ($search_by == 2 && $search_string != "")
		$search_con = " and a.job_no like '%$search_string%'";
	else if ($search_by == 3 && $search_string != "")
		$search_con = " and b.file_no like '%$search_string%'";
	else if ($search_by == 4 && $search_string != "")
		$search_con = " and b.grouping like '%$search_string%'";

	$company_id = $data[2];
	$buyer_id = $data[3];
	$all_po_id = $data[4];
	$receiveBasis = $data[5];

	if ($all_po_id != "")
		$po_id_cond = " or b.id in($all_po_id)";
	else
		$po_id_cond = "";

	$hidden_po_id = explode(",", $all_po_id);
	if ($buyer_id == 0) {
		echo "<b>Please Select Buyer First</b>";
		die;
	}

	if ($receiveBasis == 1) {
		$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date, b.grouping, b.file_no
		from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$buyer_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date, a.job_no, a.style_ref_no, a.order_uom,b.grouping,b.file_no";
	} else {
		$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date, b.grouping, b.file_no
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date, a.job_no, a.style_ref_no, a.order_uom, b.grouping, b.file_no";
	}
	//echo $sql;die;
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="120">Job No</th>
				<th width="150">Style No</th>
				<th width="140">PO No</th>
				<th width="90">Internal Ref.</th>
				<th width="90">File No</th>
				<th width="90">PO Quantity</th>
				<th width="50">UOM</th>
				<th>Shipment Date</th>
			</thead>
		</table>
		<div style="width:930px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			$po_row_id = '';
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				if (in_array($selectResult[csf('id')], $hidden_po_id)) {
					if ($po_row_id == "") $po_row_id = $i; else $po_row_id .= "," . $i;
				}

				$po_qnty_in_pcs = $selectResult[csf('po_quantity')] * $selectResult[csf('total_set_qnty')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="40" align="center"><?php echo "$i"; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>"
						value="<? echo $selectResult[csf('id')]; ?>"/>
					</td>
					<td width="120"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
					<td width="150"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
					<td width="140"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
					<td width="90"><p><? echo $selectResult[csf('grouping')]; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
					<td width="90" align="right"><? echo $po_qnty_in_pcs; ?></td>
					<td width="50" align="center">
						<p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
						<td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
			</table>
		</div>
		<table width="820" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
							Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="show_grey_prod_recv();" class="formbutton"
							value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
	if ($issue_date < $max_recv_date)
	{
		echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
		disconnect($con);die;
	}

	// check variable settings if allocation is available or not
	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=18 and item_category_id = 1");

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		//---------------Check Duplicate product in Same return number ------------------------//
		$requigitionCond = "";
		if (str_replace("'", "", $cbo_basis) == 3) $requigitionCond = " and a.buyer_id=" . $cbo_buyer_name . " and b.requisition_no=" . $txt_req_no . "";

		if (str_replace("'", "", $cbo_issue_purpose) != 2) {
			$duplicate = is_duplicate_field("b.id", "inv_issue_master a, inv_transaction b", "a.id=b.mst_id and a.issue_number=$txt_system_no and b.prod_id=$txt_prod_id and b.transaction_type=2 $requigitionCond");

			if ($duplicate == 1 && str_replace("'", "", $txt_system_no) != "") {
				echo "20**Duplicate Product is Not Allow in Same Issue Number.";
				disconnect($con);
				die;
			}
		}

		if ( str_replace("'", "", $txt_issue_qnty) > str_replace("'", "", $txt_current_stock) )
		{
			echo "11**Issue Quantity can not be greater than Current Stock quantity";
			disconnect($con);die;
		}

		//------------------------------Check Brand END---------------------------------------//
		if (str_replace("'", "", $txt_system_no) != "") //new insert cbo_ready_to_approved
		{
			$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no=$txt_system_no and status_active=1 and is_deleted=0", "sys_number");
			if ($check_in_gate_pass != "") {
				echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";
				disconnect($con);die;
			}
		}

		//yarn issue master table entry here START---------------------------------------//
		if (str_replace("'", "", $txt_system_no) == "") //new insert cbo_ready_to_approved
		{
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";//defined Later

			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'YIS',3,date("Y",time()),1 ));

			$field_array_mst = "id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, booking_id, booking_no, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, remarks,ready_to_approve, inserted_by, insert_date";
			$data_array_mst = "(" . $id . ",'" . $new_mrr_number[1] . "','" . $new_mrr_number[2] . "','" . $new_mrr_number[0] . "'," . $cbo_basis . "," . $cbo_issue_purpose . ",3,1," . $cbo_company_id . "," . $cbo_location_id . "," . $cbo_supplier . "," . $cbo_store_name . "," . $cbo_buyer_name . "," . $txt_buyer_job_no . "," . $txt_style_ref . "," . $txt_booking_id . "," . $txt_booking_no . "," . $txt_issue_date . "," . $cbo_sample_type . "," . $cbo_knitting_source . "," . $cbo_knitting_company . "," . $txt_challan_no . "," . $cbo_loan_party . "," . $txt_remarks . "," . $cbo_ready_to_approved . ",'" . $user_id . "','" . $pc_date_time . "')";
		} else //update
		{
			$new_mrr_number[0] = str_replace("'", "", $txt_system_no);
			$id = return_field_value("id", "inv_issue_master", "issue_number=$txt_system_no");
			$field_array_mst = "issue_basis*issue_purpose*entry_form*item_category*company_id*location_id*supplier_id*store_id*buyer_id*buyer_job_no*style_ref*booking_id*booking_no*issue_date*sample_type*knit_dye_source*knit_dye_company*challan_no*loan_party*remarks*ready_to_approve*updated_by*update_date";
			$data_array_mst = "" . $cbo_basis . "*" . $cbo_issue_purpose . "*3*1*" . $cbo_company_id . "*" . $cbo_location_id . "*" . $cbo_supplier . "*" . $cbo_store_name . "*" . $cbo_buyer_name . "*" . $txt_buyer_job_no . "*" . $txt_style_ref . "*" . $txt_booking_id . "*" . $txt_booking_no . "*" . $txt_issue_date . "*" . $cbo_sample_type . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . $txt_challan_no . "*" . $cbo_loan_party . "*" . $txt_remarks . "*" . $cbo_ready_to_approved . "*'" . $user_id . "'*'" . $pc_date_time . "'";
			$id = str_replace("'", "", $update_id_mst);
		}
		//yarn issue master table entry here END---------------------------------------//
		//product master table information
		$sql = sql_select("select supplier_id,avg_rate_per_unit,current_stock,stock_value,allocated_qnty,available_qnty from product_details_master where id=$txt_prod_id and item_category_id=1");
		$avg_rate = $stock_qnty = $stock_value = $allocated_qnty = $available_qnty = 0;
		$supplier_id_for_tran = '';
		foreach ($sql as $result) {
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
			$allocated_qnty = $result[csf("allocated_qnty")];
			$available_qnty = $result[csf("available_qnty")];
			$supplier_id_for_tran = $result[csf("supplier_id")];
		}

		/******** original product id check start ********/

		$origin_prod_id = return_field_value("origin_prod_id", "inv_transaction", "prod_id=$txt_prod_id and status_active=1 and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1", "origin_prod_id");

		/******** original product id check end ********/

		//inventory TRANSACTION table data entry START----------------------------------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$txt_issue_qnty = str_replace("'", "", $txt_issue_qnty);
		$issue_stock_value = $avg_rate * $txt_issue_qnty;
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,requisition_no,receive_basis,company_id,supplier_id,prod_id,origin_prod_id,dyeing_color_id,item_category,transaction_type,transaction_date,store_id,brand_id,cons_uom,cons_quantity,return_qnty,item_return_qty,cons_rate,cons_amount,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,room,rack,self,floor_id,using_item,job_no,inserted_by,insert_date,btb_lc_id";
		$data_array_trans = "(" . $transactionID . "," . $id . "," . $txt_req_no . "," . $cbo_basis . "," . $cbo_company_id . ",'" . $supplier_id_for_tran . "'," . $txt_prod_id . ",'" . $origin_prod_id . "'," . $cbo_dyeing_color . ",1,2," . $txt_issue_date . "," . $cbo_store_name . "," . $cbo_brand . "," . $cbo_uom . "," . $txt_issue_qnty . "," . $txt_returnable_qty . "," . $extra_quantity . "," . $avg_rate . "," . $issue_stock_value . "," . $txt_no_bag . "," . $txt_no_cone . "," . $txt_weight_per_bag . "," . $txt_weight_per_cone . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_floor . "," . $cbo_item . "," . $job_no . ",'" . $user_id . "','" . $pc_date_time . "'," . $txt_btb_lc_id . ")";

		//inventory TRANSACTION table data entry  END----------------------------------------------------------//
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate = 0;
		$data_array = "";
		$updateID_array = array();
		$update_data = array();
		$issueQnty = $txt_issue_qnty;
		// check variable settings issue method(LIFO/FIFO)
		$isLIFOfifo = '';
		$check_allocation = '';
		$sql_variable = sql_select("select store_method,allocation,variable_list from variable_settings_inventory where company_name=$cbo_company_id and variable_list in(17,18) and item_category_id=1 and status_active=1 and is_deleted=0");
		foreach ($sql_variable as $row) {
			if ($row[csf('variable_list')] == 17) {
				$isLIFOfifo = $row[csf('store_method')];
			} else if ($row[csf('variable_list')] == 18) {
				$check_allocation = $row[csf('allocation')];
			}
		}

		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";

		// Trans type: 1=>"Receive",4=>"Issue Return",5=>"Item Transfer Receive"
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 and status_active=1 order by transaction_date,id $cond_lifofifo");
		foreach ($sql as $result) {
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);
			if ($issueQntyBalance >= 0) {
				$amount = $issueQnty * $cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $transactionID . ",3," . $txt_prod_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $recv_trans_id;
				$update_data[$recv_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			} else if ($issueQntyBalance < 0) {
				//$issueQntyBalance = $balance_qnty+$issueQntyBalance; // adjust issue qnty
				//$issueQntyBalance = $issueQntyBalance-$balance_qnty;
				$issueQntyBalance = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $cons_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $transactionID . ",3," . $txt_prod_id . "," . $balance_qnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $recv_trans_id;
				$update_data[$recv_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		}//end foreach
		// LIFO/FIFO then END-----------------------------------------------//
		$mrrWiseIssueID = true;
		$upTrID = true;

		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$currentStock = $stock_qnty - $txt_issue_qnty;
		$StockValue = $stock_value - ($txt_issue_qnty * $avg_rate);
		//$avgRate	 	= number_format($StockValue/$currentStock,$dec_place[3],'.','');
		$avgRate = number_format($avg_rate, '.', '');
		//newly added code here =============================================
		//item allocation----------------------------------------------------
		$store_wise_sql = sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)- (case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_qnty from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name  and item_category=1 and status_active=1");

		// If allocation is allowed
		if ($variable_set_allocation == 1) {
			if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
				$store_balance = $allocated_qnty;
				$msg_qnt = "\nAllocation Quantity = ".$store_balance;
				$msg_label = "Allocation";
			} else if (str_replace("'", "", $cbo_basis) == 1 && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
				// Entry Page: 42=>"Yarn Dying Without Order",135=>"Yarn Dyeing Work Order Sales";
				if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {
					$store_balance = $available_qnty;
					$msg_label = "Available";
					$msg_qnt = "\nAvailable Quantity = ".$store_balance;
				} else {
					$store_balance = $allocated_qnty;
					$msg_label = "Allocation";
					$msg_qnt = "\nAllocation Quantity = ".$store_balance;
				}
			} else {
				$store_balance = $available_qnty;
				$msg_label = "Available";
				$msg_qnt = "\nAvailable Quantity = ".$store_balance;
			}
		} else {
			$store_balance = $available_qnty;
			$msg_label = "Available";
			$msg_qnt = "\nAvailable Quantity = ".$store_balance;
		}

		$availableChk = ($store_balance >= $txt_issue_qnty) ? true : false;
		$msg = "Issue Quantity can not be greater than $msg_label quantity.".$msg_qnt;

		if ($availableChk == false) {
			if ($db_type == 0) {
				mysql_query("ROLLBACK");
			} else {
				oci_rollback($con);
			}
			echo "11**" . $msg;
			//check_table_status($_SESSION['menu_id'], 0);
			disconnect($con);
			exit();
		}

		$allocated_qnty_balance = 0;
		$available_qnty_balance = 0;
		// if yarn allocation variable set to yes
		if ($variable_set_allocation == 1) {
			if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
				$allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
				$available_qnty_balance = $available_qnty;
			} else if (str_replace("'", "", $cbo_basis) == 1 && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
				// Entry Page: 42=>"Yarn Dying Without Order",135=>"Yarn Dyeing Work Order Sales";
				if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {
					$allocated_qnty_balance = $allocated_qnty;
					$available_qnty_balance = $available_qnty - $txt_issue_qnty;
				} else {
					$allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
					$available_qnty_balance = $available_qnty;
				}
			} else {
				$allocated_qnty_balance = $allocated_qnty;
				$available_qnty_balance = $available_qnty - $txt_issue_qnty;
			}
		} else {
			$allocated_qnty_balance = $allocated_qnty;
			$available_qnty_balance = $available_qnty - $txt_issue_qnty;
		}

		if ($allocated_qnty_balance == "") $allocated_qnty_balance = 0;
		if ($available_qnty_balance == "") $available_qnty_balance = 0;

		$field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";//*avg_rate_per_unit*
		$data_array_prod = "" . $txt_issue_qnty . "*" . $currentStock . "*" . number_format($StockValue, $dec_place[4], '.', '') . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		//*".$allocated_qnty_balance."*".$available_qnty_balance."$avgRate."*".
		//$prodUpdate 	= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,0);

		//------------------ product_details_master END--------------//
		//weighted and average rate END here-------------------------//

		$proportQ = true;
		$data_array_prop = "";
		$save_string = explode(",", str_replace("'", "", $save_data));
		if (count($save_string) > 0 && str_replace("'", "", $save_data) != "") {
			$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by,insert_date";
			//order_wise_pro_details table data insert START-----//
			$po_array = array();
			for ($i = 0; $i < count($save_string); $i++) {
				$order_dtls = explode("**", $save_string[$i]);
				$order_id = $order_dtls[0];
				$order_qnty = $order_dtls[1];
				$returnable_qnty = $order_dtls[2];

				if (array_key_exists($order_id, $po_array)) {
					$po_array[$order_id] += $order_qnty;
					$po_rt_array[$order_id] += $returnable_qnty;
				} else {
					$po_array[$order_id] = $order_qnty;
					$po_rt_array[$order_id] = $returnable_qnty;
				}
			}
			$i = 0;
			$is_salesOrder = 0;
			if (str_replace("'", "", $cbo_basis) == 3) {
				$is_salesOrder = return_field_value("a.is_sales", "ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b", "a.id=b.knit_id and b.requisition_no=$txt_req_no", "is_sales");
			}
			if (str_replace("'", "", $txt_entry_form) == 135) {
				$is_salesOrder = 1;
			}
			if(str_replace("'", "", $txt_entry_form) == 94){
				$yarn_dyeing_info=sql_select("select entry_form,is_sales from wo_yarn_dyeing_mst where status_active=1 and ydw_no=$txt_booking_no and company_id=$cbo_company_id");
				$is_salesOrder = 1;
			}
			foreach ($po_array as $key => $val) {
				if ($i > 0) $data_array_prop .= ",";
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_id = $key;
				$order_qnty = $val;
				$returnable_qnty = $po_rt_array[$key];
				$data_array_prop .= "(" . $id_proport . "," . $transactionID . ",2,3," . $order_id . "," . $txt_prod_id . "," . $order_qnty . "," . $cbo_issue_purpose . ",'" . $returnable_qnty . "','" . $is_salesOrder . "'," . $user_id . ",'" . $pc_date_time . "')";
				$i++;
			}
		}//end if
		//order_wise_pro_details table data insert END -----//

		if (str_replace("'", "", $cbo_basis) == 4) {
			$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by,insert_date";
			$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop = "(" . $id_proport . "," . $transactionID . ",2,3," . $txt_booking_id . "," . $txt_prod_id . "," . $txt_issue_qnty . "," . $cbo_issue_purpose . "," . $txt_returnable_qty . ",1," . $user_id . ",'" . $pc_date_time . "')";
		}

		//echo "20**".$rID." && ".$transID." && ".$prodUpdate." && ".$proportQ;
		if (str_replace("'", "", $txt_system_no) == "") {
			$rID = sql_insert("inv_issue_master", $field_array_mst, $data_array_mst, 0);
		} else {
			$rID = sql_update("inv_issue_master", $field_array_mst, $data_array_mst, "id", $id, 0);
		}

		$transID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
		if ($data_array != "") {
			$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array, $data_array, 0);
		}

		//transaction table stock update here------------------------//
		if (count($updateID_array) > 0) {
			$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array));
		}

		$prodUpdate = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $txt_prod_id, 0);

		if ($data_array_prop != "") {
			//echo "10**INSERT INTO order_wise_pro_details (".$field_array_proportionate.") VALUES ".$data_array_prop.""; die;
			$proportQ = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
		}

		//echo "10**INSERT INTO order_wise_pro_details (".$field_array_trans.") VALUES ".$data_array_trans.""; die;
		//echo "10**".$rID. "&&". $transID. "&&".$prodUpdate. "&&". $proportQ . "&&". $upTrID;die;
		if ($db_type == 0) {
			if ($rID && $transID && $prodUpdate && $proportQ && $mrrWiseIssueID && $upTrID) {
				mysql_query("COMMIT");
				echo "0**" . $new_mrr_number[0] . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_mrr_number[0] . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $transID && $prodUpdate && $proportQ && $mrrWiseIssueID && $upTrID) {
				oci_commit($con);
				echo "0**" . $new_mrr_number[0] . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**0";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no=$txt_system_no and status_active=1 and is_deleted=0", "sys_number");
		if ($check_in_gate_pass != "") {
			echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";
			disconnect($con);die;
		}

		$isLIFOfifo = '';
		$check_allocation = '';
		$sql_variable = sql_select("select store_method,allocation,variable_list from variable_settings_inventory where company_name=$cbo_company_id and variable_list in(17,18) and item_category_id=1 and status_active=1 and is_deleted=0");
		foreach ($sql_variable as $row) {
			if ($row[csf('variable_list')] == 17) {
				$isLIFOfifo = $row[csf('store_method')];
			} else if ($row[csf('variable_list')] == 18) {
				$check_allocation = $row[csf('allocation')];
			}
		}

		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$total_pre_issue_qty = return_field_value("sum(cons_quantity) total_issue", "inv_transaction", "item_category=1 and transaction_type=2 and requisition_no=$txt_req_no and prod_id=$txt_prod_id and id !=$update_id and status_active=1", "total_issue");

		$sql = sql_select("select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity, b.cons_amount,a.allocated_qnty,a.available_qnty from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id=1 and b.item_category=1 and b.transaction_type=2 and b.status_active=1");
		$before_prod_id = $before_issue_qnty = $before_stock_qnty = $before_stock_value = 0;
		foreach ($sql as $result) {
			$before_prod_id = $result[csf("id")];
			$before_stock_qnty = $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			//before quantity and stock value
			$before_issue_qnty = $result[csf("cons_quantity")];
			$before_issue_value = $result[csf("cons_amount")];
			$before_allocated_qnty = $result[csf("allocated_qnty")];
			$before_available_qnty = $result[csf("available_qnty")];
		}
		//current product ID
		$txt_prod_id = str_replace("'", "", $txt_prod_id);
		$txt_issue_qnty = str_replace("'", "", $txt_issue_qnty);

		//echo "10**";
		//echo $txt_issue_qnty ."+". $total_req_qty."-".$before_issue_qnty .">". $total_req_qty; die;
		//echo "10**".$total_pre_issue_qty ."+(". $before_issue_qnty ."+(".$txt_issue_qnty."-".$before_issue_qnty ."))>". $total_req_qty;die;
		if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
			$total_req_qty = return_field_value("sum(yarn_qnty) as total_req_qty", "ppl_yarn_requisition_entry", "requisition_no=$txt_req_no and prod_id=$txt_prod_id and status_active=1", "total_req_qty");
			if($total_pre_issue_qty + ($before_issue_qnty + ($txt_issue_qnty - $before_issue_qnty)) > $total_req_qty){
				echo "11**Issue Quantity can not be greater than Requisition Quantity.\nRequisition quantity = ".$total_req_qty . "\nBefore Issue = ". $total_pre_issue_qty . "\nAvailable quantity = " . ($total_req_qty - $total_pre_issue_qty);
				disconnect($con);die;
			}
		}
		/*
		 else if (str_replace("'", "", $cbo_basis) == 1 && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
			if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {

			} else {
				if($total_pre_issue_qty + ($before_issue_qnty + ($txt_issue_qnty - $before_issue_qnty)) > str_replace("'", "", $hdn_wo_qnty)){
					echo "11**Issue Quantity can not be greater than Work order Quantity.\nWork order quantity = ".$hdn_wo_qnty . "\nBefore Issue = ". $total_pre_issue_qty . "\nAvailable quantity = " . ($hdn_wo_qnty - $total_pre_issue_qty);
					die;
				}
			}
		}
		 */

		$sql = sql_select("select supplier_id, avg_rate_per_unit,current_stock,stock_value,allocated_qnty,available_qnty from product_details_master where id=$txt_prod_id and item_category_id=1");
		$curr_avg_rate = $curr_stock_qnty = $curr_stock_value = $allocated_qnty = $available_qnty = 0;
		$supplier_id_for_tran = '';
		foreach ($sql as $result) {
			$curr_avg_rate = $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty = $result[csf("current_stock")];
			$curr_stock_value = $result[csf("stock_value")];
			$allocated_qnty = $result[csf("allocated_qnty")];
			$available_qnty = $result[csf("available_qnty")];
			$supplier_id_for_tran = $result[csf("supplier_id")];
		}
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$store_wise_sql = sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)- (case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_qnty from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name  and item_category=1 and status_active=1");
		$store_wise_stock = $store_wise_sql[0][csf("balance_qnty")];

		$update_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		if ($before_prod_id == $txt_prod_id) {
			// CurrentStock + Before Issue Qnty - Current Issue Qnty
			$adj_stock_qnty = $curr_stock_qnty + $before_issue_qnty - $txt_issue_qnty;
			//item allocation----------------------------------------------------
			if ($variable_set_allocation == 1) {
				if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
					$availableChk = $allocated_qnty + $before_issue_qnty >= $txt_issue_qnty ? true : false;
					$msg_label = "Allocation";
					$msg_qnt = $allocated_qnty + $before_issue_qnty - $txt_issue_qnty;
				} else if (str_replace("'", "", $cbo_basis) == 1 && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
					if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {
						$availableChk = $available_qnty + $before_issue_qnty >= $txt_issue_qnty ? true : false;
						$msg_label = "Available";
						$msg_qnt = $available_qnty + $before_issue_qnty - $txt_issue_qnty;
					} else {
						$availableChk = $allocated_qnty + $before_issue_qnty >= $txt_issue_qnty ? true : false;
						$msg_label = "Allocation";
						$msg_qnt = $allocated_qnty + $before_issue_qnty - $txt_issue_qnty;
					}
				} else {
					$availableChk = $available_qnty + $before_issue_qnty >= $txt_issue_qnty ? true : false;
					$msg_label = "Available";
					$msg_qnt = $available_qnty + $before_issue_qnty - $txt_issue_qnty;
				}
			} else {
				$availableChk = $available_qnty + $before_issue_qnty >= $txt_issue_qnty ? true : false;
				$msg_label = "Available";
				$msg_qnt = $available_qnty + $before_issue_qnty - $txt_issue_qnty;
			}

			$storeAvailableChk = $store_wise_stock+$before_issue_qnty>=$txt_issue_qnty ?  true : false;
			$msg = "Issue Quantity can not be greater than $msg_label quantity.\n$msg_label quantity = ".(($msg_qnt < 0)?0:$msg_qnt);
			$msg2 = "Issue Quantity is exceed the Store wise Stock Quantity";

			if ($availableChk == false) {
				if ($db_type == 0) {
					mysql_query("ROLLBACK");
				} else {
					oci_rollback($con);
				}
				echo "11**" . $msg;
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				exit();
			}
			if ($storeAvailableChk == false) {
				if ($db_type == 0) {
					mysql_query("ROLLBACK");
				} else {
					oci_rollback($con);
				}
				echo "11**" . $msg2;
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				exit();
			}

			$allocated_qnty_balance = 0;
			$available_qnty_balance = 0;
			// if yarn allocation variable set to yes
			if ($variable_set_allocation == 1) {
				if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
					$allocated_qnty_balance = $allocated_qnty + $before_issue_qnty - $txt_issue_qnty;
					$available_qnty_balance = $available_qnty;
				} else if ((str_replace("'", "", $cbo_basis) == 1) && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
					if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {
						$allocated_qnty_balance = $allocated_qnty;
						$available_qnty_balance = $available_qnty + $before_issue_qnty - $txt_issue_qnty;
					} else {
						$allocated_qnty_balance = $allocated_qnty + $before_issue_qnty - $txt_issue_qnty;
						$available_qnty_balance = $available_qnty;
					}
				} else {
					$allocated_qnty_balance = $allocated_qnty;
					$available_qnty_balance = $available_qnty + $before_issue_qnty - $txt_issue_qnty;
				}
			} else {
				$allocated_qnty_balance = $allocated_qnty;
				$available_qnty_balance = $available_qnty + $before_issue_qnty - $txt_issue_qnty;
			}
			// CurrentStockValue + Before Issue Value - Current Issue Value
			$adj_stock_val = $curr_stock_value + $before_issue_value - ($txt_issue_qnty * $curr_avg_rate);
			//$adj_avgrate	= number_format($adj_stock_val/$adj_stock_qnty,$dec_place[3],'.','');
			$adj_avgrate = number_format($curr_avg_rate, $dec_place[3], '.', '');

			$data_array_prod = "" . $txt_issue_qnty . "*" . $adj_stock_qnty . "*" . number_format($adj_stock_val, $dec_place[4], '.', '') . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";

			//now current stock
			$curr_avg_rate = $adj_avgrate;
			$curr_stock_qnty = $adj_stock_qnty;
			$curr_stock_value = $adj_stock_val;
		} else {
			$updateID_array_prod = $update_data_prod = array();
			//before product adjust
			$adj_before_stock_qnty = $before_stock_qnty + $before_issue_qnty; // CurrentStock + Before Issue Qnty
			//$before_allocated_qnty	= $before_allocated_qnty+$before_issue_qnty;
			//$before_available_qnty	= $adj_before_stock_qnty-$before_allocated_qnty;
			$allocated_qnty_balance = 0;
			$available_qnty_balance = 0;

			if ($variable_set_allocation == 1) {
				if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
					$before_allocated_qnty = $before_allocated_qnty + $before_issue_qnty;
					$before_available_qnty = $before_available_qnty;

					$allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
					$available_qnty_balance = $available_qnty;
				} else if (str_replace("'", "", $cbo_basis) == 1 && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
					if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {
						$before_allocated_qnty = $before_allocated_qnty;
						$before_available_qnty = $before_available_qnty + $before_issue_qnty;

						$allocated_qnty_balance = $allocated_qnty;
						$available_qnty_balance = $available_qnty - $txt_issue_qnty;
					} else {
						$before_allocated_qnty = $before_allocated_qnty + $before_issue_qnty;
						$before_available_qnty = $before_available_qnty;

						$allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
						$available_qnty_balance = $available_qnty;
					}
				} else {
					$before_allocated_qnty = $before_allocated_qnty;
					$before_available_qnty = $before_available_qnty + $before_issue_qnty;

					$allocated_qnty_balance = $allocated_qnty;
					$available_qnty_balance = $available_qnty - $txt_issue_qnty;
				}
			} else {
				$before_allocated_qnty = $before_allocated_qnty;
				$before_available_qnty = $before_available_qnty + $before_issue_qnty;

				$allocated_qnty_balance = $allocated_qnty;
				$available_qnty_balance = $available_qnty - $txt_issue_qnty;
			}

			$adj_before_stock_val = $before_stock_value + $before_issue_value; // CurrentStockValue + Before Issue Value
			$adj_before_avgrate = number_format($adj_before_stock_val / $adj_before_stock_qnty, $dec_place[3], '.', '');

			$updateID_array_prod[] = $before_prod_id;
			$update_data_prod[$before_prod_id] = explode("*", ("" . $txt_issue_qnty . "*" . $adj_before_stock_qnty . "*" . number_format($adj_before_stock_val, $dec_place[4], '.', '') . "*" . $before_allocated_qnty . "*" . $before_available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));//$adj_before_avgrate."*".

			//current product adjust
			$adj_curr_stock_qnty = $curr_stock_qnty - $txt_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_val = $curr_stock_value - ($txt_issue_qnty * $curr_avg_rate); // CurrentStockValue + Before Issue Value
			//$adj_curr_avgrate	 = number_format($adj_curr_stock_val/$adj_curr_stock_qnty,$dec_place[3],'.','');
			$adj_curr_avgrate = number_format($curr_avg_rate, $dec_place[3], '.', '');
			//for current product-------------
			//item allocation----------------------------------------------------

			$availableChk = $curr_stock_qnty >= $txt_issue_qnty ? true : false;
			$msg = "Issue Quantity is exceed the current Stock Quantity";

			if ($availableChk == false) {
				//check_table_status( $_SESSION['menu_id'],0);
				if ($db_type == 0) {
					mysql_query("ROLLBACK");
				} else {
					oci_rollback($con);
				}
				echo "11**" . $msg;
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				exit();
			}

			$updateID_array_prod[] = $txt_prod_id;
			$update_data_prod[$txt_prod_id] = explode("*", ("" . $txt_issue_qnty . "*" . $adj_curr_stock_qnty . "*" . number_format($adj_curr_stock_val, $dec_place[4], '.', '') . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));//.$adj_curr_avgrate."*"

			//$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_data_prod,$updateID_array_prod));

			//now current stock
			$curr_avg_rate = $adj_curr_avgrate;
			$curr_stock_qnty = $adj_curr_stock_qnty;
			$curr_stock_value = $adj_curr_stock_val;
		}
		//------------------ product_details_master END--------------//
		//weighted and average rate END here-------------------------//
		//transaction table START--------------------------//
		$trans_data_array = array();
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount, b.recv_trans_id from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=3 and a.item_category=1");
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach ($sql as $result) {
			$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];
			$updateID_array_trans[] = $result[csf("id")];
			$update_data_trans[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			$trans_data_array[$result[csf("id")]]['qnty'] = $adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt'] = $adjAmount;
			$recv_trans_id .= $result[csf("recv_trans_id")] . ",";
		}

		$recv_trans_id = chop($recv_trans_id, ",");

		$query2 = true;
		$query3 = true;
		/*if(count($updateID_array_trans)>0)
		{
 			$query2=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_trans,$update_data_trans,$updateID_array_trans));
		}

		//transaction table END----------------------------//
		//LIFO/FIFO  START here------------------------//

		if(count($updateID_array_trans)>0)
		{
			 $updateIDArray = implode(",",$updateID_array);
			 $query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=3");
			}*/
		//PROPORTIONATE table here-------------------------//
		//$query4 = execute_query("DELETE FROM  order_wise_pro_details WHERE trans_id=$update_id and entry_form=3");
		//****************************************** BEFORE ENTRY ADJUST END *****************************************//
		//############## SAVE POINT START  ###################
			if ($db_type == 0) {
				$savepoint = "updatesql";
				mysql_query("SAVEPOINT $savepoint");
			}
		//############## SAVE POINT END    ###################
		//****************************************** NEW ENTRY START *****************************************//
		//issue master update START--------------------------------------//

			/*#### Stop not eligible field from update operation start ####*/
		// issue_basis*company_id*location_id*supplier_id*knit_dye_source*knit_dye_company*issue_purpose*
		// $cbo_basis . "*" . $cbo_company_id . "*" . $cbo_location_id . "*" . $cbo_supplier . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . " . $cbo_issue_purpose . "*
			/*#### Stop not eligible field from update operation end ####*/


			$field_array_mst = "entry_form*item_category*store_id*buyer_id*buyer_job_no*style_ref*booking_id*booking_no*issue_date*sample_type*challan_no*loan_party*remarks*ready_to_approve*updated_by*update_date";
			$data_array_mst = "3*1*" . $cbo_store_name . "*" . $cbo_buyer_name . "*" . $txt_buyer_job_no . "*" . $txt_style_ref . "*" . $txt_booking_id . "*" . $txt_booking_no . "*" . $txt_issue_date . "*" . $cbo_sample_type . "*" . $txt_challan_no . "*" . $cbo_loan_party . "*" . $txt_remarks . "*" . $cbo_ready_to_approved . "*'" . $user_id . "'*'" . $pc_date_time . "'";
			$id = str_replace("'", "", $update_id_mst);
		//echo $field_array."<br>".$data_array;."-".;
		//$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"issue_number",$txt_system_no,0);

			/******** original product id check start ********/

		//$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$txt_prod_id and status_active=1","origin_prod_id");
			if ($before_prod_id == $txt_prod_id) $balance_cond = " and( balance_qnty>0 or id in($recv_trans_id))";
			else $balance_cond = " and balance_qnty>0";
			$origin_prod_id = return_field_value("origin_prod_id", "inv_transaction", "prod_id=$txt_prod_id and status_active=1 and store_id=$cbo_store_name $balance_cond and transaction_type in (1,4,5) and item_category=1", "origin_prod_id");

			/******** original product id check end ********/


		//issue master update END---------------------------------------//
		//inventory TRANSACTION table data UPDATE START----------------------------------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
			$txt_issue_qnty = str_replace("'", "", $txt_issue_qnty);
		$avg_rate = $curr_avg_rate; // asign current rate
		$issue_stock_value = $avg_rate * $txt_issue_qnty;
		$field_array_trans = "requisition_no*receive_basis*company_id*supplier_id*prod_id*origin_prod_id*dyeing_color_id*item_category*transaction_type*transaction_date*store_id*brand_id*cons_uom*cons_quantity*return_qnty*item_return_qty*cons_rate*cons_amount*no_of_bags*cone_per_bag*weight_per_bag*weight_per_cone*floor_id*room*rack*self*using_item*job_no*updated_by*update_date*btb_lc_id";
		$data_array_trans = "" . $txt_req_no . "*" . $cbo_basis . "*" . $cbo_company_id . "*'" . $supplier_id_for_tran . "'*" . $txt_prod_id . "*'" . $origin_prod_id . "'*" . $cbo_dyeing_color . "*1*2*" . $txt_issue_date . "*" . $cbo_store_name . "*" . $cbo_brand . "*" . $cbo_uom . "*" . $txt_issue_qnty . "*" . $txt_returnable_qty . "*" . $extra_quantity . "*" . $avg_rate . "*" . $issue_stock_value . "*" . $txt_no_bag . "*" . $txt_no_cone . "*" . $txt_weight_per_bag . "*" . $txt_weight_per_cone . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_item . "*" . $job_no . "*'" . $user_id . "'*'" . $pc_date_time . "'*" . $txt_btb_lc_id;
		//echo $field_array."<br>".$data_array;."-".;
		//$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
		//inventory TRANSACTION table data UPDATE  END----------------------------------------------------------//
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate = 0;
		$data_array = "";
		$updateID_array = array();
		$update_data = array();
		$issueQnty = $txt_issue_qnty;


		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";
		if ($before_prod_id == $txt_prod_id) $balance_cond = " and( balance_qnty>0 or id in($recv_trans_id))";
		else $balance_cond = " and balance_qnty>0";
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date, id $cond_lifofifo");
		foreach ($sql as $result) {
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			if ($trans_data_array[$issue_trans_id]['qnty'] == "") {
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			} else {
				$balance_qnty = $trans_data_array[$issue_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$issue_trans_id]['amnt'];
			}

			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);
			if ($issueQntyBalance >= 0) {
				$amount = $issueQnty * $cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_id . ",3," . $txt_prod_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $issue_trans_id;
				$update_data[$issue_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			} else if ($issueQntyBalance < 0) {
				$issueQntyBalance = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $cons_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_id . ",3," . $txt_prod_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//echo "20**".$data_array;die;
				//for update
				$updateID_array[] = $issue_trans_id;
				$update_data[$issue_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		}//end foreach
		// LIFO/FIFO then END-----------------------------------------------//txt_prod_id

		//echo "20**".$data_array;mysql_query("ROLLBACK");die;
		//mrr wise issue data insert here----------------------------//
		$mrrWiseIssueID = true;
		$upTrID = true;

		//----------order_wise_pro_details table data insert Start --------------------------------//
		$proportQ = true;
		$save_string = explode(",", str_replace("'", "", $save_data));
		if (count($save_string) > 0 && str_replace("'", "", $save_data) != "") {
			//order_wise_pro_details table data insert START-----//
			$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by,insert_date";
			$po_array = array();
			$po_rt_array = array();
			for ($i = 0; $i < count($save_string); $i++) {
				$order_dtls = explode("**", $save_string[$i]);
				$order_id = $order_dtls[0];
				$order_qnty = $order_dtls[1];
				$returnable_qnty = $order_dtls[2];

				if (array_key_exists($order_id, $po_array)) {
					$po_array[$order_id] += $order_qnty;
					$po_rt_array[$order_id] += $returnable_qnty;
				} else {
					$po_array[$order_id] = $order_qnty;
					$po_rt_array[$order_id] = $returnable_qnty;
				}
			}
			$i = 0;
			$is_salesOrder = 0;
			if (str_replace("'", "", $cbo_basis) == 3) {
				$is_salesOrder = return_field_value("a.is_sales", "ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b", "a.id=b.knit_id and b.requisition_no=$txt_req_no", "is_sales");
			}

			if (str_replace("'", "", $txt_entry_form) == 135) {
				$is_salesOrder = 1;
			}

			if(str_replace("'", "", $txt_entry_form) == 94){
				$yarn_dyeing_info=sql_select("select entry_form,is_sales from wo_yarn_dyeing_mst where status_active=1 and ydw_no=$txt_booking_no and company_id=$cbo_company_id");
				$is_salesOrder = 1;
			}

			foreach ($po_array as $key => $val) {
				if ($i > 0) $data_array_prop .= ",";
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_id = $key;
				$order_qnty = $val;
				$returnable_qnty = $po_rt_array[$key];
				$data_array_prop .= "(" . $id_proport . "," . $update_id . ",2,3," . $order_id . "," . $txt_prod_id . "," . $order_qnty . "," . $cbo_issue_purpose . ",'" . $returnable_qnty . "','" . $is_salesOrder . "'," . $user_id . ",'" . $pc_date_time . "')";
				$i++;
			}
			/*if($data_array_prop!="")
			{
				$proportQ=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);

			}*/
		}

		if (str_replace("'", "", $cbo_basis) == 4) {
			$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by,insert_date";
			$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop = "(" . $id_proport . "," . $update_id . ",2,3," . $txt_booking_id . "," . $txt_prod_id . "," . $txt_issue_qnty . "," . $cbo_issue_purpose . "," . $txt_returnable_qty . ",1," . $user_id . ",'" . $pc_date_time . "')";
		}
		//----------order_wise_pro_details table data insert END --------------------------------//
		//****************************************** NEW ENTRY END *****************************************//
		//echo "10**"."insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;

		if ($before_prod_id == $txt_prod_id) {
			$query1 = sql_update("product_details_master", $update_array_prod, $data_array_prod, "id", $before_prod_id, 0);
		} else {
			$query1 = execute_query(bulk_update_sql_statement("product_details_master", "id", $update_array_prod, $update_data_prod, $updateID_array_prod));
		}

		if (count($updateID_array_trans) > 0) {
			$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array_trans, $update_data_trans, $updateID_array_trans));
			$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=3");
		}

		$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=3");
		$rID = sql_update("inv_issue_master", $field_array_mst, $data_array_mst, "issue_number", $txt_system_no, 0);
		$transID = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 1);

		if ($data_array != "") {
			$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array, $data_array, 0);
		}
		if (count($updateID_array) > 0) {
			$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array));
		}

		if ($data_array_prop != "") {
			$proportQ = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
		}
		//mysql_query("ROLLBACK");
		/*echo "10**".$transID;
		die;*/
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		//echo "10**$query1 && $query2 && $query3 && $query4 && $rID && $transID && $upTrID && $mrrWiseIssueID && $proportQ";die;
		if ($db_type == 0) {
			if ($query1 && $query2 && $query3 && $query4 && $rID && $transID && $upTrID && $mrrWiseIssueID && $proportQ) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				mysql_query("ROLLBACK TO $savepoint");
				echo "10**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($query1 && $query2 && $query3 && $query4 && $rID && $transID && $upTrID && $mrrWiseIssueID && $proportQ) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	} else if ($operation == 2) // Not Used Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		//echo "SELECT a.recv_number,b.order_qnty FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id and a.issue_id=$update_id_mst and a.booking_no=$txt_req_no and b.transaction_type=4 and b.transaction_type=4";

		$return_result = sql_select("SELECT a.recv_number,b.order_qnty FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id and a.issue_id=$update_id_mst and a.booking_no=$txt_req_no and b.transaction_type=4 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		if(!empty($return_result))
		{
			foreach ($return_result as $return_row) {
				$returnString .= ",".$return_row[csf('recv_number')]." -> ".$return_row[csf('order_qnty')];
			}
			disconnect($con);exit("30**".$returnString);
		}


		if (str_replace("'", "", $txt_system_no) != "") //new insert cbo_ready_to_approved
		{
			$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no=$txt_system_no and status_active=1 and is_deleted=0", "sys_number");
			if ($check_in_gate_pass != "") {
				echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";
				disconnect($con);die;
			}
		}

		//check update id
		if (str_replace("'", "", $update_id) == "" || str_replace("'", "", $txt_system_no) == "") {
			echo "15";
			disconnect($con);
			exit();
		}

		//product master table information
		//before stock update
		$sql = sql_select("select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity, b.cons_amount,a.allocated_qnty,a.available_qnty from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id=1 and b.item_category=1 and b.transaction_type=2");
		$before_prod_id = $before_issue_qnty = $before_stock_qnty = $before_stock_value = 0;
		foreach ($sql as $result) {
			$before_prod_id = $result[csf("id")];
			$before_stock_qnty = $result[csf("current_stock")];
			$before_avg_rate_per_unit = $result[csf("avg_rate_per_unit")];

			$before_issue_qnty = $result[csf("cons_quantity")];
			$before_issue_value = $result[csf("cons_amount")];
			$before_allocated_qnty = $result[csf("allocated_qnty")];
			$before_available_qnty = $result[csf("available_qnty")];
		}

		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$field_array_prod = "current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$adj_stock_qnty = $before_stock_qnty + $before_issue_qnty;
		$adj_stock_val = $adj_stock_qnty * $before_avg_rate_per_unit;
		$allocated_qnty_balance = 0;
		$available_qnty_balance = 0;

		if ($variable_set_allocation == 1) {
			if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
				$allocated_qnty_balance = $before_allocated_qnty + $before_issue_qnty;
				$available_qnty_balance = $before_available_qnty;
			} else if ((str_replace("'", "", $cbo_basis) == 1) && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
				if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {
					$allocated_qnty_balance = $before_allocated_qnty;
					$available_qnty_balance = $before_available_qnty + $before_issue_qnty;
				} else {
					$allocated_qnty_balance = $before_allocated_qnty + $before_issue_qnty;
					$available_qnty_balance = $before_available_qnty;
				}
			} else {
				$allocated_qnty_balance = $before_allocated_qnty;
				$available_qnty_balance = $before_available_qnty + $before_issue_qnty;
			}
		} else {
			$allocated_qnty_balance = $before_allocated_qnty;
			$available_qnty_balance = $before_available_qnty + $before_issue_qnty;
		}

		$data_array_prod = "" . $adj_stock_qnty . "*" . $adj_stock_val . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";

		$trans_data_array = array();
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=3 and a.item_category=1");
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach ($sql as $result) {
			$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];
			$updateID_array_trans[] = $result[csf("id")];
			$update_data_trans[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
		}
		// echo "10**<pre>";
		// print_r($data_array_prod);
		// echo "10**ok";die;
		$query1 = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $before_prod_id, 0);

		if (!empty($update_data_trans)) {
			$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array_trans, $update_data_trans, $updateID_array_trans));
		} else {
			$query2 = 1;
		}
		$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=3");
		$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=3");
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$changeStatus = sql_update("inv_transaction", $field_array_status, $data_array_status, "id", $update_id, 1);

		//echo $query1."&&".$query2."&&".$query3."&&".$query4."&&".$changeStatus;
		if ($db_type == 0) {
			if ($query1 && $query2 && $query3 && $query4 && $changeStatus) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_system_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_system_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($query1 && $query2 && $query3 && $query4 && $changeStatus) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_system_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_system_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "mrr_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(sys_number) {
            $("#hidden_sys_number").val(sys_number); // mrr number
            parent.emailwindow.hide();
        }
    </script>

</head>
<body>
	<div align="center" style="width:100%;">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="150">Supplier</th>
						<th width="150">Search By</th>

						<th width="250" align="center" id="search_by_td_up">Enter Issue Number</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
							class="formbutton"/></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td>
								<?
								$search_by = array(1 => 'Issue No', 2 => 'Challan No', 3 => 'In House', 4 => 'Out Bound Subcontact', 5 => 'Job No', 6 => 'Wo No', 7 => 'Buyer');
								$dd = "change_search_event(this.value, '0*0*1*1*0*0*1', '0*0*select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name*select c.id, c.supplier_name from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name*0*0*select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name', '../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
								placeholder="From Date"/>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
								placeholder="To Date"/>
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'yarn_issue_store_update_controller', 'setFilterGrid(\'list_view\',-1)')"
								style="width:100px;"/>
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1); ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_sys_number" value=""/>
								<!-- END -->
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}

if ($action == "create_mrr_search_list_view") {
	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$sql_cond = "";

	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$sql_cond .= " and issue_date between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and issue_date between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}

	if ($supplier != "" && $supplier * 1 != 0) $sql_cond .= " and a.supplier_id='$supplier'";
	if ($company != "" && $company * 1 != 0) $sql_cond .= " and a.company_id='$company'";

	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$within_group_arr = return_library_array("select id, within_group from fabric_sales_order_mst", 'id', 'within_group');

	if ($txt_search_common != "" || $txt_search_common != 0) {
		if ($txt_search_by == 1) {
			$sql_cond .= " and a.issue_number like '%$txt_search_common%'";
		} else if ($txt_search_by == 2) {
			$sql_cond .= " and a.challan_no like '%$txt_search_common%'";
		} else if ($txt_search_by == 3) {
			$sql_cond .= " and a.knit_dye_source=1 and a.knit_dye_company='$txt_search_common'";
		} else if ($txt_search_by == 4) {
			$sql_cond .= " and a.knit_dye_source=2 and a.knit_dye_company='$txt_search_common'";
		} else if ($txt_search_by == 5) {
			$sql_cond .= " and a.buyer_job_no like '%$txt_search_common%'";
		} else if ($txt_search_by == 6) {
			$sql_cond .= " and a.booking_no like '%$txt_search_common%'";
		} else if ($txt_search_by == 7) {
			$sql_cond .= " and a.buyer_id = '$txt_search_common'";
		}
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";

	if($user_store_ids) $user_store_cond = " and a.store_id in ($user_store_ids)"; else $user_store_cond = "";

	if ($db_type == 0) {
		$sql = "select a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.is_posted_account , $year_field a.is_approved,sum(b.cons_quantity) issue_quantity from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.entry_form=3 $sql_cond $user_store_cond group by a.id order by a.issue_number";
	}else{
		$sql = "select a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.is_posted_account , $year_field a.is_approved,sum(b.cons_quantity) issue_quantity from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.entry_form=3 $sql_cond $user_store_cond group by a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.insert_date,a.is_posted_account,a.is_approved order by a.issue_number";
	}
	//echo $sql;
	$result = sql_select($sql);
	?>
	<div style="margin-top:5px">
		<div style="width:1020px;">
			<table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="60">Issue No</th>
					<th width="50">Year</th>
					<th width="70">Date</th>
					<th width="100">Purpose</th>
					<th width="70">Challan No</th>
					<th width="60">Issue Qnty</th>
					<th width="110">Booking No</th>
					<th width="100">knitting Comp.</th>
					<th width="115">Buyer</th>
					<th width="85">Job No.</th>
					<th width="90">Store</th>
					<th>Ready to Approve</th>
				</thead>
			</table>
		</div>
		<div style="width:1020px;overflow-y:scroll; max-height:210px;" id="search_div">
			<table cellspacing="0" cellpadding="0" width="1002" class="rpt_table" id="list_view" border="1" rules="all">
				<?php
				$i = 1;
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if ($row[csf("issue_basis")] == 4) {
						if ($within_group_arr[$row[csf("booking_id")]] == 1) {
							$buyer = $company_arr[$row[csf("buyer_id")]];
						} else {
							$buyer = $buyer_arr[$row[csf("buyer_id")]];
						}
					} else {
						$buyer = $buyer_arr[$row[csf("buyer_id")]];
					}
					if($row[csf("is_approved")]==3){
						$is_approved=1;
					}else{
						$is_approved=$row[csf("is_approved")];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onclick="js_set_value('<? echo $row[csf("issue_number")]; ?>,<? echo $is_approved; ?>,<? echo $row[csf("id")]; ?>,<? echo $within_group_arr[$row[csf("booking_id")]] . "," . $row[csf("buyer_id")] . "," . $buyer . "," . $row[csf("is_posted_account")]; ?>');">
						<td width="30"><?php echo $i; ?></td>
						<td width="60"><p><?php echo $row[csf("issue_number_prefix_num")]; ?></p></td>
						<td width="50"><p><?php echo $row[csf("year")]; ?></p></td>
						<td width="70"><p><?php echo change_date_format($row[csf("issue_date")]); ?></p></td>
						<td width="100"><p><?php echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
						<td width="70"><p><?php echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
						<td width="60" align="right"><?php echo number_format($row[csf("issue_quantity")], 2, '.', ''); ?></p></td>
						<td width="110"><p><?php echo $row[csf("booking_no")]; ?></p></td>
						<td width="100">
							<p>
								<?php
								if ($row[csf("knit_dye_source")] == 1) $knit_com = $company_arr[$row[csf("knit_dye_company")]];
								else $knit_com = $supplier_arr[$row[csf("knit_dye_company")]];
								echo $knit_com;
								?>
							</p>
						</td>
						<td width="115"><p><?php echo $buyer; ?>&nbsp;</p></td>
						<td width="85"><p><?php echo $row[csf("buyer_job_no")]; ?>&nbsp;</p></td>
						<td width="90"><p><?php echo $store_arr[$row[csf("store_id")]]; ?></p></td>
						<td>
                            <p><?php echo $yes_no[$row[csf("ready_to_approve")]]; //if($row[csf("ready_to_approve")]!=1) else echo "";
                            ?>&nbsp;</p></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </table>
            </div>
        </div>
        <?
        exit();
    }

    if ($action == "populate_data_from_data") {
    	$sql = "select id, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, booking_id, booking_no, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, lap_dip_no, gate_pass_no, item_color, color_range, remarks, ready_to_approve, loan_party, is_approved
    	from inv_issue_master
    	where id='$data' and entry_form=3";
	//echo $sql;
    	$res = sql_select($sql);
    	foreach ($res as $row) {
    		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
    		echo "$('#update_id_mst').val(" . $row[csf("id")] . ");\n";

		//echo"load_drop_down( 'requires/yarn_issue_store_update_controller', ".$row[csf("company_id")].", 'load_drop_down_supplier', 'supplier' );\n";

		//echo"load_drop_down( 'requires/yarn_issue_store_update_controller', ".$row[csf("company_id")].", 'load_drop_down_store', 'store_td' );\n";

    		echo "$('#cbo_basis').val(" . $row[csf("issue_basis")] . ");\n";

    		echo "load_drop_down('requires/yarn_issue_store_update_controller', '" . $row[csf("issue_basis")] . "_1', 'load_drop_down_purpose', 'issue_purpose_td');";
    		echo "$('#cbo_issue_purpose').val(" . $row[csf("issue_purpose")] . ");\n";

    		echo "load_supplier();\n";
    		echo "active_inactive();\n";
    		echo "disable_enable_fields( 'cbo_company_id*cbo_basis', 1, '', '');\n";
    		echo "$('#txt_issue_date').val('" . change_date_format($row[csf("issue_date")]) . "');\n";
    		echo "$('#txt_booking_id').val(" . $row[csf("booking_id")] . ");\n";
    		echo "$('#txt_booking_no').val('" . $row[csf("booking_no")] . "');\n";

    		echo "$('#cbo_knitting_source').val(" . $row[csf("knit_dye_source")] . ");\n";

    		if ($row[csf("knit_dye_source")] != 0) {
    			echo "load_drop_down( 'requires/yarn_issue_store_update_controller', " . $row[csf("knit_dye_source")] . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_knit_com', 'knitting_company_td' );\n";
    		}
    		echo"load_drop_down( 'requires/yarn_issue_store_update_controller', ".$row[csf("knit_dye_company")]."+'_'+".$row[csf("knit_dye_source")].", 'load_drop_down_location', 'location_td' );\n";

    		echo "$('#cbo_location_id').val(" . $row[csf("location_id")] . ");\n";
    		if ($row[csf("issue_purpose")] == 2) {
    			$entry_form = return_field_value("entry_form", "wo_yarn_dyeing_mst", "id='" . $row[csf("booking_id")] . "'");
    			if ($entry_form == 42) {
    				echo "$('#txt_issue_qnty').attr('placeholder','Entry');\n";
    				echo "$('#txt_issue_qnty').removeAttr('ondblclick');\n";
    				echo "$('#txt_issue_qnty').removeAttr('readOnly');\n";
    				echo "$('#txt_returnable_qty').removeAttr('readOnly');\n";
    				echo "$('#txt_returnable_qty').attr('placeholder','Entry');\n";
    			} else {
    				echo "$('#txt_issue_qnty').attr('placeholder','Double Click');\n";
    				echo "$('#txt_issue_qnty').attr('ondblclick','openmypage_po()');\n";
    				echo "$('#txt_issue_qnty').attr('readOnly',true);\n";
    				echo "$('#txt_returnable_qty').attr('readOnly',true);\n";
    				echo "$('#txt_returnable_qty').attr('placeholder','Display');\n";
    			}
    			echo "$('#txt_entry_form').val(" . $entry_form . ");\n";
    			echo "show_list_view('" . $row[csf("booking_id")] . "','show_yarn_dyeing_list_view','requisition_item','requires/yarn_issue_store_update_controller','');\n";
    			echo "load_drop_down( 'requires/yarn_issue_store_update_controller','" . $row[csf("booking_id")] . "','load_drop_down_dyeing_color', 'dyeingColor_td' );\n";
    		} else {
    			echo "load_drop_down( 'requires/yarn_issue_store_update_controller','0','load_drop_down_dyeing_color', 'dyeingColor_td' );\n";
    		}
		//echo "load_drop_down( 'requires/yarn_issue_store_update_controller', ".$row[csf("knit_dye_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knit_com', 'knitting_company_td' );\n";

    		echo "$('#cbo_knitting_company').val(" . $row[csf("knit_dye_company")] . ");\n";
    		echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
    		echo "$('#txt_challan_no').val('" . $row[csf("challan_no")] . "');\n";
    		echo "$('#cbo_loan_party').val(" . $row[csf("loan_party")] . ");\n";
    		echo "$('#cbo_sample_type').val(" . $row[csf("sample_type")] . ");\n";
    		echo "$('#cbo_buyer_name').val(" . $row[csf("buyer_id")] . ");\n";
    		echo "$('#txt_style_ref').val('" . $row[csf("style_ref")] . "');\n";
    		echo "$('#txt_buyer_job_no').val('" . $row[csf("buyer_job_no")] . "');\n";
    		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";
    		echo "$('#cbo_ready_to_approved').val(" . $row[csf("ready_to_approve")] . ");\n";
		//clear child form
    		echo "$('#tbl_child').find('select,input').val('');\n";
    		if($row[csf("is_approved")]==3){
    			$is_approved=1;
    		}else{
    			$is_approved=$row[csf("is_approved")];
    		}
    		if ($is_approved == 1) {
    			echo "$('#approved').text('Approved');\n";
    		} else {
    			echo "$('#approved').text('');\n";
    		}
    	}
    	exit();
    }

    if ($action == "show_dtls_list_view") {
    	$ex_data = explode("**", $data);
    	$up_id = $ex_data[0];

    	$cond = "";
    	if ($up_id != "") $cond .= " and a.id='$up_id'";
    	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
    	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
    	$supplier_arr = return_library_array("select id,short_name from lib_supplier", 'id', 'short_name');

    	$sql = "select b.requisition_no, a.challan_no, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.rack, b.self, b.supplier_id
    	from inv_issue_master a, inv_transaction b, product_details_master c
    	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and b.item_category=1 and a.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond";
	//echo $sql;
    	$result = sql_select($sql);
    	$i = 1;
    	$total_qnty = 0;
    	?>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="970" rules="all">
    		<thead>
    			<tr>
    				<th>SL</th>
    				<th>Challan No</th>
    				<th>Lot No</th>
    				<th>Supplier</th>
    				<th>Yarn Count</th>
    				<th>Composition</th>
    				<th>Yarn Type</th>
    				<th>Color</th>
    				<th>Store</th>
    				<th>Issue Qnty</th>
    				<th>UOM</th>
    				<th>Req. No</th>
    				<th>Rack</th>
    				<th>Shelf</th>
    			</tr>
    		</thead>
    		<tbody>
    			<? foreach ($result as $row) {

    				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
    				else $bgcolor = "#FFFFFF";

    				$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
    				if ($row[csf('yarn_comp_type2nd')] != 0) $composition_string .= " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')];

    				$total_qnty += $row[csf("cons_quantity")];
    				?>
    				<tr bgcolor="<? echo $bgcolor; ?>"
    					onClick='get_php_form_data("<? echo $row[csf("id")]; ?>","child_form_input_data","requires/yarn_issue_store_update_controller")'
    					style="cursor:pointer">
    					<td width="30"><?php echo $i; ?></td>
    					<td width="70"><p><?php echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
    					<td width="70"><p><?php echo $row[csf("lot")]; ?></p></td>
    					<td width="70"><p><?php echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
    					<td width="70"><p><?php echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>
    					<td width="120"><p><?php echo $composition_string; ?></p></td>
    					<td width="70"><p><?php echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
    					<td width="80"><p><?php echo $color_name_arr[$row[csf("color")]]; ?></p></td>
    					<td width="80"><p><?php echo $store_arr[$row[csf("store_id")]]; ?></p></td>
    					<td width="70" align="right"><p><?php echo number_format($row[csf("cons_quantity")], 2, '.', ''); ?></p>
    					</td>
    					<td width="50"><p><?php echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
    					<td width="60"><p><?php echo $row[csf("requisition_no")]; ?>&nbsp;</p></td>
    					<td width="50"><p><?php echo $row[csf("rack")]; ?>&nbsp;</p></td>
    					<td><p><?php echo $row[csf("self")]; ?>&nbsp;</p></td>
    				</tr>
    				<? $i++;
    			} ?>
    			<tfoot>
    				<th colspan="9" align="right">Sum</th>
    				<th><?php echo number_format($total_qnty,2); ?></th>
    				<th>&nbsp;</th>
    				<th>&nbsp;</th>
    				<th>&nbsp;</th>
    				<th>&nbsp;</th>
    			</tfoot>
    		</tbody>
    	</table>
    	<?
    	exit();
    }


    if ($action == "child_form_input_data") {
    	$rcv_dtls_id = $data;

    	$sql = "select a.company_id, a.issue_basis, a.issue_purpose, b.requisition_no,b.id, b.store_id, b.supplier_id, b.cons_uom, b.cons_quantity, b.return_qnty, b.item_return_qty, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, b.dyeing_color_id, b.room, b.rack, b.self,b.floor_id, b.using_item, b.job_no, c.current_stock, c.allocated_qnty, c.available_qnty, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.brand, c.lot, c.id as prod_id, b.btb_lc_id,(select sum(d.yarn_qnty) from ppl_yarn_requisition_entry d where b.requisition_no=d.requisition_no and c.id=d.prod_id and d.status_active=1) yarn_qnty from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id' and b.transaction_type=2 and b.item_category=1";
    	$result = sql_select($sql);
    	foreach ($result as $row) {
    		$product_id_arr[] = $row[csf("prod_id")];
    		$product_store_id_arr[] = $row[csf("store_id")];
    		$trans_arr[] = $row[csf("id")];
    	}
    	$trans_cond = (!empty($trans_arr))?" and trans_id in(".implode(",",$trans_arr).")":"";
    	$order_wise_sql = sql_select("select trans_id,po_breakdown_id, quantity, returnable_qnty from order_wise_pro_details where entry_form=3 and trans_type=2 and status_active=1 $trans_cond");
    	foreach ($order_wise_sql as $order_row) {
    		$order_wise_arr[$order_row[csf("trans_id")]] = $order_row[csf("po_breakdown_id")]."**".$order_row[csf("quantity")]."**".$order_row[csf("returnable_qnty")];
    	}

    	$prod_cond = !empty($product_id_arr)?" and prod_id in(".implode(",",$product_id_arr).")":"";
    	$store_cond = !empty($product_store_id_arr)?" and store_id in(".implode(",",$product_store_id_arr).")":"";
    	$store_wise_sql = sql_select("select prod_id,store_id,sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)- (case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_qnty from inv_transaction where item_category=1 $prod_cond $store_cond group by prod_id,store_id");
    	foreach ($store_wise_sql as $store_row) {
    		$product_store_arr[$store_row[csf("prod_id")]][$store_row[csf("store_id")]] += $store_row[csf("balance_qnty")];
    	}

    	$load_store_dropdown = return_library_array("select id, store_name from lib_store_location where id in (".implode(",",$product_store_id_arr).")", 'id', 'store_name');
    	echo "$('#store_td').html('" . create_drop_down("cbo_store_name", 142, $load_store_dropdown, "", 1, "-- Select Store --", 0, "fn_empty_lot(this.value);", 0) . "');\n";

    	foreach ($result as $row) {
    		echo "$('#txt_req_no').val(" . $row[csf("requisition_no")] . ");\n";
    		if ($row[csf("issue_basis")] == 3) {
    			echo "show_list_view('" . $row[csf("requisition_no")] . "," . $row[csf("company_id")] . "','show_req_list_view','requisition_item','requires/yarn_issue_store_update_controller','');\n";
    		}
    		echo "$('#txt_lot_no').val('" . $row[csf("lot")] . "');\n";
    		echo "$('#txt_prod_id').val(" . $row[csf("prod_id")] . ");\n";
    		echo "$('#txt_issue_qnty').val(" . $row[csf("cons_quantity")] . ");\n";
    		echo "$('#hidden_p_issue_qnty').val(" . $row[csf("cons_quantity")] . ");\n";
    		echo "$('#txt_returnable_qty').val(" . $row[csf("return_qnty")] . ");\n";
    		echo "$('#extra_quantity').val(" . $row[csf("item_return_qty")] . ");\n";
    		$txt_prod_id = $row[csf("prod_id")];
    		$store_name = $row[csf("store_id")];
    		$stock_qnty = $product_store_arr[$txt_prod_id][$store_name];

    		echo "$('#txt_current_stock').val(" . $stock_qnty . ");\n";
    		echo "$('#txt_no_bag').val(" . $row[csf("no_of_bags")] . ");\n";
    		echo "$('#cbo_supplier_lot').val(" . $row[csf("supplier_id")] . ");\n";
    		echo "$('#txt_no_cone').val(" . $row[csf("cone_per_bag")] . ");\n";
    		echo "$('#txt_weight_per_bag').val(" . $row[csf("weight_per_bag")] . ");\n";
    		echo "$('#txt_weight_per_cone').val(" . $row[csf("weight_per_cone")] . ");\n";


    		echo "load_room_rack_self_bin('requires/yarn_issue_store_update_controller*1', 'store','store_td', '".$row[csf('company_id')]."',this.value,'', '','','','','','fn_empty_lot(this.value);');\n";

    		echo "$('#cbo_store_name').val(" . $row[csf("store_id")] . ");\n";
    		echo "load_room_rack_self_bin('requires/yarn_issue_store_update_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
    		echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
    		$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
    		if ($row[csf('yarn_comp_type2nd')] != 0) $composition_string .= " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')];
    		echo "$('#txt_composition').val('" . $composition_string . "');\n";
    		echo "$('#txt_composition_id').val('" . $row[csf("yarn_comp_type1st")] . "');\n";
    		echo "$('#txt_composition_percent').val('" . $row[csf("yarn_comp_percent1st")] . "');\n";
    		echo "$('#cbo_yarn_type').val('" . $row[csf("yarn_type")] . "');\n";
    		echo "$('#cbo_color').val(" . $row[csf("color")] . ");\n";
    		echo "$('#cbo_brand').val(" . $row[csf("brand")] . ");\n";
    		echo "$('#cbo_dyeing_color').val('" . $row[csf("dyeing_color_id")] . "');\n";
    		echo "$('#cbo_uom').val(" . $row[csf("cons_uom")] . ");\n";
    		echo "$('#cbo_floor').val(" . $row[csf("floor_id")] . ");\n";
    		echo "load_room_rack_self_bin('requires/yarn_issue_store_update_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
    		echo "$('#cbo_room').val(" . $row[csf("room")] . ");\n";
    		echo "load_room_rack_self_bin('requires/yarn_issue_store_update_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
    		echo "$('#txt_rack').val(" . $row[csf("rack")] . ");\n";
    		echo "load_room_rack_self_bin('requires/yarn_issue_store_update_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
    		echo "$('#txt_shelf').val(" . $row[csf("self")] . ");\n";
    		echo "$('#cbo_item').val(" . $row[csf("using_item")] . ");\n";
    		echo "$('#job_no').val('" . $row[csf("job_no")] . "');\n";
    		echo "$('#hdn_requis_qnty').val('" . $row[csf("yarn_qnty")] . "');\n";
    		if ($row[csf("btb_lc_id")] > 0) {
    			$btb_lc_num = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $row[csf("btb_lc_id")]."'", "lc_number");
    			echo "$('#txt_btb_selection').val('" . $btb_lc_num . "');\n";
    			echo "$('#txt_btb_lc_id').val('" . $row[csf("btb_lc_id")] . "');\n";
    		}

			//issue qnty popup data arrange
    		$sqlIN = sql_select("select po_breakdown_id, quantity, returnable_qnty from order_wise_pro_details where trans_id=" . $row[csf("id")] . " and entry_form=3 and trans_type=2 and status_active=1");
    		$poWithValue = "";
    		$poID = "";
    		if (count($sqlIN) > 0) {
    			foreach ($sqlIN as $res) {
    				if ($poWithValue != "") $poWithValue .= ",";
    				if ($poID != "") $poID .= ",";
    				$poWithValue .= $res[csf("po_breakdown_id")] . "**" . $res[csf("quantity")] . "**" . $res[csf("returnable_qnty")];
    				$poID .= $res[csf("po_breakdown_id")];
    			}
    			echo "$('#save_data').val('" . $poWithValue . "');\n";
    			echo "$('#all_po_id').val('" . $poID . "');\n";
    		} else {
    			echo "$('#txt_issue_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick');\n";
    		}
			//update id here
    		echo "$('#update_id').val(" . $row[csf("id")] . ");\n";
    		echo "set_button_status(1, permission, 'fnc_yarn_issue_entry',1);\n";
    	}
    	exit();
    }

//newly addedd--- requisition popup
    if ($action == "requis_popup") {
    	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
    	extract($_REQUEST);
    	?>
    	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    	<script>
    		<?
    		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][3]);
    		echo "var field_level_data= " . $data_arr . ";\n";
    		?>
    		window.onload = function () {
    			set_field_level_access( <? echo $company; ?> );
    		};

    		function fn_show_check() {
    			show_list_view(document.getElementById('cbo_buyer_name').value + '_' + document.getElementById('txt_search_common').value + '_' + '<? echo $company; ?>' +
    				'_' + document.getElementById('cbo_sales_order').value, 'create_req_search_list', 'search_divs', 'yarn_issue_store_update_controller', 'setFilterGrid(\'list_view\',-1)'
    				)
    			;
    		}

    		function js_set_value(req_no) {
    			$("#hidden_req_no").val(req_no);
    			parent.emailwindow.hide();
    		}

    		function fnc_disable(str) {
    			$("#cbo_buyer_name").val(0);
    			if (str == 1) {
    				$('#cbo_buyer_name').attr('disabled', 'disabled');
    			}
    			else {
    				$('#cbo_buyer_name').removeAttr('disabled', 'disabled');
    			}

    		}
    	</script>

    </head>
    <body>
    	<div align="center" style="width:100%;">
    		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
    			<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
    				<thead>
    					<tr>
    						<th>Sales Order</th>
    						<th>Buyer Name</th>
    						<th align="center">Requisition No</th>
    						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
    							class="formbutton"/></th>
    						</tr>
    					</thead>
    					<tbody>
    						<tr class="general">
    							<td>
    								<? echo create_drop_down("cbo_sales_order", 100, $yes_no, "", 0, "-- Select --", 2, "fnc_disable(this.value);", '', ''); ?>
    							</td>
    							<td>
    								<?
    								echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select --", $selected, "");
    								?>
    							</td>
    							<td width="180" align="center">
    								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
    								id="txt_search_common"/>
    							</td>
    							<td align="center">
    								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_show_check()"
    								style="width:100px;"/>
    							</td>
    						</tr>
    						<!-- Hidden field here -->
    						<input type="hidden" id="hidden_req_no" value=""/>
    						<!-- END -->
    					</tbody>
    				</tr>
    			</table>
    			<div align="center" valign="top" id="search_divs"></div>
    		</form>
    	</div>
    </body>

    </html>
    <?
    exit();
}

if ($action == "create_req_search_list") {
	$ex_data = explode("_", $data);
	$buyer = str_replace("'", "", $ex_data[0]);
	$req_no = str_replace("'", "", $ex_data[1]);
	$company = str_replace("'", "", $ex_data[2]);
	$sales_order = str_replace("'", "", $ex_data[3]);

	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$cond = "";
	if ($buyer != "" && $buyer != 0) $cond .= " and a.buyer_id=$buyer";
	if ($req_no != "" && $req_no != 0) $cond .= " and c.requisition_no=$req_no";
	if ($company != "" && $company != 0) $cond .= " and a.company_id=$company";

	if ($sales_order == 1) {
		$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

		$sql = "select a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id as knit_id, sum(c.yarn_qnty) as yarn_qnty,b.knitting_source,b.knitting_party,'' as issue_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and a.is_sales=1 and c.status_active=1 and c.is_deleted=0 $cond group by a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id,b.knitting_source,b.knitting_party";
		$result = sql_select($sql);
		foreach ($result as $row) {
			$plan_id_arr[]=$row[csf("knit_id")];
		}

		/*if ($db_type == 0) {
			$plan_details_array = return_library_array("select dtls_id, group_concat(po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company and is_sales=1 group by dtls_id", "dtls_id", "po_id");
		} else {
			$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company and is_sales=1 group by dtls_id", "dtls_id", "po_id");
		}*/
		$plan_cond = (!empty($plan_id_arr))?" and dtls_id in(".implode(",",$plan_id_arr).")":"";
		if ($db_type == 2) {
			$plan_details_array = sql_select("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company and is_sales=1 $plan_cond group by dtls_id");
		}else{
			$plan_details_array = sql_select("select dtls_id, group_concat(po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company and is_sales=1 $plan_cond group by dtls_id");
		}

		$plan_arr=array();
		foreach ($plan_details_array as $plan_row) {
			$sales_order_arr[$plan_row[csf("dtls_id")]] = $plan_row[csf("po_id")];
			$sales_order_id_arr[] = $plan_row[csf("po_id")];
		}

		$sales_no_array = array();
		$sales_id_cond = (!empty($sales_order_id_arr))?" and id in(".implode(",",$sales_order_id_arr).")":"";
		$salesData = sql_select("select id,job_no,style_ref_no,buyer_id,sales_booking_no,within_group from fabric_sales_order_mst where company_id=$company and status_active=1 and is_deleted=0 $sales_id_cond");
		foreach ($salesData as $row) {
			$sales_no_array[$row[csf('id')]]['sales_order'] = $row[csf('job_no')];
			$sales_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$sales_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
			$sales_no_array[$row[csf('id')]]['sales_booking'] = $row[csf('sales_booking_no')];
			$sales_booking[]= "'".$row[csf('sales_booking_no')]."'";
		}

		$job_arr = array();
		$booking_cond = (!empty($sales_booking))?" and c.booking_no in(".implode(",",$sales_booking).")":"";
		$jobData = sql_select("select a.job_no, a.style_ref_no,a.buyer_name, c.booking_no from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $booking_cond group by a.job_no, a.style_ref_no,a.buyer_name, c.booking_no");
		foreach ($jobData as $row) {
			$job_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
			$job_arr[$row[csf('booking_no')]]['buyer_name'] = $row[csf('buyer_name')];
		}
		$i = 1;
		?>
		<div align="center">
			<div style="width:970px;">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="100%" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="150">Buyer</th>
							<th width="120">Job No</th>
							<th width="130">Sales / Booking No</th>
							<th width="130">Style Ref.</th>
							<th width="100">Req. No</th>
							<th width="100">Req. Date</th>
							<th>Quantity</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:970px; overflow-y:scroll; max-height:250px;">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="950" rules="all"
				id="list_view">
				<tbody>
					<?
					foreach ($result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$job_id = array_unique(explode(",", $sales_order_arr[$row[csf('knit_id')]]));
						$style_ref = '';
						$sales_order = '';
						$buyer_id = '';
						$sales_booking = '';
						foreach ($job_id as $val) {
							if ($style_ref == '') $style_ref = $sales_no_array[$val]['style_ref'];
							if ($sales_order == '') $sales_order = $sales_no_array[$val]['sales_order'];
							if ($buyer_id == '') $buyer_id = $sales_no_array[$val]['buyer_id'];
							if ($sales_booking == '') $sales_booking = $sales_no_array[$val]['sales_booking'];
						}
						if ($row[csf('within_group')] == 1) {
							$buyer = $job_arr[$sales_booking]['buyer_name'];
							$job_no= $job_arr[$sales_booking]['job_no'];
						} else {
							$buyer = $buyer_id;
							$job_no="";
						}

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="js_set_value('<? echo $row[csf('requisition_no')] . ',' . $row[csf('company_id')] . ',' . $buyer . ',' . $row[csf("yarn_qnty")] . ',' . $row[csf("issue_qnty")] . ',' . $row[csf("knitting_source")]. ',' . $row[csf("knitting_party")]; ?>')"
							style="cursor:pointer">
							<td width="50"><?php echo $i; ?></td>
							<td width="150"><p><?php echo $buyer_arr[$buyer]; ?></p></td>
							<td width="120"><p><?php echo $job_no; ?></p></td>
							<td width="130"><p><?php echo $sales_order; ?></p></td>
							<td width="130"><p><?php echo $style_ref; ?></p></td>
							<td width="100" align="center"><p><?php echo $row[csf("requisition_no")]; ?></p></td>
							<td width="100" align="center"><p>
								<?php echo change_date_format($row[csf("requisition_date")]); ?></p></td>
								<td width="" align="right">
									<p><?php echo number_format($row[csf("yarn_qnty")], 2, '.', ''); ?></p></td>
								</tr>
								<?
								$i++;
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<?
		} else {
			$po_array = array();
			$po_sql = sql_select("select a.job_no,a.style_ref_no,b.grouping,b.file_no,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['internal'] = $row[csf('grouping')];
			}

			if ($db_type == 0) {
				$plan_details_array = return_library_array("select dtls_id, group_concat(po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company group by dtls_id", "dtls_id", "po_id");
			} else {
				$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company group by dtls_id", "dtls_id", "po_id");
			}


			/*$sql=sql_select("select approval_need from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company and b.page_id=27 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
			$app_nessity=2;
			foreach($sql as $row){
				$app_nessity=$row[csf('approval_need')];
			}*/

			$app_nessity=2;
			$app_nessity=return_field_value("yarn_iss_with_serv_app","variable_order_tracking","company_name =$company and variable_list=60 and is_deleted=0 and status_active=1");
			if($app_nessity=="" || $app_nessity==0){
				$app_nessity=2;
			}


			if($knitting_source==1){
				$sql = "select a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id as knit_id, sum(c.yarn_qnty) as yarn_qnty,b.knitting_source,b.knitting_party,'' as issue_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and a.is_sales != 1 and c.status_active=1 and c.is_deleted=0 $cond group by a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id,b.knitting_source,b.knitting_party";

			}else{
				if($app_nessity==2){
					$sql = "select a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id as knit_id, sum(c.yarn_qnty) as yarn_qnty,b.knitting_source,b.knitting_party,'' as issue_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and a.is_sales != 1 and c.status_active=1 and c.is_deleted=0 $cond group by a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id,b.knitting_source,b.knitting_party";
				}else{
					$sql = "select a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id as knit_id, sum(c.yarn_qnty) as yarn_qnty,b.knitting_source,b.knitting_party,'' as issue_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c,wo_booking_dtls d,wo_booking_mst e where a.id=b.mst_id and b.id=c.knit_id and b.id=d.program_no and d.booking_no=e.booking_no and e.is_approved=1 and a.is_sales != 1 and c.status_active=1 and c.is_deleted=0  $cond group by a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id,b.knitting_source,b.knitting_party";
				}
			}

			if($app_nessity==2){
				$sql = "select a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id as knit_id, sum(c.yarn_qnty) as yarn_qnty,b.knitting_source,b.knitting_party,'' as issue_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and a.is_sales != 1 and c.status_active=1 and c.is_deleted=0 $cond group by a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id,b.knitting_source,b.knitting_party";
			}else{
				$sql = "select a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id as knit_id, sum(c.yarn_qnty) as yarn_qnty,b.knitting_source,b.knitting_party,'' as issue_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c,wo_booking_dtls d,wo_booking_mst e where a.id=b.mst_id and b.id=c.knit_id and b.id=d.program_no and d.booking_no=e.booking_no and e.is_approved=1 and a.is_sales != 1 and c.status_active=1 and c.is_deleted=0  $cond group by a.company_id, a.buyer_id, a.within_group, a.booking_no, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id,b.knitting_source,b.knitting_party";
			}

			$result = sql_select($sql);
			$i = 1;
			?>
			<div align="center">
				<div style="width:970px;">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="100%" rules="all">
						<thead>
							<tr>
								<th width="50">SL</th>
								<th width="130">Buyer</th>
								<th width="250">Order No</th>
								<th width="100">Internal Ref</th>
								<th width="100">File No</th>
								<th width="100">Req. No</th>
								<th width="100">Req. Date</th>
								<th width="">Quantity</th>
							</tr>
						</thead>
					</table>
				</div>
				<div style="width:970px; overflow-y:scroll; max-height:250px;">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="950" rules="all"
					id="list_view">
					<tbody>
						<? foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$po_id = array_unique(explode(",", $plan_details_array[$row[csf('knit_id')]]));
							$po_no = '';
							$style_no = '';
							$job_no = '';
							$internal_ref = '';
							$file_no = '';

							foreach ($po_id as $val) {
								if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= "," . $po_array[$val]['no'];
								if ($job_no == '') $job_no = $po_array[$val]['job_no'];
								if ($style_no == '') $style_no = $po_array[$val]['style_ref'];
								if ($internal_ref == '') $internal_ref = $po_array[$val]['internal']; else $internal_ref .= "," . $po_array[$val]['internal'];
								if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onClick="js_set_value('<? echo $row[csf('requisition_no')] . ',' . $row[csf('company_id')] . ',' . $row[csf('buyer_id')] . ',' . $row[csf("yarn_qnty")] . ',' . $row[csf("issue_qnty")] . ',' . $row[csf("knitting_source")]. ',' . $row[csf("knitting_party")]; ?>')"
								style="cursor:pointer">
								<td width="50"><?php echo $i; ?></td>
								<td width="130"><p><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
								<td width="250"><p><?php echo $po_no; ?></p></td>
								<td width="100"><p><?php echo $internal_ref; ?>&nbsp;</p></td>
								<td width="100"><p><?php echo $file_no; ?>&nbsp;</p></td>
								<td width="100" align="center"><p>&nbsp;<?php echo $row[csf("requisition_no")]; ?></p></td>
								<td width="100" align="center"><p>
									&nbsp;<?php echo change_date_format($row[csf("requisition_date")]); ?></p></td>
									<td width="" align="right">
										<p><?php echo number_format($row[csf("yarn_qnty")], 2, '.', ''); ?></p></td>
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?
			}
			exit();
		}

		if ($action == "show_req_list_view") {
			$ex_data = explode(",", $data);
			$requisition_no = $ex_data[0];
			$company = $ex_data[1];
			$buyer = $ex_data[2];

			$product_array = array();
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
			$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
			$productDataArray = sql_select("select id, product_name_details, color, lot,yarn_count_id,yarn_comp_type1st,yarn_comp_percent1st,yarn_type from product_details_master where item_category_id=1");
			foreach ($productDataArray as $rowProd) {
		//$product_array[$rowProd[csf('id')]]['desc'] = $rowProd[csf('product_name_details')];
				$product_array[$rowProd[csf('id')]]['desc'] = $count_arr[$rowProd[csf('yarn_count_id')]]." ".$composition[$rowProd[csf('yarn_comp_type1st')]]." ".$rowProd[csf('yarn_comp_percent1st')]."% ".$yarn_type[$rowProd[csf('yarn_type')]]." ".$color_arr[$rowProd[csf('color')]];
				$product_array[$rowProd[csf('id')]]['color'] = $rowProd[csf('color')];
				$product_array[$rowProd[csf('id')]]['lot'] = $rowProd[csf('lot')];
			}

			if ($reqsation_no != '' || $company != 0) {
				$sql = "select a.buyer_id,c.requisition_no,c.prod_id,c.requisition_date,sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and requisition_no='$requisition_no' and a.company_id='$company' and c.status_active=1 and c.is_deleted=0 group by c.requisition_no,c.prod_id,c.requisition_date,a.buyer_id";
		//echo $sql;
			}

			$result = sql_select($sql);
	//$yes_no_req=count($result);
			$i = 1;
			?>
			<fieldset style="width:330px;">
				<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th>SL</th>
						<th>Product</th>
						<th>Lot No</th>
						<th>Color</th>
						<th>Qnty</th>
					</thead>
					<tbody>
						<?
						foreach ($result as $key => $val) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$product_name = $product_array[$val[csf("prod_id")]]['desc'];
							$color = $product_array[$val[csf("prod_id")]]['color'];
							$lot = $product_array[$val[csf("prod_id")]]['lot'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onClick="get_php_form_data('<? echo $val[csf("requisition_no")] . "_" . $val[csf("prod_id")]; ?>', 'populate_child_from_data', 'requires/yarn_issue_store_update_controller');"
								style="cursor:pointer">
								<td width="20"><? echo $i; ?></td>
								<td width="130"><? echo $product_name; ?></td>
								<td width="60"><p><? echo $lot; ?></p></td>
								<td width="60"><? echo $color_arr[$color]; ?></td>
								<td align="right"><? echo $val[csf("yarn_qnty")]; ?></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</fieldset>
			<?
			exit();
		}

		if ($action == "populate_child_from_data") {
			$exp_data = explode("_", str_replace("'", "", $data));
			$req_no = $exp_data[0];
			$prod_id = $exp_data[1];

	/*$sql = "select a.requisition_no,a.prod_id, a.yarn_qnty,b.*,(select sum(d.cons_quantity )  from inv_transaction d where a.requisition_no=d.requisition_no and b.id=d.prod_id  and d.status_active=1 and d.is_deleted=0 )issue_qnty,(select sum(c.cons_quantity) from inv_transaction c where b.id=c.prod_id  and c.transaction_type=4 and c.status_active=1 and c.is_deleted=0 group by c.issue_id) issue_return
	from ppl_yarn_requisition_entry a, product_details_master b
	where a.prod_id=b.id and a.requisition_no=$req_no and a.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";*/
	$balance_arr=array();
	$sql_trans = sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance,prod_id,store_id from inv_transaction where prod_id=" . $prod_id . " and status_active=1 and is_deleted=0 group by prod_id,store_id");
	foreach ($sql_trans as $trans_row) {
		$balance_arr[$trans_row[csf("prod_id")]][$trans_row[csf("store_id")]] += $trans_row[csf("balance")];
	}

	if($db_type==0)
	{
		$sql = "select a.requisition_no,a.prod_id, a.yarn_qnty,b.*,(select sum(d.cons_quantity )  from inv_transaction d where d.transaction_type=2 and d.item_category=1 and a.requisition_no=d.requisition_no and b.id=d.prod_id  and d.status_active=1 and d.is_deleted=0 )issue_qnty,(select group_concat(d.mst_id) from inv_transaction d where  d.transaction_type=2 and d.item_category=1 and a.requisition_no=d.requisition_no and b.id=d.prod_id and d.status_active=1 and d.is_deleted=0) issue_id,c.color_name
		from ppl_yarn_requisition_entry a, product_details_master b,lib_color c
		where a.prod_id=b.id and b.color=c.id and a.requisition_no=$req_no and a.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		$sql = "select a.requisition_no,a.prod_id, a.yarn_qnty,b.*,(select sum(d.cons_quantity )  from inv_transaction d where d.transaction_type=2 and d.item_category=1 and a.requisition_no=d.requisition_no and b.id=d.prod_id  and d.status_active=1 and d.is_deleted=0 )issue_qnty,(select listagg(cast(d.mst_id as varchar(4000)),',') within group (order by d.mst_id)  from inv_transaction d where d.transaction_type=2 and d.item_category=1 and a.requisition_no=d.requisition_no and b.id=d.prod_id  and d.status_active=1 and d.is_deleted=0) issue_id,c.color_name
		from ppl_yarn_requisition_entry a, product_details_master b,lib_color c
		where a.prod_id=b.id and b.color=c.id and a.requisition_no=$req_no and a.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}

	$result = sql_select($sql);
	foreach ($result as $row) {
		$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
		if ($row[csf('yarn_comp_type2nd')] != 0) $composition_string .= $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')];

		echo "$('#cbo_supplier').val('" . $row[csf("supplier_id")] . "');\n";
		//echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "$('#txt_lot_no').val('" . $row[csf("lot")] . "');\n";
		echo "$('#txt_prod_id').val('" . $row[csf("id")] . "');\n";
		//echo "$('#txt_current_stock').val(" . $row[csf("current_stock")] . ");\n";
		echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
		echo "$('#txt_composition').val('" . $composition_string . "');\n";
		echo "$('#cbo_yarn_type').val('" . $row[csf("yarn_type")] . "');\n";
		echo "$('#cbo_uom').val('" . $row[csf("unit_of_measure")] . "');\n";
		//echo "$('#cbo_color').val('" . $row[csf("color")] . "');\n";
		echo "$('#cbo_color').html('<option value=\'" . $row[csf("color")] . "\'>" . $row[csf("color_name")] . "</option>');\n";
		echo "$('#cbo_brand').val('" . $row[csf("brand")] . "');\n";
		if($row[csf("issue_id")]!="")
		{
			$issue_rtn_qnty=return_field_value("sum(cons_quantity) as cons_quantity", "inv_transaction", "issue_id in(".$row[csf("issue_id")].") and transaction_type=4 and status_active=1 and item_category=1", "cons_quantity");
		}

		echo "$('#hidden_p_issue_qnty').val('" . ($row[csf("issue_qnty")] - $issue_rtn_qnty) . "');\n";
		echo "$('#hdn_requis_qnty').val('" . $row[csf("yarn_qnty")] . "');\n";

		/*$sqsl = "select a.po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c
			where a.id=b.mst_id and b.id=c.knit_id and requisition_no=".$row[csf("requisition_no")];*/
			if ($db_type == 0) {
				$poID = return_field_value("group_concat(distinct(a.po_id)) as poid", "ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c", "b.id=a.dtls_id and b.id=c.knit_id and requisition_no='" . $row[csf("requisition_no")] . "'", "poid");

				$invData = sql_select("select group_concat(distinct(store_id)) as store_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where prod_id=" . $row[csf('id')] . " and item_category=1 and transaction_type in(1,4,5)");
				$store_id = $invData[0][csf('store_id')];
			} else {
				$poID = return_field_value("LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid", "ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c", "b.id=a.dtls_id and b.id=c.knit_id and requisition_no='" . $row[csf("requisition_no")] . "'", "poid");

				$invData = sql_select("select LISTAGG(store_id, ',') WITHIN GROUP (ORDER BY store_id) as store_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where prod_id=" . $row[csf('id')] . " and item_category=1 and transaction_type in(1,4,5)");

				$store_id = $invData[0][csf('store_id')];
				//$store_id = implode(",", array_unique(explode(",", $store_id)));

			}
			echo "load_drop_down( 'requires/yarn_issue_store_update_controller', '$store_id', 'load_drop_down_store_req', 'store_td' );\n";
			echo "$('#cbo_store_name').val('" . $store_id. "');\n";
			//$sql_trans = sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance from inv_transaction where prod_id=" . $row[csf('id')] . " and store_id in($store_id) and status_active=1 and is_deleted=0");
			$store_id = array_unique(explode(",", $store_id));
			foreach ($store_id as $store_row) {
				$store_balance += $balance_arr[$row[csf('id')]][$store_row];
			}

			echo "$('#txt_current_stock').val('" . $store_balance . "');\n";
			echo "set_all_onclick();\n";
			echo "$('#txt_weight_per_bag').val('" . $invData[0][csf("weight_per_bag")] . "');\n";
			echo "$('#txt_weight_per_cone').val('" . $invData[0][csf("weight_per_cone")] . "');\n";

			echo "$('#all_po_id').val('" . $poID . "');\n";
		}
		exit();
	}

	if ($action == "populate_req_store_data") {
		$exp_data = explode("**", str_replace("'", "", $data));
		$prod_id = $exp_data[0];
		$store_id = $exp_data[1];
		$sql_trans = sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance from inv_transaction where prod_id=$prod_id and store_id=$store_id and status_active=1 and is_deleted=0");
		echo "$('#txt_current_stock').val('" . $sql_trans[0][csf("balance")] . "');\n";
	}

	if ($action == "show_yarn_dyeing_list_view") {
		$product_array = return_library_array("select id, lot from product_details_master where item_category_id=1", 'id', 'lot');
		$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

		$sql = "select id, job_no,job_no_id, product_id, count, yarn_description, entry_form, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, yarn_color, yarn_wo_qty from wo_yarn_dyeing_dtls where mst_id=$data and status_active=1 and is_deleted=0";
		$result = sql_select($sql);
		foreach ($result as $row) {
			$sample_arr[] = $row[csf("job_no_id")];
			if ($row[csf("entry_form")] == 42 || $row[csf("entry_form")] == 114) {
				$job_label = "Style";
			}else{
				$job_label = "Job";
			}
		}
		$sample_array = return_library_array("select id,style_ref_no from sample_development_mst where id in(".implode(",",$sample_arr).") and status_active=1 and is_deleted=0 ", 'id', 'style_ref_no');
		?>
		<fieldset style="width:310px;">
			<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" border="1">
				<thead>
					<th>SL</th>
					<th><? echo $job_label;?></th>
					<th>Lot</th>
					<th>Product</th>
					<th>Qnty</th>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach ($result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						if ($row[csf("entry_form")] == 114 || $row[csf("entry_form")] == 125) {
							$product_name = $count_arr[$row[csf("count")]] . " " . $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . " " . $yarn_type[$row[csf("yarn_type")]] . " " . $color_name_arr[$row[csf("yarn_color")]];
							$lot = "&nbsp;";
						} else {
							if ($row[csf("product_id")] == 0) {
								$product_name = $count_arr[$row[csf("count")]] . " " . $row[csf("yarn_description")];
								$lot = "&nbsp;";
							} else {
								$product_name = $count_arr[$row[csf("count")]] . " " . $row[csf("yarn_description")];
								$lot = $product_array[$row[csf("product_id")]];
							}
						}
						if ($row[csf("entry_form")] == 42 || $row[csf("entry_form")] == 114) {
							$job_no = $sample_array[$row[csf("job_no_id")]];
						}else{
							$job_no = $row[csf("job_no")];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="get_php_form_data(<? echo $row[csf("id")]; ?>, 'populate_child_from_yarn_dyeing_data', 'requires/yarn_issue_store_update_controller');"
							style="cursor:pointer">
							<td width="20"><? echo $i; ?></td>
							<td width="80"><? echo $job_no; ?>&nbsp;</td>
							<td width="50"><p><? echo $lot; ?></p></td>
							<td><p><? echo $product_name; ?></p></td>
							<td width="50" align="right"><? echo $row[csf("yarn_wo_qty")]; ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</fieldset>
		<?
		exit();
	}

	if ($action == "populate_child_from_yarn_dyeing_data") {
		$sql = "select a.id, a.job_no, a.product_id, a.count, a.yarn_description, a.entry_form, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type, a.yarn_color, a.yarn_wo_qty,b.lot,b.supplier_id from wo_yarn_dyeing_dtls a left join product_details_master b on a.product_id=b.id where a.id=$data and a.status_active=1 and a.is_deleted=0";
		$result = sql_select($sql);
		foreach ($result as $row) {
			echo "$('#job_no').val('" . $row[csf("job_no")] . "');\n";
			$style_ref_no = return_field_value("style_ref_no", "wo_po_details_master", "job_no='" . $row[csf("job_no")] . "'");
			echo "$('#txt_style_ref').val('" . $style_ref_no . "');\n";
			echo "$('#txt_buyer_job_no').val('" . $row[csf("job_no")] . "');\n";
			if ($row[csf("entry_form")] == 42 || $row[csf("entry_form")] == 114) {
				echo "$('#txt_issue_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick');\n";
			} else {
				echo "$('#txt_issue_qnty').attr('placeholder','Double Click').attr('onDblClick','openmypage_po()').attr('readonly',true);\n";
			}

			if ($row[csf("entry_form")] == 114 || $row[csf("entry_form")] == 125 || $row[csf("entry_form")] == 135) {
				echo "$('#cbo_yarn_count').val(" . $row[csf("count")] . ");\n";
				$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
				echo "$('#txt_lot_no').val('" . $row[csf("lot")] . "');\n";
				echo "$('#txt_prod_id').val('" . $row[csf("product_id")] . "');\n";
				echo "$('#txt_composition').val('" . $composition_string . "');\n";
				echo "$('#cbo_yarn_type').val('" . $row[csf("yarn_type")] . "');\n";
				echo "$('#cbo_color').val('" . $row[csf("yarn_color")] . "');\n";
				echo "$('#cbo_dyeing_color').val('" . $row[csf("yarn_color")] . "');\n";
				echo "$('#txt_composition_id').val('" . $row[csf("yarn_comp_type1st")] . "');\n";
				echo "$('#txt_composition_percent').val('" . $row[csf("yarn_comp_percent1st")] . "');\n";
				echo "$('#cbo_supplier').val('" . $row[csf("supplier_id")] . "');\n";
			} else {
				$prodData = sql_select("select id, unit_of_measure, supplier_id, lot, current_stock, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, brand, item_code from product_details_master where id=" . $row[csf("product_id")]);

				$composition_string = $composition[$prodData[0][csf('yarn_comp_type1st')]] . " " . $prodData[0][csf('yarn_comp_percent1st')] . "%";
				if ($prodData[0][csf('yarn_comp_type2nd')] != 0) $composition_string .= $composition[$prodData[0][csf('yarn_comp_type2nd')]] . " " . $prodData[0][csf('yarn_comp_percent2nd')] . "%";

				echo "$('#cbo_supplier').val('" . $prodData[0][csf("supplier_id")] . "');\n";
				echo "$('#txt_lot_no').val('" . $prodData[0][csf("lot")] . "');\n";
				echo "$('#txt_prod_id').val('" . $prodData[0][csf("id")] . "');\n";
				echo "$('#txt_current_stock').val(" . $prodData[0][csf("current_stock")] . ");\n";
				echo "$('#cbo_yarn_count').val(" . $prodData[0][csf("yarn_count_id")] . ");\n";
				echo "$('#txt_composition').val('" . $composition_string . "');\n";
				echo "$('#cbo_yarn_type').val('" . $prodData[0][csf("yarn_type")] . "');\n";
				echo "$('#cbo_uom').val('" . $prodData[0][csf("unit_of_measure")] . "');\n";
				echo "$('#cbo_color').val('" . $prodData[0][csf("color")] . "');\n";
				echo "$('#cbo_brand').val('" . $prodData[0][csf("brand")] . "');\n";

				if ($db_type == 0) {
					$invData = sql_select("select group_concat(distinct(store_id)) as store_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where prod_id=" . $row[csf("product_id")] . " and item_category=1 and transaction_type in(1,5,4)");
					$store_id = $invData[0][csf('store_id')];
				} else {
					$invData = sql_select("select LISTAGG(store_id, ',') WITHIN GROUP (ORDER BY store_id) as store_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where prod_id=" . $row[csf("product_id")] . " and item_category=1 and transaction_type in(1,5,4)");
					$store_id = $invData[0][csf('store_id')];
					$store_id = implode(",", array_unique(explode(",", $store_id)));
				}
				echo "load_drop_down( 'requires/yarn_issue_store_update_controller', '$store_id', 'load_drop_down_store_req', 'store_td' );\n";
				echo "$('#cbo_store_name').val('" . $store_id . "');\n";
				echo "$('#txt_weight_per_bag').val('" . $invData[0][csf("weight_per_bag")] . "');\n";
				echo "$('#txt_weight_per_cone').val('" . $invData[0][csf("weight_per_cone")] . "');\n";
			}
			echo "$('#hdn_wo_qnty').val('" . $row[csf("yarn_wo_qty")] . "');\n";
		}
		exit();
	}

	if ($action == "yarn_issue_print")
	{
		extract($_REQUEST);
		echo load_html_head_contents("Yarn Issue Challan Print", "../../", 1, 1, '', '', '');
		$data = explode('*', $data);

		$print_with_vat = $data[7];
		//$supplier_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
		$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
		$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	    //$other_party_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$sql = "select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";
	    //echo $sql;die;

		$dataArray = sql_select($sql);

		if( $dataArray[0][csf('issue_basis')]==3 )
		{
			$planbooking = sql_select("select e.booking_no as pln_booking_no from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_info_entry_dtls d,ppl_planning_info_entry_mst e where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.id and d.mst_id=e.id and a.id='$data[5]' ");
		}

		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

		$copyNo = "";
		for ($x = 1; $x <= 3; $x++)
		{

			if($x==1)
			{
				$copyNo ="<span style='font-size:x-large;'>1<sup>st</sup> Copy</span>";
			} else if($x==2){
				$copyNo ="<span style='font-size:x-large;'>2<sup>nd</sup> Copy</span>";
			} else {
				$copyNo ="<span style='font-size:x-large;'>3<sup>rd</sup> Copy</span>";
			}
			?>
			<div style="width:1100px;">
				<table width="1100" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
					<tr>
						<td colspan="6" align="center" style="font-size:18px"></td>


						<td style="font-size:x-large; font-style:italic;" align="right">
							<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
						</td>
					</tr>
					<tr class="form_caption">

						<?
						$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
						?>
						<td align="left" width="50">
							<?
							foreach ($data_array as $img_row) {
								if ($data[5] != 1) {
									?>
									<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								} else {
									?>
									<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								}
							}
							?>
						</td>
						<td colspan="2" align="center">
							<strong style="font-size:18px"><? echo $company_library[$data[0]]; ?></strong><br>
							<?
							echo show_company($data[0], '', array('city'));
							?>
						</td>
						<td colspan="2" id="barcode_img_id">&nbsp;</td>
						<td colspan="3" style="color:black; font-weight:bold;"><? echo $copyNo;?></td>
					</tr>
					<tr>
						<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan/Gate
						Pass</u></strong></center></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="160"><strong>Issue No:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
						<td width="120"><strong>Booking:</strong></td>
						<td width="175px">
							<?
							if( $dataArray[0][csf('issue_basis')]==3 )
							{
								$bookingNo = $planbooking[0][csf('pln_booking_no')];
							} else {
								$bookingNo = $dataArray[0][csf('booking_no')];
							}
							echo $bookingNo;
							?>
						</td>
						<td width="125"><strong>Knitting Source:</strong></td>
						<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Challan/Program No:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
						<td><strong>Issue Purpose:</strong></td>
			<td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else
			echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
			?></td>
			<td><strong>Issue Date:</strong></td>
			<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
		</tr>
		<tr>
			<td><strong>Issue To:</strong></td>
			<?
			if ($dataArray[0][csf('knit_dye_source')] == 3) {
				$supp_add = $dataArray[0][csf('knit_dye_company')];
				$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
				foreach ($nameArray as $result) {
					$address = "";
								if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
							}
							unset($nameArray);
						}

						$loan_party_add = $dataArray[0][csf('loan_party')];
						$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
						foreach ($loanPartyArray as $result) {
							$addressParty = "";
							if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
						}
						unset($loanPartyArray);
						?>
						<td colspan="3">
							<?
							if ($dataArray[0][csf('issue_purpose')] == 3) {
								echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
							} else if ($dataArray[0][csf('issue_purpose')] == 5) {
								echo $supplier_arr[$dataArray[0][csf('loan_party')]] . ' : Address :- ' . $addressParty;
							} else {
								if ($dataArray[0][csf('knit_dye_source')] == 1)
									echo $company_library[$dataArray[0][csf('knit_dye_company')]];
								else
									echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]] . ' : Address :- ' . $address;
							}
							?>
						</td>
						<td colspan="2"><strong>Location : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong>
						</td>
					</tr>
					<tr>
						<td><strong>Demand No.:</strong></td>
						<td width="175px"><? //echo $dataArray[0][csf('booking_id')];
						?></td>
						<td><strong>Gate Pass No.:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
						<td><strong>Do No:</strong></td>
						<td width="175px"><? //echo $dataArray[0][csf('remarks')];
						?></td>
					</tr>
					<tr>
						<td valign="top"><strong>Remarks :</strong></td>
						<td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
					</tr>
					<?
					if ($print_with_vat == 1) {
						?>
						<tr>
							<td><strong>VAT Number :</strong></td>
							<td><p>
								<?
								$vat_no = return_field_value("vat_number", "lib_company", "id=" . $data[0], "vat_number");
								echo $vat_no;
								?></p></td>

							</tr>
							<?
						} else {
							?>
							<tr>
								<td valign="top">&nbsp;</strong></td>
								<td>&nbsp;</td>

							</tr>
							<?
						}
						?>
					</table>
					<br>
					<div style="width:100%;">
						<div style="clear:both;">
							<table style="margin-right:-40px;" cellspacing="0" width="1000" border="1" rules="all"
							class="rpt_table">
							<thead bgcolor="#dddddd" style="font-size:13px">
								<th width="20">SL</th>
								<th width="45">Req. No</th>
								<th width="50">Lot No</th>
								<th width="65">Y. Type</th>
								<th width="110">Item Details</th>
								<th width="65">Grey Color</th>
								<th width="65">Dye. Color</th>
								<th width="60">Supp.</th>
								<th width="60"><b>Issue Qty(kg)</b></th>
								<th width="60">Rtnbl Qty(kg)</th>
								<th width="80">Bag & Cone</th>
								<th width="80">Store</th>
								<th width="100">Buyer & Job</th>
								<th width="120">Order & Style</th>
								<th>Inte. Ref & File</th>
							</thead>
							<?
							$wopi_library = return_library_array("select id,pi_number from com_pi_master_details", "id", "pi_number");

							$cond = "";
							if ($data[5] != "") $cond .= " and c.id='$data[5]'";

							$po_array = array();
							$job_array = array();
							$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0]");
							foreach ($costing_sql as $row) {
								$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
								$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
								$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
								$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
								$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
								$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
								$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
								$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
							}
							unset($costing_sql);

							$job_no_array = array();
							$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id from fabric_sales_order_mst");
							foreach ($jobData as $row) {
								$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
								$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
								$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
								$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
							}
						//var_dump($po_array);
							$po_id_req_array = array();
							$is_sales_array = array();
							if ($db_type == 0) {
								$poID = " select c.requisition_no, a.is_sales, group_concat(distinct(a.po_id)) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
							} else {
								$poID = "select c.requisition_no, a.is_sales, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
							}
							$poID_sql_result = sql_select($poID);
							foreach ($poID_sql_result as $row) {
								$po_id_req_array[$row[csf('requisition_no')]] = $row[csf('poid')];
								$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
							}
							unset($poID_sql_result);

							$i = 1;
							if ($db_type == 0) {
								$sql_result = sql_select("select a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro
									from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id
									where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond
									group by a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no");
							} else {
								$sql_result = sql_select("select a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, LISTAGG(d.po_breakdown_id, ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro
									from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond
									group by a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no");
							}
							$all_req_no = '';
							$all_prod_id = '';
							$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
							$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
							foreach ($sql_result as $row) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								if ($row[csf('requisition_no')] != '') {
									if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
									if ($all_prod_id == '') $all_prod_id = $row[csf('po_id')]; else $all_prod_id .= ',' . $row[csf('po_id')];
								}
								$order_qnty_val = $row[csf('cons_quantity')];
								$order_qnty_val_sum += $order_qnty_val;

								$order_amount_val = $row[csf('order_amount')];
								$order_amount_val_sum += $order_amount_val;

								$no_of_bags_val = $row[csf('no_of_bags')];
								$no_of_bags_val_sum += $no_of_bags_val;

								$po_id = explode(",", $row[csf('po_id')]);
								$po_no = '';
								$style_ref = '';
								$job_no = '';
								$buyer = '';
							//$data=explode('*',$data);
								$productdtls = $row[csf('product_name_details')];
								$productArr = explode(' ', $productdtls);

								$proddtls='';
								$proddtls=$yarn_count_details[$row[csf('yarn_count_id')]];
								if($row[csf('yarn_count_id')]>0) $proddtls.=", ";
								$proddtls.=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%";

							/*if($row[csf('yarn_comp_type1st')]>0) $proddtls.=", ";
							$proddtls.=$yarn_type[$row[csf('yarn_type')]];*/

							/*if ($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='' && $productArr[3]!='' && $productArr[4]!=''&& $productArr[5]!='')
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].'%, '.$productArr[3].' '.$productArr[4]."%, ".$productArr[5].", ".$productArr[6];
							}
							else if ($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='' && $productArr[3]!='' && $productArr[4]!='')
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].' %, '.$productArr[3].', '.$productArr[4];
							}
							else if($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='' && $productArr[3]!='' )
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].' %, '.$productArr[3];
							}
							else if($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='')
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].' %, ';
							}
							else if($productArr[0]!='' && $productArr[1]!='' )
							{
								$proddtls=$productArr[0].', '.$productArr[1];
							}
							else if($productArr[0]!='')
							{
								$proddtls=$productArr[0];
							}
							else
							{
								$proddtls='';
							}*/

							$internal_ref = '';
							$file_no = '';
							if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2) {
								foreach ($po_id as $val) {
									if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
									if ($job_no == '') $job_no = $po_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
									if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

									if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
									if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
								}
								$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
								$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
								$ref_cond = '';
								foreach ($job_ref as $ref) {
									//$ref_cond.=", ".$ref;
									if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
								}
								$file_con = '';
								foreach ($job_file as $file) {
									if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
								}



								$buyer_job = '';
								if ($row[csf('issue_basis')] == 1) {
									if ($dataArray[0][csf('issue_purpose')] == 8) {
										$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
									} else {
										$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
										if ($row[csf('buyer_job_no')] != "") {
											$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
										}
									}
								} else if ($row[csf('issue_basis')] == 2) {
									$buyer_job = '';
								}
							} else if ($row[csf('issue_basis')] == 3) {
								//$ex_data = array_unique(explode(",",$po_id_req_array[$row[csf('requisition_no')]]));
								//print_r($ex_data);

								if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
									$buyer_job = '';
									foreach (array_unique($po_id) as $val) {
										if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
										if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];

										if ($buyer_job == '') {
											if ($job_no_array[$val]['within_group'] == 1) {
												$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
											} else {
												$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
											}
										}
									}
								} else {
									foreach ($po_id as $val) {
										if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
										if ($job_no == '') $job_no = $po_array[$val]['job_no'];
										if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
										if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

										if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
										if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
									}

									$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
									$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

									$ref_cond = '';
									foreach ($job_ref as $ref) {
										//$ref_cond.=", ".$ref;
										if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
									}

									$file_con = '';
									foreach ($job_file as $file) {
										if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
									}
									if ($job_no != '') {
										$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
									} else {
										$buyer_job = $buyer_arr[$buyer_name];
									}
								}
							} else {
								foreach (array_unique($po_id) as $val) {
									if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
									if ($buyer_job == '') {
										if ($job_no_array[$val]['within_group'] == 1) {

											$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
										} else {
											$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
										}
									}
								}
							}

							$no_bag_cone = "";
							$wt_bag_cone = "";
							if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

								if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
										<td width="20"><? echo $i; ?></td>
										<td width="45">
											<div style="word-wrap:break-word; width:45px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
										</td>
										<td width="50">
											<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></div>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
												<?
										//echo $proddtls;
												echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_name_arr[$row[csf('color')]];
												?>
											</div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('color')]]; ?></div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('dyeing_color_id')]]; ?>
											&nbsp;</div>
										</td>
										<td width="60">
											<div style="word-wrap:break-word; width:60px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div>
										</td>
										<td align="right"
										width="60"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>
										<td align="right"
										width="60"><? echo number_format($row[csf('returnable_qnty')], 2, '.', ''); ?></td>
										<td width="80">
											<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
											&nbsp;</div>
										</td>
										<td width="80">
											<div style="word-wrap:break-word; width:80px"><? echo $store_arr[$row[csf('store_id')]]; ?></div>
										</td>
										<td width="100">
											<div style="word-wrap:break-word; width:100px"><? echo $buyer_job; ?></div>
										</td>
										<td width="120">
											<div style="word-wrap:break-word; width:120px"><? echo $po_no;
											if ($style_ref != "") echo ' & ' . $style_ref; else echo '&nbsp;'; ?></div>
										</td>
										<td>
											<div style="word-wrap:break-word; width:80px"><? echo ltrim($ref_cond, ", ");
											if ($file_cond != "") echo ' & ' . ltrim($file_cond, ", "); else echo '&nbsp;'; ?></div>
										</td>
									</tr>
									<?
									$uom_unit = "Kg";
									$uom_gm = "Grams";
									$tot_return_qty += $row[csf('returnable_qnty')];
									$tot_bag += $no_of_bags_val;
									$tot_cone += $row[csf('cone_per_bag')];
									$tot_w_bag += $row[csf('weight_per_bag')];
									$tot_w_cone += $row[csf('weight_per_cone')];
									$req_no = $row[csf('requisition_no')];
									$i++;
								}
								?>
								<tr bgcolor="#CCCCCC" style="font-size:13px">
									<td align="right" colspan="8"><b>Total</b></td>
									<td align="right">
										<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
									</td>
									<td align="right"><? echo number_format($tot_return_qty, 2, '.', ''); ?></td>
									<td><? echo 'N:' . number_format($tot_bag, 2);
									if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
									echo '<br>W:' . number_format($tot_w_bag, 2);
									if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>
									<td align="right" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="14" align="left"><b>In
										Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
									</tr>
								</table>
							</div>
							<br>
							<!--================================================================-->
							<?
							if ($data[6] == 1)
							{
								if ($dataArray[0][csf('issue_basis')] == 3)
								{
									?>
									<div style="">
										<table width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
											<thead>
												<tr>
													<th colspan="7" align="center">Requisition Details</th>
												</tr>
												<tr>
													<th width="40">SL</th>
													<th width="100">Requisition No</th>
													<th width="110">Lot No</th>
													<th width="220">Yarn Description</th>
													<th width="110">Brand</th>
													<th width="90">Requisition Qty</th>
													<th>Remarks</th>
												</tr>
											</thead>
											<?
						//echo $all_req_no;
											$i = 1;
											$tot_reqsn_qnty = 0;
											$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
											$product_details_array = array();
											$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id='$data[0]' and status_active=1 and is_deleted=0";
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
											}
											unset($result);
											$sql = "select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where requisition_no in ($all_req_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
											$nameArray = sql_select($sql);
											foreach ($nameArray as $selectResult) {
												?>
												<tr>
													<td width="40" align="center"><? echo $i; ?></td>
													<td width="100" align="center">&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
													<td width="110" align="center">
														&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
														<td width="220">
															&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
															<td width="110" align="center">
																&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
																<td width="90" align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?>
																&nbsp;</td>
								<td>&nbsp;<? //echo $selectResult[csf('requisition_no')];
								?></td>
							</tr>
							<?
							$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
							$i++;
						}
						?>
						<tfoot>
							<th colspan="5" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<?
					if ($data[5] != 1) {
						$z = 1;
						$k = 1;
						$colorArray = sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.requisition_no in ($all_req_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");

						$booking_al = "";
						foreach ($colorArray as $book) {
							if ($booking_al == "") $booking_al = $book[csf('booking_no')]; else $booking_al .= ',' . $book[csf('booking_no')];
						}
						//unset($colorArray);
						//echo $booking_no;
						$booking_no = "'" . implode(',', array_unique(explode(',', $booking_al))) . "'";
						$booking_count = count(array_unique(explode(',', $booking_no)));
						//$booking_job_arr=array();
						if ($dataArray[0][csf('issue_purpose')] == 2) {
							$booking_job = sql_select("select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no in ($booking_no) group by b.job_no");
						} else {
							$booking_job = sql_select("select job_no from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no in ($booking_no) group by  job_no");
						}
						//
						$booking_job_no = $booking_job[0][csf('job_no')];
						unset($booking_job);

						if ($db_type == 0) $poNoCond = "group_concat(id)";
						else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
						/*echo '='.$sql_Job.'=';
					if($sql_Job!="")
					{*/
						$sql_returnJob = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");

						$job_po = $sql_returnJob[0][csf('po_break_down_id')];
						unset($sql_returnJob);
						?>

						<table width="710" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;"
						class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="250">Fabrication</th>
							<th width="120">Color</th>
							<th width="120">GGSM OR S/L</th>
							<th>FGSM</th>
						</thead>
						<tbody>
							<tr>
								<td width="40" align="center"><? echo $z; ?></td>
								<td width="250"
								align="center"><? echo $body_part[$colorArray[0][csf('body_part_id')]] . ', ' . $colorArray[0][csf('fabric_desc')]; ?></td>
								<td width="120"
								align="center"><? echo $color_range[$colorArray[0][csf('color_range')]]; ?></td>
								<td width="120" align="center"><? echo $colorArray[0][csf('stitch_length')]; ?></td>
								<td align="center"><? echo $colorArray[0][csf('gsm_weight')]; ?></td>
							</tr>
						</tbody>
					</table>
					<table style="margin-top:20px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0"
					class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="50">Prog. No</th>
						<th width="110">Finish Dia</th>
						<th width="170">Machine Dia & Gauge</th>
						<th width="110">Program Qty</th>
						<th>Remarks</th>
					</thead>
					<tbody>
						<?
						$k=1;
						foreach ($colorArray as $program_row) {
							?>
							<tr>
								<td width="40" align="center"><? echo $k; ?></td>
								<td width="50" align="center"><? echo $program_row[csf('knit_id')]; ?></td>
								<td width="110" align="center"><? echo $program_row[csf('dia')]; ?></td>
								<td width="170" align="center"><? echo $program_row[csf('machine_dia')] . "X" . $program_row[csf('machine_gg')]; ?></td>
								<td width="110" align="right"><? echo number_format($program_row[csf('program_qnty')], 2); ?></td>
								<td><? echo $program_row[csf('remarks')]; ?></td>
							</tr>
							<?
							$k++;
							$tot_prog_qty += $program_row[csf('program_qnty')];
						}
						?>
						<tr>
							<td colspan="4" align="right"><strong>Total : </strong></td>
							<td align="right"><? echo number_format($tot_prog_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
				<?
				$returnJob = '';
				$returnPo = '';
				if ($db_type == 0) {
					$booking_cond = "group_concat(a.booking_no)";
					$wo_cond = "group_concat(a.ydw_no)";
					$reqs_no = "group_concat(b.requisition_no)";
				} else if ($db_type == 2) {
					$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
					$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
					$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
				}

				if ($dataArray[0][csf('issue_purpose')] == 8) {
					$sql_returnJob = sql_select("select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_no) group by a.id");
				} else if ($dataArray[0][csf('issue_purpose')] == 2) {
					$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$booking_job_no' group by b.job_no");

					if ($db_type == 0) $poNoCond = "group_concat(id)";
					else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
					$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");
				} else {

							$sql_returnJob = sql_select("select a.job_no, a.booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$booking_job_no' and a.booking_no in($booking_no) group by a.job_no,a.booking_no"); //and a.booking_no='".$dataArray[0][csf('booking_no')]."'

						}
						$returnJob = $sql_returnJob[0][csf('job_no')];
						//$returnPo=$sql_po[0][csf('po_break_down_id')];
						$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
						$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";
						$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
						unset($sql_returnJob);

						/*$req_no_retInJob=sql_select("select $reqs_no as req_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");
						$reqNo_inJob=$req_no_retInJob[0][csf('req_no')];*/
						?>
						<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0"
						class="rpt_table">
						<thead>
							<tr>
								<th colspan="6" align="center">Comments (Booking No:<p> <? echo $returnBooking; ?>) [Booking Job
								Deatils]</p></th>
							</tr>
							<tr>
								<th>Buyer</th>
								<th>Job</th>
								<th>Req. Qty</th>
								<th>Cuml. Issue Qty</th>
								<th>Balance Qty</th>
								<th>Remarks</th>
							</tr>
						</thead>
						<?

						$all_requ_no = "";
						if ($dataArray[0][csf('issue_purpose')] != 8) {
							$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 ");

							//$all_requ_no="'".$all_booking_sql[0][csf('requisition_no')]."'";
							$all_requ_no = "'" . implode("','", array_unique(explode(",", $all_booking_sql[0][csf('requisition_no')]))) . "'";
						}
						if ($dataArray[0][csf('issue_purpose')] == 8) {
							$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3", "total_issue_qty");    //
							$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=1", "total_issue_qty");
						} else if ($dataArray[0][csf('issue_purpose')] == 2) {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}

							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and b.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
						} else {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}
							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");


							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");
						}
						$cumulative_qty = 0;
						if ($booking_count == 1) {
							?>
							<tbody>
								<tr>
									<td align="center">
										<? echo $buyer_arr[$job_array[$booking_job_no]['buyer']]; ?>
									</td>
									<td align="center">
										<? echo $job_array[$booking_job_no]['job']; ?>
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($returnReqQty < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}
			}
			else if ($dataArray[0][csf('issue_basis')] == 1) {
				?>
				<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0"
				class="rpt_table">
				<thead>
					<tr>
						<th colspan="6" align="center">Comments <? if ($dataArray[0][csf('issue_purpose')] != 8) {
							if ($dataArray[0][csf('buyer_job_no')] != '') echo "(Booking Job Details)";
						} ?> </th>
					</tr>
					<tr>
						<th>Buyer</th>
						<th>Job</th>
						<th>Req. Qty</th>
						<th>Cuml. Qty</th>
						<th>Balance Qty</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<?
							//$booking_job_arr=array();
				$returnJob = '';
				$returnPo = '';
				if ($db_type == 0) {
					$booking_cond = "group_concat( distinct a.booking_no)";
					$wo_cond = "group_concat(a.ydw_no)";
					$reqs_no = "group_concat(b.requisition_no)";
				} else if ($db_type == 2) {
					$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
					$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
					$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
				}

				if ($dataArray[0][csf('issue_purpose')] == 8) {
					$sql_returnJob = sql_select("select a.id, a.buyer_id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id, a.buyer_id");
				} else if ($dataArray[0][csf('issue_purpose')] == 2) {
					if ($dataArray[0][csf('buyer_job_no')] != '') {
						$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by b.job_no");

						if ($db_type == 0) $poNoCond = "group_concat(id)";
						else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
						$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='" . $dataArray[0][csf('buyer_job_no')] . "' group by job_no_mst");
					} else {
						$sql_returnJob = sql_select("select a.id, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.item_category_id=24 and a.entry_form in(42,114) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id");
					}
				} else {
								//echo "select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='".$dataArray[0][csf('buyer_job_no')]."' group by a.job_no";
								$sql_returnJob = sql_select("select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by a.job_no");//and a.booking_no='".$dataArray[0][csf('booking_no')]."'
							}
							$returnJob = $sql_returnJob[0][csf('job_no')];
							$returnPo = $sql_po[0][csf('po_break_down_id')];
							$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
							$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";

							$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
							unset($sql_returnJob);

							//$booking_all="'".implode("','",explode(",",$bill_item_id))."'";
							//echo $returnJob.'---------'.$returnPo.'dfgdfgd';

							/*if( $dataArray[0][csf('issue_purpose')]==8 )
						{
							$sql = "select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$dataArray[0][csf('booking_no')]."' group by a.id";
						}
						else if( $dataArray[0][csf('issue_purpose')]==2)
						{
							if($dataArray[0][csf('buyer_job_no')]!="")
							{
								$sql = "select sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='".$dataArray[0][csf('buyer_job_no')]."'"; // and a.ydw_no='".$dataArray[0][csf('booking_no')]."'
							}
						}
						else
						{
							if($dataArray[0][csf('buyer_job_no')]!="")
							{
								$sql = "select a.job_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($returnBooking) and a.job_no='".$dataArray[0][csf('buyer_job_no')]."' group by a.job_no";
							}
						}
						$result = sql_select($sql);*/
						$all_requ_no = "";
						if ($dataArray[0][csf('issue_purpose')] != 8) {
								//echo "select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no";
							$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no");

							$all_requ_no = $all_booking_sql[0][csf('requisition_no')];
							unset($all_booking_sql);
						}

						if ($dataArray[0][csf('issue_purpose')] == 8) {
								$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3", "total_issue_qty");    //
								$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1", "total_issue_qty");
							} else if ($dataArray[0][csf('issue_purpose')] == 2) {
								/*if($all_requ_no!="" || $all_requ_no!=0)
							{
								//$req_cond="or a.requisition_no in ($all_requ_no)";
								//$reqRet_cond="or b.booking_no in ($all_requ_no)";
							}
							else
							{
								//$req_cond="or a.requisition_no in (0)";
								$reqRet_cond="or b.booking_no in (0)";
							}*/
							if ($dataArray[0][csf('buyer_job_no')] != "") {
								$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and b.booking_no in ($returnBooking) and b.buyer_job_no='" . $dataArray[0][csf('buyer_job_no')] . "'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2";
								$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
							} else {
									//echo "select sum(a.cons_quantity) as total_issue_qty from inv_transaction a, inv_issue_master b where b.id=a.mst_id and b.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.buyer_job_no<>'' and b.issue_purpose=2";
								$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.issue_purpose=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2";
								$total_return_qty = return_field_value("sum(a.cons_quantity) as total_return_qty", "inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
							}
						} else {
							if ($dataArray[0][csf('buyer_job_no')] != "") {
								if ($all_requ_no != "" || $all_requ_no != 0) {
									$req_cond = "or a.requisition_no in ($all_requ_no)";
									$reqRet_cond = "or b.booking_no in ($all_requ_no)";
								} else {
										$req_cond = "";//or a.requisition_no in (0)
										$reqRet_cond = "";//or b.booking_no in (0)
									}
									$total_issue_qty = 0;
									//echo "select sum(c.quantity) as total_issue_qty from inv_transaction a, inv_issue_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2";
									$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and ( b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2";
									$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");

									//$total_issue_qty=return_field_value("sum(quantity) as total_issue_qty","order_wise_pro_details","po_breakdown_id in ($returnPo) and status_active=1 and is_deleted=0 and trans_type=2 and entry_form=3","total_issue_qty");
									//$total_return_qty=return_field_value("sum(quantity) as total_return_qty","order_wise_pro_details","po_breakdown_id in ($returnPo) and status_active=1 and is_deleted=0 and trans_type=4 and entry_form=8","total_return_qty");
								}
							}
							?>
							<tbody>
								<tr>
									<td align="center">
										<?
										if ($dataArray[0][csf('issue_purpose')] == 8) echo $buyer_arr[$sql_returnJob[0][csf('buyer_id')]];
										else echo $buyer_arr[$job_array[$dataArray[0][csf('buyer_job_no')]]['buyer']];
										?>
									</td>
									<td align="center">
										<? echo $job_array[$dataArray[0][csf('buyer_job_no')]]['job']; ?>&nbsp;
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = 0;
										$cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')] < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}

				echo signature_table(49, $data[0], "1030px",'',0);
				?>
			</div>
		</div>



		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
					var value = valuess;//$("#barcodeValue").val();
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
					$("#barcode_img_id").html('11');
					value = {code: value, rect: false};
					$("#barcode_img_id").show().barcode(value, btype, settings);
				}
				generateBarcode('<? echo $data[1]; ?>');
			</script>
			<p style="page-break-after: always;"> </p>

			<?
		}
		exit();
	}


	if ($action == "yarn_issue_print12")
	{
		extract($_REQUEST);
		echo load_html_head_contents("Yarn Issue Challan Print7", "../../", 1, 1, '', '', '');
		$data = explode('*', $data);

		$print_with_vat = $data[7];
		//$supplier_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
		$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
		$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	    //$other_party_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$sql = "select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";
	    //echo $sql;die;

		$dataArray = sql_select($sql);

		if( $dataArray[0][csf('issue_basis')]==3 )
		{
			$planbooking = sql_select("select e.booking_no as pln_booking_no from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_info_entry_dtls d,ppl_planning_info_entry_mst e where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.id and d.mst_id=e.id and a.id='$data[5]' ");
		}

		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

		$copyNo = "";
		for ($x = 1; $x <= 3; $x++)
		{

			if($x==1)
			{
				$copyNo ="<span style='font-size:x-large;'>1<sup>st</sup> Copy</span>";
			} else if($x==2){
				$copyNo ="<span style='font-size:x-large;'>2<sup>nd</sup> Copy</span>";
			} else {
				$copyNo ="<span style='font-size:x-large;'>3<sup>rd</sup> Copy</span>";
			}
			?>
			<div style="width:1100px;">
				<table width="1100" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
					<tr>
						<td colspan="6" align="center" style="font-size:18px"></td>


						<td style="font-size:x-large; font-style:italic;" align="right">
							<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
						</td>
					</tr>
					<tr class="form_caption">

						<?
						$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
						?>
						<td align="left" width="50">
							<?
							foreach ($data_array as $img_row) {
								if ($data[5] != 1) {
									?>
									<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								} else {
									?>
									<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								}
							}
							?>
						</td>
						<td colspan="2" align="center">
							<strong style="font-size:18px"><? echo $company_library[$data[0]]; ?></strong><br>
							<?
							echo show_company($data[0], '', array('city'));
							?>
						</td>
						<td colspan="2" id="barcode_img_id">&nbsp;</td>
						<td colspan="3" style="color:black; font-weight:bold;"><? echo $copyNo;?></td>
					</tr>
					<tr>
						<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan/Gate
						Pass</u></strong></center></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="160"><strong>Issue No:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
						<td width="120"><strong>Booking:</strong></td>
						<td width="175px">
							<?
							if( $dataArray[0][csf('issue_basis')]==3 )
							{
								$bookingNo = $planbooking[0][csf('pln_booking_no')];
							} else {
								$bookingNo = $dataArray[0][csf('booking_no')];
							}
							echo $bookingNo;
							?>
						</td>
						<td width="125"><strong>Knitting Source:</strong></td>
						<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Challan/Program No:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
						<td><strong>Issue Purpose:</strong></td>
			<td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else
			echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
			?></td>
			<td><strong>Issue Date:</strong></td>
			<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
		</tr>
		<tr>
			<td><strong>Issue To:</strong></td>
			<?
			if ($dataArray[0][csf('knit_dye_source')] == 3) {
				$supp_add = $dataArray[0][csf('knit_dye_company')];
				$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
				foreach ($nameArray as $result) {
					$address = "";
								if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
							}
							unset($nameArray);
						}

						$loan_party_add = $dataArray[0][csf('loan_party')];
						$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
						foreach ($loanPartyArray as $result) {
							$addressParty = "";
							if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
						}
						unset($loanPartyArray);
						?>
						<td colspan="3">
							<?
							if ($dataArray[0][csf('issue_purpose')] == 3) {
								echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
							} else if ($dataArray[0][csf('issue_purpose')] == 5) {
								echo $supplier_arr[$dataArray[0][csf('loan_party')]] . ' : Address :- ' . $addressParty;
							} else {
								if ($dataArray[0][csf('knit_dye_source')] == 1)
									echo $company_library[$dataArray[0][csf('knit_dye_company')]];
								else
									echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]] . ' : Address :- ' . $address;
							}
							?>
						</td>
						<td colspan="2"><strong>Location : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong>
						</td>
					</tr>
					<tr>
						<td><strong>Demand No.:</strong></td>
						<td width="175px"><? //echo $dataArray[0][csf('booking_id')];
						?></td>
						<td><strong>Gate Pass No.:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
						<td><strong>Do No:</strong></td>
						<td width="175px"><? //echo $dataArray[0][csf('remarks')];
						?></td>
					</tr>
					<tr>
						<td valign="top"><strong>Remarks :</strong></td>
						<td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
					</tr>
					<?
					if ($print_with_vat == 1) {
						?>
						<tr>
							<td><strong>VAT Number :</strong></td>
							<td><p>
								<?
								$vat_no = return_field_value("vat_number", "lib_company", "id=" . $data[0], "vat_number");
								echo $vat_no;
								?></p></td>

							</tr>
							<?
						} else {
							?>
							<tr>
								<td valign="top">&nbsp;</strong></td>
								<td>&nbsp;</td>

							</tr>
							<?
						}
						?>
					</table>
					<br>
					<div style="width:100%;">
						<div style="clear:both;">
							<table style="margin-right:-40px;" cellspacing="0" width="1000" border="1" rules="all"
							class="rpt_table">
							<thead bgcolor="#dddddd" style="font-size:13px">
								<th width="20">SL</th>
								<th width="45">Req. No</th>
								<th width="50">Lot No</th>
								<th width="65">Y. Type</th>
								<th width="110">Item Details</th>
								<th width="65">Grey Color</th>
								<th width="65">Dye. Color</th>
								<th width="60">Supp.</th>
								<th width="60"><b>Issue Qty(kg)</b></th>
								<th width="60">Rtnbl Qty(kg)</th>
								<th width="80">Bag & Cone</th>
								<th width="80">Store</th>
								<th width="100">Buyer & Job</th>
								<th width="120">Order & Style</th>
								<th>Inte. Ref & File</th>
							</thead>
							<?
							$wopi_library = return_library_array("select id,pi_number from com_pi_master_details", "id", "pi_number");

							$cond = "";
							if ($data[5] != "") $cond .= " and c.id='$data[5]'";

							$po_array = array();
							$job_array = array();
							$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0]");
							foreach ($costing_sql as $row) {
								$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
								$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
								$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
								$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
								$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
								$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
								$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
								$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
							}
							unset($costing_sql);

							$job_no_array = array();
							$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id from fabric_sales_order_mst");
							foreach ($jobData as $row) {
								$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
								$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
								$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
								$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
							}
						//var_dump($po_array);
							$po_id_req_array = array();
							$is_sales_array = array();
							if ($db_type == 0) {
								$poID = " select c.requisition_no, a.is_sales, group_concat(distinct(a.po_id)) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
							} else {
								$poID = "select c.requisition_no, a.is_sales, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
							}
							$poID_sql_result = sql_select($poID);
							foreach ($poID_sql_result as $row) {
								$po_id_req_array[$row[csf('requisition_no')]] = $row[csf('poid')];
								$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
							}
							unset($poID_sql_result);

							$i = 1;
							if ($db_type == 0) {
								$sql_result = sql_select("select a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro
									from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id
									where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond
									group by a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no");
							} else {
								$sql_result = sql_select("select a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, LISTAGG(d.po_breakdown_id, ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro
									from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond
									group by a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no");
							}
							$all_req_no = '';
							$all_prod_id = '';
							$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
							$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
							foreach ($sql_result as $row) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								if ($row[csf('requisition_no')] != '') {
									if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
									if ($all_prod_id == '') $all_prod_id = $row[csf('po_id')]; else $all_prod_id .= ',' . $row[csf('po_id')];
								}
								$order_qnty_val = $row[csf('cons_quantity')];
								$order_qnty_val_sum += $order_qnty_val;

								$order_amount_val = $row[csf('order_amount')];
								$order_amount_val_sum += $order_amount_val;

								$no_of_bags_val = $row[csf('no_of_bags')];
								$no_of_bags_val_sum += $no_of_bags_val;

								$po_id = explode(",", $row[csf('po_id')]);
								$po_no = '';
								$style_ref = '';
								$job_no = '';
								$buyer = '';
							//$data=explode('*',$data);
								$productdtls = $row[csf('product_name_details')];
								$productArr = explode(' ', $productdtls);

								$proddtls='';
								$proddtls=$yarn_count_details[$row[csf('yarn_count_id')]];
								if($row[csf('yarn_count_id')]>0) $proddtls.=", ";
								$proddtls.=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%";

							/*if($row[csf('yarn_comp_type1st')]>0) $proddtls.=", ";
							$proddtls.=$yarn_type[$row[csf('yarn_type')]];*/

							/*if ($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='' && $productArr[3]!='' && $productArr[4]!=''&& $productArr[5]!='')
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].'%, '.$productArr[3].' '.$productArr[4]."%, ".$productArr[5].", ".$productArr[6];
							}
							else if ($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='' && $productArr[3]!='' && $productArr[4]!='')
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].' %, '.$productArr[3].', '.$productArr[4];
							}
							else if($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='' && $productArr[3]!='' )
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].' %, '.$productArr[3];
							}
							else if($productArr[0]!='' && $productArr[1]!='' && $productArr[2]!='')
							{
								$proddtls=$productArr[0].', '.$productArr[1].' '.$productArr[2].' %, ';
							}
							else if($productArr[0]!='' && $productArr[1]!='' )
							{
								$proddtls=$productArr[0].', '.$productArr[1];
							}
							else if($productArr[0]!='')
							{
								$proddtls=$productArr[0];
							}
							else
							{
								$proddtls='';
							}*/

							$internal_ref = '';
							$file_no = '';
							if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2) {
								foreach ($po_id as $val) {
									if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
									if ($job_no == '') $job_no = $po_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
									if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

									if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
									if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
								}
								$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
								$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
								$ref_cond = '';
								foreach ($job_ref as $ref) {
									//$ref_cond.=", ".$ref;
									if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
								}
								$file_con = '';
								foreach ($job_file as $file) {
									if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
								}



								$buyer_job = '';
								if ($row[csf('issue_basis')] == 1) {
									if ($dataArray[0][csf('issue_purpose')] == 8) {
										$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
									} else {
										$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
										if ($row[csf('buyer_job_no')] != "") {
											$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
										}
									}
								} else if ($row[csf('issue_basis')] == 2) {
									$buyer_job = '';
								}
							} else if ($row[csf('issue_basis')] == 3) {
								//$ex_data = array_unique(explode(",",$po_id_req_array[$row[csf('requisition_no')]]));
								//print_r($ex_data);

								if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
									$buyer_job = '';
									foreach (array_unique($po_id) as $val) {
										if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
										if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];

										if ($buyer_job == '') {
											if ($job_no_array[$val]['within_group'] == 1) {
												$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
											} else {
												$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
											}
										}
									}
								} else {
									foreach ($po_id as $val) {
										if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
										if ($job_no == '') $job_no = $po_array[$val]['job_no'];
										if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
										if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

										if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
										if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
									}

									$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
									$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

									$ref_cond = '';
									foreach ($job_ref as $ref) {
										//$ref_cond.=", ".$ref;
										if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
									}

									$file_con = '';
									foreach ($job_file as $file) {
										if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
									}
									if ($job_no != '') {
										$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
									} else {
										$buyer_job = $buyer_arr[$buyer_name];
									}
								}
							} else {
								foreach (array_unique($po_id) as $val) {
									if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
									if ($buyer_job == '') {
										if ($job_no_array[$val]['within_group'] == 1) {

											$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
										} else {
											$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
										}
									}
								}
							}

							$no_bag_cone = "";
							$wt_bag_cone = "";
							if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

								if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
										<td width="20"><? echo $i; ?></td>
										<td width="45">
											<div style="word-wrap:break-word; width:45px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
										</td>
										<td width="50">
											<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></div>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
												<?
										//echo $proddtls;
												echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_name_arr[$row[csf('color')]];
												?>
											</div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('color')]]; ?></div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('dyeing_color_id')]]; ?>
											&nbsp;</div>
										</td>
										<td width="60">
											<div style="word-wrap:break-word; width:60px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div>
										</td>
										<td align="right"
										width="60"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>
										<td align="right"
										width="60"><? echo number_format($row[csf('returnable_qnty')], 2, '.', ''); ?></td>
										<td width="80">
											<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
											&nbsp;</div>
										</td>
										<td width="80">
											<div style="word-wrap:break-word; width:80px"><? echo $store_arr[$row[csf('store_id')]]; ?></div>
										</td>
										<td width="100">
											<div style="word-wrap:break-word; width:100px"><? echo $buyer_job; ?></div>
										</td>
										<td width="120">
											<div style="word-wrap:break-word; width:120px"><? echo $po_no;
											if ($style_ref != "") echo ' & ' . $style_ref; else echo '&nbsp;'; ?></div>
										</td>
										<td>
											<div style="word-wrap:break-word; width:80px"><? echo ltrim($ref_cond, ", ");
											if ($file_cond != "") echo ' & ' . ltrim($file_cond, ", "); else echo '&nbsp;'; ?></div>
										</td>
									</tr>
									<?
									$uom_unit = "Kg";
									$uom_gm = "Grams";
									$tot_return_qty += $row[csf('returnable_qnty')];
									$tot_bag += $no_of_bags_val;
									$tot_cone += $row[csf('cone_per_bag')];
									$tot_w_bag += $row[csf('weight_per_bag')];
									$tot_w_cone += $row[csf('weight_per_cone')];
									$req_no = $row[csf('requisition_no')];
									$i++;
								}
								?>
								<tr bgcolor="#CCCCCC" style="font-size:13px">
									<td align="right" colspan="8"><b>Total</b></td>
									<td align="right">
										<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
									</td>
									<td align="right"><? echo number_format($tot_return_qty, 2, '.', ''); ?></td>
									<td><? echo 'N:' . number_format($tot_bag, 2);
									if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
									echo '<br>W:' . number_format($tot_w_bag, 2);
									if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>
									<td align="right" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="14" align="left"><b>In
										Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
									</tr>
								</table>
							</div>
							<br>
							<!--================================================================-->
							<?
							if ($data[6] == 1)
							{
								if ($dataArray[0][csf('issue_basis')] == 3)
								{
									?>
									<div style="">
										<table width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
											<thead>
												<tr>
													<th colspan="7" align="center">Requisition Details</th>
												</tr>
												<tr>
													<th width="40">SL</th>
													<th width="100">Requisition No</th>
													<th width="110">Lot No</th>
													<th width="220">Yarn Description</th>
													<th width="110">Brand</th>
													<th width="90">Requisition Qty</th>
													<th>Remarks</th>
												</tr>
											</thead>
											<?
						//echo $all_req_no;
											$i = 1;
											$tot_reqsn_qnty = 0;
											$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
											$product_details_array = array();
											$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id='$data[0]' and status_active=1 and is_deleted=0";
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
											}
											unset($result);
											$sql = "select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where requisition_no in ($all_req_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
											$nameArray = sql_select($sql);
											foreach ($nameArray as $selectResult) {
												?>
												<tr>
													<td width="40" align="center"><? echo $i; ?></td>
													<td width="100" align="center">&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
													<td width="110" align="center">
														&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
														<td width="220">
															&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
															<td width="110" align="center">
																&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
																<td width="90" align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?>
																&nbsp;</td>
								<td>&nbsp;<? //echo $selectResult[csf('requisition_no')];
								?></td>
							</tr>
							<?
							$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
							$i++;
						}
						?>
						<tfoot>
							<th colspan="5" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<?
					if ($data[5] != 1) {
						$z = 1;
						$k = 1;
						$colorArray = sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.requisition_no in ($all_req_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");

						$booking_al = "";
						foreach ($colorArray as $book) {
							if ($booking_al == "") $booking_al = $book[csf('booking_no')]; else $booking_al .= ',' . $book[csf('booking_no')];
						}
						//unset($colorArray);
						//echo $booking_no;
						$booking_no = "'" . implode(',', array_unique(explode(',', $booking_al))) . "'";
						$booking_count = count(array_unique(explode(',', $booking_no)));
						//$booking_job_arr=array();
						if ($dataArray[0][csf('issue_purpose')] == 2) {
							$booking_job = sql_select("select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no in ($booking_no) group by b.job_no");
						} else {
							$booking_job = sql_select("select job_no from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no in ($booking_no) group by  job_no");
						}
						//
						$booking_job_no = $booking_job[0][csf('job_no')];
						unset($booking_job);

						if ($db_type == 0) $poNoCond = "group_concat(id)";
						else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
						/*echo '='.$sql_Job.'=';
					if($sql_Job!="")
					{*/
						$sql_returnJob = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");

						$job_po = $sql_returnJob[0][csf('po_break_down_id')];
						unset($sql_returnJob);
						?>

						<table width="710" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;"
						class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="250">Fabrication</th>
							<th width="120">Color</th>
							<th width="120">GGSM OR S/L</th>
							<th>FGSM</th>
						</thead>
						<tbody>
							<tr>
								<td width="40" align="center"><? echo $z; ?></td>
								<td width="250"
								align="center"><? echo $body_part[$colorArray[0][csf('body_part_id')]] . ', ' . $colorArray[0][csf('fabric_desc')]; ?></td>
								<td width="120"
								align="center"><? echo $color_range[$colorArray[0][csf('color_range')]]; ?></td>
								<td width="120" align="center"><? echo $colorArray[0][csf('stitch_length')]; ?></td>
								<td align="center"><? echo $colorArray[0][csf('gsm_weight')]; ?></td>
							</tr>
						</tbody>
					</table>
					<table style="margin-top:20px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0"
					class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="50">Prog. No</th>
						<th width="110">Finish Dia</th>
						<th width="170">Machine Dia & Gauge</th>
						<th width="110">Program Qty</th>
						<th>Remarks</th>
					</thead>
					<tbody>
						<?
						$k=1;
						foreach ($colorArray as $program_row) {
							?>
							<tr>
								<td width="40" align="center"><? echo $k; ?></td>
								<td width="50" align="center"><? echo $program_row[csf('knit_id')]; ?></td>
								<td width="110" align="center"><? echo $program_row[csf('dia')]; ?></td>
								<td width="170" align="center"><? echo $program_row[csf('machine_dia')] . "X" . $program_row[csf('machine_gg')]; ?></td>
								<td width="110" align="right"><? echo number_format($program_row[csf('program_qnty')], 2); ?></td>
								<td><? echo $program_row[csf('remarks')]; ?></td>
							</tr>
							<?
							$k++;
							$tot_prog_qty += $program_row[csf('program_qnty')];
						}
						?>
						<tr>
							<td colspan="4" align="right"><strong>Total : </strong></td>
							<td align="right"><? echo number_format($tot_prog_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
				<?
				$returnJob = '';
				$returnPo = '';
				if ($db_type == 0) {
					$booking_cond = "group_concat(a.booking_no)";
					$wo_cond = "group_concat(a.ydw_no)";
					$reqs_no = "group_concat(b.requisition_no)";
				} else if ($db_type == 2) {
					$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
					$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
					$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
				}

				if ($dataArray[0][csf('issue_purpose')] == 8) {
					$sql_returnJob = sql_select("select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_no) group by a.id");
				} else if ($dataArray[0][csf('issue_purpose')] == 2) {
					$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$booking_job_no' group by b.job_no");

					if ($db_type == 0) $poNoCond = "group_concat(id)";
					else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
					$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");
				} else {

							$sql_returnJob = sql_select("select a.job_no, a.booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$booking_job_no' and a.booking_no in($booking_no) group by a.job_no,a.booking_no"); //and a.booking_no='".$dataArray[0][csf('booking_no')]."'

						}
						$returnJob = $sql_returnJob[0][csf('job_no')];
						//$returnPo=$sql_po[0][csf('po_break_down_id')];
						$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
						$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";
						$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
						unset($sql_returnJob);

						/*$req_no_retInJob=sql_select("select $reqs_no as req_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");
						$reqNo_inJob=$req_no_retInJob[0][csf('req_no')];*/
						?>
						<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0"
						class="rpt_table">
						<thead>
							<tr>
								<th colspan="6" align="center">Comments (Booking No:<p> <? echo $returnBooking; ?>) [Booking Job
								Deatils]</p></th>
							</tr>
							<tr>
								<th>Buyer</th>
								<th>Job</th>
								<th>Req. Qty</th>
								<th>Cuml. Issue Qty</th>
								<th>Balance Qty</th>
								<th>Remarks</th>
							</tr>
						</thead>
						<?

						$all_requ_no = "";
						if ($dataArray[0][csf('issue_purpose')] != 8) {
							$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 ");

							//$all_requ_no="'".$all_booking_sql[0][csf('requisition_no')]."'";
							$all_requ_no = "'" . implode("','", array_unique(explode(",", $all_booking_sql[0][csf('requisition_no')]))) . "'";
						}
						if ($dataArray[0][csf('issue_purpose')] == 8) {
							$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3", "total_issue_qty");    //
							$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=1", "total_issue_qty");
						} else if ($dataArray[0][csf('issue_purpose')] == 2) {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}

							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and b.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
						} else {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}
							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");


							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");
						}
						$cumulative_qty = 0;
						if ($booking_count == 1) {
							?>
							<tbody>
								<tr>
									<td align="center">
										<? echo $buyer_arr[$job_array[$booking_job_no]['buyer']]; ?>
									</td>
									<td align="center">
										<? echo $job_array[$booking_job_no]['job']; ?>
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($returnReqQty < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}
			}
			else if ($dataArray[0][csf('issue_basis')] == 1) {
				?>
				<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0"
				class="rpt_table">
				<thead>
					<tr>
						<th colspan="6" align="center">Comments <? if ($dataArray[0][csf('issue_purpose')] != 8) {
							if ($dataArray[0][csf('buyer_job_no')] != '') echo "(Booking Job Details)";
						} ?> </th>
					</tr>
					<tr>
						<th>Buyer</th>
						<th>Job</th>
						<th>Req. Qty</th>
						<th>Cuml. Qty</th>
						<th>Balance Qty</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<?
							//$booking_job_arr=array();
				$returnJob = '';
				$returnPo = '';
				if ($db_type == 0) {
					$booking_cond = "group_concat( distinct a.booking_no)";
					$wo_cond = "group_concat(a.ydw_no)";
					$reqs_no = "group_concat(b.requisition_no)";
				} else if ($db_type == 2) {
					$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
					$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
					$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
				}

				if ($dataArray[0][csf('issue_purpose')] == 8) {
					$sql_returnJob = sql_select("select a.id, a.buyer_id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id, a.buyer_id");
				} else if ($dataArray[0][csf('issue_purpose')] == 2) {
					if ($dataArray[0][csf('buyer_job_no')] != '') {
						$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by b.job_no");

						if ($db_type == 0) $poNoCond = "group_concat(id)";
						else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
						$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='" . $dataArray[0][csf('buyer_job_no')] . "' group by job_no_mst");
					} else {
						$sql_returnJob = sql_select("select a.id, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.item_category_id=24 and a.entry_form in(42,114) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id");
					}
				} else {
								//echo "select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='".$dataArray[0][csf('buyer_job_no')]."' group by a.job_no";
								$sql_returnJob = sql_select("select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by a.job_no");//and a.booking_no='".$dataArray[0][csf('booking_no')]."'
							}
							$returnJob = $sql_returnJob[0][csf('job_no')];
							$returnPo = $sql_po[0][csf('po_break_down_id')];
							$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
							$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";

							$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
							unset($sql_returnJob);

							//$booking_all="'".implode("','",explode(",",$bill_item_id))."'";
							//echo $returnJob.'---------'.$returnPo.'dfgdfgd';

							/*if( $dataArray[0][csf('issue_purpose')]==8 )
						{
							$sql = "select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$dataArray[0][csf('booking_no')]."' group by a.id";
						}
						else if( $dataArray[0][csf('issue_purpose')]==2)
						{
							if($dataArray[0][csf('buyer_job_no')]!="")
							{
								$sql = "select sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='".$dataArray[0][csf('buyer_job_no')]."'"; // and a.ydw_no='".$dataArray[0][csf('booking_no')]."'
							}
						}
						else
						{
							if($dataArray[0][csf('buyer_job_no')]!="")
							{
								$sql = "select a.job_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($returnBooking) and a.job_no='".$dataArray[0][csf('buyer_job_no')]."' group by a.job_no";
							}
						}
						$result = sql_select($sql);*/
						$all_requ_no = "";
						if ($dataArray[0][csf('issue_purpose')] != 8) {
								//echo "select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no";
							$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no");

							$all_requ_no = $all_booking_sql[0][csf('requisition_no')];
							unset($all_booking_sql);
						}

						if ($dataArray[0][csf('issue_purpose')] == 8) {
								$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3", "total_issue_qty");    //
								$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1", "total_issue_qty");
							} else if ($dataArray[0][csf('issue_purpose')] == 2) {
								/*if($all_requ_no!="" || $all_requ_no!=0)
							{
								//$req_cond="or a.requisition_no in ($all_requ_no)";
								//$reqRet_cond="or b.booking_no in ($all_requ_no)";
							}
							else
							{
								//$req_cond="or a.requisition_no in (0)";
								$reqRet_cond="or b.booking_no in (0)";
							}*/
							if ($dataArray[0][csf('buyer_job_no')] != "") {
								$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and b.booking_no in ($returnBooking) and b.buyer_job_no='" . $dataArray[0][csf('buyer_job_no')] . "'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2";
								$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
							} else {
									//echo "select sum(a.cons_quantity) as total_issue_qty from inv_transaction a, inv_issue_master b where b.id=a.mst_id and b.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.buyer_job_no<>'' and b.issue_purpose=2";
								$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.issue_purpose=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2";
								$total_return_qty = return_field_value("sum(a.cons_quantity) as total_return_qty", "inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
							}
						} else {
							if ($dataArray[0][csf('buyer_job_no')] != "") {
								if ($all_requ_no != "" || $all_requ_no != 0) {
									$req_cond = "or a.requisition_no in ($all_requ_no)";
									$reqRet_cond = "or b.booking_no in ($all_requ_no)";
								} else {
										$req_cond = "";//or a.requisition_no in (0)
										$reqRet_cond = "";//or b.booking_no in (0)
									}
									$total_issue_qty = 0;
									//echo "select sum(c.quantity) as total_issue_qty from inv_transaction a, inv_issue_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2";
									$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and ( b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2";
									$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");

									//$total_issue_qty=return_field_value("sum(quantity) as total_issue_qty","order_wise_pro_details","po_breakdown_id in ($returnPo) and status_active=1 and is_deleted=0 and trans_type=2 and entry_form=3","total_issue_qty");
									//$total_return_qty=return_field_value("sum(quantity) as total_return_qty","order_wise_pro_details","po_breakdown_id in ($returnPo) and status_active=1 and is_deleted=0 and trans_type=4 and entry_form=8","total_return_qty");
								}
							}
							?>
							<tbody>
								<tr>
									<td align="center">
										<?
										if ($dataArray[0][csf('issue_purpose')] == 8) echo $buyer_arr[$sql_returnJob[0][csf('buyer_id')]];
										else echo $buyer_arr[$job_array[$dataArray[0][csf('buyer_job_no')]]['buyer']];
										?>
									</td>
									<td align="center">
										<? echo $job_array[$dataArray[0][csf('buyer_job_no')]]['job']; ?>&nbsp;
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = 0;
										$cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')] < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}
				?>
				<table width="1030" cellspacing="0" border="0" style="margin-left:10px; margin-top:60px">
					<tr>
						<td><strong>Transport No:</strong></td>
						<td><strong>Driver Name:</strong></td>
						<td><strong>D/L No:</strong></td>
						<td><strong>Mobile No:</strong></td>
					</tr>
				</table>
				<br>
				<br>
				<?
				echo signature_table(49, $data[0], "1030px",'',0);
				?>
			</div>
		</div>



		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
					var value = valuess;//$("#barcodeValue").val();
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
					$("#barcode_img_id").html('11');
					value = {code: value, rect: false};
					$("#barcode_img_id").show().barcode(value, btype, settings);
				}
				generateBarcode('<? echo $data[1]; ?>');
			</script>
			<p style="page-break-after: always;"> </p>

			<?
		}
		exit();
	}



	if ($action == "yarn_issue_print10")
	{
		extract($_REQUEST);
		echo load_html_head_contents("Yarn Issue Challan Print", "../../", 1, 1, '', '', '');
		$data = explode('*', $data);

		$print_with_vat = $data[7];
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	//$other_party_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
		$sql = " select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";
	//echo $sql;die;


		$dataArray = sql_select($sql);

		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

		?>
		<div style="width:1000px;">
			<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td colspan="3" style="font-size:xx-large; font-style:italic;" align="right">
						<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
					</td>
				</tr>
				<tr class="form_caption">

					<?
					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					?>
					<td align="left" width="100">
						<?
						foreach ($data_array as $img_row) {
							if ($data[5] != 1) {
								?>
								<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
								align="middle"/>
								<?
							} else {
								?>
								<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
								align="middle"/>
								<?
							}
						}
						?>
					</td>
					<td width="100" align="left">
						<strong style="font-size:18px"><? echo $company_library[$data[0]]; ?></strong><br>
						<?
						echo show_company($data[0], '', array('city'));
						?>
					</td>

					<td width="100" align="left">
						<div style="float:left; color:#000;">
							<table>
								<tr>
									<td>Issue No: </td>
									<td><? echo $dataArray[0][csf('issue_number')]; ?></td>
								</tr>
								<tr>
									<td>Issue Date: </td>
									<td><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
								</tr>
								<tr>
									<?
									$issueNumber="'".$dataArray[0][csf('issue_number')]."'";
									$sql_issue_approve=sql_select("select  c.approved_by,c.approved_date from  inv_issue_master a,  approval_history c where  a.id=c.mst_id and  a.issue_number=$issueNumber and a.is_approved in(1,3)and a.is_deleted=0 and a.status_active=1");
									?>
									<td>Approved Date & Time: </td>
									<td><? echo $sql_issue_approve[0][csf('approved_date')];   //date_format($sql_issue_approve[0][csf('insert_date')],"Y/m/d H:i"); ?></td>
								</tr>
								<tr>
									<td>Approved By: </td>
									<td><? echo $user_arr[$sql_issue_approve[0][csf('approved_by')]]; ?></td>
								</tr>
							</table>
						</div>
					</td>

				</tr>
			</table>




			<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan</u></strong></center></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><strong>Issue To:</strong></td>
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 3) {
						$supp_add = $dataArray[0][csf('knit_dye_company')];
						$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
						foreach ($nameArray as $result) {
							$address = "";
                            if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                        }
                        unset($nameArray);
                    }

                    $loan_party_add = $dataArray[0][csf('loan_party')];
                    $loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
                    foreach ($loanPartyArray as $result) {
                    	$addressParty = "";
                        if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                    }
                    unset($loanPartyArray);
                    ?>
                    <td width="125">
                    	<?
                    	if ($dataArray[0][csf('issue_purpose')] == 3) {
                    		echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
                    	} else if ($dataArray[0][csf('issue_purpose')] == 5) {
                    		echo $supplier_arr[$dataArray[0][csf('loan_party')]];
                    	} else {
                    		if ($dataArray[0][csf('knit_dye_source')] == 1)
                    			echo $company_library[$dataArray[0][csf('knit_dye_company')]];
                    		else
                    			echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
                    	}
                    	?>
                    </td>
                    <td><strong>Gate Pass No.:</strong></td>
                    <td width="175px"><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
                    <td  id="barcode_img_id">&nbsp;</td>
                </tr>
                <tr>
                	<td width="120"><strong>Address:</strong></td>
                	<td width="175">
                		<?
                		if ($dataArray[0][csf('issue_purpose')] == 3) {
                    		//echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
                		} else if ($dataArray[0][csf('issue_purpose')] == 5) {
                			echo ' : Address :- ' . $addressParty;
                		} else {
                			if ($dataArray[0][csf('knit_dye_source')] == 1)
                			{
                    			//echo $company_library[$dataArray[0][csf('knit_dye_company')]];
                			}
                			else{
                				echo  ' : Address :- ' . $address; }
                			}
                			?>
                		</td>
                		<td width="125"><strong>Knitting Source:</strong></td>
                		<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
                		<td style="text-align:center;"><? echo $dataArray[0][csf('issue_number')];?></td>
                	</tr>
                	<tr>

                		<td width="120"><strong>Booking:</strong></td>
                		<td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>



                		<td colspan="2"><strong>Location : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong>
                		</td>
                	</tr>
                	<tr>
                		<td><strong>Issue Purpose:</strong></td>
			        <td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else
			        echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
			        ?></td>

			        <td><strong>Do No:</strong></td>
                    <td width="175px"><? //echo $dataArray[0][csf('remarks')];
                    ?></td>
                </tr>
                <tr>
                	<td valign="top"><strong>Remarks :</strong></td>
                	<td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
                </tr>
                <?
                if ($print_with_vat == 1) {
                	?>
                	<tr>
                		<td><strong>VAT Number :</strong></td>
                		<td><p>
                			<?
                			$vat_no = return_field_value("vat_number", "lib_company", "id=" . $data[0], "vat_number");
                			echo $vat_no;
                			?></p></td>

                		</tr>
                		<?
                	} else {
                		?>
                		<tr>
                			<td valign="top">&nbsp;</strong></td>
                			<td>&nbsp;</td>

                		</tr>
                		<?
                	}
                	?>
                </table>
                <br>
                <div style="width:100%;">
                	<div style="clear:both;">
                		<table style="margin-right:-40px;" cellspacing="0" width="1000" border="1" rules="all"
                		class="rpt_table">
                		<thead bgcolor="#dddddd" style="font-size:13px">
                			<th width="20">SL</th>
                			<th width="45">Req. No</th>
                			<th width="50">Lot No</th>
                			<th width="65">Y. Type</th>
                			<th width="110">Item Details</th>
                			<th width="65">Grey Color</th>
                			<th width="65">Dye. Color</th>
                			<th width="60">Supp.</th>
                			<th width="60"><b>Issue Qty(kg)</b></th>
                			<th width="60">Rtnbl Qty(kg)</th>
                			<th width="80">Bag & Cone</th>
                			<th width="80">Store</th>
                			<th width="100">Buyer & Job</th>
                			<th width="120">Order & Style</th>
                			<th>Inte. Ref & File</th>
                		</thead>
                		<?
                		$i = 1;
                		$cond = "";
                		if ($data[5] != "") $cond .= " and c.id='$data[5]'";
                		if ($db_type == 0) {
                			$sql_result = sql_select("select a.id prod_id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro
                				from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id
                				where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond
                				group by a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no");
                		} else {
                			$sql_result = sql_select("select a.id prod_id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, LISTAGG(d.po_breakdown_id, ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro
                				from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond
                				group by a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no");
                		}

                		$all_req_no = '';
                		$all_prod_id = '';
                		foreach ($sql_result as $row) {
                			if($row[csf("po_id")]!="")
                			{
                				$po_arr[] = $row[csf("po_id")];
                			}
                			$requisition_arr[] = $row[csf("requisition_no")];
                		}

                		$po_array = array();
                		$job_array = array();
                		$po_cond = (!empty($po_arr))?" and b.id in (".implode(",",$po_arr).")":"";
                		if(!empty($po_arr))
                		{
                			$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0] $po_cond");
                			foreach ($costing_sql as $row) {
                				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
                				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
                				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
                				$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
                				$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
                			}
                		}
                		unset($costing_sql);

                		$job_no_array = array();
                		$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id from fabric_sales_order_mst");
                		foreach ($jobData as $row) {
                			$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                			$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                			$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
                			$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
                		}

                		$po_id_req_array = array();
                		$is_sales_array = array();
                		$requisition_arr_cond = (!empty($requisition_arr))?" and c.requisition_no in (".implode(",",$requisition_arr).")":"";
                		if ($db_type == 0) {
                			$poID = " select c.requisition_no, a.is_sales, group_concat(distinct(a.po_id)) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id $requisition_arr_cond group by c.requisition_no, a.is_sales ";
                		} else {
                			$poID = "select c.requisition_no, a.is_sales, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id $requisition_arr_cond group by c.requisition_no, a.is_sales ";
                		}
                		$poID_sql_result = sql_select($poID);
                		foreach ($poID_sql_result as $row) {
                			$po_id_req_array[$row[csf('requisition_no')]] = $row[csf('poid')];
                			$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
                		}
                		unset($poID_sql_result);

                		$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
                		$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
                		foreach ($sql_result as $row) {
                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			if ($row[csf('requisition_no')] != '') {
                				if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
                				if ($all_prod_id == '') $all_prod_id = $row[csf('prod_id')]; else $all_prod_id .= ',' . $row[csf('prod_id')];
                			}
                			$order_qnty_val = $row[csf('cons_quantity')];
                			$order_qnty_val_sum += $order_qnty_val;

                			$order_amount_val = $row[csf('order_amount')];
                			$order_amount_val_sum += $order_amount_val;

                			$no_of_bags_val = $row[csf('no_of_bags')];
                			$no_of_bags_val_sum += $no_of_bags_val;

                			$po_id = explode(",", $row[csf('po_id')]);
                			$po_no = '';
                			$style_ref = '';
                			$job_no = '';
                			$buyer = '';
                			$productdtls = $row[csf('product_name_details')];
                			$productArr = explode(' ', $productdtls);

                			$proddtls='';
                			$proddtls=$yarn_count_details[$row[csf('yarn_count_id')]];
                			if($row[csf('yarn_count_id')]>0) $proddtls.=", ";
                			$proddtls.=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%";

                			$internal_ref = '';
                			$file_no = '';
                			if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2) {
                				foreach ($po_id as $val) {
                					if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
                					if ($job_no == '') $job_no = $po_array[$val]['job_no'];
                					if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
                					if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

                					if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
                					if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
                				}
                				$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
                				$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
                				$ref_cond = '';
                				foreach ($job_ref as $ref) {
								//$ref_cond.=", ".$ref;
                					if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
                				}
                				$file_con = '';
                				foreach ($job_file as $file) {
                					if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
                				}



                				$buyer_job = '';
                				if ($row[csf('issue_basis')] == 1) {
                					if ($dataArray[0][csf('issue_purpose')] == 8) {
                						$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
                					} else {
                						$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
                						if ($row[csf('buyer_job_no')] != "") {
                							$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
                						}
                					}
                				} else if ($row[csf('issue_basis')] == 2) {
                					$buyer_job = '';
                				}
                			} else if ($row[csf('issue_basis')] == 3) {

                				if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
                					$buyer_job = '';
                					foreach (array_unique($po_id) as $val) {
                						if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
                						if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];

                						if ($buyer_job == '') {
                							if ($job_no_array[$val]['within_group'] == 1) {
                								$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
                							} else {
                								$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
                							}
                						}
                					}
                				} else {
                					foreach ($po_id as $val) {
                						if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
                						if ($job_no == '') $job_no = $po_array[$val]['job_no'];
                						if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
                						if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

                						if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
                						if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
                					}

                					$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
                					$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

                					$ref_cond = '';
                					foreach ($job_ref as $ref) {
                						if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
                					}

                					$file_con = '';
                					foreach ($job_file as $file) {
                						if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
                					}
                					if ($job_no != '') {
                						$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
                					} else {
                						$buyer_job = $buyer_arr[$buyer_name];
                					}
                				}
                			} else {
                				foreach (array_unique($po_id) as $val) {
                					if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
                					if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
                					if ($buyer_job == '') {
                						if ($job_no_array[$val]['within_group'] == 1) {
                							$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
                						} else {
                							$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
                						}
                					}
                				}
                			}

                			$no_bag_cone = "";
                			$wt_bag_cone = "";
                			if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

                				if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];
                					?>
                					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
                						<td width="20"><? echo $i; ?></td>
                						<td width="45">
                							<div style="word-wrap:break-word; width:45px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
                						</td>
                						<td width="50">
                							<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
                						</td>
                						<td width="65">
                							<div style="word-wrap:break-word; width:65px"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></div>
                						</td>
                						<td width="110">
                							<div style="word-wrap:break-word; width:110px">
                								<?
                								echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_name_arr[$row[csf('color')]];
                								?>
                							</div>
                						</td>
                						<td width="65">
                							<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('color')]]; ?></div>
                						</td>
                						<td width="65">
                							<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('dyeing_color_id')]]; ?>
                							&nbsp;</div>
                						</td>
                						<td width="60">
                							<div style="word-wrap:break-word; width:60px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div>
                						</td>
                						<td align="right"
                						width="60"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>
                						<td align="right"
                						width="60"><? echo number_format($row[csf('returnable_qnty')], 2, '.', ''); ?></td>
                						<td width="80">
                							<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
                							&nbsp;</div>
                						</td>
                						<td width="80">
                							<div style="word-wrap:break-word; width:80px"><? echo $store_arr[$row[csf('store_id')]]; ?></div>
                						</td>
                						<td width="100">
                							<div style="word-wrap:break-word; width:100px"><? echo $buyer_job; ?></div>
                						</td>
                						<td width="120">
                							<div style="word-wrap:break-word; width:120px"><? echo $po_no;
                							if ($style_ref != "") echo ' & ' . $style_ref; else echo '&nbsp;'; ?></div>
                						</td>
                						<td>
                							<div style="word-wrap:break-word; width:80px"><? echo ltrim($ref_cond, ", ");
                							if ($file_cond != "") echo ' & ' . ltrim($file_cond, ", "); else echo '&nbsp;'; ?></div>
                						</td>
                					</tr>
                					<?
                					$uom_unit = "Kg";
                					$uom_gm = "Grams";
                					$tot_return_qty += $row[csf('returnable_qnty')];
                					$tot_bag += $no_of_bags_val;
                					$tot_cone += $row[csf('cone_per_bag')];
                					$tot_w_bag += $row[csf('weight_per_bag')];
                					$tot_w_cone += $row[csf('weight_per_cone')];
                					$req_no = $row[csf('requisition_no')];
                					$i++;
                				}
                				?>
                				<tr bgcolor="#CCCCCC" style="font-size:13px">
                					<td align="right" colspan="8"><b>Total</b></td>
                					<td align="right">
                						<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
                					</td>
                					<td align="right"><? echo number_format($tot_return_qty, 2, '.', ''); ?></td>
                					<td><? echo 'N:' . number_format($tot_bag, 2);
                					if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
                					echo '<br>W:' . number_format($tot_w_bag, 2);
                					if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>
                					<td align="right" colspan="4">&nbsp;</td>
                				</tr>
                				<tr>
                					<td colspan="14" align="left"><b>In
                						Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
                					</tr>
                				</table>
                			</div>
                			<br>
                			<!--================================================================-->
                			<?
                			if ($data[6] == 1)
                			{
                				if ($dataArray[0][csf('issue_basis')] == 3)
                				{
                					?>
                					<div style="">
                						<table width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
                							<thead>
                								<tr>
                									<th colspan="7" align="center">Requisition Details</th>
                								</tr>
                								<tr>
                									<th width="40">SL</th>
                									<th width="100">Requisition No</th>
                									<th width="110">Lot No</th>
                									<th width="220">Yarn Description</th>
                									<th width="110">Brand</th>
                									<th width="90">Requisition Qty</th>
                									<th>Remarks</th>
                								</tr>
                							</thead>
                							<?
                							$i = 1;
                							$tot_reqsn_qnty = 0;
                							$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
                							$product_details_array = array();
                							$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id='$data[0]' and status_active=1 and is_deleted=0 and id in(".trim($all_prod_id,", ").")";
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
                							}
                							unset($result);

                							$colorArray = sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.requisition_no in ($all_req_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");

                							$sql = "select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where requisition_no in ($all_req_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
                							$nameArray = sql_select($sql);
                							foreach ($nameArray as $selectResult) {
                								?>
                								<tr>
                									<td width="40" align="center"><? echo $i; ?></td>
                									<td width="100" align="center"><? echo $selectResult[csf('requisition_no')]; ?></td>
                									<td width="110" align="center">
                										<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
                										<td width="220">
                											<? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
                											<td width="110" align="center">
                												<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
                												<td width="90" align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?>
                												</td>
                												<td></td>
                											</tr>
                											<?
                											$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
                											$i++;
                										}
                										?>
                										<tfoot>
                											<th colspan="5" align="right"><b>Total</b></th>
                											<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;</th>
                											<th>&nbsp;</th>
                										</tfoot>
                									</table>
                									<?
                									if ($data[5] != 1) {
                										$z = 1;
                										$k = 1;
                										$booking_al = "";
                										foreach ($colorArray as $book) {
                											if ($booking_al == "") $booking_al = $book[csf('booking_no')]; else $booking_al .= ',' . $book[csf('booking_no')];
                										}

                										$booking_no = "'" . implode(',', array_unique(explode(',', $booking_al))) . "'";
                										$booking_count = count(array_unique(explode(',', $booking_no)));

                										if ($dataArray[0][csf('issue_purpose')] == 2) {
                											$booking_job = sql_select("select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no in ($booking_no) group by b.job_no");
                										} else {
                											$booking_job = sql_select("select job_no from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no in ($booking_no) group by  job_no");
                										}

                										$booking_job_no = $booking_job[0][csf('job_no')];
                										unset($booking_job);

                										if ($db_type == 0) $poNoCond = "group_concat(id)";
                										else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";

                										$sql_returnJob = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");

                										$job_po = $sql_returnJob[0][csf('po_break_down_id')];
                										unset($sql_returnJob);
                										?>

                										<table width="710" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;"
                										class="rpt_table">
                										<thead>
                											<th width="40">SL</th>
                											<th width="250">Fabrication</th>
                											<th width="120">Color</th>
                											<th width="120">GGSM OR S/L</th>
                											<th>FGSM</th>
                										</thead>
                										<tbody>
                											<tr>
                												<td width="40" align="center"><? echo $z; ?></td>
                												<td width="250"
                												align="center"><? echo $body_part[$colorArray[0][csf('body_part_id')]] . ', ' . $colorArray[0][csf('fabric_desc')]; ?></td>
                												<td width="120"
                												align="center"><? echo $color_range[$colorArray[0][csf('color_range')]]; ?></td>
                												<td width="120" align="center"><? echo $colorArray[0][csf('stitch_length')]; ?></td>
                												<td align="center"><? echo $colorArray[0][csf('gsm_weight')]; ?></td>
                											</tr>
                										</tbody>
                									</table>
                									<table style="margin-top:20px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0"
                									class="rpt_table">
                									<thead>
                										<th width="40">SL</th>
                										<th width="50">Prog. No</th>
                										<th width="110">Finish Dia</th>
                										<th width="170">Machine Dia & Gauge</th>
                										<th width="110">Program Qty</th>
                										<th>Remarks</th>
                									</thead>
                									<tbody>
                										<tr>
                											<td width="40" align="center"><? echo $k; ?></td>
                											<td width="50" align="center">&nbsp;<? echo $colorArray[0][csf('knit_id')]; ?></td>
                											<td width="110" align="center"><? echo $colorArray[0][csf('dia')]; ?></td>
                											<td width="170"
                											align="center"><? echo $colorArray[0][csf('machine_dia')] . "X" . $colorArray[0][csf('machine_gg')]; ?></td>
                											<td width="110"
                											align="right"><? echo number_format($colorArray[0][csf('program_qnty')], 2); ?>
                										&nbsp;</td>
                										<td><? echo $colorArray[0][csf('remarks')]; ?></td>
                									</tr>
                									<?
                									$tot_prog_qty += $colorArray[0][csf('program_qnty')];
                									?>
                									<tr>
                										<td colspan="4" align="right"><strong>Total : </strong></td>
                										<td align="right"><? echo number_format($tot_prog_qty, 2); ?></td>
                										<td>&nbsp;</td>
                									</tr>
                								</tbody>
                							</table>
                							<?
                							$returnJob = '';
                							$returnPo = '';
                							if ($db_type == 0) {
                								$booking_cond = "group_concat(a.booking_no)";
                								$wo_cond = "group_concat(a.ydw_no)";
                								$reqs_no = "group_concat(b.requisition_no)";
                							} else if ($db_type == 2) {
                								$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
                								$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
                								$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
                							}

                							if ($dataArray[0][csf('issue_purpose')] == 8) {
                								$sql_returnJob = sql_select("select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_no) group by a.id");
                							} else if ($dataArray[0][csf('issue_purpose')] == 2) {
                								$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$booking_job_no' group by b.job_no");

                								if ($db_type == 0) $poNoCond = "group_concat(id)";
                								else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
                								$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");
                							} else {
                								$sql_returnJob = sql_select("select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$booking_job_no' group by a.job_no");
                							}
                							$returnJob = $sql_returnJob[0][csf('job_no')];
                							$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
                							$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";
                							$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
                							unset($sql_returnJob);
                							?>
                							<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0"
                							class="rpt_table">
                							<thead>
                								<tr>
                									<th colspan="6" align="center">Comments (Booking No:<p> <? echo $returnBooking; ?>) [Booking Job
                									Deatils]</p></th>
                								</tr>
                								<tr>
                									<th>Buyer</th>
                									<th>Job</th>
                									<th>Req. Qty</th>
                									<th>Cuml. Issue Qty</th>
                									<th>Balance Qty</th>
                									<th>Remarks</th>
                								</tr>
                							</thead>
                							<?

                							$all_requ_no = "";
                							if ($dataArray[0][csf('issue_purpose')] != 8) {
                								$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 ");
                								$all_requ_no = "'" . implode("','", array_unique(explode(",", $all_booking_sql[0][csf('requisition_no')]))) . "'";
                							}
                							if ($dataArray[0][csf('issue_purpose')] == 8) {
                								$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3", "total_issue_qty");
                								$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=1", "total_issue_qty");
                							} else if ($dataArray[0][csf('issue_purpose')] == 2) {
                								if ($all_requ_no != "" || $all_requ_no != 0) {
                									$req_cond = "or a.requisition_no in ($all_requ_no)";
                									$reqRet_cond = "or b.booking_no in ($all_requ_no)";
                								} else {
                									$req_cond = "or a.requisition_no in (0)";
                									$reqRet_cond = "or b.booking_no in (0)";
                								}

                								$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

                								$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and b.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
                							} else {
                								if ($all_requ_no != "" || $all_requ_no != 0) {
                									$req_cond = "or a.requisition_no in ($all_requ_no)";
                									$reqRet_cond = "or b.booking_no in ($all_requ_no)";
                								} else {
                									$req_cond = "or a.requisition_no in (0)";
                									$reqRet_cond = "or b.booking_no in (0)";
                								}
                								$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");


                								$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");
                							}
                							$cumulative_qty = 0;
                							if ($booking_count == 1) {
                								?>
                								<tbody>
                									<tr>
                										<td align="center">
                											<? echo $buyer_arr[$job_array[$booking_job_no]['buyer']]; ?>
                										</td>
                										<td align="center">
                											<? echo $job_array[$booking_job_no]['job']; ?>
                										</td>
                										<td align="center">
                											<? echo number_format($returnReqQty, 3); ?>
                										</td>
                										<td align="center">
                											<? $cumulative_qty = $total_issue_qty - $total_return_qty;
                											echo number_format($cumulative_qty, 3); ?>
                										</td>
                										<td align="center">
                											<? $balance_qty = $returnReqQty - $cumulative_qty;
                											echo number_format($balance_qty, 3); ?>
                										</td>
                										<td align="center">
                											<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($returnReqQty < $cumulative_qty) echo "Over"; else echo ""; ?>
                										</td>
                									</tr>
                								</tbody>
                							</table>
                							<?
                						}
                					}
                				}
                				else if ($dataArray[0][csf('issue_basis')] == 1) {
                					?>
                					<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0"
                					class="rpt_table">
                					<thead>
                						<tr>
                							<th colspan="6" align="center">Comments <? if ($dataArray[0][csf('issue_purpose')] != 8) {
                								if ($dataArray[0][csf('buyer_job_no')] != '') echo "(Booking Job Details)";
                							} ?> </th>
                						</tr>
                						<tr>
                							<th>Buyer</th>
                							<th>Job</th>
                							<th>Req. Qty</th>
                							<th>Cuml. Qty</th>
                							<th>Balance Qty</th>
                							<th>Remarks</th>
                						</tr>
                					</thead>
                					<?
                					$returnJob = '';
                					$returnPo = '';
                					if ($db_type == 0) {
                						$booking_cond = "group_concat( distinct a.booking_no)";
                						$wo_cond = "group_concat(a.ydw_no)";
                						$reqs_no = "group_concat(b.requisition_no)";
                					} else if ($db_type == 2) {
                						$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
                						$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
                						$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
                					}

                					if ($dataArray[0][csf('issue_purpose')] == 8) {
                						$sql_returnJob = sql_select("select a.id, a.buyer_id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id, a.buyer_id");
                					} else if ($dataArray[0][csf('issue_purpose')] == 2) {
                						if ($dataArray[0][csf('buyer_job_no')] != '') {
                							$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by b.job_no");

                							if ($db_type == 0) $poNoCond = "group_concat(id)";
                							else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
                							$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='" . $dataArray[0][csf('buyer_job_no')] . "' group by job_no_mst");
                						} else {
                							$sql_returnJob = sql_select("select a.id, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.item_category_id=24 and a.entry_form in(42,114) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id");
                						}
                					} else {
                						$sql_returnJob = sql_select("select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by a.job_no");
                					}
                					$returnJob = $sql_returnJob[0][csf('job_no')];
                					$returnPo = $sql_po[0][csf('po_break_down_id')];
                					$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
                					$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";

                					$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
                					unset($sql_returnJob);


                					$all_requ_no = "";
                					if ($dataArray[0][csf('issue_purpose')] != 8) {
                						$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no");

                						$all_requ_no = $all_booking_sql[0][csf('requisition_no')];
                						unset($all_booking_sql);
                					}

                					if ($dataArray[0][csf('issue_purpose')] == 8) {
                						$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3", "total_issue_qty");
                						$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1", "total_issue_qty");
                					} else if ($dataArray[0][csf('issue_purpose')] == 2) {

                						if ($dataArray[0][csf('buyer_job_no')] != "") {
                							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and b.booking_no in ($returnBooking) and b.buyer_job_no='" . $dataArray[0][csf('buyer_job_no')] . "'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");
                							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
                						} else {
                							$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

                							$total_return_qty = return_field_value("sum(a.cons_quantity) as total_return_qty", "inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9  and c.issue_purpose=2", "total_return_qty");
                						}
                					} else {
                						if ($dataArray[0][csf('buyer_job_no')] != "") {
                							if ($all_requ_no != "" || $all_requ_no != 0) {
                								$req_cond = "or a.requisition_no in ($all_requ_no)";
                								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
                							} else {
                								$req_cond = "";
                								$reqRet_cond = "";
                							}
                							$total_issue_qty = 0;
                							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and ( b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");
                							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");
                						}
                					}
                					?>
                					<tbody>
                						<tr>
                							<td align="center">
                								<?
                								if ($dataArray[0][csf('issue_purpose')] == 8) echo $buyer_arr[$sql_returnJob[0][csf('buyer_id')]];
                								else echo $buyer_arr[$job_array[$dataArray[0][csf('buyer_job_no')]]['buyer']];
                								?>
                							</td>
                							<td align="center">
                								<? echo $job_array[$dataArray[0][csf('buyer_job_no')]]['job']; ?>&nbsp;
                							</td>
                							<td align="center">
                								<? echo number_format($returnReqQty, 3); ?>
                							</td>
                							<td align="center">
                								<? $cumulative_qty = 0;
                								$cumulative_qty = $total_issue_qty - $total_return_qty;
                								echo number_format($cumulative_qty, 3); ?>
                							</td>
                							<td align="center">
                								<? $balance_qty = $returnReqQty - $cumulative_qty;
                								echo number_format($balance_qty, 3); ?>
                							</td>
                							<td align="center">
                								<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')] < $cumulative_qty) echo "Over"; else echo ""; ?>
                							</td>
                						</tr>
                					</tbody>
                				</table>
                				<?
                			}
                		}
                		?>
                		<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
                			<?
                			if($data[4] == 1){}else{echo "Draft";}
                			?>
                		</div>

                		<?
                		echo signature_table(49, $data[0], "1030px",'',0);
                		?>
                	</div>
                </div>

                <script type="text/javascript" src="../../js/jquery.js"></script>
                <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
                <script>
                	function generateBarcode(valuess) {
                var value = valuess;//$("#barcodeValue").val();
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
                $("#barcode_img_id").html('11');
                value = {code: value, rect: false};
                $("#barcode_img_id").show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $data[1]; ?>');
        </script>
        <?
        exit();
    }





    if ($action == "requisition_print") {
    	extract($_REQUEST);
    	$data = explode('_', $data);

    	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
    	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    	$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

    	$order_id = str_replace("'", "", $hide_order_id);
    	$company_name = $cbo_company_id;

    	$po_array = array();
    	$costing_sql = sql_select("select a.job_no, a.style_ref_no,b.file_no,b.grouping a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0]");
    	foreach ($costing_sql as $row) {
    		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
    		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
    		$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
    		$po_array[$row[csf('id')]]['buyer_name'] = $buyer_arr[$row[csf('buyer_name')]];

    		$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
    		$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
    	}

    	$product_details_arr = array();
    	$pro_sql = sql_select("select id, yarn_count_id, lot, color, brand from product_details_master where company_id=$data[0] and item_category_id=1");
    	foreach ($pro_sql as $row) {
    		$product_details_arr[$row[csf('id')]]['count'] = $yarn_count_details[$row[csf('yarn_count_id')]];
    		$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
    		$product_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
    		$product_details_arr[$row[csf('id')]]['brand'] = $brand_details[$row[csf('brand')]];
    	}
			//var_dump($product_details_arr);
    	$reqsn_details_arr = array();
    	$sql = sql_select("select prod_id, requisition_no, sum(yarn_demand_qnty) as demand_qnty from ppl_yarn_demand_reqsn_dtls group by prod_id, requisition_no");
    	foreach ($sql as $row) {
    		$reqsn_details_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]] = $row[csf('demand_qnty')];
    	}
    	?>
    	<div>
    		<label><strong>Bar Code:</strong></label><label id="barcode_img_id"></label>
    		<br> <br>
    		<table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table">
    			<thead bgcolor="#dddddd" align="center">
    				<tr>
    					<th colspan="13">Requisition Details</th>
    				</tr>
    				<tr>
    					<th width="30">SL</th>
    					<th width="60">Buyer</th>
    					<th width="130">Order No</th>
    					<th width="100">Internal Ref & File No</th>
    					<th width="70">Reqsn. No.</th>
    					<th width="70">Yarn Count</th>
    					<th width="100">Yarn Brand</th>
    					<th width="100">Lot No</th>
    					<th width="100">Color</th>
    					<th width="80">Reqsn. Qty</th>
    					<th width="80">Demand Qty</th>
    					<th width="80">Delivery Qty</th>
    					<th width="80">Balance Qty</th>
    				</tr>
    			</thead>
    		</table>
    		<div style="width:1157px;" id="scroll_body">
    			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table"
    			id="table_body">
    			<tbody>
    				<?
    				$i = 1;
    				$tot_reqsn_qnty = 0;
    				$tot_demand_qnty = 0;
    				$tot_delivery_qnty = 0;
    				$tot_balance = 0;
    				if ($db_type == 0) {
    					$sql = "select c.id, c.requisition_no as reqs_no, c.prod_id, c.no_of_cone, c.yarn_qnty as reqsn_qnty, group_concat(distinct(a.po_id)) as po_id from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id and c.requisition_no in ($data[1]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.requisition_no, c.prod_id, c.no_of_cone, c.yarn_qnty order by c.requisition_no";
    				} else {
    					$sql = "select c.id, c.requisition_no as reqs_no, c.prod_id, c.no_of_cone, c.yarn_qnty as reqsn_qnty, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as po_id from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id and c.requisition_no in ($data[1]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.requisition_no, c.prod_id, c.no_of_cone, c.yarn_qnty order by c.requisition_no";
    				}
						//echo $sql;
    				$nameArray = sql_select($sql);
    				foreach ($nameArray as $row) {
    					if ($i % 2 == 0)
    						$bgcolor = "#E9F3FF";
    					else
    						$bgcolor = "#FFFFFF";

    					$yarn_iss_data = sql_select("select sum(b.cons_quantity) as delivery_qnty, sum(b.no_of_bags) as no_of_bags from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=3 and a.item_category=1 and a.entry_form=3 and b.receive_basis=3 and b.transaction_type=2 and b.item_category=1 and b.requisition_no=" . $row[csf('reqs_no')] . " and b.prod_id=" . $row[csf('prod_id')] . " and b.status_active=1 and b.is_deleted=0");

    					$delivery_qnty = $yarn_iss_data[0][csf('delivery_qnty')];
    					$no_of_bags = $yarn_iss_data[0][csf('no_of_bags')];

    					$demand_qnty = $reqsn_details_arr[$row[csf('prod_id')]][$row[csf('reqs_no')]];
    					$balance_qnty = $row[csf('reqsn_qnty')] - $delivery_qnty;

    					$po_id = array_unique(explode(",", $row[csf('po_id')]));
    					$po_no = '';
    					$buyer = '';
    					$internal_ref = '';
    					$file_no = '';

    					foreach ($po_id as $val) {
    						if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
    						if ($buyer == '') $buyer = $po_array[$val]['buyer_name'];

    						if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= ", " . $po_array[$val]['grouping'];
    						if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= ", " . $po_array[$val]['file_no'];

    					}
    					$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
    					$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
    					$ref_cond = '';
    					foreach ($job_ref as $ref) {
    						if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
								//$ref_cond.=", ".$ref;
    					}
    					$file_con = '';
    					foreach ($job_file as $file) {
    						if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
    					}

    					?>
    					<tr bgcolor="<? echo $bgcolor; ?>"
    						onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')"
    						id="tr_<? echo $i; ?>">
    						<td width="30"><? echo $i; ?></td>
    						<td width="60"><p><? echo $buyer; ?></p></td>
    						<td width="130"><p><? echo $po_no; ?></p></td>
    						<td width="100">
    							<p><? echo ltrim($ref_cond, ", ") . '<br>' . ltrim($file_no_cond, ", "); ?></p></td>

    							<td width="70" align="center"><? echo $row[csf('reqs_no')]; ?></td>
    							<td width="70"><p><? echo $product_details_arr[$row[csf('prod_id')]]['count']; ?></p>
    							</td>
    							<td width="100"><? echo $product_details_arr[$row[csf('prod_id')]]['brand']; ?></td>
    							<td width="100"><p><? echo $product_details_arr[$row[csf('prod_id')]]['lot']; ?></p>
    							</td>
    							<td width="100"><p><? echo $product_details_arr[$row[csf('prod_id')]]['color']; ?></p>
    							</td>
    							<td align="right" width="80"><? echo number_format($row[csf('reqsn_qnty')], 2); ?></td>
    							<td align="right" width="80"><? echo number_format($demand_qnty, 2); ?></td>
    							<td align="right" width="80"><? echo number_format($delivery_qnty, 2); ?></td>
    							<td align="right" width="80"><? echo number_format($balance_qnty, 2); ?></td>

    						</tr>
    						<?
    						$tot_reqsn_qnty += $row[csf('reqsn_qnty')];
    						$tot_demand_qnty += $demand_qnty;
    						$tot_delivery_qnty += $delivery_qnty;
    						$tot_balance += $balance_qnty;

    						$i++;
    					}
    					?>
    				</tbody>
    			</table>
    			<table class="rpt_table" width="1140" id="report_table_footer" cellpadding="0" cellspacing="0"
    			border="1" rules="all">
    			<tfoot>
    				<th width="30">&nbsp;</th>
    				<th width="60">&nbsp;</th>
    				<th width="130">&nbsp;</th>
    				<th width="100">&nbsp;</th>
    				<th width="70">&nbsp;</th>
    				<th width="70">&nbsp;</th>
    				<th width="100">&nbsp;</th>
    				<th width="100">&nbsp;</th>
    				<th width="100" align="right">Total</th>
    				<th width="80" align="right"
    				id="value_tot_reqsn_qnty"><? echo number_format($tot_reqsn_qnty, 2, '.', ''); ?></th>
    				<th width="80" align="right"
    				id="value_tot_demand_qnty"><? echo number_format($tot_demand_qnty, 2, '.', ''); ?></th>
    				<th width="80" align="right" id="value_tot_delivery_qnty">
    					<b><? echo number_format($tot_delivery_qnty, 2, '.', ''); ?></b></th>
    					<th width="80" align="right"
    					id="value_tot_balance"><? echo number_format($tot_balance, 2, '.', ''); ?></th>
    				</tfoot>
    			</table>
    		</div>
    	</div>

    	<script type="text/javascript" src="../../js/jquery.js"></script>
    	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    	<script>
    		function generateBarcode(valuess) {
                    var value = valuess;//$("#barcodeValue").val();
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
                generateBarcode('<? echo $data[2]; ?>');
            </script>
            <?
            exit();
        }

        if ($action == "btb_selection_popup") {
        	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
        	extract($_REQUEST);
        	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
        	$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
        	if ($update_id_mst > 0) $mst_cond = " and mst_id <> $update_id_mst";
        	$issue_qnty_arr = return_library_array("select btb_lc_id, sum(cons_quantity) as issue_qnty from inv_transaction where item_category=1 and transaction_type=2 and status_active=1 and is_deleted=0 and btb_lc_id>0 $mst_cond group by btb_lc_id", 'btb_lc_id', 'issue_qnty');
        	$suplier_cond = "";
        	if ($supplier > 0) $suplier_cond = " and d.supplier_id=$supplier and b.supplier_id=$supplier";
        	$sql = "select d.id, d.lc_number, d.importer_id, d.lc_date, d.last_shipment_date, d.supplier_id, sum(a.quantity) as lc_qnty
        	from product_details_master p, inv_transaction b, com_pi_item_details a, com_btb_lc_pi c, com_btb_lc_master_details d
        	where p.id=b.prod_id and b.pi_wo_batch_no=a.pi_id and a.pi_id=c.pi_id and c.com_btb_lc_master_details_id=d.id and b.item_category = 1 and p.item_category_id=1 and b.receive_basis = 1 and b.transaction_type=1 and p.lot='$lot_no' and p.company_id=$comany_name and b.company_id=$comany_name $suplier_cond
        	group by d.id, d.lc_number, d.importer_id, d.lc_date, d.last_shipment_date, d.supplier_id";
			//echo $sql;
        	?>
        	<script>
        		function js_set_value(data) {
        			var splitSTR = data.split("**");
        			var id = splitSTR[0];
        			var lc_number = splitSTR[1];

        			$("#hidden_btb_id").val(id);
        			$("#hidden_btb_lc_no").val(lc_number);
        			parent.emailwindow.hide();
        		}
        	</script>
        	<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        		<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center"
        		rules="all">
        		<thead>
        			<tr>
        				<th width="30">SL</th>
        				<th width="120">LC NO.</th>
        				<th width="140">Importer</th>
        				<th width="140">Supplier Name</th>
        				<th width="80">LC Date</th>
        				<th width="80">Last Shipment Date</th>
        				<th width="100">LC Qty</th>
        				<th width="100">Cumulative Issue Qty</th>
        				<th>Balance Qty</th>
        			</tr>
        		</thead>
        		<tbody>
        			<? $nameArray = sql_select($sql);
        			$i = 1;
        			foreach ($nameArray as $row) {
        				if ($i % 2 == 0)
        					$bgcolor = "#E9F3FF";
        				else
        					$bgcolor = "#FFFFFF";
        				$issue_balance = $row[csf('lc_qnty')] - $issue_qnty_arr[$row[csf('id')]];
        				?>
        				<tr bgcolor="<? echo $bgcolor; ?>"
        					onClick="js_set_value('<? echo $row[csf('id')] . '**' . $row[csf('lc_number')]; ?>')"
        					id="tr_<? echo $i; ?>" style=" cursor: pointer;">
        					<td><? echo $i; ?></td>
        					<td><? echo $row[csf('lc_number')] ?></td>
        					<td><? echo $company_arr[$row[csf('importer_id')]]; ?></td>
        					<td><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
        					<td><? echo ($row[csf('lc_date')] != "") ? date("d-m-Y", strtotime($row[csf('lc_date')])) : ""; ?></td>
        					<td><? echo date("d-m-Y", strtotime($row[csf('last_shipment_date')])); ?></td>
        					<td align="right"><? echo number_format($row[csf('lc_qnty')], 2); ?></td>
        					<td align="right"><? echo number_format($issue_qnty_arr[$row[csf('id')]], 2); ?></td>
        					<td align="right"><? echo number_format($issue_balance, 2); ?></td>
        				</tr>
        				<?
        				$i++;
        			} ?>
        		</tbody>
        		<tfoot>
        			<input type="hidden" id="hidden_btb_id" value="">
        			<input type="hidden" id="hidden_btb_lc_no" value="">
        		</tfoot>

        	</table>
        </form>
        <?
    }


    /*#################### created by foysal ####################################*/


    if ($action == "yarn_issue_print2")
    {
    	extract($_REQUEST);
    	echo load_html_head_contents("Yarn Issue Challan Print2", "../../", 1, 1, '', '', '');
    	$data = explode('*', $data);

    	$print_with_vat = $data[7];

		/*$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');*/

		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
		$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');


		//$other_party_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$sql = " select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";
		//echo $sql;die;
		$dataArray = sql_select($sql);

		if( $dataArray[0][csf('issue_basis')]==3 )
		{
			$planbooking = sql_select("select e.booking_no as pln_booking_no from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_info_entry_dtls d,ppl_planning_info_entry_mst e where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.id and d.mst_id=e.id and a.id='$data[5]' ");
		}

		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

		?>
		<style type="text/css">
		table tbody tr td {
			font-size: 14px;
		}
	</style>
	<div style="width:1000px;">
		<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
			<tr>
				<td colspan="6" align="center" style="font-size:18px">
					<strong><? echo $company_library[$data[0]]; ?></strong></td>
					<td style="font-size:xx-large; font-style:italic;" align="right">
						<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
					</td>
				</tr>
				<tr class="form_caption">

					<?
					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					?>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row) {
							if ($data[5] != 1) {
								?>
								<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
								align="middle"/>
								<?
							} else {
								?>
								<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
								align="middle"/>
								<?
							}
						}
						?>
					</td>
					<td colspan="4" align="center">
						<?
						echo show_company($data[0], '', '');
						/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}*/
					?>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan/Gate Pass</u></strong></center>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="160"><strong>Issue No:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="120"><strong>Booking:</strong></td>
				<td width="175px">
					<?
					if( $dataArray[0][csf('issue_basis')]==3 )
					{
						$bookingNo = $planbooking[0][csf('pln_booking_no')];
					} else {
						$bookingNo = $dataArray[0][csf('booking_no')];
					}
					echo $bookingNo;
					?>
				</td>
				<td width="125"><strong>Knitting Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Program No:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>

				<td><strong>Issue Purpose:</strong></td>
                    <td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else
                    echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
                    ?></td>
                    <td><strong>Issue Date:</strong></td>
                    <td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
                </tr>
                <tr>
                	<td><strong>Issue To:</strong></td>
                	<?
                	if ($dataArray[0][csf('knit_dye_source')] == 3) {
                		$supp_add = $dataArray[0][csf('knit_dye_company')];
                		$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                		foreach ($nameArray as $result) {
                			$address = "";
							if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
						}
						unset($nameArray);
					}

					$loan_party_add = $dataArray[0][csf('loan_party')];
					$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
					foreach ($loanPartyArray as $result) {
						$addressParty = "";
						if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					unset($loanPartyArray);
					?>
					<td colspan="3">
						<?
						if ($dataArray[0][csf('issue_purpose')] == 3) {
							echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
						} else if ($dataArray[0][csf('issue_purpose')] == 5) {
							echo $supplier_arr[$dataArray[0][csf('loan_party')]] . ' : Address :- ' . $addressParty;
						} else {
							if ($dataArray[0][csf('knit_dye_source')] == 1)
								echo $company_library[$dataArray[0][csf('knit_dye_company')]];
							else
								echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]] . ' : Address :- ' . $address;
						}
						?>
					</td>
					<td><strong>Location:</strong></td>
					<td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>


				</tr>
				<tr>
					<td><strong>Demand No.:</strong></td>
                    <td width="175px"><? //echo $dataArray[0][csf('booking_id')];
                    ?></td>

                    <td><strong>Do No:</strong></td>
                    <td width="175px"><? //echo $dataArray[0][csf('remarks')];
                    ?></td>
                </tr>
                <tr>
                	<td valign="top"><strong>Remarks :</strong></td>
                	<td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
                </tr>
                <?
                if ($print_with_vat == 1) {
                	?>
                	<tr>
                        <!-- <td><strong>VAT Number :</strong></td>
						<td><p>
							<?
						$vat_no = return_field_value("vat_number", "lib_company", "id=" . $data[0], "vat_number");
						echo $vat_no;
						?></p></td> -->
						<td><strong>Bar Code:</strong></td>
						<td colspan="3" id="barcode_img_id"></td>
					</tr>
					<?
				} else {
					?>
					<tr>
						<td valign="top">&nbsp;</strong></td>
						<td>&nbsp;</td>
						<td><strong>Bar Code:</strong></td>
						<td colspan="3" id="barcode_img_id"></td>
					</tr>
					<?
				}
				?>
			</table>
			<br>
			<div style="width:100%;">
				<div style="clear:both;">
					<table style="margin-right:-40px; " cellspacing="0" width="1000" border="1" rules="all"
					class="rpt_table">
					<thead bgcolor="#dddddd" style="font-size:13px">
						<th width="20">SL</th>
						<th width="45">Req. No</th>
						<th width="50">Lot No</th>
						<th width="65">Y. Type</th>
						<th width="110">Item Details</th>

						<th width="65">Dye. Color</th>
						<th width="60">Supp.</th>
						<th width="60"><b>Issue Qty(kg)</b></th>
						<th width="60">Rtnbl Qty(kg)</th>
						<th width="80">Bag & Cone</th>
						<th width="80">Store</th>
						<th width="100">Buyer & Job</th>
						<th width="120">Order & Style</th>
						<th>Inte. Ref & File</th>
					</thead>
					<?
					$wopi_library = return_library_array("select id,pi_number from com_pi_master_details", "id", "pi_number");

					$cond = "";
					if ($data[5] != "") $cond .= " and c.id='$data[5]'";

					$po_array = array();
					$job_array = array();
					$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0]");
					foreach ($costing_sql as $row) {
						$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
						$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
						$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
						$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
						$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
						$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
						$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
						$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
					}
					unset($costing_sql);

					$job_no_array = array();
					$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id from fabric_sales_order_mst");
					foreach ($jobData as $row) {
						$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
						$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
						$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
						$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
					}
						//var_dump($po_array);
					$po_id_req_array = array();
					$is_sales_array = array();
					if ($db_type == 0) {
						$poID = " select c.requisition_no, a.is_sales, group_concat(distinct(a.po_id)) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
					} else {
						$poID = "select c.requisition_no, a.is_sales, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
					}
					$poID_sql_result = sql_select($poID);
					foreach ($poID_sql_result as $row) {
						$po_id_req_array[$row[csf('requisition_no')]] = $row[csf('poid')];
						$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
					}
					unset($poID_sql_result);

					$i = 1;
					if ($db_type == 0) {
						$sql_result = sql_select("select a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
					} else {
						$sql_result = sql_select("select a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, LISTAGG(d.po_breakdown_id, ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
					}
					$all_req_no = '';
					$all_prod_id = '';
					$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
					$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
					foreach ($sql_result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						if ($row[csf('requisition_no')] != '') {
							if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
							if ($all_prod_id == '') $all_prod_id = $row[csf('po_id')]; else $all_prod_id .= ',' . $row[csf('po_id')];
						}
						$order_qnty_val = $row[csf('cons_quantity')];
						$order_qnty_val_sum += $order_qnty_val;

						$order_amount_val = $row[csf('order_amount')];
						$order_amount_val_sum += $order_amount_val;

						$no_of_bags_val = $row[csf('no_of_bags')];
						$no_of_bags_val_sum += $no_of_bags_val;

						$po_id = explode(",", $row[csf('po_id')]);
						$po_no = '';
						$style_ref = '';
						$job_no = '';
						$buyer = '';
							//$data=explode('*',$data);
						$productdtls = $row[csf('product_name_details')];
						$productArr = explode(' ', $productdtls);

						$proddtls = '';
						if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '' && $productArr[5] != '') {
							$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . '%, ' . $productArr[3] . ' ' . $productArr[4] . "%, " . $productArr[5] . ", " . $productArr[6];
						} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '') {
							$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3] . ', ' . $productArr[4];
						} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '') {
							$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3];
						} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '') {
							$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ';
						} else if ($productArr[0] != '' && $productArr[1] != '') {
							$proddtls = $productArr[0] . ', ' . $productArr[1];
						} else if ($productArr[0] != '') {
							$proddtls = $productArr[0];
						} else {
							$proddtls = '';
						}

						$internal_ref = '';
						$file_no = '';
						if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2) {
							foreach ($po_id as $val) {
								if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
								if ($job_no == '') $job_no = $po_array[$val]['job_no'];
								if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
								if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

								if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
								if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
							}
							$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
							$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
							$ref_cond = '';
							foreach ($job_ref as $ref) {
									//$ref_cond.=", ".$ref;
								if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
							}
							$file_con = '';
							foreach ($job_file as $file) {
								if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
							}

							$buyer_job = '';
							if ($row[csf('issue_basis')] == 1) {
								if ($dataArray[0][csf('issue_purpose')] == 8) {
									$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
								} else {
									$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
									if ($row[csf('buyer_job_no')] != "") {
										$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
									}
								}
							} else if ($row[csf('issue_basis')] == 2) {
								$buyer_job = '';
							}
						} else if ($row[csf('issue_basis')] == 3) {
								//$ex_data = array_unique(explode(",",$po_id_req_array[$row[csf('requisition_no')]]));
								//print_r($ex_data);

							if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
								$buyer_job = '';
								foreach (array_unique($po_id) as $val) {
									if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];

									if ($buyer_job == '') {
										if ($job_no_array[$val]['within_group'] == 1) {
											$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
										} else {
											$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
										}
									}
								}
							} else {
								foreach ($po_id as $val) {
									if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
									if ($job_no == '') $job_no = $po_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
									if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

									if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
									if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
								}

								$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
								$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

								$ref_cond = '';
								foreach ($job_ref as $ref) {
										//$ref_cond.=", ".$ref;
									if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
								}

								$file_con = '';
								foreach ($job_file as $file) {
									if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
								}
								if ($job_no != '') {
									$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
								} else {
									$buyer_job = $buyer_arr[$buyer_name];
								}
							}
						} else {
							foreach (array_unique($po_id) as $val) {
								if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
								if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
								if ($buyer_job == '') {
									if ($job_no_array[$val]['within_group'] == 1) {
										$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
									} else {
										$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
									}
								}
							}
						}

						$no_bag_cone = "";
						$wt_bag_cone = "";
						if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

							if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];
								?>
								<tbody>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td width="20"><? echo $i; ?></td>
										<td width="45">
											<div style="word-wrap:break-word; width:45px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
										</td>
										<td width="50">
											<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></div>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
												<?
                                        //echo $proddtls;
												echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_name_arr[$row[csf('color')]];
												?>
											</div>
										</td>


										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('dyeing_color_id')]]; ?>
											&nbsp;</div>
										</td>

										<td width="60">
											<div style="word-wrap:break-word; width:60px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div>
										</td>

										<td align="right"
										width="60"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>
										<td align="right"
										width="60"><? echo number_format($row[csf('returnable_qnty')], 2, '.', ''); ?></td>
										<td width="80">
											<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
											&nbsp;</div>
										</td>
										<td width="80">
											<div style="word-wrap:break-word; width:80px"><? echo $store_arr[$row[csf('store_id')]]; ?></div>
										</td>
										<td width="100">
											<div style="word-wrap:break-word; width:100px"><? echo $buyer_job; ?></div>
										</td>
										<td width="120">
											<div style="word-wrap:break-word; width:120px"><? echo $po_no;
											if ($style_ref != "") echo ' & ' . $style_ref; else echo '&nbsp;'; ?></div>
										</td>
										<td>
											<div style="word-wrap:break-word; width:80px"><? echo ltrim($ref_cond, ", ");
											if ($file_cond != "") echo ' & ' . ltrim($file_cond, ", "); else echo '&nbsp;'; ?></div>
										</td>
									</tr>
									<?
									$uom_unit = "Kg";
									$uom_gm = "Grams";
									$tot_return_qty += $row[csf('returnable_qnty')];
									$tot_bag += $no_of_bags_val;
									$tot_cone += $row[csf('cone_per_bag')];
									$tot_w_bag += $row[csf('weight_per_bag')];
									$tot_w_cone += $row[csf('weight_per_cone')];
									$req_no = $row[csf('requisition_no')];
									$i++;
								}
								?>
								<tr bgcolor="#CCCCCC" style="font-size:13px">
									<td align="right" colspan="7"><b>Total</b></td>
									<td align="right">
										<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
									</td>
									<td align="right"><? echo number_format($tot_return_qty, 2, '.', ''); ?></td>
									<td><? echo 'N:' . number_format($tot_bag, 2);
									if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
									echo '<br>W:' . number_format($tot_w_bag, 2);
									if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>
									<td align="right" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="14" align="left"><b>In
										Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
									</tr>
								</tbody>
							</table>
						</div>
						<br>
						<br>&nbsp;
						<!--================================================================-->
						<?
						if ($data[6] == 1)
						{
							if ($dataArray[0][csf('issue_basis')] == 3){
								?>
								<div style="">
									<table width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
										<thead>
											<tr>
												<th colspan="7" align="center">Requisition Details</th>
											</tr>
											<tr>
												<th width="40">SL</th>
												<th width="100">Requisition No</th>
												<th width="110">Lot No</th>
												<th width="220">Yarn Description</th>
												<th width="110">Brand</th>
												<th width="90">Requisition Qty</th>
												<th>Remarks</th>
											</tr>
										</thead>
										<?
						//echo $all_req_no;
										$i = 1;
										$tot_reqsn_qnty = 0;
										$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
										$product_details_array = array();
										$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id='$data[0]' and status_active=1 and is_deleted=0";
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
										}
										unset($result);
										$sql = "select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where requisition_no in ($all_req_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
										$nameArray = sql_select($sql);
										foreach ($nameArray as $selectResult) {
											?>
											<tr>
												<td width="40" align="center"><? echo $i; ?></td>
												<td width="100" align="center">
													&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
													<td width="110" align="center">
														&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
														<td width="220">
															&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
															<td width="110" align="center">
																&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
																<td width="90"
																align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?>
															&nbsp;</td>
                                <td>&nbsp;<? //echo $selectResult[csf('requisition_no')];
                                ?></td>
                            </tr>
                            <?
                            $tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
                            $i++;
                        }
                        ?>
                        <tfoot>
                        	<th colspan="5" align="right"><b>Total</b></th>
                        	<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;</th>
                        	<th>&nbsp;</th>
                        </tfoot>
                    </table>
                    <?
                    if ($data[5] != 1) {
                    	$z = 1;
                    	$k = 1;
                    	$colorArray = sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.requisition_no in ($all_req_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");

                    	$booking_al = "";
                    	foreach ($colorArray as $book) {
                    		if ($booking_al == "") $booking_al = $book[csf('booking_no')]; else $booking_al .= ',' . $book[csf('booking_no')];
                    	}
						//unset($colorArray);
						//echo $booking_no;
                    	$booking_no = "'" . implode(',', array_unique(explode(',', $booking_al))) . "'";
                    	$booking_count = count(array_unique(explode(',', $booking_no)));
						//$booking_job_arr=array();
                    	if ($dataArray[0][csf('issue_purpose')] == 2) {
                    		$booking_job = sql_select("select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no in ($booking_no) group by b.job_no");
                    	} else {
                    		$booking_job = sql_select("select job_no from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no in ($booking_no) group by  job_no");
                    	}
						//
                    	$booking_job_no = $booking_job[0][csf('job_no')];
                    	unset($booking_job);

                    	if ($db_type == 0) $poNoCond = "group_concat(id)";
                    	else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
						/*echo '='.$sql_Job.'=';
				if($sql_Job!="")
				{*/
					$sql_returnJob = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");

					$job_po = $sql_returnJob[0][csf('po_break_down_id')];
					unset($sql_returnJob);
					?>

					<table width="710" cellpadding="0" cellspacing="0" border="1" rules="all"
					style="margin-top:20px;" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="250">Fabrication</th>
						<th width="120">Color</th>
						<th width="120">GGSM OR S/L</th>
						<th>FGSM</th>
					</thead>
					<tbody>
						<tr>
							<td width="40" align="center"><? echo $z; ?></td>
							<td width="250"
							align="center"><? echo $body_part[$colorArray[0][csf('body_part_id')]] . ', ' . $colorArray[0][csf('fabric_desc')]; ?></td>
							<td width="120"
							align="center"><? echo $color_range[$colorArray[0][csf('color_range')]]; ?></td>
							<td width="120" align="center"><? echo $colorArray[0][csf('stitch_length')]; ?></td>
							<td align="center"><? echo $colorArray[0][csf('gsm_weight')]; ?></td>
						</tr>
					</tbody>
				</table>
				<table style="margin-top:20px;" width="650" border="1" rules="all" cellpadding="0"
				cellspacing="0" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="50">Prog. No</th>
					<th width="110">Finish Dia</th>
					<th width="170">Machine Dia & Gauge</th>
					<th width="110">Program Qty</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<tr>
						<td width="40" align="center"><? echo $k; ?></td>
						<td width="50" align="center">&nbsp;<? echo $colorArray[0][csf('knit_id')]; ?></td>
						<td width="110" align="center"><? echo $colorArray[0][csf('dia')]; ?></td>
						<td width="170"
						align="center"><? echo $colorArray[0][csf('machine_dia')] . "X" . $colorArray[0][csf('machine_gg')]; ?></td>
						<td width="110"
						align="right"><? echo number_format($colorArray[0][csf('program_qnty')], 2); ?>
					&nbsp;</td>
					<td><? echo $colorArray[0][csf('remarks')]; ?></td>
				</tr>
				<?
				$tot_prog_qty += $colorArray[0][csf('program_qnty')];
				?>
				<tr>
					<td colspan="4" align="right"><strong>Total : </strong></td>
					<td align="right"><? echo number_format($tot_prog_qty, 2); ?></td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<?
		$returnJob = '';
		$returnPo = '';
		if ($db_type == 0) {
			$booking_cond = "group_concat(a.booking_no)";
			$wo_cond = "group_concat(a.ydw_no)";
			$reqs_no = "group_concat(b.requisition_no)";
		} else if ($db_type == 2) {
			$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
			$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
			$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
		}

		if ($dataArray[0][csf('issue_purpose')] == 8) {
			$sql_returnJob = sql_select("select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_no) group by a.id");
		} else if ($dataArray[0][csf('issue_purpose')] == 2) {
			$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$booking_job_no' group by b.job_no");

			if ($db_type == 0) $poNoCond = "group_concat(id)";
			else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
			$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");
		} else {
			$sql_returnJob = sql_select("select a.job_no, a.booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$booking_job_no' and a.booking_no in($booking_no) group by a.job_no,a.booking_no");

		}

		$returnJob = $sql_returnJob[0][csf('job_no')];
						//$returnPo=$sql_po[0][csf('po_break_down_id')];
		$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
		$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";
		$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
		unset($sql_returnJob);

						/*$req_no_retInJob=sql_select("select $reqs_no as req_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");
						$reqNo_inJob=$req_no_retInJob[0][csf('req_no')];*/
						?>
						<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0"
						cellspacing="0" class="rpt_table">
						<thead>
							<tr>
								<th colspan="6" align="center">Comments (Booking No:<p> <? echo $returnBooking; ?>) [Booking
								Job Deatils]</p></th>
							</tr>
							<tr>
								<th>Buyer</th>
								<th>Job</th>
								<th>Req. Qty</th>
								<th>Cuml. Issue Qty</th>
								<th>Balance Qty</th>
								<th>Remarks</th>
							</tr>
						</thead>
						<?

						$all_requ_no = "";
						if ($dataArray[0][csf('issue_purpose')] != 8) {
							$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 ");

							//$all_requ_no="'".$all_booking_sql[0][csf('requisition_no')]."'";
							$all_requ_no = "'" . implode("','", array_unique(explode(",", $all_booking_sql[0][csf('requisition_no')]))) . "'";
						}
						if ($dataArray[0][csf('issue_purpose')] == 8) {
							$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3", "total_issue_qty");    //
							$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=1", "total_issue_qty");
						} else if ($dataArray[0][csf('issue_purpose')] == 2) {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}

							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and b.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
						} else {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}
							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");


							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");
						}
						$cumulative_qty = 0;
						if ($booking_count == 1) {
							?>
							<tbody>
								<tr>
									<td align="center">
										<? echo $buyer_arr[$job_array[$booking_job_no]['buyer']]; ?>
									</td>
									<td align="center">
										<? echo $job_array[$booking_job_no]['job']; ?>
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($returnReqQty < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}
			}
			else if ($dataArray[0][csf('issue_basis')] == 1) {
				?>
				<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0"
				cellspacing="0" class="rpt_table">
				<thead>
					<tr>
						<th colspan="6" align="center">
							Comments <? if ($dataArray[0][csf('issue_purpose')] != 8) {
								if ($dataArray[0][csf('buyer_job_no')] != '') echo "(Booking Job Details)";
							} ?> </th>
						</tr>
						<tr>
							<th>Buyer</th>
							<th>Job</th>
							<th>Req. Qty</th>
							<th>Cuml. Qty</th>
							<th>Balance Qty</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<?
							//$booking_job_arr=array();
					$returnJob = '';
					$returnPo = '';
					if ($db_type == 0) {
						$booking_cond = "group_concat( distinct a.booking_no)";
						$wo_cond = "group_concat(a.ydw_no)";
						$reqs_no = "group_concat(b.requisition_no)";
					} else if ($db_type == 2) {
						$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
						$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
						$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
					}

					if ($dataArray[0][csf('issue_purpose')] == 8) {
						$sql_returnJob = sql_select("select a.id, a.buyer_id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id, a.buyer_id");
					} else if ($dataArray[0][csf('issue_purpose')] == 2) {
						if ($dataArray[0][csf('buyer_job_no')] != '') {
							$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by b.job_no");

							if ($db_type == 0) $poNoCond = "group_concat(id)";
							else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
							$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='" . $dataArray[0][csf('buyer_job_no')] . "' group by job_no_mst");
						} else {
							$sql_returnJob = sql_select("select a.id, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.item_category_id=24 and a.entry_form in(42,114) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id");
						}
					} else {
								//echo "select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='".$dataArray[0][csf('buyer_job_no')]."' group by a.job_no";
								$sql_returnJob = sql_select("select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by a.job_no");//and a.booking_no='".$dataArray[0][csf('booking_no')]."'
							}
							$returnJob = $sql_returnJob[0][csf('job_no')];
							$returnPo = $sql_po[0][csf('po_break_down_id')];
							$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
							$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";

							$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
							unset($sql_returnJob);

							//$booking_all="'".implode("','",explode(",",$bill_item_id))."'";
							//echo $returnJob.'---------'.$returnPo.'dfgdfgd';

							/*if( $dataArray[0][csf('issue_purpose')]==8 )
					{
						$sql = "select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$dataArray[0][csf('booking_no')]."' group by a.id";
					}
					else if( $dataArray[0][csf('issue_purpose')]==2)
					{
						if($dataArray[0][csf('buyer_job_no')]!="")
						{
							$sql = "select sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='".$dataArray[0][csf('buyer_job_no')]."'"; // and a.ydw_no='".$dataArray[0][csf('booking_no')]."'
						}
					}
					else
					{
						if($dataArray[0][csf('buyer_job_no')]!="")
						{
							$sql = "select a.job_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($returnBooking) and a.job_no='".$dataArray[0][csf('buyer_job_no')]."' group by a.job_no";
						}
					}
					$result = sql_select($sql);*/
					$all_requ_no = "";
					if ($dataArray[0][csf('issue_purpose')] != 8) {
								//echo "select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no";
						$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no");

						$all_requ_no = $all_booking_sql[0][csf('requisition_no')];
						unset($all_booking_sql);
					}

					if ($dataArray[0][csf('issue_purpose')] == 8) {
								$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3", "total_issue_qty");    //
								$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1", "total_issue_qty");
							} else if ($dataArray[0][csf('issue_purpose')] == 2) {
								/*if($all_requ_no!="" || $all_requ_no!=0)
						{
							//$req_cond="or a.requisition_no in ($all_requ_no)";
							//$reqRet_cond="or b.booking_no in ($all_requ_no)";
						}
						else
						{
							//$req_cond="or a.requisition_no in (0)";
							$reqRet_cond="or b.booking_no in (0)";
						}*/
						if ($dataArray[0][csf('buyer_job_no')] != "") {
							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and b.booking_no in ($returnBooking) and b.buyer_job_no='" . $dataArray[0][csf('buyer_job_no')] . "'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2";
							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
						} else {
									//echo "select sum(a.cons_quantity) as total_issue_qty from inv_transaction a, inv_issue_master b where b.id=a.mst_id and b.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.buyer_job_no<>'' and b.issue_purpose=2";
							$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.issue_purpose=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2";
							$total_return_qty = return_field_value("sum(a.cons_quantity) as total_return_qty", "inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
						}
					} else {
						if ($dataArray[0][csf('buyer_job_no')] != "") {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
										$req_cond = "";//or a.requisition_no in (0)
										$reqRet_cond = "";//or b.booking_no in (0)
									}
									$total_issue_qty = 0;
									//echo "select sum(c.quantity) as total_issue_qty from inv_transaction a, inv_issue_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2";
									$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and ( b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");
									//echo "SELECT sum(c.quantity) as total_return_qty from inv_transaction a, inv_receive_master b, order_wise_pro_details c where b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and b.buyer_job_no='".$dataArray[0][csf('buyer_job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2";
									$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");

									//$total_issue_qty=return_field_value("sum(quantity) as total_issue_qty","order_wise_pro_details","po_breakdown_id in ($returnPo) and status_active=1 and is_deleted=0 and trans_type=2 and entry_form=3","total_issue_qty");
									//$total_return_qty=return_field_value("sum(quantity) as total_return_qty","order_wise_pro_details","po_breakdown_id in ($returnPo) and status_active=1 and is_deleted=0 and trans_type=4 and entry_form=8","total_return_qty");
								}
							}
							?>
							<tbody>
								<tr>
									<td align="center">
										<?
										if ($dataArray[0][csf('issue_purpose')] == 8) echo $buyer_arr[$sql_returnJob[0][csf('buyer_id')]];
										else echo $buyer_arr[$job_array[$dataArray[0][csf('buyer_job_no')]]['buyer']];
										?>
									</td>
									<td align="center">
										<? echo $job_array[$dataArray[0][csf('buyer_job_no')]]['job']; ?>&nbsp;
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = 0;
										$cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')] < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}
				?>
				<br>
				<?
				echo signature_table(49, $data[0], "1030px");
				?>
			</div>
		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
                    var value = valuess;//$("#barcodeValue").val();
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
                    $("#barcode_img_id").html('11');
                    value = {code: value, rect: false};
                    $("#barcode_img_id").show().barcode(value, btype, settings);
                }
                generateBarcode('<? echo $data[1]; ?>');
            </script>
            <?
            exit();
        }

        if ($action == "yarn_issue_print3")
        {
        	extract($_REQUEST);
        	echo load_html_head_contents("Yarn Issue Challan Print3", "../../", 1, 1, '', '', '');
        	$data= explode('*', $data);
        	$print_with_vat = $data[7];

        	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
        	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	//$other_party_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
        	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
        	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
        	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
        	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

        	$sql = " select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";
	//echo $sql;die;
        	$dataArray = sql_select($sql);

        	?>
        	<div style="width:1000px;">
        		<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
        			<tr>
        				<td colspan="6" align="center" style="font-size:18px">
        					<strong><? echo $company_library[$data[0]]; ?></strong></td>
        					<td style="font-size:xx-large; font-style:italic;" align="right">
        						<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
        					</td>
        				</tr>
        				<tr class="form_caption">
        					<?
        					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
        					?>
        					<td align="left" width="50">
        						<?
        						foreach ($data_array as $img_row) {
        							if ($data[5] != 1) {
        								?>
        								<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
        								align="middle"/>
        								<?
        							} else {
        								?>
        								<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
        								align="middle"/>
        								<?
        							}
        						}
        						?>
        					</td>
        					<td colspan="4" align="center">
        						<?
        						echo show_company($data[0], '', '');
        						?>
        					</td>
        					<td>&nbsp;</td>
        				</tr>
        				<tr>
        					<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan/Gate Pass</u></strong></center>
        					</td>
        					<td>&nbsp;</td>
        				</tr>
        				<tr>
        					<td><strong>Issue Date:</strong></td>
        					<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
        				</tr>
        				<tr>
        					<td width="160"><strong>Issue No:</strong></td>
        					<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
        					<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else
				echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
				?>
			</td>
			<td><strong>Gate Pass No:</strong></td>
			<td width="175px"><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
		</tr>
		<tr>
			<td><strong>Issue To:</strong></td>
			<?
			if ($dataArray[0][csf('knit_dye_source')] == 3)
			{
				$supp_add = $dataArray[0][csf('knit_dye_company')];
				$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
				foreach ($nameArray as $result) {
					$address = "";
						if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					unset($nameArray);
				}

				$loan_party_add = $dataArray[0][csf('loan_party')];
				$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
				foreach ($loanPartyArray as $result) {
					$addressParty = "";
					if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
				}
				unset($loanPartyArray);
				?>
				<td colspan="3">
					<?
					if ($dataArray[0][csf('issue_purpose')] == 3) {
						echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
					} else if ($dataArray[0][csf('issue_purpose')] == 5) {
						echo $supplier_arr[$dataArray[0][csf('loan_party')]] . ' : <b>Address</b>: ' . $addressParty;
					} else {
						if ($dataArray[0][csf('knit_dye_source')] == 1)
							echo $company_library[$dataArray[0][csf('knit_dye_company')]];
						else
							echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					}
					?>
				</td>
				<td><strong>Knitting Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
			</tr>

			<tr>
				<td><strong>Address:</strong></td>
				<?
				if ($dataArray[0][csf('knit_dye_source')] == 3)
				{
					$supp_add = $dataArray[0][csf('knit_dye_company')];
					$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
					foreach ($nameArray as $result) {
						$address = "";
						if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					unset($nameArray);
				}

				$loan_party_add = $dataArray[0][csf('loan_party')];
				$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
				foreach ($loanPartyArray as $result) {
					$addressParty = "";
					if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
				}
				unset($loanPartyArray);
				?>
				<td>
					<?
					if ($dataArray[0][csf('issue_purpose')] == 3) {
						echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
					} else if ($dataArray[0][csf('issue_purpose')] == 5) {
						echo $supplier_arr[$dataArray[0][csf('loan_party')]] . ' : <b>Address</b>: ' . $addressParty;
					} else {
						if ($dataArray[0][csf('knit_dye_source')] == 1)
							echo $company_library[$dataArray[0][csf('knit_dye_company')]];
						else
							echo $address;
					}
					?>
				</td>
				<td><strong>Challan No:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td valign="top"><strong>Remarks :</strong></td>
				<td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
			</tr>
			<?
			if ($print_with_vat == 1) {
				?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><strong>Bar Code:</strong></td>
					<td colspan="3" id="barcode_img_id"></td>
				</tr>
				<?
			} else {
				?>
				<tr>
					<td valign="top">&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td><strong>Bar Code:</strong></td>
					<td colspan="3" id="barcode_img_id"></td>
				</tr>
				<?
			}
			?>
		</table>
	</div>
	<div style="width:100%;">
		<table style="margin-left:5px;"  cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" style="font-size:13px">
				<th width="30">SL</th>
				<th width="150">Buyer</th>
				<th width="100">FSO & Fab. Booking No</th>
				<th width="100">Req. No</th>
				<th width="120">Program No</th>

				<th width="100">Lot No</th>
				<th width="150">Supplier</th>
				<th width="150"><b>Item Details</b></th>
				<th width="100">Issue Qty(kg)</th>
				<th>Bag & Cone</th>
			</thead>
			<?
			//$wopi_library = return_library_array("select id,pi_number from com_pi_master_details", "id", "pi_number");

			$cond = "";
			if ($data[5] != "") $cond .= " and c.id='$data[5]'";

			$po_array = array();
			$job_array = array();
			$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0]");
			foreach ($costing_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
				$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
			}
			unset($costing_sql);

			$job_no_array = array();
			$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id from fabric_sales_order_mst");
			foreach ($jobData as $row) {
				$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
				$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
			}
					//var_dump($po_array);
			$po_id_req_array = array();
			$is_sales_array = array();
			if ($db_type == 0) {
				$poID = " select c.requisition_no, a.is_sales, group_concat(distinct(a.po_id)) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
			} else {
				$poID = "select c.requisition_no, a.is_sales, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
			}
			$poID_sql_result = sql_select($poID);
			foreach ($poID_sql_result as $row) {
				$po_id_req_array[$row[csf('requisition_no')]] = $row[csf('poid')];
				$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
			}
			unset($poID_sql_result);

			$i = 1;
			if ($db_type == 0) {
				$sql_result = sql_select("select a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.detarmination_id,
					f.knit_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty,
					b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone,
					c.issue_basis,e.sales_booking_no, h.buyer_id,i.buyer_id as non_ord_samp_buyer, c.buyer_job_no, a.supplier_id,group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro,e.job_no,e.buyer_id sales_buyer,e.within_group
					from product_details_master a,
					inv_issue_master c,
					inv_transaction b
					left join order_wise_pro_details d on b.id=d.trans_id
					left join fabric_sales_order_mst e on d.po_breakdown_id=e.id
					left join ppl_yarn_requisition_entry f on b.requisition_no=f.requisition_no
					left join wo_booking_mst h on h.booking_no=e.sales_booking_no
					left join wo_non_ord_samp_booking_mst i on i.booking_no=e.sales_booking_no and e.within_group=1
					where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.detarmination_id,f.knit_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, e.sales_booking_no, c.buyer_id, c.buyer_job_no,e.job_no,e.buyer_id,i.buyer_id, e.within_group");

			} else {
				$sql_result = sql_select("select a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.detarmination_id,
					f.knit_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty,
					b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone,
					c.issue_basis,e.sales_booking_no, h.buyer_id,i.buyer_id as non_ord_samp_buyer, c.buyer_job_no, a.supplier_id,listagg(d.po_breakdown_id, ',')
					within group (order by d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro,e.job_no,e.buyer_id sales_buyer,e.within_group
					from product_details_master a,
					inv_issue_master c,
					inv_transaction b
					left join order_wise_pro_details d on b.id=d.trans_id
					left join fabric_sales_order_mst e on d.po_breakdown_id=e.id
					left join ppl_yarn_requisition_entry f on b.requisition_no=f.requisition_no
					left join wo_booking_mst h on h.booking_no=e.sales_booking_no
					left join wo_non_ord_samp_booking_mst i on i.booking_no=e.sales_booking_no and e.within_group=1
					where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.detarmination_id,f.knit_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, e.sales_booking_no, c.buyer_id, c.buyer_job_no,e.job_no,h.buyer_id,e.buyer_id,i.buyer_id,e.within_group");

			}

			$all_req_no = '';
			$all_prod_id = '';
			$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
			$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
			foreach ($sql_result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if ($row[csf('requisition_no')] != '') {
					if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
					if ($all_prod_id == '') $all_prod_id = $row[csf('po_id')]; else $all_prod_id .= ',' . $row[csf('po_id')];
				}
				$order_qnty_val = $row[csf('cons_quantity')];
				$order_qnty_val_sum += $order_qnty_val;

				$order_amount_val = $row[csf('order_amount')];
				$order_amount_val_sum += $order_amount_val;

				$no_of_bags_val = $row[csf('no_of_bags')];
				$no_of_bags_val_sum += $no_of_bags_val;

				$po_id = explode(",", $row[csf('po_id')]);
				$po_no = '';
				$style_ref = '';
				$job_no = '';
				$buyer = '';
						//$data=explode('*',$data);
				$productdtls = $row[csf('product_name_details')];
				$productArr = explode(' ', $productdtls);

				$proddtls = '';
				if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '' && $productArr[5] != '') {
					$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . '%, ' . $productArr[3] . ' ' . $productArr[4] . "%, " . $productArr[5] . ", " . $productArr[6];
				} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '') {
					$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3] . ', ' . $productArr[4];
				} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '') {
					$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3];
				} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '') {
					$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ';
				} else if ($productArr[0] != '' && $productArr[1] != '') {
					$proddtls = $productArr[0] . ', ' . $productArr[1];
				} else if ($productArr[0] != '') {
					$proddtls = $productArr[0];
				} else {
					$proddtls = '';
				}

				$internal_ref = '';
				$file_no = '';
				if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2)
				{
					foreach ($po_id as $val) {
						if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
						if ($job_no == '') $job_no = $po_array[$val]['job_no'];
						if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
						if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

						if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
						if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
					}
					$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
					$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
					$ref_cond = '';
					foreach ($job_ref as $ref) {
								//$ref_cond.=", ".$ref;
						if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
					}
					$file_con = '';
					foreach ($job_file as $file) {
						if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
					}

					$buyer_job = '';
					if ($row[csf('issue_basis')] == 1) {
						if ($dataArray[0][csf('issue_purpose')] == 8) {
							$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
						} else {
							$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
							if ($row[csf('buyer_job_no')] != "") {
								$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
							}
						}
					} else if ($row[csf('issue_basis')] == 2) {
						$buyer_job = '';
					}
				}
				else if ($row[csf('issue_basis')] == 3)
				{

					if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
						$buyer_job = '';
						foreach (array_unique($po_id) as $val) {
							if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
							if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];

							if ($buyer_job == '') {
								if ($job_no_array[$val]['within_group'] == 1) {
									$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
								} else {
									$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
								}
							}
						}
					} else {
						foreach ($po_id as $val) {
							if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
							if ($job_no == '') $job_no = $po_array[$val]['job_no'];
							if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
							if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

							if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
							if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
						}

						$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
						$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

						$ref_cond = '';
						foreach ($job_ref as $ref) {
									//$ref_cond.=", ".$ref;
							if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
						}

						$file_con = '';
						foreach ($job_file as $file) {
							if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
						}
						if ($job_no != '') {
							$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
						} else {
							$buyer_job = $buyer_arr[$buyer_name];
						}
					}
				}
				else
				{
					foreach (array_unique($po_id) as $val) {
						if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
						if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
						if ($buyer_job == '') {
							if ($job_no_array[$val]['within_group'] == 1) {
								$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
							} else {
								$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
							}
						}
					}
				}


				$job_buyer_arr = explode("&",$buyer_job);
				$buyer=$job_buyer_arr[0];
				$job_no=$job_buyer_arr[1];

				$no_bag_cone = "";
				$wt_bag_cone = "";
				if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

					if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];

						//$buyer_id = ($row[csf("within_group")] == 2)?$row[csf('sales_buyer')]:$row[csf('buyer_id')]
						if($row[csf("within_group")] == 2){
							$buyer_id=$row[csf('sales_buyer')];
						}
						else if($row[csf('buyer_id')])
						{
							$buyer_id=$row[csf('buyer_id')];
						}else{
							$buyer_id=$row[csf('non_ord_samp_buyer')];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
							<td width="30"><? echo $i; ?></td>
							<td width="150">
								<div style="word-wrap:break-word; width:100px"><? echo $buyer_arr[$buyer_id]; ?></div>
							</td>
							<td width="100">
								<div style="word-wrap:break-word; width:110px"><? echo $row[csf('job_no')]." &<br>".$row[csf('sales_booking_no')]; ?></div>
							</td>
							<td width="100">
								<div style="word-wrap:break-word; width:110px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
							</td>
							<td width="120">
								<div style="word-wrap:break-word; width:110px"><? echo $row[csf('knit_id')]; ?></div>
							</td>


							<td width="100">
								<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
							</td>

							<td width="150">
								<? echo $supplier_arr[$row[csf('supplier_id')]]; ?>
							</td>

							<td width="150">
								<div style="word-wrap:break-word; width:110px"><? echo $proddtls; ?></div>
							</td>

							<td align="right"
							width="100"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?>
						</td>

						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
							&nbsp;</div>
						</td>
					</tr>
					<?
					$uom_unit = "Kg";
					$uom_gm = "Grams";
					$tot_return_qty += $row[csf('returnable_qnty')];
					$tot_bag += $no_of_bags_val;
					$tot_cone += $row[csf('cone_per_bag')];
					$tot_w_bag += $row[csf('weight_per_bag')];
					$tot_w_cone += $row[csf('weight_per_cone')];
					$req_no = $row[csf('requisition_no')];
					$i++;
				}
				?>
				<tr bgcolor="#CCCCCC" style="font-size:14px">
					<td align="right" colspan="8"><b>Total:</b></td>
					<td align="right">
						<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
					</td>
					<td><? echo 'N:' . number_format($tot_bag, 2);
					if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
					echo '<br>W:' . number_format($tot_w_bag, 2);
					if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?>
				</td>
			</tr>
			<tr>
				<td colspan="10" align="left"><b>In Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
			</tr>
		</table>
		<table width="1030" cellspacing="0" border="0" style="margin-left:10px; margin-top:60px">
			<tr>
				<td><strong>Transport No:</strong></td>
				<td><strong>Driver Name:</strong></td>
				<td><strong>D/L No:</strong></td>
				<td><strong>Mobile No:</strong></td>
			</tr>
		</table>
		<br>
		<?
		echo signature_table(49, $data[0], "1030px");
		?>
	</div>


	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
				var value = valuess;//$("#barcodeValue").val();
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
				$("#barcode_img_id").html('11');
				value = {code: value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');
		</script>
		<?
		exit();
	}

	if ($action == "yarn_issue_print8")
	{
		extract($_REQUEST);
		echo load_html_head_contents("Yarn Issue Challan Print2", "../../", 1, 1, '', '', '');
		$data = explode('*', $data);
		$print_with_vat = $data[7];

		/*$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');*/

		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
		$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');


		//$other_party_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$sql = " select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";
		//echo $sql;die;
		$dataArray = sql_select($sql);

		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

		?>
		<div style="width:1000px;">
			<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td colspan="6" align="center" style="font-size:18px">
						<strong><? echo $company_library[$data[0]]; ?></strong></td>
						<td style="font-size:xx-large; font-style:italic;" align="right">
							<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
						</td>
					</tr>
					<tr class="form_caption">

						<?
						$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
						?>
						<td align="left" width="50">
							<?
							foreach ($data_array as $img_row) {
								if ($data[5] != 1) {
									?>
									<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								} else {
									?>
									<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								}
							}
							?>
						</td>
						<td colspan="4" align="center">
							<?
							echo show_company($data[0], '', '');
						/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}*/
					?>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan</u></strong></center>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>

				<td width="100"><strong>Issue To:</strong></td>
				<?
				if ($dataArray[0][csf('knit_dye_source')] == 3) {
					$supp_add = $dataArray[0][csf('knit_dye_company')];
					$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
					foreach ($nameArray as $result) {
						$address = "";
							if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
						}
						unset($nameArray);
					}

					$loan_party_add = $dataArray[0][csf('loan_party')];
					$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
					foreach ($loanPartyArray as $result) {
						$addressParty = "";
						if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					unset($loanPartyArray);
					?>
					<td >
						<?
						if ($dataArray[0][csf('issue_purpose')] == 3) {
							echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
						} else if ($dataArray[0][csf('issue_purpose')] == 5) {
							echo $supplier_arr[$dataArray[0][csf('loan_party')]] ;
						} else {
							if ($dataArray[0][csf('knit_dye_source')] == 1)
								echo $company_library[$dataArray[0][csf('knit_dye_company')]];
							else
								echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
						}
						?>
					</td>
					<td width="120"><strong>Work Order:</strong></td>
					<td width="175px"><? //echo $dataArray[0][csf('booking_no')]; ?></td>
					<td width="160"><strong>Issue No:</strong></td>
					<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				</tr>
				<tr>
					<td rowspan="2" valign="top"><strong>Address:</strong></td>
					<td rowspan="2" width="175px" valign="top">
						<?
						if ($dataArray[0][csf('issue_purpose')] == 3) {
							echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
						} else if ($dataArray[0][csf('issue_purpose')] == 5) {
							echo $addressParty;
						} else {
							if ($dataArray[0][csf('knit_dye_source')] == 1)
								echo $company_library[$dataArray[0][csf('knit_dye_company')]];
							else
								echo $address;
						}
						?>
					</td>

					<td width="125" valign="top"><strong>Knitting Source:</strong></td>
					<td width="175px" valign="top"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>


					<td valign="top"><strong>Issue Date:</strong></td>
					<td width="175px" valign="top"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				</tr>

				<tr>

					<td><strong>Issue Purpose:</strong></td>
                <td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else
                echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
                ?></td>

                <td><strong>Do No:</strong></td>
                <td width="175px"><? //echo $dataArray[0][csf('remarks')];
                ?></td>
            </tr>
            <tr>
            	<td></td>
            	<td></td>
            	<td><strong>Fabric Booking No:</strong></td>
            	<td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            	<td><strong>DL No:</strong></td>
                <td width="175px"><? //echo $dataArray[0][csf('remarks')];
                ?></td>
            </tr>
            <tr>
            	<td><strong>Through By:</strong></td>
            	<td></td>
            	<td><strong>Driver Name:</strong></td>
                <td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
                ?></td>

                <td><strong>Gate Pass No:</strong></td>
                <td width="175px"><? //echo $dataArray[0][csf('remarks')];
                ?></td>
            </tr>
            <?
            if ($print_with_vat == 1) {
            	?>
            	<tr>
                    <!-- <td><strong>VAT Number :</strong></td>
                    <td><p>
                        <?
                    $vat_no = return_field_value("vat_number", "lib_company", "id=" . $data[0], "vat_number");
                    echo $vat_no;
                    ?></p></td> -->
                    <td><strong>Bar Code:</strong></td>
                    <td colspan="3" id="barcode_img_id"></td>

                    <td><strong>Demand No.:</strong></td>
                    <td width="175px"><? //echo $dataArray[0][csf('booking_id')];?></td>
                </tr>
                <?
            } else {
            	?>
            	<tr>
            		<td valign="top">&nbsp;</strong></td>
            		<td>&nbsp;</td>
            		<td><strong>Bar Code:</strong></td>
            		<td colspan="3" id="barcode_img_id"></td>

            		<td><strong>Demand No.:</strong></td>
            		<td width="175px"><? //echo $dataArray[0][csf('booking_id')];?></td>
            	</tr>
            	<?
            }
            ?>
            <tr>
            	<td valign="top"><strong>Remarks :</strong></td>
            	<td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
            </tr>

        </table>
        <br>
        <div style="width:100%;">
        	<div style="clear:both;">
        		<table style="margin-right:-40px;" cellspacing="0" width="1000" border="1" rules="all"
        		class="rpt_table">
        		<thead bgcolor="#dddddd" style="font-size:13px">
        			<th width="20">SL</th>
        			<th width="45">Req. No</th>
        			<th width="50">Lot No</th>
        			<th width="110">Item Details</th>
        			<th width="65">Y. Type</th>
        			<th width="80">Bag & Cone</th>
        			<th width="60">Rtnbl Qty(kg)</th>
						<!--<th width="65">Dye. Color</th>
							<th width="60">Supp.</th>-->
							<th width="60"><b>Issue Qty(kg)</b></th>
							<!--<th width="80">Store</th>-->
							<th width="100">Buyer & Job</th>
							<th width="120">Order</th>
							<!--<th>Inte. Ref & File</th>-->
						</thead>
						<?
						$wopi_library = return_library_array("select id,pi_number from com_pi_master_details", "id", "pi_number");

						$cond = "";
						if ($data[5] != "") $cond .= " and c.id='$data[5]'";

						$po_array = array();
						$job_array = array();
						$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0]");
						foreach ($costing_sql as $row) {
							$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
							$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
							$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
							$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
							$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
							$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
							$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
							$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
						}
						unset($costing_sql);

						$job_no_array = array();
						$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id from fabric_sales_order_mst");
						foreach ($jobData as $row) {
							$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
							$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
							$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
							$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
						}
						//var_dump($po_array);
						$po_id_req_array = array();
						$is_sales_array = array();
						if ($db_type == 0) {
							$poID = " select c.requisition_no, a.is_sales, group_concat(distinct(a.po_id)) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
						} else {
							$poID = "select c.requisition_no, a.is_sales, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
						}
						$poID_sql_result = sql_select($poID);
						foreach ($poID_sql_result as $row) {
							$po_id_req_array[$row[csf('requisition_no')]] = $row[csf('poid')];
							$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
						}
						unset($poID_sql_result);

						$i = 1;
						if ($db_type == 0) {
							$sql_result = sql_select("select a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
						} else {
							$sql_result = sql_select("select a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, LISTAGG(d.po_breakdown_id, ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
						}
						$all_req_no = '';
						$all_prod_id = '';
						$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
						$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
						foreach ($sql_result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							if ($row[csf('requisition_no')] != '') {
								if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
								if ($all_prod_id == '') $all_prod_id = $row[csf('po_id')]; else $all_prod_id .= ',' . $row[csf('po_id')];
							}
							$order_qnty_val = $row[csf('cons_quantity')];
							$order_qnty_val_sum += $order_qnty_val;

							$order_amount_val = $row[csf('order_amount')];
							$order_amount_val_sum += $order_amount_val;

							$no_of_bags_val = $row[csf('no_of_bags')];
							$no_of_bags_val_sum += $no_of_bags_val;

							$po_id = explode(",", $row[csf('po_id')]);
							$po_no = '';
							$style_ref = '';
							$job_no = '';
							$buyer = '';
							//$data=explode('*',$data);
							$productdtls = $row[csf('product_name_details')];
							$productArr = explode(' ', $productdtls);

							$proddtls = '';
							if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '' && $productArr[5] != '') {
								$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . '%, ' . $productArr[3] . ' ' . $productArr[4] . "%, " . $productArr[5] . ", " . $productArr[6];
							} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '') {
								$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3] . ', ' . $productArr[4];
							} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '') {
								$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3];
							} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '') {
								$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ';
							} else if ($productArr[0] != '' && $productArr[1] != '') {
								$proddtls = $productArr[0] . ', ' . $productArr[1];
							} else if ($productArr[0] != '') {
								$proddtls = $productArr[0];
							} else {
								$proddtls = '';
							}

							$internal_ref = '';
							$file_no = '';
							if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2) {
								foreach ($po_id as $val) {
									if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
									if ($job_no == '') $job_no = $po_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
									if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

									if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
									if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
								}
								$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
								$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
								$ref_cond = '';
								foreach ($job_ref as $ref) {
									//$ref_cond.=", ".$ref;
									if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
								}
								$file_con = '';
								foreach ($job_file as $file) {
									if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
								}

								$buyer_job = '';
								if ($row[csf('issue_basis')] == 1) {
									if ($dataArray[0][csf('issue_purpose')] == 8) {
										$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
									} else {
										$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
										if ($row[csf('buyer_job_no')] != "") {
											$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
										}
									}
								} else if ($row[csf('issue_basis')] == 2) {
									$buyer_job = '';
								}
							} else if ($row[csf('issue_basis')] == 3) {
								//$ex_data = array_unique(explode(",",$po_id_req_array[$row[csf('requisition_no')]]));
								//print_r($ex_data);

								if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
									$buyer_job = '';
									foreach (array_unique($po_id) as $val) {
										if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
										if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];

										if ($buyer_job == '') {
											if ($job_no_array[$val]['within_group'] == 1) {
												$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
											} else {
												$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
											}
										}
									}
								} else {
									foreach ($po_id as $val) {
										if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
										if ($job_no == '') $job_no = $po_array[$val]['job_no'];
										if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
										if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

										if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
										if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
									}

									$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
									$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

									$ref_cond = '';
									foreach ($job_ref as $ref) {
										//$ref_cond.=", ".$ref;
										if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
									}

									$file_con = '';
									foreach ($job_file as $file) {
										if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
									}
									if ($job_no != '') {
										$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
									} else {
										$buyer_job = $buyer_arr[$buyer_name];
									}
								}
							} else {
								foreach (array_unique($po_id) as $val) {
									if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
									if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
									if ($buyer_job == '') {
										if ($job_no_array[$val]['within_group'] == 1) {
											$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
										} else {
											$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
										}
									}
								}
							}

							$no_bag_cone = "";
							$wt_bag_cone = "";
							if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

								if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
										<td width="20"><? echo $i; ?></td>
										<td width="45">
											<div style="word-wrap:break-word; width:45px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
										</td>
										<td width="50">
											<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
												<?
                                        //echo $proddtls;
												echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_name_arr[$row[csf('color')]];
												?>
											</div>
										</td>
										<td width="65">
											<div style="word-wrap:break-word; width:65px"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></div>
										</td>
										<td width="80">
											<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
											&nbsp;</div>
										</td>
										<td align="right"
										width="60"><? echo number_format($row[csf('returnable_qnty')], 2, '.', ''); ?></td>

<?php /*?>
									<td width="65">
										<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('dyeing_color_id')]]; ?>
										&nbsp;</div>
									</td>

									<td width="60">
										<div style="word-wrap:break-word; width:60px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div>
									</td>
									<?php */?>
									<td align="right"
									width="60"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>


								<?php /*?>	<td width="80">
										<div style="word-wrap:break-word; width:80px"><? echo $store_arr[$row[csf('store_id')]]; ?></div>
									</td><?php */?>
									<td width="100">
										<div style="word-wrap:break-word; width:100px"><? echo $buyer_job; ?></div>
									</td>
									<td width="120">
										<div style="word-wrap:break-word; width:120px"><? echo $po_no;
										//if ($style_ref != "") echo ' & ' . $style_ref; else echo '&nbsp;'; ?></div>
									</td>
									<?php /*?><td>
										<div style="word-wrap:break-word; width:80px"><? echo ltrim($ref_cond, ", ");
										if ($file_cond != "") echo ' & ' . ltrim($file_cond, ", "); else echo '&nbsp;'; ?></div>
									</td><?php */?>
								</tr>
								<?
								$uom_unit = "Kg";
								$uom_gm = "Grams";
								$tot_return_qty += $row[csf('returnable_qnty')];
								$tot_bag += $no_of_bags_val;
								$tot_cone += $row[csf('cone_per_bag')];
								$tot_w_bag += $row[csf('weight_per_bag')];
								$tot_w_cone += $row[csf('weight_per_cone')];
								$req_no = $row[csf('requisition_no')];
								$i++;
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-size:13px">
								<td align="right" colspan="7"><b>Total</b></td>
								<td align="right">
									<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
								</td>
								<td><? echo 'N:' . number_format($tot_bag, 2);
								if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
								echo '<br>W:' . number_format($tot_w_bag, 2);
								if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>

								<td></td>
								<?php /*?><td align="right"><? echo number_format($tot_return_qty, 2, '.', ''); ?></td>
								<td><? echo 'N:' . number_format($tot_bag, 2);
								if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
								echo '<br>W:' . number_format($tot_w_bag, 2);
								if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>
								<td align="right" colspan="4">&nbsp;</td><?php */?>
							</tr>
							<tr>
								<td colspan="14" align="left"><b>In
									Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
								</tr>
							</table>
						</div>
						<br>
						<br>&nbsp;
						<!--================================================================-->


						<script type="text/javascript" src="../../js/jquery.js"></script>
						<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
						<script>
							function generateBarcode(valuess) {
							var value = valuess;//$("#barcodeValue").val();
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
							$("#barcode_img_id").html('11');
							value = {code: value, rect: false};
							$("#barcode_img_id").show().barcode(value, btype, settings);
						}
						generateBarcode('<? echo $data[1]; ?>');
					</script>
					<?
					exit();

				}


				if ($action == "yarn_issue_print5")
				{
					extract($_REQUEST);
					echo load_html_head_contents("Yarn Issue Challan Print5", "../../", 1, 1, '', '', '');
					$data = explode('*', $data);

					$print_with_vat = $data[7];
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
					$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
					$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
					$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
					$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
					$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
					$sql = " select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";

					$dataArray = sql_select($sql);

					$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

					?>
					<div style="width:1000px;">
						<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
							<tr>
								<td colspan="6" align="center" style="font-size:18px">
									<strong><? echo $company_library[$data[0]]; ?></strong></td>
									<td style="font-size:xx-large; font-style:italic;" align="right">
										<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
									</td>
								</tr>
								<tr class="form_caption">

									<?
									$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
									?>
									<td align="left" width="50">
										<?
										foreach ($data_array as $img_row) {
											if ($data[5] != 1) {
												?>
												<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
												align="middle"/>
												<?
											} else {
												?>
												<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
												align="middle"/>
												<?
											}
										}
										?>
									</td>
									<td colspan="4" align="center">
										<?
										echo show_company($data[0], '', '');

										?>
									</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan</u></strong></center>
									</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td width="160"><strong>Issue No:</strong></td>
									<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
									<td width="120"><strong>Booking:</strong></td>
									<td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
									<td width="125"><strong>Knitting Source:</strong></td>
									<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
								</tr>
								<tr>
									<td><strong>Program No:</strong></td>
									<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>

									<td><strong>Issue Purpose:</strong></td>
                    <td width="175px"><? //if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else
                    echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
                    ?></td>
                    <td><strong>Issue Date:</strong></td>
                    <td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
                </tr>
                <tr>
                	<td><strong>Issue To:</strong></td>
                	<?
                	if ($dataArray[0][csf('knit_dye_source')] == 3) {
                		$supp_add = $dataArray[0][csf('knit_dye_company')];
                		$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                		foreach ($nameArray as $result) {
                			$address = "";
                			if ($result != "") $address = $result[csf('address_1')];
                		}
                		unset($nameArray);
                	}

                	$loan_party_add = $dataArray[0][csf('loan_party')];
                	$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
                	foreach ($loanPartyArray as $result) {
                		$addressParty = "";
                		if ($result != "") $addressParty = $result['address'];
                	}
                	unset($loanPartyArray);
                	?>
                	<td colspan="3">
                		<?
                		if ($dataArray[0][csf('issue_purpose')] == 3) {
                			echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
                		} else if ($dataArray[0][csf('issue_purpose')] == 5) {
                			echo $supplier_arr[$dataArray[0][csf('loan_party')]] . ' : Address :- ' . $addressParty;
                		} else {
                			if ($dataArray[0][csf('knit_dye_source')] == 1)
                				echo $company_library[$dataArray[0][csf('knit_dye_company')]];
                			else
                				echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]] . ' : Address :- ' . $address;
                		}
                		?>
                	</td>
                	<td><strong>Location:</strong></td>
                	<td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>


                </tr>
                <tr>
                	<td><strong>Demand No.:</strong></td>
                	<td width="175px"><?
                	?></td>

                	<td><strong>Do No:</strong></td>
                	<td width="175px"><?
                	?></td>
                </tr>
                <tr>
                	<td valign="top"><strong>Remarks :</strong></td>
                	<td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
                </tr>
                <?
                if ($print_with_vat == 1) {
                	?>
                	<tr>

                		<td><strong>Bar Code:</strong></td>
                		<td colspan="3" id="barcode_img_id"></td>
                	</tr>
                	<?
                } else {
                	?>
                	<tr>
                		<td valign="top">&nbsp;</strong></td>
                		<td>&nbsp;</td>
                		<td><strong>Bar Code:</strong></td>
                		<td colspan="3" id="barcode_img_id"></td>
                	</tr>
                	<?
                }
                ?>
            </table>
            <br>
            <div style="width:100%;">
            	<div style="clear:both;">
            		<table style="margin-right:-40px;" cellspacing="0" width="1100" border="1" rules="all"
            		class="rpt_table">
            		<thead bgcolor="#dddddd" style="font-size:13px">
            			<th width="20">SL</th>
            			<th width="45">Req. No</th>
            			<th width="50">Lot No</th>
            			<th width="65">Y. Type</th>
            			<th width="110">Item Details</th>

            			<th width="65">Dye. Color</th>
            			<th width="60">Supp.</th>
            			<th width="60"><b>Issue Qty(kg)</b></th>
            			<th width="60">Rtnbl Qty(kg)</th>
            			<th width="80">Bag & Cone</th>
            			<th width="80">Store</th>
            			<th width="100">Booking No</th>
            			<th width="100">Buyer & Job</th>
            			<th width="120">Order & Style</th>
            			<th>Inte. Ref & File</th>
            		</thead>
            		<?
            		$wopi_library = return_library_array("select id,pi_number from com_pi_master_details", "id", "pi_number");

            		$cond = "";
            		if ($data[5] != "") $cond .= " and c.id='$data[5]'";

            		$po_array = array();
            		$job_array = array();

            		$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number ,c.booking_no
            			from wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on b.id = c.po_break_down_id and c.status_active = 1
            			where a.job_no=b.job_no_mst and b.status_active = 1  and a.company_name= ".$data[0]."
            			group by a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number, c.booking_no");

            		foreach ($costing_sql as $row) {
            			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
            			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
            			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
            			$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
            			$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
            			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
            			$po_array[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
            			$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
            			$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];

            		}
            		unset($costing_sql);

            		$job_no_array = array();
            		$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id, sales_booking_no from fabric_sales_order_mst");
            		foreach ($jobData as $row) {
            			$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
            			$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
            			$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
            			$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
            			$job_no_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
            		}

            		$po_id_req_array = array();
            		$is_sales_array = array();

            		$poID = "select c.requisition_no, a.booking_no, a.is_sales
            		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c
            		where a.id = b.mst_id and b.id = c.knit_id and b.status_active=1 and c.status_active=1
            		group by c.requisition_no, a.booking_no, a.is_sales";

            		$poID_sql_result = sql_select($poID);
            		foreach ($poID_sql_result as $row) {
            			$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
            			$bookFromRequ_array[$row[csf('requisition_no')]] = $row[csf('booking_no')];
            		}
            		unset($poID_sql_result);

            		$i = 1;
            		if ($db_type == 0) {
            			$sql_result = sql_select("select a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
            		} else {
            			$sql_result = sql_select("select a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, LISTAGG(d.po_breakdown_id, ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
            		}
            		$all_req_no = '';
            		$all_prod_id = '';
            		$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
            		$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
            		foreach ($sql_result as $row) {
            			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
            			if ($row[csf('requisition_no')] != '') {
            				if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
            				if ($all_prod_id == '') $all_prod_id = $row[csf('po_id')]; else $all_prod_id .= ',' . $row[csf('po_id')];
            			}
            			$order_qnty_val = $row[csf('cons_quantity')];
            			$order_qnty_val_sum += $order_qnty_val;

            			$order_amount_val = $row[csf('order_amount')];
            			$order_amount_val_sum += $order_amount_val;

            			$no_of_bags_val = $row[csf('no_of_bags')];
            			$no_of_bags_val_sum += $no_of_bags_val;

            			$po_id = explode(",", $row[csf('po_id')]);
            			$po_no = '';
            			$style_ref = '';
            			$job_no = '';
            			$buyer = '';

            			$productdtls = $row[csf('product_name_details')];
            			$productArr = explode(' ', $productdtls);

            			$proddtls = '';
            			if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '' && $productArr[5] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . '%, ' . $productArr[3] . ' ' . $productArr[4] . "%, " . $productArr[5] . ", " . $productArr[6];
            			} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3] . ', ' . $productArr[4];
            			} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3];
            			} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ';
            			} else if ($productArr[0] != '' && $productArr[1] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1];
            			} else if ($productArr[0] != '') {
            				$proddtls = $productArr[0];
            			} else {
            				$proddtls = '';
            			}

            			$internal_ref = '';
            			$file_no = '';$booking_no='';
            			if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2) {
            				foreach ($po_id as $val) {
            					if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
            					if ($job_no == '') $job_no = $po_array[$val]['job_no'];
            					if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
            					if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];
            					if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
            					if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];

            					if ($booking_no == '') $booking_no = $po_array[$val]['booking_no']; else $booking_no .= "," . $po_array[$val]['booking_no'];
            				}
            				$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
            				$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
            				$ref_cond = '';
            				foreach ($job_ref as $ref) {

            					if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
            				}
            				$file_con = '';
            				foreach ($job_file as $file) {
            					if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
            				}

            				$buyer_job = '';
            				if ($row[csf('issue_basis')] == 1) {

            					$booking_no =$row[csf('booking_no')];
            					if ($dataArray[0][csf('issue_purpose')] == 8) {
            						$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
            					} else {
            						$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
            						if ($row[csf('buyer_job_no')] != "") {
            							$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
            						}
            					}
            				} else if ($row[csf('issue_basis')] == 2) {

            					$buyer_job = '';
            				}
            			} else if ($row[csf('issue_basis')] == 3) {

            				if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
            					$buyer_job = '';
            					foreach (array_unique($po_id) as $val) {
            						if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
            						if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
            						if ($booking_no == '') $booking_no = $job_no_array[$val]['sales_booking_no'];
            						if ($buyer_job == '') {
            							if ($job_no_array[$val]['within_group'] == 1) {
            								$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
            							} else {
            								$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
            							}
            						}
            					}
            				} else {
            					foreach ($po_id as $val) {
            						if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
            						if ($job_no == '') $job_no = $po_array[$val]['job_no'];
            						if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
            						if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

            						if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
            						if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
            					}

            					$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
            					$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

            					$ref_cond = '';
            					foreach ($job_ref as $ref) {

            						if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
            					}

            					$file_con = '';
            					foreach ($job_file as $file) {
            						if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
            					}
            					if ($job_no != '') {
            						$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
            					} else {
            						$buyer_job = $buyer_arr[$buyer_name];
            					}

            					$booking_no = $bookFromRequ_array[$row[csf('requisition_no')]];
            				}
            			} else {
            				foreach (array_unique($po_id) as $val) {
            					if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
            					if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
            					if ($buyer_job == '') {
            						if ($job_no_array[$val]['within_group'] == 1) {
            							$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
            						} else {
            							$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
            						}
            					}

            					if ($booking_no == '') $booking_no = $po_array[$val]['booking_no']; else $booking_no .= "," . $po_array[$val]['booking_no'];

            				}
            			}

            			$no_bag_cone = "";
            			$wt_bag_cone = "";
            			if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

            				if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];
            					?>
            					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
            						<td width="20"><? echo $i; ?></td>
            						<td width="45">
            							<div style="word-wrap:break-word; width:45px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
            						</td>
            						<td width="50">
            							<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
            						</td>
            						<td width="65">
            							<div style="word-wrap:break-word; width:65px"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></div>
            						</td>
            						<td width="110">
            							<div style="word-wrap:break-word; width:110px">
            								<?

            								echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_name_arr[$row[csf('color')]];
            								?>
            							</div>
            						</td>


            						<td width="65">
            							<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('dyeing_color_id')]]; ?>
            							&nbsp;</div>
            						</td>

            						<td width="60">
            							<div style="word-wrap:break-word; width:60px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div>
            						</td>

            						<td align="right"
            						width="60"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>
            						<td align="right"
            						width="60"><? echo number_format($row[csf('returnable_qnty')], 2, '.', ''); ?></td>
            						<td width="80">
            							<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
            							&nbsp;</div>
            						</td>
            						<td width="80">
            							<div style="word-wrap:break-word; width:80px"><? echo $store_arr[$row[csf('store_id')]]; ?></div>
            						</td>
            						<td width="100">
            							<div style="word-wrap:break-word; width:100px"><? echo $booking_no; ?></div>
            						</td>
            						<td width="100">
            							<div style="word-wrap:break-word; width:100px"><? echo $buyer_job; ?></div>
            						</td>
            						<td width="120">
            							<div style="word-wrap:break-word; width:120px"><? echo $po_no;
            							if ($style_ref != "") echo ' & ' . $style_ref; else echo '&nbsp;'; ?></div>
            						</td>
            						<td>
            							<div style="word-wrap:break-word; width:80px"><? echo ltrim($ref_cond, ", ");
            							if ($file_cond != "") echo ' & ' . ltrim($file_cond, ", "); else echo '&nbsp;'; ?></div>
            						</td>
            					</tr>
            					<?
            					$uom_unit = "Kg";
            					$uom_gm = "Grams";
            					$tot_return_qty += $row[csf('returnable_qnty')];
            					$tot_bag += $no_of_bags_val;
            					$tot_cone += $row[csf('cone_per_bag')];
            					$tot_w_bag += $row[csf('weight_per_bag')];
            					$tot_w_cone += $row[csf('weight_per_cone')];
            					$req_no = $row[csf('requisition_no')];
            					$i++;
            				}
            				?>
            				<tr bgcolor="#CCCCCC" style="font-size:13px">
            					<td align="right" colspan="7"><b>Total</b></td>
            					<td align="right">
            						<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
            					</td>
            					<td align="right"><? echo number_format($tot_return_qty, 2, '.', ''); ?></td>
            					<td><? echo 'N:' . number_format($tot_bag, 2);
            					if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
            					echo '<br>W:' . number_format($tot_w_bag, 2);
            					if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>
            					<td align="right" colspan="5">&nbsp;</td>
            				</tr>
            				<tr>
            					<td colspan="15" align="left"><b>In
            						Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
            					</tr>
            				</table>
            			</div>
            			<br>
            			<br>&nbsp;

            			<?
            			if ($data[6] == 1)
            			{
            				if ($dataArray[0][csf('issue_basis')] == 3){
            					?>
            					<div style="">
            						<table width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
            							<thead>
            								<tr>
            									<th colspan="7" align="center">Requisition Details</th>
            								</tr>
            								<tr>
            									<th width="40">SL</th>
            									<th width="100">Requisition No</th>
            									<th width="110">Lot No</th>
            									<th width="220">Yarn Description</th>
            									<th width="110">Brand</th>
            									<th width="90">Requisition Qty</th>
            									<th>Remarks</th>
            								</tr>
            							</thead>
            							<?

            							$i = 1;
            							$tot_reqsn_qnty = 0;
            							$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
            							$product_details_array = array();
            							$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id='$data[0]' and status_active=1 and is_deleted=0";
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
            							}
            							unset($result);
            							$sql = "select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where requisition_no in ($all_req_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
            							$nameArray = sql_select($sql);
            							foreach ($nameArray as $selectResult) {
            								?>
            								<tr>
            									<td width="40" align="center"><? echo $i; ?></td>
            									<td width="100" align="center">
            										&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
            										<td width="110" align="center">
            											&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
            											<td width="220">
            												&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
            												<td width="110" align="center">
            													&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
            													<td width="90"
            													align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?>
            												&nbsp;</td>
                                <td>&nbsp;<? //echo $selectResult[csf('requisition_no')];
                                ?></td>
                            </tr>
                            <?
                            $tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
                            $i++;
                        }
                        ?>
                        <tfoot>
                        	<th colspan="5" align="right"><b>Total</b></th>
                        	<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;</th>
                        	<th>&nbsp;</th>
                        </tfoot>
                    </table>
                    <?
                    if ($data[5] != 1) {
                    	$z = 1;
                    	$k = 1;
                    	$colorArray = sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.requisition_no in ($all_req_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");

                    	$booking_al = "";
                    	foreach ($colorArray as $book) {
                    		if ($booking_al == "") $booking_al = $book[csf('booking_no')]; else $booking_al .= ',' . $book[csf('booking_no')];
                    	}

                    	$booking_no = "'" . implode(',', array_unique(explode(',', $booking_al))) . "'";
                    	$booking_count = count(array_unique(explode(',', $booking_no)));

                    	if ($dataArray[0][csf('issue_purpose')] == 2) {
                    		$booking_job = sql_select("select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no in ($booking_no) group by b.job_no");
                    	} else {
                    		$booking_job = sql_select("select job_no from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no in ($booking_no) group by  job_no");
                    	}

                    	$booking_job_no = $booking_job[0][csf('job_no')];
                    	unset($booking_job);

                    	if ($db_type == 0) $poNoCond = "group_concat(id)";
                    	else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";

                    	$sql_returnJob = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");

                    	$job_po = $sql_returnJob[0][csf('po_break_down_id')];
                    	unset($sql_returnJob);
                    	?>

                    	<table width="710" cellpadding="0" cellspacing="0" border="1" rules="all"
                    	style="margin-top:20px;" class="rpt_table">
                    	<thead>
                    		<th width="40">SL</th>
                    		<th width="250">Fabrication</th>
                    		<th width="120">Color</th>
                    		<th width="120">GGSM OR S/L</th>
                    		<th>FGSM</th>
                    	</thead>
                    	<tbody>
                    		<tr>
                    			<td width="40" align="center"><? echo $z; ?></td>
                    			<td width="250"
                    			align="center"><? echo $body_part[$colorArray[0][csf('body_part_id')]] . ', ' . $colorArray[0][csf('fabric_desc')]; ?></td>
                    			<td width="120"
                    			align="center"><? echo $color_range[$colorArray[0][csf('color_range')]]; ?></td>
                    			<td width="120" align="center"><? echo $colorArray[0][csf('stitch_length')]; ?></td>
                    			<td align="center"><? echo $colorArray[0][csf('gsm_weight')]; ?></td>
                    		</tr>
                    	</tbody>
                    </table>
                    <table style="margin-top:20px;" width="650" border="1" rules="all" cellpadding="0"
                    cellspacing="0" class="rpt_table">
                    <thead>
                    	<th width="40">SL</th>
                    	<th width="50">Prog. No</th>
                    	<th width="110">Finish Dia</th>
                    	<th width="170">Machine Dia & Gauge</th>
                    	<th width="110">Program Qty</th>
                    	<th>Remarks</th>
                    </thead>
                    <tbody>
                    	<tr>
                    		<td width="40" align="center"><? echo $k; ?></td>
                    		<td width="50" align="center">&nbsp;<? echo $colorArray[0][csf('knit_id')]; ?></td>
                    		<td width="110" align="center"><? echo $colorArray[0][csf('dia')]; ?></td>
                    		<td width="170"
                    		align="center"><? echo $colorArray[0][csf('machine_dia')] . "X" . $colorArray[0][csf('machine_gg')]; ?></td>
                    		<td width="110"
                    		align="right"><? echo number_format($colorArray[0][csf('program_qnty')], 2); ?>
                    	&nbsp;</td>
                    	<td><? echo $colorArray[0][csf('remarks')]; ?></td>
                    </tr>
                    <?
                    $tot_prog_qty += $colorArray[0][csf('program_qnty')];
                    ?>
                    <tr>
                    	<td colspan="4" align="right"><strong>Total : </strong></td>
                    	<td align="right"><? echo number_format($tot_prog_qty, 2); ?></td>
                    	<td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            <?
            $returnJob = '';
            $returnPo = '';
            if ($db_type == 0) {
            	$booking_cond = "group_concat(a.booking_no)";
            	$wo_cond = "group_concat(a.ydw_no)";
            	$reqs_no = "group_concat(b.requisition_no)";
            } else if ($db_type == 2) {
            	$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
            	$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
            	$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
            }

            if ($dataArray[0][csf('issue_purpose')] == 8) {
            	$sql_returnJob = sql_select("select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_no) group by a.id");
            } else if ($dataArray[0][csf('issue_purpose')] == 2) {
            	$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$booking_job_no' group by b.job_no");

            	if ($db_type == 0) $poNoCond = "group_concat(id)";
            	else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
            	$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='$booking_job_no' group by job_no_mst");
            } else {
            	$sql_returnJob = sql_select("select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$booking_job_no' group by a.job_no");

            }
            $returnJob = $sql_returnJob[0][csf('job_no')];
						//$returnPo=$sql_po[0][csf('po_break_down_id')];
            $returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
            $returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";
            $returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
            unset($sql_returnJob);


            ?>
            <table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0"
            cellspacing="0" class="rpt_table">
            <thead>
            	<tr>
            		<th colspan="6" align="center">Comments (Booking No:<p> <? echo $returnBooking; ?>) [Booking
            		Job Deatils]</p></th>
            	</tr>
            	<tr>
            		<th>Buyer</th>
            		<th>Job</th>
            		<th>Req. Qty</th>
            		<th>Cuml. Issue Qty</th>
            		<th>Balance Qty</th>
            		<th>Remarks</th>
            	</tr>
            </thead>
            <?

            $all_requ_no = "";
            if ($dataArray[0][csf('issue_purpose')] != 8) {
            	$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 ");


            	$all_requ_no = "'" . implode("','", array_unique(explode(",", $all_booking_sql[0][csf('requisition_no')]))) . "'";
            }
            if ($dataArray[0][csf('issue_purpose')] == 8) {
							$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3", "total_issue_qty");    //
							$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=1", "total_issue_qty");
						} else if ($dataArray[0][csf('issue_purpose')] == 2) {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}

							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and b.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and b.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
						} else {
							if ($all_requ_no != "" || $all_requ_no != 0) {
								$req_cond = "or a.requisition_no in ($all_requ_no)";
								$reqRet_cond = "or b.booking_no in ($all_requ_no)";
							} else {
								$req_cond = "or a.requisition_no in (0)";
								$reqRet_cond = "or b.booking_no in (0)";
							}
							$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");


							$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");
						}
						$cumulative_qty = 0;
						if ($booking_count == 1) {
							?>
							<tbody>
								<tr>
									<td align="center">
										<? echo $buyer_arr[$job_array[$booking_job_no]['buyer']]; ?>
									</td>
									<td align="center">
										<? echo $job_array[$booking_job_no]['job']; ?>
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($returnReqQty < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}
			}
			else if ($dataArray[0][csf('issue_basis')] == 1) {
				?>
				<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0"
				cellspacing="0" class="rpt_table">
				<thead>
					<tr>
						<th colspan="6" align="center">
							Comments <? if ($dataArray[0][csf('issue_purpose')] != 8) {
								if ($dataArray[0][csf('buyer_job_no')] != '') echo "(Booking Job Details)";
							} ?> </th>
						</tr>
						<tr>
							<th>Buyer</th>
							<th>Job</th>
							<th>Req. Qty</th>
							<th>Cuml. Qty</th>
							<th>Balance Qty</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<?

					$returnJob = '';
					$returnPo = '';
					if ($db_type == 0) {
						$booking_cond = "group_concat( distinct a.booking_no)";
						$wo_cond = "group_concat(a.ydw_no)";
						$reqs_no = "group_concat(b.requisition_no)";
					} else if ($db_type == 2) {
						$booking_cond = "listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no)";
						$wo_cond = "listagg(cast(a.ydw_no as varchar2(4000)),',') within group (order by a.ydw_no)";
						$reqs_no = "listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no)";
					}

					if ($dataArray[0][csf('issue_purpose')] == 8) {
						$sql_returnJob = sql_select("select a.id, a.buyer_id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id, a.buyer_id");
					} else if ($dataArray[0][csf('issue_purpose')] == 2) {
						if ($dataArray[0][csf('buyer_job_no')] != '') {
							$sql_returnJob = sql_select("select b.job_no, $wo_cond as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by b.job_no");

							if ($db_type == 0) $poNoCond = "group_concat(id)";
							else if ($db_type == 2) $poNoCond = "listagg(cast(id as varchar2(4000)),',') within group (order by id)";
							$sql_po = sql_select("select job_no_mst as job_no, $poNoCond as po_break_down_id from wo_po_break_down where job_no_mst='" . $dataArray[0][csf('buyer_job_no')] . "' group by job_no_mst");
						} else {
							$sql_returnJob = sql_select("select a.id, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.item_category_id=24 and a.entry_form in(42,114) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no='" . $dataArray[0][csf('booking_no')] . "' group by a.id");
						}
					} else {

						$sql_returnJob = sql_select("select a.job_no, $booking_cond as booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='" . $dataArray[0][csf('buyer_job_no')] . "' group by a.job_no");
					}
					$returnJob = $sql_returnJob[0][csf('job_no')];
					$returnPo = $sql_po[0][csf('po_break_down_id')];
					$returnPoId = $sql_returnJob[0][csf('po_break_down_id')];
					$returnBooking = "'" . implode("','", array_unique(explode(",", $sql_returnJob[0][csf('booking_no')]))) . "'";

					$returnReqQty = $sql_returnJob[0][csf('fabric_qty')];
					unset($sql_returnJob);


					$all_requ_no = "";
					if ($dataArray[0][csf('issue_purpose')] != 8) {

						$all_booking_sql = sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no");

						$all_requ_no = $all_booking_sql[0][csf('requisition_no')];
						unset($all_booking_sql);
					}

					if ($dataArray[0][csf('issue_purpose')] == 8) {
								$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3", "total_issue_qty");    //
								$total_return_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", " inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1", "total_issue_qty");
							} else if ($dataArray[0][csf('issue_purpose')] == 2) {

								if ($dataArray[0][csf('buyer_job_no')] != "") {
									$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and b.booking_no in ($returnBooking) and b.buyer_job_no='" . $dataArray[0][csf('buyer_job_no')] . "'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

									$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and b.booking_no in ($returnBooking) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
								} else {

									$total_issue_qty = return_field_value("sum(a.cons_quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and b.issue_purpose=2", "total_issue_qty");

									$total_return_qty = return_field_value("sum(a.cons_quantity) as total_return_qty", "inv_transaction a, inv_receive_master b", "b.id=a.mst_id and b.booking_no='" . $dataArray[0][csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose=2", "total_return_qty");
								}
							} else {
								if ($dataArray[0][csf('buyer_job_no')] != "") {
									if ($all_requ_no != "" || $all_requ_no != 0) {
										$req_cond = "or a.requisition_no in ($all_requ_no)";
										$reqRet_cond = "or b.booking_no in ($all_requ_no)";
									} else {
										$req_cond = "";
										$reqRet_cond = "";
									}
									$total_issue_qty = 0;

									$total_issue_qty = return_field_value("sum(c.quantity) as total_issue_qty", "inv_transaction a, inv_issue_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id and ( b.booking_no in ($returnBooking) $req_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=2 and a.item_category=1 and b.entry_form=3 and c.entry_form=3 and b.issue_purpose!=2", "total_issue_qty");

									$total_return_qty = return_field_value("sum(c.quantity) as total_return_qty", "inv_transaction a, inv_receive_master b, order_wise_pro_details c", "b.id=a.mst_id and a.id=c.trans_id  and (b.booking_no in ($returnBooking) $reqRet_cond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=4 and a.item_category=1 and b.entry_form=9 and c.entry_form=9 and c.issue_purpose!=2", "total_return_qty");


								}
							}
							?>
							<tbody>
								<tr>
									<td align="center">
										<?
										if ($dataArray[0][csf('issue_purpose')] == 8) echo $buyer_arr[$sql_returnJob[0][csf('buyer_id')]];
										else echo $buyer_arr[$job_array[$dataArray[0][csf('buyer_job_no')]]['buyer']];
										?>
									</td>
									<td align="center">
										<? echo $job_array[$dataArray[0][csf('buyer_job_no')]]['job']; ?>&nbsp;
									</td>
									<td align="center">
										<? echo number_format($returnReqQty, 3); ?>
									</td>
									<td align="center">
										<? $cumulative_qty = 0;
										$cumulative_qty = $total_issue_qty - $total_return_qty;
										echo number_format($cumulative_qty, 3); ?>
									</td>
									<td align="center">
										<? $balance_qty = $returnReqQty - $cumulative_qty;
										echo number_format($balance_qty, 3); ?>
									</td>
									<td align="center">
										<? if ($returnReqQty > $cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')] < $cumulative_qty) echo "Over"; else echo ""; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?
					}
				}
				?>
				<br>
				<?
				echo signature_table(49, $data[0], "1030px");
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
				$("#barcode_img_id").html('11');
				value = {code: value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');
		</script>
		<?
		exit();
	}


	// ==========================================
	if ($action == "yarn_issue_print6")
	{
		extract($_REQUEST);
		echo load_html_head_contents("Yarn Issue Challan Print2", "../../", 1, 1, '', '', '');
		$data = explode('*', $data);

		$print_with_vat = $data[7];
		$organicyesno = $data[8];


		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
		$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');


		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$sql = " select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id='$data[5]'";
		//echo $sql;die;
		$dataArray = sql_select($sql);

		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

		?>
		<div style="width:1000px;">
			<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td colspan="6" align="center" style="font-size:18px">
						<strong><? echo $company_library[$data[0]]; ?></strong></td>
						<td style="font-size:xx-large; font-style:italic;" align="right">
							<div style="color:#FF0000"><? if ($data[4] == 1) echo "<b>Approved</b>"; ?></div>
						</td>
					</tr>
					<tr class="form_caption">

						<?
						$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
						?>
						<td align="left" width="50">
							<?
							foreach ($data_array as $img_row) {
								if ($data[5] != 1) {
									?>
									<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								} else {
									?>
									<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
									align="middle"/>
									<?
								}
							}
							?>
						</td>
						<td colspan="4" align="center"><? echo show_company($data[0], '', '');?></td>
						<td>
							<?php
							if($organicyesno==1)
							{
								?>
								<div style="border: 2px solid #000; padding: 5px; color: #000;">ORGANIC</div>
								<?
							}else{
								echo "&nbsp;";
							}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" style="font-size:16px"><strong><u>Yarn Delivery Challan/Gate Pass</u></strong></center>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="160"><strong>Issue No:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
						<td width="120"><strong>Booking:</strong></td>
						<td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
						<td width="125"><strong>Knitting Source:</strong></td>
						<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Issue To:</strong></td>
						<?
						if ($dataArray[0][csf('knit_dye_source')] == 3) {
							$supp_add = $dataArray[0][csf('knit_dye_company')];
							$nameArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
							foreach ($nameArray as $result) {
								$address = "";
							if ($result != "") $address = $result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
						}
						unset($nameArray);
					}

					$loan_party_add = $dataArray[0][csf('loan_party')];
					$loanPartyArray = sql_select("select address_1,web_site,email,country_id from lib_supplier where id=$loan_party_add");
					foreach ($loanPartyArray as $result) {
						$addressParty = "";
						if ($result != "") $addressParty = $result['address'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					unset($loanPartyArray);
					?>
					<td>
						<?
						if ($dataArray[0][csf('issue_purpose')] == 3) {
							echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
						} else if ($dataArray[0][csf('issue_purpose')] == 5) {
							echo $supplier_arr[$dataArray[0][csf('loan_party')]] . ' : Address :- ' . $addressParty;
						} else {
							if ($dataArray[0][csf('knit_dye_source')] == 1)
								echo $company_library[$dataArray[0][csf('knit_dye_company')]];
							else
								echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]] . ' : Address :- ' . $address;
						}
						?>
					</td>

					<td><strong>Issue Purpose:</strong></td>
                    <td width="175px"><? if ($dataArray[0][csf('issue_purpose')] == 3) echo "Knitting Purpose"; else echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];//
                    ?>
                </td>
                <td width="100"><strong>Issue Date:</strong></td>
                <td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            </tr>
            <tr>
            	<td colspan="3"><strong>Demand No.:</strong></td>
            	<td><? //echo $dataArray[0][csf('booking_id')];?></td>
            	<td><strong>Location:</strong></td>
            	<td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>

            <tr height="25">
            	<td><strong>Remarks :</strong></td>
            	<td><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
            	<td colspan="3"><strong>Do No:</strong></td>
            	<td>&nbsp;</td>
            </tr>
            <?
            if ($print_with_vat == 1) {
            	?>
            	<tr>
            		<td>
            			<p>
            				<?
            				$vat_no = return_field_value("vat_number", "lib_company", "id=" . $data[0], "vat_number");
            				echo $vat_no;
            				?></p>
            			</td>
            			<td><strong>Bar Code:</strong></td>
            			<td colspan="3" id="barcode_img_id"></td>
            		</tr>
            		<?
            	} else {
            		?>
            		<tr>
            			<td><strong>Bar Code:</strong></td>
            			<td colspan="3" id="barcode_img_id"></td>
            		</tr>
            		<?
            	}
            	?>
            </table>
            <br>
            <div style="width:100%;">
            	<div style="clear:both;">
            		<table style="margin-right:-40px;" cellspacing="0" width="1000" border="1" rules="all"
            		class="rpt_table">
            		<thead bgcolor="#dddddd" style="font-size:13px">
            			<th width="20">SL</th>
            			<th width="45">Req. No</th>
            			<th width="50">Lot No</th>
            			<th width="65">Y. Type</th>
            			<th width="110">Item Details</th>

            			<th width="65">Dye. Color</th>
            			<th width="60">Supp.</th>
            			<th width="60"><b>Issue Qty(kg)</b></th>
            			<th width="60">Rtnbl Qty(kg)</th>
            			<th width="80">Bag & Cone</th>
            			<th width="80">Store</th>
            			<th width="100">Buyer & Job</th>
            			<th width="120">Order & Style</th>
            			<th>Remarks</th>
            		</thead>
            		<?
            		$wopi_library = return_library_array("select id,pi_number from com_pi_master_details", "id", "pi_number");

            		$cond = "";
            		if ($data[5] != "") $cond .= " and c.id='$data[5]'";

            		$po_array = array();
            		$job_array = array();
            		$costing_sql = sql_select("select a.job_no, b.file_no,b.grouping,a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0]");
            		foreach ($costing_sql as $row) {
            			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
            			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
            			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
            			$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
            			$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
            			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
            			$job_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
            			$job_array[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
            		}
            		unset($costing_sql);

            		$job_no_array = array();
            		$jobData = sql_select("select id, job_no, style_ref_no, within_group, buyer_id from fabric_sales_order_mst");
            		foreach ($jobData as $row) {
            			$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
            			$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
            			$job_no_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
            			$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
            		}
						//var_dump($po_array);
            		$po_id_req_array = array();
            		$is_sales_array = array();
            		if ($db_type == 0) {
            			$poID = " select c.requisition_no, a.is_sales, group_concat(distinct(a.po_id)) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
            		} else {
            			$poID = "select c.requisition_no, a.is_sales, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as poid from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id group by c.requisition_no, a.is_sales ";
            		}
            		$poID_sql_result = sql_select($poID);
            		foreach ($poID_sql_result as $row) {
            			$po_id_req_array[$row[csf('requisition_no')]] = $row[csf('poid')];
            			$is_sales_array[$row[csf('requisition_no')]] = $row[csf('is_sales')];
            		}
            		unset($poID_sql_result);

            		$i = 1;
            		if ($db_type == 0) {
            			$sql_result = sql_select("select a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, group_concat(d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
            		} else {
            			$sql_result = sql_select("select a.id, a.lot,a.color,a.yarn_type, a.product_name_details, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st, a.detarmination_id, b.dyeing_color_id, b.id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.supplier_id, LISTAGG(d.po_breakdown_id, ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_id, sum(d.returnable_qnty) as returnable_qnty_pro from product_details_master a, inv_issue_master c, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id where c.id=b.mst_id and b.prod_id=a.id and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and b.status_active=1 and b.is_deleted=0 $cond group by a.id, a.lot,a.color, a.yarn_type, a.product_name_details, a.detarmination_id, a.supplier_id, b.id, b.dyeing_color_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, c.issue_basis, c.booking_no, c.buyer_id, c.buyer_job_no, a.yarn_count_id,a.yarn_comp_percent1st,a.yarn_comp_type1st");
            		}
            		$all_req_no = '';
            		$all_prod_id = '';
            		$order_qnty_val_sum=$order_amount_val_sum=$no_of_bags_val_sum=0;
            		$tot_return_qty = $tot_bag = $tot_cone = $tot_w_bag = $tot_w_cone = 0;
            		foreach ($sql_result as $row) {
            			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
            			if ($row[csf('requisition_no')] != '') {
            				if ($all_req_no == '') $all_req_no = $row[csf('requisition_no')]; else $all_req_no .= ',' . $row[csf('requisition_no')];
            				if ($all_prod_id == '') $all_prod_id = $row[csf('po_id')]; else $all_prod_id .= ',' . $row[csf('po_id')];
            			}
            			$order_qnty_val = $row[csf('cons_quantity')];
            			$order_qnty_val_sum += $order_qnty_val;

            			$order_amount_val = $row[csf('order_amount')];
            			$order_amount_val_sum += $order_amount_val;

            			$no_of_bags_val = $row[csf('no_of_bags')];
            			$no_of_bags_val_sum += $no_of_bags_val;

            			$po_id = explode(",", $row[csf('po_id')]);
            			$po_no = '';
            			$style_ref = '';
            			$job_no = '';
            			$buyer = '';
							//$data=explode('*',$data);
            			$productdtls = $row[csf('product_name_details')];
            			$productArr = explode(' ', $productdtls);

            			$proddtls = '';
            			if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '' && $productArr[5] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . '%, ' . $productArr[3] . ' ' . $productArr[4] . "%, " . $productArr[5] . ", " . $productArr[6];
            			} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '' && $productArr[4] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3] . ', ' . $productArr[4];
            			} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '' && $productArr[3] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ' . $productArr[3];
            			} else if ($productArr[0] != '' && $productArr[1] != '' && $productArr[2] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1] . ' ' . $productArr[2] . ' %, ';
            			} else if ($productArr[0] != '' && $productArr[1] != '') {
            				$proddtls = $productArr[0] . ', ' . $productArr[1];
            			} else if ($productArr[0] != '') {
            				$proddtls = $productArr[0];
            			} else {
            				$proddtls = '';
            			}

            			$internal_ref = '';
            			$file_no = '';
            			if ($row[csf('issue_basis')] == 1 || $row[csf('issue_basis')] == 2) {
            				foreach ($po_id as $val) {
            					if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
            					if ($job_no == '') $job_no = $po_array[$val]['job_no'];
            					if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
            					if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

            					if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
            					if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
            				}
            				$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
            				$job_file = array_unique(explode(",", rtrim($file_no, ", ")));
            				$ref_cond = '';
            				foreach ($job_ref as $ref) {
									//$ref_cond.=", ".$ref;
            					if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
            				}
            				$file_con = '';
            				foreach ($job_file as $file) {
            					if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
            				}

            				$buyer_job = '';
            				if ($row[csf('issue_basis')] == 1) {
            					if ($dataArray[0][csf('issue_purpose')] == 8) {
            						$buyer_job = $buyer_arr[$row[csf('buyer_id')]];
            					} else {
            						$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']];
            						if ($row[csf('buyer_job_no')] != "") {
            							$buyer_job = $buyer_arr[$job_array[$row[csf('buyer_job_no')]]['buyer']] . ' & ' . $row[csf('buyer_job_no')];
            						}
            					}
            				} else if ($row[csf('issue_basis')] == 2) {
            					$buyer_job = '';
            				}
            			} else if ($row[csf('issue_basis')] == 3) {
								//$ex_data = array_unique(explode(",",$po_id_req_array[$row[csf('requisition_no')]]));
								//print_r($ex_data);

            				if ($is_sales_array[$row[csf('requisition_no')]] == 1) {
            					$buyer_job = '';
            					foreach (array_unique($po_id) as $val) {
            						if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
            						if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];

            						if ($buyer_job == '') {
            							if ($job_no_array[$val]['within_group'] == 1) {
            								$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
            							} else {
            								$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
            							}
            						}
            					}
            				} else {
            					foreach ($po_id as $val) {
            						if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= ", " . $po_array[$val]['no'];
            						if ($job_no == '') $job_no = $po_array[$val]['job_no'];
            						if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
            						if ($buyer == '') $buyer_name = $po_array[$val]['buyer_name'];

            						if ($internal_ref == '') $internal_ref = $po_array[$val]['grouping']; else $internal_ref .= "," . $po_array[$val]['grouping'];
            						if ($file_no == '') $file_no = $po_array[$val]['file_no']; else $file_no .= "," . $po_array[$val]['file_no'];
            					}

            					$job_ref = array_unique(explode(",", rtrim($internal_ref, ", ")));
            					$job_file = array_unique(explode(",", rtrim($file_no, ", ")));

            					$ref_cond = '';
            					foreach ($job_ref as $ref) {
										//$ref_cond.=", ".$ref;
            						if ($ref_cond == '') $ref_cond = $ref; else $ref_cond .= ", " . $ref;
            					}

            					$file_con = '';
            					foreach ($job_file as $file) {
            						if ($file_con == '') $file_cond = $file; else $file_cond .= ", " . $file;
            					}
            					if ($job_no != '') {
            						$buyer_job = $buyer_arr[$buyer_name] . ' & ' . $job_no;
            					} else {
            						$buyer_job = $buyer_arr[$buyer_name];
            					}
            				}
            			} else {
            				foreach (array_unique($po_id) as $val) {
            					if ($po_no == '') $po_no = $job_no_array[$val]['job_no']; else $po_no .= ", " . $job_no_array[$val]['job_no'];
            					if ($style_ref == '') $style_ref = $job_no_array[$val]['style_ref'];
            					if ($buyer_job == '') {
            						if ($job_no_array[$val]['within_group'] == 1) {
            							$buyer_job = $company_library[$job_no_array[$val]['buyer_id']];
            						} else {
            							$buyer_job = $buyer_arr[$job_no_array[$val]['buyer_id']];
            						}
            					}
            				}
            			}

            			$no_bag_cone = "";
            			$wt_bag_cone = "";
            			if ($row[csf('cone_per_bag')] == "" || $row[csf('cone_per_bag')] == 0) $no_bag_cone = 'N:' . $no_of_bags_val; else $no_bag_cone = 'N:' . $no_of_bags_val . ' & ' . $row[csf('cone_per_bag')];

            				if ($row[csf('weight_per_cone')] == "" || $row[csf('weight_per_cone')] == 0) $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')]; else $wt_bag_cone = 'W:' . $row[csf('weight_per_bag')] . ' & ' . $row[csf('weight_per_cone')];
            					?>
            					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
            						<td width="20"><? echo $i; ?></td>
            						<td width="45">
            							<div style="word-wrap:break-word; width:45px"><? if ($row[csf('requisition_no')] != 0) echo $row[csf('requisition_no')]; else echo '&nbsp;'; ?></div>
            						</td>
            						<td width="50">
            							<div style="word-wrap:break-word; width:50px"><? echo $row[csf('lot')]; ?></div>
            						</td>
            						<td width="65">
            							<div style="word-wrap:break-word; width:65px"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></div>
            						</td>
            						<td width="110">
            							<div style="word-wrap:break-word; width:110px">
            								<?
                                        //echo $proddtls;
            								echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_name_arr[$row[csf('color')]];
            								?>
            							</div>
            						</td>


            						<td width="65">
            							<div style="word-wrap:break-word; width:65px"><? echo $color_name_arr[$row[csf('dyeing_color_id')]]; ?>
            							&nbsp;</div>
            						</td>

            						<td width="60">
            							<div style="word-wrap:break-word; width:60px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div>
            						</td>

            						<td align="right"
            						width="60"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>
            						<td align="right"
            						width="60"><? echo number_format($row[csf('returnable_qnty')], 2, '.', ''); ?></td>
            						<td width="80">
            							<div style="word-wrap:break-word; width:80px"><? echo $no_bag_cone . '<br>' . $wt_bag_cone ?>
            							&nbsp;</div>
            						</td>
            						<td width="80">
            							<div style="word-wrap:break-word; width:80px"><? echo $store_arr[$row[csf('store_id')]]; ?></div>
            						</td>
            						<td width="100">
            							<div style="word-wrap:break-word; width:100px"><? echo $buyer_job; ?></div>
            						</td>
            						<td width="120">
            							<div style="word-wrap:break-word; width:120px"><? echo $po_no;
            							if ($style_ref != "") echo ' & ' . $style_ref; else echo '&nbsp;'; ?></div>
            						</td>
            						<td width="150">&nbsp;</td>
            					</tr>
            					<?
            					$uom_unit = "Kg";
            					$uom_gm = "Grams";
            					$tot_return_qty += $row[csf('returnable_qnty')];
            					$tot_bag += $no_of_bags_val;
            					$tot_cone += $row[csf('cone_per_bag')];
            					$tot_w_bag += $row[csf('weight_per_bag')];
            					$tot_w_cone += $row[csf('weight_per_cone')];
            					$req_no = $row[csf('requisition_no')];
            					$i++;
            				}
            				?>
            				<tr bgcolor="#CCCCCC" style="font-size:13px">
            					<td align="right" colspan="7"><b>Total</b></td>
            					<td align="right">
            						<b><? echo $format_total_amount = number_format($order_qnty_val_sum, 3, '.', ''); ?></b>
            					</td>
            					<td align="right"><? echo number_format($tot_return_qty, 2, '.', ''); ?></td>
            					<td><? echo 'N:' . number_format($tot_bag, 2);
            					if ($tot_cone != "") echo ' & ' . number_format($tot_cone, 2);
            					echo '<br>W:' . number_format($tot_w_bag, 2);
            					if ($tot_w_cone != "") echo ' & ' . number_format($tot_w_cone, 2); ?></td>
            					<td align="right" colspan="4">&nbsp;</td>
            				</tr>
            				<tr>
            					<td colspan="14" align="left"><b>In
            						Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
            					</tr>
            				</table>

            				<br>
            				<?
            				echo signature_table(49, $data[0], "1030px");
            				?>

            			</div>
            		</div>

            		<script type="text/javascript" src="../../js/jquery.js"></script>
            		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
            		<script>
            			function generateBarcode(valuess) {
                    var value = valuess;//$("#barcodeValue").val();
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
                    $("#barcode_img_id").html('11');
                    value = {code: value, rect: false};
                    $("#barcode_img_id").show().barcode(value, btype, settings);
                }
                generateBarcode('<? echo $data[1]; ?>');
            </script>
            <?
            exit();
        }


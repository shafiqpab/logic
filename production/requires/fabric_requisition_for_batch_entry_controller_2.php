<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
$user_id=$_SESSION['logic_erp']['user_id'];
include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$color_range_arr=$color_range;
$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

$cbuyer_arr = return_library_array("select buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", 'id', 'short_name');

$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');




//==================================

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=55 and is_deleted=0 and status_active=1");
	echo trim($print_report_format);
	exit();

}




if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

if ($action == "po_popup") {
	echo load_html_head_contents("Fabric Info", "../../", 1, '', '', '', '');
	extract($_REQUEST);
	?>
	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][123]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function () {
			set_field_level_access( <? echo $company_id; ?> );
		}
		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function check_all_data(is_checked) {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				if (is_checked == true) {
					document.getElementById('search' + i).style.backgroundColor = 'yellow';
				}
				else {
					document.getElementById('search' + i).style.backgroundColor = '#FFFFCC';
				}
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');
		}

		function reset_hide_field() {
			$('#hidden_data').val('');
		}

		function set_receive_basis(recieve_basis) {
			$('#txt_search_common').val('');
			$('#txt_search_common').removeAttr('disabled', 'disabled');

			if (recieve_basis == 1) {
				$('#td_caption').text('Enter Program No');
			}
			else if (recieve_basis == 2) {
				$('#td_caption').text('Enter Booking No');
			}
			else if (recieve_basis == 3) {
				$('#td_caption').text('Enter PI No');
			}
			else {
				$('#td_caption').text('');
				$('#txt_search_common').attr('disabled', 'disabled');
			}
		}

		function set_search_by(type) {
			$('#txt_search_val').val('');

			if (type == 1) {
				$('#td_search').text('Enter Reff NO');
			}
			else if (type == 2) {
				$('#td_search').text('Enter Booking No');
			}
			else if (type == 3) {
				$('#td_search').text('Enter Job NO');
			}
			else if (type == 4) {
				$('#td_search').text('Enter File No');
			}
			else if (type == 6) {
				$('#td_search').text('Enter Sales Order No');
			}
			else if (type == 7) {
				$('#td_search').text('Enter Style No');
			}
			else {
				$('#td_search').text('Enter Order No');
			}
		}

		function fnc_close() {
			var hidden_data = '';

			$("#tbl_list_search").find('tr:not(:first)').each(function () {
				var tr_id = $(this).attr("id");
				var bgColor = document.getElementById(tr_id).style.backgroundColor;
				if (bgColor == 'yellow') {
					var buyerId = $(this).find('input[name="buyerId[]"]').val();
					var cbuyerId = $(this).find('input[name="cbuyerId[]"]').val();
					var cbuyerName = $(this).find('input[name="cbuyerName[]"]').val();
					var poId = $(this).find('input[name="poId[]"]').val();
					var poNo = $(this).find('input[name="poNo[]"]').val();
					var bookingQty = $(this).find('input[name="bookingQty[]"]').val();
					var totReqnQty = $(this).find('input[name="totReqnQty[]"]').val();
					var balance = $(this).find('input[name="balanceQty[]"]').val();
					var colorId = $(this).find('input[name="colorId[]"]').val();
					var colorTypeId = $(this).find('input[name="colorTypeId[]"]').val();
					var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();

					var receiveBasisId = $(this).find('input[name="receiveBasisId[]"]').val();
					var receiveBasis = $(this).find('input[name="receiveBasis[]"]').val();
					var isSales = $(this).find('input[name="isSales[]"]').val() * 1;
					var styleRef = $(this).find('input[name="styleRef[]"]').val();
					// var isShort = $(this).find('input[name="isShort[]"]').val();

					var nullData = '';

					var data = '';
					$(this).find('td:not(:first-child)').each(function () {

						var td_class = $(this).attr('class');
						//console.log(td_class);
						if(td_class == 'not_taken')
						{
							//data += "**" + $(this).text();
							// not taking this .
						}
						else{
							data += "**" + $(this).text();
						}

					});

					if (hidden_data == "") {
						// hidden_data = buyerId + "**" + poId + "**" + poNo + "**" + nullData + "**" + bodyPartId + "**" + colorId + "**" + bookingQty + "**" + receiveBasisId + "**" + receiveBasis + "**" + colorTypeId + "**" + totReqnQty + "**" + balance + data + "**" + isSales + "**" + cbuyerId + "**" + cbuyerName + "**" + styleRef + "**" + isShort;
						hidden_data = buyerId + "**" + poId + "**" + poNo + "**" + nullData + "**" + bodyPartId + "**" + colorId + "**" + bookingQty + "**" + receiveBasisId + "**" + receiveBasis + "**" + colorTypeId + "**" + totReqnQty + "**" + balance + data + "**" + isSales + "**" + cbuyerId + "**" + cbuyerName + "**" + styleRef ;
					}
					else {
						// hidden_data += "####" + buyerId + "**" + poId + "**" + poNo + "**" + nullData + "**" + bodyPartId + "**" + colorId + "**" + bookingQty + "**" + receiveBasisId + "**" + receiveBasis + "**" + colorTypeId + "**" + totReqnQty + "**" + balance + data + "**" + isSales + "**" + cbuyerId + "**" + cbuyerName + "**" + styleRef + "**" + isShort;
						hidden_data += "####" + buyerId + "**" + poId + "**" + poNo + "**" + nullData + "**" + bodyPartId + "**" + colorId + "**" + bookingQty + "**" + receiveBasisId + "**" + receiveBasis + "**" + colorTypeId + "**" + totReqnQty + "**" + balance + data + "**" + isSales + "**" + cbuyerId + "**" + cbuyerName + "**" + styleRef ;
					}
				}
			});
			// alert(hidden_data);return;
			//console.log(hidden_data);
			$('#hidden_data').val(hidden_data);
			parent.emailwindow.hide();
		}
		function field_visible(thisValue) {
			$("#chkIsSales").prop("checked", false);
			if (thisValue == 2 || thisValue == 1) 
			{
				$("#is_sales_booking").css("display", "block");
			} 
			else 
			{
				$("#is_sales_booking").css("display", "none");
			}
		}

		function fnc_show()
		{
			var cbo_buyer_name = $('#cbo_buyer_name').val();
			var txt_search_val = $('#txt_search_val').val().trim();
			if(cbo_buyer_name==0 && txt_search_val=="")
			{
				alert("please select buyer")
				return;
			}
			show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_val').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('chkIsSales').checked, 'create_fabric_search_list_view', 'search_div', 'fabric_requisition_for_batch_entry_controller_2', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
		}
	</script>

</head>

<body>
	<div align="center" style="width:1235px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:1030px; margin-left:2px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="500" class="rpt_table">
					<thead>
						<th>Buyer</th>
						<th>Job Year</th>
						<th>Search Type</th>
						<th id="td_search"><?php echo ($field_label_arr[1]['cbo_search_by']['defalt_value'] == 6) ? "Enter Sales Order No" : "Enter Reff No" ?></th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", $data[0]);
							?>
						</td>

						<td>
							<?
							echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
							?>
						</td>
						<td>
							<?
							//$search_by_arr=array(1=>"Job",2=>"Order",3=>"File",4=>"Ref. No");
							$search_by_arr = array(1 => "Reff No", 2 => "Booking No", 3 => "Job No", 4 => "File No", 5 => "Order No", 6 => "Sales Order No", 7 => "Style No");
							echo create_drop_down("cbo_search_by", 90, $search_by_arr, "", 0, "--Select--", "", "set_search_by(this.value);field_visible(this.value);", 0);
							?>
						</td>


						<td>
							<input type="text" name="txt_search_val" id="txt_search_val" style="width:100px"
							class="text_boxes"/>
							<div id="is_sales_booking" style="display: none;"><input type="checkbox" name="chkIsSales" id="chkIsSales"/> <label
								for="chkIsSales">For sales order </label></div>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="fnc_show()"
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
	<script type="text/javascript">
		var search_val=$('#cbo_search_by').val();
		set_search_by(search_val);
		field_visible(search_val);
	</script>
	</html>
	<?
}

if ($action == "create_fabric_search_list_view") {
	$data = explode("_", $data);

	$company_id        = $data[0];
	$buyer_id          = $data[1];
	$search_type       = trim($data[2]);
	$search_val        = trim($data[3]);
	$cbo_year          = trim($data[4]);
	$is_sales_booking  = $data[5];
	$sales_table_sql   = "";
	$sales_join_sql    = "";
	// echo $search_type.'='.$is_sales_booking.'*'.$search_val;die;

	if ($buyer_id == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyer_id_cond2 = " and f.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			}else{
				$buyer_id_cond = "";
				$buyer_id_cond2 = "";
			}
		} else {
			$buyer_id_cond = "";
			$buyer_id_cond2 = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
		$buyer_id_cond2 = " and f.buyer_id=$buyer_id";
	}

	if (trim($cbo_year) != 0) {
		if ($db_type == 0)
		{
			$year_cond = " and YEAR(e.insert_date)=$cbo_year";
			$year_cond_no_order = " and YEAR(f.insert_date)=$cbo_year";
		}
		else if ($db_type == 2)
		{
			$year_cond = " and to_char(e.insert_date,'YYYY')=$cbo_year";
			$year_cond_no_order = " and to_char(f.insert_date,'YYYY')=$cbo_year";
		}
		else
		{
			$year_cond = "";
			$year_cond_no_order = "";
		}
	}
	else
	{
		$year_cond = "";
		$year_cond_no_order = "";
	}

	$search_cond = ""; $nonOrdBooking_cond="";$samp_ref_cond2="";
	//echo $search_type.'DDXXX';
	if ($search_type == 1 && $is_sales_booking == "false") {
		if ($search_val != "")
		{
			$search_cond = "and d.grouping like '%" . $search_val . "%'";
			$samp_ref_cond2 = "and f.grouping like '%" . $search_val . "%'";
		}
	} 
	else if ($search_type == 2 && $is_sales_booking == "false")
	{
		if ($search_val != "") $search_cond = "and a.booking_no like '%" . $search_val . "'";
		if ($search_val != "") $nonOrdBooking_cond = "and f.booking_no like '%" . $search_val . "'";
	}
	else if ($search_type == 3)
	{
		if ($search_val != "") $search_cond = "and b.job_no like '%" . $search_val . "'";
	} else if ($search_type == 4) {
		if ($search_val != "") $search_cond = "and d.file_no like '%" . $search_val . "%'";
	} else if ($search_type == 5) {
		if ($search_val != "") $search_cond = "and d.po_number like '%" . $search_val . "%'";
	} else if ($search_type == 6)
	{
		if (trim($cbo_year) != 0)
		{
			if ($db_type == 0) $year_cond = " and YEAR(a.insert_date)=$cbo_year";
			else if ($db_type == 2) $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond = "";
		}
		else $year_cond = "";
		if($is_sales_booking == "true")
		{
			$search_cond = ($search_val != "") ? "and a.sales_booking_no like '%" . $search_val . "%'" : "";
		}
		else
		{
			$search_cond = ($search_val != "") ? "and a.job_no_prefix_num like '%" . $search_val . "%'" : "";
		}
	} 
	else if ($search_type == 7) {
		if ($search_val != "") $search_cond = "and e.style_ref_no like '%" . $search_val . "%'";
	}
	else if ($search_type == 1 && $is_sales_booking == "true") // int ref
	{
		$year_cond="";
		if (trim($cbo_year) != 0)
		{
			if ($db_type == 0) $year_cond = " and YEAR(a.insert_date)=$cbo_year";
			else if ($db_type == 2) $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond = "";
		}
		else $year_cond = "";

		$sales_job_cond="";
		if ($search_val != "")
		{			
			$po_sql="SELECT a.grouping, b.booking_no, b.booking_mst_id 
			from wo_po_break_down a, wo_booking_dtls b 
			where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and a.grouping ='$search_val' and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0";
			//echo $po_sql;die;
			$po_sql_result=sql_select($po_sql);
			$refBooking_cond="";
			foreach ($po_sql_result as $key => $row) 
			{
				$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
			}
			$sales_job_cond=" and a.booking_id in(".implode(",",$bookingNo_arr).") ";
		}
	}
	else if ($search_type == 2 && $is_sales_booking == "true") // FSO booking
	{
		$year_cond="";
		if (trim($cbo_year) != 0)
		{
			if ($db_type == 0) $year_cond = " and YEAR(a.insert_date)=$cbo_year";
			else if ($db_type == 2) $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond = "";
		}
		else $year_cond = "";
		$search_cond = ($search_val != "") ? "and a.sales_booking_no like '%" . $search_val . "%'" : "";
	}
	// echo $sales_job_cond;die;

	$lib_deterData = sql_select("select a.id,a.construction,b.copmposition_id,b.percent,a.color_range_id from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($lib_deterData as $row) {
		$determin_array[$row[csf('id')]][1]= $row[csf('construction')];
		$determin_array[$row[csf('id')]][2]= $composition[$row[csf('copmposition_id')]].' '.$row[csf('percent')].'%';
	}

	$reqn_qnty_array = array();
	$reqnData = sql_select("select reqn_qty,color_type_id,color_id,program_booking_pi_no,buyer_id, body_part_id,gsm_weight, dia_width, construction, composition, grouping,file_no from pro_fab_reqn_for_batch_dtls where ENTRY_FORM=123 and status_active=1 and is_deleted=0");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
		//echo $key;
	}
	if ($db_type == 2)
	{
		$order_group_concat = " listagg(cast(c.po_break_down_id as varchar2(4000)),',') within group (order by c.po_break_down_id) as po_break_down_id, listagg(cast(d.po_number as varchar2(4000)),',') within group (order by d.po_number) as po_number, listagg(cast(c.id as varchar2(4000)),',') within group (order by c.id) as id";
	}
	else
	{
		$order_group_concat = "group_concat(c.po_break_down_id) as po_break_down_id, group_concat(d.po_number) as po_number, group_concat(c.id) as id";
	}
	$con = connect();
	$r_id2=execute_query("delete from tmp_booking_no where userid=$user_id and type=1");
	oci_commit($con);

	if ( ($search_type == 6 || $is_sales_booking == "true") || ($search_type == 1 && $is_sales_booking == "true") ) // Sales order
	{
		if($buyer_id>0)
		{
			$buyer_cond = " and ( (a.buyer_id=$buyer_id and a.within_group=2) or (a.po_buyer=$buyer_id and a.within_group=1) )";
		}

		$sql = "SELECT a.id as fso_id,a.job_no,a.sales_booking_no booking_no,a.buyer_id,a.po_buyer,a.within_group, a.booking_without_order, a.style_ref_no, b.body_part_id, b.color_type_id, b.gsm_weight, b.fabric_desc, b.dia dia_width, b.color_id as color_number_id, sum(b.grey_qty) as grey_fab_qnty, sum(b.adjust_grey_qnty) as adjust_grey_fab_qnty,b.determination_id febric_description_id, a.customer_buyer , c.is_short , a.booking_entry_form , a.booking_type, c.booking_type
		from fabric_sales_order_mst a left join wo_booking_mst c on a.booking_id = c.id and a.within_group=1 and a.booking_without_order!=1, fabric_sales_order_dtls b
		where a.id = b.mst_id and a.company_id=$company_id $year_cond $search_cond $buyer_cond $sales_job_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.job_no,a.sales_booking_no,a.buyer_id,a.po_buyer,a.within_group, a.booking_without_order, a.style_ref_no,b.body_part_id, b.color_type_id, b.gsm_weight, b.fabric_desc, b.dia, b.color_id,b.determination_id, a.customer_buyer , c.is_short , a.booking_entry_form , a.booking_type, c.booking_type";
		// echo $sql;die;
		$fso_data_array=sql_select($sql);
		$sales_booking_arr=array();
		foreach ($fso_data_array as $key => $row)
		{
			if($row[csf("within_group")]==1 && $row[csf("booking_without_order")] !=1){
				$sales_booking_arr[$row[csf('booking_no')]] = "'".$row[csf('booking_no')]."'";
			}
		}

		if(!empty($sales_booking_arr))
		{
			foreach ($sales_booking_arr as $val)
			{
				$r_id2=execute_query("insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 1, ".$val.")");
				if($r_id2)
				{
					$r_id2=1;
				}
				else
				{
					echo "insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 1, ".$val.")";
					$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id ");
					oci_rollback($con);
					die;
				}
				
			}

			$booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, tmp_booking_no d
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) and a.booking_no=d.booking_no and d.userid=$user_id and d.type=1
			group by a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping");

            foreach ($booking_details as $po_row)
            {
				$po_arr[$po_row[csf("booking_no")]]["buyer_id"]          = $po_row[csf("buyer_id")];
				$po_arr[$po_row[csf("booking_no")]]["job_no"]            = $po_row[csf("job_no")];
				$po_arr[$po_row[csf("booking_no")]]["int_ref"]           = $po_row[csf("ref_no")];
            }
		}

		if (!empty($sales_booking_arr))
		{
			$booking_cond = " and a.booking_no in (".implode(",",array_unique($sales_booking_arr)).")";
			$booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond
			group by a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping");

            foreach ($booking_details as $po_row)
            {
				$po_arr[$po_row[csf("booking_no")]]["buyer_id"]          = $po_row[csf("buyer_id")];
				$po_arr[$po_row[csf("booking_no")]]["job_no"]            = $po_row[csf("job_no")];
				$po_arr[$po_row[csf("booking_no")]]["int_ref"]           = $po_row[csf("ref_no")];
            }
		}
	}
	else // with order and non order
	{
		$sql = "SELECT a.booking_no, a.buyer_id,b.job_no, b.body_part_id, b.color_type_id, b.gsm_weight,b.lib_yarn_count_deter_id as febric_description_id, b.construction, b.composition,b.color_size_sensitive, c.dia_width, c.fabric_color_id as color_number_id, d.file_no, d.grouping, e.style_ref_no, (c.grey_fab_qnty) as grey_fab_qnty, 0 as adjust_grey_fab_qnty,c.po_break_down_id,d.po_number,c.id, a.is_short
		FROM wo_booking_mst a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c, wo_po_break_down d, wo_po_details_master e
		WHERE b.company_id=$company_id $buyer_id_cond $year_cond $search_cond and a.job_no=b.job_no and b.id=c.pre_cost_fabric_cost_dtls_id and a.booking_no=c.booking_no and d.id=c.po_break_down_id and a.status_active=1 and a.job_no=e.job_no and a.booking_type in(1,4)  and c.fabric_color_id !=0 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";// with order

		//and c.dia_width != '0'

		//GROUP BY a.booking_no, a.buyer_id,b.job_no, b.body_part_id,b.lib_yarn_count_deter_id, b.color_type_id, b.gsm_weight, c.dia_width, b.construction, b.composition, c.fabric_color_id, b.color_size_sensitive, d.file_no, d.grouping, e.style_ref_no

		if (($search_type == 2 || $search_type == 1) && $is_sales_booking == 'false') // non order
		{
			//echo $search_type.'x'.$is_sales_booking;
		   	$sql .=" UNION ALL
		  	SELECT f.booking_no,f.buyer_id, null as job_no, g.body_part as body_part_id, g.color_type_id, g.gsm_weight,g.lib_yarn_count_deter_id as febric_description_id, g.construction, g.composition, null as color_size_sensitive, g.dia as dia_width, g.fabric_color as color_number_id,  null as file_no, f.grouping  as grouping, null as style_ref_no, (g.grey_fabric) as grey_fab_qnty, 0 as adjust_grey_fab_qnty, null as po_break_down_id, null as po_number, g.id, f.is_short
		  	from wo_non_ord_samp_booking_mst f, wo_non_ord_samp_booking_dtls g where f.booking_no = g.booking_no and ( g.fabric_source = 1 or f.fabric_source = 1) and f.booking_type in (1,4) and f.company_id =$company_id $nonOrdBooking_cond $samp_ref_cond2 $buyer_id_cond2 $year_cond_no_order ";

			 //group by f.booking_no,f.buyer_id,f.grouping ,g.lib_yarn_count_deter_id,  g.body_part , g.color_type_id, g.gsm_weight, g.construction, g.composition, g.dia, g.fabric_color
		}
	}
	// echo "string";die;
	//echo $sql;die;

	$r_id2=execute_query("delete from tmp_booking_no where userid=$user_id and type=1");
	oci_commit($con);
	disconnect($con);
	//echo $search_type.'='.$is_sales_booking;

	// echo $sql;

	$result = sql_select($sql);
	foreach ($result as $row ) 
	{
		$string_index = $row[csf("booking_no")]."*".$row[csf("buyer_id")]."*".$row[csf("job_no")]."*".$row[csf("body_part_id")]."*".$row[csf("color_type_id")]."*".$row[csf("gsm_weight")]."*".$row[csf("febric_description_id")]."*".$row[csf("construction")]."*".$row[csf("composition")]."*".$row[csf("color_size_sensitive")]."*".$row[csf("dia_width")]."*".$row[csf("color_number_id")]."*".$row[csf("file_no")]."*".$row[csf("grouping")]."*".$row[csf("style_ref_no")]."*".$row[csf("is_short")];
		
		$data_array[$string_index]['booking_no']=$row[csf("booking_no")];
		$data_array[$string_index]['buyer_id']=$row[csf("buyer_id")];
		$data_array[$string_index]['job_no']=$row[csf("job_no")];
		$data_array[$string_index]['body_part_id']=$row[csf("body_part_id")];
		$data_array[$string_index]['color_type_id']=$row[csf("color_type_id")];
		$data_array[$string_index]['gsm_weight']=$row[csf("gsm_weight")];
		$data_array[$string_index]['febric_description_id']=$row[csf("febric_description_id")];
		$data_array[$string_index]['construction']=$row[csf("construction")];
		$data_array[$string_index]['composition']=$row[csf("composition")];
		$data_array[$string_index]['color_size_sensitive']=$row[csf("color_size_sensitive")];
		$data_array[$string_index]['dia_width']=$row[csf("dia_width")];
		$data_array[$string_index]['color_number_id']=$row[csf("color_number_id")];
		$data_array[$string_index]['file_no']=$row[csf("file_no")];
		$data_array[$string_index]['grouping']=$row[csf("grouping")];
		$data_array[$string_index]['style_ref_no']=$row[csf("style_ref_no")];
		$data_array[$string_index]['is_short']=$row[csf("is_short")];

		$data_array[$string_index]['po_break_down_id'] .=$row[csf("po_break_down_id")].',';
		$data_array[$string_index]['po_number'] .=$row[csf("po_number")].',';
		$data_array[$string_index]['id'] .=$row[csf("id")].',';

		$data_array[$string_index]['po_buyer']=$row[csf("po_buyer")];
		$data_array[$string_index]['within_group']=$row[csf("within_group")];
		$data_array[$string_index]['fabric_desc']=$row[csf("fabric_desc")];
		$data_array[$string_index]['customer_buyer']=$row[csf("customer_buyer")];
		$data_array[$string_index]['fso_id']=$row[csf("fso_id")];

		$data_array[$string_index]['grey_fab_qnty'] +=$row[csf("grey_fab_qnty")];
		$data_array[$string_index]['adjust_grey_fab_qnty'] +=$row[csf("adjust_grey_fab_qnty")];

		
		
	}

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" style="width: 1050px;">
		<thead>
			<th width="25">Sl</th>
			<th width="50">Buyer</th>
			<th width="50">Ref. No</th>
			<th width="50">Booking type</th>
			<th width="50">Style Name</th>
			<th width="80">Booking No</th>
			<th width="60">Body Part</th>
			<th width="50">Color Type</th>
			<th width="60">Construction</th>
			<th width="70">Composition</th>
			<th width="40">F. GSM</th>
			<th width="40">F. Dia</th>
			<th width="50">Fabric Color</th>
			<th width="80">Job No</th>
			<?php if ($search_type == 6 || $is_sales_booking == "true") { ?>
				<th width="80">Sales Order No</th>
				<?php }else{?>
				<th width="80">Order No</th>
				<?php } ?>
			<th width="40">File No</th>
            <th width="70">Balance</th>
		</thead>
	</table>
	<div style="max-height:250px; overflow-y:scroll;width: 1070px;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search" style="width: 1050px;">
			<?
			$i = 1;
			/* foreach ($result as $row) {
				$is_sales= 0;
				$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
				// sales order wise search
				if ($search_type == 6 || $is_sales_booking == "true")
				{
					$is_sales       = 1;
					$within_group   = $row[csf("within_group")];
					$po_id          = $row[csf("id")];
					$po_no          = $row[csf("job_no")];
					// $job_no         = ($within_group == 1) ? $po_arr[$row[csf("booking_no")]]["job_no"] : "";
					$sales_order_no = $row[csf("job_no")];
					$fab_desc       = explode(",", $row[csf("fabric_desc")]);
					$construction   = $fab_desc[0];
					$composition    = $fab_desc[1];
					if($within_group == 1)
					{
						$buyer = $row[csf('po_buyer')];
						$job_no=$po_arr[$row[csf("booking_no")]]["job_no"];
						$int_ref=$po_arr[$row[csf("booking_no")]]["int_ref"];
					}
					else
					{
						$buyer = $row[csf('buyer_id')];
						$cbuyer = $row[csf('customer_buyer')];
						$job_no='';
						$int_ref='';
					}
				}
				else
				{
					$po_id          = implode(",", array_unique(explode(',', $row[csf('po_break_down_id')])));
					$po_no          = implode(",", array_unique(explode(',', $row[csf('po_number')])));
					$job_no         = $row[csf("job_no")];
					if($row[csf("construction")]=="" || $row[csf("composition")]=="")
					{
						$construction =$determin_array[$row[csf('febric_description_id')]][1];
						$composition =$determin_array[$row[csf('febric_description_id')]][2];
					}
					else
					{
						$construction   = $row[csf("construction")];
						$composition    = $row[csf("composition")];
					}
					$buyer 			= $row[csf('buyer_id')];
					$int_ref		= $row[csf('grouping')];
				}
				//echo $row[csf('febric_description_id')].'d';
				$key = $row[csf('color_type_id')] . $row[csf('color_number_id')] . $row[csf('booking_no')] . $buyer . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $construction . $composition . $int_ref . $row[csf('file_no')];
				$totReqnQty  = $reqn_qnty_array[$key];
				$booking_qty = 0;
				$booking_qty = $row[csf('grey_fab_qnty')] + $row[csf('adjust_grey_fab_qnty')];
				$balance_qty     = ($booking_qty - $totReqnQty);
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="25" align="center">
						<? echo $i; ?>
						<input type="hidden" name="buyerId[]" id="buyerId<? echo $i; ?>" value="<? echo $buyer; ?>"/>
						<input type="hidden" name="cbuyerId[]" id="cbuyerId<? echo $i; ?>" value="<? echo $cbuyer; ?>"/>
						<input type="hidden" name="cbuyerName[]" id="cbuyerName<? echo $i; ?>" value="<? echo $cbuyer_arr[$cbuyer]; ?>"/>
						<input type="hidden" name="poId[]" id="poId<? echo $i; ?>" value="<? echo $po_id; ?>"/>
						<input type="hidden" name="poNo[]" id="poNo<? echo $i; ?>" value="<? echo $po_no; ?>"/>
						<input type="hidden" name="bookingQty[]" id="bookingQty<? echo $i; ?>" value="<? echo number_format($booking_qty,2, '.', ''); ?>"/>
						<input type="hidden" name="totReqnQty[]" id="totReqnQty<? echo $i; ?>" value="<? echo number_format($totReqnQty,2, '.', ''); ?>"/>
						<input type="hidden" name="balanceQty[]" id="balanceQty<? echo $i; ?>" value="<? echo number_format($balance_qty,2, '.', ''); ?>"/>
						<input type="hidden" name="colorId[]" id="colorId<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
						<input type="hidden" name="colorTypeId[]" id="colorTypeId<? echo $i; ?>" value="<? echo $row[csf('color_type_id')]; ?>"/>
						<input type="hidden" name="bodyPartId[]" id="bodyPartId<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
						<input type="hidden" name="deterId[]" id="deterId<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
						<input type="hidden" name="programBookingId[]" id="programBookingId<? echo $i; ?>" value="<? echo $row[csf('booking_id')]; ?>"/>
						<input type="hidden" name="receiveBasisId[]" id="receiveBasisId<? echo $i; ?>" value="<? echo $recieve_basis; ?>"/>
						<input type="hidden" name="receiveBasis[]" id="receiveBasis<? echo $i; ?>" value="<? echo $basis_arr[$recieve_basis]; ?>"/>
						<input type="hidden" name="isSales[]" id="isSales<? echo $i; ?>" value="<? echo $is_sales; ?>"/>
						<input type="hidden" name="styleRef[]" id="styleRef<? echo $i; ?>" value="<? echo $row[csf('style_ref_no')]; ?>"/>
					</td>
					<td width="50" align="center"><? echo $buyer_arr[$buyer]; ?></td>
					<td width="100"><p><? echo $int_ref; ?></p></td>
					<td width="100" class="not_taken" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width="80" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="90" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
					<td width="70" align="center"><p><? echo $color_type[$row[csf('color_type_id')]]; ?></p></td>
					<td width="80" align="center"><p><? echo $construction; ?></p></td>
					<td width="100" align="center"><p><? echo $composition; ?></p></td>
					<td width="40" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
					<td width="40" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
					<td width="100" align="center"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
					<td width="80" align="center"><p><? echo $job_no; ?></p></td>
					<?php if ($search_type == 6 || $is_sales_booking == "true") { ?>
					<td width="80" align="center"><p><? echo $sales_order_no; ?></p></td>
					<?php }else{?>
					<td width="80" align="center"><p><? echo $po_no ; ?></p></td>
					<?php }?>
					<td width="40"><p><? echo $row[csf('file_no')]; ?></p></td>
                    <td width="70" align="right" title="BookQty=<? echo $booking_qty.',PrevQty='.$totReqnQty?>"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
				</tr>
				<?
				$i++;
			} */

			foreach ($data_array as $refString=> $row) 
			{
				$is_sales= 0;
				$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
				// sales order wise search
				if ($search_type == 6 || $is_sales_booking == "true")
				{
					$is_sales       = 1;
					$within_group   = $row["within_group"];
					$po_id          = $row["fso_id"];
					$po_no          = $row["job_no"];
					$sales_order_no = $row["job_no"];
					$fab_desc       = explode(",", $row["fabric_desc"]);
					$construction   = $fab_desc[0];
					$composition    = $fab_desc[1];
					if($within_group == 1)
					{
						$buyer = $row['po_buyer'];
						$job_no=$po_arr[$row["booking_no"]]["job_no"];
						$int_ref=$po_arr[$row["booking_no"]]["int_ref"];
					}
					else
					{
						$buyer = $row['buyer_id'];
						$cbuyer = $row['customer_buyer'];
						$job_no='';
						$int_ref='';
					}
				}
				else
				{
					$po_id          = implode(",", array_unique(explode(',', chop($row['po_break_down_id'],','))));
					$po_no          = implode(",", array_unique(explode(',', chop($row['po_number'],','))));
					$job_no         = $row["job_no"];
					if($row["construction"]=="" || $row["composition"]=="")
					{
						$construction =$determin_array[$row['febric_description_id']][1];
						$composition =$determin_array[$row['febric_description_id']][2];
					}
					else
					{
						$construction   = $row["construction"];
						$composition    = $row["composition"];
					}
					$buyer 			= $row['buyer_id'];
					$int_ref		= $row['grouping'];
				}
				//echo $row[csf('febric_description_id')].'d';
				$key = $row['color_type_id'] . $row['color_number_id'] . $row['booking_no'] . $buyer . $row['body_part_id'] . $row['gsm_weight'] . $row['dia_width'] . $construction . $composition . $int_ref . $row['file_no'];
				$totReqnQty  = $reqn_qnty_array[$key];
				$booking_qty = 0;
				$booking_qty = $row['grey_fab_qnty'] + $row['adjust_grey_fab_qnty'];
				$balance_qty     = ($booking_qty - $totReqnQty);
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="25" align="center">
						<? echo $i; ?>
						<input type="hidden" name="buyerId[]" id="buyerId<? echo $i; ?>" value="<? echo $buyer; ?>"/>
						<input type="hidden" name="cbuyerId[]" id="cbuyerId<? echo $i; ?>" value="<? echo $cbuyer; ?>"/>
						<input type="hidden" name="cbuyerName[]" id="cbuyerName<? echo $i; ?>" value="<? echo $cbuyer_arr[$cbuyer]; ?>"/>
						<input type="hidden" name="poId[]" id="poId<? echo $i; ?>" value="<? echo $po_id; ?>"/>
						<input type="hidden" name="poNo[]" id="poNo<? echo $i; ?>" value="<? echo $po_no; ?>"/>
						<input type="hidden" name="bookingQty[]" id="bookingQty<? echo $i; ?>" value="<? echo number_format($booking_qty,2, '.', ''); ?>"/>
						<input type="hidden" name="totReqnQty[]" id="totReqnQty<? echo $i; ?>" value="<? echo number_format($totReqnQty,2, '.', ''); ?>"/>
						<input type="hidden" name="balanceQty[]" id="balanceQty<? echo $i; ?>" value="<? echo number_format($balance_qty,2, '.', ''); ?>"/>
						<input type="hidden" name="colorId[]" id="colorId<? echo $i; ?>" value="<? echo $row['color_number_id']; ?>"/>
						<input type="hidden" name="colorTypeId[]" id="colorTypeId<? echo $i; ?>" value="<? echo $row['color_type_id']; ?>"/>
						<input type="hidden" name="bodyPartId[]" id="bodyPartId<? echo $i; ?>" value="<? echo $row['body_part_id']; ?>"/>
						<input type="hidden" name="deterId[]" id="deterId<? echo $i; ?>" value="<? echo $row['febric_description_id']; ?>"/>
						<input type="hidden" name="programBookingId[]" id="programBookingId<? echo $i; ?>" value="<? echo $row['booking_id']; ?>"/>
						<input type="hidden" name="receiveBasisId[]" id="receiveBasisId<? echo $i; ?>" value="<? echo $recieve_basis; ?>"/>
						<input type="hidden" name="receiveBasis[]" id="receiveBasis<? echo $i; ?>" value="<? echo $basis_arr[$recieve_basis]; ?>"/>
						<input type="hidden" name="isSales[]" id="isSales<? echo $i; ?>" value="<? echo $is_sales; ?>"/>
						<input type="hidden" name="styleRef[]" id="styleRef<? echo $i; ?>" value="<? echo $row['style_ref_no']; ?>"/>
						<input type="hidden" name="isShort[]" id="isShort<? echo $i; ?>" value="<? echo $row['is_short']; ?>"/>
					</td>
					<td width="50" align="center"><? echo $buyer_arr[$buyer]; ?></td>
					<td width="50"><p><? echo $int_ref; ?></p></td>
					<td width="50" class="not_taken"><p><? 
						if($row['is_short']==2){
							echo "Main";
						}
						if($row['is_short']==1){
							echo "Short";
						}
					 
					 ?></p></td>
					<td width="50" class="not_taken" align="center"><p><? echo $row['style_ref_no']; ?></p></td>
					<td width="80" align="center"><p><? echo $row['booking_no']; ?></p></td>
					<td width="60" align="center"><p><? echo $body_part[$row['body_part_id']]; ?></p></td>
					<td width="50" align="center"><p><? echo $color_type[$row['color_type_id']]; ?></p></td>
					<td width="60" align="center"><p><? echo $construction; ?></p></td>
					<td width="70" align="center"><p><? echo $composition; ?></p></td>
					<td width="40" align="center"><p><? echo $row['gsm_weight']; ?></p></td>
					<td width="40" align="center"><p><? echo $row['dia_width']; ?></p></td>
					<td width="50" align="center"><p><? echo $color_arr[$row['color_number_id']]; ?></p></td>
					<td width="80" align="center"><p><? echo $job_no; ?></p></td>
					<?php if ($search_type == 6 || $is_sales_booking == "true") { ?>
					<td width="80" align="center"><p><? echo $sales_order_no; ?></p></td>
					<?php }else{?>
					<td width="80" align="center"><p><? echo $po_no ; ?></p></td>
					<?php }?>
					<td width="40"><p><? echo $row['file_no']; ?></p></td>
                    <td width="70" align="right" title="BookQty=<? echo $booking_qty.',PrevQty='.$totReqnQty?>"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
				</tr>
				<?
				$i++;
			}



			?>
		</table>
	</div>

	<table width="900" cellspacing="0" cellpadding="0" border="1" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:45%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)"/>
						Check / Uncheck All
					</div>
					<div style="width:55%; float:left" align="left">
						<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close"
						style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if ($db_type == 0) $year_cond = "YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
		else $year_cond = "";//defined Later

		$new_mrr_number = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', 'FRB', date("Y", time()), 5, "select reqn_number_prefix, reqn_number_prefix_num from pro_fab_reqn_for_batch_mst where company_id=$cbo_company_id and $year_cond=" . date('Y', time()) . " order by id desc ", "reqn_number_prefix", "reqn_number_prefix_num"));
		$id = return_next_id("id", "pro_fab_reqn_for_batch_mst", 1);

		$field_array = "id,reqn_number_prefix,reqn_number_prefix_num,reqn_number,company_id,location_id,reqn_date,inserted_by,insert_date";
		$data_array = "(" . $id . ",'" . $new_mrr_number[1] . "'," . $new_mrr_number[2] . ",'" . $new_mrr_number[0] . "'," . $cbo_company_id . "," . $cbo_location_name . "," . $txt_requisition_date . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		$field_array_dtls = "id, mst_id, program_booking_pi_no, po_id, buyer_id, customer_buyer, job_no, color_id, reqn_qty,gsm_weight,dia_width,construction,composition,file_no,grouping,body_part_id,color_type_id,booking_qty,entry_form, remarks, inserted_by, insert_date,is_sales";
		$dtls_id = return_next_id("id", "pro_fab_reqn_for_batch_dtls", 1);


		$field_array_po_break = "id, dtls_id,mst_id, po_id,inserted_by, insert_date";
		$po_break_id = return_next_id("id", "pro_fab_reqn_po_break", 1);


		for ($j = 1; $j <= $tot_row; $j++) {

			$job = "job" . $j;
			$bookingNo = "bookingNo" . $j;
			$poId = "poId" . $j;
			$buyerId = "buyerId" . $j;
			$colorId = "colorId" . $j;
			$reqsnQty = "reqsnQty" . $j;
			$remarks = "remarks" . $j;
			$fileNo = "fileNo" . $j;
			$grouping = "grouping" . $j;
			$constraction = "constraction" . $j;
			$composition = "composition" . $j;
			$gsm = "gsm" . $j;
			$dia = "dia" . $j;
			$colorTypeId = "colorTypeId" . $j;
			$bodyPartId = "bodyPartId" . $j;
			$bookintQty = "bookintQty" . $j;
			$dtlsId = "dtlsId" . $j;
			$isSales = "isSales" . $j;
			$cbuyerId = "cbuyerId" . $j;

			//po id break down.........................
			foreach (explode(',', $$poId) as $po_val) {
				if ($data_array_po_break != "") $data_array_po_break .= ",";
				$data_array_po_break .= "(" . $po_break_id . "," . $dtls_id . "," . $id . ",'" . $po_val . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$po_break_id++;
			}

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$bookingNo . "','" . $$poId . "','" . $$buyerId . "','" . $$cbuyerId . "','" . $$job . "','" . $$colorId . "','" . $$reqsnQty . "','" . $$gsm . "','" . $$dia . "','" . $$constraction . "','" . $$composition . "','" . $$fileNo . "','" . $$grouping . "','" . $$bodyPartId . "','" . $$colorTypeId . "','" . $$bookintQty . "',123,'" . $$remarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$isSales . ")";

			$dtls_id = $dtls_id + 1;
		}
		//echo $data_array_dtls;
		$rID = sql_insert("pro_fab_reqn_for_batch_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("pro_fab_reqn_for_batch_dtls", $field_array_dtls, $data_array_dtls, 1);
		$rID3 = sql_insert("pro_fab_reqn_po_break", $field_array_po_break, $data_array_po_break, 1);
        //echo "10**"."INSERT INTO pro_fab_reqn_for_batch_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls;die;
        //echo "10**".$rID ."&&". $rID2 ."&&". $rID3;die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_mrr_number[0];
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_mrr_number[0];
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$field_array = "location_id*reqn_date*updated_by*update_date";
		$data_array = $cbo_location_name . "*" . $txt_requisition_date . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$field_array_dtls = "id, mst_id, program_booking_pi_no,po_id, buyer_id, customer_buyer, job_no, color_id, reqn_qty,gsm_weight,dia_width,construction,composition,file_no,grouping,body_part_id,color_type_id,booking_qty,entry_form, remarks, inserted_by, insert_date,is_sales";
		$field_array_update = "reqn_qty*remarks*updated_by*update_date";
		$dtls_id = return_next_id("id", "pro_fab_reqn_for_batch_dtls", 1);

		$field_array_po_break = "id, dtls_id,mst_id, po_id,inserted_by, insert_date";
		$po_break_id = return_next_id("id", "pro_fab_reqn_po_break", 1);

		$deleted_id = '';

		for ($j = 1; $j <= $tot_row; $j++) {
			$job = "job" . $j;
			$bookingNo = "bookingNo" . $j;
			$poId = "poId" . $j;
			$buyerId = "buyerId" . $j;
			$colorId = "colorId" . $j;
			$reqsnQty = "reqsnQty" . $j;
			$remarks = "remarks" . $j;
			$fileNo = "fileNo" . $j;
			$grouping = "grouping" . $j;
			$constraction = "constraction" . $j;
			$composition = "composition" . $j;
			$gsm = "gsm" . $j;
			$dia = "dia" . $j;
			$colorTypeId = "colorTypeId" . $j;
			$bodyPartId = "bodyPartId" . $j;
			$bookintQty = "bookintQty" . $j;
			$dtlsId = "dtlsId" . $j;
			$isSales = "isSales" . $j;
			$cbuyerId = "cbuyerId" . $j;

			if ($$dtlsId > 0) {
				if ($$reqsnQty > 0) {
					$dtlsId_arr[] = $$dtlsId;
					$data_array_update[$$dtlsId] = explode("*", ("'" . $$reqsnQty . "'*'" . $$remarks . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				} else {
					$deleted_id .= $$dtlsId . ",";
				}
			} else {

				//po id break down.........................
				foreach (explode(',', $$poId) as $po_val) {
					if ($data_array_po_break != "") $data_array_po_break .= ",";
					$data_array_po_break .= "(" . $po_break_id . "," . $dtls_id . "," . $update_id . ",'" . $po_val . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$po_break_id++;
				}


				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$bookingNo . "','" . $$poId . "','" . $$buyerId . "','" . $$cbuyerId . "','" . $$job . "','" . $$colorId . "','" . $$reqsnQty . "','" . $$gsm . "','" . $$dia . "','" . $$constraction . "','" . $$composition . "','" . $$fileNo . "','" . $$grouping . "','" . $$bodyPartId . "','" . $$colorTypeId . "','" . $$bookintQty . "',123,'" . $$remarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$isSales . ")";

				$dtls_id = $dtls_id + 1;

			}
		}
		//echo '10**'.$data_array_po_break;die;

		$rID = sql_update("pro_fab_reqn_for_batch_mst", $field_array, $data_array, "id", $update_id, 0);
		$rID2 = true;
		$rID3 = true;
		$statusChange = true;
		$statusChange_break = true;
		if (count($data_array_update) > 0) {
			$rID2 = execute_query(bulk_update_sql_statement("pro_fab_reqn_for_batch_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr));
			//$rID3=sql_insert("pro_fab_reqn_po_break",$field_array_po_break,$data_array_po_break,1);
		}

		if ($data_array_dtls != "") {
			$rID2 = sql_insert("pro_fab_reqn_for_batch_dtls", $field_array_dtls, $data_array_dtls, 1);
			$rID3 = sql_insert("pro_fab_reqn_po_break", $field_array_po_break, $data_array_po_break, 1);
		}

		$deleted_id = substr($deleted_id, 0, -1);
		if ($deleted_id != "") {
			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			$statusChange = sql_multirow_update("pro_fab_reqn_for_batch_dtls", $field_array_status, $data_array_status, "id", $deleted_id, 0);
			$statusChange_break = sql_multirow_update("pro_fab_reqn_po_break", $field_array_status, $data_array_status, "dtls_id", $deleted_id, 0);
		}
		// oci_rollback($con);
		// echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusChange."&&".$statusChange_break;die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $statusChange && $statusChange_break) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_requisition_no);
			} else {
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $update_id) . "**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $statusChange) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_requisition_no);
			} else {
				oci_rollback($con);
				echo "6**" . str_replace("'", '', $update_id) . "**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "requisition_popup") {
	echo load_html_head_contents("Requisition Info", "../../", 1, '', '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data) {
			$('#hidden_reqn_id').val(data);
			parent.emailwindow.hide();
		}

		function set_search_by(type)
	 	{
			$('#txt_search_val').val('');

			if (type == 1) {
				$('#td_search').text('Enter Reff NO');
			}
			else if (type == 2) {
				$('#td_search').text('Enter Booking No');
			}
			else if (type == 3) {
				$('#td_search').text('Enter Job No');
			}
			else if (type == 4) {
				$('#td_search').text('Enter File No');
			}
			else if (type == 6) {
				$('#td_search').text('Enter Sales Order No');
			}
			else if (type == 7) {
				$('#td_search').text('Enter Style No');
			}
			else {
				$('#td_search').text('Enter Order No');
			}
		}
	</script>
</head>
<body>
	<div align="center" style="width:1060px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:1060px; margin-left:2px">
				<table cellpadding="0" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Location</th>
						<th>Buyer</th>
						<th>Search Type</th>
						<th id="td_search">Enter Reff No</th>
						<th>Requisition Date Range</th>
						<th id="search_by_td_up" width="180">Requisition No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">
						</th>
					</thead>


					<tr class="general">
						<td align="center">
							<? echo create_drop_down("cbo_location_id", 150, "select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name", 'id,location_name', 1, '-- Select Location --', 0, "", 0); ?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", 0, "", 0);
							?>
						</td>
						<td>
							<?
								$search_by_arr = array(1 => "Reff No", 2 => "Booking No", 3 => "Job No", 4 => "File No", 5 => "Order No", 6 => "Sales Order No", 7 => "Style No.");
								echo create_drop_down("cbo_search_by", 90, $search_by_arr, "", 0, "--Select--", "", "set_search_by(this.value);", 0);
							?>
						</td>
						<td>
							<input type="text" name="txt_search_val" id="txt_search_val" style="width:100px"
							class="text_boxes"/>
						</td>

						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:80px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"
							readonly>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_reqn_no"
							id="txt_reqn_no"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_location_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_val').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_reqn_search_list_view', 'search_div', 'fabric_requisition_for_batch_entry_controller_2', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>

					<tr>
						<td colspan="8" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_id').val(0);
</script>
</html>
<?
}
if ($action == "create_reqn_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]);
	$start_date = $data[1];
	$end_date = $data[2];
	$location_id = $data[3];
	$company_id = $data[4];

	$cbo_search_by = $data[5];
	$txt_search_val = $data[6];
	$cbo_buyer_id= $data[7];

	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$buyer_arr = return_library_array( "select buy.id, buy.short_name as buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name",'id','buyer_name');

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.reqn_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.reqn_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		$search_field_cond = "and a.reqn_number like '$search_string'";
	}

	$location_cond = "";
	if ($location_id > 0) {
		$location_cond = "and a.location_id=$location_id";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "max(to_char(a.insert_date,'YYYY')) as year,";
	} else $year_field = "";//defined Later


	$req_search_cond=""; $tbl_cond="";
	if ($cbo_buyer_id>0)
	{
		$req_search_cond .=" and b.buyer_id='$cbo_buyer_id' ";
	}
	if($txt_search_val)
	{
		if($cbo_search_by==1) { $req_search_cond .=" and b.grouping='$txt_search_val' "; }
		if($cbo_search_by==2) { $req_search_cond .=" and b.program_booking_pi_no like '%$txt_search_val%' "; }
		if($cbo_search_by==3) { $req_search_cond .=" and b.job_no like '%$txt_search_val%' "; }
		if($cbo_search_by==4) { $req_search_cond .=" and b.file_no='$txt_search_val' "; }
		if($cbo_search_by==5)
		{
			$tbl_cond .=", wo_po_break_down c ";
			//$req_search_cond .=" and c.id=b.po_id and c.po_number like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
			$req_search_cond .=" and find_in_set(b.po_id, c.id)>0 and c.po_number like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
		}

		if($cbo_search_by==6)
		{
			$tbl_cond .=", fabric_sales_order_mst c ";
			//$req_search_cond .=" and c.id=b.po_id and c.job_no like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
			$req_search_cond .=" and find_in_set(b.po_id, c.id)>0 and c.job_no like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
		}
		if($cbo_search_by==7)
		{
			$style_from_job = return_field_value("job_no", "wo_po_details_master", "company_name =$company_id and style_ref_no='$txt_search_val'");
			$style_from_job_cond =" and b.job_no ='$style_from_job' ";
		}
	}
	// echo $style_from_job_cond;die;

	//PO Id----------------------------------------------------------------------------------------
	$po_number_arr=array();
	/*$po_arr=sql_select("select id, po_number from wo_po_break_down where id in($order_ids) and status_active=1 and is_deleted=0");*/
	$po_arr=sql_select("select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0");
	foreach ($po_arr as $row)
	{
		$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
	}

	//Sales order Id-------------------------------------------------------------------------------

	$salesOrderArr = array();
	/*$salesOrderData = sql_select("select id,job_no,sales_booking_no,within_group from fabric_sales_order_mst where id in($sales_order_ids) and status_active = 1 and is_deleted = 0");*/
	$salesOrderData = sql_select("select id,job_no,sales_booking_no,within_group from fabric_sales_order_mst where status_active = 1 and is_deleted = 0");
	foreach ($salesOrderData as $row)
	{
		$salesOrderArr[$row[csf('id')]] = $row[csf('job_no')];
	}

	//Making Requisition wise data array--------------------------------------------------------------
	$requ_array=array();
	$reqn_qty=$reqn_buyer=$reqn_file=$reqn_ref=$reqn_job=$reqn_booking=$reqn_color=$reqn_order_num=array();
	$requ_sql = sql_select("SELECT a.id, b.program_booking_pi_no, b.po_id, b.buyer_id, b.job_no, b.file_no, b.grouping, b.reqn_qty, b.is_sales, b.color_id
	from pro_fab_reqn_for_batch_mst a,pro_fab_reqn_for_batch_dtls b $tbl_cond
	where a.id=b.mst_id and b.entry_form=123 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $req_search_cond $style_from_job_cond order by a.id");
	
	$m=0;$reqn_buyer=array();
	foreach ($requ_sql as $value)
	{
		if($value[csf('reqn_qty')]){ $reqn_qty[$value[csf('id')]]+=$value[csf('reqn_qty')]; }

		if($value[csf('grouping')])
		{
			if (!in_array($value[csf('grouping')], $requ_array[$value[csf('id')]]['grouping']))
			{
				$requ_array[$value[csf('id')]]['grouping'][$value[csf('grouping')]]=$value[csf('grouping')];
				$reqn_ref[$value[csf('id')]].=$value[csf('grouping')].", ";
			}
		}

		if($value[csf('file_no')])
		{
			if (!in_array($value[csf('file_no')], $requ_array[$value[csf('id')]]['file_no']))
			{
				$requ_array[$value[csf('id')]]['file_no'][$value[csf('file_no')]]=$value[csf('file_no')];
				$reqn_file[$value[csf('id')]].=$value[csf('file_no')].", ";
			}
		}

		if($value[csf('program_booking_pi_no')])
		{
			if (!in_array($value[csf('program_booking_pi_no')], $requ_array[$value[csf('id')]]['program_booking_pi_no']))
			{
				$requ_array[$value[csf('id')]]['program_booking_pi_no'][$value[csf('program_booking_pi_no')]]=$value[csf('program_booking_pi_no')];
				$reqn_booking[$value[csf('id')]].=$value[csf('program_booking_pi_no')].", ";
			}
		}

		if($value[csf('color_id')])
		{
			if (!in_array($value[csf('color_id')], $requ_array[$value[csf('id')]]['color_id']))
			{
				//echo "d,";
				$requ_array[$value[csf('id')]]['color_id'][$value[csf('color_id')]]=$value[csf('color_id')];
				if($value[csf('color_id')]>0)
				{
					//echo $value[csf('id')].', ';
					$reqn_color[$value[csf('id')]].=$color_arr[$value[csf('color_id')]].', ';
				}
			}
		}

		if($value[csf('job_no')])
		{
			if (!in_array($value[csf('job_no')], $requ_array[$value[csf('id')]]['job_no']))
			{
				$requ_array[$value[csf('id')]]['job_no'][$value[csf('job_no')]]=$value[csf('job_no')];
				$reqn_job[$value[csf('id')]].=$value[csf('job_no')].", ";
				//$reqn_job_arr[$value[csf('job_no')]]=$value[csf('job_no')];
			}
		}

		if($value[csf('buyer_id')])
		{
			if (!in_array($value[csf('buyer_id')], $requ_array[$value[csf('id')]]['buyer']))
			{
				//echo "d,";
				$requ_array[$value[csf('id')]]['buyer'][$value[csf('buyer_id')]]=$value[csf('buyer_id')];
				if($value[csf('buyer_id')]>0)
				{
					//echo $value[csf('id')].', ';

						$reqn_buyer[$value[csf('id')]].=$buyer_arr[$value[csf('buyer_id')]].', ';


				}
			}
		}

		if($value[csf('po_id')])
		{
			if($value[csf('is_sales')]==1) //for sales order
			{
				foreach (explode(",", $value[csf('po_id')]) as $row)
				{
					if (!in_array($row, $requ_array[$value[csf('id')]]['po_id']))
					{
						$requ_array[$value[csf('id')]]['po_id'][$row]=$row;
						$reqn_order_num[$value[csf('id')]].=$salesOrderArr[$row].", ";
					}
				}
			}
			else
			{
				foreach (explode(",", $value[csf('po_id')]) as $row)
				{
					if (!in_array($row, $requ_array[$value[csf('id')]]['po_id']))
					{
						$requ_array[$value[csf('id')]]['po_id'][$row]=$row;
						$reqn_order_num[$value[csf('id')]].=$po_number_arr[$row].", ";
						$po_id_arr[$row]=$row;
					}
				}
			}
		}

	}
	// $tt=rtrim(implode(",",$reqn_order_num),',');
	// echo '<pre>'; print_r($po_id_arr);die;

	//Style no----------------------------------------------------------------------------------------
	if(count($po_id_arr)>0)
	{
		$style_number_arr=array();
		$po_ids=implode(",",$po_id_arr);
		$style_po_arr=sql_select("SELECT a.id, a.job_no, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");//and b.id in($po_ids)// and a.job_no in('D n C-17-00142','D n C-17-00294','D n C-17-00191','D n C-17-00537','OG-18-01218')
		foreach ($style_po_arr as $row)
		{
			$style_number_arr[$row[csf('job_no')]] = $row[csf('style_ref_no')];
		}
	}
	// echo '<pre>';print_r($style_number_arr);

	$sql = "SELECT a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date from pro_fab_reqn_for_batch_mst a,pro_fab_reqn_for_batch_dtls b $tbl_cond where a.id=b.mst_id and b.entry_form=123 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $req_search_cond $style_from_job_cond group by a.id, a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date order by a.id";
	$result = sql_select($sql);

	/*$arr = array(0 => $location_arr,4=>$reqn_buyer,5=>$reqn_booking, 6=>$reqn_job, 7=>$reqn_order_num, 8=>$reqn_file, 9=>$reqn_ref, 10=>$reqn_qty);

	echo create_list_view("tbl_list_search", "Location, Year, Requisition No, Requisition Date, Buyer, Booking No, Job No, Order/FSO No, File No, Ref. No, Rqsn Qty", "130,70,70,70,100,100,120,70,70,70", "1020", "200", 0, $sql, "js_set_value", "id", "", 1, "location_id,0,0,0,id,id,id,id,id,id,id", $arr, "location_id,year,reqn_number_prefix_num,reqn_date,id,id,id,id,id,id,id", "", "", '0,0,0,3,0,0,0,0,0,0,0', '');*/
	//Location, Year, Requisition No, Requisition Date, Buyer, Booking No, Job No, Order/FSO No, File No, Ref. No, Rqsn Qty
	?>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="1060" cellspacing="0" cellpadding="0"
	border="0">
	<thead>
		<tr>
			<th width="20">SL No</th>
			<th width="100">Location</th>
			<th width="50">Year</th>
			<th width="60">Requisition No</th>
			<th width="80">Requisition Date</th>
			<th width="100">Buyer</th>
			<th width="100">Booking No</th>
			<th width="60">Fabric Color</th>
			<th width="80">Job No</th>
			<th width="100">Style No</th>
			<th width="100">Order/FSO No</th>
			<th width="70">File No</th>
            <th width="70">Ref. No</th>
			<th width="60">Booking Type</th>
			<th>Rqsn Qty</th>
		</tr>
	</thead>
	<tbody>
		<?
		$i = 1;
		//echo '<pre>';print_r($style_number_arr);
		foreach ($result as $row)
		{
			$style_no="";
			$job_arr= explode(",",rtrim($reqn_job[$row[csf("id")]],','));
			foreach ($job_arr as $job)
            {
            	// echo $job.'='.$style_number_arr[trim($job)].'<br>';
                $style_no.=$style_number_arr[trim($job)].",";
            }
            $style_no = rtrim($style_no,",");

			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			?>
			<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>)" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
				<td width="20"><? echo $i; ?></td>
				<td width="100"><? echo $location_arr[$row[csf("location_id")]]; ?></td>
				<td width="50"><? echo $row[csf("year")]; ?></td>
				<td width="60"><p><? echo $row[csf("reqn_number_prefix_num")];// trim($order_ids, ","); ?></p></td>
				<td width="80"><? echo change_date_format($row[csf("reqn_date")]); ?></td>
				<td width="100"><? echo rtrim($reqn_buyer[$row[csf("id")]],', '); ?></td>
				<td width="100"><?
				 $booking_number = rtrim($reqn_booking[$row[csf("id")]],', '); 
				 echo $booking_number ;
				 ?></td>
				<td width="60"><? echo rtrim($reqn_color[$row[csf("id")]],', '); ?></td>
				<td width="80"><? echo rtrim($reqn_job[$row[csf("id")]],', '); ?></td>
				<td width="100"><? echo $style_no; ?></td>
				<td width="100"><? echo rtrim($reqn_order_num[$row[csf("id")]],', '); ?></td>
				<td width="70"><? echo rtrim($reqn_file[$row[csf("id")]],', '); ?></td>
				<td width="70"><? echo rtrim($reqn_ref[$row[csf("id")]],', '); ?></td>
				<td width="70"><?
				 	$booking_type=return_field_value("is_short"," wo_booking_mst","booking_no='$booking_number' and is_deleted=0 and status_active=1");
					 if($booking_type==1){
						 echo "Short";
					 }
					 if($booking_type==2){
						 echo "Main";
					 } 
				 ?></td>
				<td><? echo $reqn_qty[$row[csf("id")]]; ?></td>
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

if ($action == "create_reqn_search_list_view_old") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]);
	$start_date = $data[1];
	$end_date = $data[2];
	$location_id = $data[3];
	$company_id = $data[4];

	$cbo_search_by = $data[5];
	$txt_search_val = $data[6];
	$cbo_buyer_id= $data[7];

	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$buyer_arr = return_library_array( "select buy.id, buy.short_name as buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name",'id','buyer_name');

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.reqn_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.reqn_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		$search_field_cond = "and a.reqn_number like '$search_string'";
	}

	$location_cond = "";
	if ($location_id > 0) {
		$location_cond = "and a.location_id=$location_id";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "max(to_char(a.insert_date,'YYYY')) as year,";
	} else $year_field = "";//defined Later


	$req_search_cond=""; $tbl_cond="";
	if ($cbo_buyer_id>0)
	{
		$req_search_cond .=" and b.buyer_id='$cbo_buyer_id' ";
	}
	if($txt_search_val)
	{
		if($cbo_search_by==1) { $req_search_cond .=" and b.grouping='$txt_search_val' "; }
		if($cbo_search_by==2) { $req_search_cond .=" and b.program_booking_pi_no like '%$txt_search_val%' "; }
		if($cbo_search_by==3) { $req_search_cond .=" and b.job_no like '%$txt_search_val%' "; }
		if($cbo_search_by==4) { $req_search_cond .=" and b.file_no='$txt_search_val' "; }
		if($cbo_search_by==5)
		{
			$tbl_cond .=", wo_po_break_down c ";
			//$req_search_cond .=" and c.id=b.po_id and c.po_number like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
			$req_search_cond .=" and find_in_set(b.po_id, c.id)>0 and c.po_number like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
		}

		if($cbo_search_by==6)
		{
			$tbl_cond .=", fabric_sales_order_mst c ";
			//$req_search_cond .=" and c.id=b.po_id and c.job_no like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
			$req_search_cond .=" and find_in_set(b.po_id, c.id)>0 and c.job_no like '%$txt_search_val%' and c.status_active=1 and c.is_deleted=0 ";
		}
	}

	//PO Id----------------------------------------------------------------------------------------
	$po_number_arr=array();
	/*$po_arr=sql_select("select id, po_number from wo_po_break_down where id in($order_ids) and status_active=1 and is_deleted=0");*/
	$po_arr=sql_select("select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0");
	foreach ($po_arr as $row)
	{
		$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
	}

	//Sales order Id-------------------------------------------------------------------------------

	$salesOrderArr = array();
	/*$salesOrderData = sql_select("select id,job_no,sales_booking_no,within_group from fabric_sales_order_mst where id in($sales_order_ids) and status_active = 1 and is_deleted = 0");*/
	$salesOrderData = sql_select("select id,job_no,sales_booking_no,within_group from fabric_sales_order_mst where status_active = 1 and is_deleted = 0");
	foreach ($salesOrderData as $row)
	{
			$salesOrderArr[$row[csf('id')]] = $row[csf('job_no')];
	}

	//Making Requisition wise data array--------------------------------------------------------------
	$requ_array=array();
	$reqn_qty=$reqn_buyer=$reqn_file=$reqn_ref=$reqn_job=$reqn_booking=$reqn_order_num=array();
	$requ_sql = sql_select("select a.id, b.program_booking_pi_no, b.po_id, b.buyer_id, b.job_no, b.file_no, b.grouping, b.reqn_qty, b.is_sales from pro_fab_reqn_for_batch_mst a,pro_fab_reqn_for_batch_dtls b $tbl_cond where a.id=b.mst_id and b.entry_form=123 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $req_search_cond  order by a.id");
	$m=0;$reqn_buyer=array();
	foreach ($requ_sql as $value)
	{
		if($value[csf('reqn_qty')]){ $reqn_qty[$value[csf('id')]]+=$value[csf('reqn_qty')]; }

		if($value[csf('grouping')])
		{
			if (!in_array($value[csf('grouping')], $requ_array[$value[csf('id')]]['grouping']))
			{
				$requ_array[$value[csf('id')]]['grouping'][$value[csf('grouping')]]=$value[csf('grouping')];
				$reqn_ref[$value[csf('id')]].=$value[csf('grouping')].", ";
			}
		}

		if($value[csf('file_no')])
		{
			if (!in_array($value[csf('file_no')], $requ_array[$value[csf('id')]]['file_no']))
			{
				$requ_array[$value[csf('id')]]['file_no'][$value[csf('file_no')]]=$value[csf('file_no')];
				$reqn_file[$value[csf('id')]].=$value[csf('file_no')].", ";
			}
		}

		if($value[csf('program_booking_pi_no')])
		{
			if (!in_array($value[csf('program_booking_pi_no')], $requ_array[$value[csf('id')]]['program_booking_pi_no']))
			{
				$requ_array[$value[csf('id')]]['program_booking_pi_no'][$value[csf('program_booking_pi_no')]]=$value[csf('program_booking_pi_no')];
				$reqn_booking[$value[csf('id')]].=$value[csf('program_booking_pi_no')].", ";
			}
		}

		if($value[csf('job_no')])
		{
			if (!in_array($value[csf('job_no')], $requ_array[$value[csf('id')]]['job_no']))
			{
				$requ_array[$value[csf('id')]]['job_no'][$value[csf('job_no')]]=$value[csf('job_no')];
				$reqn_job[$value[csf('id')]].=$value[csf('job_no')].", ";
			}
		}

		if($value[csf('buyer_id')])
		{
			if (!in_array($value[csf('buyer_id')], $requ_array[$value[csf('id')]]['buyer']))
			{
				//echo "d,";
				$requ_array[$value[csf('id')]]['buyer'][$value[csf('buyer_id')]]=$value[csf('buyer_id')];
				if($value[csf('buyer_id')]>0)
				{
					//echo $value[csf('id')].', ';

						$reqn_buyer[$value[csf('id')]].=$buyer_arr[$value[csf('buyer_id')]].',';


				}
			}
		}

		if($value[csf('po_id')])
		{
			if($value[csf('is_sales')]==1) //for sales order
			{
				foreach (explode(",", $value[csf('po_id')]) as $row)
				{
					if (!in_array($row, $requ_array[$value[csf('id')]]['po_id']))
					{
						$requ_array[$value[csf('id')]]['po_id'][$row]=$row;
						$reqn_order_num[$value[csf('id')]].=$salesOrderArr[$row].", ";
					}
				}
			}
			else
			{
				foreach (explode(",", $value[csf('po_id')]) as $row)
				{
					if (!in_array($row, $requ_array[$value[csf('id')]]['po_id']))
					{
						$requ_array[$value[csf('id')]]['po_id'][$row]=$row;
						$reqn_order_num[$value[csf('id')]].=$po_number_arr[$row].", ";
					}
				}
			}
		}

	}
	//$tt=rtrim(implode(",",$reqn_buyer),',');

	//print_r($tt);

	$sql = "select a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date from pro_fab_reqn_for_batch_mst a,pro_fab_reqn_for_batch_dtls b $tbl_cond where a.id=b.mst_id and b.entry_form=123 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond $req_search_cond group by a.id, a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date order by a.id";

	$arr = array(0 => $location_arr,4=>$reqn_buyer,5=>$reqn_booking, 6=>$reqn_job, 7=>$reqn_order_num, 8=>$reqn_file, 9=>$reqn_ref, 10=>$reqn_qty);

	echo create_list_view("tbl_list_search", "Location, Year, Requisition No, Requisition Date, Buyer, Booking No, Job No, Order/FSO No, File No, Ref. No, Rqsn Qty", "130,70,70,70,100,100,120,70,70,70", "1020", "200", 0, $sql, "js_set_value", "id", "", 1, "location_id,0,0,0,id,id,id,id,id,id,id", $arr, "location_id,year,reqn_number_prefix_num,reqn_date,id,id,id,id,id,id,id", "", "", '0,0,0,3,0,0,0,0,0,0,0', '');

	exit();
}

if ($action == 'populate_data_from_requisition') {
	$data_array = sql_select("select id, reqn_number, company_id, location_id, reqn_date from pro_fab_reqn_for_batch_mst where id='$data'");
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_requisition_no').value 			= '" . $row[csf("reqn_number")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_location_name').value 			= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_requisition_date').value 		= '" . change_date_format($row[csf("reqn_date")]) . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_fabric_requisition_for_batch_2',1);\n";
		exit();
	}
}

if ($action == 'populate_list_view')
{
	$reqn_qnty_array = array();
	$sales_order_ids = $order_ids = "";
	$reqnData = sql_select("SELECT reqn_qty, color_type_id, color_id, program_booking_pi_no, po_id, buyer_id, body_part_id, gsm_weight, dia_width, construction, composition, grouping,file_no,is_sales from pro_fab_reqn_for_batch_dtls where entry_form=123 and status_active=1 and is_deleted=0 ");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
		if ($row[csf('is_sales')] == 1) {
			$sales_order_ids .= $row[csf('po_id')] . ",";
		}else{
			$order_ids .= $row[csf('po_id')] . ",";
		}
	}

	$sales_order_ids = implode(",",array_unique(explode(",",rtrim($sales_order_ids, ","))));
	if($sales_order_ids != "")
	{
		$salesOrderArr = array();
		$salesOrderData = sql_select("SELECT id,job_no,sales_booking_no,within_group,style_ref_no from fabric_sales_order_mst where id in($sales_order_ids) and status_active = 1 and is_deleted = 0");
		foreach ($salesOrderData as $row)
		{
			if($row[csf('within_group')] ==1)
			{
				$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('sales_booking_no')] . "_".$row[csf('within_group')] . "_".$row[csf('job_no')];
				$sales_booking_nos .= "'".$row[csf('sales_booking_no')] . "',";
			}
			else
			{
				$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('sales_booking_no')] . "_".$row[csf('within_group')] . "_".$row[csf('job_no')];
			}
			$style_no_arr[$row[csf('job_no')]] = $row[csf('style_ref_no')];
		}

		$sales_booking_nos = rtrim($sales_booking_nos, ",");
		/*$po_buyer_arr = sql_select("SELECT a.booking_no, a.buyer_id,b.job_no
		from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_type in(1,4) and a.booking_no in($sales_booking_nos)");
		$po_arr=array();
		foreach ($po_buyer_arr as $po_row)
		{
			$po_arr[$po_row[csf("booking_no")]]["buyer_id"] = $po_row[csf("buyer_id")];
			$po_arr[$po_row[csf("booking_no")]]["job_no"] = $po_row[csf("job_no")];
		}*/

		$booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) and a.booking_no in($sales_booking_nos)
		group by a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping");
		//and A.BOOKING_NO='MF-Fb-22-00130'

        foreach ($booking_details as $po_row)
        {
			$booking_arr[$po_row[csf("booking_no")]]["buyer_id"]          = $po_row[csf("buyer_id")];
			$booking_arr[$po_row[csf("booking_no")]]["job_no"]            = $po_row[csf("job_no")];
			$booking_arr[$po_row[csf("booking_no")]]["int_ref"]           = $po_row[csf("ref_no")];
        }
	}
	else
	{
		$order_ids = rtrim($order_ids, ",");
		if($order_ids != "") {
			$reqnData = sql_select("SELECT a.dtls_id,a.po_id,b.po_number from pro_fab_reqn_po_break a,wo_po_break_down b where a.po_id=b.id and b.id in($order_ids) and a.status_active=1 and a.is_deleted=0");
			foreach ($reqnData as $row)
			{
				$po_number_arr[$row[csf('dtls_id')]][] = $row[csf('po_number')];
				$po_id_arr[$row[csf('dtls_id')]][] = $row[csf('po_id')];
			}
		}
	}


	//-------------------------------------  making Po Number  ------------------------------------------------------

	$po_arr=sql_select("select a.job_no, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where A.ID=B.JOB_ID and b.status_active=1 and b.is_deleted=0");
	foreach ($po_arr as $row)
	{
		$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
		$style_no_arr[$row[csf('job_no')]] = $row[csf('style_ref_no')];
	}

	//Sales order Id-------------------------------------------------------------------------------
	$salesOrderData = sql_select("SELECT id, job_no, sales_booking_no, within_group from fabric_sales_order_mst where status_active = 1 and is_deleted = 0");
	foreach ($salesOrderData as $row)
	{
			$salesOrderArr[$row[csf('id')]] = $row[csf('job_no')];
	}

	$sql = "SELECT id, receive_basis, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks, gsm_weight, dia_width, construction, composition,file_no, grouping, body_part_id, color_type_id, booking_qty, is_sales, customer_buyer from pro_fab_reqn_for_batch_dtls where entry_form=123 and mst_id='$data' and status_active=1 and is_deleted=0";
	$result = sql_select($sql);
	$i = 1;
	foreach ($result as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$totReqQty = $reqn_qnty_array[$key];
		$balance = $row[csf('booking_qty')] - $totReqQty;
		$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
		if($row[csf('is_sales')] == 1)
		{
			$sales_data = explode("_",$salesOrderArr[$row[csf('po_id')]]['job_no']);
			$booking_no = $sales_data[0];
			$within_group = $sales_data[1];
			$job_no = $po_arr[$booking_no]["job_no"];
			// $buyer = ($within_group==1)?$po_arr[$booking_no]["buyer_id"]:$row[csf('buyer_id')];
			$sales_order_no = $sales_data[2];
			if($within_group == 1)
			{
				$buyer = $po_arr[$booking_no]["buyer_id"];
				$int_ref=$po_arr[$row[csf('program_booking_pi_no')]]["int_ref"];
			}
			else
			{
				$buyer = $row[csf('buyer_id')];
				$customer_buyer = $row[csf('customer_buyer')];
				$int_ref="";
			}
		}
		else
		{
			$job_no = $row[csf('job_no')];
			$booking_no = $row[csf('program_booking_pi_id')];
			$buyer = $row[csf('buyer_id')];
		}
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
			<td width="30" align="center"><? echo $i; ?></td>
			<td width="60" style="word-break:break-all; text-align: center;"><? echo $buyer_arr[$buyer]; ?></td>
			<td width="100" style="word-break:break-all; text-align: center;"><? echo $cbuyer_arr[$customer_buyer]; ?></td>
			<td width="120" style="word-break:break-all;text-align: center;" id="job<? echo $i; ?>"><? echo $row[csf('job_no')]; ?></td>
			<td width="100" style="word-break:break-all;text-align: center;" id="styleRef<? echo $i; ?>"><? echo $style_no_arr[$row[csf('job_no')]]; ?></td>
			<td width="80" style="word-break:break-all;"><? echo $row[csf('file_no')]; ?></td>
			<td width="80" id="gsm<? echo $i; ?>"><p><? echo $row[csf('grouping')]; ?></p></td>
			<td width="100" align="center">
				<p><?
					/*if($row[csf('is_sales')] == 1) {
						echo $sales_order_no;
					}else{
						echo implode(',', array_unique($po_number_arr[$row[csf('id')]]));
					}*/

						if($row[csf('is_sales')]==1) //for sales order
						{
							foreach (explode(",", $row[csf('po_id')]) as $value)
							{

								$po_array[$salesOrderArr[$value]]=$salesOrderArr[$value];
							}
						}
						else
						{
							foreach (explode(",", $row[csf('po_id')]) as $value)
							{
								$po_array[$po_number_arr[$value]]=$po_number_arr[$value];
							}
						}

						echo implode(",", array_unique($po_array));
						unset($po_array);
					?>
				</p>
			</td>
			<td width="100" align="center"><p><? echo $row[csf('program_booking_pi_no')]; ?></p></td>
			<td width="150" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
			<td width="100" align="center"><p><? echo $color_type[$row[csf('color_type_id')]]; ?></p></td>
			<td width="100"><p><? echo $row[csf('construction')]; ?></p></td>
			<td width="100"><p><? echo $row[csf('composition')]; ?></p></td>
			<td width="50" align="center"><? echo $row[csf('gsm_weight')]; ?></td>
			<td width="50" align="center"><? echo $row[csf('dia_width')]; ?></td>
			<td width="100"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
			<td width="80" align="right" id="bookQty<? echo $i; ?>"><? echo number_format($row[csf('booking_qty')], 2,'.',''); ?></td>
			<td width="80" align="right" id="totReqQty<? echo $i; ?>"><? echo number_format($totReqQty, 2,'.',''); ?></td>
			<td width="80" align="right" id="totBalQty<? echo $i; ?>"><? echo number_format($balance, 2,'.',''); ?></td>
			<td width="80" align="center"><input type="text" value="<? echo number_format($row[csf('reqn_qty')], 2,'.',''); ?>"
				class="text_boxes_numeric" style="width:65px"
				id="reqsnQty<? echo $i; ?>" name="reqsnQty[]" onKeyUp="fnc_count_total_qty();fnc_check_balance_qty(<? echo $i ?>);"/>
				<input type="hidden" id="previous_reqsnQty<? echo $i; ?>" value="<? echo number_format($row[csf('reqn_qty')], 2,'.',''); ?>">
			</td>
			<td width="100">
				<input type="text" value="<? echo $row[csf('remarks')]; ?>" class="text_boxes" style="width:90%"
				id="remarks<? echo $i; ?>" name="remarks[]"/>

				<input type="hidden" value="<? echo $row[csf('buyer_id')]; ?>" id="buyerId<? echo $i; ?>"
				name="buyerId[]"/>
				<input type="hidden" value="<? echo $row[csf('customer_buyer')]; ?>" id="cbuyerId<? echo $i; ?>"
				name="cbuyerId[]"/>
				<input type="hidden" value="<? echo implode(',', array_unique($po_id_arr[$row[csf('id')]])); ?>"
				id="poId<? echo $i; ?>" name="poId[]"/>
				<input type="hidden" value="<? echo $row[csf('color_type_id')]; ?>" id="colorTypeId<? echo $i; ?>"
				name="colorTypeId[]"/>
				<input type="hidden" value="<? echo $row[csf('color_id')]; ?>" id="colorId<? echo $i; ?>"
				name="colorId[]"/>
				<input type="hidden" value="<? echo $row[csf('body_part_id')]; ?>" id="bodyPartId<? echo $i; ?>"
				name="bodyPartId[]"/>
				<input type="hidden" value="<? echo $row[csf('booking_qty')]; ?>" id="bookintQty<? echo $i; ?>"
				name="bookintQty[]"/>
				<input type="hidden" value="<? echo $row[csf('id')]; ?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>
				<input type="hidden" value="<? echo $row[csf('is_sales')] * 1; ?>" id="isSales<? echo $i; ?>" name="isSales[]"/>
			</td>
			<td width="30"><input type="button" value="-" class="formbuttonplasminus" style="width:30px" id="decrease1" name="decrease[]" /></td>
		</tr>
		<?
		$totalReqQty+=$row[csf('booking_qty')];
		$grandtotReqnQty+=$totReqQty;
		$totBalance+=$balance;
		$tot_reqNewQty+=$row[csf('reqn_qty')];
		$i++;
	}
	?>
	<!--  <tr>
	<td width="80" colspan="14" align="right"><strong>Total-1</strong></td>
	<td width="80" align="right"><strong><? //echo number_format($totalReqQty,2); ?></strong></td>
	<td width="80" align="right"><strong><? //echo number_format($grandtotReqnQty,2); ?></strong></td>
	<td width="80" align="right"><strong><? //echo number_format($totBalance,2); ?></strong></td>
	<td width="80" align="right"><strong><input type="text" class="text_boxes_numeric" style="width:65px" id="total_blnc_qty_td_id" name="" value="<? //echo number_format($tot_reqNewQty,2); ?>"  readonly /></strong></td>
	</tr> -->
	<?

	exit();
}

if ($action == "print_fab_req_for_batch")
{
	extract($_REQUEST);
	$ex_data = explode('*', $data);

	if($ex_data[1]=="")
	{
		echo "Data Not Saved";die;
	}
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$sql = "SELECT id, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks,gsm_weight,dia_width,construction,composition,file_no,grouping,body_part_id,color_type_id,booking_qty, is_sales, customer_buyer from pro_fab_reqn_for_batch_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $vals)
	{
		$all_booking_nos_arr[$vals[csf("program_booking_pi_no")]]=$vals[csf("program_booking_pi_no")];
		$all_po_id_arr[$vals[csf("po_id")]]=$vals[csf("po_id")];
		$all_composition_arr[$vals[csf("composition")]]=$vals[csf("composition")];
		// $job_no_arr[$vals[csf("job_no")]] = $vals[csf("job_no")];
	}
	$job_nos="'".implode("','", $job_no_arr)."'";
	$all_booking_nos = "'" . implode("','", $all_booking_nos_arr) . "'";
	$all_po_ids=implode(",", $all_po_id_arr);
	$all_composition_ids="'".implode("','", $all_composition_arr)."'";

	//$transferData = sql_select("select to_order_id,from_order_id,b.from_program from inv_item_transfer_mst a,INV_ITEM_TRANSFER_DTLS b where a.id=b.mst_id and to_order_id in($all_po_ids)");
	$booking_no_arrs=array();
	$booking_data = sql_select("select b.BOOKING_NO,a.STYLE_REF_NO  from sample_development_mst a, wo_non_ord_samp_booking_dtls b  where a.id=b.style_id and a.status_active=1 and b.status_active=1 and a.ENTRY_FORM_ID=203 and b.BOOKING_NO in($all_booking_nos)");

	foreach ($booking_data as $value) {

		$booking_no_arrs[$value[csf("BOOKING_NO")]]=$value[csf("STYLE_REF_NO")];

	}

	//for Style Ref
    $sqlBooking = "SELECT b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, c.STYLE_REF_NO FROM wo_po_break_down a, wo_booking_dtls b, wo_po_details_master c  where a.id = b.po_break_down_id AND a.job_no_mst=c.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and b.BOOKING_NO in($all_booking_nos)";
    // echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $booking_no_arrs = array();
    foreach($sqlBookingRslt as $row)
    {
        $booking_no_arrs[$row['BOOKING_NO']] = $row['STYLE_REF_NO'];
    }
    unset($sqlBookingRslt);



	if(!empty($all_po_id_arr))
	{
		$poidCond =$poId=$to_po_id_cond=$to_po_id="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$poId.=" id in(".implode(",",$chunk_arr).") or ";
				$to_po_id.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$poidCond.=" and (".chop($poId,'or ').")";
			$to_po_id_cond.=" and (".chop($to_po_id,'or ').")";
		}
		else
		{
			$poidCond=" and id in($all_po_ids)";
			$to_po_id_cond=" and a.to_order_id in($all_po_ids)";
		}

		$po_arr=sql_select("select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 $poidCond");
		foreach ($po_arr as $row)
		{
			$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
		}

		$transferData = sql_select("select a.to_order_id,a.from_order_id,b.from_program from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id $to_po_id_cond");
		foreach ($transferData as $row) {
		$transfer_po_arr[$row[csf('from_order_id')]]= $row[csf('to_order_id')];
		$from_po_id_arr[$row[csf("from_program")]]=$row[csf("from_program")];
		}
		$all_from_po_ids="'".implode("','", $from_po_id_arr)."'";

	}

	$reqn_qnty_array = array();
	$sales_id="";
	
	$reqnData = sql_select("select reqn_qty,color_type_id,color_id,program_booking_pi_no,po_id,job_no,buyer_id, body_part_id,gsm_weight, dia_width, construction, composition, grouping,file_no from pro_fab_reqn_for_batch_dtls where entry_form=123 and status_active=1 and is_deleted=0 and program_booking_pi_no in ($all_booking_nos)");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
		if($row[csf('po_id')]!="")
		{
			$sales_id .= $row[csf('po_id')] . ",";
		}
	}
	//$sales_ids = rtrim($sales_id,",");

	if($sales_id!="")
	{
		$all_sales_id=implode(",",array_unique(explode(',',chop($sales_id,','))));

		$sales_id=array_unique(explode(',',chop($sales_id,',')));
		$salesidCond = "";
		if($db_type==2 && count($sales_id)>999)
		{
			$all_sale_id_chunk=array_chunk($sales_id,999) ;
			foreach($all_sale_id_chunk as $chunk_arr)
			{
				$salesId.=" id in(".implode(",",$chunk_arr).") or ";
			}

			$salesidCond.=" and (".chop($salesId,'or ').")";
		}
		else
		{
			$salesidCond=" and id in($all_sales_id)";
		}

		$salesOrderArr = array();
		$salesOrderData = sql_select("SELECT id,job_no,sales_booking_no,within_group from fabric_sales_order_mst  where status_active=1  $salesidCond   group by id,job_no,sales_booking_no,within_group ");
		$sales_wise_color_range=array();
		foreach ($salesOrderData as $row)
		{
			$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('job_no')] . "_" . $row[csf('sales_booking_no')] . "_".$row[csf('within_group')];
	
		}
	}

	$color_range_sql="SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range, null as po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.booking_no in($all_booking_nos) group by  a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range
	union all
	SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,PPL_PLANNING_ENTRY_PLAN_DTLS c
	where a.id=b.mst_id and b.id=c.DTLS_ID and a.status_active=1 and b.status_active=1 and b.id in($all_from_po_ids)
	and c.status_active=1 group by a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id ";

	foreach ( sql_select($color_range_sql) as $row)
	{
		$fab_desc=explode(",",trim($row[csf('fabric_desc')]));
		foreach( array_unique(explode(",",$row[csf('color_id')])) as $col_id)
		{
			if($sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id])
			$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id].=','.$color_range[$row[csf('color_range')]];
			else
				$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id]=$color_range[$row[csf('color_range')]];

			if($row[csf('po_id')] != "")
			{
				$po_id=$transfer_po_arr[$row[csf('po_id')]];
				if($tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])])
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])].=','.$color_range[$row[csf('color_range')]];
				else
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])]=$color_range[$row[csf('color_range')]];
			}
		}

	}
	//echo "<pre>";print_r($tr_sales_wise_color_range);//die;
	//echo "</pre>";

	$sql_mst = "Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst);
	?>
	<div style="width:1910px; border:1px solid #999">
		<table width="95%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%'/>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="center">
						<tr class="form_caption">
							<td align="center" style="font-size:18px">
							<strong><? echo $company_library[$ex_data[0]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:14px"><strong>Unit
								: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center"
							style="font-size:14px"><? echo show_company($ex_data[0], '', ''); ?> </td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing="0" align="" border="0">
			<tr>
				<td width="130"><strong>Requisition No :</strong></td>
				<td width="175"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
				<td width="130"><strong>Requisition Date : </strong></td>
				<td width="175px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
				<td width="130">&nbsp;</td>
				<td width="175">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table align="left" cellspacing="0" width="99%" border="1" rules="all" class="rpt_table">
			<?
			$i = 1;
			$totCurrRed_Qty = 0;
			foreach ($result as $row)
			{
				$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
				$totReqQty = $reqn_qnty_array[$key];
				$balance = $row[csf('booking_qty')] - $totReqQty;
				if($row[csf('is_sales')] == 1)
				{
					$sales_data = explode("_",$salesOrderArr[$row[csf('po_id')]]['job_no']);
					$job_no = $sales_data[0];
					$booking_no = $sales_data[1];
					$within_group = $sales_data[2];
					//$buyer = ($within_group==1)?$po_buyer_arr[$booking_no]:$row[csf('buyer_id')];
					$buyer = $row[csf('buyer_id')];
					$cbuyer = $row[csf('customer_buyer')];
				}
				else
				{
					$job_no = $row[csf('job_no')];
					$booking_no = $row[csf('program_booking_pi_no')];
					$buyer = $row[csf('buyer_id')];
				}

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if ($i==1){
				?>
				<thead bgcolor="#dddddd" style="font-size:18px">
					<th width="30">Sl</th>
					<th width="60">Buyer</th>
					<th width="60">Customer <br> Buyer</th>
					<th width="120"><?php echo ($row[csf('is_sales')] == 1) ? "Sales Order No" : "Job No" ?></th>
					<?php if ($row[csf('is_sales')] != 1) { ?>
					<th width="80">File No</th>
					<!-- <th width="80">Ref. No</th> -->
					<th width="100">Order No</th>
					<?php } ?>
					<th width="80">Ref. No/<br>Style Ref. </th>
					<th width="100">Booking No</th>
					<th width="140">Body Part</th>
					<th width="100">Color Type</th>
					<th width="140">Construction</th>
					<th width="140">Composition</th>
					<th width="50">F. GSM</th>
					<th width="50">F. Dia</th>
					<th width="100">Color Range</th>
					<th width="100">Color/Code</th>
					<th width="80">Book Qty.</th>
					<th width="80">Total Reqn. Qty.</th>
					<th width="80">Balance</th>
					<th width="80">Current Reqn. Qty.</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?php } ?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:15px">
						<td align="center"><? echo $i; ?></td>
						<td align="center"><p><? echo $buyer_arr[$buyer]; ?></p></td>
						<td align="center"><p><? echo $cbuyer_arr[$cbuyer]; ?></p></td>
						<td align="center"><p><? echo $job_no; ?></p></td>
						<?php if($row[csf('is_sales')] != 1){ ?>
						<td><p><? echo $row[csf('file_no')]; ?></p></td>
						<!-- <td><? //echo $row[csf('grouping')]; ?></td> -->
						<td><div style="word-wrap:break-word; width:100px"><?
							foreach (explode(",", $row[csf('po_id')]) as $value)
							{
								$po_array[$po_number_arr[$value]]=$po_number_arr[$value];
							}
							echo implode(",", array_unique($po_array));
							unset($po_array);
							?></div>
						</td>
						<?php } ?>
						<td><? echo "R:".$row[csf('grouping')]."<br>"."S:".$booking_no_arrs[$row[csf("program_booking_pi_no")]]; ?></td>
						<td align="center"><? echo $row[csf('program_booking_pi_no')]; ?></td>
						<td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
						<td><? echo $row[csf('construction')]; ?></td>
						<td><? echo $row[csf('composition')]; ?></td>
						<td align="center"><? echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? echo $row[csf('dia_width')]; ?></td>
						<td align="right" title="<? echo trim($row[csf('program_booking_pi_no')]).'='.$row[csf('body_part_id')].'='.$row[csf('color_type_id')].'='.trim($row[csf('composition')]).'='.$row[csf("color_id")]; ?>">
							<?
							 //echo "booking ".trim($row[csf('program_booking_pi_no')])."body= ".$row[csf('body_part_id')]."type ".$row[csf('color_type_id')]."compo ".trim($row[csf('composition')])."color ".$row[csf("color_id")];
							 $color_range = $sales_wise_color_range[trim($row[csf('program_booking_pi_no')])][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])][$row[csf("color_id")]];
							 if($color_range =="")
							 {
								$color_range = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])];
							 }
							 echo $color_range = implode(",",array_unique(explode(",",$color_range)));
							 ?>

						 </td>
						<td align="right"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td align="right"><? echo $row[csf('booking_qty')]; ?></td>
						<td align="right"><? echo number_format($totReqQty, 2); ?></td>
						<td align="right"><? echo $balances = number_format($balance, 2); ?></td>
						<td align="right"><? echo number_format($row[csf('reqn_qty')], 2); ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$totBookingQty += $row[csf('booking_qty')];
					$totCurrQty += $totReqQty;
					$totRenqQty += $totReqQty;
					$balance_tot += $balance;
					$totCurrRed_Qty += $row[csf('reqn_qty')];
					$i++;
			}
			?>
			</tbody>
			<tfoot bgcolor="#dddddd" style="font-size:13px">
				<tr>
					<td colspan="<? echo ($result[0][csf('is_sales')] == 1) ? 14 : 16; ?>" align="right">
						<strong>Total :</strong>
					</td>

					<td align="right"><? echo number_format($totBookingQty, 2); ?></td>
					<td align="right"><? echo number_format($totCurrQty, 2); ?></td>
					<td align="right"><? echo number_format($balance_tot, 2); ?></td>
					<td align="right"><? echo number_format($totCurrRed_Qty, 2); ?></td>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<br>
		<strong style="font: bold 12px tahoma;">Special Instruction</strong>
		<table border="1" rules="all" cellpadding="3" style="font-size: 12px;">
			<tr bgcolor="#CCCCCC">
				<td align="center"><strong>Sl</strong></td>
				<td><strong>Terms</strong></td>
			</tr>
			<?
			$reqn_number=$dataArray[0][csf('reqn_number')];
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$reqn_number'");
			if (count($data_array) > 0)
			{
				$i = 0;
				foreach ($data_array as $row)
				{
					$i++;
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td><? echo $row[csf('terms')]; ?></td>
					</tr>
					<?
				}
			}
	        ?>
	    </tbody>
		<?
		echo signature_table(93, $ex_data[0], "1460px");
		?>
	</div>
	<?
	exit();
}

if ($action == "print_fab_req_for_batch_5") // Print 5
{
	extract($_REQUEST);
	$ex_data = explode('*', $data);

	if($ex_data[1]=="")
	{
		echo "Data Not Saved";die;
	}
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$sql = "SELECT id, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks, gsm_weight, dia_width, construction, composition, file_no, grouping, body_part_id, color_type_id, booking_qty, is_sales, customer_buyer from pro_fab_reqn_for_batch_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $vals)
	{
		$all_booking_nos_arr[$vals[csf("program_booking_pi_no")]]=$vals[csf("program_booking_pi_no")];
		$all_po_id_arr[$vals[csf("po_id")]]=$vals[csf("po_id")];
		$all_composition_arr[$vals[csf("composition")]]=$vals[csf("composition")];
		// $job_no_arr[$vals[csf("job_no")]] = $vals[csf("job_no")];
	}
	$job_nos="'".implode("','", $job_no_arr)."'";
	$all_booking_nos = "'" . implode("','", $all_booking_nos_arr) . "'";
	$all_po_ids=implode(",", $all_po_id_arr);
	$all_composition_ids="'".implode("','", $all_composition_arr)."'";

	//$transferData = sql_select("select to_order_id,from_order_id,b.from_program from inv_item_transfer_mst a,INV_ITEM_TRANSFER_DTLS b where a.id=b.mst_id and to_order_id in($all_po_ids)");
	$booking_no_arrs=array();
	$booking_data = sql_select("select b.BOOKING_NO,a.STYLE_REF_NO  from sample_development_mst a, wo_non_ord_samp_booking_dtls b  where a.id=b.style_id and a.status_active=1 and b.status_active=1 and a.ENTRY_FORM_ID=203 and b.BOOKING_NO in($all_booking_nos)");

	foreach ($booking_data as $value) {

		$booking_no_arrs[$value[csf("BOOKING_NO")]]=$value[csf("STYLE_REF_NO")];

	}

	//for Style Ref
    $sqlBooking = "SELECT b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, c.STYLE_REF_NO FROM wo_po_break_down a, wo_booking_dtls b, wo_po_details_master c  where a.id = b.po_break_down_id AND a.job_no_mst=c.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and b.BOOKING_NO in($all_booking_nos)";
    // echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $booking_no_arrs = array();
    foreach($sqlBookingRslt as $row)
    {
        $booking_no_arrs[$row['BOOKING_NO']] = $row['STYLE_REF_NO'];
    }
    unset($sqlBookingRslt);



	if(!empty($all_po_id_arr))
	{
		$poidCond =$poId=$to_po_id_cond=$to_po_id="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$poId.=" id in(".implode(",",$chunk_arr).") or ";
				$to_po_id.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$poidCond.=" and (".chop($poId,'or ').")";
			$to_po_id_cond.=" and (".chop($to_po_id,'or ').")";
		}
		else
		{
			$poidCond=" and id in($all_po_ids)";
			$to_po_id_cond=" and a.to_order_id in($all_po_ids)";
		}

		$po_arr=sql_select("select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 $poidCond");
		foreach ($po_arr as $row)
		{
			$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
		}

		$transferData = sql_select("select a.to_order_id,a.from_order_id,b.from_program from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id $to_po_id_cond");
		foreach ($transferData as $row) {
		$transfer_po_arr[$row[csf('from_order_id')]]= $row[csf('to_order_id')];
		$from_po_id_arr[$row[csf("from_program")]]=$row[csf("from_program")];
		}
		$all_from_po_ids="'".implode("','", $from_po_id_arr)."'";
	}

	$reqn_qnty_array = array();
	$sales_id="";
	
	$reqnData = sql_select("SELECT reqn_qty,color_type_id,color_id,program_booking_pi_no,po_id,job_no,buyer_id, body_part_id,gsm_weight, dia_width, construction, composition, grouping,file_no from pro_fab_reqn_for_batch_dtls where entry_form=123 and status_active=1 and is_deleted=0 and program_booking_pi_no in ($all_booking_nos)");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
		if($row[csf('po_id')]!="")
		{
			$sales_id .= $row[csf('po_id')] . ",";
		}
	}
	//$sales_ids = rtrim($sales_id,",");

	if($sales_id!="")
	{
		$all_sales_id=implode(",",array_unique(explode(',',chop($sales_id,','))));

		$sales_id=array_unique(explode(',',chop($sales_id,',')));
		$salesidCond = "";
		if($db_type==2 && count($sales_id)>999)
		{
			$all_sale_id_chunk=array_chunk($sales_id,999) ;
			foreach($all_sale_id_chunk as $chunk_arr)
			{
				$salesId.=" a.id in(".implode(",",$chunk_arr).") or ";
			}

			$salesidCond.=" and (".chop($salesId,'or ').")";
		}
		else
		{
			$salesidCond=" and a.id in($all_sales_id)";
		}

		$lib_fabric_composition=return_library_array( "SELECT id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");

		$determination_sql = sql_select("SELECT a.id, a.construction, b.copmposition_id,b.type_id, b.percent, a.fabric_composition_id 
		from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id");
		$f_comp_arr=array();
		foreach ($determination_sql as $d_row) 
		{
			$f_comp_arr[$d_row[csf("id")]]=$lib_fabric_composition[$d_row[csf("fabric_composition_id")]];
		}

		/*$salesOrderArr = array();
		$salesOrderData = sql_select("SELECT id,job_no,sales_booking_no,within_group from fabric_sales_order_mst  where status_active=1  $salesidCond   group by id,job_no,sales_booking_no,within_group ");
		$sales_wise_color_range=array();
		foreach ($salesOrderData as $row)
		{
			$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('job_no')] . "_" . $row[csf('sales_booking_no')] . "_".$row[csf('within_group')];	
		}*/

		$salesOrderData=sql_select("SELECT a.id, a.job_no, a.sales_booking_no, a.within_group, b.determination_id, b.body_part_id, b.color_type_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id
		from fabric_sales_order_mst a, fabric_sales_order_dtls b 
		where a.id=b.mst_id  $salesidCond and a.status_active=1 and b.status_active=1 
		group by a.id, a.job_no, a.sales_booking_no, a.within_group, b.determination_id, b.body_part_id, b.color_type_id, b.gsm_weight, b.fabric_desc, b.dia, b.color_id");
		foreach ($salesOrderData as $key => $row) 
		{
			$fabric_desc_ar=explode(",", $row[csf("fabric_desc")]);
			$fab_cons=$fabric_desc_ar[0];
			$fso_comp_arr[$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("color_type_id")]][$fab_cons][$row[csf("gsm_weight")]][$row[csf("dia")]][$row[csf("color_id")]]=$f_comp_arr[$row[csf("determination_id")]];

			$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('job_no')] . "_" . $row[csf('sales_booking_no')] . "_".$row[csf('within_group')];	
		}
		// echo "<pre>";print_r($fso_comp_arr);
	}

	$color_range_sql="SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range, null as po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.booking_no in($all_booking_nos) group by  a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range
	union all
	SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,PPL_PLANNING_ENTRY_PLAN_DTLS c
	where a.id=b.mst_id and b.id=c.DTLS_ID and a.status_active=1 and b.status_active=1 and b.id in($all_from_po_ids)
	and c.status_active=1 group by a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id ";

	foreach ( sql_select($color_range_sql) as $row)
	{
		$fab_desc=explode(",",trim($row[csf('fabric_desc')]));
		foreach( array_unique(explode(",",$row[csf('color_id')])) as $col_id)
		{
			if($sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id])
			$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id].=','.$color_range[$row[csf('color_range')]];
			else
				$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id]=$color_range[$row[csf('color_range')]];

			if($row[csf('po_id')] != "")
			{
				$po_id=$transfer_po_arr[$row[csf('po_id')]];
				if($tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])])
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])].=','.$color_range[$row[csf('color_range')]];
				else
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])]=$color_range[$row[csf('color_range')]];
			}
		}
	}
	//echo "<pre>";print_r($tr_sales_wise_color_range);//die;
	//echo "</pre>";

	$sql_mst = "Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst);
	?>
	<div style="width:1910px; border:1px solid #999">
		<table width="95%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%'/>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="center">
						<tr class="form_caption">
							<td align="center" style="font-size:18px">
							<strong><? echo $company_library[$ex_data[0]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:14px"><strong>Unit
								: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center"
							style="font-size:14px"><? echo show_company($ex_data[0], '', ''); ?> </td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing="0" align="" border="0">
			<tr>
				<td width="20"><strong>Requisition No :</strong></td>
				<td width="30px"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
				<td width="20"><strong>Requisition Date : </strong></td>
				<td width="30px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
				<td width="440">&nbsp;</td>
				<td width="440">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table align="left" cellspacing="0" width="99%" border="1" rules="all" class="rpt_table">
			<?
			$i = 1;
			$totCurrRed_Qty = 0;
			foreach ($result as $row)
			{
				$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
				$totReqQty = $reqn_qnty_array[$key];
				$balance = $row[csf('booking_qty')] - $totReqQty;
				if($row[csf('is_sales')] == 1)
				{
					$sales_data = explode("_",$salesOrderArr[$row[csf('po_id')]]['job_no']);
					$job_no = $sales_data[0];
					$booking_no = $sales_data[1];
					$within_group = $sales_data[2];
					//$buyer = ($within_group==1)?$po_buyer_arr[$booking_no]:$row[csf('buyer_id')];
					$buyer = $row[csf('buyer_id')];
					$cbuyer = $row[csf('customer_buyer')];
				}
				else
				{
					$job_no = $row[csf('job_no')];
					$booking_no = $row[csf('program_booking_pi_no')];
					$buyer = $row[csf('buyer_id')];
				}
				$title=$row[csf("po_id")].']['.$row[csf("body_part_id")].']['.$row[csf("color_type_id")].']['.$row[csf('construction')].']['.$row[csf("gsm_weight")].']['.$row[csf("dia_width")].']['.$row[csf("color_id")];
				$fabric_composition=$fso_comp_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("color_type_id")]][$row[csf('construction')]][$row[csf("gsm_weight")]][$row[csf("dia_width")]][$row[csf("color_id")]];

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if ($i==1){
				?>
				<thead bgcolor="#dddddd" style="font-size:18px">
					<th width="30">Sl</th>
					<th width="60">Buyer</th>
					<th width="60">Customer <br> Buyer</th>
					<th width="60">Booking Type</th>
					<th width="120"><?php echo ($row[csf('is_sales')] == 1) ? "Sales Order No" : "Job No" ?></th>
					<?php if ($row[csf('is_sales')] != 1) { ?>
					<th width="80">File No</th>
					<!-- <th width="80">Ref. No</th> -->
					<th width="100">Order No</th>
					<?php } ?>
					<th width="80">Ref. No/<br>Style Ref. </th>
					<th width="100">Booking No</th>
					<th width="140">Body Part</th>
					<th width="100">Color Type</th>
					<th width="140">Construction</th>
					<th width="140">Composition</th>
					<th width="50">F. GSM</th>
					<th width="50">F. Dia</th>
					<th width="100">Color Range</th>
					<th width="100">Color/Code</th>
					<th width="80">Book Qty.</th>
					<th width="80">Total Reqn. Qty.</th>
					<th width="80">Balance</th>
					<th width="80">Current Reqn. Qty.</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?php } ?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:15px">
						<td align="center"><? echo $i; ?></td>
						<td align="center"><p><? echo $buyer_arr[$buyer]; ?></p></td>
						<td align="center"><p><? echo $cbuyer_arr[$cbuyer]; ?></p></td>
						<td align="center"><p><?
						$booking_number= $row[csf('program_booking_pi_no')];
						$booking_type=return_field_value("is_short"," wo_booking_mst","booking_no='$booking_number' and is_deleted=0 and status_active=1");
						if($booking_type==1){
							echo "Short";
						}
						if($booking_type==2){
							echo "Main";
						} 
						
						//  echo "TBA"; 
						 ?></p></td>
						<td align="center"><p><? echo $job_no; ?></p></td>
						<?php if($row[csf('is_sales')] != 1){ ?>
						<td><p><? echo $row[csf('file_no')]; ?></p></td>
						<!-- <td><? //echo $row[csf('grouping')]; ?></td> -->
						<td><div style="word-wrap:break-word; width:100px"><?
							foreach (explode(",", $row[csf('po_id')]) as $value)
							{
								$po_array[$po_number_arr[$value]]=$po_number_arr[$value];
							}
							echo implode(",", array_unique($po_array));
							unset($po_array);
							?></div>
						</td>
						<?php } ?>
						<td><? echo "R:".$row[csf('grouping')]."<br>"."S:".$booking_no_arrs[$row[csf("program_booking_pi_no")]]; ?></td>
						<td align="center"><? echo $row[csf('program_booking_pi_no')]; ?></td>
						<td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
						<td><? echo $row[csf('construction')]; ?></td>
						<td title="<?=$title;?>"><? echo $fabric_composition;//$row[csf('composition')]; ?></td>
						<td align="center"><? echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? echo $row[csf('dia_width')]; ?></td>
						<td align="right" title="<? echo trim($row[csf('program_booking_pi_no')]).'='.$row[csf('body_part_id')].'='.$row[csf('color_type_id')].'='.trim($row[csf('composition')]).'='.$row[csf("color_id")]; ?>">
							<?
							 //echo "booking ".trim($row[csf('program_booking_pi_no')])."body= ".$row[csf('body_part_id')]."type ".$row[csf('color_type_id')]."compo ".trim($row[csf('composition')])."color ".$row[csf("color_id")];
							 $color_range = $sales_wise_color_range[trim($row[csf('program_booking_pi_no')])][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])][$row[csf("color_id")]];
							 if($color_range =="")
							 {
								$color_range = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])];
							 }
							 echo $color_range = implode(",",array_unique(explode(",",$color_range)));
							 ?>

						 </td>
						<td align="right"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td align="right"><? echo $row[csf('booking_qty')]; ?></td>
						<td align="right"><? echo number_format($totReqQty, 2); ?></td>
						<td align="right"><? echo $balances = number_format($balance, 2); ?></td>
						<td align="right"><? echo number_format($row[csf('reqn_qty')], 2); ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$totBookingQty += $row[csf('booking_qty')];
					$totCurrQty += $totReqQty;
					$totRenqQty += $totReqQty;
					$balance_tot += $balance;
					$totCurrRed_Qty += $row[csf('reqn_qty')];
					$i++;
			}
			?>
			</tbody>
			<tfoot bgcolor="#dddddd" style="font-size:13px">
				<tr>
					<td colspan="<? echo ($result[0][csf('is_sales')] == 1) ? 15 : 17; ?>" align="right">
						<strong>Total :</strong>
					</td>

					<td align="right"><? echo number_format($totBookingQty, 2); ?></td>
					<td align="right"><? echo number_format($totCurrQty, 2); ?></td>
					<td align="right"><? echo number_format($balance_tot, 2); ?></td>
					<td align="right"><? echo number_format($totCurrRed_Qty, 2); ?></td>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<br>
		<?
		$reqn_number=$dataArray[0][csf('reqn_number')];
		$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$reqn_number'");
		if (count($data_array) > 0)
		{
			?>
			<strong style="font: bold 12px tahoma;">Special Instruction</strong>
			<table border="1" rules="all" cellpadding="3" style="font-size: 12px;">
				<tr bgcolor="#CCCCCC">
					<td align="center"><strong>Sl</strong></td>
					<td><strong>Terms</strong></td>
				</tr>
				<?
					$i = 0;
					foreach ($data_array as $row)
					{
						$i++;
						?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td><? echo $row[csf('terms')]; ?></td>
						</tr>
						<?
					}
		        ?>
		    </tbody>
			<?
		}
		echo signature_table(93, $ex_data[0], "1460px");
		?>
	</div>
	<?
	exit();
}

if ($action == "print_fab_req_for_batch_3")
{
	extract($_REQUEST);
	$ex_data = explode('*', $data);
	if($ex_data[1]=="")
	{
		echo "Data Not Saved";die;
	}
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$sql = "SELECT id, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks,gsm_weight,dia_width,construction,composition,file_no,grouping,body_part_id,color_type_id,booking_qty, is_sales, customer_buyer from pro_fab_reqn_for_batch_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$result = sql_select($sql);
	foreach($result as $vals)
	{
		$all_booking_nos_arr[$vals[csf("program_booking_pi_no")]]=$vals[csf("program_booking_pi_no")];
		$all_po_id_arr[$vals[csf("po_id")]]=$vals[csf("po_id")];
		$all_composition_arr[$vals[csf("composition")]]=$vals[csf("composition")];
	}
	$all_booking_nos="'".implode("','", $all_booking_nos_arr)."'";
	$all_po_ids=implode(",", $all_po_id_arr);
	$all_composition_ids="'".implode("','", $all_composition_arr)."'";

	$reqn_qnty_array = array();
	$sales_id="";
	$reqnData = sql_select("select reqn_qty,color_type_id,color_id,program_booking_pi_no,po_id,job_no,buyer_id, body_part_id,gsm_weight, dia_width, construction, composition, grouping,file_no from pro_fab_reqn_for_batch_dtls where entry_form=123 and status_active=1 and is_deleted=0 and program_booking_pi_no in($all_booking_nos)");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
		if($row[csf('po_id')]!="")
		{
			$sales_id .= $row[csf('po_id')] . ",";
		}
	}
	//$sales_ids = rtrim($sales_id,",");
	// echo "<pre>";print_r($reqn_qnty_array);

	if(!empty($all_po_id_arr))
	{
		$poidCond =$poId=$to_po_id_cond=$to_po_id="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$poId.=" id in(".implode(",",$chunk_arr).") or ";
				$to_po_id.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$poidCond.=" and (".chop($poId,'or ').")";
			$to_po_id_cond.=" and (".chop($to_po_id,'or ').")";
		}
		else
		{
			$poidCond=" and id in($all_po_ids)";
			$to_po_id_cond=" and a.to_order_id in($all_po_ids)";
		}

		$po_arr=sql_select("select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 $poidCond");
		foreach ($po_arr as $row)
		{
			$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
		}

		$transferData = sql_select("select a.to_order_id,a.from_order_id,b.from_program from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id $to_po_id_cond");
		foreach ($transferData as $row) {
		$transfer_po_arr[$row[csf('from_order_id')]]= $row[csf('to_order_id')];
		$from_po_id_arr[$row[csf("from_program")]]=$row[csf("from_program")];
		}
		$all_from_po_ids="'".implode("','", $from_po_id_arr)."'";
	}

	if($sales_id!="")
	{
		$all_sales_id=implode(",",array_unique(explode(',',chop($sales_id,','))));

		$sales_id=array_unique(explode(',',chop($sales_id,',')));
		$salesidCond = "";
		if($db_type==2 && count($sales_id)>999)
		{
			$all_sale_id_chunk=array_chunk($sales_id,999) ;
			foreach($all_sale_id_chunk as $chunk_arr)
			{
				$salesId.=" id in(".implode(",",$chunk_arr).") or ";
			}

			$salesidCond.=" and (".chop($salesId,'or ').")";
		}
		else
		{
			$salesidCond=" and id in($all_sales_id)";
		}

		$salesOrderArr = array();
		$salesOrderData = sql_select("SELECT id,job_no,sales_booking_no,within_group from fabric_sales_order_mst  where status_active=1  $salesidCond group by id,job_no,sales_booking_no,within_group ");
		$sales_wise_color_range=array();
		foreach ($salesOrderData as $row)
		{
			$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('job_no')] . "_" . $row[csf('sales_booking_no')] . "_".$row[csf('within_group')];
	
		}
	}

	$color_range_sql="SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range, null as po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.booking_no in($all_booking_nos) group by  a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range
	union all
	SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,PPL_PLANNING_ENTRY_PLAN_DTLS c
	where a.id=b.mst_id and b.id=c.DTLS_ID and a.status_active=1 and b.status_active=1 and b.id in($all_from_po_ids)
	and c.status_active=1 group by a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id ";

	foreach ( sql_select($color_range_sql) as $row)
	{
		$fab_desc=explode(",",trim($row[csf('fabric_desc')]));
		foreach( array_unique(explode(",",$row[csf('color_id')])) as $col_id)
		{
			if($sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id])
			$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id].=','.$color_range[$row[csf('color_range')]];
			else
				$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id]=$color_range[$row[csf('color_range')]];

			if($row[csf('po_id')] != "")
			{
				$po_id=$transfer_po_arr[$row[csf('po_id')]];
				if($tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])])
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])].=','.$color_range[$row[csf('color_range')]];
				else
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])]=$color_range[$row[csf('color_range')]];
			}
		}
	}
	//echo "<pre>";print_r($tr_sales_wise_color_range);//die;
	//echo "</pre>";

	$sql_mst = "Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst);
	?>
	<div style="width:1910px; border:1px solid #999">
		<table width="95%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%'/>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="center">
						<tr class="form_caption">
							<td align="center" style="font-size:18px">
							<strong><? echo $company_library[$ex_data[0]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:14px"><strong>Unit
								: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center"
							style="font-size:14px"><? echo show_company($ex_data[0], '', ''); ?> </td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing="0" align="" border="0">
			<tr>
				<td width="130"><strong>Requisition No :</strong></td>
				<td width="175"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
				<td width="130"><strong>Requisition Date : </strong></td>
				<td width="175px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
				<td width="130">&nbsp;</td>
				<td width="175">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table align="left" cellspacing="0" width="99%" border="1" rules="all" class="rpt_table">
			<?
			$i = 1;
			$totCurrRed_Qty = 0;

			//for 2nd part reprot info
			$companyID=$ex_data[0];
			$buyerIDs="";
			$salesJob="";
			$salesBooking="";
			$colorIds="";
			//for End 2nd part reprot info
			foreach ($result as $row)
			{
				$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
				$totReqQty = $reqn_qnty_array[$key];
				$balance = $row[csf('booking_qty')] - $totReqQty;
				if($row[csf('is_sales')] == 1)
				{
					$sales_data = explode("_",$salesOrderArr[$row[csf('po_id')]]['job_no']);
					$job_no = $sales_data[0];
					$booking_no = $sales_data[1];
					$within_group = $sales_data[2];
					//$buyer = ($within_group==1)?$po_buyer_arr[$booking_no]:$row[csf('buyer_id')];
					$buyer = $row[csf('buyer_id')];
					$cbuyer = $row[csf('customer_buyer')];
					$buyerIDs.=$buyer.",";
					$salesBooking.="'".$row[csf('program_booking_pi_no')]."',";
					$salesJob.="'".$job_no."',";
					$colorIds.="'".$row[csf('color_id')]."',";
				}
				else
				{
					$job_no = $row[csf('job_no')];
					$booking_no = $row[csf('program_booking_pi_no')];
					$buyer = $row[csf('buyer_id')];
					$colorIds=$row[csf('color_id')];
				}

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if ($i==1){
				?>
				<thead bgcolor="#dddddd" style="font-size:18px">
					<th width="30">Sl</th>
					<th width="60">Buyer</th>
					<th width="60">Customer <br> Buyer</th>
					<th width="120"><?php echo ($row[csf('is_sales')] == 1) ? "Sales Order No" : "Job No" ?></th>
					<?php if ($row[csf('is_sales')] != 1) { ?>
					<th width="80">File No</th>
					<!-- <th width="80">Ref. No</th> -->
					<th width="100">Order No</th>
					<?php } ?>
					<th width="80">Ref. No</th>
					<th width="100">Booking No</th>
					<th width="140">Body Part</th>
					<th width="100">Color Type</th>
					<th width="140">Construction</th>
					<th width="140">Composition</th>
					<th width="50">F. GSM</th>
					<th width="50">F. Dia</th>
					<th width="100">Color Range</th>
					<th width="100">Color/Code</th>
					<th width="80">Book Qty.</th>
					<th width="80">Total Reqn. Qty.</th>
					<th width="80">Balance</th>
					<th width="80">Current Reqn. Qty.</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?php } ?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:15px">
						<td align="center"><? echo $i; ?></td>
						<td align="center"><p><? echo $buyer_arr[$buyer]; ?></p></td>
						<td align="center"><p><? echo $cbuyer_arr[$cbuyer]; ?></p></td>
						<td align="center"><p><? echo $job_no; ?></p></td>
						<?php if($row[csf('is_sales')] != 1){ ?>
						<td><p><? echo $row[csf('file_no')]; ?></p></td>
						<!-- <td><? //echo $row[csf('grouping')]; ?></td> -->
						<td> <div style="word-wrap:break-word; width:100px"><?
								foreach (explode(",", $row[csf('po_id')]) as $value)
								{
									$po_array[$po_number_arr[$value]]=$po_number_arr[$value];
								}
								echo implode(",", array_unique($po_array));
								unset($po_array);
							?>
							</div>
						</td>
						<?php } ?>
						<td><? echo $row[csf('grouping')]; ?></td>
						<td align="center"><? echo $row[csf('program_booking_pi_no')]; ?></td>
						<td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
						<td><? echo $row[csf('construction')]; ?></td>
						<td><? echo $row[csf('composition')]; ?></td>
						<td align="center"><? echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? echo $row[csf('dia_width')]; ?></td>
						<td align="right" title="<? echo trim($row[csf('program_booking_pi_no')]).'='.$row[csf('body_part_id')].'='.$row[csf('color_type_id')].'='.trim($row[csf('composition')]).'='.$row[csf("color_id")]; ?>">
							<?
							 //echo "booking ".trim($row[csf('program_booking_pi_no')])."body= ".$row[csf('body_part_id')]."type ".$row[csf('color_type_id')]."compo ".trim($row[csf('composition')])."color ".$row[csf("color_id")];
							 $color_range = $sales_wise_color_range[trim($row[csf('program_booking_pi_no')])][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])][$row[csf("color_id")]];
							 if($color_range =="")
							 {
								$color_range = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])];
							 }
							 echo $color_range = implode(",",array_unique(explode(",",$color_range)));
							 ?>

						 </td>
						<td align="right"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td align="right"><? echo $row[csf('booking_qty')]; ?></td>
						<td align="right"><? echo number_format($totReqQty, 2); ?></td>
						<td align="right"><? echo $balances = number_format($balance, 2); ?></td>
						<td align="right"><? echo number_format($row[csf('reqn_qty')], 2); ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$totBookingQty += $row[csf('booking_qty')];
					$totCurrQty += $totReqQty;
					$totRenqQty += $totReqQty;
					$balance_tot += $balance;
					$totCurrRed_Qty += $row[csf('reqn_qty')];
					$i++;
			}
			?>
			</tbody>
			<tfoot bgcolor="#dddddd" style="font-size:13px">
				<tr>
					<td colspan="<? echo ($result[0][csf('is_sales')] == 1) ? 14 : 16; ?>" align="right">
						<strong>Total :</strong>
					</td>

					<td align="right"><? echo number_format($totBookingQty, 2); ?></td>
					<td align="right"><? echo number_format($totCurrQty, 2); ?></td>
					<td align="right"><? echo number_format($balance_tot, 2); ?></td>
					<td align="right"><? echo number_format($totCurrRed_Qty, 2); ?></td>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<br/>


		<?
		$con = connect();
        $r_id=execute_query("delete from tmp_barcode_no where userid=$user_id");
        oci_commit($con);

		$companyID=chop($companyID,",");
		$buyerIDs=chop($buyerIDs,",");
		$salesJob=chop($salesJob,",");
		$salesBooking=chop($salesBooking,",");
		$colorIds=chop($colorIds,",");

		//only for roll basis
		$sqlRcvRollQty = "
			SELECT
				d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(h.qnty) AS rcv_qty, COUNT(h.id) AS no_of_roll_rcv,g.color_id, h.barcode_no
	        FROM
				fabric_sales_order_mst d
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
	            INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
				LEFT JOIN pro_roll_details h ON g.id = h.dtls_id
	        WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(2,22,58,84)
				AND e.trans_type IN(1,4)
				AND e.trans_id > 0
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND d.company_id IN(".$companyID.") and d.buyer_id in($buyerIDs) and g.color_id in($colorIds) and d.job_no in($salesJob) and d.sales_booking_no in($salesBooking)
				AND h.entry_form IN(2,22,58,84) and h.is_sales=1
	        GROUP BY
				d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,g.color_id, h.barcode_no
		";
		//echo $sqlRcvRollQty; //die;
		$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
		foreach($sqlRcvRollRslt as $row) // recv barcode insert into tmp_barcode_no table
		{
			if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
	        {
	            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	            $barcodeno = $row[csf('barcode_no')];
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,58)");
	        }
		}
		oci_commit($con);
		//unset($sqlRcvRollRslt);
		/*echo "<pre>";
		print_r($dataArr); die;*/

		/*
		|--------------------------------------------------------------------------
		| for issue qty and roll
		| order to order transfer
		|--------------------------------------------------------------------------
		|
		*/
		$sqlNoOfRoll="
			SELECT
				d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(e.quantity) AS rcv_qty, COUNT(g.id) AS issue_roll,g.color_names, h.barcode_no
			FROM
				fabric_sales_order_mst d
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id
				LEFT JOIN pro_roll_details h ON g.id = h.dtls_id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(133)
				AND h.entry_form IN(133)
				AND e.trans_type IN(5,6)
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND h.status_active = 1
				AND h.is_deleted = 0
			AND d.company_id IN(".$companyID.") and d.buyer_id in($buyerIDs) and d.job_no in($salesJob) and g.color_names in($colorIds) and d.sales_booking_no in($salesBooking)
	        GROUP BY
				d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,g.color_names, h.barcode_no
			UNION ALL
			SELECT
				d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(e.quantity) AS rcv_qty, SUM(g.roll) AS issue_roll,g.color_names, 0 as barcode_no
			FROM
				fabric_sales_order_mst d
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(362)
				AND e.trans_type IN(5,6)
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND d.company_id IN(".$companyID.") and d.buyer_id in($buyerIDs) and d.job_no in($salesJob) and g.color_names in($colorIds) and d.sales_booking_no in($salesBooking)
	        GROUP BY
				d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,g.color_names
		";
		//echo "<br>".$sqlNoOfRoll; //die;
		$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
		foreach($sqlNoOfRollResult as $row) // Transfered barcode insert into tmp_barcode_no table
		{
			if( $barcode_no_check2[$row[csf('barcode_no')]] =="" )
	        {
	            $barcode_no_check2[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	            $barcodeno = $row[csf('barcode_no')];
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            $r_id2=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,133)");
	        }
		}
		oci_commit($con);
		//unset($sqlNoOfRollResult);
		//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
		//echo "<pre>";
		//print_r($noOfRollIssueArr);

		/*
		|--------------------------------------------------------------------------
		| for knitting production roll
		|--------------------------------------------------------------------------
		|
		*/
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
        from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, tmp_barcode_no d
        where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            //$prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            //$prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
            //$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
            $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            //$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            //$allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];

            if($row[csf('receive_basis')] == 2 )
            {
                $prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
            }
        }

        /*
		|--------------------------------------------------------------------------
		| for data array prepard roll
		|--------------------------------------------------------------------------
		|
		*/
        $dataArr = array();
		$poArr = array();
		foreach($sqlRcvRollRslt as $row) // Receive data array
		{
			$compId = $row[csf('company_id')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			$colorID = $row[csf('color_id')];
			$storeId = $row[csf('store_id')]*1;
			$floorId = $row[csf('floor_id')]*1;
			$roomId = $row[csf('room')]*1;
			$rackId = $row[csf('rack')]*1;
			$selfId = $row[csf('self')]*1;
			$binId = $row[csf('bin_box')]*1;
			$poArr[$orderId] = $orderId;

			$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
			$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
			$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
			$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
			$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];

			//$rollRcvQty += $row[csf('no_of_roll_rcv')];
			//$rcvQty += $row[csf('rcv_qty')];

			$dataArr[$compId][$productId][$orderId][$colorID]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			if($row[csf('entry_form')]  == 84)
			{
				$dataArr[$compId][$productId][$orderId][$colorID]['issueReturnQty'] += $row[csf('rcv_qty')];

				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
			}
			else
			{
				$dataArr[$compId][$productId][$orderId][$colorID]['rcvQty'] += $row[csf('rcv_qty')];

				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
			}
		}
		unset($sqlRcvRollRslt);

        // Transfer data arr
		foreach($sqlNoOfRollResult as $row)
		{
			$compId = $row[csf('company_id')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			$colorID = $row[csf('color_names')];
			$storeId = $row[csf('store_id')]*1;
			$floorId = $row[csf('floor_id')]*1;
			$roomId = $row[csf('room')]*1;
			$rackId = $row[csf('rack')]*1;
			$selfId = $row[csf('self')]*1;
			$binId = $row[csf('bin_box')]*1;


			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];

				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$colorID]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$colorID]['transferInQty'] += $row[csf('rcv_qty')];

				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$colorID]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];

				$transOutArr[$compId][$productId][$orderId][$colorID]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$colorID]['transferOutQty'] += $row[csf('rcv_qty')];
			}


			$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
			$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
			$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
			$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
			$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
		}
		unset($sqlNoOfRollResult);

		// echo "<pre>"; print_r($dataArr); echo "</pre>";

		/*
		|--------------------------------------------------------------------------
		| for issue qty and roll
		|--------------------------------------------------------------------------
		|
		*/
		if(!empty($poArr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
			oci_commit($con);

			$con = connect();
			foreach($poArr as $poId)
			{
				execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
				oci_commit($con);
			}
			//disconnect($con);

			$sqlNoOfRollIssue="
				SELECT
					d.company_id,
					e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty,
					f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
					SUM(g.no_of_roll) AS issue_roll,g.color_id
				FROM
					fabric_sales_order_mst d
					INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
					INNER JOIN inv_transaction f ON e.trans_id = f.id
					INNER JOIN inv_grey_fabric_issue_dtls g ON e.dtls_id = g.id
				WHERE
					e.status_active = 1
					AND e.is_deleted = 0
					AND e.entry_form IN(16) and e.is_sales=1
					AND e.trans_type = 2
					AND f.status_active = 1
					AND f.is_deleted = 0
					AND g.status_active = 1
					AND g.is_deleted = 0
					AND d.company_id IN(".$companyID.") and d.buyer_id in($buyerIDs) and g.color_id in($colorIds) and d.job_no in($salesJob) and d.sales_booking_no in($salesBooking)
					AND e.po_breakdown_id in(SELECT PO_ID FROM TMP_PO_ID WHERE USER_ID = ".$user_id.")
				GROUP BY
					d.company_id,
					e.prod_id, e.po_breakdown_id,
					f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,g.color_id
				UNION ALL
				SELECT
					d.company_id,
					e.prod_id, e.po_breakdown_id, SUM(g.qnty) AS issue_qty,
					f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
					COUNT(g.id) AS issue_roll,CAST(k.color_id AS VARCHAR2(30)) as color_id
				FROM
					fabric_sales_order_mst d
					INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
					INNER JOIN inv_transaction f ON e.trans_id = f.id
					INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
					INNER JOIN inv_grey_fabric_issue_dtls k ON e.dtls_id = k.id and f.id=k.trans_id
				WHERE
					e.status_active = 1
					AND e.is_deleted = 0
					AND e.entry_form IN(61)
					AND e.trans_type = 2
					AND f.status_active = 1
					AND f.is_deleted = 0
					AND g.status_active = 1
					AND g.is_deleted = 0
					AND g.entry_form IN(61) and g.is_sales=1
					AND d.company_id IN(".$companyID.") and d.buyer_id in($buyerIDs) and k.color_id in($colorIds) AND k.status_active = 1 AND k.is_deleted = 0 and d.job_no in($salesJob) and d.sales_booking_no in($salesBooking)
					AND e.po_breakdown_id in(SELECT PO_ID FROM TMP_PO_ID WHERE USER_ID = ".$user_id.")
				GROUP BY
					d.company_id,
					e.prod_id, e.po_breakdown_id,
					f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,k.color_id
			";
			//disconnect($con); die;
			// echo $sqlNoOfRollIssue; die;
			$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
			$noOfRollIssueArr = array();
			foreach($sqlNoOfRollIssueResult as $row)
			{
				$compId = $row[csf('company_id')];
				$productId = $row[csf('prod_id')];
				$orderId = $row[csf('po_breakdown_id')];
				$colorID = $row[csf('color_id')];
				$storeId = $row[csf('store_id')]*1;
				$floorId = $row[csf('floor_id')]*1;
				$roomId = $row[csf('room')]*1;
				$rackId = $row[csf('rack')]*1;
				$selfId = $row[csf('self')]*1;
				$binId = $row[csf('bin_box')]*1;

				$issueQtyArr[$compId][$productId][$orderId][$colorID]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$colorID]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			unset($sqlNoOfRollIssueResult);
		}
		//echo $issueQty."=".$rollIssueQty;
		// echo "<pre>"; print_r($issueQtyArr);die;

		if(empty($dataArr))
		{
			echo get_empty_data_msg();
			die;
		}

		$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
		$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');

		$floorRoomRackSelfArr = return_library_array( "SELECT a.floor_room_rack_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a  WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.company_id IN(".$companyID.")", 'floor_room_rack_id', 'floor_room_rack_name');


		//composition and constructtion
		$composition_arr=array();
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$deter_array=sql_select($sql_deter);
		foreach( $deter_array as $row)
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}
		unset($deter_array);

		//for order wise and rack wise button

		$prodArray = array();
		$poArray = array();


		/*echo "<pre>";
		print_r($dataArr);
		echo "</pre>";*/
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $colorIDS=>$row)
					{
						//echo $colorIDS."string";
						$prodArray[$productId] = $productId;
						$poArray[$orderId] = $orderId;
					}
				}
			}
		}

		$sqlYarn = "SELECT e.prod_id, e.entry_form, g.febric_description_id, g.gsm, g.width, g.machine_dia, g.stitch_length, g.color_id, g.color_range_id, g.yarn_count, g.brand_id, g.yarn_lot, d.booking_id, g.yarn_prod_id
		from order_wise_pro_details e inner join pro_grey_prod_entry_dtls g on e.dtls_id = g.id   inner join inv_receive_master d on d.id = g.mst_id
		where e.entry_form in(2,22,58,84) ".where_con_using_array($prodArray, '0', 'e.prod_id')."";
		//echo $sqlYarn; die;
		$sqlYarnRslt = sql_select($sqlYarn);
		$yarnInfoArr = array();
		foreach($sqlYarnRslt as $row)
		{
			$prodId = $row[csf('prod_id')];
			// echo $prodId.'===<br>';
			$yarnInfoArr[$prodId]['construction'] = $constructtion_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['composition'] = $composition_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['gsm'] = $row[csf('gsm')];
			$yarnInfoArr[$prodId]['width'] = $row[csf('width')];
			$yarnInfoArr[$prodId]['machine_dia'] = $row[csf('machine_dia')];
			$yarnInfoArr[$prodId]['stitch_length'] = $row[csf('stitch_length')];
			if ($row[csf('entry_form')]==2)
			{
				$yarnInfoArr[$prodId]['program_no'] = $row[csf('booking_id')];
			}

			$expColor = explode(',', $row[csf('color_id')]);
			$clrArr = array();
			foreach($expColor as $clr)
			{
				$clrArr[$clr] = $color_arr[$clr];
			}

			$yarnInfoArr[$prodId]['color_id'] = implode(',', $clrArr);
			$yarnInfoArr[$prodId]['color_range_id'] = $color_range[$row[csf('color_range_id')]];
			$yarnInfoArr[$prodId]['yarn_count'] = $row[csf('yarn_count')];
			$yarnInfoArr[$prodId]['brand_id'] = $row[csf('brand_id')];
			$yarnInfoArr[$prodId]['yarn_lot'] = $row[csf('yarn_lot')];

			if($row[csf("yarn_prod_id")] !=""){
				$yarnInfoArr[$prodId]['yarn_prod_id'] = $row[csf('yarn_prod_id')];
				$all_yarn_prod_id_arr[$row[csf("yarn_prod_id")]] = $row[csf("yarn_prod_id")];
			}
		}
		unset($sqlYarnRslt);
		//echo "<pre>";
		//print_r($all_yarn_prod_id_arr);die;
		$all_yarn_prod_id_arr = array_filter($all_yarn_prod_id_arr);

		if(count($all_yarn_prod_id_arr) > 0)
		{
			$all_yarn_prod_id = implode(",", $all_yarn_prod_id_arr);
			$yarnProdCond = $all_yarn_prod_id_cond = "";
			if($db_type==2 && count($all_yarn_prod_id_arr)>999)
			{
				$all_yarn_prod_id_chunk=array_chunk($all_yarn_prod_id_arr,999) ;
				foreach($all_yarn_prod_id_chunk as $chunk_prog)
				{
					$chunk_prog_val=implode(",",$chunk_prog);
					$yarnProdCond.=" a.id in($chunk_prog_val) or ";
				}

				$all_yarn_prod_id_cond.=" and (".chop($yarnProdCond,'or ').")";

			}
			else
			{
				$all_yarn_prod_id_cond=" and a.id in($all_yarn_prod_id)";
			}

			$supplier_yarn = sql_select("select a.id, b.short_name, a.yarn_type, a.yarn_comp_type1st from product_details_master a, lib_supplier b where  a.supplier_id = b.id $all_yarn_prod_id_cond and b.status_active = 1 and a.status_active=1");
			foreach ($supplier_yarn as $val) {
				$yarnProdArr[$val[csf('id')]]['yarn_supplier'] = $val[csf('short_name')];
				$yarnProdArr[$val[csf('id')]]['yarn_type'] = $val[csf('yarn_type')];
				$yarnProdArr[$val[csf('id')]]['yarn_comp_type1st'] = $val[csf('yarn_comp_type1st')];
			}
			unset($supplier_yarn);
		}



		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		execute_query("DELETE from tmp_barcode_no where userid=$user_id");
		oci_commit($con);

		$con = connect();
		foreach($poArray as $poId)
		{
			$dataPoArr[]= "(".$poId.",".$user_id.")";
		}
		/*$con = connect();
		$rID = sql_insert_zs("TMP_PO_ID", 'PO_ID,USER_ID', $dataPoArr, 1, 0);
		oci_commit($con);*/
		//disconnect($con);

		//for booking information

		//echo "<pre>";
		//print_r($bookingInfoArr);
		// echo '<pre>';print_r($dataArr);
		$width = 2970;
		?>
		<table align="left" cellspacing="0" width="99%" border="1" rules="all" class="rpt_table">
			<h3 align="left" title="(This report similar as Rack Wise Grey Fabrics Stock Report Sales - Summary button)">Job & Colour wise Information (This report similar as Rack Wise Grey Fabrics Stock Report Sales- FSO Wise button)<span style="color:red;">Note: It may wrong stock quantity showing when found any multicolor wise transaction</span></h3>
			<thead bgcolor="#dddddd" style="font-size:18px">
               <!--  <tr class="form_caption" style="border:none;">
                	<th align="center" colspan="37" style="font-size:16px"><strong>Job & Colour wise Information (This report similar as Rack Wise Grey Fabrics Stock Report Sales- Summary button)</strong></th>
                </tr> -->
                <tr>
					<th width="30">SL</th>
					<th width="120">Sales Order No</th>
					<th width="100">Sales Job/ Booking No.</th>
					<th width="100">Style Ref. No.</th>
					<th width="100">Customer Name</th>
					<th width="100">Cust. Buyer Name</th>

					<th width="110">Fab. Constraction</th>
					<th width="120">Fab. Composition</th>
					<th width="50">GSM</th>
					<th width="50">F/Dia</th>
					<th width="50">M/C Dia</th>
					<th width="60">Stich Length</th>
					<th width="100">Fabric Color</th>
					<th width="100">Color Range</th>
					<th width="60">Y. Count</th>
					<th width="120">Y. Composition</th>
					<th width="80">Y. Type</th>
					<th width="60">Y. Brand</th>
					<th width="100">Y. Lot</th>
					<th>Stock Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sl = 0;
				$grandTotal = array();
				foreach($dataArr as $compId=>$compArr)
				{
					foreach($compArr as $productId=>$productArr)
					{
						foreach($productArr as $orderId=>$orderArr)
						{
							foreach($orderArr as $colorIDS=>$row)
							{
								//echo $colorIDS."s";
								//echo "[compId=$compId][productId=$productId][orderId=$orderId][storeId=$storeId][floorId=$floorId][roomId=$roomId][rackId=$rackId][selfId=$selfId][binId=$binId]<br>";
								//total receive calculation
								$yarn_counts_arr = array_unique(array_filter(explode(",", $row['yarn_count'])));
                                $yarn_counts="";
                                foreach ($yarn_counts_arr as $count) {
                                    $yarn_counts .= $count_arr[$count] . ",";
                                }
                                $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                                $yarn_lot =implode(",",array_filter(array_unique(explode(",", $row['yarn_lot']))));
                                $stitch_length =implode(",",array_filter(array_unique(explode(",", $row['stitch_length']))));
                                $machine_dia =implode(",",array_filter(array_unique(explode(",", $row['machine_dia']))));

								$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');

								//total issue calculation
								$row['issueQty'] = $issueQtyArr[$compId][$productId][$orderId][$colorIDS]['issueQty'];
								$row['rcvReturnQty'] = 0;
								$row['transferOutQty'] = $transOutArr[$compId][$productId][$orderId][$colorIDS]['transferOutQty'];
								$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');

								//stock qty calculation
								//echo $row['totalRcvQty'] .'-'. $row['totalIssueQty'].'<br>';
								$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

								//roll balance calculation
								$row['rollIssueQty'] = $transOutArr[$compId][$productId][$orderId][$colorIDS]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$colorIDS][$orderId]['rollIssueQty'];
								$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];



								/*if($row['stockQty'] >= 0)
								{*/
									$sl++;
									/*if($sl == 10000)
									{
										break;
									}*/
									//echo $yarnCount;
									//print_r($yarnCountArr);
									$row['fso_no'] = $bookingInfoArr[$orderId]['fso_no'];
									$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
									$row['customer_buyer'] = $bookingInfoArr[$orderId]['customer_buyer'];
									$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
									$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];

									$row['construction'] = $yarnInfoArr[$productId]['construction'];
									$row['composition'] = $yarnInfoArr[$productId]['composition'];
									$row['gsm'] = $yarnInfoArr[$productId]['gsm'];
									$row['width'] = $yarnInfoArr[$productId]['width'];
									$row['machine_dia'] = $yarnInfoArr[$productId]['machine_dia'];
									$row['stitch_length'] = $yarnInfoArr[$productId]['stitch_length'];
									$row['program_no'] = $yarnInfoArr[$productId]['program_no'];
									// echo $row['program_no'].'<br>';
									//$row['color_id'] = $yarnInfoArr[$productId]['color_id'];
									$row['color_range_id'] = $yarnInfoArr[$productId]['color_range_id'];
									$row['yarn_count'] = $yarnInfoArr[$productId]['yarn_count'];
									$row['yarn_prod_id'] = $yarnInfoArr[$productId]['yarn_prod_id'];

									/*$yarnCountArr=explode(',', $row['yarn_count']);
									$yarnCount="";
									foreach ($yarnCountArr as $key => $yCount)
									{
										if ($yarnCount=="")
										{
											$yarnCount.=$count_arr[$yCount];
										}
										else
										{
											$yarnCount.=', '.$count_arr[$yCount];
										}
									}*/
									$row['brand_id'] = $yarnInfoArr[$productId]['brand_id'];
									$row['yarn_lot'] = $yarnInfoArr[$productId]['yarn_lot'];

									$yarn_prod_idArr=explode(',', $row['yarn_prod_id']);
									$yarn_supplier=$yarn_type_no=$yarn_compositions="";
									foreach ($yarn_prod_idArr as $yProd)
									{
										$yarn_supplier .= $yarnProdArr[$yProd]['yarn_supplier'].",";
										$yarn_type_no .= $yarn_type[$yarnProdArr[$yProd]['yarn_type']].",";
										$yarn_compositions .=$composition_arr[$yarnProdArr[$yProd]['yarn_comp_type1st']]."*";
									}
									$yarn_supplier = implode(",",array_unique(array_filter(explode(",", chop($yarn_supplier,",")))));
									$yarn_type_no = implode(",",array_unique(array_filter(explode(",", chop($yarn_type_no,",")))));
									$yarn_compositions = implode("*",array_unique(array_filter(explode("*", chop($yarn_compositions,"*")))));

									?>
                                    <tr valign="middle">
                                        <td width="30" align="center"><?php echo $sl; ?></td>
                                        <td width="120"><div style="word-break:break-all"><?php echo $row['fso_no']; ?></div></td>
                                        <td width="100"><div style="word-break:break-all"><?php echo $row['booking_no']; ?></div></td>
                                        <td width="100"><div style="word-break:break-all"><?php echo $row['style_ref_no']; ?></div></td>
                                        <td width="100">
                                        	<div style="word-break:break-all"><?php echo $buyer_array[$row['buyer_id']]; ?></div>
                                        </td>
                                        <td width="100">
                                        	<div style="word-break:break-all"><?php echo $buyer_array[$row['customer_buyer']]; ?></div>
                                        </td>

                                        <td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
                                        <td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
                                        <td width="50" title="prodId: <? echo $prodId.'='.$productId; ?>"><?php echo $row['gsm']; ?></td>
                                        <td width="50"><?php echo $row['width']; ?></td>
                                        <td width="50"><?php echo $machine_dia;//$row['machine_dia']; ?></td>
                                        <td width="60"><?php echo $stitch_length;//$row['stitch_length']; ?></td>
                                        <td width="100"><div style="word-break:break-all"><?php echo $color_arr[$colorIDS]; ?></div></td>
                                        <td width="100"><div style="word-break:break-all"><?php echo $row['color_range_id']; ?></div></td>
                                        <td width="60"><div style="word-break:break-all"><?php echo $yarn_counts;//$yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
                                        <td width="120"><div style="word-break:break-all"><?php echo $yarn_compositions;?></div></td>
                                        <td width="80"><div style="word-break:break-all"><?php echo $yarn_type_no;?></div></td>

                                        <td width="60"><div style="word-break:break-all"><?php echo $yarn_supplier;//$brand_arr[$row['brand_id']]; ?></div></td>
                                        <td width="100"><div style="word-break:break-all"><?php echo $yarn_lot;//$row['yarn_lot']; ?></div></td>

                                        <td align="right" title="<? echo $compId.'='.$productId.'='.$orderId.'='.$colorIDS;?>"><?php echo number_format($row['stockQty'],2); ?></td>
                                    </tr>
									<?php
									//$grandTotal

									$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
								//}
							}
						}
					}
				}
                ?>
        	</tbody>
        	<tfoot>
                <tr>
                    <th colspan="19" align="right">Total</th>
                    <th align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                </tr>
        	</tfoot>
        </table>


		<br/>
		<?
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		echo signature_table(93, $ex_data[0], "1460px");
		?>
	</div>
	<?
	exit();
}

if ($action == "print_fab_req_for_batch_4")
{
	extract($_REQUEST);
	$ex_data = explode('*', $data);
	if($ex_data[1]=="")
	{
		echo "Data Not Saved";die;
	}
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$sql = "SELECT id, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks,gsm_weight,dia_width,construction,composition,file_no,grouping,body_part_id,color_type_id,booking_qty, is_sales, customer_buyer from pro_fab_reqn_for_batch_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $vals)
	{
		$all_booking_nos_arr[$vals[csf("program_booking_pi_no")]]=$vals[csf("program_booking_pi_no")];
		$all_po_id_arr[$vals[csf("po_id")]]=$vals[csf("po_id")];
		$all_composition_arr[$vals[csf("composition")]]=$vals[csf("composition")];
		// $job_no_arr[$vals[csf("job_no")]] = $vals[csf("job_no")];
	}
	$job_nos="'".implode("','", $job_no_arr)."'";
	$all_booking_nos = "'" . implode("','", $all_booking_nos_arr) . "'";
	$all_po_ids=implode(",", $all_po_id_arr);
	$all_composition_ids="'".implode("','", $all_composition_arr)."'";


	$booking_no_arrs=array();
	$booking_data = sql_select("select b.BOOKING_NO,a.STYLE_REF_NO  from sample_development_mst a, wo_non_ord_samp_booking_dtls b  where a.id=b.style_id and a.status_active=1 and b.status_active=1 and a.ENTRY_FORM_ID=203 and b.BOOKING_NO in($all_booking_nos)");

	foreach ($booking_data as $value) {

		$booking_no_arrs[$value[csf("BOOKING_NO")]]=$value[csf("STYLE_REF_NO")];

	}

	//for Style Ref
    $sqlBooking = "SELECT b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, c.STYLE_REF_NO FROM wo_po_break_down a, wo_booking_dtls b, wo_po_details_master c  where a.id = b.po_break_down_id AND a.job_id=c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and b.BOOKING_NO in($all_booking_nos)";
    // echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $booking_no_arrs = array();
    foreach($sqlBookingRslt as $row)
    {
        $booking_no_arrs[$row['BOOKING_NO']] = $row['STYLE_REF_NO'];
    }
    unset($sqlBookingRslt);

	if(!empty($all_po_id_arr))
	{
		$poidCond =$poId=$to_po_id_cond=$to_po_id="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$poId.=" id in(".implode(",",$chunk_arr).") or ";
				$to_po_id.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$poidCond.=" and (".chop($poId,'or ').")";
			$to_po_id_cond.=" and (".chop($to_po_id,'or ').")";
		}
		else
		{
			$poidCond=" and id in($all_po_ids)";
			$to_po_id_cond=" and a.to_order_id in($all_po_ids)";
		}

		$po_arr=sql_select("select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 $poidCond");
		foreach ($po_arr as $row)
		{
			$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
		}

		$transferData = sql_select("select a.to_order_id,a.from_order_id,b.from_program from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id $to_po_id_cond");
		foreach ($transferData as $row) {
		$transfer_po_arr[$row[csf('from_order_id')]]= $row[csf('to_order_id')];
		$from_po_id_arr[$row[csf("from_program")]]=$row[csf("from_program")];
		}
		$all_from_po_ids="'".implode("','", $from_po_id_arr)."'";

	}

	$reqn_qnty_array = array();
	$sales_id="";
	$reqnData = sql_select("select reqn_qty,color_type_id,color_id,program_booking_pi_no,po_id,job_no,buyer_id, body_part_id,gsm_weight, dia_width, construction, composition, grouping,file_no from pro_fab_reqn_for_batch_dtls where entry_form=123 and status_active=1 and is_deleted=0 and program_booking_pi_no in($all_booking_nos)");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
		if($row[csf('po_id')]!="")
		{
			$sales_id .= $row[csf('po_id')] . ",";
		}
	}
	//$sales_ids = rtrim($sales_id,",");

	if($sales_id!="")
	{
		$all_sales_id=implode(",",array_unique(explode(',',chop($sales_id,','))));

		$sales_id=array_unique(explode(',',chop($sales_id,',')));
		$salesidCond = "";
		if($db_type==2 && count($sales_id)>999)
		{
			$all_sale_id_chunk=array_chunk($sales_id,999) ;
			foreach($all_sale_id_chunk as $chunk_arr)
			{
				$salesId.=" id in(".implode(",",$chunk_arr).") or ";
			}

			$salesidCond.=" and (".chop($salesId,'or ').")";
		}
		else
		{
			$salesidCond=" and id in($all_sales_id)";
		}


		$salesOrderArr = array();
		$salesOrderData = sql_select("SELECT id,job_no,sales_booking_no,within_group from fabric_sales_order_mst  where status_active=1  $salesidCond   group by id,job_no,sales_booking_no,within_group ");
		$sales_wise_color_range=array();
		foreach ($salesOrderData as $row)
		{
			$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('job_no')] . "_" . $row[csf('sales_booking_no')] . "_".$row[csf('within_group')];

		}
	}
	
	$color_range_sql="SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range, null as po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.booking_no in($all_booking_nos) group by  a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range
	union all
	SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,PPL_PLANNING_ENTRY_PLAN_DTLS c
	where a.id=b.mst_id and b.id=c.DTLS_ID and a.status_active=1 and b.status_active=1 and b.id in($all_from_po_ids)
	and c.status_active=1 group by a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id ";

	foreach ( sql_select($color_range_sql) as $row)
	{
		$fab_desc=explode(",",trim($row[csf('fabric_desc')]));
		foreach( array_unique(explode(",",$row[csf('color_id')])) as $col_id)
		{
			if($sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id])
			$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id].=','.$color_range[$row[csf('color_range')]];
			else
				$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id]=$color_range[$row[csf('color_range')]];

			if($row[csf('po_id')] != "")
			{
				$po_id=$transfer_po_arr[$row[csf('po_id')]];
				if($tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])])
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])].=','.$color_range[$row[csf('color_range')]];
				else
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])]=$color_range[$row[csf('color_range')]];
			}
		}

	}
	//echo "<pre>";print_r($tr_sales_wise_color_range);//die;
	//echo "</pre>";

	$sql_mst = "Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst);
	?>
	<div style="width:2250px; border:1px solid #999">
		<table width="95%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%'/>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="center">
						<tr class="form_caption">
							<td align="center" style="font-size:18px">
							<strong><? echo $company_library[$ex_data[0]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:14px"><strong>Unit
								: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center"
							style="font-size:14px"><? echo show_company($ex_data[0], '', ''); ?> </td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing="0" align="" border="0">
			<tr>
				<td width="130"><strong>Requisition No :</strong></td>
				<td width="175"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
				<td width="130"><strong>Requisition Date : </strong></td>
				<td width="175px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
				<td width="130">&nbsp;</td>
				<td width="175">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table align="left" cellspacing="0" width="99%" border="1" rules="all" class="rpt_table">
			<?
			$i = 1;
			$totCurrRed_Qty = 0;
			foreach ($result as $row)
			{
				$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
				$totReqQty = $reqn_qnty_array[$key];
				$balance = $row[csf('booking_qty')] - $totReqQty;
				if($row[csf('is_sales')] == 1)
				{
					$sales_data = explode("_",$salesOrderArr[$row[csf('po_id')]]['job_no']);
					$job_no = $sales_data[0];
					$booking_no = $sales_data[1];
					$within_group = $sales_data[2];
					//$buyer = ($within_group==1)?$po_buyer_arr[$booking_no]:$row[csf('buyer_id')];
					$buyer = $row[csf('buyer_id')];
					$cbuyer = $row[csf('customer_buyer')];
				}
				else
				{
					$job_no = $row[csf('job_no')];
					$booking_no = $row[csf('program_booking_pi_no')];
					$buyer = $row[csf('buyer_id')];
				}

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if ($i==1){
				?>
				<thead bgcolor="#dddddd" style="font-size:18px">
					<th width="30">Sl</th>
					<th width="60">Buyer</th>
					<th width="120"><?php echo ($row[csf('is_sales')] == 1) ? "Sales Order No" : "Job No" ?></th>
					<?php if ($row[csf('is_sales')] != 1) { ?>
					<th width="80">File No</th>
					<!-- <th width="80">Ref. No</th> -->
					<th width="100">Order No</th>
					<?php } ?>
					<th width="80">Ref. No/<br>Style Ref. </th>
					<th width="100">Booking No</th>
					<th width="140">Body Part</th>
					<th width="100">Color Type</th>
					<th width="140">Construction</th>
					<th width="140">Composition</th>
					<th width="100">Yarn count</th>
					<th width="100">Yarn lot</th>
					<th width="100">Stich lenth</th>
					<th width="100">Machine dia</th>
					<th width="50">F. GSM</th>
					<th width="50">F. Dia</th>
					<th width="100">Color Range</th>
					<th width="100">Color/Code</th>
					<th width="80">Book Qty.</th>
					<th width="80">Total Reqn. Qty.</th>
					<th width="80">Balance</th>
					<th width="80">Current Reqn. Qty.</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?php } ?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:15px">
						<td align="center"><? echo $i; ?></td>
						<td align="center"><p><? echo $buyer_arr[$buyer]; ?></p></td>
						<td align="center"><p><? echo $job_no; ?></p></td>
						<?php if($row[csf('is_sales')] != 1){ ?>
						<td><p><? echo $row[csf('file_no')]; ?></p></td>
						<!-- <td><? //echo $row[csf('grouping')]; ?></td> -->
						<td><div style="word-wrap:break-word; width:100px"><?
							foreach (explode(",", $row[csf('po_id')]) as $value)
							{
								$po_array[$po_number_arr[$value]]=$po_number_arr[$value];
							}
							echo implode(",", array_unique($po_array));
							unset($po_array);
							?></div>
						</td>
						<?php } ?>
						<td><? echo "R:".$row[csf('grouping')]."<br>"."S:".$booking_no_arrs[$row[csf("program_booking_pi_no")]]; ?></td>
						<td align="center"><? echo $row[csf('program_booking_pi_no')]; ?></td>
						<td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
						<td><? echo $row[csf('construction')]; ?></td>
						<td><? echo $row[csf('composition')]; ?></td>
						<td align="center"><? //echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? //echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? //echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? //echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? echo $row[csf('dia_width')]; ?></td>
						<td align="right" title="<? echo trim($row[csf('program_booking_pi_no')]).'='.$row[csf('body_part_id')].'='.$row[csf('color_type_id')].'='.trim($row[csf('composition')]).'='.$row[csf("color_id")]; ?>">
							<?
							 //echo "booking ".trim($row[csf('program_booking_pi_no')])."body= ".$row[csf('body_part_id')]."type ".$row[csf('color_type_id')]."compo ".trim($row[csf('composition')])."color ".$row[csf("color_id")];
							 $color_range = $sales_wise_color_range[trim($row[csf('program_booking_pi_no')])][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])][$row[csf("color_id")]];
							 if($color_range =="")
							 {
								$color_range = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($row[csf('composition')])];
							 }
							 echo $color_range = implode(",",array_unique(explode(",",$color_range)));
							 ?>

						 </td>
						<td align="right"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td align="right"><? echo $row[csf('booking_qty')]; ?></td>
						<td align="right"><? echo number_format($totReqQty, 2); ?></td>
						<td align="right"><? echo $balances = number_format($balance, 2); ?></td>
						<td align="right"><? echo number_format($row[csf('reqn_qty')], 2); ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$totBookingQty += $row[csf('booking_qty')];
					$totCurrQty += $totReqQty;
					$totRenqQty += $totReqQty;
					$balance_tot += $balance;
					$totCurrRed_Qty += $row[csf('reqn_qty')];
					$i++;
			}
			?>
			</tbody>
			<tfoot bgcolor="#dddddd" style="font-size:13px">
				<tr>
					<td colspan="<? echo ($result[0][csf('is_sales')] == 1) ? 17 : 19; ?>" align="right">
						<strong>Total :</strong>
					</td>

					<td align="right"><? echo number_format($totBookingQty, 2); ?></td>
					<td align="right"><? echo number_format($totCurrQty, 2); ?></td>
					<td align="right"><? echo number_format($balance_tot, 2); ?></td>
					<td align="right"><? echo number_format($totCurrRed_Qty, 2); ?></td>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<br>
		<strong style="font: bold 12px tahoma;">Special Instruction</strong>
		<table border="1" rules="all" cellpadding="3" style="font-size: 12px;">
			<tr bgcolor="#CCCCCC">
				<td align="center"><strong>Sl</strong></td>
				<td><strong>Terms</strong></td>
			</tr>
			<?
			$reqn_number=$dataArray[0][csf('reqn_number')];
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$reqn_number'");
			if (count($data_array) > 0)
			{
				$i = 0;
				foreach ($data_array as $row)
				{
					$i++;
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td><? echo $row[csf('terms')]; ?></td>
					</tr>
					<?
				}
			}
	        ?>
	    </tbody>
		<?
		echo signature_table(93, $ex_data[0], "1460px");
		?>
	</div>
	<?
	exit();
}

if ($action == "print_fab_req_for_batch_tg")
{
	extract($_REQUEST);
	$ex_data = explode('*', $data);

	if($ex_data[1]=="")
	{
		echo "Data Not Saved";die;
	}
	$company_library = return_library_array("SELECT id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("SELECT master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$location_arr = return_library_array("SELECT id,location_name from lib_location", "id", "location_name");

	$sql = "SELECT id, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks,gsm_weight,dia_width,construction,composition,file_no,grouping,body_part_id,color_type_id,booking_qty, is_sales, customer_buyer from pro_fab_reqn_for_batch_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $vals)
	{
		$all_booking_nos_arr[$vals[csf("program_booking_pi_no")]]=$vals[csf("program_booking_pi_no")];
		$all_po_id_arr[$vals[csf("po_id")]]=$vals[csf("po_id")];
		$all_composition_arr[$vals[csf("composition")]]=$vals[csf("composition")];
		// $job_no_arr[$vals[csf("job_no")]] = $vals[csf("job_no")];
	}
	// echo "<pre>";print_r($main_data_arr);

	$job_nos="'".implode("','", $job_no_arr)."'";
	$all_booking_nos = "'" . implode("','", $all_booking_nos_arr) . "'";
	$all_po_ids=implode(",", $all_po_id_arr);
	$all_composition_ids="'".implode("','", $all_composition_arr)."'";

	//$transferData = sql_select("select to_order_id,from_order_id,b.from_program from inv_item_transfer_mst a,INV_ITEM_TRANSFER_DTLS b where a.id=b.mst_id and to_order_id in($all_po_ids)");
	$booking_no_arrs=array();
	$booking_data = sql_select("select b.BOOKING_NO,a.STYLE_REF_NO  from sample_development_mst a, wo_non_ord_samp_booking_dtls b  where a.id=b.style_id and a.status_active=1 and b.status_active=1 and a.ENTRY_FORM_ID=203 and b.BOOKING_NO in($all_booking_nos)");

	foreach ($booking_data as $value) {

		$booking_no_arrs[$value[csf("BOOKING_NO")]]=$value[csf("STYLE_REF_NO")];

	}

	//for Style Ref
    $sqlBooking = "SELECT b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, c.STYLE_REF_NO, a.grouping as GROUPING FROM wo_po_break_down a, wo_booking_dtls b, wo_po_details_master c  where a.id = b.po_break_down_id AND a.job_no_mst=c.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and b.BOOKING_NO in($all_booking_nos)";
    // echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $booking_no_arrs = array();
    foreach($sqlBookingRslt as $row)
    {
        $booking_no_arrs[$row['BOOKING_NO']] = $row['STYLE_REF_NO'];
        $int_ref_arrs[$row['BOOKING_NO']] = $row['GROUPING'];
    }
    unset($sqlBookingRslt);



	if(!empty($all_po_id_arr))
	{
		$poidCond =$poId=$to_po_id_cond=$to_po_id="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$poId.=" id in(".implode(",",$chunk_arr).") or ";
				$to_po_id.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$poidCond.=" and (".chop($poId,'or ').")";
			$to_po_id_cond.=" and (".chop($to_po_id,'or ').")";
		}
		else
		{
			$poidCond=" and id in($all_po_ids)";
			$to_po_id_cond=" and a.to_order_id in($all_po_ids)";
		}

		//echo "select id, po_number,grouping from wo_po_break_down where status_active=1 and is_deleted=0 $poidCond";
		$po_arr=sql_select("select id, po_number,grouping from wo_po_break_down where status_active=1 and is_deleted=0 $poidCond");
		foreach ($po_arr as $row)
		{
			$po_number_arr[$row[csf('id')]] = $row[csf('po_number')];
			$po_grouping_arr[$row[csf('id')]] = $row[csf('grouping')];
		}

		$transferData = sql_select("select a.to_order_id,a.from_order_id,b.from_program from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id $to_po_id_cond");
		foreach ($transferData as $row) {
		$transfer_po_arr[$row[csf('from_order_id')]]= $row[csf('to_order_id')];
		$from_po_id_arr[$row[csf("from_program")]]=$row[csf("from_program")];
		}
		$all_from_po_ids="'".implode("','", $from_po_id_arr)."'";

	}

	$reqn_qnty_array = array();
	$sales_id="";

	$reqnData = sql_select("select reqn_qty,color_type_id,color_id,program_booking_pi_no,po_id,job_no,buyer_id, body_part_id,gsm_weight, dia_width, construction, composition, grouping,file_no from pro_fab_reqn_for_batch_dtls where entry_form=123 and status_active=1 and is_deleted=0 and program_booking_pi_no in ($all_booking_nos)");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('construction')] . $row[csf('composition')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
		if($row[csf('po_id')]!="")
		{
			$sales_id .= $row[csf('po_id')] . ",";
		}
	}
	// echo "<pre>";print_r($reqn_qnty_array);
	//$sales_ids = rtrim($sales_id,",");

	if($sales_id!="")
	{
		$all_sales_id=implode(",",array_unique(explode(',',chop($sales_id,','))));

		$sales_id=array_unique(explode(',',chop($sales_id,',')));
		$salesidCond = "";
		if($db_type==2 && count($sales_id)>999)
		{
			$all_sale_id_chunk=array_chunk($sales_id,999) ;
			foreach($all_sale_id_chunk as $chunk_arr)
			{
				$salesId.=" id in(".implode(",",$chunk_arr).") or ";
			}

			$salesidCond.=" and (".chop($salesId,'or ').")";
		}
		else
		{
			$salesidCond=" and id in($all_sales_id)";
		}

		$salesOrderArr = array();
		$salesOrderData = sql_select("SELECT id,job_no,sales_booking_no,within_group,customer_buyer, buyer_id, po_buyer from fabric_sales_order_mst  where status_active=1  $salesidCond  
		group by id,job_no,sales_booking_no,within_group,customer_buyer, buyer_id, po_buyer ");
		$sales_wise_color_range=array();
		foreach ($salesOrderData as $row)
		{
			$salesOrderArr[$row[csf('id')]]['job_no'] = $row[csf('job_no')] . "_" . $row[csf('sales_booking_no')] . "_".$row[csf('within_group')] . "_".$row[csf('customer_buyer')] . "_".$row[csf('buyer_id')] . "_".$row[csf('po_buyer')];
	
		}
	}

	$color_range_sql="SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range, null as po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.booking_no in($all_booking_nos) group by  a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range
	union all
	SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,PPL_PLANNING_ENTRY_PLAN_DTLS c
	where a.id=b.mst_id and b.id=c.DTLS_ID and a.status_active=1 and b.status_active=1 and b.id in($all_from_po_ids)
	and c.status_active=1 group by a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id ";

	foreach ( sql_select($color_range_sql) as $row)
	{
		$fab_desc=explode(",",trim($row[csf('fabric_desc')]));
		foreach( array_unique(explode(",",$row[csf('color_id')])) as $col_id)
		{
			if($sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id])
			$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id].=','.$color_range[$row[csf('color_range')]];
			else
				$sales_wise_color_range[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])][$col_id]=$color_range[$row[csf('color_range')]];

			if($row[csf('po_id')] != "")
			{
				$po_id=$transfer_po_arr[$row[csf('po_id')]];
				if($tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])])
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])].=','.$color_range[$row[csf('color_range')]];
				else
					$tr_sales_wise_color_range[$po_id][$row[csf('body_part_id')]][$row[csf('color_type_id')]][trim($fab_desc[1])]=$color_range[$row[csf('color_range')]];
			}
		}

	}
	//echo "<pre>";print_r($tr_sales_wise_color_range);//die;
	//echo "</pre>";

	$sql_mst = "Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst);

	foreach($result as $vals)
	{
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['buyer_id'] = $vals[csf("buyer_id")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['is_sales'] = $vals[csf("is_sales")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['program_booking_pi_no'] = $vals[csf("program_booking_pi_no")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['grouping'] = $vals[csf("grouping")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['file_no'] = $vals[csf("file_no")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['booking_qty'] += $vals[csf("booking_qty")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['po_id'] = $vals[csf("po_id")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['reqn_qty'] += $vals[csf("reqn_qty")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['remarks'] = $vals[csf("remarks")];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['color_id'] .= $vals[csf("color_id")].',';
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['job_no'] = $vals[csf("job_no")];

		$key = $vals[csf('color_type_id')] . $vals[csf('color_id')] . $vals[csf('program_booking_pi_no')] . $vals[csf('buyer_id')] . $vals[csf('body_part_id')] . $vals[csf('gsm_weight')] . $vals[csf('dia_width')] . $vals[csf('construction')] . $vals[csf('composition')] . $vals[csf('grouping')] . $vals[csf('file_no')];
		$main_data_arr[$vals[csf("po_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("construction")]][$vals[csf("composition")]][$vals[csf("color_type_id")]][$vals[csf("gsm_weight")]][$vals[csf("dia_width")]]['reqn_qnty']+=$reqn_qnty_array[$key];
	}
	// echo "<pre>";print_r($main_data_arr);
	?>
	<div style="width:1910px; border:1px solid #999">
		<table width="95%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%'/>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="center">
						<tr class="form_caption">
							<td align="center" style="font-size:18px">
							<strong><? echo $company_library[$ex_data[0]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:14px"><strong>Unit
								: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center"
							style="font-size:14px"><? echo show_company($ex_data[0], '', ''); ?> </td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing="0" align="" border="0">
			<tr>
				<td width="130"><strong>Requisition No :</strong></td>
				<td width="175"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
				<td width="130"><strong>Requisition Date : </strong></td>
				<td width="175px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
				<td width="130">&nbsp;</td>
				<td width="175">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table align="left" cellspacing="0" width="99%" border="1" rules="all" class="rpt_table">
			<?
			// echo "<pre>";print_r($main_data_arr);die;
			$i = 1;
			$totBookingQty = 0;
			$totCurrQty = 0;
			$totRenqQty = 0;
			$balance_tot = 0;
			$totCurrRed_Qty = 0;
			foreach ($main_data_arr as $k_po_id=>$v_po_id)
			{
				foreach ($v_po_id as $k_color_id=>$v_color_id)
				{
					$sub_totBookingQty = 0;
					$sub_totCurrQty = 0;
					$sub_totRenqQty = 0;
					$sub_balance_tot = 0;
					$sub_totCurrRed_Qty = 0;
					foreach ($v_color_id as $k_bpart_id=>$v_bpart_id)
					{
						foreach ($v_bpart_id as $k_construction=>$v_construction)
						{
							foreach ($v_construction as $k_composition=>$v_composition)
							{
								foreach ($v_composition as $k_ctype_id=>$v_ctype_id)
								{
									foreach ($v_ctype_id as $k_gsm=>$v_gsm)
									{
										foreach ($v_gsm as $k_dia=>$row)
										{
											$totReqQty = $row['reqn_qnty'];
											$balance = $row['booking_qty'] - $totReqQty;
											$int_ref='';//$po_grouping_array=array();
											if($row['is_sales'] == 1)
											{
												$sales_data = explode("_",$salesOrderArr[$row['po_id']]['job_no']);
												$job_no = $sales_data[0];
												$booking_no = $sales_data[1];
												$within_group = $sales_data[2];
												$cbuyer = $sales_data[3];
												$buyer_id = $sales_data[4];
												$po_buyer_id = $sales_data[5];
												//$buyer = ($within_group==1)?$po_buyer_arr[$booking_no]:$row[csf('buyer_id')];
												
												if($within_group == 1)
												{
													$int_ref = $int_ref_arrs[$booking_no];
													$buyer=$company_library[$buyer_id];
												}
												else
												{
													$buyer = $buyer_arr[$po_buyer_id];
												}
											}
											else
											{
												$job_no = $row['job_no'];
												$booking_no = $row['program_booking_pi_no'];
												$buyer = $buyer_arr[$row['buyer_id']];
												/*foreach (explode(",", $row['po_id']) as $value)
												{
													$po_grouping_array[$po_grouping_arr[$value]]=$po_grouping_arr[$value];
												}
												$int_ref = implode(",", array_unique($po_grouping_array));*/
												$int_ref = $po_grouping_arr[$k_po_id];
											}

											if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
											if ($i==1){
											?>
											<thead bgcolor="#dddddd" style="font-size:18px">
												<th width="30">Sl</th>
												<th width="100">Buyer/<br>Cus. Buyer</th>
												<th width="150"><?php echo ($row['is_sales'] == 1) ? "Sales Order No" : "Job No" ?></th>
												<th width="120">Booking No/<br>Internal reff</th>
												<th width="80">Body Part</th>
												<th width="100">Construction</th>
												<th width="180">Composition</th>
												<th width="100">Color Range</th>
												<th width="100">Color Type</th>
												<th width="80">Fin. GSM</th>
												<th width="80">Fin. Dia</th>
												<th width="100">Color/Code</th>
												<th width="80">Book Qty.</th>
												<th width="80">Total Reqn. Qty.</th>
												<th width="80">Balance</th>
												<th width="80">Current Reqn. Qty.</th>
												<th>Remarks</th>
											</thead>
											<tbody>
												<?php } ?>
												<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:15px">
													<td width="30" align="center"><? echo $i; ?></td>
													<td width="100" align="center"><? echo "B: ".$buyer.'<hr>Cust. B :'.$cbuyer_arr[$cbuyer]; ?></td>
													<td width="150" align="center"><? echo $job_no; ?></td>
													<td width="120" align="center"><? 
													
													echo $booking_no."<hr>".$int_ref; 
													
													?>
													</td>
													<td width="80" align="center"><? echo $body_part[$k_bpart_id]; ?></td>
													<td width="100" align="center"><? echo $k_construction; ?></td>
													<td width="180" align="center"><? echo $k_composition; ?></td>
													<td width="100" align="center" title="<? echo trim($row['program_booking_pi_no']).'='.$k_bpart_id.'='.$k_ctype_id.'='.trim($k_composition).'='.$k_color_id; ?>">
														<?
															$color_range = $sales_wise_color_range[trim($row['program_booking_pi_no'])][$k_bpart_id][$k_ctype_id][trim($k_composition)][$k_color_id];
															if($color_range =="")
															{
																$color_range = $tr_sales_wise_color_range[$row['po_id']][$k_bpart_id][$k_ctype_id][trim($k_composition)];
															}
															echo $color_range = implode(",",array_unique(explode(",",$color_range)));
															?>
													</td>
													<td width="100" align="center"><? echo $color_type[$k_ctype_id]; ?></td>
													<td width="80" align="center"><? echo $k_gsm; ?></td>
													<td width="80" align="center"><? echo $k_dia; ?></td>
													<td width="100" align="center"><? echo $color_arr[$k_color_id]; ?></td>
													<td width="80" align="right"><? echo number_format($row['booking_qty'], 2); ?></td>
													<td width="80" align="right"><? echo number_format($totReqQty, 2); ?></td>
													<td width="80" align="right"><? echo $balances = number_format($balance, 2); ?></td>
													<td width="80" align="right"><? echo number_format($row['reqn_qty'], 2); ?></td>
													<td><p><? echo $row['remarks']; ?></p></td>
												</tr>
											</tbody>
											<?
											$i++;
											$sub_totBookingQty += $row['booking_qty'];
											$sub_totCurrQty += $totReqQty;
											$sub_totRenqQty += $totReqQty;
											$sub_balance_tot += $balance;
											$sub_totCurrRed_Qty += $row['reqn_qty'];

											$totBookingQty += $row['booking_qty'];
											$totCurrQty += $totReqQty;
											$totRenqQty += $totReqQty;
											$balance_tot += $balance;
											$totCurrRed_Qty += $row['reqn_qty'];
										}
									}
								}
							}
						}
					}
					?>
						<tr>
							<td colspan="12" align="right"><strong>Sub Total :</strong></td>
							<td align="right"><b><? echo number_format($sub_totBookingQty, 2); ?></b></td>
							<td align="right"><b><? echo number_format($sub_totCurrQty, 2); ?></b></td>
							<td align="right"><b><? echo number_format($sub_balance_tot, 2); ?></b></td>
							<td align="right"><b><? echo number_format($sub_totCurrRed_Qty, 2); ?></b></td>
							<td colspan="4">&nbsp;</td>
						</tr>
					<?
				}
			}
			?>
			<tfoot bgcolor="#dddddd" style="font-size:13px">
				<tr style="font-size: 18px;">
					<td colspan="12" align="right"><strong>Total :</strong></td>
					<td align="right"><b><? echo number_format($totBookingQty, 2); ?></b></td>
					<td align="right"><? echo number_format($totCurrQty, 2); ?></b></td>
					<td align="right"><? echo number_format($balance_tot, 2); ?></b></td>
					<td align="right"><? echo number_format($totCurrRed_Qty, 2); ?></b></td>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<br>
		<strong style="font: bold 12px tahoma;">Special Instruction</strong>
		<table border="1" rules="all" cellpadding="3" style="font-size: 12px;">
			<tr bgcolor="#CCCCCC">
				<td align="center"><strong>Sl</strong></td>
				<td><strong>Terms</strong></td>
			</tr>
			<?
			$reqn_number=$dataArray[0][csf('reqn_number')];
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$reqn_number'");
			if (count($data_array) > 0)
			{
				$i = 0;
				foreach ($data_array as $row)
				{
					$i++;
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td><? echo $row[csf('terms')]; ?></td>
					</tr>
					<?
				}
			}
	        ?>
	    </tbody>
		<?
		echo signature_table(93, $ex_data[0], "1460px");
		?>
	</div>
	<?
	exit();
}

?>

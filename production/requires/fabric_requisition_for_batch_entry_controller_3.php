<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name = $_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if ($action == "po_popup") {
	echo load_html_head_contents("Fabric Info", "../../", 1, '', '', '', '');
	extract($_REQUEST);
	?>
	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][553]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function () {
			set_field_level_access( <? echo $company_id; ?> );
		}
		selected_name = new Array();

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function check_all_data(){
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				if($("#search"+i).css("display") !='none'){
				document.getElementById("search"+i).click();
				}
			}
		}

		function js_set_value(i,str_data, hidden_is_sales) 
		{
			toggle(document.getElementById('search' + i), '#FFFFCC');
			var hidden_data=str_data;

			//hidden_data = jobNo + "**" + BookingNo + "**" + fileNo + "**" + intRef + "**" + colorId + "**" + bodyPartId + "**" + diaWidth + "**" + gsm + "**" + deterId + "**" + isSales + "**" + bookingWithoutOrder;
			if( jQuery.inArray( hidden_data , selected_name ) == -1 )
			{
				selected_name.push( hidden_data );
			}
			else
			{
				for( var i = 0; i < selected_name.length; i++ )
				{
					if( selected_name[i] == hidden_data ) break;
				}

				selected_name.splice( i, 1 );
				if(selected_name.length==0)
				{
					document.getElementById('hidden_data').value="";
				}
			}
			var name = '';
			for( var i = 0; i < selected_name.length; i++ )
			{
				name += selected_name[i] + '!!!!';
			}
			$('#hidden_data').val( name );
			$('#hidden_is_sales').val( hidden_is_sales );
		}

		function reset_hide_field() {
			//$('#hidden_data').val('');
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
			else {
				$('#td_search').text('Enter Order No');
			}
		}

		function fnc_close() {
			parent.emailwindow.hide();
		}

		function field_visible(thisValue) 
		{
			//alert(thisValue);
			$("#chkIsSales").prop("checked", false);
			if (thisValue == 2) 
			{
				$("#is_sales_booking").css("display", "block");
			} 
			else if (thisValue == 8) 
			{
				$("#is_sales_booking").css("display", "block");
			}
			else 
			{
				$("#is_sales_booking").css("display", "none");
			}
		}
		function fnc_show_dtls()
		{
			show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_val').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('cbo_search_category').value, 'create_fabric_search_list_view', 'search_div', 'fabric_requisition_for_batch_entry_controller_3', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');
		}
	</script>
</head>

<body>
	<div align="center" style="width:1035px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:1030px; margin-left:2px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="500" class="rpt_table">
					<thead>
						<tr>
							<th  colspan="5">
								<?
								echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
								?>
							</th>
						</tr>
						<tr>
							<th>Buyer</th>
							<th>Job Year</th>
							<th>Search Type</th>
							<th id="td_search"><?php echo ($field_label_arr[1]['cbo_search_by']['defalt_value'] == 6) ? "Enter Sales Order No" : "Enter Reff No" ?></th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
								<input type="hidden" name="hidden_is_sales" id="hidden_is_sales" class="text_boxes" value="">
							</th>
						</tr>
						
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
							if ($hdn_is_sales==1) 
							{
								$is_selected=6;$is_disabled=1;
							}
							$search_by_arr = array(1 => "Ref. No", 2 => "Booking No", 3 => "Job No", 4 => "File No", 5 => "Order No", 6 => "Sales Order No", 8 => "Style Ref.");
							echo create_drop_down("cbo_search_by", 90, $search_by_arr, "", 0, "--Select--", $is_selected, "set_search_by(this.value);field_visible(this.value);", $is_disabled);
							?>
						</td>
						<td>
							<input type="text" name="txt_search_val" id="txt_search_val" style="width:100px"
							class="text_boxes"/>
							<div id="is_sales_booking" style="display: none;"><input type="checkbox" name="chkIsSales" id="chkIsSales"/> <label
								for="chkIsSales">For sales order </label></div>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show_dtls();"  style="width:100px;"/>
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

if ($action == "create_fabric_search_list_view") 
{
	$data = explode("_", $data);

	$company_id        = $data[0];
	$buyer_id          = $data[1];
	$search_type       = trim($data[2]);
	$search_val        = trim($data[3]);
	$cbo_year          = trim($data[4]);
	$is_sales_booking  = $data[5];
	$search_category   = $data[6];
	$sales_table_sql   = "";
	$sales_join_sql    = "";
	
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

	if (trim($cbo_year) != 0) 
	{
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
	//echo $search_type.'='.$search_category;die;
	$search_cond = ""; $nonOrdBooking_cond="";$samp_ref_cond2="";
	if ($search_type == 1) 
	{
		if($search_val!="")
		{
			//$search_cond = "and d.grouping like '%" . $search_val . "%'";
			//$samp_ref_cond2 = "and f.grouping like '%" . $search_val . "%'";
			if ($cbo_year != 0) 
			{
				$search_cond =" and b.job_no like '%-".substr($cbo_year,-2)."-%'";
			}
			
			if($search_category==1) {
				$search_cond.=" and d.grouping = '$search_val'";
				$samp_ref_cond2 = "and f.grouping = '$search_val'";
			}
			else if($search_category==0 || $search_category==4) {
				$search_cond.=" and d.grouping like '%$search_val%'";
				$samp_ref_cond2 = "and f.grouping like '%$search_val%'";
			}
			else if($search_category==2) {
				$search_cond.=" and d.grouping like '$search_val%'";
				$samp_ref_cond2 = "and f.grouping like '$search_val%'";
			}
			else if($search_category==3) {
				$search_cond.=" and d.grouping like '%$search_val'";
				$samp_ref_cond2 = "and f.grouping like '%$search_val'";
			}
			else {
				$search_cond.="";
				$samp_ref_cond2 ="";
			}
		}
	} 
	else if ($search_type == 2 && $is_sales_booking == "false") 
	{
		//if ($search_val != "") $search_cond = "and a.booking_no like '%" . $search_val . "'";
		//if ($search_val != "") $nonOrdBooking_cond = "and f.booking_no like '%" . $search_val . "'";

		if ($search_val != "") 
		{
			if($search_category==1) {
				$search_cond.=" and a.booking_no = '$search_val'";
				$nonOrdBooking_cond = "and f.booking_no = '$search_val'";
			}
			else if($search_category==0 || $search_category==4) {
				$search_cond.=" and a.booking_no like '%$search_val%'";
				$nonOrdBooking_cond = "and f.booking_no like '%$search_val%'";
			}
			else if($search_category==2) {
				$search_cond.=" and a.booking_no like '$search_val%'";
				$nonOrdBooking_cond = "and f.booking_no like '$search_val%'";
			}
			else if($search_category==3) {
				$search_cond.=" and a.booking_no like '%$search_val'";
				$nonOrdBooking_cond = "and f.booking_no like '%$search_val'";
			}
			else {
				$search_cond.="";
				$nonOrdBooking_cond ="";
			}
		}
	} 
	else if ($search_type == 3) // Job no
	{
		//if ($search_val != "") $search_cond = "and b.job_no like '%" . $search_val . "'";
		if($search_val!="")
		{
			if ($cbo_year != 0) 
			{
				$search_cond =" and b.job_no like '%-".substr($cbo_year,-2)."-%'";
			}
			
			if($search_category==1) {
				$search_cond.=" and e.job_no_prefix_num = '$search_val'";
			}
			else if($search_category==0 || $search_category==4) {
				$search_cond.=" and b.job_no like '%$search_val%'";
			}
			else if($search_category==2) {
				$search_cond.=" and b.job_no like '$search_val%'";
			}
			else if($search_category==3) {
				$search_cond.=" and b.job_no like '%$search_val'";
			}
			else {$search_cond.="";}
		}
	}
	else if ($search_type == 8 && $is_sales_booking == "false") // Style Ref
	{
		if($search_val!="")
		{			
			if($search_category==1) {
				$search_cond.=" and e.style_ref_no = '$search_val'";
			}
			else if($search_category==0 || $search_category==4) {
				$search_cond.=" and b.style_ref_no like '%$search_val%'";
			}
			else if($search_category==2) {
				$search_cond.=" and b.style_ref_no like '$search_val%'";
			}
			else if($search_category==3) {
				$search_cond.=" and b.style_ref_no like '%$search_val'";
			}
			else {$search_cond.="";}
		}
	}
	else if ($search_type == 8 && $is_sales_booking == "true") // Style Ref
	{
		if (trim($cbo_year) != 0) 
		{
			if ($db_type == 0) $year_cond = " and YEAR(a.insert_date)=$cbo_year";
			else if ($db_type == 2) $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond = "";
		} 
		else $year_cond = "";
		if($search_val!="")
		{			
			if($search_category==1) {
				$search_cond.=" and a.style_ref_no = '$search_val'";
			}
			else if($search_category==0 || $search_category==4) {
				$search_cond.=" and a.style_ref_no like '%$search_val%'";
			}
			else if($search_category==2) {
				$search_cond.=" and a.style_ref_no like '$search_val%'";
			}
			else if($search_category==3) {
				$search_cond.=" and a.style_ref_no like '%$search_val'";
			}
			else {$search_cond.="";}
		}
	}
	else if ($search_type == 4) 
	{
		//if ($search_val != "") $search_cond = "and d.file_no like '%" . $search_val . "%'";
		if($search_val!="")
		{
			if ($cbo_year != 0) 
			{
				$search_cond =" and b.job_no like '%-".substr($cbo_year,-2)."-%'";
			}
			
			if($search_category==1) {
				$search_cond.=" and d.file_no = '$search_val'";
			}
			else if($search_category==0 || $search_category==4) {
				$search_cond.=" and d.file_no like '%$search_val%'";
			}
			else if($search_category==2) {
				$search_cond.=" and d.file_no like '$search_val%'";
			}
			else if($search_category==3) {
				$search_cond.=" and d.file_no like '%$search_val'";
			}
			else {$search_cond.="";}
		}
	} 
	else if ($search_type == 5) 
	{
		//if ($search_val != "") $search_cond = "and d.po_number like '%" . $search_val . "%'";
		if($search_val!="")
		{
			if ($cbo_year != 0) 
			{
				$search_cond =" and b.job_no like '%-".substr($cbo_year,-2)."-%'";
			}
			
			if($search_category==1) {
				$search_cond.=" and d.po_number = '$search_val'";
			}
			else if($search_category==0 || $search_category==4) {
				$search_cond.=" and d.po_number like '%$search_val%'";
			}
			else if($search_category==2) {
				$search_cond.=" and d.po_number like '$search_val%'";
			}
			else if($search_category==3) {
				$search_cond.=" and d.po_number like '%$search_val'";
			}
			else {$search_cond.="";}
		}
	} 
	else if ($search_type == 6 || $is_sales_booking == "true") 
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
	$lib_deterData = sql_select("select a.id,a.construction,b.copmposition_id,b.percent,a.color_range_id from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($lib_deterData as $row) {
		$determin_array[$row[csf('id')]][1]= $row[csf('construction')];
		$determin_array[$row[csf('id')]][2]= $composition[$row[csf('copmposition_id')]].' '.$row[csf('percent')].'%';
	}

	$reqn_qnty_array = array();
	$reqnData = sql_select("select reqn_qty, color_id, job_no, buyer_id, body_part_id, gsm_weight, dia_width, determination_id, grouping, file_no from pro_fab_reqn_for_batch_dtls where entry_form=553 and status_active=1 and is_deleted=0");
	foreach ($reqnData as $row) 
	{
		$key = $row[csf('color_id')] ."=". $row[csf('job_no')] ."=". $row[csf('buyer_id')] ."=". $row[csf('body_part_id')] ."=". $row[csf('determination_id')] ."=". $row[csf('grouping')] ."=". $row[csf('file_no')];
		//."=". $row[csf('gsm_weight')] ."=". $row[csf('dia_width')]
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
	}
	if ($db_type == 2) 
	{
		$select_colorType = " listagg(cast(b.color_type_id as varchar2(4000)),',') within group (order by b.color_type_id) "; 
		$select_colorType_2 = " listagg(cast(g.color_type_id as varchar2(4000)),',') within group (order by g.color_type_id) ";
		$select_gsm = " listagg(cast(b.gsm_weight as varchar2(4000)),',') within group (order by b.gsm_weight) ";
		$select_gsm_2 = " listagg(cast(g.gsm_weight as varchar2(4000)),',') within group (order by g.gsm_weight) ";

		$select_dia = " listagg(cast(c.dia_width as varchar2(4000)),',') within group (order by c.dia_width) ";
		$select_dia_2 = " listagg(cast(g.dia as varchar2(4000)),',') within group (order by g.dia) ";
		$select_dia_3 = " listagg(cast(b.dia as varchar2(4000)),',') within group (order by b.dia) ";
	} 
	else 
	{
		$select_colorType = " group_concat(b.color_type_id) ";
		$select_colorType_2 = " group_concat(g.color_type_id) ";
		$select_gsm = " group_concat(b.gsm_weight) ";
		$select_gsm_2 = " group_concat(g.gsm_weight) ";
	}
	//echo $search_cond;die;
	if ($search_type == 6 || $is_sales_booking == "true") // Sales order
	{
		$sql = "SELECT a.id,a.job_no,a.sales_booking_no booking_no,a.buyer_id,a.po_buyer,a.within_group,b.body_part_id, b.color_type_id, $select_gsm as gsm_weight, b.fabric_desc, $select_dia_3 as dia_width, b.color_id as color_number_id,sum(b.grey_qty) grey_fab_qnty,b.determination_id febric_description_id, a.customer_buyer, 0 as booking_without_order, a.style_ref_no 
		from fabric_sales_order_mst a, fabric_sales_order_dtls b 
		where a.id = b.mst_id and a.company_id=$company_id $year_cond $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.job_no,a.sales_booking_no,a.buyer_id,a.po_buyer,a.within_group,b.body_part_id, b.color_type_id, b.fabric_desc, b.color_id,b.determination_id, a.customer_buyer, a.style_ref_no";
		// echo $sql;die;
		$fso_data_array=sql_select($sql);
		$sales_booking_arr=array();
		foreach ($fso_data_array as $key => $row) 
		{
			$sales_booking_arr[] = "'".$row[csf('booking_no')]."'";
		}
		if (!empty($sales_booking_arr)) 
		{
			$booking_cond = " and a.booking_no in (".implode(",",array_unique($sales_booking_arr)).")";
			$booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping ref_no, c.job_id from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c 
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond 
			group by a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping,c.job_id");

            foreach ($booking_details as $po_row)
            {
				$po_arr[$po_row[csf("booking_no")]]["buyer_id"]          = $po_row[csf("buyer_id")];
				$po_arr[$po_row[csf("booking_no")]]["job_no"]            = $po_row[csf("job_no")];
				$po_arr[$po_row[csf("booking_no")]]["job_id"]            = $po_row[csf("job_id")];
				$po_arr[$po_row[csf("booking_no")]]["int_ref"]           = $po_row[csf("ref_no")];
            }
		}
	} 
	else // with order and non order
	{
		$sql = "SELECT a.booking_no, a.buyer_id,b.job_no, b.body_part_id, $select_colorType as color_type_id, $select_gsm as gsm_weight, b.lib_yarn_count_deter_id as febric_description_id, b.construction, b.composition, $select_dia as dia_width, c.fabric_color_id as color_number_id, d.file_no, d.grouping, sum(c.grey_fab_qnty) as grey_fab_qnty, 0 as booking_without_order, e.style_ref_no, e.id as job_id
		FROM wo_booking_mst a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c, wo_po_break_down d, wo_po_details_master e 
		WHERE b.company_id=$company_id $buyer_id_cond $year_cond $search_cond and a.job_no=b.job_no and b.id=c.pre_cost_fabric_cost_dtls_id and a.booking_no=c.booking_no and d.id=c.po_break_down_id and a.status_active=1 and a.job_no=e.job_no and a.booking_type in(1,4) and c.dia_width != '0' and c.fabric_color_id !=0 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.job_id=e.id
		GROUP BY a.booking_no, a.buyer_id,b.job_no, b.body_part_id,b.lib_yarn_count_deter_id, b.construction, b.composition, c.fabric_color_id, d.file_no, d.grouping, e.style_ref_no, e.id";// with order  b.gsm_weight,
		// echo $sql;	
		if (($search_type == 7) && $is_sales_booking == 'false') // non order, is not used confirm by Tofael vai
		{
		   	$sql ="SELECT f.booking_no,f.buyer_id, null as job_no, g.body_part as body_part_id, $select_colorType_2 as color_type_id, $select_gsm_2  as gsm_weight, g.lib_yarn_count_deter_id as febric_description_id, g.construction, g.composition, $select_dia_2 as dia_width, g.fabric_color as color_number_id, null as file_no, f.grouping  as grouping, sum(g.grey_fabric) as grey_fab_qnty, 1 as booking_without_order
		  	from wo_non_ord_samp_booking_mst f, wo_non_ord_samp_booking_dtls g where f.booking_no = g.booking_no and ( g.fabric_source = 1 or f.fabric_source = 1) and f.booking_type in (1,4) and f.company_id =$company_id $nonOrdBooking_cond $samp_ref_cond2 $buyer_id_cond2 $year_cond_no_order 
		  	group by f.booking_no,f.buyer_id,f.grouping ,g.lib_yarn_count_deter_id, g.body_part, g.construction, g.composition, g.fabric_color ";
			//g.gsm_weight,
		}

		$sql .= " order by booking_no, color_number_id";
	}
	//echo "<pre>";print_r($reqn_qnty_array);
	//echo $sql;
	$result = sql_select($sql);
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" style="width: 1050px;">
		<thead>
			<th width="25">Sl</th>
			<th width="50">Buyer</th>
			<th width="100">Ref. No</th>
			<th width="80">Style Ref </th>
			<th width="80">Booking No</th>
			<th width="90">Body Part</th>
			<th width="70">Color Type</th>
			<th width="80">Construction</th>
			<th width="100">Composition</th>
			<th width="40">F. GSM</th>
			<th width="40">F. Dia</th>
			<th width="100">Fabric Color</th>
			<th width="80">Job No</th>
			<th width="80">Sales Order No</th>
			<th width="40">File No</th>
            <th width="70">Requisition Balance</th>
		</thead>
	</table>
	<div style="max-height:250px; overflow-y:scroll;width: 1070px;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search" style="width: 1050px;">
			<?
			$i = 1;
			foreach ($result as $row) 
			{
				$is_sales= 0;
				$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
				// sales order wise search
				if ($search_type == 6 || $is_sales_booking == "true") 
				{
					$is_sales       = 1;
					$within_group   = $row[csf("within_group")];
					$sales_order_no = $job_no = $row[csf("job_no")];
					$booking_no     = $row[csf("booking_no")];
					$fab_desc       = explode(",", $row[csf("fabric_desc")]);
					$construction   = $fab_desc[0];
					$composition    = $fab_desc[1];
					if($within_group == 1)
					{
						$buyer = $row[csf('po_buyer')];
						$job_no=$po_arr[$row[csf("booking_no")]]["job_no"];
						$job_id=$po_arr[$row[csf("booking_no")]]["job_id"];
						$int_ref=$po_arr[$row[csf("booking_no")]]["int_ref"];
					}
					else
					{
						$buyer = $row[csf('buyer_id')];
						$cbuyer = $row[csf('customer_buyer')];
						$job_no='';
						$job_id='';
						$int_ref='';
					}
					$job_id   		= $row[csf("id")];//fso_id
				}
				else 
				{
					$job_no         = $row[csf("job_no")];
					$job_id         = $row[csf("job_id")];
					$booking_no     = $row[csf("booking_no")];
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
					$int_ref=$row[csf('grouping')];
				}

				$key = $row[csf('color_number_id')] ."=". $row[csf('job_no')] ."=". $buyer ."=". $row[csf('body_part_id')] ."=". $row[csf('febric_description_id')] ."=". $row[csf('grouping')] ."=". $row[csf('file_no')];
				//."=". $row[csf('gsm_weight')] ."=". $row[csf('dia_width')]

				//echo $key."<br>";
				$totReqnQty  = $reqn_qnty_array[$key];
				$booking_qty = 0;
				$booking_qty = $row[csf('grey_fab_qnty')];
				$balance_qty     = ($booking_qty - $totReqnQty);

				$gsm_weight = implode(",",array_unique(explode(",",$row[csf('gsm_weight')])));
				$dia_width = implode(",",array_unique(explode(",",$row[csf('dia_width')])));
				$color_types = array_unique(explode(",",$row[csf('color_type_id')]));
				$color_type_names="";
				foreach ($color_types as  $val) 
				{
					$color_type_names .= $color_type[$val].",";
				}
				if(number_format($balance_qty,2,'.','') > 0)
				{
					$hidden_data = $job_no . "**" . $booking_no . "**" . $row[csf('file_no')] . "**" . $int_ref . "**" . $row[csf('color_number_id')] . "**" . $row[csf('body_part_id')] . "**" . $dia_width . "**" . $gsm_weight . "**" . $row[csf('febric_description_id')] . "**" . $is_sales . "**" . $row[csf('booking_without_order')] . "**" . $job_id;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>,'<? echo $hidden_data; ?>','<? echo $is_sales; ?>')">
						<td width="25" align="center">
							<? echo $i; ?>
							<input type="hidden" name="buyerId[]" id="buyerId<? echo $i; ?>" value="<? echo $buyer; ?>"/>
							<input type="hidden" name="jobNo[]" id="jobNo<? echo $i; ?>" value="<? echo $job_no; ?>"/>
							<input type="hidden" name="BookingNo[]" id="BookingNo<? echo $i; ?>" value="<? echo $booking_no; ?>"/>
							<input type="hidden" name="intRef[]" id="intRef<? echo $i; ?>" value="<? echo $int_ref; ?>"/>
							<input type="hidden" name="fileNo[]" id="fileNo<? echo $i; ?>" value="<? echo $row[csf('file_no')]; ?>"/>
							<input type="hidden" name="bookingQty[]" id="bookingQty<? echo $i; ?>" value="<? echo number_format($booking_qty,2, '.', ''); ?>"/>
							<input type="hidden" name="totReqnQty[]" id="totReqnQty<? echo $i; ?>" value="<? echo number_format($totReqnQty,2, '.', ''); ?>"/>
							<input type="hidden" name="balanceQty[]" id="balanceQty<? echo $i; ?>" value="<? echo number_format($balance_qty,2, '.', ''); ?>"/>
							<input type="hidden" name="colorId[]" id="colorId<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
							<input type="hidden" name="bodyPartId[]" id="bodyPartId<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
							<input type="hidden" name="deterId[]" id="deterId<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
							<input type="hidden" name="gsm[]" id="gsm<? echo $i; ?>" value="<? echo $gsm_weight; ?>"/>
							<input type="hidden" name="diaWidth[]" id="diaWidth<? echo $i; ?>" value="<? echo $dia_width; ?>"/>
							<input type="hidden" name="isSales[]" id="isSales<? echo $i; ?>" value="<? echo $is_sales; ?>"/>
							<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder<? echo $i; ?>" value="<? echo $row[csf('booking_without_order')]; ?>"/>
						</td>
						<td width="50" align="center"><? echo $buyer_arr[$buyer]; ?></td>
						<td width="100"><p><? echo $int_ref; ?></p></td>
						<td width="80" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="80" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="90" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70" align="center"><p><? echo chop($color_type_names,',');//$color_type[$row[csf('color_type_id')]]; ?></p></td>
						<td width="80" align="center"><p><? echo $construction; ?></p></td>
						<td width="100" align="center"><p><? echo $composition; ?></p></td>
						<td width="40" align="center"><p><? echo $gsm_weight; ?></p></td>
						<td width="40" align="center"><p><? echo $dia_width; ?></p></td>
						<td width="100" align="center"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
						<td width="80" align="center"><p><? echo $job_no; ?></p></td>
						<td width="80" align="center"><p><? echo $sales_order_no; ?></p></td>
						<td width="40"><p><? echo $row[csf('file_no')]; ?></p></td>
						<td width="70" align="right" title="BookQty=<? echo $booking_qty.',PrevQty='.$totReqnQty?>"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
					</tr>
					<?
					$i++;
				}
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FRB', date("Y",time()), 5, "select reqn_number_prefix, reqn_number_prefix_num from pro_fab_reqn_for_batch_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc ", "reqn_number_prefix","reqn_number_prefix_num"));
		$id=return_next_id( "id", "pro_fab_reqn_for_batch_mst", 1 ) ;
				 
		$field_array="id,reqn_number_prefix,reqn_number_prefix_num,reqn_number,company_id,location_id,reqn_date,is_sales,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_requisition_date.",".$hidden_is_sales.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, file_no, grouping, gsm_weight, dia_width, prod_id, determination_id, color_id, batch_color, body_part_id, reqn_qty, booking_without_order,is_sales, booking_qty, entry_form, remarks, yarn_lot, yarn_count, brand_id, inserted_by, insert_date";
		$dtls_id = return_next_id( "id", "pro_fab_reqn_for_batch_dtls", 1 );


		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$poId="poId".$j;
			$buyerId="buyerId".$j;
			$job="job".$j;
			$fileNo="fileNo".$j;
			$grouping="grouping".$j;
			$programBookingId="programBookingId".$j;
			$gsm="gsm".$j;
			$dia="dia".$j;
			$prodId="prodId".$j;
			$bodyPartId="bodyPartId".$j;
			$deterId="deterId".$j;
			$colorId="colorId".$j;
			$batchColorId="batchColorId".$j;
			$reqsnQty="reqsnQty".$j;
			$booking_without_order="booking_without_order".$j;
			$isSales="isSales".$j;
			$bookintQty="bookintQty".$j;
			$remarks="remarks".$j;

			$yLotId="yLotId".$j;
			$yCountId="yCountId".$j;
			$brandId="brandId".$j;

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",'".$$programBookingId."','".$$programBookingId."','".$$poId."','".$$buyerId."','".$$job."','".$$fileNo."','".$$grouping."','".$$gsm."','".$$dia."','".$$prodId."','".$$deterId."','".$$colorId."','".$$batchColorId."','".$$bodyPartId."','".$$reqsnQty."','".$$booking_without_order."','".$$isSales ."','" . $$bookintQty ."',553,'".$$remarks."','".$$yLotId."','".$$yCountId."','".$$brandId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$dtls_id = $dtls_id+1;
		}

		//echo "10**insert into pro_fab_reqn_for_batch_mst (".$field_array.") values ".$data_array;oci_rollback($con);die;
		//echo "10**insert into pro_fab_reqn_for_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		
		$rID=sql_insert("pro_fab_reqn_for_batch_mst",$field_array,$data_array,0);
		$rID2=sql_insert("pro_fab_reqn_for_batch_dtls",$field_array_dtls,$data_array_dtls,1);
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="company_id*location_id*reqn_date*updated_by*update_date";
		$data_array=$cbo_company_id."*".$cbo_location_name."*".$txt_requisition_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id, mst_id, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, file_no, grouping, gsm_weight, dia_width, prod_id, determination_id, color_id, batch_color, body_part_id, reqn_qty, booking_without_order,is_sales, booking_qty, entry_form, remarks, yarn_lot, yarn_count, brand_id, inserted_by, insert_date";
		$field_array_update="batch_color*reqn_qty*remarks*updated_by*update_date";
		$dtls_id = return_next_id( "id", "pro_fab_reqn_for_batch_dtls", 1 );

		
		$deleted_id=str_replace("'","",$txt_deleted_id);
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$poId="poId".$j;
			$buyerId="buyerId".$j;
			$job="job".$j;
			$fileNo="fileNo".$j;
			$grouping="grouping".$j;
			$programBookingId="programBookingId".$j;
			$gsm="gsm".$j;
			$dia="dia".$j;
			$prodId="prodId".$j;
			$bodyPartId="bodyPartId".$j;
			$deterId="deterId".$j;
			$colorId="colorId".$j;
			$batchColorId="batchColorId".$j;
			$reqsnQty="reqsnQty".$j;
			$booking_without_order="booking_without_order".$j;
			$isSales="isSales".$j;
			$bookintQty="bookintQty".$j;
			$remarks="remarks".$j;
			$dtlsId="dtlsId".$j;

			$yLotId="yLotId".$j;
			$yCountId="yCountId".$j;
			$brandId="brandId".$j;
			
			if($$dtlsId>0)
			{
				if($$reqsnQty>0)
				{
					$dtlsId_arr[]=$$dtlsId;
					$data_array_update[$$dtlsId]=explode("*",("'".$$batchColorId."'*'".$$reqsnQty."'*'".$$remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					if($deleted_id=="")
					{
						$deleted_id = $$dtlsId;
					}
					else{
						$deleted_id.=",".$$dtlsId;
					}
					
				}
			}
			else
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",'".$$programBookingId."','".$$programBookingId."','".$$poId."','".$$buyerId."','".$$job."','".$$fileNo."','".$$grouping."','".$$gsm."','".$$dia."','".$$prodId."','".$$deterId."','".$$colorId."','".$$batchColorId."','".$$bodyPartId."','".$$reqsnQty."','".$$booking_without_order."','".$$isSales ."','" . $$bookintQty ."',553,'".$$remarks."','".$$yLotId."','".$$yCountId."','".$$brandId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$dtls_id = $dtls_id+1;
	
			}
		}
		
		$rID=sql_update("pro_fab_reqn_for_batch_mst",$field_array,$data_array,"id",$update_id,0);
		
		$rID2=true; $rID3=true; $statusChange=true;
		if(count($data_array_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_fab_reqn_for_batch_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr ));

			//echo "10**".bulk_update_sql_statement( "pro_fab_reqn_for_batch_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr );
			//oci_rollback($con);die;
		}
		
		if($data_array_dtls!="")
		{
			$rID3=sql_insert("pro_fab_reqn_for_batch_dtls",$field_array_dtls,$data_array_dtls,1);

			//echo "10**insert into pro_fab_reqn_for_batch_dtls ($field_array_dtls) values $data_array_dtls <br>";die;
		}

		$deleted_id=chop($deleted_id,",");
		if($deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChange=sql_multirow_update("pro_fab_reqn_for_batch_dtls",$field_array_status,$data_array_status,"id",$deleted_id,0);
		}
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusChange;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="requisition_popup")
{
	echo load_html_head_contents("Requisition Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
	
		function js_set_value(data)
		{
			$('#hidden_reqn_id').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Location</th>
                    <th>Requisition Date Range</th>
                    <th id="search_by_td_up" width="180">Requisition No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_location_id", 150,"select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name",'id,location_name', 1, '-- Select Location --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
					</td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_reqn_no" id="txt_reqn_no" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_location_id').value+'_'+<? echo $company_id; ?>, 'create_reqn_search_list_view', 'search_div', 'fabric_requisition_for_batch_entry_controller_3', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_reqn_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$location_id =$data[3];
	$company_id =$data[4];

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and reqn_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and reqn_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and reqn_number like '$search_string'";
	}
	
	$location_cond="";
	if($location_id>0)
	{
		$location_cond="and location_id=$location_id";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	$sql = "select a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date from pro_fab_reqn_for_batch_mst a, pro_fab_reqn_for_batch_dtls b where b.entry_form=553 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and company_id=$company_id $search_field_cond $location_cond $date_cond group by a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date  order by id"; 
	$arr=array(0=>$location_arr);
	
	echo create_list_view("tbl_list_search", "Location, Year, Requisition No, Requisition Date", "250,70,130","700","200",0, $sql, "js_set_value", "id", "", 1, "location_id,0,0,0", $arr, "location_id,year,reqn_number_prefix_num,reqn_date","","",'0,0,0,3','');
	
	exit();
}

if($action=='populate_data_from_requisition')
{
	$data_array=sql_select("select id, reqn_number, company_id, location_id, reqn_date, is_sales from pro_fab_reqn_for_batch_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("reqn_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value 			= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_requisition_date').value 		= '".change_date_format($row[csf("reqn_date")])."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hidden_is_sales').value 				= '".$row[csf("is_sales")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_fabric_requisition_for_batch',1);\n";  
		exit();
	}
}

if( $action == 'populate_list_view' ) 
{
 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$sql="SELECT id, receive_basis, program_booking_pi_no, program_booking_pi_id, body_part_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, batch_color, reqn_qty, remarks, gsm_weight, dia_width, booking_qty, is_sales, booking_without_order, yarn_count, yarn_lot, brand_id from pro_fab_reqn_for_batch_dtls where mst_id='$data' and entry_form=553 and status_active=1 and is_deleted=0";
	$result=sql_select($sql);

	foreach ($result as $row)
	{
		if ($row[csf('is_sales')] == 1) {
			$fsoArr[$row[csf('po_id')]] = $row[csf('po_id')];
		}else{
			$orderArr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$prodIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$programNoArr[$row[csf('program_booking_pi_id')]] = $row[csf('program_booking_pi_id')];
	}

	$order_ids = implode(",",$orderArr);
	$po_arr=array();
	$poDataArr=sql_select("SELECT a.id, a.po_number, a.grouping, a.file_no, a.pub_shipment_date, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in ($order_ids)");
	foreach($poDataArr as $row )
	{
		$po_arr[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_arr[$row[csf('id')]]['ref']=$row[csf('grouping')];
		$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
		$po_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$po_arr[$row[csf('id')]]['shipment_date']=$row[csf('pub_shipment_date')];
	}

	$prod_ids = implode(",",$prodIdArr);
	$product_arr=array();
 	$sql_product="SELECT id, gsm, dia_width from product_details_master where item_category_id=13 and id in ($prod_ids)";
	$data_array_product=sql_select($sql_product);
	foreach( $data_array_product as $row )
	{
		$product_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$product_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	$program_nos = implode(",",$programNoArr);
	$reqn_qnty_array=array();
	$reqnData=sql_select("SELECT  program_booking_pi_id, po_id, prod_id, determination_id, color_id, body_part_id, sum(reqn_qty) as qnty from pro_fab_reqn_for_batch_dtls where entry_form=553 and status_active=1 and is_deleted=0 and program_booking_pi_id in ($program_nos) and mst_id !=$data group by program_booking_pi_id, po_id, prod_id, determination_id, color_id, body_part_id");

	foreach($reqnData as $row)
	{
		$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]+=$row[csf('qnty')];
	}


	//Current Grey Stock Quantity here

	$auto_rcv_sql="SELECT f.id as program_no, g.booking_no, e.job_no, d.id as po_id, d.po_number, d.grouping, d.file_no, e.buyer_name, b.prod_id, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, c.quantity, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g
	where a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=b.mst_id and c.entry_form=2 and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and b.status_active=1 and b.is_deleted=0 and a.booking_id=f.id and f.mst_id=g.id and b.trans_id!=0 and d.id in ($order_ids)";
	//echo $auto_rcv_sql;die;
	$auto_rcv_data = sql_select($auto_rcv_sql);

	foreach ($auto_rcv_data as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];

		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("quantity")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_recv_gross=sql_select("SELECT g.booking_id as program_no, i.booking_no, e.job_no, e.buyer_name, d.id as po_id, d.po_number, d.file_no, d.grouping, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, sum(c.quantity) as knitting_qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e, inv_receive_master g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i
	where  a.id=b.mst_id and a.receive_basis=9 and a.item_category=13 and a.entry_form=22 and b.id=c.dtls_id  and c.entry_form=22 and c.po_breakdown_id=d.id and d.job_id=e.id and g.entry_form=2 and a.booking_id=g.id and b.status_active=1 and b.is_deleted=0 and g.booking_id=h.id and h.mst_id=i.id  and d.id in ($order_ids)
	group by g.booking_id, i.booking_no, e.job_no, e.buyer_name, d.id, d.po_number, d.file_no, d.grouping, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id"); 

	foreach ($sql_recv_gross as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("knitting_qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_recv_roll = sql_select("SELECT f.id as program_no, g.booking_no, d.grouping, d.id as po_id, d.po_number, d.file_no, e.job_no, e.buyer_name, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, c.qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from  pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e, pro_roll_details a,
	ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g
	where  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=2 and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and c.barcode_no=a.barcode_no and a.entry_form=2 and a.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and b.status_active=1 and b.is_deleted=0 and d.id in ($order_ids)");
	

	foreach ($sql_recv_roll as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_iss_roll = sql_select("SELECT f.id as program_no, g.booking_no, d.po_number, d.id as po_id, d.grouping, d.file_no, e.job_no, e.buyer_name, i.detarmination_id, i.gsm, i.dia_width, a.body_part_id, a.color_id, a.prod_id, j.quantity
	from inv_grey_fabric_issue_dtls a, pro_roll_details b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e, 
	ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g, product_details_master i, order_wise_pro_details j
	where a.id=b.dtls_id and b.barcode_no=c.barcode_no and b.entry_form=61 and c.entry_form=2 and b.booking_without_order=0 and b.is_sales=0
	and b.po_breakdown_id=d.id and d.job_id=e.id and c.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.prod_id=i.id and a.id=j.dtls_id and j.entry_form=61 and d.id in ($order_ids)");

	foreach ($sql_iss_roll as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];
		$iss_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_data_arr[$iss_string] += $val[csf("quantity")];
	}
	//print_r($issue_data_arr);

	$roll_iss_rtn_sql="SELECT h.id as program_no, i.booking_no, f.job_no, f.buyer_name, e.id as po_id, e.po_number, e.file_no, e.grouping, a.body_part_id, a.color_id, a.febric_description_id, a.gsm, a.width, a.prod_id, c.qnty 
	from pro_grey_prod_entry_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, wo_po_break_down e, wo_po_details_master f, inv_receive_master x, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i 
	where a.id=b.dtls_id and b.entry_form=84 and a.id=c.dtls_id and c.entry_form=84 and c.barcode_no=d.barcode_no and d.entry_form=2 and d.receive_basis=2 and c.po_breakdown_id=e.id and e.job_id=f.id and d.mst_id=x.id and x.booking_id=h.id and h.mst_id=i.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id in ($order_ids)";

	$roll_iss_rtn_data=sql_select($roll_iss_rtn_sql);
	foreach($roll_iss_rtn_data as $val)
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$iss_ret_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_ret_data_arr[$iss_ret_string] += $val[csf("qnty")];
	}

	$roll_trans_in_sql = "SELECT h.id as program_no, i.booking_no, d.id as po_id, d.po_number, d.grouping, d.file_no, e.job_no, e.buyer_name, g.color_id, b.detarmination_id, b.gsm, b.dia_width, a.to_body_part as body_part_id, a.to_prod_id as prod_id, c.qnty, g.yarn_lot, g.yarn_count, g.brand_id
	from inv_item_transfer_dtls a, product_details_master b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e, pro_roll_details f, pro_grey_prod_entry_dtls g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i
	where a.to_prod_id=b.id and a.id=c.dtls_id and c.entry_form in (82,110) and c.po_breakdown_id=d.id and d.job_id=e.id and c.barcode_no=f.barcode_no and f.receive_basis=2 and f.dtls_id=g.id and f.booking_no=cast(h.id as varchar(4000)) and h.mst_id=i.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id in ($order_ids)";// b.yarn_lot, b.y_count, b.brand_id

	$roll_trans_in_data=sql_select($roll_trans_in_sql);
	foreach($roll_trans_in_data as $val)
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_in_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$trans_in_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count'] .= $count_arr[$val[csf("y_count")]].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count_id'] .= $val[csf("y_count")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	//echo "<pre>";
	//print_r($search_ref);

	$roll_trans_out_sql = "SELECT i.id as program_no, j.booking_no, g.job_no, g.buyer_name, f.id as po_id, f.po_number, f.grouping, f.file_no, a.body_part_id, k.detarmination_id, k.gsm, k.dia_width, e.color_id, a.from_prod_id as prod_id, c.qnty
	from inv_item_transfer_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, pro_grey_prod_entry_dtls e, wo_po_break_down f, wo_po_details_master g, ppl_planning_info_entry_dtls i, ppl_planning_info_entry_mst j, product_details_master k
	where a.id=b.dtls_id and b.entry_form in (82,110) and b.trans_type=6 and a.id=c.dtls_id and c.entry_form in (82,110) and c.barcode_no=d.barcode_no and d.entry_form in(2) and d.receive_basis=2 and d.dtls_id=e.id and b.po_breakdown_id=f.id and f.job_id=g.id and d.booking_no=cast(i.id as varchar(4000)) and i.mst_id=j.id and a.from_prod_id=k.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.id in ($order_ids)";

	$roll_trans_out_data=sql_select($roll_trans_out_sql);
	foreach($roll_trans_out_data as $val)
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_out_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$transfer_out_data_arr[$trans_out_string] += $val[csf("qnty")];
	}

	$sql_book_color = sql_select("SELECT a.id as po_id, b.fabric_color_id, d.color_name from wo_po_break_down a, wo_booking_dtls b, lib_color d where a.id = b.po_break_down_id and b.booking_type=1 and b.is_short=2 and a.id in ($order_ids) and b.fabric_color_id=d.id and b.status_active=1 and b.is_deleted=0 group by a.id, b.fabric_color_id, d.color_name");
	foreach( $sql_book_color as $row) 
	{
		$book_color_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]]=$row[csf('color_name')];
	}
	unset($sql_book_color);

	$i=1;
	foreach ($result as $row)
	{

		$dataStr = $row[csf('program_booking_pi_id')].'*'. $row[csf('po_id')].'*'.$row[csf('prod_id')].'*'. $row[csf('body_part_id')] .'*'. $row[csf('color_id')];

		$grey_stock = $rcv_trans_in_data_arr[$dataStr]['qnty'] + $issue_ret_data_arr[$dataStr] - $issue_data_arr[$dataStr] - $transfer_out_data_arr[$dataStr];
		$grey_stock_without_issue = $rcv_trans_in_data_arr[$dataStr]['qnty'] + $issue_ret_data_arr[$dataStr] - $transfer_out_data_arr[$dataStr];


		
		$totReqnQty=$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]];
		$reqQty=number_format($grey_stock_without_issue ,2,'.','');
		$totReqnQty=number_format($totReqnQty,2,'.','');
		$balance=number_format($reqQty-$totReqnQty,2,'.','');
	
		
		$color='';
		$color_id=array_unique(explode(',',$row[csf('color_id')]));
		foreach($color_id as $id)
		{
			if($id>0)
			{
				if($color=='') $color=$color_arr[$id]; else $color.=", ".$color_arr[$id];
			}
		}	

		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		$yarn_count_arr = explode(",",$row[csf('yarn_count')]);
		$yarn_counts="";
		foreach ($yarn_count_arr as $val) 
		{
			$yarn_counts .= $count_arr[$val].",";
		}
		$yarn_counts = chop($yarn_counts,",");

		$brand_id_arr = explode(",",$row[csf('brand_id')]);
		$brand_name="";
		foreach ($brand_id_arr as $val) 
		{
			$brand_name .= $brand_arr[$val].",";
		}
		$brand_name = chop($brand_name,",");
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" >
			<td width="30"><? echo $i;?></td>
			<td width="70"><? echo $po_arr[$row[csf('po_id')]]['ref'];?></td>
			<td width="60"><? echo $po_arr[$row[csf('po_id')]]['file'];?></td>
			<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
			<td width="80"><? echo $po_arr[$row[csf('po_id')]]['style_ref_no'];?></td>
			<td width="80"><? echo $row[csf('job_no')];?></td>
			<td width="80"><? echo $po_arr[$row[csf('po_id')]]['no'];?></td>
			<td width="80"><? echo $po_arr[$row[csf('po_id')]]['shipment_date'];?></td>
			<td width="70" align="center"><a href='##' onClick="generate_report2(<? echo $row[csf('program_booking_pi_id')]; ?>)"><? echo $row[csf('program_booking_pi_id')]; ?></a>&nbsp;</td>
			<td width="80"  style="word-wrap: break-word;word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]];?></td>
			<td width="175" style="word-wrap: break-word;word-break: break-all;"><? echo $constructtion_arr[$row[csf('determination_id')]]."". $composition_arr[$row[csf('determination_id')]];?></td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;">
			<? 
			echo $yarn_counts."<br>". $brand_name;
			?>
			</td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('yarn_lot')];?></td>
			<td width="40"><? echo $row[csf('gsm_weight')];?></td>
			<td width="40"><? echo $row[csf('dia_width')];?></td>
			<td width="90" style="word-wrap: break-word;word-break: break-all;"><? echo $color;?></td>

			<td width="90" style="word-wrap: break-word;word-break: break-all;">
				<?
				$batch_color = $row[csf('batch_color')];
					echo create_drop_down( "batchColorId$i", 80,$book_color_arr[$row[csf('po_id')]],"", 1, "--Select--", $batch_color, "","","","","","","","","batchColorId[]","" );
				?>
			</td> 

			<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo number_format($grey_stock_without_issue,2);?></td>
			
			<td width="70" align="right"><? echo number_format($grey_stock,2);//number_format( $row[csf('booking_qty')],2);?></td>
			<td width="70" align="right">
				<input type="text" id="previous_reqsnQty<? echo $i; ?>" name="previous_reqsnQty[]" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($totReqnQty,2); ?>" readonly disabled>
			</td>
			<td width="80" align="right"><? echo number_format($balance,2); ?></td>

			<td width="80" align="right"><input type="text" name="txt_collar_cuff_qnty<? echo $i; ?>" id="txt_collar_cuff_qnty<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>

			<td width="90" align="right">
				<input type="text" value="<? echo number_format($row[csf('reqn_qty')],2,'.',''); ?>" class="text_boxes_numeric" style="width:65px" id="reqsnQty<? echo $i; ?>" name="reqsnQty[]" onKeyUp="fnc_count_total_qty();fnc_check_balance_qty(<? echo $i ?>);"  />
			</td>
			<td width="90" align="right"><? echo number_format($issue_data_arr[$dataStr],2);?></td>
			<td width="90" >
				<input type="text" value="<? echo $row[csf('remarks')];?>" class="text_boxes" style="width:65px" id="remarks<? echo $i; ?>" name="remarks[]"/>
			</td>
			<td width="60"><? echo $row[csf('prod_id')]?></td>
			<td width="50">
				<input type="button" value="-" class="formbuttonplasminus" style="width:30px" id="decrease1" name="decrease[]" onclick="fn_deleteRow(<? echo $i;?>)"/>

				<input type="hidden" value="<? echo $row[csf('program_booking_pi_id')]; ?>" id="programBookingId<? echo $i; ?>" name="programBookingId[]"/>
				<input type="hidden" value="<? echo $row[csf('buyer_id')]; ?>" id="buyerId<? echo $i; ?>" name="buyerId[]"/>
				<input type="hidden" value="<? echo $row[csf('po_id')]; ?>" id="poId<? echo $i; ?>" name="poId[]"/>
				<input type="hidden" value="<? echo $po_arr[$row[csf('po_id')]]['file']; ?>" id="fileNo<? echo $i; ?>" name="fileNo[]"/>
				<input type="hidden" value="<? echo $po_arr[$row[csf('po_id')]]['ref']; ?>" id="grouping<? echo $i; ?>" name="grouping[]"/>
				<input type="hidden" value="<? echo $row[csf('job_no')]; ?>" id="jobNo<? echo $i; ?>" name="jobNo[]"/>
				<input type="hidden" value="<? echo $row[csf('color_id')]; ?>" id="colorId<? echo $i; ?>" name="colorId[]"/>
				<input type="hidden" value="<? echo $row[csf('body_part_id')]; ?>" id="bodyPartId<? echo $i; ?>" name="bodyPartId[]"/>
				<input type="hidden" value="<? echo $row[csf('determination_id')]; ?>" id="deterId<? echo $i; ?>" name="deterId[]"/>
				<input type="hidden" value="<? echo $row[csf('gsm_weight')] ; ?>" id="gsm<? echo $i; ?>" name="gsm[]"/>
				<input type="hidden" value="<? echo $row[csf('dia_width')]; ?>" id="width<? echo $i; ?>" name="width[]"/>
				<input type="hidden" value="<? echo $row[csf('prod_id')]; ?>" id="prodId<? echo $i; ?>" name="prodId[]"/>
				<input type="hidden" value="<? echo $row[csf('id')];?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>
				<input type="hidden" value="<? echo $row[csf('is_sales')];?>" id="isSales<? echo $i; ?>" name="isSales[]"/>
				<input type="hidden" value="<? echo $row[csf('booking_without_order')];?>" id="booking_without_order<? echo $i; ?>" name="booking_without_order[]"/>
				<input type="hidden" value="<? echo number_format($balance,2,'.','');?>" id="reqnBalQty<? echo $i; ?>" name="reqnBalQty[]"/>
				<input type="hidden" value="<? echo number_format($row[csf('booking_qty')],2,'.','');?>" id="bookintQty<? echo $i; ?>" name="bookintQty[]"/>

				<input type="hidden" value="<? echo number_format($grey_stock_without_issue,2,'.','');?>" id="stockWithoutIssueQty<? echo $i; ?>" name="stockWithoutIssueQty[]"/>
				<input type="hidden" value="<? echo number_format($issue_data_arr[$dataStr],2,'.','');?>" id="issueQty<? echo $i; ?>" name="issueQty[]"/>

				<input type="hidden" value="<? echo $row[csf('yarn_count')]; ?>" id="yCountId<? echo $i; ?>" name="yCountId[]"/>
				<input type="hidden" value="<? echo $row[csf('brand_id')]; ?>" id="brandId<? echo $i; ?>" name="brandId[]"/>
				<input type="hidden" value="<? echo $row[csf('yarn_lot')]; ?>" id="yLotId<? echo $i; ?>" name="yLotId[]"/>
			</td>
		</tr>
	<?
		$i++;
	}
	
	exit();
}

if($action=="print_fab_req_for_batch")
{
	extract($_REQUEST);
	//echo $data;
	$ex_data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	
	$sql_mst="Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	ob_start();
	?>
    <div style="width:1060px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr class="form_caption">
                    	<td align="center" style="font-size:18px"><strong ><? echo $company_library[$ex_data[0]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><? echo show_company($ex_data[0],'',''); ?> </td>  
                    </tr>
                    <tr class="form_caption">
                    	<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="930" cellspacing="0" align="" border="0">
        <tr>
            <td width="130"><strong>Requisition No :</strong></td> <td width="175"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
            <td width="130"><strong>Requisition Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
            <td width="130">&nbsp;</td> <td width="175">&nbsp;</td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" style="font-size:13px">
                <th width="20">SL</th>
                <th width="100">Buyer/ Job /Order</th>
				<th width="60">Prog.No.</th>
                <th width="120">Construction, Composition</th>
                <th width="120">Yarn Count & Brand</th>
                <th width="120">Yarn Lot</th>
                <th width="30">GSM</th> 
                <th width="30">Dia</th>
                <th width="80">Color/ Code</th>
                <th width="80">Batch Color</th>
                <th width="70">Total Req. Qty. (Kg)</th>
                <th width="60">Reqsn. Qty.</th>
                <th width="90">Remarks</th>
            </thead>
            <tbody>
    		<?
			if($db_type==0) $year_val="year(a.insert_date)"; else if( $db_type==2) $year_val="TO_CHAR(a.insert_date,'YYYY')";
			$po_arr=array();
			$po_sql="select a.style_ref_no, $year_val as year, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$ex_data[0]'";
			$po_sql_result=sql_select($po_sql);
			foreach( $po_sql_result as $row )
			{
				$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
				$po_arr[$row[csf('id')]]['ref']=$row[csf('grouping')];
				$po_arr[$row[csf('id')]]['year']=$row[csf('year')];
			}
			
			$composition_arr=array(); $constructtion_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array=sql_select($sql_deter);
			foreach( $data_array as $row )
			{
				$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
				$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			
			
			$product_arr=array();
			$sql_product="select id, gsm, dia_width from product_details_master where item_category_id=13";
			$data_array=sql_select($sql_product);
			foreach( $data_array as $row )
			{
				$product_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			}
		
			
			
			$reqn_qnty_array=array();
			$reqnData=sql_select("select receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id, sum(reqn_qty) as qnty from pro_fab_reqn_for_batch_dtls where entry_form=553 and status_active=1 and is_deleted=0 group by receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id");
			foreach($reqnData as $row)
			{
				$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]=$row[csf('qnty')];
			}
			
			$sql="SELECT id, receive_basis, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, batch_color, reqn_qty, remarks, yarn_count, yarn_lot, brand_id from pro_fab_reqn_for_batch_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
			//echo $sql;
			$result=sql_select($sql);
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$buyer_job_ord="";
				$buyer_job_ord="B: ".$buyer_arr[$row[csf('buyer_id')]].'<br> J: '.$row[csf('job_no')].'<br> O: '.$po_arr[$row[csf('po_id')]]['po'];
				$const_comp="";
				$const_comp=$constructtion_arr[$row[csf('determination_id')]].', '.$composition_arr[$row[csf('determination_id')]];
				
				$gsm=$product_arr[$row[csf('prod_id')]]['gsm'];
				$dia=$product_arr[$row[csf('prod_id')]]['dia'];
				

				
				$totReqnQty=$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]];
				$totReqnQty=number_format($totReqnQty,2,'.','');

				$color='';
				$color_id=array_unique(explode(',',$row[csf('color_id')]));
				foreach($color_id as $id)
				{
					if($id>0)
					{
						if($color=='') $color=$color_arr[$id]; else $color.=", ".$color_arr[$id];
					}
				}	

				$batch_color='';
				$batch_color_arr=array_unique(explode(',',$row[csf('batch_color')]));
				foreach($batch_color_arr as $id)
				{
					if($id>0)
					{
						if($batch_color=='') $batch_color=$color_arr[$id]; else $batch_color.=", ".$color_arr[$id];
					}
				}
				
				$lot_count="";


				$yarn_count_arr = explode(",",$row[csf('yarn_count')]);
				$yarn_counts="";
				foreach ($yarn_count_arr as $val) 
				{
					$yarn_counts .= $count_arr[$val].",";
				}
				$yarn_counts = chop($yarn_counts,",");

				$brand_id_arr = explode(",",$row[csf('brand_id')]);
				$brand_name="";
				foreach ($brand_id_arr as $val) 
				{
					$brand_name .= $brand_arr[$val].",";
				}
				$brand_name = chop($brand_name,",");
				
				?>
				 <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
					<td><? echo $i; ?></td>
					<td style="word-wrap:break-word; width:70px"><? echo $buyer_job_ord; ?></td>
					<td align="right"><? echo $row[csf("program_booking_pi_id")]; ?></td>
					<td style="word-wrap:break-word; width:70px"><? echo $const_comp; ?></td>
					<td style="word-wrap:break-word; width:70px"><? echo $yarn_counts."<br>". $brand_name; ?></td>
					<td style="word-wrap:break-word; width:70px"><? echo $row[csf("yarn_lot")]; ?></td>
					<td><? echo $gsm; ?></td> 
					<td><? echo $dia; ?></td>
					<td style="word-wrap:break-word; width:70px" align="center"><? echo $color; ?></td>
					<td style="word-wrap:break-word; width:70px" align="center"><? echo $batch_color; ?></td>
					
					<td align="right"><? echo number_format($totReqnQty,2); ?></td>
					<td align="right"><? echo number_format($row[csf('reqn_qty')],2); ?></td>
					<td style="word-wrap:break-word; width:70px;" align="center"><? echo $row[csf('remarks')]; ?></td>
				</tr>
				<?
				$grnd_tot_req_qty+=$totReqnQty;
				$grnd_reqn_qty+=$row[csf('reqn_qty')];
				$i++;
			}
			?>
            </tbody>
            <tfoot bgcolor="#dddddd" style="font-size:13px">
            	<tr>
                	<td colspan="10" align="right"><strong>Total :</strong></td>
                    <td align="right"><? echo number_format($grnd_tot_req_qty,2); ?></td>
                    <td align="right"><? echo number_format($grnd_reqn_qty,2); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        </div>
        <br>
		 <?
            echo signature_table(93, $ex_data[0], "1060px");
         ?>
    </div>
    <?
    $user_id=$_SESSION['logic_erp']['user_id'];
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w') or die('Cant open');
    $is_created = fwrite($create_new_doc, $html) or die('Cant open');
    //echo "$html##$filename##$is_created";

    echo $html;

	?>
	<a id="abc_excel_open" href="<? echo $filename; ?>" target="_blank"></a>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		document.getElementById('abc_excel_open').click();
	</script>
	<?
	exit();
}

if($action =="populate_details_data")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$hidden_data = str_replace("'","",$hidden_data);
	$hidden_data_arr = explode("!!!!", chop($hidden_data,'!!!!'));
	
	// echo '<pre>'; print_r($hidden_data_arr);die;
	// "'RpC-22-00001**RpC-Fb-22-00002******35**565**60**160**1**0**0'"
	$con = connect();
	$r_id7=execute_query("delete from tmp_job_no where userid=$user_name");
	if($r_id7)
	{
		oci_commit($con);
	}
	// echo $update_id.'=test';die;
	$update_id=str_replace("'", "", $update_id);
	if($update_id)
	{
		$this_reqn_qnty_array = array();
		$reqnData = sql_select("SELECT a.id, a.job_no, a.reqn_qty, a.program_booking_pi_id, a.color_id, a.po_id, a.body_part_id, a.is_sales, a.prod_id, a.determination_id, b.id as job_id from pro_fab_reqn_for_batch_dtls a, WO_PO_DETAILS_MASTER b where a.job_no=b.job_no and a.entry_form=553 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id");
		
		foreach ($reqnData as $row) 
		{
			$this_reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['qnty']+=$row[csf('reqn_qty')];
			$this_reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['id']=$row[csf('id')];

			//$job_noArr[$row[csf('job_no')]]="'".$row[csf('job_no')]."'";
			$job_idArr[$row[csf('job_id')]]=$row[csf('job_id')];
		}
		unset($reqnData);
	}

	foreach ($hidden_data_arr as $value) 
	{
		$dataStr = explode("**",$value);
		$job_no = $dataStr[0];
		$booking_no = $dataStr[1];
		$file_no = $dataStr[2];
		$internal_ref = $dataStr[3];
		$color_id = $dataStr[4];
		$bodyPart_id = $dataStr[5];
		$dia_width = $dataStr[6];
		$gsm = $dataStr[7];
		$determination_id = $dataStr[8];
		$is_sales = $dataStr[9];
		$booking_without_order = $dataStr[10];
		$job_id = $dataStr[11];

		$deterArr[$determination_id]=$determination_id;
		//$job_noArr[$job_no]="'".$job_no."'";
		$job_idArr[$job_id]=$job_id;

		$search_ref[$job_no][$file_no][$internal_ref][$bodyPart_id][$determination_id][$color_id]=$job_no;
	}

	foreach ($job_idArr as  $val) 
	{
		$rID2=execute_query("insert into tmp_job_no (userid, job_id,entry_form) values ($user_name,$val,999)");
	}

	if($rID2)
	{
		oci_commit($con);
	}
	// echo "string";die;
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$auto_rcv_sql="SELECT f.id as program_no, g.booking_no, e.job_no, e.style_ref_no, d.id as po_id, d.po_number, d.grouping, d.file_no, d.pub_shipment_date, e.buyer_name, b.prod_id, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, c.quantity, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g, tmp_job_no h
	where a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=b.mst_id and c.entry_form=2 and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and e.id=h.job_id and h.userid=$user_name and h.entry_form=999 and b.status_active=1 and b.is_deleted=0 and a.booking_id=f.id and f.mst_id=g.id and b.trans_id!=0";
	//echo $auto_rcv_sql;die;
	$auto_rcv_data = sql_select($auto_rcv_sql);

	foreach ($auto_rcv_data as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];

		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("quantity")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_recv_gross=sql_select("SELECT g.booking_id as program_no, i.booking_no, e.job_no, e.style_ref_no, e.buyer_name, d.id as po_id, d.po_number, d.file_no, d.pub_shipment_date, d.grouping, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, sum(c.quantity) as knitting_qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e, tmp_job_no f, inv_receive_master g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i
	where  a.id=b.mst_id and a.receive_basis=9 and a.item_category=13 and a.entry_form=22 and b.id=c.dtls_id  and c.entry_form=22 and c.po_breakdown_id=d.id and d.job_id=e.id and e.id=f.job_id and f.userid =$user_name and f.entry_form=999 and g.entry_form=2 and a.booking_id=g.id and b.status_active=1 and b.is_deleted=0 and g.booking_id=h.id and h.mst_id=i.id
	group by g.booking_id, i.booking_no, e.job_no, e.style_ref_no, e.buyer_name, d.id, d.po_number, d.file_no, d.pub_shipment_date, d.grouping, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id"); 

	foreach ($sql_recv_gross as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];


		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("knitting_qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}
	//slow
	$sql_recv_roll = sql_select("SELECT f.id as program_no, g.booking_no, d.grouping, d.id as po_id, d.po_number, d.file_no, d.pub_shipment_date, e.job_no,e.style_ref_no, e.buyer_name, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, c.qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from  pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e, pro_roll_details a, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g, tmp_job_no h 
	where  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=58 and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and c.barcode_no=a.barcode_no and a.entry_form=2
	and a.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and e.id=h.job_id and h.userid=$user_name and h.entry_form=999 and h.entry_form=999 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 and c.booking_without_order=0");

	foreach ($sql_recv_roll as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_roll_iss = sql_select("SELECT f.id as program_no, g.booking_no, d.po_number, d.id as po_id, d.grouping, d.file_no, d.pub_shipment_date, e.job_no,e.style_ref_no, e.buyer_name, i.detarmination_id, i.gsm, i.dia_width, a.body_part_id, a.color_id, a.prod_id, j.quantity
	from inv_grey_fabric_issue_dtls a, pro_roll_details b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e, 
	ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g, tmp_job_no h, product_details_master i, order_wise_pro_details j
	where a.id=b.dtls_id and b.barcode_no=c.barcode_no and b.entry_form=61 and c.entry_form=2 and b.booking_without_order=0 and b.is_sales=0
	and b.po_breakdown_id=d.id and d.job_id=e.id and c.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and e.id=h.job_id and h.userid =$user_name and h.entry_form=999 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.prod_id=i.id and a.id=j.dtls_id and j.entry_form=61");
	//echo "string";die;
	foreach ($sql_roll_iss as $val) 
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];
		$iss_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_data_arr[$iss_string] += $val[csf("quantity")];
	}
	// echo "string";die;
	//print_r($issue_data_arr);

	$roll_iss_rtn_sql="SELECT h.id as program_no, i.booking_no, f.job_no,f.style_ref_no, f.buyer_name, e.id as po_id, e.po_number, e.file_no, e.pub_shipment_date, e.grouping, a.body_part_id, a.color_id, a.febric_description_id, a.gsm, a.width, a.prod_id, c.qnty 
	from pro_grey_prod_entry_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, wo_po_break_down e, wo_po_details_master f, tmp_job_no g , inv_receive_master x, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i 
	where a.id=b.dtls_id and b.entry_form=84 and a.id=c.dtls_id and c.entry_form=84 and c.barcode_no=d.barcode_no and d.entry_form=2 and d.receive_basis=2 and c.po_breakdown_id=e.id and e.job_id=f.id and f.id=g.job_id and g.userid=$user_name and g.entry_form=999 and d.mst_id=x.id and x.booking_id=h.id and h.mst_id=i.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $roll_iss_rtn_sql;die;
	$roll_iss_rtn_data=sql_select($roll_iss_rtn_sql);
	foreach($roll_iss_rtn_data as $val)
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$iss_ret_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_ret_data_arr[$iss_ret_string] += $val[csf("qnty")];
	}
	// echo "string";die;

	$roll_trans_in_sql = "SELECT h.id as program_no, i.booking_no, d.id as po_id, d.po_number, d.grouping, d.file_no, d.pub_shipment_date, e.job_no,e.style_ref_no, e.buyer_name, g.color_id, b.detarmination_id, b.gsm, b.dia_width, a.to_body_part as body_part_id, a.to_prod_id as prod_id, c.qnty, a.yarn_lot, a.y_count, a.brand_id
	from inv_item_transfer_dtls a, product_details_master b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e, pro_roll_details f, pro_grey_prod_entry_dtls g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i, tmp_job_no j
	where a.to_prod_id=b.id and a.id=c.dtls_id and c.entry_form in (82,110) and c.po_breakdown_id=d.id and d.job_id=e.id and c.barcode_no=f.barcode_no and f.receive_basis=2 and f.dtls_id=g.id and f.booking_no=cast(h.id as varchar(4000)) and h.mst_id=i.id and e.id=j.job_id and j.userid=$user_name and j.entry_form=999 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $roll_trans_in_sql;die;
	$roll_trans_in_data=sql_select($roll_trans_in_sql);
	foreach($roll_trans_in_data as $val)
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_in_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$trans_in_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count'] .= $val[csf("y_count")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count_id'] .= $val[csf("y_count")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_id'] .= $val[csf("brand_id")].",";
	}
	// echo "string";die;
	//echo "<pre>";
	//print_r($search_ref);

	$roll_trans_out_sql = "SELECT i.id as program_no, j.booking_no, g.job_no,g.style_ref_no, g.buyer_name, f.id as po_id, f.po_number, f.grouping, f.file_no, f.pub_shipment_date, a.body_part_id, k.detarmination_id, k.gsm, k.dia_width, e.color_id, a.from_prod_id as prod_id, c.qnty
	from inv_item_transfer_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, pro_grey_prod_entry_dtls e, wo_po_break_down f, wo_po_details_master g, tmp_job_no h, ppl_planning_info_entry_dtls i, ppl_planning_info_entry_mst j, product_details_master k
	where a.id=b.dtls_id and b.entry_form in (82,110) and b.trans_type=6 and a.id=c.dtls_id and c.entry_form in (82,110) and c.barcode_no=d.barcode_no and d.entry_form in(2) and d.receive_basis=2 and d.dtls_id=e.id and b.po_breakdown_id=f.id and f.job_id=g.id and g.id=h.job_id and h.userid=$user_name and h.entry_form=999 and d.booking_no=cast(i.id as varchar(4000)) and i.mst_id=j.id and a.from_prod_id=k.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $roll_trans_out_sql;die;
	$roll_trans_out_data=sql_select($roll_trans_out_sql);
	foreach($roll_trans_out_data as $val)
	{
		$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_out_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$transfer_out_data_arr[$trans_out_string] += $val[csf("qnty")];
	}
	// echo "string";die;

	$reqn_qnty_array = array();
	if($update_id)
	{
		$up_cond = " and a.mst_id !=$update_id";
	}
	/*$reqnData = sql_select("SELECT a.reqn_qty, a.program_booking_pi_id, a.color_id, a.po_id, a.body_part_id, a.is_sales, a.prod_id, a.determination_id from pro_fab_reqn_for_batch_dtls a, tmp_job_no b where a.entry_form=553 and a.job_no=b.job_no and b.userid=$user_name and a.status_active=1 and a.is_deleted=0 $up_cond");*/

	$reqnData = sql_select("SELECT a.reqn_qty, a.program_booking_pi_id, a.color_id, a.po_id, a.body_part_id, a.is_sales, a.prod_id, a.determination_id 
	from tmp_job_no c, WO_PO_DETAILS_MASTER b, pro_fab_reqn_for_batch_dtls a 
	where a.entry_form=553 and c.job_id=b.id and c.userid=$user_name and c.entry_form=999 and b.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 $up_cond");
	foreach ($reqnData as $row) 
	{
		$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]+=$row[csf('reqn_qty')];
	}

	$i=1;
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	$sql_book_color = sql_select("SELECT a.id as po_id, b.fabric_color_id, d.color_name from wo_po_break_down a, wo_booking_dtls b, tmp_job_no c, lib_color d where a.id = b.po_break_down_id and b.booking_type=1 and b.is_short=2 and a.job_id=c.job_id and c.userid=$user_name and c.entry_form=999  and b.fabric_color_id=d.id and b.status_active=1 and b.is_deleted=0 group by a.id, b.fabric_color_id, d.color_name");

	foreach( $sql_book_color as $row) 
	{
		$book_color_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]]=$row[csf('color_name')];
	}
	unset($sql_book_color);

	$r_id7=execute_query("delete from tmp_job_no where userid=$user_name");
	if($r_id7)
	{
		oci_commit($con);
	}

	
	/* echo "<pre>";
	print_r($search_ref);
	echo "</pre>"; */
	

	foreach ($rcv_trans_in_data_arr as $dataStr=> $val) 
	{
		$dataStrArr = explode("*",$dataStr);
		$program_no = $dataStrArr[0];
		$po_id = $dataStrArr[1];
		$prod_id = $dataStrArr[2];
		$body_part_id = $dataStrArr[3];
		$color_id = $dataStrArr[4];

		$po_number = $po_ref_arr[$po_id]['po_number'];
		$grouping = $po_ref_arr[$po_id]['grouping'];
		$file_no = $po_ref_arr[$po_id]['file_no'];
		$shipment_date = $po_ref_arr[$po_id]['shipment_date'];
		$job_no = $po_ref_arr[$po_id]['job_no'];
		$style_ref_no = $po_ref_arr[$po_id]['style_ref_no'];
		$buyer_name = $po_ref_arr[$po_id]['buyer_name'];

		$febric_description_id = $prod_ref_arr[$prod_id]['febric_description_id'];
		$gsm = $prod_ref_arr[$prod_id]['gsm'];
		$width = $prod_ref_arr[$prod_id]['width'];

		$yarn_counts = $val['yarn_count'];
		$brand_names = $val['brand_name'];
		$yarn_lots = $val['yarn_lot'];

		$yarn_counts_arr = array_unique(array_filter(explode(",", chop($yarn_counts,","))));
        $yarn_counts="";
        foreach ($yarn_counts_arr as $count) {
            $yarn_counts .= $count_arr[$count] . ",";
        }
        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

		//$yarn_counts = implode(",",array_unique(explode(",",chop($yarn_counts,","))));
		$brand_names = implode(",",array_unique(explode(",",chop($brand_names,","))));
		$yarn_lot = implode(",",array_unique(explode(",",chop($yarn_lots,","))));


		$yarn_count_id = implode(",",array_unique(explode(",",chop($val['yarn_count_id'],","))));
		$brand_id = implode(",",array_unique(explode(",",chop($val['brand_id'],","))));

		$color_names="";
		$colorArr= explode(",",$color_id);
		foreach ($colorArr as  $colorval) {
			$color_names .= $color_arr[$colorval].",";
		}
		$color_names = chop($color_names,",");

		$grey_stock = $val['qnty'] + $issue_ret_data_arr[$dataStr] - $issue_data_arr[$dataStr] - $transfer_out_data_arr[$dataStr];
		//echo $val['qnty'] ."+". $issue_ret_data_arr[$dataStr] ."-". $issue_data_arr[$dataStr] ."-". $transfer_out_data_arr[$dataStr]."<br>";
		$grey_stock_without_issue = $val['qnty'] + $issue_ret_data_arr[$dataStr]-$transfer_out_data_arr[$dataStr];

		$pre_reqn_qnty = $reqn_qnty_array[$program_no][$po_id][ $prod_id][$body_part_id][$febric_description_id][$color_id];
		$req_balance = $grey_stock_without_issue -$pre_reqn_qnty;
		//echo "job = $job_no, file = $file_no, int=$grouping, body= $body_part_id, deter=$febric_description_id, color=$color_id<br>";
		if($search_ref[$job_no][$file_no][$grouping][$body_part_id][$febric_description_id][$color_id]=="")
		{
			$bgcolor ="#ff9c94";
		}
		else
		{
			$bgcolor ="#b6fec7";
		}

		$requisition_qnty = $this_reqn_qnty_array[$program_no][$po_id][ $prod_id][$body_part_id][$febric_description_id][$color_id]['qnty'];
		$details_id = $this_reqn_qnty_array[$program_no][$po_id][ $prod_id][$body_part_id][$febric_description_id][$color_id]['id'];

		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" >
			<td width="30"><? echo $i;?></td>
			<td width="70"><? echo $grouping;?></td>
			<td width="60"><? echo $file_no;?></td>
			<td width="100"><? echo $buyer_arr[$buyer_name];?></td>
			<td width="80"><? echo $style_ref_no;?></td>
			<td width="80"><? echo $job_no;?></td>
			<td width="80"><? echo $po_number;?></td>
			<td width="80"><? echo $shipment_date;?></td>
			<td width="70" align="center"><a href='##' onClick="generate_report2(<? echo $program_no; ?>)"><? echo $program_no; ?></a>&nbsp;</td>
			<td width="80"  style="word-wrap: break-word;word-break: break-all;"><? echo $body_part[$body_part_id];?></td>
			<td width="175" style="word-wrap: break-word;word-break: break-all;"><? echo $constructtion_arr[$febric_description_id]."". $composition_arr[$febric_description_id];?></td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;">
				<? 
				$br="";
					if($yarn_counts) 
					{
						echo $yarn_counts;
						$br="<br>";
					}
					echo $br.$brand_names;
				?>
			</td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;"><? echo $yarn_lot;?></td>
			<td width="40"><? echo $gsm;?></td>
			<td width="40"><? echo $width;?></td>
			<td width="90" style="word-wrap: break-word;word-break: break-all;"><? echo $color_names;?></td>

			<td width="90" style="word-wrap: break-word;word-break: break-all;">
				<?
					echo create_drop_down( "batchColorId$i", 80,$book_color_arr[$po_id],"", 1, "--Select--", "", "","","","","","","","","batchColorId[]","" );
				?>
			</td> 

			<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo number_format( $grey_stock_without_issue,2,'.','');?></td>
			
			<td width="70" align="right"><? echo number_format( $grey_stock,2);?></td>
			<td width="70" align="right">
				<input type="text" id="previous_reqsnQty<? echo $i; ?>" name="previous_reqsnQty[]" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($pre_reqn_qnty,2); ?>" readonly disabled>
			</td>
			<td width="80" align="right"><? echo number_format($req_balance,2); ?></td>

			<td width="80" align="right"><input type="text" name="txt_collar_cuff_qnty<? echo $i; ?>" id="txt_collar_cuff_qnty<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>

			<td width="90" align="right">
				<input class="text_boxes_numeric" type="text" value="<? echo $requisition_qnty;?>" style="width:65px" id="reqsnQty<? echo $i; ?>" name="reqsnQty[]" onKeyUp="fnc_count_total_qty();fnc_check_balance_qty(<? echo $i ?>);"/>
			</td>
			<td width="90" align="right"><? echo number_format($issue_data_arr[$dataStr],2); ?></td>
			<td width="90" ><input type="text" value="" class="text_boxes" style="width:65px" id="remarks<? echo $i; ?>" name="remarks[]"/></td>
			<td width="60"><? echo $prod_id?></td>
			<td width="50">
				<input type="button" value="-" class="formbuttonplasminus" style="width:30px" id="decrease1" name="decrease[]" onclick="fn_deleteRow(<? echo $i;?>)"/>
				<input type="hidden" value="<? echo $program_no; ?>" id="programBookingId<? echo $i; ?>" name="programBookingId[]"/>
				<input type="hidden" value="<? echo $buyer_name; ?>" id="buyerId<? echo $i; ?>" name="buyerId[]"/>
				<input type="hidden" value="<? echo $po_id; ?>" id="poId<? echo $i; ?>" name="poId[]"/>
				<input type="hidden" value="<? echo $file_no; ?>" id="fileNo<? echo $i; ?>" name="fileNo[]"/>
				<input type="hidden" value="<? echo $grouping; ?>" id="grouping<? echo $i; ?>" name="grouping[]"/>
				<input type="hidden" value="<? echo $job_no; ?>" id="jobNo<? echo $i; ?>" name="jobNo[]"/>
				<input type="hidden" value="<? echo $color_id; ?>" id="colorId<? echo $i; ?>" name="colorId[]"/>
				<input type="hidden" value="<? echo $body_part_id; ?>" id="bodyPartId<? echo $i; ?>" name="bodyPartId[]"/>
				<input type="hidden" value="<? echo $febric_description_id; ?>" id="deterId<? echo $i; ?>" name="deterId[]"/>
				<input type="hidden" value="<? echo $gsm ; ?>" id="gsm<? echo $i; ?>" name="gsm[]"/>
				<input type="hidden" value="<? echo $width; ?>" id="width<? echo $i; ?>" name="width[]"/>
				<input type="hidden" value="<? echo $prod_id; ?>" id="prodId<? echo $i; ?>" name="prodId[]"/>
				<input type="hidden" value="<? echo $details_id;?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>
				<input type="hidden" value="<? echo $is_sales;?>" id="isSales<? echo $i; ?>" name="isSales[]"/>
				
				<input type="hidden" value="<? echo $booking_without_order;?>" id="booking_without_order<? echo $i; ?>" name="booking_without_order[]"/>
				<input type="hidden" value="<? echo number_format($req_balance,2,'.','');?>" id="totBalQty<? echo $i; ?>" name="reqnBalQty[]"/>
				<input type="hidden" value="<? echo number_format($grey_stock,2,'.','');?>" id="bookintQty<? echo $i; ?>" name="bookintQty[]"/>
				<input type="hidden" value="<? echo number_format($grey_stock_without_issue,2,'.','');?>" id="stockWithoutIssueQty<? echo $i; ?>" name="stockWithoutIssueQty[]"/>
				<input type="hidden" value="<? echo number_format($issue_data_arr[$dataStr],2,'.','');?>" id="issueQty<? echo $i; ?>" name="issueQty[]"/>
				
				<input type="hidden" value="<? echo $yarn_count_id; ?>" id="yCountId<? echo $i; ?>" name="yCountId[]"/>
				<input type="hidden" value="<? echo $brand_id; ?>" id="brandId<? echo $i; ?>" name="brandId[]"/>
				<input type="hidden" value="<? echo $yarn_lot; ?>" id="yLotId<? echo $i; ?>" name="yLotId[]"/>
			</td>
		</tr>
		<?
		$i++;
	}
}

if($action =="populate_details_data_for_sales") // Select Fabric onClose dtls list view
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$hidden_data = str_replace("'","",$hidden_data);
	$hidden_data_arr = explode("!!!!", chop($hidden_data,'!!!!'));
	
	// echo '<pre>'; print_r($hidden_data_arr);die;
	// "'RpC-22-00001**RpC-Fb-22-00002******35**565**60**160**1**0**0'"
	$con = connect();
	execute_query("delete from tmp_job_no where userid=$user_name and entry_form=999");
	execute_query("delete from tmp_barcode_no where userid=$user_name and entry_form=175");
	oci_commit($con);
	
	// echo $update_id.'=test';die;
	$update_id=str_replace("'", "", $update_id);
	if($update_id)
	{
		$this_reqn_qnty_array = array();
		$reqnData = sql_select("SELECT a.id, a.job_no, a.reqn_qty, a.program_booking_pi_id, a.color_id, a.po_id, a.body_part_id, a.is_sales, a.prod_id, a.determination_id, b.id as job_id 
		from pro_fab_reqn_for_batch_dtls a, fabric_sales_order_mst b where a.po_id=b.id and a.entry_form=553 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$update_id and a.is_sales=1");
		
		foreach ($reqnData as $row) 
		{
			$this_reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['qnty']+=$row[csf('reqn_qty')];
			$this_reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['id']=$row[csf('id')];

			//$job_noArr[$row[csf('job_no')]]="'".$row[csf('job_no')]."'";
			$job_idArr[$row[csf('job_id')]]=$row[csf('job_id')];
		}
		unset($reqnData);
	}

	foreach ($hidden_data_arr as $value) 
	{
		$dataStr = explode("**",$value);
		$job_no = $dataStr[0];
		$booking_no = $dataStr[1];
		$file_no = $dataStr[2];
		$internal_ref = $dataStr[3];
		$color_id = $dataStr[4];
		$bodyPart_id = $dataStr[5];
		$dia_width = $dataStr[6];
		$gsm = $dataStr[7];
		$determination_id = $dataStr[8];
		$is_sales = $dataStr[9];
		$booking_without_order = $dataStr[10];
		$job_id = $dataStr[11];//fso_id

		$deterArr[$determination_id]=$determination_id;
		//$job_noArr[$job_no]="'".$job_no."'";
		$job_idArr[$job_id]=$job_id;//fso_id

		$search_ref[$job_no][$file_no][$internal_ref][$bodyPart_id][$determination_id][$color_id]=$job_no;
	}

	foreach ($job_idArr as  $val) 
	{
		$rID2=execute_query("insert into tmp_job_no (userid, job_id,entry_form) values ($user_name,$val,999)");
	}

	if($rID2)
	{
		oci_commit($con);
	}
	// echo "string";die;

	$job_fso_chk=array();
	$job_from_fso =  sql_select("SELECT a.id, c.booking_no,c.booking_type,c.is_short, b.job_no_prefix_num,b.job_no, a.job_no as fso_no, d.po_number,d.grouping,d.file_no,d.pub_shipment_date, b.job_no,b.buyer_name,b.style_ref_no
	from tmp_job_no h,fabric_sales_order_mst a, wo_booking_dtls c, wo_po_details_master b, WO_PO_BREAK_DOWN d
	where h.job_id=a.id and h.userid=$user_name and h.entry_form=999 and a.sales_booking_no=c.booking_no and c.job_no=b.job_no and b.id=d.JOB_ID and b.job_no = d.job_no_mst and c.PO_BREAK_DOWN_ID=d.id and c.BOOKING_TYPE in(1,4) and a.within_group=1 
	union all 
	select a.id, b.booking_no,4 as booking_type,0 as is_short,0 as job_no_prefix_num,null as job_no, a.job_no as fso_no, null as po_number,null as grouping,null as file_no,null as pub_shipment_date,null as job_no,null as buyer_name,null as style_ref_no
	from tmp_job_no h,fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b 
	where h.job_id=a.id and h.userid=$user_name and h.entry_form=999 and a.within_group=1 and a.sales_booking_no=b.booking_no");
    foreach ($job_from_fso as $val)
    {
        if($job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] == "")
        {
            $job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];

            $po_ref_arr[$val[csf("id")]]['po_number'] =$val[csf("po_number")];
			$po_ref_arr[$val[csf("id")]]['grouping'] =$val[csf("grouping")];
			$po_ref_arr[$val[csf("id")]]['file_no'] =$val[csf("file_no")];
			$po_ref_arr[$val[csf("id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
			$po_ref_arr[$val[csf("id")]]['job_no'] =$val[csf("job_no")];
			$po_ref_arr[$val[csf("id")]]['buyer_name'] =$val[csf("buyer_name")];
			$po_ref_arr[$val[csf("id")]]['style_ref_no'] =$val[csf("style_ref_no")];
        }
    }
    // echo "<pre>";print_r($po_ref_arr);die;
    // echo "string";die;
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$auto_rcv_sql="SELECT f.id as program_no, g.booking_no, d.id as po_id, d.job_no as fso_no, b.prod_id, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, c.quantity, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g, tmp_job_no h
	where a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=b.mst_id and c.entry_form=2 and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.id=h.job_id and h.userid=$user_name and h.entry_form=999 and b.status_active=1 and b.is_deleted=0 and a.booking_id=f.id and f.mst_id=g.id and b.trans_id!=0";
	//echo $auto_rcv_sql;die;
	$auto_rcv_data = sql_select($auto_rcv_sql);

	foreach ($auto_rcv_data as $val) 
	{
		/*$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];*/

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];

		$rcv_trans_in_data_arr[$rcv_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$rcv_string]['booking_no'] = $val[csf("booking_no")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("quantity")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_recv_gross=sql_select("SELECT g.booking_id as program_no, i.booking_no, e.id as po_id, e.job_no as fso_no, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, sum(c.quantity) as knitting_qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, fabric_sales_order_mst e, tmp_job_no f, inv_receive_master g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i
	where  a.id=b.mst_id and a.receive_basis=9 and a.item_category=13 and a.entry_form=22 and b.id=c.dtls_id  and c.entry_form=22 and c.po_breakdown_id=e.id and e.id=f.job_id and f.userid =$user_name and f.entry_form=999 and g.entry_form=2 and a.booking_id=g.id and b.status_active=1 and b.is_deleted=0 and g.booking_id=h.id and h.mst_id=i.id
	group by g.booking_id, i.booking_no, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id,e.id, e.job_no"); 

	foreach ($sql_recv_gross as $val) 
	{
		/*$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];*/


		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$rcv_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$rcv_string]['booking_no'] = $val[csf("booking_no")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("knitting_qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}
	//slow
	$sql_recv_roll = sql_select("SELECT f.id as program_no, g.booking_no, c.barcode_no, d.id as po_id, d.job_no as fso_no, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, c.qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from  pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d, pro_roll_details a, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g, tmp_job_no h
	where  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=58 and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.barcode_no=a.barcode_no and a.entry_form=2 and a.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and h.job_id=d.id and h.userid=$user_name and h.entry_form=999 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 and c.booking_without_order=0");

	foreach ($sql_recv_roll as $val) 
	{
		/*$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];*/

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$rcv_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$rcv_string]['booking_no'] = $val[csf("booking_no")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_roll_iss = sql_select("SELECT f.id as program_no, g.booking_no, e.id as po_id, e.job_no as fso_no, 
	i.detarmination_id, i.gsm, i.dia_width, a.body_part_id, a.color_id, a.prod_id, j.quantity 
	from inv_grey_fabric_issue_dtls a, pro_roll_details b, pro_roll_details c, fabric_sales_order_mst e, ppl_planning_info_entry_dtls f, 
	ppl_planning_info_entry_mst g, tmp_job_no h, product_details_master i, order_wise_pro_details j 
	where a.id=b.dtls_id and b.barcode_no=c.barcode_no and b.entry_form=61 and c.entry_form=2 and b.booking_without_order=0 and b.is_sales=1 and b.po_breakdown_id=e.id and c.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and e.id=h.job_id and h.userid =$user_name and h.entry_form=999 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.prod_id=i.id and a.id=j.dtls_id and j.entry_form=61");
	//echo "string";die;
	foreach ($sql_roll_iss as $val) 
	{
		/*$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];*/

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];
		$iss_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_data_arr[$iss_string] += $val[csf("quantity")];
	}
	// echo "string";die;
	//print_r($issue_data_arr);

	$roll_iss_rtn_sql="SELECT h.id as program_no, i.booking_no, f.id as po_id, f.job_no as fso_no, c.barcode_no,
	a.body_part_id, a.color_id, a.febric_description_id, a.gsm, a.width, a.prod_id, c.qnty 
	from pro_grey_prod_entry_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, FABRIC_SALES_ORDER_MST f, tmp_job_no g, inv_receive_master x, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i 
	where a.id=b.dtls_id and b.entry_form=84 and a.id=c.dtls_id and c.entry_form=84 and c.barcode_no=d.barcode_no and d.entry_form=2 and d.receive_basis=2 and c.po_breakdown_id=f.id and f.id=g.job_id and g.userid=$user_name and g.entry_form=999 and d.mst_id=x.id and x.booking_id=h.id and h.mst_id=i.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $roll_iss_rtn_sql;die;
	$roll_iss_rtn_data=sql_select($roll_iss_rtn_sql);
	foreach($roll_iss_rtn_data as $val)
	{
		/*$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];*/

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$iss_ret_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_ret_data_arr[$iss_ret_string] += $val[csf("qnty")];
	}
	// echo "string";die;

	$roll_trans_in_sql = "SELECT h.id as program_no, i.booking_no, e.id as po_id, e.job_no as fso_no,
	g.color_id, b.detarmination_id, b.gsm, b.dia_width, a.to_body_part as body_part_id, a.to_prod_id as prod_id, c.qnty, a.yarn_lot, a.y_count, a.brand_id, c.barcode_no 
	from inv_item_transfer_dtls a, product_details_master b, pro_roll_details c, FABRIC_SALES_ORDER_MST e, pro_roll_details f, pro_grey_prod_entry_dtls g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i, tmp_job_no j 
	where a.to_prod_id=b.id and a.id=c.dtls_id and c.entry_form in (133) and c.po_breakdown_id=e.id and c.barcode_no=f.barcode_no and f.receive_basis=2 and f.dtls_id=g.id and f.booking_no=cast(h.id as varchar(4000)) and h.mst_id=i.id and e.id=j.job_id and j.userid=$user_name and j.entry_form=999 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $roll_trans_in_sql;die;
	$roll_trans_in_data=sql_select($roll_trans_in_sql);
	foreach($roll_trans_in_data as $val)
	{
		/*$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];*/

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_in_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$rcv_trans_in_data_arr[$trans_in_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$trans_in_string]['booking_no'] = $val[csf("booking_no")];
		$rcv_trans_in_data_arr[$trans_in_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count'] .= $count_arr[$val[csf("y_count")]].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count_id'] .= $val[csf("y_count")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_id'] .= $val[csf("brand_id")].",";

		$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

		$rcv_trans_in_data_arr[$trans_in_string]['trans_in'] = 5;
	}
	// echo "string";die;
	// echo "<pre>"; print_r($rcv_trans_in_data_arr);die;

	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcodeno)
		{
			execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_name,$barcodeno,175)");
		}
		oci_commit($con);
	}

	$production_sql = sql_select("SELECT a.body_part_id, a.color_id, b.po_breakdown_id, b.barcode_no, c.booking_id as program_no
    from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
    where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2) and b.entry_form in(2) and c.receive_basis=2 and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid=$user_name and d.entry_form=175");
    $yarn_prod_id_check=array();$prog_no_check=array();
    foreach ($production_sql as $row)
    {
        $prodBarcodeData[$row[csf("program_no")]]["body_part_id"]=$row[csf("body_part_id")];
        $prodBarcodeData[$row[csf('program_no')]]["color_id"]=$row[csf('color_id')];
        $prodBarcodeData[$row[csf('program_no')]]["po_id"]=$row[csf('po_breakdown_id')];
    }

	$roll_trans_out_sql = "SELECT i.id as program_no, j.booking_no, g.job_no as fso_no, g.id as po_id, a.body_part_id, k.detarmination_id, k.gsm, k.dia_width, e.color_id, 
	a.from_prod_id as prod_id, c.qnty
	from inv_item_transfer_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, pro_grey_prod_entry_dtls e, FABRIC_SALES_ORDER_MST g, tmp_job_no h, ppl_planning_info_entry_dtls i, ppl_planning_info_entry_mst j, product_details_master k 
	where a.id=b.dtls_id and b.entry_form in (133) and b.trans_type=6 and a.id=c.dtls_id and c.entry_form in (133) and c.barcode_no=d.barcode_no and d.entry_form in(2) and d.receive_basis=2 and d.dtls_id=e.id and b.po_breakdown_id=g.id and g.id=h.job_id and h.userid=$user_name and h.entry_form=999 and d.booking_no=cast(i.id as varchar(4000)) and i.mst_id=j.id and a.from_prod_id=k.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $roll_trans_out_sql;die;
	$roll_trans_out_data=sql_select($roll_trans_out_sql);
	foreach($roll_trans_out_data as $val)
	{
		/*$po_ref_arr[$val[csf("po_id")]]['po_number'] =$val[csf("po_number")];
		$po_ref_arr[$val[csf("po_id")]]['grouping'] =$val[csf("grouping")];
		$po_ref_arr[$val[csf("po_id")]]['file_no'] =$val[csf("file_no")];
		$po_ref_arr[$val[csf("po_id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_ref_arr[$val[csf("po_id")]]['job_no'] =$val[csf("job_no")];
		$po_ref_arr[$val[csf("po_id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_ref_arr[$val[csf("po_id")]]['style_ref_no'] =$val[csf("style_ref_no")];*/

		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_out_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$transfer_out_data_arr[$trans_out_string] += $val[csf("qnty")];
	}
	// echo "string";die;

	$reqn_qnty_array = array();
	if($update_id)
	{
		$up_cond = " and a.mst_id !=$update_id";
	}

	$reqnData = sql_select("SELECT a.reqn_qty, a.program_booking_pi_id, a.color_id, a.po_id, a.body_part_id, a.is_sales, a.prod_id, a.determination_id 
	from tmp_job_no c, FABRIC_SALES_ORDER_MST b, pro_fab_reqn_for_batch_dtls a 
	where a.entry_form=553 and c.job_id=b.id and c.userid=$user_name and c.entry_form=999 and b.id=a.po_id and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 $up_cond");
	foreach ($reqnData as $row) 
	{
		$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]+=$row[csf('reqn_qty')];
	}

	$i=1;
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	$sql_book_color = sql_select("SELECT a.id as po_id, b.fabric_color_id, d.color_name 
	from tmp_job_no c, FABRIC_SALES_ORDER_MST a, wo_booking_dtls b, lib_color d 
	where c.job_id=a.id and b.booking_type=1 and b.is_short=2 and c.userid=$user_name and a.sales_booking_no=b.booking_no
	and c.entry_form=999  and b.fabric_color_id=d.id and b.status_active=1 and b.is_deleted=0 
	group by a.id, b.fabric_color_id, d.color_name");

	foreach( $sql_book_color as $row) 
	{
		$book_color_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]]=$row[csf('color_name')];
	}
	unset($sql_book_color);

	execute_query("delete from tmp_job_no where userid=$user_name and entry_form=999");
	execute_query("delete from tmp_barcode_no where userid=$user_name and entry_form=175");
	oci_commit($con);

	/* echo "<pre>";
	print_r($search_ref);
	echo "</pre>"; */
	

	foreach ($rcv_trans_in_data_arr as $dataStr=> $val) 
	{
		$dataStrArr = explode("*",$dataStr);
		$program_no = $dataStrArr[0];
		$po_id = $dataStrArr[1];
		$prod_id = $dataStrArr[2];
		$body_part_id = $dataStrArr[3];
		$color_id = $dataStrArr[4];

		$po_number = $po_ref_arr[$po_id]['po_number'];
		$grouping = $po_ref_arr[$po_id]['grouping'];
		$file_no = $po_ref_arr[$po_id]['file_no'];
		$shipment_date = $po_ref_arr[$po_id]['shipment_date'];
		$job_no = $po_ref_arr[$po_id]['job_no'];
		$style_ref_no = $po_ref_arr[$po_id]['style_ref_no'];
		$buyer_name = $po_ref_arr[$po_id]['buyer_name'];

		$febric_description_id = $prod_ref_arr[$prod_id]['febric_description_id'];
		$gsm = $prod_ref_arr[$prod_id]['gsm'];
		$width = $prod_ref_arr[$prod_id]['width'];

		$yarn_counts = $val['yarn_count'];
		$brand_names = $val['brand_name'];
		$yarn_lots = $val['yarn_lot'];


		$yarn_counts = implode(",",array_unique(explode(",",chop($yarn_counts,","))));
		$brand_names = implode(",",array_unique(explode(",",chop($brand_names,","))));
		$yarn_lot = implode(",",array_unique(explode(",",chop($yarn_lots,","))));


		$yarn_count_id = implode(",",array_unique(explode(",",chop($val['yarn_count_id'],","))));
		$brand_id = implode(",",array_unique(explode(",",chop($val['brand_id'],","))));

		$color_names="";
		$colorArr= explode(",",$color_id);
		foreach ($colorArr as  $colorval) {
			$color_names .= $color_arr[$colorval].",";
		}
		$color_names = chop($color_names,",");

		$grey_stock = $val['qnty'] + $issue_ret_data_arr[$dataStr] - $issue_data_arr[$dataStr] - $transfer_out_data_arr[$dataStr];
		//echo $val['qnty'] ."+". $issue_ret_data_arr[$dataStr] ."-". $issue_data_arr[$dataStr] ."-". $transfer_out_data_arr[$dataStr]."<br>";
		//$grey_stock_without_issue = $val['qnty'] + $issue_ret_data_arr[$dataStr]-$transfer_out_data_arr[$dataStr];
		$grey_stock_without_issue = $val['qnty']-$transfer_out_data_arr[$dataStr];

		$pre_reqn_qnty = $reqn_qnty_array[$program_no][$po_id][ $prod_id][$body_part_id][$febric_description_id][$color_id];
		$req_balance = $grey_stock_without_issue -$pre_reqn_qnty;
		//echo "job = $job_no, file = $file_no, int=$grouping, body= $body_part_id, deter=$febric_description_id, color=$color_id<br>";
		if($search_ref[$job_no][$file_no][$grouping][$body_part_id][$febric_description_id][$color_id]=="")
		{
			$bgcolor ="#ff9c94";
		}
		else
		{
			$bgcolor ="#b6fec7";
		}

		$requisition_qnty = $this_reqn_qnty_array[$program_no][$po_id][ $prod_id][$body_part_id][$febric_description_id][$color_id]['qnty'];
		$details_id = $this_reqn_qnty_array[$program_no][$po_id][ $prod_id][$body_part_id][$febric_description_id][$color_id]['id'];

		if ($val['trans_in']==5) 
		{
			$ref_body_part_id=$prodBarcodeData[$program_no]["body_part_id"];
			$ref_color_id=$prodBarcodeData[$program_no]["color_id"];
			$ref_po_id=$prodBarcodeData[$program_no]["po_id"];
		}
		else
		{
			$ref_body_part_id=$body_part_id;
			$ref_color_id=$color_id;
			$ref_po_id=$po_id;
		}

		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" >
			<td width="30"><? echo $i;?></td>
			<td width="70"><? echo $grouping;?></td>
			<td width="60"><? echo $file_no;?></td>
			<td width="100"><? echo $buyer_arr[$buyer_name];?></td>
			<td width="80"><? echo $style_ref_no;?></td>
			<td width="80"><? echo $job_no;?></td>
			<td width="80"><? echo $val['fso_no'];//$po_number;?></td>
			<td width="80"><? echo $shipment_date;?></td>
			<td width="70" align="center"><a href='##' onClick="generate_report2(<? echo $program_no; ?>)"><? echo $program_no; ?></a>&nbsp;</td>
			<td width="80"  style="word-wrap: break-word;word-break: break-all;" title="<?=$ref_body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
			<td width="175" style="word-wrap: break-word;word-break: break-all;"><? echo $constructtion_arr[$febric_description_id]."". $composition_arr[$febric_description_id];?></td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;">
				<? 
				$br="";
					if($yarn_counts) 
					{
						echo $yarn_counts;
						$br="<br>";
					}
					echo $br.$brand_names;
				?>
			</td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;"><? echo $yarn_lot;?></td>
			<td width="40"><? echo $gsm;?></td>
			<td width="40"><? echo $width;?></td>
			<td width="90" style="word-wrap: break-word;word-break: break-all;" title="<?=$ref_color_id;?>"><? echo $color_names;?></td>

			<td width="90" style="word-wrap: break-word;word-break: break-all;">
				<?
					echo create_drop_down( "batchColorId$i", 80,$book_color_arr[$po_id],"", 1, "--Select--", "", "","","","","","","","","batchColorId[]","" );
				?>
			</td> 

			<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right" title="<?=$grey_stock_without_issue;?>"><a href="##" onclick="open_mypage('<? echo $program_no;?>','<? echo $po_id;?>','<? echo $prod_id;?>','<? echo $body_part_id;?>','<? echo $color_id;?>','ttl_rcv_popup');"><? echo number_format( $grey_stock_without_issue,2,'.','');?></a></td>
			
			<td width="70" align="right"><? echo number_format( $grey_stock,2);?></td>
			<td width="70" align="right">
				<input type="text" id="previous_reqsnQty<? echo $i; ?>" name="previous_reqsnQty[]" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($pre_reqn_qnty,2); ?>" readonly disabled>
			</td>
			<td width="80" align="right"><? echo number_format($req_balance,2); ?></td>

			<td width="80" align="right"><input type="text" name="txt_collar_cuff_qnty<? echo $i; ?>" id="txt_collar_cuff_qnty<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onClick="openmypage_collar_cuff('<? echo $i; ?>','<? echo $program_no;?>','<? echo $ref_po_id;?>','<? echo $prod_id;?>','<? echo $ref_body_part_id;?>','<? echo $ref_color_id;?>','<? echo $val['booking_no'];?>')" placeholder="Single Click" readonly/></td>

			<td width="90" align="right">
				<input class="text_boxes_numeric" type="text" value="<? echo $requisition_qnty;?>" style="width:65px" id="reqsnQty<? echo $i; ?>" name="reqsnQty[]" onKeyUp="fnc_count_total_qty();fnc_check_balance_qty(<? echo $i ?>);"/>
			</td>
			<td width="90" align="right"><? echo number_format($issue_data_arr[$dataStr]-$issue_ret_data_arr[$dataStr],2); ?></td>
			<td width="90" ><input type="text" value="" class="text_boxes" style="width:65px" id="remarks<? echo $i; ?>" name="remarks[]"/></td>
			<td width="60"><? echo $prod_id?></td>
			<td width="50">
				<input type="button" value="-" class="formbuttonplasminus" style="width:30px" id="decrease1" name="decrease[]" onclick="fn_deleteRow(<? echo $i;?>)"/>
				<input type="hidden" value="<? echo $program_no; ?>" id="programBookingId<? echo $i; ?>" name="programBookingId[]"/>
				<input type="hidden" value="<? echo $buyer_name; ?>" id="buyerId<? echo $i; ?>" name="buyerId[]"/>
				<input type="hidden" value="<? echo $po_id; ?>" id="poId<? echo $i; ?>" name="poId[]"/>
				<input type="hidden" value="<? echo $file_no; ?>" id="fileNo<? echo $i; ?>" name="fileNo[]"/>
				<input type="hidden" value="<? echo $grouping; ?>" id="grouping<? echo $i; ?>" name="grouping[]"/>
				<input type="hidden" value="<? echo $job_no; ?>" id="jobNo<? echo $i; ?>" name="jobNo[]"/>
				<input type="hidden" value="<? echo $color_id; ?>" id="colorId<? echo $i; ?>" name="colorId[]"/>
				<input type="hidden" value="<? echo $body_part_id; ?>" id="bodyPartId<? echo $i; ?>" name="bodyPartId[]"/>
				<input type="hidden" value="<? echo $febric_description_id; ?>" id="deterId<? echo $i; ?>" name="deterId[]"/>
				<input type="hidden" value="<? echo $gsm ; ?>" id="gsm<? echo $i; ?>" name="gsm[]"/>
				<input type="hidden" value="<? echo $width; ?>" id="width<? echo $i; ?>" name="width[]"/>
				<input type="hidden" value="<? echo $prod_id; ?>" id="prodId<? echo $i; ?>" name="prodId[]"/>
				<input type="hidden" value="<? echo $details_id;?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>
				<input type="hidden" value="<? echo $is_sales;?>" id="isSales<? echo $i; ?>" name="isSales[]"/>
				
				<input type="hidden" value="<? echo $booking_without_order;?>" id="booking_without_order<? echo $i; ?>" name="booking_without_order[]"/>
				<input type="hidden" value="<? echo number_format($req_balance,2,'.','');?>" id="totBalQty<? echo $i; ?>" name="reqnBalQty[]"/>
				<input type="hidden" value="<? echo number_format($grey_stock,2,'.','');?>" id="bookintQty<? echo $i; ?>" name="bookintQty[]"/>
				<input type="hidden" value="<? echo number_format($grey_stock_without_issue,2,'.','');?>" id="stockWithoutIssueQty<? echo $i; ?>" name="stockWithoutIssueQty[]"/>
				<input type="hidden" value="<? echo number_format($issue_data_arr[$dataStr]-$issue_ret_data_arr[$dataStr],2,'.','');?>" id="issueQty<? echo $i; ?>" name="issueQty[]"/>
				
				<input type="hidden" value="<? echo $yarn_count_id; ?>" id="yCountId<? echo $i; ?>" name="yCountId[]"/>
				<input type="hidden" value="<? echo $brand_id; ?>" id="brandId<? echo $i; ?>" name="brandId[]"/>
				<input type="hidden" value="<? echo $yarn_lot; ?>" id="yLotId<? echo $i; ?>" name="yLotId[]"/>
				<input type="hidden" id="txt_collar_cuff_string<? echo $i; ?>" name="txt_collar_cuff_string[]"/>
			</td>
		</tr>
		<?
		$i++;
	}
}

if( $action == 'populate_list_view_for_sales' ) // inserted data
{
 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$sql="SELECT id, receive_basis, program_booking_pi_no, program_booking_pi_id, body_part_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, batch_color, reqn_qty, remarks, gsm_weight, dia_width, booking_qty, is_sales, booking_without_order, yarn_count, yarn_lot, brand_id from pro_fab_reqn_for_batch_dtls where mst_id='$data' and entry_form=553 and status_active=1 and is_deleted=0 and is_sales=1";
	$result=sql_select($sql);

	foreach ($result as $row)
	{
		if ($row[csf('is_sales')] == 1) {
			$fsoArr[$row[csf('po_id')]] = $row[csf('po_id')];
		}else{
			$orderArr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$prodIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$programNoArr[$row[csf('program_booking_pi_id')]] = $row[csf('program_booking_pi_id')];
	}
	$order_ids = implode(",",$fsoArr);

	$job_fso_chk=array();
	$job_from_fso =  sql_select("SELECT a.id, c.booking_no,c.booking_type,c.is_short, b.job_no_prefix_num,b.job_no, a.job_no as fso_no, d.po_number,d.grouping,d.file_no,d.pub_shipment_date,b.buyer_name,b.style_ref_no
	from fabric_sales_order_mst a, wo_booking_dtls c, wo_po_details_master b, WO_PO_BREAK_DOWN d
	where a.sales_booking_no=c.booking_no and c.job_no=b.job_no and b.id=d.JOB_ID and b.job_no = d.job_no_mst and c.PO_BREAK_DOWN_ID=d.id and c.BOOKING_TYPE in(1,4) and a.within_group=1 and a.id in($order_ids)
	union all 
	select a.id, b.booking_no,4 as booking_type,0 as is_short,0 as job_no_prefix_num,null as job_no, a.job_no as fso_no, null as po_number,null as grouping,null as file_no,null as pub_shipment_date,null as buyer_name,null as style_ref_no from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b 
	where a.within_group=1 and a.sales_booking_no=b.booking_no and a.id in($order_ids)");
	$po_arr=array();
    foreach ($job_from_fso as $val)
    {
        $po_arr[$val[csf("id")]]['no'] =$val[csf("po_number")];
		$po_arr[$val[csf("id")]]['ref'] =$val[csf("grouping")];
		$po_arr[$val[csf("id")]]['file'] =$val[csf("file_no")];
		$po_arr[$val[csf("id")]]['shipment_date'] =$val[csf("pub_shipment_date")];
		$po_arr[$val[csf("id")]]['job_no'] =$val[csf("job_no")];
		$po_arr[$val[csf("id")]]['buyer_name'] =$val[csf("buyer_name")];
		$po_arr[$val[csf("id")]]['style_ref_no'] =$val[csf("style_ref_no")];
    }
    // echo "<pre>";print_r($po_arr);die;

	$prod_ids = implode(",",$prodIdArr);
	$product_arr=array();
 	$sql_product="SELECT id, gsm, dia_width from product_details_master where item_category_id=13 and id in ($prod_ids)";
	$data_array_product=sql_select($sql_product);
	foreach( $data_array_product as $row )
	{
		$product_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$product_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	$program_nos = implode(",",$programNoArr);
	$reqn_qnty_array=array();
	$reqnData=sql_select("SELECT  program_booking_pi_id, po_id, prod_id, determination_id, color_id, body_part_id, sum(reqn_qty) as qnty from pro_fab_reqn_for_batch_dtls where entry_form=553 and status_active=1 and is_deleted=0 and program_booking_pi_id in ($program_nos) and mst_id !=$data group by program_booking_pi_id, po_id, prod_id, determination_id, color_id, body_part_id");

	foreach($reqnData as $row)
	{
		$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]+=$row[csf('qnty')];
	}


	//Current Grey Stock Quantity here
	$auto_rcv_sql="SELECT f.id as program_no, g.booking_no, d.id as po_id, d.job_no as fso_no, b.prod_id, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, c.quantity, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g
	where a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=b.mst_id and c.entry_form=2 and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.status_active=1 and b.is_deleted=0 and a.booking_id=f.id and f.mst_id=g.id and b.trans_id!=0 and d.id in ($order_ids)";
	//echo $auto_rcv_sql;die;
	$auto_rcv_data = sql_select($auto_rcv_sql);

	foreach ($auto_rcv_data as $val) 
	{
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];

		$rcv_trans_in_data_arr[$rcv_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("quantity")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_recv_gross=sql_select("SELECT g.booking_id as program_no, i.booking_no, e.id as po_id, e.job_no as fso_no, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, sum(c.quantity) as knitting_qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, fabric_sales_order_mst e, inv_receive_master g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i
	where  a.id=b.mst_id and a.receive_basis=9 and a.item_category=13 and a.entry_form=22 and b.id=c.dtls_id  and c.entry_form=22 and c.po_breakdown_id=e.id and g.entry_form=2 and a.booking_id=g.id and b.status_active=1 and b.is_deleted=0 and g.booking_id=h.id and h.mst_id=i.id and e.id in ($order_ids)
	group by g.booking_id, i.booking_no, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id,e.id, e.job_no"); 
	foreach ($sql_recv_gross as $val) 
	{
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];

		$rcv_trans_in_data_arr[$rcv_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("knitting_qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_recv_roll = sql_select("SELECT f.id as program_no, g.booking_no, c.barcode_no, d.id as po_id, d.job_no as fso_no, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.color_id, b.prod_id, c.qnty, b.yarn_lot, b.yarn_count, b.brand_id
	from  pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d, pro_roll_details a, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g
	where  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=58 and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.barcode_no=a.barcode_no and a.entry_form=2 and a.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 and c.booking_without_order=0 and d.id in ($order_ids)");
	foreach ($sql_recv_roll as $val) 
	{
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$rcv_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];

		$rcv_trans_in_data_arr[$rcv_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$rcv_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$rcv_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$rcv_string]['yarn_count'] .= $count_arr[$val[csf("yarn_count")]].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$rcv_string]['yarn_count_id'] .= $val[csf("yarn_count")].",";
		$rcv_trans_in_data_arr[$rcv_string]['brand_id'] .= $val[csf("brand_id")].",";
	}

	$sql_iss_roll = sql_select("SELECT f.id as program_no, g.booking_no, e.id as po_id, e.job_no as fso_no, 
	i.detarmination_id, i.gsm, i.dia_width, a.body_part_id, a.color_id, a.prod_id, j.quantity 
	from inv_grey_fabric_issue_dtls a, pro_roll_details b, pro_roll_details c, fabric_sales_order_mst e, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g, product_details_master i, order_wise_pro_details j 
	where a.id=b.dtls_id and b.barcode_no=c.barcode_no and b.entry_form=61 and c.entry_form=2 and b.booking_without_order=0 and b.is_sales=1 and b.po_breakdown_id=e.id and c.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.prod_id=i.id and a.id=j.dtls_id and j.entry_form=61 and e.id in ($order_ids)");
	foreach ($sql_iss_roll as $val) 
	{
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];
		$iss_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_data_arr[$iss_string] += $val[csf("quantity")];
	}
	//print_r($issue_data_arr);

	$roll_iss_rtn_sql="SELECT h.id as program_no, i.booking_no, f.id as po_id, f.job_no as fso_no, c.barcode_no,
	a.body_part_id, a.color_id, a.febric_description_id, a.gsm, a.width, a.prod_id, c.qnty 
	from pro_grey_prod_entry_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, FABRIC_SALES_ORDER_MST f, inv_receive_master x, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i 
	where a.id=b.dtls_id and b.entry_form=84 and a.id=c.dtls_id and c.entry_form=84 and c.barcode_no=d.barcode_no and d.entry_form=2 and d.receive_basis=2 and c.po_breakdown_id=f.id and d.mst_id=x.id and x.booking_id=h.id and h.mst_id=i.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.id in ($order_ids)";
	// echo $roll_iss_rtn_sql;die;
	$roll_iss_rtn_data=sql_select($roll_iss_rtn_sql);
	foreach($roll_iss_rtn_data as $val)
	{
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("febric_description_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("width")];

		$iss_ret_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$issue_ret_data_arr[$iss_ret_string] += $val[csf("qnty")];
	}

	$roll_trans_in_sql = "SELECT h.id as program_no, i.booking_no, e.id as po_id, e.job_no as fso_no,
	g.color_id, b.detarmination_id, b.gsm, b.dia_width, a.to_body_part as body_part_id, a.to_prod_id as prod_id, c.qnty, a.yarn_lot, a.y_count, a.brand_id 
	from inv_item_transfer_dtls a, product_details_master b, pro_roll_details c, FABRIC_SALES_ORDER_MST e, pro_roll_details f, pro_grey_prod_entry_dtls g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i
	where a.to_prod_id=b.id and a.id=c.dtls_id and c.entry_form in (133) and c.po_breakdown_id=e.id and c.barcode_no=f.barcode_no and f.receive_basis=2 and f.dtls_id=g.id and f.booking_no=cast(h.id as varchar(4000)) and h.mst_id=i.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id in ($order_ids)";
	// echo $roll_trans_in_sql;die;
	$roll_trans_in_data=sql_select($roll_trans_in_sql);
	foreach($roll_trans_in_data as $val)
	{
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_in_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];

		$rcv_trans_in_data_arr[$trans_in_string]['fso_no'] = $val[csf("fso_no")];
		$rcv_trans_in_data_arr[$trans_in_string]['qnty'] += $val[csf("qnty")];
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_lot'] .= $val[csf("yarn_lot")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count'] .= $count_arr[$val[csf("y_count")]].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_name'] .= $brand_arr[$val[csf("brand_id")]].",";

		$rcv_trans_in_data_arr[$trans_in_string]['yarn_count_id'] .= $val[csf("y_count")].",";
		$rcv_trans_in_data_arr[$trans_in_string]['brand_id'] .= $val[csf("brand_id")].",";
	}
	//echo "<pre>";
	//print_r($search_ref);

	$roll_trans_out_sql = "SELECT i.id as program_no, j.booking_no, g.job_no as fso_no, g.id as po_id, a.body_part_id, k.detarmination_id, k.gsm, k.dia_width, e.color_id, 
	a.from_prod_id as prod_id, c.qnty
	from inv_item_transfer_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, pro_grey_prod_entry_dtls e, FABRIC_SALES_ORDER_MST g, ppl_planning_info_entry_dtls i, ppl_planning_info_entry_mst j, product_details_master k 
	where a.id=b.dtls_id and b.entry_form in (133) and b.trans_type=6 and a.id=c.dtls_id and c.entry_form in (133) and c.barcode_no=d.barcode_no and d.entry_form in(2) and d.receive_basis=2 and d.dtls_id=e.id and b.po_breakdown_id=g.id and d.booking_no=cast(i.id as varchar(4000)) and i.mst_id=j.id and a.from_prod_id=k.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.id in ($order_ids)";
	// echo $roll_trans_out_sql;die;
	$roll_trans_out_data=sql_select($roll_trans_out_sql);
	foreach($roll_trans_out_data as $val)
	{
		$prod_ref_arr[$val[csf("prod_id")]]['febric_description_id'] =$val[csf("detarmination_id")];
		$prod_ref_arr[$val[csf("prod_id")]]['gsm'] =$val[csf("gsm")];
		$prod_ref_arr[$val[csf("prod_id")]]['width'] =$val[csf("dia_width")];

		$trans_out_string = $val[csf("program_no")].'*'.$val[csf("po_id")].'*'.$val[csf("prod_id")].'*'.$val[csf("body_part_id")].'*'.$val[csf("color_id")];
		$transfer_out_data_arr[$trans_out_string] += $val[csf("qnty")];
	}

	$sql_book_color = sql_select("SELECT a.id as po_id, b.fabric_color_id, d.color_name from FABRIC_SALES_ORDER_MST a, wo_booking_dtls b, lib_color d where b.booking_type=1 and b.is_short=2 and a.sales_booking_no=b.booking_no and b.fabric_color_id=d.id and b.status_active=1 and b.is_deleted=0 and a.id in ($order_ids) group by a.id, b.fabric_color_id, d.color_name");

	foreach( $sql_book_color as $row) 
	{
		$book_color_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]]=$row[csf('color_name')];
	}
	unset($sql_book_color);

	$i=1;
	foreach ($result as $row) // data show here
	{
		$dataStr = $row[csf('program_booking_pi_id')].'*'. $row[csf('po_id')].'*'.$row[csf('prod_id')].'*'. $row[csf('body_part_id')] .'*'. $row[csf('color_id')];
		$fso_no=$rcv_trans_in_data_arr[$dataStr]['fso_no'];
		$grey_stock = $rcv_trans_in_data_arr[$dataStr]['qnty'] + $issue_ret_data_arr[$dataStr] - $issue_data_arr[$dataStr] - $transfer_out_data_arr[$dataStr];
		$grey_stock_without_issue = $rcv_trans_in_data_arr[$dataStr]['qnty'] - $transfer_out_data_arr[$dataStr];
		//$grey_stock_without_issue = $rcv_trans_in_data_arr[$dataStr]['qnty'] + $issue_ret_data_arr[$dataStr] - $transfer_out_data_arr[$dataStr];
		
		$totReqnQty=$reqn_qnty_array[$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]];
		$reqQty=number_format($grey_stock_without_issue ,2,'.','');
		$totReqnQty=number_format($totReqnQty,2,'.','');
		$balance=number_format($reqQty-$totReqnQty,2,'.','');
	
		
		$color='';
		$color_id=array_unique(explode(',',$row[csf('color_id')]));
		foreach($color_id as $id)
		{
			if($id>0)
			{
				if($color=='') $color=$color_arr[$id]; else $color.=", ".$color_arr[$id];
			}
		}	

		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		$yarn_count_arr = explode(",",$row[csf('yarn_count')]);
		$yarn_counts="";
		foreach ($yarn_count_arr as $val) 
		{
			$yarn_counts .= $count_arr[$val].",";
		}
		$yarn_counts = chop($yarn_counts,",");

		$brand_id_arr = explode(",",$row[csf('brand_id')]);
		$brand_name="";
		foreach ($brand_id_arr as $val) 
		{
			$brand_name .= $brand_arr[$val].",";
		}
		$brand_name = chop($brand_name,",");
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" >
			<td width="30"><? echo $i;?></td>
			<td width="70"><? echo $po_arr[$row[csf('po_id')]]['ref'];?></td>
			<td width="60"><? echo $po_arr[$row[csf('po_id')]]['file'];?></td>
			<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
			<td width="80"><? echo $po_arr[$row[csf('po_id')]]['style_ref_no'];?></td>
			<td width="80"><? echo $row[csf('job_no')];?></td>
			<td width="80"><? echo $fso_no;//$po_arr[$row[csf('po_id')]]['no'];?></td>
			<td width="80"><? echo $po_arr[$row[csf('po_id')]]['shipment_date'];?></td>
			<td width="70" align="center"><a href='##' onClick="generate_report2(<? echo $row[csf('program_booking_pi_id')]; ?>)"><? echo $row[csf('program_booking_pi_id')]; ?></a>&nbsp;</td>
			<td width="80"  style="word-wrap: break-word;word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]];?></td>
			<td width="175" style="word-wrap: break-word;word-break: break-all;"><? echo $constructtion_arr[$row[csf('determination_id')]]."". $composition_arr[$row[csf('determination_id')]];?></td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;">
			<? 
			echo $yarn_counts."<br>". $brand_name;
			?>
			</td>
			<td width="150" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('yarn_lot')];?></td>
			<td width="40"><? echo $row[csf('gsm_weight')];?></td>
			<td width="40"><? echo $row[csf('dia_width')];?></td>
			<td width="90" style="word-wrap: break-word;word-break: break-all;"><? echo $color;?></td>

			<td width="90" style="word-wrap: break-word;word-break: break-all;">
				<?
				$batch_color = $row[csf('batch_color')];
					echo create_drop_down( "batchColorId$i", 80,$book_color_arr[$row[csf('po_id')]],"", 1, "--Select--", $batch_color, "","","","","","","","","batchColorId[]","" );
				?>
			</td>

			<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right" title="<?=$grey_stock_without_issue;?>"><a href="##" onclick="open_mypage('<? echo $row[csf('program_booking_pi_id')];?>','<? echo $row[csf('po_id')];?>','<? echo $row[csf('prod_id')];?>','<? echo $row[csf('body_part_id')];?>','<? echo $row[csf('color_id')];?>','ttl_rcv_popup');"><? echo number_format( $grey_stock_without_issue,2,'.','');?></a></td>

			
			<td width="70" align="right"><? echo number_format($grey_stock,2);//number_format( $row[csf('booking_qty')],2);?></td>
			<td width="70" align="right">
				<input type="text" id="previous_reqsnQty<? echo $i; ?>" name="previous_reqsnQty[]" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($totReqnQty,2); ?>" readonly disabled>
			</td>
			<td width="80" align="right"><? echo number_format($balance,2); ?></td>

			<td width="80" align="right"><input type="text" name="txt_collar_cuff_qnty<? echo $i; ?>" id="txt_collar_cuff_qnty<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onClick="openmypage_collar_cuff(<? echo $i; ?>)" placeholder="Single Click" readonly/></td>
			
			<td width="90" align="right">
				<input type="text" value="<? echo number_format($row[csf('reqn_qty')],2,'.',''); ?>" class="text_boxes_numeric" style="width:65px" id="reqsnQty<? echo $i; ?>" name="reqsnQty[]" onKeyUp="fnc_count_total_qty();fnc_check_balance_qty(<? echo $i ?>);"  />
			</td>
			<td width="90" align="right"><? echo number_format($issue_data_arr[$dataStr]-$issue_ret_data_arr[$dataStr],2);?></td>
			<td width="90" >
				<input type="text" value="<? echo $row[csf('remarks')];?>" class="text_boxes" style="width:65px" id="remarks<? echo $i; ?>" name="remarks[]"/>
			</td>
			<td width="60"><? echo $row[csf('prod_id')]?></td>
			<td width="50">
				<input type="button" value="-" class="formbuttonplasminus" style="width:30px" id="decrease1" name="decrease[]" onclick="fn_deleteRow(<? echo $i;?>)"/>

				<input type="hidden" value="<? echo $row[csf('program_booking_pi_id')]; ?>" id="programBookingId<? echo $i; ?>" name="programBookingId[]"/>
				<input type="hidden" value="<? echo $row[csf('buyer_id')]; ?>" id="buyerId<? echo $i; ?>" name="buyerId[]"/>
				<input type="hidden" value="<? echo $row[csf('po_id')]; ?>" id="poId<? echo $i; ?>" name="poId[]"/>
				<input type="hidden" value="<? echo $po_arr[$row[csf('po_id')]]['file']; ?>" id="fileNo<? echo $i; ?>" name="fileNo[]"/>
				<input type="hidden" value="<? echo $po_arr[$row[csf('po_id')]]['ref']; ?>" id="grouping<? echo $i; ?>" name="grouping[]"/>
				<input type="hidden" value="<? echo $row[csf('job_no')]; ?>" id="jobNo<? echo $i; ?>" name="jobNo[]"/>
				<input type="hidden" value="<? echo $row[csf('color_id')]; ?>" id="colorId<? echo $i; ?>" name="colorId[]"/>
				<input type="hidden" value="<? echo $row[csf('body_part_id')]; ?>" id="bodyPartId<? echo $i; ?>" name="bodyPartId[]"/>
				<input type="hidden" value="<? echo $row[csf('determination_id')]; ?>" id="deterId<? echo $i; ?>" name="deterId[]"/>
				<input type="hidden" value="<? echo $row[csf('gsm_weight')] ; ?>" id="gsm<? echo $i; ?>" name="gsm[]"/>
				<input type="hidden" value="<? echo $row[csf('dia_width')]; ?>" id="width<? echo $i; ?>" name="width[]"/>
				<input type="hidden" value="<? echo $row[csf('prod_id')]; ?>" id="prodId<? echo $i; ?>" name="prodId[]"/>
				<input type="hidden" value="<? echo $row[csf('id')];?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>
				<input type="hidden" value="<? echo $row[csf('is_sales')];?>" id="isSales<? echo $i; ?>" name="isSales[]"/>
				<input type="hidden" value="<? echo $row[csf('booking_without_order')];?>" id="booking_without_order<? echo $i; ?>" name="booking_without_order[]"/>
				<input type="hidden" value="<? echo number_format($balance,2,'.','');?>" id="reqnBalQty<? echo $i; ?>" name="reqnBalQty[]"/>
				<input type="hidden" value="<? echo number_format($grey_stock,2,'.','');//$row[csf('booking_qty')]?>" id="bookintQty<? echo $i; ?>" name="bookintQty[]"/>

				<input type="hidden" value="<? echo number_format($grey_stock_without_issue,2,'.','');?>" id="stockWithoutIssueQty<? echo $i; ?>" name="stockWithoutIssueQty[]"/>
				<input type="hidden" value="<? echo number_format($issue_data_arr[$dataStr]-$issue_ret_data_arr[$dataStr],2,'.','');?>" id="issueQty<? echo $i; ?>" name="issueQty[]"/>

				<input type="hidden" value="<? echo $row[csf('yarn_count')]; ?>" id="yCountId<? echo $i; ?>" name="yCountId[]"/>
				<input type="hidden" value="<? echo $row[csf('brand_id')]; ?>" id="brandId<? echo $i; ?>" name="brandId[]"/>
				<input type="hidden" value="<? echo $row[csf('yarn_lot')]; ?>" id="yLotId<? echo $i; ?>" name="yLotId[]"/>
			</td>
		</tr>
		<?
		$i++;
	}
	
	exit();
}

if ($action == "print_fab_req_for_batch_2")
{
	extract($_REQUEST);
	$ex_data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	
	$sql = "SELECT a.id, a.program_booking_pi_no, a.program_booking_pi_id, a.po_id, a.buyer_id, a.job_no, a.prod_id, a.determination_id, a.color_id, a.reqn_qty, a.remarks,a.gsm_weight, a.dia_width,a.construction,a.composition,a.file_no,a.grouping, a.body_part_id,a.color_type_id,a.yarn_lot, a.brand_id,a.yarn_count,a.booking_qty, a.is_sales, a.customer_buyer, b.job_no as fso_no,b.sales_booking_no, b.within_group, b.style_ref_no, a.batch_color
	from pro_fab_reqn_for_batch_dtls a, fabric_sales_order_mst b where a.po_id=b.id and a.mst_id=$ex_data[1] and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.entry_form=553";
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $vals)
	{
		$all_booking_nos_arr[$vals[csf("program_booking_pi_no")]]=$vals[csf("program_booking_pi_no")];
		$all_booking_ids_arr[$vals[csf("program_booking_pi_id")]]=$vals[csf("program_booking_pi_id")];
		$all_po_id_arr[$vals[csf("po_id")]]=$vals[csf("po_id")];
	}
	$all_booking_nos = "'" . implode("','", $all_booking_nos_arr) . "'";
	$all_booking_ids=implode(",", $all_booking_ids_arr);
	$all_po_ids=implode(",", $all_po_id_arr);

	$reqn_qnty_array = array();
	$sales_id="";
	$reqnData = sql_select("SELECT reqn_qty,color_type_id,color_id,program_booking_pi_no,po_id,job_no,buyer_id, body_part_id,gsm_weight, dia_width,determination_id, construction, composition, grouping,file_no 
	from pro_fab_reqn_for_batch_dtls where entry_form=553 and status_active=1 and is_deleted=0");
	foreach ($reqnData as $row)
	{
		$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('determination_id')] . $row[csf('grouping')] . $row[csf('file_no')];
		$reqn_qnty_array[$key] += $row[csf('reqn_qty')];
	}
	
	$color_range_sql="SELECT a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range, b.stitch_length, b.machine_dia,c.po_id, c.determination_id
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,PPL_PLANNING_ENTRY_PLAN_DTLS c
	where a.id=b.mst_id and b.id=c.DTLS_ID and a.status_active=1 and b.status_active=1 and b.id in($all_booking_ids)
	and c.status_active=1 group by a.booking_no, a.body_part_id, a.fabric_desc,a.color_type_id,b.color_id, b.color_range,c.po_id, c.determination_id, b.stitch_length, b.machine_dia ";

	foreach ( sql_select($color_range_sql) as $row)
	{
		$fab_desc=explode(",",trim($row[csf('fabric_desc')]));
		foreach( array_unique(explode(",",$row[csf('color_id')])) as $col_id)
		{
			$tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['color_range'].=$color_range[$row[csf('color_range')]].',';
			$tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['color_type_id'].=$color_type[$row[csf('color_type_id')]].',';
			$tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['stitch_length'].=$row[csf('stitch_length')].',';
			$tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['machine_dia'].=$row[csf('machine_dia')].',';
		}
	}
	// echo "<pre>";print_r($tr_sales_wise_color_range);die;

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$sql_mst = "Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst);
	?>
	<div style="width:2100px; border:1px solid #999">
		<table width="95%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%'/>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="center">
						<tr class="form_caption">
							<td align="center" style="font-size:34px">
							<strong><? echo $company_library[$ex_data[0]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:34px"><strong>Unit
								: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center"
							style="font-size:34px"><? echo show_company($ex_data[0], '', ''); ?> </td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:34px"><u><strong><? echo $ex_data[3]; ?></strong></u>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing="0" align="" border="0" style="font-size: 34px;">
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
				$key = $row[csf('color_type_id')] . $row[csf('color_id')] . $row[csf('program_booking_pi_no')] . $row[csf('buyer_id')] . $row[csf('body_part_id')] . $row[csf('gsm_weight')] . $row[csf('dia_width')] . $row[csf('determination_id')] . $row[csf('grouping')] . $row[csf('file_no')];
				$totReqQty = $reqn_qnty_array[$key];
				$balance = $row[csf('booking_qty')] - $totReqQty;
				$buyer = $row[csf('buyer_id')];

				$yarn_count_arr = array_unique(explode(",",$row[csf('yarn_count')]));
				$yarn_counts="";
				foreach ($yarn_count_arr as $val) 
				{
					$yarn_counts .= $count_arr[$val].",";
				}
				$yarn_counts = chop($yarn_counts,",");

				$yarn_count_arr = array_unique(explode(",",$row[csf('yarn_count')]));
				$yarn_counts="";
				foreach ($yarn_count_arr as $val) 
				{
					$yarn_counts .= $count_arr[$val].",";
				}
				$yarn_counts = chop($yarn_counts,",");

				$color_range_name = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['color_range'];
				$color_range_name = implode(",",array_unique(explode(",",$color_range_name)));

				$color_type_id_name = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['color_type_id'];
				$color_type_id_name = implode(",",array_unique(explode(",",$color_type_id_name)));

				$stitch_length = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['stitch_length'];
				$stitch_length = implode(",",array_unique(explode(",",$stitch_length)));

				$machine_dia = $tr_sales_wise_color_range[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['machine_dia'];
				$machine_dia = implode(",",array_unique(explode(",",$machine_dia)));

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if ($i==1){
				?>
				<thead bgcolor="#dddddd" style="font-size:18px">
					<th width="30">Sl</th>
					<th width="60">Buyer</th>
					<th width="120">Sales Order No</th>
					<th width="100">Booking No</th>
					<th width="80">Style Ref. </th>
					<th width="140">Body Part</th>
					<th width="100">Color Type</th>
					<th width="140">Construction</th>
					<th width="140">Composition</th>
					<th width="50">Yarn count</th>
					<th width="50">Yarn lot</th>
					<th width="50">Yarn Brand</th>
					<th width="100">Stich lenth</th>
					<th width="50">Machine dia</th>
					<th width="50">F. GSM</th>
					<th width="50">F. Dia</th>
					<th width="100">Color Range</th>
					<th width="100">Color/Code</th>
					<th width="100">Batch Color</th>
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
						<td align="center"><p><? echo $row[csf('fso_no')]; ?></p></td>
						<td align="center"><? echo $row[csf('sales_booking_no')];//$booking_no; ?></td>
						<td><? echo $row[csf('style_ref_no')]; ?></td>					
						<td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td title="<? echo $row[csf('po_id')].'='.$row[csf('body_part_id')].'='.$row[csf('determination_id')]; ?>"><? echo chop($color_type_id_name,","); ?></td>
						<td><? echo $constructtion_arr[$row[csf('determination_id')]]; ?></td>
						<td><? echo $composition_arr[$row[csf('determination_id')]]; ?></td>
						<td align="center"><? echo $yarn_counts; ?></td>
						<td align="center"><? echo $row[csf('yarn_lot')]; ?></td>
						<td align="center"><? echo $brand_arr[$row[csf('brand_id')]]; ?></td>
						<td align="center"><? echo chop($stitch_length,","); ?></td>
						<td align="center"><? echo chop($machine_dia,","); ?></td>
						<td align="center"><? echo $row[csf('gsm_weight')]; ?></td>
						<td align="center"><? echo $row[csf('dia_width')]; ?></td>
						<td align="right" title="<? echo $row[csf('po_id')].'='.$row[csf('body_part_id')].'='.$row[csf('determination_id')]; ?>"><? echo chop($color_range_name,","); ?></td>
						<td align="right"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td align="right"><? echo $color_arr[$row[csf('batch_color')]]; ?></td>

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
					<td colspan="19" align="right"><strong>Total :</strong></td>
					<td align="right"><? echo number_format($totBookingQty, 2); ?></td>
					<td align="right"><? echo number_format($totCurrQty, 2); ?></td>
					<td align="right"><? echo number_format($balance_tot, 2); ?></td>
					<td align="right"><? echo number_format($totCurrRed_Qty, 2); ?></td>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="ttl_rcv_popup") // ttl_rcv_popup
{
	echo load_html_head_contents("Details Info", "../../", 1, 1,'','','');
	// echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$companyID= $companyID;
	$program_id=$program_id;
	$po_id=$po_id;
	$prod_id=$prod_id;
	$body_part_id=$body_part_id;
	$color_id=$color_id;

	$color_arr = return_library_array("select id, color_name from lib_color where id=$color_id and status_active=1 and is_deleted=0", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');

	$fso_info=sql_select("SELECT job_no, style_ref_no, customer_buyer FROM fabric_sales_order_mst WHERE ID=$po_id AND status_active=1 and is_deleted=0");

	?>

	<script>
		function print_window()
		{
			$('.fltrow').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="all"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$('.fltrow').show();
		}
		function exportToExcel()
		{
			$(".fltrow").hide();
			var tableData = document.getElementById("report_container").innerHTML;
			var data_type = 'data:application/vnd.ms-excel;base64,',
			template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
			base64 = function (s) {
				return window.btoa(unescape(encodeURIComponent(s)))
			},
			format = function (s, c) {
				return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
			}
			
			var ctx = {
				worksheet: 'Worksheet',
				table: tableData
			}
			
			document.getElementById('dlink').href = data_type + base64(format(template, ctx));
			document.getElementById('dlink').traget = "_blank";
			document.getElementById('dlink').click();
			$(".fltrow").show();
		}	
	</script>

	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div style="width:1490;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/><a href="##" id="dlink" onClick="exportToExcel()"><input  type="button" value="Excel Download"  style="width:110px"  class="formbutton"/></a>
        </div>
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="400" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="5"><b><? echo 'Buyer-'.$buyer_arr[$fso_info[0][csf('customer_buyer')]].', Style-'.$fso_info[0][csf('style_ref_no')].', Sales Order-'.$fso_info[0][csf('job_no')].', Color-'.$color_arr[$color_id]; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Barcode No</th>
                        <th width="100">Roll Weight</th>
                        <th width="100">Size</th>
                        <th width="">Qty In Pcs</th>
                    </tr>
				</thead>
            </table>
            <div style="width:420px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="400" cellpadding="0" cellspacing="0" id="table_body">
                    <?
                    $con = connect();
				    $r_id=execute_query("delete from tmp_barcode_no where userid=$user_name and entry_form=999");
				    oci_commit($con);

                    // Roll Receive
                    $sql_recv_roll = sql_select("SELECT c.barcode_no, d.job_no as fso_no, d.customer_buyer, c.qnty as roll_wight
					from  pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d, pro_roll_details a, ppl_planning_info_entry_dtls f, ppl_planning_info_entry_mst g
					where  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=58 and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.barcode_no=a.barcode_no and a.entry_form=2 and a.booking_no=cast(f.id as varchar(4000)) and f.mst_id=g.id and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 and c.booking_without_order=0 and f.id=$program_id and d.id=$po_id and b.prod_id=$prod_id and b.body_part_id=$body_part_id and b.color_id=$color_id");

					foreach ($sql_recv_roll as $val) 
					{
						$rcv_trans_in_data_arr[$val[csf("barcode_no")]]['roll_wight'] += $val[csf("roll_wight")];
						$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
					}
					// echo '<pre>';print_r($rcv_trans_in_data_arr);

					// Transfer in
					$roll_trans_in_sql = " SELECT c.barcode_no, e.job_no as fso_no, e.customer_buyer, c.qnty as roll_wight
					from inv_item_transfer_dtls a, product_details_master b, pro_roll_details c, FABRIC_SALES_ORDER_MST e, pro_roll_details f, pro_grey_prod_entry_dtls g, ppl_planning_info_entry_dtls h, ppl_planning_info_entry_mst i
					where a.to_prod_id=b.id and a.id=c.dtls_id and c.entry_form in (133) and c.po_breakdown_id=e.id and c.barcode_no=f.barcode_no and f.receive_basis=2 and f.dtls_id=g.id and f.booking_no=cast(h.id as varchar(4000)) and h.mst_id=i.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and h.id=$program_id and e.id=$po_id and a.to_prod_id=$prod_id and a.to_body_part=$body_part_id and g.color_id=$color_id";
					// echo $roll_trans_in_sql;die;
					$roll_trans_in_data=sql_select($roll_trans_in_sql);
					foreach($roll_trans_in_data as $val)
					{
						$rcv_trans_in_data_arr[$val[csf("barcode_no")]]['roll_wight'] += $val[csf("roll_wight")];
						$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
					}

					// Transfer out
					$roll_trans_out_sql="SELECT c.barcode_no, g.job_no as fso_no, g.customer_buyer, c.qnty 
					from inv_item_transfer_dtls a, order_wise_pro_details b, pro_roll_details c, pro_roll_details d, pro_grey_prod_entry_dtls e, FABRIC_SALES_ORDER_MST g, ppl_planning_info_entry_dtls i, ppl_planning_info_entry_mst j, product_details_master k 
					where a.id=b.dtls_id and b.entry_form in (133) and b.trans_type=6 and a.id=c.dtls_id and c.entry_form in (133) and c.barcode_no=d.barcode_no and d.entry_form in(2) and d.receive_basis=2 and d.dtls_id=e.id and b.po_breakdown_id=g.id and d.booking_no=cast(i.id as varchar(4000)) and i.mst_id=j.id and a.from_prod_id=k.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and i.id=$program_id and g.id=$po_id and a.from_prod_id=$prod_id and a.body_part_id=$body_part_id and e.color_id=$color_id";
					// echo $roll_trans_out_sql;die;
					$roll_trans_out_data=sql_select($roll_trans_out_sql);
					foreach($roll_trans_out_data as $val)
					{
						$transfer_out_data_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
					}

					if(!empty($barcode_no_arr))
					{
						foreach($barcode_no_arr as $barcodeno)
						{
							execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_name,$barcodeno,999)");
						}
						oci_commit($con);
					}

					$production_sql = sql_select("SELECT b.barcode_no, b.coller_cuff_size, b.qc_pass_qnty_pcs
				    from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
				    where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2) and b.entry_form in(2) and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid=$user_name and d.entry_form=999");
				    $yarn_prod_id_check=array();$prog_no_check=array();
				    foreach ($production_sql as $row)
				    {
				        $prodBarcodeData[$row[csf("barcode_no")]]["coller_cuff_size"] =$row[csf("coller_cuff_size")];
				        $prodBarcodeData[$row[csf('barcode_no')]]["qc_pass_qnty_pcs"]+=$row[csf('qc_pass_qnty_pcs')];
				    }

					// echo '<pre>';print_r($rcv_trans_in_data_arr);
					$i=1;
					foreach ($rcv_trans_in_data_arr as $barcode_no => $row) 
					{
						if ($transfer_out_data_arr[$barcode_no]=='') 
						{
							$size=$prodBarcodeData[$barcode_no]["coller_cuff_size"];
							$qty_in_pcs=$prodBarcodeData[$barcode_no]["qc_pass_qnty_pcs"];
		                    ?>
		                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
		                        <td width="30"><? echo $i; ?></td>
		                        <td width="100"><p><? echo $barcode_no; ?></p></td>
		                        <td width="100" align="right"><p><? echo $row['roll_wight']; ?></p></td>
		                        <td width="100"><p><? echo $size; ?></p></td>
		                        <td align="right"><? echo $qty_in_pcs; ?>&nbsp;</td>
		                    </tr>
		                    <?
		                    $total_roll_wight+=$row['roll_wight'];
		                    $total_qty_in_pcs+=$qty_in_pcs;
		                    $i++;
	                	}
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th width="30" align="right"></th>
                            <th width="100" align="right">Total</th>
                            <th width="100" align="right"><? echo number_format($total_roll_wight,2); ?></th>
                            <th width="100" align="right"></th>
                            <th align="right"><? echo number_format($total_qty_in_pcs,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<?
	$r_id=execute_query("delete from tmp_barcode_no where userid=$user_name and entry_form=999");
	oci_commit($con);
	exit();
}

if ($action == "collar_cuff_qnty_popup") 
{
	echo load_html_head_contents("Yarn Details Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
	<script>
		var greyQty = '<? echo str_replace(",", "", $txtGreyQty); ?>';

		function calculate_qty() {
			var tot_qty = '';

			$("#tbl_list_search").find('tbody tr').each(function () 
			{
				var txtSizeQnty = trim($(this).find('input[name="txtSizeQnty[]"]').val());
				tot_qty = tot_qty * 1 + txtSizeQnty * 1;
			});

			$('#txtTotGreyQty').val(tot_qty.toFixed(4));
		}

		function fnc_close() 
		{
			var save_data = '';
			var tot_qnty = '';

			$("#tbl_list_search").find('tbody tr').each(function () 
			{
				var txtSizeQnty = trim($(this).find('input[name="txtSizeQnty[]"]').val());
				var txtConsQty = trim($(this).find('input[name="txtConsQty[]"]').val());
				var txtUnitRate = $(this).find('input[name="txtUnitRate[]"]').val();

				if (txtSizeQnty * 1 > 0) 
				{
					if (save_data == "") 
					{
						save_data = txtSizeQnty + "_" + txtConsQty+ "_" + txtUnitRate;
					}
					else 
					{
						save_data += "|" + txtSizeQnty + "_" + txtConsQty+ "_" + txtUnitRate;
					}

					tot_qnty = tot_qnty * 1 + txtSizeQnty * 1;
				}
			});

			$('#hidden_save_string_data').val(save_data);
			$('#hidden_tot_qnty').val(tot_qnty);
			
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<form name="searchdescfrm" id="searchdescfrm">
		<fieldset style="width:480px;margin-left:5px">
			<input type="hidden" name="hidden_save_string_data" id="hidden_save_string_data" class="text_boxes" value="">
			<input type="hidden" name="hidden_tot_qnty" id="hidden_tot_qnty" class="text_boxes" value="">
			<div style="margin-top:5px; margin-left:5px">
				<div style="width:680px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
					<?
					$body_part_type = return_field_value("body_part_type", "lib_body_part", "id=$body_part_id and body_part_type in(40,50) and status_active=1 and is_deleted=0");
					// echo $body_part_type.'==';die;

					$color_library 	= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
					$size_library=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name");
					
					$fso_sql=sql_select("SELECT sales_booking_no, booking_id, po_job_no, booking_without_order FROM FABRIC_SALES_ORDER_MST WHERE id=$po_id and within_group=1 and status_active=1 and is_deleted=0");//sales_booking_no='$bookingNo' 
					$fso_booking=$fso_sql[0][csf('sales_booking_no')];
					// $fso_booking=$bookingNo;
					// echo $fso_booking.'==';
					$fso_po_job=$fso_sql[0][csf('po_job_no')];
					$booking_without_order=$fso_sql[0][csf('booking_without_order')];

					$nameArray=sql_select( "SELECT a.booking_no, a.po_break_down_id
						from wo_booking_mst a, wo_po_details_master b 
						where a.job_no=b.job_no and a.booking_no='$fso_booking'");		
					$po_id_all=$nameArray[0][csf('po_break_down_id')];

					if ($booking_without_order==0) 
					{
						$lab_dip_color_arr=array();
						$lab_dip_color_sql=sql_select("SELECT pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$fso_po_job'");
						foreach($lab_dip_color_sql as $row)
						{
							$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
						}
					}

					$collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

					// FOR CONTRAST_COLOR UNION ALL WITHOUT CONTRAST_COLOR
					$collar_cuff_sql="SELECT a.id, a.item_number_id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, 
					d.colar_cuff_per, e.body_part_full_name, e.body_part_type 
					FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e, 
					wo_pre_cos_fab_co_color_dtls f
					WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.id=f.PRE_COST_FABRIC_COST_DTLS_ID and b.COLOR_NUMBER_ID=f.GMTS_COLOR_ID and d.booking_no ='$fso_booking' and a.body_part_id=e.id and e.body_part_type=$body_part_type and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and f.CONTRAST_COLOR_ID=$color_id
					union all 
					SELECT a.id, a.item_number_id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, d.colar_cuff_per, e.body_part_full_name, e.body_part_type
					FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e
					WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$fso_booking'  and a.body_part_id=e.id and e.body_part_type=$body_part_type and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and b.color_number_id=$color_id";

					/*$collar_cuff_sql="SELECT a.id, a.item_number_id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, d.colar_cuff_per, e.body_part_full_name, e.body_part_type
					FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e
					WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$fso_booking'  and a.body_part_id=e.id and e.body_part_type=$body_part_type and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and b.color_number_id=$color_id
					order by  c.size_order";*///and a.body_part_id=$body_part_id
					// echo $collar_cuff_sql;die;// and b.color_number_id=$color_id

					$collar_cuff_sql_res=sql_select($collar_cuff_sql);
					
					$itemIdArr=array();
					foreach($collar_cuff_sql_res as $collar_cuff_row)
					{
						$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
						$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
						$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
						if(!empty($collar_cuff_row[csf('item_size')]))
						{
							$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
						}
						
						$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
						
						$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
					}
					unset($collar_cuff_sql_res);
					// echo '<pre>';print_r($collar_cuff_item_size_arr);
					
					$order_plan_qty_arr=array();
					$color_wise_wo_sql_qnty=sql_select( "SELECT item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown 
					where  po_break_down_id in ($po_id_all) and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id"); 
					foreach($color_wise_wo_sql_qnty as $row)
					{
						$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
						$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
					}
					unset($color_wise_wo_sql_qnty);

					foreach($collar_cuff_body_arr as $body_type=>$body_name)
					{
						$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
						foreach($body_name as $body_val)
						{
							$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
							$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

							?>
							<div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
							<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
								<tr>
									<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
								</tr>
								<tr>
									<td style="font-size:12px; width: 100px;"><strong><? echo $body_val; ?> Size</strong></td>
									<?
									foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
									{
										if(count($size_number)>0)
										{
											foreach($size_number  as $item_size=>$val)
											{
												?>
												<td align="center" title="<?=$size_number_id;?>" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
												<?
											}
										}
										else
										{
											?>
											<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
											<?
										}
									}
									?>
									<td width="60" align="center"><strong>Total</strong></td>
								</tr>
									<?
									$pre_size_total_arr=array();
									foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
									{
										foreach($pre_cost_data as $color_number_id=>$color_number_data)
										{
											foreach($color_number_data as $color_size_sensitive=>$color_break_down)
											{
												$pre_color_total_collar=0;
												$pre_color_total_collar_order_qnty=0;
												$process_loss_method=$process_loss_method;
												$constrast_color_arr=array();
												if($color_size_sensitive==3)
												{
													$constrast_color=explode('__',$color_break_down);
													for($i=0;$i<count($constrast_color);$i++)
													{
														$constrast_color2=explode('_',$constrast_color[$i]);
														//echo $constrast_color2[0].'='.$constrast_color2[2].'<br>';
														$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
													}
												}
												?>
												<!-- Size wise qnty below -->
												<tr>
													<td title="<?=$color_number_id;?>"><strong>Booking Qty</strong></td>
													<?
													foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
													{
														?>
														<td align="center" style="border:1px solid black">
															<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
															$plan_cut=0;
															foreach($gmtsItemId as $giid)
															{
																if($body_type==50) $plan_cut+=($order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan']); // ISsue id- 7185
																else $plan_cut+=$order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'];
															}

															$collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];


															if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
															else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
															$colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
															$collerqty=($plan_cut+$colar_excess_per);
															echo number_format($collerqty);
															$pre_size_total_arr[$size_number_id]+=$collerqty;
															$pre_color_total_collar+=$collerqty;
															$pre_color_total_collar_order_qnty+=$plan_cut;
															?>
														</td>

														<?
													}
													?>
													<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
												</tr>
												<!-- input qnty below -->
												<tr>
													<td title="<?=$color_number_id;?>"><strong>
														<?
														if( $color_size_sensitive==3)
														{
															echo strtoupper ($constrast_color_arr[$color_number_id]);
															$lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
														}
														else
														{
															echo $color_library[$color_number_id];
															$lab_dip_color_id=$color_number_id;
														}
														?></strong>
													</td>
													<?
													$j=1;
													foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
													{
														if (str_replace("'", '', $save_data) != "") 
														{
															$i = 1;
															$save_datas = explode("|", str_replace("'", '', $save_data));
															foreach ($save_datas as $value) 
															{
																$yarn_val = explode('_', $value);
																$txtSizeQnty = $yarn_val[0];

																$tot_grey_qty += $txtSizeQnty;
																?>
																<td width="80">
																	<input type="text" name="txtSizeQnty[]" id="txtSizeQnty_<?=$i;?>" class="text_boxes_numeric" style="width:65px""/>
																</td>
																<?
															}
														}
														else
														{
															?>
															<td width="80">
																<input type="text" name="txtSizeQnty[]" id="txtSizeQnty_<?=$j;?>" class="text_boxes_numeric" style="width:65px""/>
															</td>
															<?
														}
														$j++;
													}
													?>
													<th><input type="text" name="txtTotGreyQty" id="txtTotGreyQty" value="<? echo number_format($tot_grey_qty, 4, '.', ''); ?>" class="text_boxes_numeric" style="width:65px" readonly/></th>
												</tr>
												<?
											}
										}
									}
									?>
							</table>
							</div>
							<br/>
							<?
						}
					}
					?>
				</div>
			</div>
			<table width="650">
				<tr>
					<td align="center">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close"
						onClick="fnc_close();" style="width:100px"/>
					</td>
				</tr>
			</table>
			<br>
		</fieldset>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
?>

<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]);
			$("#hide_booking_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:840px;">
					<table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="130">Please Enter Job No</th>
							<th width="130">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
								</td>
								<td align="center">
									<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
								</td>
								<td align="center">
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('cbo_year_selection').value, 'create_booking_no_search_list_view', 'search_div', 'date_wise_grey_fabric_delivery_to_store_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?
exit();
}//bookingnumbershow;

if($action == "create_booking_no_search_list_view")
{
	//echo 111; die();
	$data=explode('**',$data);
	//echo '<pre>';print_r($data);
	$txt_date_from = $data[3];
	$txt_date_to = $data[4];
 	$year = $data[6];
	if ($data[0]!=0) $company="  a.company_id in($data[0])";
	//	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if ($data[5]!="") $job_no_cond =" and a.job_no like '%$data[5]'"; else $job_no_cond='';
	if ($data[5]!="") $job_no_prefix_num =" and c.job_no_prefix_num = '$data[5]'"; else $job_no_prefix_num='';

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.booking_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if($year !=0)
	{
		if($db_type==0) { $booking_date=" and YEAR(a.booking_date)=$year";   }
		if($db_type==2) {$booking_date=" and to_char(a.booking_date,'YYYY')=$year";}
	}
	// JOB_NO_PREFIX_NUM
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

	//pro_batch_create_mst//wo_non_ord_samp_booking_mst
 	$sql= "SELECT a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved
 	from wo_booking_mst a, wo_booking_dtls d, wo_po_break_down b, wo_po_details_master c
 	where a.booking_no = d.booking_no and d.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.job_no=c.job_no and $company $buyer $job_no_prefix_num $booking_no $sql_cond $booking_date and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0
 	group by a.id,a.booking_no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved
	union all
	select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved
	from wo_non_ord_samp_booking_mst a
	where $company $buyer $booking_no $sql_cond $booking_date $job_no_cond and a.booking_type=4 order by id Desc";
	//echo $sql;

	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit();
}

if ($action == "load_drop_down_knitting_com")
{
	extract($_REQUEST);

	if ($knitting_source == 1) {
		echo create_drop_down("cbo_working_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "load_drop_down( 'requires/date_wise_grey_fabric_delivery_to_store_report_controller', this.value, 'load_drop_down_floor','floor_td');", "");
	} else if ($knitting_source == 3) {
		echo create_drop_down("cbo_working_company_id", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "");
	} else {
		echo create_drop_down("cbo_working_company_id", 152, $blank_array, "", 1, "--Select Company--", 0, "");
	}
	exit();
}

if ($action == "floor_popup")
{
	echo load_html_head_contents("Floor Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
		<script>
			var selected_id = new Array();
			var selected_name = new Array();

			function check_all_data() {
				var tbl_row_count = document.getElementById('table_body').rows.length;

				tbl_row_count = tbl_row_count - 1;
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

			function js_set_value(str) {

				toggle(document.getElementById('search' + str), '#FFFFCC');

				if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
					selected_id.push($('#txt_individual_id' + str).val());
					selected_name.push($('#txt_individual' + str).val());

				} else {
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

				$('#hidden_floor_id').val(id);
				$('#hidden_floor_name').val(name);
			}
		</script>
		</head>
		<fieldset style="width:390px">
			<input type="hidden" name="hidden_floor_name" id="hidden_floor_name" value="">
			<input type="hidden" name="hidden_floor_id" id="hidden_floor_id" value="">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="">Floor Name</th>
					</tr>
				</thead>
			</table>
			<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$sql_floor ="SELECT a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id in($workingCompanyID) and b.status_active=1 and b.is_deleted=0 and a.production_process=2 group by a.id, a.floor_name order by a.floor_name";

					$i = 1;
					//echo $sql_floor;
					$sql_floor_result = sql_select($sql_floor);

					foreach ($sql_floor_result as $row)
					{
						//var_dump($row);
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="50">
									<? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>" />
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('floor_name')]; ?>" />
								</td>
								<td width="">
									<p><? echo $row[csf('floor_name')]; ?></p>
								</td>
							</tr>
						<?
							$i++;
					}
					?>

				</table>
			</div>
			<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('table_body', -1);
		</script>
	<?
}

/* if ($action == "load_drop_down_floor")
{
	$data = explode("_", $data);
	$company_id = $data[0];
	//$location_id = $data[1];
	//if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and b.location_id=$location_id";

	echo create_drop_down("cbo_floor_id", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");//load_drop_down( 'requires/grey_production_entry_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );
	exit();
} */

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
//--------------------------------------------------------------------------------------------------------------------



/*$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
$floor_details=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
$reqsn_details=return_library_array( "select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id,requisition_no", "knit_id", "requisition_no"  );
$color_details=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );*/
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");



$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
$brand_details_arr = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
$address_arr = return_library_array("select id, address from lib_location", "id", "address");
$buyer_brand_details_arr= return_library_array("select id, brand_name from lib_buyer_brand", "id", "brand_name");


$company_array = array();
$company_data = sql_select("select id, company_name, company_short_name,group_id,vat_number from lib_company");
$group_com_arr_lib = return_library_array("select id,group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

$floor_name_arr_lib = return_library_array("select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1 order by floor_name", 'id', 'floor_name');

//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
foreach ($company_data as $row)
{
	$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
	$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
	$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
}
$composition_arr = array();
$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
$data_array = sql_select($sql_deter);
foreach ($data_array as $row)
{
	if (array_key_exists($row[csf('id')], $composition_arr)) {
		$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	} else {
		$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	}
}

if($action=="report_generate") // Show
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_type=str_replace("'","",$report_type);
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_floor_id=str_replace("'","",$txt_floor_id);
	$txt_programme_no=str_replace("'","",$txt_programme_no);
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);

	if($cbo_knitting_source==0) $cbo_knitting_source_cond=""; else $cbo_knitting_source_cond=" and a.knitting_source=$cbo_knitting_source";
	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id in($cbo_company)";
	if($txt_floor_id==0) $floor_cond=""; else $floor_cond=" and b.floor_id in($txt_floor_id)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.knitting_company in($cbo_working_company)";
	if ($txt_booking_no=="") $booking_cond_a=""; else $booking_cond_a=" and a.booking_no like '%$txt_booking_no%' ";
	if ($txt_booking_no=="") $booking_cond_b=""; else $booking_cond_b=" and b.booking_no like '%$txt_booking_no%' ";
	if ($txt_booking_no=="") $booking_cond_c=""; else $booking_cond_c=" and c.booking_no like '%$txt_booking_no%' ";
	if ($txt_booking_no=="") $booking_cond_sales=""; else $booking_cond_sales=" and sales_booking_no like '%$txt_booking_no%' ";
	if ($txt_programme_no=="") $programme_no_cond=""; else $programme_no_cond=" and c.booking_no like '%$txt_programme_no%' ";

	$from_date=$txt_date_from;
	if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;
	$date_con="";
	if($from_date!="" && $to_date!="") $date_con=" and a.delevery_date between '$from_date' and '$to_date'";

	if($from_date!="" && $to_date!="") $date_con_production=" and b.receive_date between '$from_date' and '$to_date'";


	//if($cbo_company==0) $cbo_company_cond_grey=""; else $cbo_company_cond_grey=" and e.company_id=$cbo_company";

	$feeder_arr = array(1 => "Full Feeder", 2 => "Half Feeder");

	if($report_type==1)
	{
		ob_start();

		/* echo	$sqls = "select a.sys_number as challan_no,a.sys_number_prefix_num as challan_prefix,a.insert_date as challan_date,a.company_id,a.knitting_company,a.knitting_source,
		sum(b.current_delivery) as challan_delivery,
		sum(c.grey_receive_qnty) as prod_qty, c.febric_description_id, c.gsm, c.width,c.yarn_count,c.yarn_lot,c.color_id,c.color_range_id,c.stitch_length,c.brand_id,c.machine_dia,c.machine_gg,
		d.receive_basis,d.booking_no,
		e.booking_no as bwo,e.booking_without_order,e.is_sales,count(e.barcode_no) as num_of_roll
		from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_grey_prod_entry_dtls c,inv_receive_master d,pro_roll_details e
		where  a.id=b.mst_id and
		b.grey_sys_id=c.mst_id and
		b.grey_sys_id=d.id and c.mst_id=d.id and d.entry_form=2 and
		b.roll_id=e.id and b.grey_sys_id=e.mst_id and c.mst_id=e.mst_id and d.id=e.mst_id and e.entry_form=2
		and a.entry_form=56 and b.entry_form=56 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 $cbo_company_cond $cbo_knitting_source_cond $company_working_cond $date_con
		group by a.sys_number,a.sys_number_prefix_num,a.insert_date,a.company_id,a.knitting_company,a.knitting_source
		,c.febric_description_id,c.gsm,c.width,c.yarn_count,c.yarn_lot,c.color_id,c.color_range_id,c.stitch_length,c.brand_id,c.machine_dia,c.machine_gg,
		d.receive_basis,d.booking_no,e.booking_no,e.booking_without_order,e.is_sales";
		*/

		$buyer_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$company_name_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');



		if($txt_booking_no !="")
		{
			$sql_booking="SELECT a.booking_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.booking_no_prefix_num=$txt_booking_no and a.status_active=1 and b.status_active=1 and a.booking_type in(1,4) and to_char(a.insert_date,'YYYY')=$cbo_year_selection AND a.company_id in($cbo_company) group by a.booking_no
			union all
			select booking_no from wo_non_ord_samp_booking_mst
			where status_active=1 and booking_no_prefix_num=$txt_booking_no and booking_type in(4) and to_char(insert_date,'YYYY')=$cbo_year_selection AND company_id in($cbo_company)";
			//echo $sql_booking;die;
			$booking_data_arr = sql_select($sql_booking);
			$bookingNo_arr=array();$booking_cond="";
			foreach ($booking_data_arr as $key => $value)
			{
				$bookingNo_arr[$value[csf('booking_no')]] = $value[csf('booking_no')];
			}
			$booking_cond="'".implode("','",$bookingNo_arr)."'";
			// echo $booking_cond;die;

			$sql_book_roll="SELECT a.id as roll_id from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.status_active=1 and a.entry_form=2 and a.receive_basis=1 and b.entry_form=2 and b.receive_basis=1 and b.booking_no in($booking_cond)
			union all
			select a.id as roll_id from pro_roll_details a, inv_receive_master b, ppl_planning_info_entry_dtls c, ppl_planning_info_entry_mst d
			where a.mst_id=b.id and b.booking_id=c.id and c.mst_id=d.id and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.receive_basis=2 and d.booking_no in($booking_cond)";
			// echo $sql_book_roll;die;
			$sql_book_roll_res=sql_select($sql_book_roll);
			if (empty($sql_book_roll_res))
			{
				echo "<h3 style='color:red; text-align:center;'>Data Not Found</h3>";die;
			}
			$booking_barcode_cond=""; $summary_barcode_cond="";
			foreach($sql_book_roll_res as $row)
			{
				$booking_roll_id[$row[csf("roll_id")]]=$row[csf("roll_id")];
			}
			if(count($booking_roll_id)>0)
			{
				$booking_roll_ids_chunk=array_chunk($booking_roll_id,999);
				$booking_barcode_cond=" and"; $summary_barcode_cond=" and";
				foreach($booking_roll_ids_chunk as $booking_roll_ids)
				{
				//if($rollIDs_cond==" and")  $rollIDs_cond.="(b.mst_id in(".implode(',',$rollIDss).")"; else $rollIDs_cond.=" or b.mst_id in(".implode(',',$rollIDss).")";
				if($booking_barcode_cond==" and")  $booking_barcode_cond.="(b.roll_id in(".implode(',',$booking_roll_ids).")"; else $booking_barcode_cond.=" or b.roll_id in(".implode(',',$booking_roll_ids).")";
				if($summary_barcode_cond==" and")  $summary_barcode_cond.="(c.id in(".implode(',',$booking_roll_ids).")"; else $summary_barcode_cond.=" or c.id in(".implode(',',$booking_roll_ids).")";
				}
				$booking_barcode_cond.=")";$summary_barcode_cond.=")";
				//$booking_barcode_cond=" and b.roll_id in(".implode(',',$booking_roll_id).")";
			}
			unset($sql_book_roll_res);
			//echo $booking_barcode_cond;die;
		}
		/*$delivery_res_sql = "SELECT a.sys_number as challan_no, a.sys_number_prefix_num as challan_prefix, a.delevery_date as challan_date, a.company_id, a.knitting_company, a.knitting_source, b.roll_id, sum(b.current_delivery) as current_delivery, b.grey_sys_id
		from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
		where a.entry_form=56 and b.entry_form=56  and b.mst_id=a.id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  $cbo_company_cond $cbo_knitting_source_cond $company_working_cond $date_con $booking_barcode_cond
		group by a.sys_number,a.sys_number_prefix_num,a.delevery_date,a.company_id,a.knitting_company,a.knitting_source,b.roll_id,b.grey_sys_id order by a.delevery_date";*/

		$delivery_res_sql = "SELECT a.id,a.sys_number as challan_no, a.sys_number_prefix_num as challan_prefix, a.delevery_date as challan_date, a.company_id,a.knitting_company, a.knitting_source, b.roll_id, sum( c.qnty ) as current_delivery, b.grey_sys_id,a.floor_ids,a.location_id,a.attention,a.remarks
		from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,pro_roll_details c
		where a.entry_form=56 and b.entry_form=56   and c.entry_form=56 and b.mst_id=a.id and b.id=c.dtls_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0  $cbo_company_cond $cbo_knitting_source_cond $company_working_cond $date_con $booking_barcode_cond
		group by a.id,a.sys_number,a.sys_number_prefix_num,a.delevery_date,a.company_id,a.knitting_company,a.knitting_source,b.roll_id,b.grey_sys_id,a.floor_ids,a.location_id,a.attention,a.remarks order by a.delevery_date";

		//echo $delivery_res_sql;
		$delivery_res = sql_select($delivery_res_sql);

		//$qntyFromRoll=array();
		$dataFromRoll=array();
		$roll_ids="";
		foreach ($delivery_res as $val)
		{
			$roll_ids .= $val[csf("roll_id")].",";
			//$roll_ids .= $val[csf("grey_sys_id")].",";

			/*$dataFromRoll[$val[csf("grey_sys_id")]]["current_delivery"]= $val[csf("current_delivery")];
			$dataFromRoll[$val[csf("grey_sys_id")]]["challan_no"] = $val[csf("challan_no")];
			$dataFromRoll[$val[csf("grey_sys_id")]]["challan_prefix"] = $val[csf("challan_prefix")];
			$dataFromRoll[$val[csf("grey_sys_id")]]["challan_date"] = $val[csf("challan_date")];*/
			$dataFromRoll[$val[csf("roll_id")]]["current_delivery"]= $val[csf("current_delivery")];
			$dataFromRoll[$val[csf("roll_id")]]["challan_no"] = $val[csf("challan_no")];
			$dataFromRoll[$val[csf("roll_id")]]["challan_prefix"] = $val[csf("challan_prefix")];
			$dataFromRoll[$val[csf("roll_id")]]["challan_date"] = $val[csf("challan_date")];
			$dataFromRoll[$val[csf("roll_id")]]["company_id"] = $val[csf("company_id")];
			$dataFromRoll[$val[csf("roll_id")]]["mst_id"] = $val[csf("id")];
			$dataFromRoll[$val[csf("roll_id")]]["knitting_source"] = $val[csf("knitting_source")];
			$dataFromRoll[$val[csf("roll_id")]]["floor_ids"] = $val[csf("floor_ids")];
			$dataFromRoll[$val[csf("roll_id")]]["location_id"] = $val[csf("location_id")];
			$dataFromRoll[$val[csf("roll_id")]]["knitting_company"] = $val[csf("knitting_company")];
			$dataFromRoll[$val[csf("roll_id")]]["remarks"] = $val[csf("remarks")];
			$dataFromRoll[$val[csf("roll_id")]]["attention"] = $val[csf("attention")];
		}
		$roll_idss = chop($roll_ids,",");

		//var_dump($dataFromRoll['OG-GDSR-18-00008']["current_delivery"][$val[csf("roll_id")]]);die;
		$rollID=explode(",",$roll_idss);

		$challan_data_arr=array(); $po_id_array = $sales_id_array = $booking_program_arr = array();  $feedar_prog_id=""; $prod_qty=0;
		$mothher_chunk_arr=array_chunk($rollID, 7000);
		foreach ($mothher_chunk_arr as $child_chunked_arr)
		{
			$rollIDs=array_chunk($child_chunked_arr,999);
			$rollIDs_cond=" and";
			foreach($rollIDs as $rollIDss)
			{
			//if($rollIDs_cond==" and")  $rollIDs_cond.="(b.mst_id in(".implode(',',$rollIDss).")"; else $rollIDs_cond.=" or b.mst_id in(".implode(',',$rollIDss).")";
			if($rollIDs_cond==" and")  $rollIDs_cond.="(c.id in(".implode(',',$rollIDss).")"; else $rollIDs_cond.=" or c.id in(".implode(',',$rollIDss).")";
			}
			$rollIDs_cond.=")";
			//echo $rollIDs_cond;die;

			/*echo  $sql = "select a.challan_no,a.challan_date,a.company_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,a.buyer_id,a.within_group,
			b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id,
			b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg,b.mst_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as challan_delivery,count(c.barcode_no) as num_of_roll,c.id,c.po_breakdown_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and c.entry_form=2 and a.status_active = 1 and a.is_deleted=0 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $rollIDs_cond
			group by  a.challan_no,a.challan_date,a.company_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,a.buyer_id,a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg,b.mst_id,c.booking_no, c.booking_without_order,c.is_sales,c.id,c.po_breakdown_id
			order by a.booking_no";*/

			$sql = "  SELECT  a.challan_no,a.challan_date,a.company_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,a.buyer_id,a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id,b.machine_dia,b.machine_gg, c.booking_no as bwo, c.booking_without_order, c.is_sales, sum(c.qnty) as current_delivery, count(c.barcode_no) as num_of_roll, c.id, c.po_breakdown_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1  and  b.is_deleted = 0 $rollIDs_cond $floor_cond $programme_no_cond
			group by  a.challan_no,a.challan_date,a.company_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,a.buyer_id,a.within_group, b.febric_description_id, b.febric_description_id, b.gsm, b.width, b.yarn_count,  b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, c.id, c.po_breakdown_id
			order by c.id  ";
			//echo $sql;

			/* $sql = "select  a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,
			a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id,
			b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1
			and b.is_deleted = 0 and c.id in ($roll_ids)
			group by  a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg, c.booking_no, c.booking_without_order,c.is_sales
			order by a.booking_no";*/

			//a.sys_number='MFG-GDSR-18-00007' and
			//,sum(e.qnty) as current_delivery

			$sql_qry=sql_select($sql);
			foreach($sql_qry  as $row)
			{
				$prod_qty+=$dataFromRoll[$row[csf("id")]]["current_delivery"];
				/*$challan_no=$dataFromRoll[$row[csf("roll_id")]]["challan_no"];
				$challanDate=date("d-m-Y",strtotime($dataFromRoll[$row[csf("roll_id")]]["challan_date"]));

				$challan_data_arr[$dataFromRoll[$row[csf("roll_id")]]["challan_no"]][]=array(
				challan_no=>$challan_no,
				challan_date=>$challanDate,
				buyer=>$row[csf('receive_basis')],
				job_year=>$row[csf('receive_basis')],
				job=>$row[csf('booking_id')],
				ref_no=>$row[csf('receive_basis')],
				prog_no=>$row[csf('receive_basis')],
				booking_no=>$row[csf('receive_basis')],
				prod_basis=>$row[csf('booking_id')],
				knitting_com=>$row[csf('receive_basis')],
				yarn_count=>$row[csf('receive_basis')],
				yarn_brand=>$row[csf('receive_basis')],
				lot_no=>$row[csf('receive_basis')],
				fab_color=>$row[csf('booking_id')],
				color_range=>$row[csf('receive_basis')],
				feeder=>$row[csf('receive_basis')],
				fab_type=>$row[csf('booking_id')],
				stich=>$row[csf('receive_basis')],
				fin_gsm=>$row[csf('receive_basis')],
				fab_dia=>$row[csf('receive_basis')],
				mc_dia=>$row[csf('booking_id')],
				mc_gauge=>$row[csf('receive_basis')],
				no_of_roll=>$row[csf('receive_basis')],
				qc_pass_qty=>$row[csf('receive_basis')]
				);*/

				$key=$dataFromRoll[$row[csf("id")]]["challan_no"].$dataFromRoll[$row[csf("id")]]["challan_date"].$row[csf("booking_no")].$row[csf("brand_id")];


				$challan_data_arr[$dataFromRoll[$row[csf("id")]]["challan_no"]][$key]=$row;
				$dataarr[$key]['current_delivery']+=$dataFromRoll[$row[csf("id")]]["current_delivery"];
				$dataarr[$key]['rollNumb']+=1;

				if($row[csf("is_sales")] == 1){
				$sales_id_array[] = $row[csf("po_breakdown_id")];
				}else{
				$po_id_array[] = $row[csf("po_breakdown_id")];
				}
				if ($row[csf('receive_basis')] == 2) {
				$booking_program_arr[] = $row[csf("booking_no")];
				}else{
				$booking_no = explode("-", $row[csf('booking_no')]);
				$booking_program_arr[] = (int)$booking_no[3];
				}

				$feedar_prog_id .= $row[csf("bwo")].",";

				if($row[csf('receive_basis')] == 1)
				{
					$booking_no_arr[$row[csf('booking_no')]] = "'".$row[csf('booking_no')]."'"; // booking
				}
				else if($row[csf('receive_basis')] == 2)
				{
					$prog_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')]; //program
				}
				else if($row[csf('receive_basis')] == 4)
				{
					$sales_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')]; //sales id
				}
			}
		}

		$feedar_prog_ids = chop($feedar_prog_id,",");

		$booking_no_arr = array_filter($booking_no_arr);
		if(!empty($booking_no_arr))
		{
			$booking_no_arr = array_filter($booking_no_arr);
			if($db_type==2 && count($booking_no_arr)>999)
			{
				$booking_no_arr_chunk=array_chunk($booking_no_arr,999) ;
				foreach($booking_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookCond.="  x.booking_no in($chunk_arr_value) or ";
				}

				$all_book_no_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_book_no_cond=" and x.booking_no in(".implode(",",$booking_no_arr).")";
			}

		}

		$prog_no_arr = array_filter($prog_no_arr);
		if(!empty($prog_no_arr))
		{
			$prog_no_arr = array_filter($prog_no_arr);
			if($db_type==2 && count($prog_no_arr)>999)
			{
				$prog_no_arr_chunk=array_chunk($prog_no_arr,999) ;
				foreach($prog_no_arr_chunk as $chunk_arrs)
				{
					$chunk_arr_values=implode(",",$chunk_arrs);
					$progCond.="  c.dtls_id in($chunk_arr_values) or ";
				}

				$all_porg_no_cond.=" and (".chop($progCond,'or ').")";
			}
			else
			{
				$all_porg_no_cond=" and c.dtls_id in(".implode(",",$prog_no_arr).")";
			}

		}
		$sales_no_arr = array_filter($sales_no_arr);
		if(!empty($sales_no_arr))
		{
			$sales_no_arr = array_filter($sales_no_arr);
			if($db_type==2 && count($sales_no_arr)>999)
			{
				$sale_no_arr_chunk=array_chunk($sales_no_arr,999) ;
				foreach($sale_no_arr_chunk as $chunk_arrss)
				{
					$chunk_arr_valuess=implode(",",$chunk_arrss);
					$salesCond.="  c.booking_id in($chunk_arr_valuess) or ";
				}

				$all_sale_no_cond.=" and (".chop($salesCond,'or ').")";
			}
			else
			{
				$all_sale_no_cond=" and c.booking_id in(".implode(",",$sales_no_arr).")";
			}

		}

		if($all_book_no_cond=="")
		{
			$all_book_no_cond="and x.booking_no in('0')";

		}
		if($all_porg_no_cond=="")
		{
			$all_porg_no_cond="and c.id in(0)";

		}
		if($all_sale_no_cond=="")
		{
			$all_sale_no_cond="and c.booking_id in(0)";

		}

		$bookingInternalRefSql=sql_select("select a.buyer_name, b.id as po_id,x.fabric_color_id,b.grouping,x.booking_no, a.brand_id
		from wo_booking_dtls x,wo_po_details_master a, wo_po_break_down b
		where x.job_no=a.job_no and x.po_break_down_id=b.id and a.id=b.job_id and a.company_name in($cbo_company) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_book_no_cond
		group by a.buyer_name, b.id,x.fabric_color_id,b.grouping,x.booking_no, a.brand_id

		union all

		select a.buyer_name, b.id as po_id,x.fabric_color_id,b.grouping,x.booking_no, a.brand_id
		from wo_booking_dtls x,wo_po_details_master a, wo_po_break_down b ,ppl_planning_entry_plan_dtls c
		where x.job_no=a.job_no and x.po_break_down_id=b.id and a.id=b.job_id and x.booking_no=c.booking_no and a.company_name in($cbo_company) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $all_porg_no_cond
		group by a.buyer_name, b.id,x.fabric_color_id,b.grouping,x.booking_no, a.brand_id

		union all

		select a.buyer_name, b.id as po_id,x.fabric_color_id,b.grouping,x.booking_no, a.brand_id
		from wo_booking_dtls x,wo_po_details_master a, wo_po_break_down b ,fabric_sales_order_mst c
		where x.job_no=a.job_no and x.po_break_down_id=b.id and a.id=b.job_id and x.booking_no=c.sales_booking_no and a.company_name in($cbo_company) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and c.status_active=1 and c.is_deleted=0 $all_sale_no_cond
		group by a.buyer_name, b.id,x.fabric_color_id,b.grouping,x.booking_no, a.brand_id");
		$internalRefArrChk=array();
		$brand_arr=array();
		foreach($bookingInternalRefSql  as $rows)
		{
			if($internalRefArrChk[$rows[csf("booking_no")]]['grouping']!=$rows[csf("grouping")])
			{
				$internalRefArr[$rows[csf("booking_no")]]['grouping'].=$rows[csf("grouping")].",";
				$internalRefArrChk[$rows[csf("booking_no")]]['grouping']=$rows[csf("grouping")];
			}

			if($brand_arr[$rows[csf("booking_no")]]['brand_id']!=$rows[csf("brand_id")])
			{
				$brand_arr[$rows[csf("booking_no")]]['brand_id']=$rows[csf("brand_id")];
			}



		}


		/*$prod_qnty_sql= sql_select("select sum(a.grey_receive_qnty) as prod_qnty,b.knitting_source from pro_grey_prod_entry_dtls a,inv_receive_master b where  a.mst_id=b.id and b.entry_form=2 and b.status_active=1 and b.is_deleted=0 and  a.status_active=1 and a.is_deleted=0  $date_con_production group by b.knitting_source");*/
		$company_cond='';
		$working_company_cond='';
		if($cbo_company==0) $company_cond=""; else $company_cond=" and  b.company_id in($cbo_company)";

		if($cbo_working_company==0) $working_company_cond=""; else $working_company_cond=" and b.knitting_company in($cbo_working_company)";

		$prod_qnty_sql= sql_select("SELECT sum(c.qnty) as prod_qnty, sum(c.reject_qnty) as reject_qnty,b.knitting_source
		from pro_grey_prod_entry_dtls a,inv_receive_master b, pro_roll_details c
		where a.mst_id=b.id and b.id=c.mst_id and a.id=c.dtls_id and b.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=2 and a.status_active=1 and a.is_deleted=0 $company_cond $working_company_cond $date_con_production $summary_barcode_cond $programme_no_cond group by b.knitting_source");

		//print_r($challan_data_arr);
		/*	$sql_rollNoAndQnty=sql_select("select a.receive_basis, a.booking_no, a.knitting_source, b.color_id, c.booking_no as bwo ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $cbo_company_cond $cbo_knitting_source_cond $company_working_cond  group by a.receive_basis, a.booking_no,a.knitting_source, b.color_id,c.booking_no");

		$rollNoAndQnty_arr=array();
		foreach($sql_rollNoAndQnty  as $row)
		{
			$rollNoAndQnty_arr[$row[csf("booking_no")]][$row[csf("receive_basis")]][$row[csf("knitting_source")]][$row[csf("color_id")]][$row[csf("bwo")]]["num_of_roll"] = $row[csf("num_of_roll")];
			$rollNoAndQnty_arr[$row[csf("booking_no")]][$row[csf("receive_basis")]][$row[csf("knitting_source")]][$row[csf("color_id")]][$row[csf("bwo")]]["current_delivery"] = $row[csf("current_delivery")];
		}*/
		?>

		<fieldset style="width:2520px">
		<table cellpadding="0" cellspacing="0" width="2500">
			<tr class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? //echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr>
		</table>

		<fieldset style="width:400px">
		<table width="400" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="font-weight:bold;">
			<?
			//$prod_qty=0;
			$inhouse_qty=0;
			$outbound_inhouse_qty=0;
			$reject_qnty=0;
			foreach($prod_qnty_sql  as $row)
			{
				if($row[csf("knitting_source")]==1)
				{
					$inhouse_qty+=$row[csf("prod_qnty")];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$outbound_inhouse_qty+=$row[csf("prod_qnty")];
				}
				//$prod_qty+=$row[csf("prod_qty")];
				$reject_qnty+=$row[csf("reject_qnty")];
			}

			/*foreach($sql_qry  as $row)
			{

				$prod_qty+=$dataFromRoll[$row[csf("id")]]["current_delivery"];
				///$prod_qty+=$row[csf("challan_delivery")];
				//$prod_qty+=$qntyFromRoll[$row[csf("mst_id")]]["current_delivery"];
				//$prod_qty+=$dataFromRoll[$row[csf("roll_id")]]["current_delivery"];
			}*/
			$totalProduction=$inhouse_qty+$outbound_inhouse_qty;
			//$total_prod_qty=$prod_qty-$totalProduction;
			$total_prod_qty=$totalProduction-$prod_qty;
			?>
			<thead>
				<tr>
					<th colspan="2">Summery (Self Order)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="300">Production (Inhouse)</td>
					<td align="right"><? echo number_format($inhouse_qty,2); ?></td>
				</tr>
				<tr>
					<td width="300">Production (Outbound Subcontract)</td>
					<td align="right"><? echo number_format($outbound_inhouse_qty,2); ?></td>
				</tr>
				<tr>
					<td width="300">Reject Qty</td>
					<td align="right"><? echo number_format($reject_qnty,2); ?></td>
				</tr>
				<tr>
					<td width="300">Total Production (Include Reject Qty)</td>
					<td align="right"><? echo number_format($totalProduction+$reject_qnty,2); ?></td>
				</tr>
				<tr>
					<td width="300" align="right">Total Production:</td>
					<td align="right"><? echo number_format($totalProduction,2); ?> </td>
				</tr>
				<tr>
					<td width="300">Grey Fab Delivery To Store</td>
					<td align="right"><? echo number_format($prod_qty,2); ?></td>
				</tr>
				<tr>
					<td width="300" align="right">Delivery Balace :</td>
					<td align="right"><? echo number_format($total_prod_qty,2); ?></td>
				</tr>
			</tbody>
		</table>
			<!-- Summary End -->
		</fieldset>
		<table width="2485" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="130">Challan No</th>
					<th width="80">Challan Date</th>
					<th width="100">Buyer</th>
					<th width="100">Cust. Buyer</th>
					<th width="100">Brand</th>
					<th width="60">Job Year</th>
					<th width="100">Job</th>
					<th width="100">IR/IB</th>
					<th width="90">Ref No</th>
					<th width="65">Prog No</th>
					<th width="100">Book. No</th>
					<th width="90">Production Basis</th>
					<th width="90">Knitting Company</th>
					<th width="110">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="130">Fab Color</th>
					<th width="100">Color Range</th>
					<th width="100">Feeder</th>
					<th width="220">Fabric Type</th>
					<th width="60">Stich</th>
					<th width="60">Fin GSM</th>
					<th width="60">Fab. Dia</th>
					<th width="60">MC. Dia</th>
					<th width="60">MC. Gauge</th>
					<th width="60">No Of Roll</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
		</table>
			<div style="width:2505px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="2485" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
					//echo "<pre>";print_r($challan_data_arr);//die;
					$booking_program="";
					foreach($booking_program_arr as $row)
					{
						$booking_program.=$row.',';
					}

					$bookingProg=rtrim($booking_program,",");
					$bookingProgID=explode(",",$bookingProg);

					$bookingProgIDs=array_chunk($bookingProgID,999);
					$bookingProgID_cond=" and";
					foreach($bookingProgIDs as $bookingProgIDss)
					{
						if($bookingProgID_cond==" and")  $bookingProgID_cond.="(a.id in(".implode(',',$bookingProgIDss).")"; else $bookingProgID_cond.=" or a.id in(".implode(',',$bookingProgIDss).")";
					}
					$bookingProgID_cond.=")";
					//echo $bookingProgID_cond;die;

					$plan_arr =$booking_arr= array();
					$planOrder = sql_select("SELECT a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.status_active=1 and a.is_deleted=0 $bookingProgID_cond $booking_cond_b");
					/*echo "select a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.status_active=1 and a.is_deleted=0 $bookingProgID_cond $booking_cond_a"; die;*/ //inv_receive_master wo_po_details_master



					foreach ($planOrder as $plan_row) {
						$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
						$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
						$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
					}



				$sales_id_array = array_filter($sales_id_array);
				if(!empty($sales_id_array))
				{
					if($db_type==2 && count($sales_id_array)>999)
					{
						$sale_no_arr_chunk=array_chunk($sales_id_array,999) ;
						foreach($sale_no_arr_chunk as $chunk_arrss)
						{
							$chunk_arr_valuess=implode(",",$chunk_arrss);
							$salesCond.="  id in($chunk_arr_valuess) or ";
						}

						$all_sale_id_cond.=" and (".chop($salesCond,'or ').")";
					}
					else
					{
						$all_sale_id_cond=" and a.id in(".implode(",",$sales_id_array).")";
					}
					// $sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id,customer_buyer from fabric_sales_order_mst where status_active=1 $all_sale_id_cond $booking_cond_sales");

					$sales_details = sql_select("select a.id, a.job_no, a.sales_booking_no, a.within_group, a.style_ref_no, a.buyer_id, a.customer_buyer , b.brand_id from fabric_sales_order_mst a, wo_po_details_master b where a.po_job_no=b.job_no and a.status_active=1 $all_sale_id_cond $booking_cond_sales");

					// echo "select a.id, a.job_no, a.sales_booking_no, a.within_group, a.style_ref_no, a.buyer_id, a.customer_buyer from fabric_sales_order_mst a, b.wo_po_details_master b where a.po_job_no=b.job_no and a.status_active=1 $all_sale_id_cond $booking_cond_sales";

					foreach ($sales_details as $sales_row)
					{
						$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
						$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
						$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
						$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
						$sales_arr[$sales_row[csf('id')]]['customer_buyer'] = $sales_row[csf('customer_buyer')];
						$sales_arr[$sales_row[csf('id')]]['job_no'] = $sales_row[csf('job_no')];

						$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";

						$sales_arr[$sales_row[csf('id')]]['brand_id'] = $sales_row[csf('brand_id')];
					}
				}

				$sql_booking_details = "SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping as ref_no,c.insert_date as job_year
				from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c
				where a.booking_no=b.booking_no and b.po_break_down_id=c.id $booking_cond_a and a.status_active=1 and b.status_active=1
				group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,c.insert_date
				union all
				select booking_no,buyer_id, null as job_no,0 as po_break_down_id,null as  ref_no,insert_date as job_year   from wo_non_ord_samp_booking_mst where status_active=1 ";
					$booking_details = sql_select($sql_booking_details);

				foreach ($booking_details as $booking_row)
				{
					$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
					$booking_arr[$booking_row[csf("booking_no")]]["job_year"] = date("Y",strtotime($booking_row[csf("job_year")]));
					$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
					$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
					$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
					$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
					$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
				}
				$ppl_count_feeder_sql = sql_select("select a.booking_no,c.count_id,c.feeding_id
				from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
				where a.mst_id=b.mst_id and a.dtls_id=b.id and a.dtls_id=c.dtls_id and a.mst_id=c.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id $cbo_company_cond $booking_cond_a and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				order by seq_no");

				$ppl_count_feeder_array=array();
				foreach ($ppl_count_feeder_sql as $row)
				{
					$feeder_count=strlen($feeding_arr[$row[csf('feeding_id')]]);
					$ppl_count_feeder_array[$row[csf('booking_no')]]['count_id'] .= substr($feeding_arr[$row[csf('feeding_id')]], -$feeder_count,1).'-'.$yarn_count_details[$row[csf('count_id')]].',';
				}

				$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

				$print_report_format=return_field_value("format_id"," lib_report_template","template_name in($cbo_company) and module_id=7 and report_id=42 and is_deleted=0 and status_active=1");
				$gReportIds=explode(",",$print_report_format);
				$gReportId=$gReportIds[0];


				$i=1;
				$grand_tot_rollNumber=0;
				$grand_tot_rollNumberDelvQty=0;
				foreach($challan_data_arr as $challanNO=>$row_arr)
				{
					if($challanNO!="")
					{
						$tot_rollNumber=0;
						$tot_rollNumberDelvQty=0;
						foreach($row_arr as $key=>$row)
						{

							//---------------------
							/*$is_sales = $row[csf('is_sales')];
							if($is_sales == 1){
								$within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
								if($within_group == 1){
									$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
									$job_no = $booking_arr[$booking_no]["job_no"];
									$po_id = $booking_arr[$booking_no]["po_break_down_id"];
									$style_ref_no = $job_array[$po_id]['style_ref_no'];
									$ref_no = $booking_arr[$po_id]["ref_no"];
									$buyer_id=$booking_arr[$booking_no]["buyer_id"];
								}else{
									$job_no = "";
									$style_ref_no = "";
									$ref_no = "";
									$po="";
									$buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
									$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
								}
							}else{
								$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
								$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
								$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
								$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
								$buyer_id=$row[csf('buyer_id')];
								$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
							}*/
							//------------

							if ($row[csf("knitting_source")] == 1) {
									$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
							} else if ($row[csf("knitting_source")] == 3) {
								$knit_company = $supplier_arr[$row[csf("knitting_company")]];
							}
							else
							{
								$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
							}

							/*if($row[csf('receive_basis')]==2)
							{
								$buyer= $buyer_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["buyer_id"]];
								$jobYear=  $booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["job_year"];
								$jobNo=$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["job_no"];
								$refNo=$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["booking_ref_no"];
								$progNo=$row[csf('bwo')];
								$bookingNo= $plan_arr[$row[csf('bwo')]]["booking_no"];


								$ppl_count_ids=$ppl_count_feeder_array[$plan_arr[$row[csf('bwo')]]["booking_no"]]['count_id'];
								$ppl_count_id =chop($ppl_count_ids,',');

							}
							else
							{
								$buyer= $buyer_array[$booking_arr[$row[csf('booking_no')]]["buyer_id"]];
								$jobYear=    $booking_arr[$row[csf('booking_no')]]["job_year"];
								$jobNo=  $booking_arr[$row[csf('booking_no')]]["job_no"];
								$refNo=  $booking_arr[$row[csf('booking_no')]]["booking_ref_no"];
								$progNo="";
								$bookingNo= $row[csf('booking_no')];
							}*/


							if($row[csf('is_sales')]==1 || $row[csf('is_sales')]==0)
							{
								if($row[csf('within_group')]!=2)
								//if($row[csf('within_group')]==1)
								{
									//$bookingNo = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];

									if($row[csf('receive_basis')]==2)
									{
										//echo $row[csf('is_sales')]; die;
										if($row[csf('is_sales')]==0)
										{
											$buyer= $buyer_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["buyer_id"]];//.'--wG-u'.$row[csf('within_group')].'/RB-'.$row[csf('receive_basis')].'/isS-'.$row[csf('is_sales')];
											$jobYear=  $booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["job_year"];
											$jobNo=$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["job_no"];
											$refNo=$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["booking_ref_no"];
											$progNo=$row[csf('bwo')];
											$bookingNo= $plan_arr[$row[csf('bwo')]]["booking_no"];
										}
										else
										{
											$buyer= $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']]; //t
											$customerBuyer= $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['customer_buyer']]; //t
											$jobNo=  $sales_arr[$row[csf('po_breakdown_id')]]['job_no'];
											$bookingNo = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
											$refNo=$sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
											//$jobYear=  $sales_arr[$row[csf('po_breakdown_id')]]['job_no'];
											$progNo=$row[csf('bwo')];

											$brand=$sales_arr[$row[csf('po_breakdown_id')]]['brand_id'];

										}
										$ppl_count_ids=$ppl_count_feeder_array[$plan_arr[$row[csf('bwo')]]["booking_no"]]['count_id'];
										$ppl_count_id =chop($ppl_count_ids,',');

									}
									else
									{
										$buyer= $buyer_array[$booking_arr[$row[csf('booking_no')]]["buyer_id"]];//.'--wG-h'.$row[csf('within_group')].'/RB-'.$row[csf('receive_basis')].'/isS-'.$row[csf('is_sales')];
										$jobYear=    $booking_arr[$row[csf('booking_no')]]["job_year"];
										$jobNo=  $booking_arr[$row[csf('booking_no')]]["job_no"];
										$refNo=  $booking_arr[$row[csf('booking_no')]]["booking_ref_no"];
										$progNo="";
										$bookingNo= $row[csf('booking_no')];
										//echo $buyer;//die(" __ with");
									}
								}
								else if($row[csf('within_group')]==2)
								{


									$jobNo=  $sales_arr[$row[csf('po_breakdown_id')]]['job_no'];
									$buyer=$buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']];//.'--wG-t'.$row[csf('within_group')].'/RB-'.$row[csf('receive_basis')].'/isS-'.$row[csf('is_sales')];
									$customerBuyer=$buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['customer_buyer']];
									$bookingNo = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
									$refNo=$sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
									//$jobYear=  $sales_arr[$row[csf('po_breakdown_id')]]['job_no'];
									$progNo=$row[csf('bwo')];
									$brand=$sales_arr[$row[csf('po_breakdown_id')]]['brand_id'];

								}
							}
							else
							{
								if($row[csf('receive_basis')]==2) // sample without order production
								{
									//echo $row[csf('is_sales')]; die;
									if($row[csf('is_sales')]==2)
									{
										$buyer= $buyer_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["buyer_id"]];
										$jobYear=  $booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["job_year"];
										$jobNo=$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["job_no"];
										$refNo=$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]["booking_ref_no"];
										$progNo=$row[csf('bwo')];
										$bookingNo= $plan_arr[$row[csf('bwo')]]["booking_no"];
									}
								}
							}

							// $buyer_from_fabric_sales_order = sql_select("SELECT BUYER_ID from fabric_sales_order_mst where status_active=1 and job_no='$jobNo'");
							// $buyer_arr_name = return_library_array("select job_no, BUYER_ID from fabric_sales_order_mst", '$jobNo', 'BUYER_ID');
							$buyer_from_fabric_sales_order= return_field_value("BUYER_ID"," fabric_sales_order_mst","job_no='$jobNo' and is_deleted=0 and status_active=1");

							// echo $buyer_from_fabric_sales_order[0];

							$count = '';
							$yarn_count = explode(",", $row[csf('yarn_count')]);
							foreach ($yarn_count as $count_id) {
								if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
							}
							$internalRefNos= implode(",",array_unique(explode(",",$internalRefArr[$bookingNo]['grouping'])));
							if($brand==""){
							$brand=$brand_arr[$bookingNo]['brand_id'];
							}
							//echo "<pre>";print_r($buyer_array);//die;
							//echo "<pre>";print_r($booking_arr);//die;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="130" align="center"><p>
									<?

									$challan_no =$dataFromRoll[$row[csf("id")]]["challan_no"];
									$company_id =$dataFromRoll[$row[csf("id")]]["company_id"];
									$mst_id =$dataFromRoll[$row[csf("id")]]["mst_id"];
									$knitting_source =$dataFromRoll[$row[csf("id")]]["knitting_source"];
									$floor_name = $floor_name_arr_lib[$dataFromRoll[$row[csf("id")]]["floor_ids"]];
									$location_id =$dataFromRoll[$row[csf("id")]]["location_id"];
									$knitting_company =$dataFromRoll[$row[csf("id")]]["knitting_company"];
									$remarks =$dataFromRoll[$row[csf("id")]]["remarks"];
									$attention =$dataFromRoll[$row[csf("id")]]["attention"];

									//echo $challan_no;
									echo "<a href='##' onclick=\"generate_report('" . $challan_no . "','" . $company_id . "','" . $mst_id . "','" . $knitting_source . "','" . $floor_name . "',$location_id,'" . $knitting_company . "','" . $remarks . "','" . $attention . "','" . $gReportId . "' )\">$challan_no</a>";
									?>
								</p></td>
								<td width="80" align="center"><p><? echo date("d-m-Y",strtotime($dataFromRoll[$row[csf("id")]]["challan_date"])); ?></p></td>
								<td width="100" align="center" title="<? echo "within group=".$row[csf('within_group')]; ?>"><p><?


								if($buyer_from_fabric_sales_order==""){
									echo $buyer;
								}else{
									echo $company_name_arr[$buyer_from_fabric_sales_order];
								}


								//echo $buyer;//.'/is sales-'.$row[csf("is_sales")].'/withinGropu-'.$row[csf("within_group")].'/-po-'.$row[csf("po_breakdown_id")];//_array[$booking_arr[$plan_arr[$row[csf('booking_no')]]["booking_no"]]["buyer_id"]];?></p></td>
								<td width="100" align="center" title="<? echo "within group=".$row[csf('within_group')]; ?>"><p><? echo $customerBuyer;?></p></td>
								<td width="100" align="center" ><p><? echo $buyer_brand_details_arr[$brand];?></p></td>
								<td width="60"  align="center"><p><? echo  $jobYear;//[$plan_arr[$row[csf('booking_no')]]["booking_no"]]["job_year"]; ?></p></td>
								<td width="100" align="center"><p><? echo  $jobNo;//[$plan_arr[$row[csf('booking_no')]]["booking_no"]]["job_no"]; ?></p></td>
								<td width="100" align="center"><p><? echo  chop($internalRefNos,",");//[$plan_arr[$row[csf('booking_no')]]["booking_no"]]["job_no"]; ?></p></td>
								<td width="90" align="center"><p><? echo $refNo; ?></p></td>
								<td width="65" align="center"><p><? echo $progNo; ?></p></td>
								<td width="100" align="center"><p><? echo $bookingNo; ?></p></td>
								<td width="90" align="center"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
								<td width="90" align="center"><p><? echo $knit_company; ?></p></td>
								<td  align="center" style="width:110px; mso-number-format:\@; word-break:break-word;"><p><? echo $count;//$ppl_count_id; ?></p></td>
								<td width="70" align="center"><p><? echo $brand_details_arr[$row[csf('brand_id')]]; ?></p></td>
								<td align="center" style="width:80px;word-break:break-word; max-width:80px;"><p><?  echo $row[csf('yarn_lot')]; ?></p></td>
								<td width="130" align="center"><p>
									<?
									$colorID=$row[csf("color_id")];
									$color_id_arr = array_unique(explode(",", $colorID));
									$all_color_name = "";
									foreach ($color_id_arr as $c_id) {
										$all_color_name .= $color_arr[$c_id] . ",";
									}
									$all_color_name = chop($all_color_name, ",");
									echo $all_color_name;
									?>
								</p></td>
								<td width="100" align="center"><p><? echo $color_range[$row[csf("color_range_id")]]; ?></p></td>
								<td width="100" align="center" title="<? echo $plan_arr[$progNo]["feeder"];?>"><p><? echo $feeder_arr[$plan_arr[$progNo]["feeder"]]; ?></p></td>
								<td width="220" align="center"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
								<td style="width:60px;word-break:break-word; max-width:60px;"  align="center"><? echo $row[csf('stitch_length')]; ?></td>
								<td width="60" align="center"><? echo $row[csf('gsm')]; ?></td>
								<td width="60" align="center"><? echo $row[csf('width')]; ?></td>
								<td width="60" align="center"><? echo $row[csf('machine_dia')]; ?></td>
								<td width="60" align="center"><? echo $row[csf('machine_gg')]; ?></td>
								<td width="60" align="center"><strong><? echo $rollNumber=$dataarr[$key]['rollNumb']; ?></strong></td>
								<td align="right"><strong><?
								echo number_format($dataarr[$key]['current_delivery'],2);
								$rollNumberDelvQty=$dataarr[$key]['current_delivery']; ?></strong></td>
							</tr>
							<?

							$tot_rollNumber+=$rollNumber;
							$tot_rollNumberDelvQty+=$rollNumberDelvQty;

							$grand_tot_rollNumber+=$rollNumber;
							$grand_tot_rollNumberDelvQty+=$rollNumberDelvQty;

							$i++;
						}
						?>
						<tr bgcolor="#CCCCCC">
							<td colspan='26' align="right"><strong>Challan Total</strong></td>
							<td align="center" width="60"><strong><? echo  $tot_rollNumber; ?></strong></td>
							<td align="right"><strong><? echo  number_format($tot_rollNumberDelvQty,2); ?></strong>&nbsp;</td>
						</tr>
						<?
					}
				}
				?>
				<tfoot>
					<tr>
						<th colspan='26' align="right"><strong>Grand Total</strong></th>
					<th style="text-align:center;" width="60"><strong><? echo  $grand_tot_rollNumber; ?></strong></th>
					<th align="right"><strong><? echo number_format($grand_tot_rollNumberDelvQty,2); ?></strong></th>
					</tr>
				</tfoot>
				</table>
			</div>
			<!--<table width="2500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">

			</table>-->

		</fieldset>
		<?
	}

	/*foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();*/
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$filename=time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$fdata=ob_get_contents();
	fwrite($create_new_doc,$fdata);
	ob_end_clean();
	echo "$fdata####$filename";
	exit();
}

if($action=="report_generate2") // Weight Level
{
	// This report without sales and SMN, only program basis and with order
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_type=str_replace("'","",$report_type);
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_floor_id=str_replace("'","",$txt_floor_id);
	$txt_programme_no=str_replace("'","",$txt_programme_no);

	if($cbo_knitting_source==0) $cbo_knitting_source_cond=""; else $cbo_knitting_source_cond=" and c.knitting_source=$cbo_knitting_source";
	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$cbo_company";
	if($txt_floor_id==0) $floor_cond=""; else $floor_cond=" and b.floor_id ($txt_floor_id)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and c.knitting_company=$cbo_working_company";
	if ($txt_booking_no=="") $booking_cond_a=""; else $booking_cond_a=" and a.booking_no like '%$txt_booking_no%' ";
	if ($txt_booking_no=="") $booking_cond_b=""; else $booking_cond_b=" and b.booking_no like '%$txt_booking_no%' ";
	if ($txt_programme_no=="") $programme_no_cond=""; else $programme_no_cond=" and c.booking_no like '%$txt_programme_no%' ";

	$from_date=$txt_date_from;
	if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;
	$date_con="";
	if($from_date!="" && $to_date!="") $date_con=" and a.delevery_date between '$from_date' and '$to_date'";

	if($from_date!="" && $to_date!="") $date_con_production=" and b.receive_date between '$from_date' and '$to_date'";

	ob_start();

	$con = connect();
    $r_id1=execute_query("delete from tmp_recv_mst_id where userid=$user_id");
    $r_id2=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id3=execute_query("delete from tmp_booking_id where userid=$user_id");
    oci_commit($con);

	if($txt_booking_no !="") // if search by booking no
	{
		$sql_booking_program="SELECT c.id as prog_no from inv_receive_master b, ppl_planning_info_entry_dtls c, ppl_planning_info_entry_mst d
		where b.booking_id=c.id and c.mst_id=d.id and b.entry_form=2 and b.receive_basis=2 and d.booking_no like '%$txt_booking_no%'";
		// echo $sql_booking_program;die;
		$sql_booking_program_res=sql_select($sql_booking_program);
		$booking_program_cond="";
		foreach($sql_booking_program_res as $row)
		{
			$booking_program_no_arr[$row[csf("prog_no")]]=$row[csf("prog_no")];
		}
		if(count($booking_program_no_arr)>0)
		{
			$booking_roll_ids_chunk=array_chunk($booking_program_no_arr,999);
			$booking_program_cond=" and";
			foreach($booking_roll_ids_chunk as $booking_program_nos)
			{
				if($booking_program_cond==" and")  $booking_program_cond.="(b.program_no in(".implode(',',$booking_program_nos).")"; else $booking_program_cond.=" or b.program_no in(".implode(',',$booking_program_nos).")";
			}
			$booking_program_cond.=")";
			//$booking_barcode_cond=" and b.roll_id in(".implode(',',$booking_program_no_arr).")";
		}
		unset($sql_booking_program_res);
	}
	// echo $booking_program_cond;die;
	// ======================= Search By Knitting Source,  Working Company, Floor Start ===============
	if($txt_floor_id>0)
	{
		$search_by_prod_sql = "SELECT a.id, a.recv_number, a.company_id,a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company,a.buyer_id,a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id,b.machine_dia,b.machine_gg, c.quantity as production_qty, c.po_breakdown_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $cbo_company_cond $cbo_knitting_source_cond $company_working_cond $floor_cond  and c.is_sales=0";
		// echo $search_by_prod_sql;die;
		$search_by_prod_sql_result=sql_select($search_by_prod_sql);
		foreach ($search_by_prod_sql_result as $key => $row)
		{
			if( $mst_id_check[$row[csf('id')]] == "" )
            {
                $mst_id_check[$row[csf('id')]]=$row[csf('id')];
                $id = $row[csf('id')];
                // echo "insert into tmp_recv_mst_id (userid, mst_id) values ($user_id,$id)";
                $r_id1=execute_query("insert into tmp_recv_mst_id (userid, mst_id) values ($user_id,$id)");
            }
		}
		oci_commit($con);

		// =========================Delivery Query Start =====================
		$delivery_res_sql = "SELECT a.id,a.sys_number as challan_no, a.sys_number_prefix_num as challan_prefix, a.delevery_date as challan_date, a.floor_ids, a.company_id, b.current_delivery, b.grey_sys_id as production_id, b.program_no, b.roll, c.knitting_company, c.knitting_source, c.receive_basis, c.recv_number
		from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c, tmp_recv_mst_id d
		where b.mst_id=a.id and b.grey_sys_id=c.id and b.program_no=c.booking_id and d.mst_id=c.id and d.mst_id=b.grey_sys_id and d.userid=$user_id and c.entry_form=2 and c.receive_basis=2 and a.entry_form=53 and b.entry_form=53 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $cbo_company_cond $cbo_knitting_source_cond $company_working_cond $date_con $booking_program_cond $programme_no_cond and b.roll_id is null and b.is_sales=0 and c.status_active = 1 and c.is_deleted = 0";
		// echo $delivery_res_sql;die;
		$delivery_res = sql_select($delivery_res_sql);

		$r_id1=execute_query("delete from tmp_recv_mst_id where userid=$user_id");
	    oci_commit($con);


		foreach ($delivery_res as $val)
		{
			if( $mst_id_check2[$val[csf('production_id')]] == "" )
            {
                $mst_id_check2[$val[csf('production_id')]]=$val[csf('production_id')];
                $production_id = $val[csf('production_id')];
                // echo "INSERT into tmp_recv_mst_id (userid, mst_id) values ($user_id,$production_id)";
                $r_id1=execute_query("insert into tmp_recv_mst_id (userid, mst_id) values ($user_id,$production_id)");
            }
            $production_id_arr[$val[csf("production_id")]] = $val[csf("production_id")];
		}
		oci_commit($con);

	} // Search By Knitting Source,  Working Company, Floor End

	else // Delivery Query Start
	{
		$delivery_res_sql = "SELECT a.id,a.sys_number as challan_no, a.sys_number_prefix_num as challan_prefix, a.delevery_date as challan_date, a.floor_ids, a.company_id, b.current_delivery, b.grey_sys_id as production_id, b.program_no, b.roll, c.knitting_company, c.knitting_source, c.receive_basis, c.recv_number
		from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c
		where b.mst_id=a.id and b.grey_sys_id=c.id and b.program_no=c.booking_id and c.entry_form=2 and c.receive_basis=2 and a.entry_form=53 and b.entry_form=53 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $cbo_company_cond $cbo_knitting_source_cond $company_working_cond $date_con $booking_program_cond $programme_no_cond and b.roll_id is null and b.is_sales=0 and c.status_active = 1 and c.is_deleted = 0";
		// echo $delivery_res_sql;die;
		$delivery_res = sql_select($delivery_res_sql);

		foreach ($delivery_res as $val)
		{
			if( $mst_id_check3[$val[csf('production_id')]] == "" )
            {
                $mst_id_check3[$val[csf('production_id')]]=$val[csf('production_id')];
                $production_id = $val[csf('production_id')];
                // echo "INSERT into tmp_recv_mst_id (userid, mst_id) values ($user_id,$production_id)";
                $r_id1=execute_query("insert into tmp_recv_mst_id (userid, mst_id) values ($user_id,$production_id)");
            }
			$production_id_arr[$val[csf("production_id")]] = $val[csf("production_id")];
		}
		oci_commit($con);
	} // Delivery Query End
	// ============================ Delivery Query End    ===========================

	// ============================ Production sql Start    =========================
	$challan_data_arr=array(); $po_id_array = $sales_id_array = $booking_program_arr = array();  $feedar_prog_id=""; $prod_qty=0;
	if (count($production_id_arr)>0) // This report without sales
	{
		$production_sql = "SELECT a.id, a.recv_number, a.company_id,a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company,a.buyer_id,a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id,b.machine_dia,b.machine_gg, c.quantity as production_qty, c.po_breakdown_id, c.is_sales
		from tmp_recv_mst_id t, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
		where t.mst_id=a.id and t.mst_id=b.mst_id and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and t.userid=$user_id and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $floor_cond ";// and c.is_sales=0
		// echo $production_sql;die;
		$production_sql_result=sql_select($production_sql);
		$summary_arr=array();
		foreach($production_sql_result as $row)
		{
			$summary_arr[$row[csf("knitting_source")]]['production_qty']+=$row[csf("production_qty")];

			$po_id_array[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			if ($po_id_check[$row[csf('po_breakdown_id')]] == "")
            {
                $po_id_check[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
                $po_id = $row[csf('po_breakdown_id')];
                //echo "insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)";
                $r_id2=execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)");
            }

			$fso_ar= array();
			$without_fso= array();
			if($row[csf("is_sales")]==1)
			{
				$fso_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
			}
			else{
				$without_fso[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
			}

			if ($row[csf('receive_basis')] == 2)
			{
				// $booking_program_arr[] = $row[csf("booking_id")];
				if( $program_no_check[$row[csf('booking_id')]] == "" )
	            {
	                $program_no_check[$row[csf('booking_id')]]=$row[csf('booking_id')];
	                $program_no = $row[csf('booking_id')];
	                $booking_no = $row[csf('booking_no')];

	                // echo "INSERT into tmp_booking_id (userid, booking_id,type) values ($user_id,$program_no,1)";
	                $r_id2=execute_query("insert into tmp_booking_id (userid, booking_id, type, booking_no) values ($user_id,$program_no, 1, $booking_no)");
	            }
			}
			else
			{
				// $booking_no = explode("-", $row[csf('booking_no')]);
				// $booking_program_arr[] = (int)$booking_no[3];
				if( $booking_id_check[$row[csf('booking_id')]] == "" )
	            {
	                $booking_id_check[$row[csf('booking_id')]]=$row[csf('booking_id')];
	                $booking_id = $row[csf('booking_id')];
	                $booking_no = $row[csf('booking_no')];
	                // echo "INSERT into tmp_booking_id (userid, booking_id, type) values ($user_id,$booking_id,2)";
	                $r_id2=execute_query("insert into tmp_booking_id (userid, booking_id, type, booking_no) values ($user_id,$booking_id, 2, $booking_no)");
	            }
			}

			/*$production_data_arr[$row[csf("booking_id")]]['knitting_source']=$row[csf("knitting_source")];
			$production_data_arr[$row[csf("booking_id")]]['recv_number'].=$row[csf("recv_number")].',';
			$production_data_arr[$row[csf("booking_id")]]['knitting_company']=$row[csf("knitting_company")];
			$production_data_arr[$row[csf("booking_id")]]['receive_basis']=$row[csf("receive_basis")];*/
			$production_data_arr[$row[csf("booking_id")]]['yarn_count'].=$row[csf("yarn_count")].',';
			$production_data_arr[$row[csf("booking_id")]]['yarn_lot'].=$row[csf("yarn_lot")].',';
			$production_data_arr[$row[csf("booking_id")]]['brand_id'].=$row[csf("brand_id")].',';
			$production_data_arr[$row[csf("booking_id")]]['color_id'].=$row[csf("color_id")].',';
			$production_data_arr[$row[csf("booking_id")]]['febric_description_id'].=$row[csf("febric_description_id")].',';
			$production_data_arr[$row[csf("booking_id")]]['stitch_length'].=$row[csf("stitch_length")].',';
			$production_data_arr[$row[csf("booking_id")]]['gsm'].=$row[csf("gsm")].',';
			$production_data_arr[$row[csf("booking_id")]]['width'].=$row[csf("width")].',';
			$production_data_arr[$row[csf("booking_id")]]['machine_dia'].=$row[csf("machine_dia")].',';
			$production_data_arr[$row[csf("booking_id")]]['machine_gg'].=$row[csf("machine_gg")].',';
			// echo $row[csf("id")].']['.$row[csf("booking_id")].'='.$row[csf("recv_number")].'==<br>';
		}
		oci_commit($con);
	}
	// echo '<pre>';print_r($booking_program_arr);die;
	// ============================ Production sql End    =========================

	// ============================ Main data array start =========================
	$delivery_data_arr=array();
	foreach ($delivery_res as $val)
	{
		// echo $val[csf("production_id")].']['.$val[csf("program_no")].'**<br>';
		$yarn_count=$production_data_arr[$val[csf("program_no")]]['yarn_count'];
		$yarn_lot=$production_data_arr[$val[csf("program_no")]]['yarn_lot'];
		$brand_id=$production_data_arr[$val[csf("program_no")]]['brand_id'];
		$color_id=$production_data_arr[$val[csf("program_no")]]['color_id'];
		$febric_description_id=$production_data_arr[$val[csf("program_no")]]['febric_description_id'];
		$stitch_length=$production_data_arr[$val[csf("program_no")]]['stitch_length'];
		$gsm=$production_data_arr[$val[csf("program_no")]]['gsm'];
		$dia=$production_data_arr[$val[csf("program_no")]]['width'];
		$machine_dia=$production_data_arr[$val[csf("program_no")]]['machine_dia'];
		$machine_gg=$production_data_arr[$val[csf("program_no")]]['machine_gg'];

		$str_data=$val[csf("challan_date")].'*'.$val[csf("program_no")];
		// heare $val[csf("program_no")] is program no or booking id, if production receive_basis==2 this program no if receive_basis==1 then booking id
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["production_id"] .= $val[csf("production_id")].',';

		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["production_no"]=$val[csf("recv_number")];
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["knitting_source"]=$val[csf("knitting_source")];
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["knitting_company"]=$val[csf("knitting_company")];
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["receive_basis"]=$val[csf("receive_basis")];
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["yarn_count"]= $yarn_count;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["yarn_lot"]= $yarn_lot;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["brand_id"]= $brand_id;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["color_id"]= $color_id;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["febric_description_id"]= $febric_description_id;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["stitch_length"]= $stitch_length;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["gsm"]= $gsm;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["dia"]= $dia;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["machine_dia"]= $machine_dia;
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["machine_gg"]= $machine_gg;

		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["delivery_qty"] += $val[csf("current_delivery")];
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["floor_ids"] = $val[csf("floor_ids")];
		$delivery_data_arr[$val[csf("challan_no")]][$str_data]["roll"] += $val[csf("roll")];

		$total_delivery_qty+= $val[csf("current_delivery")];
	}
	// echo '<pre>';print_r($delivery_data_arr);die;
	// ============================ Main data array End   =========================
	?>
	<fieldset style="width:2520px">
	<table cellpadding="0" cellspacing="0" width="2500">
		<tr class="form_caption" style="border:none;">
		<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
		</tr>
		<tr class="form_caption" style="border:none;">
		<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
		</tr>
	</table>

	<!-- Summary Start -->
	<fieldset style="width:400px">
	<table width="400" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="font-weight:bold;">
		<?
		$inhouse_qty=0;
		$outbound_inhouse_qty=0;
		foreach ($summary_arr as $knitting_source => $row)
		{
			if($knitting_source==1)
			{
				$inhouse_qty+=$row["production_qty"];
			}
			else if($knitting_source==3)
			{
				$outbound_inhouse_qty+=$row["production_qty"];
			}
		}
		$totalProduction=$inhouse_qty+$outbound_inhouse_qty;
		$total_prod_qty=$totalProduction-$total_delivery_qty;
		?>
		<thead>
			<tr>
				<th colspan="2">Summery (Self Order)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="300">Production (Inhouse)</td>
				<td align="right"><? echo number_format($inhouse_qty,2); ?></td>
			</tr>
			<tr>
				<td width="300">Production (Outbound Subcontract)</td>
				<td align="right"><? echo number_format($outbound_inhouse_qty,2); ?></td>
			</tr>
			<tr>
				<td width="300" align="right">Total Production:</td>
				<td align="right"><? echo number_format($totalProduction,2); ?> </td>
			</tr>
			<tr>
				<td width="300">Grey Fab Delivery To Store</td>
				<td align="right"><? echo number_format($total_delivery_qty,2); ?></td>
			</tr>
			<tr>
				<td width="300" align="right">Delivery Balace :</td>
				<td align="right"><? echo number_format($total_prod_qty,2); ?></td>
			</tr>
		</tbody>
	</table>
	</fieldset>
	<!-- Summary End -->

	<!-- Details Start -->
	<table width="2085" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="130">Challan No</th>
				<th width="80">Challan Date</th>
				<th width="100">Buyer</th>
				<th width="60">Job Year</th>
				<th width="100">Brand</th>
				<th width="100">Job</th>
				<th width="90">Style Ref</th>
				<th width="65">Prog No</th>
				<th width="100">Book. No</th>
				<th width="90">Production Basis</th>
				<th width="90">Knitting Company</th>
				<th width="110">Yarn Count</th>
				<th width="70">Yarn Brand</th>
				<th width="80">Lot No</th>
				<th width="130">Fab Color</th>
				<th width="220">Fabric Description</th>
				<th width="60">Stich</th>
				<th width="60">Fin GSM</th>
				<th width="60">Fab. Dia</th>
				<th width="60">MC. Dia</th>
				<th width="60">MC. Gauge</th>
				<th width="60">No Of Roll</th>
				<th>QC Pass Qty</th>
			</tr>
		</thead>
	</table>

	<div style="width:2105px; overflow-y: scroll; max-height:380px;" id="scroll_body">
		<table width="2085" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
			<?
			//echo "<pre>";print_r($challan_data_arr);//die;

			$plan_arr =$booking_arr= array();
			$plan_sql = "SELECT a.id, b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder
			from TMP_BOOKING_ID t, ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b
			where t.booking_id=a.id and t.type=1 and t.userid=$user_id and a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 $booking_cond_b";
			// echo $plan_sql;die;
			$plan_sql_result = sql_select($plan_sql);
			foreach ($plan_sql_result as $plan_row)
			{
				$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
				$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
			}

			if(!empty($fso_arr))
			{
				$fab_sales = "select a.customer_buyer, a.job_no , a.style_ref_no , a.insert_date as job_year, a.sales_booking_no, b.brand_id
				from tmp_po_id t, fabric_sales_order_mst a , wo_po_details_master b
				where t.po_id=a.id  and t.user_id=$user_id and a.po_job_no=b.job_no and a.status_active=1";

				// echo $fab_sales;

				$fab_sales_dtls = sql_select($fab_sales);

				foreach($fab_sales_dtls as $sales_row)
				{
					$fso_data_arr[$sales_row[csf("sales_booking_no")]]["job_no"] = $sales_row[csf("job_no")];
					$fso_data_arr[$sales_row[csf("sales_booking_no")]]["style_ref_no"] = $sales_row[csf("style_ref_no")];
					$fso_data_arr[$sales_row[csf("sales_booking_no")]]["job_year"] = date("Y",strtotime($sales_row[csf("job_year")]));
					$fso_data_arr[$sales_row[csf("sales_booking_no")]]["customer_buyer"] = $sales_row[csf("customer_buyer")];
					$fso_data_arr[$sales_row[csf("sales_booking_no")]]["sales_booking_no"] = $sales_row[csf("sales_booking_no")];
					$fso_data_arr[$sales_row[csf("sales_booking_no")]]["brand_id"] = $sales_row[csf("brand_id")];
				}
			}
			else
			{


				$sql_booking_details = "SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping as ref_no,c.insert_date as job_year, d.style_ref_no , d.brand_id
				from  tmp_po_id t, wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
				where  t.po_id=c.id and t.user_id=$user_id and a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id $booking_cond_a and a.status_active=1 and b.status_active=1
				group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,c.insert_date, d.style_ref_no, d.brand_id
				union all
				select a.booking_no, a.buyer_id, null as job_no, 0 as po_break_down_id, null as ref_no,insert_date as job_year, null as style_ref_no, null as brand_id
				from tmp_booking_id t, wo_non_ord_samp_booking_mst a where t.booking_id=a.id and t.type=2 and t.user_id=$user_id and a.status_active=1";
				// echo $sql_booking_details;die;
				$booking_details = sql_select($sql_booking_details);

				foreach ($booking_details as $booking_row)
				{
					$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];

					$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
					$booking_arr[$booking_row[csf("booking_no")]]["job_year"] = date("Y",strtotime($booking_row[csf("job_year")]));
					$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
					$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
					$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
					$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
					$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];

					$booking_arr[$booking_row[csf("booking_no")]]["brand_id"] = $booking_row[csf("brand_id")];
				}
			}
			// echo "<pre>"; print_r($fso_data_arr);die;


			$r_id1=execute_query("delete from tmp_recv_mst_id where userid=$user_id");
		    $r_id2=execute_query("delete from tmp_po_id where user_id=$user_id");
		    $r_id3=execute_query("delete from tmp_booking_id where userid=$user_id");
		    oci_commit($con);

			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			$i=1;
			$grand_tot_rollNumber=0;
			$grand_tot_rollNumberDelvQty=0;
			foreach($delivery_data_arr as $challanNO=>$str_arr)
			{
				if($challanNO!="")
				{
					$tot_rollNumber=0;
					$tot_rollNumberDelvQty=0;
					foreach($str_arr as $strData=>$row)
					{
						$data_str=explode("*", $strData);
						$challan_date=$data_str[0];
						$challan_program=$data_str[1];

						if ($row["knitting_source"] == 1)
						{
							$knit_company = $company_array[$row["knitting_company"]]['shortname'];
						}
						else if ($row["knitting_source"] == 3)
						{
							$knit_company = $supplier_arr[$row["knitting_company"]];
						}
						else
						{
							$knit_company=$company_array[$row["knitting_company"]]['shortname'];
						}

						if(!empty($fso_arr))
						{
							if($row['receive_basis']==2) // Program basis
							{
								$progNo=$challan_program;
								$bookingNo= $plan_arr[$challan_program]["booking_no"];
							}
							else{
								$bookingNo= $row['booking_no'];
								$progNo="";
							}

							$buyer= $buyer_array[$fso_data_arr[$bookingNo]["customer_buyer"]];
							$jobYear=    $fso_data_arr[$bookingNo]["job_year"];
							$jobNo=  $fso_data_arr[$bookingNo]["job_no"];
							$style_ref_no=  $fso_data_arr[$bookingNo]["style_ref_no"];

							$buyer_brand_id=  $fso_data_arr[$bookingNo]["brand_id"];

						}
						else
						{

							if($row['receive_basis']==2) // Program basis
							{
								$buyer= $buyer_array[$booking_arr[$plan_arr[$challan_program]["booking_no"]]["buyer_id"]];
								$jobYear=  $booking_arr[$plan_arr[$challan_program]["booking_no"]]["job_year"];
								$jobNo=$booking_arr[$plan_arr[$challan_program]["booking_no"]]["job_no"];
								$style_ref_no=$booking_arr[$plan_arr[$challan_program]["booking_no"]]["style_ref_no"];
								$refNo=$booking_arr[$plan_arr[$challan_program]["booking_no"]]["booking_ref_no"];
								$progNo=$challan_program;
								$bookingNo= $plan_arr[$challan_program]["booking_no"];

								$buyer_brand_id = $booking_arr[$plan_arr[$challan_program]["booking_no"]]["brand_id"];

							}
							else
							{
								$buyer= $buyer_array[$booking_arr[$row['booking_no']]["buyer_id"]];
								$jobYear=    $booking_arr[$row['booking_no']]["job_year"];
								$jobNo=  $booking_arr[$row['booking_no']]["job_no"];
								$refNo=  $booking_arr[$row['booking_no']]["booking_ref_no"];
								$progNo="";
								$bookingNo= $row['booking_no'];

								$buyer_brand_id =  $booking_arr[$row['booking_no']]["brand_id"];

							}
						}

						$count = '';
						$yarn_count = array_unique(explode(",", chop($row['yarn_count'],',')));
						foreach ($yarn_count as $count_id)
						{
							if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
						}

						$brand = '';
						$brand_id = array_unique(explode(",", chop($row['brand_id'],',')));
						foreach ($brand_id as $id)
						{
							if ($brand == '') $brand = $brand_details_arr[$id]; else $brand .= "," . $brand_details_arr[$id];
						}

						$yarn_lot = implode(",", array_unique(explode(",", chop($row['yarn_lot'],','))));

						$color_id_arr = array_unique(explode(",", chop($row['color_id'],',')));
						$all_color_name = "";
						foreach ($color_id_arr as $c_id)
						{
							$all_color_name .= $color_arr[$c_id] . ",";
						}
						$all_color_name = chop($all_color_name, ",");

						$febric_descr_arr=array_unique(explode(",", chop($row['febric_description_id'],',')));
						$febric_description = "";
						foreach ($febric_descr_arr as $f_id)
						{
							$febric_description .= $composition_arr[$f_id] . ",";
						}
						$febric_description = chop($febric_description, ",");
						$stitch_length = implode(",", array_unique(explode(",", chop($row['stitch_length'],','))));
						$gsm = implode(",", array_unique(explode(",", chop($row['gsm'],','))));
						$dia = implode(",", array_unique(explode(",", chop($row['dia'],','))));
						$machine_dia = implode(",", array_unique(explode(",", chop($row['machine_dia'],','))));
						$machine_gg = implode(",", array_unique(explode(",", chop($row['machine_gg'],','))));

						//echo "<pre>";print_r($buyer_array);//die;
						//echo "<pre>";print_r($booking_arr);//die;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td width="130" align="center"><p><? echo $challanNO; ?></p></td>
							<td width="80" align="center"><p><? echo change_date_format($challan_date); ?></p></td>
							<td width="100" align="center"><p><? echo $buyer;//.'/is sales-'.$row[csf("is_sales")].'/withinGropu-'.$row[csf("within_group")].'/-po-'.$row[csf("po_breakdown_id")];//_array[$booking_arr[$plan_arr[$row[csf('booking_no')]]["booking_no"]]["buyer_id"]];?></p></td>
							<td width="60"  align="center"><p><? echo $jobYear;//[$plan_arr[$row[csf('booking_no')]]["booking_no"]]["job_year"]; ?></p></td>

							<td width="100" align="center"><p><? echo $buyer_brand_details_arr[$buyer_brand_id] ; ?></p></td>

							<td width="100" align="center"><p><? echo $jobNo; ?></p></td>
							<td width="90" align="center"><p><? echo $style_ref_no; ?></p></td>
							<td width="65" align="center"><p><? echo $progNo; ?></p></td>
							<td width="100" align="center"><p><? echo $bookingNo; ?></p></td>
							<td width="90" align="center"><p><? echo $receive_basis[$row['receive_basis']]; ?></p></td>
							<td width="90" align="center"><p><? echo $knit_company; ?></p></td>
							<td width="110" align="center"><p><? echo $count; ?></p></td>
							<td width="70" align="center"><p><? echo $brand; ?></p></td>
							<td width="80" align="center"><p><?  echo $yarn_lot; ?></p></td>
							<td width="130" align="center"><p><? echo $all_color_name; ?></p></td>
							<td width="220" align="center"><p><? echo $febric_description; ?></p></td>
							<td width="60" align="center"><? echo $stitch_length; ?></td>
							<td width="60" align="center"><? echo $gsm; ?></td>
							<td width="60" align="center"><? echo $dia; ?></td>
							<td width="60" align="center"><? echo $machine_dia; ?></td>
							<td width="60" align="center"><? echo $machine_gg; ?></td>
							<td width="60" align="center"><strong><? echo $rollNumber=$row['roll']; ?></strong></td>
							<td align="right"><strong><? echo number_format($row['delivery_qty'],2); $DelvQty=$row['delivery_qty']; ?></strong></td>
						</tr>
						<?

						$tot_rollNumber+=$rollNumber;
						$tot_DelvQty+=$DelvQty;

						$grand_tot_rollNumber+=$rollNumber;
						$grand_tot_DelvQty+=$DelvQty;

						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td colspan='22' align="right"><strong>Challan Total</strong></td>
						<td align="center" width="60"><strong><? echo  $tot_rollNumber; ?></strong></td>
						<td align="right"><strong><? echo  number_format($tot_DelvQty,2); ?></strong>&nbsp;</td>
					</tr>
					<?
				}
			}
			?>
			<tfoot>
				<tr>
					<th colspan='22' align="right"><strong>Grand Total</strong></th>
					<th style="text-align:center;" width="60"><strong><? echo  $grand_tot_rollNumber; ?></strong></th>
					<th align="right"><strong><? echo number_format($grand_tot_DelvQty,2); ?></strong></th>
				</tr>
			</tfoot>
		</table>
	</div>
	</fieldset>
	<!-- Details End -->

	<?
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$filename=time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$fdata=ob_get_contents();
	fwrite($create_new_doc,$fdata);
	ob_end_clean();
	echo "$fdata####$filename";
	exit();
}
?>
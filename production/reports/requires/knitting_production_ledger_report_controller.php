
<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:logout.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action == "load_drop_down_floor")
{
	$data = explode("_", $data);
	$company_id = $data[0];
	echo create_drop_down("cbo_floor_id", 120, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and a.production_process=2 group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

if($action=="machine_no_search_popup")
{
	echo load_html_head_contents("Machine No popup", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	if ($floor_id == 0 || $floor_id == "") $floor_cond = ""; else $floor_cond = " and floor_id=$floor_id";
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
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			}
			else {
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
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_order_id').val(id);
			$('#hide_order_no').val(name);
		}
	</script>
	</head>
	<body>
		<div align="center">			
			<input type="hidden" name="hide_order_no" id="hide_order_no" value=""/>
			<input type="hidden" name="hide_order_id" id="hide_order_id" value=""/>
			<?
				$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
				$arr = array( 0 => $floor_arr);
				$sql="select id,company_id,floor_id,machine_no,machine_group,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and company_id=$company_id $floor_cond order by seq_no";
				//echo $sql;die;
				echo create_list_view("tbl_list_search", "Floor,Machine No,Brand Name,Machine Group,Dia Width, Gauge", "120,100,100,110,60", "660", "220", 0, $sql, "js_set_value", "id,machine_no", "", 1, "floor_id,0,0,0,0,0", $arr, "floor_id,machine_no,brand,machine_group,dia_width,gauge", "", '', '0,0,0,0,0,0', '', 1);
			?>
		</div>
	</body>
	<script type="text/javascript">
	setFilterGrid('tbl_list_search',-1);
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="sales_order_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:830px;margin-left:4px;">
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Year</th>
							<th>Within Group</th>
							<th>Search By</th>
							<th>Search</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center"><? echo create_drop_down("cbo_year", 80, $year,"", 1, "-- All --", date('Y'), "",0 ); ?></td>
							<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
								//,4=>"Batch No."
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value, 'create_sales_order_no_search_list_view', 'search_div', 'knitting_production_ledger_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_sales_order_no_search_list_view")
{
	$data=explode('_',$data);

	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	$cbo_year = $data[4];

	$company_arr=return_library_array( "select id,company_short_name from lib_company where id=$company_id",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."%'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."%'";
		else  if($search_by==3)  $search_field_cond=" and a.style_ref_no like '%".$search_string."%'";
	}

	if ($db_type == 0)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and YEAR(a.insert_date)=$cbo_year";
		}
	}
	else if ($db_type == 2)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	$sql = "SELECT a.po_buyer, a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $search_field_cond $sales_order_year_condition order by a.id";

	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>			
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$buyer_arr[$row[csf('po_buyer')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$fso_data =$row[csf('id')]."**".$row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $fso_data; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="110" align="center"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>
					<td width="110" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| report_generate
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_knitting_source = str_replace("'", "", trim($cbo_knitting_source));
	$cbo_floor_id = str_replace("'", "", trim($cbo_floor_id));
	$txt_machine_name = str_replace("'", "", trim($txt_machine_name));
	$txt_machine_id = str_replace("'", "", trim($txt_machine_id));
	$sales_job_no = str_replace("'", "", $txt_sales_job_no);
	$hide_job_id = str_replace("'", "", $hide_job_id);
	$cbo_get_upto = str_replace("'", "", trim($cbo_get_upto));
	$txt_qty = str_replace("'", "", trim($txt_qty));
	$cbo_knitting_status = str_replace("'", "", trim($cbo_knitting_status));
	$cbo_Shift_id = str_replace("'", "", trim($cbo_Shift_id));
	$txt_date_from = str_replace("'", "", trim($txt_date_from));
	$txt_date_to = str_replace("'", "", trim($txt_date_to));
	if ($txt_date_from!="") 
	{
		$txt_date_from = date('d-M-Y',strtotime($txt_date_from));
	}
	
	/*if($db_type==0)
	{
		$year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
	}
	else
	{
		$year_cond="";
	}*/

	/*if ($txt_date_to == "") {
		$txt_date_to = $txt_date_from;
	} else {
		$txt_date_to = $txt_date_to;
	}

	if ($txt_date_from != "" && $txt_date_to != "") 
	{
		if ($db_type == 0) 
		{
			$date_range_cond = " and a.receive_date between '" . $txt_date_from . "' and '" . $txt_date_to . "'";
		} 
		else 
		{
			$date_range_cond = " and a.receive_date between '" . $txt_date_from . "' and '" . $txt_date_to . "'";
		}
	} else 
	{
		$date_range_cond = "";
	}*/

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$to_production_date_cond = " and a.receive_date <= '".$txt_date_to."'";
	}

	
	$floor_cond = ($cbo_floor_id >0)?" and b.floor_id=$cbo_floor_id " : "";
	$machine_id_cond = ($txt_machine_id !="")?" and b.machine_no_id in($txt_machine_id)" : "";
	$sales_order_cond = ($sales_job_no !="")?" and e.job_no like '%$sales_job_no%' " : "";
	$Shift_cond = ($cbo_Shift_id >0)?" and b.shift_name=$cbo_Shift_id " : "";
	$knitting_source_cond = ($cbo_knitting_source >0)?" and a.knitting_source like '$cbo_knitting_source' " : "";
	

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$floor_details=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
	$color_details=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

	$machine_details=array();
	$machine_data=sql_select("select id,machine_no,dia_width,gauge,brand,seq_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and machine_no is not null order by seq_no");
	//echo "select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0";
	$machine_in_not=array("CC","GS");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
		$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
		$machine_details[$row[csf('id')]]['brand']=$row[csf('brand')];
		$machine_arr[$row[csf('id')]]=$row[csf('machine_no')];
		
		if(!in_array($row[csf('machine_no')],$machine_in_not) && ($row[csf('dia_width')]!="" && $row[csf('gauge')]!="")) 
		{ 
			//if($row[csf('machine_no')]=='GS') echo $row[csf('machine_no')].', ';
			$total_machine[$row[csf('id')]]=$row[csf('id')];
		}
	}
	//print_r($machine_in_not);
	$composition_arr=$construction_arr=array();
	$sql_deter="select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}

			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
			$yarn_type_arr[$row[csf('id')]]=$yarn_type[$row[csf('yarn_type')]];
		}
	}
	
	/*$get_booking_buyer = sql_select("select booking_no,buyer_id from wo_booking_mst where status_active=1 and is_deleted=0 union all select booking_no,buyer_id from wo_non_ord_samp_booking_mst where status_active = 1 and is_deleted = 0");
	foreach ($get_booking_buyer as $booking_row)
	{
		$booking_arr[$booking_row[csf("booking_no")]] = $buyer_arr[$booking_row[csf("buyer_id")]];
	}*/

	$con = connect();
    $r_id=execute_query("delete from tmp_prog_no where userid=$user_name");
    $r_id2=execute_query("delete from tmp_prod_id where userid=$user_name");
    oci_commit($con);

	$sql_inhouse=" SELECT A.ID, A.COMPANY_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.RECEIVE_BASIS, A.RECEIVE_DATE AS RECEIVE_DATE, A.RECEIVE_DATE AS LAST_PRODUCTION_DATE, A.BOOKING_NO, A.BOOKING_ID, A.REMARKS as KNITTING_REMARKS, F.REMARKS as PROGRAM_REMARKS, B.ID AS DTLS_ID, B.PROD_ID, B.FEBRIC_DESCRIPTION_ID, B.BODY_PART_ID, B.GSM, B.WIDTH, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.MACHINE_DIA, B.MACHINE_GG, B.FLOOR_ID, B.COLOR_ID, B.COLOR_RANGE_ID, C.PO_BREAKDOWN_ID, B.YARN_PROD_ID, E.ID AS FSO_ID, E.JOB_NO AS FSO_NO, E.SALES_BOOKING_NO, E.BUYER_ID, E.WITHIN_GROUP, E.PO_BUYER, E.BOOK_WITHOUT_ORDER, B.SHIFT_NAME, B.REJECT_FABRIC_RECEIVE AS REJECT_QTY, C.IS_SALES, C.QUANTITY, F.PROGRAM_QNTY, F.DISTRIBUTION_QNTY, F.MACHINE_ID, F.SAVE_DATA, F.STATUS, E.BOOKING_WITHOUT_ORDER, E.BOOKING_ID AS FSO_BOOKING_ID, E.BOOKING_ENTRY_FORM
	FROM PPL_PLANNING_INFO_ENTRY_MST D, PPL_PLANNING_INFO_ENTRY_DTLS F, INV_RECEIVE_MASTER A, PRO_GREY_PROD_ENTRY_DTLS B, ORDER_WISE_PRO_DETAILS C, FABRIC_SALES_ORDER_MST E 
	where d.id=f.mst_id and a.booking_id=f.id and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and a.entry_form=2 and c.is_sales=1 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.company_id=$company_name $knitting_source_cond $to_production_date_cond $sales_order_cond $machine_id_cond $floor_cond $Shift_cond ";
	//ORDER BY A.KNITTING_SOURCE, B.MACHINE_NO_ID, E.ID, A.ID DESC
	// echo $sql_inhouse;die;
	// UG-GPE-21-00041, UG-GPE-21-00062 //  and A.BOOKING_ID=11599
	$nameArray_inhouse = sql_select($sql_inhouse);
	$inhouse_arr = array();
	foreach ($nameArray_inhouse as $key => $row) 
	{
		if( $txt_date_from=="" || $txt_date_to=="" )
		{
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["LAST_PRODUCTION_DATE"] .= $row['LAST_PRODUCTION_DATE'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["WITHIN_GROUP"] = $row['WITHIN_GROUP'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["KNITTING_COMPANY"] = $row['KNITTING_COMPANY'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MACHINE_DIA"] .= $row['MACHINE_DIA'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MACHINE_GG"] .= $row['MACHINE_GG'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["SALES_BOOKING_NO"] = $row['SALES_BOOKING_NO'];			
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["FSO_NO"] = $row['FSO_NO'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BOOKING_WITHOUT_ORDER"] = $row['BOOKING_WITHOUT_ORDER'];			
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BOOKING_ENTRY_FORM"] = $row['BOOKING_ENTRY_FORM'];			
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BUYER_ID"] = $row['BUYER_ID'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["PO_BUYER"] = $row['PO_BUYER'];			
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BODY_PART_ID"] .= $row['BODY_PART_ID'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["YARN_COUNT"] .= $row['YARN_COUNT'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BRAND_ID"] .= $row['BRAND_ID'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["YARN_LOT"] .= $row['YARN_LOT'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["FEBRIC_DESCRIPTION_ID"] .= $row['FEBRIC_DESCRIPTION_ID'].',';//Construction, Composition
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["COLOR_ID"] .= $row['COLOR_ID'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["COLOR_RANGE_ID"] .= $row['COLOR_RANGE_ID'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["STITCH_LENGTH"] .= $row['STITCH_LENGTH'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["WIDTH"] .= $row['WIDTH'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["GSM"] .= $row['GSM'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["YARN_PROD_ID"] .= $row['YARN_PROD_ID'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["PROGRAM_QNTY"] = $row['PROGRAM_QNTY'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["DISTRIBUTION_QNTY"] += $row['DISTRIBUTION_QNTY'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MACHINE_ID"] = $row['MACHINE_ID'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["SAVE_DATA"] = $row['SAVE_DATA'];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["STATUS"] = $row['STATUS'];

			if ($row["SHIFT_NAME"]==0) 
			{
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["WITHOUT_SHIFT_QTY"] += $row["QUANTITY"];
			}
			elseif ($row["SHIFT_NAME"]==1) 
			{
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTA"] += $row["QUANTITY"];
			}
			elseif ($row["SHIFT_NAME"]==2) 
			{
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTB"] += $row["QUANTITY"];
			}
			elseif ($row["SHIFT_NAME"]==3) 
			{
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTC"] += $row["QUANTITY"];
			}		
			
			/*$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTB"] += $row["QNTYSHIFTB"];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTC"] += $row["QNTYSHIFTC"];*/
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["REJECT_QTY"] += $row["REJECT_QTY"];

			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["REMARKS"] .= $row['REMARKS'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["KNITTING_REMARKS"] .= $row['KNITTING_REMARKS'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["PROGRAM_REMARKS"] .= $row['PROGRAM_REMARKS'].',';
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MC_ID"] = $row["MACHINE_NO_ID"];
			$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["FSO_ID"] = $row["FSO_ID"];
			// UG-GPE-21-00062
		}
		else
		{
			if(strtotime($row["RECEIVE_DATE"]) >= strtotime($txt_date_from) && strtotime($row["RECEIVE_DATE"]) <= strtotime($txt_date_to))
			{
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["LAST_PRODUCTION_DATE"] .= $row['LAST_PRODUCTION_DATE'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["WITHIN_GROUP"] = $row['WITHIN_GROUP'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["KNITTING_COMPANY"] = $row['KNITTING_COMPANY'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MACHINE_DIA"] .= $row['MACHINE_DIA'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MACHINE_GG"] .= $row['MACHINE_GG'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["SALES_BOOKING_NO"] = $row['SALES_BOOKING_NO'];			
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["FSO_NO"] = $row['FSO_NO'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BOOKING_WITHOUT_ORDER"] = $row['BOOKING_WITHOUT_ORDER'];			
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BOOKING_ENTRY_FORM"] = $row['BOOKING_ENTRY_FORM'];			
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BUYER_ID"] = $row['BUYER_ID'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["PO_BUYER"] = $row['PO_BUYER'];			
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BODY_PART_ID"] .= $row['BODY_PART_ID'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["YARN_COUNT"] .= $row['YARN_COUNT'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["BRAND_ID"] .= $row['BRAND_ID'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["YARN_LOT"] .= $row['YARN_LOT'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["FEBRIC_DESCRIPTION_ID"] .= $row['FEBRIC_DESCRIPTION_ID'].',';//Construction, Composition
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["COLOR_ID"] .= $row['COLOR_ID'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["COLOR_RANGE_ID"] .= $row['COLOR_RANGE_ID'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["STITCH_LENGTH"] .= $row['STITCH_LENGTH'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["WIDTH"] .= $row['WIDTH'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["GSM"] .= $row['GSM'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["YARN_PROD_ID"] .= $row['YARN_PROD_ID'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["PROGRAM_QNTY"] = $row['PROGRAM_QNTY'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["DISTRIBUTION_QNTY"] += $row['DISTRIBUTION_QNTY'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MACHINE_ID"] = $row['MACHINE_ID'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["SAVE_DATA"] = $row['SAVE_DATA'];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["STATUS"] = $row['STATUS'];

				if ($row["SHIFT_NAME"]==0) 
				{
					$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["WITHOUT_SHIFT_QTY"] += $row["QUANTITY"];
				}
				elseif ($row["SHIFT_NAME"]==1) 
				{
					$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTA"] += $row["QUANTITY"];
				}
				elseif ($row["SHIFT_NAME"]==2) 
				{
					$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTB"] += $row["QUANTITY"];
				}
				elseif ($row["SHIFT_NAME"]==3) 
				{
					$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTC"] += $row["QUANTITY"];
				}		
				
				/*$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTB"] += $row["QNTYSHIFTB"];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTC"] += $row["QNTYSHIFTC"];*/
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["REJECT_QTY"] += $row["REJECT_QTY"];

				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["REMARKS"] .= $row['REMARKS'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["KNITTING_REMARKS"] .= $row['KNITTING_REMARKS'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["PROGRAM_REMARKS"] .= $row['PROGRAM_REMARKS'].',';
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["MC_ID"] = $row["MACHINE_NO_ID"];
				$inhouse_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["FSO_ID"] = $row["FSO_ID"];
				// UG-GPE-21-00062
			}
			else
			{
				if(strtotime($row["RECEIVE_DATE"]) < strtotime($txt_date_from))
				{
					if ($row["SHIFT_NAME"]==0) 
					{
						$opening_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["WITHOUT_SHIFT_QTY"] += $row["QUANTITY"];
					}
					elseif ($row["SHIFT_NAME"]==1) 
					{
						$opening_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTA"] += $row["QUANTITY"];
					}
					elseif ($row["SHIFT_NAME"]==2) 
					{					
						$opening_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTB"] += $row["QUANTITY"];
					}
					elseif ($row["SHIFT_NAME"]==3) 
					{
						$opening_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["QNTYSHIFTC"] += $row["QUANTITY"];
					}				
					$opening_arr[$row["KNITTING_SOURCE"]][$machine_arr[$row["MACHINE_NO_ID"]]][$row['FSO_ID']][$row['BOOKING_ID']]["REJECT_QTY"] += $row["REJECT_QTY"];
				}
			}
		}
		

		if ($row['WITHIN_GROUP']==1) 
		{
			$booking_id_arr[$row["FSO_BOOKING_ID"]]=$row["FSO_BOOKING_ID"];
		}
		// $program_arr[$row["BOOKING_ID"]]=$row["BOOKING_ID"];
		if( $program_check[$row["BOOKING_ID"]] == "" )
        {
            $program_check[$row["BOOKING_ID"]]=$row["BOOKING_ID"];
            $program_no = $row["BOOKING_ID"];
            // echo "insert into tmp_prog_no (userid, prog_no) values ($user_name,$program_no)";
            $r_id=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name,$program_no)");
        }
	}
	oci_commit($con);
	// echo "<pre>";print_r($opening_arr);die;
	// echo $sql_inhouse;die;//die;

    /*if(!empty($program_arr))
    {
        foreach ($program_arr as $row)
        {
            $r_id=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name,".$row.")");
            if($r_id) 
            {
                $r_id=1;
            } 
            else 
            {
                echo "insert into tmp_prog_no (userid, prog_no) values ($user_name,".$row.")";
                oci_rollback($con);
                disconnect($con);
                die;
            }
        }
    }*/
    // echo $sql_inhouse;die;

    // Yarn Requisition
	$yarn_requis_sql="SELECT A.PROD_ID, A.KNIT_ID, A.REQUISITION_NO, B.ID, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.YARN_TYPE, B.LOT, B.BRAND
	from ppl_yarn_requisition_entry a, tmp_prog_no c, product_details_master b
	where a.knit_id=c.prog_no and a.prod_id=b.id and c.userid=$user_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $yarn_requis_sql;die;
	 // and b.id in(195570,43567,154947)
	$yarn_requis_data = sql_select($yarn_requis_sql);	
	$yarn_req_data_arr = array();$yarn_brand_arr = array();$yarn_compos2="";$yarn_requ_check=array();
	foreach ($yarn_requis_data as $key => $val) 
	{
		$compos = '';
		if ($val['YARN_COMP_PERCENT2ND'] != 0) {
			$compos = $composition[$val['YARN_COMP_TYPE1ST']] . " " . $val['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$val['YARN_COMP_TYPE2ND']] . " " . $val['YARN_COMP_PERCENT2ND'] . "%";
		} else {
			$compos = $composition[$val['YARN_COMP_TYPE1ST']] . " " . $val['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$val['YARN_COMP_TYPE2ND']];
		}

		$yarn_req_data_arr[$val["KNIT_ID"]]["REQUISITION_NO"] .= $val["REQUISITION_NO"].',';
		$yarn_req_data_arr[$val['KNIT_ID']]['YARN_COUNT_ID'] .= $val['YARN_COUNT_ID'].',';
		$yarn_req_data_arr[$val['KNIT_ID']]['YARN_COMP'] .= $compos.',';
		$yarn_req_data_arr[$val['KNIT_ID']]['YARN_TYPE'] .= $val['YARN_TYPE'].',';
		$yarn_req_data_arr[$val['KNIT_ID']]['LOT'] .= $val['LOT'].',';
		$yarn_brand_arr[$val['ID']]['BRAND'] = $brand_details[$val['BRAND']];

		if( $yarn_requ_check[$val["PROD_ID"]] == "" )
        {
            $yarn_requ_check[$val["PROD_ID"]]=$val["PROD_ID"];
            $yarn_requisition_no = $val["PROD_ID"];
            // echo "insert into tmp_reqs_no (userid, reqs_no) values ($user_name,$yarn_requisition_no)";
            $r_id2=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_name,$yarn_requisition_no)");
        }
	}
	oci_commit($con);
	// echo $yarn_requis_sql;die;
	// Yarn Requisition Entry For Sales end

	// Yarn Issue Start
	$yarn_issue_sql="SELECT A.ISSUE_NUMBER, B.REQUISITION_NO, B.CONS_QUANTITY AS YARN_ISSUE_QTY, C.LOT 
	FROM inv_issue_master a, inv_transaction b, product_details_master c, tmp_prod_id d
	WHERE a.id=b.mst_id and b.prod_id=c.id and c.id=d.prod_id and a.item_category=1  and a.entry_form=3 and a.issue_basis=3 and a.company_id=$company_name and b.requisition_no<>0 and b.item_category=1 and c.item_category_id=1 and d.userid=$user_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; //  and b.requisition_no='9234'
	// echo $yarn_issue_sql;die;
	$yarn_issue_res = sql_select($yarn_issue_sql);	
	$yarn_issue_qty_arr = array();
	foreach ($yarn_issue_res as $key => $val)
	{
		$yarn_issue_qty_arr[$val["REQUISITION_NO"]][$val["LOT"]] += $val["YARN_ISSUE_QTY"];
	}
	// echo "<pre>";print_r($yarn_issue_qty_arr);
	// Yarn Issue End

	$r_id=execute_query("delete from tmp_prog_no where userid=$user_name");
	$r_id2=execute_query("delete from tmp_prod_id where userid=$user_name");
    oci_commit($con);
    disconnect($con);
    // echo "<pre>";print_r($yarn_brand_arr);echo "</pre>";
    // Yarn Issue End
	

	ob_start();
	$width=3250;
	?>
	<div>
	<table cellspacing="0"  width="<?= $width;?>">		
	    <tr class="form_caption">
	        <td colspan="12" align="center" style="border:none;font-size:20px; font-weight:bold">Program Wise Knitting Production Ledger<br>
	        </td>
	    </tr>
	    <tr class="form_caption">
	        <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"><? echo $company_arr[$company_name]; ?>
	        <br></b>
	        <?
			echo ($txt_date_from == '0000-00-00' || $txt_date_from == '' ? '' : ' From: '.change_date_format($txt_date_from));echo  ($txt_date_to == '0000-00-00' || $txt_date_to == '' ? '' : ' To: '.change_date_format($txt_date_to));
	        ?> </b>
	    	</td>
	    </tr>
    </table>

    <div style="width:<?= $width+20;?>px;">
    <table width="<?= $width+2;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" id="table_header_1">    
    <thead>
        <tr style="font-size:12px;">
        	<th width="40">Sl</th>
            <th width="50">WG (Yes/No)</th>
            <th width="100">Knitting Company</th>
            <th width="100">M/C No.</th>
            <th width="50">M/C Brand</th>
            <th width="60">M/C Dia & Gauge</th>
            <th width="80">Buyer</th>
            <th width="100">Booking No</th>
            <th width="100">Sales Order No</th>
            <th width="60">Program No</th>
            <th width="60">Program Priority No</th>
            <th width="80">Requisition No</th>
            <th width="60">Body Part</th>
            <th width="60">Booking Type</th>
            <th width="40">Yarn Count</th>
            <th width="120">Yarn Composition</th>
            <th width="70">Yarn Type</th>
            <th width="60">Lot</th>

            <th width="60">Brand</th>
            <th width="60">Yarn Issue Qty</th>
            <th width="80">Construction</th>
            <th width="100">Composition</th>
            <th width="60">Color</th>
            <th width="60">Color Range</th>
            <th width="60">Stich</th>
            <th width="60">Dia</th>
            <th width="70">GSM</th>
            <th width="60">M/C Capacity [Target]</th>
            <th width="60">Program Qty</th>
            <th width="80">M/C Wise Program Qty</th>
            <th width="80">Previous production Qty</th>
            <th width="80">A</th>
            <th width="80">B</th>
            <th width="80">C</th>
            <th width="80">Current production Qty</th>
            <th width="80">Total Production</th>
            <th width="80">Balance Qty</th>
            <th width="80">Reject Qty</th>
            <th width="80">Knitting Status</th>
            <th width="80">Program Status</th>
            <th width="80">Last Production Date</th>
            <th width="80">Hold Days</th>

            <th>Remarks</th>
        </tr>
    </thead>	
    </table>   
    </div>	
    <div style="width:<?= $width+19;?>px; max-height:350px; overflow-y:scroll; clear:both" id="scroll_body">	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width+2;?>" class="rpt_table" align="left" id="tbl_list_dtls">  
		<?
		// echo "<pre>";print_r($inhouse_arr);echo "<pre>";die;
        $i=1;
        $grand_tot_mc_wise_capacity=$grand_tot_program_qnty=$grand_tot_prog_mc_wise_qty=$grand_tot_previous_qty=$grand_tot_qntyshifta=$grand_tot_qntyshiftb=$grand_tot_qntyshiftc=$grand_tot_current_production_qty=$grand_tot_total_production=$grand_tot_balance=$grand_tot_reject_qty=0;
        $tot_booking_qnty=$tot_grey_qty=$tot_finish_qty=$tot_deliv_qty=$tot_stock_qty=0;
		
        //ksort($inhouse_arr);
		foreach ($inhouse_arr as $knit_source => $knit_sourceArr) 
		{
			?>
			<tr  bgcolor="#CCCCCC">
				<td colspan="43" align="left" ><b><?=$knitting_source[$knit_source];?></b></td>
			</tr>
			<?			
			$tot_mc_wise_capacity=$tot_program_qnty=$tot_prog_mc_wise_qty=$tot_previous_qty=$tot_qntyshifta=$tot_qntyshiftb=$tot_qntyshiftc=$tot_current_production_qty=$tot_total_production=$tot_balance=$tot_reject_qty=0;

			ksort($knit_sourceArr, SORT_NUMERIC);
			// echo "<pre>";print_r($knit_sourceArr); echo "</pre>";die;
			// array_multisort($knit_sourceArr,SORT_ASC);
			foreach ($knit_sourceArr as $mc_no => $mc_idArr) 
			{
				ksort($mc_idArr);
				foreach ($mc_idArr as $fso_id => $fso_idArr) 
				{
					//sort($fso_idArr);
					foreach ($fso_idArr as $program => $rows) 
					{
						$yarn_prod_id_arr=array_filter(array_unique(explode(",",chop($rows['YARN_PROD_ID'],","))));
						$yarn_brand_name = "";
						foreach ($yarn_prod_id_arr as $yProd)
                        {
				        	$yarn_brand_name .= $yarn_brand_arr[$yProd]['BRAND']."*";
                        }
                        $yarn_brand_name=implode(",",array_filter(array_unique(explode("*",$yarn_brand_name))));
                        // echo $yarn_brand_name.'<br>';

						/*$brand_arr=array_unique(explode(",",$rows['BRAND_ID']));
	                    $all_brand="";
	                    foreach($brand_arr as $id)
	                    {
	                        $all_brand.=$brand_details[$id].",";
	                    }
	                    $all_brand=chop($all_brand,",");*/
	                    // echo $all_brand;

	                    $description_arr=array_unique(explode(",",$rows['FEBRIC_DESCRIPTION_ID']));
	                    $all_construction="";
	                    foreach($description_arr as $id)
	                    {
	                        $all_construction.=$construction_arr[$id].",";
	                    }
	                    $all_construction=chop($all_construction,",");
	                    // echo $all_construction; 
	                    $all_composition="";
	                    foreach($description_arr as $id)
	                    {
	                        $all_composition.=$composition_arr[$id].",";
	                    }
	                    $all_composition=chop($all_composition,",");
	                    // echo $all_composition; 

	                    $color_arr=array_unique(explode(",",$rows['COLOR_ID']));
						$all_color="";
						foreach($color_arr as $id)
						{
							$all_color.=$color_details[$id].",";
						}
						$all_color=chop($all_color,",");
						// echo $all_color; 

	                    $color_range_arr=array_unique(explode(",",$rows['COLOR_RANGE_ID']));
	                    $all_color_range="";
	                    foreach($color_range_arr as $id)
	                    {
	                        $all_color_range.=$color_range[$id].",";
	                    }
	                    $all_color_range=chop($all_color_range,",");
	                    // echo $all_color_range;
	                    $stitch_length=implode(",",array_unique(explode(",",$rows['STITCH_LENGTH'])));
	                    $dia=implode(",",array_unique(explode(",",$rows['WIDTH'])));
	                    $gsm=implode(",",array_unique(explode(",",$rows['GSM'])));
	                    $machine_dia=implode(",",array_unique(explode(",",$rows['MACHINE_DIA'])));
	                    $machine_gg=implode(",",array_unique(explode(",",$rows['MACHINE_GG'])));

	                    $machine_brand = $machine_details[$rows['MC_ID']]['brand'];
						$body_part_ids=array_unique(explode(",",$rows['BODY_PART_ID']));
						$body_partName='';
						foreach($body_part_ids as $body_id)
						{
							if($body_partName=='') $body_partName=$body_part[$body_id]; else $body_partName.=",".$body_part[$body_id];
						}
						// echo $body_partName;

						$requisition_no=$yarn_req_data_arr[$program]["REQUISITION_NO"];
						$requisition_no=chop(implode(",",array_unique(explode(",",$requisition_no))),",");

						$all_count='';
						$yarn_count_id=$yarn_req_data_arr[$program]["YARN_COUNT_ID"];
						$yarn_count=array_unique(explode(",",$yarn_count_id));
						foreach($yarn_count as $count_id)
						{
							if($all_count=='') $all_count=$yarn_count_details[$count_id]; else $all_count.=",".$yarn_count_details[$count_id];
						}
						$all_count=chop($all_count,",");

						$all_yarn_comp='';
						$yarn_comp_name=$yarn_req_data_arr[$program]["YARN_COMP"];
						$yarn_comp_names=array_unique(explode(",",$yarn_comp_name));
						foreach($yarn_comp_names as $type_id)
						{
							if($all_yarn_comp=='') $all_yarn_comp=$type_id; else $all_yarn_comp.=",".$type_id;
						}
						$all_yarn_comp=chop($all_yarn_comp,",");


						$all_yarn_type='';
						$yarn_type_id=$yarn_req_data_arr[$program]["YARN_TYPE"];
						$yarn_type_ids=array_unique(explode(",",$yarn_type_id));
						foreach($yarn_type_ids as $type_id)
						{
							if($all_yarn_type=='') $all_yarn_type=$yarn_type[$type_id]; else $all_yarn_type.=",".$yarn_type[$type_id];
						}
						$all_yarn_type=chop($all_yarn_type,",");

						$yarn_lot=$yarn_req_data_arr[$program]["LOT"];
						$yarn_lot= chop(implode(",",array_unique(explode(",",$yarn_lot))),",");

						$yarn_issue_qty='';
						$requisition_no_arr=array_unique(explode(",",$requisition_no));
						$yarn_lot_name_arr=array_unique(explode(",",$yarn_lot));
						foreach ($requisition_no_arr as $key => $requi_no) 
						{
							foreach($yarn_lot_name_arr as $lot)
							{
								// echo $requi_no.'='.$lot.'<br>';
								// $yarn_issue_qty+=$yarn_issue_qty_arr[$requi_no][$lot];
								if($yarn_issue_qty=='') $yarn_issue_qty=number_format($yarn_issue_qty_arr[$requi_no][$lot],2,'.',''); else $yarn_issue_qty.=",".number_format($yarn_issue_qty_arr[$requi_no][$lot],2,'.','');
							}
						}
						$yarn_issue_qty=chop($yarn_issue_qty,",");
						// echo $yarn_issue_qty;
	                    // $buyer_id = ($rows["WITHIN_GROUP"]==1)?$booking_arr[$rows["SALES_BOOKING_NO"]]:$buyer_arr[$rows["BUYER_ID"]];
	                    $buyer_id = ($rows["WITHIN_GROUP"]==1)?$buyer_arr[$rows["PO_BUYER"]]:$buyer_arr[$rows["BUYER_ID"]];

						$booking_type_arr=array("118"=>"Main Fabric","108"=>"Partial","88"=>"Short Fabric","89"=>"Sample With Order","90"=>"Sample Without Order");
						$booking_type_string="";
						if ($rows["BOOKING_WITHOUT_ORDER"] == 0) 
						{
							if($rows['BOOKING_TYPE'] == 4)
	                        {
	                            $booking_type_string="Sample With Order";
	                        }
	                        else
	                        {
	                            $booking_type_string=$booking_type_arr[$rows['BOOKING_ENTRY_FORM']];
	                        }
						}
						else if ($rows["BOOKING_WITHOUT_ORDER"] == 1) 
						{
							$booking_type_string="Sample Without Order";
						}
						

						/*if ($rows["BOOKING_WITHOUT_ORDER"] != 1) 
						{
							$booking_no = $rows['SALES_BOOKING_NO'];
							$booking_type_id=$booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}				
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
						}
						else if ($rows["BOOKING_WITHOUT_ORDER"] == 1) 
						{
							$booking_type_string="Sample Without Order";
						}*/

						$save_str=implode(",",array_unique(explode(",",$rows['SAVE_DATA'])));
						$explSaveData = explode(",", $save_str);
						// echo "<pre>"; print_r($explSaveData).'<br>';
						$prog_mc_wise_capacity_arr=array();$prog_mc_wise_qty_arr=array();$program_mc_arr=array();$program_mc_id=0;//$program_mc_id2='';
						$prog_mc_arr = array();
						foreach ($explSaveData as $data_ref) 
						{
							$data_ref = explode("_", $data_ref);
							$program_mc_id=$data_ref[0];
							$prog_mc_wise_capacity_arr[$program_mc_id]=$data_ref[2];
							$prog_mc_wise_qty_arr[$program_mc_id]=$data_ref[3];
							$program_mc_arr[$program_mc_id]=$program_mc_id;
							// echo "<pre>"; print_r($data_ref).'<br>';
							//$program_mc_id2.=$data_ref[0].',';

							$prog_mc_arr[$data_ref[0]]=$data_ref[0];
						}
						// echo $program_mc_id2.'<br>';
						// echo "<pre>";print_r($prog_mc_arr).'<br>';
						// echo chop($program_mc_id2,',').'<br>';
						// echo "<pre>";print_r($program_mc_arr).'<br>';
						$previous_production_qty = $opening_arr[$knit_source][$mc_no][$fso_id][$program]["WITHOUT_SHIFT"]+$opening_arr[$knit_source][$mc_no][$fso_id][$program]["QNTYSHIFTA"]+$opening_arr[$knit_source][$mc_no][$fso_id][$program]["QNTYSHIFTB"]+$opening_arr[$knit_source][$mc_no][$fso_id][$program]["QNTYSHIFTC"]+$opening_arr[$knit_source][$mc_no][$fso_id][$program]["REJECT_QTY"];

						$current_production_qty=$rows['QNTYSHIFTA']+$rows['QNTYSHIFTB']+$rows['QNTYSHIFTC']+$rows['WITHOUT_SHIFT_QTY'];
						$total_production=$previous_production_qty+$current_production_qty;
						
						$balance=0;$title="";
						if ($knit_source==1) // In-house
						{
							$balance=$prog_mc_wise_qty_arr[$rows['MC_ID']]-($total_production+$rows['REJECT_QTY']);
							$title="M/C Wise Program Qty - (Total Production + Reject Qty)=".$prog_mc_wise_qty_arr[$rows['MC_ID']].'-('.$total_production.'+'.$rows['REJECT_QTY'].')';
						}
						else
						{
							$balance=$rows['PROGRAM_QNTY']-$total_production;
							$title="Program Qty - Total Production=".$rows['PROGRAM_QNTY'].'-'.$total_production;
						}
						

						$kniting_status = '&nbsp;';
						if ($total_production >= $rows['PROGRAM_QNTY']) // Complete
						$kniting_status = 'Complete';
						if($total_production < $rows['PROGRAM_QNTY']) // partial
						$kniting_status = 'Partial Complete';

						$program_status="";
						if($kniting_status != 'Complete' && $rows['STATUS'] == 0)
						{
							$program_status="";
						}
						else if($kniting_status != 'Complete' && $rows['STATUS'] == 1)
						{
							$program_status="Waiting";
						}
						else if($kniting_status != 'Complete' && $rows['STATUS'] == 2)
						{
							$program_status="Running";
						}
						else if($kniting_status != 'Complete' && $rows['STATUS'] == 3)
						{
							$program_status="Stop";
						}
						else if($kniting_status != 'Complete' && $rows['STATUS'] == 4)
						{
							$program_status="Closed";
						}
						else
						{
							$program_status="Complete";
						}

						//$all_production_date="07-SEP-21,13-SEP-21,20-SEP-21,";
						$all_production_date = chop($rows['LAST_PRODUCTION_DATE'],',');
						$all_production_date_arr=explode(',',$all_production_date);
						$last_production_date=date("Y-m-d",max(array_map('strtotime',$all_production_date_arr)));

						$knitting_remarks=chop(implode(",",array_unique(explode(",",$rows['KNITTING_REMARKS']))),",");
						$program_remarks=chop(implode(",",array_unique(explode(",",$rows['PROGRAM_REMARKS']))),",");
						// $remarks="";
						$remarks=$knitting_remarks;
		                if ($program_remarks !="" ) 
		                {
		                	$remarks.=','.$program_remarks;
		                }

						$hold_days = datediff("d",$last_production_date,date("Y-m-d"));

						if ((($cbo_knitting_status == 2 && $total_production >= $rows['PROGRAM_QNTY']) || ($cbo_knitting_status == 1 && $total_production < $rows['PROGRAM_QNTY']) || $cbo_knitting_status == 0) && (($cbo_get_upto == 1 && $balance > $txt_qty) || ($cbo_get_upto == 2 && $balance >= $txt_qty) || ($cbo_get_upto == 3 && $balance == $txt_qty) || $cbo_get_upto == 0))
						{
							// echo $program_mc_id.'<br>';
							$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
							if (($knit_source==1) && ( !in_array($rows['MC_ID'], $prog_mc_arr) )) 
							{
								$bgcolor = "#FF0000";
							}
							elseif ($last_production_date==date("Y-m-d")) 
							{
								$bgcolor = "#00FF00";
							}
							else
							{
								$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
							}

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?= $i; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">
								<td width="40" align="center" title="<? echo $last_production_date; ?>"><?= $i;?></td>
				                <td width="50" align="center"><?= $yes_no[$rows['WITHIN_GROUP']];?></td>
				                <td width="100" align="center"><p><?
				                if ($knit_source==1) 
                                {
                                    echo $company_arr[$rows['KNITTING_COMPANY']];
                                }
                                else
                                {
                                    echo $supplier_arr[$rows['KNITTING_COMPANY']];
                                }
                                ?></p></td>
				                <td width="100" align="center" title="Production MC ID: <? echo $rows['MC_ID'].', Program MC ID: '.$program_mc_id;?>"><p><?= $machine_arr[$rows['MC_ID']];?></p></td>
				                <td width="50" align="center"><p><?= $machine_brand;?></p></td>
				                <td width="60" align="center"><p><?= chop($machine_dia,",").'X'.chop($machine_gg,",");?></p></td>	
				                <td width="80" align="center"><p><?= $buyer_id;?></p></td>
				                <td width="100" align="center"><p><?= $rows['SALES_BOOKING_NO'];?></p></td>
				                <td width="100" align="center"><p><?= $rows['FSO_NO'];?></p></td>
				                <td width="60" align="center"><p><?= $program;?></p></td>
				                <td width="60" align="center"><p></p></td>
				                <td width="80" align="center"><p><?= $requisition_no;?></p></td>
				                <td width="60" align="center"><p><?= chop($body_partName,",");?></p></td>
				                <td width="60" align="center"><p><?= $booking_type_string;?></p></td>
				                <td width="40" align="center"><p><?= $all_count;?></p></td>
				                <td width="120" align="center"><p><?= $all_yarn_comp;?></p></td>
				                <td width="70" align="center"><p><?= $all_yarn_type; ?></p></td>
				                <td width="60" align="center"><p><?= $yarn_lot;?></p></td>

				                <td width="60" align="center"><p><?= $yarn_brand_name;?></p></td>
				                <td width="60" align="center"><p><?= $yarn_issue_qty;?></p></td>
				                <td width="80" align="center"><p><?= $all_construction;?></p></td>
				                <td width="100" align="center"><p><?= $all_composition;?></p></td>
				                <td width="60" align="center"><p><?= $all_color;?></p></td>
				                <td width="60" align="center"><p><?= $all_color_range;?></p></td>
				                <td width="60" align="center"><p><?= chop($stitch_length,",");?></p></td>
				                <td width="60" align="center"><p><?= chop($dia,",");?></p></td>
				                <td width="70" align="center"><p><?= chop($gsm,",");?></p></td>

				                <td width="60" align="right" title="program_mc_id: <?=$program_mc_id;?>"><p><?= number_format($prog_mc_wise_capacity_arr[$rows['MC_ID']],2,'.','');?></p></td>
				                <td width="60" align="right"><p><?= number_format($rows['PROGRAM_QNTY'],2,'.','');?></p></td>
				                <td width="80" align="right"><p><?= number_format($prog_mc_wise_qty_arr[$rows['MC_ID']],2,'.','');?></p></td>
				                <td width="80" align="right" title="MC ID: <? echo $rows['MC_ID'].', FSO ID: '.$rows['FSO_ID'].', Program: '.$program;?>"><p><?= number_format($previous_production_qty,2,'.','');?></p></td>
				                <td width="80" align="right"><p><?= number_format($rows['QNTYSHIFTA'],2,'.','');?></p></td>
				                <td width="80" align="right"><p><?= number_format($rows['QNTYSHIFTB'],2,'.','');?></p></td>
				                <td width="80" align="right"><p><?= number_format($rows['QNTYSHIFTC'],2,'.','');?></p></td>
				                <td width="80" align="right"><p><?= number_format($current_production_qty,2,'.','');?></p></td>
				                <td width="80" align="right" title="Previous production Qty + Current production Qty=<? echo $previous_production_qty.'+'.$current_production_qty; ?>"><p><?= number_format($total_production,2,'.','');?></p></td>
				                <td width="80" align="right" title="<? echo $title; ?>"><p><?= number_format($balance,2,'.','');?></p></td>
				                <td width="80" align="right"><p><?= number_format($rows['REJECT_QTY'],2,'.','');?></p></td>
				                <td width="80" align="center"><p><?= $kniting_status;?></p></td>
				                <td width="80" align="center"><p><?= $program_status;?></p></td>
				                <td width="80" align="center"><p><?= change_date_format($last_production_date); ?></p></td>
				                <td width="80" align="center"><p><?= $hold_days;?></p></td>
				                <td><p><?= $remarks; ?></p></td>
				            </tr>
							<?
							$tot_mc_wise_capacity+=$prog_mc_wise_capacity_arr[$rows['MC_ID']];
							$tot_program_qnty+=$rows['PROGRAM_QNTY'];
							$tot_prog_mc_wise_qty+=$prog_mc_wise_qty_arr[$rows['MC_ID']];
							$tot_previous_qty+=$previous_production_qty;
							$tot_qntyshifta+=$rows['QNTYSHIFTA'];
							$tot_qntyshiftb+=$rows['QNTYSHIFTB'];
							$tot_qntyshiftc+=$rows['QNTYSHIFTC'];
							$tot_current_production_qty+=$current_production_qty;
							$tot_total_production+=$total_production;
							$tot_balance+=$balance;
							$tot_reject_qty+=$rows['REJECT_QTY'];

							$grand_tot_mc_wise_capacity+=$prog_mc_wise_capacity_arr[$rows['MC_ID']];
							$grand_tot_program_qnty+=$rows['PROGRAM_QNTY'];
							$grand_tot_prog_mc_wise_qty+=$prog_mc_wise_qty_arr[$rows['MC_ID']];
							$grand_tot_previous_qty+=$previous_production_qty;
							$grand_tot_qntyshifta+=$rows['QNTYSHIFTA'];
							$grand_tot_qntyshiftb+=$rows['QNTYSHIFTB'];
							$grand_tot_qntyshiftc+=$rows['QNTYSHIFTC'];
							$grand_tot_current_production_qty+=$current_production_qty;
							$grand_tot_total_production+=$total_production;
							$grand_tot_balance+=$balance;
							$grand_tot_reject_qty+=$rows['REJECT_QTY'];
						    $i++;
						}
					}
				}
			}
			?>
			<tr class="tbl_bottom">
				<td width="40"></td>
				<td width="50"></td>
	            <td width="100"></td>
	            <td width="100"></td>
	            <td width="50"></td>
	            <td width="60"></td>
	            <td width="80"></td>
	            <td width="100"></td>
	            <td width="100"></td>
	            <td width="60"></td>
	            <td width="60"></td>
	            <td width="80"></td>
	            <td width="60"></td>
	            <td width="60"></td>
	            <td width="40"></td>            
	            <td width="120"></td>
	            <td width="70"></td>
	            <td width="60"></td>            
	            <td width="60"></td>            
	            <td width="60"></td>
	            <td width="80"></td>
	            <td width="100"></td>
	            <td width="60"></td>
	            <td width="60"></td>
	            <td width="60"></td>
	            <td width="60"></td>
	            <td width="70" align="right"><strong><?=$knitting_source[$knit_source];?> Total</strong></td>
	            <td width="60" align="right"><strong><?= number_format($tot_mc_wise_capacity,2,'.',''); ?></strong></td>
	            <td width="60" align="right"><strong><?= number_format($tot_program_qnty,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_prog_mc_wise_qty,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_previous_qty,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_qntyshifta,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_qntyshiftb,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_qntyshiftc,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_current_production_qty,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_total_production,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_balance,2,'.',''); ?></strong></td>
	            <td width="80" align="right"><strong><?= number_format($tot_reject_qty,2,'.',''); ?></strong></td>
	            <td width="80" align="right"></td>
	            <td width="80" align="right"></td>
	            <td width="80" align="right"></td>
	            <td width="80" align="right"></td>
	            <td></td>
			</tr>
			<?
		}
        ?>
    </table>
    </div>
    <!-- foot start -->
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width+2;?>" class="rpt_table" id="report_table_footer">
		<tfoot>
			<th width="40"></th>
			<th width="50"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="50"></th>
            <th width="60"></th>
            <th width="80"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="80"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="40"></th>            
            <th width="120"></th>
            <th width="70"></th>
            <th width="60"></th>            
            <th width="60"></th>            
            <th width="60"></th>
            <th width="80"></th>
            <th width="100"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="70" align="right">Grand Total</th>
            <th width="60" align="right"><strong><?= number_format($grand_tot_mc_wise_capacity,2,'.',''); ?></strong></th>
            <th width="60" align="right"><strong><?= number_format($grand_tot_program_qnty,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_prog_mc_wise_qty,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_previous_qty,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_qntyshifta,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_qntyshiftb,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_qntyshiftc,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_current_production_qty,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_total_production,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_balance,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_reject_qty,2,'.',''); ?></strong></th>
            <th width="80" align="right"></th>
            <th width="80" align="right"></th>
            <th width="80" align="right"></th>
            <th width="80" align="right"></th>
            <th></th>
		</tfoot>
	</table>
	<!-- foot End-->
	</div>
    
    <!-- Data show End- -->
    <?
	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html####$filename";
	
	exit();	
}

?>
      
 
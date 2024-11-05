
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0);
	exit();
}

if ($action == "load_drop_down_cust_buyer") 
{
    echo create_drop_down("cbo_cust_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", $selected, "", 0);
   
    exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
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
		<fieldset style="width:820px;margin-left:4px;">
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th> 
					</thead>
					<tr class="general">
						<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>   
						<td align="center">	
							<?
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>                 
						<td align="center">				
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'splitting_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>   
			</form>
		</fieldset>
	</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	$booking_arr = array();
	$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1");
	foreach ($booking_info as $row) {
		$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
		$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
		$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
		$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
		$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
		$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
	}
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC"; 

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>               
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll; float:left;" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search" align="left">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
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

if($action=="booking_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
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
			<fieldset style="width:820px;margin-left:4px;">
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Within Group</th>
							<th>Search By</th>
							<th>Search</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							</th> 
						</thead>
						<tr class="general">
							<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>   
							<td align="center">	
								<?
								$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", 2,$dd,0 );
								?>
							</td>                 
							<td align="center">				
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
							</td> 						
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_booking_no_search_list_view', 'search_div', 'splitting_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>   
				</form>
			</fieldset>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_no_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	$booking_arr = array();
	$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1");
	foreach ($booking_info as $row) {
		$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
		$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
		$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
		$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
		$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
		$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
	}
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC"; 

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>               
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll; float:left;" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search" align="left">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('sales_booking_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
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

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name    =str_replace("'","",$cbo_company_name);
	$cbo_buyer_name      =str_replace("'","",$cbo_buyer_name);
	$cbo_cust_buyer_name =str_replace("'","",$cbo_cust_buyer_name);
    $txt_booking_no      =str_replace("'","",$txt_booking_no);
	$txt_date_from       =str_replace("'","",$txt_date_from);
	$txt_date_to         =str_replace("'","",$txt_date_to);
	$txt_order           =str_replace("'","",$txt_order);
	$txt_order_id        =str_replace("'","",$txt_order_id);
	$cbo_order_type      =str_replace("'","",$cbo_order_type);
	$txt_barcode_no      =str_replace("'","",$txt_barcode_no);
	$cbo_knitting_source =str_replace("'","",$cbo_knitting_source);
	$txt_style_no 		 =str_replace("'","",$txt_style_no);

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from=change_date_format($txt_date_from,"","",1);
		$txt_date_to=change_date_format($txt_date_to,"","",1);
	}

	$str_cond="";
	if($txt_order_id) $str_cond .=" and a.id =$txt_order_id";
	if($txt_style_no) $str_cond .=" and a.style_ref_no =$txt_style_no";
	if($cbo_order_type==2) $str_cond .=" and a.booking_without_order =1";
	if($cbo_order_type==1) $str_cond .=" and a.booking_without_order !=1 and a.within_group=1";
    if($cbo_cust_buyer_name>0) $str_cona.=" and a.customer_buyer=$cbo_cust_buyer_name";

    if($txt_booking_no) $str_cond .=" and a.sales_booking_no in('$txt_booking_no')";
    // echo $str_cond;die;
	if($cbo_buyer_name>0)
	{
		$str_cond .= " and ((a.within_group = 2 and a.buyer_id = $cbo_buyer_name) or (a.within_group = 1 and a.po_buyer = $cbo_buyer_name)) ";
	}

	if($txt_barcode_no>0)
	{
		$str_cond .= " and ((c.barcode_no = $txt_barcode_no) or (d.barcode_no = $txt_barcode_no)) ";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		if($db_type==0)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and c.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and c.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
		}
	}

	$company_sql = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_sql as  $val) 
	{
		$company_array[$val[csf("id")]] = $val[csf("company_name")];
		$company_short_array[$val[csf("id")]] = $val[csf("company_short_name")];
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$con = connect();
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (112022,112023,112024)");
	execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll query
	| fso Roll_split
	|--------------------------------------------------------------------------
	|
	*/
	$roll_split_sql="SELECT a.job_no, a.sales_booking_no, a.style_ref_no, a.customer_buyer, a.buyer_id, c.system_number, c.barcode_no as mother_barcode, c.roll_wgt, c.order_id, c.insert_date, d.barcode_no, d.qnty, d.po_breakdown_id, d.entry_form
	from fabric_sales_order_mst a, pro_roll_split c, pro_roll_details d
	where a.id=c.order_id and a.id=d.po_breakdown_id and c.entry_form = 113 and c.split_from_id = d.roll_split_from
	and c.status_active = 1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and a.status_active = 1 and a.is_deleted=0 and a.company_id=$cbo_company_name $str_cond $date_cond and d.is_sales=1 order by c.system_number";
	// echo $roll_split_sql; die;
	$roll_split_sqlResult = sql_select($roll_split_sql);
	foreach($roll_split_sqlResult as $row) // Transfered barcode insert into tmp_barcode_no table
	{
		$barcodeArr[$row[csf("mother_barcode")]] =$row[csf("mother_barcode")];
		$barcodeArr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
	}
	// echo "<pre>";print_r($barcodeArr);die;
	// ============================== Roll wise Grey Sales Order To Sales Order Transfer End =================
	
	// ============================== $productionBarcodeData Start ===========================================
	//fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 112022, 1,$barcodeArr, $empty_arr);
	//oci_commit($con);

	$barcodeArr = array_filter($barcodeArr);
	if(!empty($barcodeArr))
	{
		foreach($barcodeArr as $barcode)
        {
            execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid) VALUES(".$barcode.", ".$user_id.")");
            oci_commit($con);
        }

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id 
		from inv_receive_master c, pro_grey_prod_entry_dtls a, pro_roll_details b, tmp_barcode_no d
		where a.mst_id = c.id and a.id=b.dtls_id and b.barcode_no=d.barcode_no and d.userid=$user_id and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1");
		foreach ($production_sql as $row) 
		{
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_basis"] =$row[csf("receive_basis")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
			$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
			$prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
			$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
			$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
			$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
			$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];

			$allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
			$allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 112023, 1,$allDeterArr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 112024, 2,$allColorArr, $empty_arr);
		oci_commit($con);
	}
	// ============================== $productionBarcodeData End ==================

	// ============================== Receive Data Array Start ====================
	$data_array = array();$poArr = array();
	foreach ($roll_split_sqlResult  as $val) 
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source ==0)
		{
			$febric_description_id=$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"];
			$color_id=$prodBarcodeData[$val[csf("barcode_no")]]["color_id"];
			if ($color_id=="") 
			{
				$color_id=0;
			}

			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["job_no"] = $val[csf("job_no")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["sales_booking_no"] = $val[csf("sales_booking_no")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["cust_buyer"] = $val[csf("customer_buyer")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["buyer_id"] = $val[csf("buyer_id")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["split_date"] = $val[csf("insert_date")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["split_date"] = $val[csf("insert_date")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["mother_barcode"] = $val[csf("mother_barcode")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["child_barcode"] = $val[csf("barcode_no")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["roll_wgt"] += $val[csf("roll_wgt")];
			$data_array[$val[csf("barcode_no")]][$val[csf("system_number")]][$febric_description_id][$color_id]["child_qnty"] += $val[csf("qnty")];
		}
	}
	// echo '<pre>';print_r($data_array);
	// ============================== Receive Data Array End =======================
	
	// =================== for yarn_count_determination Start ======================
	$allDeterArr = array_filter($allDeterArr);
	if(!empty($allDeterArr))
	{
		$construction_arr=array(); $composition_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, GBL_TEMP_ENGINE c where a.id=b.mst_id and a.id=c.ref_val and c.entry_form=112023 and c.user_id=$user_id and c.ref_from=1";
		$deter_array=sql_select($sql_deter);
		foreach( $deter_array as $row )
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];

			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
		unset($deter_array);
	}
	// =================== for yarn_count_determination end ========================

	// =================== for lib_color Start =====================================
	$allColorArr = array_filter($allColorArr);
	if(!empty($allColorArr))
	{
		$color_array=return_library_array( "SELECT b.id, b.color_name from GBL_TEMP_ENGINE a, lib_color b where b.status_active=1 and a.ref_val=b.id and a.entry_form=112024 and a.user_id=$user_id and a.ref_from=2 $allColorCond", "id", "color_name");
	}
	// =================== for lib_color end =======================================
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (112022,112023,112024)");
	execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	ob_start();
	?>
	<!-- <style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:1300px" id="main_body">
		<table width="1300" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold" >Splitting Summary Report [Before Issue]</td>
			</tr>
			<tr style="border:none;">
				<td colspan="7" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="1280" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">SPLIT Date</th>
					<th width="110">FSO</th>
					<th width="120">Sales Job/ Booking No.</th>
					<th width="100">Buyer</th>
					<th width="100">Cust Buyer</th>
					<th width="80">Color</th>
					<th width="100">Construction</th>
					<th width="110">Split ID</th>
					<th width="110">Split Barcode No.</th>
					<th width="80">Main Wgt</th>
					<th width="120">New Barcode No.</th>
					<th width="">New Qty</th>
					
				</tr>
			</thead>
		</table>
		<div style="width:1300px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="1280" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					foreach($data_array as $barcode_no_k => $barcode_no_v)
					{ 
						foreach($barcode_no_v as $system_no_k => $system_no_v)
						{ 
							foreach($system_no_v as $detar_id_k => $detar_id_v)
							{ 
								foreach($detar_id_v as $color_k => $row)
								{
									$system_no_count[$system_no_k]++;
								}
							}
						}
					}
					// echo "<pre>";print_r($system_no_count);
					$i=1;
					foreach($data_array as $barcode_no_k => $barcode_no_v)
					{
						foreach($barcode_no_v as $system_no_k => $system_no_v)	
						{
							foreach($system_no_v as $detar_id_k => $detar_id_v)
							{
								foreach($detar_id_v as $color_k => $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$system_no_span = $system_no_count[$system_no_k]++;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<?
										if(!in_array($system_no_k,$system_chk))
										{
											$system_chk[]=$system_no_k;
											?>
											<td width="30" rowspan="<? echo $system_no_span ;?>" valign="middle"><? echo $i;?></td>
											<td width="100" rowspan="<? echo $system_no_span ;?>" valign="middle"><p><? echo change_date_format($row["split_date"]);?></p></td>
			                                <td width="110" rowspan="<? echo $system_no_span ;?>" valign="middle"><p><? echo $row["job_no"];?></p></td>
			                                <td width="120" rowspan="<? echo $system_no_span ;?>" valign="middle"><p><? echo $row["sales_booking_no"];?></p></td>
			                                <td width="100" rowspan="<? echo $system_no_span ;?>" valign="middle"><? echo $buyer_arr[$row["buyer_id"]]; ?></td>
			                                <td width="100" rowspan="<? echo $system_no_span ;?>" valign="middle"><p><? echo $buyer_arr[$row["cust_buyer"]];?></p></td>
			                                <td width="80" rowspan="<? echo $system_no_span ;?>" valign="middle" title="<?echo $color_k;?>">
		                                		<p><? 
		                                		$color_names="";
		                                		foreach (explode(",",$color_k) as $key => $color) 
		                                		{
		                                			$color_names .= $color_array[$color].",";
		                                		}
		                                		echo chop($color_names,",");
		                                		?>
		                                		</p>
			                                </td>
			                                <td width="100" rowspan="<? echo $system_no_span ;?>" valign="middle" title="<?echo $detar_id_k;?>"><p><? echo $construction_arr[$detar_id_k];?></p></td>	                                
			                                <td width="110" rowspan="<? echo $system_no_span ;?>" valign="middle"><p><? echo $system_no_k; ?></p></td>
			                                <td width="110" rowspan="<? echo $system_no_span ;?>" valign="middle"><p><? echo $row["mother_barcode"]; ?></p></td>
			                                <td width="80" rowspan="<? echo $system_no_span ;?>" valign="middle" align="right"><p><? echo number_format($row["roll_wgt"],2,'.',''); $total_original_wgt_qnty += $row["roll_wgt"];?></p></td>
											<?
											$i++;
										}
										?>		                                
		                                <td width="120" align="center"><p><? echo $row["child_barcode"]; ?></p></td>
		                                <td width="" align="right"><p><? echo number_format($row["child_qnty"],2,'.',''); ?></p></td>
									</tr>
									<?
									$total_child_qty += $row["child_qnty"];
									
								}
							}
						}	
					}			
					?>
				</tbody>
			</table>
		</div>
		<table width="1280" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="100"></th>
					<th width="110"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="110"></th>
					<th width="110" align="right"><strong>Total</strong></th>
					<th width="80" align="right"><strong><? echo number_format($total_original_wgt_qnty,2,'.',''); ?></strong></th>
					<th width="120"></th>
					<th width="" align="right"><strong><? echo number_format($total_child_qty,2,'.',''); ?></strong></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	
	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$rptType";
	disconnect($con);
	exit();
}

?>

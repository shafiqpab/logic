
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

if ($action=="load_drop_down_location")
{
	//echo $data;die;
	echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );		
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'grey_fabric_transfer_report_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$sql = "SELECT id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, customer_buyer, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC"; 

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

				
				$buyer=$buyer_arr[$row[csf('customer_buyer')]];

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
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_booking_no_search_list_view', 'search_div', 'grey_fabric_transfer_report_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$sql = "SELECT id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, customer_buyer, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC"; 

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
				$buyer=$buyer_arr[$row[csf('customer_buyer')]];
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
	$cbo_cust_buyer_name =str_replace("'","",$cbo_cust_buyer_name);
    $txt_booking_no      =str_replace("'","",$txt_booking_no);
	$txt_date_from       =str_replace("'","",$txt_date_from);
	$txt_date_to         =str_replace("'","",$txt_date_to);
	$txt_order           =str_replace("'","",$txt_order);
	$txt_order_id        =str_replace("'","",$txt_order_id);
	$cbo_location_id 	 =str_replace("'","",$cbo_location_id);
	$txt_int_ref_no 	 =str_replace("'","",$txt_int_ref_no);
	$txt_barcode_no 	 =str_replace("'","",$txt_barcode_no);

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
    if($cbo_cust_buyer_name>0) $str_cona.=" and a.customer_buyer=$cbo_cust_buyer_name";

    if($txt_booking_no) $str_cond .=" and a.sales_booking_no in('$txt_booking_no')";
    if($txt_barcode_no!="") $barcode_cond =" and e.BARCODE_NO=$txt_barcode_no";

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$date_cond = " and f.transfer_date between '$txt_date_from' and '$txt_date_to' ";
	}

	if ($txt_int_ref_no != "") 
	{
		$po_sql = "SELECT A.ID as FSO_ID, C.GROUPING, B.BOOKING_NO from FABRIC_SALES_ORDER_MST a, wo_booking_dtls b, wo_po_break_down c
		where a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id and c.grouping='$txt_int_ref_no' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;
		$po_sql_result = sql_select($po_sql);
		if (empty($po_sql_result)) 
		{
			echo "Data Not Found";die;
		}
		$fso_id_cond = "";
		$fso_id_arr = array();
		foreach ($po_sql_result as $key => $row) 
		{
			$fso_id_arr[$row['FSO_ID']] = $row['FSO_ID'];
		}
		$fso_id_cond = " and a.id in(" . implode(",", $fso_id_arr).") ";
	}
	// echo $fso_id_cond.'==';die;

	if ($cbo_location_id!=0) 
	{
		$store_location_sql="SELECT a.ID, A.STORE_NAME, A.LOCATION_ID, A.STORE_LOCATION from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$cbo_company_name and b.category_type=13 and a.status_active=1 and a.is_deleted=0 and a.location_id=$cbo_location_id";
		// echo $store_location_sql;die;
		$store_location_sql_result=sql_select($store_location_sql);
		// echo "<pre>";print_r($store_sql_result);die;
		if(empty($store_location_sql_result))
		{
			echo "<p style='font-size:20px;color:red;', align='center'>Location Wise Store Not Found.<p/>";
			disconnect($con);
			die;		
		}
		$all_store_id_arr=array();
		foreach ($store_location_sql_result as $key => $rows) 
		{
			$all_store_id_arr[$rows['ID']]=$rows['ID'];
		}
		$all_store_id=implode(",", $all_store_id_arr);
		$store_id_cond=" and d.from_store in($all_store_id)";
	}

	$company_array=return_library_array( "select id, company_name, company_short_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0","id","yarn_count");
	$store_arr = return_library_array("select a.id, a.store_name from lib_store_location a where  a.status_active=1 order by a.store_name", 'id', 'store_name');

	$con = connect();
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (151)");
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=151");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll query
	| fso order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="SELECT A.JOB_NO, A.SALES_BOOKING_NO, A.CUSTOMER_BUYER, A.BUYER_ID, A.STYLE_REF_NO, A.COMPANY_ID, B.PROD_ID, B.PO_BREAKDOWN_ID, B.TRANS_TYPE, D.FROM_STORE, D.TO_STORE, E.QNTY, E.BARCODE_NO, F.TRANSFER_SYSTEM_ID as TRANSFER_NO, F.TRANSFER_DATE, F.FROM_ORDER_ID, F.TO_ORDER_ID, F.TRANSFER_CRITERIA, F.ID as TRANSFER_ID
	FROM fabric_sales_order_mst a, order_wise_pro_details b, inv_item_transfer_dtls d, pro_roll_details e, inv_item_transfer_mst f
	WHERE a.id=b.po_breakdown_id and b.dtls_id=d.id and d.id=e.dtls_id and e.mst_id=f.id and d.mst_id=f.id and b.status_active = 1 AND b.is_deleted = 0 AND b.entry_form IN(133) and e.entry_form=133 AND b.trans_type IN(6) AND d.status_active = 1 AND d.is_deleted = 0 and e.is_sales=1 AND a.company_id=$cbo_company_name $str_cond $date_cond $fso_id_cond $store_id_cond $barcode_cond";// and f.id=9641
	// echo $sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	$order_arr=array();
	foreach($sqlNoOfRollResult as $row) // Transfered barcode insert into tmp_barcode_no table
	{
		$transbarcodearr[$row["BARCODE_NO"]] =$row["BARCODE_NO"];
		$order_arr[$row["FROM_ORDER_ID"]] =$row["FROM_ORDER_ID"];
		$order_arr[$row["TO_ORDER_ID"]] =$row["TO_ORDER_ID"];
		$all_transfer_arr[$row["TRANSFER_ID"]]=$row["TRANSFER_ID"];

		if( $barcode_no_check[$row['BARCODE_NO']] =="" )
        {
            $barcode_no_check[$row['BARCODE_NO']]=$row['BARCODE_NO'];
            $barcodeno = $row['BARCODE_NO'];
            execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,151)");
        }
	}
	// echo "<pre>";print_r($order_arr);
	// ================= Roll wise Grey Sales Order To Sales Order Transfer End ===
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 151, 1,$order_arr, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 151, 4, $all_transfer_arr, $empty_arr);

	// ================== $productionBarcodeData Start ============================
	if(!empty($transbarcodearr))
	{
		$production_sql = sql_select("SELECT c.REMARKS,b.BARCODE_NO,A.COLOR_RANGE_ID,A.YARN_LOT, A.YARN_COUNT,B.PO_BREAKDOWN_ID,A.PROD_ID,B.BOOKING_NO, B.RECEIVE_BASIS, A.COLOR_ID, A.FEBRIC_DESCRIPTION_ID, A.GSM, A.WIDTH, A.STITCH_LENGTH, A.MACHINE_DIA, A.MACHINE_GG,A.MACHINE_NO_ID, C.KNITTING_SOURCE, C.CHALLAN_NO AS PRODUCTION_CHALLAN,C.KNITTING_COMPANY, A.YARN_PROD_ID, A.BODY_PART_ID, A.ORIGINAL_WIDTH
		from inv_receive_master c, pro_grey_prod_entry_dtls a, pro_roll_details b, tmp_barcode_no d
		where a.mst_id = c.id and a.id=b.dtls_id and b.barcode_no=d.barcode_no and d.userid=$user_id and d.entry_form=151 and c.entry_form = 2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1");
		foreach ($production_sql as $row) 
		{
			$prodBarcodeData[$row["BARCODE_NO"]]["remarks"] =$row["REMARKS"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prod_basis"] =$row["RECEIVE_BASIS"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prog_book"] =$row["BOOKING_NO"];
			$prodBarcodeData[$row["BARCODE_NO"]]["color_range_id"] =$row["COLOR_RANGE_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["yarn_lot"] =$row["YARN_LOT"];
			$prodBarcodeData[$row["BARCODE_NO"]]["yarn_count"] =$row["YARN_COUNT"];
			$prodBarcodeData[$row["BARCODE_NO"]]["yarn_prod_id"] =$row["YARN_PROD_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prod_id"] =$row["PROD_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["color_id"] =$row["COLOR_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["febric_description_id"] =$row["FEBRIC_DESCRIPTION_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["gsm"] =$row["GSM"];
			$prodBarcodeData[$row["BARCODE_NO"]]["width"] =$row["WIDTH"];
			$prodBarcodeData[$row["BARCODE_NO"]]["finish_dia"] =$row["ORIGINAL_WIDTH"];
			$prodBarcodeData[$row["BARCODE_NO"]]["stitch_length"] =$row["STITCH_LENGTH"];
			$prodBarcodeData[$row["BARCODE_NO"]]["machine_dia"] =$row["MACHINE_DIA"];
			$prodBarcodeData[$row["BARCODE_NO"]]["machine_gg"] =$row["MACHINE_GG"];
			$prodBarcodeData[$row["BARCODE_NO"]]["machine_no_id"] =$row["MACHINE_NO_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prod_challan"] =$row["PRODUCTION_CHALLAN"];
			$prodBarcodeData[$row["BARCODE_NO"]]["knitting_source"] =$row["KNITTING_SOURCE"];
			$prodBarcodeData[$row["BARCODE_NO"]]["knitting_company"] =$row["KNITTING_COMPANY"];
			$prodBarcodeData[$row["BARCODE_NO"]]["body_part_id"] =$row["BODY_PART_ID"];

			$allDeterArr[$row["FEBRIC_DESCRIPTION_ID"]] =$row["FEBRIC_DESCRIPTION_ID"];
			$allColorArr[$row["COLOR_ID"]] =$row["COLOR_ID"];
		}
		// echo "<pre>";print_r($prodBarcodeData);die;
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 151, 2,$allDeterArr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 151, 3,$allColorArr, $empty_arr);
	}
	// ============================== $productionBarcodeData End ==================

	// ============================== Receive Data Array Start ====================
	$data_array = array();$poArr = array();
	foreach ($sqlNoOfRollResult  as $val) 
	{
		$febric_description_id=$prodBarcodeData[$val["BARCODE_NO"]]["febric_description_id"];
		$yarn_lot=$prodBarcodeData[$val["BARCODE_NO"]]["yarn_lot"];
		$yarn_count=$prodBarcodeData[$val["BARCODE_NO"]]["yarn_count"];
		$gsm=$prodBarcodeData[$val["BARCODE_NO"]]["gsm"];
		$width=$prodBarcodeData[$val["BARCODE_NO"]]["width"];
		$finish_dia=$prodBarcodeData[$val["BARCODE_NO"]]["finish_dia"];
		$stitch_length=$prodBarcodeData[$val["BARCODE_NO"]]["stitch_length"];
		$machine_dia=$prodBarcodeData[$val["BARCODE_NO"]]["machine_dia"];
		$machine_gg=$prodBarcodeData[$val["BARCODE_NO"]]["machine_gg"];
		$mc_dia_gg=$machine_dia.'x'.$machine_gg;
		$color_id=$prodBarcodeData[$val["BARCODE_NO"]]["color_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}
		$remarks=$prodBarcodeData[$val["BARCODE_NO"]]["remarks"];

		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["COMPANY_ID"] = $val["COMPANY_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_ID"] = $val["TRANSFER_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_CRITERIA"] = $val["TRANSFER_CRITERIA"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["SALES_BOOKING_NO"] = $val["SALES_BOOKING_NO"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["CUST_BUYER"] = $val["CUSTOMER_BUYER"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_DATE"] = $val["TRANSFER_DATE"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_QTY"] +=  $val["QNTY"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FROM_ORDER_ID"] =  $val["FROM_ORDER_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TO_ORDER_ID"] = $val["TO_ORDER_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FROM_STORE"] =  $val["FROM_STORE"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TO_STORE"] =  $val["TO_STORE"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FABRIC_COLOR_ID"] = $color_id;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FEBRIC_DETER_ID"] = $febric_description_id;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["YARN_COUNT"] = $yarn_count;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["YARN_LOT"] = $yarn_lot;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["MC_DIA_GG"] = $mc_dia_gg;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["GSM"] = $gsm;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["WIDTH"] = $width;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FINISH_DIA"] = $finish_dia;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["STITCH_LENGTH"] = $stitch_length;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["REMARKS"] = $remarks;
	}
	// echo '<pre>';print_r($data_array);die;
	// ============================== Receive Data Array End ======================
	
	// =================== for yarn_count_determination Start =====================
	$allDeterArr = array_filter($allDeterArr);
	if(!empty($allDeterArr))
	{
		$construction_arr=array(); $composition_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, GBL_TEMP_ENGINE c where a.id=b.mst_id and a.id=c.ref_val and c.entry_form=151 and c.user_id=$user_id and c.ref_from=2";
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
	// =================== for yarn_count_determination end =======================

	// =================== for lib_color Start ====================================
	$allColorArr = array_filter($allColorArr);
	if(!empty($allColorArr))
	{
		$color_array=return_library_array( "SELECT b.id, b.color_name from GBL_TEMP_ENGINE a, lib_color b where b.status_active=1 and a.ref_val=b.id and a.entry_form=151 and a.user_id=$user_id and a.ref_from=3 $allColorCond", "id", "color_name");
	}
	// =================== for lib_color end ======================================

	// =================== For FSO Start ==========================================
	if (!empty($order_arr)) 
	{
		$po_sql = "SELECT A.ID as FSO_ID, C.GROUPING, B.BOOKING_NO from GBL_TEMP_ENGINE g, FABRIC_SALES_ORDER_MST a, wo_booking_dtls b, wo_po_break_down c
		where g.ref_val=a.id and g.entry_form=151 and g.user_id=$user_id and g.ref_from=1 and a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result = sql_select($po_sql);
		$int_ref_arr = array();
		foreach ($po_sql_result as $key => $row) 
		{
			$int_ref_arr[$row['FSO_ID']] = $row['GROUPING'];
		}
		// echo "<pre>";print_r($int_ref_arr);die;
	}	
	// =================== For FSO End ============================================

	// =================== For acknowledgement Start ==============================
	if (!empty($all_transfer_arr)) 
	{
		$ack_transfer_sql = sql_select("SELECT B.CHALLAN_ID, B.ID AS ACK_ID from GBL_TEMP_ENGINE a, inv_item_trans_acknowledgement b where a.ref_val=b.challan_id and a.user_id=$user_id and a.entry_form=151 and a.ref_from=4 and b.status_active=1");
		foreach ($ack_transfer_sql as $row) 
		{
			$transfer_ack_arr[$row["CHALLAN_ID"]]=$row["ACK_ID"];
		}
	}
	// =================== For acknowledgement End =================================
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (151)");
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=151");
	oci_commit($con);

	ob_start();
	$table_width=2330;
	$div_width=2350;
	?>
	<!-- <style type="text/css">
		.txt_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:<?=$div_width;?>px" id="main_body">
		<table width="<?=$div_width;?>" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold" >Order To Order Transfer Report Sales</td>
			</tr>
			<tr style="border:none;">
				<td colspan="7" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="<?=$table_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="100">Company / Location</th>
					<th width="80">Transfer Date</th>
					<th width="120">System Challan/ Transfer ID</th>
					<th width="100">Transfer Acknowledgement ID</th>
					<th width="100">Tranfer Criteria</th>
					<th width="100" title="From IR/IB">IR/IB</th>
					<th width="100">Buyer</th>
					<th width="100">Fabric Color</th>
					<th width="100">Count</th>
					<th width="100">Composition</th>
					<th width="100">Fab Type</th>
					<th width="100">Lot No.</th>
					<th width="100">Guage</th>
					<th width="100">Stitch Lenth</th>
					<th width="60">Grey Dia</th>
					<th width="60">Fin Dia</th>
					<th width="80">Quantity</th>
					<th width="100">Barcode No</th>
					<th width="100">From Store</th>
					<th width="100">To Store</th>
					<th width="100">From IR/IB</th>
					<th width="100">To IR/IB</th>
					<th width="">Remaks</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?=$div_width;?>px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="<?=$table_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1;
					foreach($data_array as $transfer_no => $transfer_v)
					{
						foreach($transfer_v as $barcode_no => $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								<td width="40" align="center"><?=$i;?></td>
                                <td width="100"><p><?=$company_array[$row["COMPANY_ID"]]; ?></p></td>
                                <td width="80"><p><?=change_date_format($row["TRANSFER_DATE"]); ?></p></td>
                                <td width="120" title="<?=$row["TRANSFER_ID"];?>"><p><?= $transfer_no; ?></p></td>                  
                                <td width="100"><p><?=$transfer_ack_arr[$row["TRANSFER_ID"]];?></p></td>
                                <td width="100"><p><?=$item_transfer_criteria[$row["TRANSFER_CRITERIA"]]; ?></p></td>
                                <td width="100"><p><?=$int_ref_arr[$row['FROM_ORDER_ID']];?></p></td>
                                <td width="100"><p><?=$buyer_arr[$row["CUST_BUYER"]];?></p></td>
                                <td width="100" title="<?=$row["FABRIC_COLOR_ID"];?>">
                            		<p><? 
                            		$color_names="";
                            		foreach (explode(",",$row["FABRIC_COLOR_ID"]) as $key => $color) 
                            		{
                            			$color_names .= $color_array[$color].",";
                            		}
                            		echo chop($color_names,",");
                            		?>
                            		</p>
                                </td>
                                <td width="100" title="<?=$row["YARN_COUNT"];?>">
                                	<p><? 
                            		$yarn_count="";
                            		foreach (explode(",",$row["YARN_COUNT"]) as $key => $COUNT) 
                            		{
                            			$yarn_count .= $yarn_count_arr[$COUNT].",";
                            		}
                            		echo chop($yarn_count,",");
                            		?>
                            		</p>
                            	</td>
                                <td width="100" title="<?=$row["FEBRIC_DETER_ID"];?>"><p><?=$composition_arr[$row["FEBRIC_DETER_ID"]];?></p></td>
                                <td width="100"><p><?=$construction_arr[$row["FEBRIC_DETER_ID"]];?></p></td>
                                <td width="100"><p><?=$row["YARN_LOT"];?></p></td>
                                <td width="100"><p><?=$row["MC_DIA_GG"];?></p></td>
                                <td width="100"><p><?=$row["STITCH_LENGTH"];?></p></td>
                                <td width="60"><p><?=$row["WIDTH"];?></p></td>
                                <td width="60"><p><?=$row["FINISH_DIA"];?></p></td>
                                <td width="80" align="right"><p><?=number_format($row["TRANSFER_QTY"],2,'.',''); ?></p></td>
                                <td width="100"><p><?=$barcode_no; ?></p></td>
                                <td width="100" title="<?=$row["FROM_STORE"];?>"><p><?=$store_arr[$row["FROM_STORE"]];?></p></td>
                                <td width="100"><p><?=$store_arr[$row["TO_STORE"]];?></p></td>
                                <td width="100"><p><?=$int_ref_arr[$row['FROM_ORDER_ID']];?></p></td>
                                <td width="100"><p><?=$int_ref_arr[$row['TO_ORDER_ID']];?></p></td>
                                <td width=""><? echo $row["REMARKS"]; ?></td>
							</tr>
							<?
							$total_trans_qnty += $row["TRANSFER_QTY"];
							$i++;
						}
					}				
					?>
				</tbody>
			</table>
		</div>
		<table width="<?=$table_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="60" align="right"><strong>Total</strong></th>
					<th width="80" align="right" id="value_total_transfer_qnty"><strong><? echo number_format($total_trans_qnty,2,'.',''); ?></strong></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width=""></th>
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

if($action=="generate_report2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name    =str_replace("'","",$cbo_company_name);
	$cbo_cust_buyer_name =str_replace("'","",$cbo_cust_buyer_name);
    $txt_booking_no      =str_replace("'","",$txt_booking_no);
	$txt_date_from       =str_replace("'","",$txt_date_from);
	$txt_date_to         =str_replace("'","",$txt_date_to);
	$txt_order           =str_replace("'","",$txt_order);
	$txt_order_id        =str_replace("'","",$txt_order_id);
	$cbo_location_id 	 =str_replace("'","",$cbo_location_id);
	$txt_int_ref_no 	 =str_replace("'","",$txt_int_ref_no);
	$txt_barcode_no 	 =str_replace("'","",$txt_barcode_no);

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
    if($cbo_cust_buyer_name>0) $str_cona.=" and a.customer_buyer=$cbo_cust_buyer_name";

    if($txt_booking_no) $str_cond .=" and a.sales_booking_no in('$txt_booking_no')";
    if($txt_barcode_no!="") $barcode_cond =" and e.BARCODE_NO=$txt_barcode_no";

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$date_cond = " and f.transfer_date between '$txt_date_from' and '$txt_date_to' ";
	}

	if ($txt_int_ref_no != "") 
	{
		$po_sql = "SELECT A.ID as FSO_ID, C.GROUPING, B.BOOKING_NO from FABRIC_SALES_ORDER_MST a, wo_booking_dtls b, wo_po_break_down c
		where a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id and c.grouping='$txt_int_ref_no' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;
		$po_sql_result = sql_select($po_sql);
		if (empty($po_sql_result)) 
		{
			echo "Data Not Found";die;
		}
		$fso_id_cond = "";
		$fso_id_arr = array();
		foreach ($po_sql_result as $key => $row) 
		{
			$fso_id_arr[$row['FSO_ID']] = $row['FSO_ID'];
		}
		$fso_id_cond = " and a.id in(" . implode(",", $fso_id_arr).") ";
	}
	// echo $fso_id_cond.'==';die;

	if ($cbo_location_id!=0) 
	{
		$store_location_sql="SELECT a.ID, A.STORE_NAME, A.LOCATION_ID, A.STORE_LOCATION from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$cbo_company_name and b.category_type=13 and a.status_active=1 and a.is_deleted=0 and a.location_id=$cbo_location_id";
		// echo $store_location_sql;die;
		$store_location_sql_result=sql_select($store_location_sql);
		// echo "<pre>";print_r($store_sql_result);die;
		if(empty($store_location_sql_result))
		{
			echo "<p style='font-size:20px;color:red;', align='center'>Location Wise Store Not Found.<p/>";
			disconnect($con);
			die;		
		}
		$all_store_id_arr=array();
		foreach ($store_location_sql_result as $key => $rows) 
		{
			$all_store_id_arr[$rows['ID']]=$rows['ID'];
		}
		$all_store_id=implode(",", $all_store_id_arr);
		$store_id_cond=" and d.from_store in($all_store_id)";
	}

	$company_array=return_library_array( "select id, company_name, company_short_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0","id","yarn_count");
	$store_arr = return_library_array("select a.id, a.store_name from lib_store_location a where  a.status_active=1 order by a.store_name", 'id', 'store_name');
	$color_arr = return_library_array("select a.id, a.color_name from lib_color a where a.status_active=1 order by a.color_name", 'id', 'color_name');
	$brand_arr = return_library_array("select a.id, a.brand_name from lib_brand a where a.status_active=1 order by a.brand_name", 'id', 'brand_name');

	$con = connect();
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (164)");
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=164");
	oci_commit($con);
	disconnect($con);

	/*
	|--------------------------------------------------------------------------
	| for roll query
	| fso order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="SELECT A.JOB_NO, A.SALES_BOOKING_NO, A.CUSTOMER_BUYER, A.BUYER_ID, A.STYLE_REF_NO, A.COMPANY_ID, B.PROD_ID, B.PO_BREAKDOWN_ID, B.TRANS_TYPE, D.FROM_STORE, D.TO_STORE, E.QNTY, E.BARCODE_NO, F.TRANSFER_SYSTEM_ID as TRANSFER_NO, F.TRANSFER_DATE, F.FROM_ORDER_ID, F.TO_ORDER_ID, F.TRANSFER_CRITERIA, F.ID as TRANSFER_ID, F.TO_COLOR_ID
	FROM fabric_sales_order_mst a, order_wise_pro_details b, inv_item_transfer_dtls d, pro_roll_details e, inv_item_transfer_mst f
	WHERE a.id=b.po_breakdown_id and b.dtls_id=d.id and d.id=e.dtls_id and e.mst_id=f.id and d.mst_id=f.id and b.status_active = 1 AND b.is_deleted = 0 AND b.entry_form IN(133) and e.entry_form=133 AND b.trans_type IN(6) AND d.status_active = 1 AND d.is_deleted = 0 and e.is_sales=1 AND a.company_id=$cbo_company_name $str_cond $date_cond $fso_id_cond $store_id_cond $barcode_cond"; 
	// echo $sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	$order_arr=array();
	foreach($sqlNoOfRollResult as $row) // Transfered barcode insert into tmp_barcode_no table
	{
		$transbarcodearr[$row["BARCODE_NO"]] =$row["BARCODE_NO"];
		$order_arr[$row["FROM_ORDER_ID"]] =$row["FROM_ORDER_ID"];
		$order_arr[$row["TO_ORDER_ID"]] =$row["TO_ORDER_ID"];
		$all_transfer_arr[$row["TRANSFER_ID"]]=$row["TRANSFER_ID"];

		if( $barcode_no_check[$row['BARCODE_NO']] =="" )
        {
            $barcode_no_check[$row['BARCODE_NO']]=$row['BARCODE_NO'];
            $barcodeno = $row['BARCODE_NO'];
            execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,164)");
        }
	}
	// echo "<pre>";print_r($order_arr);
	// ================= Roll wise Grey Sales Order To Sales Order Transfer End ===
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 164, 1,$order_arr, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 164, 4, $all_transfer_arr, $empty_arr);

	// ================== $productionBarcodeData Start ============================
	if(!empty($transbarcodearr))
	{
		$production_sql = sql_select("SELECT c.REMARKS,b.BARCODE_NO,A.COLOR_RANGE_ID,A.YARN_LOT, A.YARN_COUNT,B.PO_BREAKDOWN_ID,A.PROD_ID,B.BOOKING_NO, B.RECEIVE_BASIS, A.COLOR_ID, A.FEBRIC_DESCRIPTION_ID, A.GSM, A.WIDTH, A.STITCH_LENGTH, A.MACHINE_DIA, A.MACHINE_GG,A.MACHINE_NO_ID, C.KNITTING_SOURCE, C.CHALLAN_NO AS PRODUCTION_CHALLAN,C.KNITTING_COMPANY, A.YARN_PROD_ID, A.BODY_PART_ID, A.ORIGINAL_WIDTH, A.BRAND_ID
		from inv_receive_master c, pro_grey_prod_entry_dtls a, pro_roll_details b, tmp_barcode_no d
		where a.mst_id = c.id and a.id=b.dtls_id and b.barcode_no=d.barcode_no and d.userid=$user_id and d.entry_form=164 and c.entry_form = 2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1");
		foreach ($production_sql as $row) 
		{
			$prodBarcodeData[$row["BARCODE_NO"]]["remarks"] =$row["REMARKS"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prod_basis"] =$row["RECEIVE_BASIS"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prog_book"] =$row["BOOKING_NO"];
			$prodBarcodeData[$row["BARCODE_NO"]]["color_range_id"] =$row["COLOR_RANGE_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["yarn_lot"] =$row["YARN_LOT"];
			$prodBarcodeData[$row["BARCODE_NO"]]["yarn_count"] =$row["YARN_COUNT"];
			$prodBarcodeData[$row["BARCODE_NO"]]["yarn_prod_id"] =$row["YARN_PROD_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prod_id"] =$row["PROD_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["color_id"] =$row["COLOR_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["febric_description_id"] =$row["FEBRIC_DESCRIPTION_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["gsm"] =$row["GSM"];
			$prodBarcodeData[$row["BARCODE_NO"]]["width"] =$row["WIDTH"];
			$prodBarcodeData[$row["BARCODE_NO"]]["finish_dia"] =$row["ORIGINAL_WIDTH"];
			$prodBarcodeData[$row["BARCODE_NO"]]["stitch_length"] =$row["STITCH_LENGTH"];
			$prodBarcodeData[$row["BARCODE_NO"]]["machine_dia"] =$row["MACHINE_DIA"];
			$prodBarcodeData[$row["BARCODE_NO"]]["machine_gg"] =$row["MACHINE_GG"];
			$prodBarcodeData[$row["BARCODE_NO"]]["machine_no_id"] =$row["MACHINE_NO_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["prod_challan"] =$row["PRODUCTION_CHALLAN"];
			$prodBarcodeData[$row["BARCODE_NO"]]["knitting_source"] =$row["KNITTING_SOURCE"];
			$prodBarcodeData[$row["BARCODE_NO"]]["knitting_company"] =$row["KNITTING_COMPANY"];
			$prodBarcodeData[$row["BARCODE_NO"]]["body_part_id"] =$row["BODY_PART_ID"];
			$prodBarcodeData[$row["BARCODE_NO"]]["brand_id"] =$row["BRAND_ID"];

			$allDeterArr[$row["FEBRIC_DESCRIPTION_ID"]] =$row["FEBRIC_DESCRIPTION_ID"];
			$allColorArr[$row["COLOR_ID"]] =$row["COLOR_ID"];
		}
		// echo "<pre>";print_r($prodBarcodeData);die;
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 164, 2,$allDeterArr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 164, 3,$allColorArr, $empty_arr);
	}
	// ============================== $productionBarcodeData End ==================

	// ============================== Receive Data Array Start ====================
	$data_array = array();$poArr = array();$dataArrayNoOfRoll = array();
	foreach ($sqlNoOfRollResult  as $val) 
	{
		$febric_description_id=$prodBarcodeData[$val["BARCODE_NO"]]["febric_description_id"];
		$yarn_lot=$prodBarcodeData[$val["BARCODE_NO"]]["yarn_lot"];
		$yarn_count=$prodBarcodeData[$val["BARCODE_NO"]]["yarn_count"];
		$gsm=$prodBarcodeData[$val["BARCODE_NO"]]["gsm"];
		$width=$prodBarcodeData[$val["BARCODE_NO"]]["width"];
		$finish_dia=$prodBarcodeData[$val["BARCODE_NO"]]["finish_dia"];
		$stitch_length=$prodBarcodeData[$val["BARCODE_NO"]]["stitch_length"];
		$machine_dia=$prodBarcodeData[$val["BARCODE_NO"]]["machine_dia"];
		$machine_gg=$prodBarcodeData[$val["BARCODE_NO"]]["machine_gg"];
		$mc_dia_gg=$machine_dia.'x'.$machine_gg;
		$color_id=$prodBarcodeData[$val["BARCODE_NO"]]["color_id"];
		$color_range_id=$prodBarcodeData[$val["BARCODE_NO"]]["color_range_id"];
		$brand_id=$prodBarcodeData[$val["BARCODE_NO"]]["brand_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}
		$remarks=$prodBarcodeData[$val["BARCODE_NO"]]["remarks"];

		/* $data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["COMPANY_ID"] = $val["COMPANY_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_ID"] = $val["TRANSFER_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_CRITERIA"] = $val["TRANSFER_CRITERIA"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["SALES_BOOKING_NO"] = $val["SALES_BOOKING_NO"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["CUST_BUYER"] = $val["CUSTOMER_BUYER"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_DATE"] = $val["TRANSFER_DATE"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TRANSFER_QTY"] +=  $val["QNTY"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FROM_ORDER_ID"] =  $val["FROM_ORDER_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TO_ORDER_ID"] = $val["TO_ORDER_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FROM_STORE"] =  $val["FROM_STORE"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TO_STORE"] =  $val["TO_STORE"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FABRIC_COLOR_ID"] = $color_id;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["COLOR_RANGE_ID"] = $color_range_id;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FEBRIC_DETER_ID"] = $febric_description_id;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["YARN_COUNT"] = $yarn_count;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["YARN_LOT"] = $yarn_lot;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["MC_DIA_GG"] = $mc_dia_gg;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["GSM"] = $gsm;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["WIDTH"] = $width;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["FINISH_DIA"] = $finish_dia;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["STITCH_LENGTH"] = $stitch_length;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["REMARKS"] = $remarks;
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["TO_COLOR_ID"] = $val["TO_COLOR_ID"];
		$data_array[$val["TRANSFER_NO"]][$val["BARCODE_NO"]]["BRAND_ID"] = $brand_id; */
		$data_array[$val["TRANSFER_NO"]]["COMPANY_ID"] = $val["COMPANY_ID"];
		$data_array[$val["TRANSFER_NO"]]["TRANSFER_ID"] = $val["TRANSFER_ID"];
		$data_array[$val["TRANSFER_NO"]]["TRANSFER_CRITERIA"] = $val["TRANSFER_CRITERIA"];
		$data_array[$val["TRANSFER_NO"]]["SALES_BOOKING_NO"] = $val["SALES_BOOKING_NO"];
		$data_array[$val["TRANSFER_NO"]]["CUST_BUYER"] = $val["CUSTOMER_BUYER"];
		$data_array[$val["TRANSFER_NO"]]["TRANSFER_DATE"] = $val["TRANSFER_DATE"];
		$data_array[$val["TRANSFER_NO"]]["TRANSFER_QTY"] +=  $val["QNTY"];
		$data_array[$val["TRANSFER_NO"]]["FROM_ORDER_ID"] =  $val["FROM_ORDER_ID"];
		$data_array[$val["TRANSFER_NO"]]["TO_ORDER_ID"] = $val["TO_ORDER_ID"];
		$data_array[$val["TRANSFER_NO"]]["FROM_STORE"] =  $val["FROM_STORE"];
		$data_array[$val["TRANSFER_NO"]]["TO_STORE"] =  $val["TO_STORE"];
		$data_array[$val["TRANSFER_NO"]]["FABRIC_COLOR_ID"] .= $color_id.',';
		$data_array[$val["TRANSFER_NO"]]["COLOR_RANGE_ID"] .= $color_range_id.',';
		$data_array[$val["TRANSFER_NO"]]["FEBRIC_DETER_ID"] .= $febric_description_id.',';
		$data_array[$val["TRANSFER_NO"]]["YARN_COUNT"] .= $yarn_count.'__';
		$data_array[$val["TRANSFER_NO"]]["YARN_LOT"] .= $yarn_lot.',';
		$data_array[$val["TRANSFER_NO"]]["MC_DIA_GG"] .= $mc_dia_gg.',';
		$data_array[$val["TRANSFER_NO"]]["GSM"] .= $gsm.',';
		$data_array[$val["TRANSFER_NO"]]["WIDTH"] .= $width.',';
		$data_array[$val["TRANSFER_NO"]]["FINISH_DIA"] .= $finish_dia.',';
		$data_array[$val["TRANSFER_NO"]]["STITCH_LENGTH"] = $stitch_length.',';
		$data_array[$val["TRANSFER_NO"]]["REMARKS"] = $remarks;
		$data_array[$val["TRANSFER_NO"]]["TO_COLOR_ID"] = $val["TO_COLOR_ID"];
		$data_array[$val["TRANSFER_NO"]]["BRAND_ID"] .= $brand_id.',';
		$data_array[$val["TRANSFER_NO"]]["NO_OF_ROLL"]++;
	}
	//echo '<pre>';print_r($data_array);//die;
	// ============================== Receive Data Array End ======================
	
	// =================== for yarn_count_determination Start =====================
	$allDeterArr = array_filter($allDeterArr);
	if(!empty($allDeterArr))
	{
		$construction_arr=array(); $composition_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, GBL_TEMP_ENGINE c where a.id=b.mst_id and a.id=c.ref_val and c.entry_form=164 and c.user_id=$user_id and c.ref_from=2";
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
	// =================== for yarn_count_determination end =======================

	// =================== for lib_color Start ====================================
	$allColorArr = array_filter($allColorArr);
	if(!empty($allColorArr))
	{
		$color_array=return_library_array( "SELECT b.id, b.color_name from GBL_TEMP_ENGINE a, lib_color b where b.status_active=1 and a.ref_val=b.id and a.entry_form=164 and a.user_id=$user_id and a.ref_from=3 $allColorCond", "id", "color_name");
	}
	// =================== for lib_color end ======================================

	// =================== For FSO Start ==========================================
	if (!empty($order_arr)) 
	{
		$po_sql = "SELECT A.ID as FSO_ID, C.GROUPING, B.BOOKING_NO from GBL_TEMP_ENGINE g, fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c
		where g.ref_val=a.id and g.entry_form=164 and g.user_id=$user_id and g.ref_from=1 and a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result = sql_select($po_sql);
		$int_ref_arr = array();
		foreach ($po_sql_result as $key => $row) 
		{
			$int_ref_arr[$row['FSO_ID']] = $row['GROUPING'];
		}
		// echo "<pre>";print_r($int_ref_arr);die;
	}	
	// =================== For FSO End ============================================
	
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name =$cbo_company_name  and module_id=6 and report_id=283 and is_deleted=0 and status_active=1");
	$fReportId=explode(",",$print_report_format);
	$fReportId=$fReportId[0];

	if($fReportId==66){$actionName='grey_fabric_order_to_order_transfer_print_2';}
	else if($fReportId==85){$actionName='grey_fabric_order_to_order_transfer_print_3';}
	else if($fReportId==137){$actionName='grey_fabric_order_to_order_transfer_print_4';}
	
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (164)");
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=164");
	oci_commit($con);
	disconnect($con);
	
	ob_start();
	$table_width=2330;
	$div_width=2350;
	?>
	<!-- <style type="text/css">
		.txt_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:<?=$div_width;?>px" id="main_body">
		<table width="<?=$div_width;?>" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold" >Order To Order Transfer Report Sales</td>
			</tr>
			<tr style="border:none;">
				<td colspan="7" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="<?=$table_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="120">Company / Location</th>
					<th width="80">Transfer Date</th>
					<th width="120">System Challan/ Transfer ID</th>
					<th width="100">Tranfer Criteria</th>
					<th width="100">Buyer</th>
					<th width="100">From IR/IB</th>
					<th width="100">To IR/IB</th>
					<th width="100">To Color</th>
					<th width="150">Fabrication</th>
					<th width="60">GSM</th>
					<th width="100">Color Range</th>
					<th width="100">Lot No.</th>
					<th width="100">Count</th>
					<th width="80">Brand</th>
					<th width="60">Machine Dia</th>
					<th width="60">Machine Guage</th>
					<th width="80">Stitch Lenth</th>
					<th width="80">Fin Dia</th>
					<th width="80">No. of Roll </th>
					<th width="100">Quantity</th>
					<th width="100">From Store</th>
					<th width="100">To Store</th>
					<th width="">Remaks</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?=$div_width;?>px; overflow-y: scroll; max-height:450px; float: left;" id="scroll_body">
			<table width="<?=$table_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
				<tbody>
					<?
					
					
					$i=1;$total_trans_qnty=$total_no_of_roll=0;
					foreach($data_array as $transfer_no => $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$sys_challan = "<a href='##' style='color:#000' onclick=\"generate_sys_challan_report(" . $row[csf('TRANSFER_ID')] . ",'" . $actionName . "')\"><font style='font-weight:bold' $wo_color >" . $transfer_no . "</font></a>";

						$mc_dia_gg = implode(",",array_filter(array_unique(explode(",", $row["MC_DIA_GG"]))));
						$finish_dia = implode(",",array_filter(array_unique(explode(",", $row["FINISH_DIA"]))));
						$stitch_length = implode(",",array_filter(array_unique(explode(",", $row["STITCH_LENGTH"]))));
						$brand_id = implode(",",array_filter(array_unique(explode(",", $row["BRAND_ID"]))));
						$yarn_lot = implode(",",array_filter(array_unique(explode(",", $row["YARN_LOT"]))));
						$gsm = implode(",",array_filter(array_unique(explode(",", $row["GSM"]))));
						$febric_deter_ids = array_filter(array_unique(explode(",", $row["FEBRIC_DETER_ID"])));
						$yarn_count_ids = implode(",",array_filter(array_unique(explode("__", $row["YARN_COUNT"]))));

						?>
						<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
							<td width="40"><?=$i;?></td>
							<td width="120" align="center"><p><?=$company_array[$row["COMPANY_ID"]]; ?></p></td>
							<td width="80" align="center"><p><?=change_date_format($row["TRANSFER_DATE"]); ?></p></td>
							<td width="120" align="center" title="<?=$row["TRANSFER_ID"];?>"><p><?= $sys_challan; ?></p></td> 
							<td width="100" align="center"><p><?=$item_transfer_criteria[$row["TRANSFER_CRITERIA"]]; ?></p></td>
							<td width="100" align="center"><p><?=$buyer_arr[$row["CUST_BUYER"]];?></p></td>
							<td width="100" align="center"><p><?=$int_ref_arr[$row['FROM_ORDER_ID']];?></p></td>
							<td width="100" align="center"><p><?=$int_ref_arr[$row['TO_ORDER_ID']];?></p></td>
							<td width="100" align="center"><p><?=$color_arr[$row["TO_COLOR_ID"]];?></p></td>
							<td width="150" align="center">
								<p>
									<?
										$fabrication="";
										foreach ($febric_deter_ids as $row_deter) 
										{
											$fabrication .= $construction_arr[$row_deter].','.$composition_arr[$row_deter];
										}
										echo chop($fabrication,",");
									?>
								</p>
							</td>
							<td width="60" align="center"><p><?=$gsm;?></p></td>
							<td width="100" align="center"><p><?=$color_range[$row["COLOR_RANGE_ID"]];?></p></td>
							<td width="100" align="center"><p><?=$yarn_lot;?></p></td>
							<td width="100" align="center" title="<?=$yarn_count_ids;?>">
								<p><? 
								$yarn_count="";
								foreach (explode(",",$yarn_count_ids) as $key => $COUNT) 
								{
									$yarn_count .= $yarn_count_arr[$COUNT].",";
								}
								echo chop($yarn_count,",");
								?>
								</p>
							</td>
							<td width="80" align="center"><p><?=$brand_arr[$brand_id];?></p></td>
							<td width="60" align="center">
								<p>
									<?
									$mc_dia_gg = explode('x',$mc_dia_gg);
									echo $mc_dia_gg[0];
									?>
								</p>
							</td>
							<td width="60" align="center"><p><?=$mc_dia_gg[1];?></p></td>
							<td width="80" align="center"><p><?=$stitch_length;?></p></td>
							<td width="80" align="center"><p><?=$finish_dia;?></p></td>
							<td width="80" align="right"><p><?=$row["NO_OF_ROLL"];?></p></td>
							<td width="100" align="right"><p><?=number_format($row["TRANSFER_QTY"],2,'.',''); ?></p></td>
							<td width="100" align="center" title="<?=$row["FROM_STORE"];?>"><p><?=$store_arr[$row["FROM_STORE"]];?></p></td>
							<td width="100" align="center"><p><?=$store_arr[$row["TO_STORE"]];?></p></td>
							<td  align="center"><p><?=$row['REMARKS'];?></p></td>
						</tr>
						<?
						$total_no_of_roll += $row["NO_OF_ROLL"];
						$total_trans_qnty += $row["TRANSFER_QTY"];
						$i++;
					}				
					?>
				</tbody>
			</table>
		</div>
		<table width="<?=$table_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="40"><!--SL--></th> 
					<th width="120"><!--Company / Location--></th>
					<th width="80"><!--Transfer Date--></th>
					<th width="120"><!--System Challan/ Transfer ID--></th>
					<th width="100"><!--Tranfer Criteria--></th>
					<th width="100"><!--Buyer--></th>
					<th width="100"><!--From IR/IB--></th>
					<th width="100"><!--To IR/IB--></th>
					<th width="100"><!--To Color--></th>
					<th width="150"><!--Fabrication--></th>
					<th width="60"><!--GSM--></th>
					<th width="100"><!--Color Range--></th>
					<th width="100"><!--Lot No.--></th>
					<th width="100"><!--Count--></th>
					<th width="80"><!--Brand--></th>
					<th width="60"><!--Machine Dia--></th>
					<th width="60"><!--Machine Guage--></th>
					<th width="80"><!--Stitch Lenth--></th>
					<th width="80" align="right"><strong>Total:</strong></th>
					<th width="80" id="value_total_roll"><strong><? echo number_format($total_no_of_roll,2,'.',''); ?></strong></th>
					<th width="100" id="value_total_transfer_qnty"><strong><? echo number_format($total_trans_qnty,2,'.',''); ?></strong></th>
					<th width="100"><!--From Store--></th>
					<th width="100"><!--To Store--></th>
					<th width=""><!--Remaks--></th>
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

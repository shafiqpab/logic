<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if( $user_id == "" ) { header("location:login.php"); die; }
$permission = $_SESSION['page_permission'];

$data 	= $_REQUEST['data'];
$action = $_REQUEST['action'];

if($action == "load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action == "load_drop_down_store")
{
	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in(13)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_store_name','0','0','','0');\n";
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array, selected_name = new Array();

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>
							<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</th>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
}

if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#hide_booking_id").val(splitData[0]);
			$("#hide_booking_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:740px;">
				<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th width="170">Please Enter Booking No</th>
						<th>Booking Date</th>

						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
						<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
						<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
							</td>
							<td align="center">
								<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
								<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
							</td>

							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'store_wise_grey_fabric_stock_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  a.company_id='$data[0]'";
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

	$sql= "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a where $company $buyer $booking_no $booking_date and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 ";

	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit();
}

if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(b.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
		}
	}

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$cbo_company_id=trim(str_replace("'","",$cbo_company_id));
	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$hide_booking_id = trim(str_replace("'","",$txt_hide_booking_id));
	$txt_booking_no = trim(str_replace("'","",$txt_booking_no));
	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";

	if($hide_booking_id!="") $booking_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $booking_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$con = connect();
	$r_id=execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $booking_no_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	order by a.id";
	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$booking_ref 				 = $row[csf('buyer_name')]."*".$row[csf('job_no')]."*".$row[csf('booking_no')];
			$poIds 						.= $row[csf('id')].",";
			$poArr[$row[csf('id')]] 	 = $booking_ref;
			$po_booking[$row[csf('id')]] = $row[csf('booking_no')];

			$fileRefArr[$booking_ref] 	.= $row[csf('id')].",";
			$booking_order_arr[$row[csf('booking_no')]] = $row[csf('po_number')] . ", ";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$poIds=chop($poIds,','); $poIds_cond_roll="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
		}

		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
	}

	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr 			= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}

	$main_query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company,b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length,
	b.brand_id, b.machine_no_id,b.prod_id,  null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id
	from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.entry_form in(2,22,58) and a.id=e.mst_id and e.id=b.trans_id and b.id=c.dtls_id and b.trans_id<>0 and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $trans_date $poIds_cond_roll and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1
	and c.booking_without_order=0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company,d.detarmination_id as febric_description_id,d.gsm gsm,d.dia_width as width,
	null as color_id, null as color_range_id,
	null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id,b.to_prod_id prod_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, d.detarmination_id as febric_description_id, d.gsm gsm, d.dia_width as width, null as color_id, null as color_range_id,
	null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id,b.to_prod_id prod_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, d.detarmination_id as febric_description_id, d.gsm gsm, d.dia_width as width, null as color_id, null as color_range_id,
	null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id,b.to_prod_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll and c.booking_without_order=0 and a.status_active=1 and b.status_active=1 and c.status_active=1";

	$result = sql_select($main_query);
	if(!empty($result))
	{
		foreach ($result as $row) {
			if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
			{
				$barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
				$barcodeno = $row[csf('barcode_no')];
				// echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
				$r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)");
			}
			
			//$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}

		// $receive_barcodes = implode(",", $barcodeArr);
		// if($db_type==2 && count($barcodeArr)>999)
		// {
		// 	$barcode_chunk=array_chunk($barcodeArr,999) ;
		// 	$barcode_cond = " and (";
		// 	$barcode_cond2 = " and (";

		// 	foreach($barcode_chunk as $chunk_arr)
		// 	{
		// 		$barcode_cond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
		// 		$barcode_cond2.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
		// 	}

		// 	$barcode_cond = chop($barcode_cond,"or ");
		// 	$barcode_cond .=")";

		// 	$barcode_cond2 = chop($barcode_cond2,"or ");
		// 	$barcode_cond2 .=")";
		// }
		// else
		// {
		// 	$barcode_cond=" and b.barcode_no in($receive_barcodes)";
		// 	$barcode_cond2=" and c.barcode_no in($receive_barcodes)";
		// }

		// $production_sql = "select b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,a.brand_id,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form = 2 and b.entry_form in(2) and a.status_active=1 and b.status_active=1 $barcode_cond";

		$production_sql = "SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,a.brand_id,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, tmp_barcode_no d where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form = 2 and b.entry_form in(2) and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id";

		$production_info = sql_select($production_sql);
		foreach ($production_info as $row)
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
			$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
			$allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
			$allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
			$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
		}

		foreach ($result as $row) {
			$fabrication = $prodBarcodeData[$row[csf('barcode_no')]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["gsm"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["width"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["brand_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["color_id"];
			//echo "<br />";
			$dataArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication] += $row[csf('qnty')];
			$storeWiseArr[$po_booking[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]]["receive"] += $row[csf('qnty')];
			$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
			$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			$febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		}
	}

	/*echo "<pre>";
	print_r($storeWiseArr);
	echo "</pre>";*/


	$all_color_arr = array_filter($all_color_arr);
	if(!empty($all_color_arr))
	{
		$all_color_ids = implode(",", $all_color_arr);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arr)>999)
		{
			$all_color_chunk=array_chunk($all_color_arr,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$colorCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_color_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_cond=" and id in($all_color_ids)";
		}

		$colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
	}

	$febric_description_arr = array_filter($febric_description_arr);

	if(!empty($febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($febric_description_arr,999) ;
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}

		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}

	// $split_chk_sql = sql_select("select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1 $barcode_cond2");

	$split_chk_sql = sql_select("SELECT d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d, tmp_barcode_no e where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1 and d.barcode_no = e.barcode_no and e.userid= $user_id");

	if(!empty($split_chk_sql))
	{
		foreach ($split_chk_sql as $val)
		{
			$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
		}

		$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.barcode_no in (".implode(",", $split_barcode_arr).") and a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
		if(!empty($split_ref_sql))
		{
			foreach ($split_ref_sql as $value)
			{
				$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
			}
		}
	}

	$issue_sql = "SELECT c.po_breakdown_id, c.barcode_no, c.qnty,a.store_id
	from pro_roll_details c,inv_grey_fabric_issue_dtls b,inv_transaction a, tmp_barcode_no d
	where c.entry_form=61 and c.status_active=1 and c.is_deleted=0
	and c.barcode_no = d.barcode_no and d.userid= $user_id
	and c.booking_without_order = 0 and c.dtls_id=b.id and b.trans_id=a.id and a.transaction_type=2
	union all
	SELECT a.po_breakdown_id, c.barcode_no, c.qnty,b.from_store store_id
	from order_wise_pro_details a, inv_item_transfer_dtls b, pro_roll_details c, tmp_barcode_no d
	where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6
	and c.barcode_no = d.barcode_no and d.userid= $user_id and c.booking_without_order = 0
	union all
	SELECT b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, tmp_barcode_no d
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id
	and c.barcode_no = d.barcode_no and d.userid= $user_id
	and a.transfer_criteria in (1) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
	group by c.barcode_no, b.from_order_id,b.from_store
	union all
	SELECT a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, tmp_barcode_no d
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id
	and c.barcode_no = d.barcode_no and d.userid= $user_id
	and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 group by c.barcode_no, a.from_order_id,b.from_store";
	$issue_info=sql_select($issue_sql);
	foreach($issue_info as $row)
	{
		$fabrication = $prodBarcodeData[$row[csf('barcode_no')]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["gsm"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["width"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["brand_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["color_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{

			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["brand_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_id"];
		}

		$storeWiseArr[$po_booking[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]]["issue"] += $row[csf('qnty')];
	}
	unset($issue_info);

	$ref_file = ""; $data_prod = ""; $issue_return_barcode_arr = array();
	$iss_rtn_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty, a.store_id
		from pro_roll_details c,pro_grey_prod_entry_dtls b,inv_transaction a
		where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 and c.dtls_id=b.id and b.trans_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.transaction_type=4 $barcode_cond2");
	foreach($iss_rtn_qty_sql as $row)
	{
		$fabrication = $prodBarcodeData[$row[csf('barcode_no')]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["gsm"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["width"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["brand_id"] . "*" . $prodBarcodeData[$row[csf('barcode_no')]]["color_id"];
		$storeWiseArr[$po_booking[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]]["issue_return"] += $row[csf('qnty')];
	}
	unset($iss_rtn_qty_sql);

	$con = connect();
	$r_id=execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	/*echo "<pre>";
	print_r($storeWiseArr);
	echo "</pre>";*/

	$width = (1496+($num_of_store*110));
	?>
	<style>
		.word-break { word-break: break-all; }
	</style>
	<?ob_start()?>
	<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
		<table width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th width='30'>SL</th>
					<th width='110'>Booking No</th>
					<th width='110'>Order No.</th>
					<th width='110'>Buyer Name</th>
					<th width='116'>Construction</th>
					<th width='150'>Composition</th>
					<th width='80'>F/Dia</th>
					<th width='110'>S. Length</th>
					<th width='110'>Yarn Lot</th>
					<th width='110'>Brand</th>
					<th width='80'>Count</th>
					<th width='60'>GSM</th>
					<th width='110'>Color Range</th>
					<th width='110'>Color</th>
					<?
					foreach ($stores as $store) {
						?>
						<th width="110" title="<? echo $store[csf("id")];?>"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
					<th>Total Qnty.</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;">
			<table width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				if(!empty($dataArr))
				{
					$i=1;
					$total_stock=0;
					foreach ($dataArr as $booking_data=>$po_row) {
						$booking_data = explode("*", $booking_data);
						foreach ($po_row as $febric_description=>$row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$fabrication = explode("*", $febric_description);

							$yarn_counts_arr = explode(",", $fabrication[6]);

							$yarn_counts="";
							foreach ($yarn_counts_arr as $count) {
								$yarn_counts .= $count_arr[$count] . ",";
							}
							$yarn_counts = rtrim($yarn_counts, ", ");

							$color_arr = explode(",", $fabrication[8]);
							$colors="";
							foreach ($color_arr as $color) {
								$colors .= $colorArr[$color] . ",";
							}
							$colors = rtrim($colors, ", ");
							$order_no = rtrim( implode(",",array_unique(explode(",",$booking_order_arr[$booking_data[2]]))), ", ");
							?>
							<tr bgcor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width='30'><? echo $i;?></td>
								<td width='110'><? echo $booking_data[2];?></td>
								<td width='110'><? echo $order_no;?></td>
								<td width='110'><? echo $buyer_arr[$booking_data[0]];?></td>
								<td width='116' title="<? echo $fabrication[0];?>"><? echo $constuction_arr[$fabrication[0]];?></td>
								<td width='150'><p><? echo $composition_arr[$fabrication[0]];?></p></td>
								<td width='80'><? echo $fabrication[2];?></td>
								<td width='110' class="word-break" title="S. Length"><? echo $fabrication[4];?></td>
								<td width='110' class="word-break" title="Yarn Lot"><? echo $fabrication[5];?></td>
								<td width='110' class="word-break" title="Brand"><? echo $brand_arr[$fabrication[7]];?></td>
								<td width='80' class="word-break" title="Count"><? echo $yarn_counts;?></td>
								<td width='60' title="GSM"><? echo $fabrication[1];?></td>
								<td width='110' class="word-break" title="Color Range"><? echo $color_range[$fabrication[3]];?></td>
								<td width='110' class="word-break" title="Color"><p><? echo $colors;?></p></td>
								<?
								$total_receive=$total_issue=$total_issue_return=$store_wise_stock=$stock=0;
								foreach ($stores as $store) {
									$fabri_desc = $fabrication[0] . "*" . $fabrication[1] . "*" . $fabrication[2] . "*" . $fabrication[3] . "*" . $fabrication[4] . "*" . $fabrication[5] . "*" . $fabrication[6] . "*" . $fabrication[7] . "*" . $fabrication[8];
									//echo "<br />";
									$total_receive 		= number_format($storeWiseArr[$booking_data[2]][$fabri_desc][$store[csf("id")]]["receive"],2,".","");
									$total_issue   		= number_format($storeWiseArr[$booking_data[2]][$fabri_desc][$store[csf('id')]]["issue"],2,".","");
									$total_issue_return	= $storeWiseArr[$booking_data[2]][$fabri_desc][$store[csf('id')]]["issue_return"];
									$store_wise_stock 	= number_format(($total_receive+$total_issue_return) - $total_issue,2,".","");
									?>
									<td width="110" align="right" title="<? echo "Store=".$store[csf("id")].'**'.$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","")."\nTotal Issue Return=".number_format($total_issue_return,2,".","");?>" ><? echo ($store_wise_stock>0)?$store_wise_stock:(($total_receive>0 || $total_issue>0)?$store_wise_stock:"");?></td>
									<?
									$stock += $store_wise_stock;
									$store_wise_stock_total[$store[csf("id")]] += $store_wise_stock;
								}
								?>
								<td align="right"><? echo number_format($stock,2,".",""); ?></td>
							</tr>
							<?
							$total_stock += $stock;
							$i++;
						}
					}
				}
				?>
			</table>
		</div>
		<table width="<? echo $width+20; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
			<tfoot>
				<tr>
					<th width='30'></th>
					<th width='110'></th>
					<th width='110'></th>
					<th width='110'></th>
					<th width='116'></th>
					<th width='150'></th>
					<th width='80'></th>
					<th width='110' class="word-break" title="S. Length"></th>
					<th width='110' class="word-break" title="Yarn Lot"></th>
					<th width='110' class="word-break" title="Brand"></th>
					<th width='80' class="word-break" title="Count"></th>
					<th width='60' title="GSM"></th>
					<th width='110' class="word-break" title="Color Range"></th>
					<th width='110' class="word-break" title="Color"></th>
					<?
					foreach ($stores as $store) {
						?>
						<th width="110" align="right"><? echo number_format($store_wise_stock_total[$store[csf("id")]],2,".","");?></th>
						<?
					}
					?>
					<th align="right"><? echo number_format($total_stock,2,".",""); ?></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?
	

	foreach (glob("$user_id*.xls") as $filename) 
	{
		// if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	
	// echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
	exit;
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(b.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
		}
	}

	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$hide_booking_id = trim(str_replace("'","",$txt_hide_booking_id));
	$txt_booking_no = trim(str_replace("'","",$txt_booking_no));

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping LIKE '%".trim($txt_ref_no)."%'";

	if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	order by a.id";
	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$ref_file=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('file_no')]."_".$row[csf('booking_no')];
			$poIds.=$row[csf('id')].",";
			$poArr[$row[csf('id')]]=$ref_file;

			$fileRefArr[$ref_file].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);


	$poIds=chop($poIds,','); $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond.=" b.po_break_down_id in($ids) or ";
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$poIds_cond_delv.=" order_id in($ids) or ";
			$otot_po_cond.=" a.to_order_id in($ids) or ";
			$stst_po_cond.=" b.to_order_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$poIds_cond_delv=$poIds_cond_pre.chop($poIds_cond_delv,'or ').$poIds_cond_suff;
		$otot_po_cond=$poIds_cond_pre.chop($otot_po_cond,'or ').$poIds_cond_suff;
		$stst_po_cond=$poIds_cond_pre.chop($stst_po_cond,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond=" and b.po_break_down_id in($poIds)";
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$poIds_cond_delv=" and order_id in($poIds)";
		$otot_po_cond=" and a.to_order_id in($poIds)";
		$stst_po_cond=" and b.to_order_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	}

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );

	$grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $poIds_cond group by b.po_break_down_id", "po_id", "grey_req_qnty");

	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $poIds_cond_delv", "barcode_num", "grey_sys_id");

	$plan_arr=array();
	$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
	}
	unset($plan_data);


	$recvDtlsDataArr=array();

	$query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, null as from_trans_id, null as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0  $trans_date $poIds_cond_roll and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $otot_po_cond and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $stst_po_cond and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, null as receive_basis, null as booking_id, null as knitting_source, null as knitting_company, null as febric_description_id, null as gsm, null as width, null as color_id, null as color_range_id, null as yarn_lot, null as yarn_count, null as stitch_length, null as brand_id, null as machine_no_id, b.trans_id as from_trans_id, b.to_trans_id as to_trans_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $otot_po_cond and c.booking_without_order = 0";

	//echo $query;//die;
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr))
	{
		$ref_barcode_nos = implode(",", $ref_barcode_arr);
		$barCond = $ref_barcode_no_cond = "";
		if($db_type==2 && count($ref_barcode_arr)>999)
		{
			$ref_barcode_arr_chunk=array_chunk($ref_barcode_arr,999) ;
			foreach($ref_barcode_arr_chunk as $chunk_arr)
			{
				$barCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$ref_barcode_no_cond.=" and (".chop($barCond,'or ').")";

		}
		else
		{
			$ref_barcode_no_cond=" and c.barcode_no in($ref_barcode_nos)";
		}

		$split_chk_sql = sql_select("select d.barcode_no , d.qnty from pro_roll_split c , pro_roll_details d where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1 $ref_barcode_no_cond");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}


			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.barcode_no in (".implode(",", $split_barcode_arr).") and a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$recvDataArrTrans=array();$recvDataArr=array();
		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond";
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			$yarn_prod_ids = implode(",", $yarn_prod_id_arr);
			$yarnCond = $yarn_prod_id_cond = "";
			if($db_type==2 && count($yarn_prod_id_arr)>999)
			{
				$yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
				foreach($yarn_prod_id_arr_chunk as $chunk_arr)
				{
					$yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
			}
			else
			{
				$yarn_prod_id_cond=" and id in($yarn_prod_ids)";
			}

			$yarn_type_id_arr=  return_library_array("select id, yarn_type from product_details_master where status_active = 1 $yarn_prod_id_cond","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		}
		unset($recvDataT);
	}

	$all_color_arr = array_filter($all_color_arr);
	if(!empty($all_color_arr))
	{
		$all_color_ids = implode(",", $all_color_arr);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arr)>999)
		{
			$all_color_chunk=array_chunk($all_color_arr,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$colorCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_color_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_cond=" and id in($all_color_ids)";
		}
		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
	}

	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$ref_febric_description_arr = array_filter($ref_febric_description_arr);

	if(!empty($ref_febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $ref_febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($ref_febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($ref_febric_description_arr,999) ;
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}

	$iss_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order = 0
		union all
		select a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $poIds_cond_trans_roll and c.booking_without_order = 0
		union all
		select b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $ctct_po_cond and a.transfer_criteria  in (1) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
		group by c.barcode_no, b.from_order_id
		union all
		select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $otst_po_cond and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
		group by c.barcode_no, a.from_order_id ");

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();

	foreach($iss_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$knitting_company='';
			if($recvDataArrTrans[$mother_barcode_no]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($recvDataArrTrans[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$mother_barcode_no]["booking_id"]];
			}

			$data_prod=$recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_count"]."**".$recvDataArrTrans[$mother_barcode_no]["brand_id"]."**".$recvDataArrTrans[$mother_barcode_no]["yarn_lot"]."**".$recvDataArrTrans[$mother_barcode_no]["width"]."**".$recvDataArrTrans[$mother_barcode_no]["stitch_length"]."**".$recvDataArrTrans[$mother_barcode_no]["gsm"]."**".$recvDataArrTrans[$mother_barcode_no]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$mother_barcode_no]["yarn_prod_id"];
		}


		$iss_qty_arr[$ref_file][$data_prod] +=$row[csf("qnty")];

		$issue_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";

	}
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($iss_qty_arr);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll");
	foreach($iss_rtn_qty_sql as $row)
	{

		$machine_dia_gg='';

		if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
		}

		$knitting_company='';
		if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
		{
			$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}
		else //if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
		}

		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
		if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

		$data_prod=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]+=$row[csf("qnty")];

		$issue_return_barcode_arr[$ref_file][$data_prod] .= $row[csf('barcode_no')].",";
	}
	unset($iss_rtn_qty_sql);


	$ref_file="";$data_prod="";
	foreach($data_array as $row)
	{
		//if($row[csf("entry_form")]==83 && $row[csf("type")]==2)
		if( $row[csf("type")]==2)
		{
			$machine_dia_gg='';

			if($recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}

			$knitting_company='';
			if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]==1)
			{
				$knitting_company=$company_short_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}
			else //if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=="") $row[csf('brand_id')]=0; else $row[csf('brand_id')]=$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"];
			if($recvDataArrTrans[$row[csf('barcode_no')]]["width"]=="") $row[csf('width')]=0; else $row[csf('width')]=$recvDataArrTrans[$row[csf('barcode_no')]]["width"];

			$data=$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["width"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];


			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer
			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$receive_qnty =$row[csf("qnty")];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$receive_qnty;
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$receive_qnty +$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"].",";
			}

			if($recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]!="")
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"].",";
			}
			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";

		}
		else
		{
			$machine_dia_gg='';
			if($row[csf("entry_form")]==58)
			{
				/*$production_id=$delv_arr[$row[csf('barcode_no')]];
				$recv_data=explode("__",$recvDataArr[$production_id]);
				$receive_basis=$recv_data[0];
				$booking_id=$recv_data[1];

				if($receive_basis==2)
				{
					$machine_dia_gg=$plan_arr[$booking_id];
				}*/

				$machine_dia_gg= $plan_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]];
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$machine_dia_gg=$plan_arr[$row[csf("booking_id")]];
			}

			$knitting_company='';
			if($row[csf('knitting_source')]==1)
			{
				$knitting_company=$company_short_arr[$row[csf('knitting_company')]];
			}
			else if($row[csf('knitting_source')]==3)
			{
				$knitting_company=$supplier_arr[$row[csf('knitting_company')]];
			}

			$ref_file=$poArr[$row[csf('po_breakdown_id')]];

			if($row[csf('brand_id')]=="") $row[csf('brand_id')]=0;
			if($row[csf('width')]=="") $row[csf('width')]=0;

			$data=$row[csf('febric_description_id')]."**".$row[csf('yarn_count')]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('width')]."**".$row[csf('stitch_length')]."**".$row[csf('gsm')]."**".$row[csf('machine_no_id')]."**".$knitting_company."**".$machine_dia_gg."**".$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"];

			//$iss_qnty=$iss_qty_arr[$row[csf('barcode_no')]];

			$iss_qnty=$iss_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]]; //with transfer

			//$iss_qnty = $iss_qty_arr[$ref_file][$data];

			$recvDtlsDataArr[$ref_file][$data]['recv']+=$row[csf("qnty")];
			$recvDtlsDataArr[$ref_file][$data]['issue_return']+=$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];

			$recvDtlsDataArr[$ref_file][$data]['recv_total']+=$row[csf("qnty")]+$iss_rtn_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('barcode_no')]];
			$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;

			if($row[csf('color_range_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['range'].=$row[csf('color_range_id')].",";
			}

			if($row[csf('color_id')]>0)
			{
				$recvDtlsDataArr[$ref_file][$data]['color'].=$row[csf('color_id')].",";
			}

			$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
			$recvDtlsDataArr[$ref_file][$data]['type'].=$row[csf("type")].",";
		}
	}
	unset($data_array);



	ob_start();
	?>
	<fieldset style="width:2200px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<th width="40">SL</th>
				<th width="70">Buyer</th>
				<th width="90">Job No</th>
				<th width="100">Booking No</th>
				<th width="70">File No</th>
				<th width="80">Ref. No</th>
				<th width="80">Grey Fabric Qty(Kg)</th>
				<th width="110">Construction</th>
				<th width="105">Color</th>
				<th width="80">Color Range</th>
				<th width="85">Y-Count</th>
				<th width="100">Yarn Type</th>
				<th width="140">Yarn Composition</th>
				<th width="70">Brand</th>
				<th width="80">Yarn Lot</th>
				<th width="70">MC Dia and Gauge</th>
				<th width="60">F/Dia</th>
				<th width="60">S. Length</th>
				<th width="70">GSM</th>
				<th width="70">M/C NO.</th>
				<th width="70">Knitting Company</th>
				<th width="90">Receive Qty.</th>
				<th width="90">Issue Rtn. Qty.</th>
				<th width="90">Total Receive Qty.</th>
				<th width="90">Issue Qty.</th>
				<th>Stock Qty.</th>
			</thead>
		</table>
		<div style="width:2200px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2180" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_stock_qnty=0;
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					$fileRefData=explode("_",$fileRefArrData);
					$buyer_id=$fileRefData[0];
					$job_no=$fileRefData[1];
					$refNo=$fileRefData[2];
					$fileNo=$fileRefData[3];
					$bookingNo=$fileRefData[4];

					$grey_qnty=0;
					$poIds=chop($poIds,",");
					$poIdsArr=explode(",",$poIds);
					foreach($poIdsArr as $po_id)
					{
						$grey_qnty+=$grey_qnty_array[$po_id];
					}
					$z=1;
					foreach($recvDtlsDataArr[$fileRefArrData] as $data=>$value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$datas=explode("**",$data);
						$febric_description_id=$datas[0];
						$brand_name=$brand_arr[$datas[2]];
						$yarn_lot=$datas[3];
						$width=$datas[4];
						$stitch_length=$datas[5];
						$gsm=$datas[6];
						$machine_no=$machine_arr[$datas[7]];
						$knitting_company=$datas[8];
						$machine_dia_gg=$datas[9];
						$yarn_product_ids=$datas[10];

						$yarn_count='';
						$yarn_count_id=array_unique(explode(",",$datas[1]));
						foreach($yarn_count_id as $count_id)
						{
							if($count_id>0) $yarn_count.=$count_arr[$count_id].',';
						}
						$yarn_count=chop($yarn_count,",");

						$constuction=$constuction_arr[$febric_description_id];
						$composition=$composition_arr[$febric_description_id];
						$yarn_type_name=implode(",",array_unique(explode(",",chop($type_array[$febric_description_id],','))));

						$recv_qty_only=$value['recv'];
						$issue_return=$iss_rtn_qty_arr[$fileRefArrData][$data];
						$recv_qty=$recv_qty_only + $issue_return;

						//echo "[$fileRefArrData][$data]"."<br>";
						$iss_qty = $iss_qty_arr[$fileRefArrData][$data];
						$recv_qty = number_format($recv_qty,2,".","");
						$iss_qty = number_format($iss_qty,2,".","");
						$stock_qty=$recv_qty-$iss_qty;

						$colorRange='';
						$colorRangeIds=array_unique(explode(",",$value['range']));
						foreach($colorRangeIds as $range_id)
						{
							if($range_id>0) $colorRange.=$color_range[$range_id].',';
						}
						$colorRange=chop($colorRange,",");

						$color='';
						$colorIds=array_unique(explode(",",$value['color']));
						foreach($colorIds as $color_id)
						{
							if($color_id>0) $color.=$color_arr[$color_id].',';
						}
						$color=chop($color,",");

						$barcode_nos=chop($value['barcode_no'],",");
						$type=chop($value['type'],",");

						$yarn_type_id= "";
						foreach(explode(",", $yarn_product_ids) as $YarnProdId)
						{
							$yarn_type_id .= $yarn_type[$yarn_type_id_arr[$YarnProdId]].",";
						}

						$yarn_type_id = implode(",",array_filter(array_unique(explode(",", chop($yarn_type_id)))));

						$rcv_barcode_no_array = explode(",",chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],","));
						$issue_barcode_array = explode(",",chop($issue_barcode_arr[$fileRefArrData][$data],","));
						$issue_return_barcode_array = explode(",",chop($issue_return_barcode_arr[$fileRefArrData][$data],","));
						$rem_barcode_array = array_diff($rcv_barcode_no_array, $issue_barcode_array );
						/*if($i == 4){
							echo implode(",",$rcv_barcode_no_array);
							echo "<br>iss=";
							echo implode(",",$issue_barcode_array);
							echo "<br>rem=";
							echo implode(",",$rem_barcode_array);
						}*/
						$stock_barcode_array = array_merge($rem_barcode_array,$issue_return_barcode_array );


						//$dataP=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".$barcode_nos."_".$poIds;

						$dataP=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],",")."_".$poIds;


						$dataIss=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".chop($issue_barcode_arr[$fileRefArrData][$data],",")."_".$poIds;


						if($z==1)
						{
							$display_font_color="";
							$font_end="";
						}
						else
						{
							$display_font_color="<font style='display:none' color='$bgcolor'>";
							$font_end="</font>";
						}

						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="70"><p><? echo $display_font_color.$buyer_arr[$buyer_id].$font_end; ?>&nbsp;</p></td>
							<td width="90"><p><? echo $display_font_color.$job_no.$font_end; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $display_font_color.$bookingNo.$font_end; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $display_font_color.$fileNo.$font_end; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $display_font_color.$refNo.$font_end; ?>&nbsp;</p></td>
							<td width="80" align="right"><p><? echo $display_font_color; ?><a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $poIds; ?>')"><? echo number_format($grey_qnty,2,'.',''); ?></a><? echo $font_end; ?>&nbsp;</p></td>
							<td width="110"><p><? echo $constuction; ?>&nbsp;</p></td>
							<td width="105"><p><? echo $color; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $colorRange; ?>&nbsp;</p></td>
							<td width="85"><p><? echo $yarn_count; ?>&nbsp;</p></td>
							<td width="100" title="<? echo $yarn_product_ids;?>"><p><? echo $yarn_type_id;//$yarn_type_name; ?>&nbsp;</p></td>
							<td width="140"><p><? echo $composition; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $brand_name; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $machine_dia_gg; ?>&nbsp;</p></td>
							<td width="60"><p><? echo $width; ?>&nbsp;</p></td>
							<td width="60"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $machine_no; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $knitting_company; ?>&nbsp;</p></td>
							<td width="90" align="right" ><? echo number_format($recv_qty_only,2,'.',''); ?></td>
							<td width="90" align="right"><? echo number_format($issue_return,2,'.',''); ?></td>
							<td width="90" align="right" title="<? echo chop($recvDtlsDataArr[$fileRefArrData][$data]['barcode_no'],","); ?>"><a href="##" onClick="openpage('recv_popup','<? echo $dataP; ?>')"><? echo number_format($recv_qty,2,'.',''); ?></a></td>
							<td width="90" align="right" title="<? echo chop($issue_barcode_arr[$fileRefArrData][$data],","); ?>"><a href="##" onClick="openpage('iss_popup','<? echo $dataIss; ?>')"><? echo number_format($iss_qty,2,'.',''); ?></a></td>

							<td align="right" ><p><a href="##" onClick="openpage('stock_popup','<? echo $dataP; ?>')"><? echo number_format($stock_qty,2,'.',''); ?></a></p></td>
						</tr>
						<?
						$z++;
						$i++;
						$tot_recv_only+=$recv_qty_only;
						$tot_issue_rtn+=$issue_return;
						$tot_recv_qty+=$recv_qty;
						$tot_iss_qty+=$iss_qty;
						$tot_stock_qnty+=$stock_qty;
					}
				}
				?>
			</table>
		</div>
		<table width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<tfoot>
				<tr>
					<th width="40">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="105">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="85">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="140">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70" align="right"><b>Total</b></th>
					<th align="right" width="90" id="value_tot_recv_only"><? echo number_format($tot_recv_only,2,'.',''); ?></th>
					<th align="right" width="90" id="value_tot_iss_rtn"><? echo number_format($tot_issue_rtn,2,'.',''); ?></th>

					<th align="right" width="90" id="value_tot_recv"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_tot_iss"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
					<th align="right" style="padding-right:20px" id="value_tot_stock"><? echo number_format($tot_stock_qnty,2,'.',''); ?></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?

	$html = ob_get_contents();
	ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
    //---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
	exit();
}

if($action=="fabric_booking_popup")
{
	echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	?>
	<fieldset style="width:890px">
		<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			<thead>
				<th width="40">SL</th>
				<th width="60">Booking No</th>
				<th width="50">Year</th>
				<th width="60">Type</th>
				<th width="80">Booking Date</th>
				<th width="90">Color</th>
				<th width="110">Fabric</th>
				<th width="150">Composition</th>
				<th width="70">GSM</th>
				<th width="70">Dia</th>
				<th>Grey Req. Qty.</th>
			</thead>
		</table>
		<div style="width:100%; max-height:320px; overflow-y:scroll">
			<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
				<?
				if($db_type==0) $year_field="YEAR(a.insert_date) as year";
				else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
                else $year_field="";//defined Later

                $i=1; $tot_grey_qnty=0;
                $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, c.construction as samp_construction, c.composition as samp_composition, c.gsm_weight as samp_gsm, sum (b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width,c.construction,c.composition,c.gsm_weight order by a.id";
               //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
                	if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                	if($row[csf('booking_type')]==4)
                	{
                		$booking_type="Sample";
                	}
                	else
                	{
                		if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main";
                	}
                	?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                		<td width="40"><? echo $i; ?></td>
                		<td width="60">&nbsp;&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                		<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                		<td width="60" align="center"><p><? echo $booking_type; ?></p></td>
                		<td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
                		<td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
                		<?if($row[csf('booking_type')]==4){
                			?>
                			<td width="110"><p><? echo $row[csf('samp_construction')]; ?>&nbsp;</p></td>
                			<td width="150"><p><? echo $row[csf('samp_composition')]; ?>&nbsp;</p></td>
                			<td width="70"><p><? echo $row[csf('samp_gsm')]; ?>&nbsp;</p></td>
                			<?
                		}else{
                			?>
                			<td width="110"><p><? echo $row[csf('construction')]; ?>&nbsp;</p></td>
                			<td width="150"><p><? echo $row[csf('copmposition')]; ?>&nbsp;</p></td>
                			<td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
                			<?
                		}
                		?>
                		<td width="70"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
                		<td align="right" style="padding-right:5px"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
                	</tr>
                	<?
                	$tot_grey_qnty+=$row[csf('grey_fab_qnty')];
                	$i++;
                }
                ?>
                <tfoot>
                	<th colspan="10">Total</th>
                	<th style="padding-right:5px"><? echo number_format($tot_grey_qnty,2); ?></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}

if($action=="recv_popup")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);

	$barcode_nos=$data[16];
	$po_ids=$data[17];
	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_grey_qty"],
				col: [4],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			var tbl_list_search_1 = document.getElementById("tbl_list_search_1");
			var tbl_list_search_2 = document.getElementById("tbl_list_search_2");
			var tbl_list_search_3 = document.getElementById("tbl_list_search_3");
			if(tbl_list_search_1){
				setFilterGrid('tbl_list_search_1',-1,tableFilters);
			}
			if(tbl_list_search_2){
				setFilterGrid('tbl_list_search_2',-1,tableFilters);
			}
			if(tbl_list_search_3){
				setFilterGrid('tbl_list_search_3',-1,tableFilters);
			}
		});
	</script>
	<fieldset style="width:1190px">
		<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
				<th width="70">File No.</th>
				<th width="70">Ref. No.</th>
				<th width="80">Construction</th>
				<th width="80">Color Range</th>
				<th width="70">Y-Count</th>
				<th width="80">Yarn Type</th>
				<th width="120">Yarn Composition</th>
				<th width="70">Brand</th>
				<th width="70">Yarn Lot</th>
				<th width="70">MC Dia & Gauge</th>
				<th width="60">F/Dia</th>
				<th width="60">S. Length</th>
				<th width="60">GSM</th>
				<th width="60">M/C NO.</th>
				<th width="60">Knitting Company</th>
				<th>Stock Qty.</th>
			</thead>
			<tr bgcolor="#FFFFFF">
				<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
				<td align="right"><p><? echo number_format($data[15],2); ?>&nbsp;</p></td>
			</tr>
		</table>

		<table cellpadding="0" width="650" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="7">Receive Details</th></tr>
				<tr>
					<th width="40">SL</th>
					<th width="100">Purpose</th>
					<th width="100">Receive No</th>
					<th width="120">Store Name</th>
					<th width="100">Bacode No</th>
					<th width="80">Roll No</th>
					<th>Roll Weight</th>
				</tr>
			</thead>
		</table>
		<div style="width:650px; max-height:250px; overflow-y:scroll">
			<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
				<?
				$orderWiseData=array();
				$total_transfer=0;
				$i=0; $tot_grey_qnty=0; $y=0;

				$trans_store_arr=return_library_array("select c.barcode_no, s.store_name from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)  and  c.barcode_no not in(select barcode_no from pro_roll_details where barcode_no in($barcode_nos) and entry_form=82 and status_active=1 and is_deleted=0)
					union all
					select c.barcode_no, s.store_name  from inv_item_transfer_mst a, lib_store_location s, inv_item_transfer_dtls b, pro_roll_details c WHERE b.to_store=s.id and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)
					order by store_name, barcode_no","barcode_no","store_name");


				$sql="select a.recv_number as system_number, c.barcode_no, c.roll_no, c.qnty, 1 as type from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids)  and c.booking_without_order = 0
				union all
				select a.recv_number as system_number, c.barcode_no, c.roll_no, c.qnty, 2 as type from inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0
				union all
				select a.transfer_system_id as system_number, c.barcode_no, c.roll_no, c.qnty, 3 as type from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id and a.transfer_criteria in (1) and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0
				and c.barcode_no in($barcode_nos) and b.to_order_id in($po_ids) and c.booking_without_order = 0
				union all
				select a.transfer_system_id as system_number, c.barcode_no, c.roll_no, c.qnty, 3 as type
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				WHERE a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0
				and c.barcode_no in($barcode_nos) and a.to_order_id in($po_ids) and c.booking_without_order = 0
				order by barcode_no ";


				$tot_qnty=0;
				$result= sql_select($sql);

				foreach($result as $row)
				{
					if($row[csf('type')]==1)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="100"><p><? echo "Receive"; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $row[csf("system_number")]; ?></p></td>
							<td width="120"><p><? echo $trans_store_arr[$row[csf("barcode_no")]];//$row[csf('store_name')]; ?>&nbsp;</p></td>
							<td width="100" title="<? echo $row[csf('type')]; ?>"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						</tr>
						<?
						$tot_qnty+=$row[csf('qnty')];
						$y++;
					}
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="650" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="7">Issue Return Details</th></tr>
				<tr>
					<th width="40">SL</th>
					<th width="100">Purpose</th>
					<th width="100">Return No</th>
					<th width="120">Store Name</th>
					<th width="100">Bacode No</th>
					<th width="80">Roll No</th>
					<th>Roll Weight</th>
				</tr>
			</thead>
		</table>
		<div style="width:650px; max-height:250px; overflow-y:scroll">
			<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search_2">
				<?

				foreach($result as $row)
				{
					if($row[csf('type')] ==2)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="100"><p><? echo "Issue Return"; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $row[csf("system_number")]; ?></p></td>
							<td width="120"><p><? echo $trans_store_arr[$row[csf("barcode_no")]];//$row[csf('store_name')]; ?>&nbsp;</p></td>
							<td width="100" title="<? echo $row[csf('type')]; ?>"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						</tr>
						<?
						$tot_qnty+=$row[csf('qnty')];
						$y++;
					}
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="650" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="7">Transfer In Details</th></tr>
				<tr>
					<th width="40">SL</th>
					<th width="100">Purpose</th>
					<th width="100">Transfer No</th>
					<th width="120">Store Name</th>
					<th width="100">Bacode No</th>
					<th width="80">Roll No</th>
					<th>Roll Weight</th>
				</tr>
			</thead>
		</table>
		<div style="width:650px; max-height:250px; overflow-y:scroll">
			<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search_3">
				<?
				foreach($result as $row)
				{
					if($row[csf("type")] == 3)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="100"><p><? echo "Transfer"; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $row[csf("system_number")]; ?></p></td>
							<td width="120"><p><? echo $trans_store_arr[$row[csf("barcode_no")]];//$row[csf('store_name')]; ?>&nbsp;</p></td>
							<td width="100" title="<? echo $row[csf('type')]; ?>"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						</tr>
						<?
						$total_transfer+=$row[csf('qnty')];
						$y++;
					}
				}


				$trans_sql="select b.mst_id, c.barcode_no, c.roll_no, c.qnty, 4 as type
				from order_wise_pro_details a,  inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.to_trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and a.po_breakdown_id in($po_ids)";

				$trans_result=sql_select($trans_sql);
				foreach($trans_result as $row)
				{
					$i++;
					if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="100"><p><? echo "Transfer"; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row[csf("system_number")]; ?></p></td>
						<td width="120"><p><? echo $trans_store_arr[$row[csf("barcode_no")]]; ?>&nbsp;</p></td>
						<td width="100" title="<? echo $row[csf('type')]; ?>"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$total_transfer+=$row[csf('qnty')];
					$y++;
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="650" class="rpt_table" rules="all" border="1">
			<tfoot>
				<tr>
					<th colspan="5">Roll Total :</th>
					<th width="80" style="text-align:center"><? echo $y; ?></th>
					<th width="110"><? echo number_format($tot_qnty,2); ?></th>
				</tr>
				<tr>
					<th colspan="5"> Total Transfer:</th>
					<th width="80" style="text-align:center"><? //echo $i; ?></th>
					<th width="110"><? echo number_format($total_transfer,2); ?></th>
				</tr>
				<tr>
					<th colspan="5"> Grand Total</th>
					<th width="80" style="text-align:center"><? //echo $i; ?></th>
					<th width="110"><? echo number_format($tot_qnty+$total_transfer,2); ?></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?
	exit();
}

if($action=="iss_popup")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);

	$barcode_nos=$data[16];
	$po_ids=$data[17];
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
			setFilterGrid('tbl_list_search_1',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1190px">
		<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
				<th width="70">File No.</th>
				<th width="70">Ref. No.</th>
				<th width="80">Construction</th>
				<th width="80">Color Range</th>
				<th width="70">Y-Count</th>
				<th width="80">Yarn Type</th>
				<th width="120">Yarn Composition</th>
				<th width="70">Brand</th>
				<th width="70">Yarn Lot</th>
				<th width="70">MC Dia & Gauge</th>
				<th width="60">F/Dia</th>
				<th width="60">S. Length</th>
				<th width="60">GSM</th>
				<th width="60">M/C NO.</th>
				<th width="60">Knitting Company</th>
				<th>Stock Qty.</th>
			</thead>
			<tr bgcolor="#FFFFFF">
				<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
				<td align="right"><p><? echo $data[15]; ?>&nbsp;</p></td>
			</tr>
		</table>

		<table cellpadding="0" width="650" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="6">Issue Details</th></tr>
				<tr>
					<th width="40">SL</th>
					<th width="110">Issue Id</th>
					<th width="120">Issue Purpose </th>
					<th width="100">Barcode No</th>
					<th width="80">Total Roll</th>
					<th>Roll Weight</th>
				</tr>
			</thead>
		</table>
		<div style="width:650px; max-height:250px; overflow-y:scroll">
			<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search">
				<?
				$i=0; $tot_iss_qnty=0; $tot_roll=0;
				$sql="select a.id, a.issue_number_prefix_num, a.issue_purpose,c.barcode_no, count(c.id) as tot_roll, sum(c.qnty) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
				group by a.id, a.issue_number_prefix_num, a.issue_purpose,c.barcode_no
				order by id";
				$result= sql_select($sql);
				foreach($result as $row)
				{
					$i++;
					if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="110" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="80" align="center"><p><? echo $row[csf('tot_roll')]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$tot_iss_qnty+=$row[csf('qnty')];
					$tot_roll+=$row[csf('tot_roll')];
				}

				?>
			</table>
		</div>
		<table cellpadding="0" width="650" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="6">Transfer Details</th></tr>
				<tr>
					<th width="40">SL</th>
					<th width="90">Transfer Id</th>
					<th width="120">Purpose </th>
					<th width="100">Barcode No</th>
					<th width="80">Total Roll</th>
					<th>Roll Weight</th>
				</tr>
			</thead>
		</table>
		<div style="width:650px; max-height:250px; overflow-y:scroll">
			<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
				<?
				$trans_sql="select d.transfer_system_id, b.mst_id,c.barcode_no, count(c.id) as tot_roll, sum(c.qnty) as qnty
				from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c,inv_item_transfer_mst d  where a.trans_id=b.trans_id and b.id=c.dtls_id and b.mst_id = d.id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active =1 and b.status_active = 1 and c.barcode_no in($barcode_nos) and a.po_breakdown_id in($po_ids) group by d.transfer_system_id, b.mst_id ,c.barcode_no
				union all
				select a.transfer_system_id, b.mst_id,c.barcode_no, count(c.id) as tot_roll,sum(c.qnty) as qnty
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.barcode_no in($barcode_nos) and b.from_order_id in($po_ids) and a.transfer_criteria in (1) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
				group by a.transfer_system_id, b.mst_id ,c.barcode_no
				union all
				select a.transfer_system_id, b.mst_id,c.barcode_no, count(c.id) as tot_roll,sum(c.qnty) as qnty
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id
				and c.barcode_no in($barcode_nos)
				and a.from_order_id in($po_ids) and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
				group by a.transfer_system_id, b.mst_id ,c.barcode_no

				order by mst_id";
				$trans_result=sql_select($trans_sql);
				$i=0; $tot_trans_iss_qnty = 0;
				foreach($trans_result as $row)
				{
					$i++;
					if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="110" align="center"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo "Transfer"; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="80" align="center"><p><? echo $row[csf('tot_roll')]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$tot_trans_iss_qnty+=$row[csf('qnty')];
					$tot_roll+=$row[csf('tot_roll')];
				}
				$total_qnty = $tot_iss_qnty +  $tot_trans_iss_qnty;
				?>
			</table>
		</div>
		<table cellpadding="0" width="630" class="rpt_table" rules="all" border="1">
			<tfoot>
				<th colspan="4">Roll Total :</th>
				<th width="80" style="text-align:center"><? echo $tot_roll; ?></th>
				<th width="180" id="value_grey_qty"><? echo number_format($total_qnty,2); ?></th>
			</tfoot>
		</table>
	</fieldset>
	<?
	exit();
}


if($action=="stock_popup")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);

	$barcode_nos=$data[16];
	$po_ids=$data[17];


	$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where  a.entry_form = 61 and a.po_breakdown_id in($po_ids)  and a.roll_id = b.id and a.status_active =1 and b.status_active=1");

	if(!empty($split_ref_sql))
	{
		foreach ($split_ref_sql as $value)
		{
			$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
		}
	}

	$iss_sql = sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.is_returned = 0 and c.booking_without_order = 0
		union all
		select a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
		union all
		select b.from_order_id as po_breakdown_id,c.barcode_no, sum(c.qnty) as qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and b.from_order_id in($po_ids)  and a.transfer_criteria  in (1) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
		group by c.barcode_no, b.from_order_id
		union all
		select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id in($po_ids) and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos)
		group by c.barcode_no, a.from_order_id");

	foreach ($iss_sql as $val)
	{
		$iss_qty_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] += $val[csf("qnty")];
		if($mother_barcode_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] != "")
		{
			$iss_qty_arr[$mother_barcode_arr[$val[csf("barcode_no")]]][$val[csf("po_breakdown_id")]] += $val[csf("qnty")];
		}
	}

	$trans_store_arr=return_library_array("select c.barcode_no, s.store_name from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)  and  c.barcode_no not in(select barcode_no from pro_roll_details where barcode_no in($barcode_nos) and entry_form=82 and status_active=1 and is_deleted=0)
		union all
		select c.barcode_no, s.store_name  from inv_item_transfer_mst a, inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)
		order by store_name, barcode_no","barcode_no","store_name");
		?>
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [4],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1,tableFilters);
			});
		</script>
		<fieldset style="width:1190px">
			<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<th width="70">File No.</th>
					<th width="70">Ref. No.</th>
					<th width="80">Construction</th>
					<th width="80">Color Range</th>
					<th width="70">Y-Count</th>
					<th width="80">Yarn Type</th>
					<th width="120">Yarn Composition</th>
					<th width="70">Brand</th>
					<th width="70">Yarn Lot</th>
					<th width="70">MC Dia & Gauge</th>
					<th width="60">F/Dia</th>
					<th width="60">S. Length</th>
					<th width="60">GSM</th>
					<th width="60">M/C NO.</th>
					<th width="60">Knitting Company</th>
					<th>Stock Qty.</th>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
					<td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
					<td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
					<td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
					<td align="right"><p><? echo $data[15]; ?>&nbsp;</p></td>
				</tr>
			</table>

			<table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
				<thead>
					<th width="40">SL</th>
					<th width="120">Store Name</th>
					<th width="100">Bacode No</th>
					<th width="80">Roll No</th>
					<th>Roll Weight</th>
				</thead>
			</table>
			<div style="width:500px; max-height:250px; overflow-y:scroll">
				<table cellpadding="0" width="480" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_stock_qnty=0;


					$sql=" SELECT s.store_name, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, 1 as type from inv_receive_master a left join lib_store_location s on a.store_id=s.id, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0
					union all
					select s.store_name, c.barcode_no, c.roll_no, c.qnty, b.to_order_id as po_breakdown_id, 2 as type
					from inv_item_transfer_mst a,  inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c
					WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and b.to_order_id in($po_ids) and a.transfer_criteria = 1 and c.booking_without_order = 0
					order by store_name, barcode_no";

               	//echo $sql;//die;
					$result= sql_select($sql);
					foreach($result as $row)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

						$stock_qty=$row[csf('qnty')]-$iss_qty_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]];
						if($stock_qty>0)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="120"><p><? echo $trans_store_arr[$row[csf('barcode_no')]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($stock_qty,2); ?></td>
							</tr>
							<?
							$tot_stock_qnty+=$stock_qty;
						}

					}



					$trans_sql="select b.mst_id, c.barcode_no, c.roll_no, c.qnty, a.po_breakdown_id
					from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.to_trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and a.po_breakdown_id in($po_ids) and c.booking_without_order = 0";

					$trans_result=sql_select($trans_sql);
					foreach($trans_result as $row)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

						$stock_qty=$row[csf('qnty')]-$iss_qty_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]];
						if($stock_qty>0)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="120"><p><? echo $trans_store_arr[$row[csf('barcode_no')]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($stock_qty,2); ?></td>
							</tr>
							<?
							$tot_stock_qnty+=$stock_qty;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
				<tfoot>
					<th colspan="3">Roll Total :</th>
					<th width="80" style="text-align:center"><? echo $i; ?></th>
					<th width="134" id="value_grey_qty"><? echo number_format($tot_stock_qnty,2); ?></th>
				</tfoot>
			</table>
		</fieldset>
		<?
		exit();
	}

	?>


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

/*
	$composition_arr=array();
	$construction_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$construction_arr))
			{
				$construction_arr[$row[csf('id')]]=$construction_arr[$row[csf('id')]];
			}
			else
			{
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];
			}
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]];
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
*/

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

	if ($action=="load_drop_down_store")
	{
		echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data and a.status_active=1 and a.is_deleted=0 and b.category_type=13 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
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
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.",4=>"IR/IB.");
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'grey_store_recv_issue_summary_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
		if($search_by==1) {$search_field_cond=" and job_no like '%".$search_string."'";}
		else if($search_by==2) {$search_field_cond=" and sales_booking_no like '%".$search_string."'";}
		else if($search_by==3) {$search_field_cond=" and style_ref_no like '%".$search_string."'";}
		//else $search_field_cond=" and style_ref_no like '".$search_string."%'";
		else if($search_by==4)
		{
			$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b, FABRIC_SALES_ORDER_MST c
			where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID  and a.grouping='$search_string' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			// echo $po_sql;die;
			$po_sql_result=sql_select($po_sql);
			$refBooking_cond="";
			foreach ($po_sql_result as $key => $row)
			{
				//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
				$bookingNo_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
			}
			//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
			$refBooking_cond=" and sales_booking_no in('".implode("','",$bookingNo_arr)."') ";
			//echo $refBooking_cond;die;
		}

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
	$sql = "SELECT id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond $refBooking_cond
	order by id DESC";

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


if($action=="generate_report_receive")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name    =str_replace("'","",$cbo_company_name);
	$cbo_buyer_name      =str_replace("'","",$cbo_buyer_name);
	$cbo_cust_buyer_name =str_replace("'","",$cbo_cust_buyer_name);
    $txt_booking_no      =str_replace("'","",$txt_booking_no);
	$cbo_store_name      =str_replace("'","",$cbo_store_name);
	$txt_date_from       =str_replace("'","",$txt_date_from);
	$txt_date_to         =str_replace("'","",$txt_date_to);
	$txt_order           =str_replace("'","",$txt_order);
	$txt_order_id        =str_replace("'","",$txt_order_id);
	$cbo_order_type      =str_replace("'","",$cbo_order_type);
	$cbo_based_on        =str_replace("'","",$cbo_based_on);
	$cbo_knitting_source =str_replace("'","",$cbo_knitting_source);


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
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($cbo_store_name>0) $str_cond.=" and c.store_id=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and e.id =$txt_order_id";
	if($cbo_order_type==2) $str_cond .=" and e.booking_without_order =1";
	if($cbo_order_type==1) $str_cond .=" and e.booking_without_order !=1 and e.within_group=1";
    if($cbo_cust_buyer_name>0) $str_cond.=" and e.customer_buyer=$cbo_cust_buyer_name";

    if($txt_booking_no) $str_cond .=" and e.sales_booking_no like '%" . $txt_booking_no . "'";

	if($cbo_buyer_name>0)
	{
		$str_cond .= " and ((e.within_group = 2 and e.buyer_id = $cbo_buyer_name) or (e.within_group = 1 and e.po_buyer = $cbo_buyer_name)) ";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		if($cbo_based_on == 1)
		{
			$date_cond_rcv = " and a.receive_date between '$txt_date_from' and '$txt_date_to' ";
			$date_cond_trans = " and a.transfer_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_rcv="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond_rcv="";
			}else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_rcv="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond_rcv="";
			}
			$date_cond_trans=$date_cond_rcv;
		}
	}

	$machine_array=return_library_array( "select id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
	$user_array=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$count_array=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$company_sql = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_sql as  $val)
	{
		$company_array[$val[csf("id")]] = $val[csf("company_name")];
		$company_short_array[$val[csf("id")]] = $val[csf("company_short_name")];
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select id,short_name from lib_supplier where status_active =1",'id','short_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$from_fso_arr=return_library_array( "select id, sales_booking_no from fabric_sales_order_mst",'id','sales_booking_no');



    $receive_sql = "SELECT a.id,a.receive_date, a.recv_number,a.booking_id as delivery_challan_id,a.booking_no as delivery_challan, a.knitting_source, a.knitting_company, a.challan_no, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.po_breakdown_id, a.inserted_by,a.insert_date,d.barcode_no, d.roll_no, d.qnty as rcv_qnty, e.within_group, e.job_no, e.sales_booking_no,e.buyer_id,e.po_job_no, e.po_buyer,e.season_id,e.delivery_date, e.booking_type,e.booking_without_order, e.booking_entry_form,e.booking_id,e.style_ref_no,a.remarks,e.sales_booking_no,e.customer_buyer,d.entry_form,a.location_id,a.knitting_location_id FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c, pro_roll_details d, fabric_sales_order_mst e WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and d.po_breakdown_id = e.id and d.is_sales=1 and d.entry_form in(58,84)  and a.entry_form in(58,84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 $date_cond_rcv $str_cond ORDER BY a.receive_date";
    //echo $receive_sql;
    $receive_sql_arr=sql_select( $receive_sql);

	$bookingType="";
	foreach ($receive_sql_arr as  $val)
	{
		$rcvTransBarcodeArr[$val[csf("barcode_no")]] =$val[csf("barcode_no")];
		if($val[csf('booking_type')] == 4)
		{
			if($val[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType =  "Sample With Order";
			}
		}
		else
		{
			$bookingType =  $booking_type_arr[$val[csf('booking_entry_form')]];
		}
		$salesData[$val[csf("sales_booking_no")]]['booking_type'] = $bookingType;

		if($val[csf('booking_without_order')] !=1 &&  $val[csf('within_group')] ==1)
		{
			if($val[csf("booking_id")]) $all_book_id_arr[$val[csf("booking_id")]]=$val[csf("booking_id")];
		}
	}

	$bookingType="";
	$transfer_sql = "SELECT a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.to_trans_id, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.qnty as trans_qnty, d.barcode_no, d.roll_no, d.po_breakdown_id, e.within_group, e.job_no, e.sales_booking_no, e.buyer_id,e.po_job_no, e.po_buyer, e.season_id, e.delivery_date, e.booking_type, e.style_ref_no, e.booking_entry_form,e.booking_without_order,e.booking_id, a.remarks, a.inserted_by, a.insert_date,e.sales_booking_no,e.customer_buyer,d.entry_form,a.location_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, pro_roll_details d, fabric_sales_order_mst e
	where a.id=b.mst_id and b.to_trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and d.po_breakdown_id = e.id and a.entry_form =133  and d.entry_form =133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category =13 $date_cond_trans $str_cond order by a.transfer_date";
    // echo $transfer_sql;
    $transfer_sql_arr=sql_select($transfer_sql);


	foreach($transfer_sql_arr as $row)
	{
		$rcvTransBarcodeArr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
		$transferBarcodeArr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];

		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType =  "Sample With Order";
			}
		}
		else
		{
			$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
		}
		$salesData[$row[csf("sales_booking_no")]]['booking_type'] = $bookingType;

		if($row[csf('booking_without_order')] !=1 &&  $row[csf('within_group')] ==1)
		{
			if($row[csf("booking_id")]) $all_book_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
		}
	}

	if(!empty($rcvTransBarcodeArr))
	{
		$receive_barcodes = implode(",", $rcvTransBarcodeArr);
		if($db_type==2 && count($rcvTransBarcodeArr)>999)
		{
			$barcode_chunk=array_chunk($rcvTransBarcodeArr,999) ;
			$barcode_cond = " and (";

			foreach($barcode_chunk as $chunk_arr)
			{
				$barcode_cond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$barcode_cond = chop($barcode_cond,"or ");
			$barcode_cond .=")";
		}
		else
		{
			$barcode_cond=" and b.barcode_no in($receive_barcodes)";
		}

		$production_sql = sql_select("select b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form = 2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1 $barcode_cond");
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
			$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
		}
	}

	if(!empty($transferBarcodeArr))
	{
		$transferBarcodeNos = implode(",", $transferBarcodeArr);
		if($db_type==2 && count($transferBarcodeArr)>999)
		{
			$barcode_chunk=array_chunk($transferBarcodeArr,999) ;
			$barcode = " and (";

			foreach($barcode_chunk as $chunk_arr)
			{
				$barcode.=" barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$barcode = chop($barcode,"or ");
			$barcode .=")";
		}
		else
		{
			$barcode=" and barcode_no in($transferBarcodeNos)";
		}

		$deli_sys_no_arr = return_library_array("select sys_number, barcode_no from pro_grey_prod_delivery_mst  a, pro_roll_details b
		where a.id = b.mst_id and a.entry_form = 56 and b.entry_form = 56 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 $barcode","barcode_no","sys_number");
	}


	$data_array = array();
	$chk_challan_id_arr = array();
	$deli_challan_id_arr = array();
	foreach ($receive_sql_arr  as $val)
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source ==0)
		{
			$challan_no=""; $program_no = "";$prod_basis = "";
			if($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == 1)
			{
				$challan_no = $val[csf("challan_no")];
			}
			else
			{
				$challan_no = $prodBarcodeData[$val[csf("barcode_no")]]["prod_challan"];
			}
			if($val[csf("within_group")] ==2 )
			{
				$buyer_id = $val[csf("buyer_id")];
			}else{
				$buyer_id = $val[csf("po_buyer")];
			}

			if($prodBarcodeData[$val[csf("barcode_no")]]["prod_basis"] ==2)
			{
				$program_no = $prodBarcodeData[$val[csf("barcode_no")]]["prog_book"];
				$prod_basis = "knitting plan";
			}
			if($val[csf("entry_form")]==58)
			{
				if($chk_challan_id_arr[$val[csf("delivery_challan_id")]]=="")
				{
					$chk_challan_id_arr[$val[csf("delivery_challan_id")]]=$val[csf("delivery_challan_id")];
					array_push($deli_challan_id_arr,$val[csf("delivery_challan_id")]);
				}

			}
			// , c.floor_id, c.room, c.rack, c.self, c.bin_box
			$paramStr = $val[csf("recv_number")]."__".$val[csf("delivery_challan")]."__".$val[csf("knitting_source")]."__".$val[csf("knitting_company")]."__".$challan_no."__".$val[csf("store_id")]."__".$val[csf("within_group")]."__".$buyer_id."__".$val[csf("job_no")]."__".$val[csf("style_ref_no")]."__".$val[csf("season_id")]."__".$val[csf("sales_booking_no")]."__".$val[csf("booking_type")]."__".$val[csf("booking_entry_form")]."__".$val[csf("delivery_date")]."__".$program_no."__".$prod_basis."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_count"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_prod_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_lot"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["body_part_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["color_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["color_range_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["width"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["gsm"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["stitch_length"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["machine_dia"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["machine_gg"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["machine_no_id"]."__".$val[csf("remarks")]."__".$val[csf("inserted_by")]."__".$val[csf("insert_date")]."__"."1"."__".$salesData[$val[csf("sales_booking_no")]]['booking_type']."__".$val[csf("booking_id")]."__".$prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["knitting_company"]."__".$val[csf("customer_buyer")]."__".$val[csf("entry_form")]."__".$val[csf("floor_id")]."__".$val[csf("room")]."__".$val[csf("rack")]."__".$val[csf("self")]."__".$val[csf("bin_box")]."__".$val[csf("roll_no")]."__".$val[csf("barcode_no")]."__".$val[csf("location_id")]."__".$val[csf("id")]."__".$val[csf("knitting_location_id")];


			$data_array[$val[csf("job_no")]][$val[csf("receive_date")]][$paramStr]["quantity"] +=  $val[csf("rcv_qnty")];
			$data_array[$val[csf("job_no")]][$val[csf("receive_date")]][$paramStr]["barcode_no"] .=  $val[csf("barcode_no")].",";
			$data_array[$val[csf("job_no")]][$val[csf("receive_date")]][$paramStr]["no_of_roll"]++;
		}
	}
	$paramStr="";
	foreach ($transfer_sql_arr as $val)
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source ==0)
		{
			$challan_no=""; $program_no = "";$prod_basis = "";
			if($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == 1)
			{
				$challan_no = $val[csf("challan_no")];
			}
			else
			{
				$challan_no = $prodBarcodeData[$val[csf("barcode_no")]]["prod_challan"];
			}
			if($val[csf("within_group")] ==2 )
			{
				$buyer_id = $val[csf("buyer_id")];
			}else{
				$buyer_id = $val[csf("po_buyer")];
			}
			if($prodBarcodeData[$val[csf("barcode_no")]]["prod_basis"] ==2)
			{
				$program_no = $prodBarcodeData[$val[csf("barcode_no")]]["prog_book"];
				$prod_basis = "knitting plan";
			}

			$paramStr = $val[csf("transfer_system_id")]."__".$deli_sys_no_arr[$val[csf("barcode_no")]]."__".$prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["knitting_company"]."__".$challan_no."__".$val[csf("store_id")]."__".$val[csf("within_group")]."__".$buyer_id."__".$val[csf("job_no")]."__".$val[csf("style_ref_no")]."__".$val[csf("season_id")]."__".$val[csf("sales_booking_no")]."__".$val[csf("booking_type")]."__".$val[csf("booking_entry_form")]."__".$val[csf("delivery_date")]."__".$program_no."__".$prod_basis."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_count"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_prod_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["yarn_lot"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["body_part_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["color_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["color_range_id"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["width"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["gsm"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["stitch_length"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["machine_dia"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["machine_gg"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["machine_no_id"]."__".$val[csf("remarks")]."__".$val[csf("inserted_by")]."__".$val[csf("insert_date")]."__"."2"."__".$salesData[$val[csf("sales_booking_no")]]['booking_type']."__".$val[csf("booking_id")]."__".$prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"]."__".$prodBarcodeData[$val[csf("barcode_no")]]["knitting_company"]."__".$val[csf("customer_buyer")]."__".$val[csf("entry_form")]."__".$val[csf("floor_id")]."__".$val[csf("room")]."__".$val[csf("rack")]."__".$val[csf("self")]."__".$val[csf("bin_box")]."__".$val[csf("roll_no")]."__".$val[csf("barcode_no")]."__".$val[csf("location_id")]."__".$val[csf("id")];


			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["quantity"] += $val[csf("trans_qnty")];
			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["from_fso"] =  $val[csf("from_order_id")];
			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["barcode_no"] =  $val[csf("barcode_no")];
			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["no_of_roll"]++;
		}
	}


	$allDeterArr = array_filter($allDeterArr);
	if(!empty($allDeterArr))
	{
		$allDeterIds=implode(",",$allDeterArr);
        $allDeterCond=""; $deterCond="";
        if($db_type==2 && count($allDeterArr)>999)
        {
        	$allDeterArr_chunk=array_chunk($allDeterArr,999) ;
        	foreach($allDeterArr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$deterCond.="  a.id in($chunk_arr_value) or ";
        	}

        	$allDeterCond.=" and (".chop($deterCond,'or ').")";
        }
        else
        {
        	$allDeterCond=" and a.id in($allDeterIds)";
        }


		$construction_arr=array(); $composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $allDeterCond";
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

	$allColorArr = array_filter($allColorArr);
	if(!empty($allColorArr))
	{
		$allColorIds=implode(",",$allColorArr);
        $allColorCond=""; $colorCond="";
        if($db_type==2 && count($allColorArr)>999)
        {
        	$allColorArr_chunk=array_chunk($allColorArr,999) ;
        	foreach($allColorArr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$colorCond.=" id in($chunk_arr_value) or ";
        	}

        	$allColorCond.=" and (".chop($colorCond,'or ').")";
        }
        else
        {
        	$allColorCond=" and id in($allColorIds)";
        }
		$color_array=return_library_array( "select id,color_name from lib_color where status_active=1 $allColorCond", "id", "color_name");
	}

	$allYarnProdArr = array_filter($allYarnProdArr);
	if(!empty($allYarnProdArr))
	{
		$allYarnProdArr=array_unique(explode(",",implode(",",$allYarnProdArr)));
		$allYarnProd_ids=implode(",",$allYarnProdArr);
        $allYarnProd_Cond=""; $yProdCond="";
        if($db_type==2 && count($allYarnProdArr)>999)
        {
        	$allYarnProdArr_chunk=array_chunk($allYarnProdArr,999) ;
        	foreach($allYarnProdArr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$yProdCond.=" id in($chunk_arr_value) or ";
        	}
        	$allYarnProd_Cond.=" and (".chop($yProdCond,'or ').")";
        }
        else
        {
        	$allYarnProd_Cond=" and id in($allYarnProd_ids)";
        }
		$yarn_sql=sql_select( "select id, yarn_type, yarn_comp_type1st, yarn_comp_percent1st, brand from product_details_master where item_category_id=1 $allYarnProd_Cond");
		foreach ($yarn_sql as  $val)
		{
			$yarn_data[$val[csf("id")]]["brand"] = $brand_array[$val[csf("brand")]];
			$yarn_data[$val[csf("id")]]["comp"] = $composition[$val[csf("yarn_comp_type1st")]]." ".$val[csf("yarn_comp_percent1st")]."%";
			$yarn_data[$val[csf("id")]]["yarn_type"] = $yarn_type[$val[csf("yarn_type")]];
		}
	}

	$all_book_id_arr =array_filter($all_book_id_arr);
	if(!empty($all_book_id_arr))
	{
		$book_id_cond="";
		if($db_type==2 && count($all_book_id_arr)>999)
		{
			$all_book_id_chunk=array_chunk($all_book_id_arr,999);
			$book_id_cond=" and";
			foreach($all_book_id_chunk as $book_id)
			{
				$book_id_cond.= "( a.id in(".implode(",",$book_id).") or";
			}
			$book_id_cond=chop($book_id_cond,"or");
			$book_id_cond.=")";
		}
		else
		{
			$book_id_cond=" and a.id in(".implode(",",$all_book_id_arr).")";
		}

		$booking_sql=sql_select("SELECT a.id as book_id, a.booking_no, a.short_booking_type, b.division_id,c.grouping
		from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4) and b.booking_type in(1,4) $book_id_cond
		group by a.id, a.booking_no, a.short_booking_type, b.division_id,c.grouping ");
		$booking_data=array();$int_ref_data_arr=array();
		foreach($booking_sql as $row)
		{
			$booking_data[$row[csf("book_id")]]["short_type"]=$short_booking_type[$row[csf("short_booking_type")]];
			$booking_data[$row[csf("book_id")]]["division_id"].=$short_division_array[$row[csf("division_id")]].",";
			$int_ref_data_arr[$row[csf("booking_no")]]=$row[csf("grouping")];
		}

		unset($booking_sql);
	}

	$deli_challan_id_arr =array_filter($deli_challan_id_arr);
	if(!empty($deli_challan_id_arr))
	{
		$delivery_sql = "SELECT a.id as mst_id, a.sys_number, a.location_id, a.knitting_source,a.knitting_company,a.floor_ids
		from pro_grey_prod_delivery_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=56 ".where_con_using_array($deli_challan_id_arr,0,'a.id')."";
		//echo $delivery_sql;
		$delivery_res=sql_select($delivery_sql);

		$delivery_data_arr=array();
		foreach($delivery_res as $row)
		{
			$delivery_data_arr[$row[csf("sys_number")]]["mst_id"]=$row[csf("mst_id")];
			$delivery_data_arr[$row[csf("sys_number")]]["location_id"]=$row[csf("location_id")];
			$delivery_data_arr[$row[csf("sys_number")]]["knitting_source"]=$row[csf("knitting_source")];
			$delivery_data_arr[$row[csf("sys_number")]]["knitting_company"]=$row[csf("knitting_company")];
			$delivery_data_arr[$row[csf("sys_number")]]["floor_ids"]=$row[csf("floor_ids")];
		}
		unset($delivery_res);
		//echo "<pre>";print_r($delivery_data_arr);
	}

	$floor_room_rack_array=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	// Print Button for Knit Grey Fabric Roll Receive
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id =171 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format);
	// pre($format_ids); die;

	if ($format_ids[0]==86) 	$roll_rcv_type=1; // Print
	elseif($format_ids[0]==84)  $roll_rcv_type=2; // Print 2
	elseif($format_ids[0]==85) 	$roll_rcv_type=3; // Print 3
	elseif($format_ids[0]==68) 	$roll_rcv_type=4; // print barcode
	elseif($format_ids[0]==69)  $roll_rcv_type=5; // fabric details
	elseif($format_ids[0]==129) $roll_rcv_type=6; // Print 5
	elseif($format_ids[0]==848) $roll_rcv_type=7; // Print mg

	// Print Button for Roll Wise Grey Fabric Delivery to Store
	$print_report_format_delivery=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=7 and report_id =42 and is_deleted=0 and status_active=1");
	$format_ids_delivery=explode(",",$print_report_format_delivery);
	// pre($format_ids); die;

	if ($format_ids_delivery[0]==274) 	$roll_deli_type=1; // Print 10
	elseif($format_ids_delivery[0]==848) $roll_deli_type=2; // Print mg


	ob_start();
	?>
	<style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
	<div style="width:3940px" id="main_body">
		<table width="3940" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary Sales</td>
			</tr>
			<tr style="border:none;">
				<td colspan="16" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="3910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Trans. Date</th>
					<th width="120">Trans. Ref.</th>
					<th width="110">Receive Challan</th>
					<th width="100">Challan No</th>
					<th width="100">FSO No</th>
					<th width="100">Sales Job/ Booking No.</th>
					<th width="100">IR/IB</th>
					<th width="80">Customer</th>
					<th width="100">Cust Buyer</th>
					<th width="120">Style No</th>
					<th width="80">Delivery Date</th>
					<th width="70">Program No</th>
					<th width="80">Knitting Source</th>
					<th width="80">Production Basis</th>
					<th width="100">Party Name</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Composition</th>
					<th width="120">Yarn Type</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="140">Composition</th>
					<th width="80">Color</th>
					<th width="80">Color Range</th>
					<th width="50">F/Dia</th>
					<th width="50">GSM</th>
					<th width="80">S. Length</th>
					<th width="50">MC.No</th>
					<th width="50">MC.Dia <br>x GG</th>
					<th width="80">Receive Qty</th>
					<th width="80">Trans. In Qty</th>
					<th width="100">Trans. From</th>
					<th width="50">Roll No</th>
					<th width="50">Roll Weight</th>
					<th width="80">Barcode No</th>
					<th width="150">Store</th>
					<th width="50">Floor</th>
					<th width="50">Room</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="50">Bin</th>
					<th width="80">User</th>
					<th width="80">Insert Date & Time</th>
					<th width="150">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:3930px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="3910" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; $total_receive=""; $total_issue="";
					if(!empty($data_array))
					{
						foreach($data_array as $sales_no => $sales_data)
						{
							foreach($sales_data as $rcv_date => $rcv_data)
							{
								foreach($rcv_data as $refStr => $row)
								{
									$refString = explode("__",$refStr);
									// echo "<pre>";print_r($refString);

									if($refString[33] ==1){ $recv_quantity= $row["quantity"];$total_recv_qnty +=$recv_quantity;}else{$recv_quantity= 0;}
									if($refString[33] ==2){ $trans_quantity= $row["quantity"]; $total_trans_qnty +=$trans_quantity;}else{$trans_quantity= 0;}

									$yarn_brand_name = $yarn_comp_name = $yarn_type_name="";
									if($refString[18]){
										$yarn_arr = explode(",", $refString[18]);
										foreach ($yarn_arr as $value)
										{
											$yarn_brand_name .= $yarn_data[$value]["brand"].",";
											$yarn_comp_name .= $yarn_data[$value]["comp"].",";
											$yarn_type_name .= $yarn_data[$value]["yarn_type"].",";
										}
									}
									$yarn_brand_name =implode(",",array_unique(explode(",",chop($yarn_brand_name,","))));
									$yarn_comp_name =implode(",",array_unique(explode(",",chop($yarn_comp_name,","))));
									$yarn_type_name =implode(",",array_unique(explode(",",chop($yarn_type_name,","))));

									$barcode_nos=	 chop($row['barcode_no'],",");

									$delivery_id 		   = $delivery_data_arr[$refString[1]]["mst_id"];
									$delivery_location 	   = $delivery_data_arr[$refString[1]]["location_id"];
									$delivery_source 	   = $delivery_data_arr[$refString[1]]["knitting_source"];
									$delivery_knit_company = $delivery_data_arr[$refString[1]]["knitting_company"];
									$delivery_floor_ids    = $delivery_data_arr[$refString[1]]["floor_ids"];

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30" class="word_wrap_break" align="center"><? echo $i;?></td>
										<td width="70" class="word_wrap_break" align="center"><? echo $rcv_date;?></td>
										<td width="120" class="word_wrap_break" align="center" title="<? echo $refString[47];?>">
											<?
											if($refString[39]==58){

												echo "<a href='##' onclick=\"generate_rcv_report_dtls($roll_rcv_type,'".$refString[0]."','".$refString[48]."','".$refString[47]."','". $refString[5]."','".$refString[39]."','".$refString[49]."','".$refString[2]."')\">".$refString[0]."</a>";
											}
											else{
												echo $refString[0];
											}

											?>
										</td>
										<td width="110" class="word_wrap_break" align="center">
											<?
											if($refString[39]==58){
												echo "<a href='##' onclick=\"generate_delivery_report_dtls($roll_deli_type,'".$refString[1]."','".$delivery_id."','".$delivery_source."','". $delivery_floor_ids."','56','".$delivery_knit_company."','".$delivery_location."')\">".$refString[1]."</a>";
											}
											else{
												echo $refString[1];
											}


											?>
										</td>
										<td width="100" class="word_wrap_break" align="center"><? echo $refString[4];?></td>
										<td width="100" class="word_wrap_break" align="center">
											<a href="#" onClick="report_dtls('<? echo $refString[8]; ?>','<? echo $refString[11];?>','<? echo $refString[6];?>')"><? echo $refString[8];?></a>
										</td>
										<td width="100" class="word_wrap_break" align="center"><? echo $refString[11];?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $int_ref_data_arr[$refString[11]];?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $buyer_arr[$refString[7]];?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $buyer_arr[$refString[38]];?></td>
										<td width="120" class="word_wrap_break" align="center"><p><? echo $refString[9];?></p></td>
		                                <td width="80" class="word_wrap_break"><? echo $refString[14];?></td>
		                                <td width="70" class="word_wrap_break"><? echo $refString[15];?></td>
		                                <td width="80" class="word_wrap_break">
		                                	<?
		                                	echo $knitting_source[$refString[36]];
		                                	?>
		                                </td>
		                                <td width="80" class="word_wrap_break"><? echo $refString[16];?></td>
		                                <td width="100" class="word_wrap_break">
		                                	<?
		                                	if($refString[36]==1){
		                                		echo $company_short_array[$refString[37]];
		                                	}else{
		                                		echo $supplier_arr[$refString[37]];
		                                	}
		                                	?>
		                                </td>
		                                <td width="100" class="word_wrap_break" align="center">
		                                	<?
		                                	$count_name="";
		                                	foreach (explode(",", $refString[17]) as  $count)
		                                	{
		                                		$count_name .= $count_array[$count].",";
		                                	}
		                                	echo chop($count_name,",");
		                                	?>

		                                </td>
		                                <td width="100" class="word_wrap_break"><p><? echo $yarn_comp_name;?></p></td>
		                                <td width="120" class="word_wrap_break"><? echo $yarn_type_name;?></td>
		                                <td width="100" class="word_wrap_break"><p>&nbsp;<? echo $refString[19];?></p></td>
		                                <td width="100" class="word_wrap_break"><p><? echo $yarn_brand_name;?></p></td>
		                                <td width="100" class="word_wrap_break"><? echo $body_part[$refString[20]];?></td>
		                                <td width="100" class="word_wrap_break"><? echo $construction_arr[$refString[21]];?></td>
		                                <td width="140" class="word_wrap_break"><p><? echo $composition_arr[$refString[21]];?></p></td>
		                                <td width="80" class="word_wrap_break">
	                                		<p><?
	                                		$color_names="";
	                                		foreach (explode(",", $refString[22]) as $key => $color) {
	                                			$color_names .= $color_array[$color].",";
	                                		}
	                                		echo chop($color_names,",");
	                                		?>
	                                		</p>
		                                </td>
		                                <td width="80" class="word_wrap_break"><? echo $color_range[$refString[23]];?></td>
		                                <td width="50" class="word_wrap_break"><? echo $refString[24];?></td>
		                                <td width="50" class="word_wrap_break"><? echo $refString[25];?></td>
		                                <td width="80" class="word_wrap_break"><? echo $refString[26];?></td>
		                                <td width="50" class="word_wrap_break"><? echo $machine_array[$refString[29]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $refString[27] .'x'. $refString[28];?></td>

		                                <td width="80" class="word_wrap_break" align="right"><? echo number_format($recv_quantity,2,'.',''); ?></td>
		                                <td width="80" class="word_wrap_break" align="right"><? echo number_format($trans_quantity,2,'.',''); ?></td>
		                                <td width="100" class="word_wrap_break" align="center"><? echo $from_fso_arr[$row["from_fso"]]; ?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $refString[45];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo number_format($row["quantity"],2,'.','');?></td>
		                                <td width="80" class="word_wrap_break" align="center"><? echo $barcode_nos;?></td>
		                                <td width="150" class="word_wrap_break" align="center"><? echo $store_arr[$refString[5]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[40]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[41]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[42]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[43]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[44]];?></td>
		                                <td width="80" class="word_wrap_break" align="center"><? echo $user_array[$refString[31]];?></td>
		                                <td width="80" class="word_wrap_break"><? echo date("d-M-Y",strtotime($refString[32]))."&\n " .date("h:i",strtotime($refString[32]));?></td>
		                                <td width="150" class="word_wrap_break"><p><? echo $refString[30];?></p></td>
									</tr>
									<?
									$total_no_of_roll += $row["no_of_roll"];
									$total_roll_weight += $row["quantity"];
									$i++;
								}
							}
						}
					}else{
						echo "No Data Found";
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="3910" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="70"></th>
					<th width="120"></th>
					<th width="110"></th>
					<th width="100"></th>

					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="120"></th>
					<th width="80"></th>
					<th width="70"></th>

					<th width="80"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="140"></th>

					<th width="80"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="50"></th>

					<th width="50">Total : </th>
					<th width="80" id="value_total_receive_qnty" align="right"><? echo number_format($total_recv_qnty,2,'.','');?></th>
					<th width="80" id="value_total_trans_in_qnty" align="right"><? echo number_format($total_trans_qnty,2,'.','');?></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="50" id="value_total_roll_weight" align="right"><? //echo $total_roll_weight;?></th>
					<th width="80"></th>
					<th width="150"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="150"></th>
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

if($action=="generate_report_issue")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name	 = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name		 = str_replace("'","",$cbo_buyer_name);
	$cbo_cust_buyer_name = str_replace("'","",$cbo_cust_buyer_name);
    $txt_booking_no      = str_replace("'","",$txt_booking_no);
	$cbo_store_name		 = str_replace("'","",$cbo_store_name);
	$txt_date_from		 = str_replace("'","",$txt_date_from);
	$txt_date_to		 = str_replace("'","",$txt_date_to);
	$txt_order			 = str_replace("'","",$txt_order);
	$txt_order_id		 = str_replace("'","",$txt_order_id);
	$cbo_order_type		 = str_replace("'","",$cbo_order_type);
	$cbo_based_on		 = str_replace("'","",$cbo_based_on);
	$cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);


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
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($cbo_store_name>0) $store_cond=" and b.store_name=$cbo_store_name";
	if($cbo_store_name>0) $store_cond_trans=" and c.store_id=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and e.id =$txt_order_id";
	if($cbo_order_type==2) $str_cond .=" and e.booking_without_order =1";
	if($cbo_order_type==1) $str_cond .=" and e.booking_without_order !=1 and e.within_group=1";
	if($cbo_cust_buyer_name>0) $str_cond.=" and e.customer_buyer=$cbo_cust_buyer_name";
    if($txt_booking_no) $str_cond .=" and e.sales_booking_no like '%" . $txt_booking_no . "'";

	if($cbo_buyer_name>0)
	{
		$str_cond .= " and ((e.within_group = 2 and e.buyer_id = $cbo_buyer_name) or (e.within_group = 1 and e.po_buyer = $cbo_buyer_name)) ";
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($cbo_based_on == 1)
		{
			$date_cond_iss = " and a.issue_date between '$txt_date_from' and '$txt_date_to' ";
			$date_cond_trans = " and a.transfer_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond_iss="";
			}else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond_iss="";
			}
			$date_cond_trans=$date_cond_iss;
		}
	}


	$machine_array=return_library_array( "SELECT id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
	$user_array=return_library_array( "SELECT id, user_name from user_passwd", "id", "user_name");
	$brand_array=return_library_array( "SELECT id, brand_name from lib_brand", "id", "brand_name");
	$count_array=return_library_array( "SELECT id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$company_array=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "SELECT id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from lib_supplier where status_active =1",'id','supplier_name');
	$season_arr=return_library_array( "SELECT id, season_name from lib_buyer_season",'id','season_name');
	$store_arr=return_library_array( "SELECT id, store_name from lib_store_location",'id','store_name');
	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$to_fso_arr=return_library_array( "select id, sales_booking_no from fabric_sales_order_mst",'id','sales_booking_no');


	$issue_sql=sql_select("SELECT a.id as mst_id,a.issue_date, a.issue_number, a.knit_dye_source, a.knit_dye_company, b.store_name as store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box,  d.po_breakdown_id, a.inserted_by,a.insert_date,d.barcode_no, d.roll_no, d.qnty as issue_qnty, e.within_group, e.job_no, e.sales_booking_no,e.buyer_id,e.po_job_no, e.po_buyer,e.season_id,e.delivery_date, e.booking_type, e.booking_without_order, e.booking_entry_form, e.booking_id,e.style_ref_no,a.remarks,e.customer_buyer,d.entry_form
	from inv_issue_master a, inv_grey_fabric_issue_dtls b,  pro_roll_details d, fabric_sales_order_mst e
	where a.id = b.mst_id  and b.id = d.dtls_id and a.id = d.mst_id and d.po_breakdown_id = e.id and a.entry_form = 61 and d.entry_form = 61 and d.status_active =1 and b.status_active =1 and a.status_active =1 $date_cond_iss $str_cond $store_cond order by a.issue_date");

	foreach ($issue_sql as  $val)
	{
		$issTransBarcodeArr[$val[csf("barcode_no")]] =$val[csf("barcode_no")];
		if($val[csf('booking_type')] == 4)
		{
			if($val[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType =  "Sample With Order";
			}
		}
		else
		{
			$bookingType =  $booking_type_arr[$val[csf('booking_entry_form')]];
		}
		$salesData[$val[csf("sales_booking_no")]]['booking_type'] = $bookingType;

		if($val[csf('booking_without_order')] !=1 &&  $val[csf('within_group')] ==1)
		{
			if($val[csf("booking_id")]) $all_book_id_arr[$val[csf("booking_id")]]=$val[csf("booking_id")];
		}
	}

	$transfer_sql=sql_select("SELECT a.id as mst_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.qnty as trans_qnty, d.barcode_no, d.roll_no, a.from_order_id, e.style_ref_no, e.within_group, e.job_no, e.sales_booking_no, e.buyer_id,e.po_job_no, e.po_buyer, e.season_id, e.delivery_date, e.booking_type, e.booking_entry_form,e.booking_without_order,e.booking_id, a.remarks, a.inserted_by, a.insert_date,e.customer_buyer,d.entry_form
	FROM inv_item_transfer_mst a,inv_item_transfer_dtls b, inv_transaction c, pro_roll_details d, fabric_sales_order_mst e
	WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and a.from_order_id = e.id and a.entry_form =133  and d.entry_form =133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category =13 $date_cond_trans $str_cond $store_cond_trans
	ORDER BY a.transfer_date");

	foreach($transfer_sql as $row)
	{
		$issTransBarcodeArr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
		$transferBarcodeArr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];

		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType =  "Sample With Order";
			}
		}
		else
		{
			$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
		}
		$salesData[$row[csf("sales_booking_no")]]['booking_type'] = $bookingType;

		if($row[csf('booking_without_order')] !=1 &&  $row[csf('within_group')] ==1)
		{
			if($row[csf("booking_id")]) $all_book_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
		}
	}

	if(!empty($issTransBarcodeArr))
	{
		$receive_barcodes = implode(",", $issTransBarcodeArr);
		if($db_type==2 && count($issTransBarcodeArr)>999)
		{
			$barcode_chunk=array_chunk($issTransBarcodeArr,999) ;
			$barcode_cond = " and (";

			foreach($barcode_chunk as $chunk_arr)
			{
				$barcode_cond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$barcode_cond = chop($barcode_cond,"or ");
			$barcode_cond .=")";
		}
		else
		{
			$barcode_cond=" and b.barcode_no in($receive_barcodes)";
		}

		$split_chk_sql = sql_select("SELECT c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 $barcode_cond");
		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}

			$split_barcodes = implode(",", $split_barcode_arr);
			if($db_type==2 && count($split_barcode_arr)>999)
			{
				$split_barcode_arr_chunk=array_chunk($split_barcode_arr,999) ;
				$split_barcode_cond = " and (";

				foreach($split_barcode_arr_chunk as $chunk_arr)
				{
					$split_barcode_cond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$split_barcode_cond = chop($split_barcode_cond,"or ");
				$split_barcode_cond .=")";
			}
			else
			{
				$split_barcode_cond=" and a.barcode_no in($split_barcodes)";
			}

			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond");

			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
					$issTransBarcodeArr[$value[csf("mother_barcode")]] =$value[csf("mother_barcode")];
				}
			}
		}
		// echo '<pre>';print_r($issTransBarcodeArr);
		$barcode_cond="";
		if(!empty($issTransBarcodeArr))
		{
			$receive_barcodes = implode(",", $issTransBarcodeArr);
			if($db_type==2 && count($issTransBarcodeArr)>999)
			{
				$barcode_chunk=array_chunk($issTransBarcodeArr,999) ;
				$barcode_cond = " and (";

				foreach($barcode_chunk as $chunk_arr)
				{
					$barcode_cond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$barcode_cond = chop($barcode_cond,"or ");
				$barcode_cond .=")";
			}
			else
			{
				$barcode_cond=" and b.barcode_no in($receive_barcodes)";
			}
		}

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form = 2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1 $barcode_cond");
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
			$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
		}
	}


	$data_array = array();
	foreach ($issue_sql  as $val)
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source==0)
		{
			$challan_no=""; $program_no = "";$prod_basis = "";
			if($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == 1)
			{
				$challan_no = $val[csf("challan_no")];
			}
			else
			{
				$challan_no = $prodBarcodeData[$val[csf("barcode_no")]]["prod_challan"];
			}
			if($val[csf("within_group")] ==2 )
			{
				$buyer_id = $val[csf("buyer_id")];
			}else{
				$buyer_id = $val[csf("po_buyer")];
			}

			/*if($prodBarcodeData[$val[csf("barcode_no")]]["prod_basis"] ==2)
			{
				$program_no = $prodBarcodeData[$val[csf("barcode_no")]]["prog_book"];
				$prod_basis = "knitting plan";
			}*/

			$mother_barcode_no = $mother_barcode_arr[$val[csf("barcode_no")]];
			if($mother_barcode_no != "")
			{
				if($prodBarcodeData[$mother_barcode_no]["prod_basis"] ==2)
				{
					$program_no = $prodBarcodeData[$mother_barcode_no]["prog_book"];
					$prod_basis = "knitting plan";
				}
				$yarn_count=$prodBarcodeData[$mother_barcode_no]["yarn_count"];
				$yarn_prod_id=$prodBarcodeData[$mother_barcode_no]["yarn_prod_id"];
				$yarn_lot=$prodBarcodeData[$mother_barcode_no]["yarn_lot"];
				$body_part_id=$prodBarcodeData[$mother_barcode_no]["body_part_id"];
				$febric_description_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
				$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
				$color_range_id=$prodBarcodeData[$mother_barcode_no]["color_range_id"];
				$width=$prodBarcodeData[$mother_barcode_no]["width"];
				$gsm=$prodBarcodeData[$mother_barcode_no]["gsm"];
				$stitch_length=$prodBarcodeData[$mother_barcode_no]["stitch_length"];
				$machine_dia=$prodBarcodeData[$mother_barcode_no]["machine_dia"];
				$machine_gg=$prodBarcodeData[$mother_barcode_no]["machine_gg"];
				$machine_no_id=$prodBarcodeData[$mother_barcode_no]["machine_no_id"];
				$knitting_company=$prodBarcodeData[$mother_barcode_no]["knitting_company"];
				$knitting_source_id=$prodBarcodeData[$mother_barcode_no]["knitting_source"];
			}
			else
			{
				if($prodBarcodeData[$val[csf("barcode_no")]]["prod_basis"] ==2)
				{
					$program_no = $prodBarcodeData[$val[csf("barcode_no")]]["prog_book"];
					$prod_basis = "knitting plan";
				}
				$yarn_count=$prodBarcodeData[$val[csf("barcode_no")]]["yarn_count"];
				$yarn_prod_id=$prodBarcodeData[$val[csf("barcode_no")]]["yarn_prod_id"];
				$yarn_lot=$prodBarcodeData[$val[csf("barcode_no")]]["yarn_lot"];
				$body_part_id=$prodBarcodeData[$val[csf("barcode_no")]]["body_part_id"];
				$febric_description_id=$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"];
				$color_id=$prodBarcodeData[$val[csf("barcode_no")]]["color_id"];
				$color_range_id=$prodBarcodeData[$val[csf("barcode_no")]]["color_range_id"];
				$width=$prodBarcodeData[$val[csf("barcode_no")]]["width"];
				$gsm=$prodBarcodeData[$val[csf("barcode_no")]]["gsm"];
				$stitch_length=$prodBarcodeData[$val[csf("barcode_no")]]["stitch_length"];
				$machine_dia=$prodBarcodeData[$val[csf("barcode_no")]]["machine_dia"];
				$machine_gg=$prodBarcodeData[$val[csf("barcode_no")]]["machine_gg"];
				$machine_no_id=$prodBarcodeData[$val[csf("barcode_no")]]["machine_no_id"];
				$knitting_company=$prodBarcodeData[$val[csf("barcode_no")]]["knitting_company"];
				$knitting_source_id=$prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"];
			}

			$paramStr = $val[csf("issue_number")]."__".''."__".$val[csf("knit_dye_source")]."__".$val[csf("knit_dye_company")]."__".$challan_no."__".$val[csf("store_id")]."__".$val[csf("within_group")]."__".$buyer_id."__".$val[csf("po_job_no")]."__".$val[csf("style_ref_no")]."__".$val[csf("season_id")]."__".$val[csf("sales_booking_no")]."__".$val[csf("booking_type")]."__".$val[csf("booking_entry_form")]."__".$val[csf("delivery_date")]."__".$program_no."__".$prod_basis."__".$yarn_count."__".$yarn_prod_id."__".$yarn_lot."__".$body_part_id."__".$febric_description_id."__".$color_id."__".$color_range_id."__".$width."__".$gsm."__".$stitch_length."__".$machine_dia."__".$machine_gg."__".$machine_no_id."__".$val[csf("remarks")]."__".$val[csf("inserted_by")]."__".$val[csf("insert_date")]."__"."1"."__".$salesData[$val[csf("sales_booking_no")]]['booking_type']."__".$val[csf("booking_id")]."__".$knitting_source."__".$knitting_company."__".$val[csf("customer_buyer")]."__".$val[csf("entry_form")]."__".$val[csf("floor_id")]."__".$val[csf("room")]."__".$val[csf("rack")]."__".$val[csf("self")]."__".$val[csf("bin_box")]."__".$val[csf("roll_no")]."__".$val[csf("barcode_no")]."__".$val[csf("mst_id")];


			$data_array[$val[csf("job_no")]][$val[csf("issue_date")]][$paramStr]["quantity"] += $val[csf("issue_qnty")];
			$data_array[$val[csf("job_no")]][$val[csf("issue_date")]][$paramStr]["barcode_no"] =  $val[csf("barcode_no")];
			$data_array[$val[csf("job_no")]][$val[csf("issue_date")]][$paramStr]["no_of_roll"]++;
		}
	}
	$paramStr="";
	foreach ($transfer_sql as $val)
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source ==0)
		{
			$challan_no=""; $program_no = "";$prod_basis = "";
			if($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == 1)
			{
				$challan_no = $val[csf("challan_no")];
			}
			else
			{
				$challan_no = $prodBarcodeData[$val[csf("barcode_no")]]["prod_challan"];
			}
			if($val[csf("within_group")] ==2 )
			{
				$buyer_id = $val[csf("buyer_id")];
			}else{
				$buyer_id = $val[csf("po_buyer")];
			}
			/*if($prodBarcodeData[$val[csf("barcode_no")]]["prod_basis"] ==2)
			{
				$program_no = $prodBarcodeData[$val[csf("barcode_no")]]["prog_book"];
				$prod_basis = "knitting plan";
			}*/
			$mother_barcode_no = $mother_barcode_arr[$val[csf("barcode_no")]];
			if($mother_barcode_no != "")
			{
				if($prodBarcodeData[$mother_barcode_no]["prod_basis"] ==2)
				{
					$program_no = $prodBarcodeData[$mother_barcode_no]["prog_book"];
					$prod_basis = "knitting plan";
				}
				$yarn_count=$prodBarcodeData[$mother_barcode_no]["yarn_count"];
				$yarn_prod_id=$prodBarcodeData[$mother_barcode_no]["yarn_prod_id"];
				$yarn_lot=$prodBarcodeData[$mother_barcode_no]["yarn_lot"];
				$body_part_id=$prodBarcodeData[$mother_barcode_no]["body_part_id"];
				$febric_description_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
				$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
				$color_range_id=$prodBarcodeData[$mother_barcode_no]["color_range_id"];
				$width=$prodBarcodeData[$mother_barcode_no]["width"];
				$gsm=$prodBarcodeData[$mother_barcode_no]["gsm"];
				$stitch_length=$prodBarcodeData[$mother_barcode_no]["stitch_length"];
				$machine_dia=$prodBarcodeData[$mother_barcode_no]["machine_dia"];
				$machine_gg=$prodBarcodeData[$mother_barcode_no]["machine_gg"];
				$machine_no_id=$prodBarcodeData[$mother_barcode_no]["machine_no_id"];
				$knitting_company=$prodBarcodeData[$mother_barcode_no]["knitting_company"];
				$knitting_source_id=$prodBarcodeData[$mother_barcode_no]["knitting_source"];
			}
			else
			{
				if($prodBarcodeData[$val[csf("barcode_no")]]["prod_basis"] ==2)
				{
					$program_no = $prodBarcodeData[$val[csf("barcode_no")]]["prog_book"];
					$prod_basis = "knitting plan";
				}
				$yarn_count=$prodBarcodeData[$val[csf("barcode_no")]]["yarn_count"];
				$yarn_prod_id=$prodBarcodeData[$val[csf("barcode_no")]]["yarn_prod_id"];
				$yarn_lot=$prodBarcodeData[$val[csf("barcode_no")]]["yarn_lot"];
				$body_part_id=$prodBarcodeData[$val[csf("barcode_no")]]["body_part_id"];
				$febric_description_id=$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"];
				$color_id=$prodBarcodeData[$val[csf("barcode_no")]]["color_id"];
				$color_range_id=$prodBarcodeData[$val[csf("barcode_no")]]["color_range_id"];
				$width=$prodBarcodeData[$val[csf("barcode_no")]]["width"];
				$gsm=$prodBarcodeData[$val[csf("barcode_no")]]["gsm"];
				$stitch_length=$prodBarcodeData[$val[csf("barcode_no")]]["stitch_length"];
				$machine_dia=$prodBarcodeData[$val[csf("barcode_no")]]["machine_dia"];
				$machine_gg=$prodBarcodeData[$val[csf("barcode_no")]]["machine_gg"];
				$machine_no_id=$prodBarcodeData[$val[csf("barcode_no")]]["machine_no_id"];
				$knitting_company=$prodBarcodeData[$val[csf("barcode_no")]]["knitting_company"];
				$knitting_source_id=$prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"];
			}

			$paramStr = $val[csf("transfer_system_id")]."__".''."__".''."__".''."__".$challan_no."__".$val[csf("store_id")]."__".$val[csf("within_group")]."__".$buyer_id."__".$val[csf("po_job_no")]."__".$val[csf("style_ref_no")]."__".$val[csf("season_id")]."__".$val[csf("sales_booking_no")]."__".$val[csf("booking_type")]."__".$val[csf("booking_entry_form")]."__".$val[csf("delivery_date")]."__".$program_no."__".$prod_basis."__".$yarn_count."__".$yarn_prod_id."__".$yarn_lot."__".$body_part_id."__".$febric_description_id."__".$color_id."__".$color_range_id."__".$width."__".$gsm."__".$stitch_length."__".$machine_dia."__".$machine_gg."__".$machine_no_id."__".$val[csf("remarks")]."__".$val[csf("inserted_by")]."__".$val[csf("insert_date")]."__"."2"."__".$salesData[$val[csf("sales_booking_no")]]['booking_type']."__".$val[csf("booking_id")]."__".$knitting_source."__".$knitting_company."__".$val[csf("customer_buyer")]."__".$val[csf("entry_form")]."__".$val[csf("floor_id")]."__".$val[csf("room")]."__".$val[csf("rack")]."__".$val[csf("self")]."__".$val[csf("bin_box")]."__".$val[csf("roll_no")]."__".$val[csf("barcode_no")]."__".$val[csf("mst_id")];


			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["quantity"] += $val[csf("trans_qnty")];
			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["to_fso"] =  $val[csf("to_order_id")];
			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["barcode_no"] =  $val[csf("barcode_no")];
			$data_array[$val[csf("job_no")]][$val[csf("transfer_date")]][$paramStr]["no_of_roll"]++;
		}
	}


	$allDeterArr = array_filter($allDeterArr);
	if(!empty($allDeterArr))
	{
		$allDeterIds=implode(",",$allDeterArr);
        $allDeterCond=""; $deterCond="";
        if($db_type==2 && count($allDeterArr)>999)
        {
        	$allDeterArr_chunk=array_chunk($allDeterArr,999) ;
        	foreach($allDeterArr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$deterCond.="  a.id in($chunk_arr_value) or ";
        	}

        	$allDeterCond.=" and (".chop($deterCond,'or ').")";
        }
        else
        {
        	$allDeterCond=" and a.id in($allDeterIds)";
        }


		$construction_arr=array(); $composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $allDeterCond";
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

	$allColorArr = array_filter($allColorArr);
	if(!empty($allColorArr))
	{
		$allColorIds=implode(",",$allColorArr);
        $allColorCond=""; $colorCond="";
        if($db_type==2 && count($allColorArr)>999)
        {
        	$allColorArr_chunk=array_chunk($allColorArr,999) ;
        	foreach($allColorArr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$colorCond.=" id in($chunk_arr_value) or ";
        	}

        	$allColorCond.=" and (".chop($colorCond,'or ').")";
        }
        else
        {
        	$allColorCond=" and id in($allColorIds)";
        }
		$color_array=return_library_array( "select id,color_name from lib_color where status_active=1 $allColorCond", "id", "color_name");
	}

	$allYarnProdArr = array_filter($allYarnProdArr);
	if(!empty($allYarnProdArr))
	{
		$allYarnProdArr=array_unique(explode(",",implode(",",$allYarnProdArr)));
		$allYarnProd_ids=implode(",",$allYarnProdArr);
        $allYarnProd_Cond=""; $yProdCond="";
        if($db_type==2 && count($allYarnProdArr)>999)
        {
        	$allYarnProdArr_chunk=array_chunk($allYarnProdArr,999) ;
        	foreach($allYarnProdArr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$yProdCond.=" id in($chunk_arr_value) or ";
        	}
        	$allYarnProd_Cond.=" and (".chop($yProdCond,'or ').")";
        }
        else
        {
        	$allYarnProd_Cond=" and id in($allYarnProd_ids)";
        }
		$yarn_sql=sql_select( "select id, yarn_type, yarn_comp_type1st, yarn_comp_percent1st, brand from product_details_master where item_category_id=1 $allYarnProd_Cond");
		foreach ($yarn_sql as  $val)
		{
			$yarn_data[$val[csf("id")]]["brand"] = $brand_array[$val[csf("brand")]];
			$yarn_data[$val[csf("id")]]["comp"] = $composition[$val[csf("yarn_comp_type1st")]]." ".$val[csf("yarn_comp_percent1st")]."%";
			$yarn_data[$val[csf("id")]]["yarn_type"] = $yarn_type[$val[csf("yarn_type")]];
		}
	}

	$all_book_id_arr =array_filter($all_book_id_arr);
	if(!empty($all_book_id_arr))
	{
		$book_id_cond="";
		if($db_type==2 && count($all_book_id_arr)>999)
		{
			$all_book_id_chunk=array_chunk($all_book_id_arr,999);
			$book_id_cond=" and";
			foreach($all_book_id_chunk as $book_id)
			{
				$book_id_cond.= "( a.id in(".implode(",",$book_id).") or";
			}
			$book_id_cond=chop($book_id_cond,"or");
			$book_id_cond.=")";
		}
		else
		{
			$book_id_cond=" and a.id in(".implode(",",$all_book_id_arr).")";
		}

		$booking_sql=sql_select("SELECT a.id as book_id, a.booking_no, a.short_booking_type, b.division_id,c.grouping
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4) and b.booking_type in(1,4) $book_id_cond
			group by a.id, a.booking_no, a.short_booking_type, b.division_id,c.grouping ");
		$booking_data=array();$int_ref_data_arr=array();
		foreach($booking_sql as $row)
		{
			$booking_data[$row[csf("book_id")]]["short_type"]=$short_booking_type[$row[csf("short_booking_type")]];
			$booking_data[$row[csf("book_id")]]["division_id"].=$short_division_array[$row[csf("division_id")]].",";
			$int_ref_data_arr[$row[csf("booking_no")]]=$row[csf("grouping")];
		}

		unset($booking_sql);
	}

	$floor_room_rack_array=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	// Print Button for Grey Fabric Roll Issue
	$print_report_format_grey_roll_issue=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and  report_id =27 and is_deleted=0 and status_active=1");

	$format_ids_grey_roll_issue=explode(",",$print_report_format_grey_roll_issue);
	// echo "<pre>";
	// print_r($format_ids_grey_roll_issue);die;

	// if ($format_ids_grey_roll_issue[0]==451) 	$grey_roll_issue_type=1; // Print with collar cuff-Outside
	// elseif($format_ids_grey_roll_issue[0]==860) $grey_roll_issue_type=2; // Print MG2
	// else $grey_roll_issue_type=3; // Print MG

	ob_start();
	?>
	<style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
	<div style="width:3970px" id="main_body">
		<table width="3970" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary Sales</td>
			</tr>
			<tr style="border:none;">
				<td colspan="20" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="3950" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="80">Trans. Date</th>
					<th width="120">Trans. Ref.</th>
					<th width="120">FSO No</th>
					<th width="120">Sales Job/<br>Fabric Booking</th>
					<th width="100">IR/IB</th>
					<th width="100">Style No</th>
					<th width="100">Customer Name</th>
					<th width="100">Cust. Buyer</th>
					<th width="80">Delivery Date</th>
					<th width="80">Dyeing Source</th>
					<th width="80">Dyeing Company</th>
					<th width="70">Program No</th>
					<th width="80">Knitting Source</th>
					<th width="80">Production Basis</th>
					<th width="100">Knitting Party</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Composition</th>
					<th width="100">Yarn Type</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="140">Composition</th>
					<th width="80">Color</th>
					<th width="80">Color Range</th>
					<th width="50">Dia</th>
					<th width="50">GSM</th>
					<th width="50">S. Length</th>
					<th width="50">MC.No</th>
					<th width="50">MC.Dia <br>x GG</th>
					<th width="80">Issue Qty</th>
					<th width="80">Trans. Out Qty</th>
					<th width="100">Trans. To</th>

					<th width="50">Roll No</th>
					<th width="50">Roll Weight</th>
					<th width="80">Barcode No</th>
					<th width="150">Store</th>
					<th width="50">Floor</th>
					<th width="50">Room</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="50">Bin</th>

					<th width="80">User</th>
					<th width="80">Insert Date & Time</th>
					<th width="150">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:3970px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="3950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; $total_receive=""; $total_issue="";
					if(!empty($data_array))
					{
						foreach($data_array as $sales_no => $sales_data)
						{
							foreach($sales_data as $rcv_date => $rcv_data)
							{
								foreach($rcv_data as $refStr => $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$refString = explode("__",$refStr);

									if($refString[33] ==1){ $recv_quantity= $row["quantity"];$total_recv_qnty +=$recv_quantity;}else{$recv_quantity= 0;}
									if($refString[33] ==2){ $trans_quantity= $row["quantity"]; $total_trans_qnty +=$trans_quantity;}else{$trans_quantity= 0;}

									$yarn_brand_name = $yarn_comp_name = $yarn_type_name="";
									if($refString[18]){
										$yarn_arr = explode(",", $refString[18]);
										foreach ($yarn_arr as $value)
										{
											$yarn_brand_name .= $yarn_data[$value]["brand"].",";
											$yarn_comp_name .= $yarn_data[$value]["comp"].",";
											$yarn_type_name .= $yarn_data[$value]["yarn_type"].",";
										}
									}
									$yarn_brand_name =implode(",",array_unique(explode(",",chop($yarn_brand_name,","))));
									$yarn_comp_name =implode(",",array_unique(explode(",",chop($yarn_comp_name,","))));
									$yarn_type_name =implode(",",array_unique(explode(",",chop($yarn_type_name,","))));
									$barcode_nos = chop($row["barcode_no"],",");
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30" class="word_wrap_break" align="center"><? echo $i;?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $rcv_date;?></td>
										<td width="120" class="word_wrap_break" align="center">
											<?
											if($refString[39]==61)
											{
												echo "<a href='##' onclick=\"generate_issue_report_dtls($format_ids_grey_roll_issue[0],'".$refString[0]."','".$refString[47]."','".$refString[39]."')\">".$refString[0]."</a>";
											}
											else
											{
												echo $refString[0];
											}
											?>
										</td>
										<td width="120" class="word_wrap_break" align="center">
											<a href="#" onClick="report_dtls('<? echo $sales_no; ?>','<? echo $refString[9];?>','<? echo $refString[6];?>')"><? echo $sales_no;?></a>
										</td>
										<td width="120" class="word_wrap_break" align="center"><? echo $refString[11];?></td>
										<td width="100" align="center"><p class="word_wrap_break"><? echo $int_ref_data_arr[$refString[11]];?></p></td>
										<td width="100" align="center"><p class="word_wrap_break"><? echo $refString[9];?></p></td>
										<td width="100" align="center"><p class="word_wrap_break"><? echo $buyer_arr[$refString[7]];?></p></td>
										<td width="100" align="center"><p class="word_wrap_break"><? echo $buyer_arr[$refString[38]];?></p></td>
		                                <td width="80" class="word_wrap_break" align="center"><? echo $refString[14];?></td>
		                                <td width="80" class="word_wrap_break" align="center" title="<? echo $refString[2];?>"><? echo $knitting_source[$refString[2]];?> </td>
										<td width="80" class="word_wrap_break" align="center" title="<? echo $refString[3];?>">
											<?
		                                	if($refString[2]==1){
		                                		echo $company_array[$refString[3]];
		                                	}else{
		                                		//echo $buyer_arr[$refString[3]];
		                                		echo $supplier_arr[$refString[3]];
		                                	}
		                                	?>
										</td>
		                                <td width="70" class="word_wrap_break" align="center"><? echo $refString[15];?></td>
		                                <td width="80" class="word_wrap_break" align="center">
		                                	<?
		                                	 echo $knitting_source[$refString[36]];
		                                	?>
		                                </td>
		                                <td width="80" class="word_wrap_break" align="center"><? echo $refString[16];?></td>
		                                <td width="100" class="word_wrap_break" align="center">
		                                	<?
		                                	if($refString[36]==1){
		                                		echo $company_array[$refString[37]];
		                                	}else{
		                                		echo $supplier_arr[$refString[37]];
		                                	}
		                                	?>
		                                </td>
		                                <td width="100" class="word_wrap_break" align="center"><p class="word_wrap_break">
		                                	<?
		                                	$count_name="";
		                                	foreach (explode(",", $refString[17]) as  $count)
		                                	{
		                                		$count_name .= $count_array[$count].",";
		                                	}
		                                	echo chop($count_name,",");
		                                	?>
		                                	</p>
		                                </td>
		                                <td width="100" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $yarn_comp_name;?></p></td>
		                                <td width="100" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $yarn_type_name;?></p></td>
		                                <td width="100" class="word_wrap_break" align="center"><p class="word_wrap_break">&nbsp;<? echo $refString[19];?></p></td>
		                                <td width="100" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $yarn_brand_name;?></p></td>
		                                <td width="100" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $body_part[$refString[20]];?></p></td>
		                                <td width="100" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $construction_arr[$refString[21]];?></p></td>
		                                <td width="140" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $composition_arr[$refString[21]];?></p></td>
		                                <td width="80" class="word_wrap_break" align="center">
		                                	<p class="word_wrap_break">
		                                	<?
		                                	$color_names="";
		                                	foreach (explode(",", $refString[22]) as  $color) {
		                                		$color_names .= $color_array[$color].",";
		                                	}
		                                	echo chop($color_names,",");?>
		                                	</p>
		                                </td>
		                                <td width="80" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $color_range[$refString[23]];?></p></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $refString[24];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $refString[25];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $refString[26];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><p class="word_wrap_break"><? echo $machine_array[$refString[29]];?></p></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $refString[27].'x'.$refString[28];?></td>
		                                <td width="80" class="word_wrap_break" align="right"><? echo number_format($recv_quantity,2,'.',''); ?></td>
		                                <td width="80" class="word_wrap_break" align="right"><? echo number_format($trans_quantity,2,'.',''); ?></td>
		                                <td width="100" class="word_wrap_break" align="center"><? echo $to_fso_arr[$row["to_fso"]];?></td>

		                                <td width="50" class="word_wrap_break" align="center"><? echo $refString[45];?></td>
		                                <td width="50" class="word_wrap_break" align="right"><? echo number_format($row["quantity"],2,'.','');?></td>
		                                <td width="80" class="word_wrap_break" align="center"><? echo $barcode_nos;?></td>
		                                <td width="150" class="word_wrap_break" align="center"><? echo $store_arr[$refString[5]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[40]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[41]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[42]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[43]];?></td>
		                                <td width="50" class="word_wrap_break" align="center"><? echo $floor_room_rack_array[$refString[44]];?></td>
		                                <td width="80" class="word_wrap_break" align="center"><? echo $user_array[$refString[31]];?></td>
		                                <td width="80" class="word_wrap_break"><? echo date("d-M-Y",strtotime($refString[32]))."&\n " .date("h:i",strtotime($refString[32]));?></td>
		                                <td width="150" class="word_wrap_break"><p><? echo $refString[30];?></p></td>

									</tr>
									<?
									$total_no_of_roll += $row["no_of_roll"];
									$total_roll_weight += $row["quantity"];
									$i++;
								}
							}
						}
					}else{
						echo "no data found";
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="3950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50">Total : </th>
					<th width="80" id="value_total_receive_qnty" align="right"><? echo number_format($total_recv_qnty,2,'.','');?></th>
					<th width="80" id="value_total_trans_in_qnty" align="right"><? echo number_format($total_trans_qnty,2,'.','');?></th>
					<th width="100"></th>

					<th width="50"></th>
					<th width="50" id="value_total_roll_weight" align="right"><? //echo $total_roll_weight;?></th>
					<th width="80"></th>
					<th width="150"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>

					<th width="80"></th>
					<th width="80"></th>
					<th width="150"></th>
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

if($action=="generate_report_receive_issue_summary")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name    =str_replace("'","",$cbo_company_name);
	$cbo_buyer_name      =str_replace("'","",$cbo_buyer_name);
	$cbo_cust_buyer_name =str_replace("'","",$cbo_cust_buyer_name);
    $txt_booking_no      =str_replace("'","",$txt_booking_no);
	$cbo_store_name      =str_replace("'","",$cbo_store_name);
	$txt_date_from       =str_replace("'","",$txt_date_from);
	$txt_date_to         =str_replace("'","",$txt_date_to);
	$txt_order           =str_replace("'","",$txt_order);
	$txt_order_id        =str_replace("'","",$txt_order_id);
	$cbo_order_type      =str_replace("'","",$cbo_order_type);
	$cbo_based_on        =str_replace("'","",$cbo_based_on);
	$cbo_knitting_source =str_replace("'","",$cbo_knitting_source);


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
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($cbo_store_name>0) $str_cond.=" and c.store_id=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and e.id =$txt_order_id";
	if($cbo_order_type==2) $str_cond .=" and e.booking_without_order =1";
	if($cbo_order_type==1) $str_cond .=" and e.booking_without_order !=1 and e.within_group=1";
    if($cbo_cust_buyer_name>0) $str_cond.=" and e.customer_buyer=$cbo_cust_buyer_name";

    if($cbo_store_name>0) $issue_store_cond=" and b.store_name=$cbo_store_name";

    if($txt_booking_no) $str_cond .=" and e.sales_booking_no like '%" . $txt_booking_no . "'";

	if($cbo_buyer_name>0)
	{
		$str_cond .= " and ((e.within_group = 2 and e.buyer_id = $cbo_buyer_name) or (e.within_group = 1 and e.po_buyer = $cbo_buyer_name)) ";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		if($cbo_based_on == 1)
		{
			$date_cond = " and a.receive_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
			}else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
			}
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
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (5884,109,61,75,5885,5886)");
	execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	// ======================= Receive SQL Start ===================================
    $receive_sql = "SELECT a.id,a.receive_date, a.recv_number,a.booking_no as delivery_challan, a.knitting_source, a.knitting_company, a.challan_no, c.store_id, d.po_breakdown_id,d.barcode_no, d.roll_no, d.qnty as rcv_qnty, e.within_group, e.job_no, e.sales_booking_no,e.buyer_id,e.po_job_no, e.po_buyer,e.season_id,e.delivery_date, e.booking_type,e.booking_without_order, e.booking_entry_form,e.booking_id,e.style_ref_no,a.remarks,e.customer_buyer,d.entry_form, e.booking_id FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c, pro_roll_details d, fabric_sales_order_mst e WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and d.po_breakdown_id = e.id and d.is_sales=1 and d.entry_form in(58,84)  and a.entry_form in(58,84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 $date_cond $str_cond ORDER BY a.receive_date";
   // echo $receive_sql; exit(); , wo_po_break_down f wo_booking_dtls
    $receive_sql_arr=sql_select( $receive_sql);
	$bookingIDArray = array();
	foreach ($receive_sql_arr as  $val)
	{
		$rcvTransBarcodeArr[$val[csf("barcode_no")]] =$val[csf("barcode_no")];
		if($val[csf("within_group")] == 1){
			$bookingIDArray[] 	= 	$val[csf("booking_id")];
		}
		
		if( $barcode_no_check[$val[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$val[csf('barcode_no')]]=$val[csf('barcode_no')];
            $barcodeno = $val[csf('barcode_no')];
            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
            execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)");
        }
	}
	oci_commit($con);
	//IR/IB query
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 109, 7,$bookingIDArray, $empty_arr);
	oci_commit($con);
	$iribQuery="SELECT b.id, b.grouping, a.booking_no
		FROM GBL_TEMP_ENGINE T, wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c
		WHERE t.REF_val=c.id and c.id=a.booking_mst_id and b.id = a.po_break_down_id and a.BOOKING_TYPE = 1 and
		b.status_active = 1 AND b.is_deleted = 0 and a.status_active = 1 AND a.is_deleted = 0 and t.entry_form=109 and t.user_id=$user_id and t.ref_from=7";
 	
	 $booking_grouping = array();
	 foreach(sql_select($iribQuery) as $irdata){
		 $booking_grouping[$irdata[csf('booking_no')]]  = $irdata[csf('grouping')];
	 }

	// ======================= Receive SQL End =====================================

	// ============================== $productionBarcodeData Start =================
	//fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 5884, 1,$rcvTransBarcodeArr, $empty_arr);
	

	if(!empty($rcvTransBarcodeArr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id
		from inv_receive_master c, pro_grey_prod_entry_dtls a, pro_roll_details b, tmp_barcode_no d
		where a.mst_id = c.id and a.id=b.dtls_id and b.barcode_no=d.barcode_no and d.userid=$user_id and c.entry_form = 2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1");
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
			$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 5885, 1,$allDeterArr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 5886, 1,$allColorArr, $empty_arr);
		oci_commit($con);
	}
	// ============================== $productionBarcodeData End ==================

	// ============================== Receive Data Array Start ====================
	$data_array = array();$poArr = array();
	foreach ($receive_sql_arr  as $val)
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source ==0)
		{
			$orderId = $val[csf('po_breakdown_id')];
			$poArr[$orderId] = $orderId;
			$febric_description_id=$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"];
			$color_id=$prodBarcodeData[$val[csf("barcode_no")]]["color_id"];
			if ($color_id=="")
			{
				$color_id=0;
			}
			// echo $color_id.'='.$val[csf("barcode_no")].'<br>';
			$data_array[$orderId][$febric_description_id][$color_id]["cust_buyer"] = $val[csf("customer_buyer")];
			$data_array[$orderId][$febric_description_id][$color_id]["fso_booking_no"] = $val[csf("sales_booking_no")];
			$data_array[$orderId][$febric_description_id][$color_id]["rcv_qnty"] +=  $val[csf("rcv_qnty")];
		}
	}
	// echo '<pre>';print_r($data_array);
	// ============================== Receive Data Array End =======================

	// ============================== Issue Data Array Start =======================
	if(!empty($poArr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 109, 1,$poArr, $empty_arr);
		oci_commit($con);

		// ====================== Issue Sql start =====================
		$sqlNoOfRollIssue="SELECT b.po_breakdown_id, d.qnty AS issue_qty, d.barcode_no
		FROM GBL_TEMP_ENGINE T, fabric_sales_order_mst a, order_wise_pro_details b, inv_transaction c, pro_roll_details d
		WHERE t.ref_val=a.id and a.id = b.po_breakdown_id and b.trans_id = c.id and b.dtls_id = d.dtls_id and
		b.status_active = 1 AND b.is_deleted = 0 AND b.entry_form IN(61) AND b.trans_type = 2 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND d.entry_form IN(61) and d.is_sales=1 and t.entry_form=109 and t.user_id=$user_id and t.ref_from=1";
		// echo $sqlNoOfRollIssue;die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$issTransBarcodeArr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
		}
		// unset($sqlNoOfRollIssueResult);
		// ====================== Issue Sql End =====================

		// ====================== Roll Splitting After Issue Start ===================
		$barcode_cond='';
		if(!empty($issTransBarcodeArr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 61, 2,$issTransBarcodeArr, $empty_arr);
			oci_commit($con);

			$split_chk_sql = sql_select("SELECT c.barcode_no, c.qnty from pro_roll_split b, pro_roll_details c, GBL_TEMP_ENGINE d where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 and b.barcode_no=d.ref_val and d.entry_form=61 and d.user_id=$user_id and d.ref_from=2");
			if(!empty($split_chk_sql))
			{
				foreach ($split_chk_sql as $val)
				{
					$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
				}

				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 75, 3,$split_barcode_arr, $empty_arr);
				oci_commit($con);

				$split_ref_sql = sql_select("SELECT a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b, GBL_TEMP_ENGINE c where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1  and a.barcode_no=c.ref_val and c.entry_form=75 and c.user_id=$user_id and c.ref_from=3");

				if(!empty($split_ref_sql))
				{
					foreach ($split_ref_sql as $value)
					{
						$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
						//$issTransBarcodeArr[$value[csf("mother_barcode")]] =$value[csf("mother_barcode")];
					}
				}
			}
		}
		// ====================== Roll Splitting After Issue End =====================

		// ====================== Issue Data array start =====================
		$issueQtyArr=array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
			if($mother_barcode_no != "")
			{
				$febric_description_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
				$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
			}
			else
			{
				$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
				$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
			}

			$orderId = $row[csf('po_breakdown_id')];
			if ($color_id=="")
			{
				$color_id=0;
			}
			//echo $febric_description_id.'='.$color_id.'='.$row[csf("barcode_no")].'='.$row[csf('issue_qty')].'=<br>';
			$issueQtyArr[$orderId][$febric_description_id][$color_id]['issueQty'] += $row[csf('issue_qty')];
		}
		// ====================== Issue Data array end =====================
	}
	// echo "<pre>"; print_r($issueQtyArr);
	// ============================== Issue Data Array End =========================

	// =================== for yarn_count_determination Start ======================
	$allDeterArr = array_filter($allDeterArr);
	if(!empty($allDeterArr))
	{
		$construction_arr=array(); $composition_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, GBL_TEMP_ENGINE c where a.id=b.mst_id and a.id=c.ref_val and c.entry_form=5885 and c.user_id=$user_id and c.ref_from=1";
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
		$color_array=return_library_array( "SELECT b.id, b.color_name from GBL_TEMP_ENGINE a, lib_color b where b.status_active=1 and a.ref_val=b.id and a.entry_form=5886 and a.user_id=$user_id and a.ref_from=1 $allColorCond", "id", "color_name");
	}
	// =================== for lib_color end =======================================
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (5884,109,61,75,5885,5886)");
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
	<div style="width:690px" id="main_body">
		<table width="690" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary Sales</td>
			</tr>
			<tr style="border:none;">
				<td colspan="7" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="670" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Sales Job/ Booking No.</th>
					<th width="100">IR/IB</th>
					<th width="100">Cust Buyer</th>
					<th width="100">Construction</th>
					<th width="80">Color</th>
					<th width="80">Receive Qty</th>
					<th width="">Issue Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:690px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="670" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1;
					foreach($data_array as $order_id_k => $order_id_v)
					{
						foreach($order_id_v as $detar_id_k => $detar_id_v)
						{
							$sub_total_rcv_qnty=$sub_total_issue_qnty=0;
							foreach($detar_id_v as $color_k => $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$issueQty=$issueQtyArr[$order_id_k][$detar_id_k][$color_k]['issueQty'];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $i;?></td>
	                                <td width="100"><p><? echo $row["fso_booking_no"];?></p></td>
	                                <td width="100"><p><? echo $booking_grouping[$row["fso_booking_no"]];?></p></td>
	                                <td width="100" align="center"><p><? echo $buyer_arr[$row["cust_buyer"]];?></p></td>
	                                <td width="100" title="<?echo $detar_id_k;?>"><p><? echo $construction_arr[$detar_id_k];?></p></td>
	                                <td width="80" title="<?echo $color_k;?>">
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
	                                <td width="80" align="right"><? echo number_format($row["rcv_qnty"],2,'.',''); ?></td>
	                                <td width="" align="right"><? echo number_format($issueQty,2,'.',''); ?></td>
								</tr>
								<?
								$total_rcv_qnty += $row["rcv_qnty"];
								$total_issue_qnty += $issueQty;
								$sub_total_rcv_qnty += $row["rcv_qnty"];
								$sub_total_issue_qnty += $issueQty;
								$i++;
							}
							?>
							<tr bgcolor="#e4e4e4">
								<td width="30"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="80" align="right"><strong>Sub Total</strong></td>
								<td width="80" align="right"><strong><? echo number_format($sub_total_rcv_qnty,2,'.',''); ?></strong></td>
								<td width="" align="right"><strong><? echo number_format($sub_total_issue_qnty,2,'.',''); ?></strong></td>
							</tr>
							<?
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="670" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="80" align="right"><strong>Grand Total</strong></th>
					<th width="80" align="right"><strong><? echo number_format($total_rcv_qnty,2,'.',''); ?></strong></th>
					<th width="" align="right"><strong><? echo number_format($total_issue_qnty,2,'.',''); ?></strong></th>
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

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info For Grey Store Wise Receive Issue Summary Sales", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$barcode_nos = $barcode_nos;
	$company_id  = $company_id;
	$store_id    = $store_id;
	$entry_form  = $entry_form;

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	?>
	<fieldset style="width:1040; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="12"><b>Barcode Details</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Knitting Production ID</th>
                        <th width="80">Roll No</th>
                        <th width="80">Roll Weight</th>
                        <th width="80">Barcode No</th>
                        <th width="140">Store</th>
                        <th width="80">Floor</th>
                        <th width="80">Room</th>
                        <th width="80">Rack</th>
                        <th width="80">Shelf</th>
                        <th width="80">Bin</th>
                    </tr>
				</thead>
            </table>
            <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0" id="table_body">
                <?
				if(!empty($barcode_nos))
				{
					$barcodeData=sql_select("SELECT a.recv_number, c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where
					a.id = b.mst_id and b.id=c.dtls_id and a.entry_form = 2 and c.entry_form = 2 and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1
					and c.is_deleted = 0 and c.barcode_no in ($barcode_nos)");

					$barcodeReceive=sql_select("SELECT a.id, c.store_id,c.floor_id, c.room, c.rack, c.self, c.bin_box, d.barcode_no, d.qnty,d.roll_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c, pro_roll_details d, fabric_sales_order_mst e WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and d.po_breakdown_id = e.id and d.is_sales=1 and d.entry_form in($entry_form)  and a.entry_form in($entry_form) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 and d.barcode_no in ($barcode_nos)  ORDER BY a.receive_date");

					$roll_details_array=array();
					foreach ($barcodeReceive as $rows)
					{
						$roll_details_array[$rows[csf("barcode_no")]]['qnty'] = $rows[csf("qnty")];
						$roll_details_array[$rows[csf("barcode_no")]]['roll_no'] = $rows[csf("roll_no")];
						$roll_details_array[$rows[csf("barcode_no")]]['store_id'] = $rows[csf("store_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['floor_id'] = $rows[csf("floor_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['room'] = $rows[csf("room")];
						$roll_details_array[$rows[csf("barcode_no")]]['rack'] = $rows[csf("rack")];
						$roll_details_array[$rows[csf("barcode_no")]]['self'] = $rows[csf("self")];
						$roll_details_array[$rows[csf("barcode_no")]]['bin_box'] = $rows[csf("bin_box")];
					}

					$barcodeIssue=sql_select("SELECT a.id,b.floor_id, b.room, b.rack, b.self, b.bin_box,b.bin_box,d.qnty,d.roll_no,d.barcode_no
					from inv_issue_master a, inv_grey_fabric_issue_dtls b,  pro_roll_details d, fabric_sales_order_mst e
					where a.id = b.mst_id  and b.id = d.dtls_id and a.id = d.mst_id and d.po_breakdown_id = e.id and a.entry_form in($entry_form) and d.entry_form in($entry_form) and d.status_active =1 and b.status_active =1 and a.status_active =1 and d.barcode_no in ($barcode_nos) order by a.issue_date");

					foreach ($barcodeIssue as $rows)
					{
						$roll_details_array[$rows[csf("barcode_no")]]['qnty'] = $rows[csf("qnty")];
						$roll_details_array[$rows[csf("barcode_no")]]['roll_no'] = $rows[csf("roll_no")];
						$roll_details_array[$rows[csf("barcode_no")]]['store_id'] = $rows[csf("store_name")];
						$roll_details_array[$rows[csf("barcode_no")]]['floor_id'] = $rows[csf("floor_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['room'] = $rows[csf("room")];
						$roll_details_array[$rows[csf("barcode_no")]]['rack'] = $rows[csf("rack")];
						$roll_details_array[$rows[csf("barcode_no")]]['self'] = $rows[csf("self")];
						$roll_details_array[$rows[csf("barcode_no")]]['bin_box'] = $rows[csf("bin_box")];
					}

					$transfer_sql=sql_select("SELECT a.id, c.store_id,c.floor_id, c.room, c.rack, c.self, c.bin_box, d.barcode_no, d.qnty,d.roll_no
					FROM inv_item_transfer_mst a,inv_item_transfer_dtls b, inv_transaction c, pro_roll_details d, fabric_sales_order_mst e
					WHERE a.id=b.mst_id and b.trans_id = c.id and a.id = d.mst_id and b.id = d.dtls_id and a.from_order_id = e.id and a.entry_form in($entry_form)  and d.entry_form in($entry_form) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category =13 and d.barcode_no in ($barcode_nos)
					ORDER BY a.transfer_date");

					foreach ($transfer_sql as $rows)
					{
						$roll_details_array[$rows[csf("barcode_no")]]['qnty'] = $rows[csf("qnty")];
						$roll_details_array[$rows[csf("barcode_no")]]['roll_no'] = $rows[csf("roll_no")];
						$roll_details_array[$rows[csf("barcode_no")]]['store_id'] = $rows[csf("store_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['floor_id'] = $rows[csf("floor_id")];
						$roll_details_array[$rows[csf("barcode_no")]]['room'] = $rows[csf("room")];
						$roll_details_array[$rows[csf("barcode_no")]]['rack'] = $rows[csf("rack")];
						$roll_details_array[$rows[csf("barcode_no")]]['self'] = $rows[csf("self")];
						$roll_details_array[$rows[csf("barcode_no")]]['bin_box'] = $rows[csf("bin_box")];
					}

					//floorSql
					$floorSql = "
					SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
					FROM lib_floor_room_rack_mst a
					INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
					WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//roomSql
					$roomSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					//echo $roomSql;
					$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//rackSql
					$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//selfSql
					$selfSql = "
					SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
					FROM lib_floor_room_rack_mst a
					INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
					WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$selfDetails = return_library_array( $selfSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//binSql
					$binSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$company_id.")
					";
					$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');


				}else{
					 echo "Barcode Not Found";
				}
                if(empty($barcodeData)){
                	echo "Barcode Not Found";
                }
				$i=1;
				foreach ($barcodeData as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="120" align="center"><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td width="80" align="center"><p><? echo $roll_details_array[$row[csf("barcode_no")]]['roll_no']; ?></p>&nbsp;</td>
                        <td width="80" align="right"><? echo number_format($roll_details_array[$row[csf("barcode_no")]]['qnty'],2,'.',''); ?></td>
                        <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="140" align="center"><? echo $store_arr[$store_id]; ?></td>
                        <td width="80" align="center"><? echo $floorDetails[$roll_details_array[$row[csf("barcode_no")]]['floor_id']]; ?></td>
                        <td width="80" align="center"><? echo $roomDetails[$roll_details_array[$row[csf("barcode_no")]]['room']]; ?></td>
                        <td width="80" align="center"><? echo $rackDetails[$roll_details_array[$rows[csf("barcode_no")]]['rack']]; ?></td>
                        <td width="80" align="center"><? echo $selfDetails[$roll_details_array[$rows[csf("barcode_no")]]['self']]; ?></td>
                        <td width="80" align="center"><? echo $binDetails[$roll_details_array[$rows[csf("barcode_no")]]['bin_box']]; ?></td>
                    </tr>
                <?
                $total_qty+=$roll_details_array[$row[csf("barcode_no")]]['qnty'];
                $i++;
                }
                ?>
                <tfoot>
                	<tr>
                        <th colspan="3" align="right">Total</th>
                        <th align="right"><? echo number_format($total_qty,2); ?></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                    </tr>

                </tfoot>
            </table>
		</div>
	</fieldset>
  <script>
  setFilterGrid("table_body",-1);
  </script>
    <?
	exit();
}
?>

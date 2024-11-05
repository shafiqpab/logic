<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../../../includes/common.php');

	$_SESSION['page_permission']=$permission;
	$user_id=$_SESSION['logic_erp']['user_id'];
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "" );
	exit();
}

if($action=="fabricBooking_popup")
{
	echo load_html_head_contents("WO Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var tableFilters =
		{
			col_11: "select",
			display_all_text:'Show All'
		}

		function js_set_value(data,is_approved)
		{
			if(is_approved==1)
			{
				$('#hidden_booking_data').val(data);
				parent.emailwindow.hide();
			}
			else
			{
				alert("Approved Booking First.");
				return;
			}
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
				<fieldset style="width:98%;">
					<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Enter search words</h3>
					<div id="content_search_panel" >
						<table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
							<thead>
								<th>Buyer</th>
								<th>Unit</th>
								<th>Booking Date</th>
								<th>Search By</th>
								<th id="search_by_td_up" width="200">Please Enter Booking No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $companyID; ?>">
									<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" class="text_boxes" value="">
								</th>
							</thead>
							<tr class="general">
								<td align="center">
									<?
									$user_wise_buyer = $_SESSION['logic_erp']['buyer_id'];
									$buyer_sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in ($user_wise_buyer) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
									echo create_drop_down( "cbo_buyer", 150, $buyer_sql,"id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
								<td align="center">
									<? echo create_drop_down( "cbo_buyer_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Unit --", $selected, "",$data[0] ); ?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
								</td>
								<td align="center">
									<?
									$search_by_arr=array(1=>"Booking No",2=>"Job No");
									$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value, 'create_booking_search_list_view', 'search_div', 'knit_prog_wise_grey_fab_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="5" align="center"  valign="middle"><? echo load_month_buttons(1); ?></td>
							</tr>
						</table>
					</div>
					<table width="100%" style="margin-top:5px">
						<tr>
							<td colspan="5">
								<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_search_list_view")
{
	$data = explode("_",$data);

	$search_string 	= "%".trim($data[0])."%";
	$search_by 		= $data[1];
	$company_id 	= $data[2];
	$unit_id 		= $data[3];
	$date_from 		= trim($data[4]);
	$date_to 		= trim($data[5]);
	$buyer_id 		= $data[6];
	$buyer_arr 		= return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	if($unit_id==0) $unit_id_cond=""; else $unit_id_cond=" and a.company_id=$unit_id";
	if($buyer_id==0) $buyer_id_cond= $buyer_id_cond; else $buyer_id_cond=" and a.buyer_id=$buyer_id";

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.booking_no like '$search_string'";
		else
			$search_field_cond="and a.job_no like '$search_string'";
	}

	$date_cond='';
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$import_booking_id_arr=return_library_array( "select id, booking_id from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0",'id','booking_id');

	$apporved_date_arr=return_library_array( "select mst_id, max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id",'mst_id','approved_date');

	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$sql= "SELECT a.booking_no_prefix_num, a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season, a.remarks FROM wo_booking_mst a,wo_booking_dtls d, wo_po_details_master b WHERE a.booking_no = d.booking_no and d.job_no =b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond and a.id in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id)  group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.id DESC";


	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="65">Buyer</th>
			<th width="65">Unit</th>
			<th width="90">Booking No</th>
			<th width="50">Booking ID</th>
			<th width="90">Job No</th>
			<th width="110">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th width="80">App. Date</th>
			<th width="80">Delivery Date</th>
			<th width="70">Currency</th>
			<th width="60">Approved</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:1080px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
			//if(!in_array($row[csf('id')],$import_booking_id_arr))
			//{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('po_break_down_id')]!="")
				{
					$po_no='';
					$po_ids=explode(",",$row[csf('po_break_down_id')]);
					foreach($po_ids as $po_id)
					{
						if($po_no=="") $po_no=$po_arr[$po_id]; else $po_no.=",".$po_arr[$po_id];
					}
				}

				$data=$row[csf('id')].'__'.$row[csf('booking_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
			//}
			}

		//partial booking...........................................................start;
			$partial_sql= "SELECT a.booking_no_prefix_num, a.id, d.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, listagg(d.po_break_down_id, ',') within group (order by d.po_break_down_id) as po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks FROM wo_booking_mst a, wo_po_details_master b,wo_booking_dtls d WHERE a.booking_no=d.booking_no and d.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.entry_form=108 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond and a.id in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id) group by a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date, a.entry_form,a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.id DESC";
			$partial_result = sql_select($partial_sql);
			foreach ($partial_result as $row)
			{
			//if(!in_array($row[csf('id')],$import_booking_id_arr))
			//{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('po_break_down_id')]!="")
				{
					$po_no='';
					$po_ids=array_unique(explode(",",$row[csf('po_break_down_id')]));
					foreach($po_ids as $po_id)
					{
						if($po_no=="") $po_no=$po_arr[$po_id]; else $po_no.=",".$po_arr[$po_id];
					}
				}
				$data=$row[csf('id')].'__'.$row[csf('booking_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
			//}
			}
		//partial booking...........................................................end;
			?>
		</table>
	</div>
	<?
	exit();
} // Booking Search end


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
		<fieldset style="width:830px;margin-left:4px;">
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year; ?>, 'create_order_no_search_list_view', 'search_div', 'knit_prog_wise_grey_fab_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$cbo_year=$data[4];

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";


	if($cbo_year>0){
		if($db_type==0) $year_cond="and YEAR(insert_date) =$cbo_year";
		else if($db_type==2) $year_cond="and to_char(insert_date,'YYYY') =$cbo_year";
		else $year_cond="";//defined Later
	}

	if($db_type==0) $year_field="YEAR(insert_date) as year";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
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
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond  $year_cond order by id DESC";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
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
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no_prefix_num')];
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

if($action=="report_generate_old")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$program_no= str_replace("'","",$txt_program_no);
	$date_from= str_replace("'","",$txt_date_from);

	if($within_group) $within_group_cond = " and a.within_group='$within_group' "; else $within_group_cond = "";

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and a.po_buyer in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else if ($within_group==2 )
		{
			$buyer_id_cond=" and a.buyer_id in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else
		{
			$buyer_id_cond="";
		}

	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond=" and a.po_company_id='$pocompany_id' ";
	$date_cond="";$prog_date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
		$prog_date_cond = " and b.program_date='$date_from'";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(c.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(c.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}



	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and a.job_no_prefix_num='$order_no'";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and d.id='$program_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and a.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and a.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}
		$prog_cond_booking_no = " and a.booking_no like '%$booking_no%'";
		$prog_cond_booking_no .= " and a.booking_no like '%-".substr($year_id, -2)."-%'";
	} else {
		$booking_no_cond="";
	}

	$variable_data=sql_select("select variable_list, fabric_roll_level, auto_update from variable_settings_production where company_name ='$company_name' and variable_list in(3,15) and item_category_id=13 and is_deleted=0 and status_active=1");
	foreach($variable_data as $row)
	{
		if($row[csf('variable_list')]==3)
		{
			$roll_maintained=$row[csf('fabric_roll_level')];
		}
		else
		{
			$fabric_store_auto_update=$row[csf('auto_update')];
		}
	}

	if ($program_no=="") $program_cond_trans=""; else $program_cond_trans=" and a.id in ($program_no) ";
	$programSqlForTrans = sql_select("select b.id , a.booking_no
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b
		where a.id = b.mst_id
		and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		and a.company_id = $company_name $program_cond_trans $prog_cond_booking_no $prog_date_cond");
	$programNos = "";
	foreach($programSqlForTrans as $prog)
	{
		$programNos .= $prog[csf("id")].",";
		$all_prog_for_issue[$prog[csf("id")]] = $prog[csf("id")];
	}

	$programNos = chop($programNos,",");
	$all_program_no_arr = array_filter(explode(",",$programNos));
	if(count($all_program_no_arr)>0)
	{
		$all_program_no_cond=""; $progCond="";
		if($db_type==2 && count($all_program_no_arr)>999)
		{
			$all_program_no_arr_chunk=array_chunk($all_program_no_arr,999) ;
			foreach($all_program_no_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$progCond.="  b.to_program in($chunk_arr_value) or ";
			}

			$all_program_no_cond.=" and (".chop($progCond,'or ').")";
		}
		else
		{

			$all_program_no_cond=" and b.to_program in(".implode(",", $all_program_no_arr).")";
		}
	}
	unset($all_program_no_arr);
	unset($programSqlForTrans);


	// Main Query
	$sql="select a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id

	from fabric_sales_order_mst a, fabric_sales_order_dtls b, ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls e

	where a.company_id='$company_name' and a.id=b.mst_id and c.id=d.mst_id and d.id=e.dtls_id and a.id=e.po_id and c.is_sales=1 and e.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $within_group_cond $buyer_id_cond $pocompany_cond $date_cond $order_no_cond $program_no_cond $booking_no_cond $year_search_cond
	group by a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, e.fabric_desc, e.program_qnty,e.po_id,d.knitting_source,d.knitting_party, d.id, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type";
	$nameArray=sql_select($sql);
	$to_poids="";
	foreach($nameArray as $row)
	{
		$program_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
		$booking_no_arr[] = "'".$row[csf("sales_booking_no")]."'";
		$to_poids.= $row[csf("id")].",";

		$trans_row_ref_arr[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$trans_row_ref_arr[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$trans_row_ref_arr[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$trans_row_ref_arr[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_source'] = $row[csf('knitting_source')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_party'] = $row[csf('knitting_party')];
		$trans_row_ref_arr[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$trans_row_ref_arr[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
		$trans_row_ref_arr[$row[csf('id')]]['po_buyer'] = $row[csf('po_buyer')];
	}

	$to_poids = implode(",", array_filter(array_unique(explode(",",chop($to_poids,",")))));
	$to_pocond = $trns_to_po_cond = "";
	$to_poids_arr=explode(",",$to_poids);
	if(count($to_poids_arr)>0)
	{
		if($db_type==2 && count($to_poids_arr)>999)
		{
			$to_poids_chunk=array_chunk($to_poids_arr,999) ;
			foreach($to_poids_chunk as $chunk_arr)
			{
				$to_pocond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$trns_to_po_cond.=" and (".chop($to_pocond,'or ').")";
		}
		else
		{
			$trns_to_po_cond=" and a.to_order_id in($to_poids)";
		}
	}

	$data_trans=sql_select("select a.to_order_id, b.to_program,e.po_buyer,e.buyer_id,
		sum(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in, count(d.id) as roll_no
		from inv_item_transfer_mst a,inv_item_transfer_dtls b,order_wise_pro_details c,pro_roll_details d,fabric_sales_order_mst e
		where a.entry_form=133 and a.item_category=13  and a.transfer_criteria=4 and a.status_active=1 and a.is_deleted=0
		and a.id=b.mst_id and b.id=c.dtls_id and c.dtls_id=d.dtls_id and d.po_breakdown_id= e.id
		and b.to_program>0 and c.trans_type in(5) and d.status_active=1 and d.is_deleted=0
		and c.entry_form=133 and d.entry_form = 133 $trns_to_po_cond $all_program_no_cond
		group by a.to_order_id, b.to_program,e.po_buyer,e.buyer_id");

	$trns_row_data=array();
	foreach($data_trans as $row_b)
	{
		$trns_row_data[$row_b[csf('to_program')]]=$row_b[csf('to_order_id')].'!!!!'.$row_b[csf('item_transfer_in')].'!!!!'.$row_b[csf('roll_no')];
		$trns_row_prog_ref_data[$row_b[csf('to_program')]] = $row_b[csf('to_program')];

		$program_no_arr[$row_b[csf("to_program")]] = $row_b[csf("to_program")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["po_buyer"] = $row_b[csf("po_buyer")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["buyer_id"] = $row_b[csf("buyer_id")];
	}
	unset($data_trans);


	$program_no_arr = array_filter($program_no_arr);

	if(count($program_no_arr)>0)
	{
		$program_no_arr = explode(",","'".implode("','",$program_no_arr)."'");

		$all_program_nos = implode(",",$program_no_arr);
		$progCond = $all_rcv_prog_cond = "";
		$to_progcond=$from_progcond=$transfer_all_prog_cond="";

		if($db_type==2 && count($program_no_arr)>999)
		{
			$program_no_arr_chunk=array_chunk($program_no_arr,999) ;
			foreach($program_no_arr_chunk as $chunk_arr)
			{
				$progCond.=" d.booking_no in(".implode(",",$chunk_arr).") or ";
				$progCond2.=" b.program_no in(".implode(",",$chunk_arr).") or ";

				$to_progcond.=" b.to_program in(".implode(",",$chunk_arr).") or ";
				$from_progcond.=" b.from_program in(".implode(",",$chunk_arr).") or ";
			}

			$all_rcv_prog_cond.=" and (".chop($progCond,'or ').")";
			$all_rcv_prog_cond2.=" and (".chop($progCond2,'or ').")";

			$transfer_all_prog_cond .= " and (" .chop($to_progcond,'or ')." or ". chop($from_progcond,'or ') .")";
		}
		else
		{
			$all_rcv_prog_cond=" and d.booking_no in($all_program_nos)";
			$all_rcv_prog_cond2=" and b.program_no in($all_program_nos)";

			$transfer_all_prog_cond = " and ( b.to_program in ($all_program_nos) or  b.from_program in ($all_program_nos) ) ";
		}

		$qnty_field = ($roll_maintained==1)?" sum(d.qnty)":"sum(c.quantity)";
		$qnty_field_cond = ($roll_maintained==1)?" and d.status_active=1 and d.is_deleted=0":"";

		$production_ref = sql_select("select a.booking_no, c.po_breakdown_id as po_id, $qnty_field as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no, c.trans_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
			left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(2) and d.is_sales= 1
			where a.item_category=13 and a.receive_basis =2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and  c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $all_rcv_prog_cond
			group by a.booking_no,c.po_breakdown_id,d.barcode_no,c.trans_id");

		foreach ($production_ref as $row)
		{
			if($row[csf('trans_id')] >0){
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
			}
			else
			{
				$production_barcode[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				$production_barcode_ref[$row[csf('barcode_no')]]["booking_no"] = $row[csf('booking_no')];
			}
			$prod_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}

		if(!empty($production_barcode)){
			$production_barcode = array_filter($production_barcode);
			$all_production_barcode_nos = implode(",", $production_barcode);
			$all_production_barcode_cond=""; $barCond="";
			if($db_type==2 && count($production_barcode)>999)
			{
				$production_barcode_chunk=array_chunk($production_barcode,999) ;
				foreach($production_barcode_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  d.barcode_no in($chunk_arr_value) or ";
				}

				$all_production_barcode_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				if($all_production_barcode_nos!=""){
					$all_production_barcode_cond=" and d.barcode_no in($all_production_barcode_nos)";
				}
			}
		}

		if(!empty($prod_id_arr)){
			$prod_id_arr = array_filter($prod_id_arr);
			$all_production_po_nos = implode(",", $prod_id_arr);
			$all_production_po_cond=""; $barCond="";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$production_po_chunk=array_chunk($prod_id_arr,999) ;
				foreach($production_po_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$all_production_po_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$all_production_po_cond=" and c.po_breakdown_id in($all_production_po_nos)";
			}
		}

		$sql_recv=sql_select("select a.entry_form,a.booking_no, c.po_breakdown_id as po_id, $qnty_field as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
			left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(2,22,58) and d.is_sales= 1 $qnty_field_cond
			where a.item_category=13 and a.entry_form in(2,22,58) and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(2,22,58) $all_production_barcode_cond $all_production_po_cond
			and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			group by a.booking_no,c.po_breakdown_id,d.barcode_no,a.entry_form");

		$recv_array=array(); $production_booking="";
		foreach($sql_recv as $row)
		{
			if($row[csf('entry_form')]==2){
				$production_booking = $row[csf('booking_no')];
			}else{
				$production_booking = $production_barcode_ref[$row[csf('barcode_no')]]["booking_no"];
			}

			$recv_array[$production_booking][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
			$recv_array[$production_booking][$row[csf('po_id')]]['roll']=$row[csf('roll')];
			$recv_array[$production_booking][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
		}
		unset($sql_recv);

		/*$sql_recv=sql_select("select d.booking_no, d.po_breakdown_id as po_id, sum(d.qnty) as knitting_qnty , count(d.barcode_no) as roll_no
				from inv_receive_master a, pro_grey_prod_entry_dtls b,  pro_roll_details d
				where a.id=b.mst_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form =58  and d.entry_form =58 and a.receive_basis=10  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_sales= 1 and d.status_active=1 and d.is_deleted=0 $all_rcv_prog_cond
				group by d.booking_no,d.po_breakdown_id
		union all
		select d.booking_no, c.po_breakdown_id as po_id, sum(d.qnty) as knitting_qnty ,count(d.barcode_no) as roll_no from
			inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_roll_details d where a.id=b.mst_id and c.dtls_id=b.id and b.id=d.dtls_id and a.item_category=13 and a.entry_form =2 and c.entry_form=2 and d.entry_form =2 and a.receive_basis=2 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $all_rcv_prog_cond
			group by d.booking_no,c.po_breakdown_id");


		$recv_array=array();
		foreach($sql_recv as $row)
		{
			$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
			$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['roll']=$row[csf('roll')];
			$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['roll_count']=$row[csf('roll_no')];
		}
		unset($sql_recv);*/
		/*$sql_iss=sql_select("select b.remarks, c.po_breakdown_id, count(b.id) as roll, $qnty_field as issue_qty, b.program_no as prog_no, count(d.barcode_no) as roll_no
			from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c
			left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(16,61) and d.is_sales=1 $qnty_field_cond
			where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.entry_form in(16,61) and c.trans_type=2 and c.entry_form in(16,61) and a.issue_purpose=11 and a.status_active=1
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_rcv_prog_cond2
			group by b.remarks, c.po_breakdown_id, b.program_no");*/

			$sql_iss=sql_select("select b.remarks, c.po_breakdown_id, count(b.id) as roll, $qnty_field as issue_qty, (case when a.entry_form = 16 then cast (b.program_no as varchar2(4000))  when a.entry_form = 61 then cast(d.booking_no as varchar2(4000)) else '0' end) as  prog_no, count(d.barcode_no) as roll_no from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(16,61) and d.is_sales=1 $qnty_field_cond where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.entry_form in(16,61) and c.trans_type=2 and c.entry_form in(16,61) and a.issue_purpose=11 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $issue_all_prog_cond group by b.remarks, c.po_breakdown_id, b.program_no, d.booking_no,a.entry_form");

			$knit_issue_arr=array();
			foreach($sql_iss as $row)
			{
				$knit_issue_arr[$row[csf('prog_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('issue_qty')];
				$knit_issue_arr[$row[csf('prog_no')]][$row[csf('po_breakdown_id')]]['remarks']=$row[csf('remarks')];
				$knit_issue_arr[$row[csf('prog_no')]][$row[csf('po_breakdown_id')]]['roll']=$row[csf('roll')];
				$knit_issue_arr[$row[csf('prog_no')]][$row[csf('po_breakdown_id')]]['roll_no']=$row[csf('roll_no')];
			}
			unset($sql_iss);


			$trans_data_array=sql_select("select a.from_order_id, a.to_order_id, b.from_program, b.to_program ,d.barcode_no,c.trans_type,d.qnty,b.id as dtls_id
				from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c
				left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form=133 and d.is_sales=1
				where a.id=b.mst_id and c.dtls_id=b.id and c.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and c.entry_form=133
				and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4 and b.from_program>0 and b.to_program>0 and b.status_active=1 and b.is_deleted=0 $transfer_all_prog_cond");


			$transfer_qty_arr=array();
			$chkDtlsIdArr=array();
			$rt = 1;
			foreach($trans_data_array as $row_b)
			{
				$chkDtlsIdArr[$row_b[csf("dtls_id")]] = $row_b[csf("dtls_id")];
				if($row_b[csf("trans_type")] == "6")
				{
					$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transfer_out']+=$row_b[csf('qnty')];
					$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transOut_roll_no']+=$rt;
				}else{
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transfer_in']+=$row_b[csf('qnty')];
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transIn_roll_no']+=$rt;
				}
			}
  		//print_r($transfer_qty_arr["18460"]);die;

			$all_trans_prog =implode(",",array_filter(array_unique($trns_row_prog_ref_data)));
			if($all_trans_prog)
			{
				if($all_trans_prog=="") $all_trans_prog=0;
				$progCond = $all_trans_prog_cond = "";
				$all_trans_prog_arr=explode(",",$all_trans_prog);
				if($db_type==2 && count($all_trans_prog_arr)>999)
				{
					$all_trans_prog_chunk=array_chunk($all_trans_prog_arr,999) ;
					foreach($all_trans_prog_chunk as $chunk_prog)
					{
						$chunk_prog_val=implode(",",$chunk_prog);
						$progCond.=" d.id in($chunk_prog_val) or ";
					}
					$all_trans_prog_cond.=" and (".chop($progCond,'or ').")";
				}
				else
				{
					$all_trans_prog_cond=" and d.id in($all_trans_prog)";
				}
				$trans_pro_ref = sql_select("select d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id from ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls e where c.id=d.mst_id and d.id=e.dtls_id  and c.is_sales=1 and e.is_sales=1 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $all_trans_prog_cond");

				foreach($trans_pro_ref as $row)
				{
					$trns_data_arr[$row[csf('prog_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
					$trns_data_arr[$row[csf('prog_no')]]['booking_id']=$row[csf('booking_id')];
					$trns_data_arr[$row[csf('prog_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$trns_data_arr[$row[csf('prog_no')]]['job_no']=$row[csf('job_no')];
					$trns_data_arr[$row[csf('prog_no')]]['knitting_source']=$row[csf('knitting_source')];
					$trns_data_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
					$trns_data_arr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
					$trns_data_arr[$row[csf('prog_no')]]['mc_dia_gg']=$row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
					$trns_data_arr[$row[csf('prog_no')]]['width_dia_type']=$row[csf('width_dia_type')];
					$trns_data_arr[$row[csf('prog_no')]]['fabric_desc']=$row[csf('fabric_desc')];
					$trns_data_arr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
					$trns_data_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
					$trns_data_arr[$row[csf('prog_no')]]['style']=$row[csf('style_ref_no')];
					$trns_data_arr[$row[csf('prog_no')]]['group']=$row[csf('within_group')];
				}
				unset($trans_pro_ref);
			}

			$yarn_lot_data=sql_select("select d.booking_id, a.yarn_lot,a.color_range_id,a.color_id,a.width as dia_width,a.stitch_length,a.gsm,a.yarn_count, b.po_breakdown_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b, inv_receive_master d where d.id=a.mst_id and a.id=b.dtls_id and b.entry_form=2 and d.entry_form=2 and d.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_sales=1 $all_rcv_prog_cond");


			foreach($yarn_lot_data as $rows)
			{
				$yarn_lot_arr[$rows[csf('booking_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
				$yarn_lot_arr[$rows[csf('booking_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
				$yarn_lot_arr[$rows[csf('booking_id')]]['roll'] .=$rows[csf('no_of_roll')];
				$yarn_lot_arr[$rows[csf('booking_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
				$yarn_lot_arr[$rows[csf('booking_id')]]['color_id'] .=$rows[csf('color_id')].",";
				$yarn_lot_arr[$rows[csf('booking_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
				$yarn_lot_arr[$rows[csf('booking_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
				$yarn_lot_arr[$rows[csf('booking_id')]]['gsm'] .=$rows[csf('gsm')].",";

				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['roll'] .=$rows[csf('no_of_roll')];
				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_id'] .=$rows[csf('color_id')].",";
				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
				$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['gsm'] .=$rows[csf('gsm')].",";
			}

			unset($yarn_lot_data);
		}



		ob_start();
		?>
		<fieldset style="width:2390px;">
			<table width="2390" cellspacing="0" cellpadding="0" border="0" rules="all" >
				<tr class="form_caption">
					<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2380" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="80">Knitting Company</th>
					<th width="50">Prog. No</th>
					<th width="60">Prog. Date</th>
					<th width="80">PO Buyer</th>
					<th width="100">Style Ref.</th>
					<th width="100">Fab. Booking No</th>
					<th width="110">Sales Order No</th>
					<th width="200">Fab. Description</th>
					<th width="100">Color Range</th>
					<th width="120">Fab. Color</th>
					<th width="70">MC DXG</th>
					<th width="70">F.Dia</th>
					<th width="70">Dia Type</th>
					<th width="70">S/L</th>
					<th width="70">FGSM</th>
					<th width="60">Y/Count</th>
					<th width="60">Y/Lot</th>
					<th width="80">Prog Qty/kg</th>
					<th width="80">Receive Qty(kg)</th>
					<th width="80">Trans In Qty(kg)</th>
					<th width="80">Total Qty(kg)</th>
					<th width="80">Delivery Qty(kg)</th>
					<th width="80">Trans Out Qty(kg)</th>
					<th width="80">Total Qty(kg)</th>
					<th width="80">Stock Qty(kg)</th>
					<th width="80">Roll</th>
					<th>Remarks</th>
				</thead>
			</table>
			<div style="width:2380px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2360" class="rpt_table" id="tbl_list_search">
					<?

					if(count($nameArray)>0 || count($trns_row_data)>0)
					{
						$i=1;
						$row_stock=0;
						foreach($nameArray as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$prog_no = $row[csf("prog_no")];
							$po_id = $row[csf("po_id")];

							$kniting_company="";
							if($row[csf("knitting_source")]==1) $kniting_company=$company_arr[$row[csf("knitting_party")]]; else if ($row[csf("knitting_source")]==3) $kniting_company=$supplier_arr[$row[csf("knitting_party")]];


							$ex_color_range_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_range_id'],",")));

							$color_range_name="";
							foreach($ex_color_range_id as $range_id)
							{
								if($range_id>0)
								{
									if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
							} //print_r( $yarn_count_value);
						}
						$ex_color_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_id'],",")));

						$color_name="";
						foreach($ex_color_id as $color_id)
						{
							if($color_id>0)
							{
								if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
							} //print_r( $yarn_count_value);
						}
						$dia_width="";
						$dia_width=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['dia_width'],",")))));

						$stitch_length="";
						$stitch_length=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['stitch_length'],",")))));

						$gsm="";
						$gsm=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['gsm'],",")))));

						$y_count=chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['ycount'],",");
						$y_count_id=array_unique(explode(',',$y_count));

						$yarn_count_value='';
						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
							}
						}

						$yarn_lot = "";
						$yarn_lot = implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['lot'],",")))));

						$knit_issue_qty=$knit_issue_arr[$prog_no][$po_id]['qnty'];
						$iss_roll_no=$knit_issue_arr[$prog_no][$po_id]['roll'];

						//echo $prog_no."==".$po_id."|";
						$recv_qnty=$recv_array[$prog_no][$po_id]['rec_qty'];
						$ex_recv_roll=explode(",",$recv_array[$prog_no][$po_id]['roll']);
						$rec_roll_no=$recv_array[$prog_no][$po_id]['roll'];

						$recv_roll_count=$recv_array[$prog_no][$po_id]['roll_count'];
						$transOut_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transOut_roll_no'];
						$transIn_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transIn_roll_no'];
						$iss_roll_no_count=$knit_issue_arr[$prog_no][$po_id]['roll_no'];
						$remaining_roll = ($recv_roll_count + $transIn_roll_no_count) - ($transOut_roll_no_count + $iss_roll_no_count);



						$trans_qty_out=$transfer_qty_arr[$prog_no][$po_id]['transfer_out'];
						$trans_qty_in=$transfer_qty_arr[$prog_no][$po_id]['transfer_in'];
						$totalRecv=$recv_qnty+$trans_qty_in;
						$totalIssue=$knit_issue_qty+$trans_qty_out;

						$row_stock=$totalRecv-$totalIssue;

						$remark=$knit_issue_arr[$prog_no][$po_id]['remarks'];


						//echo $title= "[$prog_no][$po_id] ## rcv=$recv_roll_count,tranin=$trans_qty_in,tranout=$trans_qty_out,issue=$iss_roll_no_count";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"><p><? echo $kniting_company; ?></p></td>
							<td width="50"><? echo $row[csf("prog_no")]; ?></td>
							<td width="60"><? echo change_date_format($row[csf("program_date")]); ?></td>
							<td width="80">
								<p>
									<?
									echo ($row[csf("within_group")] == 1)?$buyer_arr[$row[csf("po_buyer")]]:$buyer_arr[$row[csf("buyer_id")]];
									?>
								</p>
							</td>
							<td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
							<td width="100"><? echo $row[csf("sales_booking_no")]; ?></td>
							<td width="110"><? echo $row[csf("job_no")]; ?></td>
							<td width="200"><p><? echo $row[csf("fabric_desc")]; ?></p></td>
							<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
							<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
							<td width="70"><? echo $row[csf("machine_dia")].'X'.$row[csf("machine_gg")]; ?>&nbsp;</td>
							<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
							<td width="70"><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?>&nbsp;</td>
							<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="70"><? echo $gsm; ?>&nbsp;</td>
							<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
							<td width="60"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
							<td width="80" align="right"><? echo number_format($row[csf("program_qnty")],2); ?></td>
							<td width="80" align="right" title="<? echo $row[csf('prog_no')]."=".$po_id?>"><a href='#report_details' onClick='openmypage_receive("<? echo $po_id; ?>","<? echo $row[csf('prog_no')]; ?>","<? echo $row[csf('sales_booking_no')]; ?>","receive_grey_popup");'><? echo number_format($recv_qnty,2,'.',''); ?></a></td>
							<td width="80" align="right">
								<?
								echo number_format($trans_qty_in,2,'.','');
								?>
								<!-- <a href="##" onClick="openmypage_issue('<? //echo $po_id; ?>','<? //echo $prog_no; ?>','<? //echo $sales_booking_no; ?>','trans_in_popup');"><? //echo number_format($trans_qty_in,2,'.',''); ?></a> -->
							</td>
							<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
							<td width="80" align="right">
								<a href='#report_details' onClick="openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','issue_grey_popup');">
									<? echo number_format($knit_issue_qty,2,'.',''); ?>
								</a>
							</td>
							<td width="80" align="right"><a href="##" onClick=" openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','trans_out_popup');"><? echo number_format($trans_qty_out,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
							<td width="80" align="right">
								<?
								if(number_format($row_stock,2,'.','') == "-0.00")
								{
									echo "0.00";
								}
								else{
									echo number_format($row_stock,2,'.','');
								}
								?>
								<!--<a href='#report_details' onClick="openmypage_issue('< echo $po_id; ?>','< echo $prog_no; ?>','< echo $sales_booking_no; ?>','stock_grey_popup');"></a>-->
							</td>
							<td width="80" align="center" title="<? echo $title;?>"><p><? echo $remaining_roll;//$roll_no//$yarn_lot_arr[$po_id]['roll'].'='.$po_id; ?></p>&nbsp;</td>
							<td><p><? echo $remark; ?></p>&nbsp;</td>
						</tr>

						<?php
						$tot_program_qnty+=$program_qnty;
						$tot_recv_qnty+=$recv_qnty;
						$tot_trans_qty_in+=$trans_qty_in;
						$tot_totalRecv+=$totalRecv;
						$tot_knit_issue_qty+=$knit_issue_qty;
						$tot_trans_qty_out+=$trans_qty_out;
						$tot_totalIssue+=$totalIssue;
						$tot_row_stock+=$row_stock;
						$tot_roll_no += $remaining_roll;
						$i++;
					}
					foreach($trns_row_data as $prog_no=>$trns_data)
					{
						$ex_trn_data=explode("!!!!",$trns_data);

						$to_order_id=$ex_trn_data[0];
						$trans_qty_in=$ex_trn_data[1];
						$trans_in_roll_tr_count=$ex_trn_data[2];

						$kniting_company=""; $prog_date=""; $booking_no=''; $booking_id=''; $fabric_desc=""; $color_range_name=""; $color_name=""; $mc_dia_gg=""; $dia_width=""; $width_dia_type=""; $stitch_length=""; $gsm=""; $yarn_count_value=""; $lot=""; $prog_qty=0;


						$booking_no = $trans_row_ref_arr[$to_order_id]['sales_booking_no'];
						$booking_id=$trans_row_ref_arr[$to_order_id]['booking_id'];
						$knitting_source=$trans_row_ref_arr[$to_order_id]['knitting_source'];
						$knitting_party=$trans_row_ref_arr[$to_order_id]['knitting_party'];
						if($knitting_source==1) $kniting_company=$company_arr[$knitting_party]; else if ($knitting_source==3) $kniting_company=$supplier_arr[$knitting_party];


						$prog_date=$trns_data_arr[$prog_no]['program_date'];
						$fabric_desc=$trns_data_arr[$prog_no]['fabric_desc'];

						$ex_color_range_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_range_id']));
						foreach($ex_color_range_id as $range_id)
						{
							if($range_id>0)
							{
								if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
							} //print_r( $yarn_count_value);
						}

						$ex_color_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_id']));
						foreach($ex_color_id as $color_id)
						{
							if($color_id>0)
							{
								if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
							} //print_r( $yarn_count_value);
						}

						$mc_dia_gg=$trns_data_arr[$prog_no]['mc_dia_gg'];
						$dia_width=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['dia_width']))));;
						$width_dia_type=$trns_data_arr[$prog_no]['width_dia_type'];
						$stitch_length="";
						$stitch_length=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['stitch_length']))));

						$gsm="";
						$gsm=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['gsm']))));

						$y_count=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['ycount'],",")))));
						$y_count_id=array_unique(explode(',',$y_count));

						$yarn_count_value='';
						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
							}
						}
						$lot=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['lot'],",")))));
						//$prog_qty=$trns_data_arr[$prog_no]['program_qnty'];

						$knit_issue_qty=$knit_issue_arr[$prog_no][$to_order_id]['qnty'];

						$ex_issue_roll=explode(",",$knit_issue_arr[$prog_no][$to_order_id]['roll']);
						$iss_roll_no="";
						foreach($ex_issue_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$iss_roll_arr))
							{
								$iss_roll_no+=$val[1];
								$iss_roll_arr[]=$val[0];
							}
						}


						$withingroup=$trans_row_ref_arr[$to_order_id]['within_group'];
						$buyer_with=''; $style_with="";
						if($withingroup==1)
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['po_buyer'];
						}
						else
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['buyer_id'];

						}
						$style_with=$trans_row_ref_arr[$to_order_id]['style_ref_no'];

						$tot_knit_grey_recv=$knit_recv_arr[$prog_no]['qnty']+$knit_recv_arr[$prog_no]['qnty'];
						$recv_qnty=$recv_array[$prog_no][$to_order_id]['rec_qty'];
						$ex_recv_roll=explode(",",$recv_array[$prog_no][$to_order_id]['roll']);
						$rec_roll_no="";
						foreach($ex_recv_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$rec_roll_arr))
							{
								$rec_roll_no+=$val[1];
								$rec_roll_arr[]=$val[0];
							}
						}

						$trans_qty_out=$transfer_qty_arr[$prog_no][$to_order_id]['transfer_out'];
						$trans_qty_in=$trans_qty_in;//$transfer_qty_arr[$prog_no][$row[csf('id')]]['transfer_in'];
						$totalRecv=$recv_qnty+$trans_qty_in;
						$totalIssue=$knit_issue_qty+$trans_qty_out;
						$row_stock=0;
						$row_stock=$totalRecv-$totalIssue;

						$roll_no='';
						$roll_data=explode(",",$yarn_lot_arr[$prog_no]['roll']);
						foreach($roll_data as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$roll_arr))
							{
								$roll_no+=$val[1];
								$roll_arr[]=$val[0];
							}
						}
						$recv_roll_tr_count=$iss_roll_tr_count=$trans_out_roll_tr_count=0;
						$recv_roll_tr_count =$recv_array[$prog_no][$to_order_id]['roll_count'];
						$iss_roll_tr_count = $knit_issue_arr[$prog_no][$to_order_id]['roll_no'];
						//$trans_in_roll_tr_count;
						$trans_out_roll_tr_count= $transfer_qty_arr[$prog_no][$to_order_id]['transOut_roll_no'];

						$remaining_roll_tran_tr = ($recv_roll_tr_count + $trans_in_roll_tr_count) - ($iss_roll_tr_count + $trans_out_roll_tr_count);
						//$title_tr = "($recv_roll_tr_count + $trans_in_roll_tr_count) - ($iss_roll_tr_count + $trans_out_roll_tr_count)";


						//$roll_no=$roll_no-$iss_roll_no;
						$remark=$knit_issue_arr[$prog_no][$to_order_id]['remarks'];




						//if($trns_data_arr[$prog_no]['program_qnty']>0)
						//{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"><p><? echo $kniting_company; ?></p></td>
							<td width="50"><? echo $prog_no.'-[T]'; ?></td>
							<td width="60"><? echo change_date_format($prog_date); ?></td>
							<td width="80"><p><? echo $buyer_arr[$buyer_with]; ?></p></td>
							<td width="100"><p><? echo $style_with; ?></p></td>
							<td width="100"><? echo $trans_row_ref_arr[$to_order_id]['sales_booking_no'];//$trns_data_arr[$prog_no]['sales_booking_no']; ?></td>
							<td width="110"><? echo $trans_row_ref_arr[$to_order_id]['job_no'];//$trns_data_arr[$prog_no]['job_no_prefix_num']; ?></td>
							<td width="200"><p><? echo $fabric_desc; ?></p></td>
							<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
							<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
							<td width="70"><? echo $mc_dia_gg; ?>&nbsp;</td>
							<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
							<td width="70"><? echo $fabric_typee[$width_dia_type]; ?>&nbsp;</td>
							<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="70"><? echo $gsm; ?>&nbsp;</td>
							<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
							<td width="60"><p><? echo $lot; ?></p>&nbsp;</td>

							<td width="80" align="right"><? echo number_format($prog_qty,2); ?></td>
							<td width="80" align="right"><? echo number_format($recv_qnty,2,'.',''); ?><!--<a href='#report_details' onClick="openmypage_receive('< echo $to_order_id; ?>','< echo $prog_no; ?>','< echo $booking_no; ?>','receive_grey_popup');"></a>--></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','trans_in_popup');"><? echo number_format($trans_qty_in,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
							<td width="80" align="right"><a href='#report_details' onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','issue_grey_popup');"><? echo number_format($knit_issue_qty,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($trans_qty_out,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
							<td width="80" align="right">
								<?
								if(number_format($row_stock,2,'.','') == "-0.00")
								{
									echo "0.00";
								}else{
									echo number_format($row_stock,2,'.','');
								}
								?>
								<!--<a href='#report_details' onClick="openmypage_issue('< echo $to_order_id; ?>','< echo $prog_no; ?>','< echo $booking_no; ?>','stock_grey_popup');"></a>-->
							</td>
							<td width="80" align="center" title="<? //echo $title_tr?>"><p><? echo $remaining_roll_tran_tr;//$roll_no;//$yarn_lot_arr[$to_order_id]['roll'].'='.$to_order_id; ?></p>&nbsp;</td>
							<td><p><? echo $remark; ?></p>&nbsp;</td>
						</tr>
						<?
						$tot_program_qnty+=$prog_qty;
						$tot_recv_qnty+=$recv_qnty;
						$tot_trans_qty_in+=$trans_qty_in;
						$tot_totalRecv+=$totalRecv;
						$tot_knit_issue_qty+=$knit_issue_qty;
						$tot_trans_qty_out+=$trans_qty_out;
						$tot_totalIssue+=$totalIssue;
						$tot_row_stock+=$row_stock;
						$tot_roll_no += $remaining_roll_tran_tr;
						$i++;
						//}
					}
				}
				else
				{
					echo "3**".'Data Not Found'; die;
				}
				unset($nameArray);
				?>
			</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2380" class="rpt_table" id="">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="200">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="80" align="right" id="value_program_qnty"><? echo number_format($tot_program_qnty,2);?></th>
				<th width="80" align="right" id="value_recv_qnty"><? echo number_format($tot_recv_qnty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_in"><? echo number_format($tot_trans_qty_in,2);?></th>
				<th width="80" align="right" id="value_totalRecv"><? echo number_format($tot_totalRecv,2);?></th>
				<th width="80" align="right" id="value_knit_issue_qty"><? echo number_format($tot_knit_issue_qty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_out"><? echo number_format($tot_trans_qty_out,2);?></th>
				<th width="80" align="right" id="value_totalIssue"><? echo number_format($tot_totalIssue,2);?></th>
				<th width="80" align="right" id="value_row_stock"><? echo number_format($tot_row_stock,2);?></th>
				<th width="80" align="right" id="value_roll"><? echo $tot_roll_no;?></th>
				<th>&nbsp;</th>
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

function fnc_tempengine_barcode($table_name, $user_id, $entry_form, $ref_value_arr)
{
	global $con ;
	$numeless=count($ref_value_arr);
	$psql = "BEGIN PRC_TEMP_BARCODE_INSERT(:user_id,:type,:po_arr); END;";
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":user_id",$user_id);
	oci_bind_by_name($stmt,":type",$entry_form);
	oci_bind_array_by_name($stmt, ":po_arr", $ref_value_arr, $numeless, -1, SQLT_INT);
	oci_execute($stmt); 
	oci_commit($con);
	disconnect($con);
}

if($action=="report_generate")// use_temp_eng crm 3298
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$program_no= str_replace("'","",$txt_program_no);
	$date_from= str_replace("'","",$txt_date_from);

	if($within_group) $within_group_cond = " and a.within_group='$within_group' "; else $within_group_cond = "";

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and a.po_buyer in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else if ($within_group==2 )
		{
			$buyer_id_cond=" and a.buyer_id in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond=" and a.po_company_id='$pocompany_id' ";
	$date_cond="";$prog_date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
		$prog_date_cond = " and b.program_date='$date_from'";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(c.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(c.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}

	if($year_id!=0) $sales_year_cond = " and a.job_no like '%-".substr($year_id, -2)."-%'"; else $sales_year_cond="";

	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and a.job_no_prefix_num='$order_no'";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and d.id='$program_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and a.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and a.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}
		$prog_cond_booking_no = " and a.booking_no like '%$booking_no%'";
		$prog_cond_booking_no .= " and a.booking_no like '%-".substr($year_id, -2)."-%'";
	} else {
		$booking_no_cond="";
	}

	$variable_data=sql_select("select variable_list, fabric_roll_level, auto_update from variable_settings_production where company_name ='$company_name' and variable_list in(3,15) and item_category_id=13 and is_deleted=0 and status_active=1");
	foreach($variable_data as $row)
	{
		if($row[csf('variable_list')]==3)
		{
			$roll_maintained=$row[csf('fabric_roll_level')];
		}
		else
		{
			$fabric_store_auto_update=$row[csf('auto_update')];
		}
	}

	if ($program_no=="") $program_cond_trans=""; else $program_cond_trans=" and b.id in ($program_no) ";
	
	if($program_no !="" || $date_from != "" || $booking_no!="")
	{	
		$programSqlForTrans = sql_select("select b.id, a.booking_no, c.po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c
			where a.id=b.mst_id and c.dtls_id=b.id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0
			and a.company_id = $company_name $program_cond_trans $prog_cond_booking_no $prog_date_cond");

		foreach($programSqlForTrans as $prog)
		{
			$sales_id_arr[$prog[csf("po_id")]] = $prog[csf("po_id")];
		}
		$sales_id_arr = array_filter($sales_id_arr);
		if(count($sales_id_arr)>0)
		{
			$all_sales_id_cond=""; $salesCond="";
			if($db_type==2 && count($sales_id_arr)>999)
			{
				$sales_id_arr_chunk=array_chunk($sales_id_arr,999) ;
				foreach($sales_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$salesCond.="  a.id in($chunk_arr_value) or ";
				}

				$all_sales_id_cond.=" and (".chop($salesCond,'or ').")";
			}
			else
			{

				$all_sales_id_cond=" and a.id in(".implode(",", $sales_id_arr).")";
			}
		}
		unset($programSqlForTrans);
	}

	$con = connect();
	execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = 1 and ENTRY_FORM=868");
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (556)");
    oci_commit($con);

	// echo "checking";die;
	// echo $program_no_cond.'='.$date_cond.'='.$year_search_cond.'='.$within_group_cond.'='.$buyer_id_cond.'='.$pocompany_cond.'='.$order_no_cond.'='.$booking_no_cond.'='.$all_sales_id_cond.'='.$sales_year_cond;die;
	// Main Query
	$sql="SELECT a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id

	from fabric_sales_order_mst a
	left join ppl_planning_entry_plan_dtls e on a.id=e.po_id and e.status_active=1
	left join ppl_planning_info_entry_dtls d on e.dtls_id=d.id and d.status_active=1 $program_no_cond $date_cond
	left join ppl_planning_info_entry_mst c on d.mst_id=c.id and c.is_sales=1 and c.status_active=1 $year_search_cond

	where a.company_id='$company_name' $within_group_cond $buyer_id_cond $pocompany_cond $order_no_cond $booking_no_cond $all_sales_id_cond $sales_year_cond
	group by a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, e.fabric_desc, e.program_qnty,e.po_id,d.knitting_source,d.knitting_party, d.id, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type";
	// echo $sql;die;
	$nameArray=sql_select($sql);
	//$to_poids="";
	foreach($nameArray as $row)
	{
		$program_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
		$booking_no_arr[] = "'".$row[csf("sales_booking_no")]."'";
		//$to_poids.= $row[csf("id")].",";

		$trans_row_ref_arr[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$trans_row_ref_arr[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$trans_row_ref_arr[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$trans_row_ref_arr[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_source'] = $row[csf('knitting_source')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_party'] = $row[csf('knitting_party')];
		$trans_row_ref_arr[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$trans_row_ref_arr[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
		$trans_row_ref_arr[$row[csf('id')]]['po_buyer'] = $row[csf('po_buyer')];

		$to_poids_arr[$row[csf("id")]]=$row[csf("id")];
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 556, 1,$to_poids_arr, $empty_arr);
    oci_commit($con);

	/*$to_poids = implode(",", array_filter(array_unique(explode(",",chop($to_poids,",")))));
	$to_pocond = $trns_to_po_cond = "";
	$to_poids_arr=explode(",",$to_poids);
	if(count($to_poids_arr)>0)
	{
		if($db_type==2 && count($to_poids_arr)>999)
		{
			$to_poids_chunk=array_chunk($to_poids_arr,999) ;
			foreach($to_poids_chunk as $chunk_arr)
			{
				$to_pocond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$trns_to_po_cond.=" and (".chop($to_pocond,'or ').")";
		}
		else
		{
			$trns_to_po_cond=" and a.to_order_id in($to_poids)";
		}
	}*/
	// echo "checking";die;//done
	$data_trans_sql="SELECT a.to_order_id, b.to_program,e.po_buyer,e.buyer_id, sum(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in, count(d.id) as roll_no 
	from GBL_TEMP_ENGINE t, inv_item_transfer_mst a,inv_item_transfer_dtls b,order_wise_pro_details c,pro_roll_details d,fabric_sales_order_mst e 
	where t.REF_VAL=a.to_order_id and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=1 and a.entry_form=133 and a.item_category=13 and a.transfer_criteria=4 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.dtls_id=d.dtls_id and d.po_breakdown_id= e.id and b.to_program>0 and c.trans_type in(5) and d.status_active=1 and d.is_deleted=0 and c.entry_form=133 and d.entry_form = 133 
	group by a.to_order_id, b.to_program,e.po_buyer,e.buyer_id";
	// echo $data_trans_sql;die;
	$data_trans=sql_select($data_trans_sql);
	$trns_row_data=array();
	foreach($data_trans as $row_b)
	{
		$trns_row_data[$row_b[csf('to_program')]]=$row_b[csf('to_order_id')].'!!!!'.$row_b[csf('item_transfer_in')].'!!!!'.$row_b[csf('roll_no')];
		$trns_row_prog_ref_data[$row_b[csf('to_program')]] = $row_b[csf('to_program')];

		$program_no_arr[$row_b[csf("to_program")]] = $row_b[csf("to_program")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["po_buyer"] = $row_b[csf("po_buyer")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["buyer_id"] = $row_b[csf("buyer_id")];
	}
	unset($data_trans);
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 556, 2,$program_no_arr, $empty_arr);
    oci_commit($con);
    // echo $data_trans_sql;die;

	$program_no_arr = array_filter($program_no_arr);
	if(count($program_no_arr)>0)
	{
		$program_no_arr = explode(",","'".implode("','",$program_no_arr)."'");

		$all_program_nos = implode(",",$program_no_arr);
		$progCond = $all_rcv_prog_cond = "";
		$to_progcond=$from_progcond=$transfer_all_prog_cond="";

		/*if($db_type==2 && count($program_no_arr)>999)
		{
			$program_no_arr_chunk=array_chunk($program_no_arr,999) ;
			foreach($program_no_arr_chunk as $chunk_arr)
			{
				$progCond.=" d.booking_no in(".implode(",",$chunk_arr).") or ";
				$progCond2.=" b.program_no in(".implode(",",$chunk_arr).") or ";

				$to_progcond.=" b.to_program in(".implode(",",$chunk_arr).") or ";
				$from_progcond.=" b.from_program in(".implode(",",$chunk_arr).") or ";
			}

			//$all_rcv_prog_cond.=" and (".chop($progCond,'or ').")";
			//$all_rcv_prog_cond2.=" and (".chop($progCond2,'or ').")";

			//$transfer_all_prog_cond .= " and (" .chop($to_progcond,'or ')." or ". chop($from_progcond,'or ') .")";
		}
		else
		{
			//$all_rcv_prog_cond=" and d.booking_no in($all_program_nos)";
			//$all_rcv_prog_cond2=" and b.program_no in($all_program_nos)";

			//$transfer_all_prog_cond = " and ( b.to_program in ($all_program_nos) or  b.from_program in ($all_program_nos) ) ";
		}*/

		/*$production_ref = sql_select("SELECT a.booking_no, c.po_breakdown_id as po_id, sum(c.quantity) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no, c.trans_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
		left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(2) and d.is_sales= 1
		where a.item_category=13 and a.receive_basis =2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $all_rcv_prog_cond 
		group by a.booking_no,c.po_breakdown_id,d.barcode_no,c.trans_id");*/

		$production_ref = sql_select("SELECT a.booking_no, c.po_breakdown_id as po_id, sum(c.quantity) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no, c.trans_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
		left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(2) and d.is_sales= 1
		left JOIN GBL_TEMP_ENGINE t on t.REF_VAL=d.booking_no and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=2 
		where a.item_category=13 and a.receive_basis =2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0
		group by a.booking_no,c.po_breakdown_id,d.barcode_no,c.trans_id");

		foreach ($production_ref as $row)
		{
			if($row[csf('trans_id')] >0)
			{
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
			}
			else
			{
				$production_barcode[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				$production_barcode_ref[$row[csf('barcode_no')]]["booking_no"] = $row[csf('booking_no')];

				$production_id_arr[$row[csf('id')]] = $row[csf('id')];
				$program_ref_arr[$row[csf('id')]] = $row[csf('booking_id')];
			}

			$production_all_ref_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			$prod_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		//echo "<pre>";print_r($prod_id_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 556, 3,$production_id_arr, $empty_arr);
		//fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 556, 4,$production_all_ref_barcode_arr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 556, 4,$prod_id_arr, $empty_arr);
		fnc_tempengine_barcode("TMP_BARCODE_NO", 1, 868, $production_all_ref_barcode_arr);
    	oci_commit($con);
    	//echo "string";die;

		$production_id_arr = array_filter($production_id_arr);		
		if(!empty($production_id_arr))
		{
			/*$all_production_id_arr = implode(",", $production_id_arr);
			$productionCond = ""; $production_cond_for_rcv = "";
			if($db_type==2 && count($production_id_arr)>999)
			{
				$all_production_id_arr_chunk=array_chunk($production_id_arr,999) ;
				foreach($all_production_id_arr_chunk as $chunk_arr)
				{
					$productionCond.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
				}

				$production_cond_for_rcv.=" and (".chop($productionCond,'or ').")";

			}
			else
			{
				$production_cond_for_rcv=" and a.booking_id in($all_production_id_arr)";
			}*/

			$sql_recv=sql_select("SELECT a.booking_id,c.po_breakdown_id as po_id,sum(c.quantity) as knitting_qnty 
			from GBL_TEMP_ENGINE t, inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c 
			where t.REF_VAL=a.booking_id and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=3 and a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 
			group by a.booking_id,c.po_breakdown_id");//$production_cond_for_rcv 
			$minimum_date = "";
			foreach($sql_recv as $row)
			{
				$recv_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];
			}
			unset($sql_recv);
		}

		/*if(!empty($production_barcode))
		{
			$production_barcode = array_filter($production_barcode);
			$all_production_barcode_nos = implode(",", $production_barcode);
			$all_production_barcode_cond=""; $barCond="";
			if($db_type==2 && count($production_barcode)>999)
			{
				$production_barcode_chunk=array_chunk($production_barcode,999) ;
				foreach($production_barcode_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  d.barcode_no in($chunk_arr_value) or ";
				}

				$all_production_barcode_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				if($all_production_barcode_nos!=""){
					$all_production_barcode_cond=" and d.barcode_no in($all_production_barcode_nos)";
				}
			}
		}*/

		/*if(!empty($prod_id_arr))
		{
			$prod_id_arr = array_filter($prod_id_arr);
			$all_production_po_nos = implode(",", $prod_id_arr);
			$all_production_po_cond=""; $barCond="";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$production_po_chunk=array_chunk($prod_id_arr,999) ;
				foreach($production_po_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$all_production_po_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$all_production_po_cond=" and c.po_breakdown_id in($all_production_po_nos)";
			}
		}*/

		/*$sql_recv_roll=sql_select("SELECT a.entry_form,a.booking_no, c.po_breakdown_id as po_id, sum(d.qnty) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no
		from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, pro_roll_details d
		where a.item_category=13 and a.entry_form in(58) and a.receive_basis =10 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(58) and b.id=d.dtls_id and d.entry_form in(58) and d.is_sales= 1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id!=0 and d.status_active=1 and d.is_deleted=0 $all_production_barcode_cond $all_production_po_cond
		group by a.booking_no,c.po_breakdown_id,d.barcode_no,a.entry_form");*/

		$sql_recv_roll=sql_select("SELECT a.entry_form,a.booking_no, c.po_breakdown_id as po_id, sum(d.qnty) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no
		from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, pro_roll_details d, TMP_BARCODE_NO x, GBL_TEMP_ENGINE y
		where x.barcode_no=d.barcode_no and x.USERID=1 and x.ENTRY_FORM =868 and y.REF_VAL=c.po_breakdown_id and y.REF_FROM=4 and y.USER_ID=$user_id and y.ENTRY_FORM =556 and a.item_category=13 and a.entry_form in(58) and a.receive_basis =10 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(58) and b.id=d.dtls_id and d.entry_form in(58) and d.is_sales= 1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id!=0 and d.status_active=1 and d.is_deleted=0 group by a.booking_no,c.po_breakdown_id,d.barcode_no,a.entry_form");
		//$all_production_barcode_cond $all_production_po_cond
		foreach($sql_recv_roll as $row)
		{
			$production_booking = $production_barcode_ref[$row[csf('barcode_no')]]["booking_no"];
			$recv_array[$production_booking][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
			$recv_array[$production_booking][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
		}
		unset($sql_recv_roll);


		$production_all_ref_barcode_arr = array_filter($production_all_ref_barcode_arr);
		if(!empty($production_all_ref_barcode_arr))
		{
			/*$production_all_ref_barcode_nos = implode(",", $production_all_ref_barcode_arr);
			$production_all_ref_barcode_cond=""; $barCond="";
			if($db_type==2 && count($production_all_ref_barcode_arr)>999)
			{
				$production_all_ref_barcode_arr_chunk=array_chunk($production_all_ref_barcode_arr,999) ;
				foreach($production_all_ref_barcode_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.=" a.barcode_no in($chunk_arr_value) or ";
				}

				$production_all_ref_barcode_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$production_all_ref_barcode_cond=" and a.barcode_no in($production_all_ref_barcode_nos)";
			}*/

			$roll_issue_sql = sql_select("SELECT count(a.barcode_no) as roll_no, a.barcode_no, a.qnty, a.entry_form, a.booking_no, a.po_breakdown_id, b.remarks 
			from TMP_BARCODE_NO t, pro_roll_details a,  inv_grey_fabric_issue_dtls b 
			where t.BARCODE_NO=a.barcode_no and t.USERID=1 and t.ENTRY_FORM=868 and a.dtls_id = b.id and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.booking_without_order=0 and a.is_returned <> 1
			group by a.barcode_no, a.qnty, a.entry_form, a.booking_no, a.po_breakdown_id, b.remarks ");
			$knit_issue_arr=array();// $production_all_ref_barcode_cond 
			foreach($roll_issue_sql as $row)
			{
				$knit_issue_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]['roll_no'] +=$row[csf('roll_no')];
				$knit_issue_arr[$row[csf("booking_no")]][$row[csf("po_breakdown_id")]]["qnty"] += $row[csf("qnty")];
				$knit_issue_arr[$row[csf("booking_no")]][$row[csf("po_breakdown_id")]]["remarks"] += $row[csf("remarks")];
			}
			unset($roll_issue_sql);
		}
		//echo "checking";die;

		$sql_data=sql_select("SELECT b.program_no, b.remarks, c.quantity,c.po_breakdown_id, sum(b.no_of_roll) as roll_no
		from GBL_TEMP_ENGINE t, inv_grey_fabric_issue_dtls b, inv_issue_master a,  order_wise_pro_details c
		where t.REF_VAL=b.program_no and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=2 and b.mst_id=a.id and b.id = c.dtls_id
		and c.trans_type = 2 and a.item_category=13 and a.entry_form in (16) and c.entry_form in (16)
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and c.status_active = 1 and c.is_deleted = 0 and b.program_no <> 0 and b.program_no is not null");
		// $all_rcv_prog_cond2

		foreach($sql_data as $row)
		{
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('quantity')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['remarks']=$row[csf('remarks')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['roll_no'] +=$row[csf('roll_no')];
		}
		unset($sql_data);
		// echo "checking";die;
		$trans_data_array=sql_select("SELECT a.from_order_id, a.to_order_id, b.from_program, b.to_program ,d.barcode_no, c.trans_type,d.qnty,b.id as dtls_id
		from GBL_TEMP_ENGINE t, inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c
		left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form=133 and d.is_sales=1
		where a.id=b.mst_id and c.dtls_id=b.id and c.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and c.entry_form=133 and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4 and b.from_program>0 and b.to_program>0 and b.status_active=1 and b.is_deleted=0 and ( t.REF_VAL=b.to_program or t.REF_VAL=b.from_program) and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=2");
		// $transfer_all_prog_cond
		// echo "checking";die;
		$transfer_qty_arr=array();
		$chkDtlsIdArr=array();
		$rt = 1;
		foreach($trans_data_array as $row_b)
		{
			$chkDtlsIdArr[$row_b[csf("dtls_id")]] = $row_b[csf("dtls_id")];
			if($row_b[csf("trans_type")] == "6")
			{
				$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transfer_out']+=$row_b[csf('qnty')];
				$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transOut_roll_no']+=$rt;
			}else{
				$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transfer_in']+=$row_b[csf('qnty')];
				$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transIn_roll_no']+=$rt;
			}
		}

		$all_trans_prog =implode(",",array_filter(array_unique($trns_row_prog_ref_data)));
		if($all_trans_prog)
		{
			/*if($all_trans_prog=="") $all_trans_prog=0;
			$progCond = $all_trans_prog_cond = "";
			$all_trans_prog_arr=explode(",",$all_trans_prog);
			if($db_type==2 && count($all_trans_prog_arr)>999)
			{
				$all_trans_prog_chunk=array_chunk($all_trans_prog_arr,999) ;
				foreach($all_trans_prog_chunk as $chunk_prog)
				{
					$chunk_prog_val=implode(",",$chunk_prog);
					$progCond.=" d.id in($chunk_prog_val) or ";
				}
				$all_trans_prog_cond.=" and (".chop($progCond,'or ').")";
			}
			else
			{
				$all_trans_prog_cond=" and d.id in($all_trans_prog)";
			}*/

			$trans_pro_ref = sql_select("SELECT d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id 
			from GBL_TEMP_ENGINE t, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst c, ppl_planning_entry_plan_dtls e 
			where t.REF_VAL=d.id and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=2 and c.id=d.mst_id and d.id=e.dtls_id and c.is_sales=1 and e.is_sales=1 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0");// $all_trans_prog_cond

			foreach($trans_pro_ref as $row)
			{
				$trns_data_arr[$row[csf('prog_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
				$trns_data_arr[$row[csf('prog_no')]]['booking_id']=$row[csf('booking_id')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no']=$row[csf('job_no')];
				$trns_data_arr[$row[csf('prog_no')]]['knitting_source']=$row[csf('knitting_source')];
				$trns_data_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
				$trns_data_arr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
				$trns_data_arr[$row[csf('prog_no')]]['mc_dia_gg']=$row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
				$trns_data_arr[$row[csf('prog_no')]]['width_dia_type']=$row[csf('width_dia_type')];
				$trns_data_arr[$row[csf('prog_no')]]['fabric_desc']=$row[csf('fabric_desc')];
				$trns_data_arr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				$trns_data_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
				$trns_data_arr[$row[csf('prog_no')]]['style']=$row[csf('style_ref_no')];
				$trns_data_arr[$row[csf('prog_no')]]['group']=$row[csf('within_group')];
			}
			unset($trans_pro_ref);
		}

		$yarn_lot_data=sql_select("SELECT d.booking_id, a.yarn_lot, a.color_range_id, a.color_id, a.width as dia_width, a.stitch_length, a.gsm, a.yarn_count, b.po_breakdown_id, a.yarn_prod_id 
		from pro_grey_prod_entry_dtls a, order_wise_pro_details b, inv_receive_master d, GBL_TEMP_ENGINE t
		where t.REF_VAL=d.booking_no and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=2 and d.id=a.mst_id and a.id=b.dtls_id and b.entry_form=2 and d.entry_form=2 and d.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_sales=1"); // $all_rcv_prog_cond

		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot_arr[$rows[csf('booking_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['roll'] .=$rows[csf('no_of_roll')];
			$yarn_lot_arr[$rows[csf('booking_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['color_id'] .=$rows[csf('color_id')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['gsm'] .=$rows[csf('gsm')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['yarn_prod_id'] .=$rows[csf('yarn_prod_id')].",";
			$yarn_prod_arr[$rows[csf('yarn_prod_id')]] =$rows[csf('yarn_prod_id')];

			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['roll'] .=$rows[csf('no_of_roll')];
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_id'] .=$rows[csf('color_id')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['gsm'] .=$rows[csf('gsm')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['yarn_prod_id'] .=$rows[csf('yarn_prod_id')].",";
		}
		unset($yarn_lot_data);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 556, 5,$yarn_prod_arr, $empty_arr);
    	oci_commit($con);

		$all_yarn_prod_id_arr = array_filter($yarn_prod_arr);
		if(count($all_yarn_prod_id_arr) > 0)
		{
			/*$all_yarn_prod_id = implode(",", $all_yarn_prod_id_arr);
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
			}*/
			$brand_yarn_arr = return_library_array("SELECT a.id, b.brand_name from GBL_TEMP_ENGINE t, product_details_master a, lib_brand b where t.REF_VAL=a.id and t.USER_ID=$user_id and t.ENTRY_FORM=556 and t.REF_FROM=5 and a.brand = b.id and b.status_active = 1 and a.status_active=1 $all_yarn_prod_id_cond ","id","brand_name");
		}
	}
	// echo "checking";die;
	execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID =1 and ENTRY_FORM=868");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (556)");
    oci_commit($con);

	ob_start();
	?>
	<fieldset style="width:2490px;">
		<table width="2490" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="80">Knitting Company</th>
				<th width="50">Prog. No</th>
				<th width="60">Prog. Date</th>
				<th width="80">PO Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="100">Fab. Booking No</th>
				<th width="110">Sales Order No</th>
				<th width="200">Fab. Description</th>
				<th width="100">Color Range</th>
				<th width="120">Fab. Color</th>
				<th width="70">MC DXG</th>
				<th width="70">F.Dia</th>
				<th width="70">Dia Type</th>
				<th width="70">S/L</th>
				<th width="70">FGSM</th>
				<th width="60">Y/Count</th>
				<th width="60">Y/Lot</th>
				<th width="100">Y/Brand</th>
				<th width="80">Prog Qty/kg</th>
				<th width="80">Receive Qty(kg)</th>
				<th width="80">Trans In Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Delivery Qty(kg)</th>
				<th width="80">Trans Out Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Stock Qty(kg)</th>
				<th width="80">Roll</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="width:2480px; overflow-y:scroll; max-height:350px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2460" class="rpt_table" id="tbl_list_search">
				<?
				if(count($nameArray)>0 || count($trns_row_data)>0)
				{
					$i=1;
					$row_stock=0;
					foreach($nameArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$prog_no = $row[csf("prog_no")];
						if($row[csf("prog_no")]!=""){
							$po_id = $row[csf("po_id")];

							$kniting_company="";
							if($row[csf("knitting_source")]==1) $kniting_company=$company_arr[$row[csf("knitting_party")]]; else if ($row[csf("knitting_source")]==3) $kniting_company=$supplier_arr[$row[csf("knitting_party")]];

							$ex_color_range_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_range_id'],",")));

							$color_range_name="";
							foreach($ex_color_range_id as $range_id)
							{
								if($range_id>0)
								{
									if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
								} 
							}
							$ex_color_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_id'],",")));

							$color_name="";
							foreach($ex_color_id as $color_id)
							{
								if($color_id>0)
								{
									if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
								}
							}
							$dia_width="";
							$dia_width=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['dia_width'],",")))));

							$stitch_length="";
							$stitch_length=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['stitch_length'],",")))));

							$gsm="";
							$gsm=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['gsm'],",")))));


							$yarn_prod_ids= array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['yarn_prod_id'],","))));

							$yarn_brand_value='';
							foreach($yarn_prod_ids as $p_val)
							{
								if($p_val)
								{
									if($yarn_brand_value=='') $yarn_brand_value=$brand_yarn_arr[$p_val]; else $yarn_brand_value.=", ".$brand_yarn_arr[$p_val];
								}
							}

							$y_count=chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['ycount'],",");
							$y_count_id=array_unique(explode(',',$y_count));

							$yarn_count_value='';
							foreach($y_count_id as $val)
							{
								if($val>0)
								{
									if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
								}
							}

							$yarn_lot = "";
							$yarn_lot = implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['lot'],",")))));

							$knit_issue_qty=$knit_issue_arr[$prog_no][$po_id]['qnty'];
							$iss_roll_no=$knit_issue_arr[$prog_no][$po_id]['roll'];
							$recv_qnty=$recv_array[$prog_no][$po_id]['rec_qty'];
							$ex_recv_roll=explode(",",$recv_array[$prog_no][$po_id]['roll']);
							$rec_roll_no=$recv_array[$prog_no][$po_id]['roll'];

							$recv_roll_count=$recv_array[$prog_no][$po_id]['roll_count'];
							$transOut_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transOut_roll_no'];
							$transIn_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transIn_roll_no'];
							$iss_roll_no_count=$knit_issue_arr[$prog_no][$po_id]['roll_no'];
							$remaining_roll = ($recv_roll_count + $transIn_roll_no_count)- ($transOut_roll_no_count + $iss_roll_no_count);

							$trans_qty_out=$transfer_qty_arr[$prog_no][$po_id]['transfer_out'];
							$trans_qty_in=$transfer_qty_arr[$prog_no][$po_id]['transfer_in'];
							$totalRecv=$recv_qnty+$trans_qty_in;
							$totalIssue=$knit_issue_qty+$trans_qty_out;

							$row_stock=$totalRecv-$totalIssue;

							$remark=$knit_issue_arr[$prog_no][$po_id]['remarks'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="80"><p><? echo $kniting_company; ?></p></td>
								<td width="50"><? echo $row[csf("prog_no")]; ?></td>
								<td width="60"><? echo change_date_format($row[csf("program_date")]); ?></td>
								<td width="80">
									<p>
										<?
										echo ($row[csf("within_group")] == 1)?$buyer_arr[$row[csf("po_buyer")]]:$buyer_arr[$row[csf("buyer_id")]];
										?>
									</p>
								</td>
								<td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="100"><? echo $row[csf("sales_booking_no")]; ?></td>
								<td width="110"><? echo $row[csf("job_no")]; ?></td>
								<td width="200"><p><? echo $row[csf("fabric_desc")]; ?></p></td>
								<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
								<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
								<td width="70"><? echo $row[csf("machine_dia")].'X'.$row[csf("machine_gg")]; ?>&nbsp;</td>
								<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
								<td width="70"><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?>&nbsp;</td>
								<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
								<td width="70"><? echo $gsm; ?>&nbsp;</td>
								<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
								<td width="60"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
								<td width="100"><p><? echo $yarn_brand_value; ?></p>&nbsp;</td>
								<td width="80" align="right"><? echo number_format($row[csf("program_qnty")],2); ?></td>
								<td width="80" align="right" title="<? echo $row[csf('prog_no')]."=".$po_id?>"><a href='#report_details' onClick='openmypage_receive("<? echo $po_id; ?>","<? echo $row[csf('prog_no')]; ?>","<? echo $row[csf('sales_booking_no')]; ?>","receive_grey_popup");'><? echo number_format($recv_qnty,2,'.',''); ?></a></td>
								<td width="80" align="right">
									<?
									echo number_format($trans_qty_in,2,'.','');
									?>
								</td>
								<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
								<td width="80" align="right">
									<a href='#report_details' onClick="openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','issue_grey_popup');">
										<? echo number_format($knit_issue_qty,2,'.',''); ?>
									</a>
								</td>
								<td width="80" align="right"><a href="##" onClick=" openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','trans_out_popup');"><? echo number_format($trans_qty_out,2,'.',''); ?></a></td>
								<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
								<td width="80" align="right">
									<?
									if(number_format($row_stock,2,'.','') == "-0.00")
									{
										echo "0.00";
									}
									else{
										echo number_format($row_stock,2,'.','');
									}
									?>
								</td>
								<td width="80" align="center" title="<? echo $title;?>"><p><? echo $remaining_roll; ?></p>&nbsp;</td>
								<td><p><? echo $remark; ?></p>&nbsp;</td>
							</tr>
							<?php
							$tot_program_qnty+=$program_qnty;
							$tot_recv_qnty+=$recv_qnty;
							$tot_trans_qty_in+=$trans_qty_in;
							$tot_totalRecv+=$totalRecv;
							$tot_knit_issue_qty+=$knit_issue_qty;
							$tot_trans_qty_out+=$trans_qty_out;
							$tot_totalIssue+=$totalIssue;
							$tot_row_stock+=$row_stock;
							$tot_roll_no += $remaining_roll;
							$i++;
						}
					}
					foreach($trns_row_data as $prog_no=>$trns_data)
					{
						$ex_trn_data=explode("!!!!",$trns_data);

						$to_order_id=$ex_trn_data[0];
						$trans_qty_in=$ex_trn_data[1];
						$trans_in_roll_tr_count=$ex_trn_data[2];

						$kniting_company=""; $prog_date=""; $booking_no=''; $booking_id=''; $fabric_desc=""; $color_range_name=""; $color_name=""; $mc_dia_gg=""; $dia_width=""; $width_dia_type=""; $stitch_length=""; $gsm=""; $yarn_count_value=""; $lot=""; $prog_qty=0;

						$booking_no = $trans_row_ref_arr[$to_order_id]['sales_booking_no'];
						$booking_id=$trans_row_ref_arr[$to_order_id]['booking_id'];

						$knitting_source=$trns_data_arr[$prog_no]['knitting_source'];
						$knitting_party=$trns_data_arr[$prog_no]['knitting_party'];

						if($knitting_source==1) $kniting_company=$company_arr[$knitting_party]; else if ($knitting_source==3) $kniting_company=$supplier_arr[$knitting_party];

						$prog_date=$trns_data_arr[$prog_no]['program_date'];
						$fabric_desc=$trns_data_arr[$prog_no]['fabric_desc'];

						$ex_color_range_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_range_id']));
						foreach($ex_color_range_id as $range_id)
						{
							if($range_id>0)
							{
								if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
							} 
						}

						$ex_color_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_id']));
						foreach($ex_color_id as $color_id)
						{
							if($color_id>0)
							{
								if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
							} 
						}

						$mc_dia_gg=$trns_data_arr[$prog_no]['mc_dia_gg'];
						$dia_width=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['dia_width']))));;
						$width_dia_type=$trns_data_arr[$prog_no]['width_dia_type'];
						$stitch_length="";
						$stitch_length=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['stitch_length']))));

						$gsm="";
						$gsm=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['gsm']))));

						$yarn_prod_ids=array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['yarn_prod_id'])));
						$yarn_brand_value='';
						foreach($yarn_prod_ids as $p_val)
						{
							if($p_val)
							{
								if($yarn_brand_value=='') $yarn_brand_value=$brand_yarn_arr[$p_val]; else $yarn_brand_value.=", ".$brand_yarn_arr[$p_val];
							}
						}

						$y_count=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['ycount'],",")))));
						$y_count_id=array_unique(explode(',',$y_count));

						$yarn_count_value='';
						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
							}
						}
						$lot=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['lot'],",")))));

						$knit_issue_qty=$knit_issue_arr[$prog_no][$to_order_id]['qnty'];
						$ex_issue_roll=explode(",",$knit_issue_arr[$prog_no][$to_order_id]['roll']);
						$iss_roll_no="";
						foreach($ex_issue_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$iss_roll_arr))
							{
								$iss_roll_no+=$val[1];
								$iss_roll_arr[]=$val[0];
							}
						}

						$withingroup=$trans_row_ref_arr[$to_order_id]['within_group'];
						$buyer_with=''; $style_with="";
						if($withingroup==1)
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['po_buyer'];
						}
						else
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['buyer_id'];

						}
						$style_with=$trans_row_ref_arr[$to_order_id]['style_ref_no'];

						$tot_knit_grey_recv=$knit_recv_arr[$prog_no]['qnty']+$knit_recv_arr[$prog_no]['qnty'];
						$recv_qnty=$recv_array[$prog_no][$to_order_id]['rec_qty'];
						$ex_recv_roll=explode(",",$recv_array[$prog_no][$to_order_id]['roll']);
						$rec_roll_no="";
						foreach($ex_recv_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$rec_roll_arr))
							{
								$rec_roll_no+=$val[1];
								$rec_roll_arr[]=$val[0];
							}
						}

						$trans_qty_out=$transfer_qty_arr[$prog_no][$to_order_id]['transfer_out'];
						$trans_qty_in=$trans_qty_in;
						$totalRecv=$recv_qnty+$trans_qty_in;
						$totalIssue=$knit_issue_qty+$trans_qty_out;
						$row_stock=0;
						$row_stock=$totalRecv-$totalIssue;

						$roll_no='';
						$roll_data=explode(",",$yarn_lot_arr[$prog_no]['roll']);
						foreach($roll_data as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$roll_arr))
							{
								$roll_no+=$val[1];
								$roll_arr[]=$val[0];
							}
						}
						$recv_roll_tr_count=$iss_roll_tr_count=$trans_out_roll_tr_count=0;
						$recv_roll_tr_count =$recv_array[$prog_no][$to_order_id]['roll_count'];
						$iss_roll_tr_count = $knit_issue_arr[$prog_no][$to_order_id]['roll_no'];

						$trans_out_roll_tr_count= $transfer_qty_arr[$prog_no][$to_order_id]['transOut_roll_no'];

						$remaining_roll_tran_tr = ($recv_roll_tr_count + $trans_in_roll_tr_count) - ($iss_roll_tr_count + $trans_out_roll_tr_count);

						$remark=$knit_issue_arr[$prog_no][$to_order_id]['remarks'];

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"><p><? echo $kniting_company; ?></p></td>
							<td width="50"><? echo $prog_no.'-[T]'; ?></td>
							<td width="60"><? echo change_date_format($prog_date); ?></td>
							<td width="80"><p><? echo $buyer_arr[$buyer_with]; ?></p></td>
							<td width="100"><p><? echo $style_with; ?></p></td>
							<td width="100"><? echo $trans_row_ref_arr[$to_order_id]['sales_booking_no']; ?></td>
							<td width="110"><? echo $trans_row_ref_arr[$to_order_id]['job_no']; ?></td>
							<td width="200"><p><? echo $fabric_desc; ?></p></td>
							<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
							<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
							<td width="70"><? echo $mc_dia_gg; ?>&nbsp;</td>
							<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
							<td width="70"><? echo $fabric_typee[$width_dia_type]; ?>&nbsp;</td>
							<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="70"><? echo $gsm; ?>&nbsp;</td>
							<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
							<td width="60"><p><? echo $lot; ?></p>&nbsp;</td>
							<td width="100"><p><? echo $yarn_brand_value; ?></p>&nbsp;</td>

							<td width="80" align="right"><? echo number_format($prog_qty,2); ?></td>
							<td width="80" align="right"><? echo number_format($recv_qnty,2,'.',''); ?></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','trans_in_popup');"><? echo number_format($trans_qty_in,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
							<td width="80" align="right"><a href='#report_details' onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','issue_grey_popup');"><? echo number_format($knit_issue_qty,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($trans_qty_out,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
							<td width="80" align="right">
								<?
								if(number_format($row_stock,2,'.','') == "-0.00")
								{
									echo "0.00";
								}else{
									echo number_format($row_stock,2,'.','');
								}
								?>

							</td>
							<td width="80" align="center" title="<? ?>"><p><? echo $remaining_roll_tran_tr;?></p>&nbsp;</td>
							<td><p><? echo $remark; ?></p>&nbsp;</td>
						</tr>
						<?
						$tot_program_qnty+=$prog_qty;
						$tot_recv_qnty+=$recv_qnty;
						$tot_trans_qty_in+=$trans_qty_in;
						$tot_totalRecv+=$totalRecv;
						$tot_knit_issue_qty+=$knit_issue_qty;
						$tot_trans_qty_out+=$trans_qty_out;
						$tot_totalIssue+=$totalIssue;
						$tot_row_stock+=$row_stock;
						$tot_roll_no += $remaining_roll_tran_tr;
						$i++;

					}
				}
				else
				{
					echo "3**".'Data Not Found'; die;
				}
				unset($nameArray);
				?>
			</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" id="">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="200">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="80" align="right" id="value_program_qnty"><? echo number_format($tot_program_qnty,2);?></th>
				<th width="80" align="right" id="value_recv_qnty"><? echo number_format($tot_recv_qnty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_in"><? echo number_format($tot_trans_qty_in,2);?></th>
				<th width="80" align="right" id="value_totalRecv"><? echo number_format($tot_totalRecv,2);?></th>
				<th width="80" align="right" id="value_knit_issue_qty"><? echo number_format($tot_knit_issue_qty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_out"><? echo number_format($tot_trans_qty_out,2);?></th>
				<th width="80" align="right" id="value_totalIssue"><? echo number_format($tot_totalIssue,2);?></th>
				<th width="80" align="right" id="value_row_stock"><? echo number_format($tot_row_stock,2);?></th>
				<th width="80" align="right" id="value_roll"><? echo $tot_roll_no;?></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
	$html = ob_get_contents();
	ob_clean();

	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
	exit();
}

if($action=="report_generate__bk")//crm 3298
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$program_no= str_replace("'","",$txt_program_no);
	$date_from= str_replace("'","",$txt_date_from);

	if($within_group) $within_group_cond = " and a.within_group='$within_group' "; else $within_group_cond = "";

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and a.po_buyer in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else if ($within_group==2 )
		{
			$buyer_id_cond=" and a.buyer_id in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond=" and a.po_company_id='$pocompany_id' ";
	$date_cond="";$prog_date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
		$prog_date_cond = " and b.program_date='$date_from'";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(c.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(c.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}

	if($year_id!=0) $sales_year_cond = " and a.job_no like '%-".substr($year_id, -2)."-%'"; else $sales_year_cond="";

	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and a.job_no_prefix_num='$order_no'";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and d.id='$program_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and a.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and a.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}
		$prog_cond_booking_no = " and a.booking_no like '%$booking_no%'";
		$prog_cond_booking_no .= " and a.booking_no like '%-".substr($year_id, -2)."-%'";
	} else {
		$booking_no_cond="";
	}

	$variable_data=sql_select("select variable_list, fabric_roll_level, auto_update from variable_settings_production where company_name ='$company_name' and variable_list in(3,15) and item_category_id=13 and is_deleted=0 and status_active=1");
	foreach($variable_data as $row)
	{
		if($row[csf('variable_list')]==3)
		{
			$roll_maintained=$row[csf('fabric_roll_level')];
		}
		else
		{
			$fabric_store_auto_update=$row[csf('auto_update')];
		}
	}

	if ($program_no=="") $program_cond_trans=""; else $program_cond_trans=" and b.id in ($program_no) ";
	
	if($program_no !="" || $date_from != "" || $booking_no!="")
	{	
		$programSqlForTrans = sql_select("select b.id, a.booking_no, c.po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c
			where a.id=b.mst_id and c.dtls_id=b.id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0
			and a.company_id = $company_name $program_cond_trans $prog_cond_booking_no $prog_date_cond");

		foreach($programSqlForTrans as $prog)
		{
			$sales_id_arr[$prog[csf("po_id")]] = $prog[csf("po_id")];
		}
		$sales_id_arr = array_filter($sales_id_arr);
		if(count($sales_id_arr)>0)
		{
			$all_sales_id_cond=""; $salesCond="";
			if($db_type==2 && count($sales_id_arr)>999)
			{
				$sales_id_arr_chunk=array_chunk($sales_id_arr,999) ;
				foreach($sales_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$salesCond.="  a.id in($chunk_arr_value) or ";
				}

				$all_sales_id_cond.=" and (".chop($salesCond,'or ').")";
			}
			else
			{

				$all_sales_id_cond=" and a.id in(".implode(",", $sales_id_arr).")";
			}
		}
		unset($programSqlForTrans);
	}

	// Main Query
	$sql="select a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id

	from fabric_sales_order_mst a
	left join ppl_planning_entry_plan_dtls e on a.id=e.po_id and e.status_active=1
	left join ppl_planning_info_entry_dtls d on e.dtls_id=d.id and d.status_active=1 $program_no_cond $date_cond
	left join ppl_planning_info_entry_mst c on d.mst_id=c.id and c.is_sales=1 and c.status_active=1 $year_search_cond

	where a.company_id='$company_name' $within_group_cond $buyer_id_cond $pocompany_cond $order_no_cond $booking_no_cond $all_sales_id_cond $sales_year_cond
	group by a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, e.fabric_desc, e.program_qnty,e.po_id,d.knitting_source,d.knitting_party, d.id, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type";

	$nameArray=sql_select($sql);
	$to_poids="";
	foreach($nameArray as $row)
	{
		$program_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
		$booking_no_arr[] = "'".$row[csf("sales_booking_no")]."'";
		$to_poids.= $row[csf("id")].",";

		$trans_row_ref_arr[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$trans_row_ref_arr[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$trans_row_ref_arr[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$trans_row_ref_arr[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_source'] = $row[csf('knitting_source')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_party'] = $row[csf('knitting_party')];
		$trans_row_ref_arr[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$trans_row_ref_arr[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
		$trans_row_ref_arr[$row[csf('id')]]['po_buyer'] = $row[csf('po_buyer')];
	}

	$to_poids = implode(",", array_filter(array_unique(explode(",",chop($to_poids,",")))));
	$to_pocond = $trns_to_po_cond = "";
	$to_poids_arr=explode(",",$to_poids);
	if(count($to_poids_arr)>0)
	{
		if($db_type==2 && count($to_poids_arr)>999)
		{
			$to_poids_chunk=array_chunk($to_poids_arr,999) ;
			foreach($to_poids_chunk as $chunk_arr)
			{
				$to_pocond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$trns_to_po_cond.=" and (".chop($to_pocond,'or ').")";
		}
		else
		{
			$trns_to_po_cond=" and a.to_order_id in($to_poids)";
		}
	}

	$data_trans_sql="select a.to_order_id, b.to_program,e.po_buyer,e.buyer_id, sum(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in, count(d.id) as roll_no from inv_item_transfer_mst a,inv_item_transfer_dtls b,order_wise_pro_details c,pro_roll_details d,fabric_sales_order_mst e where a.entry_form=133 and a.item_category=13 and a.transfer_criteria=4 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.dtls_id=d.dtls_id and d.po_breakdown_id= e.id and b.to_program>0 and c.trans_type in(5) and d.status_active=1 and d.is_deleted=0 and c.entry_form=133 and d.entry_form = 133 $trns_to_po_cond group by a.to_order_id, b.to_program,e.po_buyer,e.buyer_id";

	$data_trans=sql_select($data_trans_sql);
	$trns_row_data=array();
	foreach($data_trans as $row_b)
	{
		$trns_row_data[$row_b[csf('to_program')]]=$row_b[csf('to_order_id')].'!!!!'.$row_b[csf('item_transfer_in')].'!!!!'.$row_b[csf('roll_no')];
		$trns_row_prog_ref_data[$row_b[csf('to_program')]] = $row_b[csf('to_program')];

		$program_no_arr[$row_b[csf("to_program")]] = $row_b[csf("to_program")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["po_buyer"] = $row_b[csf("po_buyer")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["buyer_id"] = $row_b[csf("buyer_id")];
	}
	unset($data_trans);

	$program_no_arr = array_filter($program_no_arr);
	if(count($program_no_arr)>0)
	{
		$program_no_arr = explode(",","'".implode("','",$program_no_arr)."'");

		$all_program_nos = implode(",",$program_no_arr);
		$progCond = $all_rcv_prog_cond = "";
		$to_progcond=$from_progcond=$transfer_all_prog_cond="";

		if($db_type==2 && count($program_no_arr)>999)
		{
			$program_no_arr_chunk=array_chunk($program_no_arr,999) ;
			foreach($program_no_arr_chunk as $chunk_arr)
			{
				$progCond.=" d.booking_no in(".implode(",",$chunk_arr).") or ";
				$progCond2.=" b.program_no in(".implode(",",$chunk_arr).") or ";

				$to_progcond.=" b.to_program in(".implode(",",$chunk_arr).") or ";
				$from_progcond.=" b.from_program in(".implode(",",$chunk_arr).") or ";
			}

			$all_rcv_prog_cond.=" and (".chop($progCond,'or ').")";
			$all_rcv_prog_cond2.=" and (".chop($progCond2,'or ').")";

			$transfer_all_prog_cond .= " and (" .chop($to_progcond,'or ')." or ". chop($from_progcond,'or ') .")";
		}
		else
		{
			$all_rcv_prog_cond=" and d.booking_no in($all_program_nos)";
			$all_rcv_prog_cond2=" and b.program_no in($all_program_nos)";

			$transfer_all_prog_cond = " and ( b.to_program in ($all_program_nos) or  b.from_program in ($all_program_nos) ) ";
		}

		$production_ref = sql_select("select a.booking_no, c.po_breakdown_id as po_id, sum(c.quantity) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no, c.trans_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
			left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(2) and d.is_sales= 1
			where a.item_category=13 and a.receive_basis =2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $all_rcv_prog_cond 
			group by a.booking_no,c.po_breakdown_id,d.barcode_no,c.trans_id");

		foreach ($production_ref as $row)
		{
			if($row[csf('trans_id')] >0){
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
			}
			else
			{
				$production_barcode[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				$production_barcode_ref[$row[csf('barcode_no')]]["booking_no"] = $row[csf('booking_no')];

				$production_id_arr[$row[csf('id')]] = $row[csf('id')];
				$program_ref_arr[$row[csf('id')]] = $row[csf('booking_id')];
			}

			$production_all_ref_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			$prod_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}

		$production_id_arr = array_filter($production_id_arr);
		if(count($production_id_arr) > 0)
		{
			$all_production_id_arr = implode(",", $production_id_arr);
			$productionCond = ""; $production_cond_for_rcv = "";
			if($db_type==2 && count($production_id_arr)>999)
			{
				$all_production_id_arr_chunk=array_chunk($production_id_arr,999) ;
				foreach($all_production_id_arr_chunk as $chunk_arr)
				{
					$productionCond.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
				}

				$production_cond_for_rcv.=" and (".chop($productionCond,'or ').")";

			}
			else
			{
				$production_cond_for_rcv=" and a.booking_id in($all_production_id_arr)";
			}

			$sql_recv=sql_select("select a.booking_id,c.po_breakdown_id as po_id,sum(c.quantity) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 $production_cond_for_rcv group by a.booking_id,c.po_breakdown_id");
			$minimum_date = "";
			foreach($sql_recv as $row)
			{
				$recv_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];
			}
			unset($sql_recv);
		}

		if(!empty($production_barcode))
		{
			$production_barcode = array_filter($production_barcode);
			$all_production_barcode_nos = implode(",", $production_barcode);
			$all_production_barcode_cond=""; $barCond="";
			if($db_type==2 && count($production_barcode)>999)
			{
				$production_barcode_chunk=array_chunk($production_barcode,999) ;
				foreach($production_barcode_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  d.barcode_no in($chunk_arr_value) or ";
				}

				$all_production_barcode_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				if($all_production_barcode_nos!=""){
					$all_production_barcode_cond=" and d.barcode_no in($all_production_barcode_nos)";
				}
			}
		}

		if(!empty($prod_id_arr)){
			$prod_id_arr = array_filter($prod_id_arr);
			$all_production_po_nos = implode(",", $prod_id_arr);
			$all_production_po_cond=""; $barCond="";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$production_po_chunk=array_chunk($prod_id_arr,999) ;
				foreach($production_po_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$all_production_po_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$all_production_po_cond=" and c.po_breakdown_id in($all_production_po_nos)";
			}
		}
		$sql_recv_roll=sql_select("select a.entry_form,a.booking_no, c.po_breakdown_id as po_id, sum(d.qnty) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, pro_roll_details d
			where a.item_category=13 and a.entry_form in(58) and a.receive_basis =10 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(58) and b.id=d.dtls_id and d.entry_form in(58) and d.is_sales= 1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id!=0 and d.status_active=1 and d.is_deleted=0 $all_production_barcode_cond $all_production_po_cond
			group by a.booking_no,c.po_breakdown_id,d.barcode_no,a.entry_form");

		foreach($sql_recv_roll as $row)
		{
			$production_booking = $production_barcode_ref[$row[csf('barcode_no')]]["booking_no"];
			$recv_array[$production_booking][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
			$recv_array[$production_booking][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
		}
		unset($sql_recv_roll);


		$production_all_ref_barcode_arr = array_filter($production_all_ref_barcode_arr);
		if(!empty($production_all_ref_barcode_arr))
		{
			$production_all_ref_barcode_nos = implode(",", $production_all_ref_barcode_arr);
			$production_all_ref_barcode_cond=""; $barCond="";
			if($db_type==2 && count($production_all_ref_barcode_arr)>999)
			{
				$production_all_ref_barcode_arr_chunk=array_chunk($production_all_ref_barcode_arr,999) ;
				foreach($production_all_ref_barcode_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.=" a.barcode_no in($chunk_arr_value) or ";
				}

				$production_all_ref_barcode_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$production_all_ref_barcode_cond=" and a.barcode_no in($production_all_ref_barcode_nos)";
			}

			$roll_issue_sql = sql_select(" select count(a.barcode_no) as roll_no, a.barcode_no, a.qnty, a.entry_form, a.booking_no, a.po_breakdown_id, b.remarks from pro_roll_details a,  inv_grey_fabric_issue_dtls b where a.dtls_id = b.id and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.booking_without_order=0 and a.is_returned <> 1 $production_all_ref_barcode_cond group by a.barcode_no, a.qnty, a.entry_form, a.booking_no, a.po_breakdown_id, b.remarks ");
			$knit_issue_arr=array();
			foreach($roll_issue_sql as $row)
			{
				$knit_issue_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]['roll_no'] +=$row[csf('roll_no')];
				$knit_issue_arr[$row[csf("booking_no")]][$row[csf("po_breakdown_id")]]["qnty"] += $row[csf("qnty")];
				$knit_issue_arr[$row[csf("booking_no")]][$row[csf("po_breakdown_id")]]["remarks"] += $row[csf("remarks")];
			}
			unset($roll_issue_sql);
		}
		
		$sql_data=sql_select("select b.program_no, b.remarks, c.quantity,c.po_breakdown_id, sum(b.no_of_roll) as roll_no
			from  inv_issue_master a,inv_grey_fabric_issue_dtls b, order_wise_pro_details c
			where a.id=b.mst_id and b.id = c.dtls_id
			and c.trans_type = 2 and a.item_category=13 and a.entry_form in (16) and c.entry_form in (16)
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and c.status_active = 1 and c.is_deleted = 0 and b.program_no <> 0 and b.program_no is not null $all_rcv_prog_cond2");

		foreach($sql_data as $row)
		{
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('quantity')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['remarks']=$row[csf('remarks')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['roll_no'] +=$row[csf('roll_no')];
		}
		unset($sql_data);

		$trans_data_array=sql_select("select a.from_order_id, a.to_order_id, b.from_program, b.to_program ,d.barcode_no, c.trans_type,d.qnty,b.id as dtls_id
			from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c
			left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form=133 and d.is_sales=1
			where a.id=b.mst_id and c.dtls_id=b.id and c.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and c.entry_form=133 and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4 and b.from_program>0 and b.to_program>0 and b.status_active=1 and b.is_deleted=0 $transfer_all_prog_cond");

		$transfer_qty_arr=array();
		$chkDtlsIdArr=array();
		$rt = 1;
		foreach($trans_data_array as $row_b)
		{
			$chkDtlsIdArr[$row_b[csf("dtls_id")]] = $row_b[csf("dtls_id")];
			if($row_b[csf("trans_type")] == "6")
			{
				$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transfer_out']+=$row_b[csf('qnty')];
				$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transOut_roll_no']+=$rt;
			}else{
				$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transfer_in']+=$row_b[csf('qnty')];
				$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transIn_roll_no']+=$rt;
			}
		}

		$all_trans_prog =implode(",",array_filter(array_unique($trns_row_prog_ref_data)));
		if($all_trans_prog)
		{
			if($all_trans_prog=="") $all_trans_prog=0;
			$progCond = $all_trans_prog_cond = "";
			$all_trans_prog_arr=explode(",",$all_trans_prog);
			if($db_type==2 && count($all_trans_prog_arr)>999)
			{
				$all_trans_prog_chunk=array_chunk($all_trans_prog_arr,999) ;
				foreach($all_trans_prog_chunk as $chunk_prog)
				{
					$chunk_prog_val=implode(",",$chunk_prog);
					$progCond.=" d.id in($chunk_prog_val) or ";
				}
				$all_trans_prog_cond.=" and (".chop($progCond,'or ').")";
			}
			else
			{
				$all_trans_prog_cond=" and d.id in($all_trans_prog)";
			}
			$trans_pro_ref = sql_select("select d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id from ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls e where c.id=d.mst_id and d.id=e.dtls_id and c.is_sales=1 and e.is_sales=1 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $all_trans_prog_cond");

			foreach($trans_pro_ref as $row)
			{
				$trns_data_arr[$row[csf('prog_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
				$trns_data_arr[$row[csf('prog_no')]]['booking_id']=$row[csf('booking_id')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no']=$row[csf('job_no')];
				$trns_data_arr[$row[csf('prog_no')]]['knitting_source']=$row[csf('knitting_source')];
				$trns_data_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
				$trns_data_arr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
				$trns_data_arr[$row[csf('prog_no')]]['mc_dia_gg']=$row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
				$trns_data_arr[$row[csf('prog_no')]]['width_dia_type']=$row[csf('width_dia_type')];
				$trns_data_arr[$row[csf('prog_no')]]['fabric_desc']=$row[csf('fabric_desc')];
				$trns_data_arr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				$trns_data_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
				$trns_data_arr[$row[csf('prog_no')]]['style']=$row[csf('style_ref_no')];
				$trns_data_arr[$row[csf('prog_no')]]['group']=$row[csf('within_group')];
			}
			unset($trans_pro_ref);
		}

		$yarn_lot_data=sql_select("select d.booking_id, a.yarn_lot, a.color_range_id, a.color_id, a.width as dia_width, a.stitch_length, a.gsm, a.yarn_count, b.po_breakdown_id, a.yarn_prod_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b, inv_receive_master d where d.id=a.mst_id and a.id=b.dtls_id and b.entry_form=2 and d.entry_form=2 and d.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_sales=1 $all_rcv_prog_cond");

		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot_arr[$rows[csf('booking_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['roll'] .=$rows[csf('no_of_roll')];
			$yarn_lot_arr[$rows[csf('booking_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['color_id'] .=$rows[csf('color_id')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['gsm'] .=$rows[csf('gsm')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['yarn_prod_id'] .=$rows[csf('yarn_prod_id')].",";
			$yarn_prod_arr[$rows[csf('yarn_prod_id')]] =$rows[csf('yarn_prod_id')];

			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['roll'] .=$rows[csf('no_of_roll')];
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_id'] .=$rows[csf('color_id')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['gsm'] .=$rows[csf('gsm')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['yarn_prod_id'] .=$rows[csf('yarn_prod_id')].",";
		}

		unset($yarn_lot_data);

		$all_yarn_prod_id_arr = array_filter($yarn_prod_arr);
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
			$brand_yarn_arr = return_library_array("select a.id, b.brand_name from product_details_master a, lib_brand b where a.brand = b.id and b.status_active = 1 and a.status_active=1 $all_yarn_prod_id_cond ","id","brand_name");
		}
	}
	ob_start();
	?>
	<fieldset style="width:2490px;">
		<table width="2490" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="80">Knitting Company</th>
				<th width="50">Prog. No</th>
				<th width="60">Prog. Date</th>
				<th width="80">PO Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="100">Fab. Booking No</th>
				<th width="110">Sales Order No</th>
				<th width="200">Fab. Description</th>
				<th width="100">Color Range</th>
				<th width="120">Fab. Color</th>
				<th width="70">MC DXG</th>
				<th width="70">F.Dia</th>
				<th width="70">Dia Type</th>
				<th width="70">S/L</th>
				<th width="70">FGSM</th>
				<th width="60">Y/Count</th>
				<th width="60">Y/Lot</th>
				<th width="100">Y/Brand</th>
				<th width="80">Prog Qty/kg</th>
				<th width="80">Receive Qty(kg)</th>
				<th width="80">Trans In Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Delivery Qty(kg)</th>
				<th width="80">Trans Out Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Stock Qty(kg)</th>
				<th width="80">Roll</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="width:2480px; overflow-y:scroll; max-height:350px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2460" class="rpt_table" id="tbl_list_search">
				<?
				if(count($nameArray)>0 || count($trns_row_data)>0)
				{
					$i=1;
					$row_stock=0;
					foreach($nameArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$prog_no = $row[csf("prog_no")];
						if($row[csf("prog_no")]!=""){
							$po_id = $row[csf("po_id")];

							$kniting_company="";
							if($row[csf("knitting_source")]==1) $kniting_company=$company_arr[$row[csf("knitting_party")]]; else if ($row[csf("knitting_source")]==3) $kniting_company=$supplier_arr[$row[csf("knitting_party")]];

							$ex_color_range_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_range_id'],",")));

							$color_range_name="";
							foreach($ex_color_range_id as $range_id)
							{
								if($range_id>0)
								{
									if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
								} 
							}
							$ex_color_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_id'],",")));

							$color_name="";
							foreach($ex_color_id as $color_id)
							{
								if($color_id>0)
								{
									if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
								}
							}
							$dia_width="";
							$dia_width=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['dia_width'],",")))));

							$stitch_length="";
							$stitch_length=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['stitch_length'],",")))));

							$gsm="";
							$gsm=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['gsm'],",")))));


							$yarn_prod_ids= array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['yarn_prod_id'],","))));

							$yarn_brand_value='';
							foreach($yarn_prod_ids as $p_val)
							{
								if($p_val)
								{
									if($yarn_brand_value=='') $yarn_brand_value=$brand_yarn_arr[$p_val]; else $yarn_brand_value.=", ".$brand_yarn_arr[$p_val];
								}
							}

							$y_count=chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['ycount'],",");
							$y_count_id=array_unique(explode(',',$y_count));

							$yarn_count_value='';
							foreach($y_count_id as $val)
							{
								if($val>0)
								{
									if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
								}
							}

							$yarn_lot = "";
							$yarn_lot = implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['lot'],",")))));

							$knit_issue_qty=$knit_issue_arr[$prog_no][$po_id]['qnty'];
							$iss_roll_no=$knit_issue_arr[$prog_no][$po_id]['roll'];
							$recv_qnty=$recv_array[$prog_no][$po_id]['rec_qty'];
							$ex_recv_roll=explode(",",$recv_array[$prog_no][$po_id]['roll']);
							$rec_roll_no=$recv_array[$prog_no][$po_id]['roll'];

							$recv_roll_count=$recv_array[$prog_no][$po_id]['roll_count'];
							$transOut_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transOut_roll_no'];
							$transIn_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transIn_roll_no'];
							$iss_roll_no_count=$knit_issue_arr[$prog_no][$po_id]['roll_no'];
							$remaining_roll = ($recv_roll_count + $transIn_roll_no_count)- ($transOut_roll_no_count + $iss_roll_no_count);

							$trans_qty_out=$transfer_qty_arr[$prog_no][$po_id]['transfer_out'];
							$trans_qty_in=$transfer_qty_arr[$prog_no][$po_id]['transfer_in'];
							$totalRecv=$recv_qnty+$trans_qty_in;
							$totalIssue=$knit_issue_qty+$trans_qty_out;

							$row_stock=$totalRecv-$totalIssue;

							$remark=$knit_issue_arr[$prog_no][$po_id]['remarks'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="80"><p><? echo $kniting_company; ?></p></td>
								<td width="50"><? echo $row[csf("prog_no")]; ?></td>
								<td width="60"><? echo change_date_format($row[csf("program_date")]); ?></td>
								<td width="80">
									<p>
										<?
										echo ($row[csf("within_group")] == 1)?$buyer_arr[$row[csf("po_buyer")]]:$buyer_arr[$row[csf("buyer_id")]];
										?>
									</p>
								</td>
								<td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="100"><? echo $row[csf("sales_booking_no")]; ?></td>
								<td width="110"><? echo $row[csf("job_no")]; ?></td>
								<td width="200"><p><? echo $row[csf("fabric_desc")]; ?></p></td>
								<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
								<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
								<td width="70"><? echo $row[csf("machine_dia")].'X'.$row[csf("machine_gg")]; ?>&nbsp;</td>
								<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
								<td width="70"><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?>&nbsp;</td>
								<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
								<td width="70"><? echo $gsm; ?>&nbsp;</td>
								<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
								<td width="60"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
								<td width="100"><p><? echo $yarn_brand_value; ?></p>&nbsp;</td>
								<td width="80" align="right"><? echo number_format($row[csf("program_qnty")],2); ?></td>
								<td width="80" align="right" title="<? echo $row[csf('prog_no')]."=".$po_id?>"><a href='#report_details' onClick='openmypage_receive("<? echo $po_id; ?>","<? echo $row[csf('prog_no')]; ?>","<? echo $row[csf('sales_booking_no')]; ?>","receive_grey_popup");'><? echo number_format($recv_qnty,2,'.',''); ?></a></td>
								<td width="80" align="right">
									<?
									echo number_format($trans_qty_in,2,'.','');
									?>
								</td>
								<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
								<td width="80" align="right">
									<a href='#report_details' onClick="openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','issue_grey_popup');">
										<? echo number_format($knit_issue_qty,2,'.',''); ?>
									</a>
								</td>
								<td width="80" align="right"><a href="##" onClick=" openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','trans_out_popup');"><? echo number_format($trans_qty_out,2,'.',''); ?></a></td>
								<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
								<td width="80" align="right">
									<?
									if(number_format($row_stock,2,'.','') == "-0.00")
									{
										echo "0.00";
									}
									else{
										echo number_format($row_stock,2,'.','');
									}
									?>
								</td>
								<td width="80" align="center" title="<? echo $title;?>"><p><? echo $remaining_roll; ?></p>&nbsp;</td>
								<td><p><? echo $remark; ?></p>&nbsp;</td>
							</tr>
							<?php
							$tot_program_qnty+=$program_qnty;
							$tot_recv_qnty+=$recv_qnty;
							$tot_trans_qty_in+=$trans_qty_in;
							$tot_totalRecv+=$totalRecv;
							$tot_knit_issue_qty+=$knit_issue_qty;
							$tot_trans_qty_out+=$trans_qty_out;
							$tot_totalIssue+=$totalIssue;
							$tot_row_stock+=$row_stock;
							$tot_roll_no += $remaining_roll;
							$i++;
						}
					}
					foreach($trns_row_data as $prog_no=>$trns_data)
					{
						$ex_trn_data=explode("!!!!",$trns_data);

						$to_order_id=$ex_trn_data[0];
						$trans_qty_in=$ex_trn_data[1];
						$trans_in_roll_tr_count=$ex_trn_data[2];

						$kniting_company=""; $prog_date=""; $booking_no=''; $booking_id=''; $fabric_desc=""; $color_range_name=""; $color_name=""; $mc_dia_gg=""; $dia_width=""; $width_dia_type=""; $stitch_length=""; $gsm=""; $yarn_count_value=""; $lot=""; $prog_qty=0;

						$booking_no = $trans_row_ref_arr[$to_order_id]['sales_booking_no'];
						$booking_id=$trans_row_ref_arr[$to_order_id]['booking_id'];

						$knitting_source=$trns_data_arr[$prog_no]['knitting_source'];
						$knitting_party=$trns_data_arr[$prog_no]['knitting_party'];

						if($knitting_source==1) $kniting_company=$company_arr[$knitting_party]; else if ($knitting_source==3) $kniting_company=$supplier_arr[$knitting_party];

						$prog_date=$trns_data_arr[$prog_no]['program_date'];
						$fabric_desc=$trns_data_arr[$prog_no]['fabric_desc'];

						$ex_color_range_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_range_id']));
						foreach($ex_color_range_id as $range_id)
						{
							if($range_id>0)
							{
								if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
							} 
						}

						$ex_color_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_id']));
						foreach($ex_color_id as $color_id)
						{
							if($color_id>0)
							{
								if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
							} 
						}

						$mc_dia_gg=$trns_data_arr[$prog_no]['mc_dia_gg'];
						$dia_width=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['dia_width']))));;
						$width_dia_type=$trns_data_arr[$prog_no]['width_dia_type'];
						$stitch_length="";
						$stitch_length=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['stitch_length']))));

						$gsm="";
						$gsm=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['gsm']))));

						$yarn_prod_ids=array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['yarn_prod_id'])));
						$yarn_brand_value='';
						foreach($yarn_prod_ids as $p_val)
						{
							if($p_val)
							{
								if($yarn_brand_value=='') $yarn_brand_value=$brand_yarn_arr[$p_val]; else $yarn_brand_value.=", ".$brand_yarn_arr[$p_val];
							}
						}

						$y_count=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['ycount'],",")))));
						$y_count_id=array_unique(explode(',',$y_count));

						$yarn_count_value='';
						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
							}
						}
						$lot=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['lot'],",")))));

						$knit_issue_qty=$knit_issue_arr[$prog_no][$to_order_id]['qnty'];
						$ex_issue_roll=explode(",",$knit_issue_arr[$prog_no][$to_order_id]['roll']);
						$iss_roll_no="";
						foreach($ex_issue_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$iss_roll_arr))
							{
								$iss_roll_no+=$val[1];
								$iss_roll_arr[]=$val[0];
							}
						}

						$withingroup=$trans_row_ref_arr[$to_order_id]['within_group'];
						$buyer_with=''; $style_with="";
						if($withingroup==1)
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['po_buyer'];
						}
						else
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['buyer_id'];

						}
						$style_with=$trans_row_ref_arr[$to_order_id]['style_ref_no'];

						$tot_knit_grey_recv=$knit_recv_arr[$prog_no]['qnty']+$knit_recv_arr[$prog_no]['qnty'];
						$recv_qnty=$recv_array[$prog_no][$to_order_id]['rec_qty'];
						$ex_recv_roll=explode(",",$recv_array[$prog_no][$to_order_id]['roll']);
						$rec_roll_no="";
						foreach($ex_recv_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$rec_roll_arr))
							{
								$rec_roll_no+=$val[1];
								$rec_roll_arr[]=$val[0];
							}
						}

						$trans_qty_out=$transfer_qty_arr[$prog_no][$to_order_id]['transfer_out'];
						$trans_qty_in=$trans_qty_in;
						$totalRecv=$recv_qnty+$trans_qty_in;
						$totalIssue=$knit_issue_qty+$trans_qty_out;
						$row_stock=0;
						$row_stock=$totalRecv-$totalIssue;

						$roll_no='';
						$roll_data=explode(",",$yarn_lot_arr[$prog_no]['roll']);
						foreach($roll_data as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$roll_arr))
							{
								$roll_no+=$val[1];
								$roll_arr[]=$val[0];
							}
						}
						$recv_roll_tr_count=$iss_roll_tr_count=$trans_out_roll_tr_count=0;
						$recv_roll_tr_count =$recv_array[$prog_no][$to_order_id]['roll_count'];
						$iss_roll_tr_count = $knit_issue_arr[$prog_no][$to_order_id]['roll_no'];

						$trans_out_roll_tr_count= $transfer_qty_arr[$prog_no][$to_order_id]['transOut_roll_no'];

						$remaining_roll_tran_tr = ($recv_roll_tr_count + $trans_in_roll_tr_count) - ($iss_roll_tr_count + $trans_out_roll_tr_count);

						$remark=$knit_issue_arr[$prog_no][$to_order_id]['remarks'];

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"><p><? echo $kniting_company; ?></p></td>
							<td width="50"><? echo $prog_no.'-[T]'; ?></td>
							<td width="60"><? echo change_date_format($prog_date); ?></td>
							<td width="80"><p><? echo $buyer_arr[$buyer_with]; ?></p></td>
							<td width="100"><p><? echo $style_with; ?></p></td>
							<td width="100"><? echo $trans_row_ref_arr[$to_order_id]['sales_booking_no']; ?></td>
							<td width="110"><? echo $trans_row_ref_arr[$to_order_id]['job_no']; ?></td>
							<td width="200"><p><? echo $fabric_desc; ?></p></td>
							<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
							<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
							<td width="70"><? echo $mc_dia_gg; ?>&nbsp;</td>
							<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
							<td width="70"><? echo $fabric_typee[$width_dia_type]; ?>&nbsp;</td>
							<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="70"><? echo $gsm; ?>&nbsp;</td>
							<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
							<td width="60"><p><? echo $lot; ?></p>&nbsp;</td>
							<td width="100"><p><? echo $yarn_brand_value; ?></p>&nbsp;</td>

							<td width="80" align="right"><? echo number_format($prog_qty,2); ?></td>
							<td width="80" align="right"><? echo number_format($recv_qnty,2,'.',''); ?></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','trans_in_popup');"><? echo number_format($trans_qty_in,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
							<td width="80" align="right"><a href='#report_details' onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','issue_grey_popup');"><? echo number_format($knit_issue_qty,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($trans_qty_out,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
							<td width="80" align="right">
								<?
								if(number_format($row_stock,2,'.','') == "-0.00")
								{
									echo "0.00";
								}else{
									echo number_format($row_stock,2,'.','');
								}
								?>

							</td>
							<td width="80" align="center" title="<? ?>"><p><? echo $remaining_roll_tran_tr;?></p>&nbsp;</td>
							<td><p><? echo $remark; ?></p>&nbsp;</td>
						</tr>
						<?
						$tot_program_qnty+=$prog_qty;
						$tot_recv_qnty+=$recv_qnty;
						$tot_trans_qty_in+=$trans_qty_in;
						$tot_totalRecv+=$totalRecv;
						$tot_knit_issue_qty+=$knit_issue_qty;
						$tot_trans_qty_out+=$trans_qty_out;
						$tot_totalIssue+=$totalIssue;
						$tot_row_stock+=$row_stock;
						$tot_roll_no += $remaining_roll_tran_tr;
						$i++;

					}
				}
				else
				{
					echo "3**".'Data Not Found'; die;
				}
				unset($nameArray);
				?>
			</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" id="">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="200">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="80" align="right" id="value_program_qnty"><? echo number_format($tot_program_qnty,2);?></th>
				<th width="80" align="right" id="value_recv_qnty"><? echo number_format($tot_recv_qnty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_in"><? echo number_format($tot_trans_qty_in,2);?></th>
				<th width="80" align="right" id="value_totalRecv"><? echo number_format($tot_totalRecv,2);?></th>
				<th width="80" align="right" id="value_knit_issue_qty"><? echo number_format($tot_knit_issue_qty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_out"><? echo number_format($tot_trans_qty_out,2);?></th>
				<th width="80" align="right" id="value_totalIssue"><? echo number_format($tot_totalIssue,2);?></th>
				<th width="80" align="right" id="value_row_stock"><? echo number_format($tot_row_stock,2);?></th>
				<th width="80" align="right" id="value_roll"><? echo $tot_roll_no;?></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
	$html = ob_get_contents();
	ob_clean();

	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
	exit();
}

if($action=="report_generate_weight_lvl")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$program_no= str_replace("'","",$txt_program_no);
	$date_from= str_replace("'","",$txt_date_from);

	if($within_group) $within_group_cond = " and a.within_group='$within_group' "; else $within_group_cond = "";

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and a.po_buyer in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else if ($within_group==2 )
		{
			$buyer_id_cond=" and a.buyer_id in (".str_replace("'","",$cbo_buyer_id).")";
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond=" and a.po_company_id='$pocompany_id' ";
	$date_cond="";$prog_date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
		$prog_date_cond = " and b.program_date='$date_from'";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(c.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(c.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}

	if($year_id!=0) $sales_year_cond = " and a.job_no like '%-".substr($year_id, -2)."-%'"; else $sales_year_cond="";

	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and a.job_no_prefix_num='$order_no'";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and d.id='$program_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and a.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and a.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}
		$prog_cond_booking_no = " and a.booking_no like '%$booking_no%'";
		$prog_cond_booking_no .= " and a.booking_no like '%-".substr($year_id, -2)."-%'";
	} else {
		$booking_no_cond="";
	}

	$variable_data=sql_select("select variable_list, fabric_roll_level, auto_update from variable_settings_production where company_name ='$company_name' and variable_list in(3,15) and item_category_id=13 and is_deleted=0 and status_active=1");
	foreach($variable_data as $row)
	{
		if($row[csf('variable_list')]==3)
		{
			$roll_maintained=$row[csf('fabric_roll_level')];
		}
		else
		{
			$fabric_store_auto_update=$row[csf('auto_update')];
		}
	}
	//echo $roll_maintained;

	if ($program_no=="") $program_cond_trans=""; else $program_cond_trans=" and b.id in ($program_no) ";
	
	if($program_no !="" || $date_from != "" || $booking_no!="")
	{	
		$programSqlForTrans = sql_select("select b.id, a.booking_no, c.po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c
			where a.id=b.mst_id and c.dtls_id=b.id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0
			and a.company_id = $company_name $program_cond_trans $prog_cond_booking_no $prog_date_cond");

		foreach($programSqlForTrans as $prog)
		{
			$sales_id_arr[$prog[csf("po_id")]] = $prog[csf("po_id")];
		}
		$sales_id_arr = array_filter($sales_id_arr);
		if(count($sales_id_arr)>0)
		{
			$all_sales_id_cond=""; $salesCond="";
			if($db_type==2 && count($sales_id_arr)>999)
			{
				$sales_id_arr_chunk=array_chunk($sales_id_arr,999) ;
				foreach($sales_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$salesCond.="  a.id in($chunk_arr_value) or ";
				}

				$all_sales_id_cond.=" and (".chop($salesCond,'or ').")";
			}
			else
			{

				$all_sales_id_cond=" and a.id in(".implode(",", $sales_id_arr).")";
			}
		}
		unset($programSqlForTrans);
	}

	// Main Query
	$sql="select a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id

	from fabric_sales_order_mst a
	left join ppl_planning_entry_plan_dtls e on a.id=e.po_id and e.status_active=1
	left join ppl_planning_info_entry_dtls d on e.dtls_id=d.id and d.status_active=1 $program_no_cond $date_cond
	left join ppl_planning_info_entry_mst c on d.mst_id=c.id and c.is_sales=1 and c.status_active=1 $year_search_cond

	where a.company_id='$company_name' $within_group_cond $buyer_id_cond $pocompany_cond $order_no_cond $booking_no_cond $all_sales_id_cond $sales_year_cond
	group by a.id, a.sales_booking_no, a.booking_id, a.job_no_prefix_num, a.job_no, a.buyer_id, a.style_ref_no,a.within_group, a.po_buyer, e.fabric_desc, e.program_qnty,e.po_id,d.knitting_source,d.knitting_party, d.id, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type";

	$nameArray=sql_select($sql);
	$to_poids="";
	foreach($nameArray as $row)
	{
		$program_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
		$booking_no_arr[] = "'".$row[csf("sales_booking_no")]."'";
		$to_poids.= $row[csf("id")].",";

		$trans_row_ref_arr[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$trans_row_ref_arr[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$trans_row_ref_arr[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$trans_row_ref_arr[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_source'] = $row[csf('knitting_source')];
		$trans_row_ref_arr[$row[csf('id')]]['knitting_party'] = $row[csf('knitting_party')];
		$trans_row_ref_arr[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$trans_row_ref_arr[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
		$trans_row_ref_arr[$row[csf('id')]]['po_buyer'] = $row[csf('po_buyer')];
	}

	$to_poids = implode(",", array_filter(array_unique(explode(",",chop($to_poids,",")))));
	$to_pocond = $trns_to_po_cond = "";
	$to_poids_arr=explode(",",$to_poids);
	if(count($to_poids_arr)>0)
	{
		if($db_type==2 && count($to_poids_arr)>999)
		{
			$to_poids_chunk=array_chunk($to_poids_arr,999) ;
			foreach($to_poids_chunk as $chunk_arr)
			{
				$to_pocond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$trns_to_po_cond.=" and (".chop($to_pocond,'or ').")";
		}
		else
		{
			$trns_to_po_cond=" and a.to_order_id in($to_poids)";
		}
	}

	$data_trans_sql = "select a.to_order_id, b.to_program,e.po_buyer,e.buyer_id, sum(c.quantity) as item_transfer_in, sum(b.no_of_roll) as roll_no from inv_item_transfer_mst a,inv_item_transfer_dtls b,order_wise_pro_details c, fabric_sales_order_mst e where a.entry_form=362 and a.item_category=13 and a.transfer_criteria=4 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id= e.id and b.to_program>0 and c.trans_type in(5) and c.entry_form=362 $trns_to_po_cond group by a.to_order_id, b.to_program,e.po_buyer,e.buyer_id";

	$data_trans=sql_select($data_trans_sql);
	$trns_row_data=array();
	foreach($data_trans as $row_b)
	{
		$trns_row_data[$row_b[csf('to_program')]]=$row_b[csf('to_order_id')].'!!!!'.$row_b[csf('item_transfer_in')].'!!!!'.$row_b[csf('roll_no')];
		$trns_row_prog_ref_data[$row_b[csf('to_program')]] = $row_b[csf('to_program')];

		$program_no_arr[$row_b[csf("to_program")]] = $row_b[csf("to_program")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["po_buyer"] = $row_b[csf("po_buyer")];
		$trans_row_buyer_style[$row_b[csf("to_order_id")]]["buyer_id"] = $row_b[csf("buyer_id")];
	}
	unset($data_trans);


	$program_no_arr = array_filter($program_no_arr);
	if(count($program_no_arr)>0)
	{
		$program_no_arr = explode(",","'".implode("','",$program_no_arr)."'");

		$all_program_nos = implode(",",$program_no_arr);
		$progCond = $all_rcv_prog_cond = "";
		$progCond3 = $all_rcv_prog_cond3 = "";
		$to_progcond=$from_progcond=$transfer_all_prog_cond="";

		if($db_type==2 && count($program_no_arr)>999)
		{
			$program_no_arr_chunk=array_chunk($program_no_arr,999) ;
			foreach($program_no_arr_chunk as $chunk_arr)
			{
				$progCond.=" d.booking_no in(".implode(",",$chunk_arr).") or ";
				$progCond2.=" b.program_no in(".implode(",",$chunk_arr).") or ";
				$progCond3.=" a.booking_id in(".implode(",",$chunk_arr).") or ";

				$to_progcond.=" b.to_program in(".implode(",",$chunk_arr).") or ";
				$from_progcond.=" b.from_program in(".implode(",",$chunk_arr).") or ";
			}

			$all_rcv_prog_cond.=" and (".chop($progCond,'or ').")";
			$all_rcv_prog_cond2.=" and (".chop($progCond2,'or ').")";
			$all_rcv_prog_cond3.=" and (".chop($progCond3,'or ').")";

			$transfer_all_prog_cond .= " and (" .chop($to_progcond,'or ')." or ". chop($from_progcond,'or ') .")";
		}
		else
		{
			$all_rcv_prog_cond=" and d.booking_no in($all_program_nos)";
			$all_rcv_prog_cond2=" and b.program_no in($all_program_nos)";
			$all_rcv_prog_cond3=" and a.booking_id in($all_program_nos)";

			$transfer_all_prog_cond = " and ( b.to_program in ($all_program_nos) or  b.from_program in ($all_program_nos) ) ";
		}

		$production_ref = sql_select("select a.booking_no, c.po_breakdown_id as po_id, sum(c.quantity) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no, c.trans_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
			left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(2) and d.is_sales= 1
			where a.item_category=13 and a.receive_basis =2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $all_rcv_prog_cond3 
			group by a.booking_no,c.po_breakdown_id,d.barcode_no,c.trans_id");

		foreach ($production_ref as $row)
		{
			if($row[csf('trans_id')] >0){
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
				$recv_array[$row[csf('booking_no')]][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
			}
			else
			{
				$production_barcode[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				$production_barcode_ref[$row[csf('barcode_no')]]["booking_no"] = $row[csf('booking_no')];

				$production_id_arr[$row[csf('id')]] = $row[csf('id')];
				$program_ref_arr[$row[csf('id')]] = $row[csf('booking_id')];
			}

			$production_all_ref_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			$prod_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}

		$production_id_arr = array_filter($production_id_arr);
		if(count($production_id_arr) > 0)
		{
			$all_production_id_arr = implode(",", $production_id_arr);
			$productionCond = ""; $production_cond_for_rcv = "";
			if($db_type==2 && count($production_id_arr)>999)
			{
				$all_production_id_arr_chunk=array_chunk($production_id_arr,999) ;
				foreach($all_production_id_arr_chunk as $chunk_arr)
				{
					$productionCond.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
				}

				$production_cond_for_rcv.=" and (".chop($productionCond,'or ').")";

			}
			else
			{
				$production_cond_for_rcv=" and a.booking_id in($all_production_id_arr)";
			}

			$sql_recv=sql_select("select a.booking_id,c.po_breakdown_id as po_id,sum(c.quantity) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 $production_cond_for_rcv group by a.booking_id,c.po_breakdown_id");
			$minimum_date = "";
			foreach($sql_recv as $row)
			{
				$recv_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];
			}
			unset($sql_recv);
		}

		if(!empty($production_barcode))
		{
			$production_barcode = array_filter($production_barcode);
			$all_production_barcode_nos = implode(",", $production_barcode);
			$all_production_barcode_cond=""; $barCond="";
			if($db_type==2 && count($production_barcode)>999)
			{
				$production_barcode_chunk=array_chunk($production_barcode,999) ;
				foreach($production_barcode_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  d.barcode_no in($chunk_arr_value) or ";
				}

				$all_production_barcode_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				if($all_production_barcode_nos!=""){
					$all_production_barcode_cond=" and d.barcode_no in($all_production_barcode_nos)";
				}
			}
		}

		if(!empty($prod_id_arr)){
			$prod_id_arr = array_filter($prod_id_arr);
			$all_production_po_nos = implode(",", $prod_id_arr);
			$all_production_po_cond=""; $barCond="";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$production_po_chunk=array_chunk($prod_id_arr,999) ;
				foreach($production_po_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.="  c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$all_production_po_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$all_production_po_cond=" and c.po_breakdown_id in($all_production_po_nos)";
			}
		}
		$sql_recv_roll=sql_select("select a.entry_form,a.booking_no, c.po_breakdown_id as po_id, sum(d.qnty) as knitting_qnty, count(d.barcode_no) as roll_no, d.barcode_no
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, pro_roll_details d
			where a.item_category=13 and a.entry_form in(58) and a.receive_basis =10 and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(58) and b.id=d.dtls_id and d.entry_form in(58) and d.is_sales= 1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id!=0 and d.status_active=1 and d.is_deleted=0 $all_production_barcode_cond $all_production_po_cond
			group by a.booking_no,c.po_breakdown_id,d.barcode_no,a.entry_form");

		foreach($sql_recv_roll as $row)
		{
			$production_booking = $production_barcode_ref[$row[csf('barcode_no')]]["booking_no"];
			$recv_array[$production_booking][$row[csf('po_id')]]['rec_qty']+=$row[csf('knitting_qnty')];
			$recv_array[$production_booking][$row[csf('po_id')]]['roll_count']+=$row[csf('roll_no')];
		}
		unset($sql_recv_roll);


		$production_all_ref_barcode_arr = array_filter($production_all_ref_barcode_arr);
		if(!empty($production_all_ref_barcode_arr))
		{
			$production_all_ref_barcode_nos = implode(",", $production_all_ref_barcode_arr);
			$production_all_ref_barcode_cond=""; $barCond="";
			if($db_type==2 && count($production_all_ref_barcode_arr)>999)
			{
				$production_all_ref_barcode_arr_chunk=array_chunk($production_all_ref_barcode_arr,999) ;
				foreach($production_all_ref_barcode_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.=" a.barcode_no in($chunk_arr_value) or ";
				}

				$production_all_ref_barcode_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$production_all_ref_barcode_cond=" and a.barcode_no in($production_all_ref_barcode_nos)";
			}

			$roll_issue_sql = sql_select(" select count(a.barcode_no) as roll_no, a.barcode_no, a.qnty, a.entry_form, a.booking_no, a.po_breakdown_id, b.remarks from pro_roll_details a,  inv_grey_fabric_issue_dtls b where a.dtls_id = b.id and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.booking_without_order=0 and a.is_returned <> 1 $production_all_ref_barcode_cond group by a.barcode_no, a.qnty, a.entry_form, a.booking_no, a.po_breakdown_id, b.remarks ");
			$knit_issue_arr=array();
			foreach($roll_issue_sql as $row)
			{
				$knit_issue_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]['roll_no'] +=$row[csf('roll_no')];
				$knit_issue_arr[$row[csf("booking_no")]][$row[csf("po_breakdown_id")]]["qnty"] += $row[csf("qnty")];
				$knit_issue_arr[$row[csf("booking_no")]][$row[csf("po_breakdown_id")]]["remarks"] += $row[csf("remarks")];
			}
			unset($roll_issue_sql);
		}

		$sql_data=sql_select("select b.program_no, b.remarks, sum(c.quantity) as quantity,c.po_breakdown_id, sum(b.no_of_roll) as roll_no
			from  inv_issue_master a,inv_grey_fabric_issue_dtls b, order_wise_pro_details c
			where a.id=b.mst_id and b.id = c.dtls_id
			and c.trans_type = 2 and a.item_category=13 and a.entry_form in (16) and c.entry_form in (16)
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and c.status_active = 1 and c.is_deleted = 0 and b.program_no <> 0 and b.program_no is not null $all_rcv_prog_cond2 group by b.program_no, b.remarks, c.po_breakdown_id");


		foreach($sql_data as $row)
		{
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('quantity')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['remarks']=$row[csf('remarks')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['roll_no'] +=$row[csf('roll_no')];
		}
		unset($sql_data);

		$trans_data_array=sql_select("select a.from_order_id, a.to_order_id, b.from_program, b.to_program ,d.barcode_no, c.trans_type,d.qnty,b.id as dtls_id, c.quantity, b.no_of_roll, c.entry_form from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form=133 and d.is_sales=1 where a.id=b.mst_id and c.dtls_id=b.id and c.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and c.entry_form in (133,362) and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4 and b.from_program>0 and b.to_program>0 and b.status_active=1 and b.is_deleted=0 $transfer_all_prog_cond");

		$transfer_qty_arr=array();
		$chkDtlsIdArr=array();
		$rt = 1;
		foreach($trans_data_array as $row_b)
		{
			$chkDtlsIdArr[$row_b[csf("dtls_id")]] = $row_b[csf("dtls_id")];
			if($row_b[csf("entry_form")] == "133")
			{
				if($row_b[csf("trans_type")] == "6")
				{
					$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transfer_out']+=$row_b[csf('qnty')];
					$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transOut_roll_no']+=$rt;
				}else{
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transfer_in']+=$row_b[csf('qnty')];
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transIn_roll_no']+=$rt;
				}
			}
			else
			{
				if($row_b[csf("trans_type")] == "6")
				{
					$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transfer_out']+=$row_b[csf('quantity')];
					$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transOut_roll_no']+=$row_b[csf('no_of_roll')];
				}else{
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transfer_in']+=$row_b[csf('quantity')];
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transIn_roll_no']+=$row_b[csf('no_of_roll')];
				}
			}
		}

		$all_trans_prog =implode(",",array_filter(array_unique($trns_row_prog_ref_data)));
		if($all_trans_prog)
		{
			if($all_trans_prog=="") $all_trans_prog=0;
			$progCond = $all_trans_prog_cond = "";
			$all_trans_prog_arr=explode(",",$all_trans_prog);
			if($db_type==2 && count($all_trans_prog_arr)>999)
			{
				$all_trans_prog_chunk=array_chunk($all_trans_prog_arr,999) ;
				foreach($all_trans_prog_chunk as $chunk_prog)
				{
					$chunk_prog_val=implode(",",$chunk_prog);
					$progCond.=" d.id in($chunk_prog_val) or ";
				}
				$all_trans_prog_cond.=" and (".chop($progCond,'or ').")";
			}
			else
			{
				$all_trans_prog_cond=" and d.id in($all_trans_prog)";
			}
			$trans_pro_ref = sql_select("select d.knitting_source, d.knitting_party, d.id as prog_no, d.program_date, d.machine_dia, d.machine_gg, d.width_dia_type, e.fabric_desc, e.program_qnty as program_qnty,e.po_id from ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls e where c.id=d.mst_id and d.id=e.dtls_id and c.is_sales=1 and e.is_sales=1 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $all_trans_prog_cond");

			foreach($trans_pro_ref as $row)
			{
				$trns_data_arr[$row[csf('prog_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
				$trns_data_arr[$row[csf('prog_no')]]['booking_id']=$row[csf('booking_id')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no']=$row[csf('job_no')];
				$trns_data_arr[$row[csf('prog_no')]]['knitting_source']=$row[csf('knitting_source')];
				$trns_data_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
				$trns_data_arr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
				$trns_data_arr[$row[csf('prog_no')]]['mc_dia_gg']=$row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
				$trns_data_arr[$row[csf('prog_no')]]['width_dia_type']=$row[csf('width_dia_type')];
				$trns_data_arr[$row[csf('prog_no')]]['fabric_desc']=$row[csf('fabric_desc')];
				$trns_data_arr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				$trns_data_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
				$trns_data_arr[$row[csf('prog_no')]]['style']=$row[csf('style_ref_no')];
				$trns_data_arr[$row[csf('prog_no')]]['group']=$row[csf('within_group')];
			}
			unset($trans_pro_ref);
		}

		$yarn_lot_data=sql_select("select d.booking_id, a.yarn_lot, a.color_range_id, a.color_id, a.width as dia_width, a.stitch_length, a.gsm, a.yarn_count, b.po_breakdown_id, a.yarn_prod_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b, inv_receive_master d where d.id=a.mst_id and a.id=b.dtls_id and b.entry_form=2 and d.entry_form=2 and d.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.is_sales=1 $all_rcv_prog_cond");

		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot_arr[$rows[csf('booking_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['roll'] .=$rows[csf('no_of_roll')];
			$yarn_lot_arr[$rows[csf('booking_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['color_id'] .=$rows[csf('color_id')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['gsm'] .=$rows[csf('gsm')].",";
			$yarn_lot_arr[$rows[csf('booking_id')]]['yarn_prod_id'] .=$rows[csf('yarn_prod_id')].",";
			$yarn_prod_arr[$rows[csf('yarn_prod_id')]] =$rows[csf('yarn_prod_id')];

			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['lot'] .=$rows[csf('yarn_lot')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['ycount'] .=$rows[csf('yarn_count')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['roll'] .=$rows[csf('no_of_roll')];
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_range_id'].=$rows[csf('color_range_id')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['color_id'] .=$rows[csf('color_id')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['dia_width'] .=$rows[csf('dia_width')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'] .=$rows[csf('stitch_length')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['gsm'] .=$rows[csf('gsm')].",";
			$yarn_production_wise_lot_count_data[$rows[csf('booking_id')]][$rows[csf('po_breakdown_id')]]['yarn_prod_id'] .=$rows[csf('yarn_prod_id')].",";
		}

		unset($yarn_lot_data);

		$all_yarn_prod_id_arr = array_filter($yarn_prod_arr);
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
			$brand_yarn_arr = return_library_array("select a.id, b.brand_name from product_details_master a, lib_brand b where a.brand = b.id and b.status_active = 1 and a.status_active=1 $all_yarn_prod_id_cond ","id","brand_name");
		}
	}
	ob_start();
	/*echo "<pre>";
	print_r($transfer_qty_arr);die;*/
	?>
	<fieldset style="width:2490px;">
		<table width="2490" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="80">Knitting Company</th>
				<th width="50">Prog. No</th>
				<th width="60">Prog. Date</th>
				<th width="80">PO Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="100">Fab. Booking No</th>
				<th width="110">Sales Order No</th>
				<th width="200">Fab. Description</th>
				<th width="100">Color Range</th>
				<th width="120">Fab. Color</th>
				<th width="70">MC DXG</th>
				<th width="70">F.Dia</th>
				<th width="70">Dia Type</th>
				<th width="70">S/L</th>
				<th width="70">FGSM</th>
				<th width="60">Y/Count</th>
				<th width="60">Y/Lot</th>
				<th width="100">Y/Brand</th>
				<th width="80">Prog Qty/kg</th>
				<th width="80">Receive Qty(kg)</th>
				<th width="80">Trans In Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Delivery Qty(kg)</th>
				<th width="80">Trans Out Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Stock Qty(kg)</th>
				<th width="80">Roll</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="width:2480px; overflow-y:scroll; max-height:350px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2460" class="rpt_table" id="tbl_list_search">
				<?
				if(count($nameArray)>0 || count($trns_row_data)>0)
				{
					$i=1;
					$row_stock=0;
					foreach($nameArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$prog_no = $row[csf("prog_no")];
						if($row[csf("prog_no")]!=""){
							$po_id = $row[csf("po_id")];

							$kniting_company="";
							if($row[csf("knitting_source")]==1) $kniting_company=$company_arr[$row[csf("knitting_party")]]; else if ($row[csf("knitting_source")]==3) $kniting_company=$supplier_arr[$row[csf("knitting_party")]];

							$ex_color_range_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_range_id'],",")));

							$color_range_name="";
							foreach($ex_color_range_id as $range_id)
							{
								if($range_id>0)
								{
									if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
								} 
							}
							$ex_color_id=array_unique(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['color_id'],",")));

							$color_name="";
							foreach($ex_color_id as $color_id)
							{
								if($color_id>0)
								{
									if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
								}
							}
							$dia_width="";
							$dia_width=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['dia_width'],",")))));

							$stitch_length="";
							$stitch_length=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['stitch_length'],",")))));

							$gsm="";
							$gsm=implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['gsm'],",")))));


							$yarn_prod_ids= array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['yarn_prod_id'],","))));

							$yarn_brand_value='';
							foreach($yarn_prod_ids as $p_val)
							{
								if($p_val)
								{
									if($yarn_brand_value=='') $yarn_brand_value=$brand_yarn_arr[$p_val]; else $yarn_brand_value.=", ".$brand_yarn_arr[$p_val];
								}
							}

							$y_count=chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['ycount'],",");
							$y_count_id=array_unique(explode(',',$y_count));

							$yarn_count_value='';
							foreach($y_count_id as $val)
							{
								if($val>0)
								{
									if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
								}
							}

							$yarn_lot = "";
							$yarn_lot = implode(",",array_unique(array_filter(explode(',',chop($yarn_production_wise_lot_count_data[$prog_no][$po_id]['lot'],",")))));

							$knit_issue_qty=$knit_issue_arr[$prog_no][$po_id]['qnty'];
							$iss_roll_no=$knit_issue_arr[$prog_no][$po_id]['roll'];
							$recv_qnty=$recv_array[$prog_no][$po_id]['rec_qty'];
							$ex_recv_roll=explode(",",$recv_array[$prog_no][$po_id]['roll']);
							$rec_roll_no=$recv_array[$prog_no][$po_id]['roll'];

							$recv_roll_count=$recv_array[$prog_no][$po_id]['roll_count'];
							$transOut_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transOut_roll_no'];
							$transIn_roll_no_count=$transfer_qty_arr[$prog_no][$po_id]['transIn_roll_no'];
							$iss_roll_no_count=$knit_issue_arr[$prog_no][$po_id]['roll_no'];
							$remaining_roll = ($recv_roll_count + $transIn_roll_no_count)- ($transOut_roll_no_count + $iss_roll_no_count);

							$trans_qty_out=$transfer_qty_arr[$prog_no][$po_id]['transfer_out'];
							$trans_qty_in=$transfer_qty_arr[$prog_no][$po_id]['transfer_in'];
							$totalRecv=$recv_qnty+$trans_qty_in;
							$totalIssue=$knit_issue_qty+$trans_qty_out;

							$row_stock=$totalRecv-$totalIssue;

							$remark=$knit_issue_arr[$prog_no][$po_id]['remarks'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="80"><p><? echo $kniting_company; ?></p></td>
								<td width="50"><? echo $row[csf("prog_no")]; ?></td>
								<td width="60"><? echo change_date_format($row[csf("program_date")]); ?></td>
								<td width="80">
									<p>
										<?
										echo ($row[csf("within_group")] == 1)?$buyer_arr[$row[csf("po_buyer")]]:$buyer_arr[$row[csf("buyer_id")]];
										?>
									</p>
								</td>
								<td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="100"><? echo $row[csf("sales_booking_no")]; ?></td>
								<td width="110"><? echo $row[csf("job_no")]; ?></td>
								<td width="200"><p><? echo $row[csf("fabric_desc")]; ?></p></td>
								<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
								<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
								<td width="70"><? echo $row[csf("machine_dia")].'X'.$row[csf("machine_gg")]; ?>&nbsp;</td>
								<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
								<td width="70"><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?>&nbsp;</td>
								<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
								<td width="70"><? echo $gsm; ?>&nbsp;</td>
								<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
								<td width="60"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
								<td width="100"><p><? echo $yarn_brand_value; ?></p>&nbsp;</td>
								<td width="80" align="right"><? echo number_format($row[csf("program_qnty")],2); ?></td>
								<td width="80" align="right" title="<? echo $row[csf('prog_no')]."=".$po_id?>"><a href='#report_details' onClick='openmypage_receive("<? echo $po_id; ?>","<? echo $row[csf('prog_no')]; ?>","<? echo $row[csf('sales_booking_no')]; ?>","receive_grey_popup_wgt_lvl");'><? echo number_format($recv_qnty,2,'.',''); ?></a></td>
								<td width="80" align="right">
									<?
									echo number_format($trans_qty_in,2,'.','');
									?>
								</td>
								<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
								<td width="80" align="right">
									<a href='#report_details' onClick="openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','issue_grey_popup');">
										<? echo number_format($knit_issue_qty,2,'.',''); ?>
									</a>
								</td>
								<td width="80" align="right"><a href="##" onClick=" openmypage_issue('<? echo $po_id; ?>','<? echo $prog_no; ?>','<? echo $sales_booking_no; ?>','trans_out_popup_wgt_lvl');"><? echo number_format($trans_qty_out,2,'.',''); ?></a></td>
								<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
								<td width="80" align="right">
									<?
									if(number_format($row_stock,2,'.','') == "-0.00")
									{
										echo "0.00";
									}
									else{
										echo number_format($row_stock,2,'.','');
									}
									?>
								</td>
								<td width="80" align="center" title="<? echo $title;?>"><p><? echo $remaining_roll; ?></p>&nbsp;</td>
								<td><p><? echo $remark; ?></p>&nbsp;</td>
							</tr>
							<?php
							$tot_program_qnty+=$program_qnty;
							$tot_recv_qnty+=$recv_qnty;
							$tot_trans_qty_in+=$trans_qty_in;
							$tot_totalRecv+=$totalRecv;
							$tot_knit_issue_qty+=$knit_issue_qty;
							$tot_trans_qty_out+=$trans_qty_out;
							$tot_totalIssue+=$totalIssue;
							$tot_row_stock+=$row_stock;
							$tot_roll_no += $remaining_roll;
							$i++;
						}
					}
					foreach($trns_row_data as $prog_no=>$trns_data)
					{
						$ex_trn_data=explode("!!!!",$trns_data);

						$to_order_id=$ex_trn_data[0];
						$trans_qty_in=$ex_trn_data[1];
						$trans_in_roll_tr_count=$ex_trn_data[2];

						$kniting_company=""; $prog_date=""; $booking_no=''; $booking_id=''; $fabric_desc=""; $color_range_name=""; $color_name=""; $mc_dia_gg=""; $dia_width=""; $width_dia_type=""; $stitch_length=""; $gsm=""; $yarn_count_value=""; $lot=""; $prog_qty=0;

						$booking_no = $trans_row_ref_arr[$to_order_id]['sales_booking_no'];
						$booking_id=$trans_row_ref_arr[$to_order_id]['booking_id'];

						$knitting_source=$trns_data_arr[$prog_no]['knitting_source'];
						$knitting_party=$trns_data_arr[$prog_no]['knitting_party'];

						if($knitting_source==1) $kniting_company=$company_arr[$knitting_party]; else if ($knitting_source==3) $kniting_company=$supplier_arr[$knitting_party];

						$prog_date=$trns_data_arr[$prog_no]['program_date'];
						$fabric_desc=$trns_data_arr[$prog_no]['fabric_desc'];

						$ex_color_range_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_range_id']));
						foreach($ex_color_range_id as $range_id)
						{
							if($range_id>0)
							{
								if($color_range_name=='') $color_range_name=$color_range[$range_id]; else $color_range_name.=", ".$color_range[$range_id];
							} 
						}

						$ex_color_id=array_unique(explode(',',$yarn_lot_arr[$prog_no]['color_id']));
						foreach($ex_color_id as $color_id)
						{
							if($color_id>0)
							{
								if($color_name=='') $color_name=$color_arr[$color_id]; else $color_name.=", ".$color_arr[$color_id];
							} 
						}

						$mc_dia_gg=$trns_data_arr[$prog_no]['mc_dia_gg'];
						$dia_width=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['dia_width']))));;
						$width_dia_type=$trns_data_arr[$prog_no]['width_dia_type'];
						$stitch_length="";
						$stitch_length=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['stitch_length']))));

						$gsm="";
						$gsm=implode(",",array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['gsm']))));

						$yarn_prod_ids=array_unique(array_filter(explode(',',$yarn_lot_arr[$prog_no]['yarn_prod_id'])));
						$yarn_brand_value='';
						foreach($yarn_prod_ids as $p_val)
						{
							if($p_val)
							{
								if($yarn_brand_value=='') $yarn_brand_value=$brand_yarn_arr[$p_val]; else $yarn_brand_value.=", ".$brand_yarn_arr[$p_val];
							}
						}

						$y_count=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['ycount'],",")))));
						$y_count_id=array_unique(explode(',',$y_count));

						$yarn_count_value='';
						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_arr[$val]; else $yarn_count_value.=", ".$yarn_count_arr[$val];
							}
						}
						$lot=implode(",",array_unique(array_filter(explode(',',chop($yarn_lot_arr[$prog_no]['lot'],",")))));

						$knit_issue_qty=$knit_issue_arr[$prog_no][$to_order_id]['qnty'];
						$ex_issue_roll=explode(",",$knit_issue_arr[$prog_no][$to_order_id]['roll']);
						$iss_roll_no="";
						foreach($ex_issue_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$iss_roll_arr))
							{
								$iss_roll_no+=$val[1];
								$iss_roll_arr[]=$val[0];
							}
						}

						$withingroup=$trans_row_ref_arr[$to_order_id]['within_group'];
						$buyer_with=''; $style_with="";
						if($withingroup==1)
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['po_buyer'];
						}
						else
						{
							$buyer_with=$trans_row_ref_arr[$to_order_id]['buyer_id'];

						}
						$style_with=$trans_row_ref_arr[$to_order_id]['style_ref_no'];

						$tot_knit_grey_recv=$knit_recv_arr[$prog_no]['qnty']+$knit_recv_arr[$prog_no]['qnty'];
						$recv_qnty=$recv_array[$prog_no][$to_order_id]['rec_qty'];
						$ex_recv_roll=explode(",",$recv_array[$prog_no][$to_order_id]['roll']);
						$rec_roll_no="";
						foreach($ex_recv_roll as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$rec_roll_arr))
							{
								$rec_roll_no+=$val[1];
								$rec_roll_arr[]=$val[0];
							}
						}

						$trans_qty_out=$transfer_qty_arr[$prog_no][$to_order_id]['transfer_out'];
						$trans_qty_in=$trans_qty_in;
						$totalRecv=$recv_qnty+$trans_qty_in;
						$totalIssue=$knit_issue_qty+$trans_qty_out;
						$row_stock=0;
						$row_stock=$totalRecv-$totalIssue;

						$roll_no='';
						$roll_data=explode(",",$yarn_lot_arr[$prog_no]['roll']);
						foreach($roll_data as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$roll_arr))
							{
								$roll_no+=$val[1];
								$roll_arr[]=$val[0];
							}
						}
						$recv_roll_tr_count=$iss_roll_tr_count=$trans_out_roll_tr_count=0;
						$recv_roll_tr_count =$recv_array[$prog_no][$to_order_id]['roll_count'];
						$iss_roll_tr_count = $knit_issue_arr[$prog_no][$to_order_id]['roll_no'];

						$trans_out_roll_tr_count= $transfer_qty_arr[$prog_no][$to_order_id]['transOut_roll_no'];

						$remaining_roll_tran_tr = ($recv_roll_tr_count + $trans_in_roll_tr_count) - ($iss_roll_tr_count + $trans_out_roll_tr_count);

						$remark=$knit_issue_arr[$prog_no][$to_order_id]['remarks'];

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"><p><? echo $kniting_company; ?></p></td>
							<td width="50"><? echo $prog_no.'-[T]'; ?></td>
							<td width="60"><? echo change_date_format($prog_date); ?></td>
							<td width="80"><p><? echo $buyer_arr[$buyer_with]; ?></p></td>
							<td width="100"><p><? echo $style_with; ?></p></td>
							<td width="100"><? echo $trans_row_ref_arr[$to_order_id]['sales_booking_no']; ?></td>
							<td width="110"><? echo $trans_row_ref_arr[$to_order_id]['job_no']; ?></td>
							<td width="200"><p><? echo $fabric_desc; ?></p></td>
							<td width="100"><p><? echo $color_range_name; ?></p>&nbsp;</td>
							<td width="120"><p><? echo $color_name; ?></p>&nbsp;</td>
							<td width="70"><? echo $mc_dia_gg; ?>&nbsp;</td>
							<td width="70"><p><? echo $dia_width; ?></p>&nbsp;</td>
							<td width="70"><? echo $fabric_typee[$width_dia_type]; ?>&nbsp;</td>
							<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="70"><? echo $gsm; ?>&nbsp;</td>
							<td width="60"><? echo $yarn_count_value; ?>&nbsp;</td>
							<td width="60"><p><? echo $lot; ?></p>&nbsp;</td>
							<td width="100"><p><? echo $yarn_brand_value; ?></p>&nbsp;</td>

							<td width="80" align="right"><? echo number_format($prog_qty,2); ?></td>
							<td width="80" align="right"><? echo number_format($recv_qnty,2,'.',''); ?></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','trans_in_popup_wgt_lvl');"><? echo number_format($trans_qty_in,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($totalRecv,2,'.',''); ?></td>
							<td width="80" align="right"><a href='#report_details' onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo $booking_no; ?>','issue_grey_popup');"><? echo number_format($knit_issue_qty,2,'.',''); ?></a></td>
							<td width="80" align="right"><? echo number_format($trans_qty_out,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
							<td width="80" align="right">
								<?
								if(number_format($row_stock,2,'.','') == "-0.00")
								{
									echo "0.00";
								}else{
									echo number_format($row_stock,2,'.','');
								}
								?>

							</td>
							<td width="80" align="center" title="<? ?>"><p><? echo $remaining_roll_tran_tr;?></p>&nbsp;</td>
							<td><p><? echo $remark; ?></p>&nbsp;</td>
						</tr>
						<?
						$tot_program_qnty+=$prog_qty;
						$tot_recv_qnty+=$recv_qnty;
						$tot_trans_qty_in+=$trans_qty_in;
						$tot_totalRecv+=$totalRecv;
						$tot_knit_issue_qty+=$knit_issue_qty;
						$tot_trans_qty_out+=$trans_qty_out;
						$tot_totalIssue+=$totalIssue;
						$tot_row_stock+=$row_stock;
						$tot_roll_no += $remaining_roll_tran_tr;
						$i++;

					}
				}
				else
				{
					echo "3**".'Data Not Found'; die;
				}
				unset($nameArray);
				?>
			</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" id="">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="200">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="80" align="right" id="value_program_qnty"><? echo number_format($tot_program_qnty,2);?></th>
				<th width="80" align="right" id="value_recv_qnty"><? echo number_format($tot_recv_qnty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_in"><? echo number_format($tot_trans_qty_in,2);?></th>
				<th width="80" align="right" id="value_totalRecv"><? echo number_format($tot_totalRecv,2);?></th>
				<th width="80" align="right" id="value_knit_issue_qty"><? echo number_format($tot_knit_issue_qty,2);?></th>
				<th width="80" align="right" id="value_trans_qty_out"><? echo number_format($tot_trans_qty_out,2);?></th>
				<th width="80" align="right" id="value_totalIssue"><? echo number_format($tot_totalIssue,2);?></th>
				<th width="80" align="right" id="value_row_stock"><? echo number_format($tot_row_stock,2);?></th>
				<th width="80" align="right" id="value_roll"><? echo $tot_roll_no;?></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
	$html = ob_get_contents();
	ob_clean();

	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
	exit();
}

if($action=="receive_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_balance","value_total_trns",],
				col: [4,5],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
			}
		}
	</script>
	<fieldset style="width:640px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Received Info</b>
				</caption>
				<thead>
					<th width="20">SL</th>
					<th width="110">Receive ID</th>
					<th width="65">Receive Date</th>
					<th width="50">Receive Ch. No</th>
					<th width="80">Receive Qty</th>
					<th width="70">Trans. In Qty</th>
					<th width="50">Roll</th>
					<th width="80">Barcode No</th>
					<th width="50">Rack No</th>
					<th>Shelf</th>
				</thead>
			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$fabricLibraryData = sql_select("select auto_update from variable_settings_production where company_name=$companyID and variable_list in(15) and item_category_id=13 and is_deleted=0 and status_active=1");
						$fabric_store_auto_update=$fabricLibraryData[0][csf("auto_update")];
						$i=1;

						$sql_data=("select b.from_program, b.to_program, b.transfer_qnty as transfer_qnty, a.from_order_id, a.to_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and b.to_program='$prog_no' and a.to_order_id in($po_id) and b.from_program>0 and b.to_program>0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0");

						$data_array=sql_select($sql_data);
						$transfer_qty_arr=array();
						foreach($data_array as $row_b)
						{
							$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['from_qnty']=$row_b[csf('transfer_qnty')];
						}

						$knitting_recv_qnty_array=array(); $prod_id_arr=array();

						if($fabric_store_auto_update==2){
							$sql_prod= "select a.id, a.recv_number, a.receive_date, a.challan_no, d.booking_no, b.order_id, b.rack, b.self, sum(d.qnty) as knitting_qnty, d.roll_no, d.barcode_no, max(b.trans_id) as trans_id
							from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details d
							where a.id=b.mst_id  and b.id=d.dtls_id and a.item_category=13 and a.entry_form in (2,58)  and d.entry_form in (2,58) and a.company_id='$companyID' and d.booking_no='$prog_no' and d.po_breakdown_id='$po_id' and a.receive_basis in (2,10) and  b.trans_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
							group by a.id, a.recv_number, a.receive_date, a.challan_no, d.booking_no, b.order_id, b.rack, b.self, d.roll_no, d.barcode_no";
						}else{
							$sql_prod= "select a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, b.order_id, b.rack, b.self, sum(c.quantity) as knitting_qnty, d.roll_no, d.barcode_no, max(b.trans_id) as trans_id
							from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
							left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in (2,22,58) and d.is_sales= 1
							where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.entry_form in (2,22,58) and a.company_id='$companyID' and a.booking_no='$prog_no' and c.po_breakdown_id='$po_id'
							and a.receive_basis in (2,10)
							and b.trans_id>0
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
							group by a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, b.order_id, b.rack, b.self, d.roll_no, d.barcode_no";
						}
					//echo $sql_prod;
						$data_prod=sql_select($sql_prod);

						foreach ($data_prod as $row) 
						{
							$all_rack_shelf_id[$row[csf("rack")]] = $row[csf("rack")];
							$all_rack_shelf_id[$row[csf("self")]] = $row[csf("self")];
						}
						$all_rack_shelf_id = array_filter($all_rack_shelf_id);

						if($all_rack_shelf_id)
						{
							$all_rack_shelf_id = implode(",", $all_rack_shelf_id);
							$rack_shelf_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in ($all_rack_shelf_id)","floor_room_rack_id","floor_room_rack_name");
						}

						$recv_id='';$i=1;
						foreach( $data_prod as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$trans_in=$transfer_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]]['from_qnty'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="20"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
								<td width="65"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="50"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($row[csf('knitting_qnty')],2); ?></td>
								<td width="70" align="right"><? echo number_format($trans_in,2); ?></td>
								<td width="50" align="center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
								<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $rack_shelf_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
								<td><p><? echo $rack_shelf_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('knitting_qnty')];
							$tot_trans_in_up+=$trans_in;
							$i++;
						}

						$total_balance=$tot_qty_gery+$tot_qty;
						$total_in_balance=$tot_trans_in_up+$tot_trans_in_down;
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="20">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="50">Total:</th>
					<th width="80" id="value_total_balance"><? echo number_format($total_balance,2); ?>&nbsp;</th>
					<th width="70" id="value_total_trns"><? echo number_format($total_in_balance,2); ?></th>
					<th width="50">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

if($action=="receive_grey_popup_wgt_lvl")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_balance","value_total_trns",],
				col: [4,5],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
			}
		}
	</script>
	<fieldset style="width:640px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Received Info</b>
				</caption>
				<thead>
					<th width="40">SL</th>
					<th width="110">Receive ID</th>
					<th width="65">Receive Date</th>
					<th width="70">Receive Ch. No</th>
					<th width="80">Receive Qty</th>
					<th width="70">Roll</th>
					<th width="70">Rack No</th>
					<th>Shelf</th>
				</thead>
			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$sql_prod="SELECT a.id, a.recv_number, a.receive_date, a.challan_no, b.rack, b.self, sum(c.quantity) as knitting_qnty, sum(b.no_of_roll) as roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.entry_form in (2) and a.company_id='$companyID' and a.booking_no='$prog_no' and c.po_breakdown_id='$po_id' and c.is_sales=1 and a.receive_basis in (2) and b.trans_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, b.order_id, b.rack, b.self union all select a.id, a.recv_number, a.receive_date, a.challan_no, b.rack, b.self, sum(c.quantity) as knitting_qnty, sum(b.no_of_roll) as roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d where a.id=b.mst_id and b.id=c.dtls_id and a.booking_id=d.id and d.entry_form=2 and d.receive_basis=2 and a.item_category=13 and a.entry_form in (22) and a.company_id='$companyID' and d.booking_no='$prog_no' and c.po_breakdown_id='$po_id' and a.receive_basis in (9) and b.trans_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, b.order_id, b.rack, b.self";
						//echo $sql_prod;
						$data_prod=sql_select($sql_prod);

						foreach ($data_prod as $row) 
						{
							$all_rack_shelf_id[$row[csf("rack")]] = $row[csf("rack")];
							$all_rack_shelf_id[$row[csf("self")]] = $row[csf("self")];
						}
						$all_rack_shelf_id = array_filter($all_rack_shelf_id);

						if($all_rack_shelf_id)
						{
							$all_rack_shelf_id = implode(",", $all_rack_shelf_id);
							$rack_shelf_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in ($all_rack_shelf_id)","floor_room_rack_id","floor_room_rack_name");
						}

						$recv_id='';$i=1;
						foreach( $data_prod as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
								<td width="65"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($row[csf('knitting_qnty')],2); ?></td>
								<td width="70" align="center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
								<td width="70"><p><? echo $rack_shelf_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
								<td><p><? echo $rack_shelf_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('knitting_qnty')];
							$tot_trans_in_up+=$trans_in;
							$i++;
						}

						$total_balance=$tot_qty_gery+$tot_qty;
						$total_in_balance=$tot_trans_in_up+$tot_trans_in_down;
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="70">Total:</th>
					<th width="80" id="value_total_balance"><? echo number_format($total_balance,2); ?>&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}


if($action=="issue_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_tot_qty","value_tot_trans_qty"],
				col: [4,5],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
			}
		}

	</script>
	<fieldset style="width:620px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Issue Info</b>
				</caption>
				<thead>
					<th width="20">SL</th>
					<th width="110">Issue ID</th>
					<th width="65">Issue Date</th>
					<th width="50">Issue Ch. No</th>
					<th width="80">Issue Qty</th>
					<th width="70">Trans. Out Qty</th>
					<th width="50">Roll</th>
					<th width="80">Barcode No</th>
					<th width="50">Rack No</th>
					<th>Shelf</th>
				</thead>
			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$variable_data=sql_select("select variable_list, fabric_roll_level, auto_update from variable_settings_production where company_name ='$company_name' and variable_list in(3,15) and item_category_id=13 and is_deleted=0 and status_active=1");
						foreach($variable_data as $row)
						{
							if($row[csf('variable_list')]==3)
							{
								$roll_maintained=$row[csf('fabric_roll_level')];
							}
							else
							{
								$fabric_store_auto_update=$row[csf('auto_update')];
							}
						}

						$i=1;
						$sql_data=("select b.from_program,b.to_program, b.transfer_qnty as transfer_qnty,a.to_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and  b.from_program='$prog_no' and b.to_program='$prog_no' and a.to_order_id in($po_id) and b.from_program>0 and b.to_program>0 ");
						$data_array=sql_select($sql_data);

						$transfer_qty_arr=array();
						foreach($data_array as $row_b)
						{
							$transfer_qty_arr[$row_b[csf('to_program')]]['to_order_id']=$row_b[csf('transfer_qnty')];
						}

						$qnty_field = ($roll_maintained==1)?" sum(d.qnty)":"sum(c.quantity)";
						$mrr_sql=( "select a.issue_number, a.issue_date, a.challan_no, b.rack, b.self, c.po_breakdown_id, d.barcode_no, d.roll_no, b.program_no as prog_no, $qnty_field as issue_qty
							from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c
							left join pro_roll_details d on c.dtls_id=d.dtls_id and d.entry_form in(16,61) and d.is_sales=1
							where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and c.po_breakdown_id='$po_id' and ( b.program_no='$prog_no' or d.booking_no = '$prog_no') and a.entry_form in(16,61) and c.entry_form in(16,61) and c.trans_type=2 and a.issue_purpose=11 and b.status_active=1 and b.is_deleted=0
							group by a.issue_number, a.issue_date, a.challan_no, b.rack, b.self, c.po_breakdown_id, d.barcode_no, d.roll_no, b.program_no");
						//and b.program_no='$prog_no'

						$dtlsArray=sql_select($mrr_sql);

						foreach ($dtlsArray as $row) 
						{
							$all_rack_shelf_id[$row[csf("rack")]] = $row[csf("rack")];
							$all_rack_shelf_id[$row[csf("self")]] = $row[csf("self")];
						}
						$all_rack_shelf_id = array_filter($all_rack_shelf_id);

						if($all_rack_shelf_id)
						{
							$all_rack_shelf_id = implode(",", $all_rack_shelf_id);
							$rack_shelf_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in ($all_rack_shelf_id)","floor_room_rack_id","floor_room_rack_name");
						}

						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$trans_out=$transfer_qty_arr[$row[csf('prog_no')]][$row[csf('po_breakdown_id')]]['to_qnty'];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="20"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
								<td width="65"><p><? echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
								<td width="50"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($row[csf('issue_qty')],2); ?></td>
								<td width="70" align="right"><? echo number_format($trans_out,2); ?></td>
								<td width="50"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $rack_shelf_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
								<td><p><? echo $rack_shelf_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('issue_qty')];
							$tot_trans_qty+=$trans_out;
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="20">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="50">Total:</th>
					<th width="80" id="value_tot_qty"><? echo number_format($tot_qty,2); ?></th>
					<th width="70" id="value_tot_trans_qty"><? echo number_format($tot_trans_qty,2); ?></th>
					<th width="50">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

if($action=="stock_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$('#table_body tbody tr:first').show();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_recv","value_total_iss","value_total_stock"],
				col: [3,4,5],
				operation: ["sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML"]
			}
		}
	</script>
	<fieldset style="width:560px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Stock Info</b>
				</caption>
				<thead>
					<th width="40">Sl</th>
					<th width="100">Rack No</th>
					<th width="100">Shelf No</th>
					<th width="100">Receive Qty</th>
					<th width="100">Issue Qty</th>
					<th>Stock Qty</th>
				</thead>
			</table>
			<div style="width:560px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$sql_data=("select b.from_program,b.to_program,b.rack, b.shelf, b.transfer_qnty as transfer_qnty,a.from_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and  b.from_program='$prog_no'  and a.from_order_id in($po_id) and b.from_program>0 and b.to_program>0 ");
						$data_array=sql_select($sql_data);
						$transfer_qty_arr=array();
						foreach($data_array as $row_b)
						{
							$transfer_qty_arr[$row_b[csf('rack')]][$row_b[csf('shelf')]]['from_qnty']+=$row_b[csf('transfer_qnty')];
					} //var_dump($transfer_qty_arr);
					$sql_result=("select b.from_program,b.to_program,b.rack, b.shelf, b.transfer_qnty as transfer_qnty,a.to_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and b.to_program='$prog_no' and a.to_order_id in($po_id) and b.from_program>0 and b.to_program>0 ");
					$data_array_issue=sql_select($sql_result);

					$transfer_qty_issue_arr=array();
					foreach($data_array_issue as $row_b)
					{
						$transfer_qty_issue_arr[$row_b[csf('rack')]][$row_b[csf('shelf')]]['from_qnty']+=$row_b[csf('transfer_qnty')];
						//$transfer_qty_arr[$row_b[csf('rack')]]['shelf']+=$row_b[csf('transfer_qnty')];
					}

					$i=1;
					$iss_arr=array(); $recv_arr=array(); $rack_shelf_arr=array(); $recv_arr_trans=array();
					$iss_data=sql_select("select b.rack, b.self, sum(b.issue_qnty) as issue_qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.item_category=13 and b.program_no='$prog_no' and a.entry_form=16 and a.issue_basis=3 and b.status_active=1 and b.is_deleted=0 group by b.rack,b.self");
					foreach($iss_data as $row)
					{
						$iss_arr[$row[csf('rack')]][$row[csf('self')]]=$row[csf('issue_qnty')];
						$rack_shelf_arr[$row[csf('rack')]][$row[csf('self')]]=$row[csf('issue_qnty')];
					}

					$recv_id='';
					$sql_prod="select a.id, a.booking_id,b.order_id, b.rack, b.self, b.grey_receive_qnty as recv_qnty, max(trans_id) as trans_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.company_id='$companyID' and a.booking_id='$prog_no' and b.status_active=1 and b.is_deleted=0 group by a.id,a.booking_id,b.order_id,b.rack,b.self,b.grey_receive_qnty
					union all
					select d.id, f.booking_id, b.order_id, b.rack, b.self, b.grey_receive_qnty as recv_qnty, max(trans_id) as trans_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_grey_prod_delivery_mst d, pro_grey_prod_delivery_dtls e, inv_receive_master f where a.id=b.mst_id and d.id=a.booking_id and d.id=e.mst_id and e.grey_sys_id=f.id and f.entry_form=2 and f.receive_basis=2 and a.entry_form=58 and a.company_id='$companyID' and f.booking_id='$prog_no' and a.receive_basis=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by d.id, f.booking_id, b.order_id, b.rack, b.self,b.grey_receive_qnty";
					//echo $sql_prod;
					/*" select f.booking_id, c.po_breakdown_id as po_id, sum(c.quantity) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_grey_prod_delivery_mst d, pro_grey_prod_delivery_dtls e, inv_receive_master f where a.id=b.mst_id and b.id=c.dtls_id and d.id=a.booking_id and d.id=e.mst_id and e.grey_sys_id=f.id and f.entry_form=2 and f.receive_basis=2 and a.entry_form=58 and c.entry_form=58 and a.receive_basis=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by f.booking_id, c.po_breakdown_id";*/
					$data_prod=sql_select($sql_prod);
					foreach($data_prod as $row)
					{
						if($row[csf('trans_id')]>0)
						{
							$recv_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];
							$rack_shelf_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];
						}
						else
						{
							if($recv_id=='') $recv_id= $row[csf('id')]; else $recv_id.=','.$row[csf('id')];
						}

					}
					//echo $recv_id;
					if($recv_id!="")
					{
						$sql_recv="select b.rack, b.self, sum(b.grey_receive_qnty) as recv_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.company_id='$companyID' and a.booking_id in($recv_id) and a.entry_form in (22,58) and a.receive_basis in (9,10) and b.status_active=1 and b.is_deleted=0 group by b.rack, b.self";

						$data_recv=sql_select($sql_recv);
						foreach($data_recv as $row)
						{
							$recv_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];
							$rack_shelf_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];//just print for rack shelf
						}
					}

					$i=1;
					foreach($rack_shelf_arr as $rack=>$data)
					{
						foreach($data as $shelf=>$qty)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$recv_qty=$recv_arr[$rack][$shelf];
							$iss_qty=$iss_arr[$rack][$shelf];
							$trans_from_recv=$transfer_qty_arr[$rack][$shelf]['from_qnty'];
							$tran_to_issue=$transfer_qty_issue_arr[$rack][$shelf]['from_qnty'];
							$tot_recv=$recv_qty-$trans_from_recv;
							$tot_issue=$iss_qty-$tran_to_issue;
							$stock_qty=$tot_recv-$tot_issue;
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $rack; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $shelf; ?>&nbsp;</p></td>
								<td width="100" align="right"><? echo number_format($tot_recv,2); ?></td>
								<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
								<td align="right"><? echo number_format($stock_qty,2); ?></td>
							</tr>
							<?
							$i++;
							$total_recv_qty=$tot_recv;
							$total_iss_qty=$tot_issue;
							$total_stock_qty=$stock_qty;
						}
					}

					?>
				</tbody>
			</table>
		</div>
		<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="report_table_footer">
			<tfoot>
				<th width="40">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th align="right" width="100">Total:</th>
				<th align="right" width="100" id="value_total_recv"><? echo number_format($total_recv_qty,2); ?></th>
				<th align="right" width="100" id="value_total_iss"><? echo number_format($total_iss_qty,2); ?></th>
				<th align="right" id="value_total_stock"><? echo number_format($total_stock_qty,2); ?></th>
			</tfoot>
		</table>
	</div>
</fieldset>
<script>setFilterGrid('table_body',-1,tableFilters);</script>
<?
exit();
}

if($action == "trans_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}

	</script>
	<fieldset style="width:620px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Transfer Info</b>
				</caption>
				<thead>
					<th width="30">SL</th>
					<th width="110">Transfer ID</th>
					<th width="80">Transfer Date</th>
					<th width="80">Transfer In Qty</th>
					<th width="50">Roll No</th>
					<th width="80">Barcode No</th>
					<th width="50">Rack No</th>
					<th width="">Shelf</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						$mrr_sql=( "select a.transfer_system_id,a.transfer_date, b.to_rack as rack, b.to_shelf as self, c.barcode_no, c.roll_no, c.qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c ,order_wise_pro_details d where a.id=b.mst_id and b.id=c.dtls_id and d.dtls_id = b.id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 and b.to_program = '$prog_no' and a.to_order_id = '$po_id' and d.trans_type = 5 group by a.transfer_system_id, a.transfer_date,b.to_rack, b.to_shelf , c.barcode_no, c.roll_no, c.qnty ");

					$dtlsArray=sql_select($mrr_sql);

					foreach ($dtlsArray as $row) 
					{
						$all_rack_shelf_id[$row[csf("rack")]] = $row[csf("rack")];
						$all_rack_shelf_id[$row[csf("self")]] = $row[csf("self")];
					}
					$all_rack_shelf_id = array_filter($all_rack_shelf_id);

					if($all_rack_shelf_id)
					{
						$all_rack_shelf_id = implode(",", $all_rack_shelf_id);
						$rack_shelf_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in ($all_rack_shelf_id)","floor_room_rack_id","floor_room_rack_name");
					}

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
							<td width="80" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
							<td width="50" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $rack_shelf_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $rack_shelf_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('qnty')];
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
		<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="report_table_footer">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="80">Total:</th>
				<th width="80"><? echo number_format($tot_trans_qty,2); ?></th>
				<th colspan="4" width="365">&nbsp;</th>
			</tfoot>
		</table>
	</div>
</fieldset>
<script>setFilterGrid('table_body',-1);</script>
<?
exit();
}

if($action == "trans_in_popup_wgt_lvl")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}

	</script>
	<fieldset style="width:620px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Transfer Info</b>
				</caption>
				<thead>
					<th width="30">SL</th>
					<th width="110">Transfer ID</th>
					<th width="100">Transfer Date</th>
					<th width="100">Transfer In Qty</th>
					<th width="70">Roll No</th>
					<th width="70">Rack No</th>
					<th width="">Shelf</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						$mrr_sql="select a.transfer_system_id,a.transfer_date, b.to_rack as rack, b.to_shelf as self, c.barcode_no, sum(case when a.entry_form =133 then c.qnty when a.entry_form = 362 then d.quantity else 0 end) as qnty, sum(case when a.entry_form =133 then  c.roll_no when a.entry_form = 362 then b.roll else 0 end) as roll_no from inv_item_transfer_mst a, inv_item_transfer_dtls b left join  pro_roll_details c on b.id = c.dtls_id and c.entry_form=133 and c.status_active=1 and c.is_deleted=0,order_wise_pro_details d where a.id=b.mst_id  and d.dtls_id = b.id and a.entry_form in(133,362) and b.to_program = '$prog_no' and a.to_order_id = '$po_id' and d.trans_type = 5 group by a.transfer_system_id, a.transfer_date,b.to_rack, b.to_shelf , c.barcode_no,  a.entry_form";


					$dtlsArray=sql_select($mrr_sql);

					foreach ($dtlsArray as $row) 
					{
						$all_rack_shelf_id[$row[csf("rack")]] = $row[csf("rack")];
						$all_rack_shelf_id[$row[csf("self")]] = $row[csf("self")];
					}
					$all_rack_shelf_id = array_filter($all_rack_shelf_id);

					if($all_rack_shelf_id)
					{
						$all_rack_shelf_id = implode(",", $all_rack_shelf_id);
						$rack_shelf_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in ($all_rack_shelf_id)","floor_room_rack_id","floor_room_rack_name");
					}

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
							<td width="100" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
							<td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $rack_shelf_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $rack_shelf_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('qnty')];
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
		<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="report_table_footer">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="100">Total:</th>
				<th width="100"><? echo number_format($tot_trans_qty,2); ?></th>
				<th colspan="3" width="290">&nbsp;</th>
			</tfoot>
		</table>
	</div>
</fieldset>
<script>setFilterGrid('table_body',-1);</script>
<?
exit();
}

if($action == "trans_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}


	</script>
	<fieldset style="width:620px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Transfer Info</b>
				</caption>
				<thead>
					<th width="30">SL</th>
					<th width="110">Transfer ID</th>
					<th width="80">Transfer Date</th>
					<th width="80">Transfer Out Qty</th>
					<th width="50">Roll No</th>
					<th width="80">Barcode No</th>
					<th width="50">Rack No</th>
					<th width="">Shelf</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						$mrr_sql= "select a.transfer_system_id,a.transfer_date, a.from_order_id, a.to_order_id, b.from_program,b.rack,b.shelf,b.roll, c.quantity, d.barcode_no , d.qnty
						from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c ,pro_roll_details d where a.id=b.mst_id and c.dtls_id=b.id and D.DTLS_ID = b.id and c.trans_type in(6) and a.item_category=13 and c.entry_form=133 and a.transfer_criteria=4 and b.from_program>0 and b.to_program>0 and  a.from_order_id='$po_id' and b.from_program = '$prog_no' and d.po_breakdown_id = a.to_order_id
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0

						order by a.id , b.id ";

						$dtlsArray=sql_select($mrr_sql);
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
								<td width="50" align="center"><p><? echo $row[csf('roll')]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('qnty')];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="80">Total:</th>
					<th width="80"><? echo number_format($tot_trans_qty,2); ?></th>
					<th colspan="4" width="365">&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}

if($action == "trans_out_popup_wgt_lvl")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}


	</script>
	<fieldset style="width:620px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Transfer Info</b>
				</caption>
				<thead>
					<th width="30">SL</th>
					<th width="110">Transfer ID</th>
					<th width="100">Transfer Date</th>
					<th width="100">Transfer Out Qty</th>
					<th width="50">Roll No</th>
					<th width="60">Rack No</th>
					<th width="">Shelf</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;
						$mrr_sql= "select a.transfer_system_id,a.transfer_date, a.from_order_id, b.from_program, b.rack, b.shelf, sum(b.roll) as roll, sum(c.quantity) as qnty from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c  where a.id=b.mst_id and c.dtls_id=b.id  and c.trans_type in(6) and a.item_category=13 and c.entry_form in (362) and a.transfer_criteria=4 and b.from_program>0 and b.to_program>0 and  a.from_order_id='$po_id' and b.from_program = '$prog_no' and c.po_breakdown_id = a.from_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.transfer_system_id,a.transfer_date, a.from_order_id, b.from_program, b.rack, b.shelf";

						$dtlsArray=sql_select($mrr_sql);
						foreach ($dtlsArray as $row) 
						{
							$all_rack_shelf_id[$row[csf("rack")]] = $row[csf("rack")];
							$all_rack_shelf_id[$row[csf("shelf")]] = $row[csf("shelf")];
						}
						$all_rack_shelf_id = array_filter($all_rack_shelf_id);

						if($all_rack_shelf_id)
						{
							$all_rack_shelf_id = implode(",", $all_rack_shelf_id);
							$rack_shelf_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in ($all_rack_shelf_id)","floor_room_rack_id","floor_room_rack_name");
						}

						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
								<td width="100" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
								<td width="50" align="center"><p><? echo $row[csf('roll')]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $rack_shelf_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $rack_shelf_arr[$row[csf('shelf')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('qnty')];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="100">Total:</th>
					<th width="100"><? echo number_format($tot_trans_qty,2); ?></th>
					<th colspan="3" width="200">&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}
?>
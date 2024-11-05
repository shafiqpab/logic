<?

session_start();
ini_set('memory_limit','8129M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

include ("../../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;


if ($action=="load_drop_down_po_company")
{
	if($data ==1){
		echo create_drop_down( "cbo_pocompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Po Company-", $selected, "load_drop_down( 'requires/batch_wise_finish_fabric_stock_report_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	}else{
		echo create_drop_down( "cbo_pocompany_id", 130, $blank_array,"", 1, "-Po Company-", $selected, "" );
	}

}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_within_no")
{
	$dataArr = explode("_",$data);
	if($dataArr[0]==2)
	{
		echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$dataArr[1]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");

	}else {
		echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" );
	}
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	if ($data[1] == 2)
		$disable = 1;
	else
		$disable = 0;
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and  b.category_type=2 order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",$disable );
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
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value, 'create_booking_search_list_view', 'search_div', 'batch_wise_finish_fabric_stock_report_urmi_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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

	//echo $sql;
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'batch_wise_finish_fabric_stock_report_urmi_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC";
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

if($action=="report_generate_bk_14_08_2022")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$store_arr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_id and  b.category_type=2 order by a.store_name",'id','store_name');


	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$cbo_store_wise= str_replace("'","",$cbo_store_wise);
	$cbo_store_name= str_replace("'","",$cbo_store_name);
	$cbo_get_upto= str_replace("'","",$cbo_get_upto);
	$txt_days= str_replace("'","",$txt_days);
	$cbo_get_upto_qnty= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty= str_replace("'","",$txt_qnty);
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);

	if($within_group==1)
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.po_buyer=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.buyer_id=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond="and a.company_id='$pocompany_id'";
	$date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
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
		if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}


	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and d.job_no_prefix_num='$order_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and d.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}

	} else {
		$booking_no_cond="";
	}

	if($cbo_store_wise ==1)
	{
		$selectRcvStore = " a.store_id,";
		$selectTransStore = " b.to_store as store_id,";
		$selectTransOutStore = " b.from_store as store_id,";
		$groupByRcvStore = " a.store_id,";
		$groupByTransStore = " b.to_store,";
		$groupByTransOutStore = " b.from_store,";

		if($cbo_store_name)
		{
			$rcvStoreCond = " and e.store_id = $cbo_store_name";
			$TransStoreCond = " and b.to_store = $cbo_store_name";
		}
	}

	if($within_group>0)
	{
		$withinGroupCond = "and d.within_group=$within_group";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$to_trans_date_cond = " and e.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond2 = " and a.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond3 = " and c.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond4 = " and f.transaction_date <= '".$txt_date_to."'";
	}

	$sql = "SELECT b.id,d.within_group, f.id as batch_id, f.batch_no,f.extention_no,1 as type, sum(g.batch_qnty) as batch_qty, min(a.receive_date) as mrr_date, a.company_id, c.po_breakdown_id,  b.body_part_id, b.fabric_description_id, $selectRcvStore b.uom, h.color as color_id,b.dia_width_type, b.width, b.gsm, (c.quantity) as quantity , sum(e.cons_amount) as amount,0 as is_transfered,0 as from_order_id, a.receive_basis, (e.order_amount) as order_amount, e.transaction_date
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master h
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.trans_id=e.id and e.prod_id=h.id and b.batch_id=f.id and f.id=g.mst_id and CAST(g.po_id AS VARCHAR2(4000)) =b.order_id and g.body_part_id = b.body_part_id and d.sales_booking_no=f.booking_no and f.status_active=1 and g.status_active=1 and a.entry_form=225 and c.entry_form=225 and b.is_sales=1 and c.is_sales=1 and a.company_id = $company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $rcvStoreCond $year_search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in (10,14) $to_trans_date_cond
	group by  b.id,d.within_group,f.id, f.batch_no,f.extention_no,a.company_id,c.po_breakdown_id, b.body_part_id,b.fabric_description_id, $groupByRcvStore b.uom, e.order_amount, h.color, b.dia_width_type, b.width, a.item_category, b.gsm,c.quantity, a.receive_basis, e.transaction_date
	union all
	select  b.id,d.within_group,f.id as batch_id, f.batch_no,f.extention_no,2 as type, sum(g.batch_qnty) as batch_qty, min(a.transfer_date) as mrr_date, a.company_id,a.to_order_id as po_breakdown_id, g.body_part_id,b.feb_description_id as fabric_description_id, $selectTransStore b.uom, c.color as color_id,b.dia_width_type, b.dia_width as width, b.gsm, (b.transfer_qnty) as quantity , sum(e.cons_amount) as amount,1 as is_transfered,a.from_order_id , 0 as receive_basis,  (e.order_amount) as order_amount, e.transaction_date
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, fabric_sales_order_mst d , inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master c 
	where a.id=b.mst_id and a.to_order_id=d.id and b.to_trans_id=e.id and e.prod_id=c.id and b.to_batch_id=f.id and f.id=g.mst_id AND g.body_part_id = b.body_part_id and f.status_active=1 and g.status_active=1  and a.company_id=$company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $TransStoreCond $year_search_cond and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_trans_date_cond
	group by  b.id,d.within_group,f.id,f.batch_no,f.extention_no,a.company_id,a.to_order_id, e.order_amount,b.from_prod_id, g.body_part_id, b.feb_description_id, $groupByTransStore b.uom, c.color, b.dia_width_type, b.dia_width, b.gsm,b.transfer_qnty,a.from_order_id, e.transaction_date
	order by uom,po_breakdown_id";
	//and d.id=f.sales_order_id 
	//echo $sql;//die;
	$nameArray=sql_select($sql);
	$ref_key="";$open=0;
	foreach($nameArray as $row)
	{
		$all_batch_id[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$row[csf("prod_id")]=$row[csf("batch_id")];
		if($row[csf("quantity")] > 0)
		{
			$fso_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			if($cbo_store_wise ==1)
			{
				$sub_total_col_span = 22;
				$ref_key =$row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("store_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}else{
				$sub_total_col_span = 21;
				$ref_key = $row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{

				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($row[csf("type")] == 1)
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
					}
					else
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_in"] += $row[csf("order_amount")];
						}else{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["rcv_in"] += $row[csf("order_amount")];
						}

						if($row[csf("type")] == 1)
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["opening"] += $row[csf("quantity")];
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += 0;//$row[csf("order_amount")];
						}else{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] = 0;
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] = 0;
						}
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
			}
			else
			{
				if($row[csf("type")] == 1)
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
				}else{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
				}

				if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
				}
				else
				{
					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
				}
			}
		}
	}

	$all_batch_ids= implode(",", $all_batch_id);
	$batch_conds=" and mst_id in($all_batch_ids)";
	if($db_type==2 && count($all_batch_id)>999)
	{
		$batch_conds="";
		$chnk=array_chunk($all_batch_id, 999);
		foreach($chnk as $val)
		{
			$ids=implode(",", $val);
			if(!$batch_conds)$batch_conds.=" and ( mst_id in($ids) ";
			else $batch_conds.=" or  mst_id in($ids) ";
		}
		$batch_conds.=")";

	}
	$batch_sql="SELECT   mst_id,  item_description,  batch_qnty , body_part_id FROM pro_batch_create_dtls Where status_active=1 $batch_conds ";
	foreach(sql_select($batch_sql) as $vals)
	{
		$items=explode(",",$vals[csf("item_description")]);
		$items_st=trim($items[0]);
		$batch_qnty_arr_new[$vals[csf("mst_id")]][$vals[csf("body_part_id")]][$items_st]+=$vals[csf("batch_qnty")];
	}
	/*echo "<pre>";
	print_r($batch_qnty_arr_new);die;*/


	$fso_id_arr = array_filter($fso_id_arr);
	if(!empty($fso_id_arr))
	{
		$fso_ids = implode(",", array_filter($fso_id_arr));
		$fsoCond = $all_fso_cond = "";
		$fsoCond2 = $all_fso_cond2 = "";
		$fsoCond3 = $all_fso_cond3 = "";
		if($db_type==2 && count($fso_id_arr)>999)
		{
			$fso_id_arr_chunk=array_chunk($fso_id_arr,999) ;
			foreach($fso_id_arr_chunk as $chunk_arr)
			{
				$fsoCond.=" a.id in(".implode(",",$chunk_arr).") or ";
				$fsoCond2.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				$fsoCond3.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_fso_cond.=" and (".chop($fsoCond,'or ').")";
			$all_fso_cond2.=" and (".chop($fsoCond2,'or ').")";
			$all_fso_cond3.=" and (".chop($fsoCond3,'or ').")";
		}
		else
		{
			$all_fso_cond=" and a.id in($fso_ids)";
			$all_fso_cond2=" and c.po_breakdown_id in($fso_ids)";
			$all_fso_cond3=" and a.from_order_id in($fso_ids)";
		}

		$fso_ref_sql = sql_select("SELECT a.company_id,a.po_buyer,a.po_company_id,a.within_group, a.id as sales_id, a.job_no,a.season,a.sales_booking_no,a.style_ref_no,a.buyer_id,a.season,a.sales_booking_no,a.booking_type,a.booking_without_order,a.booking_entry_form, b.determination_id, b.gsm_weight,b.width_dia_type, b.dia, b.cons_uom, b.color_id, b.color_type_id,b.finish_qty,b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id = b.mst_id $all_fso_cond and a.status_active =1 and b.status_active =1");

		$fso_ref_data_arr=array();$fso_ref_data=array();
		$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		foreach($fso_ref_sql as $row)
		{
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['book_qnty'] +=$row[csf('finish_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['fso_qnty'] +=$row[csf('grey_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['color_type'] .=$row[csf('color_type_id')].",";

			$fso_ref_data[$row[csf('sales_id')]]["within_group"] = $row[csf('within_group')];
			$fso_ref_data[$row[csf('sales_id')]]["po_company_id"] = $row[csf('po_company_id')];

			if($row[csf('within_group')]==1)
			{
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('po_buyer')];
			}else {
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('buyer_id')];
			}

			$fso_ref_data[$row[csf('sales_id')]]["style_ref_no"] = $row[csf('style_ref_no')];
			$fso_ref_data[$row[csf('sales_id')]]["season"] = $row[csf('season')];
			$fso_ref_data[$row[csf('sales_id')]]["job_no"] = $row[csf('job_no')];
			$fso_ref_data[$row[csf('sales_id')]]["sales_booking_no"] = $row[csf('sales_booking_no')];

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

			$salesTypeData[$row[csf("sales_id")]]['booking_type'] = $bookingType;
		}

		unset($fso_ref_sql);

		$delivery_qnty_sql = sql_select("SELECT b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia, $selectRcvStore d.color color_id, a.transaction_date
			from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a
			where a.company_id=$company_name and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id $all_fso_cond2 $rcvStoreCond and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 $to_trans_date_cond2 group by b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id,d.gsm, $groupByRcvStore d.dia_width,d.color,a.transaction_date");

		foreach ($delivery_qnty_sql as $row)  
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
					}
					else
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise==1)
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
				}
				else
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
				}
			}
		}
		unset($delivery_qnty_sql);

		$issue_return_sql = sql_select("SELECT a.company_id, c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,  b.uom, f.color as color_id,b.dia_width_type, b.width, b.gsm, $selectRcvStore sum(c.quantity) as quantity , sum(e.order_amount) as amount, e.transaction_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, inv_transaction e, product_details_master f where a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=e.id and e.prod_id=f.id and a.entry_form=233 and c.entry_form=233 and b.is_sales=1 and c.is_sales=1 and a.company_id=$company_name $all_fso_cond2 $rcvStoreCond $to_trans_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id,c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,b.uom, f.color, $groupByRcvStore b.dia_width_type, b.width, a.item_category, b.gsm, e.transaction_date");
		foreach ($issue_return_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($issue_return_sql);

		$transfered_fabric_sql = sql_select("SELECT a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id, b.feb_description_id as fabric_description_id, $selectTransOutStore b.uom, d.color as color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity, sum(c.order_amount) as amount, c.transaction_date
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
			where a.id=b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.transaction_type=6 and a.entry_form in(230) and a.company_id = $company_name $all_fso_cond3 $to_trans_date_cond3 and a.status_active =1 and a.is_deleted =0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
			group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, $groupByTransOutStore b.uom, d.color, b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm, c.transaction_date");

		foreach ($transfered_fabric_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}

				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["out"] += $row[csf("amount")];
						}
						else
						{

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["out"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($transfered_fabric_sql);

		$rcv_return_sql = sql_select("SELECT c.po_breakdown_id, c.entry_form , c.quantity, c.is_sales, d.store_id, d.batch_id, e.detarmination_id, e.gsm, e.dia_width, d.body_part_id, d.width_type, e.color,d.uom, f.order_amount as amount, f.transaction_date
			from order_wise_pro_details c, inv_finish_fabric_issue_dtls d, product_details_master e, inv_transaction f
			where c.dtls_id = d.id and d.prod_id = e.id and c.trans_id = f.id and c.entry_form = 287 $all_fso_cond2 $to_trans_date_cond4 and c.is_sales =1 and e.item_category_id =2 and c.status_active =1 and c.is_deleted = 0 and d.status_active =1 and d.is_deleted = 0 and f.status_active =1 and f.is_deleted = 0");

		foreach ($rcv_return_sql as $row)
		{

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["rcv_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}

		$prod_id_arr = array_filter($prod_id_arr);
		if(count($prod_id_arr)>0)
		{
			$prod_ids = implode(",", $prod_id_arr);
			$prodCond = $all_prod_id_cond = "";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$prod_id_arr_chunk=array_chunk($prod_id_arr,999) ;
				foreach($prod_id_arr_chunk as $chunk_arr)
				{
					$prodCond.=" b.pi_wo_batch_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
			}
			else
			{
				$all_prod_id_cond=" and b.pi_wo_batch_no in($prod_ids)";
			}
		}

		$date_array=array();
		$dateRes_date="SELECT c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit, min(b.transaction_date) as min_date, max(b.transaction_date) as max_date 
		from product_details_master a, inv_transaction b,order_wise_pro_details c
		where a.id=b.prod_id and b.id=c.trans_id and b.is_deleted=0 and b.status_active=1 and b.item_category=2 and b.transaction_type in (2,6) and c.trans_type in (2,6)
		$all_prod_id_cond
		group by c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit ";
		$result_dateRes_date = sql_select($dateRes_date);
		foreach($result_dateRes_date as $row)
		{
			if(!$row[csf("pi_wo_batch_no")])$row[csf("pi_wo_batch_no")]=0;
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['max_date']=$row[csf("max_date")];
			$avg_rate_arr[$row[csf("pi_wo_batch_no")]] = $row[csf("avg_rate_per_unit")];
		}
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array_deter);
 


	if($cbo_store_wise ==1)
	{
		$sub_total_col_span = 23;
	}else{
		$sub_total_col_span = 22;
	}
	if($txt_date_from != "" && $txt_date_to != "")
	{
		$receive_col_span = 7;
	}else{
		$receive_col_span = 6;
	}

	ob_start();
	?>
	<style type="text/css">
	.word_wrap_break{
		word-wrap: break-word;
		word-break: break-all;
	}
</style>

<fieldset style="width:3310px;">
	<table width="3410" cellspacing="0" cellpadding="0" border="0" rules="all" >
		<tr class="form_caption">
			<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		</tr>
		<tr class="form_caption">
			<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3330" class="rpt_table" >
		<thead>
			<tr>
				<th width="30" rowspan="2">SL</th>
				<th width="60" rowspan="2">Company</th>
				<th width="70" rowspan="2">LC Company</th>
				<th width="80" rowspan="2">PO Buyer</th>
				<th width="100" rowspan="2">Style Ref.</th>
				<th width="80" rowspan="2">Season</th>
				<th width="50" rowspan="2">Within<br> Group</th>
				<th width="100" rowspan="2">Booking No</th>
				<th width="80" rowspan="2">Booking Type</th>
				<th width="110" rowspan="2">FSO</th>
				<th width="100" rowspan="2">Batch No</th>
				<th width="100" rowspan="2">Ext. No</th>
				<th width="100" rowspan="2">Batch Qty.</th>

				<? if($cbo_store_wise ==1){?>
					<th width="100" rowspan="2">Store Name</th>
					<?}?>
					<!--<th width="80" rowspan="2">Booking Qnty</th>
					<th width="80" rowspan="2">FSO Qnty</th>-->
					<th colspan="9">Fabric Details</th>
					<th colspan="<? echo $receive_col_span;?>">Receive Details</th>
					<th colspan="6">Issue Details</th>
					<th colspan="5">Stock Details</th>
				</tr>
				<tr>
					<th width="80">Body Part</th>
					<th width="100">Construction</th>
					<th width="100">Composition</th>
					<th width="80">GSM</th>
					<th width="80">F/Dia</th>
					<th width="80">Dia Type</th>
					<th width="80">Color Type</th>
					<th width="120">Fab. Color</th>
					<th width="50">UOM</th>

					<? if($txt_date_from != "" && $txt_date_to != ""){?>
						<th width="100">Opening Stock</th>
						<?}?>
						<th width="80">Receive</th>
						<th width="80">Issue Ret.</th>
						<th width="80">Trans In</th>
						<th width="80">Total Rcv</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Receive Amount</th>

						<th width="80">Issue</th>
						<th width="80">Receive Rtn.</th>
						<th width="80">Trans Out</th>
						<th width="80">Total Issue</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Issue Amount</th>

						<th width="80">Stock</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Stock Amount</th>
						<th width="50">Age (days)</th>
						<th width="50">DOH</th>
					</tr>
				</thead>
			</table>
			<div style="width:3350px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3330" class="rpt_table" id="tbl_list_search">
					<?
					
					$i=1;
					$pp=1;
					foreach ($source_arr as $uom_id => $uom_data)
					{
						$uom_arr=array();
						$sub_rcv=$sub_trans_in=$sub_iss_ret=$sub_rcv_tot=$sub_rcv_amount=$sub_issue=$sub_issue_return=$sub_rcv_ret=$sub_tran_out=$sub_issue_tot=$sub_issue_amount=$sub_stock_qty=$sub_stock_amount=$sub_opening_qnty=0;
						foreach ($uom_data as $po_breakdown_id => $po_breakdown_data)
						{
							$y=1; $show_row_sub_total = false;
							$opening_balance_qnty=0;
							foreach ($po_breakdown_data as $prod_ref => $row)
							{
								$sales_prod_key_arr=explode("**", $prod_ref);
								$company_id = $sales_prod_key_arr[0];
								$prod_id = $sales_prod_key_arr[1];//here prod id is equal to batch id

								$fabric_description_id = $sales_prod_key_arr[2];
								$gsm = $sales_prod_key_arr[3];
								$width = $sales_prod_key_arr[4];
								$body_part_id = $sales_prod_key_arr[5];
								$dia_width_type = $sales_prod_key_arr[6];
								$color_id  = $sales_prod_key_arr[7];
								if($cbo_store_wise ==1)
								{
									$batch_no  =$sales_prod_key_arr[9];
									$ext_no  = $sales_prod_key_arr[10];
									//$batch_qty  = $sales_prod_key_arr[11];
									$within_group  = $yes_no[$sales_prod_key_arr[12]];
								}
								else
								{
									$batch_no  =$sales_prod_key_arr[8];
									$ext_no  = $sales_prod_key_arr[9];
									//$batch_qty  = $sales_prod_key_arr[10];
									$within_group  = $yes_no[$sales_prod_key_arr[11]];
								}
								
								
								$booking_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['book_qnty'];
								$fso_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['fso_qnty'];
								$color_type_id = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['color_type'];

								$daysOnHand = datediff("d",$date_array[$po_breakdown_id][$prod_id]['max_date'],date("Y-m-d"));
								if(!$daysOnHand)$daysOnHand = datediff("d",$date_array[$po_breakdown_id][0]['max_date'],date("Y-m-d"));
								if($cbo_store_wise ==1)
								{
									$store_id  = $sales_prod_key_arr[8];
									$is_transfered  = $sales_prod_key_arr[9];
									$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_qnty"];
									$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_amount"];

									$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rcv_qnty"];

									$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_qnty"];

									$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_trans_out"];

									$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rec_ret_qnty"];

									$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_ret_qnty"];

									$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_balance_amt"];


									//===amount for title===
									$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_in"];
									$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss"];
									$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["out"];
									$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_ret"];
									$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss_ret"];
									//======


								}else{
									$is_transfered  = $sales_prod_key_arr[8];
									$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_qnty"];
									$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_amount"];
									$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rcv_qnty"];
									$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_qnty"];
									$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_trans_out"];
									$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rec_ret_qnty"];
									$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_ret_qnty"];

									$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_balance_amt"];


									//===amount for title===
									$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_in"];
									$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss"];
									$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["out"];
									$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_ret"];
									$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss_ret"];
									//======
								}

								$total_rcv_qnty = $row['rcv_qnty']+$issue_return_qnty+$row['trans_in_qnty'];

								$rcv_amount = $row['rcv_amount'] + $issue_return_amount + $row['trans_in_amount'];
								if($total_rcv_qnty > 0)
								{
									$rcv_avg_rate = $rcv_amount/$total_rcv_qnty;
								}else{
									$rcv_avg_rate = 0;
								}


								$total_issue_qnty = $delivery_qnty+$rcv_ret_qnty+ $transferOutQnty;
								$issue_amount = $delivery_amount+$rcv_ret_amount+ $transferOutAmount;
								if($total_issue_qnty>0)
								{
									$issue_avg_rate = $issue_amount/$total_issue_qnty;
								}else{
									$issue_avg_rate = 0;
								}

								$opening_bal = ($opening_balance_qnty+$open_iss_ret_qnty)-($opening_issue_qnty+$opening_trans_out_qnty+$opening_recv_rtn_qnty);
								$opening_title = "Receive=$opening_balance_qnty,Issue Return=$open_iss_ret_qnty\n Issue=$opening_issue_qnty,Trans. Out=$opening_trans_out_qnty";
								//$opening_title .= "<br><br>Receive amount=$opening_rcv_amount,Iss Ret amount=$open_iss_ret_amount\n Iss amount=$opening_issue_amount,Trans. Out amount=$opening_trans_out_amount";

								$total_stock_qty =  $opening_bal + ($total_rcv_qnty-$total_issue_qnty);
								if($user_id != 276){
									$total_stock_qty = ($total_stock_qty>0)?$total_stock_qty:0.00;
								}

								$total_stock_amount = ($opening_balance_amount + $rcv_amount) - $issue_amount;
								$total_stock_amount = ($total_stock_amount>0)?$total_stock_amount:0.00;
								if($total_stock_qty>0)
								{
									$total_stock_avg_rate = $total_stock_amount/$total_stock_qty;
								}

								$color_type_ids="";
								$color_type_arr =  array_filter(array_unique(explode(",",chop($color_type_id,","))));
								foreach ($color_type_arr as $val)
								{
									if($color_type_ids == "") $color_type_ids = $color_type[$val]; else $color_type_ids .= ", ". $color_type[$val];
								}

								if ((($cbo_get_upto_qnty == 1 && $total_stock_qty > $txt_qnty) || ($cbo_get_upto_qnty == 2 && $total_stock_qty < $txt_qnty) || ($cbo_get_upto_qnty == 3 && $total_stock_qty >= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $total_stock_qty <= $txt_qnty) || ($cbo_get_upto_qnty == 5 && $total_stock_qty == $txt_qnty) || $cbo_get_upto_qnty == 0) && (($cbo_get_upto == 1 && $daysOnHand > $txt_days) || ($cbo_get_upto == 2 && $daysOnHand < $txt_days) || ($cbo_get_upto == 3 && $daysOnHand >= $txt_days) || ($cbo_get_upto == 4 && $daysOnHand <= $txt_days) || ($cbo_get_upto == 5 && $daysOnHand == $txt_days) || $cbo_get_upto == 0))
								{

									if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$fabric_span = $details_row_span_arr[$uom_id."##".$po_breakdown_id];

									$pop_ref = $po_breakdown_id."__".$prod_id."__".$fabric_description_id."__".$gsm."__".$width."__".$body_part_id."__".$dia_width_type."__".$color_id."__".$uom_id;
									$transfered = ($is_transfered==1)?"<strong style='color:red'>[T]</strong>":"";

									$mrr_date = "";
									$mrr_date =$row['mrr_date'];
									$ageOfDays = datediff("d", $mrr_date, date("Y-m-d"));

									?>

									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
										<td width="30" ><? echo $i; ?></td>
										<td width="60" class="word_wrap_break" ><? echo $company_arr[$company_id]; ?></td>
										<td width="70" class="word_wrap_break"><? echo $company_arr[$fso_ref_data[$po_breakdown_id]["po_company_id"]];?></td>
										<td width="80" class="word_wrap_break"><? echo $buyer_arr[$fso_ref_data[$po_breakdown_id]["po_buyer"]]; ?></td>
										<td width="100" class="word_wrap_break"><? echo $fso_ref_data[$po_breakdown_id]["style_ref_no"]; ?></td>
										<td width="80" class="word_wrap_break"><? echo $fso_ref_data[$po_breakdown_id]["season"]; ?></td>
										<td width="50" class="word_wrap_break"><? echo $within_group; ?></td>
										<td width="100" class="word_wrap_break"><? echo  $fso_ref_data[$po_breakdown_id]["sales_booking_no"]; ?></td>
										<td width="80" class="word_wrap_break"><? echo $salesTypeData[$po_breakdown_id]['booking_type']; ?></td>
										<td width="110" class="word_wrap_break" title="<? echo $po_breakdown_id;?>"><? echo $fso_ref_data[$po_breakdown_id]["job_no"];?></td>
										<?
										$batch_qty=number_format($batch_qnty_arr_new[$prod_id][$body_part_id][trim($constructtion_arr[$fabric_description_id])],2);
										?>
										<!--<td width="80" class="word_wrap_break" align="right"><? //echo number_format($booking_qnty,2); ?></td>
										<td width="80" class="word_wrap_break" align="right" title="<? //echo $fabric_description_id.'_'.$dia_width_type.'_'.$color_id;?>"><? //echo number_format($fso_qnty,2); ?></td>-->
										<td width="100" class="word_wrap_break" align="center" title="<? echo $prod_id;?>"><? echo $batch_no; ?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $ext_no; ?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $batch_qty; ?></td>
										<?
										if($cbo_store_wise ==1)
										{
											$store_id = $sales_prod_key_arr[8];
											?>
											<td width="100" class="word_wrap_break" title="<? echo $store_id;?>"><? echo $store_arr[$store_id]; ?></td>
											<?
										}
										?>
										<td width="80" class="word_wrap_break" align="center"><? echo $body_part[$body_part_id] ?></td>

										<td width="100" class="word_wrap_break" align="center"><? echo $constructtion_arr[$fabric_description_id]; ?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $composition_arr[$fabric_description_id]; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $gsm; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $width; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $fabric_typee[$dia_width_type]; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $color_type_ids; ?></td>
										<td width="120" class="word_wrap_break" align="center"><? echo $color_arr[$color_id]; ?></td>
										<td width="50" class="word_wrap_break" align="center"><? echo $unit_of_measurement[$uom_id]; ?></td>
										<? if($txt_date_from != "" && $txt_date_to != ""){?>
											<td width="100" class="word_wrap_break" align="right" title="<? echo $opening_title; ?>"><? echo number_format($opening_bal,2); ?></td>
											<?}?>

											<td width="80" class="word_wrap_break" align="right"><a href="##" onClick="openmypage_receive('<? echo $pop_ref;?>','receive_finish_popup')"><? echo number_format($row['rcv_qnty'],2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($issue_return_qnty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><a href="##" onClick="openmypage_trans('<? echo $pop_ref;?>','trans_in_popup')"><? echo number_format($row['trans_in_qnty'],2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_rcv_qnty,2,'.',''); ?></td>
											<td width="80"  class="word_wrap_break" align="right"><? echo number_format($rcv_avg_rate,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($rcv_amount,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right" title='<? //echo "po = $po_breakdown_id, prod = $prod_id , deter = $fabric_description_id,gsm = $gsm, width=$width, body =$body_part_id , dia type = $dia_width_type , color = $color_id";?>'><a href="##" onClick="openmypage_issue('<? echo $pop_ref;?>','issue_finish_popup')"><? echo number_format($delivery_qnty,2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($rcv_ret_qnty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><a href="##" onClick="openmypage_trans('<? echo $pop_ref;?>','trans_out_popup')"><? echo number_format($transferOutQnty,2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_issue_qnty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($issue_avg_rate,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($issue_amount,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_stock_avg_rate,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_stock_amount,2,'.',''); ?></td>
											<td width="50" class="word_wrap_break" align="right" title="<? echo $mrr_date;?>"><? echo $ageOfDays;?></td>
											<td width="50" class="word_wrap_break" align="right" title="<? echo $date_array[$po_breakdown_id][$prod_id]['max_date'];?>"><? echo $daysOnHand;?></td>
										</tr>
										<?
										$show_row_sub_total = true;
										$y++;$m++;$i++;
										$uom_arr[$uom_id]=$uom_id;
										$sub_rcv += $row['rcv_qnty'];
										$sub_trans_in += $row['trans_in_qnty'];

										$sub_opening_qnty += $opening_bal;

										$sub_iss_ret = 0;
										$sub_rcv_tot +=$total_rcv_qnty;
										$sub_rcv_amount += $rcv_amount;
										$sub_issue  += $delivery_qnty;
										$sub_issue_return  += $issue_return_qnty;
										$sub_rcv_ret +=$rcv_ret_qnty;
										$sub_tran_out +=$transferOutQnty;
										$sub_issue_tot +=$total_issue_qnty;
										$sub_issue_amount += $issue_amount;

										$sub_stock_qty += $total_stock_qty;
										$sub_stock_amount += $total_stock_amount;
									}
								}

							}
							$unit_of_measurement[implode(",", $uom_arr)];
							if($show_row_sub_total == true)
							{
								if($pp%2==0) $bgcolor2="#e4e4e4"; else $bgcolor2="#9ab2d6";$pp++;
								?>
								<tr bgcolor="<? echo $bgcolor2;?>" style="font-weight: bold;">
									<td colspan="<? echo $sub_total_col_span;?>" align="right">Total <? echo $unit_of_measurement[implode(",", $uom_arr)];?>&nbsp;</td>
									<?
									if($txt_date_from != "" && $txt_date_to != ""){?>
										<td class="word_wrap_break" align="right"><? echo number_format($sub_opening_qnty,2,'.',''); ?></td>
										<?
									}?>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv,2,'.','');?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue_return,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_trans_in,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv_tot,2,".","");?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv_amount,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv_ret,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_tran_out,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue_tot,2,".","");?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue_amount,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_stock_qty,2,'.','');?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_stock_amount,2,'.','');?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
								</tr>
								<?
							}
						}
						?>
					</table>
				</div>
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$store_arr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_id and  b.category_type=2 order by a.store_name",'id','store_name');


	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$cbo_store_wise= str_replace("'","",$cbo_store_wise);
	$cbo_store_name= str_replace("'","",$cbo_store_name);
	$cbo_get_upto= str_replace("'","",$cbo_get_upto);
	$txt_days= str_replace("'","",$txt_days);
	$cbo_get_upto_qnty= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty= str_replace("'","",$txt_qnty);
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);

	if($within_group==1)
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.po_buyer=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.buyer_id=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond="and a.company_id='$pocompany_id'";
	$date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
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
		if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}


	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and d.job_no_prefix_num='$order_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and d.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}

	} else {
		$booking_no_cond="";
	}

	if($cbo_store_wise ==1)
	{
		$selectRcvStore = " a.store_id,";
		$selectTransStore = " b.to_store as store_id,";
		$selectTransOutStore = " b.from_store as store_id,";
		$groupByRcvStore = " a.store_id,";
		$groupByTransStore = " b.to_store,";
		$groupByTransOutStore = " b.from_store,";

		if($cbo_store_name)
		{
			$rcvStoreCond = " and e.store_id = $cbo_store_name";
			$TransStoreCond = " and b.to_store = $cbo_store_name";
		}
	}

	if($within_group>0)
	{
		$withinGroupCond = "and d.within_group=$within_group";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$to_trans_date_cond = " and e.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond2 = " and a.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond3 = " and c.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond4 = " and f.transaction_date <= '".$txt_date_to."'";
	}

	$con = connect();
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (888,889)");
    oci_commit($con);

	$sql = "SELECT b.id,d.within_group, f.id as batch_id, f.batch_no,f.extention_no,1 as type, sum(g.batch_qnty) as batch_qty, min(a.receive_date) as mrr_date, a.company_id, c.po_breakdown_id,  b.body_part_id, b.fabric_description_id, $selectRcvStore b.uom, h.color as color_id,b.dia_width_type, b.width, b.gsm, (c.quantity) as quantity , sum(e.cons_amount) as amount,0 as is_transfered,0 as from_order_id, a.receive_basis, (e.order_amount) as order_amount, e.transaction_date
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master h
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.trans_id=e.id and e.prod_id=h.id and b.batch_id=f.id and f.id=g.mst_id and CAST(g.po_id AS VARCHAR2(4000)) =b.order_id and g.body_part_id = b.body_part_id and d.sales_booking_no=f.booking_no and f.status_active=1 and g.status_active=1 and a.entry_form=225 and c.entry_form=225 and b.is_sales=1 and c.is_sales=1 and a.company_id = $company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $rcvStoreCond $year_search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in (10,14) $to_trans_date_cond
	group by  b.id,d.within_group,f.id, f.batch_no,f.extention_no,a.company_id,c.po_breakdown_id, b.body_part_id,b.fabric_description_id, $groupByRcvStore b.uom, e.order_amount, h.color, b.dia_width_type, b.width, a.item_category, b.gsm,c.quantity, a.receive_basis, e.transaction_date
	union all
	select  b.id,d.within_group,f.id as batch_id, f.batch_no,f.extention_no,2 as type, sum(g.batch_qnty) as batch_qty, min(a.transfer_date) as mrr_date, a.company_id,a.to_order_id as po_breakdown_id, g.body_part_id,b.feb_description_id as fabric_description_id, $selectTransStore b.uom, c.color as color_id,b.dia_width_type, b.dia_width as width, b.gsm, (b.transfer_qnty) as quantity , sum(e.cons_amount) as amount,1 as is_transfered,a.from_order_id , 0 as receive_basis,  (e.order_amount) as order_amount, e.transaction_date
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, fabric_sales_order_mst d , inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master c 
	where a.id=b.mst_id and a.to_order_id=d.id and b.to_trans_id=e.id and e.prod_id=c.id and b.to_batch_id=f.id and f.id=g.mst_id AND g.body_part_id = b.body_part_id and f.status_active=1 and g.status_active=1  and a.company_id=$company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $TransStoreCond $year_search_cond and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_trans_date_cond
	group by  b.id,d.within_group,f.id,f.batch_no,f.extention_no,a.company_id,a.to_order_id, e.order_amount,b.from_prod_id, g.body_part_id, b.feb_description_id, $groupByTransStore b.uom, c.color, b.dia_width_type, b.dia_width, b.gsm,b.transfer_qnty,a.from_order_id, e.transaction_date
	order by uom,po_breakdown_id";
	//and d.id=f.sales_order_id 
	//echo $sql;//die;
	$nameArray=sql_select($sql);
	$ref_key="";$open=0;
	foreach($nameArray as $row)
	{
		$all_batch_id[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$row[csf("prod_id")]=$row[csf("batch_id")]; //this is intensional so don't change it
		if($row[csf("quantity")] > 0)
		{
			$fso_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			if($cbo_store_wise ==1)
			{
				$sub_total_col_span = 22;
				$ref_key =$row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("store_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}else{
				$sub_total_col_span = 21;
				$ref_key = $row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{

				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($row[csf("type")] == 1)
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
					}
					else
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_in"] += $row[csf("order_amount")];
						}else{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["rcv_in"] += $row[csf("order_amount")];
						}

						if($row[csf("type")] == 1)
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["opening"] += $row[csf("quantity")];
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += 0;//$row[csf("order_amount")];
						}else{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] = 0;
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] = 0;
						}
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
			}
			else
			{
				if($row[csf("type")] == 1)
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
				}else{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
				}

				if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
				}
				else
				{
					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
				}
			}
		}
	}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 888, 1,$all_batch_id, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 889, 1,$fso_id_arr, $empty_arr);
	oci_commit($con);

	$all_batch_ids= implode(",", $all_batch_id);
	$batch_conds=" and mst_id in($all_batch_ids)";
	if($db_type==2 && count($all_batch_id)>999)
	{
		$batch_conds="";
		$chnk=array_chunk($all_batch_id, 999);
		foreach($chnk as $val)
		{
			$ids=implode(",", $val);
			if(!$batch_conds)$batch_conds.=" and ( mst_id in($ids) ";
			else $batch_conds.=" or  mst_id in($ids) ";
		}
		$batch_conds.=")";

	}
	$batch_sql="SELECT a.mst_id,  a.item_description,  a.batch_qnty, a.body_part_id FROM pro_batch_create_dtls a, GBL_TEMP_ENGINE b Where status_active=1 and a.mst_id=b.ref_val and b.user_id=$user_id and b.entry_form=888 "; //$batch_conds 
	foreach(sql_select($batch_sql) as $vals)
	{
		$items=explode(",",$vals[csf("item_description")]);
		$items_st=trim($items[0]);
		$batch_qnty_arr_new[$vals[csf("mst_id")]][$vals[csf("body_part_id")]][$items_st]+=$vals[csf("batch_qnty")];
	}
	/*echo "<pre>";
	print_r($batch_qnty_arr_new);die;*/


	$fso_id_arr = array_filter($fso_id_arr);
	if(!empty($fso_id_arr))
	{
		$fso_ids = implode(",", array_filter($fso_id_arr));
		$fsoCond = $all_fso_cond = "";
		$fsoCond2 = $all_fso_cond2 = "";
		$fsoCond3 = $all_fso_cond3 = "";
		if($db_type==2 && count($fso_id_arr)>999)
		{
			$fso_id_arr_chunk=array_chunk($fso_id_arr,999) ;
			foreach($fso_id_arr_chunk as $chunk_arr)
			{
				$fsoCond.=" a.id in(".implode(",",$chunk_arr).") or ";
				$fsoCond2.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				$fsoCond3.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_fso_cond.=" and (".chop($fsoCond,'or ').")";
			$all_fso_cond2.=" and (".chop($fsoCond2,'or ').")";
			$all_fso_cond3.=" and (".chop($fsoCond3,'or ').")";
		}
		else
		{
			$all_fso_cond=" and a.id in($fso_ids)";
			$all_fso_cond2=" and c.po_breakdown_id in($fso_ids)";
			$all_fso_cond3=" and a.from_order_id in($fso_ids)";
		}

		$fso_ref_sql = sql_select("SELECT a.company_id,a.po_buyer,a.po_company_id,a.within_group, a.id as sales_id, a.job_no,a.season,a.sales_booking_no,a.style_ref_no,a.buyer_id,a.season,a.sales_booking_no,a.booking_type,a.booking_without_order,a.booking_entry_form, b.determination_id, b.gsm_weight, b.width_dia_type, b.dia, b.cons_uom, b.color_id, b.color_type_id, b.finish_qty, b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b, GBL_TEMP_ENGINE c where a.id = b.mst_id and a.id=c.ref_val and c.user_id=$user_id and c.entry_form=889 and a.status_active =1 and b.status_active =1"); //$all_fso_cond

		$fso_ref_data_arr=array();$fso_ref_data=array();
		$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		foreach($fso_ref_sql as $row)
		{
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['book_qnty'] +=$row[csf('finish_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['fso_qnty'] +=$row[csf('grey_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['color_type'] .=$row[csf('color_type_id')].",";

			$fso_ref_data[$row[csf('sales_id')]]["within_group"] = $row[csf('within_group')];
			$fso_ref_data[$row[csf('sales_id')]]["po_company_id"] = $row[csf('po_company_id')];

			if($row[csf('within_group')]==1)
			{
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('po_buyer')];
			}else {
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('buyer_id')];
			}

			$fso_ref_data[$row[csf('sales_id')]]["style_ref_no"] = $row[csf('style_ref_no')];
			$fso_ref_data[$row[csf('sales_id')]]["season"] = $row[csf('season')];
			$fso_ref_data[$row[csf('sales_id')]]["job_no"] = $row[csf('job_no')];
			$fso_ref_data[$row[csf('sales_id')]]["sales_booking_no"] = $row[csf('sales_booking_no')];

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

			$salesTypeData[$row[csf("sales_id")]]['booking_type'] = $bookingType;
		}

		unset($fso_ref_sql);

		$delivery_qnty_sql = sql_select("SELECT b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia, $selectRcvStore d.color color_id, a.transaction_date
			from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a, GBL_TEMP_ENGINE e
			where a.company_id=$company_name and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id  $rcvStoreCond and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 $to_trans_date_cond2 and c.po_breakdown_id=e.ref_val and e.user_id=$user_id and e.entry_form=889 group by b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id,d.gsm, $groupByRcvStore d.dia_width,d.color,a.transaction_date"); //$all_fso_cond2 

		foreach ($delivery_qnty_sql as $row)  
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
					}
					else
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise==1)
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
				}
				else
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
				}
			}
		}
		unset($delivery_qnty_sql);

		$issue_return_sql = sql_select("SELECT a.company_id, c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,  b.uom, f.color as color_id,b.dia_width_type, b.width, b.gsm, $selectRcvStore sum(c.quantity) as quantity , sum(e.order_amount) as amount, e.transaction_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, inv_transaction e, product_details_master f, GBL_TEMP_ENGINE d where a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=e.id and e.prod_id=f.id and a.entry_form=233 and c.entry_form=233 and b.is_sales=1 and c.is_sales=1 and a.company_id=$company_name  $rcvStoreCond $to_trans_date_cond and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=889 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id,c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,b.uom, f.color, $groupByRcvStore b.dia_width_type, b.width, a.item_category, b.gsm, e.transaction_date"); //$all_fso_cond2
		foreach ($issue_return_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($issue_return_sql);

		$transfered_fabric_sql = sql_select("SELECT a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id, b.feb_description_id as fabric_description_id, $selectTransOutStore b.uom, d.color as color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity, sum(c.order_amount) as amount, c.transaction_date
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d, GBL_TEMP_ENGINE e
			where a.id=b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.transaction_type=6 and a.entry_form in(230) and a.company_id = $company_name and a.from_order_id=e.ref_val and e.user_id=$user_id and e.entry_form=889 $to_trans_date_cond3 and a.status_active =1 and a.is_deleted =0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
			group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, $groupByTransOutStore b.uom, d.color, b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm, c.transaction_date"); //$all_fso_cond3

		foreach ($transfered_fabric_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}

				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["out"] += $row[csf("amount")];
						}
						else
						{

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["out"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($transfered_fabric_sql);

		$rcv_return_sql = sql_select("SELECT c.po_breakdown_id, c.entry_form , c.quantity, c.is_sales, d.store_id, d.batch_id, e.detarmination_id, e.gsm, e.dia_width, d.body_part_id, d.width_type, e.color,d.uom, f.order_amount as amount, f.transaction_date
			from order_wise_pro_details c, inv_finish_fabric_issue_dtls d, product_details_master e, inv_transaction f, GBL_TEMP_ENGINE b
			where c.dtls_id = d.id and d.prod_id = e.id and c.trans_id=f.id and c.entry_form = 287 and c.po_breakdown_id=b.ref_val and b.entry_form=889 and b.user_id=$user_id $to_trans_date_cond4 and c.is_sales=1 and e.item_category_id =2 and c.status_active =1 and c.is_deleted = 0 and d.status_active =1 and d.is_deleted = 0 and f.status_active =1 and f.is_deleted = 0"); //$all_fso_cond2



		foreach ($rcv_return_sql as $row)
		{

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["rcv_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}

		$prod_id_arr = array_filter($prod_id_arr);
		if(count($prod_id_arr)>0)
		{
			$prod_ids = implode(",", $prod_id_arr);
			$prodCond = $all_prod_id_cond = "";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$prod_id_arr_chunk=array_chunk($prod_id_arr,999) ;
				foreach($prod_id_arr_chunk as $chunk_arr)
				{
					$prodCond.=" b.pi_wo_batch_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
			}
			else
			{
				$all_prod_id_cond=" and b.pi_wo_batch_no in($prod_ids)";
			}
		}

		$date_array=array();
		$dateRes_date="SELECT c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit, min(b.transaction_date) as min_date, max(b.transaction_date) as max_date 
		from product_details_master a, inv_transaction b,order_wise_pro_details c, GBL_TEMP_ENGINE d
		where a.id=b.prod_id and b.id=c.trans_id and b.is_deleted=0 and b.status_active=1 and b.item_category=2 and b.transaction_type in (2,6) and c.trans_type in (2,6)
		and b.pi_wo_batch_no=d.ref_val and d.user_id=$user_id and d.entry_form=888
		group by c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit "; //$all_prod_id_cond
		$result_dateRes_date = sql_select($dateRes_date);
		foreach($result_dateRes_date as $row)
		{
			if(!$row[csf("pi_wo_batch_no")])$row[csf("pi_wo_batch_no")]=0;
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['max_date']=$row[csf("max_date")];
			$avg_rate_arr[$row[csf("pi_wo_batch_no")]] = $row[csf("avg_rate_per_unit")];
		}
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array_deter);
 


	if($cbo_store_wise ==1)
	{
		$sub_total_col_span = 23;
	}else{
		$sub_total_col_span = 22;
	}
	if($txt_date_from != "" && $txt_date_to != "")
	{
		$receive_col_span = 7;
	}else{
		$receive_col_span = 6;
	}

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (888,889)");
	oci_commit($con);
	//echo "Here";die;

	ob_start();
	?>
	<style type="text/css">
	.word_wrap_break{
		word-wrap: break-word;
		word-break: break-all;
	}
</style>

<fieldset style="width:3310px;">
	<table width="3410" cellspacing="0" cellpadding="0" border="0" rules="all" >
		<tr class="form_caption">
			<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		</tr>
		<tr class="form_caption">
			<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3330" class="rpt_table" >
		<thead>
			<tr>
				<th width="30" rowspan="2">SL</th>
				<th width="60" rowspan="2">Company</th>
				<th width="70" rowspan="2">LC Company</th>
				<th width="80" rowspan="2">PO Buyer</th>
				<th width="100" rowspan="2">Style Ref.</th>
				<th width="80" rowspan="2">Season</th>
				<th width="50" rowspan="2">Within<br> Group</th>
				<th width="100" rowspan="2">Booking No</th>
				<th width="80" rowspan="2">Booking Type</th>
				<th width="110" rowspan="2">FSO</th>
				<th width="100" rowspan="2">Batch No</th>
				<th width="100" rowspan="2">Ext. No</th>
				<th width="100" rowspan="2">Batch Qty.</th>

				<? if($cbo_store_wise ==1){?>
					<th width="100" rowspan="2">Store Name</th>
					<?}?>
					<!--<th width="80" rowspan="2">Booking Qnty</th>
					<th width="80" rowspan="2">FSO Qnty</th>-->
					<th colspan="9">Fabric Details</th>
					<th colspan="<? echo $receive_col_span;?>">Receive Details</th>
					<th colspan="6">Issue Details</th>
					<th colspan="5">Stock Details</th>
				</tr>
				<tr>
					<th width="80">Body Part</th>
					<th width="100">Construction</th>
					<th width="100">Composition</th>
					<th width="80">GSM</th>
					<th width="80">F/Dia</th>
					<th width="80">Dia Type</th>
					<th width="80">Color Type</th>
					<th width="120">Fab. Color</th>
					<th width="50">UOM</th>

					<? if($txt_date_from != "" && $txt_date_to != ""){?>
						<th width="100">Opening Stock</th>
						<?}?>
						<th width="80">Receive</th>
						<th width="80">Issue Ret.</th>
						<th width="80">Trans In</th>
						<th width="80">Total Rcv</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Receive Amount</th>

						<th width="80">Issue</th>
						<th width="80">Receive Rtn.</th>
						<th width="80">Trans Out</th>
						<th width="80">Total Issue</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Issue Amount</th>

						<th width="80">Stock</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Stock Amount</th>
						<th width="50">Age (days)</th>
						<th width="50">DOH</th>
					</tr>
				</thead>
			</table>
			<div style="width:3350px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3330" class="rpt_table" id="tbl_list_search">
					<?
					
					$i=1;
					$pp=1;
					foreach ($source_arr as $uom_id => $uom_data)
					{
						$uom_arr=array();
						$sub_rcv=$sub_trans_in=$sub_iss_ret=$sub_rcv_tot=$sub_rcv_amount=$sub_issue=$sub_issue_return=$sub_rcv_ret=$sub_tran_out=$sub_issue_tot=$sub_issue_amount=$sub_stock_qty=$sub_stock_amount=$sub_opening_qnty=0;
						foreach ($uom_data as $po_breakdown_id => $po_breakdown_data)
						{
							$y=1; $show_row_sub_total = false;
							$opening_balance_qnty=0;
							foreach ($po_breakdown_data as $prod_ref => $row)
							{
								$sales_prod_key_arr=explode("**", $prod_ref);
								$company_id = $sales_prod_key_arr[0];
								$prod_id = $sales_prod_key_arr[1];//here prod id is equal to batch id

								$fabric_description_id = $sales_prod_key_arr[2];
								$gsm = $sales_prod_key_arr[3];
								$width = $sales_prod_key_arr[4];
								$body_part_id = $sales_prod_key_arr[5];
								$dia_width_type = $sales_prod_key_arr[6];
								$color_id  = $sales_prod_key_arr[7];
								if($cbo_store_wise ==1)
								{
									$batch_no  =$sales_prod_key_arr[9];
									$ext_no  = $sales_prod_key_arr[10];
									//$batch_qty  = $sales_prod_key_arr[11];
									$within_group  = $yes_no[$sales_prod_key_arr[12]];
								}
								else
								{
									$batch_no  =$sales_prod_key_arr[8];
									$ext_no  = $sales_prod_key_arr[9];
									//$batch_qty  = $sales_prod_key_arr[10];
									$within_group  = $yes_no[$sales_prod_key_arr[11]];
								}
								
								
								$booking_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['book_qnty'];
								$fso_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['fso_qnty'];
								$color_type_id = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['color_type'];

								$daysOnHand = datediff("d",$date_array[$po_breakdown_id][$prod_id]['max_date'],date("Y-m-d"));
								if(!$daysOnHand)$daysOnHand = datediff("d",$date_array[$po_breakdown_id][0]['max_date'],date("Y-m-d"));
								if($cbo_store_wise ==1)
								{
									$store_id  = $sales_prod_key_arr[8];
									$is_transfered  = $sales_prod_key_arr[9];
									$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_qnty"];
									$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_amount"];

									$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rcv_qnty"];

									$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_qnty"];

									$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_trans_out"];

									$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rec_ret_qnty"];

									$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_ret_qnty"];

									$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_balance_amt"];


									//===amount for title===
									$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_in"];
									$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss"];
									$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["out"];
									$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_ret"];
									$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss_ret"];
									//======


								}else{
									$is_transfered  = $sales_prod_key_arr[8];
									$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_qnty"];
									$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_amount"];
									$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rcv_qnty"];
									$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_qnty"];
									$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_trans_out"];
									$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rec_ret_qnty"];
									$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_ret_qnty"];

									$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_balance_amt"];


									//===amount for title===
									$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_in"];
									$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss"];
									$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["out"];
									$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_ret"];
									$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss_ret"];
									//======
								}

								$total_rcv_qnty = $row['rcv_qnty']+$issue_return_qnty+$row['trans_in_qnty'];

								$rcv_amount = $row['rcv_amount'] + $issue_return_amount + $row['trans_in_amount'];
								if($total_rcv_qnty > 0)
								{
									$rcv_avg_rate = $rcv_amount/$total_rcv_qnty;
								}else{
									$rcv_avg_rate = 0;
								}


								$total_issue_qnty = $delivery_qnty+$rcv_ret_qnty+ $transferOutQnty;
								$issue_amount = $delivery_amount+$rcv_ret_amount+ $transferOutAmount;
								if($total_issue_qnty>0)
								{
									$issue_avg_rate = $issue_amount/$total_issue_qnty;
								}else{
									$issue_avg_rate = 0;
								}

								$opening_bal = ($opening_balance_qnty+$open_iss_ret_qnty)-($opening_issue_qnty+$opening_trans_out_qnty+$opening_recv_rtn_qnty);
								$opening_title = "Receive=$opening_balance_qnty,Issue Return=$open_iss_ret_qnty\n Issue=$opening_issue_qnty,Trans. Out=$opening_trans_out_qnty";
								//$opening_title .= "<br><br>Receive amount=$opening_rcv_amount,Iss Ret amount=$open_iss_ret_amount\n Iss amount=$opening_issue_amount,Trans. Out amount=$opening_trans_out_amount";

								$total_stock_qty =  $opening_bal + ($total_rcv_qnty-$total_issue_qnty);
								if($user_id != 276){
									$total_stock_qty = ($total_stock_qty>0)?$total_stock_qty:0.00;
								}

								$total_stock_amount = ($opening_balance_amount + $rcv_amount) - $issue_amount;
								$total_stock_amount = ($total_stock_amount>0)?$total_stock_amount:0.00;
								if($total_stock_qty>0)
								{
									$total_stock_avg_rate = $total_stock_amount/$total_stock_qty;
								}

								$color_type_ids="";
								$color_type_arr =  array_filter(array_unique(explode(",",chop($color_type_id,","))));
								foreach ($color_type_arr as $val)
								{
									if($color_type_ids == "") $color_type_ids = $color_type[$val]; else $color_type_ids .= ", ". $color_type[$val];
								}

								if ((($cbo_get_upto_qnty == 1 && $total_stock_qty > $txt_qnty) || ($cbo_get_upto_qnty == 2 && $total_stock_qty < $txt_qnty) || ($cbo_get_upto_qnty == 3 && $total_stock_qty >= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $total_stock_qty <= $txt_qnty) || ($cbo_get_upto_qnty == 5 && $total_stock_qty == $txt_qnty) || $cbo_get_upto_qnty == 0) && (($cbo_get_upto == 1 && $daysOnHand > $txt_days) || ($cbo_get_upto == 2 && $daysOnHand < $txt_days) || ($cbo_get_upto == 3 && $daysOnHand >= $txt_days) || ($cbo_get_upto == 4 && $daysOnHand <= $txt_days) || ($cbo_get_upto == 5 && $daysOnHand == $txt_days) || $cbo_get_upto == 0))
								{

									if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$fabric_span = $details_row_span_arr[$uom_id."##".$po_breakdown_id];

									$pop_ref = $po_breakdown_id."__".$prod_id."__".$fabric_description_id."__".$gsm."__".$width."__".$body_part_id."__".$dia_width_type."__".$color_id."__".$uom_id;
									$transfered = ($is_transfered==1)?"<strong style='color:red'>[T]</strong>":"";

									$mrr_date = "";
									$mrr_date =$row['mrr_date'];
									$ageOfDays = datediff("d", $mrr_date, date("Y-m-d"));

									?>

									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
										<td width="30" ><? echo $i; ?></td>
										<td width="60" class="word_wrap_break" ><? echo $company_arr[$company_id]; ?></td>
										<td width="70" class="word_wrap_break"><? echo $company_arr[$fso_ref_data[$po_breakdown_id]["po_company_id"]];?></td>
										<td width="80" class="word_wrap_break"><? echo $buyer_arr[$fso_ref_data[$po_breakdown_id]["po_buyer"]]; ?></td>
										<td width="100" class="word_wrap_break"><? echo $fso_ref_data[$po_breakdown_id]["style_ref_no"]; ?></td>
										<td width="80" class="word_wrap_break"><? echo $fso_ref_data[$po_breakdown_id]["season"]; ?></td>
										<td width="50" class="word_wrap_break"><? echo $within_group; ?></td>
										<td width="100" class="word_wrap_break"><? echo  $fso_ref_data[$po_breakdown_id]["sales_booking_no"]; ?></td>
										<td width="80" class="word_wrap_break"><? echo $salesTypeData[$po_breakdown_id]['booking_type']; ?></td>
										<td width="110" class="word_wrap_break" title="<? echo $po_breakdown_id;?>"><? echo $fso_ref_data[$po_breakdown_id]["job_no"];?></td>
										<?
										$batch_qty=number_format($batch_qnty_arr_new[$prod_id][$body_part_id][trim($constructtion_arr[$fabric_description_id])],2);
										?>
										<!--<td width="80" class="word_wrap_break" align="right"><? //echo number_format($booking_qnty,2); ?></td>
										<td width="80" class="word_wrap_break" align="right" title="<? //echo $fabric_description_id.'_'.$dia_width_type.'_'.$color_id;?>"><? //echo number_format($fso_qnty,2); ?></td>-->
										<td width="100" class="word_wrap_break" align="center" title="<? echo $prod_id;?>"><? echo $batch_no; ?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $ext_no; ?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $batch_qty; ?></td>
										<?
										if($cbo_store_wise ==1)
										{
											$store_id = $sales_prod_key_arr[8];
											?>
											<td width="100" class="word_wrap_break" title="<? echo $store_id;?>"><? echo $store_arr[$store_id]; ?></td>
											<?
										}
										?>
										<td width="80" class="word_wrap_break" align="center"><? echo $body_part[$body_part_id] ?></td>

										<td width="100" class="word_wrap_break" align="center"><? echo $constructtion_arr[$fabric_description_id]; ?></td>
										<td width="100" class="word_wrap_break" align="center"><? echo $composition_arr[$fabric_description_id]; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $gsm; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $width; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $fabric_typee[$dia_width_type]; ?></td>
										<td width="80" class="word_wrap_break" align="center"><? echo $color_type_ids; ?></td>
										<td width="120" class="word_wrap_break" align="center"><? echo $color_arr[$color_id]; ?></td>
										<td width="50" class="word_wrap_break" align="center"><? echo $unit_of_measurement[$uom_id]; ?></td>
										<? if($txt_date_from != "" && $txt_date_to != ""){?>
											<td width="100" class="word_wrap_break" align="right" title="<? echo $opening_title; ?>"><? echo number_format($opening_bal,2); ?></td>
											<?}?>

											<td width="80" class="word_wrap_break" align="right"><a href="##" onClick="openmypage_receive('<? echo $pop_ref;?>','receive_finish_popup')"><? echo number_format($row['rcv_qnty'],2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($issue_return_qnty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><a href="##" onClick="openmypage_trans('<? echo $pop_ref;?>','trans_in_popup')"><? echo number_format($row['trans_in_qnty'],2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_rcv_qnty,2,'.',''); ?></td>
											<td width="80"  class="word_wrap_break" align="right"><? echo number_format($rcv_avg_rate,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($rcv_amount,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right" title='<? //echo "po = $po_breakdown_id, prod = $prod_id , deter = $fabric_description_id,gsm = $gsm, width=$width, body =$body_part_id , dia type = $dia_width_type , color = $color_id";?>'><a href="##" onClick="openmypage_issue('<? echo $pop_ref;?>','issue_finish_popup')"><? echo number_format($delivery_qnty,2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($rcv_ret_qnty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><a href="##" onClick="openmypage_trans('<? echo $pop_ref;?>','trans_out_popup')"><? echo number_format($transferOutQnty,2,'.',''); ?></a></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_issue_qnty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($issue_avg_rate,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($issue_amount,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_stock_avg_rate,2,'.',''); ?></td>
											<td width="80" class="word_wrap_break" align="right"><? echo number_format($total_stock_amount,2,'.',''); ?></td>
											<td width="50" class="word_wrap_break" align="right" title="<? echo $mrr_date;?>"><? echo $ageOfDays;?></td>
											<td width="50" class="word_wrap_break" align="right" title="<? echo $date_array[$po_breakdown_id][$prod_id]['max_date'];?>"><? echo $daysOnHand;?></td>
										</tr>
										<?
										$show_row_sub_total = true;
										$y++;$m++;$i++;
										$uom_arr[$uom_id]=$uom_id;
										$sub_rcv += $row['rcv_qnty'];
										$sub_trans_in += $row['trans_in_qnty'];

										$sub_opening_qnty += $opening_bal;

										$sub_iss_ret = 0;
										$sub_rcv_tot +=$total_rcv_qnty;
										$sub_rcv_amount += $rcv_amount;
										$sub_issue  += $delivery_qnty;
										$sub_issue_return  += $issue_return_qnty;
										$sub_rcv_ret +=$rcv_ret_qnty;
										$sub_tran_out +=$transferOutQnty;
										$sub_issue_tot +=$total_issue_qnty;
										$sub_issue_amount += $issue_amount;

										$sub_stock_qty += $total_stock_qty;
										$sub_stock_amount += $total_stock_amount;
									}
								}

							}
							$unit_of_measurement[implode(",", $uom_arr)];
							if($show_row_sub_total == true)
							{
								if($pp%2==0) $bgcolor2="#e4e4e4"; else $bgcolor2="#9ab2d6";$pp++;
								?>
								<tr bgcolor="<? echo $bgcolor2;?>" style="font-weight: bold;">
									<td colspan="<? echo $sub_total_col_span;?>" align="right">Total <? echo $unit_of_measurement[implode(",", $uom_arr)];?>&nbsp;</td>
									<?
									if($txt_date_from != "" && $txt_date_to != ""){?>
										<td class="word_wrap_break" align="right"><? echo number_format($sub_opening_qnty,2,'.',''); ?></td>
										<?
									}?>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv,2,'.','');?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue_return,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_trans_in,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv_tot,2,".","");?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv_amount,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_rcv_ret,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_tran_out,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue_tot,2,".","");?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_issue_amount,2,".","");?></td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_stock_qty,2,'.','');?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right"><? echo number_format($sub_stock_amount,2,'.','');?></td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
									<td class="word_wrap_break" align="right">&nbsp;</td>
								</tr>
								<?
							}
						}
						?>
					</table>
				</div>
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



if($action=="report_generate_exel_only")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$store_arr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_id and  b.category_type=2 order by a.store_name",'id','store_name');


	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$cbo_store_wise= str_replace("'","",$cbo_store_wise);
	$cbo_store_name= str_replace("'","",$cbo_store_name);
	$cbo_get_upto= str_replace("'","",$cbo_get_upto);
	$txt_days= str_replace("'","",$txt_days);
	$cbo_get_upto_qnty= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty= str_replace("'","",$txt_qnty);
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);

	if($within_group==1)
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.po_buyer=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.buyer_id=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond="and a.company_id='$pocompany_id'";
	$date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
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
		if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}


	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and d.job_no_prefix_num='$order_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and d.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}

	} else {
		$booking_no_cond="";
	}

	if($cbo_store_wise ==1)
	{
		$selectRcvStore = " a.store_id,";
		$selectTransStore = " b.to_store as store_id,";
		$selectTransOutStore = " b.from_store as store_id,";
		$groupByRcvStore = " a.store_id,";
		$groupByTransStore = " b.to_store,";
		$groupByTransOutStore = " b.from_store,";

		if($cbo_store_name)
		{
			$rcvStoreCond = " and e.store_id = $cbo_store_name";
			$TransStoreCond = " and b.to_store = $cbo_store_name";
		}
	}

	if($within_group>0)
	{
		$withinGroupCond = "and d.within_group=$within_group";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$to_trans_date_cond = " and e.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond2 = " and a.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond3 = " and c.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond4 = " and f.transaction_date <= '".$txt_date_to."'";
	}

	$con = connect();
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (888,889)");
    oci_commit($con);

	$sql = "SELECT b.id,d.within_group, f.id as batch_id, f.batch_no,f.extention_no,1 as type, sum(g.batch_qnty) as batch_qty, min(a.receive_date) as mrr_date, a.company_id, c.po_breakdown_id,  b.body_part_id, b.fabric_description_id, $selectRcvStore b.uom, h.color as color_id,b.dia_width_type, b.width, b.gsm, (c.quantity) as quantity , sum(e.cons_amount) as amount,0 as is_transfered,0 as from_order_id, a.receive_basis, (e.order_amount) as order_amount, e.transaction_date
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master h
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.trans_id=e.id and e.prod_id=h.id and b.batch_id=f.id and f.id=g.mst_id and CAST(g.po_id AS VARCHAR2(4000)) =b.order_id and g.body_part_id = b.body_part_id and d.sales_booking_no=f.booking_no and f.status_active=1 and g.status_active=1 and a.entry_form=225 and c.entry_form=225 and b.is_sales=1 and c.is_sales=1 and a.company_id = $company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $rcvStoreCond $year_search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in (10,14) $to_trans_date_cond
	group by  b.id,d.within_group,f.id, f.batch_no,f.extention_no,a.company_id,c.po_breakdown_id, b.body_part_id,b.fabric_description_id, $groupByRcvStore b.uom, e.order_amount, h.color, b.dia_width_type, b.width, a.item_category, b.gsm,c.quantity, a.receive_basis, e.transaction_date
	union all
	select  b.id,d.within_group,f.id as batch_id, f.batch_no,f.extention_no,2 as type, sum(g.batch_qnty) as batch_qty, min(a.transfer_date) as mrr_date, a.company_id,a.to_order_id as po_breakdown_id, g.body_part_id,b.feb_description_id as fabric_description_id, $selectTransStore b.uom, c.color as color_id,b.dia_width_type, b.dia_width as width, b.gsm, (b.transfer_qnty) as quantity , sum(e.cons_amount) as amount,1 as is_transfered,a.from_order_id , 0 as receive_basis,  (e.order_amount) as order_amount, e.transaction_date
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, fabric_sales_order_mst d , inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master c 
	where a.id=b.mst_id and a.to_order_id=d.id and b.to_trans_id=e.id and e.prod_id=c.id and b.to_batch_id=f.id and f.id=g.mst_id AND g.body_part_id = b.body_part_id and f.status_active=1 and g.status_active=1  and a.company_id=$company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $TransStoreCond $year_search_cond and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_trans_date_cond
	group by  b.id,d.within_group,f.id,f.batch_no,f.extention_no,a.company_id,a.to_order_id, e.order_amount,b.from_prod_id, g.body_part_id, b.feb_description_id, $groupByTransStore b.uom, c.color, b.dia_width_type, b.dia_width, b.gsm,b.transfer_qnty,a.from_order_id, e.transaction_date
	order by uom,po_breakdown_id";
	//and d.id=f.sales_order_id 
	//echo $sql;//die;
	$nameArray=sql_select($sql);
	$ref_key="";$open=0;
	foreach($nameArray as $row)
	{
		$all_batch_id[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$row[csf("prod_id")]=$row[csf("batch_id")]; //this is intensional so don't change it
		if($row[csf("quantity")] > 0)
		{
			$fso_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			if($cbo_store_wise ==1)
			{
				$sub_total_col_span = 22;
				$ref_key =$row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("store_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}else{
				$sub_total_col_span = 21;
				$ref_key = $row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{

				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($row[csf("type")] == 1)
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
					}
					else
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_in"] += $row[csf("order_amount")];
						}else{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["rcv_in"] += $row[csf("order_amount")];
						}

						if($row[csf("type")] == 1)
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["opening"] += $row[csf("quantity")];
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += 0;//$row[csf("order_amount")];
						}else{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] = 0;
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] = 0;
						}
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
			}
			else
			{
				if($row[csf("type")] == 1)
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
				}else{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
				}

				if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
				}
				else
				{
					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
				}
			}
		}
	}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 888, 1,$all_batch_id, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 889, 1,$fso_id_arr, $empty_arr);
	oci_commit($con);

	$all_batch_ids= implode(",", $all_batch_id);
	$batch_conds=" and mst_id in($all_batch_ids)";
	if($db_type==2 && count($all_batch_id)>999)
	{
		$batch_conds="";
		$chnk=array_chunk($all_batch_id, 999);
		foreach($chnk as $val)
		{
			$ids=implode(",", $val);
			if(!$batch_conds)$batch_conds.=" and ( mst_id in($ids) ";
			else $batch_conds.=" or  mst_id in($ids) ";
		}
		$batch_conds.=")";

	}
	$batch_sql="SELECT a.mst_id,  a.item_description,  a.batch_qnty, a.body_part_id FROM pro_batch_create_dtls a, GBL_TEMP_ENGINE b Where status_active=1 and a.mst_id=b.ref_val and b.user_id=$user_id and b.entry_form=888 "; //$batch_conds 
	foreach(sql_select($batch_sql) as $vals)
	{
		$items=explode(",",$vals[csf("item_description")]);
		$items_st=trim($items[0]);
		$batch_qnty_arr_new[$vals[csf("mst_id")]][$vals[csf("body_part_id")]][$items_st]+=$vals[csf("batch_qnty")];
	}
	/*echo "<pre>";
	print_r($batch_qnty_arr_new);die;*/


	$fso_id_arr = array_filter($fso_id_arr);
	if(!empty($fso_id_arr))
	{
		$fso_ids = implode(",", array_filter($fso_id_arr));
		$fsoCond = $all_fso_cond = "";
		$fsoCond2 = $all_fso_cond2 = "";
		$fsoCond3 = $all_fso_cond3 = "";
		if($db_type==2 && count($fso_id_arr)>999)
		{
			$fso_id_arr_chunk=array_chunk($fso_id_arr,999) ;
			foreach($fso_id_arr_chunk as $chunk_arr)
			{
				$fsoCond.=" a.id in(".implode(",",$chunk_arr).") or ";
				$fsoCond2.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				$fsoCond3.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_fso_cond.=" and (".chop($fsoCond,'or ').")";
			$all_fso_cond2.=" and (".chop($fsoCond2,'or ').")";
			$all_fso_cond3.=" and (".chop($fsoCond3,'or ').")";
		}
		else
		{
			$all_fso_cond=" and a.id in($fso_ids)";
			$all_fso_cond2=" and c.po_breakdown_id in($fso_ids)";
			$all_fso_cond3=" and a.from_order_id in($fso_ids)";
		}

		$fso_ref_sql = sql_select("SELECT a.company_id,a.po_buyer,a.po_company_id,a.within_group, a.id as sales_id, a.job_no,a.season,a.sales_booking_no,a.style_ref_no,a.buyer_id,a.season,a.sales_booking_no,a.booking_type,a.booking_without_order,a.booking_entry_form, b.determination_id, b.gsm_weight, b.width_dia_type, b.dia, b.cons_uom, b.color_id, b.color_type_id, b.finish_qty, b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b, GBL_TEMP_ENGINE c where a.id = b.mst_id and a.id=c.ref_val and c.user_id=$user_id and c.entry_form=889 and a.status_active =1 and b.status_active =1"); //$all_fso_cond

		$fso_ref_data_arr=array();$fso_ref_data=array();
		$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		foreach($fso_ref_sql as $row)
		{
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['book_qnty'] +=$row[csf('finish_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['fso_qnty'] +=$row[csf('grey_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['color_type'] .=$row[csf('color_type_id')].",";

			$fso_ref_data[$row[csf('sales_id')]]["within_group"] = $row[csf('within_group')];
			$fso_ref_data[$row[csf('sales_id')]]["po_company_id"] = $row[csf('po_company_id')];

			if($row[csf('within_group')]==1)
			{
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('po_buyer')];
			}else {
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('buyer_id')];
			}

			$fso_ref_data[$row[csf('sales_id')]]["style_ref_no"] = $row[csf('style_ref_no')];
			$fso_ref_data[$row[csf('sales_id')]]["season"] = $row[csf('season')];
			$fso_ref_data[$row[csf('sales_id')]]["job_no"] = $row[csf('job_no')];
			$fso_ref_data[$row[csf('sales_id')]]["sales_booking_no"] = $row[csf('sales_booking_no')];

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

			$salesTypeData[$row[csf("sales_id")]]['booking_type'] = $bookingType;
		}

		unset($fso_ref_sql);

		$delivery_qnty_sql = sql_select("SELECT b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia, $selectRcvStore d.color color_id, a.transaction_date
			from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a, GBL_TEMP_ENGINE e
			where a.company_id=$company_name and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id  $rcvStoreCond and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 $to_trans_date_cond2 and c.po_breakdown_id=e.ref_val and e.user_id=$user_id and e.entry_form=889 group by b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id,d.gsm, $groupByRcvStore d.dia_width,d.color,a.transaction_date"); //$all_fso_cond2 

		foreach ($delivery_qnty_sql as $row)  
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
					}
					else
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise==1)
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
				}
				else
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
				}
			}
		}
		unset($delivery_qnty_sql);

		$issue_return_sql = sql_select("SELECT a.company_id, c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,  b.uom, f.color as color_id,b.dia_width_type, b.width, b.gsm, $selectRcvStore sum(c.quantity) as quantity , sum(e.order_amount) as amount, e.transaction_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, inv_transaction e, product_details_master f, GBL_TEMP_ENGINE d where a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=e.id and e.prod_id=f.id and a.entry_form=233 and c.entry_form=233 and b.is_sales=1 and c.is_sales=1 and a.company_id=$company_name  $rcvStoreCond $to_trans_date_cond and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=889 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id,c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,b.uom, f.color, $groupByRcvStore b.dia_width_type, b.width, a.item_category, b.gsm, e.transaction_date"); //$all_fso_cond2
		foreach ($issue_return_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($issue_return_sql);

		$transfered_fabric_sql = sql_select("SELECT a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id, b.feb_description_id as fabric_description_id, $selectTransOutStore b.uom, d.color as color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity, sum(c.order_amount) as amount, c.transaction_date
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d, GBL_TEMP_ENGINE e
			where a.id=b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.transaction_type=6 and a.entry_form in(230) and a.company_id = $company_name and a.from_order_id=e.ref_val and e.user_id=$user_id and e.entry_form=889 $to_trans_date_cond3 and a.status_active =1 and a.is_deleted =0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
			group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, $groupByTransOutStore b.uom, d.color, b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm, c.transaction_date"); //$all_fso_cond3

		foreach ($transfered_fabric_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}

				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["out"] += $row[csf("amount")];
						}
						else
						{

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["out"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($transfered_fabric_sql);

		$rcv_return_sql = sql_select("SELECT c.po_breakdown_id, c.entry_form , c.quantity, c.is_sales, d.store_id, d.batch_id, e.detarmination_id, e.gsm, e.dia_width, d.body_part_id, d.width_type, e.color,d.uom, f.order_amount as amount, f.transaction_date
			from order_wise_pro_details c, inv_finish_fabric_issue_dtls d, product_details_master e, inv_transaction f, GBL_TEMP_ENGINE b
			where c.dtls_id = d.id and d.prod_id = e.id and c.trans_id=f.id and c.entry_form = 287 and c.po_breakdown_id=b.ref_val and b.entry_form=889 and b.user_id=$user_id $to_trans_date_cond4 and c.is_sales=1 and e.item_category_id =2 and c.status_active =1 and c.is_deleted = 0 and d.status_active =1 and d.is_deleted = 0 and f.status_active =1 and f.is_deleted = 0"); //$all_fso_cond2



		foreach ($rcv_return_sql as $row)
		{

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["rcv_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}

		$prod_id_arr = array_filter($prod_id_arr);
		if(count($prod_id_arr)>0)
		{
			$prod_ids = implode(",", $prod_id_arr);
			$prodCond = $all_prod_id_cond = "";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$prod_id_arr_chunk=array_chunk($prod_id_arr,999) ;
				foreach($prod_id_arr_chunk as $chunk_arr)
				{
					$prodCond.=" b.pi_wo_batch_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
			}
			else
			{
				$all_prod_id_cond=" and b.pi_wo_batch_no in($prod_ids)";
			}
		}

		$date_array=array();
		$dateRes_date="SELECT c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit, min(b.transaction_date) as min_date, max(b.transaction_date) as max_date 
		from product_details_master a, inv_transaction b,order_wise_pro_details c, GBL_TEMP_ENGINE d
		where a.id=b.prod_id and b.id=c.trans_id and b.is_deleted=0 and b.status_active=1 and b.item_category=2 and b.transaction_type in (2,6) and c.trans_type in (2,6)
		and b.pi_wo_batch_no=d.ref_val and d.user_id=$user_id and d.entry_form=888
		group by c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit "; //$all_prod_id_cond
		$result_dateRes_date = sql_select($dateRes_date);
		foreach($result_dateRes_date as $row)
		{
			if(!$row[csf("pi_wo_batch_no")])$row[csf("pi_wo_batch_no")]=0;
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['max_date']=$row[csf("max_date")];
			$avg_rate_arr[$row[csf("pi_wo_batch_no")]] = $row[csf("avg_rate_per_unit")];
		}
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array_deter);
 


	if($cbo_store_wise ==1)
	{
		$sub_total_col_span = 23;
	}else{
		$sub_total_col_span = 22;
	}
	if($txt_date_from != "" && $txt_date_to != "")
	{
		$receive_col_span = 7;
	}else{
		$receive_col_span = 6;
	}

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (888,889)");
	oci_commit($con);
	//echo "Here";die;

	//ob_start();
	$html = "";
	

	/* <fieldset style="width:3310px;">*/
	$html .= '<table width="3410" cellspacing="0" cellpadding="0" border="0" rules="all" >
		<tr class="form_caption">
			<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold">'. $report_title.'</td>
		</tr>
		<tr class="form_caption">
			<td colspan="28" align="center">'. $company_library[$company_name].'</td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3330" class="rpt_table" >
		<thead>
			<tr>
				<th width="30" rowspan="2">SL</th>
				<th width="60" rowspan="2">Company</th>
				<th width="70" rowspan="2">LC Company</th>
				<th width="80" rowspan="2">PO Buyer</th>
				<th width="100" rowspan="2">Style Ref.</th>
				<th width="80" rowspan="2">Season</th>
				<th width="50" rowspan="2">Within<br> Group</th>
				<th width="100" rowspan="2">Booking No</th>
				<th width="80" rowspan="2">Booking Type</th>
				<th width="110" rowspan="2">FSO</th>
				<th width="100" rowspan="2">Batch No</th>
				<th width="100" rowspan="2">Ext. No</th>
				<th width="100" rowspan="2">Batch Qty.</th>';
				if($cbo_store_wise ==1)
				{
					$html .= '<th width="100" rowspan="2">Store Name</th>';
				}
				$html .='<th colspan="9">Fabric Details</th>
					<th colspan="'. $receive_col_span . '">Receive Details</th>
					<th colspan="6">Issue Details</th>
					<th colspan="5">Stock Details</th>
				</tr>
				<tr>
					<th width="80">Body Part</th>
					<th width="100">Construction</th>
					<th width="100">Composition</th>
					<th width="80">GSM</th>
					<th width="80">F/Dia</th>
					<th width="80">Dia Type</th>
					<th width="80">Color Type</th>
					<th width="120">Fab. Color</th>
					<th width="50">UOM</th>';

					if($txt_date_from != "" && $txt_date_to != "")
					{
						$html .= '<th width="100">Opening Stock</th>';
					}
					$html .= '<th width="80">Receive</th>
						<th width="80">Issue Ret.</th>
						<th width="80">Trans In</th>
						<th width="80">Total Rcv</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Receive Amount</th>

						<th width="80">Issue</th>
						<th width="80">Receive Rtn.</th>
						<th width="80">Trans Out</th>
						<th width="80">Total Issue</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Issue Amount</th>

						<th width="80">Stock</th>
						<th width="80">Avg. Rate</th>
						<th width="80">Stock Amount</th>
						<th width="50">Age (days)</th>
						<th width="50">DOH</th>
					</tr>
				</thead>
			</table>';
			/*<div style="width:3350px; overflow-y:scroll; max-height:350px;" id="scroll_body"> */
	$html .= '<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3330" class="rpt_table" id="tbl_list_search">';	
			$i=1;
			$pp=1;
			foreach ($source_arr as $uom_id => $uom_data)
			{
				$uom_arr=array();
				$sub_rcv=$sub_trans_in=$sub_iss_ret=$sub_rcv_tot=$sub_rcv_amount=$sub_issue=$sub_issue_return=$sub_rcv_ret=$sub_tran_out=$sub_issue_tot=$sub_issue_amount=$sub_stock_qty=$sub_stock_amount=$sub_opening_qnty=0;
				foreach ($uom_data as $po_breakdown_id => $po_breakdown_data)
				{
					$y=1; $show_row_sub_total = false;
					$opening_balance_qnty=0;
					foreach ($po_breakdown_data as $prod_ref => $row)
					{
						$sales_prod_key_arr=explode("**", $prod_ref);
						$company_id = $sales_prod_key_arr[0];
						$prod_id = $sales_prod_key_arr[1];//here prod id is equal to batch id

						$fabric_description_id = $sales_prod_key_arr[2];
						$gsm = $sales_prod_key_arr[3];
						$width = $sales_prod_key_arr[4];
						$body_part_id = $sales_prod_key_arr[5];
						$dia_width_type = $sales_prod_key_arr[6];
						$color_id  = $sales_prod_key_arr[7];
						if($cbo_store_wise ==1)
						{
							$batch_no  =$sales_prod_key_arr[9];
							$ext_no  = $sales_prod_key_arr[10];
							//$batch_qty  = $sales_prod_key_arr[11];
							$within_group  = $yes_no[$sales_prod_key_arr[12]];
						}
						else
						{
							$batch_no  =$sales_prod_key_arr[8];
							$ext_no  = $sales_prod_key_arr[9];
							//$batch_qty  = $sales_prod_key_arr[10];
							$within_group  = $yes_no[$sales_prod_key_arr[11]];
						}
						
						
						$booking_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['book_qnty'];
						$fso_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['fso_qnty'];
						$color_type_id = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['color_type'];

						$daysOnHand = datediff("d",$date_array[$po_breakdown_id][$prod_id]['max_date'],date("Y-m-d"));
						if(!$daysOnHand)$daysOnHand = datediff("d",$date_array[$po_breakdown_id][0]['max_date'],date("Y-m-d"));
						if($cbo_store_wise ==1)
						{
							$store_id  = $sales_prod_key_arr[8];
							$is_transfered  = $sales_prod_key_arr[9];
							$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_qnty"];
							$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_amount"];

							$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
							$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

							$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
							$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

							$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
							$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

							$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rcv_qnty"];

							$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_qnty"];

							$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_trans_out"];

							$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rec_ret_qnty"];

							$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_ret_qnty"];

							$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_balance_amt"];


							//===amount for title===
							$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_in"];
							$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss"];
							$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["out"];
							$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_ret"];
							$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss_ret"];
							//======


						}
						else
						{
							$is_transfered  = $sales_prod_key_arr[8];
							$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_qnty"];
							$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_amount"];
							$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
							$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

							$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
							$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

							$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
							$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

							$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rcv_qnty"];
							$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_qnty"];
							$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_trans_out"];
							$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rec_ret_qnty"];
							$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_ret_qnty"];

							$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_balance_amt"];


							//===amount for title===
							$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_in"];
							$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss"];
							$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["out"];
							$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_ret"];
							$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss_ret"];
							//======
						}

						$total_rcv_qnty = $row['rcv_qnty']+$issue_return_qnty+$row['trans_in_qnty'];

						$rcv_amount = $row['rcv_amount'] + $issue_return_amount + $row['trans_in_amount'];
						if($total_rcv_qnty > 0)
						{
							$rcv_avg_rate = $rcv_amount/$total_rcv_qnty;
						}else{
							$rcv_avg_rate = 0;
						}


						$total_issue_qnty = $delivery_qnty+$rcv_ret_qnty+ $transferOutQnty;
						$issue_amount = $delivery_amount+$rcv_ret_amount+ $transferOutAmount;
						if($total_issue_qnty>0)
						{
							$issue_avg_rate = $issue_amount/$total_issue_qnty;
						}else{
							$issue_avg_rate = 0;
						}

						$opening_bal = ($opening_balance_qnty+$open_iss_ret_qnty)-($opening_issue_qnty+$opening_trans_out_qnty+$opening_recv_rtn_qnty);
						$opening_title = "Receive=$opening_balance_qnty,Issue Return=$open_iss_ret_qnty\n Issue=$opening_issue_qnty,Trans. Out=$opening_trans_out_qnty";

						$total_stock_qty =  $opening_bal + ($total_rcv_qnty-$total_issue_qnty);
						if($user_id != 276){
							$total_stock_qty = ($total_stock_qty>0)?$total_stock_qty:0.00;
						}

						$total_stock_amount = ($opening_balance_amount + $rcv_amount) - $issue_amount;
						$total_stock_amount = ($total_stock_amount>0)?$total_stock_amount:0.00;
						if($total_stock_qty>0)
						{
							$total_stock_avg_rate = $total_stock_amount/$total_stock_qty;
						}

						$color_type_ids="";
						$color_type_arr =  array_filter(array_unique(explode(",",chop($color_type_id,","))));
						foreach ($color_type_arr as $val)
						{
							if($color_type_ids == "") $color_type_ids = $color_type[$val]; else $color_type_ids .= ", ". $color_type[$val];
						}

						if ((($cbo_get_upto_qnty == 1 && $total_stock_qty > $txt_qnty) || ($cbo_get_upto_qnty == 2 && $total_stock_qty < $txt_qnty) || ($cbo_get_upto_qnty == 3 && $total_stock_qty >= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $total_stock_qty <= $txt_qnty) || ($cbo_get_upto_qnty == 5 && $total_stock_qty == $txt_qnty) || $cbo_get_upto_qnty == 0) && (($cbo_get_upto == 1 && $daysOnHand > $txt_days) || ($cbo_get_upto == 2 && $daysOnHand < $txt_days) || ($cbo_get_upto == 3 && $daysOnHand >= $txt_days) || ($cbo_get_upto == 4 && $daysOnHand <= $txt_days) || ($cbo_get_upto == 5 && $daysOnHand == $txt_days) || $cbo_get_upto == 0))
						{
							if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$fabric_span = $details_row_span_arr[$uom_id."##".$po_breakdown_id];

							$pop_ref = $po_breakdown_id."__".$prod_id."__".$fabric_description_id."__".$gsm."__".$width."__".$body_part_id."__".$dia_width_type."__".$color_id."__".$uom_id;
							$transfered = ($is_transfered==1)?"<strong style='color:red'>[T]</strong>":"";

							$mrr_date = "";
							$mrr_date =$row['mrr_date'];
							$ageOfDays = datediff("d", $mrr_date, date("Y-m-d"));

							$html .= '<tr id="tr_'. $m .'">
								<td>'. $i.'</td>
								<td>'. $company_arr[$company_id].'</td>
								<td>'. $company_arr[$fso_ref_data[$po_breakdown_id]["po_company_id"]].'</td>
								<td>'. $buyer_arr[$fso_ref_data[$po_breakdown_id]["po_buyer"]].'</td>
								<td>'. $fso_ref_data[$po_breakdown_id]["style_ref_no"].'</td>
								<td>'. $fso_ref_data[$po_breakdown_id]["season"].'</td>
								<td>'. $within_group.'</td>
								<td>'.  $fso_ref_data[$po_breakdown_id]["sales_booking_no"].'</td>
								<td>'. $salesTypeData[$po_breakdown_id]['booking_type'].'</td>
								<td>'. $fso_ref_data[$po_breakdown_id]["job_no"].'</td>';
								
								$batch_qty=number_format($batch_qnty_arr_new[$prod_id][$body_part_id][trim($constructtion_arr[$fabric_description_id])],2);
								
								$html .= '<td width="100">'. $batch_no.'</td>
								<td>'. $ext_no.'</td>
								<td>'. $batch_qty.'</td>';
								if($cbo_store_wise ==1)
								{
									$store_id = $sales_prod_key_arr[8];
									$html .= '<td>'. $store_arr[$store_id].'</td>';
								}
								
								$html .= '<td>'. $body_part[$body_part_id] .'</td>
								<td>'. $constructtion_arr[$fabric_description_id].'</td>
								<td>'. $composition_arr[$fabric_description_id].'</td>
								<td>'. $gsm.'</td>
								<td>'. $width.'</td>
								<td>'. $fabric_typee[$dia_width_type].'</td>
								<td>'. $color_type_ids.'</td>
								<td>'. $color_arr[$color_id].'</td>
								<td>'. $unit_of_measurement[$uom_id].'</td>';
								if($txt_date_from != "" && $txt_date_to != "")
								{
									$html .='<td>'. number_format($opening_bal,2). '</td>';
									
								}
								$html .='<td>'. number_format($row['rcv_qnty'],2,'.','').'</td>
									<td>'. number_format($issue_return_qnty,2,'.','').'</td>
									<td>'. number_format($row['trans_in_qnty'],2,'.','').'</td>
									<td>'. number_format($total_rcv_qnty,2,'.','').'</td>
									<td>'. number_format($rcv_avg_rate,2,'.','').'</td>
									<td>'. number_format($rcv_amount,2,'.','').'</td>
									<td>'. number_format($delivery_qnty,2,'.','').'</td>
									<td>'. number_format($rcv_ret_qnty,2,'.','') .'</td>
									<td>'. number_format($transferOutQnty,2,'.','').'</td>
									<td>'. number_format($total_issue_qnty,2,'.','').'</td>
									<td>'. number_format($issue_avg_rate,2,'.','').'</td>
									<td>'. number_format($issue_amount,2,'.','').'</td>
									<td>'. number_format($total_stock_qty,2,'.','').'</td>
									<td>'. number_format($total_stock_avg_rate,2,'.','').'</td>
									<td>'. number_format($total_stock_amount,2,'.','').'</td>
									<td>'. $ageOfDays.'</td>
									<td>'. $daysOnHand.'</td>
								</tr>';
								
								$show_row_sub_total = true;
								$y++;$m++;$i++;
								$uom_arr[$uom_id]=$uom_id;
								$sub_rcv += $row['rcv_qnty'];
								$sub_trans_in += $row['trans_in_qnty'];

								$sub_opening_qnty += $opening_bal;

								$sub_iss_ret = 0;
								$sub_rcv_tot +=$total_rcv_qnty;
								$sub_rcv_amount += $rcv_amount;
								$sub_issue  += $delivery_qnty;
								$sub_issue_return  += $issue_return_qnty;
								$sub_rcv_ret +=$rcv_ret_qnty;
								$sub_tran_out +=$transferOutQnty;
								$sub_issue_tot +=$total_issue_qnty;
								$sub_issue_amount += $issue_amount;

								$sub_stock_qty += $total_stock_qty;
								$sub_stock_amount += $total_stock_amount;
						}
					}
				}
				$unit_of_measurement[implode(",", $uom_arr)];
				if($show_row_sub_total == true)
				{
					//if($pp%2==0) $bgcolor2="#e4e4e4"; else $bgcolor2="#9ab2d6";$pp++;
					$html .= '<tr>
						<td colspan="'. $sub_total_col_span. '" align="right">Total '. $unit_of_measurement[implode(",", $uom_arr)] . '&nbsp;</td>';
						if($txt_date_from != "" && $txt_date_to != "")
						{
							$html .= '<td>'. number_format($sub_opening_qnty,2,'.','').'</td>';
						}
						$html .=
						'<td>'. number_format($sub_rcv,2,'.','').'</td>
						<td>'. number_format($sub_issue_return,2,".","").'</td>
						<td>'. number_format($sub_trans_in,2,".","").'</td>
						<td>'. number_format($sub_rcv_tot,2,".","").'</td>
						<td>&nbsp;</td>
						<td>'. number_format($sub_rcv_amount,2,".","").'</td>
						<td>'. number_format($sub_issue,2,".","").'</td>
						<td>'. number_format($sub_rcv_ret,2,".","").'</td>
						<td>'. number_format($sub_tran_out,2,".","").'</td>
						<td>'. number_format($sub_issue_tot,2,".","").'</td>
						<td>&nbsp;</td>
						<td>'. number_format($sub_issue_amount,2,".","").'</td>
						<td>'. number_format($sub_stock_qty,2,'.','').'</td>
						<td>&nbsp;</td>
						<td>'. number_format($sub_stock_amount,2,'.','').'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';
				}
			}
			$html .= '</table>';
		
			/*</div>*/
	
	//$html = ob_get_contents();
	//ob_clean();
	foreach (glob("bwffsr_*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='bwffsr_'.$user_id."_".$name.".xls";
	
	/*$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($html);

	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save($filename); */  

	$temporary_html_file = './tmp_html/' . time() . '.html';
	file_put_contents($temporary_html_file, $html);
	$reader = IOFactory::createReader('Html');

	$spreadsheet = $reader->load($temporary_html_file);
	$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save($filename);
	header('Content-Type: application/x-www-form-urlencoded');
	header('Content-Transfer-Encoding: Binary');
	header("Content-disposition: attachment; filename=\"".$filename."\"");

	//readfile($filename);
	unlink($temporary_html_file);
	//unlink($filename);

	echo "$filename####$filename"; 
	exit();
}



if($action=="receive_finish_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$ref_data_arr =  explode("__", $ref_data);
	$po_breakdown_id = $ref_data_arr[0];
	$prod_id = $ref_data_arr[1];
	$fabric_description_id = $ref_data_arr[2];
	$gsm = $ref_data_arr[3];
	$width = $ref_data_arr[4];
	$body_part_id = $ref_data_arr[5];
	$dia_width_type = $ref_data_arr[6];
	$color_id = $ref_data_arr[7];
	$uom_id = $ref_data_arr[8];

	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			$(".flt").css("display","none");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
		}
		var tableFilters =
		{
			col_operation: {
				id: ["value_total_batch","value_total_rcv",],
				col: [11,12],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
			}
		}
	</script>
	<fieldset style="width:980px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Finish Fabrics Received Info</b>
				</caption>
				<thead>
					<th width="20">SL</th>
					<th width="60">Receive Date</th>
					<th width="110">Receive ID</th>
					<th width="80">Batch No</th>
					<th width="50">Ext. No</th>
					<th width="50">Sales Order No</th>
					<th width="70">Booking No</th>
					<th width="60">Batch Date</th>
					<th width="60">Batch Against</th>
					<th width="50">Batch For</th>

					<th width="60">Color</th>
					<th width="80">Batch Quantity</th>
					<th width="70">Receive Qty.</th>
					<th width="70">No of Roll</th>
					<th width="60">Remarks</th>
				</thead>
			</table>
			<div style="width:988px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$sql_data="SELECT a.id as rcv_id,a.recv_number,a.receive_date,b.batch_id, d.sales_booking_no,c.po_breakdown_id, (c.quantity) as quantity,b.fabric_description_id, b.gsm,b.width,b.body_part_id,b.dia_width_type,b.color_id,d.job_no_prefix_num ,b.no_of_roll from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, inv_transaction e where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.trans_id=e.id and a.entry_form=225 and c.entry_form=225 and b.is_sales=1 and c.is_sales=1 and a.company_id=$companyID and c.po_breakdown_id=$po_breakdown_id and b.batch_id=$prod_id and b.fabric_description_id=$fabric_description_id and b.gsm=$gsm and b.width='$width' and b.body_part_id=$body_part_id and b.dia_width_type=$dia_width_type and b.color_id=$color_id and b.uom = $uom_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in (10,14)";

						$source_array=sql_select($sql_data);
						foreach($source_array as $row)
						{
							$batch_id_arr[$row[csf('batch_id')]] =	$row[csf('batch_id')];
						}
						$batch_id_arr = array_filter($batch_id_arr);
						$batch_ids = implode(",", $batch_id_arr);
						if($batch_ids != "")
						{
							$sql_batch= sql_select("SELECT a.id as batch_id, a.batch_no, a.booking_no, a.extention_no, a.batch_for, a.batch_against, a.batch_date, b.body_part_id, b.width_dia_type, c.detarmination_id, c.gsm, c.dia_width, a.color_id , sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b , product_details_master c where a.id = b.mst_id and b.prod_id =  c.id and a.id in ($batch_ids) group by a.id, a.batch_no, a.booking_no,a.extention_no, a.batch_for, a.batch_against, a.batch_date, b.body_part_id, b.width_dia_type,c.detarmination_id, c.gsm, c.dia_width, a.color_id");
							foreach ($sql_batch as $val)
							{
								$batch_qnty_arr[$val[csf("batch_id")]][$val[csf("detarmination_id")]][$val[csf("gsm")]][$val[csf("dia_width")]][$val[csf("body_part_id")]][$val[csf("width_dia_type")]]["qnty"] += $val[csf("batch_qnty")];

								$batch_data_arr[$val[csf("batch_id")]]["batch_against"] = $batch_against[$val[csf("batch_against")]];
								$batch_data_arr[$val[csf("batch_id")]]["batch_date"] = $val[csf("batch_date")];
								$batch_data_arr[$val[csf("batch_id")]]["batch_no"] = $val[csf("batch_no")];
								$batch_data_arr[$val[csf("batch_id")]]["batch_for"] = $batch_for[$val[csf("batch_for")]];
								$batch_data_arr[$val[csf("batch_id")]]["extention_no"] = $val[csf("extention_no")];
								$batch_data_arr[$val[csf("batch_id")]]["booking_no"] = $val[csf("booking_no")];
								$batch_data_arr[$val[csf("batch_id")]]["color_id"] = $val[csf("color_id")];
							}
						}


						foreach($source_array as $row)
						{
							$data_array[$row[csf("rcv_id")]][$row[csf("batch_id")]]["rcv_qnty"] += 	$row[csf("quantity")];
							$data_array[$row[csf("rcv_id")]][$row[csf("batch_id")]]["no_of_roll"] += 	$row[csf("no_of_roll")];
							$data_array[$row[csf("rcv_id")]][$row[csf("batch_id")]]["batch_qnty"] += $batch_qnty_arr[$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]]["qnty"];
							$rcv_data_ref[$row[csf("rcv_id")]]["recv_number"] = $row[csf("recv_number")];
							$rcv_data_ref[$row[csf("rcv_id")]]["receive_date"] = $row[csf("receive_date")];
							$rcv_data_ref[$row[csf("rcv_id")]]["sales_booking_no"] = $row[csf("sales_booking_no")];
							$rcv_data_ref[$row[csf("rcv_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
						}

						$i=1;
						foreach( $data_array as $rcv_id => $rcv_data)
						{
							foreach ($rcv_data as $batch_id => $row)
							{
								$receive_date = $rcv_data_ref[$rcv_id]["receive_date"];
								$recv_number = $rcv_data_ref[$rcv_id]["recv_number"];
								$booking_no = $rcv_data_ref[$rcv_id]["sales_booking_no"];
								$sales_order_no = $rcv_data_ref[$rcv_id]["job_no_prefix_num"];
								$batch_no = $batch_data_arr[$batch_id]["batch_no"];
								$batch_date = $batch_data_arr[$batch_id]["batch_date"];
								$batch_for = $batch_data_arr[$batch_id]["batch_for"];
								$extention_no = $batch_data_arr[$batch_id]["extention_no"];
								$batch_against = $batch_data_arr[$batch_id]["batch_against"];
								$color_id = $batch_data_arr[$batch_id]["color_id"];

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($row["rcv_qnty"]>0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="20"><? echo $i;?></td>
										<td width="60"><p><? echo change_date_format($receive_date); ?></p></td>
										<td width="110"><? echo $recv_number;?></td>
										<td width="80"><? echo $batch_no;?></td>
										<td width="50"><? echo $extention_no;?></td>
										<td width="50"><? echo $sales_order_no;?></td>
										<td width="70"><? echo $booking_no; ?></td>
										<td width="60"><p style="word-wrap: break-word;word-break: break-all;"><? echo change_date_format($batch_date);?></p></td>
										<td width="60"><? echo $batch_against;?></td>
										<td width="50"><? echo $batch_for;?></td>

										<td width="60"><p style="word-wrap: break-word;word-break: break-all;"><? echo $color_arr[$color_id]; ?></p></td>
										<td width="80" align="right"><? echo number_format($row["batch_qnty"],2,'.','');?></td>
										<td width="70" align="right"><? echo number_format($row["rcv_qnty"],2,'.','');?></td>
										<td width="70" align="right"><? echo number_format($row["no_of_roll"],0,'.','');?></td>
										<td width="60">&nbsp;</td>
									</tr>

									<?
									$i++;
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="20"></th>
					<th width="60"></th>
					<th width="110"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="70"></th>
					<th width="60"></th>
					<th width="60"></th>
					<th width="50"></th>
					<th width="60"></th>
					<th width="80" id="value_total_batch" align="right"></th>
					<th width="70" id="value_total_rcv" align="right"></th>
					<th width="70" id="" align="right"></th>
					<th width="60">&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

			if($action=="issue_finish_popup")
			{
				echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
				extract($_REQUEST);

				$ref_data_arr =  explode("__", $ref_data);
				$po_breakdown_id = $ref_data_arr[0];
				$prod_id = $ref_data_arr[1];
				$fabric_description_id = $ref_data_arr[2];
				$gsm = $ref_data_arr[3];
				$width = $ref_data_arr[4];
				$body_part_id = $ref_data_arr[5];
				$dia_width_type = $ref_data_arr[6];
				$color_id = $ref_data_arr[7];
				$uom_id = $ref_data_arr[8];

				$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
				?>
				<script>
					function print_window()
					{
						var w = window.open("Surprise", "#");
						$(".flt").css("display","none");
						var d = w.document.open();
						d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
							'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
						$(".flt").css("display","block");
						d.close();
					}
					var tableFilters =
					{
		//col_10: "none",
		col_operation: {
			id: ["value_issue_qty"],
			col: [12],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
	}

</script>
<fieldset style="width:918px; margin-left:3px">
	<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<div style="width:100%" id="report_container">
		<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_header">
			<caption>
				<b>Finish Fabrics Issue Info</b>
			</caption>
			<thead>
				<th width="20">SL</th>
				<th width="65">Issue Date</th>
				<th width="100">Issue ID</th>
				<th width="50">Batch No</th>
				<th width="50">Ext. No</th>
				<th width="50">Sales Order No</th>
				<th width="60">Booking No</th>
				<th width="60">Batch Date</th>
				<th width="60">Batch Against</th>
				<th width="60">Batch For</th>
				<th width="60">Color</th>
				<th width="60">Batch Quantity</th>
				<th width="60">Issue Qty.</th>
				<th width="60">Remarks</th>
			</thead>
		</table>
		<div style="width:918px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body">
				<tbody>
					<?
					$sql_data=("SELECT a.id issue_id, a.issue_number, a.issue_date, b.batch_id, b.body_part_id bodypart_id,b.width_type,(c.quantity) delivery_qnty, c.is_sales, c.po_breakdown_id, c.prod_id, d.detarmination_id determination_id,d.gsm,d.dia_width dia,  d.color color_id,e.job_no_prefix_num, e.sales_booking_no from inv_issue_master a, inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, fabric_sales_order_mst e where a.id = b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id = e.id and a.entry_form = 224 and b.status_active=1 and c.entry_form=224 and c.is_sales = 1 and c.status_active=1 and a.company_id = $companyID and c.po_breakdown_id = $po_breakdown_id and b.batch_id = $prod_id and d.detarmination_id = $fabric_description_id and d.gsm= $gsm and d.dia_width = $width and b.body_part_id = $body_part_id and b.width_type = $dia_width_type and d.color = $color_id and b.uom = $uom_id");

					$source_array=sql_select($sql_data);
					foreach($source_array as $row)
					{
						$batch_id_arr[$row[csf('batch_id')]] =	$row[csf('batch_id')];
					}
					$batch_id_arr = array_filter($batch_id_arr);
					$batch_ids = implode(",", $batch_id_arr);
					if($batch_ids != "")
					{
						$sql_batch= sql_select("select a.id as batch_id, a.batch_no, a.booking_no, a.extention_no, a.batch_for, a.batch_against, a.batch_date, b.body_part_id, b.width_dia_type, c.detarmination_id, c.gsm, c.dia_width, a.color_id , sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b , product_details_master c where a.id = b.mst_id and b.prod_id =  c.id and a.id in ($batch_ids) group by a.id, a.batch_no, a.booking_no,a.extention_no, a.batch_for, a.batch_against, a.batch_date, b.body_part_id, b.width_dia_type,c.detarmination_id, c.gsm, c.dia_width, a.color_id");
						foreach ($sql_batch as $val)
						{
							$batch_qnty_arr[$val[csf("batch_id")]][$val[csf("detarmination_id")]][$val[csf("gsm")]][$val[csf("dia_width")]][$val[csf("body_part_id")]][$val[csf("width_dia_type")]]["qnty"] += $val[csf("batch_qnty")];

							$batch_data_arr[$val[csf("batch_id")]]["batch_against"] = $batch_against[$val[csf("batch_against")]];
							$batch_data_arr[$val[csf("batch_id")]]["batch_date"] = $val[csf("batch_date")];
							$batch_data_arr[$val[csf("batch_id")]]["batch_no"] = $val[csf("batch_no")];
							$batch_data_arr[$val[csf("batch_id")]]["batch_for"] = $batch_for[$val[csf("batch_for")]];
							$batch_data_arr[$val[csf("batch_id")]]["extention_no"] = $val[csf("extention_no")];
							//$batch_data_arr[$val[csf("batch_id")]]["booking_no"] = $val[csf("booking_no")];
							$batch_data_arr[$val[csf("batch_id")]]["color_id"] = $val[csf("color_id")];
						}
					}


					foreach($source_array as $row)
					{
						$data_array[$row[csf("issue_id")]][$row[csf("batch_id")]]["issue_qnty"] += 	$row[csf("delivery_qnty")];

						$data_array[$row[csf("issue_id")]][$row[csf("batch_id")]]["batch_qnty"] += $batch_qnty_arr[$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]]["qnty"];
						$iss_data_ref[$row[csf("issue_id")]]["issue_number"] = $row[csf("issue_number")];
						$iss_data_ref[$row[csf("issue_id")]]["issue_date"] = $row[csf("issue_date")];
						$iss_data_ref[$row[csf("issue_id")]]["sales_booking_no"] = $row[csf("sales_booking_no")];
						$iss_data_ref[$row[csf("issue_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
					}

					$i=1;
					foreach( $data_array as $issue_id => $iss_data)
					{
						foreach ($iss_data as $batch_id => $row)
						{
							$issue_date = $iss_data_ref[$issue_id]["issue_date"];
							$issue_number = $iss_data_ref[$issue_id]["issue_number"];
							$booking_no = $iss_data_ref[$issue_id]["sales_booking_no"];
							$sales_order_no = $iss_data_ref[$issue_id]["job_no_prefix_num"];

							$batch_no = $batch_data_arr[$batch_id]["batch_no"];
							$batch_date = $batch_data_arr[$batch_id]["batch_date"];
							$batch_for = $batch_data_arr[$batch_id]["batch_for"];
							$extention_no = $batch_data_arr[$batch_id]["extention_no"];
							$batch_against = $batch_data_arr[$batch_id]["batch_against"];
							$color_id = $batch_data_arr[$batch_id]["color_id"];

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($row["issue_qnty"]>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="20"><? echo $i;?></td>
									<td width="65"><? echo change_date_format($issue_date); ?></td>
									<td width="100"><? echo $issue_number;?></td>
									<td width="50"><p style="word-wrap: break-word;word-break: break-all;"><? echo $batch_no;?></p></td>
									<td width="50"><? echo $extention_no;?></td>
									<td width="50"><? echo $sales_order_no;?></td>
									<td width="60"><? echo $booking_no;?></td>
									<td width="60"><? echo change_date_format($batch_date); ?></td>
									<td width="60"><? echo $batch_against;?></td>
									<td width="60"><? echo $batch_for;?></td>
									<td width="60"><p style="word-wrap: break-word;word-break: break-all;"><? echo $color_arr[$color_id];?></p></td>
									<td width="60" align="right"><? echo number_format($row["batch_qnty"],2);?></td>
									<td width="60" align="right"><? echo number_format($row["issue_qnty"],2);?></td>
									<td width="60">&nbsp;</td>
								</tr>

								<?
								$i++;
							}

						}
					}
					?>
				</tbody>
			</table>
		</div>
		<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="report_table_footer">
			<tfoot>
				<th width="20">&nbsp;</th>
				<th width="65"></th>
				<th width="100"></th>
				<th width="50"></th>
				<th width="50"></th>
				<th width="50"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="60" align="right" id="value_issue_qty"></th>
				<th width="60"></th>
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

	$ref_data_arr =  explode("__", $ref_data);
	$po_breakdown_id = $ref_data_arr[0];
	$prod_id = $ref_data_arr[1];
	$fabric_description_id = $ref_data_arr[2];
	$gsm = $ref_data_arr[3];
	$width = $ref_data_arr[4];
	$body_part_id = $ref_data_arr[5];
	$dia_width_type = $ref_data_arr[6];
	$color_id = $ref_data_arr[7];
	$uom_id = $ref_data_arr[8];

	$shelf_arr = return_library_array("select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.company_id='$companyID' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id","floor_room_rack_name");

	$rack_arr = return_library_array("select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.company_id='$companyID' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id","floor_room_rack_name");
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
					<b>Finish Fabrics Transfer In Info</b>
				</caption>
				<thead>
					<th width="30">SL</th>
					<th width="110">Transfer ID</th>
					<th width="80">Transfer Date</th>
					<th width="80">Transfer In Qty</th>
					<th width="50">Roll No</th>
					<th width="50">Rack No</th>
					<th width="50">Shelf</th>
					<th width="">Remarks</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						$sql="SELECT  a.transfer_date, a.transfer_system_id, sum(b.no_of_roll) as no_of_roll, b.to_rack, b.to_shelf,sum(b.transfer_qnty) as quantity ,a.remarks from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.company_id=$companyID and a.to_order_id=$po_breakdown_id and b.to_batch_id=$prod_id and b.feb_description_id=$fabric_description_id and b.gsm=$gsm and b.dia_width='$width' and b.body_part_id=$body_part_id and b.dia_width_type=$dia_width_type and b.color_id= $color_id and b.uom=$uom_id and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.transfer_date, a.transfer_system_id,b.no_of_roll, b.to_rack, b.to_shelf,a.remarks ";

						$dtlsArray=sql_select($sql);
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($row[csf('quantity')]>0)
							{
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
									<td width="80" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
									<td width="50" align="center"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $rack_arr[$row[csf('to_rack')]]; ?>&nbsp;</p></td>
									<td align="center" width="50"><p><? echo $shelf_arr[$row[csf('to_shelf')]]; ?>&nbsp;</p></td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_trans_qty+=$row[csf('quantity')];
								$i++;
							}
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
					<th width="80" align="right"><? echo number_format($tot_trans_qty,2); ?></th>
					<th colspan="4" width="365">&nbsp;</th>
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

	$ref_data_arr =  explode("__", $ref_data);
	$po_breakdown_id = $ref_data_arr[0];
	$prod_id = $ref_data_arr[1];
	$fabric_description_id = $ref_data_arr[2];
	$gsm = $ref_data_arr[3];
	$width = $ref_data_arr[4];
	$body_part_id = $ref_data_arr[5];
	$dia_width_type = $ref_data_arr[6];
	$color_id = $ref_data_arr[7];
	$uom_id = $ref_data_arr[8];

	$shelf_arr = return_library_array("select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.company_id='$companyID' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id","floor_room_rack_name");

	$rack_arr = return_library_array("select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.company_id='$companyID' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id","floor_room_rack_name");


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
					<b>Finish Fabrics Transfer Out Info</b>
				</caption>

				<thead>
					<th width="30">SL</th>
					<th width="80">Transfer Date</th>
					<th width="110">Transfer ID</th>
					<th width="80">Transfer Out Qty</th>
					<th width="50">No of Roll</th>
					<th width="50">Rack No</th>
					<th width="50">Shelf</th>
					<th width="">Remarks</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						$trans_out_sql= "SELECT a.transfer_date, a.transfer_system_id, a.remarks,b.rack, b.shelf, sum(b.transfer_qnty) as quantity, sum(b.no_of_roll) as no_of_roll from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.company_id=$companyID and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.from_order_id=$po_breakdown_id and b.batch_id=$prod_id and b.feb_description_id=$fabric_description_id and b.gsm=$gsm and b.dia_width='$width' and b.body_part_id=$body_part_id and b.dia_width_type=$dia_width_type and b.color_id=$color_id and b.uom = $uom_id group by a.transfer_date, a.transfer_system_id, a.remarks,  b.rack, b.shelf";

						$dtlsArray=sql_select($trans_out_sql);
						foreach($dtlsArray as $row)
						{
							if($row[csf('quantity')]>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
									<td width="80" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
									<td width="50" align="center"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $rack_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
									<td align="center" width="50"><p><? echo $shelf_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
									<td width=""><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_trans_qty+=$row[csf('quantity')];
								$i++;
							}
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
?>
<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_po_company")
{
	if($data == 1){
		echo create_drop_down( "cbo_pocompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Po Company-", $selected, "load_drop_down( 'requires/style_wise_grey_fabric_stock_report_demo_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/style_wise_grey_fabric_stock_report_controller' );" );
	}
	else
	{
		echo create_drop_down( "cbo_pocompany_id", 130, $blank_array,"", 1, "-Po Company-", $selected, "",0,"" );
	}
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_buyer_id','0','0','','0');\n";
	exit();
}
if ($action == "load_drop_down_store") {
	$data = explode("**", $data);

	if ($data[1] == 2)
	{
		$disable = 1;

	}
	else
	{
		$disable = 0;
	}

	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(13)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
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

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data(str) {
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}

		function toggle( x='', origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function js_set_value( data ) {

			var exdata=data.split("__");

			toggle( document.getElementById( 'search' + exdata[0] ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + exdata[0]).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + exdata[0]).val() );
				selected_name.push( $('#txt_individual' + exdata[1]).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + exdata[0]).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}

	</script>
</head>
<body>
	<div align="center">
		<form name="searchwofrm"  id="searchwofrm" autocomplete=off>

			<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
			<input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />

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
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value +'_'+document.getElementById('cbo_year_selection').value, 'create_booking_search_list_view', 'search_div', 'style_wise_grey_fabric_stock_report_demo_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
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
	$cbo_booking_year = $data[7];

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
	$booking_year_condition="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			if($cbo_booking_year>0)
			{
				$booking_year_condition=" and YEAR(a.insert_date)=$cbo_year";
			}

			$date_cond="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			if($cbo_booking_year>0)
			{
				$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}

			$date_cond="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}else {

		if($db_type==0)
		{
			if($cbo_booking_year>0)
			{
				$booking_year_condition=" and YEAR(a.insert_date)=$cbo_booking_year";
			}
		}
		else
		{
			if($cbo_booking_year>0)
			{
				$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_booking_year";
			}
		}
	}

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$import_booking_id_arr=return_library_array( "select id, booking_id from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0",'id','booking_id');

	$apporved_date_arr=return_library_array( "select mst_id, max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id",'mst_id','approved_date');

	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');

	$sql= "SELECT a.booking_no_prefix_num, a.id, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no FROM wo_booking_mst a,wo_booking_dtls d, wo_po_details_master b WHERE a.booking_no = d.booking_no and d.job_no =b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.company_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond  $booking_year_condition and a.id in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id)  group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date,a.company_id, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no order by a.id DESC";

	//echo $sql; die();
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
			<th>Approved</th>
		</thead>
	</table>
	<div style="width:1080px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$data= $row[csf('id')].'__'.$row[csf('booking_no')].'__'.$row[csf('is_approved')];
				$id_arr[]=$row[csf('id')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $row[csf('id')];?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
					<td width="40" align="center"><? echo $i; ?>
					<input type="hidden" name="txt_individual" id="txt_individual<? echo $row[csf('booking_no')];?>" value="<?php echo $row[csf('booking_no')]; ?>"/>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $row[csf('id')];?>" value="<?php echo $row[csf('id')]; ?>"/>
				</td>
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
				<td align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
			</tr>
			<?
			$i++;
		}
			//partial booking...........................................................end;
		?>
	</table>

	<div style="width:625px;" align="left">
		<table width="100%">
			<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year; ?>, 'create_order_no_search_list_view', 'search_div', 'style_wise_grey_fabric_stock_report_demo_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and a.style_ref_no like '".$search_string."%'";
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
	else $year_field="";//defined Later

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
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
					<td width="90"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="70"><p><? echo $buyer; ?></p></td>
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$started = microtime(true);
	$company_name  	= str_replace("'","",$cbo_company_id);
	$within_group  	= str_replace("'","",$cbo_within_group);
	$pocompany_id 	= str_replace("'","",$cbo_pocompany_id);
	$po_buyer_id 	= str_replace("'","",$cbo_buyer_id);
	$cbo_year 		= str_replace("'","",$cbo_year);
	$booking_no 	= str_replace("'","",$txt_booking_no);
	$booking_id 	= str_replace("'","",$txt_booking_id);
	$order_no 		= str_replace("'","",$txt_order_no);
	$order_id 		= str_replace("'","",$hide_order_id);
	$program_no 	= str_replace("'","",$txt_program_no);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);

	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr  = return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	$season_arr = return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');

	if($pocompany_id>0) {

		if($within_group==1)
		{
			$pocompany_cond="and d.po_company_id='$pocompany_id'";
		}else {
			$pocompany_cond="and d.company_id='$pocompany_id'";
		}
	} else {
		$pocompany_cond="";
	}

	if($cbo_store_wise==1){
		$store_cond = " and e.store_id=$cbo_store_name";
		$store_cond2 = " and a.store_id=$cbo_store_name";
	}
	if($po_buyer_id!=0 || $po_buyer_id!="")
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and d.po_buyer in (".str_replace("'","",$po_buyer_id).")";
		}else {

			$buyer_id_cond=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
		}
	}

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond=" and e.transaction_date <= '$end_date'";
	}

	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and d.job_no_prefix_num='$order_no'";

	if($date_from=="")
	{
		if ($db_type == 0)
		{
			if($cbo_year>0)
			{
				$sales_order_year_condition=" and YEAR(d.insert_date)=$cbo_year";
			}
		}
		else if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$sales_order_year_condition=" and to_char(d.insert_date,'YYYY')=$cbo_year";
			}
		}
	} else {
		$sales_order_year_condition="";
	}

	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and b.booking_no='$program_no'";

	if ($booking_no!="")
	{
		$booking_numbers =  "'".implode("','",explode('*', $booking_no))."'";
		$booking_no_cond=" and d.sales_booking_no in ($booking_numbers)";
	} else {
		$booking_no_cond="";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and d.within_group='$within_group' ";
	}
	else
	{
		$within_group_cond = " ";
	}

	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no
	from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
	where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $order_no_cond $booking_no_cond $program_no_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $testCond $store_cond
	group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no";


	
	// Main query once
	$masterData=sql_select($sql);

	if(empty($masterData))
	{
		/* If sales order data not found in receive then this part will check for transfer in data*/
		$trans_in_row = sql_select("SELECT a.company_id,a.to_order_id as po_id,b.from_prod_id as prod_id, e.color_range,d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no, d.po_company_id as lc_company_id,d.po_buyer, d.po_job_no, d.booking_without_order, d.booking_type, d.booking_entry_form , c.detarmination_id,c.gsm
			from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , product_details_master c
			where a.id=b.mst_id and a.to_order_id=d.id and b.from_prod_id = c.id and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.company_id = $company_name $order_no_cond $booking_no_cond $date_cond
			group by a.company_id,a.to_order_id,b.from_prod_id, e.color_range, d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no,d.po_company_id, d.po_buyer,d.po_job_no, d.booking_without_order, d.booking_type,d.booking_entry_form,c.detarmination_id,c.gsm");

		foreach($trans_in_row as $row)
		{
			$poids .= $row[csf("po_id")].",";
			$prod_id .= $row[csf("prod_id")].",";

			$salesData[$row[csf("po_id")]]['working_company_id'] = $row[csf("company_id")];
			$salesData[$row[csf("po_id")]]['booking_no'] = $row[csf("sales_booking_no")];
			$salesData[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$salesData[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$salesData_color_range[$row[csf("po_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]]['color_range_id'] .= $row[csf("color_range")].",";
			$salesData[$row[csf("po_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
			$salesData[$row[csf("po_id")]]['fso_no'] = $row[csf("job_no")];

			// within group yes
			if($row[csf("within_group")]==1)
			{
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("po_buyer")];
				$salesData[$row[csf("po_id")]]['job_no'] = $row[csf("po_job_no")];
			} else {
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("buyer_id")];
				$salesData[$row[csf("po_id")]]['job_no'] = "";
			}

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
			$salesData[$row[csf("po_id")]]['booking_type'] = $bookingType;
		}
		unset($trans_in_row);

	}
	else
	{
		$prodWiseSalesDataStatus = $prodWiseOpening=array();
		foreach($masterData as $row)
		{
			$poids .= $row[csf("po_id")].",";
			$prod_id .= $row[csf("prod_id")].",";
			$all_po_arr[$row[csf('po_id')]] = $row[csf('po_id')];
			$determinationids .= ",".$row[csf('febric_description_id')];
			$receive_barcodes[$row[csf('barcode_no')]] = $row[csf('barcode_no')];

			$salesData[$row[csf("po_id")]]['booking_id'] = $row[csf("booking_id")];
			$salesData[$row[csf("po_id")]]['working_company_id'] = $row[csf("company_id")];
			$salesData[$row[csf("po_id")]]['booking_no'] = $row[csf("sales_booking_no")];
			$salesData[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$salesData[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$salesData[$row[csf("po_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
			$salesData[$row[csf("po_id")]]['fso_no'] = $row[csf("job_no")];

			// within group yes
			if($row[csf("within_group")]==1)
			{
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("po_buyer")];
				$salesData[$row[csf("po_id")]]['job_no'] = $row[csf("po_job_no")];
			} else {
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("buyer_id")];
				$salesData[$row[csf("po_id")]]['job_no'] = "";
			}

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
			$salesData[$row[csf("po_id")]]['booking_type'] = $bookingType;

			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			if($transaction_date >= $date_frm){
				$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*1_";
			}else{
				$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*2_";

				//$prodWiseOpening[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]] += $row[csf("receive_qty")];
			}
		}
	}
	

	$determinationids = implode(",", array_filter(array_unique(explode(",",chop($determinationids,",")))));
	$determinationidArr=explode(",",$determinationids);

	if($db_type==2 && count($determinationidArr)>999)
	{
		$determinationidsArr=array_chunk($determinationidArr, 999);
		$determinationid_cond=" and (";
		foreach ($determinationidsArr as $value)
		{
			$determinationid_cond .="a.id in (".implode(",", $value).") or ";
		}
		$determinationid_cond=chop($determinationid_cond,"or ");
		$determinationid_cond.=")";
	}
	else
	{
		$determinationid_cond=" and a.id in (".implode(",", $determinationidArr).")";
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
	$data_array=sql_select($sql_deter);

	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}

	if($within_group==1)
	{
		$booking_year_condition="";
		if ($db_type == 0)
		{

			if($cbo_year>0)
			{
				$booking_year_condition=" and YEAR(a.insert_date)=$cbo_year";
			}
		}
		else if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}
		}
	}

	$poids = implode(",", array_filter(array_unique(explode(",",chop($poids,",")))));
	$poids_arr=explode(",",$poids);

	if($db_type==2 && count($poids_arr)>999)
	{
		$poids_chunk=array_chunk($poids_arr,999) ;
		$salse_id_cond = " and (";
		$trans_po_id_cond = " and (";
		$po_cond=" and (";
		$toOrderIdCond = " and (";
		$fromOrderIdCond = " and (";

		foreach($poids_chunk as $chunk_arr)
		{
			$po_cond.=" d.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$trans_po_id_cond.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$salse_id_cond.=" a.id in(".implode(",",$chunk_arr).") or ";
			$toOrderIdCond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			$fromOrderIdCond.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
		}

		$fromOrderIdCond =chop($fromOrderIdCond,"or ");
		$toOrderIdCond =chop($toOrderIdCond,"or ");
		$salse_id_cond=chop($salse_id_cond,"or ");
		$po_cond=chop($po_cond,"or ");
		$trans_po_id_cond=chop($trans_po_id_cond,"or ");

		$fromOrderIdCond .=")";
		$toOrderIdCond .=")";
		$salse_id_cond.=")";
		$po_cond.=")";
		$trans_po_id_cond.=")";
	}
	else
	{
		$fromOrderIdCond=" and a.from_order_id in($poids)";
		$toOrderIdCond=" and a.to_order_id in($poids)";
		$salse_id_cond=" and a.id in($poids)";
		$po_cond=" and d.po_breakdown_id in($poids)";
		$trans_po_id_cond=" and c.po_breakdown_id in($poids)";
	}

	// add salses id in where clause
	if($salse_id_cond!="")
	{
		$salesSql ="SELECT a.id,sum(b.grey_qty) as fso_qty, sum(b.finish_qty) as booking_qty,a.po_job_no
		from fabric_sales_order_mst a,fabric_sales_order_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $salse_id_cond
		group by a.id,a.company_id,a.buyer_id, a.style_ref_no, a.job_no, a.job_no_prefix_num, a.sales_booking_no, a.booking_id,a.within_group,a.po_job_no";

		$sales_result = sql_select($salesSql);

		foreach ($sales_result as $row) {
			$salesData[$row[csf('id')]]['fso_qty'] = $row[csf('fso_qty')];
			$salesData[$row[csf('id')]]['booking_qty'] = $row[csf('booking_qty')];
			$po_jobs = explode(",",$row[csf('po_job_no')]);
			foreach ($po_jobs as $po_job) {
				if($po_job!=""){
					$po_job_arr[$row[csf('po_job_no')]] = "'".$po_job."'";
				}
			}

		}

		if(!empty($po_job_arr)){
			if($db_type==2 && count($po_job_arr)>999)
			{
				$job_chunk=array_chunk($po_job_arr,999) ;
				$job_cond = " (";

				foreach($job_chunk as $chunk_arr)
				{
					$job_cond.=" job_no in(".implode(",",$chunk_arr).") or ";
				}

				$job_cond = chop($job_cond,"or ");
				$job_cond .=")";
			}
			else
			{
				$job_cond=" job_no in(".implode(",",$po_job_arr).")";
			}

			$job_sql = sql_select("SELECT job_no,product_category,product_dept,product_code,season_buyer_wise,style_description from wo_po_details_master where $job_cond and status_active!=0 and is_deleted!=1");
			foreach ($job_sql as $job_row) {
				$job_info[$job_row[csf("job_no")]]["product_category"] 	= $product_category[$job_row[csf("product_category")]];
				$job_info[$job_row[csf("job_no")]]["product_dept"] 		= $product_dept[$job_row[csf("product_dept")]] . "<br />".$job_row[csf("product_code")];
				$job_info[$job_row[csf("job_no")]]["season"] 			= $job_row[csf("season_buyer_wise")];
				$job_info[$job_row[csf("job_no")]]["style_ref_no"] 		= $job_row[csf("style_description")];
			}
		}
	}

	if($poids!="")
	{
		$trans_in_data = sql_select("SELECT a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,sum(d.qnty) as transfer_in_qnty,d.barcode_no
			from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d
			where a.id=e.mst_id and e.id=b.trans_id and b.from_prod_id=c.id and b.id=d.dtls_id and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 $toOrderIdCond $date_cond $store_cond and d.entry_form=133 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
			group by a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,d.barcode_no");
		foreach($trans_in_data as $row)
		{
			$receive_barcodes[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}

		if(!empty($receive_barcodes)){
			if($db_type==2 && count($receive_barcodes)>999)
			{
				$barcode_chunk=array_chunk($receive_barcodes,999) ;
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
				$barcode_cond=" and b.barcode_no in(".implode(",",$receive_barcodes).")";
			}

			$production_sql = sql_select("select a.color_range_id,b.barcode_no,a.yarn_lot,a.yarn_count,b.po_breakdown_id,a.prod_id from pro_grey_prod_entry_dtls a,pro_roll_details b where a.id=b.dtls_id and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1 $barcode_cond");
			foreach ($production_sql as $production_row) {
				$barcode_color_range[$production_row[csf("barcode_no")]] = $production_row[csf("color_range_id")];

				$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]]["yarn_lot"] = $production_row[csf("yarn_lot")];
				$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]]["yarn_count"] = $production_row[csf("yarn_count")];
			}
		}

		$trans_out_data = sql_select("SELECT a.from_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,sum(d.qnty) as transfer_out_qnty,d.barcode_no
			from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d
			where a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and b.from_prod_id=c.id and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6 $fromOrderIdCond $date_cond $store_cond and a.id=d.mst_id and d.entry_form=133 and b.id=d.dtls_id and d.status_active=1 and d.is_deleted=0
			group by a.from_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,d.barcode_no");

		foreach($trans_out_data as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$transOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges] += $row[csf("transfer_out_qnty")];
			}else{
				$openingTransOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges] += $row[csf("transfer_out_qnty")];
			}
		}
		//die;
		$sql_iss=sql_select("SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty
			from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d
			where a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2
			and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $date_cond $store_cond
			group by  d.po_breakdown_id,e.transaction_date, d.booking_no,b.prod_id,d.barcode_no,d.qnty order by d.po_breakdown_id"); //  and d.is_returned<>1
		$knit_issue_arr=array();
		foreach($sql_iss as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm)
			{
				$knit_issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges]['issue_qty'] += $row[csf('issue_qty')];
			}
			else
			{
				$opening_issue[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges]['issue_qty'] += $row[csf('issue_qty')];
			}
		}
		unset($sql_iss);

		$sql_issue_return = sql_select("SELECT b.prod_id,e.transaction_date,b.febric_description_id,b.gsm,b.color_range_id,b.width,d.booking_no,d.po_breakdown_id as po_id,d.qnty as issue_return_qty, d.barcode_no
			from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details d
			where a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form =84 and e.transaction_type=4
			and d.entry_form=84 and a.receive_basis in(0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1
			and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $po_cond $store_cond
			group by b.prod_id,e.transaction_date,b.febric_description_id,b.gsm,b.color_range_id,b.width,d.booking_no, d.po_breakdown_id,d.qnty,d.barcode_no");
		$inssue_return_array=array();
		foreach($sql_issue_return as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];

			if($transaction_date >= $date_frm){
				$inssue_return_array[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges]['issue_return_qty'] += $row[csf('issue_return_qty')];
			}else{
				$opening_issue_return[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges]['issue_return_qty'] += $row[csf('issue_return_qty')];
			}
		}
		unset($sql_issue_return);

		foreach($trans_in_data as $row)
		{
			$prod_id .= $row[csf("from_prod_id")].",";
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*1*".$row[csf("from_order_id")]."_";
			}else{
				$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*2*".$row[csf("from_order_id")]."_";
				//$transInOpening[$row[csf("to_order_id")]][$row[csf("from_prod_id")]][$color_ranges] += $row[csf("transfer_in_qnty")];
			}
			$all_po_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
		}

		unset($trans_out_data);
		unset($trans_in_data);
	}


	$prodId = chop($prod_id,",");

	$prodIdArr = array_filter(array_unique(explode(",",$prodId)));
	if(count($prodIdArr)>0)
	{
		$prodId = implode(",", $prodIdArr);
		$prodCond = $all_prod_id_cond = "";

		if($db_type==2 && count($prodIdArr)>999)
		{
			$prodIdArr_chunk=array_chunk($prodIdArr,999) ;
			foreach($prodIdArr_chunk as $chunk_arr)
			{
				$prodCond.=" a.prod_id in(".implode(",",$chunk_arr).") or ";
			}
			$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
		}
		else
		{
			$all_prod_id_cond=" and a.prod_id in($prodId)";
		}
	}

	if($prodId!="")
	{
		$transaction_date_array=array();
		$sql_date="SELECT c.po_breakdown_id,a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date
		from inv_transaction a,order_wise_pro_details c
		where a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13
		$all_prod_id_cond $trans_po_id_cond $store_cond2 group by c.po_breakdown_id,a.prod_id";

		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}
		unset($sql_date_result);
	}
	ob_start();
	$table_width = ($cbo_store_wise==1)?"3170":"3080";
	?>
	<style type="text/css">
	.word_wrap_break{
		word-wrap: break-word;
		word-break: break-all;
	}
</style>

<fieldset style="width:2480">
	<table cellpadding="0" cellspacing="0" width="1300">
		<tr  class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="22" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
		</tr>
		<tr  class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="22" style="font-size:16px">
				<strong>
					<?
					echo $company_arr[str_replace("'","",$cbo_company_id)];
					?>
				</strong>
			</td>
		</tr>
		<tr  class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="22" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
		<thead>
			<tr>
				<th width="40"  rowspan="2">SL</th>
				<th width="60" rowspan="2">Company</th>
				<th width="80" rowspan="2">LC Company</th>
				<th width="100" rowspan="2">Buyer</th>
				<th width="100" rowspan="2">Style No</th>
				<th width="100" rowspan="2">Job No</th>
				<th width="50" rowspan="2">Season</th>
				<th width="80" rowspan="2">Product Dept.</th>
				<th width="80" rowspan="2">Style Desc.</th>
				<th width="80" rowspan="2">Product Category</th>
				<th width="130" rowspan="2">Booking No</th>
				<th width="80" rowspan="2">Booking Type</th>
				<th width="100" rowspan="2">Booking Qnty</th>
				<th width="130" rowspan="2">FSO</th>
				<th width="80" rowspan="2">FSO Qty</th>

				<th colspan="6">Fabric Details</th>
				<th colspan="7">Receive Details</th>
				<th colspan="4">Issue Details</th>
				<th colspan="3">Stock Details</th>
			</tr>
			<tr>
				<th width="70">Product ID</th>
				<th width="90">Construction</th>
				<th width="200">Composition</th>
				<th width="70">GSM</th>
				<th width="70">Color Range</th>
				<th width="80">F/Dia</th>

				<th width="90">Yarn Lot</th>
				<th width="90">Yarn Count</th>

				<th width="90">Opening</th>
				<th width="90">Recv. Qty.</th>
				<th width="90">Issue Return Qty.</th>
				<th width="90">Transf. In Qty.</th>
				<th width="90">Total Recv.</th>

				<th width="90">Issue Qty.</th>
				<th width="90">Receive Return Qty.</th>
				<th width="90">Transf. Out Qty.</th>
				<th width="90">Total Issue</th>

				<th width="90">Stock Qty.</th>
				<? if($cbo_store_wise==1){?>
					<th width="90">Store</th>
				<? } ?>
				<th width="50">Age(days)</th>
				<th>DOH</th>
			</tr>
		</thead>
	</table>
	<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search"  align="left">
			<?
			$i=1;
			$tot_recv_qty=0;
			foreach($prodWiseSalesDataStatus as $poId=>$prodArr)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				foreach ($prodArr as $prodId=>$colorRange)
				{
					foreach ($colorRange as $crange=>$row)
					{

						$yarn_lot = $yarn_info[$poId][$prodId][$crange]["yarn_lot"];
						$yarn_count = $yarn_info[$poId][$prodId][$crange]["yarn_count"];

						$all_prodData = explode("_",chop($row,"_"));
						$recv_qnty=$trans_out_qty=$trans_in_qty=$opening_recv=$opening_trans=0;
						foreach ($all_prodData as $prodData) {
							$data = explode("*",$prodData);
							if($data[5] == 1){
								if($data[6] == 1){
									$recv_qnty += $data[3]*1;
								}
								if($data[6] == 2){
									$opening_recv += $data[3]*1;
								}
							}

							if($data[5] == 3){
								if($data[6] == 1){
									$trans_in_qty += $data[3]*1;
								}
								if($data[6] == 2){
									$opening_trans += $data[3]*1;
								}

								$from_order_id = $data[7];

								$yarn_lot = $yarn_info[$from_order_id][$prodId][$crange]["yarn_lot"];
								$yarn_count = $yarn_info[$from_order_id][$prodId][$crange]["yarn_count"];
							}
							$detarmination_id = $data[0];
							$store_id = $data[4];
						}


						$issue_return_qnty  = $inssue_return_array[$poId][$prodId][$crange]['issue_return_qty'];
						$iss_qty 			= $knit_issue_arr[$poId][$prodId][$crange]['issue_qty'];
						//echo $iss_qty.'='.$issue_return_qnty.'<br>';
						$opening_title = "Receive=".$opening_recv ."+". $opening_trans."\nIssue=".$opening_issue[$poId][$prodId][$crange]['issue_qty'] ."+". $openingTransOutQnty[$poId][$prodId][$crange];
						$opening = ($opening_recv+$opening_trans)-($opening_issue[$poId][$prodId][$crange]['issue_qty']+$openingTransOutQnty[$poId][$prodId][$crange]);

						// roll wise $recv_ret_qty page did not developed yet
						$recv_tot_qty  = ($recv_qnty+$issue_return_qnty+$trans_in_qty);
						$trans_out_qty = $transOutQnty[$poId][$prodId][$crange];
						$iss_tot_qty   = ($iss_qty+$trans_out_qty);

						$stock_qty 	   = $opening+($recv_tot_qty-$iss_tot_qty);
						$stock_qty     = number_format($stock_qty,2,".","");

						$daysOnHand = datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['max_date'],'','',1),date("Y-m-d"));
						$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['min_date'],'','',1),date("Y-m-d"));

						$transaction_date_array[$poId][$prodId]['min_date'];

						$product_category 	= $job_info[$salesData[$poId]['job_no']]["product_category"];
						$product_dept 		= $job_info[$salesData[$poId]['job_no']]["product_dept"];
						$season 			= $season_arr[$job_info[$salesData[$poId]['job_no']]["season"]];
						$style_ref_no 		= $job_info[$salesData[$poId]['job_no']]["style_ref_no"];

						if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qty == $txt_qnty) || $get_upto_qnty == 0))
						{

							//if($stock_qty!=0 && $cbo_value_with==2)
							if($stock_qty > 0 && $cbo_value_with==2)
							{
								$tot_opening  		+= $opening;
								$tot_recv_qty 		+= $recv_qnty;
								$tot_iss_ret_qty 	+= $issue_return_qnty;
								$tot_trans_in_qty 	+= $trans_in_qty;
								$grand_tot_recv_qty += $recv_tot_qty;

								$tot_iss_qty 		+= $iss_qty;
								$tot_rec_ret_qty 	+= $recv_ret_qty;
								$tot_trans_out_qty 	+= $trans_out_qty;
								$grand_tot_iss_qty 	+= ($iss_qty+$trans_out_qty);
								$grand_stock_qty 	+= $stock_qty;

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

									<td width="40" align="center"><?  echo $i; ?></td>
									<td width="60"><p class="word_wrap_break"><? echo $company_arr[$salesData[$poId]['working_company_id']]; ?>  </p></td>
									<td width="80"><p class="word_wrap_break"><? echo $company_arr[$salesData[$poId]['lc_company_id']]; ?> </p></td>
									<td width="100"><p class="word_wrap_break"><? echo $buyer_arr[$salesData[$poId]['buyer_id']]; ?> </p></td>
									<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['style_ref_no']; ?></p></td>
									<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['job_no']; ?></p></td>
									<td width="50"><p class="word_wrap_break"><? echo $season; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $product_dept; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $style_ref_no; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $product_category; ?></p></td>
									<td width="130"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_no']; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_type'];//$bookingType; ?></p></td>
									<td width="100" align="right" class="word_wrap_break"><? echo number_format($salesData[$poId]['booking_qty'],2); ?></td>
									<td width="130"title="<? echo $poId;?>"><p class="word_wrap_break"><? echo $salesData[$poId]['fso_no']; ?> </p></td>
									<td width="80" align="right"><p><? echo $salesData[$poId]['fso_qty']; ?> </p></td>

									<td width="70"><p><? echo $prodId;?></p></td>
									<td width="90"><p class="word_wrap_break"><? echo $constructionArr[$detarmination_id]; ?></p></td>
									<td width="200"><p class="word_wrap_break"><? echo $composition_arr[$detarmination_id]; ?></p></td>
									<td width="70" align="center"><p><? echo $data[1]; ?></p></td>
									<td width="70"><p class="word_wrap_break"><? echo $color_range[$crange];?></p></td>
									<td width="80" align="center"><p><? echo $data[2]; ?></p></td>

									<td width="90"><? echo $yarn_lot; ?></td>
									<td width="90">
										<? 
										$counts="";
										$yarn_counts = array_unique(explode(",",$yarn_count));
										foreach ($yarn_counts as $yarn_count) {
											$counts .= $yarn_count_arr[$yarn_count].",";
										}
										echo trim($counts,", ");
										?> 
									</td>

									<td width="90" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening,2); ?></td>
									<td width="90" align="right"><? echo number_format($recv_qnty,2); ?></td>
									<td width="90" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
									<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange; ?>','trans_in_popup');"><? echo number_format($trans_in_qty,2);?></a></td>
									<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>

									<td width="90" align="right"><p><? echo number_format($iss_qty,2); ?></p></td>
									<td width="90" align="right"><p><? echo number_format($recv_ret_qty,2);?></p></td>
									<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>

									<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
									<? if($cbo_store_wise==1){?>
										<td width="90"><? echo $store_arr[$store_id]; ?></td>
									<? } ?>
									<td align="center" width="50"><? if($stock_qty>0) echo $ageOfDays; ?></td>
									<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
								</tr>
								<?
								$i++;
							}
							else if($stock_qty>=0 && $cbo_value_with==1)
							{
								$tot_opening  		+= $opening;
								$tot_recv_qty 		+= $recv_qnty;
								$tot_iss_ret_qty 	+= $issue_return_qnty;
								$tot_trans_in_qty 	+= $trans_in_qty;
								$grand_tot_recv_qty += $recv_tot_qty;

								$tot_iss_qty 		+= $iss_qty;
								$tot_rec_ret_qty 	+= $recv_ret_qty;
								$tot_trans_out_qty 	+= $trans_out_qty;
								$grand_tot_iss_qty 	+= ($iss_qty+$trans_out_qty);
								$grand_stock_qty 	+= $stock_qty;
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

									<td width="40" align="center"><?  echo $i; ?></td>
									<td width="60"><p class="word_wrap_break"><? echo $company_arr[$salesData[$poId]['working_company_id']]; ?>  </p></td>
									<td width="80"><p class="word_wrap_break"><? echo $company_arr[$salesData[$poId]['lc_company_id']]; ?> </p></td>
									<td width="100"><p class="word_wrap_break"><? echo $buyer_arr[$salesData[$poId]['buyer_id']]; ?> </p></td>
									<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['style_ref_no']; ?></p></td>
									<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['job_no']; ?></p></td>
									<td width="50"><p class="word_wrap_break"><? echo $season; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $product_dept; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $style_ref_no; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $product_category; ?></p></td>
									<td width="130"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_no']; ?></p></td>
									<td width="80"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_type']; ?></p></td>
									<td width="100" align="right"><? echo number_format($salesData[$poId]['booking_qty'],2); ?></td>
									<td width="130"><p class="word_wrap_break"><? echo $salesData[$poId]['fso_no']; ?> </p></td>
									<td width="80" align="right"><p class="word_wrap_break"><? echo $salesData[$poId]['fso_qty']; ?> </p></td>

									<td width="70"><p><? echo $prodId;?></p></td>
									<td width="90"><p class="word_wrap_break"><? echo $constructionArr[$detarmination_id]; ?></p></td>
									<td width="200"><p class="word_wrap_break"><? echo $composition_arr[$detarmination_id]; ?></p></td>
									<td width="70" align="center"><p><? echo $data[1]; ?></p></td>
									<td width="70"><p class="word_wrap_break"><? echo  $color_range[$crange];?></p></td>
									<td width="80" align="center"><p><? echo $data[2]; ?></p></td>

									<td width="90"><? echo $yarn_lot; ?></td>
									<td width="90">
										<? 
										$counts="";
										$yarn_counts = array_unique(explode(",",$yarn_count));
										foreach ($yarn_counts as $yarn_count) {
											$counts .= $yarn_count_arr[$yarn_count].",";
										}
										echo trim($counts,", ");
										?>
									</td>

									<td width="90" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening,2); ?></td>
									<td width="90" align="right"><? echo number_format($recv_qnty,2); ?></td>
									<td width="90" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
									<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]; ?>','trans_in_popup');"><? echo number_format($trans_in_qty,2);?></a></td>
									<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>

									<td width="90" align="right" title="<?=$poId.'='.$prodId.'='.$crange;?>"><p><? echo number_format($iss_qty,2); ?></p></td>
									<td width="90" align="right"><p><? echo number_format($recv_ret_qty,2);?></p></td>
									<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]; ?>','trans_out_popup');"><? echo number_format($trans_out_qty,2); ?></a></td>
									<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>

									<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
									<? if($cbo_store_wise==1){?>
										<td width="90"><? echo $store_arr[$store_id]; ?></td>
									<? } ?>
									<td align="center" width="50"><? if($stock_qty>0) echo $ageOfDays; ?></td>
									<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
								</tr>
								<?
								$i++;
							}
							$temp_tr[$poId] = $poId;
						}
					}
				}
			}
			?>
		</table>
	</div>
	<table width="<? echo $table_width; ?>"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
		<tfoot>
			<tr>
				<th width="40"></th>
				<th width="60"></th>
				<th width="80"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="50"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="130"></th>
				<th width="80"></th>
				<th width="100"></th>
				<th width="130"></th>
				<th width="80"></th>

				<th width="70"></th>
				<th width="90"></th>
				<th width="200"></th>
				<th width="70"></th>
				<th width="70"></th>
				<th width="80">Grand Total = </th>

				<th width="90"></th>
				<th width="90"></th>

				<th width="90" align="right" id="value_html_opening_qnty"><? echo number_format($tot_opening,2); ?></th>
				<th width="90" align="right" id="value_html_recv_qnty"><? echo number_format($tot_recv_qty,2); ?></th>
				<th width="90" align="right" id="value_html_issue_rtn_qnty"><? echo number_format($tot_iss_ret_qty,2); ?></th>
				<th width="90" align="right" id="value_html_trans_qty_in"><? echo number_format($tot_trans_in_qty,2); ?></th>
				<th width="90" align="right" id="value_html_total_recv"><? echo number_format($grand_tot_recv_qty,2); ?></th>

				<th width="90" align="right" id="value_html_issue_qty"><? echo number_format($tot_iss_qty,2); ?></th>
				<th width="90" align="right" id="value_html_rcv_rtn_qnty"><? echo number_format($tot_rec_ret_qty,2);?></th>
				<th width="90" align="right" id="value_html_trans_qty_out"><? echo number_format($tot_trans_out_qty,2); ?></th>
				<th width="90" align="right" id="value_html_toal_issue"><? echo number_format($grand_tot_iss_qty,2); ?></th>
				<th width="90" align="right" id="value_html_total_stock"><? echo number_format($grand_stock_qty,2); ?></th>
				<? if($cbo_store_wise==1){?>
					<th width="90">&nbsp;</th>
				<? } ?>
				<th align="right" width="50"></th>
				<th align="right"></th>
			</tr>
		</tfoot>
	</table>
</fieldset>
<?
echo "Execution Time: " . (microtime(true) - $started) . "S"; // in seconds
$html = ob_get_contents();
ob_clean();

foreach (glob("*.xls") as $filename) {
	@unlink($filename);
}
    //---------end------------//
$name=time();
$filename=$user_id."_".$name.".xls";
$create_new_doc = fopen($filename, 'w');
$is_created = fwrite($create_new_doc, $html);
echo "$html####$filename####$rpt_type";
exit();
}


if($action == "trans_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$dataArr = explode("_", $data);
	$poId = $dataArr[0];
	$prodId = $dataArr[1];
	$detarmination_id = $dataArr[2]; 
	$gms = $dataArr[3];
	$color_range = $dataArr[4];
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
	<fieldset style="width:90%; margin:auto;">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:95%; margin:auto;" id="report_container">
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
					<th width="50">Program No</th>
					<th width="80">Barcode No</th>
					<th width="50">Rack No</th>
					<th width="">Shelf</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						$mrr_sql="select a.transfer_system_id,a.transfer_date, b.to_rack as rack, b.to_shelf as self, c.barcode_no, c.roll_no, c.qnty,c.booking_no
						from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
						left join pro_roll_details d on c.barcode_no=d.barcode_no and d.entry_form=58 and d.status_active=1
						left join pro_grey_prod_entry_dtls e on d.dtls_id=e.id
						where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.to_order_id='$poId' and b.from_prod_id=$prodId and e.color_range_id=$color_range order by a.id,b.id";

						$dtlsArray=sql_select($mrr_sql);
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td width="80" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
								<td width="50" align="center"><p><? echo $row[csf('roll_no')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('barcode_no')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('rack')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('self')]; ?></p></td>
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
					<th width="30"></th>
					<th width="110"></th>
					<th width="80">Total:</th>
					<th width="80"><? echo number_format($tot_trans_qty,2); ?></th>
					<th colspan="4" width="365"></th>
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
	$dataArr = explode("_", $data);
	$poId = $dataArr[0];
	$prodId = $dataArr[1];
	$detarmination_id = $dataArr[2];
	$gms = $dataArr[3];
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
					<th width="50">Program No</th>
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

						$mrr_sql= "select a.transfer_system_id,a.transfer_date, a.from_order_id, a.to_order_id, b.from_program,b.rack,b.shelf,b.roll, c.quantity, d.barcode_no,d.booking_no
						from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c ,pro_roll_details d where a.id=b.mst_id and c.dtls_id=b.id and d.dtls_id = b.id and c.trans_type in(6) and a.item_category=13 and c.entry_form=133 and d.entry_form=133 and a.transfer_criteria=4 and a.from_order_id='$poId' and b.from_prod_id=$prodId and d.po_breakdown_id = a.to_order_id and d.roll_split_from=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
						order by a.id , b.id ";

						$dtlsArray=sql_select($mrr_sql);
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td width="80" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
								<td width="50" align="center"><p><? echo $row[csf('roll')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('barcode_no')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('rack')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('self')]; ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('quantity')];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80">Total:</th>
					<th width="80"><? echo number_format($tot_trans_qty,2); ?></th>
					<th colspan="4" width="365"></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}
?>
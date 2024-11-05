<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=6 and report_id=299 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90) group by buyer_id)  $buyer_cond group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 0, "-All Buyer-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_po_company")
{
	$data=explode("_", $data);
	if($data[0] == 1){
		echo create_drop_down( "cbo_pocompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-Po Company-", $selected, "load_drop_down( 'requires/style_wise_grey_fabric_stock_report_demo_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/style_wise_grey_fabric_stock_report_demo_controller' );" );
	}
	else
	{
		echo create_drop_down( "cbo_pocompany_id", 130, $blank_array,"", 1, "-Po Company-", $selected, "",0,"" );
	}
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
if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_buyer_id','0','0','','0');\n";
	echo "set_multiselect('cbo_pocompany_id','0','0','','0');\n";
	echo "setTimeout[($('#po_company_td a').attr('onclick',\"disappear_list(cbo_pocompany_id,'0');getCompanyId();\") ,3000)];\n";


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

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
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

if($action=="report_generate") // Show
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
	$order_no 		= trim(str_replace("'","",$txt_order_no));
	$int_ref 		= trim(str_replace("'","",$txt_int_ref));
	$order_id 		= str_replace("'","",$hide_order_id);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);
	$style_ref 		= str_replace("'","",$txt_style_ref);

	$store_arr 	 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$company_arr 	= return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	$season_arr  	= return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_arr 		= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	if($pocompany_id!=0 || $pocompany_id!=""){

		if($within_group==1)
		{
			$pocompany_cond="and d.po_company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
			$pocompany_cond3="and d.po_company_id in($pocompany_id)";
		}else {
			$pocompany_cond="and d.company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
			$pocompany_cond3="and d.po_company_id in($pocompany_id)";
		}
	} else {
		$pocompany_cond="";
	}

	if($cbo_store_wise==1){
		$store_cond = " and e.store_id=$cbo_store_name";
		$store_cond2 = " and a.store_id=$cbo_store_name";
		$store_cond3 = " and b.store_id=$cbo_store_name";
	}

	if($po_buyer_id!=0 || $po_buyer_id!="")
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and d.po_buyer in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond2=" and f.po_buyer in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond3=" and d.po_buyer in (".str_replace("'","",$po_buyer_id).")";
		}else {

			$buyer_id_cond=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond2=" and f.buyer_id in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond3=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
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

	if ($order_id=='') $order_no_cond=""; else $order_no_cond="and d.id='$order_id'";
	if ($order_id=='') $toOrderIdCond=""; else $toOrderIdCond="and a.to_order_id='$order_id'";

	if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and d.job_no like '%$order_no%'";
	if ($order_no=='') $sales_to_order_no_cond=""; else $sales_to_order_no_cond=" and f.job_no like '%$order_no%'";


	if($date_from=="")
	{
		if ($db_type == 0)
		{
			if($cbo_year>0)
			{
				$sales_order_year_condition=" and YEAR(d.insert_date)=$cbo_year";
				$sales_order_year_condition2=" and YEAR(f.insert_date)=$cbo_year";
				$sales_order_year_condition3=" and YEAR(d.insert_date)=$cbo_year";
			}
		}
		else if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$sales_order_year_condition=" and to_char(d.insert_date,'YYYY')=$cbo_year";
				$sales_order_year_condition2=" and to_char(f.insert_date,'YYYY')=$cbo_year";
				$sales_order_year_condition3=" and to_char(d.insert_date,'YYYY')=$cbo_year";
			}
		}
	} else {
		$sales_order_year_condition="";
	}

	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and b.booking_no='$program_no'";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no'";
		$booking_no_cond2=" and f.sales_booking_no like '%$booking_no'";
		$booking_no_cond3=" and d.sales_booking_no like '%$booking_no'";
	} else {
		$booking_no_cond=$booking_no_cond2=$booking_no_cond3="";
	}

	if ($style_ref!="")
	{
		$style_ref_no_cond=" and d.style_ref_no like '%$style_ref%'";
		$style_ref_no_cond2=" and f.style_ref_no like '%$style_ref%'";
	}
	else
	{
		$style_ref_no_cond=$style_ref_no_cond2="";
	}

	if ($int_ref!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b, FABRIC_SALES_ORDER_MST c
		where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID and c.within_group=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond=""; $refBooking_cond2=""; $refBooking_cond3="";
		foreach ($po_sql_result as $key => $row)
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
		$refBooking_cond2=" and f.booking_id in('".implode("','",$bookingNo_arr)."') ";
		$refBooking_cond3=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and d.within_group='$within_group' ";
		$within_group_cond2 = " and f.within_group='$within_group' ";
		$within_group_cond3 = " and d.within_group='$within_group' ";
	}
	else
	{
		$within_group_cond = $within_group_cond2=$within_group_cond3="";
	}

	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no,a.booking_no,a.booking_id as production_id,a.entry_form
	from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
	where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $order_no_cond $booking_no_cond $refBooking_cond $program_no_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $testCond $store_cond $sales_order_no_cond $style_ref_no_cond
	group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no,a.booking_no,a.booking_id,a.entry_form
	union all
	SELECT sum(e.cons_quantity) as receive_qty,c.po_breakdown_id as po_id,b.prod_id,b.febric_description_id,b.gsm,b.color_range_id,b.color_id,b.stitch_length,b.width,d.company_id,d.buyer_id,d.style_ref_no,d.job_no,d.job_no_prefix_num,d.sales_booking_no,d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id, null as barcode_no,a.booking_no,a.booking_id as production_id,a.entry_form
	from inv_receive_master a,pro_grey_prod_entry_dtls b,order_wise_pro_details c,fabric_sales_order_mst d,inv_transaction e
	where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id = d.id and a.id=e.mst_id and c.trans_id=e.id and c.trans_type=e.transaction_type and c.entry_form in (22) and c.is_sales = 1 and c.trans_type = 1 and c.status_active = 1 and c.is_deleted = 0 and a.entry_form in (22) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.receive_basis in (9) and a.roll_maintained is null and e.transaction_type=1 $within_group_cond and d.company_id = $company_name $order_no_cond $booking_no_cond $refBooking_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $store_cond $sales_order_no_cond $style_ref_no_cond
	group by c.po_breakdown_id,b.prod_id,b.febric_description_id,b.gsm,b.color_range_id,b.color_id,b.stitch_length,b.width,d.company_id,d.buyer_id,d.style_ref_no,d.job_no,d.job_no_prefix_num,d.sales_booking_no,d.booking_id,d.within_group,d.po_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,a.booking_no,a.booking_id,a.entry_form
	";
	//die;
	//echo "<br />";
	// Main query once
	//echo $sql;die;
	// if(empty($rcv_weight_sql_result))
	// {

	// }

	$masterData=sql_select($sql);
	//echo $sql;die;
	$allPoIdsArr = array();
	$poIdsChk = array();
	if(empty($masterData))
	{
		/* If sales order data not found in receive then this part will check for transfer in data*/

		$trans_in_row = sql_select("SELECT a.company_id,a.to_order_id as po_id,b.from_prod_id as prod_id, e.color_range,d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no, d.po_company_id as lc_company_id,d.po_buyer, d.po_job_no, d.booking_without_order, d.booking_type, d.booking_entry_form , c.detarmination_id,c.gsm
			from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , product_details_master c
			where a.id=b.mst_id and a.to_order_id=d.id and b.from_prod_id = c.id and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form in(133,362) and a.transfer_criteria=4 and and e.transaction_type=6 and a.company_id = $company_name $order_no_cond $booking_no_cond $refBooking_cond $date_cond $sales_order_no_cond $style_ref_no_cond
			group by a.company_id,a.to_order_id,b.from_prod_id, e.color_range, d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no,d.po_company_id, d.po_buyer,d.po_job_no, d.booking_without_order, d.booking_type,d.booking_entry_form,c.detarmination_id,c.gsm");

		foreach($trans_in_row as $row)
		{
			if($poIdsChk[$row[csf('po_id')]]=='')
			{
				$poIdsChk[$row[csf('po_id')]] = $row[csf('po_id')];
				array_push($allPoIdsArr, $row[csf('po_id')]);
			}

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
		$prodWiseSalesDataStatus = $prodWiseOpening = $all_productionIdArr = array();
		foreach($masterData as $row)
		{
			if($poIdsChk[$row[csf('po_id')]]=='')
			{
				$poIdsChk[$row[csf('po_id')]] = $row[csf('po_id')];
				array_push($allPoIdsArr, $row[csf('po_id')]);
			}
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

			if($row[csf("entry_form")]==22)
			{
				if($jo_no_chk[$row[csf("production_id")]] == "")
				{
					$jo_no_chk[$row[csf("production_id")]] = $row[csf("production_id")];
					array_push($all_productionIdArr,$row[csf("production_id")]);
				}
			}
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
			if($row[csf("color_range_id")]!=""){
				if($transaction_date >= $date_frm){
					$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*1**".$row[csf("color_id")]."_";
				}else{
					if($transaction_date < $date_frm){
						$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*2**".$row[csf("color_id")]."_";
						$receiveOpening[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] += $row[csf("receive_qty")];
					}
				}
			}
		}
	}
	/* echo "<pre>";
	print_r($all_productionIdArr);
	die; */
	//echo "<br />";


	$trans_in_sql = "SELECT a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,sum(d.qnty) as transfer_in_qnty,d.barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id as lc_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form
	from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f
	where a.entry_form=133 and a.status_active=1 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.to_trans_id and b.from_prod_id=c.id and b.id=d.dtls_id and d.po_breakdown_id=f.id and b.status_active=1 $toOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and d.entry_form=133 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sales_to_order_no_cond  $style_ref_no_cond2
	group by a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,d.barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form
	union all
	SELECT a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,sum(e.cons_quantity) as transfer_in_qnty,null as barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id as lc_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form
	from inv_item_transfer_mst a,inv_transaction e, inv_item_transfer_dtls b,product_details_master c,order_wise_pro_details d,fabric_sales_order_mst f
	where a.entry_form in(362) and a.status_active = 1 and a.transfer_criteria = 4 and a.id = e.mst_id and e.id = b.to_trans_id and b.to_prod_id = c.id and b.id = d.dtls_id and d.po_breakdown_id = f.id and b.status_active = 1 $toOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and d.entry_form in(362) and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and e.transaction_type=5 $sales_to_order_no_cond  $style_ref_no_cond2
	group by a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form
	";
	//echo "<br />";
	//echo $trans_in_sql;
	$trans_in_data = sql_select($trans_in_sql);

	foreach($trans_in_data as $row)
	{
		if($poIdsChk[$row[csf('po_id')]]=='')
		{
			$poIdsChk[$row[csf('po_id')]] = $row[csf('po_id')];
			array_push($allPoIdsArr, $row[csf('po_id')]);
		}
		$poids .= $row[csf("to_order_id")].",";
		$salesData[$row[csf("to_order_id")]]['booking_id'] = $row[csf("booking_id")];
		$salesData[$row[csf("to_order_id")]]['working_company_id'] = $row[csf("company_id")];
		$salesData[$row[csf("to_order_id")]]['booking_no'] = $row[csf("sales_booking_no")];
		$salesData[$row[csf("to_order_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$salesData[$row[csf("to_order_id")]]['within_group'] = $row[csf("within_group")];
		$salesData[$row[csf("to_order_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
		$salesData[$row[csf("to_order_id")]]['fso_no'] = $row[csf("job_no")];

		// within group yes
		if($row[csf("within_group")]==1)
		{
			$salesData[$row[csf("to_order_id")]]['buyer_id'] = $row[csf("po_buyer")];
			$salesData[$row[csf("to_order_id")]]['job_no'] = $row[csf("po_job_no")];
		} else {
			$salesData[$row[csf("to_order_id")]]['buyer_id'] = $row[csf("buyer_id")];
			$salesData[$row[csf("to_order_id")]]['job_no'] = "";
		}

		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType = "Sample With Order";
			}
		}
		else
		{
			$bookingType = $booking_type_arr[$row[csf('booking_entry_form')]];
		}

		$salesData[$row[csf("to_order_id")]]['booking_type'] = $bookingType;
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
	//echo "hi";die;
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

	if(!empty($allPoIdsArr))
	{
		$salesIdCond="".where_con_using_array($allPoIdsArr,0,'d.id')."";
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
		$ProductionCond = " and (";

		foreach($poids_chunk as $chunk_arr)
		{
			$po_cond.=" d.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$trans_po_id_cond.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$salse_id_cond.=" a.id in(".implode(",",$chunk_arr).") or ";
			$toOrderIdCond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			$fromOrderIdCond.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			$ProductionCond.=" b.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
		}

		$fromOrderIdCond =chop($fromOrderIdCond,"or ");
		$toOrderIdCond =chop($toOrderIdCond,"or ");
		$salse_id_cond=chop($salse_id_cond,"or ");
		$po_cond=chop($po_cond,"or ");
		$trans_po_id_cond=chop($trans_po_id_cond,"or ");
		$ProductionCond=chop($ProductionCond,"or ");

		$fromOrderIdCond .=")";
		$toOrderIdCond .=")";
		$salse_id_cond.=")";
		$po_cond.=")";
		$trans_po_id_cond.=")";
		$ProductionCond.=")";
	}
	else
	{
		$fromOrderIdCond=" and a.from_order_id in($poids)";
		$toOrderIdCond=" and a.to_order_id in($poids)";
		$salse_id_cond=" and a.id in($poids)";
		$po_cond=" and d.po_breakdown_id in($poids)";
		$po_cond1=" and c.po_breakdown_id in($poids)";
		$trans_po_id_cond=" and c.po_breakdown_id in($poids)";
		$ProductionCond=" and b.po_breakdown_id in($poids)";
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

		// fso int ref
		$int_ref_sql="SELECT c.id, c.po_number, c.grouping, b.booking_no, b.booking_mst_id
		from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c
		where a.BOOKING_ID=b.BOOKING_MST_ID and b.po_break_down_id=c.id and a.within_group=1 and b.booking_type in(1,4) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $salse_id_cond";
		// echo $int_ref_sql;die;
		$int_ref_sql_result=sql_select($int_ref_sql);
		$int_ref_arr=array();
		foreach ($int_ref_sql_result as $key => $row)
		{
			$int_ref_arr[$row[csf('booking_no')]] = $row[csf('grouping')];
		}
		// echo "<pre>";print_r($int_ref_arr);die;

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

	$yarn_info = array();
	if($all_productionIdArr)
	{
		$production_w_sql="SELECT a.booking_id, b.color_range_id, b.yarn_lot, b.yarn_count, c.po_breakdown_id, b.prod_id, b.color_id, b.stitch_length from inv_receive_master a,pro_grey_prod_entry_dtls b,order_wise_pro_details c,fabric_sales_order_mst d
		where a.id = b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.entry_form in (2) and c.is_sales = 1
		and c.trans_type = 1 and c.status_active = 1 and c.is_deleted = 0 and a.entry_form in (2) and a.status_active = 1
		and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.receive_basis in (2) and  a.roll_maintained=0 and a.company_id = $company_name  ".where_con_using_array($all_productionIdArr,0,'a.id')." group by a.booking_id,b.color_range_id,b.yarn_lot,b.yarn_count,c.po_breakdown_id,b.prod_id,b.color_id,b.stitch_length";
		//echo $production_w_sql;
		$production_w_sql_rslt = sql_select($production_w_sql);
		$booking_id_color_range = array();
		$booking_id_color_ids = array();
		$booking_id_stitch_length_arr = array();
		foreach ($production_w_sql_rslt as $production_row)
		{
			$bookingId_color_range[$production_row[csf("booking_id")]] = $production_row[csf("color_range_id")];
			$bookingId_color_ids[$production_row[csf("booking_id")]] = $production_row[csf("color_id")];
			$bookingId_stitch_length_arr[$production_row[csf("booking_id")]] = $production_row[csf("stitch_length")];

			$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_lot"][$production_row[csf("yarn_lot")]] = $production_row[csf("yarn_lot")];
			$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_count"][$production_row[csf("yarn_count")]] = $production_row[csf("yarn_count")];
		}

	}

	$production_sql = sql_select("SELECT a.color_range_id,b.barcode_no,a.yarn_lot,a.yarn_count,b.po_breakdown_id,a.prod_id,a.color_id,a.stitch_length from pro_grey_prod_entry_dtls a,pro_roll_details b where a.trans_id=0 and a.status_active=1 and a.id=b.dtls_id and b.entry_form in(2)");
	foreach ($production_sql as $production_row) {
		$barcode_color_range[$production_row[csf("barcode_no")]] = $production_row[csf("color_range_id")];
		$barcode_color_ids[$production_row[csf("barcode_no")]] = $production_row[csf("color_id")];
		$stitch_length_arr[$production_row[csf("barcode_no")]] = $production_row[csf("stitch_length")];

		$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_lot"][$production_row[csf("yarn_lot")]] = $production_row[csf("yarn_lot")];
		$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_count"][$production_row[csf("yarn_count")]] = $production_row[csf("yarn_count")];
	}
	//echo "Here 10";die;
	/*if(!empty($receive_barcodes)){
		if($db_type==2 && count($receive_barcodes)>999)
		{
			$barcode_chunk=array_chunk($receive_barcodes,999) ;
			$barcode_cond = " and (";
			$barcode_cond2 = " and (";

			foreach($barcode_chunk as $chunk_arr)
			{
				$barcode_cond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
				$barcode_cond2.=" d.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$barcode_cond = chop($barcode_cond,"or ");
			$barcode_cond .=")";
			$barcode_cond2 = chop($barcode_cond,"or ");
			$barcode_cond2 .=")";
		}
		else
		{
			$barcode_cond=" and b.barcode_no in(".implode(",",$receive_barcodes).")";
			$barcode_cond2=" and d.barcode_no in(".implode(",",$receive_barcodes).")";
		}

	}*/

	if($poids!="")
	{
		$trans_out_sql = "SELECT a.from_order_id,e.transaction_date,b.from_prod_id,d.qnty as transfer_out_qnty,d.barcode_no, b.from_program, a.entry_form from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and b.from_prod_id=c.id and a.from_order_id=f.id and b.status_active=1 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6 $fromOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and a.id=d.mst_id and d.entry_form=133 and b.id=d.dtls_id and d.status_active=1 and d.is_deleted=0
		union all
		SELECT a.from_order_id,e.transaction_date,b.from_prod_id,e.cons_quantity as transfer_out_qnty, null as barcode_no, b.from_program,a.entry_form
		from inv_item_transfer_mst a,inv_transaction e, inv_item_transfer_dtls b,product_details_master c,order_wise_pro_details d,fabric_sales_order_mst f
		where a.id = e.mst_id and e.id = b.trans_id and b.from_prod_id = c.id and b.id = d.dtls_id and d.po_breakdown_id = f.id and a.entry_form in(362) and a.transfer_criteria = 4 and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted=0 and e.transaction_type=6 $fromOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and d.entry_form in(362) and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and f.status_active = 1 and f.is_deleted = 0 $sales_to_order_no_cond  $style_ref_no_cond2
		";

		/* $trans_out_sql = "SELECT a.from_order_id,e.transaction_date,b.from_prod_id,d.qnty as transfer_out_qnty,d.barcode_no	from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and b.from_prod_id=c.id and a.from_order_id=f.id and b.status_active=1 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6 $fromOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and a.id=d.mst_id and d.entry_form=133 and b.id=d.dtls_id and d.status_active=1 and d.is_deleted=0"; */

		//echo $trans_out_sql;
		$trans_out_data = sql_select($trans_out_sql);

		foreach($trans_out_data as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			if($row[csf("entry_form")]==133)
			{
				$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
				$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			}
			else
			{
				$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
				$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			}


			if($transaction_date >= $date_frm){
				$transOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_out_qnty")];
			}else{
				if($transaction_date < $date_frm){
					$openingTransOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_out_qnty")];
				}
			}
		}

		$issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,b.prod_id,d.barcode_no,d.qnty as issue_qty, b.issue_qnty as dtls_issue_qnty, b.id as dtls_id,d.entry_form, null as stitch_length, b.program_no from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2 and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and d.roll_split_from=0 $po_cond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2
		union all
		SELECT d.po_breakdown_id,e.transaction_date, b.prod_id,null as barcode_no,e.cons_quantity as issue_qty,b.issue_qnty as dtls_issue_qnty,b.id as dtls_id,d.entry_form, b.stitch_length, b.program_no
   		from inv_issue_master a, inv_transaction e, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, fabric_sales_order_mst f
		where a.id = e.mst_id and e.id = b.trans_id and b.id=d.dtls_id and d.po_breakdown_id = f.id and a.item_category = 13 and a.entry_form = 16 and d.entry_form = 16 and e.transaction_type = 2 and a.status_active = 1 and a.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and f.status_active = 1 and f.is_deleted = 0 $po_cond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2";		//and d.is_returned<>1
		//echo $issue_sql;
		//Here quantity comes from details table as we neglected childeren barcode with " d.roll_split_from=0 " condition, beacuase  "color_ranges, stitch_lengths" are not coming with child barcodes

		$sql_iss=sql_select($issue_sql);

		$knit_issue_arr=array();
		foreach($sql_iss as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			if($row[csf("entry_form")]==61)
			{
				$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
				$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			}
			else if($row[csf("entry_form")]==16)
			{
				$color_ranges = $bookingId_color_range[$row[csf("program_no")]];
				$stitch_lengths = $row[csf("stitch_length")];
			}


			if($issue_dtls_chk[$row[csf("dtls_id")]]==""){
				$issue_dtls_chk[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				if($transaction_date >= $date_frm){
					$knit_issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_qty'] += $row[csf('dtls_issue_qnty')];
				}else{
					if($transaction_date < $date_frm){
						$opening_issue[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_qty'] += $row[csf('dtls_issue_qnty')];
					}
				}
			}

		}

		unset($sql_iss);


		$sql_issue_rtn = "SELECT a.booking_no,a.entry_form, b.prod_id,e.transaction_date,d.po_breakdown_id as po_id,d.qnty as issue_return_qty, d.barcode_no from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details d,fabric_sales_order_mst f where a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=84 and e.transaction_type=4	and d.entry_form=84 and a.receive_basis in(0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=f.id $po_cond $store_cond $pocompany_cond2 $booking_no_cond2 $refBooking_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2
		union all
		SELECT a.booking_no,a.entry_form, b.prod_id,b.transaction_date,c.po_breakdown_id as po_id,b.cons_quantity as issue_return_qty, null as barcode_no
		FROM inv_receive_master a,inv_transaction b, order_wise_pro_details c,fabric_sales_order_mst d
	  	WHERE a.id = b.mst_id AND b.id = c.trans_id AND c.PO_BREAKDOWN_ID = d.id AND a.item_category = 13 AND a.entry_form = 51 AND b.transaction_type = 4 AND c.entry_form = 51 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.receive_basis in(3) $salesIdCond $store_cond3 $pocompany_cond3 $booking_no_cond3 $refBooking_cond3 $buyer_id_cond3 $sales_order_year_condition3 $within_group_cond3";
		//echo $sql_issue_rtn;
		$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
		$inssue_return_array=array();
		foreach($sql_issue_rtn_rslt as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			if($row[csf("entry_form")]==51)
			{
				$color_ranges = $bookingId_color_range[$row[csf("booking_no")]];
				$stitch_lengths = $bookingId_stitch_length_arr[$row[csf("booking_no")]];
			}
			else
			{
				$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
				$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			}


			if($transaction_date >= $date_frm){
				$inssue_return_array[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_return_qty'] += $row[csf('issue_return_qty')];
			}else{
				if($transaction_date < $date_frm){
					$opening_issue_return[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_return_qty'] += $row[csf('issue_return_qty')];
				}
			}
		}
		unset($sql_issue_rtn_rslt);

		foreach($trans_in_data as $row)
		{
			$prod_id .= $row[csf("from_prod_id")].",";
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			$color_ids = $barcode_color_ids[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*1*".$row[csf("from_order_id")]."*".$color_ids."_";
			}else{
				if($transaction_date < $date_frm){
					$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*2*".$row[csf("from_order_id")]."*".$color_ids."_";
					$transferInOpening[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_in_qnty")];
				}
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
	$table_width = ($cbo_store_wise==1)?"3300":"3210";



	/*echo "here";
	echo "<pre>";
	print_r($prodWiseSalesDataStatus);
	die;*/
	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<fieldset style="width:2580">
		<table cellpadding="0" cellspacing="0" width="1400">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:16px">
					<strong>
						<?
						echo $company_arr[str_replace("'","",$cbo_company_id)];
						?>
					</strong>
				</td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
			<thead>
				<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="60" rowspan="2">Company</th>
					<th width="80" rowspan="2">LC Company</th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="100" rowspan="2">IR/IB</th>
					<th width="100" rowspan="2">Style No</th>
					<th width="100" rowspan="2">Job No</th>
					<th width="50" rowspan="2">Season</th>
					<th width="80" rowspan="2">Product Dept.</th>
					<th width="80" rowspan="2">Style Desc.</th>
					<th width="80" rowspan="2">Product Category</th>
					<th width="130" rowspan="2">Booking No</th>
					<th width="80" rowspan="2">Booking Type</th>
					<th width="130" rowspan="2">FSO</th>

					<th colspan="7">Fabric Details</th>
					<th colspan="7">Receive Details</th>
					<th colspan="4">Issue Details</th>
					<th colspan="4">Stock Details</th>
				</tr>
				<tr>
					<th width="70">Product ID</th>
					<th width="90">Construction</th>
					<th width="200">Composition</th>
					<th width="70">GSM</th>
					<th width="140">Color</th>
					<th width="70">Color Range</th>
					<th width="70">Stitch Length</th>
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
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search" align="left">
				<?
				$i=1;
				$tot_recv_qty=0;

				foreach($prodWiseSalesDataStatus as $poId=>$prodArr)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					foreach ($prodArr as $prodId=>$colorRange)
					{
						foreach ($colorRange as $crange=>$stitchLength)
						{

							$opening=$iss_qty=$trans_out_qty=0;
							foreach ($stitchLength as $slength=>$row)
							{

								$yarn_lot = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_lot"]));
								$yarn_count = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_count"]));

								$all_prodData = explode("_",chop($row,"_"));
								$recv_qnty=$trans_in_qty=$opening_recv=$opening_trans=0;
								$color_ids="";$color_names="";
								foreach ($all_prodData as $prodData)
								{
									$data = explode("*",$prodData);
									if($data[5] == 1)
									{
										if($data[6] == 1)
										{
											$recv_qnty += $data[3]*1;
										}
									}

									if($data[5] == 3)
									{
										if($data[6] == 1)
										{
											$trans_in_qty += $data[3]*1;
										}

										$from_order_id = $data[7];

										$yarn_lot = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_lot"]));
										$yarn_count = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_count"]));
									}
									$detarmination_id = $data[0];
									$store_id = $data[4];
									$color_ids .= $data[8]."**";//$color_arr[
								}
								$yarn_lot = implode(",",array_filter(array_unique(explode(",", $yarn_lot))));

								$color_ids_arr = array_filter(array_unique(explode("**",rtrim($color_ids,","))));
								$color_ids2=implode(",", $color_ids_arr);
								$color_ids_arr2=array_filter(array_unique(explode(",", $color_ids2)));

								$color_names="";
								foreach ($color_ids_arr2 as $color)
								{
									//echo $color.'<br>';
									$color_names.= $color_arr[$color].",";
								}

								$issue_return_qnty  = $inssue_return_array[$poId][$prodId][$crange][$slength]['issue_return_qty'];
								$iss_qty 			= $knit_issue_arr[$poId][$prodId][$crange][$slength]['issue_qty'];

								$opening_receive  = $receiveOpening[$poId][$prodId][$crange][$slength];
								$opening_trans_in = $transferInOpening[$poId][$prodId][$crange][$slength];

								$opening_title = "Receive=".number_format($opening_receive,2) ."+". number_format($opening_trans_in,2)."\nIssue=".number_format($opening_issue[$poId][$prodId][$crange][$slength]['issue_qty'],2) ."+". number_format($openingTransOutQnty[$poId][$prodId][$crange][$slength],2);

								$opening = ($opening_receive+$opening_trans_in)-($opening_issue[$poId][$prodId][$crange][$slength]['issue_qty']+$openingTransOutQnty[$poId][$prodId][$crange][$slength]);

								// roll wise $recv_ret_qty page did not developed yet
								$recv_tot_qty  = ($recv_qnty+$issue_return_qnty+$trans_in_qty);
								$trans_out_qty = $transOutQnty[$poId][$prodId][$crange][$slength];
								$iss_tot_qty   = ($iss_qty+$trans_out_qty);

								$stock_qty 	   = $opening+($recv_tot_qty-$iss_tot_qty);
								//$stock_qty     = number_format($stock_qty,2,".","");
								if($stock_qty < .001)
								{
									$stock_qty = 0;
								}

								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['max_date'],'','',1),date("Y-m-d"));
								$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['min_date'],'','',1),date("Y-m-d"));

								$product_category 	= $job_info[$salesData[$poId]['job_no']]["product_category"];
								$product_dept 		= $job_info[$salesData[$poId]['job_no']]["product_dept"];
								$season 			= $season_arr[$job_info[$salesData[$poId]['job_no']]["season"]];
								$style_ref_no 		= $job_info[$salesData[$poId]['job_no']]["style_ref_no"];
								$int_ref=$int_ref_arr[$salesData[$poId]['booking_no']];


								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qty == $txt_qnty) || $get_upto_qnty == 0))
								{

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
											<td width="100"><p class="word_wrap_break"><? echo $int_ref; ?> </p></td>
											<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['style_ref_no']; ?></p></td>
											<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['job_no']; ?></p></td>
											<td width="50"><p class="word_wrap_break"><? echo $season; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $product_dept; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $style_ref_no; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $product_category; ?></p></td>
											<td width="130"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_no']; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_type'];//$bookingType; ?></p></td>
											<td width="130"title="<? echo $poId;?>"><p class="word_wrap_break"><? echo $salesData[$poId]['fso_no']; ?></p></td>

											<td width="70"><p><? echo $prodId;?></p></td>
											<td width="90"><p class="word_wrap_break"><? echo $constructionArr[$detarmination_id]; ?></p></td>
											<td width="200"><p class="word_wrap_break"><? echo $composition_arr[$detarmination_id]; ?></p></td>
											<td width="70" align="center"><p><? echo $data[1]; ?></p></td>
											<td width="140" align="center"><p class="word_wrap_break"><? echo rtrim($color_names,", "); ?></p></td>
											<td width="70"><p class="word_wrap_break" title="<? echo $crange;?>"><? echo $color_range[$crange];?></p></td>
											<td width="70"><p class="word_wrap_break"><? echo $slength;?></p></td>
											<td width="80" align="center"><p><? echo $data[2]; ?></p></td>

											<td width="90"><p><? echo $yarn_lot; ?></p></td>
											<td width="90">
												<p><?
												$counts="";
												$yarn_counts = array_unique(explode(",",$yarn_count));
												foreach ($yarn_counts as $yarn_count) {
													$counts .= $yarn_count_arr[$yarn_count].",";
												}
												echo trim($counts,", ");
												?></p>
											</td>

											<td width="90" align="right" title="<? echo $opening_title;?>"><? echo ($opening==-0)?0:number_format($opening,2); ?></td>
											<td width="90" align="right"><? echo number_format($recv_qnty,2); ?></td>
											<td width="90" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength; ?>','trans_in_popup');"><? echo number_format($trans_in_qty,2);?></a></td>
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
									//else if( $cbo_value_with==1)
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
											<td width="60"><p class="word_wrap_break"><? echo $company_arr[$salesData[$poId]['working_company_id']]; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $company_arr[$salesData[$poId]['lc_company_id']]; ?> </p></td>
											<td width="100"><p class="word_wrap_break"><? echo $buyer_arr[$salesData[$poId]['buyer_id']]; ?> </p></td>
											<td width="100"><p class="word_wrap_break"><? echo $int_ref; ?> </p></td>
											<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['style_ref_no']; ?></p></td>
											<td width="100"><p class="word_wrap_break"><? echo $salesData[$poId]['job_no']; ?></p></td>
											<td width="50"><p class="word_wrap_break"><? echo $season; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $product_dept; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $style_ref_no; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $product_category; ?></p></td>
											<td width="130"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_no']; ?></p></td>
											<td width="80"><p class="word_wrap_break"><? echo $salesData[$poId]['booking_type']; ?></p></td>
											<td width="130"><p class="word_wrap_break"><? echo $salesData[$poId]['fso_no']; ?> </p></td>

											<td width="70"><p><? echo $prodId;?></p></td>
											<td width="90"><p class="word_wrap_break"><? echo $constructionArr[$detarmination_id]; ?></p></td>
											<td width="200"><p class="word_wrap_break"><? echo $composition_arr[$detarmination_id]; ?></p></td>
											<td width="70" align="center"><p><? echo $data[1]; ?></p></td>
											<td width="140" align="center"><p class="word_wrap_break"><? echo rtrim($color_names,", "); ?></p></td>
											<td width="70"><p class="word_wrap_break" title="<? echo $crange;?>"><? echo  $color_range[$crange];?></p></td>
											<td width="70"><p class="word_wrap_break"><? echo $slength;?></p></td>
											<td width="80" align="center"><p><? echo $data[2]; ?></p></td>

											<td width="90"><p><? echo $yarn_lot; ?></p></td>
											<td width="90">
												<p><?
												$counts="";
												$yarn_counts = array_unique(explode(",",$yarn_count));
												foreach ($yarn_counts as $yarn_count) {
													$counts .= $yarn_count_arr[$yarn_count].",";
												}
												echo trim($counts,", ");
												?></p>
											</td>

											<td width="90" align="right" title="<? echo $opening_title;?>"><? echo ($opening==-0)?0:number_format($opening,2); ?></td>
											<td width="90" align="right"><? echo number_format($recv_qnty,2); ?></td>
											<td width="90" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength; ?>','trans_in_popup');"><? echo number_format($trans_in_qty,2);?></a></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>

											<td width="90" align="right"><p><? echo number_format($iss_qty,2); ?></p></td>
											<td width="90" align="right"><p><? echo number_format($recv_ret_qty,2);?></p></td>
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength;; ?>','trans_out_popup');"><? echo number_format($trans_out_qty,2); ?></a></td>
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
					<th width="100"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="130"></th>
					<th width="80"></th>
					<th width="130"></th>

					<th width="70"></th>
					<th width="90"></th>
					<th width="200"></th>
					<th width="70"></th>
					<th width="140"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="80"></th>

					<th width="90"></th>
					<th width="90">Grand Total = </th>

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

if($action=="report_generate2") // Show 2
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
	$order_no 		= trim(str_replace("'","",$txt_order_no));
	$order_id 		= str_replace("'","",$hide_order_id);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);
	$int_ref 		= trim(str_replace("'","",$txt_int_ref));

	$store_arr 	 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$company_arr 	= return_library_array( "select id,company_name from lib_company",'id','company_name');
	$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	$season_arr  	= return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
	$yarn_brand_arr = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
	$color_library 	= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');



	if($pocompany_id!=0 || $pocompany_id!=""){

		if($within_group==1)
		{
			$pocompany_cond="and d.po_company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
		}else {
			$pocompany_cond="and d.company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
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
			$buyer_id_cond2=" and f.po_buyer in (".str_replace("'","",$po_buyer_id).")";
		}else {

			$buyer_id_cond=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond2=" and f.buyer_id in (".str_replace("'","",$po_buyer_id).")";
		}
	}

	$date_cond="";

	if( $date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}

		$date_cond = " and e.transaction_date between '$date_from' and '$date_to' ";
	}


	if ($order_id=='') $order_no_cond=""; else $order_no_cond="and d.id='$order_id'";
	if ($order_id=='') $toOrderIdCond=""; else $toOrderIdCond="and a.to_order_id='$order_id'";

	if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and d.job_no like '%$order_no%'";
	if ($order_no=='') $sales_to_order_no_cond=""; else $sales_to_order_no_cond=" and f.job_no like '%$order_no%'";


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

	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and a.booking_no='$program_no'";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no'";
	} else {
		$booking_no_cond="";
	}

	if ($int_ref!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b, FABRIC_SALES_ORDER_MST c
		where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID and c.within_group=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row)
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and d.within_group='$within_group' ";
	}
	else
	{
		$within_group_cond = "";
	}

	$rcv_weight_sql="SELECT a.id as production_id ,a.recv_number,a.booking_id,a.booking_no,b.body_part_id,b.width,b.gsm,b.order_id,
		c.po_breakdown_id AS po_id, b.febric_description_id,b.prod_id,d.id as sales_id,d.job_no FROM inv_receive_master a,pro_grey_prod_entry_dtls b,order_wise_pro_details c,fabric_sales_order_mst d
		WHERE a.id = b.mst_id AND b.id=c.dtls_id AND c.po_breakdown_id=d.id AND c.entry_form IN (2) AND c.IS_SALES = 1
		AND c.trans_type = 1 AND c.status_active = 1 AND c.is_deleted = 0 AND a.entry_form IN (2) AND a.status_active = 1
		AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.receive_basis IN (2,4,10) AND  a.roll_maintained=0 AND a.company_id = $company_name $within_group_cond $order_no_cond $booking_no_cond $program_no_cond $sales_order_year_condition $pocompany_cond $buyer_id_cond $sales_order_no_cond $refBooking_cond
		group by a.id,a.recv_number,a.booking_id,a.booking_no,b.body_part_id,b.width,b.gsm,b.order_id,c.po_breakdown_id,b.febric_description_id,b.prod_id, d.id,d.job_no ";

	//echo $rcv_weight_sql;
	$rcv_weight_sql_result=sql_select($rcv_weight_sql);

	$productionIdsChk=array();
	$productionIdsArr=array();
	foreach($rcv_weight_sql_result as $row )
	{
		if($productionIdsChk[$row[csf('production_id')]]=='')
		{
			$productionIdsChk[$row[csf('production_id')]] = $row[csf('production_id')];
			array_push($productionIdsArr, $row[csf('production_id')]);
		}
	}

	//echo "<pre>";print_r($salesIdsArr);

	if(!empty($productionIdsArr))
	{
		$grey_rcv_weight_sql="SELECT a.id as production_id ,a.recv_number,a.booking_id, a.booking_no, sum(b.grey_receive_qnty) as receive_qty FROM inv_receive_master a,pro_grey_prod_entry_dtls b
		WHERE a.id = b.mst_id and a.entry_form in (22) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 ".where_con_using_array($productionIdsArr,0,'a.booking_id')."
		group by a.id, a.recv_number,a.booking_id,a.booking_no";
		//echo $rcv_weight_sql;
		$grey_rcv_weight_sql_result=sql_select($grey_rcv_weight_sql);
		$greyRcvQntyArr = array();
		foreach($grey_rcv_weight_sql_result as $row )
		{
			$greyRcvQntyArr[$row[csf('booking_no')]]['receive_qty'] = $row[csf('receive_qty')];
		}
		unset($grey_rcv_weight_sql_result);
		//echo "<pre>";print_r($greyRcvQntyArr);
	}

	$salesIdsChk = array();
	$salesIdsArr=array();
	$rcvQntyArr=array();
	$productionIdsChk=array();
	$productionIdsArr=array();
	foreach($rcv_weight_sql_result as $row )
	{
		if($salesIdsChk[$row[csf('sales_id')]]=='')
		{
			$salesIdsChk[$row[csf('sales_id')]] = $row[csf('sales_id')];
			array_push($salesIdsArr, $row[csf('sales_id')]);
		}

		$rcvQntyArr[$row[csf('job_no')]][$row[csf('febric_description_id')]][$row[csf('gsm')]]['receive_qty'] += $greyRcvQntyArr[$row[csf('recv_number')]]['receive_qty'];
	}
	unset($rcv_weight_sql_result);
	//echo "<pre>";print_r($rcvQntyArr);


	// $rcv_weight_sql =  "SELECT a.id, a.recv_number, sum(b.grey_receive_qnty) as receive_qty, c.booking_no as program_no, f.job_no, b.febric_description_id, b.gsm from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls e, fabric_sales_order_mst f where a.id=b.mst_id and a.booking_no=c.recv_number and d.id=e.dtls_id and e.po_id=f.id and a.entry_form = 22 and c.entry_form = 2 and a.status_active = 1 and a.is_deleted = 0 and c.company_id=$company_name  group by a.id, a.recv_number, c.booking_no, f.job_no, b.febric_description_id, b.gsm";
	//echo $rcv_weight_sql;

	$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no, d.id as sales_id
	from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
	where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $order_no_cond $booking_no_cond $program_no_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $sales_order_no_cond $refBooking_cond
	group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no,d.id";
	//echo $sql; die;

	$sql_result=sql_select($sql);


	foreach( $sql_result as $row )
	{
		if($salesIdsChk[$row[csf('sales_id')]]=='')
		{
			$salesIdsChk[$row[csf('sales_id')]] = $row[csf('sales_id')];
			array_push($salesIdsArr, $row[csf('sales_id')]);
		}

		$rcvQntyArr[$row[csf('job_no')]][$row[csf('febric_description_id')]][$row[csf('gsm')]]['receive_qty'] += $row[csf('receive_qty')];
	}
	//var_dump($salesIdsArr);
	// echo "<pre>";
	// print_r($rcvQntyArr);


	$booking_sql = "SELECT A.JOB_NO, B.DETERMINATION_ID, B.GSM_WEIGHT,B.COLOR_ID, B.GREY_QNTY_BY_UOM
		FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B
		WHERE A.ID = B.MST_ID  AND A.COMPANY_ID = ".$company_name."  AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 ".where_con_using_array($salesIdsArr,0,'A.ID')."
		ORDER BY A.ID";
		//echo $booking_sql;

		$booking_sql_rslt=sql_select($booking_sql);
		$bookingQntyArr = array();
		foreach ($booking_sql_rslt as $row)
		{
			$bookingQntyArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_ID']]['GREY_QNTY_BY_UOM'] += $row['GREY_QNTY_BY_UOM'];
		}

		// echo "<pre>";
		// print_r($bookingQntyArr);



	$main_sql = "SELECT
			A.BOOKING_NO,
			B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA,
			C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY ,
			E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID,
			F.ID AS SALES_ID, F.GREY_QTY, B.MACHINE_GG, E.PO_JOB_NO, E.COMPANY_ID, E.CUSTOMER_BUYER, B.COLOR_RANGE, B.STITCH_LENGTH
			FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F
			WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND C.IS_SALES = 1  AND A.COMPANY_ID = ".$company_name."  AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ".where_con_using_array($salesIdsArr,0,'E.ID')."
			ORDER BY B.ID, A.BOOKING_NO";

	// echo $main_sql;die;
	$main_sql_rslt=sql_select($main_sql);
	$mainArr = array();
	$programIdsArr = array();
	foreach ($main_sql_rslt as $row)
	{


		if($duplicate_check[$row['DTLS_ID']] != $row['DTLS_ID'])
		{
			$duplicate_check[$row['DTLS_ID']] = $row['DTLS_ID'];

			if($prog_ids_check[$row['ID']] == '')
			{
				$prog_ids_check[$row['ID']] = $row['ID'];
				array_push($programIdsArr,$row['ID']);
			}


			//for color
			$color_arr = array();
			$exp_color = array();
			$exp_color = explode(",", $row['COLOR_ID']);
			foreach ($exp_color as $key=>$val)
			{
				$color_arr[$val] = $color_library[$val];
			}
			//end for color

			//for color_range
			$color_range_arr = array();
			$exp_color_range = array();
			$exp_color_range = explode(",", $row['COLOR_RANGE']);
			foreach ($exp_color_range as $key=>$val)
			{
				$color_range_arr[$val] = $color_range[$val];
			}
			//end for color_range

			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['JOB_NO'] = $row['JOB_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['DETERMINATION_ID'] = $row['DETERMINATION_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['CUSTOMER_BUYER'] = $row['CUSTOMER_BUYER'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_DIA'] = $row['FABRIC_DIA'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_COLOR_ID'] = $row['COLOR_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_COLOR'] = implode(', ', $color_arr);
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['COLOR_RANGE'] = implode(', ', $color_range_arr);
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];

		}
	}
	unset($main_sql_rslt);
	// echo "<pre>";
	// print_r($mainArr);

	$rcv_weight_sql =  "SELECT A.ID, A.RECV_NUMBER, SUM(B.GREY_RECEIVE_QNTY) AS QNTY, C.BOOKING_NO AS PROGRAM_NO FROM INV_RECEIVE_MASTER A, PRO_GREY_PROD_ENTRY_DTLS B, INV_RECEIVE_MASTER C WHERE A.ID=B.MST_ID AND A.BOOKING_NO=C.RECV_NUMBER AND A.ENTRY_FORM = 22 AND C.ENTRY_FORM = 2 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 ".where_con_using_array($programIdsArr,1,'C.BOOKING_NO')." GROUP BY A.ID, A.RECV_NUMBER, C.BOOKING_NO";
	//echo $rcv_weight_sql;

	//echo $rcv_weight_sql;//die;
	$rcv_weight_rslt=sql_select($rcv_weight_sql);
	$knitProQtyArr = array();
	foreach($rcv_weight_rslt as $row)
	{
		$knitProQtyArr[$row['PROGRAM_NO']] += $row['QNTY'];
	}
	unset($rcv_weight_rslt);
	//echo "<pre>";print_r($knitProQtyArr);

	$rcv_sql =  "SELECT A.BOOKING_NO  AS PROGRAM_NO,B.BOOKING_ID, SUM(A.QNTY) QNTY FROM PRO_ROLL_DETAILS A, INV_RECEIVE_MASTER B WHERE A.ENTRY_FORM = 58 AND A.MST_ID = B.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 ".where_con_using_array($programIdsArr,1,'A.BOOKING_NO')."  GROUP BY A.BOOKING_NO,B.BOOKING_ID";

	//echo $rcv_sql;//die;
	$rcv_sql_rslt=sql_select($rcv_sql);
	foreach($rcv_sql_rslt as $row)
	{
		$knitProQtyArr[$row['PROGRAM_NO']] += $row['QNTY'];
	}


	$sql_requ = "SELECT KNIT_ID, REQUISITION_NO, PROD_ID, YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($programIdsArr,0,'KNIT_ID')." ";
	//echo $sql_requ;
	$sql_requ_result = sql_select($sql_requ);
	$requArr = array();
	foreach ($sql_requ_result as $row)
	{
		$requArr[$row['KNIT_ID']]['prod_id']  .= $row['PROD_ID'].', ';
	}
	//var_dump($requArr);

	$product_details_array = array();
	$yarn_info_sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND COMPANY_ID=".$company_name." AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PROD_ID FROM PPL_YARN_REQUISITION_ENTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($programIdsArr,0,'KNIT_ID').")";

	//echo $yarn_info_sql;

	$yarn_info_result = sql_select($yarn_info_sql);
	foreach ($yarn_info_result as $row)
	{
		$product_details_array[$row['ID']]['count'] = $yarn_count_arr[$row['YARN_COUNT_ID']];
		$product_details_array[$row['ID']]['lot'] = $row['LOT'];
		$product_details_array[$row['ID']]['brand'] = $yarn_brand_arr[$row['BRAND']];
	}
	unset($yarn_info_result);
	// echo "<pre>";
	// print_r($product_details_array);

	$prod_info_sql = "SELECT ID, DETARMINATION_ID, GSM FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=13 AND COMPANY_ID=".$company_name." AND STATUS_ACTIVE=1 AND IS_DELETED=0   ";
	//echo $prod_info_sql; //".where_con_using_array($prodIdsArr,0,'ID')."

	$prod_info_result = sql_select($prod_info_sql);
	$prodInfoArr = array();
	foreach($prod_info_result as $row )
	{
		$prodInfoArr[$row[csf('ID')]]['detarmination_id'] = $row[csf('DETARMINATION_ID')];
		$prodInfoArr[$row[csf('ID')]]['gsm'] = $row[csf('GSM')];
	}

	$issue_rtn_gross_sql = "SELECT a.recv_number,e.company_id,e.job_no,b.prod_id,sum(b.cons_quantity) as issue_rtn_qty
		from inv_receive_master a,inv_transaction b,ppl_planning_info_entry_dtls c,ppl_planning_entry_plan_dtls d,
		fabric_sales_order_mst e
		where a.id = b.mst_id
			and a.booking_id=c.id
			and c.id = d.dtls_id
			and d.po_id = e.id
			and a.entry_form in (51)
			and a.item_category = 13
			and a.company_id = 1
			and b.status_active = 1
			and b.is_deleted = 0
			and d.status_active = 1
			and e.is_deleted = 0
			and b.status_active = 1
			and b.is_deleted = 0
			and b.transaction_type = 4
			".where_con_using_array($salesIdsArr,0,'e.id')."
		group by a.recv_number,
			e.company_id,
			e.job_no,
			b.prod_id";
		//echo $issue_rtn_gross_sql;

		$issue_rtn_gross_sql_result = sql_select($issue_rtn_gross_sql);
		$issueRtnQtyArr = array();
		foreach ($issue_rtn_gross_sql_result as $row)
		{
			$detarmination_id = $prodInfoArr[$row[csf('prod_id')]]['detarmination_id'];
			$gsm = $prodInfoArr[$row[csf('prod_id')]]['gsm'];
			$issueRtnQtyArr[$row[csf('job_no')]][$detarmination_id][$gsm]['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
		}
		unset($issue_rtn_gross_sql_result);
		//echo "<pre>";print_r($issueRtnQtyArr);




	$issue_rtn_sql = "SELECT A.RECV_NUMBER,SUM(B.QNTY) AS ISSUE_RTN_QTY,B.PO_BREAKDOWN_ID AS PO_ID, C.PROD_ID,C.FEBRIC_DESCRIPTION_ID,C.GSM,C.COLOR_ID,D.COMPANY_ID, D.JOB_NO
	FROM INV_RECEIVE_MASTER A,INV_TRANSACTION E,PRO_GREY_PROD_ENTRY_DTLS C,PRO_ROLL_DETAILS B,FABRIC_SALES_ORDER_MST D
	WHERE A.ID=E.MST_ID AND E.ID=C.TRANS_ID AND C.ID=B.DTLS_ID AND B.PO_BREAKDOWN_ID=D.ID  AND B.ENTRY_FORM IN(84) AND C.TRANS_ID>0 AND A.ITEM_CATEGORY=13 AND D.COMPANY_ID=".$company_name." AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND
	C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND
	E.TRANSACTION_TYPE=4 ".where_con_using_array($salesIdsArr,0,'D.ID')."
	GROUP BY A.RECV_NUMBER,B.PO_BREAKDOWN_ID, C.PROD_ID,C.FEBRIC_DESCRIPTION_ID,C.GSM,C.COLOR_ID,D.COMPANY_ID, D.JOB_NO";

	//echo $issue_rtn_sql;

	$issue_rtn_sql_result = sql_select($issue_rtn_sql);
	//$issueRtnQtyArr = array();
	foreach ($issue_rtn_sql_result as $row)
	{
		$issueRtnQtyArr[$row['JOB_NO']][$row['FEBRIC_DESCRIPTION_ID']][$row['GSM']]['issue_rtn_qty'] += $row['ISSUE_RTN_QTY'];
	}
	unset($issue_rtn_sql_result);
	// echo "<pre>";
	// print_r($issueRtnQtyArr);


	$issue_weight_sql ="SELECT a.issue_number,e.transaction_date,b.prod_id,f.job_no,e.cons_quantity as issue_qty
		FROM inv_issue_master              a,
				inv_transaction               e,
				inv_grey_fabric_issue_dtls    b,
				fabric_sales_order_mst        f,
				ppl_planning_info_entry_dtls  g,
				ppl_planning_entry_plan_dtls  h
		WHERE     a.status_active = 1
				AND a.is_deleted = 0
				AND a.id = e.mst_id
				AND e.id = b.trans_id
				AND g.id = h.dtls_id
				AND h.po_id = f.id
				AND b.program_no = g.id
				AND a.item_category = 13
				AND a.entry_form = 16
				AND e.transaction_type = 2
				AND e.status_active = 1
				AND e.is_deleted = 0
				AND b.status_active = 1
				AND b.is_deleted = 0
				".where_con_using_array($salesIdsArr,0,'f.id')."";


	//echo $issue_weight_sql;
	$issue_weight_sql_result = sql_select($issue_weight_sql);

	$issueQtyArr = array();
	foreach($issue_weight_sql_result as $row )
	{
		$date_frm=date('Y-m-d',strtotime($date_from));
		$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

		if($transaction_date >= $date_frm)
		{
			$detarmination_id = $prodInfoArr[$row[csf('prod_id')]]['detarmination_id'];
			$gsm = $prodInfoArr[$row[csf('prod_id')]]['gsm'];

			$issueQtyArr[$row[csf('job_no')]][$detarmination_id][$gsm]['issue_qnty'] += $row[csf('issue_qty')];
		}
	}
	//echo "<pre>";print_r($issueQtyArr);


	$issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty,f.job_no from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2 and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($salesIdsArr,0,'f.id')." ";

	//echo $issue_sql;// and d.is_returned<>1

	$issue_sql_result = sql_select($issue_sql);
	$prodIdsArr = array();
	foreach($issue_sql_result as $row )
	{
		if($prodIdsChk[$row[csf('prod_id')]]=='')
		{
			$prodIdsChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
			array_push($prodIdsArr, $row[csf('prod_id')]);
		}
	}


	//var_dump($prodInfoArr);

	foreach($issue_sql_result as $row )
	{
		$date_frm=date('Y-m-d',strtotime($date_from));
		$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

		if($transaction_date >= $date_frm)
		{
			$detarmination_id = $prodInfoArr[$row[csf('prod_id')]]['detarmination_id'];
			$gsm = $prodInfoArr[$row[csf('prod_id')]]['gsm'];

			$issueQtyArr[$row[csf('job_no')]][$detarmination_id][$gsm]['issue_qnty'] += $row[csf('issue_qty')];
		}
	}
	/* echo "<pre>";
	print_r($issueQtyArr); */

	$trans_out_w_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID,E.JOB_NO, C.CONS_QUANTITY AS TRANSFER_OUT_QNTY FROM INV_ITEM_TRANSFER_MST A, INV_ITEM_TRANSFER_DTLS B,
	INV_TRANSACTION C, PRODUCT_DETAILS_MASTER D, FABRIC_SALES_ORDER_MST E WHERE A.ID = B.MST_ID AND A.ID = C.MST_ID AND A.FROM_ORDER_ID=E.ID AND B.FROM_PROD_ID = D.ID AND C.ID = B.TO_TRANS_ID AND A.ENTRY_FORM = 362 AND A.TRANSFER_CRITERIA = 4 AND C.TRANSACTION_TYPE = 6 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND E.STATUS_ACTIVE = 1 AND E.IS_DELETED = 0 ".where_con_using_array($salesIdsArr,0,'E.ID')." GROUP BY A.FROM_ORDER_ID,B.FROM_PROD_ID,E.JOB_NO,C.CONS_QUANTITY";
	//echo $trans_out_w_sql;
	$trans_out_w_sql_result = sql_select($trans_out_w_sql);
	$trnsOutQtyArr = array();
	foreach($trans_out_w_sql_result as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['gsm'];

		$trnsOutQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['TRANSFER_OUT_QNTY'] += $row['TRANSFER_OUT_QNTY'];
	}
	unset($trans_out_w_sql_result);
	//echo "<pre>";print_r($trnsOutQtyArr);die;


	$trans_out_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID,SUM(D.QNTY) AS TRANSFER_OUT_QNTY,F.JOB_NO FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F WHERE A.STATUS_ACTIVE=1 AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.FROM_ORDER_ID=F.ID AND B.STATUS_ACTIVE=1 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=6  AND A.ID=D.MST_ID AND D.ENTRY_FORM=133 AND B.ID=D.DTLS_ID AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.FROM_ORDER_ID,B.FROM_PROD_ID,F.JOB_NO";
	//echo $trans_out_sql;
	$trans_out_rslt = sql_select($trans_out_sql);


	foreach($trans_out_rslt as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['gsm'];

		$trnsOutQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['TRANSFER_OUT_QNTY'] += $row['TRANSFER_OUT_QNTY'];
	}
	unset($trans_out_rslt);
	// echo "<pre>";
	// print_r($trnsOutQtyArr);

	$trans_in_w_sql = "SELECT A.TO_ORDER_ID,B.TO_PROD_ID,E.JOB_NO, C.CONS_QUANTITY AS TRANSFER_IN_QNTY FROM INV_ITEM_TRANSFER_MST A, INV_ITEM_TRANSFER_DTLS B,
	INV_TRANSACTION C, PRODUCT_DETAILS_MASTER D, FABRIC_SALES_ORDER_MST E WHERE A.ID = B.MST_ID AND A.ID = C.MST_ID AND A.TO_ORDER_ID=E.ID AND B.FROM_PROD_ID = D.ID AND C.ID = B.TRANS_ID AND A.ENTRY_FORM = 362 AND A.TRANSFER_CRITERIA = 4 AND C.TRANSACTION_TYPE = 5 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND E.STATUS_ACTIVE = 1 AND E.IS_DELETED = 0 ".where_con_using_array($salesIdsArr,0,'E.ID')." GROUP BY A.TO_ORDER_ID,B.TO_PROD_ID,E.JOB_NO,C.CONS_QUANTITY";
	//echo $trans_in_w_sql;
	$trans_in_w_sql_result = sql_select($trans_in_w_sql);
	$trnsInQtyArr = array();
	foreach($trans_in_w_sql_result as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('TO_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('TO_PROD_ID')]]['gsm'];

		$trnsInQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['transfer_in_qnty'] += $row['TRANSFER_IN_QNTY'];
	}
	unset($trans_in_w_sql_result);

	$trans_in_sql = "SELECT A.TO_ORDER_ID,B.TO_PROD_ID,SUM(D.QNTY) AS TRANSFER_IN_QNTY,F.JOB_NO FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F WHERE  A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.TO_ORDER_ID=F.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=5  AND A.ID=D.MST_ID AND D.ENTRY_FORM=133 AND B.ID=D.DTLS_ID  ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.TO_ORDER_ID,B.TO_PROD_ID,F.JOB_NO";
	//echo $trans_in_sql;

	$trans_in_sql_result = sql_select($trans_in_sql);
	foreach($trans_in_sql_result as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('TO_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('TO_PROD_ID')]]['gsm'];

		$trnsInQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['transfer_in_qnty'] += $row['TRANSFER_IN_QNTY'];
	}
	unset($trans_in_sql_result);
	// echo "<pre>";
	// print_r($trnsInQtyArr);


	ob_start();
	$table_width = "3150";

	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<fieldset style="width:3200">
		<table cellpadding="0" cellspacing="0" width="3140">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:16px">
					<strong>
						<?
						echo $company_arr[str_replace("'","",$cbo_company_id)];
						?>
					</strong>
				</td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td colspan="24" width="100%" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? if($date_from!="" || $date_to!="") echo "From : ".change_date_format($date_from)." To : ".change_date_format($date_to)."" ;?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
			<thead>
				<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="130" rowspan="2">Company </th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="100" rowspan="2">Style No</th>
					<th width="100" rowspan="2">Booking No</th>
					<th width="130" rowspan="2">FSO</th>

					<th colspan="14">Fabric Details</th>
					<th colspan="5">Receive Details</th>
					<th colspan="4">Issue Details</th>
					<th colspan="1">Stock Details</th>

				</tr>
				<tr>
					<th width="100">Construction</th>
					<th width="200">Composition</th>
					<th width="100">GSM</th>
					<th width="100">Program No.</th>
					<th width="150">Color</th>
					<th width="100">Color Range</th>
					<th width="100">Stitch Length</th>
					<th width="100">M/Dia</th>
					<th width="100">F/Dia</th>
					<th width="100">Program Qty</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Booking Qty</th>

					<th width="100">Recv. Qty.</th>
					<th width="100">Booking receive <br> Balance Qty</th>
					<th width="100">Issue Return Qty.</th>
					<th width="100">Transf. In Qty.</th>
					<th width="100">Total Recv.</th>

					<th width="100">Issue Qty.</th>
					<th width="100">Receive Return Qty.</th>
					<th width="100">Transf. Out Qty.</th>
					<th width="100">Total Issue</th>

					<th width="">Stock Qty.</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search2" align="left">

				<?
				$job_count = array();
				$deter_count = array();
				$gsm_count = array();
				//$program_count = array();
				foreach($mainArr as $k_job=>$v_job)
				{
					foreach($v_job as $k_deter=>$v_deter)
					{
						foreach($v_deter as $k_gsm=>$v_gsm)
						{
							foreach($v_gsm as $k_prog_no=>$row)
							{
								$job_count[$k_job]++;
								$deter_count[$k_job][$k_deter]++;
								$gsm_count[$k_job][$k_deter][$k_gsm]++;
							}
						}
					}
				}

				$i=1;
				$g_total_booking_qty = 0;
				$g_total_receive_qty = 0;
				$g_total_Booking_rcv_b_qty = 0;
				$g_tot_issue_rtn_qty = 0;
				$g_total_Recv = 0;
				$g_tot_issue_qnty = 0;
				$g_tot_trans_in_qty = 0;
				$g_tot_trans_out_qty = 0;
				$g_tot_issue_qty = 0;
				$g_tot_stock_qnty = 0;
				foreach($mainArr as $k_job=>$v_job)
				{
					foreach($v_job as $k_deter=>$v_deter)
					{
						foreach($v_deter as $k_gsm=>$v_gsm)
						{
							foreach($v_gsm as $k_prog_no=>$row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								// echo "<pre>";
								// print_r($k_prog_no);

								$prodIdsArrr = $requArr[$k_prog_no]['prod_id'];
								$prodIdsArrData = array_unique(explode(", ",chop($prodIdsArrr ,",")));
								//var_dump($prodIdsArrData);

								$yarn_count = '';
								$yarn_lot = '';
								$yarn_brand = '';
								$booking_qty = 0;
								$receive_qty = 0;
								$issue_rtn_qty = 0;
								$tot_rcv_qnty =0;
								$issue_qnty =0;
								$trans_in_qty =0;
								$trans_out_qty =0;
								foreach ($prodIdsArrData as $prod_id)
								{
									//var_dump($prod_id);
									if($yarn_count=='')
									{
										$yarn_count = $product_details_array[$prod_id]['count'];
									}
									else
									{
										$yarn_count .= ", ".$product_details_array[$prod_id]['count'];
									}

									if($yarn_lot=='')
									{
										$yarn_lot = $product_details_array[$prod_id]['lot'];
									}
									else
									{
										$yarn_lot .= ', '.$product_details_array[$prod_id]['lot'];
									}

									if($yarn_brand=='')
									{
										$yarn_brand = $product_details_array[$prod_id]['brand'];
									}
									else
									{
										$yarn_brand .= ', '.$product_details_array[$prod_id]['brand'];
									}
								}


								$booking_qty += $bookingQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']][$row['FABRIC_COLOR_ID']]['GREY_QNTY_BY_UOM'];
								$tot_rcv_qnty += $rcvQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['receive_qty'];
								$receive_qty += $knitProQtyArr[$k_prog_no];
								$issue_qnty += $issueQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['issue_qnty'];
								$issue_rtn_qty += $issueRtnQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['issue_rtn_qty'];
								$trans_in_qty += $trnsInQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['transfer_in_qnty'];
								$trans_out_qty += $trnsOutQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['TRANSFER_OUT_QNTY'];

								$job_span = $job_count[$k_job]++;
								$deter_span = $deter_count[$k_job][$k_deter]++;
								$gsm_span = $gsm_count[$k_job][$k_deter][$k_gsm]++;

								$fabric_desc = $row['FABRIC_DESC'];
								$fabric_desc_data = explode(',',$fabric_desc);
								$construction  = $fabric_desc_data[0];
								$composition   = $fabric_desc_data[1];



								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

								<?
								if(!in_array($k_job,$job_chk))
								{
									$job_chk[]=$k_job;
									?>

									<td width="40" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $i; ?>&nbsp;</td>
									<td width="130" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $company_arr[$row['COMPANY_ID']]; ?></td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?></td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['STYLE_REF_NO']; ?></td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['BOOKING_NO']; ?></td>
									<td width="130" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['JOB_NO']; ?></td>
								<?
								}
								if(!in_array($k_job."**".$k_deter,$deter_chk))
								{
									$deter_chk[]=$k_job."**".$k_deter;
								?>
									<td width="100" class="word_wrap_break" title="<? echo $k_deter;?>" rowspan="<? echo $deter_span ;?>" valign="middle"><? echo $construction; ?></td>
									<td width="200" class="word_wrap_break" rowspan="<? echo $deter_span ;?>" valign="middle"><? echo $composition; ?></td>
								<? }
								if(!in_array($k_job."**".$k_deter."**".$k_gsm,$gsm_chk))
								{
									$gsm_chk[]=$k_job."**".$k_deter."**".$k_gsm;
								?>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle"><? echo $row['GSM_WEIGHT']; ?></td>
								<?
								}
								?>
								<td width="100" class="word_wrap_break"><? echo $k_prog_no; ?></td>
								<td width="150" class="word_wrap_break"><? echo $row['FABRIC_COLOR']; ?></td>
								<td width="100" class="word_wrap_break"><? echo $row['COLOR_RANGE']; ?></td>
								<td width="100" class="word_wrap_break"><? echo $row['STITCH_LENGTH']; ?></td>
								<td width="100" class="word_wrap_break"><? echo $row['MACHINE_DIA']; ?></td>
								<td width="100" class="word_wrap_break"><? echo $row['FABRIC_DIA']; ?></td>
								<td width="100" class="word_wrap_break"><? echo decimal_format($row['PROGRAM_QNTY'], '1', ','); ?></td>
								<td width="100" class="word_wrap_break"><? echo rtrim($yarn_lot,', '); ?></td>
								<td width="100" class="word_wrap_break"><? echo rtrim($yarn_count,', '); ?></td>
								<td width="100" class="word_wrap_break"><? echo rtrim($yarn_brand,', '); ?></td>
								<td width="100" align="right" class="word_wrap_break"><? echo number_format($booking_qty,2);  ?></th>
								<td width="100" align="right" class="word_wrap_break"><? echo number_format($receive_qty,2); $tot_receive_qty += $receive_qty;?></th>
								<td width="100" align="right" class="word_wrap_break"><? echo number_format($booking_qty-$receive_qty,2); ?></th>
								<?
								if(!in_array($k_job."**".$k_deter."**".$k_gsm,$gsm1_chk))
								{
									$gsm1_chk[]=$k_job."**".$k_deter."**".$k_gsm;
								?>
									<td title="<?=$row['JOB_NO'].'='.$k_deter.'='.$row['GSM_WEIGHT'];?>" width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($issue_rtn_qty,2); $g_tot_issue_rtn_qty += $issue_rtn_qty; ?>&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($trans_in_qty,2); $g_tot_trans_in_qty += $trans_in_qty; ?>&nbsp;</th>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right" title="Recv. Qty+Issue Return Qty+Transf. In Qty">
									<?
									$total_Recv = ($tot_rcv_qnty+$issue_rtn_qty+$trans_in_qty);
									echo number_format($total_Recv,2); $g_total_Recv += $total_Recv; ?>&nbsp;
									</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($issue_qnty,2); 	$g_tot_issue_qnty += $issue_qnty; ?>&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right">&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($trans_out_qty,2); $g_tot_trans_out_qty += $trans_out_qty; ?>&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right" title="Issue Qty+Receive Return Qty+Transf. Out Qty"><? echo number_format($issue_qnty+$trans_out_qty,2); $g_tot_issue_qty += $issue_qnty+$trans_out_qty; ?>&nbsp;</td>
									<td width="" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right" title="(Total Recv-Total Issue)"><? echo number_format($total_Recv-($issue_qnty+$trans_out_qty),2); 	$g_tot_stock_qnty += $total_Recv-($issue_qnty+$trans_out_qty); ?>&nbsp;</td>
								<? } ?>
								</tr>
								<?
								$g_total_booking_qty += $booking_qty;
								$g_total_receive_qty += $receive_qty;
								$g_total_Booking_rcv_b_qty +=$booking_qty-$receive_qty;

							}
						}
					}

					$i++;
				}
				?>
			</table>
		</div>
		<table width="<? echo $table_width; ?>"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="130"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="130"></th>
					<th width="100"></th>
					<th width="200"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100">Grand Total : </th>
					<th width="100"><? echo number_format($g_total_booking_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_receive_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_Booking_rcv_b_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_rtn_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_trans_in_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_Recv,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_qnty,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($g_tot_trans_out_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_qty,2);?></th>
					<th width=""><? echo number_format($g_tot_stock_qnty,2);?></th>
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

if($action=="report_generate3") // Show 3 By Tipu
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
	$order_no 		= trim(str_replace("'","",$txt_order_no));
	$order_id 		= str_replace("'","",$hide_order_id);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);
	$int_ref 		= trim(str_replace("'","",$txt_int_ref));

	$store_arr 	 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$company_arr 	= return_library_array( "select id,company_name from lib_company",'id','company_name');
	$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	$season_arr  	= return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
	$yarn_brand_arr = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
	$color_library 	= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	// =============================== All Search Criteria Condition Start =========================
	if($pocompany_id!=0 || $pocompany_id!="")
	{
		if($within_group==1)
		{
			$pocompany_cond="and d.po_company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
		}else {
			$pocompany_cond="and d.company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
		}
	}
	else
	{
		$pocompany_cond="";
	}

	if($cbo_store_wise==1)
	{
		$store_cond = " and e.store_id=$cbo_store_name";
		$store_cond2 = " and a.store_id=$cbo_store_name";
	}

	if($po_buyer_id!=0 || $po_buyer_id!="")
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and d.po_buyer in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond2=" and f.po_buyer in (".str_replace("'","",$po_buyer_id).")";
		}else {

			$buyer_id_cond=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond2=" and f.buyer_id in (".str_replace("'","",$po_buyer_id).")";
		}
	}

	$date_cond="";

	if( $date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}

		$date_cond = " and e.transaction_date between '$date_from' and '$date_to' ";
	}

	if ($order_id=='') $order_no_cond=""; else $order_no_cond="and d.id='$order_id'";
	if ($order_id=='') $toOrderIdCond=""; else $toOrderIdCond="and a.to_order_id='$order_id'";

	if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and d.job_no like '%$order_no%'";
	if ($order_no=='') $sales_to_order_no_cond=""; else $sales_to_order_no_cond=" and f.job_no like '%$order_no%'";

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
	}
	else
	{
		$sales_order_year_condition="";
	}

	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and b.booking_no='$program_no'";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no'";
	}
	else
	{
		$booking_no_cond="";
	}

	if ($int_ref!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b, FABRIC_SALES_ORDER_MST c
		where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID and c.within_group=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond=""; $refBooking_cond2="";
		foreach ($po_sql_result as $key => $row)
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
		$refBooking_cond2=" and f.booking_id in('".implode("','",$bookingNo_arr)."') ";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and d.within_group='$within_group' ";
	}
	else
	{
		$within_group_cond = "";
	}
	// =============================== All Search Criteria Condition End =========================

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
    oci_commit($con);

	// =============================== Search Criteria Wise Sql From Receive Start ===============
	$sql = "SELECT b.qnty as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no, d.id as sales_id
	from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
	where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $order_no_cond $booking_no_cond $refBooking_cond $program_no_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $sales_order_no_cond";
	// echo $sql; die;

	$sql_result=sql_select($sql);

	foreach( $sql_result as $row )
	{
		if ($po_id_check[$row[csf('sales_id')]] == "")
        {
            $po_id_check[$row[csf('sales_id')]]=$row[csf('sales_id')];
            $po_id = $row[csf('sales_id')];
            // echo "insert into tmp_po_id (userid, po_id) values ($user_id,$po_id)";
            $r_id1=execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)");
        }
	}
	oci_commit($con);
	// =============================== Search Criteria Wise Sql From Receive End =================

	// =============================== Booking Qty Sql From FSO Start ============================
	$booking_sql = "SELECT A.JOB_NO, B.DETERMINATION_ID, B.GSM_WEIGHT,B.COLOR_ID, B.GREY_QNTY_BY_UOM, B.COLOR_TYPE_ID, B.GREY_QTY
	FROM TMP_PO_ID T, FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B
	WHERE T.PO_ID=A.ID AND A.ID = B.MST_ID  AND A.COMPANY_ID = ".$company_name."  AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND T.USER_ID=$user_id";
	// echo $booking_sql;

	$booking_sql_rslt=sql_select($booking_sql);
	$bookingQntyArr = array();
	foreach ($booking_sql_rslt as $row)
	{
		$bookingQntyArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']]['GREY_QTY'] += $row['GREY_QTY'];

		//echo $row['JOB_NO'].']['.$row['DETERMINATION_ID'].']['.$row['GSM_WEIGHT'].']['.$row['COLOR_TYPE_ID'].']['.$row['COLOR_ID'].'qty='.$row['GREY_QTY'].'<br>';
	}
	// echo "<pre>"; print_r($bookingQntyArr);
	// =============================== Booking Qty Sql From FSO End =============================

	// =============================== Main Sql From Program Start ==============================
	$main_sql = "SELECT A.BOOKING_NO, B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY, C.COLOR_TYPE_ID, E.ID as FSO_ID, E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID, F.ID AS SALES_ID, F.GREY_QTY, B.MACHINE_GG, E.PO_JOB_NO, E.COMPANY_ID, E.CUSTOMER_BUYER, B.COLOR_RANGE, B.STITCH_LENGTH
	FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F, TMP_PO_ID T
	WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND T.PO_ID=F.MST_ID AND T.PO_ID=E.ID AND T.PO_ID=C.PO_ID AND T.USER_ID=$user_id AND C.IS_SALES = 1 AND A.COMPANY_ID = ".$company_name."  AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ORDER BY B.ID, A.BOOKING_NO";
	// echo $main_sql;
	$main_sql_rslt=sql_select($main_sql);
	$mainDataArr = array();
	foreach ($main_sql_rslt as $row)
	{
		if($duplicate_check[$row['DTLS_ID']] != $row['DTLS_ID'])
		{
			$duplicate_check[$row['DTLS_ID']] = $row['DTLS_ID'];

			//for color_range
			$color_range_arr = array();
			$exp_color_range = array();
			$exp_color_range = explode(",", $row['COLOR_RANGE']);
			foreach ($exp_color_range as $key=>$val)
			{
				$color_range_arr[$val] = $color_range[$val];
			}
			//end for color_range

			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['JOB_NO'] = $row['JOB_NO'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['DETERMINATION_ID'] = $row['DETERMINATION_ID'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['CUSTOMER_BUYER'] = $row['CUSTOMER_BUYER'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['FABRIC_DIA'] = $row['FABRIC_DIA'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['FABRIC_COLOR_ID'] = $row['COLOR_ID'];
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['COLOR_RANGE'] = implode(', ', $color_range_arr);
			$mainDataArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_TYPE_ID']][$row['COLOR_ID']][$row['ID']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];

			if( $prog_no_check[$row['ID']] == "" )
            {
                $prog_no_check[$row['ID']]=$row['ID'];
                $prog_no = $row['ID'];
                // echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,$prog_no)";
                $r_id2=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_id,$prog_no)");
            }
		}
	}
	oci_commit($con);
	unset($main_sql_rslt);
	// echo "<pre>"; print_r($mainDataArr);die;
	// =============================== Main Sql From Program End ==============================

	// =============================== Roll Receive Sql Start =================================
	$rcv_sql =  "SELECT A.BOOKING_NO,B.BOOKING_ID, A.QNTY FROM TMP_PROG_NO T, PRO_ROLL_DETAILS A, INV_RECEIVE_MASTER B, ORDER_WISE_PRO_DETAILS C
	WHERE T.PROG_NO=A.BOOKING_NO AND T.USERID=$user_id AND A.MST_ID = B.ID and a.dtls_id=c.dtls_id and a.entry_form in(58,2) AND A.ENTRY_FORM in(58,2) and c.entry_form in(58,2) and b.receive_basis in(2,10) and c.trans_type=1 and c.trans_id>0 and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and A.IS_SALES=1";
	// echo $rcv_sql;//die;
	$rcv_sql_rslt=sql_select($rcv_sql);
	$duplicate_check = array();
	foreach($rcv_sql_rslt as $row)
	{
		$knitProQtyArr[$row['BOOKING_NO']] += $row['QNTY'];
	}
	// =============================== Roll Receive Sql End ===================================

	// =============================== Production Days Sql Start ==============================
	$max_production_date_sql="SELECT  B.BOOKING_ID, max(B.RECEIVE_DATE) as MAX_DATE
	FROM TMP_PROG_NO A, INV_RECEIVE_MASTER B
	WHERE A.PROG_NO=B.BOOKING_ID AND A.USERID=$user_id AND b.entry_form in(2) and b.receive_basis in(2) and b.status_active = 1 and b.is_deleted = 0 group by B.BOOKING_ID";
	// echo $max_production_date_sql;
	$max_production_date_sql_rslt=sql_select($max_production_date_sql);
	$max_production_date_arr = array();
	foreach($max_production_date_sql_rslt as $row)
	{
		$max_production_date_arr[$row['BOOKING_ID']]['max_date'] = $row['MAX_DATE'];
	}
	// =============================== Production Days Sql End =================================

	// =============================== Yarn Requisition Sql Start ==============================
	$sql_requ = "SELECT B.KNIT_ID, B.REQUISITION_NO, B.PROD_ID, B.YARN_QNTY
	FROM TMP_PROG_NO A, PPL_YARN_REQUISITION_ENTRY B WHERE A.PROG_NO=B.KNIT_ID AND A.USERID=$user_id AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
	// echo $sql_requ;
	$sql_requ_result = sql_select($sql_requ);
	$requArr = array();
	foreach ($sql_requ_result as $row)
	{
		$requArr[$row['KNIT_ID']]['prod_id']  .= $row['PROD_ID'].', ';
	}
	//var_dump($requArr);
	// =============================== Yarn Requisition Sql End ================================

	// =============================== Yarn Information Sql Start ==============================
	$product_details_array = array();
	$yarn_info_sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND COMPANY_ID=".$company_name." AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT B.PROD_ID FROM TMP_PROG_NO A, PPL_YARN_REQUISITION_ENTRY B WHERE A.PROG_NO=B.KNIT_ID AND A.USERID=$user_id AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0)";
	// echo $yarn_info_sql;
	$yarn_info_result = sql_select($yarn_info_sql);
	foreach ($yarn_info_result as $row)
	{
		$product_details_array[$row['ID']]['count'] = $yarn_count_arr[$row['YARN_COUNT_ID']];
		$product_details_array[$row['ID']]['lot'] = $row['LOT'];
		$product_details_array[$row['ID']]['brand'] = $yarn_brand_arr[$row['BRAND']];
	}
	unset($yarn_info_result);
	// echo "<pre>"; print_r($product_details_array);
	// =============================== Yarn Information Sql End ================================

	// =============================== Roll Issue Return Sql Start =============================
	$issue_rtn_sql = "SELECT B.QNTY AS ISSUE_RTN_QTY,B.PO_BREAKDOWN_ID AS PO_ID, B.BOOKING_NO AS PROGRAM_NO
	FROM ORDER_WISE_PRO_DETAILS A, PRO_ROLL_DETAILS B, FABRIC_SALES_ORDER_MST D, TMP_PO_ID T
	WHERE A.DTLS_ID=B.DTLS_ID AND B.PO_BREAKDOWN_ID=D.ID AND T.PO_ID=D.ID AND T.PO_ID=B.PO_BREAKDOWN_ID AND T.USER_ID=$user_id AND B.ENTRY_FORM IN(84) AND A.ENTRY_FORM IN(84) AND A.TRANS_ID>0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.TRANS_TYPE=4 AND B.IS_SALES=1";

	// echo $issue_rtn_sql;

	$issue_rtn_sql_result = sql_select($issue_rtn_sql);
	$issueRtnQtyArr = array();
	foreach ($issue_rtn_sql_result as $row)
	{
		$issueRtnQtyArr[$row['PROGRAM_NO']]['issue_rtn_qty'] += $row['ISSUE_RTN_QTY'];
	}
	unset($issue_rtn_sql_result);
	// echo "<pre>"; print_r($issueRtnQtyArr);
	// =============================== Roll Issue Return Sql End ============================

	// =============================== Roll Issue Sql Start ===================================
	$issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty,f.job_no, d.barcode_no
	from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b, pro_roll_details d, fabric_sales_order_mst f, TMP_PO_ID T
	where a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and d.po_breakdown_id=f.id AND T.PO_ID=f.id AND T.USER_ID=$user_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and d.is_sales=1 and e.transaction_type=2	and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	//and d.is_returned<>1
	// echo $issue_sql;
	$issue_sql_result = sql_select($issue_sql);
	$prodIdsArr = array();$issueQtyArr = array();$barcode_wise_issue=array();
	foreach($issue_sql_result as $row )
	{
		if($prodIdsChk[$row[csf('prod_id')]]=='')
		{
			$prodIdsChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
			array_push($prodIdsArr, $row[csf('prod_id')]);
		}

		$issueQtyArr[$row[csf('prog_no')]] += $row[csf('issue_qty')];
		$barcode_wise_issue[$row[csf('barcode_no')]] += $row[csf('issue_qty')];
	}
	// echo "<pre>"; print_r($issueQtyArr);
	// =============================== Roll Issue Sql End =======================================

	$prod_info_sql = "SELECT ID, DETARMINATION_ID, GSM FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=13 AND COMPANY_ID=".$company_name." AND STATUS_ACTIVE=1 AND IS_DELETED=0   ";
	//echo $prod_info_sql; //".where_con_using_array($prodIdsArr,0,'ID')."

	$prod_info_result = sql_select($prod_info_sql);
	$prodInfoArr = array();
	foreach($prod_info_result as $row )
	{
		$prodInfoArr[$row[csf('ID')]]['detarmination_id'] = $row[csf('DETARMINATION_ID')];
		$prodInfoArr[$row[csf('ID')]]['gsm'] = $row[csf('GSM')];
	}
	//var_dump($prodInfoArr);

	// =============================== Roll Transfer In Sql Start ================================
	$trans_in_sql = "SELECT A.TO_ORDER_ID,B.TO_PROD_ID, D.QNTY AS TRANSFER_IN_QNTY,F.JOB_NO, D.BARCODE_NO
	FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F, TMP_PO_ID T
	WHERE  A.ID=E.MST_ID AND A.ID=D.MST_ID AND E.ID=B.TO_TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.TO_ORDER_ID=F.ID AND T.PO_ID=F.ID AND A.TO_ORDER_ID=T.PO_ID AND T.USER_ID=$user_id AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=5  AND D.ENTRY_FORM=133";
	// echo $trans_in_sql;
	$trans_in_sql_result = sql_select($trans_in_sql);
	$trnsInQtyArr = array();$transfer_in_barcode_qty_arr=array();
	foreach($trans_in_sql_result as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('TO_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('TO_PROD_ID')]]['gsm'];

		$trnsInQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['transfer_in_qnty'] += $row['TRANSFER_IN_QNTY'];

		$barcode_wise_trans_in[$row['JOB_NO']][$detarmination_id][$gsm]['transfer_in_qnty'] += $barcode_wise_issue[$row['BARCODE_NO']];
		$transfer_in_barcode_qty_arr[$row['BARCODE_NO']] += $row['TRANSFER_IN_QNTY'];
	}
	unset($trans_in_sql_result);
	// echo "<pre>"; print_r($transfer_in_barcode_qty_arr);die;
	// =============================== Roll Transfer In Sql End ================================

	// =============================== Roll Transfer Out Sql Start ================================
	$trans_out_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID, B.FROM_PROGRAM, D.QNTY AS TRANSFER_OUT_QNTY,F.JOB_NO, D.BARCODE_NO
	FROM INV_ITEM_TRANSFER_MST A, INV_TRANSACTION E, INV_ITEM_TRANSFER_DTLS B, PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D, FABRIC_SALES_ORDER_MST F, TMP_PO_ID T
	WHERE A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.FROM_ORDER_ID=F.ID  AND A.ID=D.MST_ID AND B.ID=D.DTLS_ID AND T.PO_ID=F.ID AND A.FROM_ORDER_ID=T.PO_ID AND T.USER_ID=$user_id AND A.STATUS_ACTIVE=1 AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND E.TRANSACTION_TYPE=6 AND B.STATUS_ACTIVE=1 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND D.ENTRY_FORM=133 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0";
	// echo $trans_out_sql;
	$trans_out_rslt = sql_select($trans_out_sql);

	$trnsOutQtyArr = array();
	foreach($trans_out_rslt as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['gsm'];

		$trnsOutQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['TRANSFER_OUT_QNTY'] += $row['TRANSFER_OUT_QNTY'];
		$transfer_out_from_transfer_in_arr[$row['JOB_NO']][$detarmination_id][$gsm]['transfer_in_from_out_qty'] += $transfer_in_barcode_qty_arr[$row['BARCODE_NO']];

		$program_wise_out_qtyArr[$row['FROM_PROGRAM']]['program_wise_out_qty'] += $row['TRANSFER_OUT_QNTY'];
	}
	unset($trans_out_rslt);
	// echo "<pre>"; print_r($transfer_out_from_transfer_in_arr);
	// =============================== Roll Transfer Out Sql End ================================



	$r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
	$r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
	oci_commit($con);

	ob_start();
	$table_width = "3350";

	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<fieldset style="width:3300">
		<table cellpadding="0" cellspacing="0" width="3240">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:16px">
					<strong>
						<?
						echo $company_arr[str_replace("'","",$cbo_company_id)];
						?>
					</strong>
				</td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td colspan="24" width="100%" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? if($date_from!="" || $date_to!="") echo "From : ".change_date_format($date_from)." To : ".change_date_format($date_to)."" ;?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
			<thead>
				<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="130" rowspan="2">Company </th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="100" rowspan="2">Style No</th>
					<th width="100" rowspan="2">Booking No</th>
					<th width="130" rowspan="2">FSO</th>

					<th colspan="15">Fabric Details</th>
					<th colspan="4">Receive Details</th>
					<th colspan="1">Issue Details</th>
					<th colspan="1">Stock Details</th>

					<th width="100" rowspan="2">Trans IN</th>
					<th width="100" rowspan="2">Issue (Transfer In)</th>
					<th width="100" rowspan="2">Trans Out</th>
					<th width="100" rowspan="2">Trans Stock</th>
					<th width="" rowspan="2">Production Days</th>

				</tr>
				<tr>
					<th width="100">Construction</th>
					<th width="200">Composition</th>
					<th width="100">GSM</th>
					<th width="100">Color Type</th>
					<th width="150">Color</th>
					<th width="100">Booking Qty</th>
					<th width="100">Program No.</th>
					<th width="100">Color Range</th>
					<th width="100">Stitch Length</th>
					<th width="100">M/Dia</th>
					<th width="100">F/Dia</th>
					<th width="100">Program Qty</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Brand</th>

					<th width="100">Recv. Qty.</th>
					<th width="100">Color<br>Rcvd Qty</th>
					<th width="100">Receive<br>Balance Qty</th>
					<th width="100">Issue<br>Return Qty.</th>

					<th width="100">Issue Qty.</th>
					<th width="100">Program Stock Qty</th>

				</tr>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search2" align="left">
				<?
				$job_count = array();
				$deter_count = array();
				$gsm_count = array();
				$type_color_count = array();
				$colorWiseQntyArr = array();
				foreach($mainDataArr as $k_job=>$v_job)
				{
					foreach($v_job as $k_deter=>$v_deter)
					{
						foreach($v_deter as $k_gsm=>$v_gsm)
						{
							foreach($v_gsm as $k_color_type=>$v_color_type)
							{
								foreach($v_color_type as $k_fabric_color=>$v_fabric_color)
								{
									foreach($v_fabric_color as $k_prog_no=>$row)
									{
										$job_count[$k_job]++;
										$deter_count[$k_job][$k_deter]++;
										$gsm_count[$k_job][$k_deter][$k_gsm]++;

										$type_color_count[$k_job][$k_deter][$k_gsm][$k_color_type][$k_fabric_color]++;

										$receive_qty = $knitProQtyArr[$k_prog_no];
										$colorWiseQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']][$k_color_type][$k_fabric_color] += $receive_qty;
									}
								}
							}
						}
					}
				}
				$i=1;
				$g_total_booking_qty = 0;
				$g_total_receive_qty = 0;
				$g_total_program_qnty = 0;
				$g_total_color_qty = 0;
				$g_total_balance_qty = 0;
				$g_tot_issue_rtn_qty = 0;
				$g_total_Recv = 0;
				$g_tot_issue_qnty = 0;
				$g_tot_trans_in_qty = 0;
				$g_tot_trans_in_issue_qty = 0;
				$g_tot_trans_out_qty = 0;
				$g_tot_issue_qty = 0;
				$g_tot_stock_qnty = 0;


				foreach($mainDataArr as $k_job=>$v_job)
				{
					foreach($v_job as $k_deter=>$v_deter)
					{
						foreach($v_deter as $k_gsm=>$v_gsm)
						{
							foreach($v_gsm as $k_color_type=>$v_color_type)
							{
								foreach($v_color_type as $k_fabric_color=>$v_fabric_color)
								{
									foreach($v_fabric_color as $k_prog_no=>$row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										// echo "<pre>";
										// print_r($k_prog_no);

										$prodIdsArrr = $requArr[$k_prog_no]['prod_id'];
										$prodIdsArrData = array_unique(explode(", ",chop($prodIdsArrr ,",")));
										//var_dump($prodIdsArrData);

										$yarn_count = '';
										$yarn_lot = '';
										$yarn_brand = '';
										$booking_qty = 0;
										$receive_qty = 0;
										$issue_rtn_qty = 0;
										$program_wise_out_qty = 0;
										$issue_qnty =0;
										$trans_in_qty =0;
										$trans_in_issue_qty =0;
										$trans_out_qty =0;
										$trans_out_from_trans_in_qty =0;
										foreach ($prodIdsArrData as $prod_id)
										{
											//var_dump($prod_id);
											if($yarn_count=='')
											{
												$yarn_count = $product_details_array[$prod_id]['count'];
											}
											else
											{
												$yarn_count .= ", ".$product_details_array[$prod_id]['count'];
											}

											if($yarn_lot=='')
											{
												$yarn_lot = $product_details_array[$prod_id]['lot'];
											}
											else
											{
												$yarn_lot .= ', '.$product_details_array[$prod_id]['lot'];
											}

											if($yarn_brand=='')
											{
												$yarn_brand = $product_details_array[$prod_id]['brand'];
											}
											else
											{
												$yarn_brand .= ', '.$product_details_array[$prod_id]['brand'];
											}
										}
										// echo $row['JOB_NO'].']['.$k_deter.']['.$row['GSM_WEIGHT'].']['.$k_color_type.']['.$k_fabric_color.'<br>';

										// $booking_qty = $bookingQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']][$k_color_type][$k_fabric_color]['GREY_QNTY_BY_UOM'];

										$color_id_arr = explode(",", $k_fabric_color);
                                        $fab_colors="";//$booking_qty=0;
                                        foreach ($color_id_arr as $color)
                                        {
                                            $fab_colors .= $color_library[$color] . ",";

                                            //$booking_qty += $bookingQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']][$k_color_type][$color]['GREY_QNTY_BY_UOM'];

                                            //echo $row['JOB_NO'].']['.$k_deter.']['.$row['GSM_WEIGHT'].']['.$k_color_type.']['.$k_fabric_color.'=='.$booking_qty.'<br>';

                                        }
                                        // echo  $booking_qty.'<br>';
                                        $fab_colors = rtrim($fab_colors, ", ");

										$receive_qty += $knitProQtyArr[$k_prog_no];

										$issue_qnty += $issueQtyArr[$k_prog_no];

										$issue_rtn_qty += $issueRtnQtyArr[$k_prog_no]['issue_rtn_qty'];
										$program_wise_out_qty += $program_wise_out_qtyArr[$k_prog_no]['program_wise_out_qty'];

										$trans_in_qty += $trnsInQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['transfer_in_qnty'];
										$trans_in_issue_qty += $barcode_wise_trans_in[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['transfer_in_qnty'];
										$trans_out_qty += $trnsOutQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['TRANSFER_OUT_QNTY'];
										$trans_out_from_trans_in_qty += $transfer_out_from_transfer_in_arr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['transfer_in_from_out_qty'];

										$job_span = $job_count[$k_job]++;
										$deter_span = $deter_count[$k_job][$k_deter]++;
										$gsm_span = $gsm_count[$k_job][$k_deter][$k_gsm]++;
										$color_span = $type_color_count[$k_job][$k_deter][$k_gsm][$k_color_type][$k_fabric_color]++;

										$fabric_desc = $row['FABRIC_DESC'];
										$fabric_desc_data = explode(',',$fabric_desc);
										$construction  = $fabric_desc_data[0];
										$composition   = $fabric_desc_data[1];

										$daysOnHand = datediff("d",$max_production_date_arr[$k_prog_no]['max_date'],date("Y-m-d"));
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

										<?
										if(!in_array($k_job,$job_chk))
										{
											$job_chk[]=$k_job;
											?>
											<td width="40" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $i; ?>&nbsp;</td>
											<td width="130" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $company_arr[$row['COMPANY_ID']]; ?></td>
											<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?></td>
											<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['STYLE_REF_NO']; ?></td>
											<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['BOOKING_NO']; ?></td>
											<td width="130" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['JOB_NO']; ?></td>
											<?
										}
										if(!in_array($k_job."**".$k_deter,$deter_chk))
										{
											$deter_chk[]=$k_job."**".$k_deter;
											?>
											<td width="100" title="<? echo $k_deter; ?>" class="word_wrap_break" rowspan="<? echo $deter_span ;?>" valign="middle"><? echo $construction; ?></td>
											<td width="200" class="word_wrap_break" rowspan="<? echo $deter_span ;?>" valign="middle"><? echo $composition; ?></td>
											<?
										}
										if(!in_array($k_job."**".$k_deter."**".$k_gsm,$gsm_chk))
										{
											$gsm_chk[]=$k_job."**".$k_deter."**".$k_gsm;
											?>
											<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle"><? echo $row['GSM_WEIGHT']; ?></td>
											<?
										}
										?>

										<?
										if(!in_array($k_job."**".$k_deter."**".$k_gsm."**".$k_color_type."**".$k_fabric_color,$color_chk))
										{
											$color_chk[]=$k_job."**".$k_deter."**".$k_gsm."**".$k_color_type."**".$k_fabric_color;
											?>
											<td width="100" class="word_wrap_break" rowspan="<? echo $color_span ;?>" valign="middle" title="<? echo $k_color_type; ?>"><? echo $color_type[$k_color_type]; ?></td>
											<td width="150" class="word_wrap_break" rowspan="<? echo $color_span ;?>" valign="middle" title="<? echo $k_fabric_color; ?>"><? echo $fab_colors; ?></td>
											<?
											$color_id_arr = explode(",", $k_fabric_color);
	                                        $booking_qty=0;
	                                        foreach ($color_id_arr as $color)
	                                        {
	                                            $booking_qty += $bookingQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']][$k_color_type][$color]['GREY_QTY'];

	                                            //echo $row['JOB_NO'].']['.$k_deter.']['.$row['GSM_WEIGHT'].']['.$k_color_type.']['.$k_fabric_color.'=='.$booking_qty.'<br>';

	                                        }
	                                        // echo  $booking_qty.'<br>';
											?>
											<td width="100" align="right" class="word_wrap_break" rowspan="<? echo $color_span ;?>" valign="middle"><? echo number_format($booking_qty,2);  $tot_booking_qty+=$booking_qty; ?></td>
											<?
										}
										?>
										<td width="100" class="word_wrap_break"><? echo $k_prog_no; ?></td>
										<td width="100" class="word_wrap_break"><? echo $row['COLOR_RANGE']; ?></td>
										<td width="100" class="word_wrap_break"><? echo $row['STITCH_LENGTH']; ?></td>
										<td width="100" class="word_wrap_break"><? echo $row['MACHINE_DIA']; ?></td>
										<td width="100" class="word_wrap_break"><? echo $row['FABRIC_DIA']; ?></td>
										<td width="100" class="word_wrap_break" align="right"><? echo decimal_format($row['PROGRAM_QNTY'], '1', ','); ?></td>
										<td width="100" class="word_wrap_break"><? echo rtrim($yarn_lot,', '); ?></td>
										<td width="100" class="word_wrap_break"><? echo rtrim($yarn_count,', '); ?></td>
										<td width="100" class="word_wrap_break"><? echo rtrim($yarn_brand,', '); ?></td>
										<td width="100" align="right" class="word_wrap_break"><? echo number_format($receive_qty,2); $tot_color_qty += $receive_qty;?></td>

										<?
										$color_qty=0;
										if(!in_array($k_job."**".$k_deter."**".$k_gsm."**".$k_color_type."**".$k_fabric_color,$color_chk2))
										{
											$color_chk2[]=$k_job."**".$k_deter."**".$k_gsm."**".$k_color_type."**".$k_fabric_color;
											$color_qty=$colorWiseQntyArr[$k_job][$k_deter][$k_gsm][$k_color_type][$k_fabric_color];
											?>
											<td width="100" align="right" class="word_wrap_break" rowspan="<? echo $color_span ;?>" valign="middle"><? echo number_format($color_qty,2); $g_total_color_qty+=$color_qty; ?></td>
											<td width="100" align="right" class="word_wrap_break" rowspan="<? echo $color_span ;?>" valign="middle"><? echo number_format($booking_qty-$color_qty,2); $g_total_balance_qty += $booking_qty-$color_qty;?></td>
											<?
										}
										?>
										<td width="100" class="word_wrap_break" align="right"><? echo number_format($issue_rtn_qty,2); $g_tot_issue_rtn_qty += $issue_rtn_qty; ?>&nbsp;</td>

										<td width="100" class="word_wrap_break" align="right"><? echo number_format($issue_qnty,2); $g_tot_issue_qnty += $issue_qnty; ?>&nbsp;</td>
										<td width="100" class="word_wrap_break" align="right" title="Color RcvdQty -Issue Qty +Issue Return Qty-Transfer Out of this Program"><? echo number_format(($color_qty-$issue_qnty)+($issue_rtn_qty-$program_wise_out_qty),2);$g_tot_stock_qnty += ($color_qty-$issue_qnty)+($issue_rtn_qty-$program_wise_out_qty); ?>&nbsp;</td>

										<?
										if(!in_array($k_job."**".$k_deter."**".$k_gsm,$gsm1_chk))
										{
											$gsm1_chk[]=$k_job."**".$k_deter."**".$k_gsm;
											?>
											<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><a href='#report_details' onClick="openmypage_transfer_in('<? echo $k_job; ?>','<? echo $k_deter; ?>','<? echo $k_gsm; ?>','650px','transfer_in_popup',5);"><? echo number_format($trans_in_qty,2); $g_tot_trans_in_qty += $trans_in_qty; ?></a>&nbsp;</td>

											<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($trans_in_issue_qty,2); $g_tot_trans_in_issue_qty += $trans_in_issue_qty; ?>&nbsp;</th>

											<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><a href='#report_details' onClick="openmypage_transfer_in('<? echo $k_job; ?>','<? echo $k_deter; ?>','<? echo $k_gsm; ?>','650px','transfer_out_popup',6);"><? echo number_format($trans_out_qty,2); $g_tot_trans_out_qty += $trans_out_qty; ?></a>&nbsp;</td>

											<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right" title="<?echo $trans_in_qty.'-'.$trans_in_issue_qty.'-'.$trans_out_from_trans_in_qty;?>"><? echo number_format($trans_in_qty-$trans_in_issue_qty-$trans_out_from_trans_in_qty,2); $g_tot_in_out_stock_qnty += $trans_in_qty-$trans_in_issue_qty-$trans_out_from_trans_in_qty; ?>&nbsp;</td>
											<?
										} ?>
										<td width="" class="word_wrap_break" align="right"><? echo $daysOnHand; ?>&nbsp;</td>
										</tr>
										<?
										$g_total_booking_qty += $booking_qty;
										$g_total_receive_qty += $receive_qty;
										$g_total_program_qnty +=$row['PROGRAM_QNTY'];
									}
								}
							}
						}
					}
					$i++;
				}
				?>
			</table>
		</div>
		<table width="<? echo $table_width; ?>"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="130"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="130"></th>
					<th width="100"></th>
					<th width="200"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150">Grand Total : </th>
					<th width="100"><? echo number_format($g_total_booking_qty,2);?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($g_total_program_qnty,2); ?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($g_total_receive_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_color_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_balance_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_rtn_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_qnty,2);?></th>
					<th width="100"><? echo number_format($g_tot_stock_qnty,2);?></th>
					<th width="100"><? echo number_format($g_tot_trans_in_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_trans_in_issue_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_trans_out_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_in_out_stock_qnty,2);?></th>
					<th width=""></th>
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

if($action=="report_generate4") // Show 4
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
	$order_no 		= trim(str_replace("'","",$txt_order_no));
	$int_ref 		= trim(str_replace("'","",$txt_int_ref));
	$order_id 		= str_replace("'","",$hide_order_id);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);

	$company_arr 	= return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_arr 		= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	if($pocompany_id!=0 || $pocompany_id!=""){

		if($within_group==1)
		{
			$pocompany_cond="and d.po_company_id in($pocompany_id)";
		}else {
			$pocompany_cond="and d.company_id in($pocompany_id)";
		}
	} else {
		$pocompany_cond="";
	}

	if($cbo_store_wise==1){
		$store_cond = " and f.store_id=$cbo_store_name";
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
	if( $date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}

		$date_cond = " and f.transaction_date between '$date_from' and '$date_to' ";
	}

	if ($order_id=='') $order_no_cond=""; else $order_no_cond="and d.id='$order_id'";

	if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and d.job_no like '%$order_no%'";

	if($date_from=="")
	{
		if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$sales_order_year_condition=" and to_char(d.insert_date,'YYYY')=$cbo_year";
			}
		}
	}
	else
	{
		$sales_order_year_condition="";
	}

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no'";
	} else {
		$booking_no_cond="";
	}

	if ($int_ref!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b, FABRIC_SALES_ORDER_MST c
		where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID and c.within_group=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row)
		{
			$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and d.within_group='$within_group' ";
	}
	else
	{
		$within_group_cond="";
	}

	$con = connect();
	execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=157");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=157");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll recv qty
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "SELECT d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.qnty AS rcv_qty, h.id AS no_of_roll_rcv, h.barcode_no
	FROM fabric_sales_order_mst d, order_wise_pro_details e, inv_transaction f, pro_grey_prod_entry_dtls g, pro_roll_details h
	WHERE d.id = e.po_breakdown_id and e.trans_id = f.id and f.id = g.trans_id and g.id = h.dtls_id AND d.company_id=$company_name  $within_group_cond $order_no_cond $booking_no_cond $refBooking_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $store_cond $sales_order_no_cond and e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(2,22,58,84) AND e.trans_type IN(1,4) AND e.trans_id > 0 AND f.status_active = 1 AND f.is_deleted = 0 AND h.entry_form IN(2,22,58,84) AND h.status_active = 1 and h.is_sales=1";
	// echo $sqlRcvRollQty; die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$barcode_no_arr = array();
	foreach($sqlRcvRollRslt as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlTransfer="SELECT d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, e.quantity AS rcv_qty, h.qnty as roll_rcv_qty, g.id AS issue_roll, h.barcode_no, i.transfer_criteria
	FROM fabric_sales_order_mst d, order_wise_pro_details e, inv_transaction f, inv_item_transfer_dtls g, pro_roll_details h, INV_ITEM_TRANSFER_MST i
	WHERE d.id = e.po_breakdown_id and e.trans_id = f.id and e.dtls_id = g.id and g.id = h.dtls_id  and g.mst_id=h.mst_id and e.dtls_id=h.dtls_id and i.id=h.mst_id and i.id=G.MST_ID and i.id= F.MST_ID and e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(133) AND h.entry_form IN(133) and i.entry_form=133 AND e.trans_type IN(5,6) AND f.status_active = 1 AND f.is_deleted = 0 AND g.status_active = 1 AND g.is_deleted = 0 AND h.status_active = 1 AND h.is_deleted = 0 and h.is_sales=1 AND d.company_id=$company_name  $within_group_cond $order_no_cond $booking_no_cond $refBooking_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $store_cond $sales_order_no_cond";
	// echo "<br>".$sqlTransfer; die;
	$sqlTransferResult = sql_select($sqlTransfer);
	foreach($sqlTransferResult as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlTransferResult);

	/*
	|--------------------------------------------------------------------------
	| for production
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,157)");
	        }
		}
		oci_commit($con);

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.color_type_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.brand_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2) and b.entry_form in(2)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=157");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_type_id"] =$row[csf("color_type_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
            $prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
            $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
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
	// echo '<pre>';print_r($allYarnProdArr);die;

	$dataArr = array();
	$poArr = array();
	foreach($sqlRcvRollRslt as $row) // Receive Data Array
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$poArr[$orderId] = $orderId;
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
		$color_type_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_type_id"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$machine_gg=$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"];
		$mc_dia_gg=$machine_dia.'x'.$machine_gg;
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];

		if ($color_id=="")
		{
			$color_id=0;
		}

		$str_ref=$febric_description_id.'*'.$gsm.'*'.$color_type_id.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot.'*'.$stitch_length.'*'.$mc_dia_gg.'*'.$width;
		if($row[csf('entry_form')]  == 84)
		{
			$dataArr[$orderId][$color_id][$str_ref]['issueReturnQty'] += $row[csf('rcv_qty')];
		}
		else
		{
			$dataArr[$orderId][$color_id][$str_ref]['rcvQty'] += $row[csf('rcv_qty')];
		}

		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
	}
	unset($sqlRcvRollRslt);
	// echo "<pre>";print_r($dataArr);die;

	foreach($sqlTransferResult as $row) // Transfer In and Out Data Array
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
		$color_type_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_type_id"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$machine_gg=$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"];
		$mc_dia_gg=$machine_dia.'x'.$machine_gg;
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];

		if ($color_id=="")
		{
			$color_id=0;
		}

		$str_ref=$febric_description_id.'*'.$gsm.'*'.$color_type_id.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot.'*'.$stitch_length.'*'.$mc_dia_gg.'*'.$width;

		if($row[csf('trans_type')] == 5)
		{
			$poArr[$orderId] = $orderId;
			if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer in qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$dataArr[$orderId][$color_id][$str_ref]['transferInQty'] += $row[csf('roll_rcv_qty')];
				}
				else
				{
					$dataArr[$orderId][$color_id][$str_ref]['transferInQty'] += $row[csf('rcv_qty')];
				}
			}
		}
		if($row[csf('trans_type')] == 6)
		{
			if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer out qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$transOutArr[$orderId][$color_id][$str_ref]['transferOutQty'] += $row[csf('roll_rcv_qty')];
				}
				else
				{
					$transOutArr[$orderId][$color_id][$str_ref]['transferOutQty'] += $row[csf('rcv_qty')];
				}
			}
		}

		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
	}
	unset($sqlTransferResult);
	// echo "<pre>";print_r($dataArr);die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	// echo "<pre>";print_r($poArr);die;

	if(!empty($poArr)) // Issue Data Array
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 157, 1,$poArr, $empty_arr);

		/*$con = connect();
		foreach($poArr as $poId)
		{
			// echo "insert into TMP_PO_ID (PO_ID, USER_ID) values ($poId,$user_id)";
			execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
		}
		oci_commit($con);*/
		//disconnect($con);

		//===== For Roll Splitting After Issue start ============
	    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY
	    from pro_roll_split C, pro_roll_details D, GBL_TEMP_ENGINE g
	    where c.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1  and d.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=157 and g.ref_from=1");

	    if(!empty($split_chk_sql))
	    {
	        foreach ($split_chk_sql as $val)
	        {
	            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
	            if ($split_barcode_check[$val['BARCODE_NO']]=="")
	            {
	                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
	                $split_barcode=$val['BARCODE_NO'];
	                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",157)");
	            }
	        }
	        oci_commit($con);

	        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE
	            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B
	            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=157 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
	        if(!empty($split_ref_sql))
	        {
	            foreach ($split_ref_sql as $value)
	            {
	                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
	            }
	        }
	    }
	    unset($split_chk_sql);
	    unset($split_ref_sql);
	    // ======== For Roll Splitting After Issue end =========
		$sqlNoOfRollIssue="SELECT d.company_id, e.prod_id, e.po_breakdown_id, SUM(g.qnty) AS issue_qty, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no
		FROM GBL_TEMP_ENGINE a, fabric_sales_order_mst d, order_wise_pro_details e, inv_transaction f, pro_roll_details g
		WHERE a.ref_val=d.id and a.user_id=$user_id and a.entry_form=157 and a.ref_from=1 and  d.id = e.po_breakdown_id and e.trans_id = f.id and e.dtls_id = g.dtls_id and e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(61) AND e.trans_type = 2 AND f.status_active = 1 AND f.is_deleted = 0 AND g.status_active = 1 AND g.is_deleted = 0 AND g.entry_form IN(61) and g.is_sales=1 AND d.company_id IN($company_name)
		GROUP BY d.company_id, e.prod_id, e.po_breakdown_id, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no ";

		//echo $sqlNoOfRollIssue; //die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$compId = $row[csf('company_id')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];

			$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
			$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
			$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
			$color_type_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_type_id"];
			$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
			$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
			$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];
			$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
			$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
			$machine_gg=$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"];
			$mc_dia_gg=$machine_dia.'x'.$machine_gg;
			$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];

			if ($color_id=="")
			{
				$color_id=0;
			}

	        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
	        if($mother_barcode_no != "")
	        {
	            $deter_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
				$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
				$gsm=$prodBarcodeData[$mother_barcode_no]["gsm"];
				$color_type_id=$prodBarcodeData[$mother_barcode_no]["color_type_id"];
				$yarn_count=$prodBarcodeData[$mother_barcode_no]["yarn_count"];
				$brand_id=$prodBarcodeData[$mother_barcode_no]["brand_id"];
				$yarn_lot=$prodBarcodeData[$mother_barcode_no]["yarn_lot"];
				$stitch_length=$prodBarcodeData[$mother_barcode_no]["stitch_length"];
				$machine_dia=$prodBarcodeData[$mother_barcode_no]["machine_dia"];
				$machine_gg=$prodBarcodeData[$mother_barcode_no]["machine_gg"];
				$mc_dia_gg=$machine_dia.'x'.$machine_gg;
				$width=$prodBarcodeData[$mother_barcode_no]["width"];
	        }
	        $str_ref=$febric_description_id.'*'.$gsm.'*'.$color_type_id.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot.'*'.$stitch_length.'*'.$mc_dia_gg.'*'.$width;
	        $issueQtyArr[$orderId][$color_id][$str_ref]['issueQty'] += $row[csf('issue_qty')];
		}
		unset($sqlNoOfRollIssueResult);
	}
	// echo "<pre>"; print_r($issueQtyArr);die;

	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id
    from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b
    where a.id=b.mst_id and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
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

    $salesSql ="SELECT a.id, b.determination_id, b.color_type_id, b.gsm_weight, b.color_id, b.grey_qty, b.finish_qty
	from GBL_TEMP_ENGINE g, fabric_sales_order_mst a, fabric_sales_order_dtls b
	where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=157 and g.ref_from=1 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sales_result = sql_select($salesSql);
	$salesData=array();
	foreach ($sales_result as $row)
	{
		$salesData[$row[csf('id')]][$row[csf('determination_id')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('color_id')]]['grey_qty'] += $row[csf('grey_qty')];
		$salesData[$row[csf('id')]][$row[csf('determination_id')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('color_id')]]['finish_qty'] += $row[csf('finish_qty')];
	}

	execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =157");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=157");
	oci_commit($con);

	ob_start();
	$table_width = 1810;

	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<fieldset style="width:1810">
		<table cellpadding="0" cellspacing="0" width="1400">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:16px">
					<strong>
						<?
						echo $company_arr[str_replace("'","",$cbo_company_id)];
						?>
					</strong>
				</td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="130">FSO</th>
					<th width="100">Style No</th>
					<th width="100">Buyer</th>
					<th width="200">Fab. Description</th>
					<th width="80">F/GSM</th>
					<th width="80">F/Process</th>
					<th width="80">Y/Count</th>
					<th width="80">Y/Brand</th>
					<th width="80">Lot</th>
					<th width="80">S/L</th>
					<th width="80">M/Dia X GG</th>
					<th width="80">F/Dia</th>
					<th width="80">Colour</th>
					<th width="80">Fin. Qty.</th>
					<th width="80">Grey Qty.</th>
					<th width="80">Grey Received</th>
					<th width="80">Rcvd Balance</th>
					<th width="80">Batch Issue</th>
					<th width="">Stock</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search" align="left">
				<?
				$i=1;
				// $prodWiseSalesDataStatus = array('1' => '1', '2' => '2');
				$grand_tot_recv_qty=$grand_tot_issue_qty=$grand_stock_qty=$grand_tot_finish_qty=$grand_tot_grey_qty=$grand_tot_rcvd_balance=0;
				foreach ($dataArr as $orderId => $orderv)
				{
					foreach ($orderv as $color => $colorv)
					{
						$color_tot_recv_qty=$color_tot_issue_qty=$color_tot_stock_qty=$color_tot_finish_qty=$color_tot_grey_qty=$color_tot_rcvd_balance=0;
						foreach ($colorv as $strRefN => $row)
						{
							$strdata=explode("*", $strRefN);
							$detar_id=$strdata[0];
							$gsm=$strdata[1];
							$color_type_id=$strdata[2];
							$yarn_count=$strdata[3];
							$brand_id=$strdata[4];
							$yarn_lot=$strdata[5];
							$stitch_length=$strdata[6];
							$mc_dia_gg=$strdata[7];
							$dia=$strdata[8];

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$all_color_arr = explode(",", $color);
							$color_names="";
							foreach ($all_color_arr as $key => $id)
							{
								$color_names.=$color_arr[$id].',';
							}
							$color_names=chop($color_names,",");

							$yarn_counts_arr = array_unique(array_filter(explode(",", $yarn_count)));
                            $yarn_counts="";
                            foreach ($yarn_counts_arr as $count) {
                                $yarn_counts .= $yarn_count_arr[$count] . ",";
                            }
                            $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                            $fso_no=$bookingInfoArr[$orderId]['fso_no'];
							$cust_buyer=$bookingInfoArr[$orderId]['customer_buyer'];
							$style_ref_no=$bookingInfoArr[$orderId]['style_ref_no'];

							// all recv
							$recv_qnty=$row['rcvQty'];
							$trans_in_qty=$row['transferInQty'];
							$issue_return_qnty  = $row['issueReturnQty'];

							// all issue
							$iss_qty 			= $issueQtyArr[$orderId][$color][$strRefN]['issueQty'];
							$trans_out_qty = $transOutArr[$orderId][$color][$strRefN]['transferOutQty'];
							// roll wise $recv_ret_qty page did not developed yet

							// echo $recv_qnty.'+'.$issue_return_qnty.'+'.$trans_in_qty.'<br>';
							// echo $iss_qty.'+'.$trans_out_qty.'<br>';
							$recv_tot_qty  = ($recv_qnty+$issue_return_qnty+$trans_in_qty);
							$iss_tot_qty   = ($iss_qty+$trans_out_qty);

							$stock_qty 	   = $recv_tot_qty-$iss_tot_qty;
							//$stock_qty     = number_format($stock_qty,2,".","");
							if($stock_qty < .001)
							{
								$stock_qty = 0;
							}

							$recv_title='Recv:'.$recv_qnty.', trans_in:'.$trans_in_qty.', issue_return:'.$issue_return_qnty;
							$issue_title='Issue:'.$iss_qty.', trans_out:'.$trans_out_qty;

							$stock_title='tot recv:'.$recv_tot_qty.' - tot issue:'.$iss_tot_qty;

							$finish_qty=$salesData[$orderId][$detar_id][$color_type_id][$gsm][$color]['finish_qty'];
							$grey_qty=$salesData[$orderId][$detar_id][$color_type_id][$gsm][$color]['grey_qty'];
							$rcvd_balance=$grey_qty-$recv_tot_qty;

							if ((($get_upto_qnty == 1 && $stock_qty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qty == $txt_qnty) || $get_upto_qnty == 0))
							{
								if($stock_qty > 0 && $cbo_value_with==2)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="40"><?  echo $i; ?></td>
										<td width="130" title="<?=$orderId;?>"><p><?=$fso_no;?></p></td>
										<td width="100"><p><?=$style_ref_no;?></p></td>
										<td width="100"><p><?=$buyer_arr[$cust_buyer];?></p></td>
										<td width="200" class="word_wrap_break" title="<?=$detar_id;?>"><? echo $constuction_arr[$detar_id].', '.$composition_arr[$detar_id]; ?></p></td>
										<td width="80"><p><?=$gsm; ?></p></td>
										<td width="80" title="<?=$color_type_id;?>"><p><?=$color_type[$color_type_id]; ?></p></td>
										<td width="80" title="<?=$yarn_count;?>"><p><?=$yarn_counts; ?></p></td>
										<td width="80" title="<?=$brand_id;?>"><p><?=$brand_arr[$brand_id]; ?></p></td>
										<td width="80"><p><?=$yarn_lot; ?></p></td>
										<td width="80"><p><?=$stitch_length; ?></p></td>
										<td width="80"><p><?=$mc_dia_gg; ?></p></td>
										<td width="80"><p><?=$dia; ?></p></td>
										<td width="80" title="<?=$color; ?>"><p><?=$color_names; ?></p></td>
										<td width="80" align="right"><p><?=number_format($finish_qty,2,'.','');?></p></td>
										<td width="80" align="right"><p><?=number_format($grey_qty,2,'.','');?></p></td>
										<td width="80" align="right" title="<?=$recv_title;?>"><p><?=number_format($recv_tot_qty,2,'.','');?></p></td>
										<td width="80" align="right"><p><?=number_format($rcvd_balance,2,'.',''); ?></p></td>
										<td width="80" align="right" title="<?=$issue_title;?>"><p><?=number_format($iss_tot_qty,2,'.','');?></p></td>
										<td width="" align="right" title="<?=$stock_title;?>"><p><?=number_format($stock_qty,2,'.',''); ?></p></td>
									</tr>
									<?
									$show_color_total=1;
									$i++;
									$color_tot_finish_qty+=$finish_qty;
									$color_tot_grey_qty+=$grey_qty;
									$color_tot_recv_qty+=$recv_tot_qty;
									$color_tot_rcvd_balance+=$rcvd_balance;
									$color_tot_issue_qty+=$iss_tot_qty;
									$color_tot_stock_qty+= $stock_qty;

									$grand_tot_finish_qty+=$finish_qty;
									$grand_tot_grey_qty+=$grey_qty;
									$grand_tot_recv_qty+=$recv_tot_qty;
									$grand_tot_rcvd_balance+=$rcvd_balance;
									$grand_tot_issue_qty+=$iss_tot_qty;
									$grand_stock_qty 	+= $stock_qty;
								}
								else if($stock_qty>=0 && $cbo_value_with==1)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="40"><?  echo $i; ?></td>
										<td width="130" title="<?=$orderId;?>"><p><?=$fso_no;?></p></td>
										<td width="100"><p><?=$style_ref_no;?></p></td>
										<td width="100"><p><?=$buyer_arr[$cust_buyer];?></p></td>
										<td width="200" class="word_wrap_break" title="<?=$detar_id;?>"><? echo $constuction_arr[$detar_id].', '.$composition_arr[$detar_id]; ?></p></td>
										<td width="80"><p><?=$gsm; ?></p></td>
										<td width="80" title="<?=$color_type_id;?>"><p><?=$color_type[$color_type_id]; ?></p></td>
										<td width="80" title="<?=$yarn_count;?>"><p><?=$yarn_counts; ?></p></td>
										<td width="80" title="<?=$brand_id;?>"><p><?=$brand_arr[$brand_id]; ?></p></td>
										<td width="80"><p><?=$yarn_lot; ?></p></td>
										<td width="80"><p><?=$stitch_length; ?></p></td>
										<td width="80"><p><?=$mc_dia_gg; ?></p></td>
										<td width="80"><p><?=$dia; ?></p></td>
										<td width="80" title="<?=$color; ?>"><p><?=$color_names; ?></p></td>
										<td width="80" align="right"><p><?=number_format($finish_qty,2,'.','');?></p></td>
										<td width="80" align="right"><p><?=number_format($grey_qty,2,'.','');?></p></td>
										<td width="80" align="right" title="<?=$recv_title;?>"><p><?=number_format($recv_tot_qty,2,'.','');?></p></td>
										<td width="80" align="right"><p><?=number_format($rcvd_balance,2,'.',''); ?></p></td>
										<td width="80" align="right" title="<?=$issue_title;?>"><p><?=number_format($iss_tot_qty,2,'.','');?></p></td>
										<td width="" align="right" title="<?=$stock_title;?>"><p><?=number_format($stock_qty,2,'.',''); ?></p></td>
									</tr>
									<?
									$show_color_total=1;
									$i++;
									$color_tot_finish_qty+=$finish_qty;
									$color_tot_grey_qty+=$grey_qty;
									$color_tot_recv_qty+=$recv_tot_qty;
									$color_tot_rcvd_balance+=$rcvd_balance;
									$color_tot_issue_qty+=$iss_tot_qty;
									$color_tot_stock_qty+= $stock_qty;

									$grand_tot_finish_qty+=$finish_qty;
									$grand_tot_grey_qty+=$grey_qty;
									$grand_tot_recv_qty+=$recv_tot_qty;
									$grand_tot_rcvd_balance+=$rcvd_balance;
									$grand_tot_issue_qty+=$iss_tot_qty;
									$grand_stock_qty 	+= $stock_qty;
								}
							}
						}
						if($show_color_total)
                        {
                            $show_color_total=0;
							?>
	                        <!-- Color Total -->
	                        <tr class="tbl_bottom">
	                        	<td width="40"></td>
								<td width="130"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="200"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80" align="right">Color Total</td>
								<td width="80"><p><?=number_format($color_tot_finish_qty,2,'.',''); ?></p></td>
								<td width="80"><p><?=number_format($color_tot_grey_qty,2,'.',''); ?></p></td>
								<td width="80"><p><?=number_format($color_tot_recv_qty,2,'.',''); ?></p></td>
								<td width="80"><p><?=number_format($color_tot_rcvd_balance,2,'.',''); ?></p></td>
								<td width="80"><p><?=number_format($color_tot_issue_qty,2,'.',''); ?></p></td>
								<td width=""><p><?=number_format($color_tot_stock_qty,2,'.','');?></p></td>
	                        </tr>
	                        <?
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
					<th width="130"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="200"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80" align="right">Grand Total</th>
					<th width="80"><p><?=number_format($grand_tot_finish_qty,2,'.',''); ?></p></th>
					<th width="80"><p><?=number_format($grand_tot_grey_qty,2,'.',''); ?></p></th>
					<th width="80"><p><?=number_format($grand_tot_recv_qty,2,'.',''); ?></p></th>
					<th width="80"><p><?=number_format($grand_tot_rcvd_balance,2,'.',''); ?></p></th>
					<th width="80"><p><?=number_format($grand_tot_issue_qty,2,'.',''); ?></p></th>
					<th width=""><p><?=number_format($grand_stock_qty,2,'.','');?></p></th>
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

if($action=="report_generate2____")
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
	$order_no 		= trim(str_replace("'","",$txt_order_no));
	$order_id 		= str_replace("'","",$hide_order_id);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);

	$store_arr 	 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$company_arr 	= return_library_array( "select id,company_name from lib_company",'id','company_name');
	$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	$season_arr  	= return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
	$yarn_brand_arr = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
	$color_library 	= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');



	if($pocompany_id!=0 || $pocompany_id!=""){

		if($within_group==1)
		{
			$pocompany_cond="and d.po_company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
		}else {
			$pocompany_cond="and d.company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
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
			$buyer_id_cond2=" and f.po_buyer in (".str_replace("'","",$po_buyer_id).")";
		}else {

			$buyer_id_cond=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond2=" and f.buyer_id in (".str_replace("'","",$po_buyer_id).")";
		}
	}

	$date_cond="";

	if( $date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}

		$date_cond = " and e.transaction_date between '$date_from' and '$date_to' ";
	}


	if ($order_id=='') $order_no_cond=""; else $order_no_cond="and d.id='$order_id'";
	if ($order_id=='') $toOrderIdCond=""; else $toOrderIdCond="and a.to_order_id='$order_id'";

	if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and d.job_no like '%$order_no%'";
	if ($order_no=='') $sales_to_order_no_cond=""; else $sales_to_order_no_cond=" and f.job_no like '%$order_no%'";


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
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no'";
	} else {
		$booking_no_cond="";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and d.within_group='$within_group' ";
	}
	else
	{
		$within_group_cond = "";
	}

	$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no, d.id as sales_id
	from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
	where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $order_no_cond $booking_no_cond $program_no_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $sales_order_no_cond
	group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no,d.id";
	//echo $sql;

	$sql_result=sql_select($sql);

	$salesIdsArr=array();
	$rcvQntyArr=array();
	foreach( $sql_result as $row )
	{
		if($salesIdsChk[$row[csf('sales_id')]]=='')
		{
			$salesIdsChk[$row[csf('sales_id')]] = $row[csf('sales_id')];
			array_push($salesIdsArr, $row[csf('sales_id')]);
		}

		$rcvQntyArr[$row[csf('job_no')]][$row[csf('febric_description_id')]][$row[csf('gsm')]]['receive_qty'] += $row[csf('receive_qty')];
	}
	//var_dump($salesIdsArr);
	// echo "<pre>";
	// print_r($rcvQntyArr);


	$booking_sql = "SELECT A.JOB_NO, B.DETERMINATION_ID, B.GSM_WEIGHT,B.COLOR_ID, B.GREY_QNTY_BY_UOM
		FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B
		WHERE A.ID = B.MST_ID  AND A.COMPANY_ID = ".$company_name."  AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 ".where_con_using_array($salesIdsArr,0,'A.ID')."
		ORDER BY A.ID";
		//echo $booking_sql;

		$booking_sql_rslt=sql_select($booking_sql);
		$bookingQntyArr = array();
		foreach ($booking_sql_rslt as $row)
		{
			$bookingQntyArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_ID']]['GREY_QNTY_BY_UOM'] += $row['GREY_QNTY_BY_UOM'];
		}

		// echo "<pre>";
		// print_r($bookingQntyArr);



	$main_sql = "SELECT
			A.BOOKING_NO,
			B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA,
			C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY ,
			E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID,
			F.ID AS SALES_ID, F.GREY_QTY, B.MACHINE_GG, E.PO_JOB_NO, E.COMPANY_ID, E.CUSTOMER_BUYER, B.COLOR_RANGE, B.STITCH_LENGTH
			FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F
			WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND C.IS_SALES = 1  AND A.COMPANY_ID = ".$company_name."  AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ".where_con_using_array($salesIdsArr,0,'E.ID')."
			ORDER BY B.ID, A.BOOKING_NO";

	//echo $main_sql;
	$main_sql_rslt=sql_select($main_sql);
	$mainArr = array();
	$programIdsArr = array();
	foreach ($main_sql_rslt as $row)
	{


		if($duplicate_check[$row['DTLS_ID']] != $row['DTLS_ID'])
		{
			$duplicate_check[$row['DTLS_ID']] = $row['DTLS_ID'];

			if($prog_ids_check[$row['ID']] == '')
			{
				$prog_ids_check[$row['ID']] = $row['ID'];
				array_push($programIdsArr,$row['ID']);
			}


			//for color
			$color_arr = array();
			$exp_color = array();
			$exp_color = explode(",", $row['COLOR_ID']);
			foreach ($exp_color as $key=>$val)
			{
				$color_arr[$val] = $color_library[$val];
			}
			//end for color

			//for color_range
			$color_range_arr = array();
			$exp_color_range = array();
			$exp_color_range = explode(",", $row['COLOR_RANGE']);
			foreach ($exp_color_range as $key=>$val)
			{
				$color_range_arr[$val] = $color_range[$val];
			}
			//end for color_range

			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['JOB_NO'] = $row['JOB_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['DETERMINATION_ID'] = $row['DETERMINATION_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['CUSTOMER_BUYER'] = $row['CUSTOMER_BUYER'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_DIA'] = $row['FABRIC_DIA'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_COLOR_ID'] = $row['COLOR_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_COLOR'] = implode(', ', $color_arr);
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['COLOR_RANGE'] = implode(', ', $color_range_arr);
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];

		}
	}
	unset($main_sql_rslt);
	// echo "<pre>";
	// print_r($mainArr);

	$rcv_sql =  "SELECT A.BOOKING_NO,B.BOOKING_ID, SUM(A.QNTY) QNTY FROM PRO_ROLL_DETAILS A, INV_RECEIVE_MASTER B WHERE A.ENTRY_FORM = 58 AND A.MST_ID = B.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 ".where_con_using_array($programIdsArr,1,'A.BOOKING_NO')."  GROUP BY A.BOOKING_NO,B.BOOKING_ID";

	//echo $rcv_sql;//die;
	$rcv_sql_rslt=sql_select($rcv_sql);
	$duplicate_check = array();
	foreach($rcv_sql_rslt as $row)
	{
		$knitProQtyArr[$row['BOOKING_NO']] += $row['QNTY'];
	}


	$sql_requ = "SELECT KNIT_ID, REQUISITION_NO, PROD_ID, YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($programIdsArr,0,'KNIT_ID')." ";
	//echo $sql_requ;
	$sql_requ_result = sql_select($sql_requ);
	$requArr = array();
	foreach ($sql_requ_result as $row)
	{
		$requArr[$row['KNIT_ID']]['prod_id']  .= $row['PROD_ID'].', ';
	}
	//var_dump($requArr);

	$product_details_array = array();
	$yarn_info_sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND COMPANY_ID=".$company_name." AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PROD_ID FROM PPL_YARN_REQUISITION_ENTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($programIdsArr,0,'KNIT_ID').")";

	//echo $yarn_info_sql;

	$yarn_info_result = sql_select($yarn_info_sql);
	foreach ($yarn_info_result as $row)
	{
		$product_details_array[$row['ID']]['count'] = $yarn_count_arr[$row['YARN_COUNT_ID']];
		$product_details_array[$row['ID']]['lot'] = $row['LOT'];
		$product_details_array[$row['ID']]['brand'] = $yarn_brand_arr[$row['BRAND']];
	}
	unset($yarn_info_result);
	// echo "<pre>";
	// print_r($product_details_array);


	$issue_rtn_sql = "SELECT A.RECV_NUMBER,SUM(B.QNTY) AS ISSUE_RTN_QTY,B.PO_BREAKDOWN_ID AS PO_ID, C.PROD_ID,C.FEBRIC_DESCRIPTION_ID,C.GSM,C.COLOR_ID,D.COMPANY_ID, D.JOB_NO
	FROM INV_RECEIVE_MASTER A,INV_TRANSACTION E,PRO_GREY_PROD_ENTRY_DTLS C,PRO_ROLL_DETAILS B,FABRIC_SALES_ORDER_MST D
	WHERE A.ID=E.MST_ID AND E.ID=C.TRANS_ID AND C.ID=B.DTLS_ID AND B.PO_BREAKDOWN_ID=D.ID  AND B.ENTRY_FORM IN(84) AND C.TRANS_ID>0 AND A.ITEM_CATEGORY=13 AND D.COMPANY_ID=3 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND
	C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND
	E.TRANSACTION_TYPE=4 ".where_con_using_array($salesIdsArr,0,'D.ID')."
	GROUP BY A.RECV_NUMBER,B.PO_BREAKDOWN_ID, C.PROD_ID,C.FEBRIC_DESCRIPTION_ID,C.GSM,C.COLOR_ID,D.COMPANY_ID, D.JOB_NO";

	//echo $issue_rtn_sql;

	$issue_rtn_sql_result = sql_select($issue_rtn_sql);
	$issueRtnQtyArr = array();
	foreach ($issue_rtn_sql_result as $row)
	{
		$issueRtnQtyArr[$row['JOB_NO']][$row['FEBRIC_DESCRIPTION_ID']][$row['GSM']]['issue_rtn_qty'] += $row['ISSUE_RTN_QTY'];
	}
	unset($issue_rtn_sql_result);
	// echo "<pre>";
	// print_r($issueRtnQtyArr);

	$issue_sql = "SELECT A.ISSUE_NUMBER, SUM(C.ISSUE_QNTY) AS ISSUE_QNTY,C.PROD_ID, D.JOB_NO FROM INV_ISSUE_MASTER A,INV_TRANSACTION E,INV_GREY_FABRIC_ISSUE_DTLS C,PRO_ROLL_DETAILS B,FABRIC_SALES_ORDER_MST D
	WHERE A.ID=E.MST_ID AND E.ID=C.TRANS_ID AND C.ID=B.DTLS_ID AND B.PO_BREAKDOWN_ID=D.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND
	C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=2 AND A.ENTRY_FORM=61
	AND A.ITEM_CATEGORY=13 ".where_con_using_array($salesIdsArr,0,'D.ID')."
	group by A.ISSUE_NUMBER, C.PROD_ID, D.JOB_NO ";

	//echo $issue_sql;

	$issue_sql_result = sql_select($issue_sql);
	$prodIdsArr = array();
	foreach($issue_sql_result as $row )
	{
		if($prodIdsChk[$row[csf('PROD_ID')]]=='')
		{
			$prodIdsChk[$row[csf('PROD_ID')]] = $row[csf('PROD_ID')];
			array_push($prodIdsArr, $row[csf('PROD_ID')]);
		}
	}

	$prod_info_sql = "SELECT ID, DETARMINATION_ID, GSM FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=13 AND COMPANY_ID=".$company_name." AND STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($prodIdsArr,0,'ID')."  ";
	//echo $prod_info_sql;

	$prod_info_result = sql_select($prod_info_sql);
	$prodInfoArr = array();
	foreach($prod_info_result as $row )
	{
		$prodInfoArr[$row[csf('ID')]]['detarmination_id'] = $row[csf('DETARMINATION_ID')];
		$prodInfoArr[$row[csf('ID')]]['gsm'] = $row[csf('GSM')];
	}
	//var_dump($prodInfoArr);
	$issueQtyArr = array();
	foreach($issue_sql_result as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('PROD_ID')]]['gsm'];

		$issueQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['issue_qnty'] += $row['ISSUE_QNTY'];
	}
	// echo "<pre>";
	// print_r($issueQtyArr);

	$trans_out_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID,SUM(D.QNTY) AS TRANSFER_OUT_QNTY,F.JOB_NO FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F WHERE A.STATUS_ACTIVE=1 AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.FROM_ORDER_ID=F.ID AND B.STATUS_ACTIVE=1 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=6  AND A.ID=D.MST_ID AND D.ENTRY_FORM=133 AND B.ID=D.DTLS_ID AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.FROM_ORDER_ID,B.FROM_PROD_ID,F.JOB_NO";
	//echo $trans_out_sql;
	$trans_out_rslt = sql_select($trans_out_sql);

	$trnsOutQtyArr = array();
	foreach($trans_out_rslt as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['gsm'];

		$trnsOutQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['TRANSFER_OUT_QNTY'] += $row['TRANSFER_OUT_QNTY'];
	}
	unset($trans_out_rslt);
	// echo "<pre>";
	// print_r($trnsOutQtyArr);

	$trans_in_sql = "SELECT A.TO_ORDER_ID,B.TO_PROD_ID,SUM(D.QNTY) AS TRANSFER_IN_QNTY,F.JOB_NO FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F WHERE  A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.TO_ORDER_ID=F.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=6  AND A.ID=D.MST_ID AND D.ENTRY_FORM=133 AND B.ID=D.DTLS_ID  ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.TO_ORDER_ID,B.TO_PROD_ID,F.JOB_NO";
	//echo $trans_in_sql;
	$trans_in_sql_result = sql_select($trans_in_sql);
	$trnsInQtyArr = array();
	foreach($trans_in_sql_result as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('TO_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('TO_PROD_ID')]]['gsm'];

		$trnsInQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['transfer_in_qnty'] += $row['TRANSFER_IN_QNTY'];
	}
	unset($trans_in_sql_result);
	// echo "<pre>";
	// print_r($trnsInQtyArr);


	ob_start();
	$table_width = "3150";

	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<fieldset style="width:3200">
		<table cellpadding="0" cellspacing="0" width="3140">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:16px">
					<strong>
						<?
						echo $company_arr[str_replace("'","",$cbo_company_id)];
						?>
					</strong>
				</td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td colspan="24" width="100%" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? if($date_from!="" || $date_to!="") echo "From : ".change_date_format($date_from)." To : ".change_date_format($date_to)."" ;?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
			<thead>
				<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="130" rowspan="2">Company </th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="100" rowspan="2">Style No</th>
					<th width="100" rowspan="2">Booking No</th>
					<th width="130" rowspan="2">FSO</th>

					<th colspan="14">Fabric Details</th>
					<th colspan="5">Receive Details</th>
					<th colspan="4">Issue Details</th>
					<th colspan="1">Stock Details</th>

				</tr>
				<tr>
					<th width="100">Construction</th>
					<th width="200">Composition</th>
					<th width="100">GSM</th>
					<th width="100">Program No.</th>
					<th width="150">Color</th>
					<th width="100">Color Range</th>
					<th width="100">Stitch Length</th>
					<th width="100">M/Dia</th>
					<th width="100">F/Dia</th>
					<th width="100">Program Qty</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Booking Qty</th>

					<th width="100">Recv. Qty.</th>
					<th width="100">Booking receive <br> Balance Qty</th>
					<th width="100">Issue Return Qty.</th>
					<th width="100">Transf. In Qty.</th>
					<th width="100">Total Recv.</th>

					<th width="100">Issue Qty.</th>
					<th width="100">Receive Return Qty.</th>
					<th width="100">Transf. Out Qty.</th>
					<th width="100">Total Issue</th>

					<th width="">Stock Qty.</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search2" align="left">

			<?
			$job_count = array();
			$deter_count = array();
			$gsm_count = array();
			//$program_count = array();
			foreach($mainArr as $k_job=>$v_job)
			{
				foreach($v_job as $k_deter=>$v_deter)
				{
					foreach($v_deter as $k_gsm=>$v_gsm)
					{
						foreach($v_gsm as $k_prog_no=>$row)
						{
							$job_count[$k_job]++;
							$deter_count[$k_job][$k_deter]++;
							$gsm_count[$k_job][$k_deter][$k_gsm]++;
						}
					}
				}
			}

				$i=1;
				$g_total_booking_qty = 0;
				$g_total_receive_qty = 0;
				$g_total_Booking_rcv_b_qty = 0;
				$g_tot_issue_rtn_qty = 0;
				$g_total_Recv = 0;
				$g_tot_issue_qnty = 0;
				$g_tot_trans_in_qty = 0;
				$g_tot_trans_out_qty = 0;
				$g_tot_issue_qty = 0;
				$g_tot_stock_qnty = 0;
				foreach($mainArr as $k_job=>$v_job)
				{
					foreach($v_job as $k_deter=>$v_deter)
					{
						foreach($v_deter as $k_gsm=>$v_gsm)
						{
							foreach($v_gsm as $k_prog_no=>$row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								// echo "<pre>";
								// print_r($k_prog_no);

								$prodIdsArrr = $requArr[$k_prog_no]['prod_id'];
								$prodIdsArrData = array_unique(explode(", ",chop($prodIdsArrr ,",")));
								//var_dump($prodIdsArrData);

								$yarn_count = '';
								$yarn_lot = '';
								$yarn_brand = '';
								$booking_qty = 0;
								$receive_qty = 0;
								$issue_rtn_qty = 0;
								$tot_rcv_qnty =0;
								$issue_qnty =0;
								$trans_in_qty =0;
								$trans_out_qty =0;
								foreach ($prodIdsArrData as $prod_id)
								{
									//var_dump($prod_id);
									if($yarn_count=='')
									{
										$yarn_count = $product_details_array[$prod_id]['count'];
									}
									else
									{
										$yarn_count .= ", ".$product_details_array[$prod_id]['count'];
									}

									if($yarn_lot=='')
									{
										$yarn_lot = $product_details_array[$prod_id]['lot'];
									}
									else
									{
										$yarn_lot .= ', '.$product_details_array[$prod_id]['lot'];
									}

									if($yarn_brand=='')
									{
										$yarn_brand = $product_details_array[$prod_id]['brand'];
									}
									else
									{
										$yarn_brand .= ', '.$product_details_array[$prod_id]['brand'];
									}
								}


								$booking_qty += $bookingQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']][$row['FABRIC_COLOR_ID']]['GREY_QNTY_BY_UOM'];
								$tot_rcv_qnty += $rcvQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['receive_qty'];
								$receive_qty += $knitProQtyArr[$k_prog_no];
								$issue_qnty += $issueQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['issue_qnty'];
								$issue_rtn_qty += $issueRtnQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['issue_rtn_qty'];
								$trans_in_qty += $trnsInQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['transfer_in_qnty'];
								$trans_out_qty += $trnsOutQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['TRANSFER_OUT_QNTY'];

								$job_span = $job_count[$k_job]++;
								$deter_span = $deter_count[$k_job][$k_deter]++;
								$gsm_span = $gsm_count[$k_job][$k_deter][$k_gsm]++;

								$fabric_desc = $row['FABRIC_DESC'];
								$fabric_desc_data = explode(',',$fabric_desc);
								$construction  = $fabric_desc_data[0];
								$composition   = $fabric_desc_data[1];



								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

								<?
								if(!in_array($k_job,$job_chk))
								{
									$job_chk[]=$k_job;
									?>

									<td width="40" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $i; ?>&nbsp;</td>
									<td width="130" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $company_arr[$row['COMPANY_ID']]; ?></td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?></td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['STYLE_REF_NO']; ?></td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['BOOKING_NO']; ?></td>
									<td width="130" class="word_wrap_break" rowspan="<? echo $job_span ;?>" valign="middle" ><? echo $row['JOB_NO']; ?></td>
								<?
								}
								if(!in_array($k_job."**".$k_deter,$deter_chk))
								{
									$deter_chk[]=$k_job."**".$k_deter;
								?>
									<td width="100" class="word_wrap_break" rowspan="<? echo $deter_span ;?>" valign="middle"><? echo $construction; ?></td>
									<td width="200" class="word_wrap_break" rowspan="<? echo $deter_span ;?>" valign="middle"><? echo $composition; ?></td>
								<? }
								if(!in_array($k_job."**".$k_deter."**".$k_gsm,$gsm_chk))
								{
									$gsm_chk[]=$k_job."**".$k_deter."**".$k_gsm;
								?>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle"><? echo $row['GSM_WEIGHT']; ?></td>
								<?
								}
								?>
									<td width="100" class="word_wrap_break"><? echo $k_prog_no; ?></td>
									<td width="150" class="word_wrap_break"><? echo $row['FABRIC_COLOR']; ?></td>
									<td width="100" class="word_wrap_break"><? echo $row['COLOR_RANGE']; ?></td>
									<td width="100" class="word_wrap_break"><? echo $row['STITCH_LENGTH']; ?></td>
									<td width="100" class="word_wrap_break"><? echo $row['MACHINE_DIA']; ?></td>
									<td width="100" class="word_wrap_break"><? echo $row['FABRIC_DIA']; ?></td>
									<td width="100" class="word_wrap_break"><? echo decimal_format($row['PROGRAM_QNTY'], '1', ','); ?></td>
									<td width="100" class="word_wrap_break"><? echo rtrim($yarn_lot,', '); ?></td>
									<td width="100" class="word_wrap_break"><? echo rtrim($yarn_count,', '); ?></td>
									<td width="100" class="word_wrap_break"><? echo rtrim($yarn_brand,', '); ?></td>
									<td width="100" align="right" class="word_wrap_break"><? echo number_format($booking_qty,2);  ?></th>
									<td width="100" align="right" class="word_wrap_break"><? echo number_format($receive_qty,2); $tot_receive_qty += $receive_qty;?></th>
									<td width="100" align="right" class="word_wrap_break"><? echo number_format($booking_qty-$receive_qty,2); ?></th>
								<?
								if(!in_array($k_job."**".$k_deter."**".$k_gsm,$gsm1_chk))
								{
									$gsm1_chk[]=$k_job."**".$k_deter."**".$k_gsm;
								?>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($issue_rtn_qty,2); $g_tot_issue_rtn_qty += $issue_rtn_qty; ?>&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($trans_in_qty,2); $g_tot_trans_in_qty += $trans_in_qty; ?>&nbsp;</th>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right" title="Recv. Qty+Issue Return Qty+Transf. In Qty">
									<?
									$total_Recv = ($tot_rcv_qnty+$issue_rtn_qty+$trans_in_qty);
									echo number_format($total_Recv,2); $g_total_Recv += $total_Recv; ?>&nbsp;
									</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($issue_qnty,2); 	$g_tot_issue_qnty += $issue_qnty; ?>&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right">&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right"><? echo number_format($trans_out_qty,2); $g_tot_trans_out_qty += $trans_out_qty; ?>&nbsp;</td>
									<td width="100" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right" title="Issue Qty+Receive Return Qty+Transf. Out Qty"><? echo number_format($issue_qnty+$trans_out_qty,2); $g_tot_issue_qty += $issue_qnty+$trans_out_qty; ?>&nbsp;</td>
									<td width="" class="word_wrap_break" rowspan="<? echo $gsm_span ;?>" valign="middle" align="right" title="(Total Recv-Total Issue)"><? echo number_format($total_Recv-($issue_qnty+$trans_out_qty),2); 	$g_tot_stock_qnty += $total_Recv-($issue_qnty+$trans_out_qty); ?>&nbsp;</td>
								<? } ?>
								</tr>
								<?
								$g_total_booking_qty += $booking_qty;
								$g_total_receive_qty += $receive_qty;
								$g_total_Booking_rcv_b_qty +=$booking_qty-$receive_qty;

							}
						}
					}

					$i++;
				}
				?>
			</table>
		</div>
		<table width="<? echo $table_width; ?>"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="130"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="130"></th>
					<th width="100"></th>
					<th width="200"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100">Grand Total : </th>
					<th width="100"><? echo number_format($g_total_booking_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_receive_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_Booking_rcv_b_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_rtn_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_trans_in_qty,2);?></th>
					<th width="100"><? echo number_format($g_total_Recv,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_qnty,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($g_tot_trans_out_qty,2);?></th>
					<th width="100"><? echo number_format($g_tot_issue_qty,2);?></th>
					<th width=""><? echo number_format($g_tot_stock_qnty,2);?></th>
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

if($action=="report_generate2_old")
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
	$order_no 		= trim(str_replace("'","",$txt_order_no));
	$order_id 		= str_replace("'","",$hide_order_id);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);

	$store_arr 	 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$company_arr 	= return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	$season_arr  	= return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$yarn_brand_arr = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
	$color_arr 		= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	if($pocompany_id!=0 || $pocompany_id!=""){

		if($within_group==1)
		{
			$pocompany_cond="and d.po_company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
		}else {
			$pocompany_cond="and d.company_id in($pocompany_id)";
			$pocompany_cond2="and f.po_company_id in($pocompany_id)";
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
			$buyer_id_cond2=" and f.po_buyer in (".str_replace("'","",$po_buyer_id).")";
		}else {

			$buyer_id_cond=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
			$buyer_id_cond2=" and f.buyer_id in (".str_replace("'","",$po_buyer_id).")";
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

	if ($order_id=='') $order_no_cond=""; else $order_no_cond="and d.id='$order_id'";
	if ($order_id=='') $toOrderIdCond=""; else $toOrderIdCond="and a.to_order_id='$order_id'";

	if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and d.job_no like '%$order_no%'";
	if ($order_no=='') $sales_to_order_no_cond=""; else $sales_to_order_no_cond=" and f.job_no like '%$order_no%'";


	if($date_from=="")
	{
		if ($db_type == 0)
		{
			if($cbo_year>0)
			{
				$sales_order_year_condition=" and YEAR(d.insert_date)=$cbo_year";
				$sales_order_year_condition2=" and YEAR(f.insert_date)=$cbo_year";
			}
		}
		else if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$sales_order_year_condition=" and to_char(d.insert_date,'YYYY')=$cbo_year";
				$sales_order_year_condition2=" and to_char(f.insert_date,'YYYY')=$cbo_year";
			}
		}
	} else {
		$sales_order_year_condition="";
	}

	if ($program_no=="") $program_no_cond=""; else $program_no_cond="and b.booking_no='$program_no'";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no'";
		$booking_no_cond2=" and f.sales_booking_no like '%$booking_no'";
	} else {
		$booking_no_cond=$booking_no_cond2="";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and d.within_group='$within_group' ";
		$within_group_cond2 = " and f.within_group='$within_group' ";
	}
	else
	{
		$within_group_cond = $within_group_cond2="";
	}

	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no
	from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
	where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $order_no_cond $booking_no_cond $program_no_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $testCond $store_cond $sales_order_no_cond
	group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no";
	//die;
	//echo "<br />";
	// Main query once
	//echo $sql;die;

	$masterData=sql_select($sql);
	//echo $sql;die;
	if(empty($masterData))
	{
		// echo "SELECT a.company_id,a.to_order_id as po_id,b.from_prod_id as prod_id, e.color_range,d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no, d.po_company_id as lc_company_id,d.po_buyer, d.po_job_no, d.booking_without_order, d.booking_type, d.booking_entry_form , c.detarmination_id,c.gsm
		// from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , product_details_master c
		// where a.id=b.mst_id and a.to_order_id=d.id and b.from_prod_id = c.id and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.company_id = $company_name $order_no_cond $booking_no_cond $date_cond $sales_order_no_cond
		// group by a.company_id,a.to_order_id,b.from_prod_id, e.color_range, d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no,d.po_company_id, d.po_buyer,d.po_job_no, d.booking_without_order, d.booking_type,d.booking_entry_form,c.detarmination_id,c.gsm";
		/* If sales order data not found in receive then this part will check for transfer in data*/
		$trans_in_row = sql_select("SELECT a.company_id,a.to_order_id as po_id,b.from_prod_id as prod_id, e.color_range,d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no, d.po_company_id as lc_company_id,d.po_buyer, d.po_job_no, d.booking_without_order, d.booking_type, d.booking_entry_form , c.detarmination_id,c.gsm
			from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , product_details_master c
			where a.id=b.mst_id and a.to_order_id=d.id and b.from_prod_id = c.id and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.company_id = $company_name $order_no_cond $booking_no_cond $date_cond $sales_order_no_cond
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
		$prodWiseSalesDataStatus = $prodWiseOpening=$jobDataArr=array();
		foreach($masterData as $row)
		{
			if($duplicate_chk[$row[csf("job_no")]]=='')
			{
				$duplicate_chk[$row[csf("job_no")]]=$row[csf("job_no")];
				array_push($jobDataArr,$row[csf("job_no")]);
			}
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
			if($row[csf("color_range_id")]!=""){
				if($transaction_date >= $date_frm){
					$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*1**".$row[csf("color_id")]."_";
				}else{
					if($transaction_date < $date_frm){
						$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*2**".$row[csf("color_id")]."_";
						$receiveOpening[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] += $row[csf("receive_qty")];
					}
				}
			}
		}
	}
	/*echo "<pre>";
	print_r($prodWiseSalesDataStatus);
	die;*/
	//echo "<br />";
	$trans_in_sql = "SELECT a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,sum(d.qnty) as transfer_in_qnty,d.barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id as lc_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form
	from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f
	where a.entry_form=133 and a.status_active=1 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.to_trans_id and b.from_prod_id=c.id and b.id=d.dtls_id and d.po_breakdown_id=f.id and b.status_active=1 $toOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and d.entry_form=133 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sales_to_order_no_cond
	group by a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,d.barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form";
	//echo "<br />";
	//echo $trans_in_sql;
	$trans_in_data = sql_select($trans_in_sql);

	foreach($trans_in_data as $row)
	{
		$poids .= $row[csf("to_order_id")].",";
		$salesData[$row[csf("to_order_id")]]['booking_id'] = $row[csf("booking_id")];
		$salesData[$row[csf("to_order_id")]]['working_company_id'] = $row[csf("company_id")];
		$salesData[$row[csf("to_order_id")]]['booking_no'] = $row[csf("sales_booking_no")];
		$salesData[$row[csf("to_order_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$salesData[$row[csf("to_order_id")]]['within_group'] = $row[csf("within_group")];
		$salesData[$row[csf("to_order_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
		$salesData[$row[csf("to_order_id")]]['fso_no'] = $row[csf("job_no")];

		// within group yes
		if($row[csf("within_group")]==1)
		{
			$salesData[$row[csf("to_order_id")]]['buyer_id'] = $row[csf("po_buyer")];
			$salesData[$row[csf("to_order_id")]]['job_no'] = $row[csf("po_job_no")];
		} else {
			$salesData[$row[csf("to_order_id")]]['buyer_id'] = $row[csf("buyer_id")];
			$salesData[$row[csf("to_order_id")]]['job_no'] = "";
		}

		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType = "Sample With Order";
			}
		}
		else
		{
			$bookingType = $booking_type_arr[$row[csf('booking_entry_form')]];
		}

		$salesData[$row[csf("to_order_id")]]['booking_type'] = $bookingType;
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
	//echo "hi";die;
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
		$ProductionCond = " and (";

		foreach($poids_chunk as $chunk_arr)
		{
			$po_cond.=" d.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$trans_po_id_cond.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$salse_id_cond.=" a.id in(".implode(",",$chunk_arr).") or ";
			$toOrderIdCond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			$fromOrderIdCond.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			$ProductionCond.=" b.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
		}

		$fromOrderIdCond =chop($fromOrderIdCond,"or ");
		$toOrderIdCond =chop($toOrderIdCond,"or ");
		$salse_id_cond=chop($salse_id_cond,"or ");
		$po_cond=chop($po_cond,"or ");
		$trans_po_id_cond=chop($trans_po_id_cond,"or ");
		$ProductionCond=chop($ProductionCond,"or ");

		$fromOrderIdCond .=")";
		$toOrderIdCond .=")";
		$salse_id_cond.=")";
		$po_cond.=")";
		$trans_po_id_cond.=")";
		$ProductionCond.=")";
	}
	else
	{
		$fromOrderIdCond=" and a.from_order_id in($poids)";
		$toOrderIdCond=" and a.to_order_id in($poids)";
		$salse_id_cond=" and a.id in($poids)";
		$po_cond=" and d.po_breakdown_id in($poids)";
		$trans_po_id_cond=" and c.po_breakdown_id in($poids)";
		$ProductionCond=" and b.po_breakdown_id in($poids)";
	}

	// add salses id in where clause
	if($salse_id_cond!="")
	{
		$salesSql ="SELECT a.id,sum(b.grey_qty) as fso_qty, sum(b.finish_qty) as booking_qty,a.po_job_no
		from fabric_sales_order_mst a,fabric_sales_order_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $salse_id_cond
		group by a.id,a.company_id,a.buyer_id, a.style_ref_no, a.job_no, a.job_no_prefix_num, a.sales_booking_no, a.booking_id,a.within_group,a.po_job_no";

		//echo $salesSql;
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


	$production_sql = sql_select("SELECT a.color_range_id,b.barcode_no,a.yarn_lot,a.yarn_count,b.po_breakdown_id,a.prod_id,a.color_id,a.stitch_length,a.brand_id from pro_grey_prod_entry_dtls a,pro_roll_details b where a.trans_id=0 and a.status_active=1 and a.id=b.dtls_id and b.entry_form in(2)");
	foreach ($production_sql as $production_row) {
		$barcode_color_range[$production_row[csf("barcode_no")]] = $production_row[csf("color_range_id")];
		$barcode_color_ids[$production_row[csf("barcode_no")]] = $production_row[csf("color_id")];
		$stitch_length_arr[$production_row[csf("barcode_no")]] = $production_row[csf("stitch_length")];

		$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_lot"][$production_row[csf("yarn_lot")]] = $production_row[csf("yarn_lot")];
		$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_count"][$production_row[csf("yarn_count")]] = $production_row[csf("yarn_count")];
		$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["brand_id"][$production_row[csf("brand_id")]] = $production_row[csf("brand_id")];
	}
	//echo "Here 10";die;
	/*if(!empty($receive_barcodes)){
		if($db_type==2 && count($receive_barcodes)>999)
		{
			$barcode_chunk=array_chunk($receive_barcodes,999) ;
			$barcode_cond = " and (";
			$barcode_cond2 = " and (";

			foreach($barcode_chunk as $chunk_arr)
			{
				$barcode_cond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
				$barcode_cond2.=" d.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$barcode_cond = chop($barcode_cond,"or ");
			$barcode_cond .=")";
			$barcode_cond2 = chop($barcode_cond,"or ");
			$barcode_cond2 .=")";
		}
		else
		{
			$barcode_cond=" and b.barcode_no in(".implode(",",$receive_barcodes).")";
			$barcode_cond2=" and d.barcode_no in(".implode(",",$receive_barcodes).")";
		}

	}*/

	if($poids!="")
	{
		$trans_out_sql = "SELECT a.from_order_id,e.transaction_date,b.from_prod_id,d.qnty as transfer_out_qnty,d.barcode_no	from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and b.from_prod_id=c.id and a.from_order_id=f.id and b.status_active=1 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6 $fromOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and a.id=d.mst_id and d.entry_form=133 and b.id=d.dtls_id and d.status_active=1 and d.is_deleted=0";
		$trans_out_data = sql_select($trans_out_sql);

		foreach($trans_out_data as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$transOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_out_qnty")];
			}else{
				if($transaction_date < $date_frm){
					$openingTransOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_out_qnty")];
				}
			}
		}

		$issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2	and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.is_returned<>1 $po_cond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2";

		$sql_iss=sql_select($issue_sql);

		$knit_issue_arr=array();
		foreach($sql_iss as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$knit_issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_qty'] += $row[csf('issue_qty')];
			}else{
				if($transaction_date < $date_frm){
					$opening_issue[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_qty'] += $row[csf('issue_qty')];
				}
			}
		}

		unset($sql_iss);

		$sql_issue_return = sql_select("SELECT b.prod_id,e.transaction_date,d.po_breakdown_id as po_id,d.qnty as issue_return_qty, d.barcode_no			from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details d,fabric_sales_order_mst f where a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=84 and e.transaction_type=4	and d.entry_form=84 and a.receive_basis in(0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=f.id $po_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2");
		$inssue_return_array=array();
		foreach($sql_issue_return as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$inssue_return_array[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_return_qty'] += $row[csf('issue_return_qty')];
			}else{
				if($transaction_date < $date_frm){
					$opening_issue_return[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_return_qty'] += $row[csf('issue_return_qty')];
				}
			}
		}
		unset($sql_issue_return);

		foreach($trans_in_data as $row)
		{
			$prod_id .= $row[csf("from_prod_id")].",";
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			$color_ids = $barcode_color_ids[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*1*".$row[csf("from_order_id")]."*".$color_ids."_";
			}else{
				if($transaction_date < $date_frm){
					$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*2*".$row[csf("from_order_id")]."*".$color_ids."_";
					$transferInOpening[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_in_qnty")];
				}
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


	$sql = "SELECT A.BOOKING_NO, B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY ,E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID,F.ID AS SALES_ID, F.GREY_QTY
	FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F
	WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND C.IS_SALES = 1 AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ".where_con_using_array($jobDataArr,1,'E.JOB_NO')."
	ORDER BY B.ID, A.BOOKING_NO";
	//echo $sql;

	$sql_result=sql_select($sql);
	$prog_no_arr=array();
	foreach( $sql_result as $row )
	{
		if($prog_id_check[$row['ID']]=='')
		{
			$prog_id_check[$row['ID']]=$row['ID'];
			$prog_no_arr[$row['BOOKING_NO']][$row['JOB_NO']][$row['FABRIC_DESC']][$row['GSM_WEIGHT']][$row['COLOR_ID']]['program_no']=$row['ID'];
			$prog_no_arr[$row['BOOKING_NO']][$row['JOB_NO']][$row['FABRIC_DESC']][$row['GSM_WEIGHT']][$row['COLOR_ID']]['program_qnty'] +=$row['PROGRAM_QNTY'];
			$prog_no_arr[$row['BOOKING_NO']][$row['JOB_NO']][$row['FABRIC_DESC']][$row['GSM_WEIGHT']][$row['COLOR_ID']]['machine_dia'] =$row['MACHINE_DIA'];
		}
	}
	unset($sql_date_result);
	/* echo "<pre>";
	print_r($sales_booking_qty);
	echo "</pre>"; */


	$sales_sql = "SELECT a.id, a.company_id, a.job_no, a.sales_booking_no, a.booking_id, a.style_ref_no, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, b.color_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($jobDataArr,1,'a.job_no')."
	group by a.id, a.company_id, a.job_no, a.sales_booking_no, a.booking_id, a.style_ref_no,  b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, b.color_id order by b.dia";

	//echo $sales_sql;
	$sales_sql_result=sql_select($sales_sql);

	$booking_qnty_arr=array();
	foreach( $sales_sql_result as $row )
	{
		$booking_qnty_arr[$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('style_ref_no')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]]['booking_qnty']+=$row[csf('grey_qty')];

	}
	unset($sales_sql_result);
	/* echo "<pre>";
	print_r($booking_qnty_arr);
	echo "</pre>"; */




	ob_start();
	$table_width = ($cbo_store_wise==1)?"3750":"3660";



	/*echo "here";
	echo "<pre>";
	print_r($prodWiseSalesDataStatus);
	die;*/
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
				<td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:16px">
					<strong>
						<?
						echo $company_arr[str_replace("'","",$cbo_company_id)];
						?>
					</strong>
				</td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
			<thead>
				<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="60" rowspan="2">Company </th>
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
					<th width="130" rowspan="2">FSO</th>
					<th width="100" rowspan="2">Program No</th>

					<th colspan="7">Fabric Details</th>
					<th colspan="12">Receive Details</th>
					<th colspan="4">Issue Details</th>
					<? if($cbo_store_wise==1){?>
						<th colspan="5">Stock Details</th>
					<? } else {?>
						<th colspan="4">Stock Details</th>
					<? } ?>

				</tr>
				<tr>
					<th width="70">Product ID</th>
					<th width="90">Construction</th>
					<th width="200">Composition</th>
					<th width="70">GSM</th>
					<th width="140">Color</th>
					<th width="70">Color Range</th>
					<th width="70">Stitch Length</th>
					<th width="70">M/Dia</th>
					<th width="80">F/Dia</th>
					<th width="80">Program Qty</th>

					<th width="90">Yarn Lot</th>
					<th width="90">Yarn Count</th>
					<th width="100">Yarn Brand</th>

					<th width="90">Opening</th>
					<th width="100">Booking Qty</th>
					<th width="90">Recv. Qty.</th>
					<th width="100">Booking receive<br>  Balance Qty</th>
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
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search2" align="left">
				<?
				$i=1;
				$tot_recv_qty=0;

				foreach($prodWiseSalesDataStatus as $poId=>$prodArr)
				{

					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					foreach ($prodArr as $prodId=>$colorRange)
					{
						foreach ($colorRange as $crange=>$stitchLength)
						{

							$opening=$iss_qty=$trans_out_qty=0;
							foreach ($stitchLength as $slength=>$row)
							{

								$yarn_lot = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_lot"]));
								$yarn_count = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_count"]));
								$yarn_brand = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["brand_id"]));

								$all_prodData = explode("_",chop($row,"_"));
								$recv_qnty=$trans_in_qty=$opening_recv=$opening_trans=0;
								$color_ids="";$color_names="";$color_ids_data="";
								foreach ($all_prodData as $prodData) {
									$data = explode("*",$prodData);
									if($data[5] == 1){
										if($data[6] == 1){
											$recv_qnty += $data[3]*1;
										}
									}

									if($data[5] == 3){
										if($data[6] == 1){
											$trans_in_qty += $data[3]*1;
										}

										$from_order_id = $data[7];

										$yarn_lot = implode(",",array_unique($yarn_info[$from_order_id][$prodId][$crange][$slength]["yarn_lot"]));
										$yarn_count = implode(",",array_unique($yarn_info[$from_order_id][$prodId][$crange][$slength]["yarn_count"]));
										$yarn_brand = implode(",",array_unique($yarn_info[$from_order_id][$prodId][$crange][$slength]["brand_id"]));
									}
									$detarmination_id = $data[0];
									$store_id = $data[4];
									$color_ids .= $data[8]."**";
									$color_names_data .= $color_arr[$data[8]]."**";
								}
								$yarn_lot = implode(",",array_filter(array_unique(explode(",", $yarn_lot))));

								$color_names_arr = array_filter(array_unique(explode("**",rtrim($color_names_data,","))));
								$color_ids_arr = array_filter(array_unique(explode("**",rtrim($color_ids,","))));
								foreach ($color_names_arr as $color) {
									$color_names .= trim($color).", ";
								}
								foreach ($color_ids_arr as $color) {
									$color_ids_data .= trim($color).", ";
								}

								$issue_return_qnty  = $inssue_return_array[$poId][$prodId][$crange][$slength]['issue_return_qty'];
								$iss_qty 			= $knit_issue_arr[$poId][$prodId][$crange][$slength]['issue_qty'];

								$opening_receive  = $receiveOpening[$poId][$prodId][$crange][$slength];
								$opening_trans_in = $transferInOpening[$poId][$prodId][$crange][$slength];

								$opening_title = "Receive=".number_format($opening_receive,2) ."+". number_format($opening_trans_in,2)."\nIssue=".number_format($opening_issue[$poId][$prodId][$crange][$slength]['issue_qty'],2) ."+". number_format($openingTransOutQnty[$poId][$prodId][$crange][$slength],2);

								$opening = ($opening_receive+$opening_trans_in)-($opening_issue[$poId][$prodId][$crange][$slength]['issue_qty']+$openingTransOutQnty[$poId][$prodId][$crange][$slength]);

								// roll wise $recv_ret_qty page did not developed yet
								$recv_tot_qty  = ($recv_qnty+$issue_return_qnty+$trans_in_qty);
								$trans_out_qty = $transOutQnty[$poId][$prodId][$crange][$slength];
								$iss_tot_qty   = ($iss_qty+$trans_out_qty);

								$stock_qty 	   = $opening+($recv_tot_qty-$iss_tot_qty);
								//$stock_qty     = number_format($stock_qty,2,".","");
								if($stock_qty < .001)
								{
									$stock_qty = 0;
								}

								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['max_date'],'','',1),date("Y-m-d"));
								$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['min_date'],'','',1),date("Y-m-d"));

								$product_category 	= $job_info[$salesData[$poId]['job_no']]["product_category"];
								$product_dept 		= $job_info[$salesData[$poId]['job_no']]["product_dept"];
								$season 			= $season_arr[$job_info[$salesData[$poId]['job_no']]["season"]];
								$style_ref_no 		= $job_info[$salesData[$poId]['job_no']]["style_ref_no"];


								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qty == $txt_qnty) || $get_upto_qnty == 0))
								{

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
											<td width="130"title="<? echo $poId;?>"><p class="word_wrap_break"><? echo $salesData[$poId]['fso_no']; ?></p></td>

											<td width="100" ><p class="word_wrap_break"><?
											$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
											echo $prog_no_arr[$salesData[$poId]['booking_no']][$salesData[$poId]['fso_no']][$feb_des][$data[1]][rtrim($color_ids_data,", ")]['program_no'];

											 ?></p></td>

											<td width="70"><p><? echo $prodId;?></p></td>
											<td width="90"><p class="word_wrap_break"><? echo $constructionArr[$detarmination_id]; ?></p></td>
											<td width="200"><p class="word_wrap_break"><? echo $composition_arr[$detarmination_id]; ?></p></td>
											<td width="70" align="center"><p><? echo $data[1]; ?></p></td>
											<td width="140" align="center"><p class="word_wrap_break"><? echo rtrim($color_names,", "); ?></p></td>
											<td width="70"><p class="word_wrap_break" title="<? echo $crange;?>"><? echo $color_range[$crange];?></p></td>
											<td width="70"><p class="word_wrap_break"><? echo $slength;?></p></td>
											<td width="70"><p class="word_wrap_break"><?
											$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
											echo $prog_no_arr[$salesData[$poId]['booking_no']][$salesData[$poId]['fso_no']][$feb_des][$data[1]][rtrim($color_ids_data,", ")]['machine_dia'];
											?></p></td>
											<td width="80" align="center"><p><? echo $data[2]; ?></p></td>
											<td width="80" align="center"><p>
												<?
												$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
												$program_qnty =  $prog_no_arr[$salesData[$poId]['booking_no']][$salesData[$poId]['fso_no']][$feb_des][$data[1]][rtrim($color_ids_data,", ")]['program_qnty'];
												echo number_format($program_qnty,2); $tot_program_qty +=$program_qnty;
												?></p></td>

											<td width="90"><p><? echo $yarn_lot; ?></p></td>
											<td width="90">
												<p><?
												$counts="";
												$yarn_counts = array_unique(explode(",",$yarn_count));
												foreach ($yarn_counts as $yarn_count) {
													$counts .= $yarn_count_arr[$yarn_count].",";
												}
												echo trim($counts,", ");
												?></p>
											</td>
											<td width="100">
												<p><?
												$brands="";
												$yarn_brands = array_unique(explode(",",$yarn_brand));

												foreach ($yarn_brands as $yarn_brand) {
													$brands .= $yarn_brand_arr[$yarn_brand].",";
												}
												echo trim($brands,", ");
												?></p>
											</td>

											<td width="90" align="right" title="<? echo $opening_title;?>"><? echo ($opening==-0)?0:number_format($opening,2); ?></td>
											<td width="100" align="right"><?

											$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
											$booking_qty =  $booking_qnty_arr[$salesData[$poId]['fso_no']][$salesData[$poId]['booking_no']][$salesData[$poId]['style_ref_no']][$feb_des][$data[1]][$data[2]][rtrim($color_ids_data,", ")]['booking_qnty'];
											 echo number_format($booking_qty,2); $tot_booking_qty +=$booking_qty;?></td>
											<td width="90" align="right"><? echo number_format($recv_qnty,2); ?></td>
											<td width="100" align="right"><?
											$brbq = $booking_qty-$recv_qnty;
											echo number_format($brbq,2); $total_brbq +=$brbq; ?></td>
											<td width="90" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength; ?>','trans_in_popup');"><? echo number_format($trans_in_qty,2);?></a></td>
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
									//else if( $cbo_value_with==1)
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
											<td width="60"><p class="word_wrap_break"><? echo $company_arr[$salesData[$poId]['working_company_id']]; ?></p></td>
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
											<td width="130"><p class="word_wrap_break"><? echo $salesData[$poId]['fso_no']; ?> </p></td>
											<td width="100"><p class="word_wrap_break"><?
											$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
											echo $prog_no_arr[$salesData[$poId]['booking_no']][$salesData[$poId]['fso_no']][$feb_des][$data[1]][rtrim($color_ids_data,", ")]['program_no'];
											 ?> </p></td>

											<td width="70"><p><? echo $prodId;?></p></td>
											<td width="90"><p class="word_wrap_break"><? echo $constructionArr[$detarmination_id]; ?></p></td>
											<td width="200"><p class="word_wrap_break"><? echo $composition_arr[$detarmination_id]; ?></p></td>
											<td width="70" align="center"><p><? echo $data[1]; ?></p></td>
											<td width="140" align="center"><p class="word_wrap_break"><? echo rtrim($color_names,", "); ?></p></td>
											<td width="70"><p class="word_wrap_break" title="<? echo $crange;?>"><? echo  $color_range[$crange];?></p></td>
											<td width="70"><p class="word_wrap_break"><? echo $slength;?></p></td>
											<td width="70"><p class="word_wrap_break"><?
											$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
											echo $prog_no_arr[$salesData[$poId]['booking_no']][$salesData[$poId]['fso_no']][$feb_des][$data[1]][rtrim($color_ids_data,", ")]['machine_dia'];
											?></p></td>
											<td width="80" align="center"><p><? echo $data[2]; ?></p></td>
											<td width="80" align="center"><p><?
											$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
											$program_qnty =  $prog_no_arr[$salesData[$poId]['booking_no']][$salesData[$poId]['fso_no']][$feb_des][$data[1]][rtrim($color_ids_data,", ")]['program_qnty'];
											echo number_format($program_qnty,2); $tot_program_qty +=$program_qnty;
											?></p></td>

											<td width="90"><p><? echo $yarn_lot; ?></p></td>
											<td width="90">
												<p><?
												$counts="";
												$yarn_counts = array_unique(explode(",",$yarn_count));
												foreach ($yarn_counts as $yarn_count) {
													$counts .= $yarn_count_arr[$yarn_count].",";
												}
												echo trim($counts,", ");
												?></p>
											</td>
											<td width="100">
												<p><?
												$brands="";
												$yarn_brands = array_unique(explode(",",$yarn_brand));

												foreach ($yarn_brands as $yarn_brand) {
													$brands .= $yarn_brand_arr[$yarn_brand].",";
												}
												echo trim($brands,", ");
												?></p>
											</td>

											<td width="90" align="right" title="<? echo $opening_title;?>"><? echo ($opening==-0)?0:number_format($opening,2); ?></td>
											<td width="100" align="right"><?
											$feb_des = $constructionArr[$detarmination_id].', '.$composition_arr[$detarmination_id];
											$booking_qty =  $booking_qnty_arr[$salesData[$poId]['fso_no']][$salesData[$poId]['booking_no']][$salesData[$poId]['style_ref_no']][$feb_des][$data[1]][$data[2]][rtrim($color_ids_data,", ")]['booking_qnty'];
											 echo number_format($booking_qty,2); $tot_booking_qty +=$booking_qty ?></td>
											<td width="90" align="right"><? echo number_format($recv_qnty,2); ?></td>
											<td width="100" align="right"><?
											$brbq = $booking_qty-$recv_qnty;
											echo number_format($brbq,2); $total_brbq +=$brbq;
											 ?></td>
											<td width="90" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength; ?>','trans_in_popup');"><? echo number_format($trans_in_qty,2);?></a></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>

											<td width="90" align="right"><p><? echo number_format($iss_qty,2); ?></p></td>
											<td width="90" align="right"><p><? echo number_format($recv_ret_qty,2);?></p></td>
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength;; ?>','trans_out_popup');"><? echo number_format($trans_out_qty,2); ?></a></td>
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
					<th width="130"></th>
					<th width="100"></th>

					<th width="70"></th>
					<th width="90"></th>
					<th width="200"></th>
					<th width="70"></th>
					<th width="140"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="80"></th>

					<th width="90"></th>
					<th width="100"></th>

					<th width="90">Grand Total = </th>
					<th width="90" align="right" id="value_html_opening_qnty"><? echo number_format($tot_opening,2); ?></th>
					<th width="100" align="right" id="value_html_booking_qnty"><? echo number_format($tot_booking_qty,2); ?></th>
					<th width="90" align="right" id="value_html_recv_qnty"><? echo number_format($tot_recv_qty,2); ?></th>
					<th width="100" align="right" id="value_html_brbq_qnty"><? echo number_format($total_brbq,2); ?></th>
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
	$stitch_length = $dataArr[5];
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
						where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.to_order_id='$poId' and b.from_prod_id=$prodId and e.stitch_length='$stitch_length' and e.color_range_id=$color_range order by a.id,b.id";// and e.color_range_id=$color_range

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
	$color_range = $dataArr[4];
	$stitch_length = $dataArr[5];
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

						/*$mrr_sql= "select a.transfer_system_id,a.transfer_date, a.from_order_id, a.to_order_id, b.from_program,b.rack,b.shelf,b.roll, c.quantity, d.barcode_no,d.booking_no
						from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c ,pro_roll_details d where a.id=b.mst_id and c.dtls_id=b.id and d.dtls_id = b.id and c.trans_type in(6) and a.item_category=13 and c.entry_form=133 and d.entry_form=133 and a.transfer_criteria=4 and a.from_order_id='$poId' and b.from_prod_id=$prodId and d.po_breakdown_id = a.to_order_id and d.roll_split_from=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
						order by a.id , b.id ";*/

						$mrr_sql= "select a.transfer_system_id,a.transfer_date, a.from_order_id, a.to_order_id, b.from_program,b.rack,b.shelf,b.roll, c.quantity, d.barcode_no,d.booking_no
						from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c ,pro_roll_details d
						left join pro_roll_details e on d.barcode_no=e.barcode_no and e.entry_form=58 and d.status_active=1
						left join pro_grey_prod_entry_dtls f on e.dtls_id=f.id
						where a.id=b.mst_id and c.dtls_id=b.id and d.dtls_id = b.id and c.trans_type in(6) and a.item_category=13 and c.entry_form=133 and d.entry_form=133 and a.transfer_criteria=4 and a.from_order_id='$poId' and b.from_prod_id=$prodId and d.po_breakdown_id = a.to_order_id and d.roll_split_from=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.stitch_length='$stitch_length' and f.color_range_id=$color_range
						order by a.id, b.id ";

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

if($action == "transfer_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$fsoNo=$fsoNo;
	$deterId=$deterId;
	$gsm=$gsm;
	$popup_width=$popup_width;
	$type=$type;
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
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th width="30">SL</th>
					<th width="110">From Sales Order </th>
					<th width="80">Booking No</th>
					<th width="80">Program No</th>
					<th width="80">Barcode No</th>
					<th width="">Transfer In Qty</th>
				</thead>
			</table>
			<div style="width:520px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;
						$trans_in_sql = "SELECT A.FROM_ORDER_ID, A.TO_ORDER_ID,B.TO_PROD_ID, D.QNTY AS TRANSFER_IN_QNTY,F.JOB_NO, D.BARCODE_NO, B.TO_PROGRAM
						FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C, PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F
						WHERE  A.ID=E.MST_ID AND A.ID=D.MST_ID AND E.ID=B.TO_TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.TO_ORDER_ID=F.ID AND F.JOB_NO in('$fsoNo') AND C.DETARMINATION_ID=$deterId AND C.GSM=$gsm AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=5  AND D.ENTRY_FORM=133";
						// echo $trans_in_sql;
						$trans_in_sql_result = sql_select($trans_in_sql);
						$trnsInQtyArr = array();$transfer_in_barcode_qty_arr=array();
						foreach($trans_in_sql_result as $row )
						{
							$transfer_in_barcode_qty_arr[$row['BARCODE_NO']] += $row['TRANSFER_IN_QNTY'];
							$from_order_id_arr[$row['FROM_ORDER_ID']] = $row['FROM_ORDER_ID'];
						}
						$from_order_ids=implode(",", $from_order_id_arr);
						$fso_sql="SELECT ID, JOB_NO, SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE id in($from_order_ids) and status_active=1 and is_deleted=0";
						// echo $fso_sql;
						$fso_sql_result = sql_select($fso_sql);
						foreach ($fso_sql_result as $key => $value)
						{
							$job_arr[$value['ID']]['FROM_JOB']=$value['JOB_NO'];
							$job_arr[$value['ID']]['FROM_BOOKING']=$value['SALES_BOOKING_NO'];
						}

						//unset($trans_in_sql_result);
						foreach($trans_in_sql_result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $job_arr[$row['FROM_ORDER_ID']]['FROM_JOB']; ?></p></td>
								<td width="80"><p><? echo $job_arr[$row['FROM_ORDER_ID']]['FROM_BOOKING']; ?></p></td>
								<td width="80"><p><? echo $row['TO_PROGRAM']; ?></p></td>
								<td width="80"><p><? echo $row['BARCODE_NO']; ?></p></td>
								<td width="" align="right"><? echo number_format($row['TRANSFER_IN_QNTY'],2); ?></td>
							</tr>
							<?
							$tot_trans_qty+=$row['TRANSFER_IN_QNTY'];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80">Total:</th>
					<th width=""><? echo number_format($tot_trans_qty,2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}

if($action == "transfer_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$fsoNo=$fsoNo;
	$deterId=$deterId;
	$gsm=$gsm;
	$popup_width=$popup_width;
	$type=$type;
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
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th width="30">SL</th>
					<th width="110">From Sales Order </th>
					<th width="80">Booking No</th>
					<th width="80">Program No</th>
					<th width="80">Barcode No</th>
					<th width="">Transfer Out Qty</th>
				</thead>
			</table>
			<div style="width:520px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;
						$trans_out_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID, B.FROM_PROGRAM, D.QNTY AS TRANSFER_OUT_QNTY,F.JOB_NO, F.SALES_BOOKING_NO, D.BARCODE_NO
						FROM INV_ITEM_TRANSFER_MST A, INV_TRANSACTION E, INV_ITEM_TRANSFER_DTLS B, PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D, FABRIC_SALES_ORDER_MST F
						WHERE A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.FROM_ORDER_ID=F.ID  AND A.ID=D.MST_ID AND B.ID=D.DTLS_ID AND F.JOB_NO in('$fsoNo') AND C.DETARMINATION_ID=$deterId AND C.GSM=$gsm AND A.STATUS_ACTIVE=1 AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND E.TRANSACTION_TYPE=6 AND B.STATUS_ACTIVE=1 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND D.ENTRY_FORM=133 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0";
						// echo $trans_out_sql;
						$trans_out_sql_result = sql_select($trans_out_sql);
						foreach($trans_out_sql_result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row['JOB_NO']; ?></p></td>
								<td width="80"><p><? echo $row['SALES_BOOKING_NO']; ?></p></td>
								<td width="80"><p><? echo $row['FROM_PROGRAM']; ?></p></td>
								<td width="80"><p><? echo $row['BARCODE_NO']; ?></p></td>
								<td width="" align="right"><? echo number_format($row['TRANSFER_OUT_QNTY'],2); ?></td>
							</tr>
							<?
							$tot_trans_qty+=$row['TRANSFER_OUT_QNTY'];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80">Total:</th>
					<th width=""><? echo number_format($tot_trans_qty,2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}
?>
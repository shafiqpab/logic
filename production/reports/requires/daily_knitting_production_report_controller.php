
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($action=="print_button_variable_setting")
{
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=71 and is_deleted=0 and status_active=1","format_id","format_id");
	echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit();
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/daily_knitting_production_report_controller', document.getElementById('cbo_working_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";

	echo create_drop_down( "cbo_floor_id", 140, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id in($ex_data[0]) and a.production_process=2 and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();
}
/*
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
	exit();
}
*/

if ($action=="load_drop_down_buyer")
{
	$ex_data=explode('**',$data);
	if($ex_data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	}
	else if($ex_data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",1,"" );
	}
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$user_name_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

//--------------------------------------------------------------------------------------------------------------------
if($action=="int_ref_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {

			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	</script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Order No</th>
						<th>Ship/Delv Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								if($ordType==1)
								{
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								}
								else if($ordType==2)
								{
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								}
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $yearID; ?>'+'**'+'<? echo $ordType; ?>', 'create_int_ref_search_list_view', 'search_div', 'daily_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
}

if($action=="create_int_ref_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[7]==1)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$data[1]";
		}

		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1)
			$search_field="b.po_number";
		else if($search_by==2)
			$search_field="a.style_ref_no";
		else
			$search_field="a.job_no";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		$search_year=$data[6];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0)$year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else
			$year_field="";//defined Later


		$arr=array(0=>$company_arr,1=>$buyer_arr);

		$sql= "SELECT b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number,b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id, b.pub_shipment_date";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No,Int Ref, Shipment Date", "80,130,50,60,100,90,60","760","220",0, $sql , "js_set_value", "id,grouping", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,pub_shipment_date", "",'','0,0,0,0,0,0,0,3','',1) ;
	}
	else if ($data[7]==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.party_id=$data[1]";
		}

		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1)
			$search_field="b.order_no";
		else if($search_by==2)
			$search_field="b.cust_style_ref";
		else
			$search_field="a.job_no_prefix_num";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		$search_year=$data[6];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0)$year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else
			$year_field="";//defined Later


		$arr=array(0=>$company_arr,1=>$buyer_arr);

		$sql= "SELECT b.id, $year_field a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, b.cust_style_ref, b.order_no,b.grouping, b.delivery_date from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id DESC";

		echo create_list_view("tbl_list_search", "Company,Party Name,Year,Job No,Cust. Style Ref., Po No,Int Ref, Delivery Date", "80,130,50,60,100,90,60","760","220",0, $sql , "js_set_value", "id,grouping", "", 1, "company_id,party_id,0,0,0,0,0,0", $arr , "company_id,party_id,year,job_no_prefix_num,cust_style_ref,order_no,grouping,delivery_date", "",'','0,0,0,0,0,0,0,3','',1) ;
	}
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {

			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	</script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Order No</th>
						<th>Ship/Delv Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								if($ordType==1)
								{
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								}
								else if($ordType==2)
								{
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								}
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $yearID; ?>'+'**'+'<? echo $ordType; ?>', 'create_order_no_search_list_view', 'search_div', 'daily_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[7]==1)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$data[1]";
		}

		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1)
			$search_field="b.po_number";
		else if($search_by==2)
			$search_field="a.style_ref_no";
		else
			$search_field="a.job_no";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		$search_year=$data[6];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0)$year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else
			$year_field="";//defined Later


		$arr=array(0=>$company_arr,1=>$buyer_arr);

		$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id, b.pub_shipment_date";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	}
	else if ($data[7]==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.party_id=$data[1]";
		}

		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1)
			$search_field="b.order_no";
		else if($search_by==2)
			$search_field="b.cust_style_ref";
		else
			$search_field="a.job_no_prefix_num";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		$search_year=$data[6];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0)$year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else
			$year_field="";//defined Later


		$arr=array(0=>$company_arr,1=>$buyer_arr);

		$sql= "select b.id, $year_field a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, b.cust_style_ref, b.order_no, b.delivery_date from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id DESC";

		echo create_list_view("tbl_list_search", "Company,Party Name,Year,Job No,Cust. Style Ref., Po No, Delivery Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,order_no", "", 1, "company_id,party_id,0,0,0,0,0", $arr , "company_id,party_id,year,job_no_prefix_num,cust_style_ref,order_no,delivery_date", "",'','0,0,0,0,0,0,3','',1) ;
	}
	exit();
}

if($action=="job_no_search_popup")
{
	echo load_html_head_contents("Job No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('hide_job_no').value=id;
			parent.emailwindow.hide();
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
						<th id="search_by_td_up" width="120">Please Enter Order No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								if($ordType==1)
								{
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								}
								else if($ordType==2)
								{
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								}
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No",4=>"Booking No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $yearID; ?>'+'**'+'<? echo $ordType; ?>', 'create_job_no_search_list_view', 'search_div', 'daily_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
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
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[5]==1)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$data[1]";
		}

		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1)
			$search_field="b.po_number";
		else if($search_by==2)
			$search_field="a.style_ref_no";
		else if($search_by==3)
			$search_field="a.job_no";
		else
			$search_field="a.booking_no";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		$search_year=$data[4];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else
			$year_field="";//defined Later


		if($search_by==4)
		{
			$sql_booking="SELECT a.po_break_down_id, a.booking_no from wo_booking_dtls a where a.status_active=1 and a.is_deleted=0  and $search_field like '$search_string' group by a.po_break_down_id, a.booking_no";
			//echo $sql_booking;
			$sql_booking_result=sql_select($sql_booking);
			$po_id_arr = array();
			foreach($sql_booking_result as $row )
			{
				array_push($po_id_arr,$row[csf('po_break_down_id')]);
			}
			unset($sql_booking_result);
			//echo "<pre>";print_r($po_id_arr);
			if(!empty($po_id_arr))
			{
				$po_id_cond="".where_con_using_array($po_id_arr,0,'b.id')."";
			}
		}

		if($search_by==4)
		{
			$sql= "select $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $buyer_id_cond $year_cond $po_id_cond order by a.id DESC";
		}
		else
		{
			$sql= "select $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by a.id DESC";
		}

		$arr=array(0=>$company_arr,1=>$buyer_arr);

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,130,50,60","560","280",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0,", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	else if($data[5]==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.party_id=$data[1]";
		}

		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";

		if($search_by==1)
			$search_field="b.order_no";
		else if($search_by==2)
			$search_field="b.cust_style_ref";
		else
			$search_field="a.job_no_prefix_num";

		$start_date =trim($data[4]);
		$end_date =trim($data[5]);

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		$search_year=$data[4];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date)";
			$style_cond="group_concat(b.cust_style_ref)";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY')";
			$style_cond="listagg((cast(b.cust_style_ref as varchar2(4000))),',') within group (order by b.cust_style_ref)";
			if($search_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else
			$year_field="";//defined Later

		$arr=array(0=>$company_arr,1=>$buyer_arr);

		$sql= "select $year_field as year, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, $style_cond as cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, a.insert_date order by a.id DESC";

		echo create_list_view("tbl_list_search", "Company,Party Name,Year,Job No,Cust. Style Ref.", "120,130,50,60","560","280",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_id,party_id,0,0,0,", $arr , "company_id,party_id,year,job_no_prefix_num,cust_style_ref", "",'','0,0,0,0,0','') ;
	}
	exit();
}

$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
$machine_lib=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
$floor_details=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
$reqsn_details=return_library_array( "select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id,requisition_no", "knit_id", "requisition_no"  );
$color_details=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$int_ref=str_replace("'","",$txt_int_ref);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_booking_type=str_replace("'","",$cbo_booking_type);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$report_type=str_replace("'","",$report_type);
	$machine_wise_section=str_replace("'","",$machine_wise_section);
	$knitting_source=str_replace("'","",$cbo_knitting_source);//and a.knitting_source
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);

	$location_cond='';$location_cond_subcontract='';
	if(!empty($cbo_location_id))
	{
		$location_cond=" and a.knitting_location_id=$cbo_location_id ";
		$location_cond_subcontract=" and a.knit_location_id=$cbo_location_id ";
	}

	if($cbo_company==0)
		$cbo_company_cond="";
	else
		$cbo_company_cond=" and a.company_id in($cbo_company)";

	if($knitting_source==0)
		$knit_source_cond="";
	else
		$knit_source_cond=" and a.knitting_source=$knitting_source";

	if($cbo_working_company==0)
	{
		$company_working_cond="";
		$company_working_cond2="";
	}
	else
	{
		$company_working_cond=" and a.knitting_company=$cbo_working_company";
		$company_working_cond2=" and company_id=$cbo_working_company";
	}

	if($cbo_company==0)
		$sub_company_cond="";
	else
		$sub_company_cond=" and a.company_id in($cbo_company)";

	if($cbo_working_company==0)
		$subcompany_working_cond="";
	else
		$subcompany_working_cond=" and a.company_id=$cbo_working_company";

	if($cbo_company==0)
		$conversion_company_cond="";
	else
		$conversion_company_cond=" and company_id=$cbo_company";

	if($cbo_working_company==0)
		$conversion_company_cond2="";
	else
		$conversion_company_cond2=" and company_id=$cbo_working_company";

	if($report_type==1) // Prod. Wise
	{
		$tbl_width=2480+count($shift_name)*205;

		if($cbo_type==1 || $cbo_type==0)
		{
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_cond=" and f.job_no_prefix_num='$txt_job' "; else $job_cond="";
			if($txt_order!="") $order_cond=" and e.po_number like '%$txt_order%' "; else $order_cond="";
			if($int_ref!="") $int_ref_cond=" and e.grouping like '%$int_ref%' "; else $int_ref_cond="";
			if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";

			if($db_type==0)
			{
				$year_field="YEAR(f.insert_date)";
				$year_field_sam="YEAR(a.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_field="to_char(f.insert_date,'YYYY')";
				$year_field_sam="to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
			}
			else $year_field="";

			$from_date=$txt_date_from;
			if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);

			$date_con="";
			if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";

			$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			}

			if($db_type==2) $year_date=" to_char(a.insert_date,'YYYY') as year";
			else if ($db_type==0) $year_date=" year(a.insert_date) as year";
			$job_data=sql_select("select c.job_no as sales_order,a.job_no, b.booking_no, $year_date,a.style_ref_no,a.insert_date from wo_po_details_master a,wo_booking_dtls b,fabric_sales_order_mst c where a.job_no=b.job_no and c.sales_booking_no=b.booking_no and a.company_name in($cbo_company,$cbo_working_company_id)");
			foreach($job_data as $row)
			{
				$sales_booking_array[$row[csf('sales_order')]]['job_no']=$row[csf('job_no')];
				$sales_booking_array[$row[csf('sales_order')]]['style_ref_no']=$row[csf('style_ref_no')];
				$sales_booking_array[$row[csf('sales_order')]]['year']=$row[csf('year')];
			}

			$po_sub_array=array();	$po_array=array();
			$po_data=sql_select("select a.job_no, a.job_no_prefix_num, $year_field_sam as year, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name in($cbo_company,$cbo_working_company_id)");
			foreach($po_data as $row)
			{
				$po_sub_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
				$po_sub_array[$row[csf('id')]]['year']=$row[csf('year')];
				$po_sub_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$po_sub_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']=$row[csf('style_ref_no')];
			}

			unset($po_data);
			//var_dump($po_sub_array);
			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}
			unset($data_array);
			$knit_plan_arr=array();
			$plan_data=sql_select("select id, color_range, stitch_length from ppl_planning_info_entry_dtls");
			foreach($plan_data as $row)
			{
				$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')];
			}
			unset($plan_data);

		}
		if($cbo_type==2 || $cbo_type==0)
		{
			$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width, gauge,machine_group from lib_machine_name");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				$machine_details[$row[csf('id')]]['machine_group']=$row[csf('machine_group')];
			}
			unset($machine_data);
			if($db_type==0)
			{
				$year_sub_field="YEAR(e.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(e.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_sub_field="to_char(e.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_sub_cond=" and to_char(e.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";
			}
			else $year_sub_field="";

			if($db_type==0)
			{
				$select_color=", b.color_id as color_id";
				$group_color=", b.color_id";
			}
			else if($db_type==2)
			{
				$select_color=", nvl(b.color_id,0) as color_id";
				$group_color=", nvl(b.color_id,0)";
			}

			$from_date=$txt_date_from;
			if($txt_date_to=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";

			if ($cbo_floor_id!=0) $floor_id_cond=" and b.floor_id='$cbo_floor_id'"; else $floor_id_cond="";
			if (str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and a.party_id=$cbo_buyer_name"; else $buyer_id_cond="";

			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_no_cond=" and e.job_no_prefix_num='$txt_job' "; else $job_no_cond="";
			if($txt_order!="") $order_no_cond=" and d.order_no like '%$txt_order%' "; else $order_no_cond="";
		}

		ob_start();
		if($cbo_company==0)
			$roll_level_company_cond="";
		else
			$roll_level_company_cond=" company_name in($cbo_company)";

		if($cbo_working_company==0)
			$roll_level_working_company_cond="";
		else
			$roll_level_working_company_cond=" company_name in($cbo_working_company)";

		$fabricData = sql_select("select fabric_roll_level from variable_settings_production where $roll_level_company_cond $roll_level_working_company_cond and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");

		$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate a","a.is_deleted=0 and a.status_active=1  and a.id=(select max(a.id) from currency_conversion_rate a where a.currency=2 and a.is_deleted=0 and a.status_active=1 $conversion_company_cond $conversion_company_cond2 )","",$con);
		// echo $conversion_rate.'=Test';
		//$conversion_rate_arr=return_library_array( "select company_id, conversion_rate from currency_conversion_rate order by id desc", "company_id", "conversion_rate"  );

		foreach ($fabricData as $row)
		{
			$roll_maintained_yesNo = $row[csf('fabric_roll_level')];
		}
		//	echo $roll_maintained_yesNo;die;

		// $cbo_booking_type=118;
		if($cbo_booking_type > 0)
		{
			if($cbo_booking_type == 89){ // SM
				$entry_form_cond = " and k.booking_type = 4";// AND IS_SHORT=2
			}
			/*elseif($cbo_booking_type == 90) // SMN
			{
				$entry_form_cond = " and k.booking_type=4 AND IS_SHORT is null";
			}*/
			else
			{
				$entry_form_cond = " and k.entry_form=$cbo_booking_type";
			}
		}
		else
		{
			$entry_form_cond = "";
		}
		// ================== Summary Start =============
		if ($cbo_type==0)
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850px">
				<tr>
					<!-- Self Order (In-House + Outbound) Knitting Production Summary Start -->
					<td width="555">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production </i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="7">Knit Production Summary (In-House + Outbound)</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="100">Buyer</th>
									<th width="90">Inhouse</th>
									<th width="90">Outbound-Subcon</th>
									<th width="100">Total</th>
									<th width="90">Sample With Order</th>
									<th width="90">Sample Without Order</th>
								</tr>
							</thead>
						</table>
						<div style="width:660px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table" >
								<tbody>
									<?
									$html .= '<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850px">'
									. '<tr>'
									. '<td width="555">'
									. '<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, \'Times New Roman\', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>'
									. '<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >'
									. '<thead>'
									. '<tr><th colspan="6">Knit Production Summary (In-House + Outbound)</th></tr>'
									. '<tr><th width="40">SL</th><th width="100">Buyer</th><th width="90">Inhouse</th><th width="90">Outbound-Subcon</th><th width="100">Total</th><th width="90">Sample Without Order</th></tr>'
									. '</thead></table>'
									. '<div style="width:570px; overflow-y:scroll; max-height:220px;" id="scroll_body">'
									. '<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >'
									. '<tbody>';
									//==============================================and b.machine_no_id>0
									// sample without order
									if($cbo_booking_type == 0 || $cbo_booking_type == 90)
									{
										if ($roll_maintained_yesNo==1)
										{
											$sql_sample_sam="SELECT a.buyer_id, a.booking_no, a.knitting_source, sum(g.qnty ) as sample_qty, g.is_sales
											from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details g
											where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id=g.mst_id and b.id=G.DTLS_ID and a.receive_basis!=4 and a.buyer_id>0 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.booking_without_order in(1) $date_con $floor_id $buyer_cond $location_cond
											group by a.buyer_id,a.booking_no, a.knitting_source,g.is_sales";
										} // and g.is_sales=2
										else
										{
											$sql_sample_sam="SELECT a.buyer_id, a.booking_no, a.knitting_source, sum(case when a.booking_without_order=1 $floor_id  then b.grey_receive_qnty end ) as sample_qty
											from inv_receive_master a, pro_grey_prod_entry_dtls b
											where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4 and a.buyer_id>0 $cbo_company_cond $company_working_cond and a.knitting_source like '$source'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(1) $date_con $floor_id $buyer_cond $location_cond
											group by a.buyer_id,a.booking_no, a.knitting_source ";
										}
									}
									//echo $sql_sample_sam;
									$sql_sample_samary=sql_select( $sql_sample_sam);
									$subcon_buyer_samary=array();
									foreach($sql_sample_samary as $inf)
									{
										$booking_no=explode("-",$inf[csf('booking_no')]);
										$without_booking_no=$booking_no[1];

										if($inf[csf('knitting_source')] == 1)
										{
											$knit_buyer_samary[$inf[csf('buyer_id')]]['in_qty']+= $inf[csf('sample_qty')];
											$total_sample_inhouse += $inf[csf('sample_qty')];
										}else{
											$knit_buyer_samary[$inf[csf('buyer_id')]]['out_qty']+= $inf[csf('sample_qty')];
											$total_sample_outbound += $inf[csf('sample_qty')];
										}
										if($without_booking_no=='SMN')
										{
											$knit_buyer_samary[$inf[csf('buyer_id')]]['with_out_qty']+= $inf[csf('sample_qty')];
										}
										else if($without_booking_no=='SM')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty']+= $row[csf('sample_qty')];
										}
										if ($inf[csf('is_sales')]==2)
										{
											$knit_buyer_samary[$inf[csf('buyer_id')]]['with_out_qty']+= $inf[csf('sample_qty')];
											//$total_sample_inhouse += $inf[csf('sample_qty')];
										}
									}
									unset($sql_sample_samary);

									/*echo "<pre>";
									print_r($knit_buyer_samary);
									die;*/

									$sql_service_samary=sql_select("SELECT a.buyer_id, sum(b.grey_receive_qnty) as service_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id $cbo_company_cond $company_working_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_con $buyer_cond $location_cond group by a.buyer_id");
									$service_buyer_data=array();
									foreach($sql_service_samary as $row)
									{
										$knit_buyer_samary[$row[csf('buyer_id')]]['out_qty']+= $row[csf('service_qty')];
										$service_buyer_data[$row[csf("buyer_id")]]=$row[csf("service_qty")];
										$total_receive_outbound += $row[csf("service_qty")];
									}

									unset($sql_service_samary);

									/*$fabricData = sql_select("select fabric_roll_level from variable_settings_production where company_name ='$cbo_company' and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");
									foreach ($fabricData as $row)
									{
										$roll_maintained_yesNo = $row[csf('fabric_roll_level')];
									}*/

									//$sql_qty="select  a.booking_no, a.buyer_id, ";

									// unionn all for Booking Type condition as per discuss with tofael vai
									if($roll_maintained_yesNo==1) // Yes
									{
										$sql_qty="SELECT a.booking_no, a.buyer_id,  sum(case when a.knitting_source=1 and b.machine_no_id>0 then g.qnty else 0 end ) as qtyinhouse, sum(case when a.knitting_source=3 then g.qnty else 0 end ) as qtyoutbound
										from wo_booking_mst k, ppl_planning_info_entry_mst h, ppl_planning_info_entry_dtls i, inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
										where k.booking_no=h.booking_no and h.id=i.mst_id and i.id=a.booking_id and k.booking_type in(1,4) and a.id=b.mst_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and b.id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis=2 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $location_cond $buyer_cond $job_cond $order_cond $job_year_cond $entry_form_cond
										group by a.booking_no, a.buyer_id
										union all
										SELECT a.booking_no, a.buyer_id,  sum(case when a.knitting_source=1 and b.machine_no_id>0 then g.qnty else 0 end ) as qtyinhouse, sum(case when a.knitting_source=3 then g.qnty else 0 end ) as qtyoutbound
										from WO_BOOKING_MST k, inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
										where k.id=a.booking_id and a.id=b.mst_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and b.id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $location_cond $buyer_cond $job_cond $order_cond $job_year_cond $entry_form_cond
										group by a.booking_no, a.buyer_id";
									}
									else // 2 No
									{
										$sql_qty="SELECT a.booking_no, a.buyer_id, sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity else 0 end ) as qtyinhouse, sum(case when a.knitting_source=3 then c.quantity else 0 end ) as qtyoutbound
										from wo_booking_mst k, ppl_planning_info_entry_mst h, ppl_planning_info_entry_dtls i, inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f
										where k.booking_no=h.booking_no and h.id=i.mst_id and i.id=a.booking_id and k.booking_type in(1,4) and a.id=b.mst_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis=2 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond $entry_form_cond
										group by a.booking_no, a.buyer_id
										union all
										SELECT a.booking_no, a.buyer_id, sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity else 0 end ) as qtyinhouse, sum(case when a.knitting_source=3 then c.quantity else 0 end ) as qtyoutbound
										from WO_BOOKING_MST k, inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f
										where k.id=a.booking_id and a.id=b.mst_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond $entry_form_cond
										group by a.booking_no, a.buyer_id";
									}
									//$sql_qty.=" group by a.booking_no, a.buyer_id";
									// echo $sql_qty;
									//-----------------------------------------------------------------------------------------------------
									// echo $roll_maintained_yesNo;die;
									//echo $sql_qty;die;
									/*echo "<pre>";
									print_r($knit_buyer_samary);
									die;*/


									$k=1;
									$sql_result=sql_select( $sql_qty);
									foreach($sql_result as $row)
									{
										$booking_no=explode("-",$row[csf('booking_no')]);
										$without_booking_no=$booking_no[1];
										echo $without_booking_no;
										$knit_buyer_samary[$row[csf('buyer_id')]]['in_qty']+= $row[csf('qtyinhouse')];
										$knit_buyer_samary[$row[csf('buyer_id')]]['out_qty']+= $row[csf('qtyoutbound')];

										$total_order_inhouse += $row[csf('qtyinhouse')];
										$total_order_outbound += $row[csf('qtyoutbound')];

										if($without_booking_no=='SMN')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_out_qty']+=$row[csf('qtyinhouse')]+$row[csf('qtyoutbound')];
										}
										else if($without_booking_no=='SM')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty']+=$row[csf('qtyinhouse')]+$row[csf('qtyoutbound')];
										}
									}
									/*echo $without_booking_no.'<br>';
									echo "<pre>";
									print_r($knit_buyer_samary);*/
									//die;
									$tot_without_ord_qty=$tot_with_ord_qty=0;

									foreach($knit_buyer_samary as $buyer_id=>$rows)
									{
										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$out_bound_qnty=0;
										$out_bound_qnty=$rows[('out_qty')];
										$with_out_qty=$rows[('with_out_qty')]+$subcon_smn_buyer_samary[$buyer_id];
										$with_qty=$rows['with_qty'];
										$tot_row_summ=$rows[('in_qty')]+$out_bound_qnty;
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="100" title="<? echo $buyer_id; ?>"><? echo $buyer_arr[$buyer_id]; ?></td>
											<td width="90" align="right"><? echo number_format($rows[('in_qty')],2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right" title="With Fab. Service Recv+SMN Outbound"><? echo number_format($out_bound_qnty,2,'.',''); ?>&nbsp;</td>
											<td width="100" align="right"><? echo  number_format($tot_row_summ,2,'.',''); ?>&nbsp;</td>

											<td width="90" align="right"><? echo number_format($with_qty,2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right" title="Qnty with SMN Outbound <? echo $rows[('with_out_qty')]; ?>"><? echo number_format($with_out_qty,2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$html .= '<tr bgcolor="'. $bgcolor .'"><td width="40">'. $k .'</td><td width="100">'. $buyer_arr[$buyer_id].'</td>
										<td width="90" align="right">'. number_format($rows[("in_qty")],2,".","").'&nbsp;</td>
										<td width="90" align="right">'. number_format($out_bound_qnty,2,".","") .'&nbsp;</td>
										<td width="100" align="right">'.  number_format($tot_row_summ,2,".","").'&nbsp;</td>
										<td width="90" align="right">'. number_format($with_out_qty,2,".","").'&nbsp;</td>
										</tr>';

										$tot_qtyinhouse+=$rows[('in_qty')];
										$tot_qtyoutbound+=$out_bound_qnty;
										$tot_without_ord_qty+=$with_out_qty;
										$tot_with_ord_qty+=$with_qty;
										$total_summ+=$tot_row_summ;

										$k++;
									}
									//die;
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_with_ord_qty,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_without_ord_qty,2,'.',''); ?>&nbsp;</th>

									</tr>
									<tr>
										<th colspan="2"><strong>In %</strong></th>
										<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
										<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
										<th align="right"><? echo "100 %"; ?></th>
										<th align="right"><? //$qtyoutbound_per=($tot_with_ord_qty/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
										<th align="right"><? //$qtyoutbound_per=($tot_without_ord_qty/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>

									</tr>
								</tfoot>
							</table>
						</div>
					</td>
					<!-- Self Order (In-House + Outbound) Knitting Production Summary End -->

					<!-- SubCon Order (Inbound) Knitting Production Summary Start -->
					<td width="50">&nbsp;</td>
					<td valign="top">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>SubCon Order (Inbound) Knitting Production</i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="6">Knit Production Summary (Inbound)</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="120">Party </th>
									<th width="100">Total Inbound Production</th>
								</tr>
							</thead>
						</table>
						<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
								<tbody>
									<?
									$html .= '</tbody>
									<tfoot>
									<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right">'. number_format($tot_qtyinhouse,2,".","").'&nbsp;</th>
									<th align="right">'. number_format($tot_qtyoutbound,2,".","").'&nbsp;</th>
									<th align="right">'. number_format($tot_without_ord_qty,2,".","").'&nbsp;</th>
									<th align="right">'. number_format($total_summ,2,".","").'&nbsp;</th>
									</tr>
									<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right">'. number_format((($tot_qtyinhouse/$total_summ)*100),2).' % &nbsp;</th>
									<th align="right">'. number_format((($tot_qtyoutbound/$total_summ)*100),2).' % &nbsp;</th>
									<th align="right">'. number_format((($tot_without_ord_qty/$total_summ)*100),2).' % &nbsp;</th>
									<th align="right"> 100 %</th>
									</tr>
									</tfoot>
									</table>
									</div></td>
									<td width="50">&nbsp;</td>
									<td valign="top">
									<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, \'Times New Roman\', Times, serif;"><strong><u><i>SubCon Order (Inbound) Knitting Production</i></u></strong></div>
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<thead>
									<tr>
									<th colspan="6">Knit Production Summary (Inbound)</th>
									</tr>
									<tr>
									<th width="40">SL</th>
									<th width="120">Party </th>
									<th width="100">Total Inbound Production</th>
									</tr>
									</thead>
									</table>
									<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<tbody>';

									$sql_inhouse_sub_summ="SELECT a.party_id, sum(b.product_qnty) as qntysubshift
									from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
									where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2
									and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.order_id = d.id $sub_company_cond $company_working_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $location_cond_subcontract $order_no_cond $job_year_sub_cond
									group by a.party_id";
									//echo $sql_inhouse_sub_summ;die;//$subcompany_working_cond
									$nameArray_inhouse_subcon_summ=sql_select( $sql_inhouse_sub_summ);

									$k=1;
									foreach($nameArray_inhouse_subcon_summ as $rows)
									{
										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="120"><? echo $buyer_arr[$rows[csf('party_id')]]; ?></td>
											<td width="100" align="right"><? echo  number_format($rows[csf('qntysubshift')],2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$html .= '<tr bgcolor="'. $bgcolor.'">
										<td width="40">'. $k.'</td>
										<td width="120">'. $buyer_arr[$rows[csf("party_id")]].'</td>
										<td width="100" align="right">'.  number_format($rows[csf("qntysubshift")],2,".","").'&nbsp;</td>
										</tr>';
										$tot_qty_sub_summ+=$rows[csf('qntysubshift')];
										unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);
										$k++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($tot_qty_sub_summ,2,'.',''); ?>&nbsp;</th>
									</tr>
									<tr>
										<th colspan="2"><strong>In %</strong></th>
										<th align="right"><? echo "100 %"; ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</td>
					<!-- SubCon Order (Inbound) Knitting Production Summary End -->

					<!-- Fabric Sales Order Knitting Production Summary Start -->
					<td width="50">&nbsp;</td>
					<td valign="top">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Fabric Sales Order Knitting Production</i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="6">Knit Production Summary (Sales Order)</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="120">Buyer </th>
									<th width="100">Total Inbound Production</th>
								</tr>
							</thead>
						</table>
						<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
								<tbody>
									<?
									$html .= '</tbody>
									<tfoot>
									<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right">'. number_format($tot_qty_sub_summ,2,".","").'&nbsp;</th>

									</tr>
									<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right">100 % &nbsp;</th>

									</tr>
									</tfoot>
									</table>
									</div></td>
									<td width="50">&nbsp;</td>
									<td valign="top">
									<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, \'Times New Roman\', Times, serif;"><strong><u><i>Fabric Sales Order Knitting Production</i></u></strong></div>
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<thead>
									<tr>
									<th colspan="6">Knit Production Summary (Sales Order)</th>
									</tr>
									<tr>
									<th width="40">SL</th>
									<th width="120">Buyer </th>
									<th width="100">Total Inbound Production</th>
									</tr>
									</thead>
									</table>
									<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<tbody>';

									$sql_sales_prod="select c.buyer_id,
									sum(case when b.machine_no_id>0 $floor_id  then b.grey_receive_qnty end ) as knit_sales_in
									from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details d, fabric_sales_order_mst c
									where a.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id=c.id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and d.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $location_cond
									group by c.buyer_id ";

									//echo $sql_sales_prod;die;

									$result_sales_prod=sql_select( $sql_sales_prod);
									foreach($result_sales_prod as $row)
									{
										$knit_sales_buyer_sammary[$row[csf('buyer_id')]]['knit_sales_in']+= $row[csf('knit_sales_in')];
									}
									unset($result_sales_prod);

									$tot_qty_sales_summ=0;
									$k=1;
									foreach($knit_sales_buyer_sammary as $buyer_id=>$rows)
									{
										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="120"><? echo $buyer_arr[$buyer_id]; ?></td>
											<td width="100" align="right"><? echo  number_format($rows[('knit_sales_in')],2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$html .= '<tr bgcolor="'. $bgcolor.'">
										<td width="40">'. $k.'</td>
										<td width="120">'. $buyer_arr[$buyer_id].'</td>
										<td width="100" align="right">'.  number_format($rows[("knit_sales_in")],2,".","").'&nbsp;</td>
										</tr>';
										$tot_qty_sales_summ+=$rows[('knit_sales_in')];
										$k++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($tot_qty_sales_summ,2,'.',''); ?>&nbsp;</th>
									</tr>
									<tr>
										<th colspan="2"><strong>In %</strong></th>
										<th align="right"><? echo "100 %"; ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</td>
					<!-- Fabric Sales Order Knitting Production Summary End -->

					<!-- Total Knitting Production Summary Start -->
					<td width="50">&nbsp;</td>
					<td valign="top">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:380px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Total Knitting Production Summary</i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="380px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="6">Total Knitting Production Summary</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="120">Production Type </th>
									<th width="120">Total  Production </th>
									<th width="100">% Of Total</th>
								</tr>
							</thead>
						</table>
						<div style="width:400px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="380px" class="rpt_table" >
								<tbody>
									<?
									$html .= '</tbody>
									<tfoot>
									<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right">'. number_format($tot_qty_sales_summ,2,".","").'&nbsp;</th>
									</tr>
									<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right">100 % &nbsp;</th>

									</tr>
									</tfoot>
									</table>
									</div></td>
									<td width="50">&nbsp;</td>
									';

									$total_summary_prod_qty=0;
									$k=1;
									$total_production_sammary=array(1=>'In-House', 2=>'Outbound-Subcontract Production', 3=>'Outbound-Subcontract Receive', 4=>'Sample Without Order (in house)', 5=>'Sample Without Order (Outbound)', 6=>'Fabric Sales Order Knitting Production', 7=>'Subcontract Order (In-bound) Knitting Production');

									$total_prod_sammaryQty=$total_order_inhouse+$total_order_outbound+$total_receive_outbound+$total_sample_inhouse+$total_sample_outbound+$tot_qty_sales_summ+$tot_qty_sub_summ;
									foreach($total_production_sammary as $type_id=>$val)
									{

										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										if($type_id==1) //Inhouse
										{
											$tot_production_qty=$total_order_inhouse;
										}
										else  if($type_id==2) //order OutBound
										{
											$tot_production_qty=$total_order_outbound;
										}
										else  if($type_id==3) //receive outbound
										{
											$tot_production_qty=$total_receive_outbound ;
										}
										else  if($type_id==4) //Sample inhouse
										{
											$tot_production_qty=$total_sample_inhouse;
										}
										else  if($type_id==5) //Sample Outbound
										{
											$tot_production_qty=$total_sample_outbound;
										}
										else  if($type_id==6) //Sales Order
										{
											$tot_production_qty=$tot_qty_sales_summ;
										}
										else  if($type_id==7) //inbound Subcontact
										{
											$tot_production_qty=$tot_qty_sub_summ;
										}
										$total_prod_per=number_format($tot_production_qty/$total_prod_sammaryQty,6,'.','');
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="120"><? echo $val; ?></td>
											<td width="120"  align="right"><? echo number_format($tot_production_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? echo  number_format(($total_prod_per*100),4,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$total_summary_prod_qty+=$tot_production_qty;
										$k++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($total_summary_prod_qty,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format(($total_summary_prod_qty/$total_summary_prod_qty)*100,4,'.',''); ?>&nbsp;</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</td>
					<!-- Total Knitting Production Summary End -->
				</tr>
			</table>
			<br />
			<?
		}
		//echo $template."Details";die;
		// ================== Summary End =============
		?>
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="36" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="36" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="36" class="form_caption" style="font-size:12px" ><strong><? echo "From ".str_replace("'","",$txt_date_from)." To ".str_replace("'","",$txt_date_to); ?></strong></td>
			</tr>
		</table>
		<?
		if($cbo_type==1 || $cbo_type==0) // Self Order
		{
			if($template==1)
			{
				?>
				<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong>
				</div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+440; ?>" class="rpt_table" id="table_head" >
					<thead>
						<tr>
							<th width="40" rowspan="2" id="chk_hide"></th>
							<th width="30" rowspan="2">SL</th>
							<th width="55" rowspan="2">Knitting Party</th>
							<th width="60" rowspan="2">Receive Challan No </th>
							<th width="60" rowspan="2">M/C No</th>
							<th width="60" rowspan="2">Job No</th>
							<th width="70" rowspan="2">File No.</th>
							<th width="70" rowspan="2">Int. Reff. No.</th>
							<th width="60" rowspan="2">Year</th>
							<th width="70" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Style</th>
							<th width="110" rowspan="2">Order No</th>
							<th width="90" rowspan="2">Prod. Basis</th>
							<th width="110" rowspan="2">Prog. No/ Booking No</th>
							<th width="60" rowspan="2">Prod. No</th>
							<th width="80" rowspan="2">Req. No.</th>
							<th width="80" rowspan="2">Yarn Count</th>
							<th width="90" rowspan="2">Yarn Brand</th>
							<th width="60" rowspan="2">Lot No</th>
							<th width="100" rowspan="2">Color Range</th>
							<th width="100" rowspan="2">Fabric Color</th>
							<th width="150" rowspan="2">Fabric Type</th>
							<th width="50" rowspan="2">M/C Dia</th>
							<th width="80" rowspan="2">M/C Gauge</th>
							<th width="50" rowspan="2">Fab. Dia</th>
							<th width="50" rowspan="2">Stitch</th>
							<th width="60" rowspan="2">Fin GSM</th>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="150" colspan="3"><? echo $val; ?></th>
								<?
							}
							?>
							<th width="150" colspan="3">No Shift</th>
							<th width="150" colspan="3">Total</th>
							<th rowspan="2" width="100">Avg. Rate (Tk)</th>
							<th rowspan="2" width="100">Amount (TK)</th>
							<th rowspan="2" width="100">Insert User</th>
							<th rowspan="2" width="100">Insert Date and Time</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="50" rowspan="2">Roll</th>
								<th width="50" rowspan="2">Pcs</th>
								<th width="100" rowspan="2">Qnty</th>
								<?
							}
							?>
							<th width="50" rowspan="2">Roll</th>
							<th width="50" rowspan="2">Pcs</th>
							<th width="100" rowspan="2">Qnty</th>
							<th width="50" rowspan="2">Roll</th>
							<th width="50" rowspan="2">Pcs</th>
							<th width="100" rowspan="2">Qnty</th>
						</tr>
					</thead>
				</table>
				<?
				$widths=$tbl_width+20;
				$html.="

				<fieldset style='width:".$widths."px;'>
				<table cellpadding='0' cellspacing='0' width='".$tbl_width."'>
				<tr>
				<td align='center' width='100%' colspan='36' class='form_caption' style='font-size:18px'>".$report_title."</td>
				</tr>
				<tr>
				<td align='center' width='100%' colspan='36' class='form_caption' style='font-size:16px'>".$company_arr[str_replace("'","",$cbo_company)]."</td>
				</tr>
				<tr>
				<td align='center' width='100%' colspan='36' class='form_caption' style='font-size:12px' ><strong>"."From ".str_replace("'","",$txt_date_from)." To ".str_replace("'","",$txt_date_to)."</strong></td>
				</tr>
				</table>
				<div align='left' style='background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;'><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>

				<table border='1'>
				<tr>
				<th width='30' rowspan='2'>SL</th>
				<th width='55' rowspan='2'>Knitting Party</th>
				<th width='60' rowspan='2'>M/C No</th>
				<th width='60' rowspan='2'>Job No</th>
				<th width='70' rowspan='2'>File No.</th>
				<th width='70' rowspan='2'>Int. Reff. No.</th>
				<th width='60' rowspan='2'>Year</th>
				<th width='70' rowspan='2'>Buyer</th>
				<th width='100' rowspan='2'>Style</th>
				<th width='110' rowspan='2'>Order No</th>
				<th width='90' rowspan='2'>Prod. Basis</th>
				<th width='110' rowspan='2'>Prog. No/ Booking No</th>
				<th width='60' rowspan='2'>Prod. No</th>
				<th width='80' rowspan='2'>Req. No.</th>
				<th width='80' rowspan='2'>Yarn Count</th>
				<th width='90' rowspan='2'>Yarn Brand</th>
				<th width='60' rowspan='2'>Lot No</th>
				<th width='100' rowspan='2'>Color Range</th>
				<th width='100' rowspan='2'>Fabric Color</th>
				<th width='150' rowspan='2'>Fabric Type</th>
				<th width='50' rowspan='2'>M/C Dia</th>
				<th width='80' rowspan='2'>M/C Gauge</th>
				<th width='50' rowspan='2'>Fab. Dia</th>
				<th width='50' rowspan='2'>Stitch</th>
				<th width='60' rowspan='2'>Fin GSM</th>";

				foreach($shift_name as $val)
				{
					$html.="<th width='150' colspan='3'>".$val."</th>";
				}
				$html.="
				<th width='150' colspan='2'>No Shift</th>
				<th width='150' colspan='2'>Total</th>
				<th rowspan='2'>Remarks</th>
				</tr>
				<tr>";
				foreach($shift_name as $val)
				{
					$html.="<th width='50'>Roll</th>
					<th width='50'>Pcs</th>
					<th width='100'>Qnty</th>";
				}
				$html.="
				<th width='50'>Roll</th>
				<th width='50'>Pcs</th>
				<th width='100'>Qnty</th>
				<th width='50'>Roll</th>
				<th width='50'>Pcs</th>
				<th width='100'>Qnty</th>
				</tr>";
				?>
				<div style="width:<? echo $tbl_width+460; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+440; ?>" class="rpt_table" id="table_body">
						<?
						$plan_booking_arr=return_library_array( "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id", "booking_no");
						$i=1; $tot_rolla=''; $tot_rollb=''; $tot_rollc=''; $tot_rolla_qnty=0; $tot_rollb_qnty=0; $tot_rollc_qnty=0; $grand_tot_roll=''; $grand_tot_qnty=0;$tot_subcontract=0;$outbound_amount=0;
						$inside_outside_array=array(); $floor_array=array(); $receive_basis=array(0=>"Independent",1=>"Fabric Booking No",2=>"Knitting Plan");

						if($db_type==0)
						{
							$select_color=", b.color_id as COLOR_ID";
							$group_color=", b.color_id";
						}
						else if($db_type==2)
						{
							$select_color=", nvl(b.color_id,0) as COLOR_ID";
							$group_color=", nvl(b.color_id,0)";
						}

						// Bulk
						if($roll_maintained_yesNo==1) // Yes
						{
							$sql_inhouse="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then c.quantity_pcs else 0 end ) as pcsnoshift,f.job_no, a.company_id";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", case when b.shift_name=$key then count(g.roll_no) else 0 end as roll".strtolower($val)."
								, sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val)
								;
							}
							$sql_inhouse.=" , case when b.shift_name=0 then count(g.roll_no) else 0 end as rollnoshift
							from wo_booking_mst k, ppl_planning_info_entry_mst h, ppl_planning_info_entry_dtls i, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
							where k.booking_no=h.booking_no and h.id=i.mst_id and i.id=a.booking_id and k.booking_type in(1,4) and a.id=b.mst_id and b.id=g.dtls_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis=2 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond $entry_form_cond
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no,b.shift_name, f.job_no, a.company_id
							union all ";
							$sql_inhouse.="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then c.quantity_pcs else 0 end ) as pcsnoshift,f.job_no, a.company_id";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", case when b.shift_name=$key then count(g.roll_no) else 0 end as roll".strtolower($val)."
								, sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val)
								;
							}
							$sql_inhouse.=" , case when b.shift_name=0 then count(g.roll_no) else 0 end as rollnoshift
							from wo_booking_mst k, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
							where k.id=a.booking_id and a.id=b.mst_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and b.id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond $entry_form_cond and k.booking_type in(1,4)
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no,b.shift_name, f.job_no, a.company_id order by floor_id";
						}
						else // 2 No
						{
							$sql_inhouse="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then c.quantity_pcs else 0 end ) as pcsnoshift,f.job_no, a.company_id";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", case when b.shift_name=$key then 0 else 0 end as roll".strtolower($val)."
								, sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then c.quantity_pcs else 0 end ) as pcsshift".strtolower($val)
								;
							}
							$sql_inhouse.=" , case when b.shift_name=0 then count(0) else 0 end as rollnoshift
							from wo_booking_mst k, ppl_planning_info_entry_mst h, ppl_planning_info_entry_dtls i, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
							where k.booking_no=h.booking_no and h.id=i.mst_id and i.id=a.booking_id and k.booking_type in(1,4) and a.id=b.mst_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis=2 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond $entry_form_cond
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no, d.seq_no,b.shift_name, f.job_no, a.company_id order by b.floor_id,d.seq_no
							union all ";
							$sql_inhouse.="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then c.quantity_pcs else 0 end ) as pcsnoshift,f.job_no, a.company_id";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", case when b.shift_name=$key then 0 else 0 end as roll".strtolower($val)."
								, sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then c.quantity_pcs else 0 end ) as pcsshift".strtolower($val)
								;
							}
							$sql_inhouse.=" , case when b.shift_name=0 then count(0) else 0 end as rollnoshift
							from wo_booking_mst k, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
							where k.id=a.booking_id and a.id=b.mst_id and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond $entry_form_cond and k.booking_type in(1,4)
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no, d.seq_no,b.shift_name, f.job_no, a.company_id order by b.floor_id,d.seq_no";
						}

						//echo $sql_inhouse;//die;//3 seconds ind database
						//===========================================================================
						// subcontract
						if($roll_maintained_yesNo==1) // Yes
						{
							$sql_subcontract="SELECT c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(g.qnty) as outqntyshift, sum(g.qc_pass_qnty_pcs) as outpcsshift, f.job_no, a.company_id
							from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f, pro_roll_details g
							where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and e.job_id=f.id and c.entry_form=2 and c.trans_type=1 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and a.receive_basis!=4 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id,b.machine_gg,b.machine_dia, c.po_breakdown_id, f.job_no, a.company_id order by b.floor_id,a.receive_date";
						}
						else
						{
							$sql_subcontract="SELECT c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift,sum(c.quantity_pcs ) as outpcsshift, f.job_no, a.company_id
							from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f
							where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_id=f.id and c.entry_form=2 and c.trans_type=1 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and a.receive_basis!=4 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id,b.machine_gg,b.machine_dia, c.po_breakdown_id, f.job_no, a.company_id order by b.floor_id,a.receive_date";
						}
						//echo $sql_subcontract; //die();
						//==========================================================================
						$sql_service_receive="SELECT c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f
						where c.po_breakdown_id=e.id and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_id=f.id and a.entry_form=22 and c.entry_form=22 and c.trans_type=1 and b.trans_id>0 and c.is_sales=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id, b.machine_gg,b.machine_dia, c.po_breakdown_id order by b.floor_id,a.receive_date";
						//echo $sql_service_receive;die;
						//==========================wout_order_start=====================================
						if($cbo_booking_type == 0 || $cbo_booking_type == 90) // SMN
						{
							$sql_wout_order="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, 0 as po_breakdown_id, d.machine_no as machine_name, '' as job_no_mst, '' po_number, 0 as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";
							if($roll_maintained_yesNo==1) // Yes
							{
								foreach($shift_name as $key=>$val)
								{
									$sql_wout_order.=", case when b.shift_name=$key then count(g.roll_no) end as roll".strtolower($val)."
									,sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
									", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
								}
								$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d , pro_roll_details g
								where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id = g.mst_id and b.id = g.dtls_id and g.entry_form = 2 and a.knitting_source=1 and a.receive_basis!=4 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
							}
							else
							{
								foreach($shift_name as $key=>$val)
								{
									$sql_wout_order.=", case when b.shift_name=$key then 0 end as roll".strtolower($val)."
									,sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val).
									", sum(case when b.shift_name=$key then b.grey_receive_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
								}

								$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d
								where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.knitting_source=1 and a.receive_basis!=4 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
							}
							$sql_wout_order.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width ,d.machine_no, d.seq_no,b.shift_name order by b.floor_id,d.seq_no";//b.floor_id,a.receive_date,
							//echo $sql_wout_order;
							//=====================================================================
							$sql_wout_order_smn="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, 0 as po_breakdown_id, 0 as machine_name, '' as job_no_mst, '' po_number,  sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";
							if($roll_maintained_yesNo==1) // Yes
							{
								$sql_wout_order_smn .= ", sum(case when b.shift_name=0 then g.qnty else 0 end ) as qntynoshift";
								foreach($shift_name as $key=>$val)
								{
									$sql_wout_order_smn.=", case when b.shift_name=$key then count(g.roll_no) end as roll".strtolower($val)."
									,sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
									", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
								}
								$sql_wout_order_smn.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details g
								where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id = g.mst_id and b.id = g.dtls_id and g.entry_form = 2 and a.knitting_source=3 and a.receive_basis!=4 and b.machine_no_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
							}
							else
							{
								$sql_wout_order_smn .= ", sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift";
								foreach($shift_name as $key=>$val)
								{
									$sql_wout_order_smn.=", case when b.shift_name=$key then 0 end as roll".strtolower($val)."
									,sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val).
									", sum(case when b.shift_name=$key then b.grey_receive_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
								}

								$sql_wout_order_smn.=" from inv_receive_master a, pro_grey_prod_entry_dtls b
								where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.knitting_source=3 and a.receive_basis!=4 and b.machine_no_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
							}
							$sql_wout_order_smn.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,b.shift_name order by b.floor_id,a.receive_date";
						}
						//echo $sql_wout_order_smn;//die();
						//==========================================================================

						$sql_knit_sales="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no,  a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id as machine_id, b.machine_gg,b.machine_dia, b.floor_id $select_color,c.job_no, c.buyer_id,c.style_ref_no,d.machine_no as machine_name,
						sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift,
						sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
						foreach($shift_name as $key=>$val)
						{
							$sql_knit_sales.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."
							, sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
						}

						if($cbo_booking_type > 0)
						{
							if($cbo_booking_type == 89){
								$entry_form_cond = " and k.booking_type = 4 ";
							}
							else
							{
								$entry_form_cond = " and k.entry_form=$cbo_booking_type";
							}
						}
						else
						{
							$entry_form_cond = "";
						}

						$sql_knit_sales.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details e, fabric_sales_order_mst c, lib_machine_name d, wo_booking_mst k
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=e.dtls_id and e.po_breakdown_id=c.id and b.machine_no_id=d.id and c.sales_booking_no=k.booking_no and k.status_active=1 and k.is_deleted=0 and a.knitting_source=1 and e.is_sales=1 and b.machine_no_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $location_cond $entry_form_cond and k.booking_type in(1,4)
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, c.buyer_id,c.style_ref_no,c.job_no,d.machine_no, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,d.seq_no
						union all ";//b.floor_id,a.receive_date,and c.within_group=2 and a.receive_basis=4

						$sql_knit_sales.="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no,  a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id as machine_id, b.machine_gg,b.machine_dia, b.floor_id $select_color,c.job_no, c.buyer_id,c.style_ref_no,d.machine_no as machine_name,
						sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift,
						sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
						foreach($shift_name as $key=>$val)
						{
							$sql_knit_sales.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."
							, sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
						}

						if($cbo_booking_type > 0)
						{
							if($cbo_booking_type == 90)
							{
								$entry_form_cond = " and k.booking_type=4";
							}else
							{
								$entry_form_cond = " and k.entry_form_id=$cbo_booking_type";
							}
						}else
						{
							$entry_form_cond = "";
						}

						$sql_knit_sales.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details e, fabric_sales_order_mst c, lib_machine_name d, wo_non_ord_samp_booking_mst k
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=e.dtls_id and e.po_breakdown_id=c.id and b.machine_no_id=d.id and c.sales_booking_no=k.booking_no and k.status_active=1 and k.is_deleted=0 and a.knitting_source=1 and e.is_sales=1 and b.machine_no_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $location_cond $entry_form_cond and k.booking_type=4
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, c.buyer_id,c.style_ref_no,c.job_no,d.machine_no, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,d.seq_no
						union all ";

						$sql_knit_sales.="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.booking_id, a.booking_no,  a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id as machine_id, b.machine_gg,b.machine_dia, b.floor_id $select_color,c.job_no, c.buyer_id,c.style_ref_no,d.machine_no as machine_name,
						sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift,
						sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
						foreach($shift_name as $key=>$val)
						{
							$sql_knit_sales.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."
							, sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
						}

						if($cbo_booking_type > 0)
						{
							$entry_form_cond = " and a.id=0";
						}
						else
						{
							$entry_form_cond = "";
						}

						$sql_knit_sales.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details e, fabric_sales_order_mst c, lib_machine_name d
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=e.dtls_id and e.po_breakdown_id=c.id and b.machine_no_id=d.id and a.knitting_source=1 and e.is_sales=1 and b.machine_no_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $location_cond and c.within_group=2
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, c.buyer_id,c.style_ref_no,c.job_no,d.machine_no, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,d.seq_no order by floor_id";
						//echo $sql_knit_sales;//1:20 sechond run time in database 48 data two days (max time)

						//echo $sql_inhouse."<br>".$sql_subcontract."<br>".$sql_service_receive."<br>".$sql_wout_order."<br>".$sql_wout_order_smn."<br>".$sql_knit_sales;//die;
						$nameArray_inhouse=sql_select($sql_inhouse);
						$nameArray_subcontract=sql_select($sql_subcontract);
						$nameArray_service_receive=sql_select($sql_service_receive);
						$nameArray_without_order=sql_select($sql_wout_order);
						$nameArray_without_order_smn=sql_select($sql_wout_order_smn);
						$nameArray_sales_order=sql_select($sql_knit_sales);

						//echo count($nameArray_subcontract);die;
						//echo "test9";die;
						if (count($nameArray_inhouse)>0 || count($nameArray_subcontract)>0)//for avg.rate
						{
							$job_no_prefix_arr = array();
							foreach ($nameArray_inhouse as $row)
							{
								$job_no_prefix_arr[$row[csf('job_no_prefix_num')]]=$row[csf('job_no_prefix_num')];
							}
							foreach ($nameArray_subcontract as $row)
							{
								$job_no_prefix_arr[$row[csf('job_no_prefix_num')]]=$row[csf('job_no_prefix_num')];
							}

							$job_no_pre = implode(",", $job_no_prefix_arr);
						    $job_no_pre_cond="";
						    if($job_no_pre)
						    {
						        $job_no_pre = implode(",",array_filter(array_unique(explode(",", $job_no_pre))));
						        $job_no_arr = explode(",", $job_no_pre);
						        if($db_type==0)
						        {
						            $job_no_pre_cond = " and a.job_no_prefix_num in ($job_no_pre )";
						        }
						        else
						        {
						            if(count($job_no_arr)>999)
						            {
						                $issue_roll_chunk_arr=array_chunk($job_no_arr, 999);
						                $job_no_pre_cond=" and (";
						                foreach ($issue_roll_chunk_arr as $value)
						                {
						                    $job_no_pre_cond .=" a.job_no_prefix_num in (".implode(",", $value).") or ";
						                }
						                $job_no_pre_cond=chop($job_no_pre_cond,"or ");
						                $job_no_pre_cond.=")";
						            }
						            else
						            {
						                $job_no_pre_cond = " and a.job_no_prefix_num in ($job_no_pre )";
						            }
						        }

						        $rate_sql="SELECT b.job_no, b.fabric_description, b.charge_unit, c.lib_yarn_count_deter_id, a.job_no_prefix_num
								from wo_po_details_master a, wo_pre_cost_fab_conv_cost_dtls b, wo_pre_cost_fabric_cost_dtls c
								where a.job_no=b.job_no and b.job_no=c.job_no and b.fabric_description=c.id and b.cons_process=1  $job_no_pre_cond
								group by b.job_no, b.fabric_description, b.charge_unit, c.lib_yarn_count_deter_id, a.job_no_prefix_num";
								$rate_data=sql_select($rate_sql);
								$rate_arr=array();
								foreach ($rate_data as $key => $row)
								{
									$rate_arr[$row[csf('job_no')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('charge_unit')];
								}
						    }
						}
						// echo "<pre>";print_r($job_no_prefix_arr);die;


						if (count($nameArray_inhouse)>0) // In-House
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="46" align="left" ><b>In-House</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td align='left' ></td>
							<td colspan='36' align='left' ><b>In-House</b></td>
							</tr>";
							foreach ($nameArray_inhouse as $row)
							{

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$inhouse_avg_rate=$rate_arr[$row[csf('job_no')]][$row[csf('febric_description_id')]];

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no='';
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else $booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								//if(!in_array($row[csf('floor_id')],$floor_array))
								if($floor_array[$row[csf('floor_id')]]!="")
								{
									$floor_array[$row[csf('floor_id')]]=$row[csf('floor_id')];
									if($i!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="27" align="right"><b>Floor Total</b></td>
											<?
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												$floor_tot_pcs_row+=$floor_tot_roll[$key]['pcs'];
												?>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['pcs'],2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.','');


												?></td>
												<?
											}
											?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_pcs_row+$pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td align="right"><? echo number_format($inhouse_floor_total_amount,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<?
										$html.="<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
										<td align='right'>".number_format($noshift_total,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";
										unset($noshift_total);
										unset($floor_tot_roll);
										unset($inhouse_floor_total_amount);
									}
									if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") $floor_id_show= "Without Floor"; else $floor_id_show = $floor_details[$row[csf('floor_id')]];
									?>
									<tr><td colspan="44" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $floor_id_show; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='37' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>$floor_id_show</b></td></tr>";
									$floor_array[$i]=$row[csf('floor_id')];
								}

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle" id="chk_hide_dtls">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "1"; ?>" />
									</td>
									<td width="30"><? echo $i; ?></td>
									<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
									<td align="center" width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
									<td width="70"><? echo $row[csf('file_no')]; ?></td>
									<td width="70"><? echo $row[csf('grouping')]; ?></td>
									<td align="center" width="60"><p><? echo $row[csf('year')]; ?></p></td>
									<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
									<td width="100"><p><? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
									<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
									<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
									<td width="110" id="booking_no_<? echo $i; ?>" align="center"><P><? echo $booking_plan_no; ?></P></td>
									<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
									<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
									<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
									<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
									<td width="60" id="yarn_lot_<? echo $i; ?>" style="max-width:60px"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
									<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
									<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
										<?
										$color_arr=array_unique(explode(",",$row[csf('color_id')]));
										$all_color="";
										foreach($color_arr as $id)
										{
											$all_color.=$color_details[$id].",";
										}
										$all_color=chop($all_color," , ");
										echo $all_color;

										?></p>
									</td>
									<td width="150" title="<? echo 'febric descr id: '.$row[csf('febric_description_id')]; ?>" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
									<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')];?></p></td>
									<td width="80" id="mc_gause_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')];?></p></td>
									<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
									<td width="50" id="stich_<? echo $i; ?>" style="max-width:50px"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
									<?
									$html.="<tr>
									<td width='30'>".$i."</td>
									<td width='55'><p>".$knitting_party."&nbsp;</p></td>
									<td width='60'><p>".$row[csf('machine_name')]."</p></td>
									<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
									<td width='70'><p>".$row[csf('file_no')]."</p></td>
									<td width='70'><p>".$row[csf('grouping')]."</p></td>
									<td width='60'><p>".$row[csf('year')]."</p></td>
									<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
									<td width='100'><p>".$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']."</p></td>
									<td width='110'><p>".$row[csf('po_number')]."</p></td>
									<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
									<td width='110'><P>".$booking_plan_no."</P></td>
									<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
									<td width='80'>".$reqsn_no."</td>
									<td width='80'><p>".$count."</p></td>
									<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
									<td width='100'><p>&nbsp;".$color."</p></td>";
									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_details[$id].",";
									}
									$all_color=chop($all_color," , ");
									$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
									<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
									<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
									<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
									$row_tot_roll=0;
									$row_tot_qnty=0;
									$row_tot_pcs = 0;
									foreach($shift_name as $key=>$val)
									{
										$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
										$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
										$tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

										$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
										$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
										$source_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

										$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
										$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
										$floor_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

										$row_tot_roll+=$row[csf('roll'.strtolower($val))];
										$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
										$row_tot_pcs+=$row[csf('pcsshift'.strtolower($val))];
										?>
										<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
										<td width="50" align="right" ><? echo number_format($row[csf('pcsshift'.strtolower($val))],2);?></td>
										<td width="100" align="right" >
											<?
											echo number_format($row[csf('qntyshift'.strtolower($val))],2);
											$machineSamarryDataArr[$machine_lib[$row[csf('machine_no_id')]]][$key]+=$row[csf('qntyshift'.strtolower($val))];
											?>
										</td>
										<?

										$html.="<td width='50' align='right' >".$row[csf('roll'.strtolower($val))]."</td>
										<td width='50' align='right' >".number_format($row[csf('pcsshift'.strtolower($val))],2)."</td>
										<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
									}

									?>
									<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>

									<td width="50" align="right" id="nopcs_<? echo $i; ?>"><? echo number_format($row[csf('pcsnoshift')],2); ?></td>
									<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
									<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
									<td width="50" align="right" id="pcs_<? echo $i; ?>"><? echo number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.',''); ?></td>
									<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','');
									$tot_in_Qnty=$row_tot_qnty+$row[csf('qntynoshift')]; ?></td>
									<td width="100" align="right" title="<? echo 'Job: '.$row[csf('job_no_prefix_num')].', Feb: '.$row[csf('febric_description_id')]; ?>"><p><? echo $inhouse_rate_in_tk=$conversion_rate*$inhouse_avg_rate; ?></p></td>
									<td width="100" align="right" title="<? echo 'Rate in USD: '.$inhouse_avg_rate; ?>"><p><?
									$inhouse_amount=$tot_in_Qnty*$inhouse_rate_in_tk;
									echo number_format($inhouse_amount,2,'.','')?></p></td>
									<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								</tbody>
								<?
								$html.="
								<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
								<td width='50' align='right'>".number_format($row[csf('pcsnoshift')],2)."</td>
								<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2)."</td>
								<td width='50' align='right'>".$row_tot_roll."</td>
								<td width='50' align='right'>".number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.','')."</td>
								<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
								<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
								</tr>
								</tbody>";

								$grand_tot_roll+=$row_tot_roll+$row[csf('no_of_roll')];
								$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
								$grand_tot_pcs+=$row_tot_pcs+$row[csf('pcsnoshift')];

								$source_grand_tot_roll+=$row_tot_roll;
								$source_grand_tot_qnty+=$row_tot_qnty;
								$source_grand_tot_pcs+=$row_tot_pcs;

								$rollshift_total+=$row[csf('rollnoshift')];
								$noshift_total+=$row[csf('qntynoshift')];
								$pcsnoshift_total+=$row[csf('pcsnoshift')];
								$inhouse_floor_total_amount+=$inhouse_amount;


								$grand_tot_floor_roll+=$row_tot_roll;
								$grand_tot_floor_qnty+=$row_tot_qnty;
								$grand_tot_floor_pcs+=$row_tot_pcs;
								$total_roll_noshift+=$row[csf('rollnoshift')];
								$total_qty_noshift+=$row[csf('qntynoshift')];
								$total_pcs_noshift+=$row[csf('pcsnoshift')];
								$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
								$in_house_total_amount+=$inhouse_amount;
								$grand_tot_amount+=$inhouse_amount;

								$i++;
							}

							?>
							<tr class="tbl_bottom">
								<td></td>
								<td colspan="26" align="right"><b>Floor Total </b></td>
								<?
								$floor_tot_qnty_row=0;
								foreach($shift_name as $key=>$val)
								{
									$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
									$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
									$floor_tot_pcs_row+=$floor_tot_roll[$key]['pcs'];
									?>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['pcs'],2,'.',''); ?></td>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($rollshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($floor_tot_roll_row+$rollshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($floor_tot_pcs_row+$pcsnoshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($inhouse_floor_total_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="tbl_bottom">
								<td></td>
								<td colspan="26" align="right"><b>In House Total</b></td>
								<?
								foreach($shift_name as $key=>$val)
								{
									$source_tot_rolls+=$source_tot_roll[$key]['roll'];
									$source_tot_qnty+=$source_tot_roll[$key]['qty'];
									$source_tot_pcs+=$source_tot_roll[$key]['pcs'];
									$source_tot_roll_row+=$source_tot_roll[$key]['roll'];
									$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
									$source_tot_pcs_row+=$source_tot_roll[$key]['pcs'];

									?>
									<td align="right"><? echo number_format($source_tot_roll_row,2,'.',''); ?></td>
									<td align="right"><? echo number_format($source_tot_pcs_row,2,'.',''); ?></td>
									<td align="right"><? echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
									<?
									unset($source_tot_roll_row);
									unset($source_tot_qnty_row);
									unset($source_tot_pcs_row);
								}
								?>
								<td align="right"><? echo number_format($total_roll_noshift,2,'.',''); ?></td>
								<td align="right"><? echo number_format($total_pcs_noshift,2,'.',''); ?></td>
								<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?></td>
								<td align="right"><? echo number_format($source_tot_rolls,2,'.',''); ?></td>
								<td align="right"><? echo number_format($source_tot_pcs,2,'.',''); ?></td>
								<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($in_house_total_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
							$html.="<tr>
							<td colspan='25' align='right'><b>Floor Total</b></td>";

							$floor_tot_qnty_row=0;
							foreach($shift_name as $key=>$val)
							{
								$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];

								$html.="<td align='right'>&nbsp;</td>
								<td align='right'>&nbsp;</td>
								<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
							}
							$html.="
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
							<td align='right'>".number_format($noshift_total,2,'.','')."</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>pcs</td>
							<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
							<td>&nbsp;</td>
							</tr>
							<tr>
							<td colspan='25' align='right'><b>In House Total</b></td>";
							$source_tot_qnty=0;
							foreach($shift_name as $key=>$val)
							{
								$source_tot_qnty+=$source_tot_roll[$key]['qty'];
								$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
								$html.="<td align='right'>&nbsp;</td>
								<td align='right'>&nbsp;</td>
								<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";

								unset($source_tot_qnty_row);
							}
							$html.="
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
							<td>&nbsp;</td>
							</tr>";
						}
						// echo "<pre>";print_r($machineSamarryDataArr);die;
						// *************** Outbound-Subcontract Production *********************
						if(count($nameArray_subcontract)>0) // Outbound Subcon
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="46" align="left"><b>Outbound-Subcontract Production</b></td>
							</tr>

							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left'><b>Outbound-Subcontract</b></td>
							</tr>";
							foreach ($nameArray_subcontract as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								$outbound_avg_rate=$rate_arr[$row[csf('job_no')]][$row[csf('febric_description_id')]];
								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no="";
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle">
									<!--<input type="checkbox" id="tbl_<? echo $i;?>" onClick="selected_row(<? //echo $i; ?>);" />-->
									<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
									<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('prod_id')]; ?>" />
									<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
									<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "3"; ?>" />
									<td width="30"><? echo $i; ?></td>
									<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
									<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['job_no']; ?></p></td>

									<td width="70"><? echo $row[csf('file_no')]; ?></td>
									<td width="70"><? echo $row[csf('grouping')]; ?></td>

									<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['year']; ?></p></td>
									<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
									<td width="100"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></p></td>
									<td width="110"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
									<td width="90"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
									<td width="110" align="center"><P><? echo $booking_plan_no; ?></P></td>
									<td width="60"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
									<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
									<td width="80"><p><? echo $count; ?>&nbsp;</p></td>
									<td width="90"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
									<td width="100"><p>&nbsp;<? echo $color; ?></p></td>
									<td width="100"><p>&nbsp;
									<?
									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_details[$id].",";
									}
									$all_color=chop($all_color," , ");
									echo $all_color;
									?></p></td>
									<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
									<td width="50"><p>&nbsp;<? echo $row[csf('machine_dia')];?></p></td>
									<td width="80"><p>&nbsp;<? echo $row[csf('machine_gg')];?></p></td>
									<td width="50"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
									<td width="50"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
									<?
									$html.="<tr>
									<td width='30'>".$i."</td>
									<td width='55'><p>".$knitting_party."&nbsp;</p></td>
									<td width='60'><p>".$row[csf('machine_name')]."</p></td>
									<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['job_no']."</p></td>
									<td width='70'><p>".$row[csf('file_no')]."</p></td>
									<td width='70'><p>".$row[csf('grouping')]."</p></td>
									<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['year']."</p></td>
									<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
									<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
									<td width='110'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['po_number']."</p></td>
									<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
									<td width='110'><P>".$booking_plan_no."</P></td>
									<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
									<td width='80'>".$reqsn_no."</td>
									<td width='80'><p>".$count."</p></td>
									<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
									<td width='100'><p>&nbsp;".$color."</p></td>";

									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_details[$id].",";
									}
									$all_color=chop($all_color," , ");
									$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
									<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
									<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
									<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";

									$row_tot_roll=0;
									$row_tot_qnty=0;
									foreach($shift_name as $key=>$val)
									{
										?>
										<td width="50" align="right"></td>
										<td width="50" align="right"></td>
										<td width="100" align="right"></td>

										<?
										$html.="<td width='50' align='right' ></td>
										<td width='50' align='right' ></td>
										<td width='100' align='right' ></td>";
									}
									?>
									<td width="50" align="right"><? echo $row_tot_roll; ?></td>
									<td width="50" align="right"><? echo number_format($row[csf('outpcsshift')],2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
									<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
									<td width="50" align="right"><? echo number_format($row[csf('outpcsshift')],2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
									<td width="100" align="right" title="<? echo 'Job: '.$row[csf('job_no_prefix_num')].', Feb: '.$row[csf('febric_description_id')]; ?>"><p><? echo $outbound_rate_in_tk=$conversion_rate*$outbound_avg_rate; ?></p></td>
									<td width="100" align="right" title="<? echo 'Rate in USD: '.$outbound_avg_rate; ?>"><p><?
									$outbound_amount=$row[csf('outqntyshift')]*$outbound_rate_in_tk;
									echo number_format($outbound_amount,2,'.',''); ?></p></td>
									<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$html.="
								<td width='50' align='right'>".$row_tot_roll."</td>
								<td width='50' align='right'>".number_format($row[csf('outpcsshift')],2,'.','')."</td>
								<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>

								<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
								<td width='50' align='right'>".number_format($row[csf('outpcsshift')],2,'.','')."</td>
								<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
								<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
								</tr>";
								$grand_tot_roll+=$row[csf('no_of_roll')];
								$grand_tot_qnty+=$row[csf('outqntyshift')];
								$grand_tot_pcs+=$row[csf('outpcsshift')];

								$source_grand_tot_qnty+=$row[csf('outqntyshift')];
								$source_grand_tot_pcs+=$row[csf('outpcsshift')];

								$tot_subcontract_noRoll+=$row_tot_roll;
								$tot_subcontract_roll+=$row[csf('no_of_roll')];
								$total_service_subcontact+=$row[csf('outqntyshift')];
								$tot_subcontract+=$row[csf('outqntyshift')];
								$tot_subcontract_pcs+=$row[csf('outpcsshift')];
								$tot_outbound_amount+=$outbound_amount;
								$grand_tot_amount+=$outbound_amount;

								$grand_tot_floor_qnty+=$row_tot_qnty;
								$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
								$i++;
							}
							?>
							<tr class="tbl_bottom">
								<td colspan="27" align="right"><b>Outbound-Subcontract Total</b></td>
								<?
								$source_tot_qnty_row=0;
								foreach($shift_name as $key=>$val)
								{
									?>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right"></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($tot_subcontract_noRoll,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract_pcs,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract_roll,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract_pcs,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($tot_outbound_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
							$html.="<tr>
							<td colspan='25' align='right'><b>Outbound-Subcontract Total</b></td>";

							$floor_tot_qnty_row=0;
							foreach($shift_name as $key=>$val)
							{
								$html.="<td align='right'>&nbsp;</td>
								<td align='right'>&nbsp;</td>
								<td align='right'></td>";
							}
							$html.="
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($tot_subcontract_pcs,2,'.','')."</td>
							<td align='right'>".number_format($tot_subcontract,2,'.','')."</td>
							<td>&nbsp;</td>
							</tr>";
						}
						 //echo "<pre>";print_r($machineSamarryDataArr);die;
						// **************************** Outbound-Subcontract Receive ************************
						if(count($nameArray_service_receive)>0) // service_receive
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="44" align="left"><b>Outbound-Subcontract Receive</b></td>
							</tr>

							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left'><b>Outbound-Subcontract</b></td>
							</tr>";
							foreach ($nameArray_service_receive as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no='';
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('prod_id')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "3"; ?>" />
										<td width="30"><? echo $i; ?></td>
										<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
										<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['job_no']; ?></p></td>

										<td width="70"><? echo $row[csf('file_no')]; ?></td>
										<td width="70"><? echo $row[csf('grouping')]; ?></td>

										<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['year']; ?></p></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></p></td>
										<td width="110"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
										<td width="90"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
										<td width="110" align="center"><P><? echo $booking_plan_no; ?></P></td>
										<td width="60"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
										<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
										<td width="80"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100"><p>&nbsp;<? echo $color; ?></p></td>
										<td width="100"><p>&nbsp;
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
											<td width="80"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr>
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['job_no']."</p></td>
											<td width='70'><p>".$row[csf('file_no')]."</p></td>
											<td width='70'><p>".$row[csf('grouping')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['year']."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['po_number']."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$booking_plan_no."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";

											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
											<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";

											$row_tot_roll=0;
											$row_tot_qnty=0;
											foreach($shift_name as $key=>$val)
											{
												?>
												<td width="50" align="right"><? //echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="50" align="right"><? //echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right"><? //echo number_format($row[csf('outqntyshift'.strtolower($val))],2); ?></td>

												<?
												$html.="<td width='50' align='right' ></td>
												<td width='50' align='right' ></td>
												<td width='100' align='right' ></td>";
											}
											?>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											<td width="50" align="right">0.00</td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											<td width="50" align="right">0.00</td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
										<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
										<td width='50' align='right'>".$pcs."</td>
										<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
										<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
										<td width='50' align='right'>".$pcs."</td>
										<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
										<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
										</tr>";
										$grand_tot_roll+=$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row[csf('outqntyshift')];
										$source_grand_tot_qnty+=$row[csf('outqntyshift')];

										$tot_subcontract_noRoll+=$row[csf('no_of_roll')];
										$tot_subcontract_roll+=$row[csf('no_of_roll')];
										$tot_subcontract+=$row[csf('outqntyshift')];
										$total_service_subcontact+= $row[csf('outqntyshift')];
										$grand_tot_floor_qnty+=$row_tot_qnty;

										$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
										$i++;
									}

									?>
									<tr class="tbl_bottom">
										<td colspan="27" align="right"><b>Outbound-Subcontract Total</b></td>
										<?
										$source_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"></td>
											<?
										}
										?>
										<td align="right"><? echo number_format($tot_subcontract_noRoll,2,'.',''); ?></td>
										<td align="right">&nbsp; </td>

										<td align="right"><? echo number_format($total_service_subcontact,2,'.',''); ?></td>
										<td align="right"><? echo number_format($tot_subcontract_roll,2,'.',''); ?></td>
										<td align="right">&nbsp; </td>
										<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="<tr>
									<td colspan='25' align='right'><b>Outbound-Subcontract Total</b></td>";

									$floor_tot_qnty_row=0;
									foreach($shift_name as $key=>$val)
									{
										$html.="<td align='right'>&nbsp;</td>
										<td align='right'>&nbsp;</td>
										<td align='right'></td>";
									}
									$html.="
									<td align='right'>&nbsp;</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($total_service_subcontact,2,'.','')."</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($tot_subcontract,2,'.','')."</td>
									<td>&nbsp;</td>
									</tr>";
						}
						 //echo "<pre>";print_r($machineSamarryDataArr);die;
						unset($floor_array); $total_qty_noshift=0;
						unset($floor_tot_roll); unset($noshift_total); unset($pcsnoshift_total); unset($source_tot_roll);  $source_tot_qnty=0;
						$j=1;

						// **************** Sample Without Order ***************
						if (count($nameArray_without_order)>0)// Sample Without Order
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="46" align="left" ><b>Sample Without Order</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left' ><b>Sample Without Order</b></td>
							</tr>";
							foreach ($nameArray_without_order as $row)
							{
								if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no="";
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								if(!in_array($row[csf('floor_id')],$floor_array))
								{
									if($j!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="27" align="right"><b>Floor Total</b></td>
											<?
											$html.="<tr>
											<td colspan='25' align='right'><b>Floor Total</b></td>";
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												?>
												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.','');
													//$machineSamarryDataArr[$row[csf('machine_no_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
												 ?></td>
												<?
												$html.="<td align='right'>&nbsp;</td>
												<td align='right'>&nbsp;</td>
												<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
											}
											?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<?
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='ight'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";
										unset($noshift_total);
										unset($floor_tot_roll);
									}
									?>
									<tr><td colspan="46" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") echo "Without Floor"; else echo $floor_details[$row[csf('floor_id')]]; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='37' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>".$floor_details[$row[csf('floor_id')]]."</b></td></tr>";
									$floor_array[$i]=$row[csf('floor_id')];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle" id="">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "2"; ?>" /></td>
										<!-- 2 mean without order-->
										<td width="30"><? echo $i; ?></td>
										<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>

										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('year')]; ?></p></td>
										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p>&nbsp;<? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
										<td width="110"><p>&nbsp;<? echo $row[csf('po_number')]; ?></p></td>
										<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
										<td width="110" id="booking_no_<? echo $i; ?>"><P><? echo $booking_plan_no; ?></P></td>
										<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
										<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
										<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60" id="yarn_lot_<? echo $i; ?>" style="max-width:60px"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
											<td width="80" id="mc_gauge_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50" id="stich_<? echo $i; ?>" style="max-width:50px"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr>
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
											<td width='70'><p></p></td>
											<td width='70'><p></p></td>
											<td width='60'><p>".$row[csf('year')]."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$row[csf('po_number')]."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$booking_plan_no."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
											<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
											$row_tot_roll=0;
											$row_tot_qnty=0;
											$row_tot_pcs=0;
											foreach($shift_name as $key=>$val)
											{
												$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$source_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$floor_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$row_tot_roll+=$row[csf('roll'.strtolower($val))];
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
												$row_tot_pcs+=$row[csf('pcsshift'.strtolower($val))];
												?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="50" align="right" ><?
												echo number_format($row[csf('pcsshift'.strtolower($val))],2);?></td>
												<td width="100" align="right" >
													<?
													echo number_format($row[csf('qntyshift'.strtolower($val))],2);
													//$machineSamarryDataArr[$row[csf('machine_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
													$machineSamarryDataArr[$machine_lib[$row[csf('machine_no_id')]]][$key]+=$row[csf('qntyshift'.strtolower($val))];
													?>
												</td>
												<?
												$html.="<td width='50' align='right'>".$row[csf('roll'.strtolower($val))]."</td>
												<td width='50' align='right'>".number_format($row[csf('pcsshift'.strtolower($val))],2)."</td>
												<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>																<td width="50" align="right" id="nopcs_<? echo $i; ?>"><? echo number_format($row[csf('pcsnoshift')],2); ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
											<td width="50" align="right" id="totpcs_<? echo $i; ?>"><? echo number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.',''); ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>

											<td width="100" align="right"></td>
											<td width="100" align="center"></td>

											<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
										<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
										<td width='50' align='right'>".number_format($row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2,'.','')."</td>
										<td width='50' align='right'>".$row_tot_roll."</td>
										<td width='50' align='right'>".number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
										<td><p>".$row[csf('remarks')]."</p></td>
										</tr>";
										$grand_tot_roll+=$row_tot_roll+$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
										$grand_tot_pcs+=$row_tot_pcs+$row[csf('pcsnoshift')];


										$source_grand_tot_roll+=$row_tot_roll;
										$source_grand_tot_qnty+=$row_tot_qnty;
										$source_grand_tot_pcs+=$row_tot_pcs;

										$noshift_total+=$row[csf('qntynoshift')];

										$grand_tot_floor_roll+=$row_tot_roll;
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$total_qty_noshift+=$row[csf('qntynoshift')];
										$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];

										$j++;
										$i++;
									}

									?></tbody>
									<tr class="tbl_bottom">
										<td colspan="27" align="right"><b>Floor Total</b></td>
										<?
										$html.="</tbody>
										<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.','');
												//$machineSamarryDataArr[$row[csf('machine_no_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
											?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
										<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
										<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr class="tbl_bottom">
										<td colspan="27" align="right"><b> Sample Without Order Total</b></td>
										<?

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>
										<tr>
										<td colspan='25' align='right'><b> Sample Without Order Total</b></td>";
										foreach($shift_name as $key=>$val)
										{
											$source_tot_qnty+=$source_tot_roll[$key]['qty'];
											$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><?  echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";
											unset($source_tot_qnty_row);
										}
										?>

										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
									<td>&nbsp;</td>
									</tr>";
						}
						// echo "<pre>";print_r($machineSamarryDataArr);die;
						unset($floor_array); $total_qty_noshift=0;
						unset($floor_tot_roll); unset($noshift_total); unset($pcsnoshift_total); unset($source_tot_roll);  $source_tot_qnty=0;
						$j=0;
						// **************** Sample Without Order Outbound ***************
						if (count($nameArray_without_order_smn)>0)
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="45" align="left" ><b>Sample Without Order Outbound</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left' ><b>Sample Without Order Outbound</b></td>
							</tr>";
							foreach ($nameArray_without_order_smn as $row)
							{
								if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no="";
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								if(!in_array($row[csf('floor_id')],$floor_array))
								{
									if($j!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="27" align="right"><b>Floor Total</b></td>
											<?
											$html.="<tr>
											<td colspan='25' align='right'><b>Floor Total</b></td>";
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												?>
												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
												<?
												$html.="<td align='right'>&nbsp;</td>
												<td align='right'>&nbsp;</td>
												<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
											}
											?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<?
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='ight'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";
										unset($noshift_total);
										unset($floor_tot_roll);
									}
									?>
									<tr><td colspan="44" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") echo "Without Floor"; else echo $floor_details[$row[csf('floor_id')]]; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='37' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>".$floor_details[$row[csf('floor_id')]]."</b></td></tr>";
									$floor_array[$i]=$row[csf('floor_id')];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle" id="">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "2"; ?>" /></td>
										<!-- 2 mean without order-->
										<td width="30"><? echo $i; ?></td>
										<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>

										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('year')]; ?></p></td>
										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p>&nbsp;<? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
										<td width="110"><p>&nbsp;<? echo $row[csf('po_number')]; ?></p></td>
										<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
										<td width="110" id="booking_no_<? echo $i; ?>"><P><? echo $booking_plan_no; ?></P></td>
										<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
										<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
										<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60" id="yarn_lot_<? echo $i; ?>" style="max-width:60px"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
											<td width="80" id="mc_gauge_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50" id="stich_<? echo $i; ?>" style="max-width:50px"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr>
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
											<td width='70'><p></p></td>
											<td width='70'><p></p></td>
											<td width='60'><p>".$row[csf('year')]."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$row[csf('po_number')]."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$booking_plan_no."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
											<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
											$row_tot_roll=0;
											$row_tot_qnty=0;
											$row_tot_pcs=0;
											foreach($shift_name as $key=>$val)
											{
												$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$source_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$floor_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$row_tot_roll+=$row[csf('roll'.strtolower($val))];
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
												$row_tot_pcs+=$row[csf('pcsshift'.strtolower($val))];
												?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="50" align="right" ><?
												echo number_format($row[csf('pcsshift'.strtolower($val))],2);?></td>
												<td width="100" align="right" >
													<?
													echo number_format($row[csf('qntyshift'.strtolower($val))],2);
													$machineSamarryDataArr[$machine_lib[$row[csf('machine_id')]]][$key]+=$row[csf('qntyshift'.strtolower($val))];
													?>
												</td>
												<?
												$html.="<td width='50' align='right'>".$row[csf('roll'.strtolower($val))]."</td>
												<td width='50' align='right'>".number_format($row[csf('pcsshift'.strtolower($val))],2)."</td>
												<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>																<td width="50" align="right" id="nopcs_<? echo $i; ?>"><? echo number_format($row[csf('pcsnoshift')],2); ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
											<td width="50" align="right" id="totpcs_<? echo $i; ?>"><? echo number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.',''); ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
											<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
										<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
										<td width='50' align='right'>".number_format($row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2,'.','')."</td>
										<td width='50' align='right'>".$row_tot_roll."</td>
										<td width='50' align='right'>".number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
										<td><p>".$row[csf('remarks')]."</p></td>
										</tr>";
										$grand_tot_roll+=$row_tot_roll+$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
										$grand_tot_pcs+=$row_tot_pcs+$row[csf('pcsnoshift')];


										$source_grand_tot_roll+=$row_tot_roll;
										$source_grand_tot_qnty+=$row_tot_qnty;
										$source_grand_tot_pcs+=$row_tot_pcs;

										$noshift_total+=$row[csf('qntynoshift')];

										$grand_tot_floor_roll+=$row_tot_roll;
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$total_qty_noshift+=$row[csf('qntynoshift')];
										$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];

										$j++;
										$i++;
									}

									?></tbody>
									<tr class="tbl_bottom">
										<td colspan="27" align="right"><b>Floor Total</b></td>
										<?
										$html.="</tbody>
										<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
										<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
										<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr class="tbl_bottom">
										<td colspan="27" align="right"><b> Sample Without Order Outbound Total</b></td>
										<?

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>
										<tr>
										<td colspan='25' align='right'><b> Sample Without Order Total</b></td>";
										foreach($shift_name as $key=>$val)
										{
											$source_tot_qnty+=$source_tot_roll[$key]['qty'];
											$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><?  echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";
											unset($source_tot_qnty_row);
										}
										?>

										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
									<td>&nbsp;</td>
									</tr>";
						}
						// echo "<pre>";print_r($machineSamarryDataArr);
						// =====Grand Total tfoot below=========
							?>
							<tfoot>
								<th></th>
								<th colspan="26" align="right">Grand Total</th>
								<?
								$html.="<tfoot>
								<th colspan='25' align='right'>Grand Total</th>";
								foreach($shift_name as $key=>$val)
								{
									$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
									$source_tot_pcs_row+=$source_tot_roll[$key]['pcs'];
									?>
									<th align="right"><? echo number_format($tot_roll[$key]['roll'],2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_roll[$key]['pcs'],2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_roll[$key]['qty'],2,'.',''); ?></th>
									<?
									$html.="<th align='right'>".number_format($tot_roll[$key]['roll'],2,'.','')."</th>
									<th align='right'>".number_format($tot_roll[$key]['pcs'],2,'.','')."</th>
									<th align='right'>".number_format($tot_roll[$key]['qty'],2,'.','')."</th>";
								}
								?>
								<th align="right"><? echo number_format($tot_subcontract_noRoll,2,'.',''); ?></th>
								<th align="right">&nbsp;</th>
								<th align="right"><? echo number_format($total_service_subcontact,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_pcs,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_amount,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tfoot>
							</table>
							<?
							$html.="
							<th align='right'>&nbsp;</th>
							<th align='right'>".number_format($total_service_subcontact,2,'.','')."</th>
							<th align='right'>&nbsp;</th>
							<th align='right'>".number_format($grand_tot_qnty,2,'.','')."</th>
							<th>&nbsp;</th>
							</tfoot>
							</table>
							</div>
							</fieldset>
							<br>";
							?>
						</div>
					</fieldset>
					<br>
					<!--  Fabric Sales Order Knitting Production Data Show -->
					<div>
						<?
						$tbl_width2=2355;
						if (count($nameArray_sales_order)>0)
						{
							?>
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width2+200; ?>" class="rpt_table" id="table_head" >
								<caption><strong style="float:left"> Fabric Sales Order Knitting Production </strong></caption>
								<thead>
									<tr>

										<th width="30" rowspan="2">SL</th>
										<th width="60" rowspan="2">Receive Challan No</th>
										<th width="55" rowspan="2">M/C No</th>
										<th width="60" rowspan="2">Floor</th>

										<th width="70" rowspan="2">Party</th>
										<th width="100" rowspan="2">Style</th>
										<th width="110" rowspan="2">Sales Order No</th>
										<th width="100" rowspan="2">Production. ID</th>

										<th width="80" rowspan="2">Yarn Count</th>
										<th width="90" rowspan="2">Yarn Brand</th>
										<th width="60" rowspan="2">Lot No</th>
										<th width="100" rowspan="2">Fabric Color</th>
										<th width="150" rowspan="2">Fabric Type</th>
										<th width="50" rowspan="2">M/C Dia</th>
										<th width="80" rowspan="2">M/C Gauge</th>
										<th width="50" rowspan="2">Fab. Dia</th>
										<th width="50" rowspan="2">Stitch</th>
										<th width="60" rowspan="2">GSM</th>
										<?
										foreach($shift_name as $val)
										{
											?>
											<th width="150" colspan="2"><? echo $val; ?></th>
											<?
										}
										?>
										<th width="150" colspan="3">No Shift</th>
										<th width="150" colspan="3">Total</th>
										<th width="100" rowspan="2">Insert User</th>
										<th width="100" rowspan="2">Insert Date and Tiime</th>
										<th rowspan="2">Remarks</th>
									</tr>
									<tr>
										<?
										foreach($shift_name as $val)
										{
											?>
											<th width="50" rowspan="2">Roll</th>
											<th width="100" rowspan="2">Qnty</th>
											<?
										}
										?>
										<th width="50" rowspan="2">Roll</th>
										<th width="50" rowspan="2">Pcs</th>
										<th width="100" rowspan="2">Qnty</th>
										<th width="50" rowspan="2">Roll</th>
										<th width="50" rowspan="2">Pcs</th>
										<th width="100" rowspan="2">Qnty</th>
									</tr>
								</thead>
							</table>

							<div style="width:<? echo $tbl_width2+220; ?>px;overflow-y:scroll; max-height:330px;" id="scroll_body">
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width2+200; ?>" class="rpt_table" id="table_body">

							<?
							$sales_floor_array=array(); $i=1;$kk=0;
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="33" align="left" ><b>In-House</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td align='left' ></td>
							<td colspan='32' align='left' ><b>In-House</b></td>
							</tr>";
							$in_house_sub_tot=0;
							foreach ($nameArray_sales_order as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no='';
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no='<strong>P:</strong>'.$row[csf('booking_no')].', <strong>S:</strong>'.$row[csf('job_no')];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else $booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								$style_ref_no=$row[csf('style_ref_no')];
								$job_no=$sales_booking_array[$booking_plan_no]['job_no'];
								$job_year=$sales_booking_array[$booking_plan_no]['year'];


								if(!in_array($row[csf('floor_id')],$sales_floor_array))
								{

									if($i!=1)
									{
										?>
										<tr class="tbl_bottom" title="<? echo $i;?>">
											<td></td>
											<td colspan="17" align="right"><b>Floor Total</b></td>
											<?

											$floor_tot_qnty_row=$floor_tot_roll_row=$floor_roll_tot=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
												$floor_roll_tot=$tot_roll_arr[$key]['roll'];
												$floor_roll_qntys=$tot_roll_qtyshift_arr[$key]['qntys'];
												?>
												<td align="right"><? echo number_format($floor_roll_tot,2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_roll_qntys,2,'.',''); ?></td>
												<?

											}

											?>
											<td align="right"><? echo number_format($no_of_shift_roll,2,'.','');?></td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
											<td align="right"><? echo number_format($sub_floor_qntynoshift,2,'.',''); ?></td>

											<td align="right"><? echo number_format($sub_floor_tot_roll,2,'.',''); ?></td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
											<td align="right"><? echo number_format($floor_tot_Qnty,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>

										</tr>

										<?
										$html.="<tr>

										<td colspan='16' align='right'><b>Floor Total</b></td>";

										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
										<td align='right'>".number_format($noshift_total,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
										<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";

										unset($noshift_total);
										unset($pcsnoshift_total);
										unset($floor_tot_roll);
										unset($no_of_shift_roll);
										unset($sub_floor_tot_roll);
										unset($floor_roll_tot);
										unset($floor_tot_Qnty);
										unset($tot_roll_arr);
										unset($tot_roll_qtyshift_arr);

									}
									if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") $floor_id_show= "Without Floor"; else $floor_id_show = $floor_details[$row[csf('floor_id')]];
									?>
									<tr><td colspan="32" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $floor_id_show; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='28' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>$floor_id_show</b></td></tr>";
									$sales_floor_array[$i]=$row[csf('floor_id')];
								}
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i; ?></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
										<td width="55"><p><? echo $row[csf('machine_name')]; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $floor_details[$row[csf('floor_id')]]; ?></p></td>

										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p><? echo $style_ref_no; ?></p></td>
										<td width="110" id="booking_no_<? echo $i; ?>" align="center"><P><? echo $booking_plan_no; ?></P></td>
										<td width="100" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number')]; ?></P></td>

										<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60" id="yarn_lot_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>

										<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
										<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
										<td width="80" id="mc_gause_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
										<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
										<td width="50" id="stich_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
										<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
										<?
										$html.="<tr>
										<td width='30'>".$i."</td>
										<td width='55'><p>".$knitting_party."&nbsp;</p></td>
										<td width='60'><p>".$row[csf('machine_name')]."</p></td>

										<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
										<td width='100'><p>".$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']."</p></td>


										<td width='110'><P>".$booking_plan_no."</P></td>
										<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>

										<td width='80'><p>".$count."</p></td>
										<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
										<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
										<td width='100'><p>&nbsp;".$color."</p></td>";

										$html.="
										<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
										<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
										<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
										<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
										<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
										<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
										$row_tot_roll=0;
										$row_tot_qnty=0;
										foreach($shift_name as $key=>$val)
										{
											$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
	                                        $tot_roll_arr[$key]['roll']+=$row[csf('roll'.strtolower($val))]; // new
	                                        $tot_roll_qtyshift_arr[$key]['qntys']+=$row[csf('qntyshift'.strtolower($val))]; // new
	                                        $tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

	                                        $source_tot_roll_sales[$key]['roll']+=$row[csf('roll'.strtolower($val))];
	                                        $source_tot_roll_sales[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

	                                        $floor_tot_roll_sales[$key]['roll']+=$row[csf('roll'.strtolower($val))];
	                                        $floor_tot_roll_sales[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

	                                        $row_tot_roll+=$row[csf('roll'.strtolower($val))];
	                                        $row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
	                                        ?>
	                                        <td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>



	                                        <td width="100" align="right" >
	                                        	<?
	                                        	echo number_format($row[csf('qntyshift'.strtolower($val))],2);
	                                        	$machineSamarryDataArr[$machine_lib[$row[csf('machine_id')]]][$key]+=$row[csf('qntyshift'.strtolower($val))];
	                                        	?>
	                                        </td>
	                                        <?

	                                        $html.="<td width='50' align='right' >".$row[csf('roll'.strtolower($val))]."</td>
	                                        <td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
	                                    }
	                                    $sub_floor_no_roll+=$row[csf('rollnoshift')];
	                                    $sub_floor_qntynoshift+=$row[csf('qntynoshift')];
	                                    $sub_floor_tot_roll+=$row_tot_roll;
	                                    $sub_floor_tot_roll_qntynoshift+=$row_tot_qnty+$row[csf('qntynoshift')];
	                                    //$row[csf('rollnoshift')]=150;
	                                    $no_of_shift_roll+= $row[csf('rollnoshift')];


	                                    $in_house_sub_tot+= $row[csf('rollnoshift')];
	                                    $in_house_sub_tot_roll+= $row_tot_roll;

	                                    $floor_tot_Qnty += $row_tot_qnty+$row[csf('qntynoshift')];
	                                     //$row_tot_roll
	                                    ?>
	                                    <td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>
	                                    <td width="50" align="right" id="nopcs_<? echo $i; ?>">0.00<? //echo $row[csf('rollnoshift')]; ?></td>
	                                    <td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
	                                    <td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
	                                    <td width="50" align="right" id="totalnopcs_<? echo $i; ?>">0.00<? //echo $row[csf('rollnoshift')]; ?></td>
	                                    <td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
	                                    <td width="100" align="center"><p><? echo $row[csf('inserted_by')]; ?>&nbsp;</p></td>
	                                    <td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
	                                    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
	                                </tr>
	                            </tbody>
	                            <?
	                            $html.="
	                            <td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
	                            <td width='50' align='right'>".$pcs."</td>
	                            <td width='100' align='right'>".number_format($row[csf('qntynoshift')],2)."</td>

	                            <td width='50' align='right'>".$row_tot_roll."</td>
	                            <td width='50' align='right'>".$totalpcs."</td>
	                            <td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
	                            <td><p>".$row[csf('remarks')]."&nbsp;</p></td>
	                            </tr>
	                            </tbody>";

	                            $grand_tot_roll+=$row_tot_roll;
	                            $grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];

	                            $source_grand_tot_roll+=$row_tot_roll;
	                            $source_grand_tot_qnty+=$row_tot_qnty;

	                            $noshift_total+=$row[csf('qntynoshift')];
	                            $pcsnoshift_total+=$row[csf('pcsnoshift')];

	                            $grand_tot_floor_roll+=$row_tot_roll;
	                            $grand_tot_floor_qnty+=$row_tot_qnty;
	                            $total_qty_noshift+=$row[csf('qntynoshift')];

	                            $i++;$kk++;

	                            $machine_name_arr[$row[csf('machine_name')]] =$row[csf('machine_name')];
	                        }

	                        ?>
	                        <tr class="tbl_bottom">
	                        	<td></td>
	                        	<td colspan="17" align="right"><b>Floor Total</b></td>

	                        	<?
	                        	$floor_tot_qnty_row=$floor_roll_tot=0;
	                        	foreach($shift_name as $key=>$val)
	                        	{
	                        		$floor_tot_qnty_row+=$floor_tot_roll_sales[$key]['qty'];
	                        		$floor_roll_tot=$tot_roll_arr[$key]['roll'];
	                        		$floor_roll_qntys=$tot_roll_qtyshift_arr[$key]['qntys'];
	                        		?>
	                        		<td align="right"><? echo number_format($floor_roll_tot,2,'.',''); ?></td>
	                        		<td align="right"><? echo number_format($floor_roll_qntys,2,'.',''); ?></td>
	                        		<?
	                        	}
	                        	?>
	                        	<td align="right"><? echo number_format($no_of_shift_roll,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($sub_floor_qntynoshift,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($sub_floor_tot_roll,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($floor_tot_Qnty,2,'.',''); ?></td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        </tr>
	                        <tr class="tbl_bottom">
	                        	<td></td>
	                        	<td colspan="17" align="right"><b>In House Total</b></td>
	                        	<?
	                        	$source_tot_qnty_row=$source_tot_qnty=$source_tot_roll_row=0;
	                        	foreach($shift_name as $key=>$val)
	                        	{
	                        		$source_tot_qnty += $source_tot_roll_sales[$key]['qty'];
	                        		$source_tot_qnty_row += $source_tot_roll_sales[$key]['qty'];

	                                // $source_tot_roll += $source_tot_roll_sales[$key]['roll'];
	                        		$source_tot_roll_row += $source_tot_roll_sales[$key]['roll'];
	                        		?>
	                        		<td align="right"><? echo number_format($source_tot_roll_row,2,'.',''); ?></td>
	                        		<td align="right"><? echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
	                        		<?
	                        		unset($source_tot_roll);
	                        		unset($source_tot_roll_row);
	                        	}


	                        	?>
	                        	<td align="right"><? echo number_format($in_house_sub_tot,2,'.','');?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?> </td>
	                        	<td align="right"><? echo number_format($in_house_sub_tot_roll,2,'.','');?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        </tr>
	                        <?
	                        $html.="<tr>
	                        <td colspan='16' align='right'><b>Floor Total</b></td>";

	                        $floor_tot_qnty_row=0;
	                        foreach($shift_name as $key=>$val)
	                        {
	                        	$floor_tot_qnty_row+=$floor_tot_roll_sales[$key]['qty'];

	                        	$html.="<td align='right'>&nbsp;</td>
	                        	<td align='right'>".number_format($floor_tot_roll_sales[$key]['qty'],2,'.','')."</td>";
	                        }
	                        $html.="
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($noshift_total,2,'.','')."</td>
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
	                        <td>&nbsp;</td>
	                        </tr>
	                        <tr>
	                        <td colspan='16' align='right'><b>In House Total</b></td>";
	                        $source_tot_qnty=0;
	                        foreach($shift_name as $key=>$val)
	                        {
	                        	$source_tot_qnty+=$source_tot_roll_sales[$key]['qty'];
	                        	$source_tot_qnty_row+=$source_tot_roll_sales[$key]['qty'];
	                        	$html.="<td align='right'>&nbsp;</td>
	                        	<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";

	                        	unset($source_tot_qnty_row);
	                        }
	                        $html.="
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
	                        <td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
	                        <td>&nbsp;</td>
	                        </tr>";
	                	}
	                    ?>
	                	</table>
	            	</div>
		        </div>
		        <?
	    	}
		}
		//echo "string";die;
		// ========Subcontract Order (In-bound) Knitting Production Data show start==============
		// Data will come from SubCon Module
		if($db_type	==0)
		{
			$order_production_relation = " and b.order_id  = d.id";
		}
		else
		{
			$order_production_relation = " and cast (b.order_id as varchar(4000)) = d.id";
		}
		// echo "string";die;
		if($cbo_type==2 || $cbo_type==0) // Subcontract Order
		{
			$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
			$sql_inhouse_sub="SELECT DISTINCT b.id, a.prefix_no_num, a.product_no, a.product_date, a.prod_chalan_no, a.party_id, c.seq_no, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type, b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id $select_color, b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, $year_sub_field as year, d.order_no, d.cust_style_ref, sum(case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, sum(case when b.shift=0 then b.no_of_roll end ) as rollnoshift,d.job_no_mst";
			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse_sub.=", sum(case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."
				, sum(case when b.shift=$key then b.product_qnty else 0 end ) as qntyshift".strtolower($val);
			}

			$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
			where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2 and d.status_active=1 and d.is_deleted=0
			and a.status_active=1 and a.is_deleted=0  $company_working_cond $cbo_company_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $order_production_relation $location_cond_subcontract
			group by b.id, a.prefix_no_num, a.product_no, a.product_date, a.prod_chalan_no, a.party_id, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,  b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id, b.color_id, b.order_id, c.machine_no, e.job_no_prefix_num, e.insert_date, d.order_no, d.cust_style_ref, c.seq_no,d.job_no_mst order by b.floor_id, a.product_date, c.seq_no";

				//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
			$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
			if (count($nameArray_inhouse_subcon)>0)//for avg.rate
			{
				$order_id_arr = array();
				foreach ($nameArray_inhouse_subcon as $row)
				{
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
				}

				$all_order_id = implode(",", $order_id_arr);
			    $order_id_cond="";
			    if($all_order_id)
			    {
			        $all_order_id = implode(",",array_filter(array_unique(explode(",", $all_order_id))));
			        $order_id_arr = explode(",", $all_order_id);
			        if($db_type==0)
			        {
			            $order_id_cond = " and c.order_id in ($all_order_id )";
			        }
			        else
			        {
			            if(count($order_id_arr)>999)
			            {
			                $order_id_chunk_arr=array_chunk($order_id_arr, 999);
			                $order_id_cond=" and (";
			                foreach ($order_id_chunk_arr as $value)
			                {
			                    $order_id_cond .=" c.order_id in (".implode(",", $value).") or ";
			                }
			                $order_id_cond=chop($order_id_cond,"or ");
			                $order_id_cond.=")";
			            }
			            else
			            {
			                $order_id_cond = " and c.order_id in ($all_order_id )";
			            }
			        }

			        $inbound_rate_sql="SELECT c.job_no_mst, c.item_id, c.gsm, c.grey_dia, c.rate from subcon_ord_breakdown c where status_active=1 and is_deleted=0 $order_id_cond";
					$inbound_rate_data=sql_select($inbound_rate_sql);
					$inbound_rate_arr=array();
					foreach ($inbound_rate_data as $key => $row)
					{
						$inbound_rate_arr[$row[csf('job_no_mst')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('grey_dia')]]=$row[csf('rate')];
					}
					//echo "<pre>";print_r($inbound_rate_arr);
			    }
			}

			if(count($nameArray_inhouse_subcon)>0)
			{
				$tbl_width=1950+count($shift_name)*157;

				?>
				<fieldset style="width:<? echo $tbl_width+220; ?>px;">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:400px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Subcontract Order (In-bound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+200; ?>" class="rpt_table" >
						<thead>
							<tr>
								<th width="30" rowspan="2">SL</th>
								<th width="60" rowspan="2">Receive Challan No</th>
								<th width="60" rowspan="2">M/C No</th>
								<th width="60" rowspan="2">Job No</th>
								<th width="60" rowspan="2">Year</th>
								<th width="70" rowspan="2">Party</th>
								<th width="100" rowspan="2">Style</th>
								<th width="110" rowspan="2">Order No</th>
								<th width="60" rowspan="2">Prod. No</th>
								<th width="80" rowspan="2">Yarn Count</th>
								<th width="90" rowspan="2">Yarn Brand</th>
								<th width="60" rowspan="2">Lot No</th>
								<th width="100" rowspan="2">Fabric Color</th>
								<th width="150" rowspan="2">Fabric Type</th>
								<th width="50" rowspan="2">M/C Dia</th>
								<th width="80" rowspan="2">M/C Gauge</th>
								<th width="50" rowspan="2">Fab. Dia</th>
								<th width="50" rowspan="2">Stitch</th>
								<th width="60" rowspan="2">GSM</th>
								<?
								$html_width = $tbl_width+20;
								$html .= "<fieldset style='width:".$html_width."px;'>
								<div align='left' style=\"background-color:#E1E1E1; color:#000; width:400px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;\"><strong><u><i>Subcontract Order (In-bound) Knitting Production</i></u></strong></div>
								<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" rules=\"all\" width=". $tbl_width ." class=\"rpt_table\" >
								<thead>
								<tr>
								<th width=\"30\" rowspan=\"2\">SL</th>
								<th width=\"60\" rowspan=\"2\">M/C No</th>
								<th width=\"60\" rowspan=\"2\">Job No</th>
								<th width=\"60\" rowspan=\"2\">Year</th>
								<th width=\"70\" rowspan=\"2\">Party</th>
								<th width=\"100\" rowspan=\"2\">Style</th>
								<th width=\"110\" rowspan=\"2\">Order No</th>
								<th width=\"60\" rowspan=\"2\">Prod. No</th>
								<th width=\"80\" rowspan=\"2\">Yarn Count</th>
								<th width=\"90\" rowspan=\"2\">Yarn Brand</th>
								<th width=\"60\" rowspan=\"2\">Lot No</th>
								<th width=\"100\" rowspan=\"2\">Fabric Color</th>
								<th width=\"150\" rowspan=\"2\">Fabric Type</th>
								<th width=\"50\" rowspan=\"2\">M/C Dia</th>
								<th width=\"80\" rowspan=\"2\">M/C Gauge</th>
								<th width=\"50\" rowspan=\"2\">Fab. Dia</th>
								<th width=\"50\" rowspan=\"2\">Stitch</th>
								<th width=\"60\" rowspan=\"2\">GSM</th>";
								foreach($shift_name as $val)
								{
									$html .= "<th width=\"150\" colspan=\"2\">$val</th>";
									?>
									<th width="150" colspan="2"><? echo $val; ?></th>
									<?
								}
								?>
								<th width="150" colspan="2">No Shift</th>
								<th width="150" colspan="2">Total</th>
								<th width="100" rowspan="2">Avg. Rate (Tk)</th>
								<th width="100" rowspan="2">Amount (TK)</th>
								<th width="100" rowspan="2">Insert User</th>
								<th width="100" rowspan="2">Insert Date and Tiime</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<?
								$html .= '<th width="150" colspan="2">No Shift</th>
								<th width="150" colspan="2">Total</th>
								<th rowspan="2">Remarks</th></tr><tr>';
								foreach($shift_name as $val)
								{
									?>
									<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>
									<?
									$html .= '<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>';
								}
								?>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $tbl_width+220; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+200; ?>" class="rpt_table" id="table_body">
							<?
							$html .= '<th width="50" rowspan="2">Roll</th>
							<th width="100" rowspan="2">Qnty</th>
							<th width="50" rowspan="2">Roll</th>
							<th width="100" rowspan="2">Qnty</th>
							</tr>
							</thead>
							</table>
							<div style="width:'. $html_width .'px; overflow-y:scroll; max-height:330px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="'. $tbl_width .'" class="rpt_table" id="table_body">';
							$i=1; $tot_sub_rolla=''; $tot_sub_rollb=''; $tot_sub_rollc=''; $tot_sub_rolla_qnty=0; $tot_sub_rollb_qnty=0; $tot_sub_rollc_qnty=0; $grand_sub_tot_roll=''; $grand_sub_tot_qnty=0; $grand_sub_tot_amount=0;
							$floor_array_subcon=array();$m=0;
							$floor_tot_roll = array();
							foreach ($nameArray_inhouse_subcon as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$inbound_rate=$inbound_rate_arr[$row[csf('job_no_mst')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]];

								$count='';
								$yarn_count=explode(",",$row[csf('yrn_count_id')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}
								if(!in_array($row[csf('floor_id')],$floor_array_subcon))
								{
									if($i!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="19" align="right"><b>Floor Total</b></td>
											<?
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												?>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>

												<?
											}
											?>
											<td align="right"><? echo number_format($total_rollnoshift,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>
											<td align="right"></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_amount,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>

										</tr>

										<?
										$html.="<tr>

										<td colspan='25' align='right'><b>Floor Total</b></td>";

										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";

										}

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($noshift_total,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";

										unset($noshift_total);
										unset($floor_tot_roll);
										unset($total_rollnoshift);
										unset($floor_tot_amount);
									}
									if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") $floor_id_show= "Without Floor"; else $floor_id_show = $floor_details[$row[csf('floor_id')]];
									?>
									<tr><td colspan="38" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $floor_id_show; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='36' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>$floor_id_show</b></td></tr>";
									$floor_array_subcon[$i]=$row[csf('floor_id')];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('prod_chalan_no')]; ?></p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
									<td align="center" width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
									<td align="center" width="60"><p><? echo $row[csf('year')]; ?></p></td>
									<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('party_id')]]; ?></p></td>
									<td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
									<td width="110"><p><? echo $row[csf('order_no')]; ?></p></td>
									<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('prefix_no_num')]; ?></P></td>
									<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
									<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('brand')]; ?></p></td>
									<td width="60" id="yarn_lot_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
									<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
										<?
										$color_arr=array_unique(explode(",",$row[csf('color_id')]));
										$all_color="";
										foreach($color_arr as $id)
										{
											$all_color.=$color_details[$id].",";
										}
										$all_color=chop($all_color," , ");
										echo $all_color;
										?></p></td>
										<td width="150" id="feb_type_<? echo $i; ?>" title="<? echo $row[csf('cons_comp_id')]; ?>"><p><? echo $const_comp_arr[$row[csf('cons_comp_id')]]; ?>&nbsp;</p></td>
										<td width="50" id="mc_dia_<? echo $i; ?>"><p><? echo $row[csf('machine_dia')];?></p></td>
										<td width="80" id="mc_gauge_<? echo $i; ?>"><p><? echo $row[csf('machine_gg')]; ?></p></td>
										<td width="50" id="fab_dia_<? echo $i; ?>"><p><? echo $row[csf('dia_width')]; ?></p></td>
										<td width="50" id="stich_<? echo $i; ?>"><p><? echo $row[csf('stitch_len')]; ?></p></td>
										<td width="60" id="fin_gsm_<? echo $i; ?>"><p><? echo $row[csf('gsm')]; ?></p></td>
										<?
										$html .= '<tr bgcolor="'. $bgcolor.'" onClick="change_color(\'tr_'. $i .'\',\''. $bgcolor .'\')" id="tr_'.$i.'">
										<td width="30">'. $i.'</td>
										<td width="60"><p>&nbsp;'. $row[csf("machine_name")].'</p></td>
										<td align="center" width="60"><p>'. $row[csf("job_no_prefix_num")].'</p></td>
										<td align="center" width="60"><p>'. $row[csf("year")].'</p></td>
										<td width="70" id="buyer_id_'. $i .'"><p>&nbsp;'. $buyer_arr[$row[csf("party_id")]].'</p></td>
										<td width="100"><p>'. $row[csf("cust_style_ref")] .'</p></td>
										<td width="110"><p>'. $row[csf("order_no")].'</p></td>
										<td width="60" id="prod_id_'. $i.'"><P>'. $row[csf("prefix_no_num")].'</P></td>
										<td width="80" id="yarn_count_'. $i.'"><p>'. $count .'&nbsp;</p></td>
										<td width="90" id="brand_id_'. $i.'"><p>&nbsp;'. $row[csf("brand")].'</p></td>
										<td width="60" id="yarn_lot_'. $i.'"><p>&nbsp;'. $row[csf("yarn_lot")].'</p></td>
										<td width="100" id="color_'. $i.'"><p>&nbsp'.$all_color.'</p></td>
										<td width="150" id="feb_type_'. $i.'"><p>'. $const_comp_arr[$row[csf("cons_comp_id")]].'&nbsp;</p></td>
										<td width="50" id="mc_dia_'. $i.'"><p>&nbsp;'. $row[csf('machine_dia')].'</p></td>
										<td width="80" id="mc_gauge_'. $i.'"><p>&nbsp;'. $row[csf('machine_gg')].'</p></td>
										<td width="50" id="fab_dia_'. $i.'"><p>&nbsp;'. $row[csf("dia_width")].'</p></td>
										<td width="50" id="stich_'. $i.'"><p>&nbsp;'. $row[csf("stitch_len")].'</p></td>
										<td width="60" id="fin_gsm_'. $i.'"><p>&nbsp;'. $row[csf("gsm")].'</p></td>';
										$row_sub_tot_roll=0;
										$row_sub_tot_qnty=0;
										foreach($shift_name as $key=>$val)
										{
											$tot_sub_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
											$tot_sub_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

											$source_sub_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
											$source_sub_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

											$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
											$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

											$row_sub_tot_roll+=$row[csf('roll'.strtolower($val))];
											$row_sub_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
											?>
											<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
											<td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); $machineSamarryDataArr[$machine_lib[$row[csf('machine_id')]]][$key]+=$row[csf('qntyshift'.strtolower($val))];
											?></td>
											<?
											$html .= '<td width="50" align="right" >'. $row[csf("roll".strtolower($val))].'</td>
											<td width="100" align="right" >'.number_format($row[csf("qntyshift".strtolower($val))],2).'</td>';
										}
											//$row[csf('rollnoshift')]=150;
										?>
										<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')];
										$total_rollnoshift += $row[csf('rollnoshift')];
										$all_rollnoshift += $row[csf('rollnoshift')];
										?></td>
										<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
										<td width="50" align="right" id="roll_<? echo $i; ?>">&nbsp;<? //echo $row_sub_tot_roll; ?></td>
										<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_sub_tot_qnty+$row[csf('qntynoshift')],2,'.','');
										$sub_tota_qnty=$row_sub_tot_qnty+$row[csf('qntynoshift')]; ?></td>
										<td width="100" align="right"><p><? echo number_format($inbound_rate,2,'.',''); ?></p></td>
										<td width="100" align="right"><p><? $sub_tota_amount=$sub_tota_qnty*$inbound_rate;
										echo number_format($sub_tota_amount,2,'.','');
										$floor_tot_amount += $sub_tota_amount;?></p></td>
										<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
										<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
									</tr>
								</tbody>
								<?
								$html .= '<td width="50" align="right" id="noqty_'. $i.'">'. $row[csf("rollnoshift")].'</td>
								<td width="100" align="right" id="noqty_'. $i.'">'. number_format($row[csf("qntynoshift")],2).'</td>
								<td width="50" align="right" id="roll_'. $i.'">'. $row_sub_tot_roll.'</td>
								<td width="100" align="right" id="qty_'. $i.'">'. number_format($row_sub_tot_qnty+$row[csf("qntynoshift")],2,".","").'</td>
								<td><p>'. $row[csf("remarks")].'&nbsp;</p></td></tr></tbody>';

								$grand_sub_tot_roll+=$row_sub_tot_roll;
								$grand_sub_tot_qnty+=$row_sub_tot_qnty+$row[csf('qntynoshift')];

								$source_sub_grand_tot_roll+=$row_sub_tot_roll;
								$source_sub_grand_tot_qnty+=$row_sub_tot_qnty;

								$noshift_sub_total+=$row[csf('qntynoshift')];

								$grand_sub_tot_floor_roll+=$row_sub_tot_roll;
								$grand_sub_tot_floor_qnty+=$row_sub_tot_qnty;
								$total_sub_qty_noshift+=$row[csf('qntynoshift')];
								$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
								$grand_sub_tot_amount+=$sub_tota_amount;

								$i++;$m++;
							}
							?>
							<tr class="tbl_bottom">
								<td colspan="19" align="right"><b>Floor Total</b></td>
								<?
								$floor_tot_qnty_row=$floor_tot_roll_row=0;
								foreach($shift_name as $key=>$val)
								{
									$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
									$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
									?>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($total_rollnoshift,2,'.',''); ?> </td>
								<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>

								<td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($floor_tot_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tfoot>
								<th colspan="19" align="right">Grand Total</th>
								<?
								$html .= '<tfoot><th colspan="18" align="right">Grand Total</th>';
								foreach($shift_name as $key=>$val)
								{
									?>
									<th align="right"><? echo number_format($tot_sub_roll[$key]['roll'],2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_sub_roll[$key]['qty'],2,'.',''); ?></th>
									<?
									$html .= "<th align='right'>&nbsp;</th>
									<th align='right'>". number_format($tot_sub_roll[$key]['qty'],2,'.','') ."</th>";
								}
								?>
								<th align="right"><? echo number_format($all_rollnoshift,2,'.',''); ?></th>
								<th align="right"><? echo number_format($total_sub_qty_noshift,2,'.',''); ?></th>
								<th align="right"></th>
								<th align="right"><? echo number_format($grand_sub_tot_qnty,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo number_format($grand_sub_tot_amount,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?
				$html .= "<th align='right'>&nbsp;</th>
				<th align='right'>". number_format($total_sub_qty_noshift,2,'.','')."</th>
				<th align='right'>". number_format($grand_sub_tot_roll,2,'.','')."</th>
				<th align='right'>". number_format($grand_sub_tot_qnty,2,'.','')."</th>
				<th>&nbsp;</th>
				</tfoot>
				</table>
				</div>
				</fieldset>";
			}
		}
		?>
		<br>
		<?
		/*echo "<pre>"; print_r($shift_name); echo "</pre>";*/
		// ========Subcontract Order (In-bound) Knitting Production Data show end ==============
		?>

		<!-- Machine wise summary Data Show -->
		<?
		// echo $machine_wise_section.'Test';
		/*echo "<pre>"; print_r($machineSamarryDataArr); echo "</pre>";*/
		if ($machine_wise_section==0)
		{
			?>
			<fieldset style=" width:750px;">
				<h2>Machine wise summary</h2>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="summary_tab">
					<thead>
						<tr>
							<th rowspan="2">SL</th>
							<th rowspan="2">M/C No</th>
							<th rowspan="2">Capacity</th>
							<th colspan="<? echo count($shift_name);?>">SHIFT NAME</th>
							<th rowspan="2" width="80">Shift Total (kg)</th>
							<th rowspan="2" width="80">Capacity Achieve %</th>
							<th rowspan="2" width="80">Yesterday Prod. Qty.</th>
							<th rowspan="2" width="80">Yesterday Capacity Achieve %</th>
						</tr>

						<?
						$html.="
						<br>
						<fieldset style='width:750px;'>
						<h2>Machine wise summary</h2>
						<table class='rpt_table' width='100%' cellpadding='0' cellspacing='0' border='1' rules='all' align='center'>
						<thead>
						<tr>
						<th rowspan='2'>SL</th>
						<th rowspan='2'>M/C No</th>
						<th rowspan='2'>Capacity</th>
						<th colspan=". count($shift_name).">SHIFT NAME</th>
						<th rowspan='2' width='80'>Shift Total (kg)</th>
						<th rowspan='2' width='80'>Capacity Achieve %</th>
						<th rowspan='2' width='80'>Yesterday Prod. Qty.</th>
						<th rowspan='2' width='80'>Yesterday Capacity Achieve %</th>
						</tr>
						<tr>
						";
						?>
						<tr>
							<?
							foreach($shift_name as $key=>$val)
							{
								?>
								<th><? echo $val;?></th>

								<?
								$html.="<th>".$val."</th>";
							}
							?>
						</tr>
					</thead>
					<?
					$html.="
					</tr>
					</thead>
					";

					if($db_type==0)
					{
						$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date)));
					}
					else
					{
						$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date))),'','',1);
					}
					$date_con_2=" and a.receive_date between '$previous_date' and '$previous_date'";

					$ymcpacity_arr=return_library_array( "select d.machine_no,sum(c.quantity) as quantity
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
						where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond group by d.machine_no", "machine_no", "quantity" );


					$ymcpacityWO_arr=return_library_array( "select d.machine_no, sum(b.grey_receive_qnty) as quantity  from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id  and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $location_cond group by d.machine_no", "machine_no", "quantity" );

					$mcpacity_arr=return_library_array( "select machine_no, prod_capacity from lib_machine_name where category_id=1", "machine_no", "prod_capacity"  );

					$i=1;
					ksort($machineSamarryDataArr);
					// echo "<pre>";print_r($machineSamarryDataArr);
					foreach($machineSamarryDataArr as $machine_no=>$row):
						$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sm<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_sm<? echo $i; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $machine_no; ?></td>
							<td align="right"><? echo $mCapacity=$mcpacity_arr[$machine_no]; $totmCapacity+=$mCapacity;?></td>
							<?
							$html.="
							<tr bgcolor='".$bgcolor."' id='tr_sm".$i."'>
							<td align='center'>".$i."</td>
							<td>".$machine_no."</td>
							<td align='right'>".$mcpacity_arr[$machine_no]."</td>
							";
							$totPro=0;
							foreach($row as $key=>$val)
							{
								?>
								<td align="right"><? echo number_format($val,2); $proQty[$key]+=$val;$totPro+=$val;  ?></td>
								<?
								$html.="<td align='right'>".number_format($val,2)."</td>";
							}
							?>
							<td align="right"><? echo number_format($totPro,2); $gTotPro+=$totPro;?></td>
							<td align="right"><? //echo number_format(round(($totPro/$mCapacity)*100),2);?></td>
							<td align="right">
								<?
								$html_sum=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
								echo number_format($html_sum,2);
								$totymc+=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
								?>
							</td>
							<td align="right"><? echo fn_number_format(round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100));?></td>
						</tr>
						<?
						$html.="
						<td align='right'>".number_format($totPro,2)."</td>
						<td align='right'>".number_format(round(($totPro/$mCapacity)*100),2)."</td>
						<td align='right'>".number_format($html_sum,2)."</td>
						<td align='right'>".fn_number_format(round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100),2)."</td>
						</tr>
						";
						$i++;
					endforeach;?>
					<tfoot>
						<th></th>
						<th>Total</th>
						<th><? echo $totmCapacity;?></th>
						<?
						$html.="
						<tfoot>
						<th></th>
						<th>Total</th>
						<th>".$totmCapacity."</th>
						";
						foreach($shift_name as $key=>$val)
						{
							?>
							<th><? echo number_format($proQty[$key],2); ?></th>
							<?
							$html.="<th>".$proQty[$key]."</th>";
						}
						?>
						<th><? echo number_format($gTotPro,2);?></th>
						<th><? echo round(($gTotPro/$totmCapacity)*100);?></th>
						<th><? echo number_format($totymc,2);?></th>
						<th><? echo round(($totymc/$totmCapacity)*100);?></th>
					</tfoot>
				</table>
			</fieldset>
			<?
			$html.="
			<th>".number_format($gTotPro,2)."</th>
			<th>".round(($gTotPro/$totmCapacity)*100)."</th>
			<th>".number_format($totymc,2)."</th>
			<th>".round(($totymc/$totmCapacity)*100)."</th>
			</tfoot>
			</table>
			</fieldset>
			";
		}
		else if ($machine_wise_section==1)
		{
			?>
			<fieldset style=" width:1050px;">
				<h2>Machine wise summary</h2>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="summary_tab">
					<thead>
						<tr>
							<th rowspan="2">SL</th>
							<th rowspan="2">M/C No</th>
							<th rowspan="2">Group,Dia/Width,Gauge</th>
							<th rowspan="2">Capacity</th>
							<th colspan="<? echo count($shift_name)*2;?>">SHIFT NAME</th>
							<th rowspan="2" width="80">Shift Total (kg)</th>
							<th rowspan="2" width="80">Capacity Achieve %</th>
							<th rowspan="2" width="80">Yesterday Prod. Qty.</th>
							<th rowspan="2" width="80">Yesterday Capacity Achieve %</th>
						</tr>
						<?
						$html.="
						<br>
						<fieldset style='width:750px;'>
						<h2>Machine wise summary</h2>
						<table class='rpt_table' width='100%' cellpadding='0' cellspacing='0' border='1' rules='all' align='center'>
						<thead>
						<tr>
						<th rowspan='2'>SL</th>
						<th rowspan='2'>M/C No</th>
						<th rowspan='2'>Capacity</th>
						<th colspan=". (count($shift_name)*2)." width='30'>SHIFT NAME</th>
						<th rowspan='2' width='80'>Shift Total (kg)</th>
						<th rowspan='2' width='80'>Capacity Achieve %</th>
						<th rowspan='2' width='80'>Yesterday Prod. Qty.</th>
						<th rowspan='2' width='80'>Yesterday Capacity Achieve %</th>
						</tr>
						<tr>
						";
						?>
						<tr>
							<?
							foreach($shift_name as $key=>$val)
							{
								$cause='Idle Cause';
								?>
								<th><? echo $val;?></th>
								<th width='200'><? echo $cause;?></th>

								<?
								$html.="<th>".$val."</th>";
								$html.="<th width='300'>".$cause."</th>";
							}
							?>
						</tr>
					</thead>
					<?
					$html.="
					</tr>
					</thead>
					";

					if($db_type==0)
					{
						$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date)));
					}
					else
					{
						$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date))),'','',1);
					}
					$date_con_2=" and a.receive_date between '$previous_date' and '$previous_date'";

					$ymcpacity_arr=return_library_array( "select d.machine_no,sum(c.quantity) as quantity
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
						where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond group by d.machine_no", "machine_no", "quantity" );


					$ymcpacityWO_arr=return_library_array( "select d.machine_no, sum(b.grey_receive_qnty) as quantity  from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id  and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $location_cond group by d.machine_no", "machine_no", "quantity" );
					$mcpacity_arr=return_library_array( "select machine_no, prod_capacity from lib_machine_name where category_id=1 $company_working_cond2", "machine_no", "prod_capacity"  );

					$machineTo_date=$to_date;
					$shift_details=array();
					$shift_data=sql_select("select start_time, end_time,shift_name from shift_duration_entry where status_active=1");
					foreach($shift_data as $row)
					{
						$shift_details[$row[csf('shift_name')]]['from_hr_min']=number_format($row[csf('start_time')],2);
						$shift_details[$row[csf('shift_name')]]['to_hr_min']=number_format($row[csf('end_time')],2);
						//for machine shift date
						if(strtotime($row[csf('start_time')]) >= strtotime($row[csf('end_time')]))
						{
							if($db_type==0)
							{
								$machineTo_date=date('Y-m-d',strtotime('+1 day', strtotime($to_date)));
							}
							else
							{
								$machineTo_date=change_date_format(date('Y-m-d',strtotime('+1 day', strtotime($to_date))),'','',1);
							}
						}
					}
					$mc_no_arr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
					$machine_data=sql_select("select id, machine_no, dia_width, gauge, machine_group from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 $lib_mc_cond");
					$machine_details=array();
					foreach($machine_data as $row)
					{
						$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
						$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
						$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
						$machine_details[$row[csf('id')]]['machine_group']=$row[csf('machine_group')];
						if($row[csf('machine_no')]!="") $total_machine[$row[csf('id')]]=$row[csf('id')];
					}
					unset($machine_data);


					$machine_data=sql_select("select to_date,machine_entry_tbl_id, from_hour, from_minute, to_hour, to_minute, machine_idle_cause from  pro_cause_of_machine_idle where status_active=1 and machine_entry_tbl_id in(1,".implode(",", array_filter($machine_name_arr)).") and from_date between '$from_date' and '$machineTo_date' and to_date between '$from_date' and '$machineTo_date'  ");



					foreach($machine_data as $row)
					{
						if($row[csf('from_hour')]!='')
						{
							$machineID=$row[csf('machine_entry_tbl_id')];
							$fromtime=$row[csf('from_hour')].':'.$row[csf('from_minute')];
							$totime=$row[csf('to_hour')].':'.$row[csf('to_minute')];
							$machine_no = $machine_details[$machineID]['no'];
							$machine_summary_arr[$machine_no]['from_hr_min']=$fromtime;
							$machine_summary_arr[$machine_no]['to_hr_min']=$totime;

							foreach ($shift_name as $key=>$shift)
							{
								$shift_from = strtotime($shift_details[$key]['from_hr_min']);
								$shift_to = strtotime($shift_details[$key]['to_hr_min']);


								/*
								if(strtotime($fromtime)>=$shift_from && strtotime($totime)<=$shift_to)
								{
									$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
								}

								if(strtotime($fromtime)>=$shift_from && strtotime($totime)>=$shift_to && $key==3)
								{
									$machine_idle_cause[$machine_no][3]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
								}
								*/




								if( strtotime($from_date) == strtotime($row[csf('to_date')]))
								{
									if($key==3)
									{
										if(strtotime($totime)>=$shift_from && strtotime($totime)<=$shift_to)
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
										else if(strtotime($totime)>=$shift_from && strtotime($totime)>$shift_to)
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}
									else
									{
										if(strtotime($totime)>=$shift_from && strtotime($totime)<=$shift_to)
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}
								}
								else
								{
									//echo "<pre> fromtime=".$fromtime." ; totime=".$totime."</pre>";
									//echo "<pre> shift_from=".$shift_from." ; shift_to=".$shift_to."</pre>";die;
									if($key==3)
									{
										if(strtotime($totime)<=$shift_from && strtotime($totime)<=$shift_to )
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}else{
										if(strtotime($totime)>=$shift_from && strtotime($totime)<=$shift_to )
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}



								}



							}
						}
					}


					foreach ($machine_details as $key => $value) {
						$machine_dia_gauge = rtrim(implode(",", $value),',');
					}

					$i=1;
					// echo "<pre>";
					// print_r($machineSamarryDataArr);
					// echo "</pre>";
					foreach($machineSamarryDataArr as $machine_no=>$row):
						// $machine_no = $machine_details[$machine_id]['no'];
						$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
						$mc_idle_hr_from = strtotime($machine_summary_arr[$machine_no]['from_hr_min']);
						$mc_idle_hr_to 	 = strtotime($machine_summary_arr[$machine_no]['to_hr_min']);

						$machine_group = $machine_details[$machine_no]['machine_group'];
						$dia_width = ($machine_group!="")?",".$machine_details[$machine_no]['dia_width']:"";
						$gauge = ($machine_group!="" || $dia_width!="")?",".$machine_details[$machine_no]['gauge']:"";

						$machine_dia_gauge = $machine_group."".$dia_width."".$gauge;

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sm<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_sm<? echo $i; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $machine_no; ?></td>
							<td><? echo $machine_dia_gauge; ?></td>
							<td align="right"><? echo $mCapacity=$mcpacity_arr[$machine_no]; $totmCapacity+=$mCapacity;?></td>
							<?
							$html.="
							<tr bgcolor='".$bgcolor."' id='tr_sm".$i."'>
							<td align='center'>".$i."</td>
							<td>".$machine_no."</td>
							<td align='right'>".$mcpacity_arr[$machine_no]."</td>";
							$totPro=0;
							foreach($row as $key=>$val)
							{
								$shift_from_hr_min 	= strtotime($shift_details[$key]['from_hr_min']);
								$shift_to_hr_min  	= strtotime($shift_details[$key]['to_hr_min']);
								?>
								<td align="right" title="<? echo $shift_details[$key]['from_hr_min'].'='.$shift_details[$key]['to_hr_min'];?>">
									<? echo number_format($val,2); $proQty[$key]+=$val;$totPro+=$val;  ?>
								</td>
								<td align="right">
									<?
									$mc_cause="";
									$causes = array_unique($machine_idle_cause[$machine_no][$key]['machine_idle_cause']);
									foreach ($causes as $cause) {
										$mc_cause .= $cause_type[$cause] . ",";
									}
									echo rtrim($mc_cause,", ");
									?>
								</td>
								<?
								$html.="<td align='right'>".$val."</td>";
								$html.="<td align='right'>".$val."</td>";
							}
							?>
							<td align="right"><? echo number_format($totPro,2); $gTotPro+=$totPro;?></td>
							<td align="right"><? echo round(($totPro/$mCapacity)*100);?></td>
							<td align="right">
								<?
								echo  $html_sum=number_format($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no],2);
								$totymc+=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
								?>
							</td>
							<td align="right"><? echo fn_number_format(round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100));?></td>
						</tr>
						<?
						$html.="
						<td align='right'>".$totPro."</td>
						<td align='right'>".round(($totPro/$mCapacity)*100)."</td>
						<td align='right'>".$html_sum."</td>
						<td align='right'>".fn_number_format(round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100))."</td>
						</tr>
						";
						$i++;
					endforeach;
					?>
					<tfoot>
						<th></th>
						<th></th>
						<th>Total</th>
						<th><? echo $totmCapacity;?></th>

						<?
						$html.="
						<tfoot>
						<th></th>
						<th>Total</th>
						<th>".$totmCapacity."</th>
						";
						foreach($shift_name as $key=>$val)
						{
							?>
							<th><? echo number_format($proQty[$key],2); ?></th>
							<th></th>
							<?
							$html.="<th>".number_format($proQty[$key],2)."</th>";
							$html.="<th>".number_format($proQty[$key],2)."</th>";
						}
						?>
						<th><? echo number_format($gTotPro,2);?></th>
						<th><? echo round(($gTotPro/$totmCapacity)*100);?></th>
						<th><? echo number_format($totymc,2);?></th>
						<th><? echo round(($totymc/$totmCapacity)*100);?></th>
					</tfoot>
				</table>
			</fieldset>
			<?
			$html.="
			<th>".$gTotPro."</th>
			<th>".round(($gTotPro/$totmCapacity)*100)."</th>
			<th>".$totymc."</th>
			<th>".round(($totymc/$totmCapacity)*100)."</th>
			</tfoot>
			</table>
			</fieldset>
			";
		}
		unset($machineSamarryDataArr);
		//  Machine wise summary Data End

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
	else if($report_type==2) //Machine Wise
	{

		if($cbo_type==1 || $cbo_type==0)
		{
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_cond=" and f.job_no_prefix_num='$txt_job' "; else $job_cond="";
			if($txt_order!="") $order_cond=" and e.po_number like '%$txt_order%' "; else $order_cond="";
			if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";

			if($db_type==0)
			{
				$year_field="YEAR(f.insert_date)";
				$year_field_sam="YEAR(a.insert_date)";
				$year_sub_field="YEAR(e.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_field="to_char(f.insert_date,'YYYY')";
				$year_field_sam="to_char(a.insert_date,'YYYY')";
				$year_sub_field="to_char(e.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
			}
			else $year_field="";
			$from_date=$txt_date_from;
			if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);

			$date_con="";
			if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
			if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";
			if ($cbo_floor_id!=0) $floor_id_cond=" and b.floor_id='$cbo_floor_id'"; else $floor_id_cond="";
			if (str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and a.party_id=$cbo_buyer_name"; else $buyer_id_cond="";
			if($txt_job!="") $job_no_cond=" and e.job_no_prefix_num='$txt_job' "; else $job_no_cond="";
			if($txt_order!="") $order_no_cond=" and d.order_no like '%$txt_order%' "; else $order_no_cond="";
			$machine_details=array();
			if ($cbo_floor_id==0) $lib_mc_cond=""; else $lib_mc_cond="and floor_id='$cbo_floor_id'";
			if($cbo_company!=0) $lib_mc_cond.=" and company_id in($cbo_company)";
			if($cbo_working_company!=0) $lib_mc_cond.=" and company_id=$cbo_working_company";
			$machine_data=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 $lib_mc_cond");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				if($row[csf('machine_no')]!="") $total_machine[$row[csf('id')]]=$row[csf('id')];
			}
			unset($machine_data);

			$composition_arr=$construction_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
				}
			}
			unset($data_array);
			$knit_plan_arr=array();
			$plan_data=sql_select("select id, color_range, stitch_length, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
			foreach($plan_data as $row)
			{
				$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')];
				$knit_plan_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
				$knit_plan_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
			}
			unset($plan_data);
		}
		$tbl_width=1870+count($shift_name)*100;
		ob_start();
		?>
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:12px" ><strong><? if(str_replace("'","",$txt_date_from)!="") echo "From ".str_replace("'","",$txt_date_from); if(str_replace("'","",$txt_date_to)!="") echo " To ".str_replace("'","",$txt_date_to); ?></strong></td>
			</tr>
		</table>

		<table cellspacing="0" cellpadding="0"  rules="all" width="1300px">
			<tr>
				<td width="645">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:640px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table" >
						<thead>
							<tr>
								<th colspan="7">Knit Production Summary (In-House + Outbound)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Buyer</th>
								<th width="90">Inhouse</th>
								<th width="90">Outbound-Subcon(With Order)</th>
								<th width="90">Sample Without Order(Inhouse)</th>
								<th width="90">Sample Without Order(Outbond)</th>
								<th width="100">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:660px; overflow-y:scroll; max-height:220px;" id="scroll_body1">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table" >
							<tbody>
								<?

								$buyer_alldata_arr=array();
								$sql_sample_samary=sql_select("select a.buyer_id, sum(case when  b.machine_no_id>0 $floor_id  then b.grey_receive_qnty end ) as qtySamWithoutOrderIshouse
									from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id $cbo_company_cond $company_working_cond and a.knitting_source like '$source'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $date_con $floor_id $buyer_cond group by a.buyer_id ");
								foreach($sql_sample_samary as $inf)
								{
									$buyer_alldata_arr[$inf[csf('buyer_id')]]['qtySamWithoutOrderIshouse'] += $inf[csf('qtySamWithoutOrderIshouse')];
								}
								unset($sql_sample_samary);

								//=============================== outbound subcon without order ==========================
								$sql_sample_sam_out="SELECT a.buyer_id, a.booking_no, sum(case when a.booking_without_order=1 and b.machine_no_id=0 $floor_id  then b.grey_receive_qnty end ) as qtySamWithoutOrderOutbond from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4 $cbo_company_cond $company_working_cond and a.knitting_source=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(1) $date_con $floor_id $buyer_cond $location_cond group by a.buyer_id,a.booking_no ";
								// echo $sql_sample_sam_out;
								$sql_sample_samary_out=sql_select( $sql_sample_sam_out);
								foreach($sql_sample_samary_out as $inf)
								{
									$booking_no=explode("-",$inf[csf('booking_no')]);
									$without_booking_no=$booking_no[1];
									if($without_booking_no=='SMN')
									{
										$buyer_alldata_arr[$inf[csf('buyer_id')]]['qtySamWithoutOrderOutbond'] += $inf[csf('qtySamWithoutOrderOutbond')];
									}
								}
								unset($sql_sample_samary_out);
								//echo '<pre>';print_r($subcon_smn_buyer_samary);
								$sql_service_samary=sql_select("select a.buyer_id, sum(b.grey_receive_qnty) as service_qty	from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id $cbo_company_cond $company_working_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_con $buyer_cond $location_cond group by a.buyer_id");
								foreach($sql_service_samary as $row)
								{
									$buyer_alldata_arr[$row[csf('buyer_id')]]['service_qty'] += $row[csf('service_qty')];
								}
								unset($sql_service_samary);

								$sql_qtybookingWithoutOrder="Select a.buyer_id, sum(case when b.machine_no_id>0 $floor_id  then b.grey_receive_qnty end ) as qtybookingWithoutOrder from inv_receive_master a, pro_grey_prod_entry_dtls b where a.item_category=13 and a.id=b.mst_id and a.entry_form=2 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $buyer_cond $job_year_cond $location_cond group by a.buyer_id";
								$sql_qtybookingWithoutOrder_res = sql_select($sql_qtybookingWithoutOrder);

								foreach ($sql_qtybookingWithoutOrder_res as $value)
								{
									$buyer_alldata_arr[$value[csf("buyer_id")]]['qtybookingWithoutOrder'] = $value[csf("qtybookingWithoutOrder")];
								}
								unset($sql_qtybookingWithoutOrder_res);
								//echo '<pre>';print_r($bookingWithoutOrder_buyer_data);
								$sql_qty="Select a.buyer_id, sum(case when a.knitting_source=1 and b.machine_no_id>0 $floor_id  then c.quantity end ) as qtyinhouse, sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_cond $job_cond $order_cond $job_year_cond $location_cond group by a.buyer_id ";
								$sql_result=sql_select( $sql_qty);
								//$buyer_data_arr=array();
								foreach ($sql_result as $row)
								{
									$buyer_alldata_arr[$row[csf("buyer_id")]]['qtyinhouse'] = $row[csf("qtyinhouse")];
									$buyer_alldata_arr[$row[csf("buyer_id")]]['qtyoutbound'] = $row[csf("qtyoutbound")];
								}
								unset($sql_result);

								$k=1;
								$total_summ_outwithout = 0;
								foreach($buyer_alldata_arr as $buyer_id => $rows)
								{
								   if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								   $out_bound_qnty=0;
								   $out_bound_qnty=$rows['qtyoutbound']+$rows['service_qty'];
								   $tot_summ = $rows['qtyinhouse']+$rows['qtyoutbound']+$rows['qtySamWithoutOrderIshouse']+$rows['qtySamWithoutOrderOutbond'];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td width="40"><? echo $k; ?></td>
										<td width="100"><? echo $buyer_arr[$buyer_id]; ?></td>
										<td width="90" align="right"><? echo number_format($rows['qtyinhouse'],2,'.',''); ?>&nbsp;</td>
										<td width="90" align="right"><? echo number_format($out_bound_qnty,2,'.',''); ?>&nbsp;</td>
										<td width="90" align="right"><? echo number_format($rows['qtySamWithoutOrderIshouse'],2,'.',''); ?>&nbsp;</td>
										<td width="90" align="right"><? echo number_format($rows['qtySamWithoutOrderOutbond'],2,'.',''); ?>&nbsp;</td>
										<td width="100" align="right"><? echo  number_format($tot_summ,2,'.',''); ?>&nbsp;</td>
									</tr>
									<?
									$tot_qtyinhouse += $rows['qtyinhouse'];
									$tot_qtyoutbound += $out_bound_qnty;
									$total_summ += $tot_summ;
									$tot_samwithout_order_ishouse += $rows['qtySamWithoutOrderIshouse'];
									$tot_samwithout_order_outbond += $rows['qtySamWithoutOrderOutbond'];
									//unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);
									$k++;
								}
								/*if(count($subcon_buyer_samary)>0)
								{
									foreach($subcon_buyer_samary as $key=>$value)
									{
									   if ($k%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="100"><? echo $buyer_arr[$key]; ?></td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right"><? echo $subcon_smn_buyer_samary[$key];?></td>
											<td width="90" align="right"><? echo number_format($value+$subcon_smn_buyer_samary[$key],2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="100" align="right"><? echo number_format($value+$subcon_smn_buyer_samary[$key],2,'.',''); ?>&nbsp;</td>
										</tr>
									<?
										$total_summ += $value+$subcon_smn_buyer_samary[$key];
										$total_summ_outwithout += $subcon_smn_buyer_samary[$key];
										$k++;
									}
								}*/
								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_qtyoutbound+$total_summ_outwithout,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_samwithout_order_ishouse,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_samwithout_order_outbond,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
								</tr>
								<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtyoutbound_per=(($tot_qtyoutbound+$total_summ_outwithout)/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtySamWithoutOrderInhouse_per=($tot_samwithout_order_ishouse/$total_summ)*100; echo number_format($qtySamWithoutOrderInhouse_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtySamWithoutOrderOutbond_per=($tot_samwithout_order_outbond/$total_summ)*100; echo number_format($qtySamWithoutOrderOutbond_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? echo "100 %"; ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
				<td width="30">&nbsp;</td>
				<td width="675" style="padding-right:20px">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:670px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Floor Wise Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670px" class="rpt_table" >
						<thead>
							<tr>
								<th colspan="8">Floor Wise Knit Production Summary (In-House + Outbound)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Floor Name</th>
								<th width="80">Inhouse</th>
								<th width="90">Inbound-Subcon</th>
								<th width="90">Outbound-Subcon</th>
								<th width="90">Sample Without Order(Inhouse)</th>
								<th width="90">Sample Without Order(Outbond)</th>
								<th width="90">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:690px; overflow-y:scroll; max-height:220px;" id="scroll_body1">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670px" class="rpt_table" >
							<tbody>
								<?

								$sql_sample_floor_samary=sql_select("select b.floor_id, sum(case when  b.machine_no_id>0  then b.grey_receive_qnty end ) as sample_qty
									from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id $cbo_company_cond $company_working_cond and a.knitting_source like '$source'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $date_con $floor_id $buyer_cond group by b.floor_id ");
								$subcon_floor_samary=array();
								foreach($sql_sample_floor_samary as $inf)
								{
									$subcon_floor_samary[$inf[csf('floor_id')]]+= $inf[csf('sample_qty')];
									$subcon_floor_samary_total['total']+= $inf[csf('sample_qty')];
								}
								//echo "<pre>";
								//print_r($subcon_floor_samary);die;
								unset($sql_sample_floor_samary);
								// subcon inbound start here ------------------------------------------------------------------------------------
								$sql_inhouse_sub_summ="select b.floor_id, sum(b.product_qnty) as qntysubshift
								from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
								where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2
								and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.id=b.order_id $sub_company_cond $company_working_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond
								group by b.floor_id";
									//echo $sql_inhouse_sub_summ;die; //$subcompany_working_cond
								$nameArray_inhouse_subcon_summ=sql_select( $sql_inhouse_sub_summ);
								$inhouse_subcon_floor_data=array();
								foreach($nameArray_inhouse_subcon_summ as $row)
								{
									$inhouse_subcon_floor_data[$row[csf("floor_id")]]=$row[csf("qntysubshift")];
									$inhouse_subcon_floor_total['total']+=$row[csf("qntysubshift")];
								}
								unset($nameArray_inhouse_subcon_summ);
								//print_r($inhouse_subcon_floor_data);die;
								// subcon inbound finish here --------------------

								$sql_service_floor_samary=sql_select("select b.floor_id, sum(b.grey_receive_qnty) as service_qty
									from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id $cbo_company_cond $company_working_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_con $buyer_cond $floor_id $location_cond group by b.floor_id");
								$service_floor_data=array();
								foreach($sql_service_floor_samary as $row)
								{
									$service_floor_data[$row[csf("floor_id")]]=$row[csf("service_qty")];
								}
								unset($sql_service_floor_samary);


								$sql_sample_sam_out="SELECT a.buyer_id,a.floor, a.booking_no, sum(case when a.booking_without_order=1 and b.machine_no_id=0 $floor_id  then b.grey_receive_qnty end ) as sample_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4 $cbo_company_cond $company_working_cond and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(1) $date_con $floor_id $buyer_cond $location_cond group by a.buyer_id,a.floor,a.booking_no";
								//echo $sql_sample_sam_out;
								$sql_sample_samary_out=sql_select( $sql_sample_sam_out);
								$flr_summary_arr=array();
								foreach($sql_sample_samary_out as $inf)
								{
									$booking_no=explode("-",$inf[csf('booking_no')]);
									$without_booking_no=$booking_no[1];
									if($without_booking_no=='SMN')
									{
										$flr_summary_arr[$inf[csf('floor')]]['smn_out'] += $inf[csf('sample_qty')];
									}
								}
								unset($sql_sample_samary_out);
								//echo '<pre>';print_r($flr_summary_arr);

								$sql_floor_qty="Select b.floor_id, sum(case when a.knitting_source=1 and b.machine_no_id>0  then c.quantity end ) as qtyinhouse, sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_cond $job_cond $order_cond $job_year_cond $floor_id $location_cond group by b.floor_id ";
								$k=1;
								$tot_summ=0;
								$total_summ=0;
								$tot_qtyinhouse=0;
								$tot_inhouse_subcon_floor_data=0;
								$tot_qtyoutbound=0;
								$tot_outbond_subcon=0;
								$sql_floor_result=sql_select( $sql_floor_qty);
								foreach($sql_floor_result as $rows)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$out_bound_qnty=0;
									$out_bound_qnty = $rows[csf('qtyoutbound')]+$service_floor_data[$rows[csf('floor_id')]];
									$tot_summ = $rows[csf('qtyinhouse')]+$out_bound_qnty+$subcon_floor_samary[$rows[csf('floor_id')]]+$inhouse_subcon_floor_data[$rows[csf('floor_id')]]+$flr_summary_arr[$rows[csf('floor_id')]]['smn_out'];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td width="40"><? echo $k; ?></td>
										<td width="100"><? echo $floor_details[$rows[csf('floor_id')]]; ?></td>
										<td width="80" align="right"><? echo number_format($rows[csf('qtyinhouse')],2,'.',''); ?>&nbsp;</td>
										<td width="90" align="right"><? echo number_format($inhouse_subcon_floor_data[$rows[csf('floor_id')]],2,'.',''); ?>&nbsp;</td>
										<td width="90" align="right"><? echo number_format($out_bound_qnty,2,'.',''); ?>&nbsp;</td>
										<td width="90" align="right"><? echo number_format($subcon_floor_samary[$rows[csf('floor_id')]],2,'.','');  ?>&nbsp;</td>
										<td width="90" align="right"><? echo number_format($flr_summary_arr[$rows[csf('floor_id')]]['smn_out'],2,'.','');  ?>&nbsp;</td>
										<td width="90" align="right"><? echo  number_format($tot_summ,2,'.',''); ?>&nbsp;</td>
									</tr>
									<?
									$tot_qtyinhouse += $rows[csf('qtyinhouse')];
									$tot_inhouse_subcon_floor_data += $inhouse_subcon_floor_data[$rows[csf('floor_id')]];
									$tot_qtyoutbound += $out_bound_qnty;
									$tot_outbond_subcon += $subcon_floor_samary[$rows[csf('floor_id')]];
									$total_summ += $tot_summ;
									$tot_withoutOrder_outbond += $flr_summary_arr[$rows[csf('floor_id')]]['smn_out'];

									//unset($inhouse_subcon_floor_data[$rows[csf('floor_id')]]);
									//unset($subcon_floor_samary[$rows[csf('floor_id')]]);
									$k++;
								}
								//echo count($flr_summary_arr).'system';
								if(count($flr_summary_arr)>0)
								{
									foreach($flr_summary_arr as $key=>$value)
									{

										if ($k%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="100"><? echo $floor_details[$key]; ?></td>
											<td width="80" align="right">&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right"><? echo number_format($value['smn_out'],2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right"><? echo number_format($value['smn_out'],2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$total_summ+=$value['smn_out'];
										$total_withoutOrder_outbond = $tot_withoutOrder_outbond + $value['smn_out'];
										$k++;
									}
								}
								/*if(count($subcon_floor_samary)>0)
								{
									foreach($subcon_floor_samary as $key=>$value)
									{
										if ($k%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
										?>
										<!-- <tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="100"><? echo $floor_details[$key]; ?></td>
											<td width="80" align="right">&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
										</tr> -->
										<?
										/*$total_summ+=$value;
										$k++;
									}
								}*/

								/*if(count($inhouse_subcon_floor_data)>0)
								{
									foreach($inhouse_subcon_floor_data as $key=>$value)
									{
										if ($k%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="100"><? echo $floor_details[$key]; ?></td>
											<td width="80" align="right">&nbsp;</td>
											<td width="90" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>
											<td width="90" align="right">&nbsp;</td>

											<td width="90" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$total_summ+=$value;
										$k++;
									}
								}*/
								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_inhouse_subcon_floor_data,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_outbond_subcon,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($total_withoutOrder_outbond,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
								</tr>
								<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtyinhouse_subcon_per=($tot_inhouse_subcon_floor_data/$total_summ)*100; echo number_format($qtyinhouse_subcon_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtyoutbond_subcon_per=($tot_outbond_subcon/$total_summ)*100; echo number_format($qtyoutbond_subcon_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qty_withoutOrder_outbond_per=($total_withoutOrder_outbond/$total_summ)*100; echo number_format($qty_withoutOrder_outbond_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? echo "100 %"; ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<br />
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_head" >
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="90" rowspan="2">M/C No.</th>
						<th width="70" rowspan="2">M/C Brand </th>
						<th width="70" rowspan="2">Production Date</th>
						<th width="60" rowspan="2">M/C Dia &  Gauge</th>
						<th width="70" rowspan="2">Unit  Name</th>
						<th width="70" rowspan="2">Buyer</th>
						<th width="100" rowspan="2">Program/ Booking No</th>
						<th width="70" rowspan="2">File No.</th>
						<th width="70" rowspan="2">Ref No.</th>
						<th width="70" rowspan="2">Yarn Count</th>
						<th width="80" rowspan="2">Brand</th>
						<th width="80" rowspan="2">Lot</th>
						<th width="100" rowspan="2">Construction</th>
						<th width="150" rowspan="2">Composition</th>
						<th width="130" rowspan="2">Color</th>
						<th width="100" rowspan="2">Color Range</th>
						<th width="60" rowspan="2">Stitch</th>
						<th width="60" rowspan="2">GSM</th>
						<th  colspan="<? echo count($shift_name); ?>">Production</th>
						<th width="100" rowspan="2">Shift Total</th>
						<th width="100" rowspan="2">Machine Total</th>
						<th width="80" rowspan="2">Reject Qty</th>
						<th rowspan="2"> Remarks</th>
					</tr>
					<tr>
						<?
						$ship_count=0;
						foreach($shift_name as $val)
						{
							$ship_count++;
							?>
							<th width="80"><? echo $val; ?></th>
							<?
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
					<tbody>
						<?
						//$plan_booking_arr=return_library_array( "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id", "booking_no");
						$prog_sql=sql_select("select b.id, a.booking_no,b.is_sales,a.within_group from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
						foreach($prog_sql as $row)
						{
							$plan_booking_arr[$row[csf('id')]]=$row[csf('booking_no')];
							$plan_booking_sales_arr[$row[csf('id')]]['is_sales']=$row[csf('is_sales')];
							$plan_booking_sales_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
						}
						$i=1;
						if($db_type==0)
						{
							$sql_inhouse="select a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, group_concat(a.remarks) as remarks, group_concat(b.id) as dtls_id, group_concat(b.prod_id) as prod_id, group_concat(b.febric_description_id) as febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width, group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, group_concat(b.stitch_length) as stitch_length, group_concat(b.brand_id) as brand_id, b.machine_no_id,d.brand as mc_brand, b.floor_id as floor_id,  group_concat(b.color_id) as color_id,  group_concat(b.color_range_id) as color_range_id, group_concat(c.po_breakdown_id) as po_breakdown_id, d.seq_no, d.machine_no as machine_name, group_concat(e.po_number) as po_number, group_concat(e.file_no) as file_no,group_concat(e.grouping) as grouping,sum(distinct b.reject_fabric_receive) as reject_qty";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
							where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no
							order by a.receive_date,d.seq_no,  b.floor_id";

						}
						else
						{
							/*$sql_inhouse="SELECT a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,  listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, d.seq_no, d.machine_no as machine_name,d.brand as mc_brand, listagg((cast(e.po_number as varchar2(4000))),',') within group (order by e.po_number) as po_number, listagg((cast(e.file_no as varchar2(4000))),',') within group (order by e.file_no) as file_no, listagg((cast(e.grouping as varchar2(4000))),',') within group (order by e.grouping) as grouping,sum(distinct b.reject_fabric_receive) as reject_qty";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
							where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no
							order by a.receive_date, d.seq_no, b.floor_id";*/

							$sql_inhouse="SELECT a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, a.remarks, b.id as dtls_id, b.prod_id, b.febric_description_id,  b.gsm, b.width, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, b.floor_id as floor_id, b.color_id, b.color_range_id, c.po_breakdown_id, d.seq_no, d.machine_no as machine_name,d.brand as mc_brand, e.po_number, e.file_no, e.grouping, sum(distinct b.reject_fabric_receive) as reject_qty";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f 
							where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond
							group by a.receive_basis, a.receive_date, a.booking_no, a.remarks, b.id, b.prod_id, b.febric_description_id,  b.gsm, b.width, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, b.floor_id, b.color_id, b.color_range_id, c.po_breakdown_id, d.seq_no, d.machine_no,d.brand, e.po_number, e.file_no, e.grouping, b.machine_no_id, b.floor_id, d.seq_no, d.machine_no 
							order by a.receive_date, d.seq_no, b.floor_id";
						}

						if($db_type==0)
						{
							$sql_wout_order="select a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, group_concat(a.remarks) as remarks, group_concat(b.id) as dtls_id, group_concat(b.prod_id) as prod_id, group_concat(b.febric_description_id) as febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width, group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, group_concat(b.stitch_length) as stitch_length, group_concat(b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id,  group_concat(b.color_id) as color_id,  group_concat(b.color_range_id) as color_range_id, d.seq_no, d.machine_no as machine_name,d.brand as mc_brand,sum(distinct b.reject_fabric_receive) as reject_qty";
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order.=",sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d
							where a.id=b.mst_id and b.machine_no_id=d.id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $date_con $floor_id  $buyer_cond $location_cond
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no
							order by  a.receive_date, d.seq_no, b.floor_id";

						}
						else
						{
							$sql_wout_order="select a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,  listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, d.seq_no, b.machine_no_id,sum(distinct b.reject_fabric_receive) as reject_qty, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, d.machine_no as machine_name,d.brand as mc_brand";
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order.=",sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d
							where a.id=b.mst_id and b.machine_no_id=d.id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $date_con $floor_id  $buyer_cond $location_cond
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no
							order by a.receive_date, d.seq_no, b.floor_id";
						}

						$yarn_type_arr=return_library_array( "select id, yarn_type from product_details_master where item_category_id=13", "id", "yarn_type");

						$nameArray_inhouse=sql_select( $sql_inhouse);
						$nameArray_without_order=sql_select( $sql_wout_order);
						$machine_inhouse_array=$total_running_machine=array();
						foreach ($nameArray_inhouse as $row)
						{
							//$machine_inhouse_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
							$total_running_machine[$row[csf('machine_no_id')]]=$row[csf('machine_no_id')];
							foreach($shift_name as $key=>$val)
							{
								$machine_inhouse_qty[$row[csf('machine_no_id')]]+=$row[csf('qntyshift'.strtolower($val))];
							}

							$ref_str=$row[csf('receive_basis')].'*'.$row[csf('booking_no')].'*'.$row[csf('machine_no_id')].'*'.$row[csf('brand')].'*'.$row[csf('floor_id')].'*'.$row[csf('seq_no')];
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['buyer_id']=$row[csf('buyer_id')];
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['remarks'].=$row[csf('remarks')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['dtls_id'].=$row[csf('dtls_id')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['prod_id'].=$row[csf('prod_id')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['febric_description_id'].=$row[csf('febric_description_id')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['gsm'].=$row[csf('gsm')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['width'].=$row[csf('width')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['yarn_lot'].=$row[csf('yarn_lot')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['yarn_count'].=$row[csf('yarn_count')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['stitch_length'].=$row[csf('stitch_length')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['brand_id'].=$row[csf('brand_id')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['color_id'].=$row[csf('color_id')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['color_range_id'].=$row[csf('color_range_id')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['po_breakdown_id'].=$row[csf('po_breakdown_id')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['machine_name'].=$row[csf('machine_name')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['mc_brand'].=$row[csf('mc_brand')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['po_number'].=$row[csf('po_number')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['file_no'].=$row[csf('file_no')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['grouping'].=$row[csf('grouping')].',';
							$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['reject_qty']+=$row[csf('reject_qty')];
							foreach($shift_name as $key=>$val)
							{
								$inhouseDataArr[$row[csf('receive_date')]][$ref_str]['qntyshift'.strtolower($val)]+=$row[csf('qntyshift'.strtolower($val))];
							}
						}
						// echo '<pre>';print_r($inhouseDataArr);die;
						
						$machine_without_array=$machine_without_qty=array();
						foreach ($nameArray_without_order as $row)
						{
							$machine_without_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
							$total_running_machine[$row[csf('machine_no_id')]]=$row[csf('machine_no_id')];
							foreach($shift_name as $key=>$val)
							{
								$machine_without_qty[$row[csf('machine_no_id')]]+=$row[csf('qntyshift'.strtolower($val))];
							}
						}

						// For rowspan Machine Total
						foreach ($inhouseDataArr as $recvDate=>$recvDateArr)
						{
							foreach($recvDateArr as $str_ref=>$row)
							{
								$str_ref_arr = explode("*", $str_ref);
                                $receive_basis=$str_ref_arr[0];
                                $booking_no=$str_ref_arr[1];
                                $machine_no_id=$str_ref_arr[2];
								$machine_inhouse_array[$machine_no_id][$recvDate]++;
							}
						}

						if($cbo_type==1 || $cbo_type==0)
						{
							if (count($nameArray_inhouse)>0)
							{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_count+23; ?>" align="left" ><b>In-House</b></td>
								</tr>
								<?
								$km=0;$tot_reject_qty=0;
								foreach ($inhouseDataArr as $recvDate=>$recvDateArr)
								{
									foreach($recvDateArr as $str_ref=>$row)
									{
										$str_ref_arr = explode("*", $str_ref);
		                                $receive_basis=$str_ref_arr[0];
		                                $booking_no=$str_ref_arr[1];
		                                $machine_no_id=$str_ref_arr[2];
		                                $brand=$str_ref_arr[3];
		                                $floor_id=$str_ref_arr[4];
		                                $seq_no=$str_ref_arr[5];

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$count='';
										$yarn_count=array_unique(explode(",",$row['yarn_count']));
										foreach($yarn_count as $count_id)
										{
											if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
										}
										$booking_plan_no='';
										if($receive_basis==2)
										{
											$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
											$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
										}
										else
										{
											$booking_plan_no=$booking_no;
											$machine_dia_gage=$machine_details[$machine_no_id]['dia_width']." X ".$machine_details[$machine_no_id]['gauge'];
										}


										?>

										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">

											<?

											if($temp_arr[$machine_no_id][$recvDate]=="" )
											{
												$km++;
												?>
												<td width="30" align="center"><? echo $km; ?></td>
												<td width="90" align="center"><p><? echo $row[csf('machine_name')]; ?></p></td>
												<?
											}
											else
											{
												?>
												<td width="30" align="center"></td>
												<td width="90" align="center"></td>
												<?
											}
											?>
											<td width="70" align="center"><p><? echo $mc_brand; ?></p></td>
											<td width="70" align="center"><p><? if($recvDate!="" && $recvDate!="0000-00-00") echo change_date_format($recvDate); ?>&nbsp;</p></td>
											<td width="60"  align="center"><p><? echo $machine_dia_gage; ?></p></td>
											<td width="70" align="center"><? echo $floor_details[$floor_id]; ?></td>
											<td width="70" align="center"><? echo $buyer_arr[$row['buyer_id']]; ?></td>
											<td width="100" align="center"><? echo $booking_plan_no; ?></td>
											<td width="70" align="center"><p><? echo implode(",",array_unique(explode(",",$row[('file_no')]))); ?></p></td>
											<td width="70"  align="center"><p>&nbsp;<? echo implode(",",array_unique(explode(",",$row[('grouping')]))); ?></p></td>
											<td width="70" align="center"><p><? echo $count; ?></p></td>
											<td width="80"><P>
												<?
												$brand_arr=array_unique(explode(",",$row[('brand_id')]));
												$all_brand="";
												foreach($brand_arr as $id)
												{
													$all_brand.=$brand_details[$id].",";
												}
												$all_brand=chop($all_brand," , ");
												echo $all_brand; 
												?>&nbsp;</P>
											</td>
											<td width="80" align="center"><P><? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
											</td>
											<td width="100"><P>
												<?
												$description_arr=array_unique(explode(",",$row[('febric_description_id')]));
												$all_construction="";
												foreach($description_arr as $id)
												{
													$all_construction.=$construction_arr[$id].",";
												}
												$all_construction=chop($all_construction," , ");
												echo $all_construction;
												?>&nbsp;</P>
											</td>
											<td width="150"><P>
												<?
												$all_composition="";
												foreach($description_arr as $id)
												{
													$all_composition.=$composition_arr[$id].",";
												}
												$all_composition=chop($all_composition," , ");
												echo $all_composition;
												?>&nbsp;</P>
											</td>
											<td width="130"><P>
												<?
												$color_arr=array_unique(explode(",",$row[('color_id')]));
												$all_color="";
												foreach($color_arr as $id)
												{
													$all_color.=$color_details[$id].",";
												}
												$all_color=chop($all_color," , ");
												echo $all_color; 
												?>&nbsp;</P>
											</td>
											<td width="100"><P>
												<?
												$color_range_arr=array_unique(explode(",",$row[('color_range_id')]));
												$all_color_range="";
												foreach($color_range_arr as $id)
												{
													$all_color_range.=$color_range[$id].",";
												}
												$all_color_range=chop($all_color_range," , ");
												echo $all_color_range;
												?>&nbsp;</P>
											</td>
											<td width="60"  align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('stitch_length')]))); ?>&nbsp;</p></td>
											<td width="60"  align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
											<?
											$row_tot_roll=0; 
											$row_tot_qnty=0; 
											foreach($shift_name as $key=>$val)
											{
												$row_tot_qnty+=$row[('qntyshift'.strtolower($val))]; 
												?>
												<td width="80" align="right" ><? echo number_format($row[('qntyshift'.strtolower($val))],2); ?> </td>
												<?
												$grand_total_ship[$key]+=$row[('qntyshift'.strtolower($val))];
												$inhouse_ship[$key]+=$row[('qntyshift'.strtolower($val))];
											}
											?>
											<td width="100" align="right" ><? echo number_format($row_tot_qnty,2,'.',''); ?></td>
											<?
											if($temp_arr[$machine_no_id][$recvDate]=="" )
											{
												$temp_arr[$machine_no_id][$recvDate]=$recvDate;
												?>
												<td width="100" valign="top" align="right" rowspan="<? echo $machine_inhouse_array[$machine_no_id][$recvDate]; ?>"><? echo number_format($machine_inhouse_qty[$machine_no_id],2,'.',''); ?></td>
												<?
												$grand_machine_total+=$machine_inhouse_qty[$machine_no_id];
												$machine_total_inhouser+=$machine_inhouse_qty[$machine_no_id];
											}

											?>
											<td width="80" align="right"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
											</td>
											<td><p><? echo $row[('remarks')]; ?>&nbsp;</p></td>
										</tr>

										<?
										$inhouse_tot_qty+=$row_tot_qnty;
										$grand_tot_qnty+=$row_tot_qnty;
										$grand_reject_qty+=$row[('reject_qty')];
										$i++;
									}
								}

								?>
								<tr class="tbl_bottom">
									<td colspan="19" align="right"><b>In-house Total(with order)</b></td>
									<?
									foreach($shift_name as $key=>$val)
									{
										?>
										<td align="right"><? echo number_format($inhouse_ship[$key],2,'.',''); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($inhouse_tot_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($machine_total_inhouser,2,'.',''); ?></td>
									<td align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
									<td>&nbsp;</td>
								</tr>
								<?
							}
						}

						if($cbo_type==2 || $cbo_type==0)
						{
							if (count($nameArray_without_order)>0)
							{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_count+23; ?>" align="left" ><b>Sample Without Order</b></td>
								</tr>
								<?
								$tot_reject_qty=0;$machine_total_non_order=0;
								foreach ($nameArray_without_order as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$count='';
									$yarn_count=array_unique(explode(",",$row[csf('yarn_count')]));
									foreach($yarn_count as $count_id)
									{
										if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
									}
									$booking_plan_no='';$within_group_id=0;
									if($row[csf('receive_basis')]==2)
									{
										$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_no')]];
										$machine_dia_gage=$knit_plan_arr[$row[csf('booking_no')]]['machine_dia']." X ".$knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
										$is_sales=$plan_booking_sales_arr[$row[csf('booking_no')]]['is_sales'];
										if($is_sales==1)
										{
										$within_group_id=$plan_booking_sales_arr[$row[csf('booking_no')]]['within_group'];
										}
									}
									else
									{
										$booking_plan_no=$row[csf('booking_no')];
										$machine_dia_gage=$machine_details[$row[csf('machine_no_id')]]['dia_width']." X ".$machine_details[$row[csf('machine_no_id')]]['gauge'];
									}

									?>

									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<?
										if($temp_ono_order_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=="")
										{
											$j++;
											?>
											<td width="30" align="center" valign="top"><? echo $j; ?></td>
											<td width="90" valign="top" align="center" rowspan="<? echo $machine_without_array[$row[csf('machine_no_id')]]; ?>"><p><? echo $row[csf('machine_name')]; ?></p>
											</td>
											<?
										}
										else
										{
											?>
											<td width="30" align="center"></td>
											<td width="90" align="center"></td>
											<?
										}
										?>

										<td width="70" align="center"><p><? echo $row[csf('mc_brand')];?></p></td>
										<td width="70" align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
										<td width="60" align="center"><p><? echo $machine_dia_gage; ?></p></td>
										<td width="70" align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
										<td width="70" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
										<td width="100" align="center"><? echo $booking_plan_no; ?></td>
										<td width="70" title="<? if($within_group_id==2) echo "WithinGroup=No";else echo " ";?>"><p><?
										if($within_group_id==2) echo " ";
										else echo implode(",",array_unique(explode(",",$row[csf('file_no')]))); ?></p>
										</td>
										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<?
										if($within_group_id==2) echo " ";
										else echo implode(",",array_unique(explode(",",$row[csf('grouping')]))); ?></p></td>
										<td width="70"><p><? echo $count; ?></p></td>
										<td width="80"><P>
											<?
											$brand_arr=array_unique(explode(",",$row[csf('brand_id')]));
											$all_brand="";
											foreach($brand_arr as $id)
											{
												$all_brand.=$brand_details[$id].",";
											}
											$all_brand=chop($all_brand," , ");
											echo $all_brand;
											?>&nbsp;</P>
										</td>
										<td width="80" style="max-width:80px"><P><? echo implode(",",array_unique(explode(",",$row[csf('yarn_lot')]))); ?>&nbsp;</P>
										</td>
										<td width="100"><P>
											<?
											$description_arr=array_unique(explode(",",$row[csf('febric_description_id')]));
											$all_construction="";
											foreach($description_arr as $id)
											{
												$all_construction.=$construction_arr[$id].",";
											}
											$all_construction=chop($all_construction," , ");
											echo $all_construction;
											?>&nbsp;</P>
										</td>
										<td width="150"><P>
											<?
											$all_composition="";
											foreach($description_arr as $id)
											{
												$all_composition.=$composition_arr[$id].",";
											}
											$all_composition=chop($all_composition," , ");
											echo $all_composition;
											?>&nbsp;</P>
										</td>
										<td width="130"><P>
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></P>
										</td>
										<td width="100"><? echo $color_range[$row[csf('color_range_id')]];?></td>
										<td width="60" style="max-width:60px"><p><? echo  implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?>&nbsp;</p>
										</td>
										<td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('gsm')])));?>&nbsp;</p>
										</td>
										<?
										$row_tot_roll=0;
										$row_tot_qnty=0; $row_tot_qnty_non_order=0;
										foreach($shift_name as $key=>$val)
										{
											$row_tot_qnty_non_order+=$row[csf('qntyshift'.strtolower($val))];
											?>
											<td width="80" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); ?> </td>
											<?
											$grand_total_ship[$key]+=$row[csf('qntyshift'.strtolower($val))];
											$inhouse_ship_non_order[$key]+=$row[csf('qntyshift'.strtolower($val))];
										}
										?>
										<td width="100" align="right" ><? echo number_format($row_tot_qnty_non_order,2,'.',''); ?>
										</td>
										<?
										if($temp_ono_order_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=="")
										{
											$temp_ono_order_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=$row[csf('machine_no_id')];
											?>
											<td width="100" valign="top" align="right" rowspan="<? echo $machine_without_array[$row[csf('machine_no_id')]]; ?>"><? echo number_format($machine_without_qty[$row[csf('machine_no_id')]],2,'.',''); ?></td>
											<?
											$grand_machine_total+=$machine_without_qty[$row[csf('machine_no_id')]];
											$machine_total_non_order+=$machine_without_qty[$row[csf('machine_no_id')]];
										}
										?>
										<td width="80" align="right"><p><? echo number_format($row[csf('reject_qty')],2,'.',''); ?></p>
										</td>
										<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
									</tr>
									<?
									$inhouse_tot_qty_non_order+=$row_tot_qnty_non_order;
									$grand_tot_qnty+=$row_tot_qnty_non_order;
									$grand_reject_qty+=$row[csf('reject_qty')];

									$i++;
								}

								?>
								<tr class="tbl_bottom">
									<td colspan="19" align="right"><b>In-house Total(without order)</b></td>
									<?
									foreach($shift_name as $key=>$val)
									{
										?>
										<td align="right"><? echo number_format($inhouse_ship_non_order[$key],2,'.',''); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($inhouse_tot_qty_non_order,2,'.',''); ?></td>
									<td align="right"><? echo number_format($machine_total_non_order,2,'.',''); ?></td>
									<td align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
									<td>&nbsp;</td>
								</tr>
								<?
							}
						}
						$j=0;

						?>
					</tbody>
				</table>
			</div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="rpt_tbl_footer">
				<tfoot>
					<tr>
						<th width="30" >&nbsp;</th>
						<th width="90" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="60" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="150" >&nbsp;</th>
						<th width="130" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th colspan="2">Grand Total</th>
						<?
						foreach($shift_name as $key=>$val)
						{
							?>
							<th align="right" width="80"><? echo number_format($grand_total_ship[$key],2,'.',''); ?></th>
							<?
						}
						?>
						<th align="right" width="100"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
						<th align="right" width="100"><? echo number_format($grand_machine_total,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></th>
						<th width="95">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
			<br />
			<?
			if($db_type	==0)
			{
				$order_production_relation = " and b.order_id  = d.id";
			}
			else
			{
				$order_production_relation = " and cast (b.order_id as varchar(4000)) = d.id";
			}
			if($cbo_type==2 || $cbo_type==0)
			{
				$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
				$sql_inhouse_sub="select DISTINCT b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, c.seq_no, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type, b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id, b.color_id, b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, $year_sub_field as year, d.order_no, d.cust_style_ref, sum(case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, sum(case when b.shift=0 then b.no_of_roll end ) as rollnoshift";
				foreach($shift_name as $key=>$val)
				{
					$sql_inhouse_sub.=", sum(case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."
					, sum(case when b.shift=$key then b.product_qnty else 0 end ) as qntyshift".strtolower($val);
				}


				$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
				where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2 and d.status_active=1 and d.is_deleted=0
				and a.status_active=1 and a.is_deleted=0  $company_working_cond $cbo_company_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $order_production_relation
				group by b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,  b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id, b.color_id, b.order_id, c.machine_no, e.job_no_prefix_num, e.insert_date, d.order_no, d.cust_style_ref, c.seq_no order by b.floor_id, a.product_date, c.seq_no";

						//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
				$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);

				if(count($nameArray_inhouse_subcon)>0)
				{
					$tbl_width=1690+count($shift_name)*157;

					?>
					<fieldset style="width:<? echo $tbl_width+220; ?>px;">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:400px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Subcontract Order (In-bound) Knitting Production D</i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+200; ?>" class="rpt_table" >
							<thead>
								<tr>
									<th width="30" rowspan="2">SL</th>
									<th width="60" rowspan="2">M/C No</th>
									<th width="60" rowspan="2">Job No</th>
									<th width="60" rowspan="2">Year</th>
									<th width="70" rowspan="2">Party</th>
									<th width="100" rowspan="2">Style</th>
									<th width="110" rowspan="2">Order No</th>
									<th width="60" rowspan="2">Prod. No</th>
									<th width="80" rowspan="2">Yarn Count</th>
									<th width="90" rowspan="2">Yarn Brand</th>
									<th width="60" rowspan="2">Lot No</th>
									<th width="100" rowspan="2">Fabric Color</th>
									<th width="150" rowspan="2">Fabric Type</th>
									<th width="50" rowspan="2">M/C Dia</th>
									<th width="80" rowspan="2">M/C Gauge</th>
									<th width="50" rowspan="2">Fab. Dia</th>
									<th width="50" rowspan="2">Stitch</th>
									<th width="60" rowspan="2">GSM</th>
									<?
									$html_width = $tbl_width+20;
									$html .= "<fieldset style='width:".$html_width."px;'>
									<div align='left' style=\"background-color:#E1E1E1; color:#000; width:400px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;\"><strong><u><i>Subcontract Order (In-bound) Knitting Production</i></u></strong></div>
									<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" rules=\"all\" width=". $tbl_width ." class=\"rpt_table\" >
									<thead>
									<tr>
									<th width=\"30\" rowspan=\"2\">SL</th>
									<th width=\"60\" rowspan=\"2\">M/C No</th>
									<th width=\"60\" rowspan=\"2\">Job No</th>
									<th width=\"60\" rowspan=\"2\">Year</th>
									<th width=\"70\" rowspan=\"2\">Party</th>
									<th width=\"100\" rowspan=\"2\">Style</th>
									<th width=\"110\" rowspan=\"2\">Order No</th>
									<th width=\"60\" rowspan=\"2\">Prod. No</th>
									<th width=\"80\" rowspan=\"2\">Yarn Count</th>
									<th width=\"90\" rowspan=\"2\">Yarn Brand</th>
									<th width=\"60\" rowspan=\"2\">Lot No</th>
									<th width=\"100\" rowspan=\"2\">Fabric Color</th>
									<th width=\"150\" rowspan=\"2\">Fabric Type</th>
									<th width=\"50\" rowspan=\"2\">M/C Dia</th>
									<th width=\"80\" rowspan=\"2\">M/C Gauge</th>
									<th width=\"50\" rowspan=\"2\">Fab. Dia</th>
									<th width=\"50\" rowspan=\"2\">Stitch</th>
									<th width=\"60\" rowspan=\"2\">GSM</th>";
									foreach($shift_name as $val)
									{
										$html .= "<th width=\"150\" colspan=\"2\">$val</th>";
										?>
										<th width="150" colspan="2"><? echo $val; ?></th>
										<?
									}
									?>
									<th width="150" colspan="2">No Shift</th>
									<th width="150" colspan="2">Total</th>
									<th width="100" rowspan="2">Insert User</th>
									<th width="100" rowspan="2">Insert Date and Tiime</th>
									<th rowspan="2">Remarks</th>
								</tr>
								<tr>
									<?
									$html .= '<th width="150" colspan="2">No Shift</th>
									<th width="150" colspan="2">Total</th>
									<th rowspan="2">Remarks</th></tr><tr>';
									foreach($shift_name as $val)
									{
										?>
										<th width="50" rowspan="2">Roll</th>
										<th width="100" rowspan="2">Qnty</th>
										<?
										$html .= '<th width="50" rowspan="2">Roll</th>
										<th width="100" rowspan="2">Qnty</th>';
									}
									?>
									<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>
									<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>
								</tr>
							</thead>
						</table>
						<div style="width:<? echo $tbl_width+220; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+200; ?>" class="rpt_table" id="table_body">
								<?
								$html .= '<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
								</tr>
								</thead>
								</table>
								<div style="width:'. $html_width .'px; overflow-y:scroll; max-height:330px;" id="scroll_body">
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="'. $tbl_width .'" class="rpt_table" id="table_body">';
								$i=1; $tot_sub_rolla=''; $tot_sub_rollb=''; $tot_sub_rollc=''; $tot_sub_rolla_qnty=0; $tot_sub_rollb_qnty=0; $tot_sub_rollc_qnty=0; $grand_sub_tot_roll=''; $grand_sub_tot_qnty=0;
								$floor_array_subcon=array();$m=0;
								foreach ($nameArray_inhouse_subcon as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$total_running_machine[$row[csf('machine_id')]]=$row[csf('machine_id')];
									$count='';
									$yarn_count=explode(",",$row[csf('yrn_count_id')]);
									foreach($yarn_count as $count_id)
									{
										if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
									}
									if(!in_array($row[csf('floor_id')],$floor_array_subcon))
									{
										if($i!=1)
										{
											?>
											<tr class="tbl_bottom">
												<td colspan="18" align="right"><b>Floor Total</b></td>
												<?
												$floor_tot_qnty_row=0;
												foreach($shift_name as $key=>$val)
												{
													$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
													?>
													<td align="right">&nbsp;</td>
													<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>

													<?
												}
												?>
												<td align="right">&nbsp;</td>
												<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>
												<td align="right">&nbsp;</td>
												<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>

											</tr>

											<?
											$html.="<tr>

											<td colspan='25' align='right'><b>Floor Total</b></td>";

											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												$html.="<td align='right'>&nbsp;</td>
												<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";

											}

											$html.="
											<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($noshift_total,2,'.','')."</td>
											<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
											<td>&nbsp;</td>
											</tr>";

											unset($noshift_total);
											unset($floor_tot_roll);
										}
										if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") $floor_id_show= "Without Floor"; else $floor_id_show = $floor_details[$row[csf('floor_id')]];
										?>
										<tr><td colspan="38" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $floor_id_show; ?></b></td></tr>
										<?
										$html.="<tr><td colspan='36' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>$floor_id_show</b></td></tr>";
										$floor_array_subcon[$i]=$row[csf('floor_id')];
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i; ?></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
										<td align="center" width="60" ><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
										<td align="center" width="60"><p><? echo $row[csf('year')]; ?></p></td>
										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('party_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
										<td width="110"><p><? echo $row[csf('order_no')]; ?></p></td>
										<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('prefix_no_num')]; ?></P></td>
										<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('brand')]; ?></p></td>
										<td width="60" id="yarn_lot_<? echo $i; ?>" style="max-width:80px"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $const_comp_arr[$row[csf('cons_comp_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p><? echo $row[csf('machine_dia')];?></p></td>
											<td width="80" id="mc_gauge_<? echo $i; ?>"><p><? echo $row[csf('machine_gg')]; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p><? echo $row[csf('dia_width')]; ?></p></td>
											<td width="50" id="stich_<? echo $i; ?>" style="max-width:50px"><p><? echo $row[csf('stitch_len')]; ?></p></td>
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p><? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html .= '<tr bgcolor="'. $bgcolor.'" onClick="change_color(\'tr_'. $i .'\',\''. $bgcolor .'\')" id="tr_'.$i.'">
											<td width="30">'. $i.'</td>
											<td width="60"><p>&nbsp;'. $row[csf("machine_name")].'</p></td>
											<td align="center" width="60"><p>'. $row[csf("job_no_prefix_num")].'</p></td>
											<td align="center" width="60"><p>'. $row[csf("year")].'</p></td>
											<td width="70" id="buyer_id_'. $i .'"><p>&nbsp;'. $buyer_arr[$row[csf("party_id")]].'</p></td>
											<td width="100"><p>'. $row[csf("cust_style_ref")] .'</p></td>
											<td width="110"><p>'. $row[csf("order_no")].'</p></td>
											<td width="60" id="prod_id_'. $i.'"><P>'. $row[csf("prefix_no_num")].'</P></td>
											<td width="80" id="yarn_count_'. $i.'"><p>'. $count .'&nbsp;</p></td>
											<td width="90" id="brand_id_'. $i.'"><p>&nbsp;'. $row[csf("brand")].'</p></td>
											<td width="60" id="yarn_lot_'. $i.'"><p>&nbsp;'. $row[csf("yarn_lot")].'</p></td>
											<td width="100" id="color_'. $i.'"><p>&nbsp'.$all_color.'</p></td>
											<td width="150" id="feb_type_'. $i.'"><p>'. $const_comp_arr[$row[csf("cons_comp_id")]].'&nbsp;</p></td>
											<td width="50" id="mc_dia_'. $i.'"><p>&nbsp;'. $row[csf('machine_dia')].'</p></td>
											<td width="80" id="mc_gauge_'. $i.'"><p>&nbsp;'. $row[csf('machine_gg')].'</p></td>
											<td width="50" id="fab_dia_'. $i.'"><p>&nbsp;'. $row[csf("dia_width")].'</p></td>
											<td width="50" id="stich_'. $i.'"><p>&nbsp;'. $row[csf("stitch_len")].'</p></td>
											<td width="60" id="fin_gsm_'. $i.'"><p>&nbsp;'. $row[csf("gsm")].'</p></td>';
											$row_sub_tot_roll=0;
											$row_sub_tot_qnty=0;
											foreach($shift_name as $key=>$val)
											{
												$tot_sub_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_sub_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

												$source_sub_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_sub_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

												$row_sub_tot_roll+=$row[csf('roll'.strtolower($val))];
												$row_sub_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
												?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); $machineSamarryDataArr[$row[csf('machine_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
												?></td>
												<?
												$html .= '<td width="50" align="right" >'. $row[csf("roll".strtolower($val))].'</td>
												<td width="100" align="right" >'.number_format($row[csf("qntyshift".strtolower($val))],2).'</td>';
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>">&nbsp;<? //echo $row_sub_tot_roll; ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_sub_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
											<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
									</tbody>
									<?
									$html .= '<td width="50" align="right" id="noqty_'. $i.'">'. $row[csf("rollnoshift")].'</td>
									<td width="100" align="right" id="noqty_'. $i.'">'. number_format($row[csf("qntynoshift")],2).'</td>
									<td width="50" align="right" id="roll_'. $i.'">'. $row_sub_tot_roll.'</td>
									<td width="100" align="right" id="qty_'. $i.'">'. number_format($row_sub_tot_qnty+$row[csf("qntynoshift")],2,".","").'</td>
									<td><p>'. $row[csf("remarks")].'&nbsp;</p></td></tr></tbody>';

									$grand_sub_tot_roll+=$row_sub_tot_roll;
									$grand_sub_tot_qnty+=$row_sub_tot_qnty+$row[csf('qntynoshift')];

									$source_sub_grand_tot_roll+=$row_sub_tot_roll;
									$source_sub_grand_tot_qnty+=$row_sub_tot_qnty;

									$noshift_sub_total+=$row[csf('qntynoshift')];

									$grand_sub_tot_floor_roll+=$row_sub_tot_roll;
									$grand_sub_tot_floor_qnty+=$row_sub_tot_qnty;
									$total_sub_qty_noshift+=$row[csf('qntynoshift')];

									$i++;$m++;
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="18" align="right"><b>Floor Total</b></td>
									<?
									$floor_tot_qnty_row=0;
									foreach($shift_name as $key=>$val)
									{
										$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
										<?
									}
									?>
									<td align="right">&nbsp;</td>
									<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>
									<td align="right">&nbsp;</td>
									<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>

								</tr>
								<tfoot>
									<th colspan="18" align="right">Grand Total</th>
									<?
									$html .= '<tfoot><th colspan="18" align="right">Grand Total</th>';
									foreach($shift_name as $key=>$val)
									{
										?>
										<th align="right">&nbsp;</th>
										<th align="right"><? echo number_format($tot_sub_roll[$key]['qty'],2,'.',''); ?></th>
										<?
										$html .= "<th align='right'>&nbsp;</th>
										<th align='right'>". number_format($tot_sub_roll[$key]['qty'],2,'.','') ."</th>";
									}
									?>
									<th align="right">&nbsp;</th>
									<th align="right"><? echo number_format($total_sub_qty_noshift,2,'.',''); ?></th>
									<th align="right">&nbsp;</th>
									<th align="right"><? echo number_format($grand_sub_tot_qnty,2,'.',''); ?></th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
								</tfoot>
							</table>
						</div>
					</fieldset>
					<?
					$html .= "<th align='right'>&nbsp;</th>
					<th align='right'>". number_format($total_sub_qty_noshift,2,'.','')."</th>
					<th align='right'>". number_format($grand_sub_tot_roll,2,'.','')."</th>
					<th align='right'>". number_format($grand_sub_tot_qnty,2,'.','')."</th>
					<th>&nbsp;</th>
					</tfoot>
					</table>
					</div>
					</fieldset>";
				}
			}
			?>
			<br>


			<?
			//print_r($total_running_machine);die;
			//-----------------------------------------------------------------------------------------
			if($txt_date_from!="")
			{
				if($txt_date_to=="") $txt_date_to=$txt_date_from;
				$date_distance=datediff("d",$txt_date_from, $txt_date_to);
				$month_name=date('F',strtotime($txt_date_from));
				$year_name=date('Y',strtotime($txt_date_from));
				$day_of_month=explode("-",$txt_date_from);
				if($db_type==0)
				{
					$fist_day_of_month=$day_of_month[2]*1;
				}
				else
				{
					$fist_day_of_month=$day_of_month[0]*1;
				}
				$tot_machine=count($total_machine);
				$running_machine=count($total_running_machine);
				$stop_machine=$tot_machine-$running_machine;
				$running_machine_percent=(($running_machine/$tot_machine)*100);
				$stop_machine_percent=(($stop_machine/$tot_machine)*100);
				if($date_distance==1 && $fist_day_of_month>1)
				{
					$query_cond_month=date('m',strtotime($txt_date_from));
					$query_cond_year=date('Y',strtotime($txt_date_from));
					$sql_cond="";
					if($db_type==0) $sql_cond="  and month(a.receive_date)='$query_cond_month' and year(a.receive_date)='$query_cond_year'"; else $sql_cond="  and to_char(a.receive_date,'mm')='$query_cond_month' and to_char(a.receive_date,'yyyy')='$query_cond_year'";
					if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
					$sql_montyly_inhouse=sql_select("select sum(c.quantity ) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date<'".$txt_date_from."' $sql_cond");


					$sql_monthly_wout_order=sql_select("select sum( b.grey_receive_qnty) as qnty  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 and a.receive_date<'".$txt_date_from."' $sql_cond $location_cond");

					$yesterday_prod=$sql_montyly_inhouse[0][csf("qnty")]+$sql_monthly_wout_order[0][csf("qnty")];
					$today_prod=$yesterday_prod+$grand_tot_qnty;
				}
				?>
				<table width="<? echo $tbl_width; ?>">
					<tr>
						<td width="25%"  valign="top">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
								<tr>
									<td>Total number of m/c running</td>
									<td width="100" align="right"><? echo $running_machine; ?></td>
									<td align="right" width="100"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
								</tr>
								<tr>
									<td>Total number of m/c stop</td>
									<td align="right"><? echo $stop_machine; ?></td>
									<td align="right"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
								</tr>
								<tr>
									<td>Total production</td>
									<td align="right"><? echo number_format($grand_tot_qnty+$grand_sub_tot_qnty,2); ?></td>
									<td align="center">Kg</td>
								</tr>
							</table>
						</td>
						<td width="10%"  valign="top">&nbsp; </td>
						<td  width="25%" valign="top">
							<?
							if($date_distance==1 && $fist_day_of_month>1)
							{
								?>
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
									<tr>
										<td>Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td  align="right" width="100"><? echo number_format($yesterday_prod,2); ?></td>
										<td align="center" width="100">Kg</td>
									</tr>
									<tr>
										<td>Upto today production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td align="right"><? echo number_format($today_prod,2); ?> </td>
										<td align="center">Kg</td>
									</tr>
								</table>
								<?
							}
							?>
						</td>
						<td  valign="top">&nbsp; </td>
					</tr>
				</table>
				<?
			}
			?>

		</fieldset>
		<br>
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
	else if($report_type==3) //Machine Wise 2
	{

		if($cbo_type==1 || $cbo_type==0)
		{
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_cond=" and f.job_no_prefix_num='$txt_job' "; else $job_cond="";
			if($txt_order!="") $order_cond=" and e.po_number like '%$txt_order%' "; else $order_cond="";
			if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";

			if($db_type==0)
			{
				$year_field="YEAR(f.insert_date)";
				$year_field_sam="YEAR(a.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
				$year_sub_field="YEAR(e.insert_date)";
				if($cbo_year!=0) $job_year_sub_cond=" and YEAR(e.insert_date)=$cbo_year";  else $job_year_sub_cond="";

			}
			else if($db_type==2)
			{
				$year_field="to_char(f.insert_date,'YYYY')";
				$year_field_sam="to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";

				$year_sub_field="to_char(e.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_sub_cond=" and to_char(e.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";

			}
			else $year_field="";
			$from_date=$txt_date_from;
			if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);

			$date_con="";
			if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
			$from_date=$txt_date_from;
			if($txt_date_to=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";

			if ($cbo_floor_id!=0) $floor_id_cond=" and b.floor_id='$cbo_floor_id'"; else $floor_id_cond="";
			if (str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and a.party_id=$cbo_buyer_name"; else $buyer_id_cond="";

			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_no_cond=" and e.job_no_prefix_num='$txt_job' "; else $job_no_cond="";
			if($txt_order!="") $order_no_cond=" and d.order_no like '%$txt_order%' "; else $order_no_cond="";

			$machine_details=array();
			if ($cbo_floor_id==0) $lib_mc_cond=""; else $lib_mc_cond="and floor_id='$cbo_floor_id'";
			$machine_data=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 $lib_mc_cond");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				if($row[csf('machine_no')]!="") $total_machine[$row[csf('id')]]=$row[csf('id')];
			}
			unset($machine_data);

			$composition_arr=$construction_arr=array();
			$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
				}
			}
			unset($data_array);

			$knit_plan_arr=array();
			$plan_data=sql_select("select id, color_range, stitch_length, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
			foreach($plan_data as $row)
			{
				$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')];
				$knit_plan_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
				$knit_plan_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
			}
			unset($plan_data);
		}
		$tbl_width=1380+count($shift_name)*100;
		ob_start();
		?>
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+16; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+16; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+16; ?>" class="form_caption" style="font-size:12px" ><strong><? if(str_replace("'","",$txt_date_from)!="") echo "From ".str_replace("'","",$txt_date_from); if(str_replace("'","",$txt_date_to)!="") echo " To ".str_replace("'","",$txt_date_to); ?></strong></td>
			</tr>
		</table>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300px">
			<?
			$yarn_type_arr=return_library_array( "select id, yarn_type from product_details_master where item_category_id=13", "id", "yarn_type");
			$plan_booking_arr=return_library_array( "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id", "booking_no");
			$i=1;

			// $cbo_booking_type=118;
			if($cbo_booking_type > 0)
			{
				if($cbo_booking_type == 89){ // SM
					$entry_form_cond = " and g.booking_type = 4";
				}
				else
				{
					$entry_form_cond = " and g.entry_form=$cbo_booking_type";
				}
			}
			else
			{
				$entry_form_cond = "";
			}

			// Bulk
			// unionn all for Booking Type condition as per discuss with tofael vai
			$sql_inhouse="SELECT a.recv_number,a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.buyer_id as buyer_id, a.remarks as remarks, b.id as dtls_id, b.prod_id as prod_id, b.febric_description_id as feb_desc_id, b.gsm as gsm, b.width as width, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.machine_no_id as mac_id,d.brand as mc_brand, b.floor_id as floor_id,  b.color_id as color_id, c.po_breakdown_id as po_id, d.seq_no, d.machine_no as machine_name, e.po_number as po_number,b.reject_fabric_receive as reject_qty,b.shift_name,c.quantity as shift_qty";

			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse.=", (case when b.shift_name=$key then c.quantity else 0 end ) as shift".strtolower($val);
			}
			$sql_inhouse.=" from wo_booking_mst g, ppl_planning_info_entry_mst h, ppl_planning_info_entry_dtls i, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e, wo_po_details_master f
			where g.booking_no=h.booking_no and h.id=i.mst_id and i.id=a.booking_id and g.booking_type in(1,4) and a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 $knit_source_cond $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond and a.receive_basis=2 $entry_form_cond and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0
			union all ";

			$sql_inhouse.="SELECT a.recv_number,a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.buyer_id as buyer_id, a.remarks as remarks, b.id as dtls_id, b.prod_id as prod_id, b.febric_description_id as feb_desc_id, b.gsm as gsm, b.width as width, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.machine_no_id as mac_id,d.brand as mc_brand, b.floor_id as floor_id,  b.color_id as color_id, c.po_breakdown_id as po_id, d.seq_no, d.machine_no as machine_name, e.po_number as po_number,b.reject_fabric_receive as reject_qty,b.shift_name,c.quantity as shift_qty";

			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse.=", (case when b.shift_name=$key then c.quantity else 0 end ) as shift".strtolower($val);
			}
			$sql_inhouse.=" from WO_BOOKING_MST g, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e, wo_po_details_master f
			where g.id=a.booking_id and a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 $knit_source_cond $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond and a.receive_basis=1 $entry_form_cond and g.status_active=1 and g.is_deleted=0 order by receive_date, seq_no, floor_id";
			// echo $sql_inhouse;die;

			// Non Order
			if($cbo_booking_type == 0 || $cbo_booking_type == 90)
			{
				$sql_non_order="SELECT a.recv_number,a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.buyer_id as buyer_id, a.remarks as remarks, b.id as dtls_id, b.prod_id as prod_id, b.febric_description_id as feb_desc_id, b.gsm as gsm, b.width as width, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.machine_no_id as mac_id,d.brand as mc_brand, b.floor_id as floor_id,  b.color_id as color_id,d.seq_no, d.machine_no as machine_name, b.reject_fabric_receive as reject_qty,b.shift_name,b.grey_receive_qnty as shift_qty";
				foreach($shift_name as $key=>$val)
				{
					$sql_non_order.=", (case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as shift".strtolower($val);
				}
				$sql_non_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d,wo_non_ord_samp_booking_mst f
				where a.id=b.mst_id and b.machine_no_id=d.id and f.booking_no=a.booking_no  and a.entry_form=2 and a.item_category=13  and a.booking_without_order=1  $knit_source_cond $cbo_company_cond $company_working_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $date_con $floor_id $buyer_cond   order by a.receive_date,d.seq_no,  b.floor_id";
			}
			// echo $sql_non_order;die;

			// SubCon
			if($db_type	==0) $order_production_relation = " and b.order_id  = d.id";
			else $order_production_relation = " and cast (b.order_id as varchar(4000)) = d.id";
			$sql_inhouse_sub="SELECT a.knitting_source,b.id, a.prefix_no_num, a.product_no as booking_no, a.product_date as receive_date, a.party_id as buyer_id, c.seq_no, a.remarks,a.inserted_by,a.insert_date, b.gsm, b.dia_width as width, b.dia_width_type, b.yarn_lot, b.yrn_count_id as yarn_count, b.stitch_len as stitch_length, b.brand as brand_id, b.machine_id as mac_id,b.machine_dia,b.machine_gg,b.cons_comp_id as feb_desc_id,b.fabric_description as fab_desc,  b.floor_id , b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, d.order_no, d.cust_style_ref, (case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, (case when b.shift=0 then b.no_of_roll end ) as rollnoshift,b.reject_qnty";
			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse_sub.=", (case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."
				, (case when b.shift=$key then b.product_qnty else 0 end ) as shift".strtolower($val);
			}

			$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
			where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2 and d.status_active=1 and d.is_deleted=0
			and a.status_active=1 and a.is_deleted=0  $knit_source_cond $company_working_cond $cbo_company_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $order_production_relation
			order by b.machine_id,c.seq_no,  b.floor_id";
			// echo $sql_inhouse_sub;
			//=============================== outbound subcon smn without order ==========================
			// Non Order
			if($cbo_booking_type == 0 || $cbo_booking_type == 90)
			{
				$sql_sample_sam_out="SELECT a.buyer_id, a.booking_no,a.floor, sum(case when a.booking_without_order=1 and b.machine_no_id=0 $floor_id  then b.grey_receive_qnty end ) as sample_qty
				from inv_receive_master a, pro_grey_prod_entry_dtls b
				where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4 $cbo_company_cond $company_working_cond and a.knitting_source=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(1) $date_con $floor_id $buyer_cond
				group by a.buyer_id,a.booking_no,a.floor ";
				// echo $sql_sample_sam_out;
				$sql_sample_samary_out=sql_select( $sql_sample_sam_out);
				// $subcon_smn_buyer_samary=array();
				$subcon_smn_floor_samary=array();
				foreach($sql_sample_samary_out as $inf)
				{
					$booking_no=explode("-",$inf[csf('booking_no')]);
					$without_booking_no=$booking_no[1];
					if($without_booking_no=='SMN')
					{
						$buyer_summary_arr[$inf[csf('buyer_id')]]['smn_out']+= $inf[csf('sample_qty')];
						// $subcon_smn_floor_samary[$inf[csf('floor')]]+= $inf[csf('sample_qty')];
					}
				}
			}
			// print_r($buyer_summary_arr);
			// echo $sql_non_order;
			$nameArray_inhouse=sql_select( $sql_inhouse);
			$nameArray_non_order=sql_select( $sql_non_order);
			$nameArray_subcon=sql_select( $sql_inhouse_sub);
			$macWise_subcon_arr=array();$macWise_subconout_arr=array();
			foreach ($nameArray_subcon as $row)
			{
				$knitting_source_sub=$row[csf('knitting_source')];
				$booking_non_indx=explode("-",$row[csf('booking_no')]);
				$booking_type_non=$booking_non_indx[1];
				if($knitting_source_sub==1)
				{
					$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['shifta']+=$row[csf('shifta')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['shiftb']+=$row[csf('shiftb')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['shiftc']+=$row[csf('shiftc')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['buyer_id']=$row[csf('buyer_id')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['receive_date']=$row[csf('receive_date')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['receive_basis']=$row[csf('receive_basis')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['knit_source']=$row[csf('knitting_source')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['remarks']=$row[csf('remarks')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['fab_desc']=$row[csf('fab_desc')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['gsm']=$row[csf('gsm')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['mc_brand']=$row[csf('mc_brand')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['width']=$row[csf('width')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['color_id']=$row[csf('color_id')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['yarn_lot']=$row[csf('yarn_lot')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['yarn_count']=$row[csf('yarn_count')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['stitch_length']=$row[csf('stitch_length')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['brand_id']=$row[csf('brand_id')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['reject_qty']+=$row[csf('reject_qty')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['machine_name']=$row[csf('machine_name')];
					$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['po_id'].=$row[csf('po_id')].",";

					$tot_macWise_subcon_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
					$buyer_summary_arr[$row[csf('buyer_id')]][5]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Buyer Wise
					$floor_summary_arr[$row[csf('floor_id')]][5]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Floor Wise
				} //InHouse
			} //SubCon End

			foreach ($nameArray_non_order as $row)
			{
				$receive_basis_non=$row[csf('receive_basis')];
				$booking_non_indx=explode("-",$row[csf('booking_no')]);
				$booking_type_non=$booking_non_indx[1];

				if($receive_basis_non==1)
				{
					if($booking_type_non=='SMN') $non_booking_type=5;
				}
				else if($receive_basis_non==4) //Sales
				{
					if($booking_type_non=='FSOE') $non_booking_type=6;
				}
				else if($receive_basis_non==2) //Plan
				{
					$non_booking_type=2;
				}
				if($non_booking_type==5) //Sample without
				{
					$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
					$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";

					$tot_macWise_sampout_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
					$buyer_summary_arr[$row[csf('buyer_id')]][4]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Buyer wise
					$floor_summary_arr[$row[csf('floor_id')]][4]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Floor Wise
				}
			} //Sampe Witout End
			//print_r($macWise_sampout_arr);

			foreach ($nameArray_inhouse as $row)
			{
				//$machine_inhouse_array[$row[csf('mac_id')]][$row[csf('receive_date')]]++;

				$receive_basis=$row[csf('receive_basis')];
				$booking_no_indx=explode("-",$row[csf('booking_no')]);
				$booking_type_indx=$booking_no_indx[1];

				if($receive_basis==1)
				{
					if($booking_type_indx=='SM') $booking_type=4;
					else if($booking_type_indx=='Fb') $booking_type=1;
				}
				else if($receive_basis==4) //Sales
				{
					if($booking_type_indx=='FSOE') $booking_type=6;
				}
				else if($receive_basis==2) //Plan
				{
					$booking_type=2;
				}

				if($booking_type==4) //Sample with
				{
					$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";

					$tot_macWise_sampwith_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
					$buyer_summary_arr[$row[csf('buyer_id')]][3]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
					$floor_summary_arr[$row[csf('floor_id')]][3]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
				}
				else if($row[csf('knitting_source')]==1 && ($booking_type==1 || $booking_type==2)) ////Inhouse with all
				{
					$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";
					$tot_macWise_inhouse_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];

					$buyer_summary_arr[$row[csf('buyer_id')]][1]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
					$floor_summary_arr[$row[csf('floor_id')]][1]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
				}
				else if($row[csf('knitting_source')]==3 && ($booking_type==1 || $booking_type==2)) ////OutBound with all
				{
					$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";

					$tot_macWise_outhouse_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];

					$buyer_summary_arr[$row[csf('buyer_id')]][2]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
					$floor_summary_arr[$row[csf('floor_id')]][2]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
				}
			}
			//print_r($macWise_outhouse_arr);
			foreach ($macWise_inhouse_arr as $mc_id=>$mc_data)
			{
				$machine_rowspan=0;
				foreach ($mc_data as $floor_id=>$floor_data)
				{
					foreach ($floor_data as $booking_no=>$booking_data)
					{
						foreach ($booking_data as $prod_id=>$row)
						{
							$machine_rowspan++;
						}
						$machine_rowspan_arr[$mc_id]= $machine_rowspan;
					}
				}
			}
			foreach ($macWise_outhouse_arr as $mc_id=>$mc_data)
			{
				$out_machine_rowspan=0;
				foreach ($mc_data as $floor_id=>$floor_data)
				{
					foreach ($floor_data as $booking_no=>$booking_data)
					{
						foreach ($booking_data as $prod_id=>$row)
						{
							$out_machine_rowspan++;
						}
						$out_machine_rowspan_arr[$mc_id]= $out_machine_rowspan;
					}
				}
			}
			foreach ($macWise_sampwith_arr as $mc_id=>$mc_data)
			{
				$sampwith_machine_rowspan=0;
				foreach ($mc_data as $floor_id=>$floor_data)
				{
					foreach ($floor_data as $booking_no=>$booking_data)
					{
						foreach ($booking_data as $prod_id=>$row)
						{
							$sampwith_machine_rowspan++;
						}
						$samp_machine_rowspan_arr[$mc_id]=$sampwith_machine_rowspan;
					}
				}
			} //macWise_sampout_arr
			foreach ($macWise_sampout_arr as $mc_id=>$mc_data)
			{
				$sampout_machine_rowspan=0;
				foreach ($mc_data as $floor_id=>$floor_data)
				{
					foreach ($floor_data as $booking_no=>$booking_data)
					{
						foreach ($booking_data as $prod_id=>$row)
						{
							$sampout_machine_rowspan++;
						}
						$sampout_machine_rowspan_arr[$mc_id]=$sampout_machine_rowspan;
					}
				}
			} //macWise_sampout_arr

			foreach ($macWise_subcon_arr as $mc_id=>$mc_data)
			{
				$subcon_machine_rowspan=0;
				foreach ($mc_data as $floor_id=>$floor_data)
				{
					foreach ($floor_data as $booking_no=>$booking_data)
					{
						foreach ($booking_data as $prod_id=>$row)
						{
							$subcon_machine_rowspan++;
						}
						$subcon_machine_rowspan_arr[$mc_id]=$subcon_machine_rowspan;
					}
				}
			} //macWise_subconout_arr
			foreach ($macWise_subconout_arr as $mc_id=>$mc_data)
			{
				$subconout_machine_rowspan=0;
				foreach ($mc_data as $floor_id=>$floor_data)
				{
					foreach ($floor_data as $booking_no=>$booking_data)
					{
						foreach ($booking_data as $prod_id=>$row)
						{
							$subconout_machine_rowspan++;
						}
						$subconout_machine_rowspan_arr[$mc_id]=$subconout_machine_rowspan;
					}
				}
			} //macWise_subconout_arr

			?>
			<tr>
				<td width="640" style="margin:5px;">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:550px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound+In bound SubCon) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table" >
						<thead>
							<tr>
								<th colspan="8">Knit Production Summary (In-House + Outbound+In bound SubCon
								)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Buyer</th>
								<th width="90">Inhouse</th>
								<th width="90" title="Data show with Outbound-Subcon sample without order ">Outbound-Subcon</th>
								<th width="90">Sample With Order</th>
								<th width="90" title="Data show with Outbound-Subcon sample without order ">Sample Without Order</th>
								<th width="90">In Bound Subcon</th>
								<th width="100">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:660px; overflow-y:scroll; max-height:220px;" id="scroll_body1">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table" >
							<tbody>
								<?
								$k=1;
								foreach($buyer_summary_arr as $key=>$value)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$out_bound_qnty=0;

									$in_bound_qnty=$value[1];
									$out_bound_qnty=$value[2];
									$samplewith_qnty=$value[3];
									$samplewithout_qnty=$value[4];
									$subcon_in_qnty=$value[5];
									$subcon_smn_out_qnty=$value['smn_out'];

									$tot_summ=$out_bound_qnty+$in_bound_qnty+$samplewithout_qnty+$subcon_in_qnty+$samplewith_qnty+$subcon_smn_out_qnty;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"  onClick="change_color('trb_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trb_<? echo $k; ?>" >
										<td width="40"><? echo $k; ?></td>
										<td width="100" title="<? echo $key;?>"><? echo $buyer_arr[$key]; ?></td>
										<td width="90" align="right"><? echo number_format($in_bound_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($out_bound_qnty+$subcon_smn_out_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($samplewith_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($samplewithout_qnty+$subcon_smn_out_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($subcon_in_qnty,2,'.',''); ?></td>
										<td width="100" align="right"><? echo  number_format($tot_summ,2,'.',''); ?></td>
									</tr>
									<?


									$tot_qtyinhouse+=$in_bound_qnty;$tot_qtyinbound+=$subcon_in_qnty;
									$tot_qtyoutbound+=$out_bound_qnty+$subcon_smn_out_qnty;$tot_samplewith_qnty+=$samplewith_qnty;$tot_samplewithout_qnty+=$samplewithout_qnty+$subcon_smn_out_qnty;
									$tot_qtywithout+=$samplewithout_qnty+$subcon_smn_out_qnty;

									$total_summ+=$tot_summ;
									//unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);
									$k++;
								}

								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_qtyoutbound,2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_samplewith_qnty,2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_samplewithout_qnty,2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_qtyinbound,2,'.',''); ?></th>
									<th align="right"><? echo number_format($total_summ,2,'.',''); ?></th>
								</tr>
								<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtywith_per=($tot_samplewith_qnty/$total_summ)*100; echo number_format($qtywith_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtywithout_per=($tot_samplewithout_qnty/$total_summ)*100; echo number_format($qtywithout_per,2).' %'; ?>&nbsp;</th>									<th align="right"><?  $qtyinbound_per=($tot_qtyinbound/$total_summ)*100; echo number_format($qtyinbound_per,2).' %';  ?>&nbsp;</th>
									<th align="right"><? echo "100 %"; ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
				<td width="660">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:580px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Floor Wise Self Order (In-House + Outbound + SubCon) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="660px" class="rpt_table" >
						<thead>
							<tr>
								<th colspan="8">Floor Wise Knit Production Summary (In-House + Outbound + SubCon)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="120">Floor</th>
								<th width="90">Inhouse</th>
								<th width="90">Outbound-Subcon</th>
								<th width="90">Sample With Order</th>
								<th width="90">Sample Without Order</th>
								<th width="90">In Bound Subcon</th>
								<th width="100">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:680px; overflow-y:scroll; max-height:220px;" id="scroll_body1">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="660px" class="rpt_table" >
							<tbody>
								<?
								$tot_qtyinhouse=$tot_qtyinbound=$tot_qtyoutbound=$tot_samplewith_qnty=$tot_samplewithout_qnty=$tot_qtywithout=$total_summ=0;
								$f=1;
								foreach($floor_summary_arr as $key=>$value)
								{
									if ($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$out_bound_qnty=$in_bound_qnty=0;

									$in_bound_qnty=$value[1];
									$out_bound_qnty=$value[2];
									$samplewith_qnty=$value[3];
									$samplewithout_qnty=$value[4];
									$subcon_in_qnty=$value[5];

									$tot_flr_summ=$out_bound_qnty+$in_bound_qnty+$samplewithout_qnty+$subcon_in_qnty+$samplewith_qnty;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"  onClick="change_color('trfl_<? echo $f; ?>','<? echo $bgcolor; ?>')" id="trfl_<? echo $f; ?>" >
										<td width="40"><? echo $f; ?></td>
										<td width="120" title="<? echo $key;?>"><? echo $floor_details[$key]; ?></td>
										<td width="90" align="right"><? echo number_format($in_bound_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($out_bound_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($samplewith_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($samplewithout_qnty,2,'.',''); ?></td>
										<td width="90" align="right"><? echo number_format($subcon_in_qnty,2,'.',''); ?></td>
										<td width="100" align="right"><? echo  number_format($tot_flr_summ,2,'.',''); ?></td>
									</tr>
									<?


									$tot_qtyinhouse+=$in_bound_qnty;
									$tot_qtyinbound+=$subcon_in_qnty;
									$tot_qtyoutbound+=$out_bound_qnty;
									$tot_samplewith_qnty+=$samplewith_qnty;
									$tot_samplewithout_qnty+=$samplewithout_qnty;
									$tot_qtywithout+=$samplewithout_qnty;

									$total_summ+=$tot_flr_summ;
									$f++;
								}

								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_samplewith_qnty,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_samplewithout_qnty,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($tot_qtyinbound,2,'.',''); ?>&nbsp;</th>
									<th align="right"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
								</tr>
								<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtywith_per=($tot_samplewith_qnty/$total_summ)*100; echo number_format($qtywith_per,2).' %'; ?>&nbsp;</th>
									<th align="right"><? $qtywithout_per=($tot_samplewithout_qnty/$total_summ)*100; echo number_format($qtywithout_per,2).' %'; ?>&nbsp;</th>									<th align="right"><?  $qtyinbound_per=($tot_qtyinbound/$total_summ)*100; echo number_format($qtyinbound_per,2).' %';  ?>&nbsp;</th>
									<th align="right"><? echo "100 %"; ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
			</tr>
			</table>
			<br />
			<fieldset style="width:<? echo $tbl_width+20; ?>px;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_head" >
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th width="90" rowspan="2">M/C No.</th>
							<th width="70" rowspan="2">M/C Brand </th>

							<th width="60" rowspan="2">M/C Dia &  Gauge</th>
							<th width="70" rowspan="2">Unit  Name</th>
							<th width="70" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Program/ Booking No</th>
							<th width="70" rowspan="2">Yarn Count</th>
							<th width="80" rowspan="2">Lot</th>
							<th width="100" rowspan="2">Construction</th>
							<th width="150" rowspan="2">Composition</th>
							<th width="130" rowspan="2">Color</th>
							<th width="60" rowspan="2">GSM</th>
							<th  colspan="<? echo count($shift_name); ?>">Production</th>
							<th width="100" rowspan="2">Shift Total</th>
							<th width="100" rowspan="2">Machine Total</th>
							<th width="80" rowspan="2">Reject Qty</th>

						</tr>
						<tr>
							<?
							$ship_count=0;
							foreach($shift_name as $val)
							{
								$ship_count++;
								?>
								<th width="80"><? echo $val; ?></th>
								<?
							}
							?>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $tbl_width+18; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
						<tbody>
							<?
							if($cbo_type==1 || $cbo_type==0)
							{
								if (count($macWise_inhouse_arr)>0)
								{
									?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="<? echo $ship_count+16; ?>" align="left" ><b>In-House</b></td>
									</tr>
									<?
									$km=0;$i=1;$tot_reject_qty=0;
									foreach ($macWise_inhouse_arr as $mc_id=>$mc_data)
									{
										$m=1;
										foreach ($mc_data as $floor_id=>$floor_data)
										{
											foreach ($floor_data as $booking_no=>$booking_data)
											{
												foreach ($booking_data as $prod_id=>$row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													$count='';
													$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
													foreach($yarn_count as $count_id)
													{
														if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
													}
													$booking_plan_no='';
													if($row[('receive_basis')]==2)
													{
														$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
														$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
													}
													else
													{
														$booking_plan_no=$booking_no;
														$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
													}


													?>

													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">


														<td width="30" align="center"><? echo $i; ?></td>
														<td width="90" align="center" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>

														<td width="70" align="center"><p><? echo $row[('mc_brand')]; ?></p></td>
														<td width="60"  align="center"><p><? echo $machine_dia_gage; ?></p></td>
														<td width="70" align="center"><? echo $floor_details[$floor_id]; ?></td>
														<td width="70" align="center"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
														<td width="100" align="center"><? echo $booking_no; ?></td>
														<td width="70" align="center"><p><? echo $count; ?></p></td>
														<td width="80" align="center"><P><? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
														</td>
														<td width="100"><P>
															<?
															$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
															$all_construction="";
															foreach($description_arr as $id)
															{
																$all_construction.=$construction_arr[$id].",";
															}
															$all_construction=chop($all_construction," , ");
															echo $all_construction;
															?>&nbsp;</P>
														</td>
														<td width="150"><P>
															<?
															$all_composition="";
															foreach($description_arr as $id)
															{
																$all_composition.=$composition_arr[$id].",";
															}
															$all_composition=chop($all_composition," , ");
															echo $all_composition;
															?>&nbsp;</P>
														</td>
														<td width="130"><P>
															<?
															$color_arr=array_unique(explode(",",$row[('color_id')]));
															$all_color="";
															foreach($color_arr as $id)
															{
																$all_color.=$color_details[$id].",";
															}
															$all_color=chop($all_color," , ");
															echo $all_color;
															?>&nbsp;</P>
														</td>

														<td width="60"  align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
														<?
														$row_tot_roll=0;
														$row_tot_qnty=0; $machine_row_tot_qnty=0;
														foreach($shift_name as $key=>$val)
														{
															$row_tot_qnty+=$row[('shift'.strtolower($val))];
															?>
															<td width="80" align="right" ><? echo number_format($row[('shift'.strtolower($val))],2); ?> </td>
															<?
															$grand_total_ship[$key]+=$row[('shift'.strtolower($val))];
															$inhouse_ship[$key]+=$row[('shift'.strtolower($val))];

															$machine_inhouse_arr[$mc_id]+=$row[('shift'.strtolower($val))];
														}
														?>
														<td width="100" align="right" ><? echo number_format($row_tot_qnty,2,'.',''); ?></td>
														<?
														if($m==1)
														{
															$tot_machine_qty=$tot_macWise_inhouse_arr[$mc_id]['tot_shift'];
															?>
															<td width="100" valign="top" rowspan="<? echo $machine_rowspan_arr[$mc_id];?>" align="right"><? echo number_format($tot_machine_qty,2,'.',''); ?></td>
															<?
															$grand_machine_total+=$tot_machine_qty;
															$machine_total_inhouser+=$tot_machine_qty;
														}

														?>
														<td width="80" align="right"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
														</td>
													</tr>

													<?
													$inhouse_tot_qty+=$row_tot_qnty;
													$grand_tot_qnty+=$row_tot_qnty;
													$grand_reject_qty+=$row[('reject_qty')];
													$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="13" align="right"><b>In-house Total(with order)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td align="right"><? echo number_format($inhouse_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td align="right"><? echo number_format($inhouse_tot_qty,2,'.',''); ?></td>
										<td align="right"><? echo number_format($machine_total_inhouser,2,'.',''); ?></td>
										<td align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>

									</tr>
									<?
								}

								if (count($macWise_outhouse_arr)>0)
								{
									?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="<? echo $ship_count+16; ?>" align="left" ><b>Outbound</b></td>
									</tr>
									<?
									$km=0;$tot_reject_qty=0;
									foreach ($macWise_outhouse_arr as $mc_id=>$mc_data)
									{
										$m=1;
										foreach ($mc_data as $floor_id=>$floor_data)
										{
											foreach ($floor_data as $booking_no=>$booking_data)
											{
												foreach ($booking_data as $prod_id=>$row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													$count='';
													$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
													foreach($yarn_count as $count_id)
													{
														if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
													}
													$booking_plan_no='';
													if($row[('receive_basis')]==2)
													{
														$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
														$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
													}
													else
													{
														$booking_plan_no=$booking_no;
														$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
													}
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
														<td width="30" align="center"><? echo $i; ?></td>
														<td width="90" align="center" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>
														<td width="70" align="center"><p><? echo $row[('mc_brand')]; ?></p></td>
														<td width="60"  align="center"><p><? echo $machine_dia_gage; ?></p></td>
														<td width="70" align="center"><? echo $floor_details[$floor_id]; ?></td>
														<td width="70" align="center"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
														<td width="100" align="center"><? echo $booking_no; ?></td>
														<td width="70" align="center"><p><? echo $count; ?></p></td>
														<td width="80" align="center"><P>
															<? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
														</td>
														<td width="100"><P>
															<?
															$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
															$all_construction="";
															foreach($description_arr as $id)
															{
																$all_construction.=$construction_arr[$id].",";
															}
															$all_construction=chop($all_construction," , ");
															echo $all_construction;
															?>&nbsp;</P>
														</td>
														<td width="150"><P>
															<?
															$all_composition="";
															foreach($description_arr as $id)
															{
																$all_composition.=$composition_arr[$id].",";
															}
															$all_composition=chop($all_composition," , ");
															echo $all_composition;
															?>&nbsp;</P>
														</td>
														<td width="130"><P>
															<?
															$color_arr=array_unique(explode(",",$row[('color_id')]));
															$all_color="";
															foreach($color_arr as $id)
															{
																$all_color.=$color_details[$id].",";
															}
															$all_color=chop($all_color," , ");
															echo $all_color;
															?>&nbsp;</P>
														</td>
														<td width="60" align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
														<?
														$row_tot_roll=0;
														$row_tot_qnty=0;
														foreach($shift_name as $key=>$val)
														{
															$row_tot_qnty+=$row[('shift'.strtolower($val))];
															?>
															<td width="80" align="right" ><? echo number_format($row[('shift'.strtolower($val))],2); ?> </td>
															<?
															$grand_total_ship[$key]+=$row[('shift'.strtolower($val))];
															$outhouse_ship[$key]+=$row[('shift'.strtolower($val))];
												//$machine_inhouse_arr[$mc_id]+=$row[('shift'.strtolower($val))];
														}
														?>
														<td width="100" align="right"><? echo number_format($row_tot_qnty,2,'.',''); ?></td>
														<?
														if($m==1)
														{
															$tot_machine_qty_out=$tot_macWise_outhouse_arr[$mc_id]['tot_shift'];
															?>
															<td width="100" valign="top" rowspan="<? echo $out_machine_rowspan_arr[$mc_id];?>" align="right"><? echo number_format($tot_machine_qty_out,2,'.',''); ?></td>
															<?
															$grand_machine_total+=$tot_machine_qty_out;
															$machine_total_outhouser+=$tot_machine_qty_out;
														}
														?>
														<td width="80" align="right"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
														</td>
													</tr>
													<?
													$outhouse_tot_qty+=$row_tot_qnty;
													$grand_tot_qnty+=$row_tot_qnty;
													$out_reject_qty+=$row[('reject_qty')];
													$grand_reject_qty+=$row[('reject_qty')];
													$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="13" align="right"><b>Outbound Total(with order)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td align="right"><? echo number_format($outhouse_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td align="right"><? echo number_format($outhouse_tot_qty,2,'.',''); ?></td>
										<td align="right"><? echo number_format($machine_total_outhouser,2,'.',''); ?></td>
										<td align="right"><? echo number_format($out_reject_qty,2,'.',''); ?></td>

									</tr>
									<?
								}

								if (count($macWise_sampwith_arr)>0) //Sample With Order
								{
									?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="<? echo $ship_count+16; ?>" align="left" ><b>Sample With Order</b></td>
									</tr>
									<?
									$km=0;$tot_reject_qty=0;
									foreach ($macWise_sampwith_arr as $mc_id=>$mc_data)
									{
										$m=1;
										foreach ($mc_data as $floor_id=>$floor_data)
										{
											foreach ($floor_data as $booking_no=>$booking_data)
											{
												foreach ($booking_data as $prod_id=>$row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													$count='';
													$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
													foreach($yarn_count as $count_id)
													{
														if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
													}
													$booking_plan_no='';
													if($row[('receive_basis')]==2)
													{
														$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
														$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
													}
													else
													{
														$booking_plan_no=$booking_no;
														$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
													}
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
														<td width="30" align="center"><? echo $i; ?></td>
														<td width="90" align="center" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>
														<td width="70" align="center"><p><? echo $row[('mc_brand')]; ?></p></td>
														<td width="60"  align="center"><p><? echo $machine_dia_gage; ?></p></td>
														<td width="70" align="center"><? echo $floor_details[$floor_id]; ?></td>
														<td width="70" align="center"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
														<td width="100" align="center"><? echo $booking_no; ?></td>
														<td width="70" align="center"><p><? echo $count; ?></p></td>
														<td width="80" align="center"><P>
															<? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
														</td>
														<td width="100"><P>
															<?
															$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
															$all_construction="";
															foreach($description_arr as $id)
															{
																$all_construction.=$construction_arr[$id].",";
															}
															$all_construction=chop($all_construction," , ");
															echo $all_construction;
															?>&nbsp;</P>
														</td>
														<td width="150"><P>
															<?
															$all_composition="";
															foreach($description_arr as $id)
															{
																$all_composition.=$composition_arr[$id].",";
															}
															$all_composition=chop($all_composition," , ");
															echo $all_composition;
															?>&nbsp;</P>
														</td>
														<td width="130"><P>
															<?
															$color_arr=array_unique(explode(",",$row[('color_id')]));
															$all_color="";
															foreach($color_arr as $id)
															{
																$all_color.=$color_details[$id].",";
															}
															$all_color=chop($all_color," , ");
															echo $all_color;
															?>&nbsp;</P>
														</td>
														<td width="60" align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
														<?
														$row_tot_roll=0;
														$row_tot_qnty=0;
														foreach($shift_name as $key=>$val)
														{
															$row_tot_qnty+=$row[('shift'.strtolower($val))];
															?>
															<td width="80" align="right" ><? echo number_format($row[('shift'.strtolower($val))],2); ?> </td>
															<?
															$grand_total_ship[$key]+=$row[('shift'.strtolower($val))];
															$samphouse_ship[$key]+=$row[('shift'.strtolower($val))];
															//$machine_inhouse_arr[$mc_id]+=$row[('shift'.strtolower($val))];
														}
														?>
														<td width="100" align="right"><? echo number_format($row_tot_qnty,2,'.',''); ?></td>
														<?
														if($m==1)
														{
															$tot_machine_qty_samp=$tot_macWise_outhouse_arr[$mc_id]['tot_shift'];
															?>
															<td width="100" valign="top" rowspan="<? echo $samp_machine_rowspan_arr[$mc_id];?>" align="right"><? echo number_format($tot_machine_qty_samp,2,'.',''); ?></td>
															<?
															$grand_machine_total+=$tot_machine_qty_samp;
															$machine_total_sampwith+=$tot_machine_qty_samp;
														}
														?>
														<td width="80" align="right"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
														</td>
													</tr>
													<?
													$samphouse_tot_qty+=$row_tot_qnty;
													$grand_tot_qnty+=$row_tot_qnty;
													$tot_samp_reject_qty+=$row[('reject_qty')];
													$grand_reject_qty+=$row[('reject_qty')];
													$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="13" align="right"><b>Sample Total(with order)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td align="right"><? echo number_format($samphouse_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td align="right"><? echo number_format($samphouse_tot_qty,2,'.',''); ?></td>
										<td align="right"><? echo number_format($machine_total_sampwith,2,'.',''); ?></td>
										<td align="right"><? echo number_format($tot_samp_reject_qty,2,'.',''); ?></td>

									</tr>
									<?
								}

								if (count($macWise_sampout_arr)>0) //Sample Without Order
								{
									?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="<? echo $ship_count+16; ?>" align="left" ><b>Sample Without Order</b></td>
									</tr>
									<?
									$km=0;$tot_reject_qty=0;
									foreach ($macWise_sampout_arr as $mc_id=>$mc_data)
									{
										$m=1;
										foreach ($mc_data as $floor_id=>$floor_data)
										{
											foreach ($floor_data as $booking_no=>$booking_data)
											{
												foreach ($booking_data as $prod_id=>$row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													$count='';
													$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
													foreach($yarn_count as $count_id)
													{
														if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
													}
													$booking_plan_no='';
													if($row[('receive_basis')]==2)
													{
														$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
														$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
													}
													else
													{
														$booking_plan_no=$booking_no;
														$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
													}
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
														<td width="30" align="center"><? echo $i; ?></td>
														<td width="90" align="center" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>
														<td width="70" align="center"><p><? echo $row[('mc_brand')]; ?></p></td>
														<td width="60"  align="center"><p><? echo $machine_dia_gage; ?></p></td>
														<td width="70" align="center"><? echo $floor_details[$floor_id]; ?></td>
														<td width="70" align="center"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
														<td width="100" align="center"><? echo $booking_no; ?></td>
														<td width="70" align="center"><p><? echo $count; ?></p></td>
														<td width="80" align="center"><P>
															<? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
														</td>
														<td width="100"><P>
															<?
															$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
															$all_construction="";
															foreach($description_arr as $id)
															{
																$all_construction.=$construction_arr[$id].",";
															}
															$all_construction=chop($all_construction," , ");
															echo $all_construction;
															?>&nbsp;</P>
														</td>
														<td width="150"><P>
															<?
															$all_composition="";
															foreach($description_arr as $id)
															{
																$all_composition.=$composition_arr[$id].",";
															}
															$all_composition=chop($all_composition," , ");
															echo $all_composition;
															?>&nbsp;</P>
														</td>
														<td width="130"><P>
															<?
															$color_arr=array_unique(explode(",",$row[('color_id')]));
															$all_color="";
															foreach($color_arr as $id)
															{
																$all_color.=$color_details[$id].",";
															}
															$all_color=chop($all_color," , ");
															echo $all_color;
															?>&nbsp;</P>
														</td>
														<td width="60" align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
														<?
														$row_tot_roll=0;
														$row_tot_qnty=0;
														foreach($shift_name as $key=>$val)
														{
															$row_tot_qnty+=$row[('shift'.strtolower($val))];
															?>
															<td width="80" align="right" ><? echo number_format($row[('shift'.strtolower($val))],2); ?> </td>
															<?
															$grand_total_ship[$key]+=$row[('shift'.strtolower($val))];
															$sampouthouse_ship[$key]+=$row[('shift'.strtolower($val))];
															//$machine_inhouse_arr[$mc_id]+=$row[('shift'.strtolower($val))];
														}
														?>
														<td width="100" align="right"><? echo number_format($row_tot_qnty,2,'.',''); ?></td>
														<?
														if($m==1)
														{
															$tot_machine_qty_sampout=$tot_macWise_sampout_arr[$mc_id]['tot_shift'];
															?>
															<td width="100" valign="top" rowspan="<? echo $sampout_machine_rowspan_arr[$mc_id];?>" align="right"><? echo number_format($tot_machine_qty_sampout,2,'.',''); ?></td>
															<?
															$grand_machine_total+=$tot_machine_qty_sampout;
															$machine_total_sampwithout+=$tot_machine_qty_sampout;
														}
														?>
														<td width="80" align="right"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
														</td>
													</tr>
													<?
													$sampouthouse_tot_qty+=$row_tot_qnty;
													$grand_tot_qnty+=$row_tot_qnty;
													$tot_sampout_reject_qty+=$row[('reject_qty')];
													$grand_reject_qty+=$row[('reject_qty')];
													$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="13" align="right"><b>Sample Total(without order)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td align="right"><? echo number_format($sampouthouse_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td align="right"><? echo number_format($sampouthouse_tot_qty,2,'.',''); ?></td>
										<td align="right"><? echo number_format($machine_total_sampwithout,2,'.',''); ?></td>
										<td align="right"><? echo number_format($tot_sampout_reject_qty,2,'.',''); ?></td>

									</tr>
									<?
								}

								if (count($macWise_subcon_arr)>0) //SubCon
								{
									?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="<? echo $ship_count+16; ?>" align="left" ><b>SubCon Inhouse</b></td>
									</tr>
									<?
									$km=0;$tot_reject_qty=0;
									foreach ($macWise_subcon_arr as $mc_id=>$mc_data)
									{
										$m=1;
										foreach ($mc_data as $floor_id=>$floor_data)
										{
											foreach ($floor_data as $booking_no=>$booking_data)
											{
												foreach ($booking_data as $prod_id=>$row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													$count='';
													$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
													foreach($yarn_count as $count_id)
													{
														if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
													}
													$booking_plan_no='';
													if($row[('receive_basis')]==2)
													{
														$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
														$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
													}
													else
													{
														$booking_plan_no=$booking_no;
														$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
													}
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
														<td width="30" align="center"><? echo $i; ?></td>
														<td width="90" align="center" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row['machine_name']; ?></p></td>
														<td width="70" align="center"><p><? echo $row[('mc_brand')]; ?></p></td>
														<td width="60"  align="center"><p><? echo $machine_dia_gage; ?></p></td>
														<td width="70" align="center"><? echo $floor_details[$floor_id]; ?></td>
														<td width="70" align="center"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
														<td width="100" align="center" title="Production No"><? echo $booking_no; ?></td>
														<td width="70" align="center"><p><? echo $count; ?></p></td>
														<td width="80" align="center"><P>
															<? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
														</td>
														<td width="100"><P>
													<? //
													$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
													$all_construction="";
													foreach($description_arr as $id)
													{
														$all_construction.=$construction_arr[$id].",";
													}
													$all_construction=chop($all_construction," , ");
													echo $row[('fab_desc')];//$all_construction;
													?>&nbsp;</P>
													</td>
													<td width="150"><P>
														<?
														$all_composition="";
														foreach($description_arr as $id)
														{
															$all_composition.=$composition_arr[$id].",";
														}
														$all_composition=chop($all_composition," , ");
														//echo $all_composition;
														?>&nbsp;</P>
													</td>
													<td width="130"><P>
														<?
														$color_arr=array_unique(explode(",",$row[('color_id')]));
														$all_color="";
														foreach($color_arr as $id)
														{
															$all_color.=$color_details[$id].",";
														}
														$all_color=chop($all_color," , ");
														echo $all_color;
														?>&nbsp;</P>
													</td>
													<td width="60" align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
													<?
													$row_tot_roll=0;
													$row_tot_qnty=0;
													foreach($shift_name as $key=>$val)
													{
														$row_tot_qnty+=$row[('shift'.strtolower($val))];
														?>
														<td width="80" align="right" ><? echo number_format($row[('shift'.strtolower($val))],2); ?> </td>
														<?
														$grand_total_ship[$key]+=$row[('shift'.strtolower($val))];
														$subconhouse_ship[$key]+=$row[('shift'.strtolower($val))];
															//$machine_inhouse_arr[$mc_id]+=$row[('shift'.strtolower($val))];
													}
													?>
													<td width="100" align="right"><? echo number_format($row_tot_qnty,2,'.',''); ?></td>
													<?
													if($m==1)
													{
														$tot_machine_qty_subcon=$tot_macWise_subcon_arr[$mc_id]['tot_shift'];
														?>
														<td width="100" valign="top" rowspan="<? echo $subcon_machine_rowspan_arr[$mc_id];?>" align="right"><? echo number_format($tot_machine_qty_subcon,2,'.',''); ?></td>
														<?
														$grand_machine_total+=$tot_machine_qty_subcon;
														$machine_total_subcon+=$tot_machine_qty_subcon;
													}
													?>
													<td width="80" align="right"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
													</td>
													</tr>
													<?
													$subconthouse_tot_qty+=$row_tot_qnty;
													$grand_tot_qnty+=$row_tot_qnty;
													$tot_subcon_reject_qty+=$row[('reject_qty')];
													$grand_reject_qty+=$row[('reject_qty')];
													$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="13" align="right"><b>SubCon Total(Inhouse)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td align="right"><? echo number_format($subconhouse_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td align="right"><? echo number_format($subconthouse_tot_qty,2,'.',''); ?></td>
										<td align="right"><? echo number_format($machine_total_subcon,2,'.',''); ?></td>
										<td align="right"><? echo number_format($tot_subcon_reject_qty,2,'.',''); ?></td>

									</tr>
									<?
								}
							}

							?>
							</tbody>
						</table>
					</div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="rpt_tbl_footer">
						<tfoot>
							<tr>
								<th width="30" >&nbsp;</th>
								<th width="90" >&nbsp;</th>
								<th width="70" >&nbsp;</th>

								<th width="60" >&nbsp;</th>
								<th width="70" >&nbsp;</th>
								<th width="70" >&nbsp;</th>
								<th width="100" >&nbsp;</th>

								<th width="70">&nbsp;</th>

								<th width="80">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="150">&nbsp;</th>
								<th width="130">Grand</th>
								<th width="60">Total</th>
								<?
								foreach($shift_name as $key=>$val)
								{
									?>
									<th align="right" width="80"><? echo number_format($grand_total_ship[$key],2,'.',''); ?></th>
									<?
								}
								?>
								<th align="right" width="100"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
								<th align="right" width="100"><? echo number_format($grand_machine_total,2,'.',''); ?></th>
								<th width="80" align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></th>

							</tr>
						</tfoot>
					</table>
					<br />
					<?
					if($txt_date_from!="")
					{
						if($txt_date_to=="") $txt_date_to=$txt_date_from;
						$date_distance=datediff("d",$txt_date_from, $txt_date_to);
						$month_name=date('F',strtotime($txt_date_from));
						$year_name=date('Y',strtotime($txt_date_from));
						$day_of_month=explode("-",$txt_date_from);
						if($db_type==0)
						{
							$fist_day_of_month=$day_of_month[2]*1;
						}
						else
						{
							$fist_day_of_month=$day_of_month[0]*1;
						}
						$tot_machine=count($total_machine);
						$running_machine=count($total_running_machine);
						$stop_machine=$tot_machine-$running_machine;
						$running_machine_percent=(($running_machine/$tot_machine)*100);
						$stop_machine_percent=(($stop_machine/$tot_machine)*100);
						if($date_distance==1 && $fist_day_of_month>1)
						{
							$query_cond_month=date('m',strtotime($txt_date_from));
							$query_cond_year=date('Y',strtotime($txt_date_from));
							$sql_cond="";
							if($db_type==0) $sql_cond="  and month(a.receive_date)='$query_cond_month' and year(a.receive_date)='$query_cond_year'"; else $sql_cond="  and to_char(a.receive_date,'mm')='$query_cond_month' and to_char(a.receive_date,'yyyy')='$query_cond_year'";
							if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
							$sql_montyly_inhouse=sql_select("SELECT sum(c.quantity ) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date<'".$txt_date_from."' $sql_cond");


							$sql_monthly_wout_order=sql_select("SELECT sum( b.grey_receive_qnty) as qnty  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 and a.receive_date<'".$txt_date_from."' $sql_cond");

							$yesterday_prod=$sql_montyly_inhouse[0][csf("qnty")]+$sql_monthly_wout_order[0][csf("qnty")];
							$today_prod=$yesterday_prod+$grand_tot_qnty;
						}
						?>
						<table width="<? echo $tbl_width; ?>">
							<tr>
								<td width="25%"  valign="top">
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
										<tr>
											<td>Total number of m/c running</td>
											<td width="100" align="right"><? echo $running_machine; ?></td>
											<td align="right" width="100"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr>
											<td>Total number of m/c stop</td>
											<td align="right"><? echo $stop_machine; ?></td>
											<td align="right"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr>
											<td>Total production</td>
											<td align="right"><? echo number_format($grand_tot_qnty,2); ?></td>
											<td align="center">Kg</td>
										</tr>
									</table>
								</td>
								<td width="10%"  valign="top">&nbsp; </td>
								<td  width="25%" valign="top">
									<?
									if($date_distance==1 && $fist_day_of_month>1)
									{
										?>
										<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
											<tr>
												<td>Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
												<td  align="right" width="100"><? echo number_format($yesterday_prod,2); ?></td>
												<td align="center" width="100">Kg</td>
											</tr>
											<tr>
												<td>Upto today production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
												<td align="right"><? echo number_format($today_prod,2); ?> </td>
												<td align="center">Kg</td>
											</tr>
										</table>
										<?
									}
									?>
								</td>
								<td  valign="top">&nbsp; </td>
							</tr>
						</table>
						<?
					}
					?>

				</fieldset>
				<br>
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
	else if($report_type==6) //Machine Wise 3
	{
		$operator_name_arr = return_library_array("select id,first_name from lib_employee", 'id', 'first_name');
		$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
		$supplier_arr = return_library_array("select id,SHORT_NAME from LIB_SUPPLIER", 'id', 'SHORT_NAME');
		if($cbo_type==1 || $cbo_type==0)
		{
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_cond=" and f.job_no_prefix_num='$txt_job' "; else $job_cond="";
			if($txt_order!="") $order_cond=" and e.po_number like '%$txt_order%' "; else $order_cond="";
			if($int_ref!="") $int_ref_cond=" and e.grouping like '%$int_ref%' "; else $int_ref_cond="";
			if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";

			if($db_type==0)
			{
				$year_field="YEAR(f.insert_date)";
				$year_field_sam="YEAR(a.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
				$year_sub_field="YEAR(e.insert_date)";
				if($cbo_year!=0) $job_year_sub_cond=" and YEAR(e.insert_date)=$cbo_year";  else $job_year_sub_cond="";

			}
			else if($db_type==2)
			{
				$year_field="to_char(f.insert_date,'YYYY')";
				$year_field_sam="to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";

				$year_sub_field="to_char(e.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_sub_cond=" and to_char(e.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";

			}
			else $year_field="";
			$from_date=$txt_date_from;
			if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);

			$date_con="";
			if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
			$from_date=$txt_date_from;
			if($txt_date_to=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";

			if ($cbo_floor_id!=0) $floor_id_cond=" and b.floor_id='$cbo_floor_id'"; else $floor_id_cond="";
			if (str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and a.party_id=$cbo_buyer_name"; else $buyer_id_cond="";

			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_no_cond=" and e.job_no_prefix_num='$txt_job' "; else $job_no_cond="";
			if($txt_order!="") $order_no_cond=" and d.order_no like '%$txt_order%' "; else $order_no_cond="";
			/* =================================================================================/
			/										machine data 								/
			/ ================================================================================ */
			$machine_details=array();
			if ($cbo_floor_id==0) $lib_mc_cond=""; else $lib_mc_cond="and floor_id='$cbo_floor_id'";
			$machine_data=sql_select("SELECT id, machine_no, dia_width, gauge from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 $lib_mc_cond");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				if($row[csf('machine_no')]!="") $total_machine[$row[csf('id')]]=$row[csf('id')];
			}
			unset($machine_data);
			/* =================================================================================/
			/									yarn count/comp data							/
			/ ================================================================================ */
			$composition_arr=$construction_arr=array();
			$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
				}
			}
			unset($data_array);
			/* =================================================================================/
			/										kniting plan data							/
			/ ================================================================================ */
			$knit_plan_arr=array();
			$plan_data=sql_select("SELECT id, color_range, stitch_length, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
			foreach($plan_data as $row)
			{
				$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')];
				$knit_plan_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
				$knit_plan_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
			}
			unset($plan_data);
		}

		$yarn_type_arr=return_library_array( "SELECT id, yarn_type from product_details_master where item_category_id=13", "id", "yarn_type");
		$plan_booking_arr=return_library_array( "SELECT b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id", "booking_no");
		$i=1;
		/* =================================================================================/
		/										inhouse data 								/
		/ ================================================================================ */
		$sql_inhouse="SELECT a.recv_number,a.receive_basis,a.knitting_company, a.receive_date, a.booking_no,a.knitting_source, a.buyer_id as buyer_id, a.remarks as remarks, b.id as dtls_id, b.prod_id as prod_id, b.febric_description_id as feb_desc_id, b.gsm as gsm, b.width as width, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.machine_no_id as mac_id,d.brand as mc_brand, b.floor_id as floor_id,  b.color_id as color_id, c.po_breakdown_id as po_id, d.seq_no, d.machine_no as machine_name, e.po_number as po_number,b.reject_fabric_receive as reject_qty,b.shift_name,c.quantity as shift_qty,e.grouping,b.color_range_id,b.operator_name";

		foreach($shift_name as $key=>$val)
		{
			// $sql_inhouse.=", (case when b.shift_name=$key then c.quantity else 0 end ) as shift".strtolower($val);
			$sql_inhouse.=", case when b.shift_name=$key then b.operator_name else 0 end as operator_name".strtolower($val)."
								, case when b.shift_name=$key then b.no_of_roll else 0 end as roll".strtolower($val)."
								, (case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", (case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val)
								;
		}
		$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
		where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and b.id = g.dtls_id and c.po_breakdown_id = g.po_breakdown_id and a.item_category=13 and c.entry_form=2 and c.trans_type=1   $knit_source_cond $cbo_company_cond $company_working_cond   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $int_ref_cond $job_year_cond order by a.receive_date,d.seq_no,  b.floor_id";
		 //echo $sql_inhouse;die;//operator_name
		/* =================================================================================/
		/										outbound data 								/
		/ ================================================================================ */
		$sql_outbound="SELECT a.recv_number,a.receive_basis,a.knitting_company, a.receive_date, a.booking_no,a.knitting_source, a.buyer_id as buyer_id, a.remarks as remarks, b.id as dtls_id, b.prod_id as prod_id, b.febric_description_id as feb_desc_id, b.gsm as gsm, b.width as width, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.machine_no_id as mac_id,b.floor_id as floor_id,  b.color_id as color_id, c.po_breakdown_id as po_id, e.po_number as po_number,b.reject_fabric_receive as reject_qty,b.shift_name,c.quantity as shift_qty,e.grouping,b.color_range_id,b.operator_name, 0 as mac_id";

		foreach($shift_name as $key=>$val)
		{
			// $sql_inhouse.=", (case when b.shift_name=$key then c.quantity else 0 end ) as shift".strtolower($val);
			$sql_outbound.=", case when b.shift_name=$key then b.no_of_roll else 0 end as roll".strtolower($val)."
								, (case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", (case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val)
								;
		}
		$sql_outbound.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
		where a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and b.id = g.dtls_id and c.po_breakdown_id = g.po_breakdown_id and a.item_category=13 and c.entry_form=2 and c.trans_type=1   $knit_source_cond $cbo_company_cond $company_working_cond   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and  a.knitting_source=3 $date_con $floor_id $buyer_cond $job_cond $order_cond $int_ref_cond $job_year_cond order by a.receive_date,  b.floor_id";
		//echo $sql_outbound;die;
		/* =================================================================================/
		/										Non order data 								/
		/ ================================================================================ */
		$sql_non_order="SELECT a.recv_number,a.receive_basis,a.knitting_company, a.receive_date, a.booking_no,a.knitting_source, a.buyer_id as buyer_id, a.remarks as remarks, b.id as dtls_id, b.prod_id as prod_id, b.febric_description_id as feb_desc_id, b.gsm as gsm, b.width as width, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.machine_no_id as mac_id,d.brand as mc_brand, b.floor_id as floor_id,  b.color_id as color_id,d.seq_no, d.machine_no as machine_name, b.reject_fabric_receive as reject_qty,b.shift_name,b.grey_receive_qnty as shift_qty,b.color_range_id,b.operator_name";
		foreach($shift_name as $key=>$val)
		{
			// $sql_non_order.=", (case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as shift".strtolower($val);
			$sql_non_order.=", case when b.shift_name=$key then b.no_of_roll else 0 end as roll".strtolower($val)."
								, (case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", (case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val)
								;
		}
		$sql_non_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d,pro_roll_details g
		where a.id=b.mst_id and b.machine_no_id=d.id and a.id = g.mst_id and b.id = g.dtls_id and g.entry_form = 2  and a.entry_form=2 and a.item_category=13  and a.booking_without_order=1  $knit_source_cond $cbo_company_cond $company_working_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 $date_con $floor_id $buyer_cond   order by a.receive_date,d.seq_no,  b.floor_id";
		// echo $sql_non_order;
		/* =================================================================================/
		/								inhouse sub-con data 								/
		/ ================================================================================ */
		if($db_type	==0) $order_production_relation = " and b.order_id  = d.id";
		else $order_production_relation = " and cast (b.order_id as varchar(4000)) = d.id";

		$sql_inhouse_sub="SELECT a.knitting_company,a.knitting_source,b.id, a.prefix_no_num, a.product_no as booking_no, a.product_date as receive_date, a.party_id as buyer_id, c.seq_no, a.remarks,a.inserted_by,a.insert_date, b.gsm, b.dia_width as width, b.dia_width_type, b.yarn_lot, b.yrn_count_id as yarn_count, b.stitch_len as stitch_length, b.brand as brand_id, b.machine_id as mac_id,b.machine_dia,b.machine_gg,b.cons_comp_id as feb_desc_id,b.fabric_description as fab_desc,  b.floor_id , b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, d.order_no, d.cust_style_ref, (case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, (case when b.shift=0 then b.no_of_roll end ) as rollnoshift,b.reject_qnty, d.main_process_id";
		foreach($shift_name as $key=>$val)
		{
			$sql_inhouse_sub.=", (case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."
			, (case when b.shift=$key then b.product_qnty else 0 end ) as shift".strtolower($val);
		}

		$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
		where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2 and d.status_active=1 and d.is_deleted=0
		and a.status_active=1 and a.is_deleted=0  $knit_source_cond $company_working_cond $cbo_company_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $order_production_relation
		order by b.machine_id,c.seq_no,  b.floor_id";
		// echo $sql_inhouse_sub;
		/* =================================================================================/
		/						outbound subcon smn without order 							/
		/ ================================================================================ */
		$sql_sample_sam_out="SELECT a.buyer_id, a.booking_no,a.floor,a.knitting_company, sum(case when a.booking_without_order=1 and b.machine_no_id=0 $floor_id  then b.grey_receive_qnty end ) as sample_qty,b.color_range_id,b.operator_name from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4 $cbo_company_cond $company_working_cond and a.knitting_source=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(1) $date_con $floor_id $buyer_cond group by a.buyer_id,a.booking_no,a.floor,a.knitting_company,b.color_range_id,b.operator_name ";
		// echo $sql_sample_sam_out;
		$sql_sample_samary_out=sql_select( $sql_sample_sam_out);
		// $subcon_smn_buyer_samary=array();
		$subcon_smn_floor_samary=array();
		foreach($sql_sample_samary_out as $inf)
		{
			$booking_no=explode("-",$inf[csf('booking_no')]);
			$without_booking_no=$booking_no[1];
			if($without_booking_no=='SMN')
			{
				$buyer_summary_arr[$inf[csf('buyer_id')]]['smn_out']+= $inf[csf('sample_qty')];
				// $subcon_smn_floor_samary[$inf[csf('floor')]]+= $inf[csf('sample_qty')];
			}
		}
		// print_r($subcon_smn_buyer_samary);

		$nameArray_inhouse=sql_select( $sql_inhouse);
		$nameArray_outbound=sql_select( $sql_outbound);
		$nameArray_non_order=sql_select( $sql_non_order);
		$nameArray_subcon=sql_select( $sql_inhouse_sub);
		$macWise_subcon_arr=array();
		$macWise_subconout_arr=array();
		foreach ($nameArray_subcon as $row)
		{
			$knitting_source_sub=$row[csf('knitting_source')];
			$booking_non_indx=explode("-",$row[csf('booking_no')]);
			$booking_type_non=$booking_non_indx[1];
			if($knitting_source_sub==1)
			{
				$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['shifta']+=$row[csf('shifta')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['shiftb']+=$row[csf('shiftb')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['shiftc']+=$row[csf('shiftc')];

				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['knitting_company']=$row[csf('knitting_company')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['buyer_id']=$row[csf('buyer_id')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['receive_date']=$row[csf('receive_date')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['receive_basis']=$row[csf('receive_basis')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['knit_source']=$row[csf('knitting_source')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['remarks']=$row[csf('remarks')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['fab_desc']=$row[csf('fab_desc')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['gsm']=$row[csf('gsm')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['mc_brand']=$row[csf('mc_brand')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['width']=$row[csf('width')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['color_id']=$row[csf('color_id')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['yarn_lot']=$row[csf('yarn_lot')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['yarn_count']=$row[csf('yarn_count')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['stitch_length']=$row[csf('stitch_length')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['brand_id']=$row[csf('brand_id')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['reject_qty']+=$row[csf('reject_qty')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['machine_name']=$row[csf('machine_name')];
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['po_id'].=$row[csf('po_id')].",";
				$macWise_subcon_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('feb_desc_id')]]['main_process_id']=$row[csf('main_process_id')];

				$tot_macWise_subcon_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
				$buyer_summary_arr[$row[csf('buyer_id')]][5]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Buyer Wise
				$floor_summary_arr[$row[csf('floor_id')]][5]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Floor Wise
			} //InHouse

		} //SubCon End


		foreach ($nameArray_non_order as $row)
		{
			$receive_basis_non=$row[csf('receive_basis')];
			$booking_non_indx=explode("-",$row[csf('booking_no')]);
			$booking_type_non=$booking_non_indx[1];

			if($receive_basis_non==1)
			{
				if($booking_type_non=='SMN') $non_booking_type=5;
			}
			else if($receive_basis_non==4) //Sales
			{
				if($booking_type_non=='FSOE') $non_booking_type=6;
			}
			else if($receive_basis_non==2) //Plan
			{
				$non_booking_type=2;
			}
			if($non_booking_type==2) //Sample without
			{
				$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rolla']+=$row[csf('rolla')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollb']+=$row[csf('rollb')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollc']+=$row[csf('rollc')];

				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshifta']+=$row[csf('qntyshifta')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftb']+=$row[csf('qntyshiftb')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftc']+=$row[csf('qntyshiftc')];

				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshifta']+=$row[csf('pcsshifta')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftb']+=$row[csf('pcsshiftc')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftc']+=$row[csf('pcsshiftc')];
				// $macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
				// $macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
				// $macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knitting_company']=$row[csf('knitting_company')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";

				$tot_macWise_sampout_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];
				$buyer_summary_arr[$row[csf('buyer_id')]][4]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];//Buyer wise
				$floor_summary_arr[$row[csf('floor_id')]][4]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];//Floor Wise
			}
			if($non_booking_type==5) //Sample without
			{
				$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];

				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knitting_company']=$row[csf('knitting_company')];

				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
				$macWise_sampout_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";

				$tot_macWise_sampout_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
				$buyer_summary_arr[$row[csf('buyer_id')]][4]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Buyer wise
				$floor_summary_arr[$row[csf('floor_id')]][4]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];//Floor Wise
			}
		} //Sampe Witout End
		//print_r($macWise_sampout_arr);
		$chk_dtls_id_array = array();


		foreach ($nameArray_inhouse as $row)
		{
			//$machine_inhouse_array[$row[csf('mac_id')]][$row[csf('receive_date')]]++;

			$receive_basis=$row[csf('receive_basis')];
			$booking_no_indx=explode("-",$row[csf('booking_no')]);
			$booking_type_indx=$booking_no_indx[1];

			if($receive_basis==1)
			{
				if($booking_type_indx=='SM') $booking_type=4;
				else if($booking_type_indx=='Fb') $booking_type=1;
			}
			else if($receive_basis==4) //Sales
			{
				if($booking_type_indx=='FSOE') $booking_type=6;
			}
			else if($receive_basis==2) //Plan
			{
				$booking_type=2;
			}

			if($booking_type==4) //Sample with
			{
				$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
				if(!in_array($row[csf('dtls_id')],$chk_dtls_id_array))
				{
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rolla']+=$row[csf('rolla')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollb']+=$row[csf('rollb')];
					$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollc']+=$row[csf('rollc')];
					$chk_dtls_id_array[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
				}
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshifta']+=$row[csf('qntyshifta')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftb']+=$row[csf('qntyshiftb')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftc']+=$row[csf('qntyshiftc')];

				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshifta']+=$row[csf('pcsshifta')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftb']+=$row[csf('pcsshiftc')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftc']+=$row[csf('pcsshiftc')];

				// $macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
				// $macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
				// $macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];

				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knitting_company']=$row[csf('knitting_company')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['grouping']=$row[csf('grouping')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_range_id']=$row[csf('color_range_id')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
				$macWise_sampwith_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";

				$tot_macWise_sampwith_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
				$buyer_summary_arr[$row[csf('buyer_id')]][3]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
				$floor_summary_arr[$row[csf('floor_id')]][3]+=$row[csf('shifta')]+$row[csf('shiftb')]+$row[csf('shiftc')];
			}
			else if($row[csf('knitting_source')]==1 && ($booking_type==1 || $booking_type==2)) ////Inhouse with all
			{
				$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];
				if(!in_array($row[csf('dtls_id')],$chk_dtls_id_array))
				{
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rolla']+=$row[csf('rolla')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollb']+=$row[csf('rollb')];
					$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollc']+=$row[csf('rollc')];
					$chk_dtls_id_array[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
				}

				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshifta']+=$row[csf('qntyshifta')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftb']+=$row[csf('qntyshiftb')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftc']+=$row[csf('qntyshiftc')];

				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['operator_namea']=$row[csf('operator_namea')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['operator_nameb']=$row[csf('operator_nameb')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['operator_namec']=$row[csf('operator_namec')];

				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshifta']+=$row[csf('pcsshifta')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftb']+=$row[csf('pcsshiftc')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftc']+=$row[csf('pcsshiftc')];

				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knitting_company']=$row[csf('knitting_company')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['grouping']=$row[csf('grouping')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];

				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_range_id']=$row[csf('color_range_id')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['operator_name']=$row[csf('operator_name')];

				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
				$macWise_inhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";
				$tot_macWise_inhouse_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];

				$buyer_summary_arr[$row[csf('buyer_id')]][1]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];
				$floor_summary_arr[$row[csf('floor_id')]][1]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];

			}

		}
		//echo "<pre>"; print_r($chk_dtls_id_array);die();
		$macWise_outhouse_arr=array();
		$unique_recv_arr=array();
		foreach ($nameArray_outbound as $row)
		{
			//$machine_inhouse_array[$row[csf('mac_id')]][$row[csf('receive_date')]]++;

			$receive_basis=$row[csf('receive_basis')];
			$booking_no_indx=explode("-",$row[csf('booking_no')]);
			$booking_type_indx=$booking_no_indx[1];

			if($receive_basis==1)
			{
				if($booking_type_indx=='SM') $booking_type=4;
				else if($booking_type_indx=='Fb') $booking_type=1;
			}
			else if($receive_basis==4) //Sales
			{
				if($booking_type_indx=='FSOE') $booking_type=6;
			}
			else if($receive_basis==2) //Plan
			{
				$booking_type=2;
			}

			if($row[csf('knitting_source')]==3 && ($booking_type==1 || $booking_type==2)) ////OutBound with all
			{
				$total_running_machine[$row[csf('mac_id')]]=$row[csf('mac_id')];

				if (!in_array($row[csf('recv_number')], $unique_recv_arr))
				{
					// echo "string=".$row[csf('rolla')]."<br>";
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rolla']+=$row[csf('rolla')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollb']+=$row[csf('rollb')];
					$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['rollc']+=$row[csf('rollc')];
					$unique_recv_arr[$row[csf('recv_number')]]=$row[csf('recv_number')];
				}

				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshifta']+=$row[csf('qntyshifta')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftb']+=$row[csf('qntyshiftb')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['qntyshiftc']+=$row[csf('qntyshiftc')];

				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshifta']+=$row[csf('pcsshifta')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftb']+=$row[csf('pcsshiftc')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['pcsshiftc']+=$row[csf('pcsshiftc')];

				// $macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shifta']+=$row[csf('shifta')];
				// $macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftb']+=$row[csf('shiftb')];
				// $macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['shiftc']+=$row[csf('shiftc')];

				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knitting_company']=$row[csf('knitting_company')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['grouping']=$row[csf('grouping')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_date']=$row[csf('receive_date')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['knit_source']=$row[csf('knitting_source')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].",";
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['feb_desc_id']=$row[csf('feb_desc_id')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['mc_brand']=$row[csf('mc_brand')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['width']=$row[csf('width')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_range_id']=$row[csf('color_range_id')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot']=$row[csf('yarn_lot')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count']=$row[csf('yarn_count')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['brand_id']=$row[csf('brand_id')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['reject_qty']+=$row[csf('reject_qty')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_name']=$row[csf('machine_name')];
				$macWise_outhouse_arr[$row[csf('mac_id')]][$row[csf('floor_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].",";

				$tot_macWise_outhouse_arr[$row[csf('mac_id')]]['tot_shift']+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];

				$buyer_summary_arr[$row[csf('buyer_id')]][2]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];
				$floor_summary_arr[$row[csf('floor_id')]][2]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];


			}
		}
		//echo "<pre>";print_r($chk_dtls_id_array);die;
		foreach ($macWise_inhouse_arr as $mc_id=>$mc_data)
		{
			$machine_rowspan=0;
			foreach ($mc_data as $floor_id=>$floor_data)
			{
				foreach ($floor_data as $booking_no=>$booking_data)
				{
					foreach ($booking_data as $prod_id=>$row)
					{
						$machine_rowspan++;
					}
					$machine_rowspan_arr[$mc_id]= $machine_rowspan;
				}
			}
		}

		foreach ($macWise_outhouse_arr as $mc_id=>$mc_data)
		{
			$out_machine_rowspan=0;
			foreach ($mc_data as $floor_id=>$floor_data)
			{
				foreach ($floor_data as $booking_no=>$booking_data)
				{
					foreach ($booking_data as $prod_id=>$row)
					{
						$out_machine_rowspan++;
					}
					$out_machine_rowspan_arr[$mc_id]= $out_machine_rowspan;
				}
			}
		}

		foreach ($macWise_sampwith_arr as $mc_id=>$mc_data)
		{
			$sampwith_machine_rowspan=0;
			foreach ($mc_data as $floor_id=>$floor_data)
			{
				foreach ($floor_data as $booking_no=>$booking_data)
				{
					foreach ($booking_data as $prod_id=>$row)
					{
						$sampwith_machine_rowspan++;
					}
					$samp_machine_rowspan_arr[$mc_id]=$sampwith_machine_rowspan;
				}
			}
		} //macWise_sampout_arr

		foreach ($macWise_sampout_arr as $mc_id=>$mc_data)
		{
			$sampout_machine_rowspan=0;
			foreach ($mc_data as $floor_id=>$floor_data)
			{
				foreach ($floor_data as $booking_no=>$booking_data)
				{
					foreach ($booking_data as $prod_id=>$row)
					{
						$sampout_machine_rowspan++;
					}
					$sampout_machine_rowspan_arr[$mc_id]=$sampout_machine_rowspan;
				}
			}
		} //macWise_sampout_arr

		foreach ($macWise_subcon_arr as $mc_id=>$mc_data)
		{
			$subcon_machine_rowspan=0;
			foreach ($mc_data as $floor_id=>$floor_data)
			{
				foreach ($floor_data as $booking_no=>$booking_data)
				{
					foreach ($booking_data as $prod_id=>$row)
					{
						$subcon_machine_rowspan++;
					}
					$subcon_machine_rowspan_arr[$mc_id]=$subcon_machine_rowspan;
				}
			}
		} //macWise_subconout_arr

		foreach ($macWise_subconout_arr as $mc_id=>$mc_data)
		{
			$subconout_machine_rowspan=0;
			foreach ($mc_data as $floor_id=>$floor_data)
			{
				foreach ($floor_data as $booking_no=>$booking_data)
				{
					foreach ($booking_data as $prod_id=>$row)
					{
						$subconout_machine_rowspan++;
					}
					$subconout_machine_rowspan_arr[$mc_id]=$subconout_machine_rowspan;
				}
			}
		} //macWise_subconout_arr

		$tbl_width=1850+count($shift_name)*250;
		$ship_dtl_count = count($shift_name)*4;
		ob_start();
		?>
		<style type="text/css">
			body{
				font-family: "Arial Narrow", Arial, sans-serif;
				font-size: 14px;
			}
			td th {
			    font-size: 14px;
			}
			.topics tr { line-height: 25px; }
		</style>
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>" class="topics" >
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+16; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+16; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count+16; ?>" class="form_caption" style="font-size:12px" ><strong><? if(str_replace("'","",$txt_date_from)!="") echo "From ".str_replace("'","",$txt_date_from); if(str_replace("'","",$txt_date_to)!="") echo " To ".str_replace("'","",$txt_date_to); ?></strong></td>
			</tr>
		</table>
		<!-- =================================================================================/
		/								SUMMARY PART START HERE  							 /
		/ ================================================================================ -->
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300px" class="topics">
			<tr>
				<td width="640" style="margin:5px;">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:550px;  "><strong><u><i>Self Order (In-House + Outbound+In bound SubCon) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table topics" style="font-size: 12px;" >
						<thead>
							<tr>
								<th colspan="8">Knit Production Summary (In-House + Outbound+In bound SubCon
								)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Buyer</th>
								<th width="90">Inhouse</th>
								<th width="90" title="Data show with Outbound-Subcon sample without order ">Outbound-Subcon</th>
								<th width="90">Sample With Order</th>
								<th width="90" title="Data show with Outbound-Subcon sample without order ">Sample Without Order</th>
								<th width="90">In Bound Subcon</th>
								<th width="100">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:660px; overflow-y:scroll; max-height:220px;" id="scroll_body1">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640px" class="rpt_table topics" style="font-size: 14px;">
							<tbody>
								<?
								$k=1;
								foreach($buyer_summary_arr as $key=>$value)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$out_bound_qnty=0;

									$in_bound_qnty=$value[1];
									$out_bound_qnty=$value[2];
									$samplewith_qnty=$value[3];
									$samplewithout_qnty=$value[4];
									$subcon_in_qnty=$value[5];
									$subcon_smn_out_qnty=$value['smn_out'];

									$tot_summ=$out_bound_qnty+$in_bound_qnty+$samplewithout_qnty+$subcon_in_qnty+$samplewith_qnty+$subcon_smn_out_qnty;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"  onClick="change_color('trb_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trb_<? echo $k; ?>"  >
										<td width="40"><? echo $k; ?></td>
										<td width="100" title="<? echo $key;?>"><? echo $buyer_arr[$key]; ?></td>
										<td width="90" align="middle"><? echo number_format($in_bound_qnty,2,'.',''); ?></td>
										<td width="90" align="middle"><? echo number_format($out_bound_qnty+$subcon_smn_out_qnty,2,'.',''); ?></td>
										<td width="90" align="middle"><? echo number_format($samplewith_qnty,2,'.',''); ?></td>
										<td width="90" align="middle"><? echo number_format($samplewithout_qnty+$subcon_smn_out_qnty,2,'.',''); ?></td>
										<td width="90" align="middle"><? echo number_format($subcon_in_qnty,2,'.',''); ?></td>
										<td width="100" align="middle"><? echo  number_format($tot_summ,2,'.',''); ?></td>
									</tr>
									<?


									$tot_qtyinhouse+=$in_bound_qnty;$tot_qtyinbound+=$subcon_in_qnty;
									$tot_qtyoutbound+=$out_bound_qnty+$subcon_smn_out_qnty;$tot_samplewith_qnty+=$samplewith_qnty;$tot_samplewithout_qnty+=$samplewithout_qnty+$subcon_smn_out_qnty;
									$tot_qtywithout+=$samplewithout_qnty+$subcon_smn_out_qnty;

									$total_summ+=$tot_summ;
									//unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);
									$k++;
								}

								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" ><strong>Total</strong></th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_qtyinhouse,2,'.',''); ?></th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_qtyoutbound,2,'.',''); ?></th>
									<th  style="justify-content: center;text-align: center;"><? echo number_format($tot_samplewith_qnty,2,'.',''); ?></th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_samplewithout_qnty,2,'.',''); ?></th>
									<th  style="justify-content: center;text-align: center;"><? echo number_format($tot_qtyinbound,2,'.',''); ?></th>
									<th  style="justify-content: center;text-align: center;"><? echo number_format($total_summ,2,'.',''); ?></th>
								</tr>
								<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th style="justify-content: center;text-align: center;"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? $qtywith_per=($tot_samplewith_qnty/$total_summ)*100; echo number_format($qtywith_per,2).' %'; ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? $qtywithout_per=($tot_samplewithout_qnty/$total_summ)*100; echo number_format($qtywithout_per,2).' %'; ?>&nbsp;</th>									<th align="middle"><?  $qtyinbound_per=($tot_qtyinbound/$total_summ)*100; echo number_format($qtyinbound_per,2).' %';  ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? echo "100 %"; ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
				<td width="660">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:580px; font-size:14px; "><strong><u><i>Floor Wise Self Order (In-House + Outbound + SubCon) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="660px" class="rpt_table topics" style="font-size: 12px;">
						<thead>
							<tr>
								<th colspan="8">Floor Wise Knit Production Summary (In-House + Outbound + SubCon)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="120">Floor</th>
								<th width="90">Inhouse</th>
								<th width="90">Outbound-Subcon</th>
								<th width="90">Sample With Order</th>
								<th width="90">Sample Without Order</th>
								<th width="90">In Bound Subcon</th>
								<th width="100">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:680px; overflow-y:scroll; max-height:220px;" id="scroll_body1">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="660px" class="rpt_table topics" style="font-size: 12px;">
							<tbody>
								<?
								$tot_qtyinhouse=$tot_qtyinbound=$tot_qtyoutbound=$tot_samplewith_qnty=$tot_samplewithout_qnty=$tot_qtywithout=$total_summ=0;
								$f=1;
								foreach($floor_summary_arr as $key=>$value)
								{
									if ($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if($value[1]>0 || $value[3]>0 || $value[4]>0 || $value[5]>0)
									{
										$out_bound_qnty=$in_bound_qnty=0;

										$in_bound_qnty=$value[1];
										// $out_bound_qnty=$value[2];
										$samplewith_qnty=$value[3];
										$samplewithout_qnty=$value[4];
										$subcon_in_qnty=$value[5];

										$tot_flr_summ=$in_bound_qnty+$samplewithout_qnty+$subcon_in_qnty+$samplewith_qnty;
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  onClick="change_color('trfl_<? echo $f; ?>','<? echo $bgcolor; ?>')" id="trfl_<? echo $f; ?>" >
											<td width="40"><? echo $f; ?></td>
											<td width="120" title="<? echo $key;?>"><? echo $floor_details[$key]; ?></td>
											<td width="90" style="justify-content: center;text-align: center;"><? echo number_format($in_bound_qnty,2,'.',''); ?></td>
											<td width="90" style="justify-content: center;text-align: center;"><? //echo number_format($out_bound_qnty,2,'.',''); ?></td>
											<td width="90" style="justify-content: center;text-align: center;"><? echo number_format($samplewith_qnty,2,'.',''); ?></td>
											<td width="90" style="justify-content: center;text-align: center;"><? echo number_format($samplewithout_qnty,2,'.',''); ?></td>
											<td width="90" style="justify-content: center;text-align: center;"><? echo number_format($subcon_in_qnty,2,'.',''); ?></td>
											<td width="100" style="justify-content: center;text-align: center;"><? echo  number_format($tot_flr_summ,2,'.',''); ?></td>
										</tr>
										<?


										$tot_qtyinhouse+=$in_bound_qnty;
										$tot_qtyinbound+=$subcon_in_qnty;
										// $tot_qtyoutbound+=$out_bound_qnty;
										$tot_samplewith_qnty+=$samplewith_qnty;
										$tot_samplewithout_qnty+=$samplewithout_qnty;
										$tot_qtywithout+=$samplewithout_qnty;

										$total_summ+=$tot_flr_summ;
										$f++;
									}
								}
								foreach($floor_summary_arr as $key=>$value)
								{
									if($value[2]>0)
									{
										if ($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$out_bound_qnty=0;
										$out_bound_qnty=$value[2];

										$tot_flr_summ_out+=$out_bound_qnty;
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  onClick="change_color('trfl_<? echo $f; ?>','<? echo $bgcolor; ?>')" id="trfl_<? echo $f; ?>" >
											<td width="40"><? echo $f; ?></td>
											<td width="120" title="<? echo $key;?>"></td>
											<td width="90" align="right"></td>
											<td width="90" style="justify-content: center;text-align: center;"><? echo number_format($out_bound_qnty,2,'.',''); ?></td>
											<td width="90" align="right"></td>
											<td width="90" align="right"></td>
											<td width="90" align="right"></td>
											<td width="100" style="justify-content: center;text-align: center;"><? echo  number_format($tot_flr_summ_out,2,'.',''); ?></td>
										</tr>
										<?
										$tot_qtyoutbound+=$out_bound_qnty;

										$total_summ+=$tot_flr_summ_out;
										$f++;
									}
								}

								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_samplewith_qnty,2,'.',''); ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_samplewithout_qnty,2,'.',''); ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($tot_qtyinbound,2,'.',''); ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
								</tr>
								<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th style="justify-content: center;text-align: center;"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? $qtywith_per=($tot_samplewith_qnty/$total_summ)*100; echo number_format($qtywith_per,2).' %'; ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? $qtywithout_per=($tot_samplewithout_qnty/$total_summ)*100; echo number_format($qtywithout_per,2).' %'; ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><?  $qtyinbound_per=($tot_qtyinbound/$total_summ)*100; echo number_format($qtyinbound_per,2).' %';  ?>&nbsp;</th>
									<th style="justify-content: center;text-align: center;"><? echo "100 %"; ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<br />
		<!-- =================================================================================/
		/								DETAILS PART START HERE  							 /
		/ ================================================================================ -->
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table topics" id="table_head" style="font-size: 14px;">
				<thead>
					<tr>
						<th width="30" rowspan="3">SL</th>
						<th width="70" rowspan="3">Production Date</th>
						<th width="70" rowspan="3">Working Company</th>
						<th width="70" rowspan="3">Floor Name</th>
						<th width="50" rowspan="3">M/C No.</th>
						<th width="60" rowspan="3">M/C Dia &  Gauge</th>
						<th width="50" rowspan="3">Program/ Booking No</th>
						<th width="50" rowspan="3">Req. No</th>
						<th width="70" rowspan="3">Buyer</th>
						<th width="70" rowspan="3">Ref No.</th>
						<th width="70" rowspan="3">Yarn Count</th>
						<th width="80" rowspan="3">Brand</th>
						<th width="80" rowspan="3">Lot</th>
						<th width="100" rowspan="3">Construction</th>
						<th width="100" rowspan="3">Composition</th>
						<th width="110" rowspan="3">Color</th>
						<th width="100" rowspan="3">Color Range</th>
						<th width="60" rowspan="3">Stitch</th>
						<th width="60" rowspan="3">GSM</th>
						<th width="<? echo count($shift_name)*250;?>" colspan="<? echo count($shift_name)*4; ?>">Production</th>
						<th width="50" rowspan="3">Roll Total</th>
						<th width="80" rowspan="3">Shift Total</th>
						<th width="80" rowspan="3">Machine Total</th>
						<th width="80" rowspan="3">Reject Qty</th>
						<th width="80" rowspan="3"> Remarks</th>
					</tr>
					<tr>
						<?
						$ship_count=0;
						foreach($shift_name as $val)
						{
							$ship_count++;
							?>
							<th colspan="4" width="250"><? echo $val; ?></th>
							<?
						}
						?>
					</tr>
					<tr>
						<?
						foreach($shift_name as $val)
						{
							?>
							<th width="100">OP Name</th>
							<th width="50">Roll</th>
							<th width="50">Pcs</th>
							<th width="50">Weight</th>
							<?
						}
						?>
					</tr>
				</thead>
			</table>

			<div style="width:<? echo $tbl_width+18; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table topics" id="table_body" style="font-size: 14px;">
					<tbody>
						<?


						if($cbo_type==1 || $cbo_type==0)
						{
							if (count($macWise_inhouse_arr)>0)
							{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_dtl_count+24; ?>" align="left" ><b>In-House</b></td>
								</tr>
								<?
								$km=0;$i=1;$tot_reject_qty=0;$reqsn_no='';
								foreach ($macWise_inhouse_arr as $mc_id=>$mc_data)
								{
									$m=1;
									foreach ($mc_data as $floor_id=>$floor_data)
									{
										foreach ($floor_data as $booking_no=>$booking_data)
										{
											foreach ($booking_data as $prod_id=>$row)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												$count='';
												$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
												foreach($yarn_count as $count_id)
												{
													if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
												}
												$booking_plan_no='';
												if($row[('receive_basis')]==2)
												{
													$reqsn_no=$reqsn_details[$booking_no];
													$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
													$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
												}
												else
												{
													$booking_plan_no=$booking_no;
													$reqsn_no=$reqsn_details[$booking_no];
													$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
												}

												?>

												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">


													<td width="30" align="middle"><? echo $i; ?></td>
													<td width="70" align="middle"><p><? if($row['receive_date']!="" && $row['receive_date']!="0000-00-00") echo change_date_format($row['receive_date']); ?>&nbsp;</p></td>
													<td width="70" align="middle"><? echo $company_arr[$row['knitting_company']]; ?></td>
													<td width="70" align="middle"><? echo $floor_details[$floor_id]; ?></td>
													<td width="50" align="middle" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>
													<td width="60"  align="middle"><p><? echo $machine_dia_gage; ?></p></td>
													<td width="50" align="middle"><? echo $booking_no; ?></td>
													<td width="50" align="middle"><? echo $reqsn_no; ?></td>
													<td width="70" align="middle"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
													<td width="70" align="middle"><? echo $row['grouping']; ?></td>
													<td width="70" align="middle"><p><? echo $count; ?></p></td>
													<td width="80"><P>
														<?
														$brand_arr=array_unique(explode(",",$row['brand_id']));
														$all_brand="";
														foreach($brand_arr as $id)
														{
															$all_brand.=$brand_details[$id].",";
														}
														$all_brand=chop($all_brand," , ");
														echo $all_brand;
														?>&nbsp;</P>
													</td>
													<td width="80" align="middle"><P><? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
														$all_construction="";
														foreach($description_arr as $id)
														{
															$all_construction.=$construction_arr[$id].",";
														}
														$all_construction=chop($all_construction," , ");
														echo $all_construction;
														?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$all_composition="";
														foreach($description_arr as $id)
														{
															$all_composition.=$composition_arr[$id].",";
														}
														$all_composition=chop($all_composition," , ");
														echo $all_composition;
														?>&nbsp;</P>
													</td>
													<td width="110"><P>
														<?
														$color_arr=array_unique(explode(",",$row[('color_id')]));
														$all_color="";
														foreach($color_arr as $id)
														{
															$all_color.=$color_details[$id].",";
														}
														$all_color=chop($all_color," , ");
														echo $all_color;
														?>&nbsp;</P>
													</td>

													<td width="100"><P>
														<?
														$color_range_arr=array_unique(explode(",",$row['color_range_id']));
														$all_color_range="";
														foreach($color_range_arr as $id)
														{
															$all_color_range.=$color_range[$id].",";
														}
														$all_color_range=chop($all_color_range," , ");
														echo $all_color_range;
														?>&nbsp;</P>
													</td>

													<td width="60"  align="center" style="max-width:60px"><p><? echo  implode(",",array_unique(explode(",",$row['stitch_length']))); ?>&nbsp;</p></td>
													<td title="OP: <? echo $operator_name_arr[$row['operator_name']]; ?>" width="60"  align="center"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
													<?
													$row_tot_pcs_qnty=0;
													$row_tot_weight=0;
													$row_tot_roll=0;
													foreach($shift_name as $key=>$val)
													{
														$row_tot_roll+=$row['roll'.strtolower($val)];
														$row_tot_pcs_qnty+=$row['pcsshift'.strtolower($val)];
														$row_tot_weight+=$row['qntyshift'.strtolower($val)];

														$grand_total_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$inhouse_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$grand_total_ship[$key]+=$row['qntyshift'.strtolower($val)];
														$inhouse_ship[$key]+=$row['qntyshift'.strtolower($val)];
														$grand_total_roll[$key]+=$row['roll'.strtolower($val)];
														$inhouse_roll[$key]+=$row['roll'.strtolower($val)];

														?>
														<td width="100"><? echo $operator_name_arr[$row['operator_name'.strtolower($val)]]; //$operator_name_arr[$row['operator_name']]; ?></td>
														<td width="50"style="justify-content: center;text-align: center;" ><? echo $row['roll'.strtolower($val)]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['pcsshift'.strtolower($val)],2);?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['qntyshift'.strtolower($val)],2);?></td>
														<?
													}
													?>
													<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_roll,0); ?></td>
													<td width="80" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_weight,2,'.',''); ?></td>
													<?
													if($m==1)
													{
														$tot_machine_qty=$tot_macWise_inhouse_arr[$mc_id]['tot_shift'];
														?>
														<td width="80" valign="top" rowspan="<? echo $machine_rowspan_arr[$mc_id];?>" style="justify-content: center;text-align: center;"><? echo number_format($tot_machine_qty,2,'.',''); ?></td>
														<?
														$grand_machine_total+=$tot_machine_qty;
														$machine_total_inhouser+=$tot_machine_qty;
													}

													?>
													<td width="80" style="justify-content: center;text-align: center;"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
													</td>
													<td width="80"></td>
												</tr>

												<?
												$inhouse_tot_roll+=$row_tot_roll;
												$inhouse_tot_qty+=$row_tot_weight;
												$inhouse_tot_qty_pcs+=$row_tot_pcs;
												$grand_tot_qnty+=$row_tot_weight;
												$grand_tot_roll+=$row_tot_roll;
												$grand_reject_qty+=$row['reject_qty'];
												$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="19" align="right"><b>In-house Total(with order)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td width="100" align="right"></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($inhouse_roll[$key],0,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($inhouse_ship_pcs[$key],2,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($inhouse_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td style="justify-content: center;text-align: center;"><? echo number_format($inhouse_tot_roll,0,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($inhouse_tot_qty,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($machine_total_inhouser,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
										<td width="80">&nbsp;</td>

									</tr>
									<?
							}

							if (count($macWise_outhouse_arr)>0)
							{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_dtl_count+24; ?>" align="left" ><b>Outbound</b></td>
								</tr>
								<?
								$km=0;$tot_reject_qty=0;$reqsn_no='';
								foreach ($macWise_outhouse_arr as $mc_id=>$mc_data)
								{
									$m=1;
									foreach ($mc_data as $floor_id=>$floor_data)
									{
										foreach ($floor_data as $booking_no=>$booking_data)
										{
											foreach ($booking_data as $prod_id=>$row)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												$count='';
												$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
												foreach($yarn_count as $count_id)
												{
													if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
												}
												$booking_plan_no='';
												if($row[('receive_basis')]==2)
												{
													$reqsn_no=$reqsn_details[$booking_no];
													$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
													$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
												}
												else
												{
													$reqsn_no=$reqsn_details[$booking_no];
													$booking_plan_no=$booking_no;
													$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">


													<td width="30" align="middle"><? echo $i; ?></td>
													<td width="70" align="middle"><p><? if($row['receive_date']!="" && $row['receive_date']!="0000-00-00") echo change_date_format($row['receive_date']); ?>&nbsp;</p></td>
													<td width="70" align="middle"><? echo $supplier_arr[$row['knitting_company']]; ?></td>
													<td width="70" align="middle"><? //echo $floor_details[$floor_id]; ?></td>
													<td width="50" align="middle" title="<? echo $row[('receive_date')]; ?>"><p><? //echo $row[('machine_name')]; ?></p></td>
													<td width="60"  align="middle"><p><? //echo $machine_dia_gage; ?></p></td>
													<td width="50" align="middle"><? echo $booking_no; ?></td>
													<td width="50" align="middle"><? echo $reqsn_no; ?></td>
													<td width="70" align="middle"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
													<td width="70" align="middle"><? echo $row['grouping']; ?></td>
													<td width="70" align="middle"><p><? echo $count; ?></p></td>
													<td width="80"><P>
														<?
														$brand_arr=array_unique(explode(",",$row['brand_id']));
														$all_brand="";
														foreach($brand_arr as $id)
														{
															$all_brand.=$brand_details[$id].",";
														}
														$all_brand=chop($all_brand," , ");
														echo $all_brand;
														?>&nbsp;</P>
													</td>
													<td width="80" align="center"><P><? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
														$all_construction="";
														foreach($description_arr as $id)
														{
															$all_construction.=$construction_arr[$id].",";
														}
														$all_construction=chop($all_construction," , ");
														echo $all_construction;
														?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$all_composition="";
														foreach($description_arr as $id)
														{
															$all_composition.=$composition_arr[$id].",";
														}
														$all_composition=chop($all_composition," , ");
														echo $all_composition;
														?>&nbsp;</P>
													</td>
													<td width="110"><P>
														<?
														$color_arr=array_unique(explode(",",$row[('color_id')]));
														$all_color="";
														foreach($color_arr as $id)
														{
															$all_color.=$color_details[$id].",";
														}
														$all_color=chop($all_color," , ");
														echo $all_color;
														?>&nbsp;</P>
													</td>

													<td width="100"><P>
														<?
														$color_range_arr=array_unique(explode(",",$row['color_range_id']));
														$all_color_range="";
														foreach($color_range_arr as $id)
														{
															$all_color_range.=$color_range[$id].",";
														}
														$all_color_range=chop($all_color_range," , ");
														echo $all_color_range;
														?>&nbsp;</P>
													</td>

													<td width="60"  align="middle" style="max-width:60px"><p><? echo  implode(",",array_unique(explode(",",$row['stitch_length']))); ?>&nbsp;</p></td>
													<td width="60"  align="middle"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
													<?
													$row_tot_pcs_qnty=0;
													$row_tot_weight=0;
													$row_tot_roll=0;
													foreach($shift_name as $key=>$val)
													{
														$row_tot_roll+=$row['roll'.strtolower($val)];
														$row_tot_pcs_qnty+=$row['pcsshift'.strtolower($val)];
														$row_tot_weight+=$row['qntyshift'.strtolower($val)];

														$grand_total_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$out_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$grand_total_ship[$key]+=$row['qntyshift'.strtolower($val)];
														$out_ship[$key]+=$row['qntyshift'.strtolower($val)];

														$grand_total_roll[$key]+=$row['roll'.strtolower($val)];
														$out_roll[$key]+=$row['roll'.strtolower($val)];

														?>
														<td width="100"><? echo $operator_name_arr[$row['operator_name']]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo $row['roll'.strtolower($val)]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['pcsshift'.strtolower($val)],2);?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['qntyshift'.strtolower($val)],2);?></td>
														<?
													}
													?>
													<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_roll,0); ?></td>
													<td width="80" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_weight,2,'.',''); ?></td>
													<?
													// if($m==1)
													// {
														$tot_machine_qty=$tot_macWise_outhouse_arr[$mc_id]['tot_shift'];
														?>
														<td width="80" valign="top" rowspan="<? //echo $machine_rowspan_arr[$mc_id];?>" align="middle"><? echo number_format($tot_machine_qty,2,'.',''); ?></td>
														<?
														$grand_machine_total+=$tot_machine_qty;
														$machine_total_out+=$tot_machine_qty;
													// }

													?>
													<td width="80" style="justify-content: center;text-align: center;"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
													</td>
													<td width="80"></td>
												</tr>
												<?
												$out_tot_roll+=$row_tot_roll;
												$out_tot_qty+=$row_tot_weight;
												$out_tot_qty_pcs+=$row_tot_pcs;
												$grand_tot_qnty+=$row_tot_weight;
												$grand_tot_roll+=$row_tot_roll;
												$grand_reject_qty+=$row['reject_qty'];
												$i++;	$m++;
											} //Product End
										} //Booking End
									} //Floor End
								} //Machine End

								?>
								<tr class="tbl_bottom">
									<td colspan="19" align="right"><b>Outbound Total(with order)</b></td>
									<?
									foreach($shift_name as $key=>$val)
									{
										?>
										<td width="100" align="right"></td>
										<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($out_roll[$key],0,'.',''); ?></td>
										<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($out_ship_pcs[$key],2,'.',''); ?></td>
										<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($out_ship[$key],2,'.',''); ?></td>
										<?
									}
									?>
										<td style="justify-content: center;text-align: center;"><? echo number_format($out_tot_roll,0,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($out_tot_qty,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($machine_total_out,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
										<td width="80">&nbsp;</td>

								</tr>
								<?
							}

							if (count($macWise_sampwith_arr)>0) //Sample With Order
							{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_dtl_count+24; ?>" align="left" ><b>Sample With Order</b></td>
								</tr>
								<?
								$km=0;$tot_reject_qty=0;$reqsn_no='';
								foreach ($macWise_sampwith_arr as $mc_id=>$mc_data)
								{
									$m=1;
									foreach ($mc_data as $floor_id=>$floor_data)
									{
										foreach ($floor_data as $booking_no=>$booking_data)
										{
											foreach ($booking_data as $prod_id=>$row)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												$count='';
												$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
												foreach($yarn_count as $count_id)
												{
													if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
												}
												$booking_plan_no='';
												if($row[('receive_basis')]==2)
												{
													$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
													$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
												}
												else
												{
													$booking_plan_no=$booking_no;
													$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">


													<td width="30" align="middle"><? echo $i; ?></td>
													<td width="70" align="middle"><p><? if($row['receive_date']!="" && $row['receive_date']!="0000-00-00") echo change_date_format($row['receive_date']); ?>&nbsp;</p></td>
													<td width="70" align="middle"><? echo $company_arr[$row['knitting_company']]; ?></td>
													<td width="70" align="middle"><? echo $floor_details[$floor_id]; ?></td>
													<td width="50" align="middle" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>
													<td width="60"  align="middle"><p><? echo $machine_dia_gage; ?></p></td>
													<td width="50" align="middle"><? echo $booking_no; ?></td>
													<td width="50" align="middle"><? echo $reqsn_no; ?></td>
													<td width="70" align="middle"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
													<td width="70" align="middle"><? echo $row['grouping']; ?></td>
													<td width="70" align="middle"><p><? echo $count; ?></p></td>
													<td width="80"><P>
														<?
														$brand_arr=array_unique(explode(",",$row['brand_id']));
														$all_brand="";
														foreach($brand_arr as $id)
														{
															$all_brand.=$brand_details[$id].",";
														}
														$all_brand=chop($all_brand," , ");
														echo $all_brand;
														?>&nbsp;</P>
													</td>
													<td width="80" align="middle"><P><? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
														$all_construction="";
														foreach($description_arr as $id)
														{
															$all_construction.=$construction_arr[$id].",";
														}
														$all_construction=chop($all_construction," , ");
														echo $all_construction;
														?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$all_composition="";
														foreach($description_arr as $id)
														{
															$all_composition.=$composition_arr[$id].",";
														}
														$all_composition=chop($all_composition," , ");
														echo $all_composition;
														?>&nbsp;</P>
													</td>
													<td width="110"><P>
														<?
														$color_arr=array_unique(explode(",",$row[('color_id')]));
														$all_color="";
														foreach($color_arr as $id)
														{
															$all_color.=$color_details[$id].",";
														}
														$all_color=chop($all_color," , ");
														echo $all_color;
														?>&nbsp;</P>
													</td>

													<td width="100"><P>
														<?
														$color_range_arr=array_unique(explode(",",$row['color_range_id']));
														$all_color_range="";
														foreach($color_range_arr as $id)
														{
															$all_color_range.=$color_range[$id].",";
														}
														$all_color_range=chop($all_color_range," , ");
														echo $all_color_range;
														?>&nbsp;</P>
													</td>

													<td width="60"  align="middle" style="max-width:60px"><p><? echo  implode(",",array_unique(explode(",",$row['stitch_length']))); ?>&nbsp;</p></td>
													<td width="60"  align="middle"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
													<?
													$row_tot_pcs_qnty=0;
													$row_tot_weight=0;
													$row_tot_roll=0;
													foreach($shift_name as $key=>$val)
													{
														$row_tot_roll+=$row['roll'.strtolower($val)];
														$row_tot_pcs_qnty+=$row['pcsshift'.strtolower($val)];
														$row_tot_weight+=$row['qntyshift'.strtolower($val)];

														$grand_total_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$sam_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$grand_total_ship[$key]+=$row['qntyshift'.strtolower($val)];
														$sam_ship[$key]+=$row['qntyshift'.strtolower($val)];
														$grand_total_roll[$key]+=$row['roll'.strtolower($val)];
														$sam_roll[$key]+=$row['roll'.strtolower($val)];

														?>
														<td width="100"><? echo $operator_name_arr[$row['operator_name']]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;"><? echo $row['roll'.strtolower($val)]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['pcsshift'.strtolower($val)],2);?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['qntyshift'.strtolower($val)],2);?></td>
														<?
													}
													?>
													<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_roll,0); ?></td>
													<td width="80" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_weight,2,'.',''); ?></td>
													<?
													if($m==1)
													{
														$tot_machine_qty=$tot_macWise_sampwith_arr[$mc_id]['tot_shift'];
														?>
														<td width="80" valign="top" rowspan="<? echo $samp_machine_rowspan_arr[$mc_id];?>" style="justify-content: center;text-align: center;"><? echo number_format($tot_machine_qty,2,'.',''); ?></td>
														<?
														$grand_machine_total+=$tot_machine_qty;
														$machine_total_sam+=$tot_machine_qty;
													}

													?>
													<td width="80" style="justify-content: center;text-align: center;"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
													</td>
													<td width="80"></td>
												</tr>
												<?
												$sam_tot_roll+=$row_tot_roll;
												$sam_tot_qty+=$row_tot_weight;
												$sam_tot_qty_pcs+=$row_tot_pcs;
												$grand_tot_qnty+=$row_tot_weight;
												$grand_tot_roll+=$row_tot_roll;
												$grand_reject_qty+=$row['reject_qty'];
												$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="19" align="right"><b>Sample Total(with order)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td width="100" align="right"></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($sam_roll[$key],0,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($sam_ship_pcs[$key],2,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($sam_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td style="justify-content: center;text-align: center;"><? echo number_format($sam_tot_roll,0,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($sam_tot_qty,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($machine_total_sam,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
										<td width="80">&nbsp;</td>

									</tr>
									<?
							}

							if (count($macWise_sampout_arr)>0) //Sample Without Order
							{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_dtl_count+24; ?>" align="left" ><b>Sample Without Order</b></td>
								</tr>
								<?
								$km=0;$tot_reject_qty=0;$reqsn_no='';
								foreach ($macWise_sampout_arr as $mc_id=>$mc_data)
								{
									$m=1;
									foreach ($mc_data as $floor_id=>$floor_data)
									{
										foreach ($floor_data as $booking_no=>$booking_data)
										{
											foreach ($booking_data as $prod_id=>$row)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												$count='';
												$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
												foreach($yarn_count as $count_id)
												{
													if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
												}
												$booking_plan_no='';
												if($row[('receive_basis')]==2)
												{
													$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
													$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
												}
												else
												{
													$booking_plan_no=$booking_no;
													$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">


													<td width="30" align="middle"><? echo $i; ?></td>
													<td width="70" align="middle"><p><? if($row['receive_date']!="" && $row['receive_date']!="0000-00-00") echo change_date_format($row['receive_date']); ?>&nbsp;</p></td>
													<td width="70" align="middle"><? echo $company_arr[$row['knitting_company']]; ?></td>
													<td width="70" align="middle"><? echo $floor_details[$floor_id]; ?></td>
													<td width="50" align="middle" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>
													<td width="60"  align="middle"><p><? echo $machine_dia_gage; ?></p></td>
													<td width="50" align="middle"><? echo $booking_no; ?></td>
													<td width="50" align="middle"><? //echo $reqsn_no; ?></td>
													<td width="70" align="middle"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
													<td width="70" align="middle"><? //echo $buyer_arr[$row[('buyer_id')]]; ?></td>
													<td width="70" align="middle"><p><? echo $count; ?></p></td>
													<td width="80"><P>
														<?
														$brand_arr=array_unique(explode(",",$row['brand_id']));
														$all_brand="";
														foreach($brand_arr as $id)
														{
															$all_brand.=$brand_details[$id].",";
														}
														$all_brand=chop($all_brand," , ");
														echo $all_brand;
														?>&nbsp;</P>
													</td>
													<td width="80" align="middle"><P><? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
														$all_construction="";
														foreach($description_arr as $id)
														{
															$all_construction.=$construction_arr[$id].",";
														}
														$all_construction=chop($all_construction," , ");
														echo $all_construction;
														?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$all_composition="";
														foreach($description_arr as $id)
														{
															$all_composition.=$composition_arr[$id].",";
														}
														$all_composition=chop($all_composition," , ");
														echo $all_composition;
														?>&nbsp;</P>
													</td>
													<td width="110"><P>
														<?
														$color_arr=array_unique(explode(",",$row[('color_id')]));
														$all_color="";
														foreach($color_arr as $id)
														{
															$all_color.=$color_details[$id].",";
														}
														$all_color=chop($all_color," , ");
														echo $all_color;
														?>&nbsp;</P>
													</td>

													<td width="100"><P>
														<?
														$color_range_arr=array_unique(explode(",",$row['color_range_id']));
														$all_color_range="";
														foreach($color_range_arr as $id)
														{
															$all_color_range.=$color_range[$id].",";
														}
														$all_color_range=chop($all_color_range," , ");
														echo $all_color_range;
														?>&nbsp;</P>
													</td>

													<td width="60"  align="middle" style="max-width:60px"><p><? echo  implode(",",array_unique(explode(",",$row['stitch_length']))); ?>&nbsp;</p></td>
													<td width="60"  align="middle"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
													<?
													$row_tot_pcs_qnty=0;
													$row_tot_weight=0;
													$row_tot_roll=0;
													foreach($shift_name as $key=>$val)
													{
														$row_tot_roll+=$row['roll'.strtolower($val)];
														$row_tot_pcs_qnty+=$row['pcsshift'.strtolower($val)];
														$row_tot_weight+=$row['qntyshift'.strtolower($val)];

														$grand_total_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$sam_out_ship_pcs[$key]+=$row['pcsshift'.strtolower($val)];
														$grand_total_ship[$key]+=$row['qntyshift'.strtolower($val)];
														$sam_out_ship[$key]+=$row['qntyshift'.strtolower($val)];
														$grand_total_roll[$key]+=$row['roll'.strtolower($val)];
														$sam_out_roll[$key]+=$row['roll'.strtolower($val)];

														?>
														<td width="100"><? echo $operator_name_arr[$row['operator_name']]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo $row['roll'.strtolower($val)]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['pcsshift'.strtolower($val)],2);?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['qntyshift'.strtolower($val)],2);?></td>
														<?
													}
													?>
													<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_roll,0); ?></td>
													<td width="80" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_weight,2,'.',''); ?></td>
													<?
													if($m==1)
													{
														$tot_machine_qty=$tot_macWise_sampout_arr[$mc_id]['tot_shift'];
														?>
														<td width="80" valign="top" rowspan="<? echo $sampout_machine_rowspan_arr[$mc_id];?>" style="justify-content: center;text-align: center;"><? echo number_format($tot_machine_qty,2,'.',''); ?></td>
														<?
														$grand_machine_total+=$tot_machine_qty;
														$machine_total_sam_out+=$tot_machine_qty;
													}

													?>
													<td width="80" style="justify-content: center;text-align: center;"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
													</td>
													<td width="80"></td>
												</tr>

												<?
												$sam_out_tot_roll+=$row_tot_roll;
												$sam_out_tot_qty+=$row_tot_weight;
												$sam_out_tot_qty_pcs+=$row_tot_pcs;
												$grand_tot_qnty+=$row_tot_weight;
												$grand_tot_roll+=$row_tot_roll;
												$grand_reject_qty+=$row['reject_qty'];
												$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="19" align="right"><b>Sample Without Order Total</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td width="100" align="right"></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($sam_out_roll[$key],0,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($sam_out_ship_pcs[$key],2,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($sam_out_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td style="justify-content: center;text-align: center;"><? echo number_format($sam_out_tot_roll,0,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($sam_out_tot_qty,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($machine_total_sam_out,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
										<td width="80">&nbsp;</td>

									</tr>
									<?
							}

							if (count($macWise_subcon_arr)>0) //SubCon
							{
								$item_arr = array();
								$sqlCharge = "SELECT id, const_comp, gsm FROM lib_subcon_charge WHERE status_active=1 and is_deleted=0";
								$resultCharge = sql_select($sqlCharge);
								foreach($resultCharge as $val)
							    {
									$item_arr[$val[csf('id')]] = $val[csf('const_comp')];
								}
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_dtl_count+24; ?>" align="left" ><b>SubCon Inhouse</b></td>
								</tr>
								<?
								$km=0;$tot_reject_qty=0;$reqsn_no='';
								foreach ($macWise_subcon_arr as $mc_id=>$mc_data)
								{
									$m=1;
									foreach ($mc_data as $floor_id=>$floor_data)
									{
										foreach ($floor_data as $booking_no=>$booking_data)
										{
											foreach ($booking_data as $prod_id=>$row)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												$count='';
												$yarn_count=array_unique(explode(",",$row[('yarn_count')]));
												foreach($yarn_count as $count_id)
												{
													if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
												}
												$booking_plan_no='';
												if($row[('receive_basis')]==2)
												{
													$booking_plan_no=$booking_no.', '.$plan_booking_arr[$booking_no];
													$machine_dia_gage=$knit_plan_arr[$booking_no]['machine_dia']." X ".$knit_plan_arr[$booking_no]['machine_gg'];
												}
												else
												{
													$booking_plan_no=$booking_no;
													$machine_dia_gage=$machine_details[$mc_id]['dia_width']." X ".$machine_details[$mc_id]['gauge'];
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">


													<td width="30" align="middle"><? echo $i; ?></td>
													<td width="70" align="middle"><p><? if($row['receive_date']!="" && $row['receive_date']!="0000-00-00") echo change_date_format($row['receive_date']); ?>&nbsp;</p></td>
													<td width="70" align="middle"><? echo $company_arr[$row['knitting_company']]; ?></td>
													<td width="70" align="middle"><? echo $floor_details[$floor_id]; ?></td>
													<td width="50" align="middle" title="<? echo $row[('receive_date')]; ?>"><p><? echo $row[('machine_name')]; ?></p></td>
													<td width="60"  align="middle"><p><? echo $machine_dia_gage; ?></p></td>
													<td width="50" align="middle"><p><? echo $booking_no; ?></p></td>
													<td width="50" align="middle"><? //echo $reqsn_no; ?></td>
													<td width="70" align="middle"><? echo $buyer_arr[$row[('buyer_id')]]; ?></td>
													<td width="70" align="middle"><? //echo $buyer_arr[$row[('buyer_id')]]; ?></td>
													<td width="70" align="middle"><p><? echo $count; ?></p></td>
													<td width="80"><P>
														<?
														$brand_arr=array_unique(explode(",",$row['brand_id']));
														$all_brand="";
														foreach($brand_arr as $id)
														{
															$all_brand.=$brand_details[$id].",";
														}
														$all_brand=chop($all_brand," , ");
														echo $all_brand;
														?>&nbsp;</P>
													</td>
													<td width="80" align="middle"><P><? echo implode(",",array_unique(explode(",",$row[('yarn_lot')]))); ?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$description_arr=array_unique(explode(",",$row[('feb_desc_id')]));
														$all_construction="";
														foreach($description_arr as $id)
														{
															if($row['main_process_id']==2 || $row['main_process_id']==3 || $row['main_process_id']==4 || $row['main_process_id']==6 || $row['main_process_id']==7)
															{
																$all_construction.=$item_arr[$id].",";
															}
															else
															{
																$all_construction.=$garments_item[$id].",";
															}
														}
														$all_construction=chop($all_construction," , ");
														echo $all_construction;
														?>&nbsp;</P>
													</td>
													<td width="100"><P>
														<?
														$all_composition="";
														foreach($description_arr as $id)
														{
															if($row['main_process_id']==2 || $row['main_process_id']==3 || $row['main_process_id']==4 || $row['main_process_id']==6 || $row['main_process_id']==7)
															{
																$all_composition.=$item_arr[$id].",";
															}
															else
															{
																$all_composition.=$garments_item[$id].",";
															}
														}
														$all_composition=chop($all_composition," , ");
														echo $all_composition;
														?>&nbsp;</P>
													</td>
													<td width="110"><P>
														<?
														$color_arr=array_unique(explode(",",$row[('color_id')]));
														$all_color="";
														foreach($color_arr as $id)
														{
															$all_color.=$color_details[$id].",";
														}
														$all_color=chop($all_color," , ");
														echo $all_color;
														?>&nbsp;</P>
													</td>

													<td width="100"><P>
														<?
														$color_range_arr=array_unique(explode(",",$row['color_range_id']));
														$all_color_range="";
														foreach($color_range_arr as $id)
														{
															$all_color_range.=$color_range[$id].",";
														}
														$all_color_range=chop($all_color_range," , ");
														echo $all_color_range;
														?>&nbsp;</P>
													</td>

													<td width="60"  align="middle" style="max-width:60px"><p><? echo  implode(",",array_unique(explode(",",$row['stitch_length']))); ?>&nbsp;</p></td>
													<td width="60"  align="middle"><p><? echo  implode(",",array_unique(explode(",",$row[('gsm')])));?>&nbsp;</p></td>
													<?
													$row_tot_pcs_qnty=0;
													$row_tot_weight=0;
													$row_tot_roll=0;
													foreach($shift_name as $key=>$val)
													{
														$row_tot_roll+=$row['roll'.strtolower($val)];
														$row_tot_pcs_qnty+=$row['shift'.strtolower($val)];
														$row_tot_weight+=$row['shift'.strtolower($val)];

														$grand_total_ship_pcs[$key]+=$row['shift'.strtolower($val)];
														$mc_subcon_ship_pcs[$key]+=$row['shift'.strtolower($val)];
														$grand_total_ship[$key]+=$row['shift'.strtolower($val)];
														$mc_subcon_ship[$key]+=$row['shift'.strtolower($val)];
														$grand_total_roll[$key]+=$row['roll'.strtolower($val)];
														$mc_subcon_roll[$key]+=$row['roll'.strtolower($val)];

														?>
														<td width="100"><? echo $operator_name_arr[$row['operator_name']]; ?></td>
														<td width="50"style="justify-content: center;text-align: center;" ><? echo $row['roll'.strtolower($val)]; ?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['shift'.strtolower($val)],2);?></td>
														<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row['shift'.strtolower($val)],2);?></td>
														<?
													}
													?>
													<td width="50" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_roll,0); ?></td>
													<td width="80" style="justify-content: center;text-align: center;" ><? echo number_format($row_tot_weight,2,'.',''); ?></td>
													<?
													if($m==1)
													{
														$tot_machine_qty=$tot_macWise_subcon_arr[$mc_id]['tot_shift'];
														?>
														<td width="80" valign="top" rowspan="<? echo $subcon_machine_rowspan_arr[$mc_id];?>" style="justify-content: center;text-align: center;"><? echo number_format($tot_machine_qty,2,'.',''); ?></td>
														<?
														$grand_machine_total+=$tot_machine_qty;
														$machine_total_mc_subcon+=$tot_machine_qty;
													}

													?>
													<td width="80" style="justify-content: center;text-align: center;"><p><? echo number_format($row[('reject_qty')],2,'.',''); ?></p>
													</td>
													<td width="80"></td>
												</tr>

												<?
												$mc_subcon_tot_roll+=$row_tot_roll;
												$mc_subcon_tot_qty+=$row_tot_weight;
												$mc_subcon_tot_qty_pcs+=$row_tot_pcs;
												$grand_tot_qnty+=$row_tot_weight;
												$grand_tot_roll+=$row_tot_roll;
												$grand_reject_qty+=$row['reject_qty'];
												$i++;	$m++;
												} //Product End
											} //Booking End
										} //Floor End
									} //Machine End

									?>
									<tr class="tbl_bottom">
										<td colspan="19" align="right"><b>In-house Total(with order)</b></td>
										<?
										foreach($shift_name as $key=>$val)
										{
											?>
											<td width="100" align="middle"></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($mc_subcon_roll[$key],0,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($mc_subcon_ship_pcs[$key],2,'.',''); ?></td>
											<td width="50" style="justify-content: center;text-align: center;"><? echo number_format($mc_subcon_ship[$key],2,'.',''); ?></td>
											<?
										}
										?>
										<td style="justify-content: center;text-align: center;"><? echo number_format($mc_subcon_tot_roll,0,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($mc_subcon_tot_qty,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($machine_total_mc_subcon,2,'.',''); ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
										<td width="80">&nbsp;</td>

									</tr>
									<?
							}

						}

						?>
					</tbody>
				</table>
			</div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table topics" id="rpt_tbl_footer" style="font-size: 14px;">
				<tfoot>
					<tr>
						<th width="30"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="50"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="50"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="110"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="60"></th>
						<?
						foreach($shift_name as $key=>$val)
						{
							?>
							<th align="middle" width="100"></th>
							<th style="justify-content: center;text-align: center;" width="50"><? echo number_format($grand_total_roll[$key],0,'.',''); ?></th>
							<th style="justify-content: center;text-align: center;" width="50"></th>
							<th style="justify-content: center;text-align: center;" width="50"><? echo number_format($grand_total_ship[$key],2,'.',''); ?></th>
							<?
						}
						?>
						<th style="justify-content: center;text-align: center;" width="50"><? echo number_format($grand_tot_roll,0,'.',''); ?></th>
						<th style="justify-content: center;text-align: center;" width="80"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
						<th style="justify-content: center;text-align: center;" width="80"><? echo number_format($grand_machine_total,2,'.',''); ?></th>
						<th width="80" style="justify-content: center;text-align: center;"><? echo number_format($grand_reject_qty,2,'.',''); ?></th>
						<th width="80" align="middle"></th>

					</tr>
				</tfoot>
			</table>
			<br />
			<?
			if($txt_date_from!="")
			{
				if($txt_date_to=="") $txt_date_to=$txt_date_from;
				$date_distance=datediff("d",$txt_date_from, $txt_date_to);
				$month_name=date('F',strtotime($txt_date_from));
				$year_name=date('Y',strtotime($txt_date_from));
				$day_of_month=explode("-",$txt_date_from);
				if($db_type==0)
				{
					$fist_day_of_month=$day_of_month[2]*1;
				}
				else
				{
					$fist_day_of_month=$day_of_month[0]*1;
				}
				$tot_machine=count($total_machine);
				$running_machine=count($total_running_machine);
				$stop_machine=$tot_machine-$running_machine;
				$running_machine_percent=(($running_machine/$tot_machine)*100);
				$stop_machine_percent=(($stop_machine/$tot_machine)*100);
				if($date_distance==1 && $fist_day_of_month>1)
				{
					$query_cond_month=date('m',strtotime($txt_date_from));
					$query_cond_year=date('Y',strtotime($txt_date_from));
					$sql_cond="";
					if($db_type==0) $sql_cond="  and month(a.receive_date)='$query_cond_month' and year(a.receive_date)='$query_cond_year'"; else $sql_cond="  and to_char(a.receive_date,'mm')='$query_cond_month' and to_char(a.receive_date,'yyyy')='$query_cond_year'";
					if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
					$sql_montyly_inhouse=sql_select("select sum(c.quantity ) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date<'".$txt_date_from."' $sql_cond");


					$sql_monthly_wout_order=sql_select("select sum( b.grey_receive_qnty) as qnty  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 $cbo_company_cond $company_working_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 and a.receive_date<'".$txt_date_from."' $sql_cond");

					$yesterday_prod=$sql_montyly_inhouse[0][csf("qnty")]+$sql_monthly_wout_order[0][csf("qnty")];
					$today_prod=$yesterday_prod+$grand_tot_qnty;
				}
				?>
				<table width="<? echo $tbl_width; ?>" class="topics" style="font-size: 14px;">
					<tr>
						<td width="25%"  valign="top">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
								<tr>
									<td>Total number of m/c running</td>
									<td width="100" style="justify-content: center;text-align: center;"><? echo $running_machine; ?></td>
									<td style="justify-content: center;text-align: center;" width="100"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
								</tr>
								<tr>
									<td>Total number of m/c stop</td>
									<td style="justify-content: center;text-align: center;"><? echo $stop_machine; ?></td>
									<td style="justify-content: center;text-align: center;"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
								</tr>
								<tr>
									<td>Total production</td>
									<td style="justify-content: center;text-align: center;"><? echo number_format($grand_tot_qnty,2); ?></td>
									<td align="middle">Kg</td>
								</tr>
							</table>
						</td>
						<td width="10%"  valign="top">&nbsp; </td>
						<td  width="25%" valign="top">
							<?
							if($date_distance==1 && $fist_day_of_month>1)
							{
								?>
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
									<tr>
										<td>Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td  style="justify-content: center;text-align: center;" width="100"><? echo number_format($yesterday_prod,2); ?></td>
										<td align="middle" width="100">Kg</td>
									</tr>
									<tr>
										<td>Upto today production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td style="justify-content: center;text-align: center;"><? echo number_format($today_prod,2); ?> </td>
										<td align="middle">Kg</td>
									</tr>
								</table>
								<?
							}
							?>
						</td>
						<td  valign="top">&nbsp; </td>
					</tr>
				</table>
				<?
			}
			?>

		</fieldset>
		<br>
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

	else if($report_type==7) // Prod. Wise 3
	{
		$tbl_width=2420+count($shift_name)*205;

		if($cbo_type==1 || $cbo_type==0)
		{
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_cond=" and f.job_no_prefix_num='$txt_job' "; else $job_cond="";
			if($txt_order!="") $order_cond=" and e.po_number like '%$txt_order%' "; else $order_cond="";
			if($int_ref!="") $int_ref_cond=" and e.grouping like '%$int_ref%' "; else $int_ref_cond="";
			if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";

			if($db_type==0)
			{
				$year_field="YEAR(f.insert_date)";
				$year_field_sam="YEAR(a.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_field="to_char(f.insert_date,'YYYY')";
				$year_field_sam="to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
			}
			else $year_field="";

			$from_date=$txt_date_from;
			if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);

			$date_con="";
			if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";

			$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			}

			if($db_type==2) $year_date=" to_char(a.insert_date,'YYYY') as year";
			else if ($db_type==0) $year_date=" year(a.insert_date) as year";
			$job_data=sql_select("select c.job_no as sales_order,a.job_no, b.booking_no, $year_date,a.style_ref_no,a.insert_date from wo_po_details_master a,wo_booking_dtls b,fabric_sales_order_mst c where a.job_no=b.job_no and c.sales_booking_no=b.booking_no and a.company_name in($cbo_company,$cbo_working_company_id)");
			foreach($job_data as $row)
			{
				$sales_booking_array[$row[csf('sales_order')]]['job_no']=$row[csf('job_no')];
				$sales_booking_array[$row[csf('sales_order')]]['style_ref_no']=$row[csf('style_ref_no')];
				$sales_booking_array[$row[csf('sales_order')]]['year']=$row[csf('year')];
			}

			$po_sub_array=array();	$po_array=array();
			$po_data=sql_select("select a.job_no, a.job_no_prefix_num, $year_field_sam as year, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name in($cbo_company,$cbo_working_company_id)");
			foreach($po_data as $row)
			{
				$po_sub_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
				$po_sub_array[$row[csf('id')]]['year']=$row[csf('year')];
				$po_sub_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$po_sub_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']=$row[csf('style_ref_no')];
			}

			unset($po_data);
			//var_dump($po_sub_array);
			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}
			unset($data_array);
			$knit_plan_arr=array();
			//$plan_data=sql_select("select id, color_range, stitch_length from ppl_planning_info_entry_dtls");
			$plan_data=sql_select("select b.id, b.color_range, b.stitch_length,a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.company_id in($cbo_company,$cbo_working_company_id)");

			foreach($plan_data as $row)
			{
				$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')];
				$plan_booking_arr[$row[csf('id')]]=$row[csf('booking_no')];
			}
			unset($plan_data);

		}
		if($cbo_type==2 || $cbo_type==0)
		{
			$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width, gauge,machine_group from lib_machine_name");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				$machine_details[$row[csf('id')]]['machine_group']=$row[csf('machine_group')];
			}
			unset($machine_data);
			if($db_type==0)
			{
				$year_sub_field="YEAR(e.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(e.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_sub_field="to_char(e.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_sub_cond=" and to_char(e.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";
			}
			else $year_sub_field="";

			if($db_type==0)
			{
				$select_color=", b.color_id as color_id";
				$group_color=", b.color_id";
			}
			else if($db_type==2)
			{
				$select_color=", nvl(b.color_id,0) as color_id";
				$group_color=", nvl(b.color_id,0)";
			}

			$from_date=$txt_date_from;
			if($txt_date_to=="") $to_date=$from_date; else $to_date=$txt_date_to;

			if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";

			if ($cbo_floor_id!=0) $floor_id_cond=" and b.floor_id='$cbo_floor_id'"; else $floor_id_cond="";
			if (str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and a.party_id=$cbo_buyer_name"; else $buyer_id_cond="";

			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_no_cond=" and e.job_no_prefix_num='$txt_job' "; else $job_no_cond="";
			if($txt_order!="") $order_no_cond=" and d.order_no like '%$txt_order%' "; else $order_no_cond="";
		}

		ob_start();
		if($cbo_company==0)
			$roll_level_company_cond="";
		else
			$roll_level_company_cond=" company_name in($cbo_company)";

		if($cbo_working_company==0)
			$roll_level_working_company_cond="";
		else
			$roll_level_working_company_cond=" company_name in($cbo_working_company)";

		$fabricData = sql_select("select fabric_roll_level from variable_settings_production where $roll_level_company_cond $roll_level_working_company_cond and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");

		$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate a","a.is_deleted=0 and a.status_active=1  and a.id=(select max(a.id) from currency_conversion_rate a where a.currency=2 and a.is_deleted=0 and a.status_active=1 $conversion_company_cond $conversion_company_cond2 )","",$con);
		// echo $conversion_rate.'=Test';
		//$conversion_rate_arr=return_library_array( "select company_id, conversion_rate from currency_conversion_rate order by id desc", "company_id", "conversion_rate"  );

		foreach ($fabricData as $row)
		{
			$roll_maintained_yesNo = $row[csf('fabric_roll_level')];
		}
		// ================== New Summary Start =============
		if ($cbo_type==0)
		{
			/*$plan_booking_arr=return_library_array( "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id", "booking_no");*/

					$i=1; $tot_rolla=''; $tot_rollb=''; $tot_rollc=''; $tot_rolla_qnty=0; $tot_rollb_qnty=0; $tot_rollc_qnty=0; $grand_tot_roll=''; $grand_tot_qnty=0;$tot_subcontract=0;$outbound_amount=0;
					$inside_outside_array=array(); $floor_array=array(); $receive_basis=array(0=>"Independent",1=>"Fabric Booking No",2=>"Knitting Plan");

					if($db_type==0)
					{
						$select_color=", b.color_id as COLOR_ID";
						$group_color=", b.color_id";
					}
					else if($db_type==2)
					{
						$select_color=", nvl(b.color_id,0) as COLOR_ID";
						$group_color=", nvl(b.color_id,0)";
					}


					$sql_inhouse="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then c.quantity_pcs else 0 end ) as pcsnoshift,f.job_no, a.company_id";
					if($roll_maintained_yesNo==1) // Yes
					{
						foreach($shift_name as $key=>$val)
						{
							$sql_inhouse.=", case when b.shift_name=$key then count(g.roll_no) else 0 end as roll".strtolower($val)."
							, sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
							", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val)
							;
						}
						$sql_inhouse.=" , case when b.shift_name=0 then count(g.roll_no) else 0 end as rollnoshift from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
						where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and b.id=c.dtls_id and e.job_id=f.id  and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond";

					}
					else // 2 No
					{
						foreach($shift_name as $key=>$val)
						{
							$sql_inhouse.=", case when b.shift_name=$key then 0 else 0 end as roll".strtolower($val)."
							, sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val).
							", sum(case when b.shift_name=$key then c.quantity_pcs else 0 end ) as pcsshift".strtolower($val)
							;
						}
						$sql_inhouse.=" , case when b.shift_name=0 then count(0) else 0 end as rollnoshift from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
						where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_id=f.id and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond";
					}
					$sql_inhouse.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no, d.seq_no,b.shift_name, f.job_no, a.company_id order by b.floor_id,d.seq_no";// b.floor_id,a.receive_date,

					//echo $sql_inhouse;//die;//3 seconds ind database
					//===========================================================================

					if($roll_maintained_yesNo==1) // Yes
					{
						$sql_subcontract="SELECT c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(g.qnty) as outqntyshift, sum(g.qc_pass_qnty_pcs) as outpcsshift, f.job_no, a.company_id
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f, pro_roll_details g
						where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and e.job_id=f.id and c.entry_form=2 and c.trans_type=1 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and a.receive_basis!=4 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id,b.machine_gg,b.machine_dia, c.po_breakdown_id, f.job_no, a.company_id order by b.floor_id,a.receive_date";
					}
					else
					{
						$sql_subcontract="SELECT c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift,sum(c.quantity_pcs ) as outpcsshift, f.job_no, a.company_id
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f
						where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_id=f.id and c.entry_form=2 and c.trans_type=1 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and a.receive_basis!=4 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id,b.machine_gg,b.machine_dia, c.po_breakdown_id, f.job_no, a.company_id order by b.floor_id,a.receive_date";
					}
					//echo $sql_subcontract; //die();
					//==========================================================================
					$sql_service_receive="select c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift
					from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f
					where c.po_breakdown_id=e.id and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_id=f.id and a.entry_form=22 and c.entry_form=22 and c.trans_type=1 and b.trans_id>0 and c.is_sales=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
					group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id, b.machine_gg,b.machine_dia, c.po_breakdown_id order by b.floor_id,a.receive_date";
					//echo $sql_service_receive;die;
					//=====================================================================
					$sql_wout_order="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, 0 as po_breakdown_id, d.machine_no as machine_name, '' as job_no_mst, '' po_number, 0 as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";
					if($roll_maintained_yesNo==1) // Yes
					{
						foreach($shift_name as $key=>$val)
						{
							$sql_wout_order.=", case when b.shift_name=$key then count(g.roll_no) end as roll".strtolower($val)."
							,sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
							", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
						}
						$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d , pro_roll_details g
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id = g.mst_id and b.id = g.dtls_id and g.entry_form = 2 and a.knitting_source=1 and a.receive_basis!=4 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
					}
					else
					{
						foreach($shift_name as $key=>$val)
						{
							$sql_wout_order.=", case when b.shift_name=$key then 0 end as roll".strtolower($val)."
							,sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val).
							", sum(case when b.shift_name=$key then b.grey_receive_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
						}

						$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.knitting_source=1 and a.receive_basis!=4 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
					}
					$sql_wout_order.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width ,d.machine_no, d.seq_no,b.shift_name order by b.floor_id,d.seq_no";//b.floor_id,a.receive_date,
					//echo $sql_wout_order;
					//=====================================================================
					$sql_wout_order_smn="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, 0 as po_breakdown_id, 0 as machine_name, '' as job_no_mst, '' po_number,  sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";
					if($roll_maintained_yesNo==1) // Yes
					{
						$sql_wout_order_smn .= ", sum(case when b.shift_name=0 then g.qnty else 0 end ) as qntynoshift";
						foreach($shift_name as $key=>$val)
						{
							$sql_wout_order_smn.=", case when b.shift_name=$key then count(g.roll_no) end as roll".strtolower($val)."
							,sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
							", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
						}
						$sql_wout_order_smn.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details g
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id = g.mst_id and b.id = g.dtls_id and g.entry_form = 2 and a.knitting_source=3 and a.receive_basis!=4 and b.machine_no_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
					}
					else
					{
						$sql_wout_order_smn .= ", sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift";
						foreach($shift_name as $key=>$val)
						{
							$sql_wout_order_smn.=", case when b.shift_name=$key then 0 end as roll".strtolower($val)."
							,sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val).
							", sum(case when b.shift_name=$key then b.grey_receive_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
						}

						$sql_wout_order_smn.=" from inv_receive_master a, pro_grey_prod_entry_dtls b
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.knitting_source=3 and a.receive_basis!=4 and b.machine_no_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
					}
					$sql_wout_order_smn.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,b.shift_name order by b.floor_id,a.receive_date";

					//echo $sql_wout_order_smn;//die();
					//==========================================================================

					$sql_knit_sales="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,  a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id as machine_id, b.machine_gg,b.machine_dia, b.floor_id $select_color,c.job_no, c.buyer_id,c.style_ref_no,d.machine_no as machine_name,
					sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift,
					sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
					foreach($shift_name as $key=>$val)
					{
						$sql_knit_sales.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."
						, sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
					}

					$sql_knit_sales.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details e, fabric_sales_order_mst c,lib_machine_name d
					where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=e.dtls_id and e.po_breakdown_id=c.id and b.machine_no_id=d.id and a.knitting_source=1 and e.is_sales=1 and b.machine_no_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $location_cond
					group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, c.buyer_id,c.style_ref_no,c.job_no,d.machine_no, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,d.seq_no order by b.floor_id,d.seq_no";//b.floor_id,a.receive_date,and c.within_group=2 and a.receive_basis=4
					//echo $sql_knit_sales;//1:20 sechond run time in database 48 data two days (max time)

					//echo $sql_inhouse."<br>".$sql_subcontract."<br>".$sql_service_receive."<br>".$sql_wout_order."<br>".$sql_wout_order_smn."<br>".$sql_knit_sales;//die;
					$nameArray_inhouse=sql_select($sql_inhouse);
					$nameArray_subcontract=sql_select($sql_subcontract);
					$nameArray_service_receive=sql_select($sql_service_receive);
					$nameArray_without_order=sql_select($sql_wout_order);
					$nameArray_without_order_smn=sql_select($sql_wout_order_smn);
					$nameArray_sales_order=sql_select($sql_knit_sales);

					//echo count($nameArray_subcontract);die;
					//echo "test9";die;
					if (count($nameArray_inhouse)>0 || count($nameArray_subcontract)>0)//for avg.rate
					{
						$job_no_prefix_arr = array();
						foreach ($nameArray_inhouse as $row)
						{
							$job_no_prefix_arr[$row[csf('job_no_prefix_num')]]=$row[csf('job_no_prefix_num')];
						}
						foreach ($nameArray_subcontract as $row)
						{
							$job_no_prefix_arr[$row[csf('job_no_prefix_num')]]=$row[csf('job_no_prefix_num')];
						}

						$job_no_pre = implode(",", $job_no_prefix_arr);
						$job_no_pre_cond="";
						if($job_no_pre)
						{
							$job_no_pre = implode(",",array_filter(array_unique(explode(",", $job_no_pre))));
							$job_no_arr = explode(",", $job_no_pre);
							if($db_type==0)
							{
								$job_no_pre_cond = " and a.job_no_prefix_num in ($job_no_pre )";
							}
							else
							{
								if(count($job_no_arr)>999)
								{
									$issue_roll_chunk_arr=array_chunk($job_no_arr, 999);
									$job_no_pre_cond=" and (";
									foreach ($issue_roll_chunk_arr as $value)
									{
										$job_no_pre_cond .=" a.job_no_prefix_num in (".implode(",", $value).") or ";
									}
									$job_no_pre_cond=chop($job_no_pre_cond,"or ");
									$job_no_pre_cond.=")";
								}
								else
								{
									$job_no_pre_cond = " and a.job_no_prefix_num in ($job_no_pre )";
								}
							}

							$rate_sql="SELECT b.job_no, b.fabric_description, b.charge_unit, c.lib_yarn_count_deter_id, a.job_no_prefix_num
							from wo_po_details_master a, wo_pre_cost_fab_conv_cost_dtls b, wo_pre_cost_fabric_cost_dtls c
							where a.job_no=b.job_no and b.job_no=c.job_no and b.fabric_description=c.id and b.cons_process=1  $job_no_pre_cond
							group by b.job_no, b.fabric_description, b.charge_unit, c.lib_yarn_count_deter_id, a.job_no_prefix_num";
							//echo $rate_sql;
							$rate_data=sql_select($rate_sql);
							$rate_arr=array();
							foreach ($rate_data as $key => $row)
							{
								$rate_arr[$row[csf('job_no')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('charge_unit')];
							}
						}
					}
					// echo "<pre>";print_r($job_no_prefix_arr);die;
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080px">
				<tr>
					<td width="735">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:820px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production </i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="10">Knit Production Summary (In-House + Outbound)</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="100">Buyer</th>
									<th width="90">Inhouse</th>
									<th width="90">Amount TK</th>
									<th width="90">Outbound-Subcon</th>
									<th width="90">Amount TK</th>
									<th width="100">Total</th>
									<th width="90">Sample With Order </th>
									<th width="90">Total Amount TK </th>
									<th width="90">Sample Without Order</th>
								</tr>
							</thead>
						</table>
						<div style="width:840px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820px" class="rpt_table" >
								<tbody>
									<?
									$html .= '<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850px">'
									. '<tr>'
									. '<td width="555">'
									. '<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, \'Times New Roman\', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>'
									. '<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >'
									. '<thead>'
									. '<tr><th colspan="6">Knit Production Summary (In-House + Outbound)</th></tr>'
									. '<tr><th width="40">SL</th><th width="100">Buyer</th><th width="90">Inhouse</th><th width="90">Outbound-Subcon</th><th width="100">Total</th><th width="90">Sample Without Order</th></tr>'
									. '</thead></table>'
									. '<div style="width:570px; overflow-y:scroll; max-height:220px;" id="scroll_body">'
									. '<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >'
									. '<tbody>';
									//================================================================and b.machine_no_id>0

									//====================== for Inhouse==================
									//echo $sql_inhouse;

									foreach($nameArray_inhouse as $inh)
									{

										$booking_plan_no = $plan_booking_arr[$inh[csf('booking_id')]];

										$booking_no=explode("-",$booking_plan_no);
										$with_booking_no=$booking_no[1];

										if($with_booking_no=='SMN')
										{
											$knit_buyer_samary[$inh[csf('buyer_id')]]['with_out_qty']+= $inh[csf('qntyshifta')];
											$knit_buyer_samary[$inh[csf('buyer_id')]]['with_out_qty']+= $inh[csf('qntyshiftb')];
											$knit_buyer_samary[$inh[csf('buyer_id')]]['with_out_qty']+= $inh[csf('qntyshiftc')];
											$knit_buyer_samary[$inh[csf('buyer_id')]]['with_out_qty']+= $inh[csf('qntynoshift')];
										}
										else if($with_booking_no=='SM')
										{
											// $knit_buyer_samary[$inh[csf('buyer_id')]]['with_qty']+= $inh[csf('qntyshifta')];
											// $knit_buyer_samary[$inh[csf('buyer_id')]]['with_qty']+= $inh[csf('qntyshiftb')];
											// $knit_buyer_samary[$inh[csf('buyer_id')]]['with_qty']+= $inh[csf('qntyshiftc')];
											// $knit_buyer_samary[$inh[csf('buyer_id')]]['with_qty']+= $inh[csf('qntynoshift')];

											$knit_buyer_samary[$inh[csf('buyer_id')]]['qntyshifta']+= $inh[csf('qntyshifta')];
											$knit_buyer_samary[$inh[csf('buyer_id')]]['qntyshiftb']+= $inh[csf('qntyshiftb')];
											$knit_buyer_samary[$inh[csf('buyer_id')]]['qntyshiftc']+= $inh[csf('qntyshiftc')];
											$knit_buyer_samary[$inh[csf('buyer_id')]]['qntynoshift']+= $inh[csf('qntynoshift')];


										}


										// $knit_buyer_job_samary[$inh[csf('buyer_id')]][$inh[csf('job_no')]][$inh[csf('febric_description_id')]]['job_no'] = $inh[csf('job_no')];
										// $knit_buyer_job_samary[$inh[csf('buyer_id')]][$inh[csf('job_no')]][$inh[csf('febric_description_id')]]['febric_description_id'] = $inh[csf('febric_description_id')];


										$knit_buyer_samary[$inh[csf('buyer_id')]]['in_qty']+= $inh[csf('qntyshifta')];
										$knit_buyer_samary[$inh[csf('buyer_id')]]['in_qty']+= $inh[csf('qntyshiftb')];
										$knit_buyer_samary[$inh[csf('buyer_id')]]['in_qty']+= $inh[csf('qntyshiftc')];
										$knit_buyer_samary[$inh[csf('buyer_id')]]['in_qty']+= $inh[csf('qntynoshift')];

										$tot_in_Qnty = $inh[csf('qntyshifta')]+$inh[csf('qntyshiftb')]+$inh[csf('qntyshiftc')]+$inh[csf('qntynoshift')];


										$inhouse_avg_rate=$rate_arr[$inh[csf('job_no')]][$inh[csf('febric_description_id')]];
										$inhouse_rate_in_tk=$conversion_rate*$inhouse_avg_rate;
										$inhouse_amount=$tot_in_Qnty*$inhouse_rate_in_tk;

										$knit_buyer_samary[$inh[csf('buyer_id')]]['amount']+= $inhouse_amount;
										$knit_buyer_samary[$inh[csf('buyer_id')]]['inhouseamount']+= $inhouse_amount;

										$total_order_inhouse += $inh[csf('qntyshifta')]+$inh[csf('qntyshiftb')]+$inh[csf('qntyshiftc')]+$inh[csf('qntynoshift')];


									}
									// echo '<pre>';
									// print_r ($knit_buyer_job_samary);die;
									//var_dump($knit_buyer_samary);

									//====================== for outbound==================

									//echo $sql_subcontract;
									foreach($nameArray_subcontract as $sub)
									{
										$booking_plan_no = $plan_booking_arr[$sub[csf('booking_id')]];

										$booking_no=explode("-",$booking_plan_no);
										$with_booking_no=$booking_no[1];

										if($with_booking_no=='SMN')
										{
											$knit_buyer_samary[$sub[csf('buyer_id')]]['with_out_qty']+= $sub[csf('outqntyshift')];
										}
										else if($with_booking_no=='SM')
										{
											$knit_buyer_samary[$sub[csf('buyer_id')]]['with_qty']+= $sub[csf('outqntyshift')];

										}


										// $knit_buyer_job_out_samary[$sub[csf('buyer_id')]][$sub[csf('job_no')]][$sub[csf('febric_description_id')]]['job_no'] = $sub[csf('job_no')];
										// $knit_buyer_job_out_samary[$sub[csf('buyer_id')]][$sub[csf('job_no')]][$sub[csf('febric_description_id')]]['febric_description_id'] = $sub[csf('febric_description_id')];


										$knit_buyer_samary[$sub[csf('buyer_id')]]['out_qty']+= $sub[csf('outqntyshift')];
										$knit_buyer_samary[$sub[csf('buyer_id')]]['outqntyshiftt']+= $sub[csf('outqntyshift')];

										$tot_in_Qnty = $sub[csf('outqntyshift')];

										$total_order_outbound += $sub[csf('outqntyshift')];

										// $knit_buyer_samary[$inh[csf('buyer_id')]]['qntyshiftbb']+= $inh[csf('qntyshiftb')];
										// $knit_buyer_samary[$inh[csf('buyer_id')]]['qntyshiftcc']+= $inh[csf('qntyshiftc')];
										// $knit_buyer_samary[$inh[csf('buyer_id')]]['qntynoshiftt']+= $inh[csf('qntynoshift')];

										 $outbound_avg_rate=$rate_arr[$sub[csf('job_no')]][$sub[csf('febric_description_id')]];
										 $outbound_rate_in_tk=$conversion_rate*$outbound_avg_rate;
										  $outbound_amount=$tot_in_Qnty*$outbound_rate_in_tk;

										$knit_buyer_samary[$sub[csf('buyer_id')]]['amount']+= $outbound_amount;
										$knit_buyer_samary[$sub[csf('buyer_id')]]['outboundamount']+= $outbound_amount;




									}

									//var_dump($knit_buyer_samary);



									//====================== for sample without==================

									//echo $sql_wout_order;
									foreach($nameArray_without_order as $swo)
									{
										$booking_no=explode("-",$swo[csf('booking_no')]);

										$with_booking_no=$booking_no[1];

										if($with_booking_no=='SMN')
										{
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_out_qty']+= $swo[csf('qntyshifta')];
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_out_qty']+= $swo[csf('qntyshiftb')];
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_out_qty']+= $swo[csf('qntyshiftc')];
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_out_qty']+= $swo[csf('qntynoshift')];

											$total_sample_inhouse += $swo[csf('qntyshifta')]+$swo[csf('qntyshiftb')]+$swo[csf('qntyshiftc')]+$swo[csf('qntynoshift')];
										}
										else if($with_booking_no=='SM')
										{
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_qty']+= $swo[csf('qntyshifta')];
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_qty']+= $swo[csf('qntyshiftb')];
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_qty']+= $swo[csf('qntyshiftc')];
											$knit_buyer_samary[$swo[csf('buyer_id')]]['with_qty']+= $swo[csf('qntynoshift')];
										}

										$knit_buyer_samary[$swo[csf('buyer_id')]]['in_qty']+= $swo[csf('qntyshifta')];
										$knit_buyer_samary[$swo[csf('buyer_id')]]['in_qty']+= $swo[csf('qntyshiftb')];
										$knit_buyer_samary[$swo[csf('buyer_id')]]['in_qty']+= $swo[csf('qntyshiftc')];
										$knit_buyer_samary[$swo[csf('buyer_id')]]['in_qty']+= $swo[csf('qntynoshift')];
									}

									//====================== for sample without_order_smn==================

									//echo $sql_wout_order_smn;
									foreach($nameArray_without_order_smn as $swos)
									{
										$booking_no=explode("-",$swos[csf('booking_no')]);
										$with_booking_no=$booking_no[1];

										if($with_booking_no=='SMN')
										{

											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_out_qty']+= $swos[csf('qntyshifta')];
											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_out_qty']+= $swos[csf('qntyshiftb')];
											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_out_qty']+= $swos[csf('qntyshiftc')];
											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_out_qty']+= $swos[csf('qntynoshift')];
											$total_sample_outbound += $swos[csf('qntyshifta')]+$swos[csf('qntyshiftb')]+$swos[csf('qntyshiftc')]+$swos[csf('qntynoshift')];
										}
										else if($with_booking_no=='SM')
										{
											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_qty']+= $swos[csf('qntyshifta')];
											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_qty']+= $swos[csf('qntyshiftb')];
											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_qty']+= $swos[csf('qntyshiftc')];
											$knit_buyer_samary[$swos[csf('buyer_id')]]['with_qty']+= $swos[csf('qntynoshift')];
										}

										$knit_buyer_samary[$swos[csf('buyer_id')]]['out_qty']+= $swos[csf('qntyshifta')];
										$knit_buyer_samary[$swos[csf('buyer_id')]]['out_qty']+= $swos[csf('qntyshiftb')];
										$knit_buyer_samary[$swos[csf('buyer_id')]]['out_qty']+= $swos[csf('qntyshiftc')];
										$knit_buyer_samary[$swos[csf('buyer_id')]]['out_qty']+= $swos[csf('qntynoshift')];


									}



									/*echo $without_booking_no.'<br>';
									echo "<pre>";
									print_r($knit_buyer_samary);*/
									//die;
									$tot_without_ord_qty=$tot_with_ord_qty=0;
									$k=1;
									foreach($knit_buyer_samary as $buyer_id=>$rows)
									{
										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$out_bound_qnty=0;
										$out_bound_qnty=$rows[('out_qty')];
										$with_out_qty=$rows['with_out_qty'];



										$with_qty1=$rows['with_qty'];

										 $with_qty2 =$rows['qntyshifta']+$rows['qntyshiftb']+$rows['qntyshiftc']+$rows['qntynoshift'];

										$with_qty = $with_qty1+$with_qty2;

										$amount=$rows['amount'];
										$outboundamount=$rows['outboundamount'];
										$inhouseamount=$rows['inhouseamount'];
										$tot_row_summ=$rows[('in_qty')]+$out_bound_qnty;

										//echo $rows['job_no'];
										// $inhouse_amount=0;

										// foreach($knit_buyer_job_samary[$buyer_id] as $job_no=>$job_data)
										// {
										// 	foreach($job_data as $fabric_id=>$fabric_data)
										// 	{
										// 		$rate=$rate_arr[$job_no][$fabric_id];
										// 		$inhouse_rate_in_tk=$conversion_rate*$rate;
										// 		//$inhouse_amount += $with_qty2*$inhouse_rate_in_tk;
										// 		$inhouse_amount += $rows[('qntyshiftaa')]*$inhouse_rate_in_tk;
										// 		$inhouse_amount += $rows[('qntyshiftbb')]*$inhouse_rate_in_tk;
										// 		$inhouse_amount += $rows[('qntyshiftcc')]*$inhouse_rate_in_tk;
										// 		$inhouse_amount += $rows[('qntynoshiftt')]*$inhouse_rate_in_tk;


										// 	}
										// }

										// $outbound_amount=0;
										// foreach($knit_buyer_job_out_samary[$buyer_id] as $job_no=>$job_data)
										// {
										// 	foreach($job_data as $fabric_id=>$fabric_data)
										// 	{
										// 		$rate=$rate_arr[$job_no][$fabric_id];
										// 		$outbound_rate_in_tk=$conversion_rate*$rate;
										// 		//$outbound_amount += $with_qty1*$outbound_rate_in_tk;
										// 		$outbound_amount += $rows[('outqntyshiftt')]*$outbound_rate_in_tk;


										// 	}
										// }

										//echo  $rows['febric_description_id';


										 //$inhouse_avg_rate=$rate_arr[$rows[csf('job_no')]][$rows[csf('febric_description_id')]];



										  //$inhouse_amount=$with_qty*$inhouse_rate_in_tk;
											//number_format($inhouse_amount,2,'.','');
										//	$knit_buyer_samary[$inh[csf('buyer_id')]]['amount']+=$inhouse_amount;


										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="100" title="<? echo $buyer_id; ?>"><? echo $buyer_arr[$buyer_id]; ?></td>
											<td width="90" align="right"><? echo number_format($rows[('in_qty')],2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right"><? echo number_format($inhouseamount,2,'.',''); ?>&nbsp;</td>

											<td width="90" align="right" title="With Fab. Service Recv+SMN Outbound"><? echo number_format($out_bound_qnty,2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right"><? echo number_format($outboundamount,2,'.',''); ?>&nbsp;</td>
											<td width="100" align="right"><? echo  number_format($tot_row_summ,2,'.',''); ?>&nbsp;</td>

											<td width="90" align="right"><? echo number_format($with_qty,2,'.',''); ?>&nbsp;</td>
											<td width="90" align="right"><? echo number_format($amount,2,'.',''); ?>&nbsp;</td>

											<td width="90" align="right" title="Qnty with SMN Outbound <? echo $rows['with_out_qty']; ?>"><? echo number_format($with_out_qty,2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$html .= '<tr bgcolor="'. $bgcolor .'"><td width="40">'. $k .'</td><td width="100">'. $buyer_arr[$buyer_id].'</td>
										<td width="90" align="right">'. number_format($rows[("in_qty")],2,".","").'&nbsp;</td>
										<td width="90" align="right">'. number_format($out_bound_qnty,2,".","") .'&nbsp;</td>
										<td width="100" align="right">'.  number_format($tot_row_summ,2,".","").'&nbsp;</td>
										<td width="90" align="right">'. number_format($with_out_qty,2,".","").'&nbsp;</td>
										</tr>';

										$tot_qtyinhouse+=$rows[('in_qty')];
										$tot_qtyoutbound+=$out_bound_qnty;
										$tot_without_ord_qty+=$with_out_qty;
										$tot_with_ord_qty+=$with_qty;
										$tot_amount+=$amount;
										$tot_inhouseamount+=$inhouseamount;
										$tot_outboundamount+=$outboundamount;
										$total_summ+=$tot_row_summ;

										$k++;
									}
									//die;
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_inhouseamount,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_outboundamount,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_with_ord_qty,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_amount,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_without_ord_qty,2,'.',''); ?>&nbsp;</th>

									</tr>
									<tr>
										<th colspan="2"><strong>In %</strong></th>
										<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
										<th align="right">&nbsp;</th>
										<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
										<th align="right">&nbsp;</th>
										<th align="right"><? echo "100 %"; ?></th>
										<th align="right"><? //$qtyoutbound_per=($tot_with_ord_qty/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
										<th align="right">&nbsp;</th>

										<th align="right"><? //$qtyoutbound_per=($tot_without_ord_qty/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>

									</tr>
								</tfoot>
							</table>
						</div>
					</td>
					<td width="50">&nbsp;</td>
					<td valign="top">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:240px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>SubCon Order (Inbound) Knitting Production</i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="240px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="4">Knit Production Summary (Inbound)</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="120">Party </th>
									<th width="100">Total Inbound Production</th>
									<th width="80">Amount TK</th>
								</tr>
							</thead>
						</table>
						<div style="width:260px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="240px" class="rpt_table" >
								<tbody>
									<?
									$html .= '</tbody>
									<tfoot>
									<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right">'. number_format($tot_qtyinhouse,2,".","").'&nbsp;</th>
									<th align="right">'. number_format($tot_qtyoutbound,2,".","").'&nbsp;</th>
									<th align="right">'. number_format($tot_without_ord_qty,2,".","").'&nbsp;</th>
									<th align="right">'. number_format($total_summ,2,".","").'&nbsp;</th>
									</tr>
									<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right">'. number_format((($tot_qtyinhouse/$total_summ)*100),2).' % &nbsp;</th>
									<th align="right">'. number_format((($tot_qtyoutbound/$total_summ)*100),2).' % &nbsp;</th>
									<th align="right">'. number_format((($tot_without_ord_qty/$total_summ)*100),2).' % &nbsp;</th>
									<th align="right"> 100 %</th>
									</tr>
									</tfoot>
									</table>
									</div></td>
									<td width="50">&nbsp;</td>
									<td valign="top">
									<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, \'Times New Roman\', Times, serif;"><strong><u><i>SubCon Order (Inbound) Knitting Production</i></u></strong></div>
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<thead>
									<tr>
									<th colspan="6">Knit Production Summary (Inbound)</th>
									</tr>
									<tr>
									<th width="40">SL</th>
									<th width="120">Party </th>
									<th width="100">Total Inbound Production</th>
									<th width="80">Amount TK</th>
									</tr>
									</thead>
									</table>
									<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<tbody>';

									if($db_type	==0)
									{
										$order_production_relation = " and b.order_id  = d.id";
									}
									else
									{
										$order_production_relation = " and cast (b.order_id as varchar(4000)) = d.id";
									}
									// echo "string";die;

										$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
										$sql_inhouse_sub="SELECT DISTINCT b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, c.seq_no, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type, b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id $select_color, b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, $year_sub_field as year, d.order_no, d.cust_style_ref, sum(case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, sum(case when b.shift=0 then b.no_of_roll end ) as rollnoshift,d.job_no_mst";
										foreach($shift_name as $key=>$val)
										{
											$sql_inhouse_sub.=", sum(case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."
											, sum(case when b.shift=$key then b.product_qnty else 0 end ) as qntyshift".strtolower($val);
										}

										$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
										where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2 and d.status_active=1 and d.is_deleted=0
										and a.status_active=1 and a.is_deleted=0  $company_working_cond $cbo_company_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $order_production_relation
										group by b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,  b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id, b.color_id, b.order_id, c.machine_no, e.job_no_prefix_num, e.insert_date, d.order_no, d.cust_style_ref, c.seq_no,d.job_no_mst order by b.floor_id, a.product_date, c.seq_no";

											//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
										$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
										if (count($nameArray_inhouse_subcon)>0)//for avg.rate
										{
											$order_id_arr = array();
											foreach ($nameArray_inhouse_subcon as $row)
											{
												$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
											}

											$all_order_id = implode(",", $order_id_arr);
											$order_id_cond="";
											if($all_order_id)
											{
												$all_order_id = implode(",",array_filter(array_unique(explode(",", $all_order_id))));
												$order_id_arr = explode(",", $all_order_id);
												if($db_type==0)
												{
													$order_id_cond = " and c.order_id in ($all_order_id )";
												}
												else
												{
													if(count($order_id_arr)>999)
													{
														$order_id_chunk_arr=array_chunk($order_id_arr, 999);
														$order_id_cond=" and (";
														foreach ($order_id_chunk_arr as $value)
														{
															$order_id_cond .=" c.order_id in (".implode(",", $value).") or ";
														}
														$order_id_cond=chop($order_id_cond,"or ");
														$order_id_cond.=")";
													}
													else
													{
														$order_id_cond = " and c.order_id in ($all_order_id )";
													}
												}

												$inbound_rate_sql="SELECT c.job_no_mst, c.item_id, c.gsm, c.grey_dia, c.rate from subcon_ord_breakdown c where status_active=1 and is_deleted=0 $order_id_cond";
												$inbound_rate_data=sql_select($inbound_rate_sql);
												$inbound_rate_arr=array();
												foreach ($inbound_rate_data as $key => $row)
												{
													$inbound_rate_arr[$row[csf('job_no_mst')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('grey_dia')]]=$row[csf('rate')];
												}
												//echo "<pre>";print_r($inbound_rate_arr);
											}
										}


									// $sql_inhouse_sub_summ="select a.party_id, sum(b.product_qnty) as qntysubshift
									// from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
									// where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2
									// and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.order_id = d.id $sub_company_cond $company_working_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond
									// group by a.party_id";
									// //echo $sql_inhouse_sub_summ;die;//$subcompany_working_cond
									// $nameArray_inhouse_subcon_summ=sql_select( $sql_inhouse_sub_summ);

									foreach($nameArray_inhouse_subcon as $nis)
									{

										$knit_buyer_sub_samary[$nis[csf('party_id')]]['qnty']+= $nis[csf('qntyshifta')];
										$knit_buyer_sub_samary[$nis[csf('party_id')]]['qnty']+= $nis[csf('qntyshiftb')];
										$knit_buyer_sub_samary[$nis[csf('party_id')]]['qnty']+= $nis[csf('qntyshiftc')];
										$knit_buyer_sub_samary[$nis[csf('party_id')]]['qnty']+= $nis[csf('qntynoshift')];



										$sub_tota_qnty = $nis[csf('qntyshifta')]+$nis[csf('qntyshiftb')]+$nis[csf('qntyshiftc')]+$nis[csf('qntynoshift')];

										$inbound_rate=$inbound_rate_arr[$nis[csf('job_no_mst')]][$nis[csf('cons_comp_id')]][$nis[csf('gsm')]][$nis[csf('dia_width')]].',';

										$sub_tota_amount=$sub_tota_qnty*$inbound_rate;

										$knit_buyer_sub_samary[$nis[csf('party_id')]]['amount']+= $sub_tota_amount;


									}

									//var_dump($knit_buyer_sub_samary);

									$k=1;
									foreach($knit_buyer_sub_samary as $party_id => $rows)
									{
										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";



										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="120"><? echo $buyer_arr[$party_id]; ?></td>
											<td width="100" align="right"><? echo  number_format($rows['qnty'],2,'.',''); ?>&nbsp;</td>
											<td width="80" align="right"><? echo number_format($rows['amount'],2,'.',''); ?></td>
										</tr>
										<?
										$html .= '<tr bgcolor="'. $bgcolor.'">
										<td width="40">'. $k.'</td>
										<td width="120">'. $buyer_arr[$party_id].'</td>
										<td width="100" align="right">'.  number_format($rows['qnty'],2,".","").'&nbsp;</td>
										<td width="80">'.  number_format($rows['amount'],2,".","").'</td>
										</tr>';
										$tot_qty_sub_summ+=$rows['qnty'];
										$tot_amount_sub_summ+=$rows['amount'];
										unset($subcon_buyer_samary[$party_id]);
										$k++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($tot_qty_sub_summ,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format($tot_amount_sub_summ,2,'.',''); ?></th>
									</tr>
									<tr>
										<th colspan="2"><strong>In %</strong></th>
										<th align="right"><? echo "100 %"; ?></th>
										<th align="right"></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</td>
					<td width="50">&nbsp;</td>
					<td valign="top">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Fabric Sales Order Knitting Production</i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="6">Knit Production Summary (Sales Order)</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="120">Buyer </th>
									<th width="100">Total Inbound Production</th>
								</tr>
							</thead>
						</table>
						<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
								<tbody>
									<?
									$html .= '</tbody>
									<tfoot>
									<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right">'. number_format($tot_qty_sub_summ,2,".","").'&nbsp;</th>

									</tr>
									<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right">100 % &nbsp;</th>

									</tr>
									</tfoot>
									</table>
									</div></td>
									<td width="50">&nbsp;</td>
									<td valign="top">
									<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, \'Times New Roman\', Times, serif;"><strong><u><i>Fabric Sales Order Knitting Production</i></u></strong></div>
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<thead>
									<tr>
									<th colspan="6">Knit Production Summary (Sales Order)</th>
									</tr>
									<tr>
									<th width="40">SL</th>
									<th width="120">Buyer </th>
									<th width="100">Total Inbound Production</th>
									</tr>
									</thead>
									</table>
									<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
									<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
									<tbody>';

									$sql_sales_prod="select c.buyer_id,
									sum(case when b.machine_no_id>0 $floor_id  then b.grey_receive_qnty end ) as knit_sales_in
									from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details d, fabric_sales_order_mst c
									where a.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id=c.id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and d.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $location_cond
									group by c.buyer_id ";

									//echo $sql_sales_prod;die;

									$result_sales_prod=sql_select( $sql_sales_prod);
									foreach($result_sales_prod as $row)
									{
										$knit_sales_buyer_sammary[$row[csf('buyer_id')]]['knit_sales_in']+= $row[csf('knit_sales_in')];
									}
									unset($result_sales_prod);

									$tot_qty_sales_summ=0;
									$k=1;
									foreach($knit_sales_buyer_sammary as $buyer_id=>$rows)
									{
										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="120"><? echo $buyer_arr[$buyer_id]; ?></td>
											<td width="100" align="right"><? echo  number_format($rows[('knit_sales_in')],2,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$html .= '<tr bgcolor="'. $bgcolor.'">
										<td width="40">'. $k.'</td>
										<td width="120">'. $buyer_arr[$buyer_id].'</td>
										<td width="100" align="right">'.  number_format($rows[("knit_sales_in")],2,".","").'&nbsp;</td>
										</tr>';
										$tot_qty_sales_summ+=$rows[('knit_sales_in')];
										$k++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($tot_qty_sales_summ,2,'.',''); ?>&nbsp;</th>

									</tr>
									<tr>
										<th colspan="2"><strong>In %</strong></th>
										<th align="right"><? echo "100 %"; ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</td>
					<td width="50">&nbsp;</td>
					<td valign="top">
						<div align="left" style="background-color:#E1E1E1; color:#000; width:380px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Total Knitting Production Summary</i></u></strong></div>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="380px" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="6">Total Knitting Production Summary</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="120">Production Type </th>
									<th width="120">Total  Production </th>
									<th width="100">% Of Total</th>
								</tr>
							</thead>
						</table>
						<div style="width:400px; overflow-y:scroll; max-height:220px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="380px" class="rpt_table" >
								<tbody>
									<?
									$html .= '</tbody>
									<tfoot>
									<tr>
									<th colspan="2" align="right"><strong>Total</strong></th>
									<th align="right">'. number_format($tot_qty_sales_summ,2,".","").'&nbsp;</th>
									</tr>
									<tr>
									<th colspan="2"><strong>In %</strong></th>
									<th align="right">100 % &nbsp;</th>

									</tr>
									</tfoot>
									</table>
									</div></td>
									<td width="50">&nbsp;</td>
									';

									$total_summary_prod_qty=0;
									$k=1;
									$total_production_sammary=array(1=>'In-House', 2=>'Outbound-Subcontract Production', 3=>'Outbound-Subcontract Receive', 4=>'Sample Without Order (in house)', 5=>'Sample Without Order (Outbound)', 6=>'Fabric Sales Order Knitting Production', 7=>'Subcontract Order (In-bound) Knitting Production');

									$total_prod_sammaryQty=$total_order_inhouse+$total_order_outbound+$total_receive_outbound+$total_sample_inhouse+$total_sample_outbound+$tot_qty_sales_summ+$tot_qty_sub_summ;
									foreach($total_production_sammary as $type_id=>$val)
									{

										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										if($type_id==1) //Inhouse
										{
											$tot_production_qty=$total_order_inhouse;
										}
										else  if($type_id==2) //order OutBound
										{
											$tot_production_qty=$total_order_outbound;
										}
										else  if($type_id==3) //receive outbound
										{
											$tot_production_qty=$total_receive_outbound ;
										}
										else  if($type_id==4) //Sample inhouse
										{
											$tot_production_qty=$total_sample_inhouse;
										}
										else  if($type_id==5) //Sample Outbound
										{
											$tot_production_qty=$total_sample_outbound;
										}
										else  if($type_id==6) //Sales Order
										{
											$tot_production_qty=$tot_qty_sales_summ;
										}
										else  if($type_id==7) //inbound Subcontact
										{
											$tot_production_qty=$tot_qty_sub_summ;
										}
										$total_prod_per=number_format($tot_production_qty/$total_prod_sammaryQty,6,'.','');
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="40"><? echo $k; ?></td>
											<td width="120"><? echo $val; ?></td>
											<td width="120"  align="right"><? echo number_format($tot_production_qty,2,'.',''); ?></td>
											<td width="100" align="right"><? echo  number_format(($total_prod_per*100),4,'.',''); ?>&nbsp;</td>
										</tr>
										<?
										$total_summary_prod_qty+=$tot_production_qty;
										$k++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" align="right"><strong>Total</strong></th>
										<th align="right"><? echo number_format($total_summary_prod_qty,2,'.',''); ?>&nbsp;</th>
										<th align="right"><? echo number_format(($total_summary_prod_qty/$total_summary_prod_qty)*100,4,'.',''); ?>&nbsp;</th>
									</tr>

								</tfoot>
							</table>
						</div>
					</td>
				</tr>
			</table>
			<br />
			<?
		}
		//echo $template."Details";die;
		// ================== New Summary End ================


		?>
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="36" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="36" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="36" class="form_caption" style="font-size:12px" ><strong><? echo "From ".str_replace("'","",$txt_date_from)." To ".str_replace("'","",$txt_date_to); ?></strong></td>
			</tr>
		</table>
		<?
		if($cbo_type==1 || $cbo_type==0) // Self Order
		{
			if($template==1)
			{
				?>
				<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong>
				</div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+440; ?>" class="rpt_table" id="table_head" >
					<thead>
						<tr>
							<th width="40" rowspan="2" id="chk_hide"></th>
							<th width="30" rowspan="2">SL</th>
							<th width="55" rowspan="2">Knitting Party</th>
							<th width="60" rowspan="2">M/C No</th>
							<th width="60" rowspan="2">Job No</th>
							<th width="70" rowspan="2">File No.</th>
							<th width="70" rowspan="2">Int. Reff. No.</th>
							<th width="60" rowspan="2">Year</th>
							<th width="70" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Style</th>
							<th width="110" rowspan="2">Order No</th>
							<th width="90" rowspan="2">Prod. Basis</th>
							<th width="110" rowspan="2">Prog. No/ Booking No</th>
							<th width="60" rowspan="2">Prod. No</th>
							<th width="80" rowspan="2">Req. No.</th>
							<th width="80" rowspan="2">Yarn Count</th>
							<th width="90" rowspan="2">Yarn Brand</th>
							<th width="60" rowspan="2">Lot No</th>
							<th width="100" rowspan="2">Color Range</th>
							<th width="100" rowspan="2">Fabric Color</th>
							<th width="150" rowspan="2">Fabric Type</th>
							<th width="50" rowspan="2">M/C Dia</th>
							<th width="80" rowspan="2">M/C Gauge</th>
							<th width="50" rowspan="2">Fab. Dia</th>
							<th width="50" rowspan="2">Stitch</th>
							<th width="60" rowspan="2">Fin GSM</th>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="150" colspan="3"><? echo $val; ?></th>
								<?
							}
							?>
							<th width="150" colspan="3">No Shift</th>
							<th width="150" colspan="3">Total</th>
							<th rowspan="2" width="100">Avg. Rate (Tk)</th>
							<th rowspan="2" width="100">Amount (TK)</th>
							<th rowspan="2" width="100">Insert User</th>
							<th rowspan="2" width="100">Insert Date and Time</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="50" rowspan="2">Roll</th>
								<th width="50" rowspan="2">Pcs</th>
								<th width="100" rowspan="2">Qnty</th>
								<?
							}
							?>
							<th width="50" rowspan="2">Roll</th>
							<th width="50" rowspan="2">Pcs</th>
							<th width="100" rowspan="2">Qnty</th>
							<th width="50" rowspan="2">Roll</th>
							<th width="50" rowspan="2">Pcs</th>
							<th width="100" rowspan="2">Qnty</th>
						</tr>
					</thead>
				</table>
				<?
				$widths=$tbl_width+20;
				$html.="

				<fieldset style='width:".$widths."px;'>
				<table cellpadding='0' cellspacing='0' width='".$tbl_width."'>
				<tr>
				<td align='center' width='100%' colspan='36' class='form_caption' style='font-size:18px'>".$report_title."</td>
				</tr>
				<tr>
				<td align='center' width='100%' colspan='36' class='form_caption' style='font-size:16px'>".$company_arr[str_replace("'","",$cbo_company)]."</td>
				</tr>
				<tr>
				<td align='center' width='100%' colspan='36' class='form_caption' style='font-size:12px' ><strong>"."From ".str_replace("'","",$txt_date_from)." To ".str_replace("'","",$txt_date_to)."</strong></td>
				</tr>
				</table>
				<div align='left' style='background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;'><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>

				<table border='1'>
				<tr>
				<th width='30' rowspan='2'>SL</th>
				<th width='55' rowspan='2'>Knitting Party</th>
				<th width='60' rowspan='2'>M/C No</th>
				<th width='60' rowspan='2'>Job No</th>
				<th width='70' rowspan='2'>File No.</th>
				<th width='70' rowspan='2'>Int. Reff. No.</th>
				<th width='60' rowspan='2'>Year</th>
				<th width='70' rowspan='2'>Buyer</th>
				<th width='100' rowspan='2'>Style</th>
				<th width='110' rowspan='2'>Order No</th>
				<th width='90' rowspan='2'>Prod. Basis</th>
				<th width='110' rowspan='2'>Prog. No/ Booking No</th>
				<th width='60' rowspan='2'>Prod. No</th>
				<th width='80' rowspan='2'>Req. No.</th>
				<th width='80' rowspan='2'>Yarn Count</th>
				<th width='90' rowspan='2'>Yarn Brand</th>
				<th width='60' rowspan='2'>Lot No</th>
				<th width='100' rowspan='2'>Color Range</th>
				<th width='100' rowspan='2'>Fabric Color</th>
				<th width='150' rowspan='2'>Fabric Type</th>
				<th width='50' rowspan='2'>M/C Dia</th>
				<th width='80' rowspan='2'>M/C Gauge</th>
				<th width='50' rowspan='2'>Fab. Dia</th>
				<th width='50' rowspan='2'>Stitch</th>
				<th width='60' rowspan='2'>Fin GSM</th>";

				foreach($shift_name as $val)
				{
					$html.="<th width='150' colspan='3'>".$val."</th>";
				}
				$html.="
				<th width='150' colspan='2'>No Shift</th>
				<th width='150' colspan='2'>Total</th>
				<th rowspan='2'>Remarks</th>
				</tr>
				<tr>";
				foreach($shift_name as $val)
				{
					$html.="<th width='50'>Roll</th>
					<th width='50'>Pcs</th>
					<th width='100'>Qnty</th>";
				}
				$html.="
				<th width='50'>Roll</th>
				<th width='50'>Pcs</th>
				<th width='100'>Qnty</th>
				<th width='50'>Roll</th>
				<th width='50'>Pcs</th>
				<th width='100'>Qnty</th>
				</tr>";
				?>
				<div style="width:<? echo $tbl_width+460; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+440; ?>" class="rpt_table" id="table_body">
						<?
						$plan_booking_arr=return_library_array( "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id", "booking_no");
						$i=1; $tot_rolla=''; $tot_rollb=''; $tot_rollc=''; $tot_rolla_qnty=0; $tot_rollb_qnty=0; $tot_rollc_qnty=0; $grand_tot_roll=''; $grand_tot_qnty=0;$tot_subcontract=0;$outbound_amount=0;
						$inside_outside_array=array(); $floor_array=array(); $receive_basis=array(0=>"Independent",1=>"Fabric Booking No",2=>"Knitting Plan");

						if($db_type==0)
						{
							$select_color=", b.color_id as COLOR_ID";
							$group_color=", b.color_id";
						}
						else if($db_type==2)
						{
							$select_color=", nvl(b.color_id,0) as COLOR_ID";
							$group_color=", nvl(b.color_id,0)";
						}


						$sql_inhouse="SELECT b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then c.quantity_pcs else 0 end ) as pcsnoshift,f.job_no, a.company_id";
						if($roll_maintained_yesNo==1) // Yes
						{
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", case when b.shift_name=$key then count(g.roll_no) else 0 end as roll".strtolower($val)."
								, sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val)
								;
							}
							$sql_inhouse.=" , case when b.shift_name=0 then count(g.roll_no) else 0 end as rollnoshift from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
							where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond";
						}
						else // 2 No
						{
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", case when b.shift_name=$key then 0 else 0 end as roll".strtolower($val)."
								, sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then c.quantity_pcs else 0 end ) as pcsshift".strtolower($val)
								;
							}
							$sql_inhouse.=" , case when b.shift_name=0 then count(0) else 0 end as rollnoshift from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
							where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond";
						}
						$sql_inhouse.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no, d.seq_no,b.shift_name, f.job_no, a.company_id order by b.floor_id,d.seq_no";// b.floor_id,a.receive_date,

						//echo $sql_inhouse;//die;//3 seconds ind database
						//===========================================================================

						if($roll_maintained_yesNo==1) // Yes
						{
							$sql_subcontract="SELECT c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(g.qnty) as outqntyshift, sum(g.qc_pass_qnty_pcs) as outpcsshift, f.job_no, a.company_id
							from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f, pro_roll_details g
							where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and e.job_id=f.id and c.entry_form=2 and c.trans_type=1 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and a.receive_basis!=4 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id,b.machine_gg,b.machine_dia, c.po_breakdown_id, f.job_no, a.company_id order by b.floor_id,a.receive_date";
						}
						else
						{
							$sql_subcontract="SELECT c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift,sum(c.quantity_pcs ) as outpcsshift, f.job_no, a.company_id
							from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f
							where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_id=f.id and c.entry_form=2 and c.trans_type=1 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and a.receive_basis!=4 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
							group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id,b.machine_gg,b.machine_dia, c.po_breakdown_id, f.job_no, a.company_id order by b.floor_id,a.receive_date";
						}
						//echo $sql_subcontract; //die();
						//==========================================================================
						$sql_service_receive="select c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f
						where c.po_breakdown_id=e.id and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_id=f.id and a.entry_form=22 and c.entry_form=22 and c.trans_type=1 and b.trans_id>0 and c.is_sales=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con $buyer_cond $job_cond $order_cond $job_year_cond $location_cond
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width, b.machine_no_id, b.machine_gg,b.machine_dia, c.po_breakdown_id order by b.floor_id,a.receive_date";
						//echo $sql_service_receive;die;
						//=====================================================================
						$sql_wout_order="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, 0 as po_breakdown_id, d.machine_no as machine_name, '' as job_no_mst, '' po_number, 0 as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";
						if($roll_maintained_yesNo==1) // Yes
						{
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order.=", case when b.shift_name=$key then count(g.roll_no) end as roll".strtolower($val)."
								,sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
							}
							$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d , pro_roll_details g
							where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id = g.mst_id and b.id = g.dtls_id and g.entry_form = 2 and a.knitting_source=1 and a.receive_basis!=4 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
						}
						else
						{
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order.=", case when b.shift_name=$key then 0 end as roll".strtolower($val)."
								,sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then b.grey_receive_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
							}

							$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d
							where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.knitting_source=1 and a.receive_basis!=4 and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
						}
						$sql_wout_order.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width ,d.machine_no, d.seq_no,b.shift_name order by b.floor_id,d.seq_no";//b.floor_id,a.receive_date,
						//echo $sql_wout_order;
						//=====================================================================
						$sql_wout_order_smn="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.floor_id $select_color, 0 as po_breakdown_id, 0 as machine_name, '' as job_no_mst, '' po_number,  sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";
						if($roll_maintained_yesNo==1) // Yes
						{
							$sql_wout_order_smn .= ", sum(case when b.shift_name=0 then g.qnty else 0 end ) as qntynoshift";
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order_smn.=", case when b.shift_name=$key then count(g.roll_no) end as roll".strtolower($val)."
								,sum(case when b.shift_name=$key then g.qnty else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then g.qc_pass_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
							}
							$sql_wout_order_smn.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details g
							where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id = g.mst_id and b.id = g.dtls_id and g.entry_form = 2 and a.knitting_source=3 and a.receive_basis!=4 and b.machine_no_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
						}
						else
						{
							$sql_wout_order_smn .= ", sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift";
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order_smn.=", case when b.shift_name=$key then 0 end as roll".strtolower($val)."
								,sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val).
								", sum(case when b.shift_name=$key then b.grey_receive_qnty_pcs else 0 end ) as pcsshift".strtolower($val);
							}

							$sql_wout_order_smn.=" from inv_receive_master a, pro_grey_prod_entry_dtls b
							where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.knitting_source=3 and a.receive_basis!=4 and b.machine_no_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $cbo_company_cond $company_working_cond $date_con $floor_id  $buyer_cond $location_cond";
						}
						$sql_wout_order_smn.=" group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id,b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,b.shift_name order by b.floor_id,a.receive_date";

						//echo $sql_wout_order_smn;//die();
						//==========================================================================

						$sql_knit_sales="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,  a.remarks,a.inserted_by,a.insert_date, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id as machine_id, b.machine_gg,b.machine_dia, b.floor_id $select_color,c.job_no, c.buyer_id,c.style_ref_no,d.machine_no as machine_name,
						sum(case when b.shift_name=0 then b.grey_receive_qnty else 0 end ) as qntynoshift,
						sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
						foreach($shift_name as $key=>$val)
						{
							$sql_knit_sales.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."
							, sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
						}

						$sql_knit_sales.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details e, fabric_sales_order_mst c,lib_machine_name d
						where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=e.dtls_id and e.po_breakdown_id=c.id and b.machine_no_id=d.id and a.knitting_source=1 and e.is_sales=1 and b.machine_no_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_cond $company_working_cond $date_con $floor_id $buyer_cond $location_cond
						group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.machine_gg,b.machine_dia, b.yarn_lot, b.yarn_count, b.brand_id $group_color,b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, c.buyer_id,c.style_ref_no,c.job_no,d.machine_no, a.remarks,a.inserted_by,a.insert_date, b.febric_description_id, b.gsm, b.width,d.seq_no order by b.floor_id,d.seq_no";//b.floor_id,a.receive_date,and c.within_group=2 and a.receive_basis=4
						//echo $sql_knit_sales;//1:20 sechond run time in database 48 data two days (max time)

						//echo $sql_inhouse."<br>".$sql_subcontract."<br>".$sql_service_receive."<br>".$sql_wout_order."<br>".$sql_wout_order_smn."<br>".$sql_knit_sales;//die;
						$nameArray_inhouse=sql_select($sql_inhouse);
						$nameArray_subcontract=sql_select($sql_subcontract);
						$nameArray_service_receive=sql_select($sql_service_receive);
						$nameArray_without_order=sql_select($sql_wout_order);
						$nameArray_without_order_smn=sql_select($sql_wout_order_smn);
						$nameArray_sales_order=sql_select($sql_knit_sales);

						//echo count($nameArray_subcontract);die;
						//echo "test9";die;
						if (count($nameArray_inhouse)>0 || count($nameArray_subcontract)>0)//for avg.rate
						{
							$job_no_prefix_arr = array();
							foreach ($nameArray_inhouse as $row)
							{
								$job_no_prefix_arr[$row[csf('job_no_prefix_num')]]=$row[csf('job_no_prefix_num')];
							}
							foreach ($nameArray_subcontract as $row)
							{
								$job_no_prefix_arr[$row[csf('job_no_prefix_num')]]=$row[csf('job_no_prefix_num')];
							}

							$job_no_pre = implode(",", $job_no_prefix_arr);
						    $job_no_pre_cond="";
						    if($job_no_pre)
						    {
						        $job_no_pre = implode(",",array_filter(array_unique(explode(",", $job_no_pre))));
						        $job_no_arr = explode(",", $job_no_pre);
						        if($db_type==0)
						        {
						            $job_no_pre_cond = " and a.job_no_prefix_num in ($job_no_pre )";
						        }
						        else
						        {
						            if(count($job_no_arr)>999)
						            {
						                $issue_roll_chunk_arr=array_chunk($job_no_arr, 999);
						                $job_no_pre_cond=" and (";
						                foreach ($issue_roll_chunk_arr as $value)
						                {
						                    $job_no_pre_cond .=" a.job_no_prefix_num in (".implode(",", $value).") or ";
						                }
						                $job_no_pre_cond=chop($job_no_pre_cond,"or ");
						                $job_no_pre_cond.=")";
						            }
						            else
						            {
						                $job_no_pre_cond = " and a.job_no_prefix_num in ($job_no_pre )";
						            }
						        }

						        $rate_sql="SELECT b.job_no, b.fabric_description, b.charge_unit, c.lib_yarn_count_deter_id, a.job_no_prefix_num
								from wo_po_details_master a, wo_pre_cost_fab_conv_cost_dtls b, wo_pre_cost_fabric_cost_dtls c
								where a.job_no=b.job_no and b.job_no=c.job_no and b.fabric_description=c.id and b.cons_process=1  $job_no_pre_cond
								group by b.job_no, b.fabric_description, b.charge_unit, c.lib_yarn_count_deter_id, a.job_no_prefix_num";
								$rate_data=sql_select($rate_sql);
								$rate_arr=array();
								foreach ($rate_data as $key => $row)
								{
									$rate_arr[$row[csf('job_no')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('charge_unit')];
								}
						    }
						}
						// echo "<pre>";print_r($job_no_prefix_arr);die;


						if (count($nameArray_inhouse)>0)
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="46" align="left" ><b>In-House</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td align='left' ></td>
							<td colspan='36' align='left' ><b>In-House</b></td>
							</tr>";
							foreach ($nameArray_inhouse as $row)
							{

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$inhouse_avg_rate=$rate_arr[$row[csf('job_no')]][$row[csf('febric_description_id')]];

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no='';
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else $booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								//if(!in_array($row[csf('floor_id')],$floor_array))
								if($floor_array[$row[csf('floor_id')]]!="")
								{
									$floor_array[$row[csf('floor_id')]]=$row[csf('floor_id')];
									if($i!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="26" align="right"><b>Floor Total</b></td>
											<?
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												$floor_tot_pcs_row+=$floor_tot_roll[$key]['pcs'];
												?>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['pcs'],2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.','');


												?></td>
												<?
											}
											?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_pcs_row+$pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td align="right"><? echo number_format($inhouse_floor_total_amount,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<?
										$html.="<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
										<td align='right'>".number_format($noshift_total,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";
										unset($noshift_total);
										unset($floor_tot_roll);
										unset($inhouse_floor_total_amount);
									}
									if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") $floor_id_show= "Without Floor"; else $floor_id_show = $floor_details[$row[csf('floor_id')]];
									?>
									<tr><td colspan="44" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $floor_id_show; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='37' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>$floor_id_show</b></td></tr>";
									$floor_array[$i]=$row[csf('floor_id')];
								}

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle" id="chk_hide_dtls">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "1"; ?>" />
									</td>
									<td width="30"><? echo $i; ?></td>
									<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
									<td align="center" width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
									<td width="70"><? echo $row[csf('file_no')]; ?></td>
									<td width="70"><? echo $row[csf('grouping')]; ?></td>
									<td align="center" width="60"><p><? echo $row[csf('year')]; ?></p></td>
									<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
									<td width="100"><p><? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
									<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
									<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
									<td width="110" id="booking_no_<? echo $i; ?>" align="center"><P><? echo $booking_plan_no; ?></P></td>
									<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
									<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
									<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
									<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
									<td width="60" id="yarn_lot_<? echo $i; ?>" style="max-width:60px"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
									<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
									<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
										<?
										$color_arr=array_unique(explode(",",$row[csf('color_id')]));
										$all_color="";
										foreach($color_arr as $id)
										{
											$all_color.=$color_details[$id].",";
										}
										$all_color=chop($all_color," , ");
										echo $all_color;

										?></p>
									</td>
									<td width="150" title="<? echo 'febric descr id: '.$row[csf('febric_description_id')]; ?>" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
									<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')];?></p></td>
									<td width="80" id="mc_gause_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')];?></p></td>
									<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
									<td width="50" id="stich_<? echo $i; ?>" style="max-width:50px"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
									<?
									$html.="<tr>
									<td width='30'>".$i."</td>
									<td width='55'><p>".$knitting_party."&nbsp;</p></td>
									<td width='60'><p>".$row[csf('machine_name')]."</p></td>
									<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
									<td width='70'><p>".$row[csf('file_no')]."</p></td>
									<td width='70'><p>".$row[csf('grouping')]."</p></td>
									<td width='60'><p>".$row[csf('year')]."</p></td>
									<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
									<td width='100'><p>".$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']."</p></td>
									<td width='110'><p>".$row[csf('po_number')]."</p></td>
									<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
									<td width='110'><P>".$booking_plan_no."</P></td>
									<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
									<td width='80'>".$reqsn_no."</td>
									<td width='80'><p>".$count."</p></td>
									<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
									<td width='100'><p>&nbsp;".$color."</p></td>";
									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_details[$id].",";
									}
									$all_color=chop($all_color," , ");
									$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
									<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
									<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
									<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
									$row_tot_roll=0;
									$row_tot_qnty=0;
									$row_tot_pcs = 0;
									foreach($shift_name as $key=>$val)
									{
										$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
										$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
										$tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

										$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
										$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
										$source_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

										$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
										$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
										$floor_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

										$row_tot_roll+=$row[csf('roll'.strtolower($val))];
										$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
										$row_tot_pcs+=$row[csf('pcsshift'.strtolower($val))];
										?>
										<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
										<td width="50" align="right" ><? echo number_format($row[csf('pcsshift'.strtolower($val))],2);?></td>
										<td width="100" align="right" >
											<?
											echo number_format($row[csf('qntyshift'.strtolower($val))],2);
											$machineSamarryDataArr[$row[csf('machine_no_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
											?>
										</td>
										<?

										$html.="<td width='50' align='right' >".$row[csf('roll'.strtolower($val))]."</td>
										<td width='50' align='right' >".number_format($row[csf('pcsshift'.strtolower($val))],2)."</td>
										<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
									}

									?>
									<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>

									<td width="50" align="right" id="nopcs_<? echo $i; ?>"><? echo number_format($row[csf('pcsnoshift')],2); ?></td>
									<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
									<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
									<td width="50" align="right" id="pcs_<? echo $i; ?>"><? echo number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.',''); ?></td>
									<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','');
									$tot_in_Qnty=$row_tot_qnty+$row[csf('qntynoshift')]; ?></td>
									<td width="100" align="right" title="<? echo 'Job: '.$row[csf('job_no_prefix_num')].', Feb: '.$row[csf('febric_description_id')]; ?>"><p><? echo $inhouse_rate_in_tk=$conversion_rate*$inhouse_avg_rate; ?></p></td>
									<td width="100" align="right" title="<? echo 'Rate in USD: '.$inhouse_avg_rate; ?>"><p><?
									$inhouse_amount=$tot_in_Qnty*$inhouse_rate_in_tk;
									echo number_format($inhouse_amount,2,'.','')?></p></td>
									<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								</tbody>
								<?
								$html.="
								<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
								<td width='50' align='right'>".number_format($row[csf('pcsnoshift')],2)."</td>
								<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2)."</td>
								<td width='50' align='right'>".$row_tot_roll."</td>
								<td width='50' align='right'>".number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.','')."</td>
								<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
								<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
								</tr>
								</tbody>";

								$grand_tot_roll+=$row_tot_roll+$row[csf('no_of_roll')];
								$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
								$grand_tot_pcs+=$row_tot_pcs+$row[csf('pcsnoshift')];

								$source_grand_tot_roll+=$row_tot_roll;
								$source_grand_tot_qnty+=$row_tot_qnty;
								$source_grand_tot_pcs+=$row_tot_pcs;

								$rollshift_total+=$row[csf('rollnoshift')];
								$noshift_total+=$row[csf('qntynoshift')];
								$pcsnoshift_total+=$row[csf('pcsnoshift')];
								$inhouse_floor_total_amount+=$inhouse_amount;


								$grand_tot_floor_roll+=$row_tot_roll;
								$grand_tot_floor_qnty+=$row_tot_qnty;
								$grand_tot_floor_pcs+=$row_tot_pcs;
								$total_roll_noshift+=$row[csf('rollnoshift')];
								$total_qty_noshift+=$row[csf('qntynoshift')];
								$total_pcs_noshift+=$row[csf('pcsnoshift')];
								$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
								$in_house_total_amount+=$inhouse_amount;
								$grand_tot_amount+=$inhouse_amount;

								$i++;
							}

							?>
							<tr class="tbl_bottom">
								<td></td>
								<td colspan="25" align="right"><b>Floor Total </b></td>
								<?
								$floor_tot_qnty_row=0;
								foreach($shift_name as $key=>$val)
								{
									$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
									$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
									$floor_tot_pcs_row+=$floor_tot_roll[$key]['pcs'];
									?>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['pcs'],2,'.',''); ?></td>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($rollshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($floor_tot_roll_row+$rollshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($floor_tot_pcs_row+$pcsnoshift_total,2,'.',''); ?></td>
								<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($inhouse_floor_total_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="tbl_bottom">
								<td></td>
								<td colspan="25" align="right"><b>In House Total</b></td>
								<?
								foreach($shift_name as $key=>$val)
								{
									$source_tot_rolls+=$source_tot_roll[$key]['roll'];
									$source_tot_qnty+=$source_tot_roll[$key]['qty'];
									$source_tot_pcs+=$source_tot_roll[$key]['pcs'];
									$source_tot_roll_row+=$source_tot_roll[$key]['roll'];
									$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
									$source_tot_pcs_row+=$source_tot_roll[$key]['pcs'];

									?>
									<td align="right"><? echo number_format($source_tot_roll_row,2,'.',''); ?></td>
									<td align="right"><? echo number_format($source_tot_pcs_row,2,'.',''); ?></td>
									<td align="right"><? echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
									<?
									unset($source_tot_roll_row);
									unset($source_tot_qnty_row);
									unset($source_tot_pcs_row);
								}
								?>
								<td align="right"><? echo number_format($total_roll_noshift,2,'.',''); ?></td>
								<td align="right"><? echo number_format($total_pcs_noshift,2,'.',''); ?></td>
								<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?></td>
								<td align="right"><? echo number_format($source_tot_rolls,2,'.',''); ?></td>
								<td align="right"><? echo number_format($source_tot_pcs,2,'.',''); ?></td>
								<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($in_house_total_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
							$html.="<tr>
							<td colspan='25' align='right'><b>Floor Total</b></td>";

							$floor_tot_qnty_row=0;
							foreach($shift_name as $key=>$val)
							{
								$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];

								$html.="<td align='right'>&nbsp;</td>
								<td align='right'>&nbsp;</td>
								<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
							}
							$html.="
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
							<td align='right'>".number_format($noshift_total,2,'.','')."</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>pcs</td>
							<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
							<td>&nbsp;</td>
							</tr>
							<tr>
							<td colspan='25' align='right'><b>In House Total</b></td>";
							$source_tot_qnty=0;
							foreach($shift_name as $key=>$val)
							{
								$source_tot_qnty+=$source_tot_roll[$key]['qty'];
								$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
								$html.="<td align='right'>&nbsp;</td>
								<td align='right'>&nbsp;</td>
								<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";

								unset($source_tot_qnty_row);
							}
							$html.="
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
							<td>&nbsp;</td>
							</tr>";
						}
						// echo "<pre>";print_r($machineSamarryDataArr);die;
						// ******************************* Outbound-Subcontract Production ************************************
						if(count($nameArray_subcontract)>0) // Outbound Subcon
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="46" align="left"><b>Outbound-Subcontract Production</b></td>
							</tr>

							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left'><b>Outbound-Subcontract</b></td>
							</tr>";
							foreach ($nameArray_subcontract as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								$outbound_avg_rate=$rate_arr[$row[csf('job_no')]][$row[csf('febric_description_id')]];
								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no="";
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle">
									<!--<input type="checkbox" id="tbl_<? echo $i;?>" onClick="selected_row(<? //echo $i; ?>);" />-->
									<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
									<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('prod_id')]; ?>" />
									<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
									<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "3"; ?>" />
									<td width="30"><? echo $i; ?></td>
									<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
									<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['job_no']; ?></p></td>

									<td width="70"><? echo $row[csf('file_no')]; ?></td>
									<td width="70"><? echo $row[csf('grouping')]; ?></td>

									<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['year']; ?></p></td>
									<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
									<td width="100"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></p></td>
									<td width="110"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
									<td width="90"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
									<td width="110" align="center"><P><? echo $booking_plan_no; ?></P></td>
									<td width="60"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
									<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
									<td width="80"><p><? echo $count; ?>&nbsp;</p></td>
									<td width="90"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
									<td width="100"><p>&nbsp;<? echo $color; ?></p></td>
									<td width="100"><p>&nbsp;
									<?
									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_details[$id].",";
									}
									$all_color=chop($all_color," , ");
									echo $all_color;
									?></p></td>
									<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
									<td width="50"><p>&nbsp;<? echo $row[csf('machine_dia')];?></p></td>
									<td width="80"><p>&nbsp;<? echo $row[csf('machine_gg')];?></p></td>
									<td width="50"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
									<td width="50"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
									<?
									$html.="<tr>
									<td width='30'>".$i."</td>
									<td width='55'><p>".$knitting_party."&nbsp;</p></td>
									<td width='60'><p>".$row[csf('machine_name')]."</p></td>
									<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['job_no']."</p></td>
									<td width='70'><p>".$row[csf('file_no')]."</p></td>
									<td width='70'><p>".$row[csf('grouping')]."</p></td>
									<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['year']."</p></td>
									<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
									<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
									<td width='110'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['po_number']."</p></td>
									<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
									<td width='110'><P>".$booking_plan_no."</P></td>
									<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
									<td width='80'>".$reqsn_no."</td>
									<td width='80'><p>".$count."</p></td>
									<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
									<td width='100'><p>&nbsp;".$color."</p></td>";

									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_details[$id].",";
									}
									$all_color=chop($all_color," , ");
									$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
									<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
									<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
									<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
									<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
									<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";

									$row_tot_roll=0;
									$row_tot_qnty=0;
									foreach($shift_name as $key=>$val)
									{
										?>
										<td width="50" align="right"></td>
										<td width="50" align="right"></td>
										<td width="100" align="right"></td>

										<?
										$html.="<td width='50' align='right' ></td>
										<td width='50' align='right' ></td>
										<td width='100' align='right' ></td>";
									}
									?>
									<td width="50" align="right"><? echo $row_tot_roll; ?></td>
									<td width="50" align="right"><? echo number_format($row[csf('outpcsshift')],2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
									<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
									<td width="50" align="right"><? echo number_format($row[csf('outpcsshift')],2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
									<td width="100" align="right" title="<? echo 'Job: '.$row[csf('job_no_prefix_num')].', Feb: '.$row[csf('febric_description_id')]; ?>"><p><? echo $outbound_rate_in_tk=$conversion_rate*$outbound_avg_rate; ?></p></td>
									<td width="100" align="right" title="<? echo 'Rate in USD: '.$outbound_avg_rate; ?>"><p><?
									$outbound_amount=$row[csf('outqntyshift')]*$outbound_rate_in_tk;
									echo number_format($outbound_amount,2,'.',''); ?></p></td>
									<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$html.="
								<td width='50' align='right'>".$row_tot_roll."</td>
								<td width='50' align='right'>".number_format($row[csf('outpcsshift')],2,'.','')."</td>
								<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>

								<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
								<td width='50' align='right'>".number_format($row[csf('outpcsshift')],2,'.','')."</td>
								<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
								<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
								</tr>";
								$grand_tot_roll+=$row[csf('no_of_roll')];
								$grand_tot_qnty+=$row[csf('outqntyshift')];
								$grand_tot_pcs+=$row[csf('outpcsshift')];

								$source_grand_tot_qnty+=$row[csf('outqntyshift')];
								$source_grand_tot_pcs+=$row[csf('outpcsshift')];

								$tot_subcontract_noRoll+=$row_tot_roll;
								$tot_subcontract_roll+=$row[csf('no_of_roll')];
								$total_service_subcontact+=$row[csf('outqntyshift')];
								$tot_subcontract+=$row[csf('outqntyshift')];
								$tot_subcontract_pcs+=$row[csf('outpcsshift')];
								$tot_outbound_amount+=$outbound_amount;
								$grand_tot_amount+=$outbound_amount;

								$grand_tot_floor_qnty+=$row_tot_qnty;
								$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
								$i++;
							}
							?>
							<tr class="tbl_bottom">
								<td colspan="26" align="right"><b>Outbound-Subcontract Total</b></td>
								<?
								$source_tot_qnty_row=0;
								foreach($shift_name as $key=>$val)
								{
									?>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right"></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($tot_subcontract_noRoll,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract_pcs,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract_roll,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract_pcs,2,'.',''); ?></td>
								<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($tot_outbound_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
							$html.="<tr>
							<td colspan='25' align='right'><b>Outbound-Subcontract Total</b></td>";

							$floor_tot_qnty_row=0;
							foreach($shift_name as $key=>$val)
							{
								$html.="<td align='right'>&nbsp;</td>
								<td align='right'>&nbsp;</td>
								<td align='right'></td>";
							}
							$html.="
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>&nbsp;</td>
							<td align='right'>".number_format($tot_subcontract_pcs,2,'.','')."</td>
							<td align='right'>".number_format($tot_subcontract,2,'.','')."</td>
							<td>&nbsp;</td>
							</tr>";
						}
						 //echo "<pre>";print_r($machineSamarryDataArr);die;
						// **************************** Outbound-Subcontract Receive ************************
						if(count($nameArray_service_receive)>0)
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="44" align="left"><b>Outbound-Subcontract Receive</b></td>
							</tr>

							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left'><b>Outbound-Subcontract</b></td>
							</tr>";
							foreach ($nameArray_service_receive as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no='';
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('prod_id')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "3"; ?>" />
										<td width="30"><? echo $i; ?></td>
										<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
										<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['job_no']; ?></p></td>

										<td width="70"><? echo $row[csf('file_no')]; ?></td>
										<td width="70"><? echo $row[csf('grouping')]; ?></td>

										<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['year']; ?></p></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></p></td>
										<td width="110"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
										<td width="90"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
										<td width="110" align="center"><P><? echo $booking_plan_no; ?></P></td>
										<td width="60"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
										<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
										<td width="80"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100"><p>&nbsp;<? echo $color; ?></p></td>
										<td width="100"><p>&nbsp;
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
											<td width="80"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr>
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['job_no']."</p></td>
											<td width='70'><p>".$row[csf('file_no')]."</p></td>
											<td width='70'><p>".$row[csf('grouping')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['year']."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['po_number']."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$booking_plan_no."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";

											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
											<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";

											$row_tot_roll=0;
											$row_tot_qnty=0;
											foreach($shift_name as $key=>$val)
											{
												?>
												<td width="50" align="right"><? //echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="50" align="right"><? //echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right"><? //echo number_format($row[csf('outqntyshift'.strtolower($val))],2); ?></td>

												<?
												$html.="<td width='50' align='right' ></td>
												<td width='50' align='right' ></td>
												<td width='100' align='right' ></td>";
											}
											?>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											<td width="50" align="right">0.00</td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											<td width="50" align="right">0.00</td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
										<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
										<td width='50' align='right'>".$pcs."</td>
										<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
										<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
										<td width='50' align='right'>".$pcs."</td>
										<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
										<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
										</tr>";
										$grand_tot_roll+=$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row[csf('outqntyshift')];
										$source_grand_tot_qnty+=$row[csf('outqntyshift')];

										$tot_subcontract_noRoll+=$row[csf('no_of_roll')];
										$tot_subcontract_roll+=$row[csf('no_of_roll')];
										$tot_subcontract+=$row[csf('outqntyshift')];
										$total_service_subcontact+= $row[csf('outqntyshift')];
										$grand_tot_floor_qnty+=$row_tot_qnty;

										$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
										$i++;
									}

									?>
									<tr class="tbl_bottom">
										<td colspan="26" align="right"><b>Outbound-Subcontract Total</b></td>
										<?
										$source_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"></td>
											<?
										}
										?>
										<td align="right"><? echo number_format($tot_subcontract_noRoll,2,'.',''); ?></td>
										<td align="right">&nbsp; </td>

										<td align="right"><? echo number_format($total_service_subcontact,2,'.',''); ?></td>
										<td align="right"><? echo number_format($tot_subcontract_roll,2,'.',''); ?></td>
										<td align="right">&nbsp; </td>
										<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="<tr>
									<td colspan='25' align='right'><b>Outbound-Subcontract Total</b></td>";

									$floor_tot_qnty_row=0;
									foreach($shift_name as $key=>$val)
									{
										$html.="<td align='right'>&nbsp;</td>
										<td align='right'>&nbsp;</td>
										<td align='right'></td>";
									}
									$html.="
									<td align='right'>&nbsp;</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($total_service_subcontact,2,'.','')."</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($tot_subcontract,2,'.','')."</td>
									<td>&nbsp;</td>
									</tr>";
						}
						 //echo "<pre>";print_r($machineSamarryDataArr);die;
						unset($floor_array); $total_qty_noshift=0;
						unset($floor_tot_roll); unset($noshift_total); unset($pcsnoshift_total); unset($source_tot_roll);  $source_tot_qnty=0;
						$j=1;
						// Sample Without Order
						if (count($nameArray_without_order)>0)
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="46" align="left" ><b>Sample Without Order</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left' ><b>Sample Without Order</b></td>
							</tr>";
							foreach ($nameArray_without_order as $row)
							{
								if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no="";
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								if(!in_array($row[csf('floor_id')],$floor_array))
								{
									if($j!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="26" align="right"><b>Floor Total</b></td>
											<?
											$html.="<tr>
											<td colspan='25' align='right'><b>Floor Total</b></td>";
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												?>
												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.','');
													//$machineSamarryDataArr[$row[csf('machine_no_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
												 ?></td>
												<?
												$html.="<td align='right'>&nbsp;</td>
												<td align='right'>&nbsp;</td>
												<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
											}
											?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<?
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='ight'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";
										unset($noshift_total);
										unset($floor_tot_roll);
									}
									?>
									<tr><td colspan="46" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") echo "Without Floor"; else echo $floor_details[$row[csf('floor_id')]]; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='37' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>".$floor_details[$row[csf('floor_id')]]."</b></td></tr>";
									$floor_array[$i]=$row[csf('floor_id')];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle" id="">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "2"; ?>" /></td>
										<!-- 2 mean without order-->
										<td width="30"><? echo $i; ?></td>
										<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>

										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('year')]; ?></p></td>
										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p>&nbsp;<? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
										<td width="110"><p>&nbsp;<? echo $row[csf('po_number')]; ?></p></td>
										<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
										<td width="110" id="booking_no_<? echo $i; ?>"><P><? echo $booking_plan_no; ?></P></td>
										<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
										<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
										<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60" id="yarn_lot_<? echo $i; ?>" style="max-width:60px"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
											<td width="80" id="mc_gauge_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50" id="stich_<? echo $i; ?>" style="max-width:50px"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr>
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
											<td width='70'><p></p></td>
											<td width='70'><p></p></td>
											<td width='60'><p>".$row[csf('year')]."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$row[csf('po_number')]."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$booking_plan_no."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
											<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
											$row_tot_roll=0;
											$row_tot_qnty=0;
											$row_tot_pcs=0;
											foreach($shift_name as $key=>$val)
											{
												$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$source_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$floor_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$row_tot_roll+=$row[csf('roll'.strtolower($val))];
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
												$row_tot_pcs+=$row[csf('pcsshift'.strtolower($val))];
												?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="50" align="right" ><?
												echo number_format($row[csf('pcsshift'.strtolower($val))],2);?></td>
												<td width="100" align="right" >
													<?
													echo number_format($row[csf('qntyshift'.strtolower($val))],2);
													//$machineSamarryDataArr[$row[csf('machine_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
													$machineSamarryDataArr[$row[csf('machine_no_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
													?>
												</td>
												<?
												$html.="<td width='50' align='right'>".$row[csf('roll'.strtolower($val))]."</td>
												<td width='50' align='right'>".number_format($row[csf('pcsshift'.strtolower($val))],2)."</td>
												<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>																<td width="50" align="right" id="nopcs_<? echo $i; ?>"><? echo number_format($row[csf('pcsnoshift')],2); ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
											<td width="50" align="right" id="totpcs_<? echo $i; ?>"><? echo number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.',''); ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>

											<td width="100" align="right"></td>
											<td width="100" align="center"></td>

											<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
										<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
										<td width='50' align='right'>".number_format($row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2,'.','')."</td>
										<td width='50' align='right'>".$row_tot_roll."</td>
										<td width='50' align='right'>".number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
										<td><p>".$row[csf('remarks')]."</p></td>
										</tr>";
										$grand_tot_roll+=$row_tot_roll+$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
										$grand_tot_pcs+=$row_tot_pcs+$row[csf('pcsnoshift')];


										$source_grand_tot_roll+=$row_tot_roll;
										$source_grand_tot_qnty+=$row_tot_qnty;
										$source_grand_tot_pcs+=$row_tot_pcs;

										$noshift_total+=$row[csf('qntynoshift')];

										$grand_tot_floor_roll+=$row_tot_roll;
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$total_qty_noshift+=$row[csf('qntynoshift')];
										$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];

										$j++;
										$i++;
									}

									?></tbody>
									<tr class="tbl_bottom">
										<td colspan="26" align="right"><b>Floor Total</b></td>
										<?
										$html.="</tbody>
										<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.','');
												//$machineSamarryDataArr[$row[csf('machine_no_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
											?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
										<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
										<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr class="tbl_bottom">
										<td colspan="26" align="right"><b> Sample Without Order Total</b></td>
										<?

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>
										<tr>
										<td colspan='25' align='right'><b> Sample Without Order Total</b></td>";
										foreach($shift_name as $key=>$val)
										{
											$source_tot_qnty+=$source_tot_roll[$key]['qty'];
											$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><?  echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";
											unset($source_tot_qnty_row);
										}
										?>

										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
									<td>&nbsp;</td>
									</tr>";
						}
						// echo "<pre>";print_r($machineSamarryDataArr);die;
						unset($floor_array); $total_qty_noshift=0;
						unset($floor_tot_roll); unset($noshift_total); unset($pcsnoshift_total); unset($source_tot_roll);  $source_tot_qnty=0;
						$j=0;
						// **************** Sample Without Order Outbound **********************************
						if (count($nameArray_without_order_smn)>0)
						{
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="46" align="left" ><b>Sample Without Order Outbound</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td colspan='39' align='left' ><b>Sample Without Order Outbound</b></td>
							</tr>";
							foreach ($nameArray_without_order_smn as $row)
							{
								if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no="";
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no=$row[csf('booking_no')].', '.$plan_booking_arr[$row[csf('booking_id')]];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else
									$booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								if(!in_array($row[csf('floor_id')],$floor_array))
								{
									if($j!=1)
									{
										?>
										<tr class="tbl_bottom">

											<td colspan="26" align="right"><b>Floor Total</b></td>
											<?
											$html.="<tr>
											<td colspan='25' align='right'><b>Floor Total</b></td>";
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												?>
												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
												<?
												$html.="<td align='right'>&nbsp;</td>
												<td align='right'>&nbsp;</td>
												<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
											}
											?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<?
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='ight'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";
										unset($noshift_total);
										unset($floor_tot_roll);
									}
									?>
									<tr><td colspan="46" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") echo "Without Floor"; else echo $floor_details[$row[csf('floor_id')]]; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='37' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>".$floor_details[$row[csf('floor_id')]]."</b></td></tr>";
									$floor_array[$i]=$row[csf('floor_id')];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle" id="">
										<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
										<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
										<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
										<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "2"; ?>" /></td>
										<!-- 2 mean without order-->
										<td width="30"><? echo $i; ?></td>
										<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>

										<td align="center" width="60"><p>&nbsp;<? echo $row[csf('year')]; ?></p></td>
										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p>&nbsp;<? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
										<td width="110"><p>&nbsp;<? echo $row[csf('po_number')]; ?></p></td>
										<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
										<td width="110" id="booking_no_<? echo $i; ?>"><P><? echo $booking_plan_no; ?></P></td>
										<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
										<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
										<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60" id="yarn_lot_<? echo $i; ?>" style="max-width:60px"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<?
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
											<td width="80" id="mc_gauge_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50" id="stich_<? echo $i; ?>" style="max-width:50px"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr>
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
											<td width='70'><p></p></td>
											<td width='70'><p></p></td>
											<td width='60'><p>".$row[csf('year')]."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$row[csf('po_number')]."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$booking_plan_no."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
											<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
											$row_tot_roll=0;
											$row_tot_qnty=0;
											$row_tot_pcs=0;
											foreach($shift_name as $key=>$val)
											{
												$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$source_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												$floor_tot_roll[$key]['pcs']+=$row[csf('pcsshift'.strtolower($val))];

												$row_tot_roll+=$row[csf('roll'.strtolower($val))];
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
												$row_tot_pcs+=$row[csf('pcsshift'.strtolower($val))];
												?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="50" align="right" ><?
												echo number_format($row[csf('pcsshift'.strtolower($val))],2);?></td>
												<td width="100" align="right" >
													<?
													echo number_format($row[csf('qntyshift'.strtolower($val))],2);
													$machineSamarryDataArr[$row[csf('machine_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
													?>
												</td>
												<?
												$html.="<td width='50' align='right'>".$row[csf('roll'.strtolower($val))]."</td>
												<td width='50' align='right'>".number_format($row[csf('pcsshift'.strtolower($val))],2)."</td>
												<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>																<td width="50" align="right" id="nopcs_<? echo $i; ?>"><? echo number_format($row[csf('pcsnoshift')],2); ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
											<td width="50" align="right" id="totpcs_<? echo $i; ?>"><? echo number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.',''); ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
											<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<?
										$html.="
										<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
										<td width='50' align='right'>".number_format($row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2,'.','')."</td>
										<td width='50' align='right'>".$row_tot_roll."</td>
										<td width='50' align='right'>".number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.','')."</td>
										<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
										<td><p>".$row[csf('remarks')]."</p></td>
										</tr>";
										$grand_tot_roll+=$row_tot_roll+$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
										$grand_tot_pcs+=$row_tot_pcs+$row[csf('pcsnoshift')];


										$source_grand_tot_roll+=$row_tot_roll;
										$source_grand_tot_qnty+=$row_tot_qnty;
										$source_grand_tot_pcs+=$row_tot_pcs;

										$noshift_total+=$row[csf('qntynoshift')];

										$grand_tot_floor_roll+=$row_tot_roll;
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$total_qty_noshift+=$row[csf('qntynoshift')];
										$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];

										$j++;
										$i++;
									}

									?></tbody>
									<tr class="tbl_bottom">
										<td colspan="28" align="right"><b>Floor Total</b></td>
										<?
										$html.="</tbody>
										<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
										<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
										<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>

									</tr>
									<tr class="tbl_bottom">
										<td colspan="28" align="right"><b> Sample Without Order Outbound Total</b></td>
										<?

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>
										<tr>
										<td colspan='25' align='right'><b> Sample Without Order Total</b></td>";
										foreach($shift_name as $key=>$val)
										{
											$source_tot_qnty+=$source_tot_roll[$key]['qty'];
											$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
											?>
											<td align="right">&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td align="right"><?  echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
											<?
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";
											unset($source_tot_qnty_row);
										}
										?>

										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
									<td align='right'>&nbsp;</td>
									<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
									<td>&nbsp;</td>
									</tr>";
						}
						// echo "<pre>";print_r($machineSamarryDataArr);
						// =====Grand Total tfoot below=========
							?>
							<tfoot>
								<th></th>
								<th colspan="25" align="right">Grand Total</th>
								<?
								$html.="<tfoot>
								<th colspan='25' align='right'>Grand Total</th>";
								foreach($shift_name as $key=>$val)
								{
									$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
									$source_tot_pcs_row+=$source_tot_roll[$key]['pcs'];
									?>
									<th align="right"><? echo number_format($tot_roll[$key]['roll'],2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_roll[$key]['pcs'],2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_roll[$key]['qty'],2,'.',''); ?></th>
									<?
									$html.="<th align='right'>".number_format($tot_roll[$key]['roll'],2,'.','')."</th>
									<th align='right'>".number_format($tot_roll[$key]['pcs'],2,'.','')."</th>
									<th align='right'>".number_format($tot_roll[$key]['qty'],2,'.','')."</th>";
								}
								?>
								<th align="right"><? echo number_format($tot_subcontract_noRoll,2,'.',''); ?></th>
								<th align="right">&nbsp;</th>
								<th align="right"><? echo number_format($total_service_subcontact,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_pcs,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_amount,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tfoot>
							</table>
							<?
							$html.="
							<th align='right'>&nbsp;</th>
							<th align='right'>".number_format($total_service_subcontact,2,'.','')."</th>
							<th align='right'>&nbsp;</th>
							<th align='right'>".number_format($grand_tot_qnty,2,'.','')."</th>
							<th>&nbsp;</th>
							</tfoot>
							</table>
							</div>
							</fieldset>
							<br>";
							?>
						</div>
					</fieldset>
					<br>
					<!--  Fabric Sales Order Knitting Production Data Show -->
					<div>
						<?
						$tbl_width2=2295;
						if (count($nameArray_sales_order)>0)
						{
							?>
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width2+200; ?>" class="rpt_table" id="table_head" >
								<caption><strong style="float:left"> Fabric Sales Order Knitting Production </strong></caption>
								<thead>
									<tr>

										<th width="30" rowspan="2">SL</th>
										<th width="55" rowspan="2">M/C No</th>
										<th width="60" rowspan="2">Floor</th>

										<th width="70" rowspan="2">Party</th>
										<th width="100" rowspan="2">Style</th>
										<th width="110" rowspan="2">Sales Order No</th>
										<th width="100" rowspan="2">Production. ID</th>

										<th width="80" rowspan="2">Yarn Count</th>
										<th width="90" rowspan="2">Yarn Brand</th>
										<th width="60" rowspan="2">Lot No</th>
										<th width="100" rowspan="2">Fabric Color</th>
										<th width="150" rowspan="2">Fabric Type</th>
										<th width="50" rowspan="2">M/C Dia</th>
										<th width="80" rowspan="2">M/C Gauge</th>
										<th width="50" rowspan="2">Fab. Dia</th>
										<th width="50" rowspan="2">Stitch</th>
										<th width="60" rowspan="2">GSM</th>
										<?
										foreach($shift_name as $val)
										{
											?>
											<th width="150" colspan="2"><? echo $val; ?></th>
											<?
										}
										?>
										<th width="150" colspan="3">No Shift</th>
										<th width="150" colspan="3">Total</th>
										<th width="100" rowspan="2">Insert User</th>
										<th width="100" rowspan="2">Insert Date and Tiime</th>
										<th rowspan="2">Remarks</th>
									</tr>
									<tr>
										<?
										foreach($shift_name as $val)
										{
											?>
											<th width="50" rowspan="2">Roll</th>
											<th width="100" rowspan="2">Qnty</th>
											<?
										}
										?>
										<th width="50" rowspan="2">Roll</th>
										<th width="50" rowspan="2">Pcs</th>
										<th width="100" rowspan="2">Qnty</th>
										<th width="50" rowspan="2">Roll</th>
										<th width="50" rowspan="2">Pcs</th>
										<th width="100" rowspan="2">Qnty</th>
									</tr>
								</thead>
							</table>

							<div style="width:<? echo $tbl_width2+220; ?>px;overflow-y:scroll; max-height:330px;" id="scroll_body">
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width2+200; ?>" class="rpt_table" id="table_body">

							<?
							$sales_floor_array=array(); $i=1;$kk=0;
							?>
							<tr  bgcolor="#CCCCCC">
								<td colspan="32" align="left" ><b>In-House</b></td>
							</tr>
							<?
							$html.="<tr  bgcolor='#CCCCCC'>
							<td align='left' ></td>
							<td colspan='32' align='left' ><b>In-House</b></td>
							</tr>";
							$in_house_sub_tot=0;
							foreach ($nameArray_sales_order as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$count='';
								$yarn_count=explode(",",$row[csf('yarn_count')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}

								$reqsn_no=""; $stich_length=""; $color=""; $booking_plan_no='';
								if($row[csf('receive_basis')]==2)
								{
									$booking_plan_no='<strong>P:</strong>'.$row[csf('booking_no')].', <strong>S:</strong>'.$row[csf('job_no')];
									$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
									$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
									$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
								}
								else $booking_plan_no=$row[csf('booking_no')];

								if($row[csf('knitting_source')]==1)
									$knitting_party=$company_arr[$row[csf('knitting_company')]];
								else if($row[csf('knitting_source')]==3)
									$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
								else
									$knitting_party="&nbsp;";

								$style_ref_no=$row[csf('style_ref_no')];
								$job_no=$sales_booking_array[$booking_plan_no]['job_no'];
								$job_year=$sales_booking_array[$booking_plan_no]['year'];


								if(!in_array($row[csf('floor_id')],$sales_floor_array))
								{

									if($i!=1)
									{
										?>
										<tr class="tbl_bottom" title="<? echo $i;?>">
											<td></td>
											<td colspan="16" align="right"><b>Floor Total</b></td>
											<?

											$floor_tot_qnty_row=$floor_tot_roll_row=$floor_roll_tot=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
												$floor_roll_tot=$tot_roll_arr[$key]['roll'];
												$floor_roll_qntys=$tot_roll_qtyshift_arr[$key]['qntys'];
												?>
												<td align="right"><? echo number_format($floor_roll_tot,2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_roll_qntys,2,'.',''); ?></td>
												<?

											}

											?>
											<td align="right"><? echo number_format($no_of_shift_roll,2,'.','');?></td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
											<td align="right"><? echo number_format($sub_floor_qntynoshift,2,'.',''); ?></td>

											<td align="right"><? echo number_format($sub_floor_tot_roll,2,'.',''); ?></td>
											<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td></td>
											<td align="right"><? echo number_format($floor_tot_Qnty,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>

										</tr>

										<?
										$html.="<tr>

										<td colspan='16' align='right'><b>Floor Total</b></td>";

										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
										<td align='right'>".number_format($noshift_total,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
										<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";

										unset($noshift_total);
										unset($pcsnoshift_total);
										unset($floor_tot_roll);
										unset($no_of_shift_roll);
										unset($sub_floor_tot_roll);
										unset($floor_roll_tot);
										unset($floor_tot_Qnty);
										unset($tot_roll_arr);
										unset($tot_roll_qtyshift_arr);

									}
									if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") $floor_id_show= "Without Floor"; else $floor_id_show = $floor_details[$row[csf('floor_id')]];
									?>
									<tr><td colspan="32" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $floor_id_show; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='28' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>$floor_id_show</b></td></tr>";
									$sales_floor_array[$i]=$row[csf('floor_id')];
								}
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i; ?></td>
										<td width="55"><p><? echo $row[csf('machine_name')]; ?>&nbsp;</p></td>
										<td width="60"><p>&nbsp;<? echo $floor_details[$row[csf('floor_id')]]; ?></p></td>

										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="100"><p><? echo $style_ref_no; ?></p></td>
										<td width="110" id="booking_no_<? echo $i; ?>" align="center"><P><? echo $booking_plan_no; ?></P></td>
										<td width="100" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number')]; ?></P></td>

										<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
										<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
										<td width="60" id="yarn_lot_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
										<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>

										<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
										<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_dia')]; ?></p></td>
										<td width="80" id="mc_gause_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('machine_gg')]; ?></p></td>
										<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
										<td width="50" id="stich_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
										<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
										<?
										$html.="<tr>
										<td width='30'>".$i."</td>
										<td width='55'><p>".$knitting_party."&nbsp;</p></td>
										<td width='60'><p>".$row[csf('machine_name')]."</p></td>

										<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
										<td width='100'><p>".$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']."</p></td>


										<td width='110'><P>".$booking_plan_no."</P></td>
										<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>

										<td width='80'><p>".$count."</p></td>
										<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
										<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
										<td width='100'><p>&nbsp;".$color."</p></td>";

										$html.="
										<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
										<td width='50'><p>&nbsp;".$row[csf('machine_dia')]."</p></td>
										<td width='80'><p>&nbsp;".$row[csf('machine_gg')]."</p></td>
										<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
										<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
										<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
										$row_tot_roll=0;
										$row_tot_qnty=0;
										foreach($shift_name as $key=>$val)
										{
											$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
	                                        $tot_roll_arr[$key]['roll']+=$row[csf('roll'.strtolower($val))]; // new
	                                        $tot_roll_qtyshift_arr[$key]['qntys']+=$row[csf('qntyshift'.strtolower($val))]; // new
	                                        $tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

	                                        $source_tot_roll_sales[$key]['roll']+=$row[csf('roll'.strtolower($val))];
	                                        $source_tot_roll_sales[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

	                                        $floor_tot_roll_sales[$key]['roll']+=$row[csf('roll'.strtolower($val))];
	                                        $floor_tot_roll_sales[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

	                                        $row_tot_roll+=$row[csf('roll'.strtolower($val))];
	                                        $row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
	                                        ?>
	                                        <td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>



	                                        <td width="100" align="right" >
	                                        	<?
	                                        	echo number_format($row[csf('qntyshift'.strtolower($val))],2);
	                                        	$machineSamarryDataArr[$row[csf('machine_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
	                                        	?>
	                                        </td>
	                                        <?

	                                        $html.="<td width='50' align='right' >".$row[csf('roll'.strtolower($val))]."</td>
	                                        <td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
	                                    }
	                                    $sub_floor_no_roll+=$row[csf('rollnoshift')];
	                                    $sub_floor_qntynoshift+=$row[csf('qntynoshift')];
	                                    $sub_floor_tot_roll+=$row_tot_roll;
	                                    $sub_floor_tot_roll_qntynoshift+=$row_tot_qnty+$row[csf('qntynoshift')];
	                                    //$row[csf('rollnoshift')]=150;
	                                    $no_of_shift_roll+= $row[csf('rollnoshift')];


	                                    $in_house_sub_tot+= $row[csf('rollnoshift')];
	                                    $in_house_sub_tot_roll+= $row_tot_roll;

	                                    $floor_tot_Qnty += $row_tot_qnty+$row[csf('qntynoshift')];
	                                     //$row_tot_roll
	                                    ?>
	                                    <td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>
	                                    <td width="50" align="right" id="nopcs_<? echo $i; ?>">0.00<? //echo $row[csf('rollnoshift')]; ?></td>
	                                    <td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
	                                    <td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
	                                    <td width="50" align="right" id="totalnopcs_<? echo $i; ?>">0.00<? //echo $row[csf('rollnoshift')]; ?></td>
	                                    <td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
	                                    <td width="100" align="center"><p><? echo $row[csf('inserted_by')]; ?>&nbsp;</p></td>
	                                    <td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
	                                    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
	                                </tr>
	                            </tbody>
	                            <?
	                            $html.="
	                            <td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
	                            <td width='50' align='right'>".$pcs."</td>
	                            <td width='100' align='right'>".number_format($row[csf('qntynoshift')],2)."</td>

	                            <td width='50' align='right'>".$row_tot_roll."</td>
	                            <td width='50' align='right'>".$totalpcs."</td>
	                            <td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
	                            <td><p>".$row[csf('remarks')]."&nbsp;</p></td>
	                            </tr>
	                            </tbody>";

	                            $grand_tot_roll+=$row_tot_roll;
	                            $grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];

	                            $source_grand_tot_roll+=$row_tot_roll;
	                            $source_grand_tot_qnty+=$row_tot_qnty;

	                            $noshift_total+=$row[csf('qntynoshift')];
	                            $pcsnoshift_total+=$row[csf('pcsnoshift')];

	                            $grand_tot_floor_roll+=$row_tot_roll;
	                            $grand_tot_floor_qnty+=$row_tot_qnty;
	                            $total_qty_noshift+=$row[csf('qntynoshift')];

	                            $i++;$kk++;

	                            $machine_name_arr[$row[csf('machine_name')]] =$row[csf('machine_name')];
	                        }

	                        ?>
	                        <tr class="tbl_bottom">
	                        	<td></td>
	                        	<td colspan="16" align="right"><b>Floor Total</b></td>

	                        	<?
	                        	$floor_tot_qnty_row=$floor_roll_tot=0;
	                        	foreach($shift_name as $key=>$val)
	                        	{
	                        		$floor_tot_qnty_row+=$floor_tot_roll_sales[$key]['qty'];
	                        		$floor_roll_tot=$tot_roll_arr[$key]['roll'];
	                        		$floor_roll_qntys=$tot_roll_qtyshift_arr[$key]['qntys'];
	                        		?>
	                        		<td align="right"><? echo number_format($floor_roll_tot,2,'.',''); ?></td>
	                        		<td align="right"><? echo number_format($floor_roll_qntys,2,'.',''); ?></td>
	                        		<?
	                        	}
	                        	?>
	                        	<td align="right"><? echo number_format($no_of_shift_roll,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($sub_floor_qntynoshift,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($sub_floor_tot_roll,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($floor_tot_Qnty,2,'.',''); ?></td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        </tr>
	                        <tr class="tbl_bottom">
	                        	<td></td>
	                        	<td colspan="16" align="right"><b>In House Total</b></td>
	                        	<?
	                        	$source_tot_qnty_row=$source_tot_qnty=$source_tot_roll_row=0;
	                        	foreach($shift_name as $key=>$val)
	                        	{
	                        		$source_tot_qnty += $source_tot_roll_sales[$key]['qty'];
	                        		$source_tot_qnty_row += $source_tot_roll_sales[$key]['qty'];

	                                // $source_tot_roll += $source_tot_roll_sales[$key]['roll'];
	                        		$source_tot_roll_row += $source_tot_roll_sales[$key]['roll'];
	                        		?>
	                        		<td align="right"><? echo number_format($source_tot_roll_row,2,'.',''); ?></td>
	                        		<td align="right"><? echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
	                        		<?
	                        		unset($source_tot_roll);
	                        		unset($source_tot_roll_row);
	                        	}


	                        	?>
	                        	<td align="right"><? echo number_format($in_house_sub_tot,2,'.','');?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?> </td>
	                        	<td align="right"><? echo number_format($in_house_sub_tot_roll,2,'.','');?></td>
	                        	<td align="right"><? echo number_format($pcsnoshift_total,2,'.',''); ?></td>
	                        	<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        	<td>&nbsp;</td>
	                        </tr>
	                        <?
	                        $html.="<tr>
	                        <td colspan='16' align='right'><b>Floor Total</b></td>";

	                        $floor_tot_qnty_row=0;
	                        foreach($shift_name as $key=>$val)
	                        {
	                        	$floor_tot_qnty_row+=$floor_tot_roll_sales[$key]['qty'];

	                        	$html.="<td align='right'>&nbsp;</td>
	                        	<td align='right'>".number_format($floor_tot_roll_sales[$key]['qty'],2,'.','')."</td>";
	                        }
	                        $html.="
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($noshift_total,2,'.','')."</td>
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
	                        <td>&nbsp;</td>
	                        </tr>
	                        <tr>
	                        <td colspan='16' align='right'><b>In House Total</b></td>";
	                        $source_tot_qnty=0;
	                        foreach($shift_name as $key=>$val)
	                        {
	                        	$source_tot_qnty+=$source_tot_roll_sales[$key]['qty'];
	                        	$source_tot_qnty_row+=$source_tot_roll_sales[$key]['qty'];
	                        	$html.="<td align='right'>&nbsp;</td>
	                        	<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";

	                        	unset($source_tot_qnty_row);
	                        }
	                        $html.="
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($pcsnoshift_total,2,'.','')."</td>
	                        <td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>&nbsp;</td>
	                        <td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
	                        <td>&nbsp;</td>
	                        </tr>";
	                	}
	                    ?>
	                </table>
	            </div>
		        </div>
		        <?
	    	}
		}
		//echo "string";die;
		// Subcontract Order (In-bound) Knitting Production D
		if($db_type	==0)
		{
			$order_production_relation = " and b.order_id  = d.id";
		}
		else
		{
			$order_production_relation = " and cast (b.order_id as varchar(4000)) = d.id";
		}
		// echo "string";die;
		if($cbo_type==2 || $cbo_type==0) // Subcontract Order
		{
			$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
			$sql_inhouse_sub="SELECT DISTINCT b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, c.seq_no, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type, b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id $select_color, b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, $year_sub_field as year, d.order_no, d.cust_style_ref, sum(case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, sum(case when b.shift=0 then b.no_of_roll end ) as rollnoshift,d.job_no_mst";
			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse_sub.=", sum(case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."
				, sum(case when b.shift=$key then b.product_qnty else 0 end ) as qntyshift".strtolower($val);
			}

			$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e
			where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and e.subcon_job=d.job_no_mst and a.product_type=2 and d.status_active=1 and d.is_deleted=0
			and a.status_active=1 and a.is_deleted=0  $company_working_cond $cbo_company_cond $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $order_production_relation
			group by b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, a.remarks,a.inserted_by,a.insert_date, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,  b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id,b.machine_dia,b.machine_gg, b.floor_id, b.color_id, b.order_id, c.machine_no, e.job_no_prefix_num, e.insert_date, d.order_no, d.cust_style_ref, c.seq_no,d.job_no_mst order by b.floor_id, a.product_date, c.seq_no";

				//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
			$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
			if (count($nameArray_inhouse_subcon)>0)//for avg.rate
			{
				$order_id_arr = array();
				foreach ($nameArray_inhouse_subcon as $row)
				{
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
				}

				$all_order_id = implode(",", $order_id_arr);
			    $order_id_cond="";
			    if($all_order_id)
			    {
			        $all_order_id = implode(",",array_filter(array_unique(explode(",", $all_order_id))));
			        $order_id_arr = explode(",", $all_order_id);
			        if($db_type==0)
			        {
			            $order_id_cond = " and c.order_id in ($all_order_id )";
			        }
			        else
			        {
			            if(count($order_id_arr)>999)
			            {
			                $order_id_chunk_arr=array_chunk($order_id_arr, 999);
			                $order_id_cond=" and (";
			                foreach ($order_id_chunk_arr as $value)
			                {
			                    $order_id_cond .=" c.order_id in (".implode(",", $value).") or ";
			                }
			                $order_id_cond=chop($order_id_cond,"or ");
			                $order_id_cond.=")";
			            }
			            else
			            {
			                $order_id_cond = " and c.order_id in ($all_order_id )";
			            }
			        }

			        $inbound_rate_sql="SELECT c.job_no_mst, c.item_id, c.gsm, c.grey_dia, c.rate from subcon_ord_breakdown c where status_active=1 and is_deleted=0 $order_id_cond";
					$inbound_rate_data=sql_select($inbound_rate_sql);
					$inbound_rate_arr=array();
					foreach ($inbound_rate_data as $key => $row)
					{
						$inbound_rate_arr[$row[csf('job_no_mst')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('grey_dia')]]=$row[csf('rate')];
					}
					//echo "<pre>";print_r($inbound_rate_arr);
			    }
			}

			if(count($nameArray_inhouse_subcon)>0)
			{
				$tbl_width=1890+count($shift_name)*157;

				?>
				<fieldset style="width:<? echo $tbl_width+220; ?>px;">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:400px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Subcontract Order (In-bound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+200; ?>" class="rpt_table" >
						<thead>
							<tr>
								<th width="30" rowspan="2">SL</th>
								<th width="60" rowspan="2">M/C No</th>
								<th width="60" rowspan="2">Job No</th>
								<th width="60" rowspan="2">Year</th>
								<th width="70" rowspan="2">Party</th>
								<th width="100" rowspan="2">Style</th>
								<th width="110" rowspan="2">Order No</th>
								<th width="60" rowspan="2">Prod. No</th>
								<th width="80" rowspan="2">Yarn Count</th>
								<th width="90" rowspan="2">Yarn Brand</th>
								<th width="60" rowspan="2">Lot No</th>
								<th width="100" rowspan="2">Fabric Color</th>
								<th width="150" rowspan="2">Fabric Type</th>
								<th width="50" rowspan="2">M/C Dia</th>
								<th width="80" rowspan="2">M/C Gauge</th>
								<th width="50" rowspan="2">Fab. Dia</th>
								<th width="50" rowspan="2">Stitch</th>
								<th width="60" rowspan="2">GSM</th>
								<?
								$html_width = $tbl_width+20;
								$html .= "<fieldset style='width:".$html_width."px;'>
								<div align='left' style=\"background-color:#E1E1E1; color:#000; width:400px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;\"><strong><u><i>Subcontract Order (In-bound) Knitting Production</i></u></strong></div>
								<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" rules=\"all\" width=". $tbl_width ." class=\"rpt_table\" >
								<thead>
								<tr>
								<th width=\"30\" rowspan=\"2\">SL</th>
								<th width=\"60\" rowspan=\"2\">M/C No</th>
								<th width=\"60\" rowspan=\"2\">Job No</th>
								<th width=\"60\" rowspan=\"2\">Year</th>
								<th width=\"70\" rowspan=\"2\">Party</th>
								<th width=\"100\" rowspan=\"2\">Style</th>
								<th width=\"110\" rowspan=\"2\">Order No</th>
								<th width=\"60\" rowspan=\"2\">Prod. No</th>
								<th width=\"80\" rowspan=\"2\">Yarn Count</th>
								<th width=\"90\" rowspan=\"2\">Yarn Brand</th>
								<th width=\"60\" rowspan=\"2\">Lot No</th>
								<th width=\"100\" rowspan=\"2\">Fabric Color</th>
								<th width=\"150\" rowspan=\"2\">Fabric Type</th>
								<th width=\"50\" rowspan=\"2\">M/C Dia</th>
								<th width=\"80\" rowspan=\"2\">M/C Gauge</th>
								<th width=\"50\" rowspan=\"2\">Fab. Dia</th>
								<th width=\"50\" rowspan=\"2\">Stitch</th>
								<th width=\"60\" rowspan=\"2\">GSM</th>";
								foreach($shift_name as $val)
								{
									$html .= "<th width=\"150\" colspan=\"2\">$val</th>";
									?>
									<th width="150" colspan="2"><? echo $val; ?></th>
									<?
								}
								?>
								<th width="150" colspan="2">No Shift</th>
								<th width="150" colspan="2">Total</th>
								<th width="100" rowspan="2">Avg. Rate (Tk)</th>
								<th width="100" rowspan="2">Amount (TK)</th>
								<th width="100" rowspan="2">Insert User</th>
								<th width="100" rowspan="2">Insert Date and Tiime</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<?
								$html .= '<th width="150" colspan="2">No Shift</th>
								<th width="150" colspan="2">Total</th>
								<th rowspan="2">Remarks</th></tr><tr>';
								foreach($shift_name as $val)
								{
									?>
									<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>
									<?
									$html .= '<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>';
								}
								?>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $tbl_width+220; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+200; ?>" class="rpt_table" id="table_body">
							<?
							$html .= '<th width="50" rowspan="2">Roll</th>
							<th width="100" rowspan="2">Qnty</th>
							<th width="50" rowspan="2">Roll</th>
							<th width="100" rowspan="2">Qnty</th>
							</tr>
							</thead>
							</table>
							<div style="width:'. $html_width .'px; overflow-y:scroll; max-height:330px;" id="scroll_body">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="'. $tbl_width .'" class="rpt_table" id="table_body">';
							$i=1; $tot_sub_rolla=''; $tot_sub_rollb=''; $tot_sub_rollc=''; $tot_sub_rolla_qnty=0; $tot_sub_rollb_qnty=0; $tot_sub_rollc_qnty=0; $grand_sub_tot_roll=''; $grand_sub_tot_qnty=0; $grand_sub_tot_amount=0;
							$floor_array_subcon=array();$m=0;
							$floor_tot_roll = array();
							foreach ($nameArray_inhouse_subcon as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$inbound_rate=$inbound_rate_arr[$row[csf('job_no_mst')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]];

								$count='';
								$yarn_count=explode(",",$row[csf('yrn_count_id')]);
								foreach($yarn_count as $count_id)
								{
									if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
								}
								if(!in_array($row[csf('floor_id')],$floor_array_subcon))
								{
									if($i!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="18" align="right"><b>Floor Total</b></td>
											<?
											$floor_tot_qnty_row=0;
											foreach($shift_name as $key=>$val)
											{
												$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
												?>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
												<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>

												<?
											}
											?>
											<td align="right"><? echo number_format($total_rollnoshift,2,'.',''); ?></td>
											<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>
											<td align="right"></td>
											<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_amount,2,'.',''); ?></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>

										</tr>

										<?
										$html.="<tr>

										<td colspan='25' align='right'><b>Floor Total</b></td>";

										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";

										}

										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($noshift_total,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
										<td>&nbsp;</td>
										</tr>";

										unset($noshift_total);
										unset($floor_tot_roll);
										unset($total_rollnoshift);
										unset($floor_tot_amount);
									}
									if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") $floor_id_show= "Without Floor"; else $floor_id_show = $floor_details[$row[csf('floor_id')]];
									?>
									<tr><td colspan="38" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $floor_id_show; ?></b></td></tr>
									<?
									$html.="<tr><td colspan='36' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>$floor_id_show</b></td></tr>";
									$floor_array_subcon[$i]=$row[csf('floor_id')];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
									<td align="center" width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
									<td align="center" width="60"><p><? echo $row[csf('year')]; ?></p></td>
									<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('party_id')]]; ?></p></td>
									<td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
									<td width="110"><p><? echo $row[csf('order_no')]; ?></p></td>
									<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('prefix_no_num')]; ?></P></td>
									<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
									<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('brand')]; ?></p></td>
									<td width="60" id="yarn_lot_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
									<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
										<?
										$color_arr=array_unique(explode(",",$row[csf('color_id')]));
										$all_color="";
										foreach($color_arr as $id)
										{
											$all_color.=$color_details[$id].",";
										}
										$all_color=chop($all_color," , ");
										echo $all_color;
										?></p></td>
										<td width="150" id="feb_type_<? echo $i; ?>" title="<? echo $row[csf('cons_comp_id')]; ?>"><p><? echo $const_comp_arr[$row[csf('cons_comp_id')]]; ?>&nbsp;</p></td>
										<td width="50" id="mc_dia_<? echo $i; ?>"><p><? echo $row[csf('machine_dia')];?></p></td>
										<td width="80" id="mc_gauge_<? echo $i; ?>"><p><? echo $row[csf('machine_gg')]; ?></p></td>
										<td width="50" id="fab_dia_<? echo $i; ?>"><p><? echo $row[csf('dia_width')]; ?></p></td>
										<td width="50" id="stich_<? echo $i; ?>"><p><? echo $row[csf('stitch_len')]; ?></p></td>
										<td width="60" id="fin_gsm_<? echo $i; ?>"><p><? echo $row[csf('gsm')]; ?></p></td>
										<?
										$html .= '<tr bgcolor="'. $bgcolor.'" onClick="change_color(\'tr_'. $i .'\',\''. $bgcolor .'\')" id="tr_'.$i.'">
										<td width="30">'. $i.'</td>
										<td width="60"><p>&nbsp;'. $row[csf("machine_name")].'</p></td>
										<td align="center" width="60"><p>'. $row[csf("job_no_prefix_num")].'</p></td>
										<td align="center" width="60"><p>'. $row[csf("year")].'</p></td>
										<td width="70" id="buyer_id_'. $i .'"><p>&nbsp;'. $buyer_arr[$row[csf("party_id")]].'</p></td>
										<td width="100"><p>'. $row[csf("cust_style_ref")] .'</p></td>
										<td width="110"><p>'. $row[csf("order_no")].'</p></td>
										<td width="60" id="prod_id_'. $i.'"><P>'. $row[csf("prefix_no_num")].'</P></td>
										<td width="80" id="yarn_count_'. $i.'"><p>'. $count .'&nbsp;</p></td>
										<td width="90" id="brand_id_'. $i.'"><p>&nbsp;'. $row[csf("brand")].'</p></td>
										<td width="60" id="yarn_lot_'. $i.'"><p>&nbsp;'. $row[csf("yarn_lot")].'</p></td>
										<td width="100" id="color_'. $i.'"><p>&nbsp'.$all_color.'</p></td>
										<td width="150" id="feb_type_'. $i.'"><p>'. $const_comp_arr[$row[csf("cons_comp_id")]].'&nbsp;</p></td>
										<td width="50" id="mc_dia_'. $i.'"><p>&nbsp;'. $row[csf('machine_dia')].'</p></td>
										<td width="80" id="mc_gauge_'. $i.'"><p>&nbsp;'. $row[csf('machine_gg')].'</p></td>
										<td width="50" id="fab_dia_'. $i.'"><p>&nbsp;'. $row[csf("dia_width")].'</p></td>
										<td width="50" id="stich_'. $i.'"><p>&nbsp;'. $row[csf("stitch_len")].'</p></td>
										<td width="60" id="fin_gsm_'. $i.'"><p>&nbsp;'. $row[csf("gsm")].'</p></td>';
										$row_sub_tot_roll=0;
										$row_sub_tot_qnty=0;
										foreach($shift_name as $key=>$val)
										{
											$tot_sub_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
											$tot_sub_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

											$source_sub_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
											$source_sub_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

											$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
											$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

											$row_sub_tot_roll+=$row[csf('roll'.strtolower($val))];
											$row_sub_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
											?>
											<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
											<td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); $machineSamarryDataArr[$row[csf('machine_id')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
											?></td>
											<?
											$html .= '<td width="50" align="right" >'. $row[csf("roll".strtolower($val))].'</td>
											<td width="100" align="right" >'.number_format($row[csf("qntyshift".strtolower($val))],2).'</td>';
										}
											//$row[csf('rollnoshift')]=150;
										?>
										<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')];
										$total_rollnoshift += $row[csf('rollnoshift')];
										$all_rollnoshift += $row[csf('rollnoshift')];
										?></td>
										<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
										<td width="50" align="right" id="roll_<? echo $i; ?>">&nbsp;<? //echo $row_sub_tot_roll; ?></td>
										<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_sub_tot_qnty+$row[csf('qntynoshift')],2,'.','');
										$sub_tota_qnty=$row_sub_tot_qnty+$row[csf('qntynoshift')]; ?></td>
										<td width="100" align="right"><p><? echo number_format($inbound_rate,2,'.',''); ?></p></td>
										<td width="100" align="right"><p><? $sub_tota_amount=$sub_tota_qnty*$inbound_rate;
										echo number_format($sub_tota_amount,2,'.','');
										$floor_tot_amount += $sub_tota_amount;?></p></td>
										<td width="100" align="center"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $row[csf('insert_date')]; ?>&nbsp;</p></td>
										<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
									</tr>
								</tbody>
								<?
								$html .= '<td width="50" align="right" id="noqty_'. $i.'">'. $row[csf("rollnoshift")].'</td>
								<td width="100" align="right" id="noqty_'. $i.'">'. number_format($row[csf("qntynoshift")],2).'</td>
								<td width="50" align="right" id="roll_'. $i.'">'. $row_sub_tot_roll.'</td>
								<td width="100" align="right" id="qty_'. $i.'">'. number_format($row_sub_tot_qnty+$row[csf("qntynoshift")],2,".","").'</td>
								<td><p>'. $row[csf("remarks")].'&nbsp;</p></td></tr></tbody>';

								$grand_sub_tot_roll+=$row_sub_tot_roll;
								$grand_sub_tot_qnty+=$row_sub_tot_qnty+$row[csf('qntynoshift')];

								$source_sub_grand_tot_roll+=$row_sub_tot_roll;
								$source_sub_grand_tot_qnty+=$row_sub_tot_qnty;

								$noshift_sub_total+=$row[csf('qntynoshift')];

								$grand_sub_tot_floor_roll+=$row_sub_tot_roll;
								$grand_sub_tot_floor_qnty+=$row_sub_tot_qnty;
								$total_sub_qty_noshift+=$row[csf('qntynoshift')];
								$machine_name_arr[$row[csf('machine_no_id')]] =$row[csf('machine_no_id')];
								$grand_sub_tot_amount+=$sub_tota_amount;

								$i++;$m++;
							}
							?>
							<tr class="tbl_bottom">
								<td colspan="18" align="right"><b>Floor Total</b></td>
								<?
								$floor_tot_qnty_row=$floor_tot_roll_row=0;
								foreach($shift_name as $key=>$val)
								{
									$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
									$floor_tot_roll_row+=$floor_tot_roll[$key]['roll'];
									?>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['roll'],2,'.',''); ?></td>
									<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($total_rollnoshift,2,'.',''); ?> </td>
								<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>

								<td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($floor_tot_amount,2,'.',''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tfoot>
								<th colspan="18" align="right">Grand Total</th>
								<?
								$html .= '<tfoot><th colspan="18" align="right">Grand Total</th>';
								foreach($shift_name as $key=>$val)
								{
									?>
									<th align="right"><? echo number_format($tot_sub_roll[$key]['roll'],2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_sub_roll[$key]['qty'],2,'.',''); ?></th>
									<?
									$html .= "<th align='right'>&nbsp;</th>
									<th align='right'>". number_format($tot_sub_roll[$key]['qty'],2,'.','') ."</th>";
								}
								?>
								<th align="right"><? echo number_format($all_rollnoshift,2,'.',''); ?></th>
								<th align="right"><? echo number_format($total_sub_qty_noshift,2,'.',''); ?></th>
								<th align="right"></th>
								<th align="right"><? echo number_format($grand_sub_tot_qnty,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo number_format($grand_sub_tot_amount,2,'.',''); ?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?
				$html .= "<th align='right'>&nbsp;</th>
				<th align='right'>". number_format($total_sub_qty_noshift,2,'.','')."</th>
				<th align='right'>". number_format($grand_sub_tot_roll,2,'.','')."</th>
				<th align='right'>". number_format($grand_sub_tot_qnty,2,'.','')."</th>
				<th>&nbsp;</th>
				</tfoot>
				</table>
				</div>
				</fieldset>";
			}
		}
		?>
		<br>
		<?
		/*echo "<pre>";
		print_r($shift_name);
		echo "</pre>";*/
		?>

		<!-- Machine wise summary Data Show -->
		<?
		// echo $machine_wise_section.'Test';
		/*echo "<pre>";
		print_r($machineSamarryDataArr);
		echo "</pre>";*/
		if ($machine_wise_section==0)
		{
			?>
			<fieldset style=" width:750px;">
				<h2>Machine wise summary</h2>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="summary_tab">
					<thead>
						<tr>
							<th rowspan="2">SL</th>
							<th rowspan="2">M/C No</th>
							<th rowspan="2">Capacity</th>
							<th colspan="<? echo count($shift_name);?>">SHIFT NAME</th>
							<th rowspan="2" width="80">Shift Total (kg)</th>
							<th rowspan="2" width="80">Capacity Achieve %</th>
							<th rowspan="2" width="80">Yesterday Prod. Qty.</th>
							<th rowspan="2" width="80">Yesterday Capacity Achieve %</th>
						</tr>

						<?
						$html.="
						<br>
						<fieldset style='width:750px;'>
						<h2>Machine wise summary</h2>
						<table class='rpt_table' width='100%' cellpadding='0' cellspacing='0' border='1' rules='all' align='center'>
						<thead>
						<tr>
						<th rowspan='2'>SL</th>
						<th rowspan='2'>M/C No</th>
						<th rowspan='2'>Capacity</th>
						<th colspan=". count($shift_name).">SHIFT NAME</th>
						<th rowspan='2' width='80'>Shift Total (kg)</th>
						<th rowspan='2' width='80'>Capacity Achieve %</th>
						<th rowspan='2' width='80'>Yesterday Prod. Qty.</th>
						<th rowspan='2' width='80'>Yesterday Capacity Achieve %</th>
						</tr>
						<tr>
						";
						?>
						<tr>
							<?
							foreach($shift_name as $key=>$val)
							{
								?>
								<th><? echo $val;?></th>

								<?
								$html.="<th>".$val."</th>";
							}
							?>
						</tr>
					</thead>
					<?
					$html.="
					</tr>
					</thead>
					";

					if($db_type==0)
					{
						$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date)));
					}
					else
					{
						$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date))),'','',1);
					}
					$date_con_2=" and a.receive_date between '$previous_date' and '$previous_date'";

					$ymcpacity_arr=return_library_array( "select d.machine_no,sum(c.quantity) as quantity
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
						where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond group by d.machine_no", "machine_no", "quantity" );


					$ymcpacityWO_arr=return_library_array( "select d.machine_no, sum(b.grey_receive_qnty) as quantity  from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id  and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $location_cond group by d.machine_no", "machine_no", "quantity" );

					$mcpacity_arr=return_library_array( "select machine_no, prod_capacity from lib_machine_name where category_id=1", "machine_no", "prod_capacity"  );

					$i=1;
					// echo "<pre>";print_r($machineSamarryDataArr);
					// asort($machineSamarryDataArr);
					foreach($machineSamarryDataArr as $machine_no=>$row):
						$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sm<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_sm<? echo $i; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $machine_lib[$machine_no]; ?></td>
							<td align="right"><? echo $mCapacity=$mcpacity_arr[$machine_no]; $totmCapacity+=$mCapacity;?></td>
							<?
							$html.="
							<tr bgcolor='".$bgcolor."' id='tr_sm".$i."'>
							<td align='center'>".$i."</td>
							<td>".$machine_no."</td>
							<td align='right'>".$mcpacity_arr[$machine_no]."</td>
							";
							$totPro=0;
							foreach($row as $key=>$val)
							{
								?>
								<td align="right"><? echo number_format($val,2); $proQty[$key]+=$val;$totPro+=$val;  ?></td>
								<?
								$html.="<td align='right'>".number_format($val,2)."</td>";
							}
							?>
							<td align="right"><? echo number_format($totPro,2); $gTotPro+=$totPro;?></td>
							<td align="right"><? //echo number_format(round(($totPro/$mCapacity)*100),2);?></td>
							<td align="right">
								<?
								$html_sum=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
								echo number_format($html_sum,2);
								$totymc+=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
								?>
							</td>
							<td align="right"><? echo round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100);?></td>
						</tr>
						<?
						$html.="
						<td align='right'>".number_format($totPro,2)."</td>
						<td align='right'>".number_format(round(($totPro/$mCapacity)*100),2)."</td>
						<td align='right'>".number_format($html_sum,2)."</td>
						<td align='right'>".number_format(round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100),2)."</td>
						</tr>
						";
						$i++;
					endforeach;?>
					<tfoot>
						<th></th>
						<th>Total</th>
						<th><? echo $totmCapacity;?></th>
						<?
						$html.="
						<tfoot>
						<th></th>
						<th>Total</th>
						<th>".$totmCapacity."</th>
						";
						foreach($shift_name as $key=>$val)
						{
							?>
							<th><? echo $proQty[$key]; ?></th>
							<?
							$html.="<th>".$proQty[$key]."</th>";
						}
						?>
						<th><? echo $gTotPro;?></th>
						<th><? echo round(($gTotPro/$totmCapacity)*100);?></th>
						<th><? echo $totymc;?></th>
						<th><? echo round(($totymc/$totmCapacity)*100);?></th>
					</tfoot>
				</table>
			</fieldset>
			<?
			$html.="
			<th>".$gTotPro."</th>
			<th>".round(($gTotPro/$totmCapacity)*100)."</th>
			<th>".$totymc."</th>
			<th>".round(($totymc/$totmCapacity)*100)."</th>
			</tfoot>
			</table>
			</fieldset>
			";
		}
		else if ($machine_wise_section==1)
		{
			?>
			<fieldset style=" width:1050px;">
				<h2>Machine wise summary</h2>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="summary_tab">
					<thead>
						<tr>
							<th rowspan="2">SL</th>
							<th rowspan="2">M/C No</th>
							<th rowspan="2">Group,Dia/Width,Gauge</th>
							<th rowspan="2">Capacity</th>
							<th colspan="<? echo count($shift_name)*2;?>">SHIFT NAME</th>
							<th rowspan="2" width="80">Shift Total (kg)</th>
							<th rowspan="2" width="80">Capacity Achieve %</th>
							<th rowspan="2" width="80">Yesterday Prod. Qty.</th>
							<th rowspan="2" width="80">Yesterday Capacity Achieve %</th>
						</tr>
						<?
						$html.="
						<br>
						<fieldset style='width:750px;'>
						<h2>Machine wise summary</h2>
						<table class='rpt_table' width='100%' cellpadding='0' cellspacing='0' border='1' rules='all' align='center'>
						<thead>
						<tr>
						<th rowspan='2'>SL</th>
						<th rowspan='2'>M/C No</th>
						<th rowspan='2'>Capacity</th>
						<th colspan=". (count($shift_name)*2)." width='30'>SHIFT NAME</th>
						<th rowspan='2' width='80'>Shift Total (kg)</th>
						<th rowspan='2' width='80'>Capacity Achieve %</th>
						<th rowspan='2' width='80'>Yesterday Prod. Qty.</th>
						<th rowspan='2' width='80'>Yesterday Capacity Achieve %</th>
						</tr>
						<tr>
						";
						?>
						<tr>
							<?
							foreach($shift_name as $key=>$val)
							{
								$cause='Idle Cause';
								?>
								<th><? echo $val;?></th>
								<th width='200'><? echo $cause;?></th>

								<?
								$html.="<th>".$val."</th>";
								$html.="<th width='300'>".$cause."</th>";
							}
							?>
						</tr>
					</thead>
					<?
					$html.="
					</tr>
					</thead>
					";

					if($db_type==0)
					{
						$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date)));
					}
					else
					{
						$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date))),'','',1);
					}
					$date_con_2=" and a.receive_date between '$previous_date' and '$previous_date'";

					$ymcpacity_arr=return_library_array( "select d.machine_no,sum(c.quantity) as quantity
						from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
						where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $location_cond group by d.machine_no", "machine_no", "quantity" );


					$ymcpacityWO_arr=return_library_array( "select d.machine_no, sum(b.grey_receive_qnty) as quantity  from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id  and a.knitting_source=1  and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $cbo_company_cond $company_working_cond $date_con_2 $floor_id $buyer_cond $location_cond group by d.machine_no", "machine_no", "quantity" );
					$mcpacity_arr=return_library_array( "select machine_no, prod_capacity from lib_machine_name where category_id=1 $company_working_cond2", "machine_no", "prod_capacity"  );

					$machineTo_date=$to_date;
					$shift_details=array();
					$shift_data=sql_select("select start_time, end_time,shift_name from shift_duration_entry where status_active=1");
					foreach($shift_data as $row)
					{
						$shift_details[$row[csf('shift_name')]]['from_hr_min']=number_format($row[csf('start_time')],2);
						$shift_details[$row[csf('shift_name')]]['to_hr_min']=number_format($row[csf('end_time')],2);
						//for machine shift date
						if(strtotime($row[csf('start_time')]) >= strtotime($row[csf('end_time')]))
						{
							if($db_type==0)
							{
								$machineTo_date=date('Y-m-d',strtotime('+1 day', strtotime($to_date)));
							}
							else
							{
								$machineTo_date=change_date_format(date('Y-m-d',strtotime('+1 day', strtotime($to_date))),'','',1);
							}
						}
					}
					$mc_no_arr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
					$machine_data=sql_select("select id, machine_no, dia_width, gauge, machine_group from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 $lib_mc_cond");
					$machine_details=array();
					foreach($machine_data as $row)
					{
						$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
						$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
						$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
						$machine_details[$row[csf('id')]]['machine_group']=$row[csf('machine_group')];
						if($row[csf('machine_no')]!="") $total_machine[$row[csf('id')]]=$row[csf('id')];
					}
					unset($machine_data);


					$machine_data=sql_select("select to_date,machine_entry_tbl_id, from_hour, from_minute, to_hour, to_minute, machine_idle_cause from  pro_cause_of_machine_idle where status_active=1 and machine_entry_tbl_id in(1,".implode(",", array_filter($machine_name_arr)).") and from_date between '$from_date' and '$machineTo_date' and to_date between '$from_date' and '$machineTo_date'  ");



					foreach($machine_data as $row)
					{
						if($row[csf('from_hour')]!='')
						{
							$machineID=$row[csf('machine_entry_tbl_id')];
							$fromtime=$row[csf('from_hour')].':'.$row[csf('from_minute')];
							$totime=$row[csf('to_hour')].':'.$row[csf('to_minute')];
							$machine_no = $machine_details[$machineID]['no'];
							$machine_summary_arr[$machine_no]['from_hr_min']=$fromtime;
							$machine_summary_arr[$machine_no]['to_hr_min']=$totime;

							foreach ($shift_name as $key=>$shift)
							{
								$shift_from = strtotime($shift_details[$key]['from_hr_min']);
								$shift_to = strtotime($shift_details[$key]['to_hr_min']);


								/*
								if(strtotime($fromtime)>=$shift_from && strtotime($totime)<=$shift_to)
								{
									$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
								}

								if(strtotime($fromtime)>=$shift_from && strtotime($totime)>=$shift_to && $key==3)
								{
									$machine_idle_cause[$machine_no][3]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
								}
								*/




								if( strtotime($from_date) == strtotime($row[csf('to_date')]))
								{
									if($key==3)
									{
										if(strtotime($totime)>=$shift_from && strtotime($totime)<=$shift_to)
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
										else if(strtotime($totime)>=$shift_from && strtotime($totime)>$shift_to)
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}
									else
									{
										if(strtotime($totime)>=$shift_from && strtotime($totime)<=$shift_to)
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}
								}
								else
								{
									//echo "<pre> fromtime=".$fromtime." ; totime=".$totime."</pre>";
									//echo "<pre> shift_from=".$shift_from." ; shift_to=".$shift_to."</pre>";die;
									if($key==3)
									{
										if(strtotime($totime)<=$shift_from && strtotime($totime)<=$shift_to )
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}else{
										if(strtotime($totime)>=$shift_from && strtotime($totime)<=$shift_to )
										{
											$machine_idle_cause[$machine_no][$key]['machine_idle_cause'][]=$row[csf('machine_idle_cause')];
										}
									}



								}



							}
						}
					}


					foreach ($machine_details as $key => $value) {
						$machine_dia_gauge = rtrim(implode(",", $value),',');
					}

					$i=1;
					// echo "<pre>";
					// print_r($machineSamarryDataArr);
					// echo "</pre>";
					foreach($machineSamarryDataArr as $machine_id=>$row):
						$machine_no = $machine_details[$machine_id]['no'];
						$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
						$mc_idle_hr_from = strtotime($machine_summary_arr[$machine_no]['from_hr_min']);
						$mc_idle_hr_to 	 = strtotime($machine_summary_arr[$machine_no]['to_hr_min']);

						$machine_group = $machine_details[$machine_id]['machine_group'];
						$dia_width = ($machine_group!="")?",".$machine_details[$machine_id]['dia_width']:"";
						$gauge = ($machine_group!="" || $dia_width!="")?",".$machine_details[$machine_id]['gauge']:"";

						$machine_dia_gauge = $machine_group."".$dia_width."".$gauge;

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sm<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_sm<? echo $i; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $machine_no; ?></td>
							<td><? echo $machine_dia_gauge; ?></td>
							<td align="right"><? echo $mCapacity=$mcpacity_arr[$machine_no]; $totmCapacity+=$mCapacity;?></td>
							<?
							$html.="
							<tr bgcolor='".$bgcolor."' id='tr_sm".$i."'>
							<td align='center'>".$i."</td>
							<td>".$machine_no."</td>
							<td align='right'>".$mcpacity_arr[$machine_no]."</td>";
							$totPro=0;
							foreach($row as $key=>$val)
							{
								$shift_from_hr_min 	= strtotime($shift_details[$key]['from_hr_min']);
								$shift_to_hr_min  	= strtotime($shift_details[$key]['to_hr_min']);
								?>
								<td align="right" title="<? echo $shift_details[$key]['from_hr_min'].'='.$shift_details[$key]['to_hr_min'];?>">
									<? echo number_format($val,2); $proQty[$key]+=$val;$totPro+=$val;  ?>
								</td>
								<td align="right">
									<?
									$mc_cause="";
									$causes = array_unique($machine_idle_cause[$machine_no][$key]['machine_idle_cause']);
									foreach ($causes as $cause) {
										$mc_cause .= $cause_type[$cause] . ",";
									}
									echo rtrim($mc_cause,", ");
									?>
								</td>
								<?
								$html.="<td align='right'>".$val."</td>";
								$html.="<td align='right'>".$val."</td>";
							}
							?>
							<td align="right"><? echo number_format($totPro,2); $gTotPro+=$totPro;?></td>
							<td align="right"><? echo round(($totPro/$mCapacity)*100);?></td>
							<td align="right">
								<?
								echo  $html_sum=number_format($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no],2);
								$totymc+=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
								?>
							</td>
							<td align="right"><? echo round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100);?></td>
						</tr>
						<?
						$html.="
						<td align='right'>".$totPro."</td>
						<td align='right'>".round(($totPro/$mCapacity)*100)."</td>
						<td align='right'>".$html_sum."</td>
						<td align='right'>".round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100)."</td>
						</tr>
						";
						$i++;
					endforeach;
					?>
					<tfoot>
						<th></th>
						<th></th>
						<th>Total</th>
						<th><? echo $totmCapacity;?></th>

						<?
						$html.="
						<tfoot>
						<th></th>
						<th>Total</th>
						<th>".$totmCapacity."</th>
						";
						foreach($shift_name as $key=>$val)
						{
							?>
							<th><? echo number_format($proQty[$key],2); ?></th>
							<th></th>
							<?
							$html.="<th>".$proQty[$key]."</th>";
							$html.="<th>".$proQty[$key]."</th>";
						}
						?>
						<th><? echo number_format($gTotPro,2);?></th>
						<th><? echo round(($gTotPro/$totmCapacity)*100);?></th>
						<th><? echo number_format($totymc,2);?></th>
						<th><? echo round(($totymc/$totmCapacity)*100);?></th>
					</tfoot>
				</table>
			</fieldset>
			<?
			$html.="
			<th>".$gTotPro."</th>
			<th>".round(($gTotPro/$totmCapacity)*100)."</th>
			<th>".$totymc."</th>
			<th>".round(($totymc/$totmCapacity)*100)."</th>
			</tfoot>
			</table>
			</fieldset>
			";
		}
		unset($machineSamarryDataArr);
		//  Machine wise summary Data End

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
}

if($action=="report_generate_today") // Today Production
{
	$process = array( &$_POST );
	//print_r($process);
	//die;
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$from_date=$txt_date_from;
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);

	if($cbo_company==0) $company_name_cond=""; else $company_name_cond=" and c.company_id in($cbo_company)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and c.knitting_company=$cbo_working_company";
	if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

	$tbl_width=2130+count($shift_name)*155;
	$col_span=26+count($shift_name)*2;
	ob_start();
	?>
	<fieldset style="width:<? echo $tbl_width+20; ?>px;">
	<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
		<tr>
			<td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:18px"><? echo "Daily Inhouse Knitting Production Report"; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:12px" ><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
		</tr>
	</table>
	<?
	if($cbo_type==1 || $cbo_type==0)
	{
		?>
		<div>
			<div align="left" style="background-color:#E1E1E1; color:#000; width:350px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House) Knitting Production</i></u></strong></div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="60" rowspan="2">Job No</th>
						<th width="100" rowspan="2">Booking No</th>
						<th width="60" rowspan="2">Year</th>
						<th width="70" rowspan="2">Buyer</th>
						<th width="100" rowspan="2">Style</th>
						<th width="110" rowspan="2">Order No</th>
						<th width="60" rowspan="2">Lot No</th>
						<th width="150" rowspan="2">Fabric Type</th>
						<th width="50" rowspan="2">Stitch</th>
						<th width="60" rowspan="2">Fin GSM</th>
						<th width="100" rowspan="2">Fabric Color</th>
						<th width="90" rowspan="2">Req. Qty.</th>
						<th width="150" colspan="2">Prev. Production</th>
						<?
						foreach($shift_name as $val)
						{
							?>
							<th width="150" colspan="2"><? echo $val; ?></th>
							<?
						}
						?>
						<th width="150" colspan="2">No Shift</th>
						<th width="150" colspan="2">Today Production</th>
						<th width="150" colspan="2">Total Production</th>
						<th width="100" rowspan="2">Yet To Production</th>
						<th width="70" rowspan="2">Rate</th>
						<th width="100" rowspan="2">Today Revenue</th>
						<th width="100" rowspan="2">Total Revenue</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
						<?
						foreach($shift_name as $val)
						{
							?>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<?
						}
						?>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
					<?
					$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
					$buyer_library=return_library_array("select id,short_name from lib_buyer", "id", "short_name");

					$composition_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
								$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}
					}

					$i=1; $tot_rolla=''; $tot_rollb=''; $tot_rollc=''; $tot_rolla_qnty=0; $tot_rollb_qnty=0; $tot_rollc_qnty=0; $grand_tot_roll=''; $grand_tot_qnty=0; $tot_subcontract=0;
					if($db_type==0)
					{
						$year_field="YEAR(a.insert_date)";
						$year_field_sam="YEAR(a.insert_date)";
						if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
					}
					else if($db_type==2)
					{
						$year_field="to_char(a.insert_date,'YYYY')";
						$year_field_sam="to_char(a.insert_date,'YYYY')";
						if($cbo_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
					}
					else $year_field="";

					$date_con="";
					if($from_date!="" && $to_date!="") $date_con=" and c.receive_date between '$from_date' and '$to_date'";

					if($db_type==0)
					{
						$select_color=", d.color_id as color_id";
						$group_color=", d.color_id";
					}
					else if($db_type==2)
					{
						$select_color=", nvl(d.color_id,0) as color_id";
						$group_color=", nvl(d.color_id,0)";
					}

					$location_cond='';

					if(!empty($cbo_location_id))
					{
						$location_cond=" and c.knitting_location_id=$cbo_location_id ";
					}

					// $cbo_booking_type=118;
					if($cbo_booking_type > 0)
					{
						if($cbo_booking_type == 89){ // SM
							$entry_form_cond = " and f.booking_type = 4 ";
						}
						else
						{
							$entry_form_cond = " and f.entry_form=$cbo_booking_type";
						}
					}
					else
					{
						$entry_form_cond = "";
					}
					if(!empty($txt_job))
					{
						$job_no_cond=" and a.job_no_prefix_num=$txt_job ";
					}
					else
					{
						$job_no_cond = "";
					}
					// unionn all for Booking Type condition as per discuss with tofael vai
					$sql_inhouse="select a.job_no, a.job_no_prefix_num, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm $select_color, d.febric_description_id, d.width, f.booking_no,
					sum(case when d.shift_name=0 then e.quantity else 0 end ) as qntynoshift,
					sum(case when d.shift_name=0 then d.no_of_roll end ) as rollnoshift";
					foreach($shift_name as $key=>$val)
					{
						$sql_inhouse.=", sum(case when d.shift_name=$key then d.no_of_roll end ) as roll".strtolower($val)."
						, sum(case when d.shift_name=$key then e.quantity else 0 end ) as qntyshift".strtolower($val);
					}
					$sql_inhouse.=" from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details e, pro_grey_prod_entry_dtls d, inv_receive_master c,
					PPL_PLANNING_INFO_ENTRY_DTLS g, PPL_PLANNING_INFO_ENTRY_MST h, WO_BOOKING_MST f
					where  a.job_no=b.job_no_mst and b.id=e.po_breakdown_id and e.dtls_id=d.id and d.mst_id=c.id and c.booking_id=g.id and g.mst_id=h.id and h.BOOKING_NO=f.BOOKING_NO and f.BOOKING_TYPE in(1,4) $company_name_cond $company_working_cond and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_con $job_year_cond $location_cond  $job_no_cond and c.receive_basis=2 $entry_form_cond and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
					group by a.job_no, a.job_no_prefix_num, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm, d.color_id,  d.febric_description_id, d.width, f.booking_no
					union all ";
					$sql_inhouse.="select a.job_no, a.job_no_prefix_num, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm $select_color, d.febric_description_id, d.width, f.booking_no,
					sum(case when d.shift_name=0 then e.quantity else 0 end ) as qntynoshift,
					sum(case when d.shift_name=0 then d.no_of_roll end ) as rollnoshift";
					foreach($shift_name as $key=>$val)
					{
						$sql_inhouse.=", sum(case when d.shift_name=$key then d.no_of_roll end ) as roll".strtolower($val)."
						, sum(case when d.shift_name=$key then e.quantity else 0 end ) as qntyshift".strtolower($val);
					}
					$sql_inhouse.=" from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details e, pro_grey_prod_entry_dtls d, inv_receive_master c, WO_BOOKING_MST f where  a.job_no=b.job_no_mst and b.id=e.po_breakdown_id and e.dtls_id=d.id and d.mst_id=c.id and c.booking_id=f.id and f.BOOKING_TYPE in(1,4) $company_name_cond $company_working_cond and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_con $job_year_cond $location_cond  $job_no_cond and c.receive_basis=1 $entry_form_cond and f.status_active=1 and f.is_deleted=0
					group by a.job_no, a.job_no_prefix_num, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm, d.color_id,  d.febric_description_id, d.width, f.booking_no order by job_no_prefix_num DESC";
					// echo $sql_inhouse;die;

					$nameArray_inhouse=sql_select($sql_inhouse);
					foreach($nameArray_inhouse as $row)
					{
						$job_no_arr[] = "'".$row[csf("job_no")]."'";
						$po_arr[] = $row[csf("id")];
					}

					//echo $sql_inhouse;
					$curr_value=array();
					if(!empty($job_no_arr)){
						$curr_value=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no in(".implode(",",$job_no_arr).")", "job_no", "exchange_rate");
					}

					$req_qty_arr=array();
					if(!empty($po_arr))
					{
						$sql_req="select a.po_break_down_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id, sum(a.grey_fab_qnty) as req_qty, sum(c.charge_unit) as rate from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_cost_fab_conv_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=b.id and b.id=c.fabric_description and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.cons_process=1 and a.po_break_down_id in(".implode(",",$po_arr).") group by a.po_break_down_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id";
						$sql_req_result=sql_select($sql_req);
						foreach( $sql_req_result as $row )
						{
							$req_qty_arr[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['req_qty']=$row[csf('req_qty')];
							$req_qty_arr[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['rate']=$row[csf('rate')];
						}

						$prev_production_arr=array();
						$prev_sql="select d.febric_description_id, d.gsm, d.width, e.po_breakdown_id, sum(e.quantity) as  beforeqnty, sum(d.no_of_roll) as beforeroll from inv_receive_master c, pro_grey_prod_entry_dtls d, order_wise_pro_details e where c.id=d.mst_id and d.id=e.dtls_id and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.receive_date<'".$from_date."' and e.po_breakdown_id in(".implode(",",$po_arr).") group by d.febric_description_id, d.gsm, d.width, e.po_breakdown_id";
						$prev_sql_result=sql_select($prev_sql);
						foreach( $prev_sql_result as $row )
						{
							$prev_production_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['qty']=$row[csf('beforeqnty')];
							$prev_production_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['roll']=$row[csf('beforeroll')];
						}

						/*$sql_booking="SELECT a.po_break_down_id, a.booking_no from wo_booking_dtls a where a.status_active=1 and a.is_deleted=0  and a.po_break_down_id in(".implode(",",$po_arr).") group by a.po_break_down_id, a.booking_no";
						//echo $sql_booking;
						$sql_booking_result=sql_select($sql_booking);
						$booking_info_arr = array();
						foreach($sql_booking_result as $row)
						{
							$booking_pre = explode('-',$row[csf('booking_no')]);
							if($booking_pre[1]=="Fb" || $booking_pre[1]=="FB")
							{
								$booking_info_arr[$row[csf('po_break_down_id')]]['booking_no']=$row[csf('booking_no')];
							}
							
						}
						unset($sql_booking_result);*/
						//echo "<pre>";print_r($booking_info_arr);

					}
					$z=0;
					foreach($nameArray_inhouse as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$req_qty=$req_qty_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['req_qty'];
						$avg_rate=$req_qty_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['rate'];
						$prev_qty=$prev_production_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['qty'];
						$prev_roll=$prev_production_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['roll'];
						$exchange_rate=$curr_value[$row[csf('job_no')]]*$avg_rate;
						//$booking_no=$booking_info_arr[$row[csf('id')]]['booking_no'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
							<td width="100"><? echo $row[csf('booking_no')];//$booking_no; ?></td>
							<td width="60"><? echo $row[csf('year')]; ?></td>
							<td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="60"><p><? echo $row[csf('yarn_lot')]; ?></p></td>
							<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
							<td width="50"><P><? echo $row[csf('stitch_length')]; ?></P></td>
							<td width="60"><P><? echo $row[csf('gsm')]; ?></P></td>
							<td width="100"><P>
								<?
								$color_arr=array_unique(explode(",",$row[csf('color_id')]));
								$all_color="";
								foreach($color_arr as $id)
								{
									$all_color.=$color_library[$id].",";
								}
								$all_color=chop($all_color," , ");
								echo $all_color;
								?></P>
							</td>
							<td width="90" align="right"><? echo number_format($req_qty,2,'.',''); ?></td>
							<td width="50" align="right"><? echo number_format($prev_roll,2,'.',''); ?></td>
							<td width="100" align="right"><? echo number_format($prev_qty,2,'.',''); ?></td>
							<?
							$row_tot_roll=0;
							$row_tot_qnty=0;
							foreach($shift_name as $key=>$val)
							{
								$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
								$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

								$row_tot_roll+=$row[csf('roll'.strtolower($val))];
								$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
								?>
								<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
								<td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); ?></td>
								<?
							}
							?>
							<td width="50" align="right"><? echo $row[csf('rollnoshift')]; ?></td>
							<td width="100" align="right"><? echo number_format($row[csf('qntynoshift')],2); ?></td>

							<td width="50" align="right"><? $today_production_roll=$row_tot_roll+$row[csf('rollnoshift')]; echo $today_production_roll; ?></td>
							<td width="100" align="right"><? $today_production_qty=$row_tot_qnty+$row[csf('qntynoshift')]; echo number_format($today_production_qty,2,'.',''); ?></td>

							<?
							$tot_production_roll=$prev_roll+$row_tot_roll+$row[csf('rollnoshift')];
							$tot_production_qty=$prev_qty+$row_tot_qnty+$row[csf('qntynoshift')];
							?>
							<td width="50" align="right"><? echo $tot_production_roll; ?></td>
							<td width="100" align="right"><? echo number_format($tot_production_qty,2); ?></td>

							<td width="100" align="right"><? $yet_prod=$req_qty-$tot_production_qty; echo number_format($yet_prod,2); ?></td>
							<td width="70" align="right"><? echo number_format($exchange_rate,4); ?></td>
							<td width="100" align="right"><? $today_revenue=$today_production_qty*$exchange_rate; echo number_format($today_revenue,2); ?></td>
							<td width="100" align="right"><? $tot_revenue=$tot_production_qty*$exchange_rate; echo number_format($tot_revenue,2); ?></td>
							<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
						</tr>
						<?
						$total_req_qty+=$req_qty;
						$total_prev_roll+=$prev_roll;
						$total_prev_qty+=$prev_qty;
						$total_noshift_roll+=$row[csf('rollnoshift')];
						$total_noshift_qty+=$row[csf('qntynoshift')];
						$total_today_production_roll+=$today_production_roll;
						$total_today_production_qty+=$today_production_qty;
						$total_production_roll+=$tot_production_roll;
						$total_production_qty+=$tot_production_qty;
						$total_yet_production+=$yet_prod;
						$total_today_revenue+=$today_revenue;
						$total_revenue+=$tot_revenue;
						$i++;
					}
					?>
				</table>
			</div>
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
				<tr>
					<td align="right" width="30">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="100">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="70">&nbsp;</td>
					<td align="right" width="100">&nbsp;</td>
					<td align="right" width="110">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="150">&nbsp;</td>
					<td align="right" width="50">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="100"><strong>Total</strong></td>
					<td align="right" width="90"><? echo number_format($total_req_qty,2); ?></td>
					<td align="right" width="50"><? echo number_format($total_prev_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_prev_qty,2); ?></td>
					<?
					foreach($shift_name as $key=>$val)
					{
						?>
						<td align="right" width="50"><? echo number_format($tot_roll[$key]['roll'],2,'.',''); ?></td>
						<td align="right" width="100"><? echo number_format($tot_roll[$key]['qty'],2,'.',''); ?></td>
						<?
					}
					?>
					<td align="right" width="50"><? echo number_format($total_noshift_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_noshift_qty,2); ?></td>
					<td align="right" width="50"><? echo number_format($total_today_production_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_today_production_qty,2); ?></td>
					<td align="right" width="50"><? echo number_format($total_production_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_production_qty,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_yet_production,2); ?></td>
					<td align="right" width="70">&nbsp;</td>
					<td align="right" width="100"><? echo number_format($total_today_revenue,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_revenue,2); ?></td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}
	if($cbo_type==2 || $cbo_type==0) // SubCon
	{
		$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
		$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");

		if($db_type==0)
		{
			$year_sub_field="YEAR(a.insert_date)";
			if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
		}
		else if($db_type==2)
		{
			$year_sub_field="to_char(a.insert_date,'YYYY')";
			if($cbo_year!=0) $job_year_sub_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";
		}
		else $year_sub_field="";

		if($db_type==0)
		{
			$select_color=", d.color_id as color_id";
			$group_color=", d.color_id";
		}
		else if($db_type==2)
		{
			$select_color=", nvl(d.color_id,0) as color_id";
			$group_color=", nvl(d.color_id,0)";
		}

		$req_qty_arr=array();
		$sql_req="select order_id, item_id, sum(qnty) as req_qty, avg(rate) as rate from  subcon_ord_breakdown group by order_id, item_id";
		$sql_req_result=sql_select( $sql_req);
		foreach($sql_req_result as $row)
		{
			$req_qty_arr[$row[csf('order_id')]][$row[csf('item_id')]]['req_qty']=$row[csf('req_qty')];
			$req_qty_arr[$row[csf('order_id')]][$row[csf('item_id')]]['rate']=$row[csf('rate')];
		}

		if($from_date!="" && $to_date!="") $date_con_sub=" and c.product_date between '$from_date' and '$to_date'"; else $date_con_sub="";
		$prev_produ_arr=array();
		$sql_prev="select b.order_id, b.cons_comp_id, b.gsm, b.dia_width, sum(b.product_qnty) as prev_qty, sum(b.no_of_roll) as prev_roll from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.product_date<'".$from_date."'  group by b.order_id, b.cons_comp_id, b.gsm, b.dia_width";
		$sql_prev_result=sql_select( $sql_prev);
		foreach($sql_prev_result as $row)
		{
			$prev_produ_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_qty']=$row[csf('prev_qty')];
			$prev_produ_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_roll']=$row[csf('prev_roll')];
		}
		if($db_type==0)
		{
			$job_ord_cond="and d.order_id=b.id";
		}
		else if ($db_type==2)
		{
			$job_ord_cond="and d.job_no=b.job_no_mst";
		}
		$sql_inhouse_sub="select a.job_no_prefix_num, a.party_id, $year_sub_field as year, d.order_id as id, b.order_no, b.cust_style_ref, d.cons_comp_id, d.gsm, d.dia_width, d.yarn_lot, d.stitch_len $select_color, sum(case when d.shift=0 then d.product_qnty else 0 end ) as qntynoshift, sum(case when d.shift=0 then d.no_of_roll end ) as rollnoshift";
		foreach($shift_name as $key=>$val)
		{
			$sql_inhouse_sub.=", sum(case when d.shift=$key then d.no_of_roll end ) as roll".strtolower($val)."
			, sum(case when d.shift=$key then d.product_qnty else 0 end ) as qntyshift".strtolower($val);
		}
		$sql_inhouse_sub.="
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d where c.company_id in($cbo_company) and c.company_id=$cbo_working_company_id and a.subcon_job=b.job_no_mst and c.id=d.mst_id $job_ord_cond and c.product_type=2 $job_year_sub_cond $date_con_sub group by a.job_no_prefix_num, a.party_id, a.insert_date, d.order_id, b.order_no, b.cust_style_ref, d.cons_comp_id, d.gsm, d.dia_width, d.yarn_lot, d.stitch_len, d.color_id order by a.job_no_prefix_num DESC ";

		$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub); $k=1; $tot_roll_sub=array();
		if(count($nameArray_inhouse_subcon)>0)
		{
			?>
			<br>
			<div>
				<div align="left" style="background-color:#E1E1E1; color:#000; width:350px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Sub-Contract Order Knitting Production</i></u></strong></div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th width="60" rowspan="2">Job No</th>
							<th width="60" rowspan="2">Year</th>
							<th width="70" rowspan="2">Party</th>
							<th width="100" rowspan="2">Cust Style</th>
							<th width="110" rowspan="2">Order No</th>
							<th width="60" rowspan="2">Lot No</th>
							<th width="150" rowspan="2">Fabric Type</th>
							<th width="50" rowspan="2">Stitch</th>
							<th width="60" rowspan="2">Fin GSM</th>
							<th width="100" rowspan="2">Fabric Color</th>
							<th width="90" rowspan="2">Req. Qty.</th>
							<th width="150" colspan="2">Prev. Production</th>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="150" colspan="2"><? echo $val; ?></th>
								<?
							}
							?>
							<th width="150" colspan="2">No Shift</th>
							<th width="150" colspan="2">Today Production</th>
							<th width="150" colspan="2">Total Production</th>
							<th width="100" rowspan="2">Yet To Production</th>
							<th width="70" rowspan="2">Rate</th>
							<th width="100" rowspan="2">Today Revenue</th>
							<th width="100" rowspan="2">Total Revenue</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="50">Roll</th>
								<th width="100">Qnty</th>
								<?
							}
							?>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
						<?
						foreach($nameArray_inhouse_subcon as $row)
						{
							if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$req_qty=$req_qty_arr[$row[csf('id')]][$row[csf('cons_comp_id')]]['req_qty'];
							$avg_rate=$req_qty_arr[$row[csf('id')]][$row[csf('cons_comp_id')]]['rate'];
							$prev_qty=$prev_produ_arr[$row[csf('id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_qty'];
							$prev_roll=$prev_produ_arr[$row[csf('id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_roll'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trw_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trw_<? echo $k; ?>">
								<td width="30"><? echo $k; ?></td>
								<td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
								<td width="60"><? echo $row[csf('year')]; ?></td>
								<td width="70"><p><? echo $buyer_library[$row[csf('party_id')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
								<td width="110"><p><? echo $row[csf('order_no')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('yarn_lot')]; ?></p></td>
								<td width="150"><p><? echo $const_comp_arr[$row[csf('cons_comp_id')]]; ?></p></td>
								<td width="50"><P><? echo $row[csf('stitch_len')]; ?></P></td>
								<td width="60"><P><? echo $row[csf('gsm')]; ?></P></td>
								<td width="100"><P>
									<?
									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_library[$id].",";
									}
									$all_color=chop($all_color," , ");
									echo $all_color;
									?></P>
								</td>
								<td width="90" align="right"><? echo number_format($req_qty,2,'.',''); ?></td>
								<td width="50" align="right"><? echo number_format($prev_roll,2,'.',''); ?></td>
								<td width="100" align="right"><? echo number_format($prev_qty,2,'.',''); ?></td>
								<?
								$row_tot_roll=0;
								$row_tot_qnty=0;
								foreach($shift_name as $key=>$name)
								{
									$tot_roll_sub[$key]['roll']+=$row[csf('roll'.strtolower($name))];
									$tot_roll_sub[$key]['qty']+=$row[csf('qntyshift'.strtolower($name))];

									$row_tot_roll+=$row[csf('roll'.strtolower($name))];
									$row_tot_qnty+=$row[csf('qntyshift'.strtolower($name))];
									?>
									<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($name))]; ?></td>
									<td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($name))],2); ?></td>
									<?
								}
								?>
								<td width="50" align="right"><? echo $row[csf('rollnoshift')]; ?></td>
								<td width="100" align="right"><? echo number_format($row[csf('qntynoshift')],2); ?></td>

								<td width="50" align="right"><? $today_production_roll=$row_tot_roll+$row[csf('rollnoshift')]; echo $today_production_roll; ?></td>
								<td width="100" align="right"><? $today_production_qty=$row_tot_qnty+$row[csf('qntynoshift')]; echo number_format($today_production_qty,2,'.',''); ?></td>

								<?
								$tot_production_roll=$prev_roll+$row_tot_roll+$row[csf('rollnoshift')];
								$tot_production_qty=$prev_qty+$row_tot_qnty+$row[csf('qntynoshift')];
								?>
								<td width="50" align="right"><? echo $tot_production_roll; ?></td>
								<td width="100" align="right"><? echo number_format($tot_production_qty,2); ?></td>

								<td width="100" align="right"><? $yet_prod=$req_qty-$tot_production_qty; echo number_format($yet_prod,2); ?></td>
								<td width="70" align="right"><? echo number_format($avg_rate,4); ?></td>
								<td width="100" align="right"><? $today_revenue=$today_production_qty*$avg_rate; echo number_format($today_revenue,2); ?></td>
								<td width="100" align="right"><? $tot_revenue=$tot_production_qty*$avg_rate; echo number_format($tot_revenue,2); ?></td>
								<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
							</tr>
							<?
							$sub_total_req_qty+=$req_qty;
							$sub_total_prev_roll+=$prev_roll;
							$sub_total_prev_qty+=$prev_qty;
							$sub_total_noshift_roll+=$row[csf('rollnoshift')];
							$sub_total_noshift_qty+=$row[csf('qntynoshift')];
							$sub_total_today_production_roll+=$today_production_roll;
							$sub_total_today_production_qty+=$today_production_qty;
							$sub_total_production_roll+=$tot_production_roll;
							$sub_total_production_qty+=$tot_production_qty;
							$sub_total_yet_production+=$yet_prod;
							$sub_total_today_revenue+=$today_revenue;
							$sub_total_revenue+=$tot_revenue;
							$k++;
						}
						?>
					</table>
				</div>
				<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
					<tr>
						<td align="right" width="30">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="100">&nbsp;</td>
						<td align="right" width="110">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="150">&nbsp;</td>
						<td align="right" width="50">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="100"><strong>Total</strong></td>
						<td align="right" width="90"><? echo number_format($sub_total_req_qty,2); ?></td>
						<td align="right" width="50"><? echo number_format($sub_total_prev_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_prev_qty,2); ?></td>
						<?
						foreach($shift_name as $key=>$val)
						{
							?>
							<td align="right" width="50"><? echo number_format($tot_roll_sub[$key]['roll'],2,'.',''); ?></td>
							<td align="right" width="100"><? echo number_format($tot_roll_sub[$key]['qty'],2,'.',''); ?></td>
							<?
						}
						?>
						<td align="right" width="50"><? echo number_format($sub_total_noshift_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_noshift_qty,2); ?></td>
						<td align="right" width="50"><? echo number_format($sub_total_today_production_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_today_production_qty,2); ?></td>
						<td align="right" width="50"><? echo number_format($sub_total_production_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_production_qty,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_yet_production,2); ?></td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="100"><? echo number_format($sub_total_today_revenue,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_revenue,2); ?></td>
						<td align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
			<?
		}
	}
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";

	disconnect($con);
	exit();
}

if($action=="report_generate_today2") // Today Production 2
{
	$process = array( &$_POST );
	//print_r($process);
	//die;
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_booking_type=str_replace("'","",$cbo_booking_type);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$from_date=$txt_date_from;
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);

	if($cbo_company==0) $company_name_cond=""; else $company_name_cond=" and c.company_id in($cbo_company)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and c.knitting_company=$cbo_working_company";
	if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

	$tbl_width=2030+count($shift_name)*155;
	$col_span=25+count($shift_name)*2;
	ob_start();
	?>
	<fieldset style="width:<? echo $tbl_width+20; ?>px;">
	<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
		<tr>
			<td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:18px"><? echo "Daily Inhouse Knitting Production Report"; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:12px" ><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
		</tr>
	</table>
	<?
	if($cbo_type==1 || $cbo_type==0) // Self
	{
		?>
		<div>
			<div align="left" style="background-color:#E1E1E1; color:#000; width:350px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House) Knitting Production</i></u></strong></div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="60" rowspan="2">Job No</th>
						<th width="60" rowspan="2">Year</th>
						<th width="70" rowspan="2">Buyer</th>
						<th width="100" rowspan="2">Style</th>
						<th width="110" rowspan="2">Order No</th>
						<th width="60" rowspan="2">Lot No</th>
						<th width="150" rowspan="2">Fabric Type</th>
						<th width="50" rowspan="2">Stitch</th>
						<th width="60" rowspan="2">Fin GSM</th>
						<th width="100" rowspan="2">Fabric Color</th>
						<th width="90" rowspan="2">Req. Qty.</th>
						<th width="150" colspan="2">Prev. Production</th>
						<?
						foreach($shift_name as $val)
						{
							?>
							<th width="150" colspan="2"><? echo $val; ?></th>
							<?
						}
						?>
						<th width="150" colspan="2">No Shift</th>
						<th width="150" colspan="2">Today Production</th>
						<th width="150" colspan="2">Total Production</th>
						<th width="100" rowspan="2">Yet To Production</th>
						<th width="70" rowspan="2">Rate</th>
						<th width="100" rowspan="2">Today Revenue</th>
						<th width="100" rowspan="2">Total Revenue</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
						<?
						foreach($shift_name as $val)
						{
							?>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<?
						}
						?>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
						<th width="50">Roll</th>
						<th width="100">Qnty</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
					<?
					$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
					$buyer_library=return_library_array("select id,short_name from lib_buyer", "id", "short_name");

					$composition_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
								$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}
					}

					$i=1; $tot_rolla=''; $tot_rollb=''; $tot_rollc=''; $tot_rolla_qnty=0; $tot_rollb_qnty=0; $tot_rollc_qnty=0; $grand_tot_roll=''; $grand_tot_qnty=0; $tot_subcontract=0;
					if($db_type==0)
					{
						$year_field="YEAR(a.insert_date)";
						$year_field_sam="YEAR(a.insert_date)";
						if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
					}
					else if($db_type==2)
					{
						$year_field="to_char(a.insert_date,'YYYY')";
						$year_field_sam="to_char(a.insert_date,'YYYY')";
						if($cbo_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
					}
					else $year_field="";

					$date_con="";
					if($from_date!="" && $to_date!="") $date_con=" and c.receive_date between '$from_date' and '$to_date'";

					if($db_type==0)
					{
						$select_color=", d.color_id as color_id";
						$group_color=", d.color_id";
					}
					else if($db_type==2)
					{
						$select_color=", nvl(d.color_id,0) as color_id";
						$group_color=", nvl(d.color_id,0)";
					}

					$location_cond='';

					if(!empty($cbo_location_id))
					{
						$location_cond=" and c.knitting_location_id=$cbo_location_id ";
					}

					// $cbo_booking_type=118;
					if($cbo_booking_type > 0)
					{
						if($cbo_booking_type == 89){ // SM
							$entry_form_cond = " and f.booking_type = 4 ";
						}
						else
						{
							$entry_form_cond = " and f.entry_form=$cbo_booking_type";
						}
					}
					else
					{
						$entry_form_cond = "";
					}

					// unionn all for Booking Type condition as per discuss with tofael vai
					$sql_inhouse="select a.job_no, a.job_no_prefix_num, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm $select_color, d.febric_description_id, d.width,
					sum(case when d.shift_name=0 then e.quantity else 0 end ) as qntynoshift,
					sum(case when d.shift_name=0 then d.no_of_roll end ) as rollnoshift";
					foreach($shift_name as $key=>$val)
					{
						$sql_inhouse.=", sum(case when d.shift_name=$key then d.no_of_roll end ) as roll".strtolower($val)."
						, sum(case when d.shift_name=$key then e.quantity else 0 end ) as qntyshift".strtolower($val);
					}
					$sql_inhouse.=" from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details e, pro_grey_prod_entry_dtls d, inv_receive_master c,
					PPL_PLANNING_INFO_ENTRY_DTLS g, PPL_PLANNING_INFO_ENTRY_MST h, WO_BOOKING_MST f
					where  a.job_no=b.job_no_mst and b.id=e.po_breakdown_id and e.dtls_id=d.id and d.mst_id=c.id and c.booking_id=g.id and g.mst_id=h.id and h.BOOKING_NO=f.BOOKING_NO and f.BOOKING_TYPE in(1,4) $company_name_cond $company_working_cond and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_con $job_year_cond $location_cond and c.receive_basis=2 $entry_form_cond and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
					group by a.job_no, a.job_no_prefix_num, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm, d.color_id,  d.febric_description_id, d.width
					union all ";

					$sql_inhouse.="select a.job_no, a.job_no_prefix_num, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm $select_color, d.febric_description_id, d.width,
					sum(case when d.shift_name=0 then e.quantity else 0 end ) as qntynoshift,
					sum(case when d.shift_name=0 then d.no_of_roll end ) as rollnoshift";
					foreach($shift_name as $key=>$val)
					{
						$sql_inhouse.=", sum(case when d.shift_name=$key then d.no_of_roll end ) as roll".strtolower($val)."
						, sum(case when d.shift_name=$key then e.quantity else 0 end ) as qntyshift".strtolower($val);
					}
					$sql_inhouse.=" from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details e, pro_grey_prod_entry_dtls d, inv_receive_master c, WO_BOOKING_MST f
					where  a.job_no=b.job_no_mst and b.id=e.po_breakdown_id and e.dtls_id=d.id and d.mst_id=c.id and c.booking_id=f.id and f.BOOKING_TYPE in(1,4) $company_name_cond $company_working_cond and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_con $job_year_cond $location_cond and c.receive_basis=1 $entry_form_cond and f.status_active=1 and f.is_deleted=0
					group by a.job_no, a.job_no_prefix_num, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm, d.color_id,  d.febric_description_id, d.width order by job_no_prefix_num DESC";
					// echo $sql_inhouse;die;

					$nameArray_inhouse=sql_select($sql_inhouse);
					foreach($nameArray_inhouse as $row)
					{
						$job_no_arr[] = "'".$row[csf("job_no")]."'";
						$po_arr[] = $row[csf("id")];
					}

					$curr_value=array();
					if(!empty($job_no_arr)){
						$curr_value=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no in(".implode(",",$job_no_arr).")", "job_no", "exchange_rate");
					}

					$req_qty_arr=array();
					if(!empty($po_arr))
					{
						$sql_req="SELECT a.po_break_down_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id, sum(a.grey_fab_qnty) as req_qty, c.charge_unit as rate from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_cost_fab_conv_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=b.id and b.id=c.fabric_description and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.cons_process=1 and a.po_break_down_id in(".implode(",",$po_arr).") group by a.po_break_down_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id, c.charge_unit";
						$sql_req_result=sql_select($sql_req);
						foreach( $sql_req_result as $row )
						{
							$req_qty_arr[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]]['req_qty']=$row[csf('req_qty')];
							$req_qty_arr[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]]['rate']=$row[csf('rate')];
						}

						$prev_production_arr=array();
						$prev_sql="select d.febric_description_id, d.gsm, d.width, e.po_breakdown_id, sum(e.quantity) as  beforeqnty, sum(d.no_of_roll) as beforeroll from inv_receive_master c, pro_grey_prod_entry_dtls d, order_wise_pro_details e where c.id=d.mst_id and d.id=e.dtls_id and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.receive_date<'".$from_date."' and e.po_breakdown_id in(".implode(",",$po_arr).") group by d.febric_description_id, d.gsm, d.width, e.po_breakdown_id";
						$prev_sql_result=sql_select($prev_sql);
						foreach( $prev_sql_result as $row )
						{
							$prev_production_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['qty']=$row[csf('beforeqnty')];
							$prev_production_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['roll']=$row[csf('beforeroll')];
						}
					}
					$z=0;
					foreach($nameArray_inhouse as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$req_qty=$req_qty_arr[$row[csf('id')]][$row[csf('febric_description_id')]]['req_qty'];
						$avg_rate=$req_qty_arr[$row[csf('id')]][$row[csf('febric_description_id')]]['rate'];
						$prev_qty=$prev_production_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['qty'];
						$prev_roll=$prev_production_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['roll'];
						$exchange_rate=$curr_value[$row[csf('job_no')]]*$avg_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
							<td width="60"><? echo $row[csf('year')]; ?></td>
							<td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="60"><p><? echo $row[csf('yarn_lot')]; ?></p></td>
							<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
							<td width="50"><P><? echo $row[csf('stitch_length')]; ?></P></td>
							<td width="60"><P><? echo $row[csf('gsm')]; ?></P></td>
							<td width="100"><P>
								<?
								$color_arr=array_unique(explode(",",$row[csf('color_id')]));
								$all_color="";
								foreach($color_arr as $id)
								{
									$all_color.=$color_library[$id].",";
								}
								$all_color=chop($all_color," , ");
								echo $all_color;
								?></P>
							</td>
							<td width="90" align="right" title="PO ID:<? echo $row[csf('id')].', Feb Detr Id:'.$row[csf('febric_description_id')]; ?>"><? echo number_format($req_qty,2,'.',''); ?></td>
							<td width="50" align="right"><? echo number_format($prev_roll,2,'.',''); ?></td>
							<td width="100" align="right"><? echo number_format($prev_qty,2,'.',''); ?></td>
							<?
							$row_tot_roll=0;
							$row_tot_qnty=0;
							foreach($shift_name as $key=>$val)
							{
								$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
								$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];

								$row_tot_roll+=$row[csf('roll'.strtolower($val))];
								$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))];
								?>
								<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
								<td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); ?></td>
								<?
							}
							?>
							<td width="50" align="right"><? echo $row[csf('rollnoshift')]; ?></td>
							<td width="100" align="right"><? echo number_format($row[csf('qntynoshift')],2); ?></td>

							<td width="50" align="right"><? $today_production_roll=$row_tot_roll+$row[csf('rollnoshift')]; echo $today_production_roll; ?></td>
							<td width="100" align="right"><? $today_production_qty=$row_tot_qnty+$row[csf('qntynoshift')]; echo number_format($today_production_qty,2,'.',''); ?></td>

							<?
							$tot_production_roll=$prev_roll+$row_tot_roll+$row[csf('rollnoshift')];
							$tot_production_qty=$prev_qty+$row_tot_qnty+$row[csf('qntynoshift')];
							?>
							<td width="50" align="right"><? echo $tot_production_roll; ?></td>
							<td width="100" align="right"><? echo number_format($tot_production_qty,2); ?></td>

							<td width="100" align="right"><? $yet_prod=$req_qty-$tot_production_qty; echo number_format($yet_prod,2); ?></td>
							<td width="70" align="right" title="Exchan: <? echo $curr_value[$row[csf('job_no')]].' * Precost Rate:'.$avg_rate; ?>"><? echo number_format($exchange_rate,4); ?></td>
							<td width="100" align="right"><? $today_revenue=$today_production_qty*$exchange_rate; echo number_format($today_revenue,2); ?></td>
							<td width="100" align="right"><? $tot_revenue=$tot_production_qty*$exchange_rate; echo number_format($tot_revenue,2); ?></td>
							<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
						</tr>
						<?
						$total_req_qty+=$req_qty;
						$total_prev_roll+=$prev_roll;
						$total_prev_qty+=$prev_qty;
						$total_noshift_roll+=$row[csf('rollnoshift')];
						$total_noshift_qty+=$row[csf('qntynoshift')];
						$total_today_production_roll+=$today_production_roll;
						$total_today_production_qty+=$today_production_qty;
						$total_production_roll+=$tot_production_roll;
						$total_production_qty+=$tot_production_qty;
						$total_yet_production+=$yet_prod;
						$total_today_revenue+=$today_revenue;
						$total_revenue+=$tot_revenue;
						$i++;
					}
					?>
				</table>
			</div>
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
				<tr>
					<td align="right" width="30">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="70">&nbsp;</td>
					<td align="right" width="100">&nbsp;</td>
					<td align="right" width="110">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="150">&nbsp;</td>
					<td align="right" width="50">&nbsp;</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="100"><strong>Total</strong></td>
					<td align="right" width="90"><? echo number_format($total_req_qty,2); ?></td>
					<td align="right" width="50"><? echo number_format($total_prev_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_prev_qty,2); ?></td>
					<?
					foreach($shift_name as $key=>$val)
					{
						?>
						<td align="right" width="50"><? echo number_format($tot_roll[$key]['roll'],2,'.',''); ?></td>
						<td align="right" width="100"><? echo number_format($tot_roll[$key]['qty'],2,'.',''); ?></td>
						<?
					}
					?>
					<td align="right" width="50"><? echo number_format($total_noshift_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_noshift_qty,2); ?></td>
					<td align="right" width="50"><? echo number_format($total_today_production_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_today_production_qty,2); ?></td>
					<td align="right" width="50"><? echo number_format($total_production_roll,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_production_qty,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_yet_production,2); ?></td>
					<td align="right" width="70">&nbsp;</td>
					<td align="right" width="100"><? echo number_format($total_today_revenue,2); ?></td>
					<td align="right" width="100"><? echo number_format($total_revenue,2); ?></td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}
	if($cbo_type==2 || $cbo_type==0) // SubCon
	{
		$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
		$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");

		if($db_type==0)
		{
			$year_sub_field="YEAR(a.insert_date)";
			if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
		}
		else if($db_type==2)
		{
			$year_sub_field="to_char(a.insert_date,'YYYY')";
			if($cbo_year!=0) $job_year_sub_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";
		}
		else $year_sub_field="";

		if($db_type==0)
		{
			$select_color=", d.color_id as color_id";
			$group_color=", d.color_id";
		}
		else if($db_type==2)
		{
			$select_color=", nvl(d.color_id,0) as color_id";
			$group_color=", nvl(d.color_id,0)";
		}

		$req_qty_arr=array();
		$sql_req="select order_id, item_id, sum(qnty) as req_qty, avg(rate) as rate from  subcon_ord_breakdown group by order_id, item_id";
		$sql_req_result=sql_select( $sql_req);
		foreach($sql_req_result as $row)
		{
			$req_qty_arr[$row[csf('order_id')]][$row[csf('item_id')]]['req_qty']=$row[csf('req_qty')];
			$req_qty_arr[$row[csf('order_id')]][$row[csf('item_id')]]['rate']=$row[csf('rate')];
		}

		if($from_date!="" && $to_date!="") $date_con_sub=" and c.product_date between '$from_date' and '$to_date'"; else $date_con_sub="";
		$prev_produ_arr=array();
		$sql_prev="select b.order_id, b.cons_comp_id, b.gsm, b.dia_width, sum(b.product_qnty) as prev_qty, sum(b.no_of_roll) as prev_roll from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.product_date<'".$from_date."'  group by b.order_id, b.cons_comp_id, b.gsm, b.dia_width";
		$sql_prev_result=sql_select( $sql_prev);
		foreach($sql_prev_result as $row)
		{
			$prev_produ_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_qty']=$row[csf('prev_qty')];
			$prev_produ_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_roll']=$row[csf('prev_roll')];
		}
		if($db_type==0)
		{
			$job_ord_cond="and d.order_id=b.id";
		}
		else if ($db_type==2)
		{
			$job_ord_cond="and d.job_no=b.job_no_mst";
		}
		$sql_inhouse_sub="select a.job_no_prefix_num, a.party_id, $year_sub_field as year, d.order_id as id, b.order_no, b.cust_style_ref, d.cons_comp_id, d.gsm, d.dia_width, d.yarn_lot, d.stitch_len $select_color, sum(case when d.shift=0 then d.product_qnty else 0 end ) as qntynoshift, sum(case when d.shift=0 then d.no_of_roll end ) as rollnoshift";
		foreach($shift_name as $key=>$val)
		{
			$sql_inhouse_sub.=", sum(case when d.shift=$key then d.no_of_roll end ) as roll".strtolower($val)."
			, sum(case when d.shift=$key then d.product_qnty else 0 end ) as qntyshift".strtolower($val);
		}
		$sql_inhouse_sub.="
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d where c.company_id in($cbo_company) and c.company_id=$cbo_working_company_id and a.subcon_job=b.job_no_mst and c.id=d.mst_id $job_ord_cond and c.product_type=2 $job_year_sub_cond $date_con_sub group by a.job_no_prefix_num, a.party_id, a.insert_date, d.order_id, b.order_no, b.cust_style_ref, d.cons_comp_id, d.gsm, d.dia_width, d.yarn_lot, d.stitch_len, d.color_id order by a.job_no_prefix_num DESC ";

		$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub); $k=1; $tot_roll_sub=array();
		if(count($nameArray_inhouse_subcon)>0)
		{
			?>
			<br>
			<div>
				<div align="left" style="background-color:#E1E1E1; color:#000; width:350px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Sub-Contract Order Knitting Production</i></u></strong></div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th width="60" rowspan="2">Job No</th>
							<th width="60" rowspan="2">Year</th>
							<th width="70" rowspan="2">Party</th>
							<th width="100" rowspan="2">Cust Style</th>
							<th width="110" rowspan="2">Order No</th>
							<th width="60" rowspan="2">Lot No</th>
							<th width="150" rowspan="2">Fabric Type</th>
							<th width="50" rowspan="2">Stitch</th>
							<th width="60" rowspan="2">Fin GSM</th>
							<th width="100" rowspan="2">Fabric Color</th>
							<th width="90" rowspan="2">Req. Qty.</th>
							<th width="150" colspan="2">Prev. Production</th>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="150" colspan="2"><? echo $val; ?></th>
								<?
							}
							?>
							<th width="150" colspan="2">No Shift</th>
							<th width="150" colspan="2">Today Production</th>
							<th width="150" colspan="2">Total Production</th>
							<th width="100" rowspan="2">Yet To Production</th>
							<th width="70" rowspan="2">Rate</th>
							<th width="100" rowspan="2">Today Revenue</th>
							<th width="100" rowspan="2">Total Revenue</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<?
							foreach($shift_name as $val)
							{
								?>
								<th width="50">Roll</th>
								<th width="100">Qnty</th>
								<?
							}
							?>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
							<th width="50">Roll</th>
							<th width="100">Qnty</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
						<?
						foreach($nameArray_inhouse_subcon as $row)
						{
							if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$req_qty=$req_qty_arr[$row[csf('id')]][$row[csf('cons_comp_id')]]['req_qty'];
							$avg_rate=$req_qty_arr[$row[csf('id')]][$row[csf('cons_comp_id')]]['rate'];
							$prev_qty=$prev_produ_arr[$row[csf('id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_qty'];
							$prev_roll=$prev_produ_arr[$row[csf('id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_roll'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trw_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trw_<? echo $k; ?>">
								<td width="30"><? echo $k; ?></td>
								<td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
								<td width="60"><? echo $row[csf('year')]; ?></td>
								<td width="70"><p><? echo $buyer_library[$row[csf('party_id')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
								<td width="110"><p><? echo $row[csf('order_no')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('yarn_lot')]; ?></p></td>
								<td width="150"><p><? echo $const_comp_arr[$row[csf('cons_comp_id')]]; ?></p></td>
								<td width="50"><P><? echo $row[csf('stitch_len')]; ?></P></td>
								<td width="60"><P><? echo $row[csf('gsm')]; ?></P></td>
								<td width="100"><P>
									<?
									$color_arr=array_unique(explode(",",$row[csf('color_id')]));
									$all_color="";
									foreach($color_arr as $id)
									{
										$all_color.=$color_library[$id].",";
									}
									$all_color=chop($all_color," , ");
									echo $all_color;
									?></P>
								</td>
								<td width="90" align="right"><? echo number_format($req_qty,2,'.',''); ?></td>
								<td width="50" align="right"><? echo number_format($prev_roll,2,'.',''); ?></td>
								<td width="100" align="right"><? echo number_format($prev_qty,2,'.',''); ?></td>
								<?
								$row_tot_roll=0;
								$row_tot_qnty=0;
								foreach($shift_name as $key=>$name)
								{
									$tot_roll_sub[$key]['roll']+=$row[csf('roll'.strtolower($name))];
									$tot_roll_sub[$key]['qty']+=$row[csf('qntyshift'.strtolower($name))];

									$row_tot_roll+=$row[csf('roll'.strtolower($name))];
									$row_tot_qnty+=$row[csf('qntyshift'.strtolower($name))];
									?>
									<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($name))]; ?></td>
									<td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($name))],2); ?></td>
									<?
								}
								?>
								<td width="50" align="right"><? echo $row[csf('rollnoshift')]; ?></td>
								<td width="100" align="right"><? echo number_format($row[csf('qntynoshift')],2); ?></td>

								<td width="50" align="right"><? $today_production_roll=$row_tot_roll+$row[csf('rollnoshift')]; echo $today_production_roll; ?></td>
								<td width="100" align="right"><? $today_production_qty=$row_tot_qnty+$row[csf('qntynoshift')]; echo number_format($today_production_qty,2,'.',''); ?></td>

								<?
								$tot_production_roll=$prev_roll+$row_tot_roll+$row[csf('rollnoshift')];
								$tot_production_qty=$prev_qty+$row_tot_qnty+$row[csf('qntynoshift')];
								?>
								<td width="50" align="right"><? echo $tot_production_roll; ?></td>
								<td width="100" align="right"><? echo number_format($tot_production_qty,2); ?></td>

								<td width="100" align="right"><? $yet_prod=$req_qty-$tot_production_qty; echo number_format($yet_prod,2); ?></td>
								<td width="70" align="right"><? echo number_format($avg_rate,4); ?></td>
								<td width="100" align="right"><? $today_revenue=$today_production_qty*$avg_rate; echo number_format($today_revenue,2); ?></td>
								<td width="100" align="right"><? $tot_revenue=$tot_production_qty*$avg_rate; echo number_format($tot_revenue,2); ?></td>
								<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
							</tr>
							<?
							$sub_total_req_qty+=$req_qty;
							$sub_total_prev_roll+=$prev_roll;
							$sub_total_prev_qty+=$prev_qty;
							$sub_total_noshift_roll+=$row[csf('rollnoshift')];
							$sub_total_noshift_qty+=$row[csf('qntynoshift')];
							$sub_total_today_production_roll+=$today_production_roll;
							$sub_total_today_production_qty+=$today_production_qty;
							$sub_total_production_roll+=$tot_production_roll;
							$sub_total_production_qty+=$tot_production_qty;
							$sub_total_yet_production+=$yet_prod;
							$sub_total_today_revenue+=$today_revenue;
							$sub_total_revenue+=$tot_revenue;
							$k++;
						}
						?>
					</table>
				</div>
				<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
					<tr>
						<td align="right" width="30">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="100">&nbsp;</td>
						<td align="right" width="110">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="150">&nbsp;</td>
						<td align="right" width="50">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="100"><strong>Total</strong></td>
						<td align="right" width="90"><? echo number_format($sub_total_req_qty,2); ?></td>
						<td align="right" width="50"><? echo number_format($sub_total_prev_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_prev_qty,2); ?></td>
						<?
						foreach($shift_name as $key=>$val)
						{
							?>
							<td align="right" width="50"><? echo number_format($tot_roll_sub[$key]['roll'],2,'.',''); ?></td>
							<td align="right" width="100"><? echo number_format($tot_roll_sub[$key]['qty'],2,'.',''); ?></td>
							<?
						}
						?>
						<td align="right" width="50"><? echo number_format($sub_total_noshift_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_noshift_qty,2); ?></td>
						<td align="right" width="50"><? echo number_format($sub_total_today_production_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_today_production_qty,2); ?></td>
						<td align="right" width="50"><? echo number_format($sub_total_production_roll,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_production_qty,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_yet_production,2); ?></td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="100"><? echo number_format($sub_total_today_revenue,2); ?></td>
						<td align="right" width="100"><? echo number_format($sub_total_revenue,2); ?></td>
						<td align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
			<?
		}
	}
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";

	disconnect($con);
	exit();
}

if($action=="report_generate_construction_wise") // Shafiq
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$from_date=$txt_date_from;
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);

	if($cbo_company==0) $company_name_cond=""; else $company_name_cond=" and c.company_id in($cbo_company)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and c.knitting_company=$cbo_working_company";
	if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";

	if(trim($cbo_year)!=0) $year_cond=" $year_field_by=$cbo_year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$date_con="and C.RECEIVE_DATE BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$date_con="and C.RECEIVE_DATE BETWEEN '$date_from' AND '$date_to'";
		}
	}
	//============================ creating date range =======================
	// $dateRange = new DatePeriod(new DateTime($txt_date_from),new DateInterval('P1D'),new DateTime($txt_date_to));
	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y')
	{
	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {

	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}
	$dateRange = get_date_range($txt_date_from,$txt_date_to);
	// echo print_r($dateRange);die();
	// ==========================================================================
	$construction_arr=array();
	$sql_deter="SELECT a.id, a.construction from lib_yarn_count_determina_mst a where a.status_active=1 and a.is_deleted=0";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		}
	}

	//================================== MAIN QUERY ===============================
	$sql = "SELECT a.id as JOB_ID,a.job_no as JOB_NO,d.febric_description_id as DTR_ID, to_char(c.receive_date,'DD-Mon-YYYY') AS DATES, SUM(e.quantity) AS QNTY from wo_po_details_master a, wo_po_break_down b, inv_receive_master c, pro_grey_prod_entry_dtls d, order_wise_pro_details e where  a.job_no=b.job_no_mst $company_name_cond $company_working_cond and c.id=d.mst_id and d.id=e.dtls_id and e.po_breakdown_id=b.id and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_con $year_cond group by a.id,a.job_no,D.FEBRIC_DESCRIPTION_ID,C.RECEIVE_DATE order by C.RECEIVE_DATE";
	// echo $sql;
	$sqlRes = sql_select($sql);
	$dataArray = array();
	$qntyArray = array();
	$poWiseQntyArray = array();
	$jobIdArray = array();
	foreach ($sqlRes as $val)
	{
		$dataArray[$construction_arr[$val['DTR_ID']]]['qty'] += $val['QNTY'];
		$qntyArray[$construction_arr[$val['DTR_ID']]][$val['DATES']]['qty'] += $val['QNTY'];
		$poWiseQntyArray[$construction_arr[$val['DTR_ID']]][$val['JOB_NO']][$val['DATES']]['qty'] += $val['QNTY'];
		$jobIdArray[$val['JOB_ID']] = $val['JOB_ID'];
	}
	$jobIds = implode(",", $jobIdArray);
	// echo "<pre>"; print_r($qntyArray);die();

	// ============================== getting rate from budget =================================
	$sqlRate = "SELECT a.JOB_NO,a.lib_yarn_count_deter_id as DETER_ID, b.charge_unit as RATE from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b where b.cons_process=1 and a.id=b.fabric_description and a.job_id in($jobIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $sqlRate;
	$sqlRateRes = sql_select($sqlRate);
	$rateArray = array();
	foreach ($sqlRateRes as $val)
	{
		$rateArray[$val['JOB_NO']][$construction_arr[$val['DETER_ID']]] = $val['RATE'];
	}
	//============================= calculate value ============================
	foreach ($poWiseQntyArray as $dtr_id => $dtr_data)
	{
		foreach ($dtr_data as $job_no => $job_data)
		{
			foreach ($job_data as $date => $val)
			{
				$rate = $rateArray[$job_no][$dtr_id];
				$qntyArray[$dtr_id][$date]['value'] += $val['qty']*$rate;
			}
		}
	}
	// echo "<pre>"; print_r($construction_arr);die();
	$tbl_width=380+count($dateRange)*70;
	// $col_span=25+count($shift_name)*2;
	ob_start();
	?>
	<fieldset style="width:<? echo $tbl_width+20; ?>px;margin: 0 auto;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td colspan="<? echo count($dateRange)+4;?>" align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:18px"><? echo "Daily Knitting Production Report"; ?></td>
			</tr>
			<tr>
				<td colspan="<? echo count($dateRange)+4;?>" align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company)]; ?></td>
			</tr>
			<tr>
				<td colspan="<? echo count($dateRange)+4;?>" align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:12px" ><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
			</tr>
		</table>

		<div id="scroll_body" class="tableFixHead" style="max-height: 250px;overflow-y: auto;width: <? echo $tbl_width+20; ?>px;">
			<table cellpadding="0" cellspacing="0" border="1" width="<? echo $tbl_width; ?>" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="150">Fabric Type/Construction</th>
						<th width="60">Qty/Value</th>
						<?
							foreach ($dateRange as $date_key => $date_val)
							{
								?>
								<th width="60"><? echo date('d-M',strtotime($date_val)); ?></th>
								<?
							}
						?>
						<th width="80">Total</th>
					</tr>
				</thead>
				<tbody>
					<?
					$sl = 1;
					$i = 1;
					$grndTotalArray = array();
					$grndTotalQty = 0;
					$grndTotalVal = 0;
					foreach ($dataArray as $dtr_id => $dtr_data)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
						?>

						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
							<td valign="middle" rowspan="2"><? echo $sl;?></td>
							<td valign="middle" title="<?echo $dtr_id;?>" rowspan="2"><? echo $dtr_id;?></td>
							<td>Qty</td>
							<?
							$totQnty = 0;
							foreach ($dateRange as $date_key => $date_val)
							{
								?>
								<td align="right" width="60"><? echo number_format($qntyArray[$dtr_id][$date_val]['qty'],0); ?></td>
								<?
								$totQnty += $qntyArray[$dtr_id][$date_val]['qty'];
								$grndTotalArray[$date_val]['qty'] += $qntyArray[$dtr_id][$date_val]['qty'];
							}
							$i++; // here increment for 2nd row
							?>
							<td align="right"><? echo number_format($totQnty,0); ?></td>
						</tr>
						<tr onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
							<td>Value</td>
							<?
							$totVal = 0;
							foreach ($dateRange as $date_key => $date_val)
							{
								?>
								<td align="right" width="60"><? echo number_format($qntyArray[$dtr_id][$date_val]['value'],2); ?></td>
								<?
								$totVal += $qntyArray[$dtr_id][$date_val]['value'];
								$grndTotalArray[$date_val]['value'] += $qntyArray[$dtr_id][$date_val]['value'];
							}
							?>
							<td align="right"><? echo number_format($totVal,2); ?></td>
						</tr>
						<?
						$sl++;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th rowspan="2"></th>
						<th rowspan="2">Total</th>
						<th>Qnty</th>
						<?
						foreach ($dateRange as $date_key => $date_val)
						{
							?>
							<th width="60"><? echo number_format($grndTotalArray[$date_val]['qty'],0); ?></th>
							<?
							$grndTotalQty += $grndTotalArray[$date_val]['qty'];
						}
						?>
						<th><? echo number_format($grndTotalQty,0); ?></th>
					</tr>
					<tr>
						<th>Value</th>
						<?
						foreach ($dateRange as $date_key => $date_val)
						{
							?>
							<th width="60"><? echo number_format($grndTotalArray[$date_val]['value'],2); ?></th>
							<?
							$grndTotalVal += $grndTotalArray[$date_val]['value'];
						}

						?>
						<th><? echo number_format($grndTotalVal,2); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";

	disconnect($con);
	exit();
}

if($action=="delivery_challan_print")
{
	echo load_html_head_contents("Delivery Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = $datas[0];
	$source_ids = $datas[1];
	$company = $datas[2];
	$from_date = $datas[3];
	$to_date = $datas[4];
	$in_out_data=explode(',',$datas[1]);
	$company_details=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');


	$machine_details=array();
	$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	$po_array=array();
	$po_data=sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by b.po_number, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id");
	foreach($po_data as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
		$po_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$knit_plan_arr=array();
	$plan_data=sql_select("select id, color_range, stitch_length from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
		$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')];
	}

	?>
	<div style="width:1360px;">
		<table width="1350" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="17" align="center" style="font-size:x-large"><strong><? echo $company_details[$company]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="17" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></center></td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></center></td>
			</tr>
			<tr >
				<td colspan="17"  style="font-size:14px"><strong><? echo "Date Range :"." ". $from_date." "."To"." ".$to_date; ?></strong></center></td>
			</tr>
		</table>
	</div>
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="60" >Job No</th>
					<th width="90" >Order No</th>
					<th width="60" >Buyer</th>
					<th width="50" >Prod. ID</th>
					<th width="60" >M/C No</th>
					<th width="60" >Req. No</th>
					<th width="90" >Booking No/ Prog. No</th>
					<th width="60" >Yarn Count</th>
					<th width="70" >Yarn Brand</th>
					<th width="70" >Lot No</th>
					<th width="100" >Color</th>
					<th width="" >Fabric Type</th>
					<th width="50" >Stitch</th>
					<th width="50" >Fin GSM</th>
					<th width="50" >Fab. Dia</th>
					<th width="50" >M/C Dia</th>
					<th width="50" >Total Roll</th>
					<th width="70" >Total Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:1350px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >

				<?
				$machine_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no" );
				$reqsn_details=return_library_array( "select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id", "knit_id", "requisition_no"  );

				if($db_type==2) $date_cond="'".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
				if($db_type==0) $date_cond="'".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
				if($in_out_data[0]==1)
				{
					$sql="select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id,sum(case when c.entry_form=2 then b.no_of_roll else 0 end)  as roll_no, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift
					from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
					where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id
					order by a.receive_date";
				}
				else if ($in_out_data[0]==3)
				{
					$sql="select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift  from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id
					order by b.floor_id,a.receive_date";
				}
				else
				{
					$sql="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id,sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift, sum(case when a.entry_form=2 then b.grey_receive_qnty else 0 end)  as outqntyshift
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where  a.item_category=13 and a.id=b.mst_id and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=2
					and a.booking_without_order=1
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id
					order by a.receive_date";
				}

				$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;
				foreach($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}

					$reqsn_no=""; $stich_length=""; $color="";
					if($row[csf('receive_basis')]==2)
					{
						$reqsn_no=$reqsn_details[$row[csf('booking_id')]];
						$stich_length=$knit_plan_arr[$row[csf('booking_id')]]['sl'];
						$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30"><div style="word-wrap:break-word; width:30px;"><? echo $i; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?></div></td>
						<td width="90"><div style="word-wrap:break-word; width:90px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['no']; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
						<td width="60" align="center"><div style="word-wrap:break-word; width:60px;"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $reqsn_no; ?></div></td>
						<td width="90"><div style="word-wrap:break-word; width:90px;"><? echo $row[csf('booking_no')]; ?></div></td>
						<td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $count; ?></div></td>
						<td width="70"><div style="word-wrap:break-word; width:70px;"><? echo $brand_details[$row[csf('brand_id')]]; ?></div></td>
						<td width="70"><div style="word-wrap:break-word; width:70px;"><? echo $row[csf('yarn_lot')]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px;"><? echo $color; ?></div></td>
						<td width=""><div style="word-wrap:break-word; width:210px;"><? echo $composition_arr[$row[csf('febric_description_id')]];; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $stich_length; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('gsm')]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('width')]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></div></td>
						<td width="50" align="right"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('roll_no')]; $tot_roll+=$row[csf('roll_no')]; ?>&nbsp;</div></td>
						<td width="70" align="right"><div style="word-wrap:break-word; width:70px;"><? echo $row[csf('outqntyshift')]; $tot_qty+=$row[csf('outqntyshift')]; ?>&nbsp;</div></td>
					</tr>
					<?
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="17" ><strong>Total:</strong></td>
					<td align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</td>
					<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="left"><b>Remarks: </b></td>
					<td colspan="17" ><? //echo number_to_words($tot_qty); ?>&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(44, $company, "1340px");
			?>
		</div>
	</div>
	<?
	exit();
}
?>
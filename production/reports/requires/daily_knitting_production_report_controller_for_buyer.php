
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
	// $txt_style_ref=str_replace("'","",$txt_style_ref);

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

	// if($knitting_source==0)
	// 	$knit_source_cond="";
	// else
	// 	$knit_source_cond=" and a.knitting_source=$knitting_source";

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
	
	// if($txt_style_ref==0)
	// 	$txt_style_ref_cond="";
	// else
	// 	$txt_style_ref_cond=" and .....=$txt_style_ref";
		

	if($report_type==1) // Prod. Wise
	{
		$tbl_width=1815+count($shift_name)*205;

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

				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+440; ?>" class="rpt_table" id="table_head" >
					<thead>
						<tr>
							<th width="40" rowspan="2" id="chk_hide"></th>
							<th width="30" rowspan="2">SL</th>
							
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
				<!--<th width='55' rowspan='2'>Knitting Party</th>
				<th width='60' rowspan='2'>M/C No</th>-->
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
				<!-- <th width='80' rowspan='2'>Yarn Count</th> -->
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
								

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" valign="middle" id="chk_hide_dtls">
									&nbsp;
									</td>
									<td width="30"><? echo $i; ?></td>
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
									$h=0;
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
										$h++;
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
									<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]."hi".$h; ?></td>

									<td width="50" align="right" id="nopcs_<? echo $i; ?>"><? echo number_format($row[csf('pcsnoshift')],2); ?></td>
									<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
									<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
									<td width="50" align="right" id="pcs_<? echo $i; ?>"><? echo number_format($row_tot_pcs+$row[csf('pcsnoshift')],2,'.',''); ?></td>
									<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','');
									$tot_in_Qnty=$row_tot_qnty+$row[csf('qntynoshift')]; ?></td>

									<td width="100"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
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
							
							
							<?
							
						}
						// echo "<pre>";print_r($machineSamarryDataArr);die;
						// *************** Outbound-Subcontract Production *********************
						if(count($nameArray_subcontract)>0) // Outbound Subcon
						{
							
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
									&nbsp;
									</td>

									<td width="30"><? echo $i; ?></td>
									
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
									$h=0;
									foreach($shift_name as $key=>$val)
									{
										?>
										<td width="50" align="right"></td>
										<td width="50" align="right"></td>
										<td width="100" align="right"></td>
										<?
										$h++;
										$html.="<td width='50' align='right' ></td>
										<td width='50' align='right' ></td>
										<td width='100' align='right' ></td>";
									}
									?>
									<td width="50" align="right"><? echo $row_tot_roll."hi".$h; ?></td>
									<td width="50" align="right"><? echo number_format($row[csf('outpcsshift')],2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
									<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
									<td width="50" align="right"><? echo number_format($row[csf('outpcsshift')],2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
									

									<td width="100" ><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
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
							
							<?
							
						}
						 //echo "<pre>";print_r($machineSamarryDataArr);die;
						// **************************** Outbound-Subcontract Receive ************************
						if(count($nameArray_service_receive)>0) // service_receive
						{
							?>
							

							<?
							
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
									&nbsp;
									</td>
									<td width="30"><? echo $i; ?>

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
											$h=0;
											foreach($shift_name as $key=>$val)
											{
												?>
												<td width="50" align="right"><? ?></td>
												<td width="50" align="right"><?  ?></td>
												<td width="100" align="right"><? ?></td>

												<?
												$html.="<td width='50' align='right' ></td>
												<td width='50' align='right' ></td>
												<td width='100' align='right' ></td>";
												$h++;
											}
											?>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]."hi".$h; ?></td>
											<td width="50" align="right">0.00</td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											
											<td width="50"  align="right">&nbsp;</td>
											<td  width="100" align="right">&nbsp;</td>

											

											<td width="100"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
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
									
									<?
									
						}
						 //echo "<pre>";print_r($machineSamarryDataArr);die;
						unset($floor_array); $total_qty_noshift=0;
						unset($floor_tot_roll); unset($noshift_total); unset($pcsnoshift_total); unset($source_tot_roll);  $source_tot_qnty=0;
						$j=1;

						unset($floor_array); $total_qty_noshift=0;
						unset($floor_tot_roll); unset($noshift_total); unset($pcsnoshift_total); unset($source_tot_roll);  $source_tot_qnty=0;
						$j=0;

						// =====Grand Total tfoot below=========
							?>
							<tfoot>
								<th></th>

								<th colspan="22" align="right">Grand Total</th>
								<?
								$html.="<tfoot>
								<th colspan='21' align='right'>Grand Total</th>";
								foreach($shift_name as $key=>$val)
								{
									$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
									$source_tot_pcs_row+=$source_tot_roll[$key]['pcs'];
									?>
									<th id="" align="right"><? echo number_format($tot_roll[$key]['roll'],2,'.',''); ?></th>
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

		
		}
		?>
		<br>
		<?
		

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



?>
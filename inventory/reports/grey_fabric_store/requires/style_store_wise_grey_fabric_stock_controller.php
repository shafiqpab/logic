<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	if($action=="load_drop_down_buyer")
	{
		$data=explode("_",$data);
		if($data[1]==1) $party="1,3,21,90"; else $party="80";
		echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
		exit();
	}

	if ($action == "load_drop_down_store")
	{
		echo create_drop_down("cbo_store_name", 180, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in(13)  order by a.store_name", "id,store_name","id,store_name", 0, "", 0, "",$disable);
		exit();
	}

	if ($action == "eval_multi_select") {
		echo "set_multiselect('cbo_store_name','0','0','','0');\n";
		exit();
	}
if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=6 and report_id=200 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
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
										<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'style_store_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'create_booking_no_search_list_view', 'search_div', 'style_store_wise_grey_fabric_stock_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
	//if (trim($data[2])!="") $booking_no=" and a.booking_no like '%".trim($data[2])."%'"; else $booking_no='';
	if (trim($data[2])!="") $booking_no=" and a.booking_no_prefix_num = trim($data[2])"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$year_id=$data[5];
	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(a.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
		}
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$arr=array (1=>$booking_type,3=>$comp,4=>$buyer_arr,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
	$sql= "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved,a.booking_type,a.is_short from wo_booking_mst a where $company $buyer $booking_no $booking_date $year_cond and a.booking_type in (1,4) and a.status_active=1 and a.is_deleted=0 ";
	//and a.is_short=2

	//echo  create_list_view("list_view", "Booking No,Booking Type,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,50,80,80,80,90,120,80,80,60,50","960","320",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,booking_type,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_type,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,0,3,0,0,0,0,0,0,0,0','','');
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="110">Booking No</th>
			<th width="60">Booking Type</th>
			<th width="100">Booking Date</th>
			<th width="90">Company</th>
			<th width="100">Buyer</th>
			<th width="90">Job No</th>
			<th width="110">Fabric Nature</th>
			<th width="110">Fabric Source</th>
			<th width="60">Supplier</th>
			<th width="50">Approved</th>
			<th>Is-Ready</th>
			
		</thead>
	</table>
	<div style="width:990px; max-height:265px; overflow-y:scroll; float: left;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="list_view" align="left">
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('booking_type')]==4)
				{
					$booking_type = "Sample";
				}
				else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
				{
					$booking_type = "Main";
				}
				else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
				{
					$booking_type = "Short";
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('booking_no')]; ?>')" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
					</td>
					<td width="110" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="60" align="center"><? echo $booking_type; ?></td>
					<td width="100" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="90" align="center"><? echo $comp[$row[csf('company_id')]]; ?></td>
					<td width="100" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><p><? echo$item_category[$row[csf('item_category')]]; ?></p></td>
					<td width="110" align="center"><p><? echo $fabric_source[$row[csf('fabric_source')]]; ?></p></td>
					<td width="60" align="center"><p><? echo $suplier[$row[csf('supplier_id')]]; ?></p></td>
					<td width="50" align="center"><p><? echo $approved[$row[csf('is_approved')]]; ?></p></td>
					<td align="center"><p><? echo $is_ready[$row[csf('ready_to_approved')]]; ?></p></td>
					
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	<?
	exit();
}

if ($action == "sales_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
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

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
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
			$('#hide_sales_id').val( id );
			$('#hide_sales_no').val( ddd );
		}

	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:940px;">
					<table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Search By</th>
							<th>Search</th>
							<th>Booking Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="text" name="hide_sales_id" id="hide_sales_id" value="" />
							<input type="text" name="hide_sales_no" id="hide_sales_no" value="" />
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
									$search_by_arr=array(1=>"Sales Order No",2=>"Sales Booking No",3=>"Style Ref.");
									echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
								</td>

								<td align="center">
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_search_by').value+'**'+'<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id;?>', 'create_sales_no_search_list_view', 'search_div', 'style_store_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action == "create_sales_no_search_list_view")
{
	$data=explode('**',$data);

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$buyer_id=$data[3];
	$cbo_year=$data[6];


	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $booking_date  = "and booking_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $booking_date  = "and booking_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	if($db_type==0) 
	{
		$year_field="YEAR(insert_date) as year";
		$year_cond = " and YEAR(insert_date) =".$cbo_year;
	}
	else if($db_type==2) 
	{
		$year_field="to_char(insert_date,'YYYY') as year";
		$year_cond = " and to_char(insert_date,'YYYY') =".$cbo_year;
	}
	else 
	{
		$year_field="";
		$year_cond="";
	}

	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id and within_group=2 $search_field_cond $booking_date $year_cond order by id DESC";
	//echo $sql;//die;
	$result = sql_select($sql);

	$arr=array (2=>$yes_no,3=>$buyer_arr,7=>$location_arr);
	echo create_list_view("tbl_list_search", "Sales Order No,Year,Within Group,Buyer Name,Sales Booking No,Booking date,Style Ref. No,Location", "90,60,80,70,120,80,110,100","830","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,within_group,buyer_id,0,0,0,location_id", $arr , "job_no_prefix_num,year,within_group,buyer_id,sales_booking_no,booking_date,style_ref_no,location_id", "",'','0,0,0,0,0,0,0,0','',1);

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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));
	
	$cbo_string_search_type=str_replace("'","",$cbo_string_search_type);
	if($cbo_string_search_type==1)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) = LOWER('".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";
		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no = '$txt_booking_no'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id = $hide_booking_id"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."'"; else $booking_id_cond="";
	}
	// echo $file_cond.'<br>'.$ref_cond.'<br>'.$booking_no_cond.'<br>'.$booking_id_cond.'<br>';

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	//if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	//if($txt_ref_no!="") $ref_cond=" and a.grouping = '".trim($txt_ref_no)."'";
	//if($hide_booking_id!="") $booking_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	//if($txt_booking_no!="") $booking_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c where b.company_name=$cbo_company_id and c.booking_type=1 and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $booking_no_cond 	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no order by a.id";
	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$booking_ref=$row[csf('buyer_name')]."*".$row[csf('job_no')]."*".$row[csf('booking_no')]."*".$row[csf('grouping')];
			$poIds.=$row[csf('id')].",";
			$poIdsArr[$row[csf('id')]]=$row[csf('id')];

			$poArr[$row[csf('id')]]=$booking_ref;
			$po_booking[$row[csf('id')]]=$row[csf('booking_no')];

			$fileRefArr[$booking_ref].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	//Initially delete temporary tables-------------------------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=25");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (25)");
	if($r_id2)
	{
		oci_commit($con);
	}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 1,$poIdsArr, $empty_arr);

	/* $poIds=implode(",",array_unique(explode(",",chop($poIds,','))));
	$poIds_cond_roll=$poIds_cond_trans_roll=$ctct_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	} */

	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr 			= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );

	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}

	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	$main_query="SELECT c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id
	from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	where a.entry_form in(2,22,58) and a.id=e.mst_id and e.id=b.trans_id and b.id=c.dtls_id and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $trans_date  $store_cond_2 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and c.booking_without_order=0
	union all
	select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=1 and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1
	union all
	select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=1 and nvl(c.booking_without_order,0) =0  and a.status_active=1 and b.status_active=1 and c.status_active=1
	union all
	select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=1 and c.booking_without_order=0 and a.status_active=1 and b.status_active=1 and c.status_active=1";
	//echo $main_query;die;
	//$poIds_cond_roll
	$result = sql_select($main_query);

	if(!empty($result))
	{
		foreach ($result as $row)
		{
			if($barcodeArr[$row[csf("barcode_no")]]=="")
			{
				$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

				$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 1, ".$row[csf('barcode_no')].")");
				if($r_id) 
				{
					$r_id=1;
				} 
				else 
				{
					echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 1, ".$row[csf('barcode_no')].")";
					oci_rollback($con);
					die;
				}
			}
		}
	}
	else
	{
		echo "Data Not Found";
		die;
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$barcodeArr = array_filter($barcodeArr);
	if(count($barcodeArr ) >0 )
	{
		/*
			$receive_barcodes = implode(",", $barcodeArr);
			if($db_type==2 && count($barcodeArr)>999)
			{
				$barcode_chunk=array_chunk($barcodeArr,999) ;
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

			$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 $barcode_cond");
		*/
		$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no g where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 and b.barcode_no = g.barcode_no and g.userid= $user_id and g.entry_form=25 and g.type=1");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				if($split_barcode_arr[$val[csf("barcode_no")]]=="")
				{
					$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];

					$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 2, ".$val[csf('barcode_no')].")");
					if($r_id) 
					{
						$r_id=1;
					} 
					else 
					{
						echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 2, ".$val[csf('barcode_no')].")";
						oci_rollback($con);
						die;
					}
				}
			}

			oci_commit($con);

			/* $split_barcodes = implode(",", $split_barcode_arr);
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
			} */

			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b, tmp_barcode_no c where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 and a.barcode_no=c.barcode_no and c.userid=$user_id and c.entry_form=25 and c.type=2");

			/*$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b, tmp_barcode_no d where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 and a.barcode_no = d.barcode_no");*/

			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$production_sql = sql_select("select b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, tmp_barcode_no d where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=25 and d.type=1 order by c.entry_form desc");

		foreach ($production_sql as $row)
		{
			$prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
			$prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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

			if($row[csf('receive_basis')] == 2 )
			{
				$program_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
			}
		}

		$febric_description_arr = array_filter($allDeterArr);
		if(!empty($febric_description_arr))
		{
			/* $ref_febric_description_ids = implode(",", $febric_description_arr);
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
			} */

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

		$all_color_arr = array_filter($allColorArr);
		if(!empty($all_color_arr))
		{
			/* $all_color_ids = implode(",", $all_color_arr);
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
			} */

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 2,$all_color_arr, $empty_arr);

			$colorArr=return_library_array( "select a.id, a.color_name from lib_color a, gbl_temp_engine g where a.status_active=1 and a.id=g.ref_val and g.entry_form=25 and g.ref_from=2 and g.user_id=$user_id", "id", "color_name" );

		}

		if(count($program_id_arr) >0 )
		{

			/* $program_ids = implode(",", $program_id_arr);
			$programCond = $program_id_cond = "";
			if($db_type==2 && count($program_id_arr)>999)
			{
				$program_id_arr_chunk=array_chunk($program_id_arr,999) ;
				foreach($program_id_arr_chunk as $chunk_arr)
				{
					$programCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$program_id_cond.=" and (".chop($programCond,'or ').")";
			}
			else
			{
				$program_id_cond=" and id in($program_ids)";
			} */

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 3,$program_id_arr, $empty_arr);

			$plan_arr=array();
			$plan_data=sql_select("select a.id, a.machine_dia, a.machine_gg from ppl_planning_info_entry_dtls a, gbl_temp_engine g where a.status_active=1 and a.id=g.ref_val and g.entry_form=25 and g.ref_from=3 and g.user_id=$user_id");
			foreach($plan_data as $row)
			{
				$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
			}
			unset($plan_data);
		}

		$yarn_prod_id_arr = array_filter($allYarnProdArr);
		if(count($yarn_prod_id_arr)>0)
		{
			/* $yarn_prod_ids = implode(",", $yarn_prod_id_arr);
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
			} */

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 4,$yarn_prod_id_arr, $empty_arr);

			$yarn_sql=  sql_select("select a.id, a.yarn_type, a.yarn_comp_type1st, a.brand from product_details_master a, gbl_temp_engine g where a.status_active = 1  and a.item_category_id =1 and a.id=g.ref_val and g.entry_form=25 and g.ref_from=4 and g.user_id=$user_id");
			foreach ($yarn_sql as $row)
			{
				$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
				$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
				$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
			}
		}

	}

	foreach ($result as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] == 1)
		{
			$production_company= $company_short_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		else
		{
			$production_company= $supplier_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}

		$machine_dia_gg='';
		if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
		}

		$fabrication = $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["width"]  . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"]."*".$production_company;

		$dataArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication] += $row[csf('qnty')];
		$storeWiseStockArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += $row[csf('qnty')];
		$storeWiseRcvArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += $row[csf('qnty')];
	}

	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty, e.store_id as store_name from pro_roll_details c, inv_grey_fabric_issue_dtls d, inv_transaction e, GBL_TEMP_ENGINE g where c.dtls_id = d.id and c.mst_id = d.mst_id and d.trans_id = e.id and e.transaction_type =2 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $store_cond_2 and c.po_breakdown_id=g.ref_val and g.entry_form=25 and g.user_id=$user_id and g.ref_from=1 and c.booking_without_order =0
		union all
		select a.po_breakdown_id, c.barcode_no, c.qnty, b.from_store as store_name from order_wise_pro_details a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $store_cond_3 and a.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=1 and c.booking_without_order =0
		union all
		select b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, b.from_store as store_name
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
		where a.id = b.mst_id and a.id=c.mst_id and b.id=c.dtls_id $store_cond_3 and b.from_order_id=g.ref_val and g.entry_form=25 and g.user_id=$user_id and g.ref_from=1 and a.entry_form =82 and c.entry_form = 82 and b.status_active =1 and c.status_active =1 and a.status_active =1 and nvl(c.booking_without_order,0) =0 
		group by c.barcode_no, b.from_order_id, b.from_store
		union all
		select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, b.from_store as store_name
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id $store_cond_3 and a.from_order_id=g.ref_val and g.entry_form=25 and g.user_id=$user_id and g.ref_from=1 and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1
		group by c.barcode_no, a.from_order_id, b.from_store ");

		//sql cond 1-->$poIds_cond_roll  2-->$poIds_cond_trans_roll  3--> $ctct_po_cond  4-->$otst_po_cond

	foreach ($iss_qty_sql as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] == 1)
		{
			$production_company= $company_short_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		else
		{
			$production_company= $supplier_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		$machine_dia_gg='';
		if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
		}

		$fabrication = $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["width"]  . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"]."*".$production_company;

		$mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
		if($mother_barcode_no != "")
		{
			if($prodBarcodeData[$mother_barcode_no]["knitting_source"]==1)
			{
				$production_company= $company_short_arr[$prodBarcodeData[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$production_company=$supplier_arr[$prodBarcodeData[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($prodBarcodeData[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$prodBarcodeData[$mother_barcode_no]["booking_id"]];
			}

			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"]  . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$mother_barcode_no]["machine_no_id"]."*".$production_company;
		}

		$storeWiseStockArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_name')]] -= $row[csf('qnty')];
		$storeWiseIssArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_name')]] += $row[csf('qnty')];
	}

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (25)");
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form in (25)");
	oci_commit($con);
	disconnect($con);

	$width = (1266+($num_of_store*110));
	ob_start();
	?>
	<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
		<table width="100%" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" border="1">
			<thead>
				<tr>
					<th width='30'>SL</th>
					<th width='150'>Construction</th>
					<th width='110'>Color</th>
					<th width='110'>Color Range</th>
					<th width='80'>Y. Count</th>
					<th width='80'>Y. Type</th>
					<th width='116'>Y. Composition</th>
					<th width='100'>Brand</th>
					<th width='110'>Yarn Lot</th>
					<th width='110'>MC Dia and Gauge</th>
					<th width='80'>F/Dia</th>
					<th width='110'>S. Length</th>
					<th width='60'>GSM</th>
					<th width='110'>M/C No.</th>
					<th width='110'>Knitting Company</th>
					<th width='80'>Total Stock Qty.</th>
					<?
					foreach ($stores as $store) {
						?>
						<th width="110"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
				</tr>
			</thead>
			<?
			if(!empty($dataArr))
			{
				$i=1;

				foreach ($storeWiseRcvArr as $booking_datas=>$po_row)
				{
					foreach ($po_row as $febric_description=>$row)
					{
						$booking_data = explode("*", $booking_datas);
						$fabrication = explode("*", $febric_description);

						$yarn_counts_arr = explode(",", $fabrication[7]);

						$yarn_counts="";
						foreach ($yarn_counts_arr as $count) {
							$yarn_counts .= $count_arr[$count] . ",";
						}
						$yarn_counts = rtrim($yarn_counts, ", ");

						$color_arr = explode(",", $fabrication[1]);
						$colors="";
						foreach ($color_arr as $color) {
							$colors .= $colorArr[$color] . ",";
						}
						$colors = rtrim($colors, ", ");

						$yarn_id_arr = array_unique(array_filter(explode(",", $fabrication[8])));
						$yarn_brand = $yarn_comp = $yarn_type_name = "";
						foreach ($yarn_id_arr as $yid)
						{
							$yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
							$yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
							$yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
						}

						$yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
						$yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
						$yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));

						$machine_dia_gg = $fabrication[9];
						$machine_name =  $machine_arr[$fabrication[10]];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if (!in_array($booking_datas, $checkBookArr))
						{
							$checkBookArr[$i] = $booking_datas;
							if ($i > 1)
							{
								?>
								<tr>
									<th colspan="15" width="1466" align="right">Job Total:</th>
									<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
									<?
									$store_ref = implode(",",array_unique(explode(",",chop($store_ref,","))));
									foreach ($stores as $store)
									{
										?>
										<th width="110" align="right"><? echo number_format($sub_store_stock[$store_ref][$store[csf("id")]],2,".","");?></th>
										<?
									}
									?>
								</tr>
								<?
								$sub_tot_stock = 0;	$store_ref="";
							}
							?>
							<tr>
								<td colspan="<? echo $num_of_store+16;?>"><b>Buyer: <? echo $buyer_arr[$booking_data[0]].", Job No : ".$booking_data[1].", Reference No : ".$booking_data[3].", Booking No : ".$booking_data[2]; ?></b></td>
							</tr>
							<?
						}
						?>

						<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

							<td width='30' title="book data=<? echo $booking_datas."\n".$febric_description;?>"><? echo $i?></td>
							<td widtd='150' title="<? echo $fabrication[0];?>" ><? echo $constuction_arr[$fabrication[0]];?></td>
							<td widtd='110' title="Color"><? echo $colors;?></td>
							<td widtd='110' title="Color Range"><? echo $color_range[$fabrication[2]];?></td>
							<td widtd='80' title="Count"><? echo $yarn_counts;?></td>
							<td widtd='80' title="Type"><? echo $yarn_type_name;?></td>
							<td widtd='116' title="y count"><? echo $yarn_comp;?></td>

							<td widtd='100' title="Brand"><? echo $yarn_brand;?></td>
							<td widtd='110' title="Yarn Lot"><? echo $fabrication[6];?></td>
							<td widtd='110' title="Mc dia gg"><? echo $machine_dia_gg;?></td>
							<td widtd='80' title="F/Dia"><? echo $fabrication[4];?></td>
							<td widtd='110' title="S.Length"><? echo $fabrication[5];?></td>
							<td widtd='60' title="GSM"><? echo $fabrication[3];?></td>
							<td widtd='110' title="MC No"><? echo $machine_name;?></td>
							<td widtd='110' title="knitting Company"><? echo $fabrication[11];?></td>

							<?
							$stock=0;
							foreach ($stores as $store)
							{
								$stock += $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$sub_tot_stock +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];

								$sub_store_stock[$booking_datas][$store[csf("id")]] +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$store_ref .= $booking_datas.",";

							}
							?>
							<td width="100" align="right"><? echo number_format($stock,2,".",""); ?></td>
							<?
							$stock=$store_stock=0;
							foreach ($stores as $store)
							{
								$store_stock = $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_receive = $storeWiseRcvArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_issue = $storeWiseIssArr[$booking_datas][$febric_description][$store[csf("id")]];
								?>
								<td width="110" align="right" title="<? echo "Store=".$store[csf("id")].'**'.$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","");?>"><? echo number_format($store_stock,2,".","");?></td>
								<?

							}
							?>

						</tr>
						<?

						$i++;
					}
				}
				?>
				<tr>
					<th colspan="15" align="right">Job Total:</th>
					<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
					<?
					foreach ($stores as $store)
					{
						?>
						<th width="110" align="right"><? echo number_format($sub_store_stock[$booking_datas][$store[csf("id")]],2,".","");?></th>
						<?
					}
					?>
				</tr>
				<?
			}
			?>
		</table>
	</fieldset>
	<?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "s";

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

if($action=="report_generate_dtls2")
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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));
	
	$cbo_string_search_type=str_replace("'","",$cbo_string_search_type);
	if($cbo_string_search_type==1)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) = LOWER('".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";
		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no = '$txt_booking_no'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id = $hide_booking_id"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."'"; else $booking_id_cond="";
	}
	// echo $file_cond.'<br>'.$ref_cond.'<br>'.$booking_no_cond.'<br>'.$booking_id_cond.'<br>';

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	//if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	//if($txt_ref_no!="") $ref_cond=" and a.grouping = '".trim($txt_ref_no)."'";

	//if($hide_booking_id!="") $booking_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	//if($txt_booking_no!="") $booking_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $iss_trans_date=""; else $iss_trans_date= " and a.issue_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	if($job_no!="" || $txt_file_no!="" || $txt_ref_no!="" || $txt_order_no!="" || $cbo_shiping_status>0)
	{
		$poIds=''; $booking_mst_ida=''; $tot_rows=0; $fileRefArr=array();
		$sql="SELECT b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id
		from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c 
		where b.company_name=$cbo_company_id and c.booking_type=1 and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $booking_no_cond
		group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id";
		// echo $sql;die;
		$result=sql_select($sql);
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$tot_rows++;
				$booking_ref=$row[csf('buyer_name')]."*".$row[csf('job_no')]."*".$row[csf('booking_no')]."*".$row[csf('grouping')];
				$poIds.=$row[csf('id')].",";
				$booking_mst_ida.=$row[csf('booking_mst_id')].",";
			}
		}
		else
		{
			echo "Data Not Found";die;
		}
		unset($result);
	}

	if($hide_booking_id!="" || $txt_booking_no!="")
	{
		$poArr=array(); $poIds=''; $booking_mst_ida=''; $tot_rows=0; $fileRefArr=array();
		$sql="SELECT b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id 
		from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c 
		where b.company_name=$cbo_company_id and c.booking_type=1 and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $booking_no_cond
		group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id
		union all
		SELECT null as job_no, null as buyer_name, 0 as id, null as po_number, null as grouping, null as file_no, c.booking_no, a.id as booking_mst_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c where a.booking_no=c.booking_no and a.company_id=$cbo_company_id and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $booking_no_cond
		group by c.booking_no, a.id";
		// echo $sql;die;
		$result=sql_select($sql);
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$tot_rows++;
				$booking_ref=$row[csf('buyer_name')]."*".$row[csf('job_no')]."*".$row[csf('booking_no')]."*".$row[csf('grouping')];
				if ($row[csf('id')]!="") 
				{
					$poIds.=$row[csf('id')].",";
				}				
				$booking_mst_ida.=$row[csf('booking_mst_id')].",";
			}
		}
		else
		{
			echo "Data Not Found";die;
		}
		unset($result);
	}
	// echo $poIds;
	if ($poIds!="") 
	{
		$poIds=implode(",",array_unique(explode(",",chop($poIds,','))));
		$poIds_cond=$poIds_cond_trans_roll=$ctct_po_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond_pre=" and (";
			$poIds_cond_suff.=")";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" a.to_order_id in($ids) or ";
				//$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
				//$ctct_po_cond.=" b.from_order_id in($ids) or ";
				//$otst_po_cond.=" a.from_order_id in($ids) or ";
			}

			$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
			//$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
			//$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
			//$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
		}
		else
		{
			$poIds_cond=" and a.to_order_id in($poIds)";
			//$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
			//$ctct_po_cond=" and b.from_order_id in($poIds)";
			//$otst_po_cond=" and a.from_order_id in($poIds)";
		}
	}
	
	// echo $poIds_cond;die;

	$booking_mst_ida=implode(",",array_unique(explode(",",chop($booking_mst_ida,','))));
	$bookingIds_cond=$bookingIds_cond_trans_roll=$smn_trans_booking_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$bookingIds_cond_pre=" and (";
		$bookingIds_cond_suff.=")";
		$bookingIdsArr=array_chunk(explode(",",$booking_mst_ida),999);
		foreach($bookingIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$bookingIds_cond.=" a.booking_id in($ids) or ";
			//$bookingIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$smn_trans_booking_cond.=" a.to_order_id in($ids) or ";
			//$booking_otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$bookingIds_cond=$bookingIds_cond_pre.chop($bookingIds_cond,'or ').$bookingIds_cond_suff;
		//$bookingIds_cond_trans_roll=$bookingIds_cond_pre.chop($bookingIds_cond_trans_roll,'or ').$bookingIds_cond_suff;
		$smn_trans_booking_cond=$bookingIds_cond_pre.chop($smn_trans_booking_cond,'or ').$bookingIds_cond_suff;
		//$booking_otst_po_cond=$bookingIds_cond_pre.chop($booking_otst_po_cond,'or ').$bookingIds_cond_suff;
	}
	else
	{
		$bookingIds_cond=" and a.booking_id in($booking_mst_ida)";
		//$bookingIds_cond_trans_roll=" and a.po_breakdown_id in($booking_mst_ida)";
		$smn_trans_booking_cond=" and a.to_order_id in($booking_mst_ida)";
		//$booking_otst_po_cond=" and a.from_order_id in($booking_mst_ida)";
	}
	// echo $poIds_cond.'='.$bookingIds_cond.'*'.$smn_trans_booking_cond;die;

	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr 			= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );

	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}

	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	$con = connect();
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (4223,4224,4225,4226,4227)");
    oci_commit($con);

    // sample transfer all Criteria to_prod_id is from_prod_id, entry_form 432,80 only for sample booking id, 13,81 transfer in order id 
	$main_query="SELECT a.booking_no, 0 as to_order_id, e.prod_id, e.cons_quantity as recv_qty, 1 as type, e.store_id, c.detarmination_id, c.gsm, c.dia_width
	from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, product_details_master c
	where a.id=e.mst_id and e.id=b.trans_id and e.prod_id=c.id and a.entry_form in(2) and a.receive_basis=1 and a.item_category=13 and c.item_category_id=13 $trans_date $bookingIds_cond $store_cond_2 and a.status_active=1 and b.status_active=1 and e.status_active=1
	union all
	select null as booking_no, a.to_order_id, b.to_prod_id as prod_id, b.transfer_qnty as recv_qty, 2 as type, b.to_store store_id, c.detarmination_id, c.gsm, c.dia_width
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c
	where a.id=b.mst_id and b.to_prod_id=c.id and a.entry_form in(13) and a.item_category=13 and c.item_category_id=13 $transfer_date $poIds_cond $store_cond_1 and a.status_active=1 and b.status_active=1
	union all
	select null as booking_no, a.to_order_id, b.from_prod_id as prod_id, b.transfer_qnty as recv_qty, 2 as type, b.to_store store_id, c.detarmination_id, c.gsm, c.dia_width 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c
	where a.id=b.mst_id and b.from_prod_id=c.id and a.entry_form in(81) and a.item_category=13 and c.item_category_id=13 $transfer_date $poIds_cond $store_cond_1 and a.status_active=1 and b.status_active=1
	union all
	select null as booking_no, a.to_order_id, b.from_prod_id as prod_id, b.transfer_qnty as recv_qty, 3 as type, b.to_store store_id, c.detarmination_id, c.gsm, c.dia_width
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c
	where a.id=b.mst_id and b.from_prod_id=c.id and a.entry_form in(432,80) and a.item_category=13 and c.item_category_id=13 $transfer_date $smn_trans_booking_cond $store_cond_1 and a.status_active=1 and b.status_active=1";
	// echo $main_query;die;
	$result = sql_select($main_query);
	foreach ($result as $key => $row) 
	{
		if ($row[csf('type')]==2) // for order id to booking no
		{
			$bulk_booking_id_arr[$row[csf('to_order_id')]]=$row[csf('to_order_id')];
		}
		if ($row[csf('type')]==3) // for booking id to booking no
		{
			$sms_booking_id_arr[$row[csf('to_order_id')]]=$row[csf('to_order_id')];// come form smn booking sql
		}
		$prduct_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
	}
	// echo "<pre>";print_r($sms_booking_id_arr);die;
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4223, 2,$bulk_booking_id_arr, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4224, 3,$sms_booking_id_arr, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4225, 4,$prduct_arr, $empty_arr);
    oci_commit($con);

	$booking_sql="SELECT b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id, c.booking_type
	from GBL_TEMP_ENGINE t, wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c 
	where t.REF_VAL=a.id and t.USER_ID=$user_id and t.ENTRY_FORM=4223 and t.REF_FROM=2 and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.company_name=$cbo_company_id and c.booking_type=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $booking_no_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id, c.booking_type
	union all
	SELECT null as job_no, null as buyer_name, null as id, null as po_number, null as grouping, null as file_no, c.booking_no, a.id as booking_mst_id,a.booking_type
	from GBL_TEMP_ENGINE t, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c where t.REF_VAL=a.id and t.USER_ID=$user_id and t.ENTRY_FORM=4224 and t.REF_FROM=3 and a.booking_no=c.booking_no and a.company_id=$cbo_company_id and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $booking_no_cond
	group by c.booking_no, a.id,a.booking_type";
	// echo $booking_sql;die;
	$booking_sql_result=sql_select($booking_sql);
	if(!empty($booking_sql_result))
	{
		foreach($booking_sql_result as $row)
		{
			if ($row[csf('booking_type')]==4) 
			{
				$smn_booking_arr[$row[csf('booking_mst_id')]]['booking']=$row[csf('booking_no')];
			}
			else
			{
				$bulk_booking_arr[$row[csf('id')]]['booking']=$row[csf('booking_no')];
			}
		}
	}

	$prduct_arr = array_filter($prduct_arr);
	if(count($prduct_arr ) >0 ) // Production
	{
		$production_sql = sql_select("SELECT a.color_range_id,a.yarn_lot, a.yarn_count,a.prod_id,c.booking_id, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id 
		from GBL_TEMP_ENGINE t, pro_grey_prod_entry_dtls a, inv_receive_master c
		where t.REF_VAL=a.prod_id and t.USER_ID=$user_id and t.ENTRY_FORM=4225 and t.REF_FROM=4 and a.mst_id = c.id and c.entry_form in(2) and a.status_active=1");

		foreach ($production_sql as $row)
		{
			if($row[csf("knitting_source")] == 1)
			{
				$production_company= $company_short_arr[$row[csf("knitting_company")]];
			}
			else
			{
				$production_company= $supplier_arr[$row[csf("knitting_company")]];
			}

			$prodData[$row[csf("prod_id")]]["color_range_id"] .=$row[csf("color_range_id")].',';
			$prodData[$row[csf("prod_id")]]["yarn_lot"] .=$row[csf("yarn_lot")].',';
			$prodData[$row[csf("prod_id")]]["yarn_count"] .=$row[csf("yarn_count")].',';
			$prodData[$row[csf("prod_id")]]["yarn_prod_id"] .=$row[csf("yarn_prod_id")].',';
			$prodData[$row[csf("prod_id")]]["color_id"] .=$row[csf("color_id")].',';
			$prodData[$row[csf("prod_id")]]["febric_description_id"] =$row[csf("febric_description_id")];
			$prodData[$row[csf("prod_id")]]["stitch_length"] .=$row[csf("stitch_length")].',';
			$prodData[$row[csf("prod_id")]]["machine_no_id"] .=$row[csf("machine_no_id")].',';
			$prodData[$row[csf("prod_id")]]["machine_dia"] .=$row[csf("machine_dia")].',';
			$prodData[$row[csf("prod_id")]]["machine_gg"] .=$row[csf("machine_gg")].',';
			$prodData[$row[csf("prod_id")]]["knitting_source"] =$row[csf("knitting_source")];
			$prodData[$row[csf("prod_id")]]["knitting_company"] =$production_company;
			$allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
			$allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
			$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
		}

		$febric_description_arr = array_filter($allDeterArr);
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

		$all_color_arr = array_filter($allColorArr);
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

		$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

			$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
			foreach ($yarn_sql as $row)
			{
				$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
				$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
				$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
			}
		}
	}

	foreach ($result as $row) // Main Loop Array
	{
		if ($row[csf('type')]==2) // for order id to booking no
		{
			$booking=$bulk_booking_arr[$row[csf('to_order_id')]]['booking'];
		}
		else if ($row[csf('type')]==3) // for booking id to booking no
		{
			$booking=$smn_booking_arr[$row[csf('to_order_id')]]['booking'];// come form smn booking sql
		}
		else
		{
			$booking=$row[csf('booking_no')];
		}
		$get_prod_id_arr[$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]=$row[csf('prod_id')];
		$fabrication=$row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')];

		//$dataArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication] += $row[csf('qnty')];
		//$storeWiseStockArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += $row[csf('qnty')];
		//$storeWiseRcvArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += $row[csf('qnty')];
		$dataArr[$booking][$fabrication] += $row[csf('qnty')];
		$storeWiseStockArr[$booking][$fabrication][$row[csf('store_id')]] += $row[csf('recv_qty')];
		$storeWiseRcvArr[$booking][$fabrication][$row[csf('store_id')]]+=$row[csf('recv_qty')];
		//$storeWiseRcvArr[$booking][$fabrication][$row[csf('store_id')]]['prod_id']=$row[csf('prod_id')];
	}
	// echo "<pre>";print_r($storeWiseRcvArr);

	$iss_qty_sql="SELECT a.booking_no, 0 as from_order_id, e.prod_id, e.cons_quantity as qnty, 1 as type, e.store_id as store_name, c.detarmination_id, c.gsm, c.dia_width
	from INV_ISSUE_MASTER a,inv_transaction e, INV_GREY_FABRIC_ISSUE_DTLS b, product_details_master c
	where a.id=e.mst_id and e.id=b.trans_id and e.prod_id=c.id and a.entry_form in(16) and a.issue_basis=1 and a.item_category=13 and c.item_category_id=13 $iss_trans_date $bookingIds_cond $store_cond_2 and a.status_active=1 and b.status_active=1 and e.status_active=1
	union all
	select null as booking_no, a.from_order_id, b.from_prod_id as prod_id, b.transfer_qnty as qnty, 2 as type, b.from_store store_name, c.detarmination_id, c.gsm, c.dia_width
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c
	where a.id=b.mst_id and b.from_prod_id=c.id and a.entry_form in(13) and a.item_category=13 and c.item_category_id=13 $transfer_date $poIds_cond $store_cond_3 and a.status_active=1 and b.status_active=1
	union all
	select null as booking_no, a.from_order_id, b.from_prod_id as prod_id, b.transfer_qnty as qnty, 2 as type, b.from_store store_name, c.detarmination_id, c.gsm, c.dia_width 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c
	where a.id=b.mst_id and b.from_prod_id=c.id and a.entry_form in(81) and a.item_category=13 and c.item_category_id=13 $transfer_date $poIds_cond $store_cond_3 and a.status_active=1 and b.status_active=1
	union all
	select null as booking_no, a.from_order_id, b.from_prod_id as prod_id, b.transfer_qnty as qnty, 3 as type, b.from_store store_name, c.detarmination_id, c.gsm, c.dia_width
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c
	where a.id=b.mst_id and b.from_prod_id=c.id and a.entry_form in(432,80) and a.item_category=13 and c.item_category_id=13 $transfer_date $smn_trans_booking_cond $store_cond_3 and a.status_active=1 and b.status_active=1";
	// echo $iss_qty_sql;die;
	$iss_sql_result=sql_select($iss_qty_sql);
	foreach ($iss_sql_result as $key => $row) 
	{
		if ($row[csf('type')]==2) // for order id to booking no
		{
			$issue_bulk_booking_id_arr[$row[csf('from_order_id')]]=$row[csf('from_order_id')];
		}
		if ($row[csf('type')]==3) // for booking id to booking no
		{
			$issue_sms_booking_id_arr[$row[csf('from_order_id')]]=$row[csf('from_order_id')];// come form smn booking sql
		}
	}
	// echo "<pre>";print_r($sms_booking_id_arr);die;
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4226, 6,$issue_bulk_booking_id_arr, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4227, 7,$issue_sms_booking_id_arr, $empty_arr);
    oci_commit($con);

    $booking_sql="SELECT b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id, c.booking_type
	from GBL_TEMP_ENGINE t, wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c 
	where t.REF_VAL=a.id and t.USER_ID=$user_id and t.ENTRY_FORM=4226 and t.REF_FROM=6 and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.company_name=$cbo_company_id and c.booking_type=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $booking_no_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no, c.booking_mst_id, c.booking_type
	union all
	SELECT null as job_no, null as buyer_name, null as id, null as po_number, null as grouping, null as file_no, c.booking_no, a.id as booking_mst_id,a.booking_type
	from GBL_TEMP_ENGINE t, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c where t.REF_VAL=a.id and t.USER_ID=$user_id and t.ENTRY_FORM=4227 and t.REF_FROM=7 and a.booking_no=c.booking_no and a.company_id=$cbo_company_id and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $booking_no_cond
	group by c.booking_no, a.id,a.booking_type";
	// echo $booking_sql;die;
	$iss_booking_sql_result=sql_select($booking_sql);
	if(!empty($iss_booking_sql_result))
	{
		foreach($iss_booking_sql_result as $row)
		{
			if ($row[csf('booking_type')]==4) 
			{
				$iss_smn_booking_arr[$row[csf('booking_mst_id')]]['booking']=$row[csf('booking_no')];
			}
			else
			{
				$iss_bulk_booking_arr[$row[csf('id')]]['booking']=$row[csf('booking_no')];
			}
		}
	}

	foreach ($iss_sql_result as $row) // All Issue Data Array
	{
		if ($row[csf('type')]==2) // for order id to booking no
		{
			$booking=$iss_bulk_booking_arr[$row[csf('from_order_id')]]['booking'];
		}
		else if ($row[csf('type')]==3) // for booking id to booking no
		{
			$booking=$iss_smn_booking_arr[$row[csf('from_order_id')]]['booking'];// come form smn booking sql
		}
		else
		{
			$booking=$row[csf('booking_no')];
		}

		$fabrication=$row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')];
		$storeWiseStockArr[$booking][$fabrication][$row[csf('store_name')]] -= $row[csf('qnty')];
		$storeWiseIssArr[$booking][$fabrication][$row[csf('store_name')]] += $row[csf('qnty')];
	}
	// echo "<pre>";print_r($iss_storeWiseIssArr);die;
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (4223,4224,4225,4226,4227)");
    oci_commit($con);
    
	$width = (1266+($num_of_store*110));
	ob_start();
	?>
	<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
		<table width="100%" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" border="1">
			<thead>
				<tr>
					<th width='30'>SL</th>
					<th width='150'>Construction</th>
					<th width='110'>Color</th>
					<th width='110'>Color Range</th>
					<th width='80'>Y. Count</th>
					<th width='80'>Y. Type</th>
					<th width='116'>Y. Composition</th>
					<th width='100'>Brand</th>
					<th width='110'>Yarn Lot</th>
					<th width='110'>MC Dia and Gauge</th>
					<th width='80'>F/Dia</th>
					<th width='110'>S. Length</th>
					<th width='60'>GSM</th>
					<th width='110'>M/C No.</th>
					<th width='110'>Knitting Company</th>
					<th width='80'>Total Stock Qty.</th>
					<?
					foreach ($stores as $store) {
						?>
						<th width="110"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
				</tr>
			</thead>
			<?
			if(!empty($dataArr))
			{
				$i=1;
				//echo "<pre>";print_r($storeWiseRcvArr);
				foreach ($storeWiseRcvArr as $booking_datas=>$booking_data_v)
				{
					foreach ($booking_data_v as $febric_description=>$row)
					{
						// echo "<pre>";print_r($row);
						//$booking_data = explode("*", $booking_datas);
						$fabrication = explode("*", $febric_description);
						$detar_id=$fabrication[0];
						$gsm=$fabrication[1];
						$dia=$fabrication[2];
						$product_id=$get_prod_id_arr[$detar_id][$gsm][$dia];
						// echo $product_id.'<br>';

						$yarn_counts_arr =array_filter(array_unique(explode(",", chop($prodData[$product_id]["yarn_count"],","))));

						$yarn_counts="";
						foreach ($yarn_counts_arr as $count) {
							$yarn_counts .= $count_arr[$count] . ",";
						}
						$yarn_counts = rtrim($yarn_counts, ", ");

						$color_range_id_arr =array_filter(array_unique(explode(",", chop($prodData[$product_id]["color_range_id"],","))));

						$color_range_name="";
						foreach ($color_range_id_arr as $id) {
							$color_range_name .= $color_range[$id] . ",";
						}
						$color_range_name = rtrim($color_range_name, ", ");

						// echo $prodData[$product_id]["color_id"].'=<br>';
						$color_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["color_id"],","))));
						// echo "<pre>";print_r($color_arr);
						$colors="";
						foreach ($color_arr as $color) {
							$colors .= $colorArr[$color] . ",";
						}
						$colors = rtrim($colors, ", ");

						$yarn_id_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["yarn_prod_id"],","))));
						$yarn_brand = $yarn_comp = $yarn_type_name = "";
						foreach ($yarn_id_arr as $yid)
						{
							$yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
							$yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
							$yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
						}

						$yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
						$yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
						$yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));

						$yarn_lot_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["yarn_lot"],","))));
						$yarn_lot = "";
						foreach ($yarn_lot_arr as $lot) {
							$yarn_lot .= $lot . ",";
						}
						$yarn_lot = rtrim($yarn_lot, ", ");

						$stitch_length_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["stitch_length"],","))));
						$stitch_length = "";
						foreach ($stitch_length_arr as $sl) {
							$stitch_length .= $sl . ",";
						}
						$stitch_length = rtrim($stitch_length, ", ");

						$machine_no_id_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["machine_no_id"],","))));
						$machine_name = "";
						foreach ($machine_no_id_arr as $mc_id) {
							$machine_name .= $machine_arr[$mc_id] . ",";
						}
						$machine_name = rtrim($machine_name, ", ");

						$machine_dia_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["machine_dia"],","))));
						$machine_dia = "";
						foreach ($machine_dia_arr as $mc_dia) {
							$machine_dia .= $mc_dia . ",";
						}
						$machine_dia = rtrim($machine_dia, ", ");

						$machine_gg_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["machine_gg"],","))));
						$machine_gg = "";
						foreach ($machine_gg_arr as $mc_gg) {
							$machine_gg .= $mc_gg . ",";
						}
						$machine_gg = rtrim($machine_gg, ", ");
						$machine_dia_gg = $machine_dia.'x'.$machine_gg;

						$knitting_company_arr = array_filter(array_unique(explode(",", chop($prodData[$product_id]["knitting_company"],","))));
						$knitting_company = "";
						foreach ($knitting_company_arr as $knitting_comp) {
							$knitting_company .= $knitting_comp . ",";
						}
						$knitting_company = rtrim($knitting_company, ", ");

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if (!in_array($booking_datas, $checkBookArr))
						{
							$checkBookArr[$i] = $booking_datas;
							if ($i > 1)
							{
								?>
								<tr>
									<th colspan="15" width="1466" align="right">Total:</th>
									<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
									<?
									$store_ref = implode(",",array_unique(explode(",",chop($store_ref,","))));
									foreach ($stores as $store)
									{
										?>
										<th width="110" align="right"><? echo number_format($sub_store_stock[$store_ref][$store[csf("id")]],2,".","");?></th>
										<?
									}
									?>
								</tr>
								<?
								$sub_tot_stock = 0;	$store_ref="";
							}
							?>
							<tr>
								<td colspan="<? echo $num_of_store+16;?>"><b>Booking No: <? echo $booking_datas; ?></b></td>
							</tr>
							<?
						}
						?>
						<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

							<td width='30' title="book data=<? echo $booking_datas."\n".$febric_description;?>"><? echo $i?></td>
							<td widtd='150' title="<? echo $detar_id;?>" ><? echo $constuction_arr[$detar_id];?></td>
							<td widtd='110' title="Color"><? echo $colors;?></td>
							<td widtd='110' title="Color Range"><? echo $color_range_name;?></td>
							<td widtd='80' title="Count"><? echo $yarn_counts;?></td>
							<td widtd='80' title="Type"><? echo $yarn_type_name;?></td>
							<td widtd='116' title="y count"><? echo $yarn_comp;?></td>
							<td widtd='100' title="Brand"><? echo $yarn_brand;?></td>
							<td widtd='110' title="Yarn Lot"><? echo $yarn_lot;?></td>
							<td widtd='110' title="Mc dia gg"><? echo $machine_dia_gg;?></td>
							<td widtd='80' title="F/Dia"><? echo $dia;?></td>
							<td widtd='110' title="S.Length"><? echo $stitch_length;?></td>
							<td widtd='60' title="GSM"><? echo $gsm;?></td>
							<td widtd='110' title="MC No"><? echo $machine_name;?></td>
							<td widtd='110' title="knitting Company"><? echo $knitting_company;?></td>

							<?
							$stock=0;
							foreach ($stores as $store)
							{
								// echo $store[csf("id")].'<br>';
								$stock += $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$sub_tot_stock +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];

								$sub_store_stock[$booking_datas][$store[csf("id")]] +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$store_ref .= $booking_datas.",";
							}
							?>
							<td width="100" align="right"><? echo number_format($stock,2,".",""); ?></td>
							<?
							$stock=$store_stock=0;
							foreach ($stores as $store)
							{
								$store_stock = $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_receive = $storeWiseRcvArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_issue = $storeWiseIssArr[$booking_datas][$febric_description][$store[csf("id")]];
								?>
								<td width="110" align="right" title="<? echo "Store=".$store[csf("id")].'**'.$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","");?>"><? echo number_format($store_stock,2,".","");?></td>
								<?
							}
							?>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<tr>
					<th colspan="15" align="right">Total:</th>
					<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
					<?
					foreach ($stores as $store)
					{
						?>
						<th width="110" align="right"><? echo number_format($sub_store_stock[$booking_datas][$store[csf("id")]],2,".","");?></th>
						<?
					}
					?>
				</tr>
				<?
			}
			?>
		</table>
	</fieldset>
	<?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "s";

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

if($action=="sales_report_generate")
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
			$year_cond=" and year(a.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
		}
	}

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$cbo_company_id=trim(str_replace("'","",$cbo_company_id));
	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));
	$txt_fso_no=trim(str_replace("'","",$txt_fso_no));
	if ($txt_fso_no=="") $sales_no_cond=""; else $sales_no_cond=" and a.job_no_prefix_num in ($txt_fso_no) ";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id,b.body_part_id, b.determination_id, b.color_id,b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.company_id = $cbo_company_id $year_cond $sales_no_cond and a.id = b.mst_id and a.status_active =1 and b.status_active=1 and a.within_group=2";

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$booking_ref=$row[csf('buyer_id')]."*".$row[csf('job_no')]."*".$row[csf('sales_booking_no')];
			$poIds.=$row[csf('id')].",";
			$poArr[$row[csf('id')]]=$booking_ref;
			$po_booking[$row[csf('id')]]=$row[csf('sales_booking_no')];

			$fileRefArr[$booking_ref].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$poIds=chop($poIds,','); $poIds_cond_roll=$poIds_cond_trans_roll=$ctct_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	}

	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr 			= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );

	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}

	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	$main_query="select c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id
	from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.entry_form in(2,22,58) and a.id=e.mst_id and e.id=b.trans_id and b.id=c.dtls_id and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $trans_date $poIds_cond_roll $store_cond_2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and c.is_sales =1
	union all
	select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $store_cond_1 and c.is_sales =1 and a.status_active=1 and b.status_active=1 and c.status_active=1";
	//echo $main_query;die;

	$result = sql_select($main_query);
	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
	if($r_id2)
	{
		oci_commit($con);
	}

	if(!empty($result))
	{
		foreach ($result as $row)
		{
			$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

			$r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")");
			if($r_id) 
			{
				$r_id=1;
			} 
			else 
			{
				echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")";
				oci_rollback($con);
				die;
			}

		}
	}
	else
	{
		echo "Data Not Found";
		die;
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$barcodeArr = array_filter($barcodeArr);
	if(count($barcodeArr ) >0 )
	{
		/*
			$receive_barcodes = implode(",", $barcodeArr);
			if($db_type==2 && count($barcodeArr)>999)
			{
				$barcode_chunk=array_chunk($barcodeArr,999) ;
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

			$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 $barcode_cond");
		*/

		$split_chk_sql = sql_select("select c.barcode_no, c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no d where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active =1 and c.status_active =1 and b.barcode_no =d.barcode_no and d.userid = $user_id");

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
				}
			}
		}

		//$production_sql = sql_select("select b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form = 2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1 $barcode_cond");

		$production_sql = sql_select("select b.barcode_no, a.color_range_id, a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no, c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58) and a.trans_id=0 and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid =$user_id order by c.entry_form desc");

		foreach ($production_sql as $row)
		{
			$prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
			$prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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

			if($row[csf('receive_basis')] == 2 )
			{
				$program_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
			}
		}
		unset($production_sql);

		$febric_description_arr = array_filter($allDeterArr);
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

		$all_color_arr = array_filter($allColorArr);
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

		if(count($program_id_arr) >0 )
		{

			$program_ids = implode(",", $program_id_arr);
			$programCond = $program_id_cond = "";
			if($db_type==2 && count($program_id_arr)>999)
			{
				$program_id_arr_chunk=array_chunk($program_id_arr,999) ;
				foreach($program_id_arr_chunk as $chunk_arr)
				{
					$programCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$program_id_cond.=" and (".chop($programCond,'or ').")";
			}
			else
			{
				$program_id_cond=" and id in($program_ids)";
			}

			$plan_arr=array();
			$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls where status_active=1 $program_id_cond");
			foreach($plan_data as $row)
			{
				$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
			}
			unset($plan_data);
		}

		$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

			$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
			foreach ($yarn_sql as $row)
			{
				$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
				$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
				$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
			}
		}
	}

	foreach ($result as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] == 1)
		{
			$production_company= $company_short_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		else
		{
			$production_company= $supplier_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}

		$machine_dia_gg='';
		if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
		}

		$fabrication = $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["width"]  . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"]."*".$production_company;

		$dataArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication] += $row[csf('qnty')];
		$storeWiseStockArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += $row[csf('qnty')];
		$storeWiseRcvArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += $row[csf('qnty')];
	}

	$iss_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty, e.store_id as store_name from pro_roll_details c, inv_grey_fabric_issue_dtls d, inv_transaction e where c.dtls_id=d.id and c.mst_id=d.mst_id and d.trans_id=e.id and e.transaction_type=2 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll $store_cond_2 and c.is_sales =1
		union all
		select a.po_breakdown_id, c.barcode_no, c.qnty, b.from_store as store_name from order_wise_pro_details a, inv_item_transfer_dtls b, pro_roll_details c where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=133 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $poIds_cond_trans_roll $store_cond_3 and c.booking_without_order =0");

	foreach ($iss_qty_sql as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] == 1)
		{
			$production_company= $company_short_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		else
		{
			$production_company= $supplier_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		$machine_dia_gg='';
		if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
		}

		$fabrication = $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["width"]  . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"]."*".$production_company;

		$mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
		if($mother_barcode_no != "")
		{
			if($prodBarcodeData[$mother_barcode_no]["knitting_source"]==1)
			{
				$production_company= $company_short_arr[$prodBarcodeData[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$production_company=$supplier_arr[$prodBarcodeData[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($prodBarcodeData[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$prodBarcodeData[$mother_barcode_no]["booking_id"]];
			}

			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"]  . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$mother_barcode_no]["machine_no_id"]."*".$production_company;
		}

		$storeWiseStockArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_name')]] -= $row[csf('qnty')];
		$storeWiseIssArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_name')]] += $row[csf('qnty')];
	}
	unset($result);
	unset($iss_qty_sql);

	$width = (1266+($num_of_store*110));
	ob_start();
	?>
	<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
		<table width="100%" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" border="1">
			<thead>
				<tr>
					<th width='30'>SL</th>
					<th width='150'>Construction</th>
					<th width='110'>Color</th>
					<th width='110'>Color Range</th>
					<th width='80'>Y. Count</th>
					<th width='80'>Y. Type</th>
					<th width='116'>Y. Composition</th>
					<th width='100'>Brand</th>
					<th width='110'>Yarn Lot</th>
					<th width='110'>MC Dia and Gauge</th>
					<th width='80'>F/Dia</th>
					<th width='110'>S. Length</th>
					<th width='60'>GSM</th>
					<th width='110'>M/C No.</th>
					<th width='110'>Knitting Company</th>
					<th width='80'>Total Stock Qty.</th>
					<?
					foreach ($stores as $store) {
						?>
						<th width="110"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
				</tr>
			</thead>
			<?
			if(!empty($dataArr))
			{
				$i=1;

				foreach ($storeWiseRcvArr as $booking_datas=>$po_row)
				{
					foreach ($po_row as $febric_description=>$row)
					{
						$booking_data = explode("*", $booking_datas);
						$fabrication = explode("*", $febric_description);

						$yarn_counts_arr = explode(",", $fabrication[7]);

						$yarn_counts="";
						foreach ($yarn_counts_arr as $count) {
							$yarn_counts .= $count_arr[$count] . ",";
						}
						$yarn_counts = rtrim($yarn_counts, ", ");

						$color_arr = explode(",", $fabrication[1]);
						$colors="";
						foreach ($color_arr as $color) {
							$colors .= $colorArr[$color] . ",";
						}
						$colors = rtrim($colors, ", ");

						$yarn_id_arr = array_unique(array_filter(explode(",", $fabrication[8])));
						$yarn_brand = $yarn_comp = $yarn_type_name = "";
						foreach ($yarn_id_arr as $yid)
						{
							$yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
							$yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
							$yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
						}

						$yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
						$yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
						$yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));

						$machine_dia_gg = $fabrication[9];
						$machine_name =  $machine_arr[$fabrication[10]];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if (!in_array($booking_datas, $checkBookArr))
						{
							$checkBookArr[$i] = $booking_datas;
							if ($i > 1)
							{
								?>
								<tr>
									<th colspan="15" width="1466" align="right">Fso Total:</th>
									<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
									<?
									$store_ref = implode(",",array_unique(explode(",",chop($store_ref,","))));
									foreach ($stores as $store)
									{
										?>
										<th width="110" align="right"><? echo number_format($sub_store_stock[$store_ref][$store[csf("id")]],2,".","");?></th>
										<?
									}
									?>
								</tr>
								<?
								$sub_tot_stock = 0;	$store_ref="";
							}
							?>
							<tr>
								<td colspan="<? echo $num_of_store+16;?>"><b>Buyer: <? echo $buyer_arr[$booking_data[0]].", Fso No : ".$booking_data[1].", Booking No : ".$booking_data[2]; ?></b></td>
							</tr>
							<?
						}
						?>

						<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

							<td width='30' title="book data=<? echo $booking_datas."\n".$febric_description;?>"><? echo $i?></td>
							<td widtd='150' title="<? echo $fabrication[0];?>" ><? echo $constuction_arr[$fabrication[0]];?></td>
							<td widtd='110' title="Color"><? echo $colors;?></td>
							<td widtd='110' title="Color Range"><? echo $color_range[$fabrication[2]];?></td>
							<td widtd='80' title="Count"><? echo $yarn_counts;?></td>
							<td widtd='80' title="Type"><? echo $yarn_type_name;?></td>
							<td widtd='116' title="y count"><? echo $yarn_comp;?></td>

							<td widtd='100' title="Brand"><? echo $yarn_brand;?></td>
							<td widtd='110' title="Yarn Lot"><? echo $fabrication[6];?></td>
							<td widtd='110' title="Mc dia gg"><? echo $machine_dia_gg;?></td>
							<td widtd='80' title="F/Dia"><? echo $fabrication[4];?></td>
							<td widtd='110' title="S.Length"><? echo $fabrication[5];?></td>
							<td widtd='60' title="GSM"><? echo $fabrication[3];?></td>
							<td widtd='110' title="MC No"><? echo $machine_name;?></td>
							<td widtd='110' title="knitting Company"><? echo $fabrication[11];?></td>

							<?
							$stock=0;
							foreach ($stores as $store)
							{
								$stock += $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$sub_tot_stock +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];

								$sub_store_stock[$booking_datas][$store[csf("id")]] +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$store_ref .= $booking_datas.",";
							}
							?>
							<td width="100" align="right"><? echo number_format($stock,2,".",""); ?></td>
							<?
							$stock=$store_stock=0;
							foreach ($stores as $store)
							{
								$store_stock = $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_receive = $storeWiseRcvArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_issue = $storeWiseIssArr[$booking_datas][$febric_description][$store[csf("id")]];
								?>
								<td width="110" align="right" title="<? echo "Store=".$store[csf("id")].'**'.$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","");?>"><? echo number_format($store_stock,2,".","");?></td>
								<?
							}
							?>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<tr>
					<th colspan="15" align="right">Fso Total:</th>
					<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
					<?
					foreach ($stores as $store)
					{
						?>
						<th width="110" align="right"><? echo number_format($sub_store_stock[$booking_datas][$store[csf("id")]],2,".","");?></th>
						<?
					}
					?>
				</tr>
				<?
			}
			?>
		</table>
	</fieldset>
	<?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "s";

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) 
	{
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

if($action=="report_generate2")
{
	ini_set('session.gc_maxlifetime', 3600); 
	session_start();
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
	$cbo_value_with=str_replace("'","",$cbo_value_with);
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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));

	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	$cbo_string_search_type=str_replace("'","",$cbo_string_search_type);
	if($cbo_string_search_type==1)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) = LOWER('".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";
		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no = '$txt_booking_no'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id = $hide_booking_id"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $bookiing_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."'"; else $bookiing_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $bookiing_id_cond=" and c.booking_id like '%".$hide_booking_id."'"; else $bookiing_id_cond="";
	}
	// echo $file_cond.'<br>'.$ref_cond.'<br>'.$bookiing_no_cond.'<br>'.$bookiing_id_cond.'<br>';

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	//if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	//if($txt_ref_no!="") $ref_cond=" and a.grouping = '".trim($txt_ref_no)."'";

	//if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	//if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="SELECT * FROM
		(select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short, sum(grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id = c.pre_cost_fabric_cost_dtls_id $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short 
		union all
		select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short, sum( c.grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_conv_cost_dtls e where b.company_name=$cbo_company_id and c.booking_type in (4) and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id = c.pre_cost_fabric_cost_dtls_id and d.id = e.fabric_description $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short) t order by grouping,fabric_color_id ";
	

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;

			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}

			$ref_file = $row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('booking_no')]."_".$row[csf('file_no')]."_".$row[csf('grouping')]."_".$row[csf('body_part_id')]."_".$row[csf('lib_yarn_count_deter_id')]."_".$row[csf('fabric_color_id')]."_".$row[csf('grey_fab_qnty')]."_".$booking_type."##";
			$poIds.=$row[csf('id')].",";

			$poArr[$row[csf('id')]]=$row[csf('job_no')];
			$fileRefArr[$row[csf('job_no')]].=$ref_file;
			$popup_job_ref[$row[csf('job_no')]] .= $row[csf('id')].",";

			if($row[csf('shiping_status')] != 3 && $shipStat[$row[csf('job_no')]] =="")
			{
				$shipStat[$row[csf('job_no')]] = $row[csf('shiping_status')];
			}
			
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$poIds=chop($poIds,',');
	$poIds = implode(",",array_unique(explode(",", $poIds)));

	 $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
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

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$recvDtlsDataArr=array();

	$query="SELECT * FROM
		(select a.id, a.entry_form, a.receive_date, b.febric_description_id, b.color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction e
	WHERE a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.id=b.mst_id and b.id=c.dtls_id and e.id=b.trans_id and c.status_active=1 and c.is_deleted=0 $trans_date $poIds_cond_roll $store_cond_2 and c.booking_without_order=0 and e.status_active=1
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $otot_po_cond $store_cond_1 and c.booking_without_order =0
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $stst_po_cond $store_cond_1 and nvl(c.booking_without_order,0) =0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $otot_po_cond $store_cond_1 and c.booking_without_order =0) abc order by color_id";

	//echo $query;//die;
	$data_array=sql_select($query);
	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
	if($r_id2)
	{
		oci_commit($con);
	}

	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];

		$r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")");
		if($r_id) 
		{
			$r_id=1;
		} 
		else 
		{
			echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")";
			oci_rollback($con);
			die;
		}
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr))
	{
		/*
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

		*/
		$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no d where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 and b.barcode_no = d.barcode_no and d.userid=$user_id");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}

			$split_barcode_nos = implode(",", $split_barcode_arr);
			$spBarCond = $split_barcode_no_cond = "";
			if($db_type==2 && count($split_barcode_arr)>999)
			{
				$split_barcode_arr_chunk=array_chunk($split_barcode_arr,999) ;
				foreach($split_barcode_arr_chunk as $chunk_arr)
				{
					$spBarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
				}
				$split_barcode_no_cond.=" and (".chop($spBarCond,'or ').")";
			}
			else
			{
				$split_barcode_no_cond=" and a.barcode_no in($split_barcode_nos)";
			}
			//$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 $split_barcode_no_cond and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 62 $split_barcode_no_cond and a.roll_split_from = b.id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$recvDataArrTrans=array();$recvDataArr=array();

		/*$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond";*/

		/*$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid=$user_id ";*/


		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid=$user_id order by a.entry_form desc";

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
			$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
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
			if($row[csf('color_id')]!="")
			{
				$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
			}
		}
		unset($recvDataT);
	}

	$all_color_ids = implode(",", $all_color_arr);
	$all_color_arr_exp=explode(",", $all_color_ids);
	$all_color_arrs = array_filter($all_color_arr_exp);
	if(!empty($all_color_arrs))
	{
		$all_color_ids = implode(",", $all_color_arrs);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arrs)>999)
		{
			$all_color_chunk=array_chunk($all_color_arrs,999) ;
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

	$transfer_out_sql=sql_select("select d.transfer_date, a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $poIds_cond_trans_roll $store_cond_3 and c.booking_without_order =0 and b.mst_id = d.id and d.entry_form=83
		union all
		select a.transfer_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $ctct_po_cond $store_cond_3 and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1  and nvl(c.booking_without_order,0) =0 
		group by a.transfer_date, c.barcode_no, b.from_order_id
		union all
		select a.transfer_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $otst_po_cond $store_cond_3 and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1
		group by a.transfer_date, c.barcode_no, a.from_order_id ");

	$ref_file="";$data_prod=""; $trans_out_barcode_arr = array();
	foreach($transfer_out_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$trans_out_qty_arr[$ref_file][$data_prod]["trans_out"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('transfer_date')]))
		{
			$trans_out_qty_arr[$ref_file][$data_prod]["today_trans_out"] +=$row[csf("qnty")];
		}
	}

	unset($transfer_out_sql);
	$iss_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty, d.issue_date, max(d.issue_date) max_issue_date,d.issue_number from pro_roll_details c, inv_issue_master d ,inv_grey_fabric_issue_dtls b, inv_transaction e where c.mst_id = d.id and d.id=b.mst_id and b.trans_id=e.id and b.id=c.dtls_id  and d.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll $store_cond_2 and c.booking_without_order = 0 and d.id = e.mst_id and e.transaction_type=2 and e.item_category=13 and e.status_active =1 group by c.po_breakdown_id,c.barcode_no,c.qnty,d.issue_date,d.issue_number");

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();
	foreach($iss_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$data_prod = $recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"]."**".$recvDataArrTrans[$mother_barcode_no]["color_id"];
		}
		$iss_qty_arr[$ref_file][$data_prod]["issue"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('issue_date')])){
			$iss_qty_arr[$ref_file][$data_prod]["today_issue"] +=$row[csf("qnty")];
		}

		$iss_qty_arr[$ref_file][$data_prod]["max_issue_date"] = $row[csf('max_issue_date')];
	}
	
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($iss_qty_arr);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	//$iss_rtn_qty_sql=sql_select("select d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c, inv_receive_master d, inv_transaction e where c.entry_form=84 and c.mst_id = d.id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0 and d.id = e.mst_id and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 $poIds_cond_roll $store_cond_2");
	$iss_rtn_qty_sql=sql_select("select d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty  from pro_roll_details c, inv_receive_master d, inv_transaction e,pro_grey_prod_entry_dtls f where c.entry_form=84 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0  and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and f.status_active =1 and f.is_deleted=0  $poIds_cond_roll $store_cond_2");
	foreach($iss_rtn_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
		{
			$iss_rtn_qty_arr[$ref_file][$data_prod]["today_issue_ret"]+=$row[csf("qnty")];
		}
	}
	unset($iss_rtn_qty_sql);

	$ref_file="";$data_prod="";$min_date="";
	foreach($data_array as $row)
	{
		if( $row[csf("type")]==2)
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['transfer_in']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
			}
		}
		else
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['recv']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['today_recv']+=$row[csf("qnty")];
			}
		}

		if($recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"] =="")
		{
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
		else if($recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"] > strtotime($row[csf("receive_date")]))
		{
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
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
		<table width="2800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="11" style='padding:0px 1px 0px 0px'>Booking Info</th>
					<th colspan="20">Receive Info</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="70" rowspan="2">Buyer</th>
					<th width="90" rowspan="2">Job No</th>
					<th width="100" rowspan="2">Booking No</th>
					<th width="100" rowspan="2">Booking Type</th>
					<th width="70" rowspan="2">File No</th>
					<th width="80" rowspan="2">Ref. No</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th width="80" rowspan="2">Grey Fabric Qty(Kg)</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th colspan="3">Today Receive</th>
					<th colspan="4">Total Receive</th>
					<th colspan="3">Today Issue</th>
					<th colspan="4">Total Issue</th>
					<th width="105" rowspan="2">Stock Qty.</th>
					<th width="105" rowspan="2">Last Issued Date</th>
					<th width="50" rowspan="2">DOH</th>
				</tr>
				<tr>
					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer From</th>

					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer From</th>
					<th width="90">Grand Total</th>

					<th width="90">Issue</th>
					<th width="90">Receive Return</th>
					<th width="90">Transfer To</th>

					<th width="90">Issue</th>
					<th width="90">Receive Return</th>
					<th width="90">Transfer To</th>
					<th width="90">Grand Total</th>
				</tr>
			</thead>
		</table>
		<div style="width:2820px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					$stock_qty_smry_arr[$job_no]=0;
					if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
					{
						foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
						{
							
							foreach ($colorData as $PartConstColor => $val)
							{
								$issue_ret_smry = $iss_rtn_qty_arr[$job_no][$PartConstColor]["issue_ret"];
								$transfer_in_smry = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['transfer_in'];

								$issue_smry = $iss_qty_arr[$job_no][$PartConstColor]["issue"];
								$trans_out_smry = $trans_out_qty_arr[$job_no][$PartConstColor]["trans_out"];

								$total_issue_smry = $issue_smry+$trans_out_smry;
								$total_receive_smry = $val["recv"] + $issue_ret_smry + $transfer_in_smry;
								$stock_qty_smry = $total_receive_smry-$total_issue_smry;
								$stock_qty_smry = number_format($stock_qty_smry,2,".","");

								//if(($cbo_value_with==1 && $stock_qty_smry>=0) || ($cbo_value_with==2 && $stock_qty_smry>0))
								if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty_smry>0))
								{
									$stock_qty_smry_arr[$job_no]=1;
								}	
							}
						}
					}
				}
				$i=1;$y=1;
				$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=0;
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					if ($stock_qty_smry_arr[$job_no]>0) 
					{

						if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
						{
							$fileRefData=explode("##",chop($fileRefArrData,"##"));
							$row_count = count($fileRefData);

							$c=1;
							?>
							<tr>
								<td width="955" style='padding:0px 1px 0px 0px'>
									<table cellpadding="0" cellspacing="0" border="0">
										<?
										for ($r=0; $r < $row_count; $r++) 
										{ 
											if(isset($fileRefData[$r]))
											{
												$fileRefDataDtls = explode("_", $fileRefData[$r]);
												$buyer_id=$fileRefDataDtls[0];
												$job_number=$fileRefDataDtls[1];
												$bookingNo=$fileRefDataDtls[2];
												$fileNo=$fileRefDataDtls[3];
												$refNo=$fileRefDataDtls[4];
												$body_part_id=$fileRefDataDtls[5];
												$deter_id=$fileRefDataDtls[6];
												$fabric_color_id=$fileRefDataDtls[7];
												$grey_qnty=$fileRefDataDtls[8];
												$booking_type=$fileRefDataDtls[9];

												$sub_job_total += $grey_qnty;
												$grand_job_total += $grey_qnty;

												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>

												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="40"><? echo $c; ?></td>
													<td width="70"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
													<td width="90"><p><? echo $job_number; ?>&nbsp;</p></td>
													<td width="100"><p><? echo $bookingNo; ?>&nbsp;</p></td>
													<td width="100"><p><? echo $booking_type; ?>&nbsp;</p></td>
													<td width="70"><p><? echo $fileNo; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $refNo; ?>&nbsp;</p></td>
													<td width="110"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
													<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
													<td width="105"><p><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($grey_qnty,2,'.',''); ?>&nbsp;</p></td>
												</tr>
												<?
											}
											else
											{
												?>
												<tr>
													<td width="40"></td>
													<td width="70"><p>&nbsp;</p></td>
													<td width="90"><p>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="70"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="110"><p>&nbsp;</p></td>
													<td width="110"><p>&nbsp;</p></td>
													<td width="105"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
												</tr>
												<?
											}
											$i++;$c++;
										}
										?>
									</table>
								</td>

								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<?
										foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
										{
											foreach ($colorData as $PartConstColor => $val)
											{
												$PartConstColorArr = explode("**", $PartConstColor);
												$deter_id = $PartConstColorArr[0];
												$body_part_id = $PartConstColorArr[1];
												$color_id = $PartConstColorArr[2];

												$color_names="";
												$color_ids = explode(",", $color_id);
												foreach ($color_ids as $color) 
												{
													$color_names .=  $color_arr[$color].",";
												}
												$color_names = chop($color_names,",");

												$trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["trans_out"];
												$today_trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["today_trans_out"];

												$issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["issue_ret"];
												$today_issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["today_issue_ret"];

												$transfer_in = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['transfer_in'];
												$today_transfer_in = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['today_transfer_in'];
												$total_receive = $val["recv"] + $issue_ret + $transfer_in;

												$issue = $iss_qty_arr[$job_no][$PartConstColor]["issue"];
												$today_issue = $iss_qty_arr[$job_no][$PartConstColor]["today_issue"];

												$total_issue = $issue+$trans_out;
												$stock_qty = $total_receive-$total_issue;

												$stock_qty=number_format($stock_qty,2,".","");
												$stock_qty= str_replace("-0.00", "0.00", $stock_qty) ;

												//if(($cbo_value_with==1 && $stock_qty>=0) || ($cbo_value_with==2 && $stock_qty>0))
												//if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty>0))
												//{
													$sub_today_recv += $val["today_recv"];
													$sub_today_issue_ret += $today_issue_ret;
													$sub_today_transfer_in += $today_transfer_in;
													$sub_recv += $val["recv"];
													$sub_issue_ret += $issue_ret;
													$sub_transfer_in += $transfer_in;
													$sub_total_receive += $total_receive;

													$sub_today_issue += $today_issue;
													$sub_today_trans_out += $today_trans_out;

													$sub_issue += $issue;
													$sub_trans_out += $trans_out;
													$sub_total_issue += $total_issue;
													$sub_stock_qty += $stock_qty;

													$grand_today_recv += $val["today_recv"];
													$grand_today_issue_ret += $today_issue_ret;
													$grand_today_transfer_in += $today_transfer_in;
													$grand_recv += $val["recv"];
													$grand_issue_ret += $issue_ret;
													$grand_transfer_in += $transfer_in;
													$grand_total_receive += $total_receive;

													$grand_today_issue += $today_issue;
													$grand_today_trans_out += $today_trans_out;

													$grand_issue += $issue;
													$grand_trans_out += $trans_out;
													$grand_total_issue += $total_issue;
													$grand_stock_qty += $stock_qty;

													$max_issue_date = $iss_qty_arr[$job_no][$PartConstColor]["max_issue_date"];
													$min_date = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]["min_date"];
													$doh="";
													if($min_date != "")
													{
														$doh = date_diff($min_date,date("Y-m-d"));

														$date1=date_create(date("Y-m-d",$min_date));
														$date2=date_create(date("Y-m-d"));
														$diff=date_diff($date1,$date2);

														$doh = $diff->format("%a");
													}
													
													$po_ids_ref = implode(",",array_unique(explode(",",chop($popup_job_ref[$job_no],","))));
													if($y%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $y;?>','<? echo $bgcolor1;?>')" id="trr<? echo $y;?>">
														<td width="110" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
														<td width="110" title="<? echo $deter_id;?>"><? echo $constuction_arr[$deter_id];?></td>
														<td width="105" title="<? echo $color_id;?>"><p><? echo $color_names; ?>&nbsp;</p></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','today_recv_popup_summary')"><? echo number_format($val["today_recv"],2);?></a></td>
														<td width="90" align="right"><? echo number_format($today_issue_ret,2);?></td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','today_trans_in_popup_summary')">
																<? echo number_format($today_transfer_in,2);?>
															</a></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
														<td width="90" align="right"><? echo number_format($issue_ret,2);?></td>
														<td width="90" align="right">
															<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','trans_in_popup_summary')">
																<? echo number_format($transfer_in,2);?>
															</a>
														</td>
														<td width="90" align="right"><? echo number_format($total_receive,2);?></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','today_issue_popup_summary')"><? echo number_format($today_issue,2);?></a></td>
														<td width="90">&nbsp;</td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','today_trans_to_popup_summary')"><? echo number_format($today_trans_out,2);?></a></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
														<td width="90">&nbsp;</td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','trans_to_popup_summary')"><? echo number_format($trans_out,2);?></a></td>
														<td width="90" align="right"><? echo number_format($total_issue,2);?></td>
														<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','0','stock_popup_summary')"><? echo number_format($stock_qty,2);?></a></td>

														<td width="105" align="center"><? echo change_date_format($max_issue_date);?></td>
														<td width="50" align="center"><? echo $doh;?></td>
													</tr>
													<?
													$y++;
												//}
											}
										}
										?>
									</table>
								</td>
							</tr>

							<tr>
								<td width="955" style='padding:0px 1px 0px 0px'>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
											<tr>
												<th width="40">&nbsp;</th>
												<th width="70"><p>&nbsp;</p></th>
												<th width="90"><p>&nbsp;</p></th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="70"><p>&nbsp;</p></th>
												<th width="80"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="105"><p>Job Total</p></th>
												<th width="80"><p><? echo number_format($sub_job_total,2);?></p></th>
											</tr>
										</tfoot>
									</table>
								</td>

								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
											<tr>
												<th width="110">&nbsp;</th>
												<th width="110">&nbsp;</th>
												<th width="105"><p>&nbsp;</p></th>

												<th width="90"><? echo number_format($sub_today_recv,2);?></th>
												<th width="90"><? echo number_format($sub_today_issue_ret,2);?></th>
												<th width="90"><? echo number_format($sub_today_transfer_in,2);?></th>

												<th width="90"><? echo number_format($sub_recv,2);?></th>
												<th width="90"><? echo number_format($sub_issue_ret,2);?></th>
												<th width="90"><? echo number_format($sub_transfer_in,2);?></th>
												<th width="90"><? echo number_format($sub_total_receive,2);?></th>

												<th width="90"><? echo number_format($sub_today_issue,2);?></th>
												<th width="90">&nbsp;</th>
												<th width="90"><? echo number_format($sub_today_trans_out,2);?></th>

												<th width="90"><? echo number_format($sub_issue,2);?></th>
												<th width="90">&nbsp;</th>
												<th width="90"><? echo number_format($sub_trans_out,2);?></th>
												<th width="90"><? echo number_format($sub_total_issue,2);?></th>
												<th width="105"><? echo number_format($sub_stock_qty,2);?></th>
												<th width="105"></th>
												<th width="50"></th>
											</tr>
										</tfoot>
									</table>
								</td>
							</tr>
							
							<?
							$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=$sub_job_total=0;
						}

					}
				}
				?>
			</table>
		</div>
		<table width="2800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<tr>
				<td width="955" style='padding:0px 1px 0px 0px'>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="40">&nbsp;</th>
								<th width="70"><p>&nbsp;</p></th>
								<th width="90"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="70"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="105"><p>Grand Total</p></th>
								<th width="80"><p><? echo number_format($grand_job_total,2);?></p></th>
							</tr>
						</tfoot>
					</table>
				</td>

				<td>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="110">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="105"><p>&nbsp;</p></th>

								<th width="90"><? echo number_format($grand_today_recv,2);?></th>
								<th width="90"><? echo number_format($grand_today_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_today_transfer_in,2);?></th>

								<th width="90"><? echo number_format($grand_recv,2);?></th>
								<th width="90"><? echo number_format($grand_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_transfer_in,2);?></th>
								<th width="90"><? echo number_format($grand_total_receive,2);?></th>

								<th width="90"><? echo number_format($grand_today_issue,2);?></th>
								<th width="90">&nbsp;</th>
								<th width="90"><? echo number_format($grand_today_trans_out,2);?></th>

								<th width="90"><? echo number_format($grand_issue,2);?></th>
								<th width="90">&nbsp;</th>
								<th width="90"><? echo number_format($grand_trans_out,2);?></th>
								<th width="90"><? echo number_format($grand_total_issue,2);?></th>
								<th width="105"><? echo number_format($grand_stock_qty,2);?></th>
								<th width="105"></th>
								<th width="50"></th>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
		</table>
	</fieldset>
	<?

	$html = ob_get_contents();
	ob_clean();
	
	foreach (glob("*.xls") as $filename) 
	{
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
if($action=="report_generate2_newBtn")
{
	ini_set('session.gc_maxlifetime', 3600); 
	session_start();
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
	$cbo_value_with=str_replace("'","",$cbo_value_with);
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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));

	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	$cbo_string_search_type=str_replace("'","",$cbo_string_search_type);
	if($cbo_string_search_type==1)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) = LOWER('".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";
		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no = '$txt_booking_no'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id = $hide_booking_id"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $bookiing_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."'"; else $bookiing_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $bookiing_id_cond=" and c.booking_id like '%".$hide_booking_id."'"; else $bookiing_id_cond="";
	}
	// echo $file_cond.'<br>'.$ref_cond.'<br>'.$bookiing_no_cond.'<br>'.$bookiing_id_cond.'<br>';

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	//if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	//if($txt_ref_no!="") $ref_cond=" and a.grouping = '".trim($txt_ref_no)."'";

	//if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	//if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="SELECT * FROM
		(select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short, sum(grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d where b.company_name=$cbo_company_id and c.booking_type=1 and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id = c.pre_cost_fabric_cost_dtls_id $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short 
		union all
		select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short, sum( c.grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_conv_cost_dtls e where b.company_name=$cbo_company_id and c.booking_type in (4) and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id = c.pre_cost_fabric_cost_dtls_id and d.id = e.fabric_description $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short) t order by grouping,fabric_color_id ";
	

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;

			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}

			$ref_file = $row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('booking_no')]."_".$row[csf('file_no')]."_".$row[csf('grouping')]."_".$row[csf('body_part_id')]."_".$row[csf('lib_yarn_count_deter_id')]."_".$row[csf('fabric_color_id')]."_".$row[csf('grey_fab_qnty')]."_".$booking_type."##";
			$poIds.=$row[csf('id')].",";
			$poIdsArr[$row[csf('id')]]=$row[csf('id')];

			$poArr[$row[csf('id')]]=$row[csf('job_no')];
			$fileRefArr[$row[csf('job_no')]].=$ref_file;
			$popup_job_ref[$row[csf('job_no')]] .= $row[csf('id')].",";

			if($row[csf('shiping_status')] != 3 && $shipStat[$row[csf('job_no')]] =="")
			{
				$shipStat[$row[csf('job_no')]] = $row[csf('shiping_status')];
			}
			
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=25 and type in (3,4)");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (25) and ref_from in (5,6,7)");
	if($r_id2)
	{
		oci_commit($con);
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 5,$poIdsArr, $empty_arr); //Order id temporary insert

	/* $poIds=chop($poIds,',');
	$poIds = implode(",",array_unique(explode(",", $poIds)));

	 $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
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
	} */

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$recvDtlsDataArr=array();

	$query="SELECT * FROM
		(select a.id, a.entry_form, a.receive_date, b.febric_description_id, b.color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction e, GBL_TEMP_ENGINE g
	WHERE a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.id=b.mst_id and b.id=c.dtls_id and e.id=b.trans_id and c.status_active=1 and c.is_deleted=0 $trans_date $store_cond_2 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and c.booking_without_order=0 and e.status_active=1 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and a.to_order_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and c.booking_without_order =0 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and nvl(c.booking_without_order,0) =0  and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and c.is_sales=0 and c.booking_without_order =0) abc order by color_id";

// sql gulate adjust kora hoise 1.$poIds_cond_roll   2.$otot_po_cond   3.$poIds_cond_roll $stst_po_cond   4.$poIds_cond_roll $otot_po_cond 
	//echo $query;//die;
	$data_array=sql_select($query);

	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];

		if($barcodeArr[$row[csf("barcode_no")]]=="")
		{
			$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

			$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 3, ".$row[csf('barcode_no')].")");
			if($r_id) 
			{
				$r_id=1;
			} 
			else 
			{
				echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 3, ".$row[csf('barcode_no')].")";
				oci_rollback($con);
				die;
			}
		}
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr))
	{
		/*
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

		*/
		$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no d where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 and b.barcode_no = d.barcode_no and d.userid=$user_id  and d.entry_form=25 and type=3");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				if($split_barcode_arr[$val[csf("barcode_no")]]=="")
				{
					$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];

					$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 4, ".$row[csf('barcode_no')].")");
					if($r_id) 
					{
						$r_id=1;
					} 
					else 
					{
						echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 4, ".$row[csf('barcode_no')].")";
						oci_rollback($con);
						die;
					}
				}
			}

			if($r_id)
			{
				oci_commit($con);
			}

			/* $split_barcode_nos = implode(",", $split_barcode_arr);
			$spBarCond = $split_barcode_no_cond = "";
			if($db_type==2 && count($split_barcode_arr)>999)
			{
				$split_barcode_arr_chunk=array_chunk($split_barcode_arr,999) ;
				foreach($split_barcode_arr_chunk as $chunk_arr)
				{
					$spBarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
				}
				$split_barcode_no_cond.=" and (".chop($spBarCond,'or ').")";
			}
			else
			{
				$split_barcode_no_cond=" and a.barcode_no in($split_barcode_nos)";
			} */

			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b, tmp_barcode_no c where a.entry_form = 62 and a.roll_split_from = b.id and a.status_active =1 and b.status_active=1 and a.barcode_no=c.barcode_no and c.userid=$user_id and c.entry_form=25 and c.type=4");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$recvDataArrTrans=array();$recvDataArr=array();

		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid=$user_id and d.entry_form=25 and d.type=3 and c.is_sales=0 order by a.entry_form desc";

		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			/* $yarn_prod_ids = implode(",", $yarn_prod_id_arr);
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
			} */
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 6,$yarn_prod_id_arr, $empty_arr);

			$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a, GBL_TEMP_ENGINE b where a.status_active = 1 and a.id=b.ref_val and b.user_id=$user_id and b.entry_form=25 and b.ref_from=6","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
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
			if($row[csf('color_id')]!="")
			{
				$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
			}
		}
		unset($recvDataT);
	}

	$all_color_ids = implode(",", $all_color_arr);
	$all_color_arr_exp=explode(",", $all_color_ids);
	$all_color_arrs = array_filter($all_color_arr_exp);
	if(!empty($all_color_arrs))
	{
		/* $all_color_ids = implode(",", $all_color_arrs);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arrs)>999)
		{
			$all_color_chunk=array_chunk($all_color_arrs,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$colorCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_color_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_cond=" and id in($all_color_ids)";
		} */
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 7,$all_color_arrs, $empty_arr);
		$color_arr=return_library_array( "select a.id, a.color_name from lib_color a, GBL_TEMP_ENGINE b  where a.status_active=1 and a.id=b.ref_val and b.entry_form=25 and b.ref_from=7 and b.user_id=$user_id", "id", "color_name" ); //$all_color_cond
	}

	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$ref_febric_description_arr = array_filter($ref_febric_description_arr);

	if(!empty($ref_febric_description_arr))
	{
		/* $ref_febric_description_ids = implode(",", $ref_febric_description_arr);
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
		} */
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

	$transfer_out_sql=sql_select("SELECT d.transfer_date, a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d, GBL_TEMP_ENGINE g where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $store_cond_3 and a.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and c.booking_without_order =0 and b.mst_id = d.id and d.entry_form=83 and c.is_sales=0 
		union all
		select a.transfer_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $store_cond_3 and b.from_order_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1  and nvl(c.booking_without_order,0) =0 and c.is_sales=0  
		group by a.transfer_date, c.barcode_no, b.from_order_id
		union all
		select a.transfer_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $store_cond_3 and a.from_order_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 and c.is_sales=0
		group by a.transfer_date, c.barcode_no, a.from_order_id ");

	$ref_file="";$data_prod=""; $trans_out_barcode_arr = array();
	foreach($transfer_out_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$trans_out_qty_arr[$ref_file][$data_prod]["trans_out"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('transfer_date')]))
		{
			$trans_out_qty_arr[$ref_file][$data_prod]["today_trans_out"] +=$row[csf("qnty")];
		}
	}

	unset($transfer_out_sql);
	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty, d.issue_date, max(d.issue_date) max_issue_date,d.issue_number,c.id from pro_roll_details c, inv_issue_master d ,inv_grey_fabric_issue_dtls b, inv_transaction e, GBL_TEMP_ENGINE g where c.mst_id = d.id and d.id=b.mst_id and b.trans_id=e.id and b.id=c.dtls_id  and d.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $store_cond_2 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and c.booking_without_order = 0 and d.id = e.mst_id and e.transaction_type=2 and e.item_category=13 and e.status_active =1 and c.is_sales=0 group by c.po_breakdown_id,c.barcode_no,c.qnty,d.issue_date,d.issue_number,c.id");

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();
	foreach($iss_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$data_prod = $recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"]."**".$recvDataArrTrans[$mother_barcode_no]["color_id"];
		}
		$iss_qty_arr[$ref_file][$data_prod]["issue"] +=$row[csf("qnty")];
		$iss_qty_arr[$ref_file][$data_prod]["barcode_no"].=$row[csf("barcode_no")]."=".$row[csf("qnty")].",";

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('issue_date')])){
			$iss_qty_arr[$ref_file][$data_prod]["today_issue"] +=$row[csf("qnty")];
		}

		$iss_qty_arr[$ref_file][$data_prod]["max_issue_date"] = $row[csf('max_issue_date')];
	}
	
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($iss_qty_arr);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();

	$iss_rtn_qty_sql=sql_select("SELECT d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty  from pro_roll_details c, inv_receive_master d, inv_transaction e,pro_grey_prod_entry_dtls f, GBL_TEMP_ENGINE g where c.entry_form=84 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0  and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and f.status_active =1 and f.is_deleted=0 $store_cond_2 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=5 and c.is_sales=0");
	foreach($iss_rtn_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
		{
			$iss_rtn_qty_arr[$ref_file][$data_prod]["today_issue_ret"]+=$row[csf("qnty")];
		}
	}
	unset($iss_rtn_qty_sql);

	$ref_file="";$data_prod="";$min_date="";
	foreach($data_array as $row)
	{
		if( $row[csf("type")]==2)
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['transfer_in']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
			}
		}
		else
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['recv']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['today_recv']+=$row[csf("qnty")];
			}
		}

		if($recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"] =="")
		{
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
		else if($recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"] > strtotime($row[csf("receive_date")]))
		{
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
	}
	unset($data_array);


	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (25) and ref_from in (5,6,7)");
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form in (25) and type in (3,4)");
	oci_commit($con);
	disconnect($con);

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
		<table width="2800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="11" style='padding:0px 1px 0px 0px'>Booking Info</th>
					<th colspan="20">Receive Info</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="70" rowspan="2">Buyer</th>
					<th width="90" rowspan="2">Job No</th>
					<th width="100" rowspan="2">Booking No</th>
					<th width="100" rowspan="2">Booking Type</th>
					<th width="70" rowspan="2">File No</th>
					<th width="80" rowspan="2">Ref. No</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th width="80" rowspan="2">Grey Fabric Qty(Kg)</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th colspan="3">Today Receive</th>
					<th colspan="4">Total Receive</th>
					<th colspan="3">Today Issue</th>
					<th colspan="4">Total Issue</th>
					<th width="105" rowspan="2">Stock Qty.</th>
					<th width="105" rowspan="2">Last Issued Date</th>
					<th width="50" rowspan="2">DOH</th>
				</tr>
				<tr>
					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer From</th>

					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer From</th>
					<th width="90">Grand Total</th>

					<th width="90">Issue</th>
					<th width="90">Receive Return</th>
					<th width="90">Transfer To</th>

					<th width="90">Issue</th>
					<th width="90">Receive Return</th>
					<th width="90">Transfer To</th>
					<th width="90">Grand Total</th>
				</tr>
			</thead>
		</table>
		<div style="width:2820px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					$stock_qty_smry_arr[$job_no]=0;
					if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
					{
						foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
						{
							
							foreach ($colorData as $PartConstColor => $val)
							{
								$issue_ret_smry = $iss_rtn_qty_arr[$job_no][$PartConstColor]["issue_ret"];
								$transfer_in_smry = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['transfer_in'];

								$issue_smry = $iss_qty_arr[$job_no][$PartConstColor]["issue"];
								$trans_out_smry = $trans_out_qty_arr[$job_no][$PartConstColor]["trans_out"];

								$total_issue_smry = $issue_smry+$trans_out_smry;
								$total_receive_smry = $val["recv"] + $issue_ret_smry + $transfer_in_smry;
								$stock_qty_smry = $total_receive_smry-$total_issue_smry;
								$stock_qty_smry = number_format($stock_qty_smry,2,".","");

								//if(($cbo_value_with==1 && $stock_qty_smry>=0) || ($cbo_value_with==2 && $stock_qty_smry>0))
								if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty_smry>0))
								{
									$stock_qty_smry_arr[$job_no]=1;
								}	
							}
						}
					}
				}
				$i=1;$y=1;
				$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=0;
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					if ($stock_qty_smry_arr[$job_no]>0) 
					{

						if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
						{
							$fileRefData=explode("##",chop($fileRefArrData,"##"));
							$row_count = count($fileRefData);

							$c=1;
							?>
							<tr>
								<td width="955" style='padding:0px 1px 0px 0px'>
									<table cellpadding="0" cellspacing="0" border="0">
										<?
										for ($r=0; $r < $row_count; $r++) 
										{ 
											if(isset($fileRefData[$r]))
											{
												$fileRefDataDtls = explode("_", $fileRefData[$r]);
												$buyer_id=$fileRefDataDtls[0];
												$job_number=$fileRefDataDtls[1];
												$bookingNo=$fileRefDataDtls[2];
												$fileNo=$fileRefDataDtls[3];
												$refNo=$fileRefDataDtls[4];
												$body_part_id=$fileRefDataDtls[5];
												$deter_id=$fileRefDataDtls[6];
												$fabric_color_id=$fileRefDataDtls[7];
												$grey_qnty=$fileRefDataDtls[8];
												$booking_type=$fileRefDataDtls[9];

												$sub_job_total += $grey_qnty;
												$grand_job_total += $grey_qnty;

												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>

												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="40"><? echo $c; ?></td>
													<td width="70"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
													<td width="90"><p><? echo $job_number; ?>&nbsp;</p></td>
													<td width="100"><p><? echo $bookingNo; ?>&nbsp;</p></td>
													<td width="100"><p><? echo $booking_type; ?>&nbsp;</p></td>
													<td width="70"><p><? echo $fileNo; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $refNo; ?>&nbsp;</p></td>
													<td width="110"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
													<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
													<td width="105"><p><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($grey_qnty,2,'.',''); ?>&nbsp;</p></td>
												</tr>
												<?
											}
											else
											{
												?>
												<tr>
													<td width="40"></td>
													<td width="70"><p>&nbsp;</p></td>
													<td width="90"><p>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="70"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="110"><p>&nbsp;</p></td>
													<td width="110"><p>&nbsp;</p></td>
													<td width="105"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
												</tr>
												<?
											}
											$i++;$c++;
										}
										?>
									</table>
								</td>

								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<?
										foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
										{
											foreach ($colorData as $PartConstColor => $val)
											{
												$PartConstColorArr = explode("**", $PartConstColor);
												$deter_id = $PartConstColorArr[0];
												$body_part_id = $PartConstColorArr[1];
												$color_id = $PartConstColorArr[2];

												$color_names="";
												$color_ids = explode(",", $color_id);
												foreach ($color_ids as $color) 
												{
													$color_names .=  $color_arr[$color].",";
												}
												$color_names = chop($color_names,",");

												$trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["trans_out"];
												$today_trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["today_trans_out"];

												$issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["issue_ret"];
												$today_issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["today_issue_ret"];

												$transfer_in = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['transfer_in'];
												$today_transfer_in = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['today_transfer_in'];
												$total_receive = $val["recv"] + $issue_ret + $transfer_in;

												$issue = $iss_qty_arr[$job_no][$PartConstColor]["issue"];
												$today_issue = $iss_qty_arr[$job_no][$PartConstColor]["today_issue"];

												$total_issue = $issue+$trans_out;
												$stock_qty = $total_receive-$total_issue;

												$stock_qty=number_format($stock_qty,2,".","");
												$stock_qty= str_replace("-0.00", "0.00", $stock_qty) ;

												//if(($cbo_value_with==1 && $stock_qty>=0) || ($cbo_value_with==2 && $stock_qty>0))
												//if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty>0))
												//{
													$sub_today_recv += $val["today_recv"];
													$sub_today_issue_ret += $today_issue_ret;
													$sub_today_transfer_in += $today_transfer_in;
													$sub_recv += $val["recv"];
													$sub_issue_ret += $issue_ret;
													$sub_transfer_in += $transfer_in;
													$sub_total_receive += $total_receive;

													$sub_today_issue += $today_issue;
													$sub_today_trans_out += $today_trans_out;

													$sub_issue += $issue;
													$sub_trans_out += $trans_out;
													$sub_total_issue += $total_issue;
													$sub_stock_qty += $stock_qty;

													$grand_today_recv += $val["today_recv"];
													$grand_today_issue_ret += $today_issue_ret;
													$grand_today_transfer_in += $today_transfer_in;
													$grand_recv += $val["recv"];
													$grand_issue_ret += $issue_ret;
													$grand_transfer_in += $transfer_in;
													$grand_total_receive += $total_receive;

													$grand_today_issue += $today_issue;
													$grand_today_trans_out += $today_trans_out;

													$grand_issue += $issue;
													$grand_trans_out += $trans_out;
													$grand_total_issue += $total_issue;
													$grand_stock_qty += $stock_qty;

													$max_issue_date = $iss_qty_arr[$job_no][$PartConstColor]["max_issue_date"];
													$min_date = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]["min_date"];
													$doh="";
													if($min_date != "")
													{
														$doh = date_diff($min_date,date("Y-m-d"));

														$date1=date_create(date("Y-m-d",$min_date));
														$date2=date_create(date("Y-m-d"));
														$diff=date_diff($date1,$date2);

														$doh = $diff->format("%a");
													}
													
													$po_ids_ref = implode(",",array_unique(explode(",",chop($popup_job_ref[$job_no],","))));
													if($y%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $y;?>','<? echo $bgcolor1;?>')" id="trr<? echo $y;?>">
														<td width="110" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
														<td width="110" title="<? echo $deter_id;?>"><? echo $constuction_arr[$deter_id];?></td>
														<td width="105" title="<? echo $color_id;?>"><p><? echo $color_names; ?>&nbsp;</p></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','today_recv_popup_summary')"><? echo number_format($val["today_recv"],2);?></a></td>
														<td width="90" align="right"><? echo number_format($today_issue_ret,2);?></td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','today_trans_in_popup_summary')">
																<? echo number_format($today_transfer_in,2);?>
															</a></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
														<td width="90" align="right"><? echo number_format($issue_ret,2);?></td>
														<td width="90" align="right">
															<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','trans_in_popup_summary')">
																<? echo number_format($transfer_in,2);?>
															</a>
														</td>
														<td width="90" align="right"><? echo number_format($total_receive,2);?></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','today_issue_popup_summary')"><? echo number_format($today_issue,2);?></a></td>
														<td width="90">&nbsp;</td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','today_trans_to_popup_summary')"><? echo number_format($today_trans_out,2);?></a></td>

														<td width="90" align="right" title="<? echo $iss_qty_arr[$job_no][$PartConstColor]["barcode_no"]; ?>"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
														<td width="90">&nbsp;</td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','trans_to_popup_summary')"><? echo number_format($trans_out,2);?></a></td>
														<td width="90" align="right"><? echo number_format($total_issue,2);?></td>
														<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','0','stock_popup_summary_2')"><? echo number_format($stock_qty,2);?></a></td>

														<td width="105" align="center"><? echo change_date_format($max_issue_date);?></td>
														<td width="50" align="center"><? echo $doh;?></td>
													</tr>
													<?
													$y++;
												//}
											}
										}
										?>
									</table>
								</td>
							</tr>

							<tr>
								<td width="955" style='padding:0px 1px 0px 0px'>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
											<tr>
												<th width="40">&nbsp;</th>
												<th width="70"><p>&nbsp;</p></th>
												<th width="90"><p>&nbsp;</p></th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="70"><p>&nbsp;</p></th>
												<th width="80"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="105"><p>Job Total</p></th>
												<th width="80"><p><? echo number_format($sub_job_total,2);?></p></th>
											</tr>
										</tfoot>
									</table>
								</td>

								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
											<tr>
												<th width="110">&nbsp;</th>
												<th width="110">&nbsp;</th>
												<th width="105"><p>&nbsp;</p></th>

												<th width="90"><? echo number_format($sub_today_recv,2);?></th>
												<th width="90"><? echo number_format($sub_today_issue_ret,2);?></th>
												<th width="90"><? echo number_format($sub_today_transfer_in,2);?></th>

												<th width="90"><? echo number_format($sub_recv,2);?></th>
												<th width="90"><? echo number_format($sub_issue_ret,2);?></th>
												<th width="90"><? echo number_format($sub_transfer_in,2);?></th>
												<th width="90"><? echo number_format($sub_total_receive,2);?></th>

												<th width="90"><? echo number_format($sub_today_issue,2);?></th>
												<th width="90">&nbsp;</th>
												<th width="90"><? echo number_format($sub_today_trans_out,2);?></th>

												<th width="90"><? echo number_format($sub_issue,2);?></th>
												<th width="90">&nbsp;</th>
												<th width="90"><? echo number_format($sub_trans_out,2);?></th>
												<th width="90"><? echo number_format($sub_total_issue,2);?></th>
												<th width="105"><? echo number_format($sub_stock_qty,2);?></th>
												<th width="105"></th>
												<th width="50"></th>
											</tr>
										</tfoot>
									</table>
								</td>
							</tr>
							
							<?
							$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=$sub_job_total=0;
						}

					}
				}
				?>
			</table>
		</div>
		<table width="2800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<tr>
				<td width="955" style='padding:0px 1px 0px 0px'>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="40">&nbsp;</th>
								<th width="70"><p>&nbsp;</p></th>
								<th width="90"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="70"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="105"><p>Grand Total</p></th>
								<th width="80"><p><? echo number_format($grand_job_total,2);?></p></th>
							</tr>
						</tfoot>
					</table>
				</td>

				<td>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="110">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="105"><p>&nbsp;</p></th>

								<th width="90"><? echo number_format($grand_today_recv,2);?></th>
								<th width="90"><? echo number_format($grand_today_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_today_transfer_in,2);?></th>

								<th width="90"><? echo number_format($grand_recv,2);?></th>
								<th width="90"><? echo number_format($grand_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_transfer_in,2);?></th>
								<th width="90"><? echo number_format($grand_total_receive,2);?></th>

								<th width="90"><? echo number_format($grand_today_issue,2);?></th>
								<th width="90">&nbsp;</th>
								<th width="90"><? echo number_format($grand_today_trans_out,2);?></th>

								<th width="90"><? echo number_format($grand_issue,2);?></th>
								<th width="90">&nbsp;</th>
								<th width="90"><? echo number_format($grand_trans_out,2);?></th>
								<th width="90"><? echo number_format($grand_total_issue,2);?></th>
								<th width="105"><? echo number_format($grand_stock_qty,2);?></th>
								<th width="105"></th>
								<th width="50"></th>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
		</table>
	</fieldset>
	<?

	$html = ob_get_contents();
	ob_clean();
	
	foreach (glob("*.xls") as $filename) 
	{
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
if($action=="report_generate333xxx") //test basis
{
	ini_set('session.gc_maxlifetime', 3600); 
	session_start();
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
	$cbo_value_with=str_replace("'","",$cbo_value_with);
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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));

	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping = '".trim($txt_ref_no)."'";

	if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from.""; 


	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="SELECT * FROM
		(select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short, sum(grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id = c.pre_cost_fabric_cost_dtls_id $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short 
		union all
		select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short, sum( c.grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_conv_cost_dtls e where b.company_name=$cbo_company_id and c.booking_type in (4) and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id = c.pre_cost_fabric_cost_dtls_id and d.id = e.fabric_description $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short) t order by grouping,fabric_color_id ";
	

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;

			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}

			$ref_file = $row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('booking_no')]."_".$row[csf('file_no')]."_".$row[csf('grouping')]."_".$row[csf('body_part_id')]."_".$row[csf('lib_yarn_count_deter_id')]."_".$row[csf('fabric_color_id')]."_".$row[csf('grey_fab_qnty')]."_".$booking_type."##";
			$poIds.=$row[csf('id')].",";

			$poArr[$row[csf('id')]]=$row[csf('job_no')];
			$fileRefArr[$row[csf('job_no')]].=$ref_file;
			$popup_job_ref[$row[csf('job_no')]] .= $row[csf('id')].",";

			if($row[csf('shiping_status')] != 3 && $shipStat[$row[csf('job_no')]] =="")
			{
				$shipStat[$row[csf('job_no')]] = $row[csf('shiping_status')];
			}
			
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$poIds=chop($poIds,',');
	$poIds = implode(",",array_unique(explode(",", $poIds)));

	 $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
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

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$recvDtlsDataArr=array();$recvDtlsDataStoreArr=array();

	$query="SELECT * FROM
		(select a.id, a.entry_form, a.receive_date, b.febric_description_id, b.color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction e
	WHERE a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.id=b.mst_id and b.id=c.dtls_id and e.id=b.trans_id and c.status_active=1 and c.is_deleted=0 $trans_date $poIds_cond_roll $store_cond_2 and c.booking_without_order=0 and e.status_active=1 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $otot_po_cond $store_cond_1 and c.booking_without_order =0 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $stst_po_cond $store_cond_1 and nvl(c.booking_without_order,0) =0  and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $otot_po_cond $store_cond_1 and c.is_sales=0 and c.booking_without_order =0) abc order by color_id";

	echo $query;//die;
	$data_array=sql_select($query);
	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
	if($r_id2)
	{
		oci_commit($con);
	}

	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];

		$r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")");
		if($r_id) 
		{
			$r_id=1;
		} 
		else 
		{
			echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")";
			oci_rollback($con);
			die;
		}
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr))
	{
		/*
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

		*/
		$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no d where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 and b.barcode_no = d.barcode_no and d.userid=$user_id");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
			}

			$split_barcode_nos = implode(",", $split_barcode_arr);
			$spBarCond = $split_barcode_no_cond = "";
			if($db_type==2 && count($split_barcode_arr)>999)
			{
				$split_barcode_arr_chunk=array_chunk($split_barcode_arr,999) ;
				foreach($split_barcode_arr_chunk as $chunk_arr)
				{
					$spBarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
				}
				$split_barcode_no_cond.=" and (".chop($spBarCond,'or ').")";
			}
			else
			{
				$split_barcode_no_cond=" and a.barcode_no in($split_barcode_nos)";
			}
			//$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 $split_barcode_no_cond and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 62 $split_barcode_no_cond and a.roll_split_from = b.id and a.status_active =1 and b.status_active=1");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$recvDataArrTrans=array();$recvDataArr=array();

		/*$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond";*/

		/*$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid=$user_id ";*/


		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id,a.store_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid=$user_id and c.is_sales=0 order by a.entry_form desc";

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
			$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"]=$row[csf('store_id')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			if($row[csf('color_id')]!="")
			{
				$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
			}
		}
		unset($recvDataT);
	}

	$all_color_ids = implode(",", $all_color_arr);
	$all_color_arr_exp=explode(",", $all_color_ids);
	$all_color_arrs = array_filter($all_color_arr_exp);
	if(!empty($all_color_arrs))
	{
		$all_color_ids = implode(",", $all_color_arrs);
		$colorCond = $all_color_cond = "";
		if($db_type==2 && count($all_color_arrs)>999)
		{
			$all_color_chunk=array_chunk($all_color_arrs,999) ;
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

	$transfer_out_sql=sql_select("select d.transfer_date, a.po_breakdown_id, c.barcode_no, c.qnty,b.from_store as store_id from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $poIds_cond_trans_roll $store_cond_3 and c.booking_without_order =0 and b.mst_id = d.id and d.entry_form=83 and c.is_sales=0 
		union all
		select a.transfer_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store as store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $ctct_po_cond $store_cond_3 and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1  and nvl(c.booking_without_order,0) =0 and c.is_sales=0  
		group by a.transfer_date, c.barcode_no, b.from_order_id,b.from_store
		union all
		select a.transfer_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store as store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $otst_po_cond $store_cond_3 and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 and c.is_sales=0
		group by a.transfer_date, c.barcode_no, a.from_order_id,b.from_store ");

	$ref_file="";$data_prod=""; $trans_out_barcode_arr = array();
	foreach($transfer_out_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$trans_out_qty_arr[$ref_file][$data_prod]["trans_out"] +=$row[csf("qnty")];
		$trans_outStore_qty_arr[$ref_file][$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]][$row[csf('store_id')]][$data_prod]["trans_out"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('transfer_date')]))
		{
			$trans_out_qty_arr[$ref_file][$data_prod]["today_trans_out"] +=$row[csf("qnty")];
		}
	}

	unset($transfer_out_sql);
	$iss_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty, d.issue_date, max(d.issue_date) max_issue_date,d.issue_number,c.id,b.color_id,b.store_name from pro_roll_details c, inv_issue_master d ,inv_grey_fabric_issue_dtls b, inv_transaction e where c.mst_id = d.id and d.id=b.mst_id and b.trans_id=e.id and b.id=c.dtls_id  and d.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll $store_cond_2 and c.booking_without_order = 0 and d.id = e.mst_id and e.transaction_type=2 and e.item_category=13 and e.status_active =1 and c.is_sales=0 group by c.po_breakdown_id,c.barcode_no,c.qnty,d.issue_date,d.issue_number,c.id,b.color_id,b.store_name");

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();
	foreach($iss_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$data_prod = $recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"]."**".$recvDataArrTrans[$mother_barcode_no]["color_id"];
		}
		$iss_qty_arr[$ref_file][$data_prod]["issue"] +=$row[csf("qnty")];
		$iss_qtyStore_arr[$ref_file][$row[csf("color_id")]][$row[csf("store_name")]][$data_prod]["issue"] +=$row[csf("qnty")];
		$iss_qty_arr[$ref_file][$data_prod]["barcode_no"].=$row[csf("barcode_no")]."=".$row[csf("qnty")].",";

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('issue_date')])){
			$iss_qty_arr[$ref_file][$data_prod]["today_issue"] +=$row[csf("qnty")];
		}

		$iss_qty_arr[$ref_file][$data_prod]["max_issue_date"] = $row[csf('max_issue_date')];
	}
	
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($iss_qty_arr);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	//$iss_rtn_qty_sql=sql_select("select d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c, inv_receive_master d, inv_transaction e where c.entry_form=84 and c.mst_id = d.id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0 and d.id = e.mst_id and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 $poIds_cond_roll $store_cond_2");
	$iss_rtn_qty_sql=sql_select("select d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty,e.store_id,f.color_id  from pro_roll_details c, inv_receive_master d, inv_transaction e,pro_grey_prod_entry_dtls f where c.entry_form=84 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0  and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and f.status_active =1 and f.is_deleted=0  $poIds_cond_roll $store_cond_2 and c.is_sales=0");
	foreach($iss_rtn_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		$iss_rtn_qty_store_arr[$ref_file][$row[csf('color_id')]][$row[csf('store_id')]][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
		{
			$iss_rtn_qty_arr[$ref_file][$data_prod]["today_issue_ret"]+=$row[csf("qnty")];
		}
	}
	unset($iss_rtn_qty_sql);

	$ref_file="";$data_prod="";$min_date="";$data_prod_store="";
	foreach($data_array as $row)
	{
		if( $row[csf("type")]==2)
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$store_index=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['transfer_in']+=$row[csf("qnty")];
			$recvDtlsDataStoreArr[$ref_file][$color_index][$store_index][$data_prod]['transfer_in']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
				$recvDtlsDataStoreArr[$ref_file][$color_index][$store_index][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
			}
		}
		else
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];
			$store_index=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];

			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['recv']+=$row[csf("qnty")];
			$recvDtlsDataStoreArr[$ref_file][$color_index][$store_index][$data_prod]['recv']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$color_index][$data_prod]['today_recv']+=$row[csf("qnty")];
			}
		}

		if($recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"] =="")
		{
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
		else if($recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"] > strtotime($row[csf("receive_date")]))
		{
			$recvDtlsDataArr[$ref_file][$color_index][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
	}
	unset($data_array);

	ob_start();
	$width = (2620+($num_of_store*110));
	?>
	<fieldset style="width:2200px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table style="width:<? echo $width+20; ?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="9" style='padding:0px 1px 0px 0px'>Booking Info</th>
					<th colspan="<? echo 20+$num_of_store; ?>">Receive Info</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="70" rowspan="2">Buyer</th>
					<th width="90" rowspan="2">Job No</th>
					<th width="100" rowspan="2">Booking No</th>
					<th width="100" rowspan="2">Booking Type</th>
					<th width="70" rowspan="2">File No</th>
					<th width="80" rowspan="2">Ref. No</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th width="80" rowspan="2">Grey Fabric Qty(Kg)</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th colspan="3">Today Receive</th>
					<th colspan="4">Total Receive</th>
					<th rowspan="2" width="105">Received Balance</th>
					<th colspan="2">Today Issue</th>
					<th colspan="3">Total Issue</th>
					<th width="105" rowspan="2">Stock Qty.</th>

					<?
					foreach ($stores as $store) {
						?>
						<th rowspan="2" width="110" title="<? echo $store[csf("id")];?>"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
					<th width="50" rowspan="2">DOH</th>
				</tr>
				<tr>
					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer In</th>

					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer In</th>
					<th width="90">Grand Total</th>

					<th width="90">Issue</th>
					<th width="90">Transfer Out</th>

					<th width="90">Issue</th>
					<th width="90">Transfer Out</th>
					<th width="90">Grand Total</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table style="width:<? echo $width+20; ?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					$stock_qty_smry_arr[$job_no]=0;
					if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
					{
						foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
						{
							
							foreach ($colorData as $PartConstColor => $val)
							{
								$issue_ret_smry = $iss_rtn_qty_arr[$job_no][$PartConstColor]["issue_ret"];
								$transfer_in_smry = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['transfer_in'];

								$issue_smry = $iss_qty_arr[$job_no][$PartConstColor]["issue"];
								$trans_out_smry = $trans_out_qty_arr[$job_no][$PartConstColor]["trans_out"];

								$total_issue_smry = $issue_smry+$trans_out_smry;
								$total_receive_smry = $val["recv"] + $issue_ret_smry + $transfer_in_smry;
								$stock_qty_smry = $total_receive_smry-$total_issue_smry;
								$stock_qty_smry = number_format($stock_qty_smry,2,".","");

								//if(($cbo_value_with==1 && $stock_qty_smry>=0) || ($cbo_value_with==2 && $stock_qty_smry>0))
								if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty_smry>0))
								{
									$stock_qty_smry_arr[$job_no]=1;
								}	
							}
						}
					}
				}
				$i=1;$y=1;
				$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_total_received_balance=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=0;
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					if ($stock_qty_smry_arr[$job_no]>0) 
					{

						if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
						{
							$fileRefData=explode("##",chop($fileRefArrData,"##"));
							$row_count = count($fileRefData);

							$c=1;
							?>
							<tr>
								<td width="955" style='padding:0px 1px 0px 0px'>
									<table cellpadding="0" cellspacing="0" border="0">
										<?
										for ($r=0; $r < $row_count; $r++) 
										{ 
											if(isset($fileRefData[$r]))
											{
												$fileRefDataDtls = explode("_", $fileRefData[$r]);
												$buyer_id=$fileRefDataDtls[0];
												$job_number=$fileRefDataDtls[1];
												$bookingNo=$fileRefDataDtls[2];
												$fileNo=$fileRefDataDtls[3];
												$refNo=$fileRefDataDtls[4];
												$body_part_id=$fileRefDataDtls[5];
												$deter_id=$fileRefDataDtls[6];
												$fabric_color_id=$fileRefDataDtls[7];
												$grey_qnty=$fileRefDataDtls[8];
												$booking_type=$fileRefDataDtls[9];

												$sub_job_total += $grey_qnty;
												$grand_job_total += $grey_qnty;

												$greyQntyArr[$body_part_id][$deter_id][$fabric_color_id]=$grey_qnty;

												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>

												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="40"><? echo $c; ?></td>
													<td width="70"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
													<td width="90"><p><? echo $job_number; ?>&nbsp;</p></td>
													<td width="100"><p><? echo $bookingNo; ?>&nbsp;</p></td>
													<td width="100"><p><? echo $booking_type; ?>&nbsp;</p></td>
													<td width="70"><p><? echo $fileNo; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $refNo; ?>&nbsp;</p></td>
													<td width="110"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
													<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
													<td width="105"><p><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($grey_qnty,2,'.',''); ?>&nbsp;</p></td>
												</tr>
												<?
											}
											else
											{
												?>
												<tr>
													<td width="40"></td>
													<td width="70"><p>&nbsp;</p></td>
													<td width="90"><p>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="70"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="110"><p>&nbsp;</p></td>
													<td width="110"><p>&nbsp;</p></td>
													<td width="105"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
												</tr>
												<?
											}
											$i++;$c++;
										}
										?>
									</table>
								</td>

								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<?
										foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
										{
											foreach ($colorData as $PartConstColor => $val)
											{
												$PartConstColorArr = explode("**", $PartConstColor);
												$deter_id = $PartConstColorArr[0];
												$body_part_id = $PartConstColorArr[1];
												$color_id = $PartConstColorArr[2];

												$color_names="";
												$color_ids = explode(",", $color_id);
												foreach ($color_ids as $color) 
												{
													$color_names .=  $color_arr[$color].",";
												}
												$color_names = chop($color_names,",");

												$trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["trans_out"];
												$today_trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["today_trans_out"];

												$issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["issue_ret"];
												$today_issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["today_issue_ret"];

												$transfer_in = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['transfer_in'];
												$today_transfer_in = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]['today_transfer_in'];
												$total_receive = $val["recv"] + $issue_ret + $transfer_in;
												$totalRecevBalance=$greyQntyArr[$body_part_id][$deter_id][$colorIndex]-$total_receive;

												$issue = $iss_qty_arr[$job_no][$PartConstColor]["issue"];
												$today_issue = $iss_qty_arr[$job_no][$PartConstColor]["today_issue"];

												$total_issue = $issue+$trans_out;
												$stock_qty = $total_receive-$total_issue;

												$stock_qty=number_format($stock_qty,2,".","");
												$stock_qty= str_replace("-0.00", "0.00", $stock_qty) ;

												//if(($cbo_value_with==1 && $stock_qty>=0) || ($cbo_value_with==2 && $stock_qty>0))
												//if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty>0))
												//{
													$sub_today_recv += $val["today_recv"];
													$sub_today_issue_ret += $today_issue_ret;
													$sub_today_transfer_in += $today_transfer_in;
													$sub_recv += $val["recv"];
													$sub_issue_ret += $issue_ret;
													$sub_transfer_in += $transfer_in;
													$sub_total_receive += $total_receive;
													$sub_total_received_balance += $totalRecevBalance;

													$sub_today_issue += $today_issue;
													$sub_today_trans_out += $today_trans_out;

													$sub_issue += $issue;
													$sub_trans_out += $trans_out;
													$sub_total_issue += $total_issue;
													$sub_stock_qty += $stock_qty;

													$grand_today_recv += $val["today_recv"];
													$grand_today_issue_ret += $today_issue_ret;
													$grand_today_transfer_in += $today_transfer_in;
													$grand_recv += $val["recv"];
													$grand_issue_ret += $issue_ret;
													$grand_transfer_in += $transfer_in;
													$grand_total_receive += $total_receive;
													$grand_total_received_balance += $totalRecevBalance;

													$grand_today_issue += $today_issue;
													$grand_today_trans_out += $today_trans_out;

													$grand_issue += $issue;
													$grand_trans_out += $trans_out;
													$grand_total_issue += $total_issue;
													$grand_stock_qty += $stock_qty;

													$max_issue_date = $iss_qty_arr[$job_no][$PartConstColor]["max_issue_date"];
													$min_date = $recvDtlsDataArr[$job_no][$colorIndex][$PartConstColor]["min_date"];
													$doh="";
													if($min_date != "")
													{
														$doh = date_diff($min_date,date("Y-m-d"));

														$date1=date_create(date("Y-m-d",$min_date));
														$date2=date_create(date("Y-m-d"));
														$diff=date_diff($date1,$date2);

														$doh = $diff->format("%a");
													}
													
													$po_ids_ref = implode(",",array_unique(explode(",",chop($popup_job_ref[$job_no],","))));
													if($y%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $y;?>','<? echo $bgcolor1;?>')" id="trr<? echo $y;?>">
														<td width="110" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
														<td width="110" title="<? echo $deter_id;?>"><? echo $constuction_arr[$deter_id];?></td>
														<td width="105" title="<? echo $color_id;?>"><p><? echo $color_names; ?>&nbsp;</p></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','today_recv_popup_summary')"><? echo number_format($val["today_recv"],2);?></a></td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_issue_ret;?>','0','today_issue_rtn_popup_summary')"><? echo number_format($today_issue_ret,2);?></a>
														</td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_transfer_in;?>','0','today_trans_in_popup_summary')">
																<? echo number_format($today_transfer_in,2);?>
															</a></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
														<td width="90" align="right">
															<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue_ret;?>','0','issue_rtn_popup_summary')"><? echo number_format($issue_ret,2);?></a>
														</td>
														<td width="90" align="right">
															<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','trans_in_popup_summary')">
																<? echo number_format($transfer_in,2);?>
															</a>
														</td>
														<td width="90" align="right"><? echo number_format($total_receive,2);?></td>
														<td width="105" align="right"><? echo number_format($totalRecevBalance ,2);?></td>

														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','today_issue_popup_summary')"><? echo number_format($today_issue,2);?></a></td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_trans_out;?>','0','today_trans_to_popup_summary')"><? echo number_format($today_trans_out,2);?></a></td>

														<td width="90" align="right" title="<? echo $iss_qty_arr[$job_no][$PartConstColor]["barcode_no"]; ?>"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
														<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','trans_to_popup_summary')"><? echo number_format($trans_out,2);?></a></td>
														<td width="90" align="right"><? echo number_format($total_issue,2);?></td>
														<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','0','stock_popup_summary_2')"><? echo number_format($stock_qty,2);?></a></td>
														<?
														$total_receive=$total_issue=$total_issue_return=$store_wise_stock=$stock=0;

														foreach ($stores as $store) {

															foreach ($recvDtlsDataStoreArr[$job_no] as $colorIndexx => $colorData) 
															{
																foreach ($colorData as $storeIndex => $StoreData) 
																{
																	foreach ($StoreData as $PartConstColorx => $valx)
																	{
																		$PartConstColorArrx = explode("**", $PartConstColorx);
																		$deter_idx = $PartConstColorArrx[0];
																		$body_part_idx = $PartConstColorArrx[1];
																		$color_idx = $PartConstColorArrx[2];

																		$color_names="";
																		$color_ids = explode(",", $color_idx);
																		foreach ($color_ids as $color) 
																		{
																			$color_names .=  $color_arr[$color].",";
																		}
																		$color_names = chop($color_names,",");

																		$receivexQnty[$job_no][$colorIndexx][$body_part_idx][$deter_idx][$storeIndex]= $valx["recv"];
																		$transfer_inx[$job_no][$colorIndexx][$body_part_idx][$deter_idx][$storeIndex] = $recvDtlsDataStoreArr[$job_no][$colorIndexx][$storeIndex][$PartConstColorx]['transfer_in'];
																		$issue_rtn_qntyx[$job_no][$colorIndexx][$body_part_idx][$deter_idx][$storeIndex] = $iss_rtn_qty_store_arr[$job_no][$colorIndexx][$storeIndex][$PartConstColorx]["issue_ret"];

																		$issue_qntyx[$job_no][$colorIndexx][$body_part_idx][$deter_idx][$storeIndex] = $iss_qtyStore_arr[$job_no][$colorIndexx][$storeIndex][$PartConstColorx]["issue"];

																		$transferOut_qntyx[$job_no][$colorIndexx][$body_part_idx][$deter_idx][$storeIndex] =$trans_outStore_qty_arr[$job_no][$colorIndexx][$storeIndex][$PartConstColorx]["trans_out"];

																		//echo $total_receivex;
																		/*echo "<pre>";
																		print_r($total_receivex);
																		echo "</pre>";*/

																	}
																}
															}

															$totalRecvQnty=$receivexQnty[$job_no][$color_id][$body_part_id][$deter_id][$store[csf("id")]]+$transfer_inx[$job_no][$color_id][$body_part_id][$deter_id][$store[csf("id")]]+$issue_rtn_qntyx[$job_no][$color_id][$body_part_id][$deter_id][$store[csf("id")]]; 

															$totalIssueQnty=$issue_qntyx[$job_no][$color_id][$body_part_id][$deter_id][$store[csf("id")]]+$transferOut_qntyx[$job_no][$color_id][$body_part_id][$deter_id][$store[csf("id")]]; 

															?>
																<td width="110" align="right"><p><? echo $totalRecvQnty-$totalIssueQnty; ?>&nbsp;</p></td>
															<?
															
															
														}
														?>
														
														<td width="50" align="center"><? echo $doh;?></td>
													</tr>
													<?
													$y++;
												//}
											}
										}
										?>
									</table>
								</td>
							</tr>

							<tr>
								<td width="955" style='padding:0px 1px 0px 0px'>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
											<tr>
												<th width="40">&nbsp;</th>
												<th width="70"><p>&nbsp;</p></th>
												<th width="90"><p>&nbsp;</p></th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="70"><p>&nbsp;</p></th>
												<th width="80"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="105"><p>Job Total</p></th>
												<th width="80"><p><? echo number_format($sub_job_total,2);?></p></th>
											</tr>
										</tfoot>
									</table>
								</td>

								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
											<tr>
												<th width="110">&nbsp;</th>
												<th width="110">&nbsp;</th>
												<th width="105"><p>&nbsp;</p></th>

												<th width="90"><? echo number_format($sub_today_recv,2);?></th>
												<th width="90"><? echo number_format($sub_today_issue_ret,2);?></th>
												<th width="90"><? echo number_format($sub_today_transfer_in,2);?></th>

												<th width="90"><? echo number_format($sub_recv,2);?></th>
												<th width="90"><? echo number_format($sub_issue_ret,2);?></th>
												<th width="90"><? echo number_format($sub_transfer_in,2);?></th>
												<th width="90"><? echo number_format($sub_total_receive,2);?></th>
												<th width="105"><? echo number_format($sub_total_received_balance,2);?></th>

												<th width="90"><? echo number_format($sub_today_issue,2);?></th>
												<th width="90"><? echo number_format($sub_today_trans_out,2);?></th>

												<th width="90"><? echo number_format($sub_issue,2);?></th>
												<th width="90"><? echo number_format($sub_trans_out,2);?></th>
												<th width="90"><? echo number_format($sub_total_issue,2);?></th>

												<th width="105"><? echo number_format($sub_stock_qty,2);?></th>

												<?
												foreach ($stores as $store) {
													?>
													<th width="110"> </th>
													<?
												}
												?>
												
												<th width="50"></th>
											</tr>
										</tfoot>
									</table>
								</td>
							</tr>
							
							<?
							$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_total_received_balance=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=$sub_job_total=0;
						}

					}
				}
				?>
			</table>
		</div>
		<table width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<tr>
				<td width="955" style='padding:0px 1px 0px 0px'>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="40">&nbsp;</th>
								<th width="70"><p>&nbsp;</p></th>
								<th width="90"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="70"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="105"><p>Grand Total</p></th>
								<th width="80"><p><? echo number_format($grand_job_total,2);?></p></th>
							</tr>
						</tfoot>
					</table>
				</td>

				<td>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="110">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="105"><p>&nbsp;</p></th>

								<th width="90"><? echo number_format($grand_today_recv,2);?></th>
								<th width="90"><? echo number_format($grand_today_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_today_transfer_in,2);?></th>

								<th width="90"><? echo number_format($grand_recv,2);?></th>
								<th width="90"><? echo number_format($grand_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_transfer_in,2);?></th>
								<th width="90"><? echo number_format($grand_total_receive,2);?></th>
								<th width="105"><? echo number_format($grand_total_received_balance,2);?></th>

								<th width="90"><? echo number_format($grand_today_issue,2);?></th>
								<th width="90"><? echo number_format($grand_today_trans_out,2);?></th>

								<th width="90"><? echo number_format($grand_issue,2);?></th>
								<th width="90"><? echo number_format($grand_trans_out,2);?></th>
								<th width="90"><? echo number_format($grand_total_issue,2);?></th>

								<th width="105"><? echo number_format($grand_stock_qty,2);?></th>
								<?
								foreach ($stores as $store) {
									?>
									<th width="110"> </th>
									<?
								}
								?>
								
								<th width="50"></th>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
		</table>
	</fieldset>
	<?

	$html = ob_get_contents();
	ob_clean();
	
	foreach (glob("*.xls") as $filename) 
	{
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
if($action=="report_generate3")
{
	ini_set('session.gc_maxlifetime', 3600); 
	session_start();
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
	$cbo_value_with=str_replace("'","",$cbo_value_with);
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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));

	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	$cbo_string_search_type=str_replace("'","",$cbo_string_search_type);
	if($cbo_string_search_type==1)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) = LOWER('".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";
		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no = '$txt_booking_no'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id = $hide_booking_id"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."')"; else $ref_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $bookiing_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."'"; else $bookiing_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $bookiing_id_cond=" and c.booking_id like '%".$hide_booking_id."'"; else $bookiing_id_cond="";
	}
	// echo $file_cond.'<br>'.$ref_cond.'<br>'.$bookiing_no_cond.'<br>'.$bookiing_id_cond.'<br>';

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	//if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	//if($txt_ref_no!="") $ref_cond=" and a.grouping = '".trim($txt_ref_no)."'";

	//if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	//if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from.""; 


	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="SELECT * FROM
		(select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,TO_CHAR(b.insert_date,'YYYY') as year, sum(grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id = c.pre_cost_fabric_cost_dtls_id $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,b.insert_date 
		union all
		select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,TO_CHAR(b.insert_date,'YYYY') as year, sum( c.grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_conv_cost_dtls e where b.company_name=$cbo_company_id and c.booking_type in (4) and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id = c.pre_cost_fabric_cost_dtls_id and d.id = e.fabric_description $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,b.insert_date ) t order by job_no,grouping,fabric_color_id ";

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;

			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}

			$ref_file = $row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('booking_no')]."_".$row[csf('file_no')]."_".$row[csf('grouping')]."_".$row[csf('body_part_id')]."_".$row[csf('lib_yarn_count_deter_id')]."_".$row[csf('grey_fab_qnty')]."_".$booking_type."##";
			$poIds.=$row[csf('id')].",";
			$poIdsArr[$row[csf('id')]]=$row[csf('id')];

			$poArr[$row[csf('id')]]=$row[csf('job_no')];
			$fileRefArr[$row[csf('job_no')]].=$ref_file;
			$popup_job_ref[$row[csf('job_no')]] .= $row[csf('id')].",";

			if($row[csf('shiping_status')] != 3 && $shipStat[$row[csf('job_no')]] =="")
			{
				$shipStat[$row[csf('job_no')]] = $row[csf('shiping_status')];
			}

			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['buyer_name']=$row[csf('buyer_name')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['job_no']=$row[csf('job_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['booking_no']=$row[csf('booking_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['file_no']=$row[csf('file_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['grouping']=$row[csf('grouping')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['year']=$row[csf('year')];
			$bookingInfoArrReqQnty[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];

			$infoForTranserByJobWise[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['year']=$row[csf('year')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['file_no']=$row[csf('file_no')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['grouping']=$row[csf('grouping')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
			
		}

	}
	else
	{
		echo "Data Not Found";die;
	}
	/*echo "<pre>";
	print_r($bookingInfoArrReqQnty);echo "</pre>";die;*/
	unset($result);

	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=25 and type in (5,6)");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (25) and ref_from in (8,9,10)");
	if($r_id2)
	{
		oci_commit($con);
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 8,$poIdsArr, $empty_arr); //Order id temporary insert

	/* $poIds=chop($poIds,',');
	$poIds = implode(",",array_unique(explode(",", $poIds)));

	 $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";$stst_po_cond="";$otot_po_cond="";$ctct_po_cond="";$otst_po_cond="";
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
	} */

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$recvDtlsDataArr=array();$recvDtlsDataStoreArr=array();

	$query="SELECT * FROM
		(select a.id, a.entry_form, a.receive_date, b.febric_description_id, b.color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction e, GBL_TEMP_ENGINE g
	WHERE a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.id=b.mst_id and b.id=c.dtls_id and e.id=b.trans_id and c.status_active=1 and c.is_deleted=0 $trans_date $store_cond_2 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=8 and c.booking_without_order=0 and e.status_active=1 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and a.to_order_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=8 and c.booking_without_order =0 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=8 and nvl(c.booking_without_order,0) =0  and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date  $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=8 and c.is_sales=0 and c.booking_without_order =0) abc order by color_id";

	//echo $query;//die;
	$data_array=sql_select($query);

	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];

		if($barcodeArr[$row[csf("barcode_no")]]=="")
		{
			$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

			$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 5, ".$row[csf('barcode_no')].")");
			if($r_id) 
			{
				$r_id=1;
			} 
			else 
			{
				echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 5, ".$row[csf('barcode_no')].")";
				oci_rollback($con);
				die;
			}
		}
		if($row[csf("type")]==2)
		{
			$TransDataArrTrans[$row[csf('barcode_no')]]["store_id"]=$row[csf('store_id')];
			//echo $row[csf('barcode_no')].'='.$row[csf('store_id')].'='.$row[csf('qnty')].'='.$row[csf('entry_form')]."<br/>";
		}
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr))
	{
		$split_chk_sql = sql_select("SELECT c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no d where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 and b.barcode_no = d.barcode_no and d.userid=$user_id and d.entry_form=25 and d.type=5");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				if($split_barcode_arr[$val[csf("barcode_no")]]=="")
				{
					$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];

					$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 6, ".$val[csf('barcode_no')].")");
					if($r_id) 
					{
						$r_id=1;
					} 
					else 
					{
						echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 6, ".$val[csf('barcode_no')].")";
						oci_rollback($con);
						die;
					}
				}
			}
			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b, tmp_barcode_no c where a.entry_form = 62 $split_barcode_no_cond and a.roll_split_from = b.id and a.status_active =1 and b.status_active=1 and a.barcode_no=c.barcode_no and c.userid=$user_id and c.entry_form=25 and c.type=6");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$recvDataArrTrans=array();$recvDataArr=array();

		$sqlRecvT="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id,a.store_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid=$user_id and d.entry_form=25 and d.type=5 and c.is_sales=0 order by a.entry_form desc";
		//echo $sqlRecvT; die;
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 9,$yarn_prod_id_arr, $empty_arr);
			$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a, GBL_TEMP_ENGINE b where a.status_active = 1 and a.id=b.ref_val and b.user_id=$user_id and b.entry_form=25 and b.ref_from=9 ","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			//$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"]=$row[csf('store_id')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			if($row[csf('color_id')]!="")
			{
				$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
			}
		}
		unset($recvDataT);
	}

	$all_color_ids = implode(",", $all_color_arr);
	$all_color_arr_exp=explode(",", $all_color_ids);
	$all_color_arrs = array_filter($all_color_arr_exp);
	if(!empty($all_color_arrs))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 10,$all_color_arrs, $empty_arr);
		$color_arr=return_library_array( "select a.id, a.color_name from lib_color a, GBL_TEMP_ENGINE b  where a.status_active=1 and a.id=b.ref_val and b.entry_form=25 and b.ref_from=10 and b.user_id=$user_id", "id", "color_name" );
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

	$transfer_out_sql=sql_select("SELECT d.transfer_date, a.po_breakdown_id, c.barcode_no, c.qnty,b.from_store as store_id from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d, GBL_TEMP_ENGINE g 
	where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $store_cond_3 and a.po_breakdown_id=g.ref_val and g.entry_form=25 and g.ref_from=8 and g.user_id=$user_id and c.booking_without_order =0 and b.mst_id = d.id and d.entry_form=83 and c.is_sales=0 
		union all
		select a.transfer_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store as store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $store_cond_3 and b.from_order_id=g.ref_val and g.entry_form=25 and g.ref_from=8 and g.user_id=$user_id and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1  and nvl(c.booking_without_order,0) =0 and c.is_sales=0  
		group by a.transfer_date, c.barcode_no, b.from_order_id,b.from_store
		union all
		select a.transfer_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store as store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $store_cond_3 and a.from_order_id=g.ref_val and g.entry_form=25 and g.ref_from=8 and g.user_id=$user_id and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 and c.is_sales=0
		group by a.transfer_date, c.barcode_no, a.from_order_id,b.from_store ");

	$ref_file="";$data_prod=""; $trans_out_barcode_arr = array();
	foreach($transfer_out_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];

		$trans_out_qty_arr[$ref_file][$data_prod]["trans_out"] +=$row[csf("qnty")];
		$trans_outStore_qty_arr[$ref_file][$row[csf('store_id')]][$data_prod]["trans_out"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('transfer_date')]))
		{
			$trans_out_qty_arr[$ref_file][$data_prod]["today_trans_out"] +=$row[csf("qnty")];
		}
	}

	unset($transfer_out_sql);
	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty, d.issue_date, max(d.issue_date) max_issue_date,d.issue_number,c.id,b.color_id,e.store_id from pro_roll_details c, inv_issue_master d ,inv_grey_fabric_issue_dtls b, inv_transaction e, GBL_TEMP_ENGINE g where c.mst_id = d.id and d.id=b.mst_id and b.trans_id=e.id and b.id=c.dtls_id  and d.entry_form=61 and c.entry_form=61 $store_cond_2 and c.po_breakdown_id=g.ref_val and g.entry_form=25 and g.ref_from=8 and g.user_id=$user_id and c.booking_without_order = 0 and d.id = e.mst_id and e.transaction_type=2 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.is_sales=0 group by c.po_breakdown_id,c.barcode_no,c.qnty,d.issue_date,d.issue_number,c.id,b.color_id,e.store_id");

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();$issueDataArrTrans = array();
	foreach($iss_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$data_prod = $recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"];
		}
		$iss_qty_arr[$ref_file][$data_prod]["issue"] +=$row[csf("qnty")];
		$iss_qtyStore_arr[$ref_file][$row[csf("store_id")]][$data_prod]["issue"] +=$row[csf("qnty")];
		$iss_qty_arr[$ref_file][$data_prod]["barcode_no"].=$row[csf("barcode_no")]."=".$row[csf("qnty")].",";

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('issue_date')])){
			$iss_qty_arr[$ref_file][$data_prod]["today_issue"] +=$row[csf("qnty")];
		}

		$iss_qty_arr[$ref_file][$data_prod]["max_issue_date"] = $row[csf('max_issue_date')];
		$recvDtlsDataStoreArr[$ref_file][$row[csf('store_id')]][$data_prod]['issue']+=$row[csf("qnty")];
	}
	
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($issueDataArrTrans);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("SELECT d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty,e.store_id,f.color_id  from pro_roll_details c, inv_receive_master d, inv_transaction e,pro_grey_prod_entry_dtls f, GBL_TEMP_ENGINE g where c.entry_form=84 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0  and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and f.status_active =1 and f.is_deleted=0  $poIds_cond_roll $store_cond_2 and c.po_breakdown_id=g.ref_val and g.entry_form=25 and g.ref_from=8 and g.user_id=$user_id and c.is_sales=0");
	foreach($iss_rtn_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		$iss_rtn_qty_store_arr[$ref_file][$row[csf('store_id')]][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
		{
			$iss_rtn_qty_arr[$ref_file][$data_prod]["today_issue_ret"]+=$row[csf("qnty")];
		}
	}
	unset($iss_rtn_qty_sql);

	$ref_file="";$data_prod="";$min_date="";$data_prod_store=""; $infoForTranserByJobWisex=array();
	foreach($data_array as $row)
	{
		if( $row[csf("type")]==2)
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];
			//$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

			
			//$store_index=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			$store_index=$TransDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			//echo $ref_file.'='.$row[csf('barcode_no')].'='.$store_index."<br/>";

			
			$recvDtlsDataArr[$ref_file][$data_prod]['transfer_in']+=$row[csf("qnty")];
			//$recvDtlsDataStoreArr[$ref_file][$store_index][$data_prod]['transfer_in']+=$row[csf("qnty")];
			$recvDtlsDataStoreArr[$ref_file][$row[csf('store_id')]][$data_prod]['transfer_in']+=$row[csf("qnty")];

			//$trans_outStore_qty_arr[$ref_file][$row[csf('store_id')]][$data_prod]["trans_out"] +=$row[csf("qnty")];



			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
				$recvDtlsDataStoreArr[$ref_file][$store_index][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
			}


			$infoForTranserByJobWisex[$ref_file]['buyer_name']=$infoForTranserByJobWise[$ref_file]['buyer_name'];
			$infoForTranserByJobWisex[$ref_file]['year']=$infoForTranserByJobWise[$ref_file]['year'];
			$infoForTranserByJobWisex[$ref_file]['file_no']=$infoForTranserByJobWise[$ref_file]['file_no'];
			$infoForTranserByJobWisex[$ref_file]['grouping']=$infoForTranserByJobWise[$ref_file]['grouping'];
			$infoForTranserByJobWisex[$ref_file]['style_ref_no']=$infoForTranserByJobWise[$ref_file]['style_ref_no'];
			$infoForTranserByJobWisex[$ref_file]['job_no']=$infoForTranserByJobWise[$ref_file]['job_no'];


		}
		else
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];
			//$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];


			/*$store_indexRec=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			$store_indexIss=$issueDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			if($store_indexRec!=$store_indexIss){
				$store_index=$issueDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			}*/


			$store_index=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];

			$recvDtlsDataArr[$ref_file][$data_prod]['recv']+=$row[csf("qnty")];
			$recvDtlsDataStoreArr[$ref_file][$store_index][$data_prod]['recv']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$data_prod]['today_recv']+=$row[csf("qnty")];
			}
		}

		if($recvDtlsDataArr[$ref_file][$data_prod]["min_date"] =="")
		{
			$recvDtlsDataArr[$ref_file][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
		else if($recvDtlsDataArr[$ref_file][$data_prod]["min_date"] > strtotime($row[csf("receive_date")]))
		{
			$recvDtlsDataArr[$ref_file][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
	}
	/*echo "<pre>";
	print_r($recvDtlsDataStoreArr);
	echo "</pre>";
	unset($data_array);*/	

	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=25 and type in (5,6)");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=25 and ref_from in (8,9,10)");
	oci_commit($con);
	disconnect($con);

	ob_start();
	$width = (2620+($num_of_store*110));
	?>
	<fieldset style="width:2200px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table style="width:<? echo $width+20; ?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="9" style='padding:0px 1px 0px 0px'>Booking Info</th>
					<th colspan="<? echo 20+$num_of_store; ?>">Receive Info</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="70" rowspan="2">Buyer</th>
					<th width="90" rowspan="2">Job No</th>


					<th width="50" rowspan="2">Year</th>
					<th width="100" rowspan="2">Style</th>
					<th width="80" rowspan="2">Ref. No</th>
					
					<th width="70" rowspan="2">File No</th>
					
					<th width="110" rowspan="2">Body Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="80" rowspan="2">Grey Fabric Qty(Kg)</th>
					
					<th colspan="3">Today Receive</th>
					<th colspan="4">Total Receive</th>
					<th rowspan="2" width="105">Received Balance</th>
					<th colspan="2">Today Issue</th>
					<th colspan="3">Total Issue</th>
					<th width="105" rowspan="2">Stock Qty.</th>

					<?
					foreach ($stores as $store) {
						?>
						<th rowspan="2" width="110" title="<? echo $store[csf("id")];?>"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
					<th width="50" rowspan="2">DOH</th>
				</tr>
				<tr>
					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer In</th>

					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer In</th>
					<th width="90">Grand Total</th>

					<th width="90">Issue</th>
					<th width="90">Transfer Out</th>

					<th width="90">Issue</th>
					<th width="90">Transfer Out</th>
					<th width="90">Grand Total</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table style="width:<? echo $width+20; ?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				$job_nos="";
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					$stock_qty_smry_arr[$job_no]=0;
					/*if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
					{
					*/
						/*foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
						{
							foreach ($colorData as $PartConstColor => $val)
							{*/
						foreach ($recvDtlsDataArr[$job_no] as $PartConstBody => $val) 
						{
							
								$issue_ret_smry = $iss_rtn_qty_arr[$job_no][$PartConstBody]["issue_ret"];
								$transfer_in_smry = $recvDtlsDataArr[$job_no][$PartConstBody]['transfer_in'];

								$issue_smry = $iss_qty_arr[$job_no][$PartConstBody]["issue"];
								$trans_out_smry = $trans_out_qty_arr[$job_no][$PartConstBody]["trans_out"];

								$total_issue_smry = $issue_smry+$trans_out_smry;
								$total_receive_smry = $val["recv"] + $issue_ret_smry + $transfer_in_smry;
								$stock_qty_smry = $total_receive_smry-$total_issue_smry;
								$stock_qty_smry = number_format($stock_qty_smry,2,".","");

								//if(($cbo_value_with==1 && $stock_qty_smry>=0) || ($cbo_value_with==2 && $stock_qty_smry>0))
								if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty_smry>0))
								{
									$job_nos.=$job_no."=";
									$stock_qty_smry_arr[$job_no]=1;
								}	
						}
					//}
				}
				$i=1;
				$job_nos=chop($job_nos,",");
				//echo $job_nos; die;
				$storeIDwiseTotal=array();
				$job_nos=array_unique(explode("=", $job_nos));
				foreach ($job_nos as $job_no) 
				{
					//echo $job_no.",";
					/*foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
					{*/
						foreach ($recvDtlsDataArr[$job_no] as $PartConstBody => $val)
						{
							$PartConstColorArr = explode("**", $PartConstBody);
							$deter_id = $PartConstColorArr[0];
							$body_part_id = $PartConstColorArr[1];
							//$color_id = $PartConstColorArr[2];

							/*$color_names="";
							$color_ids = explode(",", $color_id);
							foreach ($color_ids as $color) 
							{
								$color_names .=  $color_arr[$color].",";
							}
							$color_names = chop($color_names,",");*/

							$trans_out = $trans_out_qty_arr[$job_no][$PartConstBody]["trans_out"];
							$today_trans_out = $trans_out_qty_arr[$job_no][$PartConstBody]["today_trans_out"];

							$issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstBody]["issue_ret"];
							$today_issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstBody]["today_issue_ret"];

							$transfer_in = $recvDtlsDataArr[$job_no][$PartConstBody]['transfer_in'];
							$today_transfer_in = $recvDtlsDataArr[$job_no][$PartConstBody]['today_transfer_in'];
							$total_receive = $val["recv"] + $issue_ret + $transfer_in;
							
							$greyQnty= $bookingInfoArrReqQnty[$job_no][$body_part_id][$deter_id]['grey_fab_qnty'];
							//$totalRecevBalance=$greyQntyArr[$body_part_id][$deter_id][$colorIndex]-$total_receive;
							$totalRecevBalance=$greyQnty-$total_receive;

							$issue = $iss_qty_arr[$job_no][$PartConstBody]["issue"];
							$today_issue = $iss_qty_arr[$job_no][$PartConstBody]["today_issue"];

							$total_issue = $issue+$trans_out;
							$stock_qty = $total_receive-$total_issue;

							$stock_qty=number_format($stock_qty,2,".","");
							$stock_qty= str_replace("-0.00", "0.00", $stock_qty) ;

							//if(($cbo_value_with==1 && $stock_qty>=0) || ($cbo_value_with==2 && $stock_qty>0))
							//if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty>0))
							//{
																	
								

								$max_issue_date = $iss_qty_arr[$job_no][$PartConstBody]["max_issue_date"];
								$min_date = $recvDtlsDataArr[$job_no][$PartConstBody]["min_date"];
								$doh="";
								if($min_date != "")
								{
									$doh = date_diff($min_date,date("Y-m-d"));

									$date1=date_create(date("Y-m-d",$min_date));
									$date2=date_create(date("Y-m-d"));
									$diff=date_diff($date1,$date2);

									$doh = $diff->format("%a");
								}
								
								$po_ids_ref = implode(",",array_unique(explode(",",chop($popup_job_ref[$job_no],","))));
							if($cbo_value_with ==2 && $stock_qty>0.00)
							{
								if($i%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $i;?>','<? echo $bgcolor1;?>')" id="trr<? echo $i;?>">
									<td width="40"><? echo  $i; ?>&nbsp;</td>
									<?
									/*if($bookingInfoArr[$body_part_id][$deter_id]['buyer_name']=="" && $bookingInfoArr[$body_part_id][$deter_id]['job_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['year']=="" && $bookingInfoArr[$body_part_id][$deter_id]['style_ref_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['grouping']=="")
									{*/
										if($val["recv"]==0){
										?>
										<td width="70"><p><? echo $buyer_arr[$infoForTranserByJobWisex[$job_no]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $infoForTranserByJobWisex[$job_no]['job_no']; ?>&nbsp;</p></td>
										<td width="50"><p><? echo $infoForTranserByJobWisex[$job_no]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $infoForTranserByJobWisex[$job_no]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $infoForTranserByJobWisex[$job_no]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $infoForTranserByJobWisex[$job_no]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									else
									{
										?>
										<td width="70"><p><? echo $buyer_arr[$bookingInfoArr[$job_no][$body_part_id][$deter_id]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $job_no;
													//echo $bookingInfoArr[$body_part_id][$deter_id]['job_no']; ?>&nbsp;</p></td>
										<td width="50"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									?>
									<td width="110" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
									<td width="110" title="<? echo $deter_id;?>"><? echo $constuction_arr[$deter_id];?></td>
									
									<td width="80" align="right"><p><?  echo number_format($greyQnty,2);?></p></td>
					

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','today_recv_popup_summary')"><? echo number_format($val["today_recv"],2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_issue_ret;?>','0','today_issue_rtn_popup_summary')"><? echo number_format($today_issue_ret,2);?></a>
									</td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_transfer_in;?>','0','today_trans_in_popup_summary')">
											<? echo number_format($today_transfer_in,2);?>
										</a></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue_ret;?>','0','issue_rtn_popup_summary')"><? echo number_format($issue_ret,2);?></a>
									</td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','trans_in_popup_summary')">
											<? echo number_format($transfer_in,2);?>
										</a>
									</td>
									<td width="90" align="right"><? echo number_format($total_receive,2);?></td>
									<td width="105" align="right"><? echo number_format($totalRecevBalance ,2);?></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','today_issue_popup_summary')"><? echo number_format($today_issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_trans_out;?>','0','today_trans_to_popup_summary')"><? echo number_format($today_trans_out,2);?></a></td>

									<td width="90" align="right" title="<? echo $iss_qty_arr[$job_no][$PartConstBody]["barcode_no"]; ?>"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','trans_to_popup_summary')"><? echo number_format($trans_out,2);?></a></td>
									<td width="90" align="right"><? echo number_format($total_issue,2);?></td>
									<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','0','stock_popup_summary_2')"><? echo number_format($stock_qty,2);?></a></td>
									<?
									$total_issue_return=$store_wise_stock=$stock=0;
									foreach ($stores as $store) {

										/*foreach ($recvDtlsDataStoreArr[$job_no] as $colorIndexx => $colorData) 
										{*/
											foreach ($recvDtlsDataStoreArr[$job_no] as $storeIndex => $StoreData) 
											{
												foreach ($StoreData as $PartConstColorx => $valx)
												{
													$PartConstColorArrx = explode("**", $PartConstColorx);
													$deter_idx = $PartConstColorArrx[0];
													$body_part_idx = $PartConstColorArrx[1];
													$color_idx = $PartConstColorArrx[2];

													$color_names="";
													$color_ids = explode(",", $color_idx);
													foreach ($color_ids as $color) 
													{
														$color_names .=  $color_arr[$color].",";
													}
													$color_names = chop($color_names,",");

													/*$receivexQnty[$job_no][$body_part_idx][$deter_idx][$storeIndex]= $valx["recv"];
													$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["transfer_in"];
													$issue_rtn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_rtn_qty_store_arr[$job_no][$storeIndex][$PartConstColorx]["issue_ret"];
													$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_qtyStore_arr[$job_no][$storeIndex][$PartConstColorx]["issue"];
													$transferOut_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] =$trans_outStore_qty_arr[$job_no][$storeIndex][$PartConstColorx]["trans_out"];*/


													$receivexQnty[$job_no][$body_part_idx][$deter_idx][$storeIndex]= $valx["recv"];
													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];
													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["transfer_in"];

													$transferIn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex]=$recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];

													$issue_rtn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_rtn_qty_store_arr[$job_no][$storeIndex][$PartConstColorx]["issue_ret"];

													$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_qtyStore_arr[$job_no][$storeIndex][$PartConstColorx]["issue"];
													//$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["issue"];

													$transferOut_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] =$trans_outStore_qty_arr[$job_no][$storeIndex][$PartConstColorx]["trans_out"];

													//$arra[store]=$transferIn_qntyx-$transferOut_qntyx

													//echo $total_receivex;
													/*echo "<pre>";
													print_r($total_receivex);
													echo "</pre>";*/

												}
											}
										//}

										/*$totalRecvQnty=$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transfer_inx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 
										$totalIssueQnty=$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; */

										$totalRecvQnty=$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferIn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 

										$totalIssueQnty=$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 
									
										?>
											<td title="<? echo $totalRecvQnty.'-'.$totalIssueQnty; ?>" width="110" align="right">
												<p><?

												//echo $stockTotal= $totalRecvQnty-$totalIssueQnty;

												$stockTotal= $totalRecvQnty-$totalIssueQnty; $stockQntyFormat=number_format($stockTotal,2,".","");
												if($stockQntyFormat==-0.00){$stockQntyFormat=0.00;}
												echo $stockQntyFormat ;



												?>&nbsp;</p></td>
										<?
										$storeIDwiseTotal[$store[csf("id")]]+=$stockTotal;
										
									}
									?>
									
									<td width="50" align="center"><? echo $doh;?></td>
								</tr>
								<?
								$i++;

								$grand_today_recv += $val["today_recv"];
								$grand_today_issue_ret += $today_issue_ret;
								$grand_today_transfer_in += $today_transfer_in;
								$grand_recv += $val["recv"];
								$grand_issue_ret += $issue_ret;
								$grand_transfer_in += $transfer_in;
								$grand_total_receive += $total_receive;
								$grand_total_received_balance += $totalRecevBalance;

								$grand_today_issue += $today_issue;
								$grand_today_trans_out += $today_trans_out;

								$grand_issue += $issue;
								$grand_trans_out += $trans_out;
								$grand_total_issue += $total_issue;
								$grand_stock_qty += $stock_qty;
								
								$grand_req_qty += $greyQnty;

							}
							else if($cbo_value_with ==1)
							{
								if($i%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $i;?>','<? echo $bgcolor1;?>')" id="trr<? echo $i;?>">
									<td width="40"><? echo  $i; ?>&nbsp;</td>
									<?
									/*if($bookingInfoArr[$body_part_id][$deter_id]['buyer_name']=="" && $bookingInfoArr[$body_part_id][$deter_id]['job_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['year']=="" && $bookingInfoArr[$body_part_id][$deter_id]['style_ref_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['grouping']=="")
									{*/
										if($val["recv"]==0){
										?>
										<td width="70"><p><? echo $buyer_arr[$infoForTranserByJobWisex[$job_no]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $infoForTranserByJobWisex[$job_no]['job_no'] ?>&nbsp;</p></td>
										<td width="50"><p><? echo $infoForTranserByJobWisex[$job_no]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $infoForTranserByJobWisex[$job_no]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $infoForTranserByJobWisex[$job_no]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $infoForTranserByJobWisex[$job_no]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									else
									{
										?>
										<td width="70"><p><? echo $buyer_arr[$bookingInfoArr[$job_no][$body_part_id][$deter_id]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $job_no;
													//echo $bookingInfoArr[$body_part_id][$deter_id]['job_no']; ?>&nbsp;</p></td>
										<td width="50"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									?>
									<td width="110" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
									<td width="110" title="<? echo $deter_id;?>"><? echo $constuction_arr[$deter_id];?></td>
									
									<td width="80" align="right"><p><?  echo number_format($greyQnty,2);?></p></td>
					

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','today_recv_popup_summary')"><? echo number_format($val["today_recv"],2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_issue_ret;?>','0','today_issue_rtn_popup_summary')"><? echo number_format($today_issue_ret,2);?></a>
									</td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_transfer_in;?>','0','today_trans_in_popup_summary')">
											<? echo number_format($today_transfer_in,2);?>
										</a></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue_ret;?>','0','issue_rtn_popup_summary')"><? echo number_format($issue_ret,2);?></a>
									</td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','trans_in_popup_summary')">
											<? echo number_format($transfer_in,2);?>
										</a>
									</td>
									<td width="90" align="right"><? echo number_format($total_receive,2);?></td>
									<td width="105" align="right"><? echo number_format($totalRecevBalance ,2);?></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','today_issue_popup_summary')"><? echo number_format($today_issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_trans_out;?>','0','today_trans_to_popup_summary')"><? echo number_format($today_trans_out,2);?></a></td>

									<td width="90" align="right" title="<? echo $iss_qty_arr[$job_no][$PartConstBody]["barcode_no"]; ?>"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','trans_to_popup_summary')"><? echo number_format($trans_out,2);?></a></td>
									<td width="90" align="right"><? echo number_format($total_issue,2);?></td>
									<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','0','stock_popup_summary_2')"><? echo number_format($stock_qty,2);?></a></td>
									<?
									$total_issue_return=$store_wise_stock=$stock=0;
									foreach ($stores as $store) {

										/*foreach ($recvDtlsDataStoreArr[$job_no] as $colorIndexx => $colorData) 
										{*/
											foreach ($recvDtlsDataStoreArr[$job_no] as $storeIndex => $StoreData) 
											{
												foreach ($StoreData as $PartConstColorx => $valx)
												{
													$PartConstColorArrx = explode("**", $PartConstColorx);
													$deter_idx = $PartConstColorArrx[0];
													$body_part_idx = $PartConstColorArrx[1];
													$color_idx = $PartConstColorArrx[2];

													$color_names="";
													$color_ids = explode(",", $color_idx);
													foreach ($color_ids as $color) 
													{
														$color_names .=  $color_arr[$color].",";
													}
													$color_names = chop($color_names,",");

													$receivexQnty[$job_no][$body_part_idx][$deter_idx][$storeIndex]= $valx["recv"];
													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];
													


													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["transfer_in"];

													$transferIn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex]=$recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];


													$issue_rtn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_rtn_qty_store_arr[$job_no][$storeIndex][$PartConstColorx]["issue_ret"];

													$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_qtyStore_arr[$job_no][$storeIndex][$PartConstColorx]["issue"];
													//$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["issue"];

													$transferOut_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] =$trans_outStore_qty_arr[$job_no][$storeIndex][$PartConstColorx]["trans_out"];

													//$arra[store]=$transferIn_qntyx-$transferOut_qntyx




													//echo $total_receivex;
													/*echo "<pre>";
													print_r($total_receivex);
													echo "</pre>";*/

												}
											}
										//}

										$totalRecvQnty=$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferIn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 

										$totalIssueQnty=$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 
									
										?>
											<td title="<? echo $totalRecvQnty.'-'.$totalIssueQnty; ?>" width="110" align="right">
												<p><?  



												//$stockTotal= $totalRecvQnty-$totalIssueQnty; echo  $stockTotal; 

												$stockTotal= $totalRecvQnty-$totalIssueQnty; $stockQntyFormat=number_format($stockTotal,2,".","");
												if($stockQntyFormat==-0.00){$stockQntyFormat=0.00;}
												echo $stockQntyFormat ;


												
												/*echo "<br>recv=".$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]];
												echo "<br>issueRtn=".$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];
												echo "<br>transIn=".$transferIn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];

												echo "<br>issue=".$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];
												echo "<br>transOut=".$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];

												echo "<br>qnty=".$totalRecvQnty.'-'.$totalIssueQnty;*/

												//echo "<br>".$store[csf("id")];

												//echo "<br>transIn=".$recvDtlsDataStoreArr[$job_no][$store[csf("id")]][$deter_id."**".$body_part_id]['transfer_in'];

												?>&nbsp;</p></td>
										<?
										$storeIDwiseTotal[$store[csf("id")]]+=$stockTotal;
										
									}
									?>
									
									<td width="50" align="center"><? echo $doh;?></td>
								</tr>
								<?
								$i++;

								$grand_today_recv += $val["today_recv"];
								$grand_today_issue_ret += $today_issue_ret;
								$grand_today_transfer_in += $today_transfer_in;
								$grand_recv += $val["recv"];
								$grand_issue_ret += $issue_ret;
								$grand_transfer_in += $transfer_in;
								$grand_total_receive += $total_receive;
								$grand_total_received_balance += $totalRecevBalance;

								$grand_today_issue += $today_issue;
								$grand_today_trans_out += $today_trans_out;

								$grand_issue += $issue;
								$grand_trans_out += $trans_out;
								$grand_total_issue += $total_issue;
								$grand_stock_qty += $stock_qty;
								
								$grand_req_qty += $greyQnty;
							
							}
						}

					//}
				}
				?>	

				<tfoot>
					<tr>
						<th width="40">&nbsp;</td>
						<th width="70"><p>&nbsp;</p></th>
						<th width="90"><p>&nbsp;</p></th>
						<th width="50"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="80"><p>&nbsp;</p></th>
						<th width="70"><p>&nbsp;</p></th>
						
						<th width="110"></th>
						<th width="110"><p>Total&nbsp;</p></th>
						<th width="80"><? echo number_format($grand_req_qty,2);?></td>

						<th width="90"><? echo number_format($grand_today_recv,2);?></th>
						<th width="90"><? echo number_format($grand_today_issue_ret,2);?></th>
						<th width="90"><? echo number_format($grand_today_transfer_in,2);?></th>

						<th width="90"><? echo number_format($grand_recv,2);?></th>
						<th width="90"><? echo number_format($grand_issue_ret,2);?></th>
						<th width="90"><? echo number_format($grand_transfer_in,2);?></th>
						<th width="90"><? echo number_format($grand_total_receive,2);?></th>
						<th width="105"><? echo number_format($grand_total_received_balance,2);?></th>

						<th width="90"><? echo number_format($grand_today_issue,2);?></th>
						<th width="90"><? echo number_format($grand_today_trans_out,2);?></th>

						<th width="90"><? echo number_format($grand_issue,2);?></th>
						<th width="90"><? echo number_format($grand_trans_out,2);?></th>
						<th width="90"><? echo number_format($grand_total_issue,2);?></th>

						<th width="105"><? echo number_format($grand_stock_qty,2);?></th>
						<?
						foreach ($stores as $store) {
							?>
							<th width="110"> <? echo number_format($storeIDwiseTotal[$store[csf("id")]],2);?>  </th>
							<?
						}
						?>
						
						<th width="50"></th>
					</tr>
				</tfoot>									
			</table>
		</div>
		
	</fieldset>
	<?

	$html = ob_get_contents();
	ob_clean();
	
	foreach (glob("*.xls") as $filename) 
	{
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
if($action=="sales_summary_report_generate")
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

	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(a.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
		}
	}

	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$hide_booking_id = trim(str_replace("'","",$txt_hide_booking_id));
	$txt_booking_no = trim(str_replace("'","",$txt_booking_no));

	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	$txt_fso_no=trim(str_replace("'","",$txt_fso_no));
	if ($txt_fso_no=="") $sales_no_cond=""; else $sales_no_cond=" and a.job_no_prefix_num in ($txt_fso_no) ";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	
	$sql="select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id,b.body_part_id, b.determination_id, b.color_id,b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.company_id = $cbo_company_id $year_cond $sales_no_cond and a.id = b.mst_id and a.status_active =1 and b.status_active=1 and a.within_group=2";

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;

			$ref_file = $row[csf('buyer_id')]."_".$row[csf('job_no')]."_".$row[csf('sales_booking_no')]."_".$row[csf('body_part_id')]."_".$row[csf('determination_id')]."_".$row[csf('color_id')]."_".$row[csf('grey_qty')]."##";
			$poIds.=$row[csf('id')].",";

			$poArr[$row[csf('id')]]=$row[csf('job_no')];

			$fileRefArr[$row[csf('job_no')]].=$ref_file;

			$popup_job_ref[$row[csf('job_no')]] .= $row[csf('id')].",";
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

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$recvDtlsDataArr=array();

	$query="select a.id, a.entry_form, a.receive_date, b.febric_description_id, b.color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction e
	WHERE a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.id=b.mst_id and b.id=c.dtls_id and e.id=b.trans_id and c.status_active=1 and c.is_deleted=0  $trans_date $poIds_cond_roll $store_cond_2 and c.is_sales=1 and e.status_active=1
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 $transfer_date $otot_po_cond $store_cond_1 and c.is_sales=1 and c.booking_without_order = 0";

	//echo $query;//die;
	$data_array=sql_select($query);
	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
	if($r_id2)
	{
		oci_commit($con);
	}

	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];

		$r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")");
		if($r_id) 
		{
			$r_id=1;
		} 
		else 
		{
			echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")";
			oci_rollback($con);
			die;
		}
	}
	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr))
	{
		/*
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

			$split_chk_sql = sql_select("select d.barcode_no, d.qnty from pro_roll_split c , pro_roll_details d where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1 $ref_barcode_no_cond");
		*/

		$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c, tmp_barcode_no d where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 and b.barcode_no = d.barcode_no and d.userid = $user_id");

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

			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form =61 and a.roll_id =b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond");

			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$recvDataArrTrans=array();$recvDataArr=array();
		$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid= $user_id order by a.entry_form desc";
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
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
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

	$transfer_out_sql=sql_select("select d.transfer_date, a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=133 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $poIds_cond_trans_roll $store_cond_3 and c.booking_without_order = 0 and b.mst_id = d.id and d.entry_form=133");

	$ref_file="";$data_prod=""; $trans_out_barcode_arr = array();
	foreach($transfer_out_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$trans_out_qty_arr[$ref_file][$data_prod]["trans_out"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('transfer_date')]))
		{
			$trans_out_qty_arr[$ref_file][$data_prod]["today_trans_out"] +=$row[csf("qnty")];
		}
	}
	unset($transfer_out_sql);

	$iss_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty, d.issue_date, max(d.issue_date) max_issue_date from pro_roll_details c, inv_issue_master d, inv_transaction e where c.mst_id = d.id and d.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll $store_cond_2 and c.is_sales=1 and d.id = e.mst_id and e.transaction_type=2 and e.item_category=13 and e.status_active =1 group by c.po_breakdown_id, c.barcode_no, c.qnty, d.issue_date");

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();
	foreach($iss_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$data_prod = $recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"]."**".$recvDataArrTrans[$mother_barcode_no]["color_id"];
		}
		$iss_qty_arr[$ref_file][$data_prod]["issue"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('issue_date')])){
			$iss_qty_arr[$ref_file][$data_prod]["today_issue"] +=$row[csf("qnty")];
		}

		$iss_qty_arr[$ref_file][$data_prod]["max_issue_date"] = $row[csf('max_issue_date')];
	}
	unset($iss_qty_sql);

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("select d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c, inv_receive_master d, inv_transaction e where c.entry_form=84 and c.mst_id = d.id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0 and d.id = e.mst_id and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 $poIds_cond_roll $store_cond_2 and c.is_sales=1");
	foreach($iss_rtn_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
		{
			$iss_rtn_qty_arr[$ref_file][$data_prod]["today_issue_ret"]+=$row[csf("qnty")];
		}
	}
	unset($iss_rtn_qty_sql);

	$ref_file="";$data_prod="";$min_date="";
	foreach($data_array as $row)
	{
		if( $row[csf("type")]==2)
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

			$recvDtlsDataArr[$ref_file][$data_prod]['transfer_in']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
			}
		}
		else
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

			$recvDtlsDataArr[$ref_file][$data_prod]['recv']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$data_prod]['today_recv']+=$row[csf("qnty")];
			}
		}

		if($recvDtlsDataArr[$ref_file][$data_prod]["min_date"] =="")
		{
			$recvDtlsDataArr[$ref_file][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
		else if($recvDtlsDataArr[$ref_file][$data_prod]["min_date"] > strtotime($row[csf("receive_date")]))
		{
			$recvDtlsDataArr[$ref_file][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
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
		<table width="2550" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="8" style='padding:0px 1px 0px 0px'>Fso Info</th>
					<th colspan="20">Receive Info</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="70" rowspan="2">Buyer</th>
					<th width="90" rowspan="2">Fso No</th>
					<th width="100" rowspan="2">Booking No</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th width="80" rowspan="2">Grey Fabric Qty(Kg)</th>
					<th width="110" rowspan="2">Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="105" rowspan="2">Color</th>
					<th colspan="3">Today Receive</th>
					<th colspan="4">Total Receive</th>
					<th colspan="3">Today Issue</th>
					<th colspan="4">Total Issue</th>
					<th width="105" rowspan="2">Stock Qty.</th>
					<th width="105" rowspan="2">Last Issued Date</th>
					<th width="50" rowspan="2">DOH</th>
				</tr>
				<tr>
					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer From</th>

					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer From</th>
					<th width="90">Grand Total</th>

					<th width="90">Issue</th>
					<th width="90">Receive Return</th>
					<th width="90">Transfer To</th>

					<th width="90">Issue</th>
					<th width="90">Receive Return</th>
					<th width="90">Transfer To</th>
					<th width="90">Grand Total</th>
				</tr>
			</thead>
		</table>
		<div style="width:2570px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2550" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				$i=1;$y=1;
				$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=0;
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					$fileRefData=explode("##",chop($fileRefArrData,"##"));
					$row_count = count($fileRefData);
					$c=1;
					?>
					<tr>
						<td width="705" style='padding:0px 1px 0px 0px'>
							<table cellpadding="0" cellspacing="0" border="0">
								<?
								for ($r=0; $r < $row_count; $r++) 
								{ 
									if(isset($fileRefData[$r]))
									{
										$fileRefDataDtls = explode("_", $fileRefData[$r]);
										$buyer_id=$fileRefDataDtls[0];
										$job_number=$fileRefDataDtls[1];
										$bookingNo=$fileRefDataDtls[2];
										$body_part_id=$fileRefDataDtls[3];
										$deter_id=$fileRefDataDtls[4];
										$fabric_color_id=$fileRefDataDtls[5];
										$grey_qnty=$fileRefDataDtls[6];

										$sub_job_total += $grey_qnty;
										$grand_job_total += $grey_qnty;

										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>

										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $c; ?></td>
											<td width="70"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
											<td width="90"><p><? echo $job_number; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $bookingNo; ?>&nbsp;</p></td>
											<td width="110"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
											<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
											<td width="105"><p><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($grey_qnty,2,'.',''); ?>&nbsp;</p></td>
										</tr>
										<?
									}
									else
									{
										?>
										<tr>
											<td width="40"></td>
											<td width="70"><p>&nbsp;</p></td>
											<td width="90"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="110"><p>&nbsp;</p></td>
											<td width="110"><p>&nbsp;</p></td>
											<td width="105"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
										</tr>
										<?
									}
									$i++;$c++;
								}
								?>
							</table>
						</td>
						<td>
							<table cellpadding="0" cellspacing="0" border="0" >
								<?
								foreach ($recvDtlsDataArr[$job_no] as $PartConstColor => $val) 
								{
									$PartConstColorArr = explode("**", $PartConstColor);
									$deter_id = $PartConstColorArr[0];
									$body_part_id = $PartConstColorArr[1];
									$color_id = $PartConstColorArr[2];

									$color_names="";
									$color_ids = explode(",", $color_id);
									foreach ($color_ids as $color) 
									{
										$color_names .=  $color_arr[$color].",";
									}
									$color_names = chop($color_names,",");

									$trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["trans_out"];
									$today_trans_out = $trans_out_qty_arr[$job_no][$PartConstColor]["today_trans_out"];

									$issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["issue_ret"];
									$today_issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstColor]["today_issue_ret"];

									$transfer_in = $recvDtlsDataArr[$job_no][$PartConstColor]['transfer_in'];
									$today_transfer_in = $recvDtlsDataArr[$job_no][$PartConstColor]['today_transfer_in'];
									$total_receive = $val["recv"] + $issue_ret + $transfer_in;

									$issue = $iss_qty_arr[$job_no][$PartConstColor]["issue"];
									$today_issue = $iss_qty_arr[$job_no][$PartConstColor]["today_issue"];

									$total_issue = $issue+$trans_out;
									$stock_qty = $total_receive-$total_issue;

									$sub_today_recv += $val["today_recv"];
									$sub_today_issue_ret += $today_issue_ret;
									$sub_today_transfer_in += $today_transfer_in;
									$sub_recv += $val["recv"];
									$sub_issue_ret += $issue_ret;
									$sub_transfer_in += $transfer_in;
									$sub_total_receive += $total_receive;

									$sub_today_issue += $today_issue;
									$sub_today_trans_out += $today_trans_out;

									$sub_issue += $issue;
									$sub_trans_out += $trans_out;
									$sub_total_issue += $total_issue;
									$sub_stock_qty += $stock_qty;

									$grand_today_recv += $val["today_recv"];
									$grand_today_issue_ret += $today_issue_ret;
									$grand_today_transfer_in += $today_transfer_in;
									$grand_recv += $val["recv"];
									$grand_issue_ret += $issue_ret;
									$grand_transfer_in += $transfer_in;
									$grand_total_receive += $total_receive;

									$grand_today_issue += $today_issue;
									$grand_today_trans_out += $today_trans_out;

									$grand_issue += $issue;
									$grand_trans_out += $trans_out;
									$grand_total_issue += $total_issue;
									$grand_stock_qty += $stock_qty;

									$max_issue_date = $iss_qty_arr[$job_no][$PartConstColor]["max_issue_date"];
									$min_date = $recvDtlsDataArr[$job_no][$PartConstColor]["min_date"];
									$doh="";
									if($min_date != "")
									{
										$doh = date_diff($min_date,date("Y-m-d"));

										$date1=date_create(date("Y-m-d",$min_date));
										$date2=date_create(date("Y-m-d"));
										$diff=date_diff($date1,$date2);

										$doh = $diff->format("%a");
									}
									
									$po_ids_ref = implode(",",array_unique(explode(",",chop($popup_job_ref[$job_no],","))));
									if($y%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $y;?>','<? echo $bgcolor1;?>')" id="trr<? echo $y;?>">
										<td width="110"><? echo $body_part[$body_part_id];?></td>
										<td width="110"><? echo $constuction_arr[$deter_id];?></td>
										<td width="105"><p><? echo $color_names; ?>&nbsp;</p></td>

										<td width="90" align="right"><? echo number_format($val["today_recv"],2);?></td>
										<td width="90" align="right"><? echo number_format($today_issue_ret,2);?></td>
										<td width="90" align="right"><? echo number_format($today_transfer_in,2);?></td>

										<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','1','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
										<td width="90" align="right"><? echo number_format($issue_ret,2);?></td>
										<td width="90" align="right"><? echo number_format($transfer_in,2);?></td>
										<td width="90" align="right"><? echo number_format($total_receive,2);?></td>

										<td width="90" align="right"><? echo number_format($today_issue,2);?></td>
										<td width="90">&nbsp;</td>
										<td width="90" align="right"><? echo number_format($today_trans_out,2);?></td>

										<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','1','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
										<td width="90">&nbsp;</td>
										<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','1','transOut_popup_summary')"><? echo number_format($trans_out,2);?></a></td>

										<td width="90" align="right"><? echo number_format($total_issue,2);?></td>

										<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstColor;?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','1','stock_popup_summary')"><? echo number_format($stock_qty,2);?></a></td>

										<td width="105" align="center"><? echo change_date_format($max_issue_date);?></td>
										<td width="50" align="center"><? echo $doh;?></td>
									</tr>
									<?
									$y++;
								}
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td width="705" style='padding:0px 1px 0px 0px'>
							<table cellpadding="0" cellspacing="0" border="0" >
								<tfoot>
									<tr>
										<th width="40">&nbsp;</th>
										<th width="70"><p>&nbsp;</p></th>
										<th width="90"><p>&nbsp;</p></th>
										<th width="100"><p>&nbsp;</p></th>
										<th width="110"><p>&nbsp;</p></th>
										<th width="110"><p>&nbsp;</p></th>
										<th width="105"><p>Fso Total</p></th>
										<th width="80"><p><? echo number_format($sub_job_total,2);?></p></th>
									</tr>
								</tfoot>
							</table>
						</td>

						<td>
							<table cellpadding="0" cellspacing="0" border="0" >
								<tfoot>
									<tr>
										<th width="110">&nbsp;</th>
										<th width="110">&nbsp;</th>
										<th width="105"><p>&nbsp;</p></th>

										<th width="90"><? echo number_format($sub_today_recv,2);?></th>
										<th width="90"><? echo number_format($sub_today_issue_ret,2);?></th>
										<th width="90"><? echo number_format($sub_today_transfer_in,2);?></th>

										<th width="90"><? echo number_format($sub_recv,2);?></th>
										<th width="90"><? echo number_format($sub_issue_ret,2);?></th>
										<th width="90"><? echo number_format($sub_transfer_in,2);?></th>
										<th width="90"><? echo number_format($sub_total_receive,2);?></th>

										<th width="90"><? echo number_format($sub_today_issue,2);?></th>
										<th width="90">&nbsp;</th>
										<th width="90"><? echo number_format($sub_today_trans_out,2);?></th>

										<th width="90"><? echo number_format($sub_issue,2);?></th>
										<th width="90">&nbsp;</th>
										<th width="90"><? echo number_format($sub_trans_out,2);?></th>
										<th width="90"><? echo number_format($sub_total_issue,2);?></th>
										<th width="105"><? echo number_format($sub_stock_qty,2);?></th>
										<th width="105"></th>
										<th width="50"></th>
									</tr>
								</tfoot>
							</table>
						</td>
					</tr>
					
					<?
					$sub_today_recv=$sub_today_issue_ret=$sub_today_transfer_in=$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_today_issue=$sub_today_trans_out=$sub_issue=$sub_trans_out=$sub_total_issue=$sub_stock_qty=$sub_job_total=0;
				}
				?>
			</table>
		</div>
		<table width="2550" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<tr>
				<td width="705" style='padding:0px 1px 0px 0px'>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="40">&nbsp;</th>
								<th width="70"><p>&nbsp;</p></th>
								<th width="90"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="110"><p>&nbsp;</p></th>
								<th width="105"><p>Grand Total</p></th>
								<th width="80"><p><? echo number_format($grand_job_total,2);?></p></th>
							</tr>
						</tfoot>
					</table>
				</td>

				<td>
					<table cellpadding="0" cellspacing="0" border="0" >
						<tfoot>
							<tr>
								<th width="110">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="105"><p>&nbsp;</p></th>

								<th width="90"><? echo number_format($grand_today_recv,2);?></th>
								<th width="90"><? echo number_format($grand_today_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_today_transfer_in,2);?></th>

								<th width="90"><? echo number_format($grand_recv,2);?></th>
								<th width="90"><? echo number_format($grand_issue_ret,2);?></th>
								<th width="90"><? echo number_format($grand_transfer_in,2);?></th>
								<th width="90"><? echo number_format($grand_total_receive,2);?></th>

								<th width="90"><? echo number_format($grand_today_issue,2);?></th>
								<th width="90">&nbsp;</th>
								<th width="90"><? echo number_format($grand_today_trans_out,2);?></th>

								<th width="90"><? echo number_format($grand_issue,2);?></th>
								<th width="90">&nbsp;</th>
								<th width="90"><? echo number_format($grand_trans_out,2);?></th>
								<th width="90"><? echo number_format($grand_total_issue,2);?></th>
								<th width="105"><? echo number_format($grand_stock_qty,2);?></th>
								<th width="105"></th>
								<th width="50"></th>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
		</table>
	</fieldset>
	<?

	$html = ob_get_contents();
	ob_clean();
	
	foreach (glob("*.xls") as $filename) 
	{
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

if($action=="report_generate_exel_only")
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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));

	$cbo_string_search_type=str_replace("'","",$cbo_string_search_type);
	if($cbo_string_search_type==1)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $txt_ref_no_cond=" and LOWER(a.grouping) = LOWER('".str_replace("'","",trim($txt_ref_no))."')"; else $txt_ref_no_cond="";
		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no = '$txt_booking_no'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id = $hide_booking_id"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $txt_ref_no_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $txt_ref_no_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('".str_replace("'","",trim($txt_file_no))."%')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $txt_ref_no_cond=" and LOWER(a.grouping) like LOWER('".str_replace("'","",trim($txt_ref_no))."%')"; else $txt_ref_no_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '".str_replace("'","",trim($txt_booking_no))."%'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '".$hide_booking_id."%'"; else $booking_id_cond="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_file_no))!="") $file_cond=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."')"; else $file_cond="";
		if(str_replace("'","",trim($txt_ref_no))!="") $txt_ref_no_cond=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."')"; else $txt_ref_no_cond="";

		if(str_replace("'","",trim($txt_booking_no))!="") $booking_no_cond=" and c.booking_no like '%".str_replace("'","",trim($txt_booking_no))."'"; else $booking_no_cond="";
		if(str_replace("'","",trim($hide_booking_id))!="") $booking_id_cond=" and c.booking_id like '%".$hide_booking_id."'"; else $booking_id_cond="";
	}
	// echo $file_cond.'<br>'.$ref_cond.'<br>'.$booking_no_cond.'<br>'.$booking_id_cond.'<br>';

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	//if($hide_booking_id!="") $booking_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	//if($txt_booking_no!="") $booking_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";
	//if($txt_ref_no!="") $txt_ref_no_cond=" and a.grouping LIKE '%".trim($txt_ref_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from."";

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
	where b.company_name=$cbo_company_id and c.booking_type=1 and b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $booking_no_cond $txt_ref_no_cond $file_cond
	group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, c.booking_no
	order by a.id";

	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;
			$booking_ref=$row[csf('buyer_name')]."*".$row[csf('job_no')]."*".$row[csf('booking_no')]."*".$row[csf('grouping')];
			$poIds.=$row[csf('id')].",";
			$poArr[$row[csf('id')]]=$booking_ref;
			$po_booking[$row[csf('id')]]=$row[csf('booking_no')];

			$fileRefArr[$booking_ref].=$row[csf('id')].",";
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	$poIds=chop($poIds,','); $poIds_cond_roll=$poIds_cond_trans_roll=$ctct_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and (";
		$poIds_cond_suff.=")";
		$poIdsArr=array_chunk(array_unique(explode(",",$poIds)),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_trans_roll.=" a.po_breakdown_id in($ids) or ";
			$ctct_po_cond.=" b.from_order_id in($ids) or ";
			$otst_po_cond.=" a.from_order_id in($ids) or ";
		}

		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_trans_roll=$poIds_cond_pre.chop($poIds_cond_trans_roll,'or ').$poIds_cond_suff;
		$ctct_po_cond=$poIds_cond_pre.chop($ctct_po_cond,'or ').$poIds_cond_suff;
		$otst_po_cond=$poIds_cond_pre.chop($otst_po_cond,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_trans_roll=" and a.po_breakdown_id in($poIds)";
		$ctct_po_cond=" and b.from_order_id in($poIds)";
		$otst_po_cond=" and a.from_order_id in($poIds)";
	}

	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr 			= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		= return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );

	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}
	$store_cond_rcv = ($cbo_store_name)?" and e.store_id in($cbo_store_name)":"";
	$store_cond_trans = ($cbo_store_name)?" and b.to_store in($cbo_store_name)":"";
		/*$main_query="select c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id
		from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details c
		where a.entry_form in(2,22,58) and a.id=e.mst_id and e.id=b.trans_id and b.id=c.dtls_id and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $trans_date $poIds_cond_roll $store_cond_2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and c.booking_without_order=0
		union all
		select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $store_cond_1 and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1
		union all
		select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $store_cond_1 and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1
		union all
		select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
		from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $store_cond_1 and c.booking_without_order=0 and a.status_active=1 and b.status_active=1 and c.status_active=1";
	*/
	$main_query="select c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id
	from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.entry_form in(2,22,58,84) and a.id=e.mst_id and e.id=b.trans_id and b.id=c.dtls_id and c.entry_form in(2,22,58,84) and c.status_active=1 and c.is_deleted=0 $trans_date $poIds_cond_roll $store_cond_rcv and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and c.booking_without_order=0
	union all
	select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $store_cond_trans and c.booking_without_order=0 and a.status_active=1 and b.status_active=1 and c.status_active=1
	union all
	select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $store_cond_trans and nvl(c.booking_without_order,0) =0 and a.status_active=1 and b.status_active=1 and c.status_active=1
	union all
	select c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type,b.to_store store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 $transfer_date $poIds_cond_roll $store_cond_trans and c.booking_without_order=0 and a.status_active=1 and b.status_active=1 and c.status_active=1";
	//echo $main_query;die;

	$result = sql_select($main_query);
	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
	if($r_id2)
	{
		oci_commit($con);
	}

	if(!empty($result))
	{
		foreach ($result as $row)
		{
			$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

			$r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")");
			if($r_id) 
			{
				$r_id=1;
			} 
			else 
			{
				echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")";
				oci_rollback($con);
				die;
			}
		}
	}
	else
	{
		echo "Data Not Found";
		die;
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}


	$barcodeArr = array_filter($barcodeArr);
	if(count($barcodeArr ) >0 )
	{
		/*
			$receive_barcodes = implode(",", $barcodeArr);
			if($db_type==2 && count($barcodeArr)>999)
			{
				$barcode_chunk=array_chunk($barcodeArr,999) ;
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

			$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 $barcode_cond");
		*/
		$split_chk_sql = sql_select("select c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c, tmp_barcode_no d where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 and b.barcode_no = d.barcode_no and d.userid =$user_id");


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

			//$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form =61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond");
			$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form =62 and a.roll_split_from = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$production_sql = sql_select("select b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, tmp_barcode_no d where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58) and a.trans_id=0 and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid = $user_id order by c.entry_form desc");
		foreach ($production_sql as $row)
		{
			$prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
			$prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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

			if($row[csf('receive_basis')] == 2 )
			{
				$program_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
			}
		}

		$febric_description_arr = array_filter($allDeterArr);
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

		$all_color_arr = array_filter($allColorArr);
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

		if(count($program_id_arr) >0 )
		{

			$program_ids = implode(",", $program_id_arr);
			$programCond = $program_id_cond = "";
			if($db_type==2 && count($program_id_arr)>999)
			{
				$program_id_arr_chunk=array_chunk($program_id_arr,999) ;
				foreach($program_id_arr_chunk as $chunk_arr)
				{
					$programCond.=" id in(".implode(",",$chunk_arr).") or ";
				}
				$program_id_cond.=" and (".chop($programCond,'or ').")";
			}
			else
			{
				$program_id_cond=" and id in($program_ids)";
			}

			$plan_arr=array();
			$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls where status_active=1 $program_id_cond");
			foreach($plan_data as $row)
			{
				$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
			}
			unset($plan_data);
		}

		$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

			$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
			foreach ($yarn_sql as $row)
			{
				$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
				$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
				$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
			}
		}
	}

	foreach ($result as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] == 1)
		{
			$production_company= $company_short_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		else
		{
			$production_company= $supplier_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}

		$machine_dia_gg='';
		if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
		}

		$fabrication = $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["width"]  . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"]."*".$production_company;

		$dataArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication] += $row[csf('qnty')];
		$storeWiseStockArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += number_format($row[csf('qnty')],2,'.','');
		$storeWiseRcvArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] += $row[csf('qnty')];
		$storeRcvBarcodeArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_id')]] .=$row[csf("barcode_no")]."=".$row[csf('qnty')].",";
		$stockBarArr[$poArr[$row[csf('po_breakdown_id')]]."!".$fabrication."!".$row[csf('store_id')]][$row[csf("barcode_no")]]+=$row[csf('qnty')];
	}

	$iss_qty_sql=sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty, d.store_name from pro_roll_details c, inv_grey_fabric_issue_dtls d where c.dtls_id = d.id and c.mst_id = d.mst_id and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll and c.booking_without_order =0
		union all
		select a.po_breakdown_id, c.barcode_no, c.qnty, b.from_store as store_name from order_wise_pro_details a, inv_item_transfer_dtls b, pro_roll_details c where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $poIds_cond_trans_roll and c.booking_without_order=0
		union all
		select b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, b.from_store as store_name
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $ctct_po_cond  and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1  and nvl(c.booking_without_order,0) =0 
		group by c.barcode_no, b.from_order_id, b.from_store
		union all
		select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, b.from_store as store_name
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $otst_po_cond and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1
		group by c.barcode_no, a.from_order_id, b.from_store ");

	foreach ($iss_qty_sql as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] == 1)
		{
			$production_company= $company_short_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		else
		{
			$production_company= $supplier_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]];
		}
		$machine_dia_gg='';
		if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
		{
			$machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
		}

		$fabrication = $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["width"]  . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] . "*" . $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"]."*".$production_company;

		$mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
		if($mother_barcode_no != "")
		{
			if($prodBarcodeData[$mother_barcode_no]["knitting_source"]==1)
			{
				$production_company= $company_short_arr[$prodBarcodeData[$mother_barcode_no]["knitting_company"]];
			}
			else
			{
				$production_company=$supplier_arr[$prodBarcodeData[$mother_barcode_no]["knitting_company"]];
			}

			$machine_dia_gg='';

			if($prodBarcodeData[$mother_barcode_no]["receive_basis"]==2)
			{
				$machine_dia_gg=$plan_arr[$prodBarcodeData[$mother_barcode_no]["booking_id"]];
			}

			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"]  . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_prod_id"]. "*" . $machine_dia_gg. "*" . $prodBarcodeData[$mother_barcode_no]["machine_no_id"]."*".$production_company;
		}

		$storeWiseStockArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_name')]] -= number_format($row[csf('qnty')],2,'.','');
		$storeWiseIssArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_name')]] += $row[csf('qnty')];
		$storeIssueBarcodeArr[$poArr[$row[csf('po_breakdown_id')]]][$fabrication][$row[csf('store_name')]] .=$row[csf("barcode_no")]."=".$row[csf('qnty')].",";
		$stockBarArr[$poArr[$row[csf('po_breakdown_id')]]."!".$fabrication."!".$row[csf('store_name')]][$row[csf("barcode_no")]]-=$row[csf('qnty')];


	}

	unset($iss_qty_sql);
	unset($result);

	$width = (1266+($num_of_store*110));
	ob_start();
	?>
	<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
		<table width="100%" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" border="1">
			<thead>
				<tr>
					<th width='30'>SL</th>
					<th width='150'>Construction</th>
					<th width='110'>Color</th>
					<th width='110'>Color Range</th>
					<th width='80'>Y. Count</th>
					<th width='80'>Y. Type</th>
					<th width='116'>Y. Composition</th>
					<th width='100'>Brand</th>
					<th width='110'>Yarn Lot</th>
					<th width='110'>MC Dia and Gauge</th>
					<th width='80'>F/Dia</th>
					<th width='110'>S. Length</th>
					<th width='60'>GSM</th>
					<th width='110'>M/C No.</th>
					<th width='110'>Knitting Company</th>
					<th width='80'>Total Stock Qty.</th>
					<?
					foreach ($stores as $store) {
						?>
						<th width="110"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
				</tr>
			</thead>
			<?
			if(!empty($dataArr))
			{
				$i=1;

				foreach ($storeWiseRcvArr as $booking_datas=>$po_row)
				{
					foreach ($po_row as $febric_description=>$row)
					{
						$booking_data = explode("*", $booking_datas);
						$fabrication = explode("*", $febric_description);

						$yarn_counts_arr = explode(",", $fabrication[7]);

						$yarn_counts="";
						foreach ($yarn_counts_arr as $count) {
							$yarn_counts .= $count_arr[$count] . ",";
						}
						$yarn_counts = rtrim($yarn_counts, ", ");

						$color_arr = explode(",", $fabrication[1]);
						$colors="";
						foreach ($color_arr as $color) {
							$colors .= $colorArr[$color] . ",";
						}
						$colors = rtrim($colors, ", ");

						$yarn_id_arr = array_unique(array_filter(explode(",", $fabrication[8])));
						$yarn_brand = $yarn_comp = $yarn_type_name = "";
						foreach ($yarn_id_arr as $yid)
						{
							$yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
							$yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
							$yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
						}

						$yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
						$yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
						$yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));

						$machine_dia_gg = $fabrication[9];
						$machine_name =  $machine_arr[$fabrication[10]];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if (!in_array($booking_datas, $checkBookArr))
						{
							$checkBookArr[$i] = $booking_datas;
							if ($i > 1)
							{
								?>
								<tr>
									<th colspan="15" width="1466" align="right">Job Total:</th>
									<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
									<?
									$store_ref = implode(",",array_unique(explode(",",chop($store_ref,","))));
									foreach ($stores as $store)
									{
										?>
										<th width="110" align="right"><? echo number_format($sub_store_stock[$store_ref][$store[csf("id")]],2,".","");?></th>
										<?
									}
									?>
								</tr>
								<?
								$sub_tot_stock = 0;	$store_ref="";
							}
							?>
							<tr>
								<td colspan="<? echo $num_of_store+16;?>"><b>Buyer: <? echo $buyer_arr[$booking_data[0]].", Job No : ".$booking_data[1].", Reference No : ".$booking_data[3].", Booking No : ".$booking_data[2]; ?></b></td>
							</tr>
							<?
						}
						?>
						<tr>
							<td width='30'><? echo $i;//."<br/>".$booking_datas; ?></td>
							<td widtd='150' ><? echo $constuction_arr[$fabrication[0]];?></td>
							<td widtd='110' ><? echo $colors;?></td>
							<td widtd='110' ><? echo $color_range[$fabrication[2]];?></td>
							<td widtd='80' ><? echo $yarn_counts;?></td>
							<td widtd='80' ><? echo $yarn_type_name;?></td>
							<td widtd='116' ><? echo $yarn_comp;?></td>

							<td widtd='100' ><? echo $yarn_brand;?></td>
							<td widtd='110' ><? echo $fabrication[6];?></td>
							<td widtd='110' ><? echo $machine_dia_gg;?></td>
							<td widtd='80' ><? echo $fabrication[4];?></td>
							<td widtd='110'><? echo $fabrication[5];?></td>
							<td widtd='60' ><? echo $fabrication[3];?></td>
							<td widtd='110' ><? echo $machine_name;?></td>
							<td widtd='110' ><? echo $fabrication[11];?></td>

							<?
							$stock=0;
							foreach ($stores as $store)
							{
								$stock += $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$sub_tot_stock +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];

								$sub_store_stock[$booking_datas][$store[csf("id")]] +=$storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$store_ref .= $booking_datas.",";

							}
							?>
							<td width="100" align="right"><? echo number_format($stock,2,".",""); ?></td>
							<?
							$stock=$store_stock=0;
							foreach ($stores as $store)
							{
								$store_stock = $storeWiseStockArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_receive = $storeWiseRcvArr[$booking_datas][$febric_description][$store[csf("id")]];
								$total_issue = $storeWiseIssArr[$booking_datas][$febric_description][$store[csf("id")]];
								?>
								<td width="110" align="right" title="<? echo "Store=".$store[csf("id")].'**'.$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","");?>"><? echo number_format($store_stock,2,".","");//."<br/> Recv= ".$storeRcvBarcodeArr[$booking_datas][$febric_description][$store[csf("id")]]."<br/> Issue= ".$storeIssueBarcodeArr[$booking_datas][$febric_description][$store[csf("id")]];

								$barcdoeS="";
								foreach ($stockBarArr[$booking_datas."!".$febric_description."!".$store[csf("id")]] as $key => $value) 
								{
									if($value<0)
									{
										$barcdoeS.=$key.",";
									}
									
								}
								echo "<br/>".$barcdoeS;


								?></td>
								<?

							}
							?>

						</tr>
						<?

						$i++;
					}
				}
				?>
				<tr>
					<th colspan="15" align="right">Job Total:</th>
					<th width="100" align="right"><? echo number_format($sub_tot_stock,2,".","");?></th>
					<?
					foreach ($stores as $store)
					{
						?>
						<th width="110" align="right"><? echo number_format($sub_store_stock[$booking_datas][$store[csf("id")]],2,".","");?></th>
						<?
					}
					?>
				</tr>
				<?
			}
			?>
		</table>
	</fieldset>
	<?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "s";

	$html = ob_get_contents();
	ob_clean();

	foreach (glob("sswgfsc_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename="sswgfsc_".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$report_button";
	//echo "$html####$filename####$report_button";
	exit;
}
if($action=="report_generate_exel_only_3")
{
	$started = microtime(true);
	session_start();
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
	$cbo_value_with=str_replace("'","",$cbo_value_with);
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
	$cbo_shiping_status = trim(str_replace("'","",$cbo_shiping_status));

	$cbo_store_name = trim(str_replace("'","",$cbo_store_name));
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_3=""; else $store_cond_3=" and b.from_store in ($cbo_store_name) ";

	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '%".trim($txt_order_no)."%'";
	if($cbo_shiping_status>0) $po_cond.=" and a.shiping_status=$cbo_shiping_status";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '%".trim($txt_file_no)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping = '".trim($txt_ref_no)."'";

	if($hide_booking_id!="") $bookiing_id_cond=" and c.booking_id LIKE '%".trim($hide_booking_id)."%'";
	if($txt_booking_no!="") $bookiing_no_cond=" and c.booking_no LIKE '%".trim($txt_booking_no)."%'";

	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_date_from)=="") $transfer_date=""; else $transfer_date= " and a.transfer_date <=".$txt_date_from.""; 


	$store_cond = ($cbo_store_name!="")?" and a.id in($cbo_store_name)":"";
	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id $store_cond order by a.company_id,a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"] ++;
		$num_of_store++;
	}

	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=25 and type in (7,8)");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (25) and ref_from in (11,12,13,14)");
	if($r_id2)
	{
		oci_commit($con);
	}

	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="SELECT * FROM
		(select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,TO_CHAR(b.insert_date,'YYYY') as year, sum(grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id = c.pre_cost_fabric_cost_dtls_id $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,b.insert_date 
		union all
		select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,TO_CHAR(b.insert_date,'YYYY') as year, sum( c.grey_fab_qnty) as grey_fab_qnty from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_conv_cost_dtls e where b.company_name=$cbo_company_id and c.booking_type in (4) and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id = c.pre_cost_fabric_cost_dtls_id and d.id = e.fabric_description $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond $bookiing_no_cond group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no,d.body_part_id, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short,b.style_ref_no,b.insert_date ) t order by job_no,grouping,fabric_color_id ";


	$result=sql_select($sql);
	if(!empty($result))
	{
		foreach($result as $row)
		{
			$tot_rows++;

			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}

			$ref_file = $row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('booking_no')]."_".$row[csf('file_no')]."_".$row[csf('grouping')]."_".$row[csf('body_part_id')]."_".$row[csf('lib_yarn_count_deter_id')]."_".$row[csf('grey_fab_qnty')]."_".$booking_type."##";
			$poIds.=$row[csf('id')].",";

			$poIdsArr[$row[csf('id')]]=$row[csf('id')];

			$poArr[$row[csf('id')]]=$row[csf('job_no')];
			$fileRefArr[$row[csf('job_no')]].=$ref_file;
			$popup_job_ref[$row[csf('job_no')]] .= $row[csf('id')].",";

			if($row[csf('shiping_status')] != 3 && $shipStat[$row[csf('job_no')]] =="")
			{
				$shipStat[$row[csf('job_no')]] = $row[csf('shiping_status')];
			}

			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['buyer_name']=$row[csf('buyer_name')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['job_no']=$row[csf('job_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['booking_no']=$row[csf('booking_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['file_no']=$row[csf('file_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['grouping']=$row[csf('grouping')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$bookingInfoArr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['year']=$row[csf('year')];
			$bookingInfoArrReqQnty[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];

			$infoForTranserByJobWise[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['year']=$row[csf('year')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['file_no']=$row[csf('file_no')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['grouping']=$row[csf('grouping')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			$infoForTranserByJobWise[$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
			
		}

	}
	else
	{
		echo "Data Not Found";die;
	}
	/*echo "<pre>";
	print_r($bookingInfoArrReqQnty);echo "</pre>";die;*/
	unset($result);

	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 11,$poIdsArr, $empty_arr); //Order id temporary insert

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$recvDtlsDataArr=array();$recvDtlsDataStoreArr=array();

	$query="SELECT * FROM
		(select a.id, a.entry_form, a.receive_date, b.febric_description_id, b.color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 1 as type,e.store_id 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction e, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=e.id and a.id=c.mst_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $trans_date $store_cond_2 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=11 and c.booking_without_order=0 and e.status_active=1 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form in(83,183) and c.entry_form in(83,183) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and a.to_order_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=11 and c.booking_without_order =0 and c.is_sales=0 
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, 2 as type ,b.to_store as store_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 $transfer_date $store_cond_1 and c.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=25 and g.ref_from=11 and nvl(c.booking_without_order,0) =0  and c.is_sales=0) abc order by color_id";

	//echo $query;//die;
	$data_array=sql_select($query);

	foreach($data_array as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
		$trans_po_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];		

		if($row[csf("type")]==2)
		{
			$TransDataArrTrans[$row[csf('barcode_no')]]["store_id"]=$row[csf('store_id')];
			//echo $row[csf('barcode_no')].'='.$row[csf('store_id')].'='.$row[csf('qnty')].'='.$row[csf('entry_form')]."<br/>";
		}
	}

	foreach ($ref_barcode_arr as $barcodeno)
	{
		if($barcodeArr[$barcodeno]=="")
		{
			$barcodeArr[$barcodeno] = $barcodeno;

			$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 7, ".$barcodeno.")");
			if($r_id) 
			{
				$r_id=1;
			} 
			else 
			{
				echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 7, ".$barcodeno.")";
				oci_rollback($con);
				die;
			}
		}
	}

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if(!empty($ref_barcode_arr))
	{
		$split_chk_sql = sql_select("SELECT c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no d where b.entry_form = 75 and b.split_from_id = c.roll_split_from and b.status_active = 1 and c.status_active = 1 and b.barcode_no = d.barcode_no and d.userid=$user_id and d.entry_form=25 and d.type=7");

		if(!empty($split_chk_sql))
		{
			foreach ($split_chk_sql as $val)
			{
				if($split_barcode_arr[$val[csf("barcode_no")]]=="")
				{
					$split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];

					$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 8, ".$val[csf('barcode_no')].")");
					if($r_id) 
					{
						$r_id=1;
					} 
					else 
					{
						echo "insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 25, 8, ".$val[csf('barcode_no')].")";
						oci_rollback($con);
						die;
					}
				}
			}

			oci_commit($con);

			$split_ref_sql = sql_select("SELECT a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b, tmp_barcode_no c where a.entry_form = 62 $split_barcode_no_cond and a.roll_split_from = b.id and a.status_active =1 and b.status_active=1 and a.barcode_no=c.barcode_no and c.userid=$user_id and c.entry_form=25 and c.type=8");
			if(!empty($split_ref_sql))
			{
				foreach ($split_ref_sql as $value)
				{
					$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
				}
			}
		}

		$recvDataArrTrans=array();$recvDataArr=array();

		$sqlRecvT="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id,a.store_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.barcode_no = d.barcode_no and d.userid=$user_id and d.entry_form=25 and d.type=7 and c.is_sales=0 order by a.entry_form desc";
		//echo $sqlRecvT; die;
		$recvDataT=sql_select($sqlRecvT);
		foreach($recvDataT as $row)
		{
			$yarn_prod_id_arr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
		}

		$yarn_prod_id_arr = array_filter($yarn_prod_id_arr);
		if(count($yarn_prod_id_arr)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 12,$yarn_prod_id_arr, $empty_arr);
			$yarn_type_id_arr=  return_library_array("select a.id, a.yarn_type from product_details_master a, GBL_TEMP_ENGINE b where a.status_active = 1 and a.id=b.ref_val and b.user_id=$user_id and b.entry_form=25 and b.ref_from=12 ","id","yarn_type");
		}

		foreach($recvDataT as $row)
		{
			$ref_febric_description_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
			$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
			//$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

			$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
			$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"]=$row[csf('store_id')];
			if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
			{
				$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
			}
			if($row[csf('color_id')]!="")
			{
				$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
			}
		}
		unset($recvDataT);
	}

	$all_color_ids = implode(",", $all_color_arr);
	$all_color_arr_exp=explode(",", $all_color_ids);
	$all_color_arrs = array_filter($all_color_arr_exp);
	if(!empty($all_color_arrs))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 25, 13,$all_color_arrs, $empty_arr);
		$color_arr=return_library_array( "select a.id, a.color_name from lib_color a, GBL_TEMP_ENGINE b  where a.status_active=1 and a.id=b.ref_val and b.entry_form=25 and b.ref_from=13 and b.user_id=$user_id", "id", "color_name" );
	}

	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$ref_febric_description_arr = array_filter($ref_febric_description_arr);

	if(!empty($ref_febric_description_arr))
	{
		/*$ref_febric_description_ids = implode(",", $ref_febric_description_arr);
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
		}*/
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id from GBL_TEMP_ENGINE g, lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where g.ref_val=a.id and g.entry_form=25 and g.ref_from=14 and g.user_id=$user_id and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		// echo $sql_deter;die;
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

	$transfer_out_sql=sql_select("SELECT d.transfer_date, a.po_breakdown_id, c.barcode_no, c.qnty,b.from_store as store_id from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d, GBL_TEMP_ENGINE g where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $store_cond_3 and a.po_breakdown_id=g.ref_val and g.entry_form=25 and g.ref_from=11 and g.user_id=$user_id and c.booking_without_order =0 and b.mst_id = d.id and d.entry_form=83 and c.is_sales=0 
	union all
	select a.transfer_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store as store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $store_cond_3 and b.from_order_id=g.ref_val and g.entry_form=25 and g.ref_from=11 and g.user_id=$user_id and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1  and nvl(c.booking_without_order,0) =0 and c.is_sales=0  
	group by a.transfer_date, c.barcode_no, b.from_order_id,b.from_store
	union all
	select a.transfer_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.from_store as store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE g
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $store_cond_3 and a.from_order_id=g.ref_val and g.entry_form=25 and g.ref_from=11 and g.user_id=$user_id and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 and c.is_sales=0
	group by a.transfer_date, c.barcode_no, a.from_order_id,b.from_store ");

	$ref_file="";$data_prod=""; $trans_out_barcode_arr = array();
	foreach($transfer_out_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];

		$trans_out_qty_arr[$ref_file][$data_prod]["trans_out"] +=$row[csf("qnty")];
		$trans_outStore_qty_arr[$ref_file][$row[csf('store_id')]][$data_prod]["trans_out"] +=$row[csf("qnty")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('transfer_date')]))
		{
			$trans_out_qty_arr[$ref_file][$data_prod]["today_trans_out"] +=$row[csf("qnty")];
		}
	}

	unset($transfer_out_sql);
	$iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty, d.issue_date, max(d.issue_date) max_issue_date,d.issue_number,c.id,b.color_id,e.store_id from pro_roll_details c, inv_issue_master d ,inv_grey_fabric_issue_dtls b, inv_transaction e, GBL_TEMP_ENGINE g where c.mst_id = d.id and d.id=b.mst_id and b.trans_id=e.id and b.id=c.dtls_id  and d.entry_form=61 and c.entry_form=61 $store_cond_2 and c.po_breakdown_id=g.ref_val and g.entry_form=25 and g.ref_from=11 and g.user_id=$user_id and c.booking_without_order = 0 and d.id = e.mst_id and e.transaction_type=2 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.is_sales=0 group by c.po_breakdown_id,c.barcode_no,c.qnty,d.issue_date,d.issue_number,c.id,b.color_id,e.store_id");

	$ref_file="";$data_prod=""; $issue_barcode_arr = array();$issueDataArrTrans = array();
	foreach($iss_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];

		$mother_barcode_no = $mother_barcode_arr[$row[csf('barcode_no')]];
		if($mother_barcode_no != "")
		{
			$data_prod = $recvDataArrTrans[$mother_barcode_no]["febric_description_id"]."**".$recvDataArrTrans[$mother_barcode_no]["body_part_id"];
		}
		$iss_qty_arr[$ref_file][$data_prod]["issue"] +=$row[csf("qnty")];
		$iss_qtyStore_arr[$ref_file][$row[csf("store_id")]][$data_prod]["issue"] +=$row[csf("qnty")];
		$iss_qty_arr[$ref_file][$data_prod]["barcode_no"].=$row[csf("barcode_no")]."=".$row[csf("qnty")].",";

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('issue_date')])){
			$iss_qty_arr[$ref_file][$data_prod]["today_issue"] +=$row[csf("qnty")];
		}

		$iss_qty_arr[$ref_file][$data_prod]["max_issue_date"] = $row[csf('max_issue_date')];
		$recvDtlsDataStoreArr[$ref_file][$row[csf('store_id')]][$data_prod]['issue']+=$row[csf("qnty")];
	}
	
	unset($iss_qty_sql);

	/*echo "<pre>";
	print_r($issueDataArrTrans);*/

	$ref_file="";$data_prod="";$issue_return_barcode_arr =array();
	$iss_rtn_qty_sql=sql_select("SELECT d.receive_date, c.po_breakdown_id, c.barcode_no, c.qnty,e.store_id,f.color_id  from pro_roll_details c, inv_receive_master d, inv_transaction e,pro_grey_prod_entry_dtls f, GBL_TEMP_ENGINE g where c.entry_form=84 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and d.entry_form=84 and c.status_active=1 and c.is_deleted=0  and e.transaction_type= 4 and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and f.status_active =1 and f.is_deleted=0 $store_cond_2 and c.po_breakdown_id=g.ref_val and g.entry_form=25 and g.ref_from=11 and g.user_id=$user_id and c.is_sales=0");
	foreach($iss_rtn_qty_sql as $row)
	{
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];
		$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];

		$iss_rtn_qty_arr[$ref_file][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		$iss_rtn_qty_store_arr[$ref_file][$row[csf('store_id')]][$data_prod]["issue_ret"]+=$row[csf("qnty")];
		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
		{
			$iss_rtn_qty_arr[$ref_file][$data_prod]["today_issue_ret"]+=$row[csf("qnty")];
		}
	}
	unset($iss_rtn_qty_sql);

	$ref_file="";$data_prod="";$min_date="";$data_prod_store=""; $infoForTranserByJobWisex=array();
	foreach($data_array as $row)
	{
		if( $row[csf("type")]==2)
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];
			//$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];

			
			//$store_index=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			$store_index=$TransDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			//echo $ref_file.'='.$row[csf('barcode_no')].'='.$store_index."<br/>";

			
			$recvDtlsDataArr[$ref_file][$data_prod]['transfer_in']+=$row[csf("qnty")];
			//$recvDtlsDataStoreArr[$ref_file][$store_index][$data_prod]['transfer_in']+=$row[csf("qnty")];
			$recvDtlsDataStoreArr[$ref_file][$row[csf('store_id')]][$data_prod]['transfer_in']+=$row[csf("qnty")];

			//$trans_outStore_qty_arr[$ref_file][$row[csf('store_id')]][$data_prod]["trans_out"] +=$row[csf("qnty")];



			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
				$recvDtlsDataStoreArr[$ref_file][$store_index][$data_prod]['today_transfer_in']+=$row[csf("qnty")];
			}


			$infoForTranserByJobWisex[$ref_file]['buyer_name']=$infoForTranserByJobWise[$ref_file]['buyer_name'];
			$infoForTranserByJobWisex[$ref_file]['year']=$infoForTranserByJobWise[$ref_file]['year'];
			$infoForTranserByJobWisex[$ref_file]['file_no']=$infoForTranserByJobWise[$ref_file]['file_no'];
			$infoForTranserByJobWisex[$ref_file]['grouping']=$infoForTranserByJobWise[$ref_file]['grouping'];
			$infoForTranserByJobWisex[$ref_file]['style_ref_no']=$infoForTranserByJobWise[$ref_file]['style_ref_no'];
			$infoForTranserByJobWisex[$ref_file]['job_no']=$infoForTranserByJobWise[$ref_file]['job_no'];


		}
		else
		{
			$ref_file=$poArr[$row[csf('po_breakdown_id')]];
			$data_prod = $recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]."**".$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"];
			//$color_index=$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"];


			/*$store_indexRec=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			$store_indexIss=$issueDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			if($store_indexRec!=$store_indexIss){
				$store_index=$issueDataArrTrans[$row[csf('barcode_no')]]["store_id"];
			}*/


			$store_index=$recvDataArrTrans[$row[csf('barcode_no')]]["store_id"];

			$recvDtlsDataArr[$ref_file][$data_prod]['recv']+=$row[csf("qnty")];
			$recvDtlsDataStoreArr[$ref_file][$store_index][$data_prod]['recv']+=$row[csf("qnty")];
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($row[csf('receive_date')]))
			{
				$recvDtlsDataArr[$ref_file][$data_prod]['today_recv']+=$row[csf("qnty")];
			}
		}

		if($recvDtlsDataArr[$ref_file][$data_prod]["min_date"] =="")
		{
			$recvDtlsDataArr[$ref_file][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
		else if($recvDtlsDataArr[$ref_file][$data_prod]["min_date"] > strtotime($row[csf("receive_date")]))
		{
			$recvDtlsDataArr[$ref_file][$data_prod]["min_date"]=strtotime($row[csf("receive_date")]);
		}
	}
	/*echo "<pre>";
	print_r($recvDtlsDataStoreArr);
	echo "</pre>";
	unset($data_array);*/	

	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=25 and type in (7,8)");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=25 and ref_from in (11,12,13,14)");
	oci_commit($con);
	disconnect($con);

	ob_start();
	$width = (2620+($num_of_store*110));
	?>
	<fieldset style="width:2200px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table style="width:<? echo $width+20; ?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="9" style='padding:0px 1px 0px 0px'>Booking Info</th>
					<th colspan="<? echo 20+$num_of_store; ?>">Receive Info</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="70" rowspan="2">Buyer</th>
					<th width="90" rowspan="2">Job No</th>


					<th width="50" rowspan="2">Year</th>
					<th width="100" rowspan="2">Style</th>
					<th width="80" rowspan="2">Ref. No</th>
					
					<th width="70" rowspan="2">File No</th>
					
					<th width="110" rowspan="2">Body Part</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="80" rowspan="2">Grey Fabric Qty(Kg)</th>
					
					<th colspan="3">Today Receive</th>
					<th colspan="4">Total Receive</th>
					<th rowspan="2" width="105">Received Balance</th>
					<th colspan="2">Today Issue</th>
					<th colspan="3">Total Issue</th>
					<th width="105" rowspan="2">Stock Qty.</th>

					<?
					foreach ($stores as $store) {
						?>
						<th rowspan="2" width="110" title="<? echo $store[csf("id")];?>"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
					<th width="50" rowspan="2">DOH</th>
				</tr>
				<tr>
					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer In</th>

					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer In</th>
					<th width="90">Grand Total</th>

					<th width="90">Issue</th>
					<th width="90">Transfer Out</th>

					<th width="90">Issue</th>
					<th width="90">Transfer Out</th>
					<th width="90">Grand Total</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table style="width:<? echo $width+20; ?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				$job_nos="";
				foreach($fileRefArr as $job_no=>$fileRefArrData)
				{
					$stock_qty_smry_arr[$job_no]=0;
					/*if(($cbo_shiping_status ==1 && $shipStat[$job_no] !="") || ($cbo_shiping_status ==2 && $shipStat[$job_no] =="") || ($cbo_shiping_status ==0 && $shipStat[$job_no] !="" || $cbo_shiping_status ==0 && $shipStat[$job_no] ==""))
					{
					*/
						/*foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
						{
							foreach ($colorData as $PartConstColor => $val)
							{*/
						foreach ($recvDtlsDataArr[$job_no] as $PartConstBody => $val) 
						{
							
								$issue_ret_smry = $iss_rtn_qty_arr[$job_no][$PartConstBody]["issue_ret"];
								$transfer_in_smry = $recvDtlsDataArr[$job_no][$PartConstBody]['transfer_in'];

								$issue_smry = $iss_qty_arr[$job_no][$PartConstBody]["issue"];
								$trans_out_smry = $trans_out_qty_arr[$job_no][$PartConstBody]["trans_out"];

								$total_issue_smry = $issue_smry+$trans_out_smry;
								$total_receive_smry = $val["recv"] + $issue_ret_smry + $transfer_in_smry;
								$stock_qty_smry = $total_receive_smry-$total_issue_smry;
								$stock_qty_smry = number_format($stock_qty_smry,2,".","");

								//if(($cbo_value_with==1 && $stock_qty_smry>=0) || ($cbo_value_with==2 && $stock_qty_smry>0))
								if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty_smry>0))
								{
									$job_nos.=$job_no."=";
									$stock_qty_smry_arr[$job_no]=1;
								}	
						}
					//}
				}
				$i=1;
				$job_nos=chop($job_nos,",");
				//echo $job_nos; die;
				$storeIDwiseTotal=array();
				$job_nos=array_unique(explode("=", $job_nos));
				foreach ($job_nos as $job_no) 
				{
					//echo $job_no.",";
					/*foreach ($recvDtlsDataArr[$job_no] as $colorIndex => $colorData) 
					{*/
						foreach ($recvDtlsDataArr[$job_no] as $PartConstBody => $val)
						{
							$PartConstColorArr = explode("**", $PartConstBody);
							$deter_id = $PartConstColorArr[0];
							$body_part_id = $PartConstColorArr[1];
							//$color_id = $PartConstColorArr[2];

							/*$color_names="";
							$color_ids = explode(",", $color_id);
							foreach ($color_ids as $color) 
							{
								$color_names .=  $color_arr[$color].",";
							}
							$color_names = chop($color_names,",");*/

							$trans_out = $trans_out_qty_arr[$job_no][$PartConstBody]["trans_out"];
							$today_trans_out = $trans_out_qty_arr[$job_no][$PartConstBody]["today_trans_out"];

							$issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstBody]["issue_ret"];
							$today_issue_ret = $iss_rtn_qty_arr[$job_no][$PartConstBody]["today_issue_ret"];

							$transfer_in = $recvDtlsDataArr[$job_no][$PartConstBody]['transfer_in'];
							$today_transfer_in = $recvDtlsDataArr[$job_no][$PartConstBody]['today_transfer_in'];
							$total_receive = $val["recv"] + $issue_ret + $transfer_in;
							
							$greyQnty= $bookingInfoArrReqQnty[$job_no][$body_part_id][$deter_id]['grey_fab_qnty'];
							//$totalRecevBalance=$greyQntyArr[$body_part_id][$deter_id][$colorIndex]-$total_receive;
							$totalRecevBalance=$greyQnty-$total_receive;

							$issue = $iss_qty_arr[$job_no][$PartConstBody]["issue"];
							$today_issue = $iss_qty_arr[$job_no][$PartConstBody]["today_issue"];

							$total_issue = $issue+$trans_out;
							$stock_qty = $total_receive-$total_issue;

							$stock_qty=number_format($stock_qty,2,".","");
							$stock_qty= str_replace("-0.00", "0.00", $stock_qty) ;

							//if(($cbo_value_with==1 && $stock_qty>=0) || ($cbo_value_with==2 && $stock_qty>0))
							//if(($cbo_value_with==1) || ($cbo_value_with==2 && $stock_qty>0))
							//{
																	
								

								$max_issue_date = $iss_qty_arr[$job_no][$PartConstBody]["max_issue_date"];
								$min_date = $recvDtlsDataArr[$job_no][$PartConstBody]["min_date"];
								$doh="";
								if($min_date != "")
								{
									$doh = date_diff($min_date,date("Y-m-d"));

									$date1=date_create(date("Y-m-d",$min_date));
									$date2=date_create(date("Y-m-d"));
									$diff=date_diff($date1,$date2);

									$doh = $diff->format("%a");
								}
								
								$po_ids_ref = implode(",",array_unique(explode(",",chop($popup_job_ref[$job_no],","))));
							if($cbo_value_with ==2 && $stock_qty>0.00)
							{
								if($i%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $i;?>','<? echo $bgcolor1;?>')" id="trr<? echo $i;?>">
									<td width="40"><? echo  $i; ?>&nbsp;</td>
									<?
									/*if($bookingInfoArr[$body_part_id][$deter_id]['buyer_name']=="" && $bookingInfoArr[$body_part_id][$deter_id]['job_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['year']=="" && $bookingInfoArr[$body_part_id][$deter_id]['style_ref_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['grouping']=="")
									{*/
										if($val["recv"]==0){
										?>
										<td width="70"><p><? echo $buyer_arr[$infoForTranserByJobWisex[$job_no]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $infoForTranserByJobWisex[$job_no]['job_no']; ?>&nbsp;</p></td>
										<td width="50"><p><? echo $infoForTranserByJobWisex[$job_no]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $infoForTranserByJobWisex[$job_no]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $infoForTranserByJobWisex[$job_no]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $infoForTranserByJobWisex[$job_no]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									else
									{
										?>
										<td width="70"><p><? echo $buyer_arr[$bookingInfoArr[$job_no][$body_part_id][$deter_id]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $job_no;
													//echo $bookingInfoArr[$body_part_id][$deter_id]['job_no']; ?>&nbsp;</p></td>
										<td width="50"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									?>
									<td width="110" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
									<td width="110" title="<? echo $deter_id;?>"><? echo $constuction_arr[$deter_id];?></td>
									
									<td width="80" align="right"><p><?  echo number_format($greyQnty,2);?></p></td>
					

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','today_recv_popup_summary')"><? echo number_format($val["today_recv"],2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_issue_ret;?>','0','today_issue_rtn_popup_summary')"><? echo number_format($today_issue_ret,2);?></a>
									</td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_transfer_in;?>','0','today_trans_in_popup_summary')">
											<? echo number_format($today_transfer_in,2);?>
										</a></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue_ret;?>','0','issue_rtn_popup_summary')"><? echo number_format($issue_ret,2);?></a>
									</td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','trans_in_popup_summary')">
											<? echo number_format($transfer_in,2);?>
										</a>
									</td>
									<td width="90" align="right"><? echo number_format($total_receive,2);?></td>
									<td width="105" align="right"><? echo number_format($totalRecevBalance ,2);?></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','today_issue_popup_summary')"><? echo number_format($today_issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_trans_out;?>','0','today_trans_to_popup_summary')"><? echo number_format($today_trans_out,2);?></a></td>

									<td width="90" align="right" title="<? echo $iss_qty_arr[$job_no][$PartConstBody]["barcode_no"]; ?>"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','trans_to_popup_summary')"><? echo number_format($trans_out,2);?></a></td>
									<td width="90" align="right"><? echo number_format($total_issue,2);?></td>
									<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','0','stock_popup_summary_2')"><? echo number_format($stock_qty,2);?></a></td>
									<?
									$total_issue_return=$store_wise_stock=$stock=0;
									foreach ($stores as $store) {

										/*foreach ($recvDtlsDataStoreArr[$job_no] as $colorIndexx => $colorData) 
										{*/
											foreach ($recvDtlsDataStoreArr[$job_no] as $storeIndex => $StoreData) 
											{
												foreach ($StoreData as $PartConstColorx => $valx)
												{
													$PartConstColorArrx = explode("**", $PartConstColorx);
													$deter_idx = $PartConstColorArrx[0];
													$body_part_idx = $PartConstColorArrx[1];
													$color_idx = $PartConstColorArrx[2];

													$color_names="";
													$color_ids = explode(",", $color_idx);
													foreach ($color_ids as $color) 
													{
														$color_names .=  $color_arr[$color].",";
													}
													$color_names = chop($color_names,",");

													/*$receivexQnty[$job_no][$body_part_idx][$deter_idx][$storeIndex]= $valx["recv"];
													$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["transfer_in"];
													$issue_rtn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_rtn_qty_store_arr[$job_no][$storeIndex][$PartConstColorx]["issue_ret"];
													$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_qtyStore_arr[$job_no][$storeIndex][$PartConstColorx]["issue"];
													$transferOut_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] =$trans_outStore_qty_arr[$job_no][$storeIndex][$PartConstColorx]["trans_out"];*/


													$receivexQnty[$job_no][$body_part_idx][$deter_idx][$storeIndex]= $valx["recv"];
													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];
													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["transfer_in"];

													$transferIn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex]=$recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];

													$issue_rtn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_rtn_qty_store_arr[$job_no][$storeIndex][$PartConstColorx]["issue_ret"];

													$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_qtyStore_arr[$job_no][$storeIndex][$PartConstColorx]["issue"];
													//$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["issue"];

													$transferOut_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] =$trans_outStore_qty_arr[$job_no][$storeIndex][$PartConstColorx]["trans_out"];

													//$arra[store]=$transferIn_qntyx-$transferOut_qntyx

													//echo $total_receivex;
													/*echo "<pre>";
													print_r($total_receivex);
													echo "</pre>";*/

												}
											}
										//}

										/*$totalRecvQnty=$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transfer_inx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 
										$totalIssueQnty=$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; */

										$totalRecvQnty=$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferIn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 

										$totalIssueQnty=$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 
									
										?>
											<td title="<? echo $totalRecvQnty.'-'.$totalIssueQnty; ?>" width="110" align="right">
												<p><?

												//echo $stockTotal= $totalRecvQnty-$totalIssueQnty;

												$stockTotal= $totalRecvQnty-$totalIssueQnty; $stockQntyFormat=number_format($stockTotal,2,".","");
												if($stockQntyFormat==-0.00){$stockQntyFormat=0.00;}
												echo $stockQntyFormat ;



												?>&nbsp;</p></td>
										<?
										$storeIDwiseTotal[$store[csf("id")]]+=$stockTotal;
										
									}
									?>
									
									<td width="50" align="center"><? echo $doh;?></td>
								</tr>
								<?
								$i++;

								$grand_today_recv += $val["today_recv"];
								$grand_today_issue_ret += $today_issue_ret;
								$grand_today_transfer_in += $today_transfer_in;
								$grand_recv += $val["recv"];
								$grand_issue_ret += $issue_ret;
								$grand_transfer_in += $transfer_in;
								$grand_total_receive += $total_receive;
								$grand_total_received_balance += $totalRecevBalance;

								$grand_today_issue += $today_issue;
								$grand_today_trans_out += $today_trans_out;

								$grand_issue += $issue;
								$grand_trans_out += $trans_out;
								$grand_total_issue += $total_issue;
								$grand_stock_qty += $stock_qty;
								
								$grand_req_qty += $greyQnty;

							}
							else if($cbo_value_with ==1)
							{
								if($i%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $i;?>','<? echo $bgcolor1;?>')" id="trr<? echo $i;?>">
									<td width="40"><? echo  $i; ?>&nbsp;</td>
									<?
									/*if($bookingInfoArr[$body_part_id][$deter_id]['buyer_name']=="" && $bookingInfoArr[$body_part_id][$deter_id]['job_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['year']=="" && $bookingInfoArr[$body_part_id][$deter_id]['style_ref_no']=="" && $bookingInfoArr[$body_part_id][$deter_id]['grouping']=="")
									{*/
										if($val["recv"]==0){
										?>
										<td width="70"><p><? echo $buyer_arr[$infoForTranserByJobWisex[$job_no]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $infoForTranserByJobWisex[$job_no]['job_no'] ?>&nbsp;</p></td>
										<td width="50"><p><? echo $infoForTranserByJobWisex[$job_no]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $infoForTranserByJobWisex[$job_no]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $infoForTranserByJobWisex[$job_no]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $infoForTranserByJobWisex[$job_no]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									else
									{
										?>
										<td width="70"><p><? echo $buyer_arr[$bookingInfoArr[$job_no][$body_part_id][$deter_id]['buyer_name']]; ?>&nbsp;</p></td>
										<td width="90"><p><? echo $job_no;
													//echo $bookingInfoArr[$body_part_id][$deter_id]['job_no']; ?>&nbsp;</p></td>
										<td width="50"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['year']; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['style_ref_no']; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['grouping']; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $bookingInfoArr[$job_no][$body_part_id][$deter_id]['file_no']; ?>&nbsp;</p></td>
										<?
									}
									?>
									<td width="110" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
									<td width="110" title="<? echo $deter_id;?>"><? echo $constuction_arr[$deter_id];?></td>
									
									<td width="80" align="right"><p><?  echo number_format($greyQnty,2);?></p></td>
					

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','today_recv_popup_summary')"><? echo number_format($val["today_recv"],2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_issue_ret;?>','0','today_issue_rtn_popup_summary')"><? echo number_format($today_issue_ret,2);?></a>
									</td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_transfer_in;?>','0','today_trans_in_popup_summary')">
											<? echo number_format($today_transfer_in,2);?>
										</a></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $val["recv"];?>','0','recv_popup_summary')"><? echo number_format($val["recv"],2);?></a></td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue_ret;?>','0','issue_rtn_popup_summary')"><? echo number_format($issue_ret,2);?></a>
									</td>
									<td width="90" align="right">
										<a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $transfer_in;?>','0','trans_in_popup_summary')">
											<? echo number_format($transfer_in,2);?>
										</a>
									</td>
									<td width="90" align="right"><? echo number_format($total_receive,2);?></td>
									<td width="105" align="right"><? echo number_format($totalRecevBalance ,2);?></td>

									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','today_issue_popup_summary')"><? echo number_format($today_issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $today_trans_out;?>','0','today_trans_to_popup_summary')"><? echo number_format($today_trans_out,2);?></a></td>

									<td width="90" align="right" title="<? echo $iss_qty_arr[$job_no][$PartConstBody]["barcode_no"]; ?>"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $issue;?>','0','issue_popup_summary')"><? echo number_format($issue,2);?></a></td>
									<td width="90" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $trans_out;?>','0','trans_to_popup_summary')"><? echo number_format($trans_out,2);?></a></td>
									<td width="90" align="right"><? echo number_format($total_issue,2);?></td>
									<td width="105" align="right"><a href="##" onClick="openmypage('<? echo $job_no;?>','<? echo $PartConstBody."**0**12";?>','<? echo change_date_format($max_issue_date);?>','<? echo $po_ids_ref?>','<? echo $stock_qty;?>','0','stock_popup_summary_2')"><? echo number_format($stock_qty,2);?></a></td>
									<?
									$total_issue_return=$store_wise_stock=$stock=0;
									foreach ($stores as $store) {

										/*foreach ($recvDtlsDataStoreArr[$job_no] as $colorIndexx => $colorData) 
										{*/
											foreach ($recvDtlsDataStoreArr[$job_no] as $storeIndex => $StoreData) 
											{
												foreach ($StoreData as $PartConstColorx => $valx)
												{
													$PartConstColorArrx = explode("**", $PartConstColorx);
													$deter_idx = $PartConstColorArrx[0];
													$body_part_idx = $PartConstColorArrx[1];
													$color_idx = $PartConstColorArrx[2];

													$color_names="";
													$color_ids = explode(",", $color_idx);
													foreach ($color_ids as $color) 
													{
														$color_names .=  $color_arr[$color].",";
													}
													$color_names = chop($color_names,",");

													$receivexQnty[$job_no][$body_part_idx][$deter_idx][$storeIndex]= $valx["recv"];
													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];
													


													//$transfer_inx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["transfer_in"];

													$transferIn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex]=$recvDtlsDataStoreArr[$job_no][$storeIndex][$PartConstColorx]['transfer_in'];


													$issue_rtn_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_rtn_qty_store_arr[$job_no][$storeIndex][$PartConstColorx]["issue_ret"];

													$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $iss_qtyStore_arr[$job_no][$storeIndex][$PartConstColorx]["issue"];
													//$issue_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] = $valx["issue"];

													$transferOut_qntyx[$job_no][$body_part_idx][$deter_idx][$storeIndex] =$trans_outStore_qty_arr[$job_no][$storeIndex][$PartConstColorx]["trans_out"];

													//$arra[store]=$transferIn_qntyx-$transferOut_qntyx




													//echo $total_receivex;
													/*echo "<pre>";
													print_r($total_receivex);
													echo "</pre>";*/

												}
											}
										//}

										$totalRecvQnty=$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferIn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 

										$totalIssueQnty=$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]+$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]]; 
									
										?>
											<td title="<? echo $totalRecvQnty.'-'.$totalIssueQnty; ?>" width="110" align="right">
												<p><?  



												//$stockTotal= $totalRecvQnty-$totalIssueQnty; echo  $stockTotal; 

												$stockTotal= $totalRecvQnty-$totalIssueQnty; $stockQntyFormat=number_format($stockTotal,2,".","");
												if($stockQntyFormat==-0.00){$stockQntyFormat=0.00;}
												echo $stockQntyFormat ;


												
												/*echo "<br>recv=".$receivexQnty[$job_no][$body_part_id][$deter_id][$store[csf("id")]];
												echo "<br>issueRtn=".$issue_rtn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];
												echo "<br>transIn=".$transferIn_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];

												echo "<br>issue=".$issue_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];
												echo "<br>transOut=".$transferOut_qntyx[$job_no][$body_part_id][$deter_id][$store[csf("id")]];

												echo "<br>qnty=".$totalRecvQnty.'-'.$totalIssueQnty;*/

												//echo "<br>".$store[csf("id")];

												//echo "<br>transIn=".$recvDtlsDataStoreArr[$job_no][$store[csf("id")]][$deter_id."**".$body_part_id]['transfer_in'];

												?>&nbsp;</p></td>
										<?
										$storeIDwiseTotal[$store[csf("id")]]+=$stockTotal;
										
									}
									?>
									
									<td width="50" align="center"><? echo $doh;?></td>
								</tr>
								<?
								$i++;

								$grand_today_recv += $val["today_recv"];
								$grand_today_issue_ret += $today_issue_ret;
								$grand_today_transfer_in += $today_transfer_in;
								$grand_recv += $val["recv"];
								$grand_issue_ret += $issue_ret;
								$grand_transfer_in += $transfer_in;
								$grand_total_receive += $total_receive;
								$grand_total_received_balance += $totalRecevBalance;

								$grand_today_issue += $today_issue;
								$grand_today_trans_out += $today_trans_out;

								$grand_issue += $issue;
								$grand_trans_out += $trans_out;
								$grand_total_issue += $total_issue;
								$grand_stock_qty += $stock_qty;
								
								$grand_req_qty += $greyQnty;
							
							}
						}

					//}
				}
				?>	

				<tfoot>
					<tr>
						<th width="40">&nbsp;</td>
						<th width="70"><p>&nbsp;</p></th>
						<th width="90"><p>&nbsp;</p></th>
						<th width="50"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="80"><p>&nbsp;</p></th>
						<th width="70"><p>&nbsp;</p></th>
						
						<th width="110"></th>
						<th width="110"><p>Total&nbsp;</p></th>
						<th width="80"><? echo number_format($grand_req_qty,2);?></td>

						<th width="90"><? echo number_format($grand_today_recv,2);?></th>
						<th width="90"><? echo number_format($grand_today_issue_ret,2);?></th>
						<th width="90"><? echo number_format($grand_today_transfer_in,2);?></th>

						<th width="90"><? echo number_format($grand_recv,2);?></th>
						<th width="90"><? echo number_format($grand_issue_ret,2);?></th>
						<th width="90"><? echo number_format($grand_transfer_in,2);?></th>
						<th width="90"><? echo number_format($grand_total_receive,2);?></th>
						<th width="105"><? echo number_format($grand_total_received_balance,2);?></th>

						<th width="90"><? echo number_format($grand_today_issue,2);?></th>
						<th width="90"><? echo number_format($grand_today_trans_out,2);?></th>

						<th width="90"><? echo number_format($grand_issue,2);?></th>
						<th width="90"><? echo number_format($grand_trans_out,2);?></th>
						<th width="90"><? echo number_format($grand_total_issue,2);?></th>

						<th width="105"><? echo number_format($grand_stock_qty,2);?></th>
						<?
						foreach ($stores as $store) {
							?>
							<th width="110"> <? echo number_format($storeIDwiseTotal[$store[csf("id")]],2);?>  </th>
							<?
						}
						?>
						
						<th width="50"></th>
					</tr>
				</tfoot>									
			</table>
		</div>
		
	</fieldset>

	
	<?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "s";

	$html = ob_get_contents();
	ob_clean();

	foreach (glob("sswgfsc_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename="sswgfsc_".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$report_button";
	//echo "$html####$filename####$report_button";
	exit;
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

if($action=="recv_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if($storeId) $store_cond = " and d.store_id in ($storeId)"; else $store_cond = "";
	$query="select a.recv_number as system_number,a.receive_date, c.barcode_no, c.roll_no, c.qnty, d.store_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids)  $booking_without_order and b.trans_id = d.id and c.is_sales=$is_sales $store_cond";

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	?>
	<fieldset style="width:1580px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [5],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<tr>
						<th colspan="6">
							<? 
							if($is_sales==0)
							{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
							}else{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
							}
							?>
						</th>
					</tr>
					<tr>
						<th width="100">Body Part</th>
						<th width="110">Construction</th>
						<th width="110">Color </th>
						<th width="80">Quantity</th>
						<th width="70">Last Issued Date</th>
						<th width="80">DOH</th>
					</tr>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
					<td width="110">
						<p>
							<? 
							$color_id_arr = explode(",", $color_id);
							foreach ($color_id_arr as $val) {
								$color_names .= $color_arr[$val].",";
							}
							echo chop($color_names,",")." &nbsp;"; 
							?>
						</p>
					</td>
					<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
					<td width="70" align="right"><? echo $lst_issue_date; ?></td>
					<td width="80"><p><?  ?>&nbsp;</p></td>
				</tr>
			</table>

			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<thead>
					<tr><th colspan="19">Receive Details</th></tr>
					<tr>
						<th width="80">Store Name</th>
						<th width="110">Transaction ID</th>
						<th width="80">Transaction Date</th>
						<th width="90">Bacode No</th>
						<th width="80">Roll No</th>
						<th width="80">Roll Weight</th>
						<th width="100">Construction</th>
						<th width="80">Color Range</th>
						<th width="80">Y-Count</th>
						<th width="80">Yarn Type</th>
						<th width="80">Yarn Composition</th>
						<th width="80">Brand</th>
						<th width="80">Yarn Lot</th>
						<th width="80">MC Dia & Gauge</th>
						<th width="80">F/Dia</th>
						<th width="80">S. Length</th>
						<th width="80">GSM</th>
						<th width="80">M/C NO.</th>
						<th width="80">Knitting Company</th>
					</tr>
				</thead>
			</table>
			<div style="width:1600px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="80"><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("system_number")]; ?></td>
								<td width="80"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								<td width="90"><p><? echo $row[csf("barcode_no")]; ?></p></td>
								<td width="80" align="right"><? echo $row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><p><? echo $color_range[$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $yarn_count_nos;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_types;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_comps;?></p></td>
								<td width="80" align="center"><p><? echo $brand_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]];?></p></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"]." & ".$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]];?></td>
								<td width="80"><? echo $knitting_company_name;?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;

						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="80">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="80">Total :</th>
						<th width="80" align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="issue_rtn_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if($storeId) $store_cond = " and d.store_id in ($storeId)"; else $store_cond = "";
	$query="select a.recv_number as system_number,a.receive_date, c.barcode_no, c.roll_no, c.qnty, d.store_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(84) and c.entry_form in(84) and d.transaction_type= 4 and d.item_category=13 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids)  $booking_without_order and b.trans_id = d.id and c.is_sales=$is_sales $store_cond";


	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(84) and c.entry_form in(84) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";

	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	?>
	<fieldset style="width:1580px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [5],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<tr>
						<th colspan="6">
							<? 
							if($is_sales==0)
							{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
							}else{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
							}
							?>
						</th>
					</tr>
					<tr>
						<th width="100">Body Part</th>
						<th width="110">Construction</th>
						<th width="110">Color </th>
						<th width="80">Quantity</th>
						<th width="70">Last Issued Date</th>
						<th width="80">DOH</th>
					</tr>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
					<td width="110">
						<p>
							<? 
							$color_id_arr = explode(",", $color_id);
							foreach ($color_id_arr as $val) {
								$color_names .= $color_arr[$val].",";
							}
							echo chop($color_names,",")." &nbsp;"; 
							?>
						</p>
					</td>
					<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
					<td width="70" align="right"><? echo $lst_issue_date; ?></td>
					<td width="80"><p><?  ?>&nbsp;</p></td>
				</tr>
			</table>

			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<thead>
					<tr><th colspan="19">Issue Return Details</th></tr>
					<tr>
						<th width="80">Store Name</th>
						<th width="110">Transaction ID</th>
						<th width="80">Transaction Date</th>
						<th width="90">Bacode No</th>
						<th width="80">Roll No</th>
						<th width="80">Roll Weight</th>
						<th width="100">Construction</th>
						<th width="80">Color Range</th>
						<th width="80">Y-Count</th>
						<th width="80">Yarn Type</th>
						<th width="80">Yarn Composition</th>
						<th width="80">Brand</th>
						<th width="80">Yarn Lot</th>
						<th width="80">MC Dia & Gauge</th>
						<th width="80">F/Dia</th>
						<th width="80">S. Length</th>
						<th width="80">GSM</th>
						<th width="80">M/C NO.</th>
						<th width="80">Knitting Company</th>
					</tr>
				</thead>
			</table>
			<div style="width:1600px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="80"><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("system_number")]; ?></td>
								<td width="80"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								<td width="90"><p><? echo $row[csf("barcode_no")]; ?></p></td>
								<td width="80" align="right"><? echo $row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><p><? echo $color_range[$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $yarn_count_nos;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_types;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_comps;?></p></td>
								<td width="80" align="center"><p><? echo $brand_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]];?></p></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"]." & ".$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]];?></td>
								<td width="80"><? echo $knitting_company_name;?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;

						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="80">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="80">Total :</th>
						<th width="80" align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="today_recv_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$today_date = date("d-M-Y", strtotime($today_date));

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if($storeId) $store_cond = " and d.store_id in ($storeId)"; else $store_cond = "";
	$query="select a.recv_number as system_number,a.receive_date, c.barcode_no, c.roll_no, c.qnty, d.store_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids)  $booking_without_order and b.trans_id = d.id  and d.transaction_date='$today_date' and c.is_sales=$is_sales $store_cond";

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	?>
	<fieldset style="width:1580px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [5],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<?
				if(!empty($result))
				{
			?>
					<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
						<thead>
							<tr>
								<th colspan="6">
									<? 
									if($is_sales==0)
									{
										echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
									}else{
										echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
									}
									?>
								</th>
							</tr>
							<tr>
								<th width="100">Body Part</th>
								<th width="110">Construction</th>
								<th width="110">Color </th>
								<th width="80">Quantity</th>
								<th width="70">Last Issued Date</th>
								<th width="80">DOH</th>
							</tr>
						</thead>
						<tr bgcolor="#FFFFFF">
							<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
							<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
							<td width="110">
								<p>
									<? 
									$color_id_arr = explode(",", $color_id);
									foreach ($color_id_arr as $val) {
										$color_names .= $color_arr[$val].",";
									}
									echo chop($color_names,",")." &nbsp;"; 
									?>
								</p>
							</td>
							<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
							<td width="70" align="right"><? echo $lst_issue_date; ?></td>
							<td width="80"><p><?  ?>&nbsp;</p></td>
						</tr>
					</table>
			<?
				}
			?>

			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<thead>
					<tr><th colspan="19">Receive Details</th></tr>
					<tr>
						<th width="80">Store Name</th>
						<th width="110">Transaction ID</th>
						<th width="80">Transaction Date</th>
						<th width="90">Bacode No</th>
						<th width="80">Roll No</th>
						<th width="80">Roll Weight</th>
						<th width="100">Construction</th>
						<th width="80">Color Range</th>
						<th width="80">Y-Count</th>
						<th width="80">Yarn Type</th>
						<th width="80">Yarn Composition</th>
						<th width="80">Brand</th>
						<th width="80">Yarn Lot</th>
						<th width="80">MC Dia & Gauge</th>
						<th width="80">F/Dia</th>
						<th width="80">S. Length</th>
						<th width="80">GSM</th>
						<th width="80">M/C NO.</th>
						<th width="80">Knitting Company</th>
					</tr>
				</thead>
			</table>
			<div style="width:1600px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="80"><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("system_number")]; ?></td>
								<td width="80"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								<td width="90"><p><? echo $row[csf("barcode_no")]; ?></p></td>
								<td width="80" align="right"><? echo $row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><p><? echo $color_range[$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $yarn_count_nos;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_types;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_comps;?></p></td>
								<td width="80" align="center"><p><? echo $brand_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]];?></p></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"]." & ".$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]];?></td>
								<td width="80"><? echo $knitting_company_name;?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="80">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="80">Total :</th>
						<th width="80" align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="today_issue_rtn_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$today_date = date("d-M-Y", strtotime($today_date));

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if($storeId) $store_cond = " and d.store_id in ($storeId)"; else $store_cond = "";
	$iss_rtn_qty_sql="select a.recv_number as system_number,a.receive_date, c.barcode_no, c.roll_no, c.qnty, d.store_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(84) and c.entry_form in(84) and d.transaction_type= 4 and d.item_category=13  and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids)  $booking_without_order and b.trans_id = d.id  and d.transaction_date='$today_date' and c.is_sales=$is_sales $store_cond";


	$result=sql_select($iss_rtn_qty_sql);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(84) and c.entry_form in(84)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";

	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	?>
	<fieldset style="width:1580px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [5],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<?
				if(!empty($result))
				{
			?>
					<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
						<thead>
							<tr>
								<th colspan="6">
									<? 
									if($is_sales==0)
									{
										echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
									}else{
										echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
									}
									?>
								</th>
							</tr>
							<tr>
								<th width="100">Body Part</th>
								<th width="110">Construction</th>
								<th width="110">Color </th>
								<th width="80">Quantity</th>
								<th width="70">Last Issued Date</th>
								<th width="80">DOH</th>
							</tr>
						</thead>
						<tr bgcolor="#FFFFFF">
							<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
							<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
							<td width="110">
								<p>
									<? 
									$color_id_arr = explode(",", $color_id);
									foreach ($color_id_arr as $val) {
										$color_names .= $color_arr[$val].",";
									}
									echo chop($color_names,",")." &nbsp;"; 
									?>
								</p>
							</td>
							<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
							<td width="70" align="right"><? echo $lst_issue_date; ?></td>
							<td width="80"><p><?  ?>&nbsp;</p></td>
						</tr>
					</table>
			<?
				}
			?>

			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<thead>
					<tr><th colspan="19">Issue Return Details</th></tr>
					<tr>
						<th width="80">Store Name</th>
						<th width="110">Transaction ID</th>
						<th width="80">Transaction Date</th>
						<th width="90">Bacode No</th>
						<th width="80">Roll No</th>
						<th width="80">Roll Weight</th>
						<th width="100">Construction</th>
						<th width="80">Color Range</th>
						<th width="80">Y-Count</th>
						<th width="80">Yarn Type</th>
						<th width="80">Yarn Composition</th>
						<th width="80">Brand</th>
						<th width="80">Yarn Lot</th>
						<th width="80">MC Dia & Gauge</th>
						<th width="80">F/Dia</th>
						<th width="80">S. Length</th>
						<th width="80">GSM</th>
						<th width="80">M/C NO.</th>
						<th width="80">Knitting Company</th>
					</tr>
				</thead>
			</table>
			<div style="width:1600px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="80"><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("system_number")]; ?></td>
								<td width="80"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								<td width="90"><p><? echo $row[csf("barcode_no")]; ?></p></td>
								<td width="80" align="right"><? echo $row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><p><? echo $color_range[$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $yarn_count_nos;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_types;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_comps;?></p></td>
								<td width="80" align="center"><p><? echo $brand_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]];?></p></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"]." & ".$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]];?></td>
								<td width="80"><? echo $knitting_company_name;?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="80">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="80">Total :</th>
						<th width="80" align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="trans_in_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select a.id,b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by a.id,b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  a.id,b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by a.id,b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
			$buyer_arrs[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		}
	}

	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if ($storeId=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($storeId) ";
	//if ($storeId=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($storeId) ";

	//if($storeId) $store_cond_1 = " and b.to_store in ($storeId)"; else $store_cond_1 = "";

	$query="select a.company_id,a.transfer_system_id, a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 
	union all
	select a.company_id,a.transfer_system_id,a.id,b.from_order_id,b.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 
	union all
	select a.company_id,a.transfer_system_id,a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1";

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");
	$po_no_arr 			= return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$internal_ref_arr 	= return_library_array( "select id, grouping from wo_po_break_down",'id','grouping');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	$company_full_arr 	= return_library_array( "select id, company_name from lib_company", "id", "company_name" );



	?>
	<fieldset style="width:1080px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [10],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<tr>
						<th colspan="6">
							<? 
							if($is_sales==0)
							{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
							}else{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
							}
							?>
						</th>
					</tr>
					<tr>
						<th width="100">Body Part</th>
						<th width="110">Construction</th>
						<th width="110">Color </th>
						<th width="80">Quantity</th>
						<th width="70">Last Issued Date</th>
						<th width="80">DOH</th>
					</tr>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
					<td width="110">
						<p>
							<? 
							$color_id_arr = explode(",", $color_id);
							foreach ($color_id_arr as $val) {
								$color_names .= $color_arr[$val].",";
							}
							echo chop($color_names,",")." &nbsp;"; 
							?>
						</p>
					</td>
					<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
					<td width="70" align="right"><? echo $lst_issue_date; ?></td>
					<td width="80"><p><?  ?>&nbsp;</p></td>
				</tr>
			</table>

			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="60">Transfer Date</th>
						<th width="120">Transfer In Company</th>
						<th width="100">From Order & Ref.</th>
						<th width="100">To Order & Ref.</th>
						<th width="100">Buyer</th>
						<th width="150">Fabric Des.</th>
						<th width="80">GSM</th>
						<th width="100">F. Dia</th>
						<th>Grey Trns.  Qty.</th>
					</tr>
				</thead>
			</table>
			<div style="width:1100px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;$barcodeNOO="";
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("transfer_system_id")]; ?></td>
								<td width="60"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								
								<td width="120"><p><? echo $company_full_arr[$row[csf("company_id")]]; ?></p></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("from_order_id")]].", ".$internal_ref_arr[$row[csf("from_order_id")]]; ?></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("to_order_id")]].", ".$internal_ref_arr[$row[csf("to_order_id")]]; ?></td>
								<td width="100" align="center"><? echo $buyer_arr[$buyer_arrs[$row[csf('to_order_id')]]['buyer_name']];  ?></td>

								<td width="150" align="center"><p><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('dia_width')];  //$yarn_ref_types;?></p></td>
								<td align="right"><? echo $row[csf("qnty")];$tot_qnty+=$row[csf("qnty")];?></td>
							</tr>
							<?
							
							$y++;
							$barcodeNOO.=$row[csf("barcode_no")].",";
						}
					}
					echo $barcodeNOO;
					?>
				</table>
			</div>
			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100"></th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">Trans From Qty :</th>
						<th align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="today_trans_in_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$today_date = date("d-M-Y", strtotime($today_date));

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select a.id,b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by a.id,b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  a.id,b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by a.id,b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
			$buyer_arrs[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		}
	}

	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";

	//if($storeId) $store_cond_1 = " and b.to_store in ($storeId)"; else $store_cond_1 = "";

	$query="select a.company_id,a.transfer_system_id, a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width 
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 and c.re_transfer=0 and a.transfer_date='$today_date' 
	union all
	select a.company_id,a.transfer_system_id,a.id,b.from_order_id,b.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width 
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 and c.re_transfer=0 and a.transfer_date='$today_date' 
	union all
	select a.company_id,a.transfer_system_id,a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width 
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 and c.re_transfer=0 and a.transfer_date='$today_date' ";

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");
	$po_no_arr 			= return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$internal_ref_arr 	= return_library_array( "select id, grouping from wo_po_break_down",'id','grouping');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	$company_full_arr 	= return_library_array( "select id, company_name from lib_company", "id", "company_name" );



	?>
	<fieldset style="width:1080px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [10],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<tr>
						<th colspan="6">
							<? 
							if($is_sales==0)
							{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
							}else{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
							}
							?>
						</th>
					</tr>
					<tr>
						<th width="100">Body Part</th>
						<th width="110">Construction</th>
						<th width="110">Color </th>
						<th width="80">Quantity</th>
						<th width="70">Last Issued Date</th>
						<th width="80">DOH</th>
					</tr>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
					<td width="110">
						<p>
							<? 
							$color_id_arr = explode(",", $color_id);
							foreach ($color_id_arr as $val) {
								$color_names .= $color_arr[$val].",";
							}
							echo chop($color_names,",")." &nbsp;"; 
							?>
						</p>
					</td>
					<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
					<td width="70" align="right"><? echo $lst_issue_date; ?></td>
					<td width="80"><p><?  ?>&nbsp;</p></td>
				</tr>
			</table>
			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="60">Transfer Date</th>
						<th width="120">Transfer In Company</th>
						<th width="100">From Order & Ref.</th>
						<th width="100">To Order & Ref.</th>
						<th width="100">Buyer</th>
						<th width="150">Fabric Des.</th>
						<th width="80">GSM</th>
						<th width="100">F. Dia</th>
						<th>Grey Trns.  Qty.</th>
					</tr>
				</thead>
			</table>
			<div style="width:1100px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("transfer_system_id")]; ?></td>
								<td width="60"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								
								<td width="120"><p><? echo $company_full_arr[$row[csf("company_id")]]; ?></p></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("from_order_id")]].", ".$internal_ref_arr[$row[csf("from_order_id")]]; ?></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("to_order_id")]].", ".$internal_ref_arr[$row[csf("to_order_id")]]; ?></td>
								<td width="100" align="center"><? echo $buyer_arr[$buyer_arrs[$row[csf('to_order_id')]]['buyer_name']];  ?></td>

								<td width="150" align="center"><p><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('dia_width')];  //$yarn_ref_types;?></p></td>
								<td align="right"><? echo $row[csf("qnty")];$tot_qnty+=$row[csf("qnty")];?></td>
							</tr>
							<?
							
							$y++;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100"></th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">Trans From Qty :</th>
						<th align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="trans_to_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select a.id,b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by a.id,b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  a.id,b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by a.id,b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
			$buyer_arrs[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		}
	}

	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if ($storeId=="") $store_cond_1=""; else $store_cond_1=" and b.from_store in ($storeId) ";
	if ($storeId=="") $store_cond_2=""; else $store_cond_2=" and b.to_store in ($storeId) ";
	
	if ($po_ids=="") $from_order_cond=""; else $from_order_cond=" and b.from_order_id in ($po_ids) ";
	if ($po_ids=="") $from_order_cond_2=""; else $from_order_cond_2=" and a.from_order_id in ($po_ids) ";
	if ($po_ids=="") $from_order_cond_3=""; else $from_order_cond_3=" and a.po_breakdown_id in ($po_ids) ";

	//if($storeId) $store_cond_1 = " and b.from_store in ($storeId)"; else $store_cond_1 = "";
	//transOut
	$query="select d.from_order_id,d.to_order_id,d.to_company,d.transfer_system_id,d.transfer_date as receive_date, a.po_breakdown_id, c.barcode_no, c.qnty,b.dia_width from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6  $store_cond_1 and c.booking_without_order =0 and b.mst_id = d.id and d.entry_form=83 $from_order_cond_3 
		union all
		select  b.from_order_id,b.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1 and c.booking_without_order=0 $from_order_cond $store_cond_1
		group by a.transfer_date, c.barcode_no, b.from_order_id,a.transfer_system_id,a.to_company,b.from_order_id,b.to_order_id ,b.dia_width
		union all
		select a.from_order_id,a.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $otst_po_cond $store_cond_1 and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 $from_order_cond_2
		group by a.transfer_date, c.barcode_no, a.from_order_id,a.transfer_system_id,a.to_company,a.from_order_id,a.to_order_id,b.dia_width";
		

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");
	$po_no_arr 			= return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$internal_ref_arr 	= return_library_array( "select id, grouping from wo_po_break_down",'id','grouping');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	$company_full_arr 	= return_library_array( "select id, company_name from lib_company", "id", "company_name" );



	?>
	<fieldset style="width:1080px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [10],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<tr>
						<th colspan="6">
							<? 
							if($is_sales==0)
							{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
							}else{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
							}
							?>
						</th>
					</tr>
					<tr>
						<th width="100">Body Part</th>
						<th width="110">Construction</th>
						<th width="110">Color </th>
						<th width="80">Quantity</th>
						<th width="70">Last Issued Date</th>
						<th width="80">DOH</th>
					</tr>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
					<td width="110">
						<p>
							<? 
							$color_id_arr = explode(",", $color_id);
							foreach ($color_id_arr as $val) {
								$color_names .= $color_arr[$val].",";
							}
							echo chop($color_names,",")." &nbsp;"; 
							?>
						</p>
					</td>
					<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
					<td width="70" align="right"><? echo $lst_issue_date; ?></td>
					<td width="80"><p><?  ?>&nbsp;</p></td>
				</tr>
			</table>
			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="60">Transfer Date</th>
						<th width="120">Transfer To Company</th>
						<th width="100">To Order & Ref.</th>
						<th width="100">From Order & Ref.</th>
						<th width="100">Buyer</th>
						<th width="150">Fabric Des.</th>
						<th width="80">GSM</th>
						<th width="100">F. Dia</th>
						<th>Grey Trns.  Qty.</th>
					</tr>
				</thead>
			</table>
			<div style="width:1100px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;$barcodeNOO="";
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("transfer_system_id")]; ?></td>
								<td width="60"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								
								<td width="120"><p><? echo $company_full_arr[$row[csf("to_company")]]; ?></p></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("to_order_id")]].", ".$internal_ref_arr[$row[csf("to_order_id")]]; ?></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("from_order_id")]].", ".$internal_ref_arr[$row[csf("from_order_id")]]; ?></td>
								<td width="100" align="center"><? echo $buyer_arr[$buyer_arrs[$row[csf('from_order_id')]]['buyer_name']];  ?></td>

								<td width="150" align="center"><p><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('dia_width')];  //$yarn_ref_types;?></p></td>
								<td align="right"><? echo $row[csf("qnty")];$tot_qnty+=$row[csf("qnty")];?></td>
							</tr>
							<?
							
							$y++;
							$barcodeNOO.=$row[csf('barcode_no')].",";
						}
					}
					echo $barcodeNOO;
					?>
				</table>
			</div>
			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100"></th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">Trans From Qty :</th>
						<th align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="today_trans_to_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$today_date = date("d-M-Y", strtotime($today_date));
	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}

			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select a.id,b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by a.id,b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  a.id,b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4) and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by a.id,b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);

		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
			$buyer_arrs[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		}
	}

	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";
	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.from_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";
	
	if ($po_ids=="") $from_order_cond=""; else $from_order_cond=" and b.from_order_id in ($po_ids) ";
	if ($po_ids=="") $from_order_cond_2=""; else $from_order_cond_2=" and a.from_order_id in ($po_ids) ";
	if ($po_ids=="") $from_order_cond_3=""; else $from_order_cond_3=" and a.po_breakdown_id in ($po_ids) ";

	//if($storeId) $store_cond_1 = " and b.from_store in ($storeId)"; else $store_cond_1 = "";

	$query="select d.from_order_id,d.to_order_id,d.to_company,d.transfer_system_id,d.transfer_date as receive_date, a.po_breakdown_id, c.barcode_no, c.qnty,b.dia_width  from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6  $store_cond_1 and c.booking_without_order =0 and b.mst_id = d.id and d.entry_form=83 $from_order_cond_3 and d.transfer_date='$today_date'  
		union all
		select  b.from_order_id,b.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id  and a.transfer_criteria in (1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1 and c.booking_without_order=0 $from_order_cond $store_cond_1 and a.transfer_date='$today_date' 
		group by a.transfer_date, c.barcode_no, b.from_order_id,a.transfer_system_id,a.to_company,b.from_order_id,b.to_order_id,b.dia_width 
		union all
		select a.from_order_id,a.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id $otst_po_cond $store_cond_1 and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 $from_order_cond_2 and a.transfer_date='$today_date' 
		group by a.transfer_date, c.barcode_no, a.from_order_id,a.transfer_system_id,a.to_company,a.from_order_id,a.to_order_id,b.dia_width";
		

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}

	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");
	$po_no_arr 			= return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$internal_ref_arr 	= return_library_array( "select id, grouping from wo_po_break_down",'id','grouping');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	$company_full_arr 	= return_library_array( "select id, company_name from lib_company", "id", "company_name" );



	?>
	<fieldset style="width:1080px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [10],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<tr>
						<th colspan="6">
							<? 
							if($is_sales==0)
							{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
							}else{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
							}
							?>
						</th>
					</tr>
					<tr>
						<th width="100">Body Part</th>
						<th width="110">Construction</th>
						<th width="110">Color </th>
						<th width="80">Quantity</th>
						<th width="70">Last Issued Date</th>
						<th width="80">DOH</th>
					</tr>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
					<td width="110">
						<p>
							<? 
							$color_id_arr = explode(",", $color_id);
							foreach ($color_id_arr as $val) {
								$color_names .= $color_arr[$val].",";
							}
							echo chop($color_names,",")." &nbsp;"; 
							?>
						</p>
					</td>
					<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
					<td width="70" align="right"><? echo $lst_issue_date; ?></td>
					<td width="80"><p><?  ?>&nbsp;</p></td>
				</tr>
			</table>
			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="60">Transfer Date</th>
						<th width="120">Transfer To Company</th>
						<th width="100">To Order & Ref.</th>
						<th width="100">From Order & Ref.</th>
						<th width="100">Buyer</th>
						<th width="150">Fabric Des.</th>
						<th width="80">GSM</th>
						<th width="100">F. Dia</th>
						<th>Grey Trns.  Qty.</th>
					</tr>
				</thead>
			</table>
			<div style="width:1100px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?>&nbsp;</td> 
								<td width="110"><? echo $row[csf("transfer_system_id")]; ?></td>
								<td width="60"><? echo change_date_format($row[csf("receive_date")]); ?></td>
								
								<td width="120"><p><? echo $company_full_arr[$row[csf("to_company")]]; ?></p></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("to_order_id")]].", ".$internal_ref_arr[$row[csf("to_order_id")]]; ?></td>
								<td width="100" align="left"><? echo $po_no_arr[$row[csf("from_order_id")]].", ".$internal_ref_arr[$row[csf("from_order_id")]]; ?></td>
								<td width="100" align="center"><? echo $buyer_arr[$buyer_arrs[$row[csf('from_order_id')]]['buyer_name']];  ?></td>

								<td width="150" align="center"><p><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('dia_width')];  //$yarn_ref_types;?></p></td>
								<td align="right"><? echo $row[csf("qnty")];$tot_qnty+=$row[csf("qnty")];?></td>
							</tr>
							<?
							
							$y++;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100"></th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">Trans From Qty :</th>
						<th align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}

if($action=="issue_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];


	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}
			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no  group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4)  and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0  group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}

	if($is_sales==0) $booking_without_order=" and c.booking_without_order= 0";else $booking_without_order="";
	if($storeId) $store_cond = " and d.store_id in ($storeId)"; else $store_cond = "";
	$query="select a.issue_number, c.po_breakdown_id, c.barcode_no, c.roll_no, c.qnty, a.issue_date, d.store_id from  inv_issue_master a,inv_grey_fabric_issue_dtls b , pro_roll_details c , inv_transaction d where a.id = b.mst_id and B.ID = c.dtls_id and b.trans_id = d.id and a.entry_form =61 and C.ENTRY_FORM =61 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.is_sales=$is_sales $booking_without_order $store_cond";

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$split_barcodeArr=return_library_array("select c.barcode_no, b.barcode_no as mother_barcode from pro_roll_details c, pro_roll_details b where c.entry_form = 62 $ref_barcode_no_cond and c.po_breakdown_id in($po_ids) and c.roll_split_from = b.id and c.status_active =1 and b.status_active=1","barcode_no","mother_barcode");
	


	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}


	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	?>

	
	<fieldset style="width:1580px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [6],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
				<thead>
					<tr>
						<th colspan="6">
							<? 
							if($is_sales==0)
							{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
							}else{
								echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
							}
							?>
						</th>
					</tr>
					<tr>
						<th width="100">Body Part</th>
						<th width="110">Construction</th>
						<th width="110">Color </th>
						<th width="80">Quantity</th>
						<th width="70">Last Issued Date</th>
						<th width="80">DOH</th>
					</tr>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
					<td width="110">
						<p>
							<? 
							$color_id_arr = explode(",", $color_id);
							foreach ($color_id_arr as $val) {
								$color_names .= $color_arr[$val].",";
							}
							echo chop($color_names,",")." &nbsp;"; 
							?>
						</p>
					</td>
					<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
					<td width="70" align="right"><? echo $lst_issue_date; ?></td>
					<td width="80"><p><?  ?>&nbsp;</p></td>
				</tr>
			</table>

			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<thead>
					<tr><th colspan="19">Receive Details</th></tr>
					<tr>
						<th width="30">SL</th>
						<th width="80">Store Name</th>
						<th width="110">Transaction ID</th>
						<th width="80">Transaction Date</th>
						<th width="90">Bacode No</th>
						<th width="80">Roll No</th>
						<th width="80">Roll Weight</th>
						<th width="100">Construction</th>
						<th width="80">Color Range</th>
						<th width="80">Y-Count</th>
						<th width="80">Yarn Type</th>
						<th width="80">Yarn Composition</th>
						<th width="80">Brand</th>
						<th width="80">Yarn Lot</th>
						<th width="80">MC Dia & Gauge</th>
						<th width="80">F/Dia</th>
						<th width="80">S. Length</th>
						<th width="80">GSM</th>
						<th width="80">M/C NO.</th>
						<th width="80">Knitting Company</th>
					</tr>
				</thead>
			</table>
			<div style="width:1600px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;$barcodeNOO="";
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="" || $production_barcode_arr[$split_barcodeArr[$row[csf("barcode_no")]]])
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?>&nbsp;</td>
								<td width="80"><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</td>
								<td width="110"><? echo $row[csf("issue_number")]; ?></td>
								<td width="80"><? echo change_date_format($row[csf("issue_date")]); ?></td>
								<td width="90"><p><? echo $row[csf("barcode_no")]; ?></p></td>
								<td width="80" align="right"><? echo $row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><p><? echo $color_range[$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $yarn_count_nos;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_types;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_comps;?></p></td>
								<td width="80" align="center"><p><? echo $brand_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]];?></p></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"]." & ".$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]];?></td>
								<td width="80"><? echo $knitting_company_name;?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;
								$barcodeNOO.=$row[csf('barcode_no')].",";
						}
					}
						echo $barcodeNOO;
					?>
				</table>
			</div>
			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="80">Total :</th>
						<th width="80" align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="today_issue_popup_summary")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$today_date = date("d-M-Y", strtotime($today_date));

	$ConstPartColorArr = explode("**", $constPartColor);

	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];


	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}
			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no  group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4)  and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0  group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}

	if($is_sales==0) $booking_without_order=" and c.booking_without_order= 0";else $booking_without_order="";
	if($storeId) $store_cond = " and d.store_id in ($storeId)"; else $store_cond = "";
	$query="select a.issue_number, c.po_breakdown_id, c.barcode_no, c.roll_no, c.qnty, a.issue_date, d.store_id from  inv_issue_master a,inv_grey_fabric_issue_dtls b , pro_roll_details c , inv_transaction d where a.id = b.mst_id and B.ID = c.dtls_id and b.trans_id = d.id and a.entry_form =61 and C.ENTRY_FORM =61 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.is_sales=$is_sales $booking_without_order $store_cond and a.issue_date='$today_date'";

	$result=sql_select($query);
	foreach($result as $row)
	{
		$ref_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

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

	$recvDataArrTrans=array();$recvDataArr=array();
	$sqlRecvT="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];

		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
		$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];

		$ref_febric_description_arr[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		$allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
	}
	unset($recvDataT);

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}


	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );
	?>

	
	<fieldset style="width:1580px">
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [5],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				var tbl_list_search = document.getElementById("tbl_list_search");
				if(tbl_list_search){
					setFilterGrid('tbl_list_search',-1,tableFilters);
				}
			});
			function print_window()
			{
				document.getElementById('div_scroll').style.overflow="auto";
				document.getElementById('div_scroll').style.maxHeight="none";
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
				document.getElementById('div_scroll').style.overflowY="scroll";
				document.getElementById('div_scroll').style.maxHeight="380px";
			}
		</script>
		<? ob_start();?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">

				<?
				if (!empty($result)) 
				{	
				?>
					<thead>
						<tr>
							<th colspan="6">
								<? 
								if($is_sales==0)
								{
									echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
								}else{
									echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
								}
								?>
							</th>
						</tr>
						<tr>
							<th width="100">Body Part</th>
							<th width="110">Construction</th>
							<th width="110">Color </th>
							<th width="80">Quantity</th>
							<th width="70">Last Issued Date</th>
							<th width="80">DOH</th>
						</tr>
					</thead>
					<tr bgcolor="#FFFFFF">
						<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
						<td width="110">
							<p>
								<? 
								$color_id_arr = explode(",", $color_id);
								foreach ($color_id_arr as $val) {
									$color_names .= $color_arr[$val].",";
								}
								echo chop($color_names,",")." &nbsp;"; 
								?>
							</p>
						</td>
						<td width="80" align="right"><? echo number_format($quantity,2); ?></td>
						<td width="70" align="right"><? echo $lst_issue_date; ?></td>
						<td width="80"><p><?  ?>&nbsp;</p></td>
					</tr>
				<?
				}
				?>
			</table>

			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<thead>
					<tr><th colspan="19">Receive Details</th></tr>
					<tr>
						<th width="80">Store Name</th>
						<th width="110">Transaction ID</th>
						<th width="80">Transaction Date</th>
						<th width="90">Bacode No</th>
						<th width="80">Roll No</th>
						<th width="80">Roll Weight</th>
						<th width="100">Construction</th>
						<th width="80">Color Range</th>
						<th width="80">Y-Count</th>
						<th width="80">Yarn Type</th>
						<th width="80">Yarn Composition</th>
						<th width="80">Brand</th>
						<th width="80">Yarn Lot</th>
						<th width="80">MC Dia & Gauge</th>
						<th width="80">F/Dia</th>
						<th width="80">S. Length</th>
						<th width="80">GSM</th>
						<th width="80">M/C NO.</th>
						<th width="80">Knitting Company</th>
					</tr>
				</thead>
			</table>
			<div style="width:1600px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$knitting_company = $recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"];
							if($recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"] == 1)
							{
								$knitting_company_name = $company_short_arr[$knitting_company];
							}else{
								$knitting_company_name = $supplier_arr[$knitting_company];
							}
							
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps= implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types= implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));

							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="80"><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</td>
								<td width="110"><? echo $row[csf("issue_number")]; ?></td>
								<td width="80"><? echo change_date_format($row[csf("issue_date")]); ?></td>
								<td width="90"><p><? echo $row[csf("barcode_no")]; ?></p></td>
								<td width="80" align="right"><? echo $row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><p><? echo $color_range[$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]];?></p></td>
								<td width="80" align="center"><p><? echo $yarn_count_nos;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_types;?></p></td>
								<td width="80" align="center"><p><? echo $yarn_ref_comps;?></p></td>
								<td width="80" align="center"><p><? echo $brand_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]];?></p></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"]." & ".$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]];?></td>
								<td width="80"><? echo $knitting_company_name;?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1580" class="rpt_table" rules="all" border="1">
				<tfoot>
					<tr>
						<th width="80">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="80">Total :</th>
						<th width="80" align="right" id="value_grey_qty"><? echo number_format($tot_qnty,2); ?></th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}

if($action=="transOut_popup_summary")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);
	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];
	
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";

	if($storeId) $store_cond = " and b.from_store in ($storeId)"; else $store_cond = "";
	if($is_sales == 1)
	{
		$sql="select d.transfer_date, d.transfer_system_id, d.company_id, a.po_breakdown_id, c.barcode_no, c.qnty, d.from_order_id, d.to_order_id from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=133 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id in ($po_ids) and b.mst_id = d.id and d.entry_form=133 $store_cond";
	}
	else
	{
		$sql="select d.transfer_date, d.transfer_system_id, d.company_id, a.po_breakdown_id, c.barcode_no, c.qnty, d.from_order_id, d.to_order_id from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id in($po_ids) $store_cond and c.booking_without_order = 0 and b.mst_id = d.id and d.entry_form=83
		union all
		select a.transfer_date, a.transfer_system_id, a.to_company as company_id, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, b.from_order_id, b.to_order_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and b.from_order_id in($po_ids) $store_cond and a.transfer_criteria  in (1) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order=0
		group by a.transfer_date, c.barcode_no, b.from_order_id, b.to_order_id
		union all
		select a.transfer_date, a.transfer_system_id, a.company_id a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty, a.from_order_id, a.to_order_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id in($po_ids) $store_cond and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1
		group by a.transfer_date, c.barcode_no, a.from_order_id, a.to_order_id ";
	}

	$result = sql_select($sql);

	foreach ($result as $val) 
	{
		$ref_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

		$all_po_arr[$val[csf("from_order_id")]] = $val[csf("from_order_id")];
		$all_po_arr[$val[csf("to_order_id")]] = $val[csf("to_order_id")];
	}

	$all_po_ids = implode(",", $all_po_arr);

	if($is_sales == 1)
	{
		$po_sql=sql_select("select id, job_no, buyer_id from fabric_sales_order_mst where id in ($all_po_ids) and status_active =1");
		foreach ($po_sql as $val) 
		{
			$po_ref[$val[csf("id")]]["po_number"] = $val[csf("job_no")];
			$po_ref[$val[csf("id")]]["buyer_id"] = $val[csf("buyer_id")];
		}
	}
	else
	{
		$po_sql=sql_select("select a.id,a.po_number, a.grouping, b.buyer_name as buyer_id from wo_po_break_down a, wo_po_details_master b where a.id in ($all_po_ids) b.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 group by a.id, a.po_number, a.grouping, b.buyer_name");

		foreach ($po_sql as $val) 
		{
			$po_ref[$val[csf("id")]]["po_number"] = $val[csf("po_number")];
			$po_ref[$val[csf("id")]]["grouping"] = $val[csf("grouping")];
			$po_ref[$val[csf("id")]]["buyer_id"] = $val[csf("buyer_id")];
		}
	}
	

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

	$sqlRecvT="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf('barcode_no')]] =$row[csf('barcode_no')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
	}

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );

	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_grey_qty"],
				col: [5],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			var tbl_list_search = document.getElementById("tbl_list_search");
			if(tbl_list_search){
				setFilterGrid('tbl_list_search',-1,tableFilters);
			}
		});
		function print_window()
		{
			document.getElementById('div_scroll').style.overflow="auto";
			document.getElementById('div_scroll').style.maxHeight="none";
			$(".flt").css("display","none");
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
			$(".flt").css("display","block");
			document.getElementById('div_scroll').style.overflowY="scroll";
			document.getElementById('div_scroll').style.maxHeight="380px";
		}
	</script>
	<? ob_start();?>
	<div style="width:870px;padding: 10px 0;" align="center">
		<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
			<tr>
				<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
				<td> <div id="report_container"> </div> </td>
			</tr>
		</table>
	</div>
	<div id="scroll_body" align="left">
		<fieldset style="width:1050px">
			<table cellpadding="0" width="1050" class="rpt_table" rules="all" border="1">
				<thead>
					<tr><th colspan="11">Transfer To Details</th></tr>
					<tr>
						<th width="40">Sl</th>
						<th width="110">System Id</th>
						<th width="90">Transfer Date</th>
						<th width="100">To Company</th>
						<th width="120">To Order & Ref</th>
						<th width="120">From Order & Ref</th>
						<th width="100">Buyer</th>
						<th width="150">Fabric Description</th>
						<th width="80">F/Dia</th>
						<th width="80">GSM</th>
						<th width="80">Grey Trans Qnty</th>
					</tr>
				</thead>
			</table>
			<div style="width:1070px; max-height:250px; overflow-y:scroll" id="div_scroll">
				<table cellpadding="0" width="1050" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_qnty=0;
					foreach($result as $row)
					{
						if($production_barcode_arr[$row[csf("barcode_no")]]!="")
						{
							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							$to_order_id_ref = ($po_ref[$row[csf("to_order_id")]]["grouping"] != "") ? " & ".$po_ref[$row[csf("to_order_id")]]["grouping"] : "";
							$from_order_id_ref = ($po_ref[$row[csf("from_order_id")]]["grouping"] != "") ? " & ".$po_ref[$row[csf("from_order_id")]]["grouping"] : "";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?>&nbsp;</td> 
								<td width="110"><p><? echo $row[csf("transfer_system_id")]; ?>&nbsp;</p></td> 
								<td width="90"><p><? echo change_date_format($row[csf("transfer_date")]); ?></p></td>
								<td width="100" align="right"><? echo $company_short_arr[$row[csf("company_id")]]; ?></td>
								<td width="120" align="right"><p><? echo $po_ref[$row[csf("to_order_id")]]["po_number"]. $to_order_id_ref; ?></p></td>
								<td width="120" align="right"><p><? echo $po_ref[$row[csf("from_order_id")]]["po_number"]. $from_order_id_ref; ?></p></td>
								<td width="100" align="right"><p><? echo $buyer_arr[$po_ref[$row[csf("from_order_id")]]["buyer_id"]]; ?></p></td>
								<td width="150" align="right"><p><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]. ", " .$composition_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></p></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
								<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="1050" class="rpt_table" rules="all" border="1">
				<tfoot>
					<th width="40">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="150">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="80">Total:</th>
					<th width="80"><? echo number_format($tot_qnty,2);?></th>
				</tfoot>
			</table>
			<?
			$html=ob_get_contents();
			ob_flush();

			foreach (glob(""."*.xls") as $filename)
			{
				@unlink($filename);
			}
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen(''.$name, 'w');
			$is_created = fwrite($create_new_excel,$html);
			?>
			<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

			<script>
				$(document).ready(function(e) {
					document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
				});
			</script>
			<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="stock_popup_summary_bk")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);
	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];

	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}
			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4)  and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}
	
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";

	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";

	if($storeId) $store_cond_1 = " and b.to_store in ($storeId)"; else $store_cond_1 = "";
	if($storeId) $store_cond_2 = " and e.store_id in ($storeId)"; else $store_cond_2 = "";




	 $sql = "select a.id, a.entry_form, a.receive_date, b.febric_description_id, b.color_id, c.barcode_no, c.po_breakdown_id, c.qnty,e.store_id, 1 as type
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction e
	WHERE a.entry_form in(2,58) and c.entry_form in(2,58) and a.id=b.mst_id and b.id=c.dtls_id and e.id=b.trans_id and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_2 and e.status_active=1 and c.re_transfer=0
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 and c.re_transfer=0
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 and c.re_transfer=0
	union all
	select a.id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) $booking_without_order $store_cond_1 and c.re_transfer=0";
	$result = sql_select($sql);


	$iss_sql = sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c, inv_issue_master d ,inv_grey_fabric_issue_dtls b, inv_transaction e where c.mst_id = d.id and d.id=b.mst_id and b.trans_id=e.id and b.id=c.dtls_id  and d.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.is_returned = 0 $booking_without_order and d.id = e.mst_id and e.transaction_type=2 and e.item_category=13 and e.status_active =1 group by c.po_breakdown_id,c.barcode_no,c.qnty,d.issue_date");


	//$iss_sql = sql_select("select c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.is_returned = 0 $booking_without_order");
	foreach ($iss_sql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	foreach ($result as $val) 
	{
		if($iss_barcode_arr[$val[csf("barcode_no")]] == "")
		{
			$ref_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}
	}

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


	$sqlRecvT="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, b.yarn_prod_id FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' and b.color_id = '$color_id' and b.febric_description_id = '$deter_id' order by a.entry_form desc";
	$recvDataT=sql_select($sqlRecvT);

	foreach($recvDataT as $row)
	{
		$production_barcode_arr[$row[csf('barcode_no')]] =$row[csf('barcode_no')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
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

	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}


	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );

	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_grey_qty"],
				col: [3],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1190px">
		<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
				<tr>
					<th colspan="6">
						<? 
						if($is_sales==0)
						{
							echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
						}else{
							echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
						}
						?>
					</th>
				</tr>
				<tr>
					<th width="100">Body Part</th>
					<th width="110">Construction</th>
					<th width="110">Color </th>
					<th width="80">Quantity</th>
					<th width="70">Last Issued Date</th>
					<th width="80">DOH</th>
				</tr>
			</thead>
			<tr bgcolor="#FFFFFF">
				<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
				<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
				<td width="110">
					<p>
						<? 
						$color_id_arr = explode(",", $color_id);
						foreach ($color_id_arr as $val) {
							$color_names .= $color_arr[$val].",";
						}
						echo chop($color_names,",")." &nbsp;"; 
						?>
					</p>
				</td>
				<td width="80" align="right"><? echo $quantity; ?></td>
				<td width="70" align="right"><? echo $lst_issue_date; ?></td>
				<td width="80"><p><?  ?>&nbsp;</p></td>
			</tr>
		</table>

		<table cellpadding="0" width="1310" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="16">Stock Details</th></tr>
				<tr>
					<th width="80">Store Name</th>
					<th width="90">Bacode No</th>
					<th width="80">Roll No</th>
					<th width="80">Roll Weight</th>
					<th width="100">Construction</th>
					<th width="80">Color Range</th>
					<th width="80">Y-Count</th>
					<th width="80">Yarn Type</th>
					<th width="80">Yarn Composition</th>
					<th width="80">Brand</th>
					<th width="80">Yarn Lot</th>
					<th width="80">MC Dia & Gauge</th>
					<th width="80">F/Dia</th>
					<th width="80">S. Length</th>
					<th width="80">GSM</th>
					<th width="80">M/C NO.</th>
				</tr>
			</thead>
		</table>
		<div style="width:1330px; max-height:250px; overflow-y:scroll" id="div_scroll">
			<table cellpadding="0" width="1310" class="rpt_table" rules="all" border="1" id="tbl_list_search">
				<?
				$i=0; $tot_qnty=0;
				foreach($result as $row)
				{
					if($production_barcode_arr[$row[csf("barcode_no")]]!="")
					{
						$yarn_cour_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]);
						$yarn_count_nos = "";
						foreach ($yarn_cour_arr as $val) 
						{
							$yarn_count_nos .= $count_arr[$val].",";
						}
						$yarn_count_nos = chop($yarn_count_nos,",");

						$yarn_prod_arr = explode(",", $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]);
						$yarn_ref_comps="";
						foreach ($yarn_prod_arr as $val) 
						{
							$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
						}
						$yarn_ref_comps=implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

						$yarn_ref_types="";
						foreach ($yarn_prod_arr as $val) 
						{
							$yarn_ref_types .= $yarn_ref[$val]["type"].",";
						}
						$yarn_ref_types=implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));



						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="80"><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</td> 
							<td width="90"><p><? echo $row[csf("barcode_no")]; ?></p></td>
							<td width="80" align="right"><? echo $row[csf("roll_no")]; ?></td>
							<td width="80" align="right"><? echo $row[csf("qnty")]; ?></td>
							<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]]; ?></td>
							<td width="80" align="center"><? echo $color_range[$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]];?></td>
							<td width="80" align="center"><? echo $yarn_count_nos;?></td>
							<td width="80" align="center"><? echo $yarn_ref_types;?></td>
							<td width="80" align="center"><? echo $yarn_ref_comps;?></td>
							<td width="80" align="center"><? echo $brand_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]];?></td>
							<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"];?></td>
							<td width="80"><? echo $recvDataArrTrans[$row[csf("barcode_no")]]["machine_dia"]." & ".$recvDataArrTrans[$row[csf("barcode_no")]]["machine_gg"];?></td>
							<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["width"];?></td>
							<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"];?></td>
							<td width="80" align="center"><? echo $recvDataArrTrans[$row[csf('barcode_no')]]["gsm"];?></td>
							<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]];?></td>
						</tr>
						<?
						$tot_qnty+=$row[csf('qnty')];
						$y++;
					}
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="1310" class="rpt_table" rules="all" border="1">
			<tfoot>
				<th width="80">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="80">Roll Total :</th>
				<th width="80" id="value_grey_qty" align="right"><? echo number_format($tot_qnty,2); ?></th>
				<th width="100">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
	exit();
}

if($action=="stock_popup_summary")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);
	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];
	$rpt_btn = $ConstPartColorArr[3];
	//echo $rpt_btn;
	if($color_id=="" && $db_type==2)
	{ 
		$color_cond= "and b.color_id is NULL";
	}
	else{$color_cond= "and b.color_id = '$color_id'";}
	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}
			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4)  and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}
	
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";

	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";

	if($storeId) $store_cond_1 = " and b.to_store in ($storeId)"; else $store_cond_1 = "";
	if($storeId) $store_cond_2 = " and e.store_id in ($storeId)"; else $store_cond_2 = "";

	//recv
	
	$recvQql= sql_select("select a.recv_number as system_number,a.receive_date, c.barcode_no, c.roll_no, c.qnty, d.store_id,c.re_transfer   
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction d
	 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0 and b.trans_id = d.id and c.is_sales=$is_sales   and c.barcode_no in(18020986532,18020942066)");

	foreach ($recvQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	 //transf form
	// echo "xxxxx select a.company_id,a.transfer_system_id, a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer  
	// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales  
	// union all 
	// select a.company_id,a.transfer_system_id,a.id,b.from_order_id,b.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer  
	//  from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales 
	//   union all 
	//   select a.company_id,a.transfer_system_id,a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer   
	// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	// WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales ";
	$tranFormQql= sql_select("select a.company_id,a.transfer_system_id, a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer  
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales  
	union all 
	select a.company_id,a.transfer_system_id,a.id,b.from_order_id,b.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer  
	 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales 
	  union all 
	  select a.company_id,a.transfer_system_id,a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer   
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales "); 
	foreach ($tranFormQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	//issue 
	$issueQql=sql_select("select a.issue_number, c.po_breakdown_id, c.barcode_no, c.roll_no, c.qnty, a.issue_date, d.store_id,c.is_returned  
	from inv_issue_master a,inv_grey_fabric_issue_dtls b , pro_roll_details c , inv_transaction d
	 where a.id = b.mst_id and B.ID = c.dtls_id and b.trans_id = d.id and a.entry_form =61 and C.ENTRY_FORM =61 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 
	 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.is_sales=$is_sales  and d.transaction_type=2 and d.item_category=13
	and c.booking_without_order= 0 ");

	

	foreach ($issueQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	 //transfer too
	$trasToQql=sql_select("select d.from_order_id,d.to_order_id,d.to_company,d.transfer_system_id,d.transfer_date as receive_date, a.po_breakdown_id, c.barcode_no, c.qnty,b.dia_width
	 from order_wise_pro_details a, inv_item_transfer_dtls b, pro_roll_details c, inv_item_transfer_mst d
	 where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and c.booking_without_order =0 and b.mst_id = d.id 
	 and d.entry_form=83 and a.po_breakdown_id in ($po_ids)  and c.is_sales=$is_sales  
	 union all select b.from_order_id,b.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width
	  from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	  where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1 
	  and c.booking_without_order=0 and b.from_order_id in ($po_ids) and c.is_sales=$is_sales  
	  group by a.transfer_date, c.barcode_no, b.from_order_id,a.transfer_system_id,a.to_company,b.from_order_id,b.to_order_id ,b.dia_width 
	  union all 
	  select a.from_order_id,a.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width 
	  from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	  where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 and a.from_order_id in ($po_ids) and c.is_sales=$is_sales 
	 group by a.transfer_date, c.barcode_no, a.from_order_id,a.transfer_system_id,a.to_company,a.from_order_id,a.to_order_id,b.dia_width"); 

	foreach ($trasToQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}
	 //issue_rtn 
	$issueRtnQql= sql_select("select c.barcode_no,c.qnty,c.re_transfer,a.store_id from inv_receive_master a ,pro_roll_details c where a.id=c.mst_id and a.entry_form=84 and c.entry_form=84 and c.po_breakdown_id in($po_ids) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_without_order=0 and c.is_sales=$is_sales");
	foreach ($issueRtnQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}


	$splitQry=	sql_select("select c.barcode_no, b.barcode_no as mother_barcode from pro_roll_details c, pro_roll_details b where c.entry_form = 62 $ref_barcode_no_cond and c.po_breakdown_id in($po_ids) and c.roll_split_from = b.id and c.status_active =1 and b.status_active=1");

 	foreach ($splitQry as $val)
	{
		$iss_barcode_arr[$val[csf("mother_barcode")]] = $val[csf("mother_barcode")];
		$mother_barcode_arr[$val[csf("barcode_no")]] = $val[csf("mother_barcode")];
	}

	$ref_barcode_nos = implode(",", $iss_barcode_arr);
	$barCond = $ref_barcode_no_cond = "";
	if($db_type==2 && count($iss_barcode_arr)>999)
	{
		$ref_barcode_arr_chunk=array_chunk($iss_barcode_arr,999) ;
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
	if($rpt_btn!=12){$color_conds=$color_cond;}
	$productionQry= sql_select("select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no,c.roll_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' $color_conds and b.febric_description_id = '$deter_id' and c.is_sales=$is_sales order by a.entry_form desc");

	// echo "AAAAAA select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no,c.roll_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' $color_conds and b.febric_description_id = '$deter_id' and c.is_sales=$is_sales order by a.entry_form desc";

	foreach ($productionQry as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["roll_no"]=$row[csf('roll_no')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_dia"]=$row[csf('machine_dia')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_gg"]=$row[csf('machine_gg')];
		if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
		{
			$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
		}
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$allYarnProdArr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];


	}
	$recvQql_tot=0;
	foreach ($recvQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			echo "string";
			$stock_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$recv_transIn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$recvQql_tot+=$val[csf("qnty")];
			if($val[csf("re_transfer")]==0)
			{
				$recvStoreArr[$val[csf("barcode_no")]]=$val[csf("store_id")];
			}
		}
	}
	/*echo "<pre>";
	print_r($recv_transIn_barcode_arr);
	echo "</pre>";*/
	$tranFormQql_tot=0;
	foreach ($tranFormQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$recv_transIn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			//echo $val[csf("barcode_no")].",";
			$tranFormQql_tot+=$val[csf("qnty")];
			if($val[csf("re_transfer")]==0)
			{
				$recvStoreArr[$val[csf("barcode_no")]]=$val[csf("store_id")];
			}
		}

	}
	$issueQql_tot=0;
	foreach ($issueQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]] || $production_barcode_arr[$mother_barcode_arr[$val[csf("barcode_no")]]] )
		//if( $production_barcode_arr[$mother_barcode_arr[20020156083]] )
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$issue_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$issueQql_tot+=$val[csf("qnty")];

			if($production_barcode_arr[$mother_barcode_arr[$val[csf("barcode_no")]]])
			{
				
				$stock_barcode_arr[$mother_barcode_arr[$val[csf("barcode_no")]]] -= $val[csf("qnty")];
			}
			if($val[csf("is_returned")]==0)
			{
				$issuedBarcodeArr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
			}
			$stock_barcode_arrQnty[$val[csf("barcode_no")]]= $val[csf("qnty")];
			$stockIssQnt.=$val[csf("barcode_no")]."=". $val[csf("qnty")].",";
		}
	}
	//echo $stockIssQnt;die;
	/*echo "<pre>";
	print_r($stock_barcode_arrQnty);
	echo "</pre>";
	die;*/

	$trasToQql_tot=0;
	foreach ($trasToQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$trnsTo_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$trasToQql_tot+=$val[csf("qnty")];
		}
	}
	$issueRtnQql_tot=0;
	foreach ($issueRtnQql as $val)
	{

		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$issueRtn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$issueRtnQql_tot+=$val[csf("qnty")];
			$recv_transIn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];

			if($val[csf("re_transfer")]==0)
			{
				$recvStoreArr[$val[csf("barcode_no")]]=$val[csf("store_id")];
			}

		}
	}
	
 	/*echo "recv = ". $recvQql_tot. "<br/>tranForm = ". $tranFormQql_tot ."<br/>issueQql = ".  $issueQql_tot ."<br/>trasToQql = ". $trasToQql_tot ."<br/>issueRtnQql = ". $issueRtnQql_tot;
	echo "<pre>";
	print_r($stock_barcode_arr);
	echo "</pre>";*/
	
	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}


	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	

	$supplier_sql=  sql_select("select id, short_name,supplier_name from lib_supplier");
	foreach ($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]] = $row[csf("short_name")];
		$supplierFull_arr[$row[csf("id")]] = $row[csf("supplier_name")];
	}

	$company_sql=  sql_select("select id, company_short_name,company_name from lib_company");
	foreach ($company_sql as $row)
	{
		$company_short_arr[$row[csf("id")]] = $row[csf("company_short_name")];
		$company_full_arr[$row[csf("id")]] = $row[csf("company_name")];
	}


	//$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	//$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );

	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_grey_qty"],
				col: [3],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1290px">
		<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
				<tr>
					<th colspan="6">
						<? 
						if($is_sales==0)
						{
							echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
						}else{
							echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
						}
						?>
					</th>
				</tr>
				<tr>
					<th width="100">Body Part</th>
					<th width="110">Construction</th>
					<th width="110">Color </th>
					<th width="80">Quantity</th>
					<th width="70">Last Issued Date</th>
					<th width="80">DOH</th>
				</tr>
			</thead>
			<tr bgcolor="#FFFFFF">
				<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
				<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
				<td width="110">
					<p>
						<? 
						$color_id_arr = explode(",", $color_id);
						foreach ($color_id_arr as $val) {
							$color_names .= $color_arr[$val].",";
						}
						echo chop($color_names,",")." &nbsp;"; 
						?>
					</p>
				</td>
				<td width="80" align="right"><? echo $quantity; ?></td>
				<td width="70" align="right"><? echo $lst_issue_date; ?></td>
				<td width="80"><p><?  ?>&nbsp;</p></td>
			</tr>
		</table>

		<table cellpadding="0" width="1410" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="17">Stock Details</th></tr>
				<tr>
					<th width="80">Store Name</th>
					<th width="90">Bacode No</th>
					<th width="80">Roll No</th>
					<th width="80">Roll Weight</th>
					<th width="100">Construction</th>
					<th width="80">Color Range</th>
					<th width="80">Y-Count</th>
					<th width="80">Yarn Type</th>
					<th width="80">Yarn Composition</th>
					<th width="80">Brand</th>
					<th width="80">Yarn Lot</th>
					<th width="80">MC Dia & Gauge</th>
					<th width="80">F/Dia</th>
					<th width="80">S. Length</th>
					<th width="80">GSM</th>
					<th width="80">M/C NO.</th>
					<th width="100">Knitting Company</th>
				</tr>
			</thead>
		</table>
		<div style="width:1430px; max-height:250px; overflow-y:scroll" id="div_scroll">
			<table cellpadding="0" width="1410" class="rpt_table" rules="all" border="1" id="tbl_list_search">
				<?
				$i=0; $tot_qnty=0;

				foreach ($recv_transIn_barcode_arr as $barcode => $val)
				{
					if($issuedBarcodeArr[$barcode]=="")
					{

						if ($stock_barcode_arr[$barcode]>0) 
						{
							$stockQnty=$stock_barcode_arr[$barcode];
							$sotreID=$recvStoreArr[$barcode];
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$barcode]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$barcode]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps=implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types=implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));



							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="80"><? echo $store_arr[$sotreID]; ?>&nbsp;</td> 
								<td width="90"><p><? echo $barcode; ?></p></td>
								<td width="80" align="right"><? echo $recvDataArrTrans[$barcode]["roll_no"]; //$row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $stockQnty; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$barcode]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><? echo $color_range[$recvDataArrTrans[$barcode]["color_range_id"]];?></td>
								<td width="80" align="center"><? echo $yarn_count_nos;?></td>
								<td width="80" align="center"><? echo $yarn_ref_types;?></td>
								<td width="80" align="center"><? echo $yarn_ref_comps;?></td>
								<td width="80" align="center"><? echo $brand_arr[$recvDataArrTrans[$barcode]["brand_id"]];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["machine_dia"]." & ".$recvDataArrTrans[$barcode]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$barcode]["machine_no_id"]];?></td>
								<td width="100" align="center"><? if($recvDataArrTrans[$barcode]["knitting_source"]==1){echo $company_full_arr[$recvDataArrTrans[$barcode]["knitting_company"]];}else{echo $supplierFull_arr[$recvDataArrTrans[$barcode]["knitting_company"]];} ?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;
						}
					}
						
				}			
				?>
			</table>
		</div>
		<table cellpadding="0" width="1410" class="rpt_table" rules="all" border="1">
			<tfoot>
				<th width="80">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="80">Roll Total :</th>
				<th width="80" id="value_grey_qty" align="right"><? echo number_format($tot_qnty,2); ?></th>
				<th width="100">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
	exit();
}

if($action=="stock_popup_summary_2")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$ConstPartColorArr = explode("**", $constPartColor);
	$deter_id = $ConstPartColorArr[0];
	$body_part_id = $ConstPartColorArr[1];
	$color_id = $ConstPartColorArr[2];
	$rpt_btn = $ConstPartColorArr[3];
	if($color_id=="" && $db_type==2)
	{ 
		$color_cond= "and b.color_id is NULL";
	}
	else{$color_cond= "and b.color_id = '$color_id'";}
	if($is_sales == 1)
	{
		$sql = "select a.id, a.job_no, a.sales_booking_no, a.booking_date, a.company_id, a.buyer_id from fabric_sales_order_mst a where a.id in ($po_ids) and a.status_active =1 ";
		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($job_ref[$row[csf('sales_booking_no')]] == "")
			{
				$job_ref[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				$booking_ref .= $row[csf('sales_booking_no')] . ",";
			}
			$buyer_id = $row[csf('buyer_id')];
		}
	}
	else
	{
		$sql="select b.buyer_name, a.grouping, c.booking_no, c.booking_type, c.is_short from wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where a.id in ($po_ids)  and c.booking_type=1 and a.id=c.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst = b.job_no group by b.buyer_name,a.id, a.grouping,  c.booking_no, c.booking_type, c.is_short 	union all select  b.buyer_name,  a.grouping,  c.booking_no, c.booking_type, c.is_short from  wo_po_break_down a, wo_booking_dtls c, wo_po_details_master b where  a.id in ($po_ids)  and c.booking_type in (4)  and a.id=c.po_break_down_id and a.job_no_mst = b.job_no and a.status_active=1 and a.is_deleted=0 group by b.buyer_name, a.grouping,  c.booking_no, c.booking_type, c.is_short";

		$booking_sql = sql_select($sql);
		foreach ($booking_sql as $row) 
		{
			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}
			if($job_ref[$row[csf('booking_no')]] == "")
			{
				$job_ref[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$booking_ref .= $row[csf('booking_no')] . " " .$booking_type. ",";
			}

			$buyer_id = $row[csf('buyer_name')];
			$grouping[$row[csf('grouping')]] = $row[csf('grouping')];
		}
	}
	
	if($is_sales ==0) $booking_without_order = " and c.booking_without_order = 0";else $booking_without_order ="";

	if ($cbo_store_name=="") $store_cond_1=""; else $store_cond_1=" and b.to_store in ($cbo_store_name) ";
	if ($cbo_store_name=="") $store_cond_2=""; else $store_cond_2=" and e.store_id in ($cbo_store_name) ";

	if($storeId) $store_cond_1 = " and b.to_store in ($storeId)"; else $store_cond_1 = "";
	if($storeId) $store_cond_2 = " and e.store_id in ($storeId)"; else $store_cond_2 = "";

	//recv
	$recvQql= sql_select("select a.recv_number as system_number,a.receive_date, c.barcode_no, c.roll_no, c.qnty, d.store_id,c.re_transfer   
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction d
	 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0 and b.trans_id = d.id and c.is_sales=$is_sales ");

	foreach ($recvQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	 //transf form
	$tranFormQql= sql_select("select a.company_id,a.transfer_system_id, a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer  
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales  
	union all 
	select a.company_id,a.transfer_system_id,a.id,b.from_order_id,b.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer  
	 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and a.transfer_criteria in (1,2,4) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales 
	  union all 
	  select a.company_id,a.transfer_system_id,a.id,a.from_order_id,a.to_order_id, a.entry_form, a.transfer_date as receive_date, null as febric_description_id, null as color_id, c.barcode_no, c.po_breakdown_id, c.qnty, b.to_store as store_id, 2 as type,b.dia_width,c.re_transfer   
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.booking_without_order = 0 and c.is_sales=$is_sales "); 
	foreach ($tranFormQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	//issue 
	$issueQql=sql_select("select a.issue_number, c.po_breakdown_id, c.barcode_no, c.roll_no, c.qnty, a.issue_date, d.store_id,c.is_returned  
	from inv_issue_master a,inv_grey_fabric_issue_dtls b , pro_roll_details c , inv_transaction d
	 where a.id = b.mst_id and B.ID = c.dtls_id and b.trans_id = d.id and a.entry_form =61 and C.ENTRY_FORM =61 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 
	 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.is_sales=$is_sales  and d.transaction_type=2 and d.item_category=13
	and c.booking_without_order= 0 ");

	

	foreach ($issueQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	 //transfer too
	$trasToQql=sql_select("select d.from_order_id,d.to_order_id,d.to_company,d.transfer_system_id,d.transfer_date as receive_date, a.po_breakdown_id, c.barcode_no, c.qnty,b.dia_width
	 from order_wise_pro_details a, inv_item_transfer_dtls b, pro_roll_details c, inv_item_transfer_mst d
	 where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 and c.booking_without_order =0 and b.mst_id = d.id 
	 and d.entry_form=83 and a.po_breakdown_id in ($po_ids)  and c.is_sales=$is_sales  
	 union all select b.from_order_id,b.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, b.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width
	  from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	  where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.transfer_criteria in(1,2,4) and a.entry_form =82 and c.entry_form =82 and b.status_active =1 and c.status_active =1 and a.status_active =1 
	  and c.booking_without_order=0 and b.from_order_id in ($po_ids) and c.is_sales=$is_sales  
	  group by a.transfer_date, c.barcode_no, b.from_order_id,a.transfer_system_id,a.to_company,b.from_order_id,b.to_order_id ,b.dia_width 
	  union all 
	  select a.from_order_id,a.to_order_id,a.to_company,a.transfer_system_id,a.transfer_date as receive_date, a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty,b.dia_width 
	  from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	  where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.entry_form =110 and c.entry_form =110 and b.status_active =1 and c.status_active =1 and a.status_active =1 and a.from_order_id in ($po_ids) and c.is_sales=$is_sales 
	 group by a.transfer_date, c.barcode_no, a.from_order_id,a.transfer_system_id,a.to_company,a.from_order_id,a.to_order_id,b.dia_width"); 

	foreach ($trasToQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}
	 //issue_rtn 
	$issueRtnQql= sql_select("select c.barcode_no,c.qnty,c.re_transfer,a.store_id from inv_receive_master a ,pro_roll_details c where a.id=c.mst_id and a.entry_form=84 and c.entry_form=84 and c.po_breakdown_id in($po_ids) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_without_order=0 and c.is_sales=$is_sales");
	foreach ($issueRtnQql as $val)
	{
		$iss_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}


	$splitQry=	sql_select("select c.barcode_no, b.barcode_no as mother_barcode from pro_roll_details c, pro_roll_details b where c.entry_form = 62 $ref_barcode_no_cond and c.po_breakdown_id in($po_ids) and c.roll_split_from = b.id and c.status_active =1 and b.status_active=1");

 	foreach ($splitQry as $val)
	{
		$iss_barcode_arr[$val[csf("mother_barcode")]] = $val[csf("mother_barcode")];
		$mother_barcode_arr[$val[csf("barcode_no")]] = $val[csf("mother_barcode")];
	}

	$ref_barcode_nos = implode(",", $iss_barcode_arr);
	$barCond = $ref_barcode_no_cond = "";
	if($db_type==2 && count($iss_barcode_arr)>999)
	{
		$ref_barcode_arr_chunk=array_chunk($iss_barcode_arr,999) ;
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
	if($rpt_btn!=12){$color_conds=$color_cond;}
	$productionQry= sql_select("select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no,c.roll_no, b.yarn_prod_id, b.machine_dia, b.machine_gg FROM inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $ref_barcode_no_cond and b.body_part_id = '$body_part_id' $color_conds and b.febric_description_id = '$deter_id' and c.is_sales=$is_sales order by a.entry_form desc");

	foreach ($productionQry as $row)
	{
		$production_barcode_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

		$recvDataArrTrans[$row[csf('barcode_no')]]["febric_description_id"]=$row[csf('febric_description_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["body_part_id"]=$row[csf('body_part_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_id"]=$row[csf('color_id')];

		$recvDataArrTrans[$row[csf('barcode_no')]]["receive_basis"]=$row[csf('receive_basis')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_source"]=$row[csf('knitting_source')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["knitting_company"]=$row[csf('knitting_company')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["gsm"]=$row[csf('gsm')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["width"]=$row[csf('width')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["color_range_id"]=$row[csf('color_range_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_lot"]=$row[csf('yarn_lot')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_count"]=$row[csf('yarn_count')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["stitch_length"]=$row[csf('stitch_length')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_no_id"]=$row[csf('machine_no_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["yarn_prod_id"]=$row[csf('yarn_prod_id')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["roll_no"]=$row[csf('roll_no')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_dia"]=$row[csf('machine_dia')];
		$recvDataArrTrans[$row[csf('barcode_no')]]["machine_gg"]=$row[csf('machine_gg')];
		if($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2)
		{
			$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
		}
		$all_color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$allYarnProdArr[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];


	}
	$recvQql_tot=0;
	foreach ($recvQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$recv_transIn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$recvQql_tot+=$val[csf("qnty")];
			if($val[csf("re_transfer")]==0)
			{
				$recvStoreArr[$val[csf("barcode_no")]]=$val[csf("store_id")];
			}
		}
	}
	$tranFormQql_tot=0;
	foreach ($tranFormQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$recv_transIn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			echo $val[csf("barcode_no")].",";
			$tranFormQql_tot+=$val[csf("qnty")];
			if($val[csf("re_transfer")]==0)
			{
				$recvStoreArr[$val[csf("barcode_no")]]=$val[csf("store_id")];
			}
		}

	}
	$issueQql_tot=0;
	foreach ($issueQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]] || $production_barcode_arr[$mother_barcode_arr[$val[csf("barcode_no")]]] )
		//if( $production_barcode_arr[$mother_barcode_arr[20020156083]] )
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$issue_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$issueQql_tot+=$val[csf("qnty")];

			if($production_barcode_arr[$mother_barcode_arr[$val[csf("barcode_no")]]])
			{
				
				$stock_barcode_arr[$mother_barcode_arr[$val[csf("barcode_no")]]] -= $val[csf("qnty")];
			}
			if($val[csf("is_returned")]==0)
			{
				$issuedBarcodeArr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
			}
			$stock_barcode_arrQnty[$val[csf("barcode_no")]]= $val[csf("qnty")];
			$stockIssQnt.=$val[csf("barcode_no")]."=". $val[csf("qnty")].",";
		}
	}
	//echo $stockIssQnt;die;
	/*echo "<pre>";
	print_r($stock_barcode_arrQnty);
	echo "</pre>";
	die;*/

	$trasToQql_tot=0;
	foreach ($trasToQql as $val)
	{
		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$trnsTo_barcode_arr[$val[csf("barcode_no")]] -= $val[csf("qnty")];
			$trasToQql_tot+=$val[csf("qnty")];
		}
	}
	$issueRtnQql_tot=0;
	foreach ($issueRtnQql as $val)
	{

		if($production_barcode_arr[$val[csf("barcode_no")]])
		{
			$stock_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$issueRtn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];
			$issueRtnQql_tot+=$val[csf("qnty")];
			$recv_transIn_barcode_arr[$val[csf("barcode_no")]] += $val[csf("qnty")];

			if($val[csf("re_transfer")]==0)
			{
				$recvStoreArr[$val[csf("barcode_no")]]=$val[csf("store_id")];
			}

		}
	}
	
 	echo "recv = ". $recvQql_tot. "<br/>tranForm = ". $tranFormQql_tot ."<br/>issueQql = ".  $issueQql_tot ."<br/>trasToQql = ". $trasToQql_tot ."<br/>issueRtnQql = ". $issueRtnQql_tot;
	echo "<pre>";
	print_r($stock_barcode_arr);
	echo "</pre>";
	
	$ref_febric_description_arr[$deter_id] = $deter_id;
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

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ref_febric_description_cond";
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

	$yarn_prod_id_arr = array_filter($allYarnProdArr);
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

		$yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
		foreach ($yarn_sql as $row)
		{
			$yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
			$yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
			$yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
		}
	}


	$all_color_ids = implode(",", $all_color_arr);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and id in ($all_color_ids)", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id =$buyer_id", "id", "short_name"  );

	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$companyID ","id","store_name");

	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$machine_arr		= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	

	$supplier_sql=  sql_select("select id, short_name,supplier_name from lib_supplier");
	foreach ($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]] = $row[csf("short_name")];
		$supplierFull_arr[$row[csf("id")]] = $row[csf("supplier_name")];
	}

	$company_sql=  sql_select("select id, company_short_name,company_name from lib_company");
	foreach ($company_sql as $row)
	{
		$company_short_arr[$row[csf("id")]] = $row[csf("company_short_name")];
		$company_full_arr[$row[csf("id")]] = $row[csf("company_name")];
	}


	//$supplier_arr		=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	//$company_short_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name" );

	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_grey_qty"],
				col: [3],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1290px">
		<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">
			<thead>
				<tr>
					<th colspan="6">
						<? 
						if($is_sales==0)
						{
							echo "Buyer : ".$buyer_arr[$buyer_id].", Job No: $job_no, Reference : ".implode(",", $grouping).", Booking No: ". chop($booking_ref,",");
						}else{
							echo "Buyer : ".$buyer_arr[$buyer_id].", Fso No: $job_no, Booking No: ". chop($booking_ref,",");
						}
						?>
					</th>
				</tr>
				<tr>
					<th width="100">Body Part</th>
					<th width="110">Construction</th>
					<th width="110">Color </th>
					<th width="80">Quantity</th>
					<th width="70">Last Issued Date</th>
					<th width="80">DOH</th>
				</tr>
			</thead>
			<tr bgcolor="#FFFFFF">
				<td width="100"><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
				<td width="110"><p><? echo $constuction_arr[$deter_id]; ?>&nbsp;</p></td>
				<td width="110">
					<p>
						<? 
						$color_id_arr = explode(",", $color_id);
						foreach ($color_id_arr as $val) {
							$color_names .= $color_arr[$val].",";
						}
						echo chop($color_names,",")." &nbsp;"; 
						?>
					</p>
				</td>
				<td width="80" align="right"><? echo $quantity; ?></td>
				<td width="70" align="right"><? echo $lst_issue_date; ?></td>
				<td width="80"><p><?  ?>&nbsp;</p></td>
			</tr>
		</table>

		<table cellpadding="0" width="1410" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="17">Stock Details</th></tr>
				<tr>
					<th width="80">Store Name</th>
					<th width="90">Bacode No</th>
					<th width="80">Roll No</th>
					<th width="80">Roll Weight</th>
					<th width="100">Construction</th>
					<th width="80">Color Range</th>
					<th width="80">Y-Count</th>
					<th width="80">Yarn Type</th>
					<th width="80">Yarn Composition</th>
					<th width="80">Brand</th>
					<th width="80">Yarn Lot</th>
					<th width="80">MC Dia & Gauge</th>
					<th width="80">F/Dia</th>
					<th width="80">S. Length</th>
					<th width="80">GSM</th>
					<th width="80">M/C NO.</th>
					<th width="100">Knitting Company</th>
				</tr>
			</thead>
		</table>
		<div style="width:1430px; max-height:250px; overflow-y:scroll" id="div_scroll">
			<table cellpadding="0" width="1410" class="rpt_table" rules="all" border="1" id="tbl_list_search">
				<?
				$i=0; $tot_qnty=0;

				foreach ($recv_transIn_barcode_arr as $barcode => $val)
				{
					if($issuedBarcodeArr[$barcode]=="")
					{

						if ($stock_barcode_arr[$barcode]>0) 
						{
							$stockQnty=$stock_barcode_arr[$barcode];
							$sotreID=$recvStoreArr[$barcode];
							$yarn_cour_arr = explode(",", $recvDataArrTrans[$barcode]["yarn_count"]);
							$yarn_count_nos = "";
							foreach ($yarn_cour_arr as $val) 
							{
								$yarn_count_nos .= $count_arr[$val].",";
							}
							$yarn_count_nos = chop($yarn_count_nos,",");

							$yarn_prod_arr = explode(",", $recvDataArrTrans[$barcode]["yarn_prod_id"]);
							$yarn_ref_comps="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_comps .= $yarn_ref[$val]["comp"].",";
							}
							$yarn_ref_comps=implode(",",array_unique(explode(",",chop($yarn_ref_comps,","))));

							$yarn_ref_types="";
							foreach ($yarn_prod_arr as $val) 
							{
								$yarn_ref_types .= $yarn_ref[$val]["type"].",";
							}
							$yarn_ref_types=implode(",",array_unique(explode(",",chop($yarn_ref_types,","))));



							$i++;
							if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="80"><? echo $store_arr[$sotreID]; ?>&nbsp;</td> 
								<td width="90"><p><? echo $barcode; ?></p></td>
								<td width="80" align="right"><? echo $recvDataArrTrans[$barcode]["roll_no"]; //$row[csf("roll_no")]; ?></td>
								<td width="80" align="right"><? echo $stockQnty; ?></td>
								<td width="100" align="center"><? echo $constuction_arr[$recvDataArrTrans[$barcode]["febric_description_id"]]; ?></td>
								<td width="80" align="center"><? echo $color_range[$recvDataArrTrans[$barcode]["color_range_id"]];?></td>
								<td width="80" align="center"><? echo $yarn_count_nos;?></td>
								<td width="80" align="center"><? echo $yarn_ref_types;?></td>
								<td width="80" align="center"><? echo $yarn_ref_comps;?></td>
								<td width="80" align="center"><? echo $brand_arr[$recvDataArrTrans[$barcode]["brand_id"]];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["yarn_lot"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["machine_dia"]." & ".$recvDataArrTrans[$barcode]["machine_gg"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["width"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["stitch_length"];?></td>
								<td width="80" align="center"><? echo $recvDataArrTrans[$barcode]["gsm"];?></td>
								<td width="80" align="center"><? echo $machine_arr[$recvDataArrTrans[$barcode]["machine_no_id"]];?></td>
								<td width="100" align="center"><? if($recvDataArrTrans[$barcode]["knitting_source"]==1){echo $company_full_arr[$recvDataArrTrans[$barcode]["knitting_company"]];}else{echo $supplierFull_arr[$recvDataArrTrans[$barcode]["knitting_company"]];} ?></td>
							</tr>
							<?
							$tot_qnty+=$row[csf('qnty')];
							$y++;
						}
					}
						
				}			
				?>
			</table>
		</div>
		<table cellpadding="0" width="1410" class="rpt_table" rules="all" border="1">
			<tfoot>
				<th width="80">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="80">Roll Total :</th>
				<th width="80" id="value_grey_qty" align="right"><? echo number_format($tot_qnty,2); ?></th>
				<th width="100">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
			</tfoot>
		</table>
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
								<td width="120"><p><? echo $trans_store_arr[$row[csf('barcode_no')]];//$row[csf('store_name')]; ?>&nbsp;</p></td>
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

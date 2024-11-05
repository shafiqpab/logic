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
	$party="1,3,21,90";
	echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id in ($data) and  b.category_type=2 group by a.id,a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
}

if($action=="load_drop_down_floors")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}

	echo create_drop_down( "cbo_floor_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_rooms")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}

	echo create_drop_down( "cbo_room_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_racks")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}

	echo create_drop_down( "cbo_rack_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_shelfs")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}
    if($datas[4] != ""){$rack_id_cond="and b.rack_id in ($datas[4])";}

	echo create_drop_down( "cbo_shelf_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond $rack_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_boxs")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}
    if($datas[4] != ""){$rack_id_cond="and b.rack_id in ($datas[4])";}
    if($datas[5] != ""){$shelf_id_cond="and b.shelf_id in ($datas[5])";}

	echo create_drop_down( "cbo_box_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond $rack_id_cond $shelf_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	$dataArr = explode("_",$data);
	if($dataArr[0]==5 || $dataArr[0]==3)
	{
		echo create_drop_down( "cbo_supplier_id", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- All Supplier --", "", "",0,"" );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 100, "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$dataArr[1]' and b.party_type in(1,9) order by id,supplier_name","id,supplier_name", 1, "-- All Supplier --", $selected, "",0 );
	}
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
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
			else
			{
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
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'room_rack_wise_finish_fabric_stock_report_gmts_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

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
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$year_id";
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";

	if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field_by from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
}

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year_id=$data[2];

	?>
	<script>
		function js_set_value(booking_no,booking_entry_type)
		{
			document.getElementById('selected_booking').value=booking_no;
			document.getElementById('selected_booking_entry_type').value=booking_entry_type;
			parent.emailwindow.hide();
		}

		function fnc_show()
		{
			if($("txt_booking_no").val() !="" || $("cbo_buyer_name").val()!=0 || ( $("txt_date_from").val()!="" && $("txt_date_to").val()!="" ))
			{
				show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txt_booking_no').value+'_'+'<? echo $year_id; ?>','create_booking_search_list_view', 'search_div', 'room_rack_wise_finish_fabric_stock_report_gmts_controller','setFilterGrid(\'list_view\',-1)');
			}
			else
			{
				alert("Please any Criteria.");
			}
		}

	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<tr>
						<td align="center" width="100%">
							<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
								<thead>
									<th width="150">Buyer Name</th>
									<th width="150">Booking No</th>
									<th width="200">Date Range</th>
									<th></th>
								</thead>
								<tr>
									<input type="hidden" id="selected_booking">
									<input type="hidden" id="selected_booking_entry_type">
									<td>
										<?
										echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
										?>
									</td>
									<td>
										<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px">
									</td>
									<td>
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show();" style="width:100px;" /></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td  align="center" height="40" valign="middle">
								<?
								echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
								?>
								<? echo load_month_buttons();  ?>
							</td>
						</tr>
						<tr>
							<td align="center" valign="top" id="search_div">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</body>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
		exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$buyer_id = $data[0];
	$company=$data[3];
	$booking_no=$data[4];
	$year_id=$data[5];

	if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(a.insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(a.insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	
	$booking_cond = ($booking_no!="")?" and a.booking_no_prefix_num=$booking_no":"";

	if($buyer_id !=0)
	{
		$buyer_id_cond=" and a.buyer_id=".$buyer_id;
	}

	$sql= "SELECT a.entry_form, a.id,a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,
	a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode, c.po_number, d.job_no
	from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
	where a.booking_no = b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and a.company_id in ($company) $booking_cond $booking_date $buyer_id_cond $year_cond
	and a.booking_type in (1,3,4) and a.item_category=2
	and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0
	union all
	SELECT a.entry_form_id as entry_form, a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source,
	a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode, null as po_number, null as job_no
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.company_id in ($company) $booking_cond $booking_date $buyer_id_cond $year_cond
	and a.booking_type in (4) and a.item_category=2
	and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0";

	$result = sql_select($sql);
	foreach ($result as $row)
	{
		$booking_data_array[$row[csf("booking_no")]]['id']=$row[csf("id")];
		$booking_data_array[$row[csf("booking_no")]]['booking_no_prefix_num']=$row[csf("booking_no_prefix_num")];
		$booking_data_array[$row[csf("booking_no")]]['entry_form']=$row[csf("entry_form")];
		$booking_data_array[$row[csf("booking_no")]]['booking_no']=$row[csf("booking_no")];
		$booking_data_array[$row[csf("booking_no")]]['booking_date']=$row[csf("booking_date")];
		$booking_data_array[$row[csf("booking_no")]]['company_id']=$row[csf("company_id")];
		$booking_data_array[$row[csf("booking_no")]]['buyer_id']=$row[csf("buyer_id")];
		$booking_data_array[$row[csf("booking_no")]]['item_category']=$row[csf("item_category")];
		$booking_data_array[$row[csf("booking_no")]]['fabric_source']=$row[csf("fabric_source")];
		$booking_data_array[$row[csf("booking_no")]]['supplier_id']=$row[csf("supplier_id")];
		$booking_data_array[$row[csf("booking_no")]]['is_approved']=$row[csf("is_approved")];
		$booking_data_array[$row[csf("booking_no")]]['ready_to_approved']=$row[csf("ready_to_approved")];
		$booking_data_array[$row[csf("booking_no")]]['pay_mode']=$row[csf("pay_mode")];
		$booking_data_array[$row[csf("booking_no")]]['po_number'] .=$row[csf("po_number")].',';
		$booking_data_array[$row[csf("booking_no")]]['job_no'] .=$row[csf("job_no")].',';
	}
	?>

	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
		
	</style>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="80">Booking No</th>
			<th width="80">Booking Date</th>
			<th width="70">Company</th>
			<th width="100">Buyer</th>
			<th width="90">Job No</th>
			<th width="200">PO Number</th>
			<th width="80">Fabric Nature</th>
			<th width="80">Fabric Source</th>
			<th width="50">Supplier</th>
			<th width="50">Approved</th>
			<th width="50">Is-Ready</th>
		</thead>
	</table>
	<div style="width:1080px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;

			foreach ($booking_data_array as $booking_id=> $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_ids= implode(",",array_unique(explode(",",chop($row['po_number'],','))));
				$job_no= implode(",",array_unique(explode(",",chop($row['job_no'],','))));

				if ($row['pay_mode']==3 || $row['pay_mode']==5) 
				{
					$suplier_company=$company_arr;
				}
				else
				{
					$suplier_company=$suplier_array;
				}

				$data=$row['booking_no_prefix_num'].'__'.$entry_form;

				if($row['entry_form']==140 || $row['entry_form']=="")
				{
					$entry_form=90;
				}
				else
				{
					$entry_form=$row['entry_form'];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row['booking_no_prefix_num']; ?>','<? echo $entry_form;?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="80" align="center"><? echo $row['booking_no']; ?></td>
					<td width="80" align="center"><? echo change_date_format($row['booking_date']); ?></td>
					<td width="70" align="center"><? echo $company_arr[$row['company_id']]; ?></td>
					<td width="100" align="center"><? echo $buyer_arr[$row['buyer_id']]; ?></td>
					<td width="90" align="center" class="word_wrap_break"><? echo $job_no; ?></td>
					<td width="200" align="center" class="word_wrap_break"><? echo $po_ids; ?></td>
					<td width="80" align="center"><? echo $item_category[$row['item_category']];?></td>
					<td width="80" align="center"><? echo $fabric_source[$row['fabric_source']];?></td>
					<td width="50" align="center"><? echo $suplier_company[$row['supplier_id']]; ?></td>
					<td width="50" align="center"><? echo ($row['is_approved']==1)? "Yes":"No"; ?></td>
					<td width="50" align="center"><? echo $is_ready[$row['ready_to_approved']];?></td>
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

if($action=="pinumber_popup")
{
	echo load_html_head_contents("PI Number Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#pi_id").val(splitData[0]);
			$("#pi_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%; margin-top:5px" >
		<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
			<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th>Supplier</th>
						<th>Search By</th>
						<th id="search_by_td_up">Enter PI Number</th>
						<th>Date Range</th>
						<th>
							<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
							<input type="hidden" id="pi_id" value="" />
							<input type="hidden" id="pi_no" value="" />
						</th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td>
							<?
							$sql_supplier = "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type in(1,9) order by id,supplier_name";
							echo create_drop_down( "cbo_supplier_id", 130,"$sql_supplier",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
							?>
						</td>

						<td align="center">
							<?
							$search_by_arr=array(1=>"PI No",2=>"LC No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
						</td>

						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" placeholder="To Date" readonly />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value, 'create_pi_search_list_view', 'search_div', 'room_rack_wise_finish_fabric_stock_report_gmts_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>
			</table>
			<div align="center" style="margin-top:10px" id="search_div"> </div>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}


if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$search_type = $ex_data[5];
	$pi_date_cond="";
	if( $from_date!="" && $to_date!="")
	{
		if($db_type==0)
		{
			$pi_date_cond= " and a.pi_date between '".change_date_format($from_date,"yyyy-mm-dd")."' and '".change_date_format($to_date,"yyyy-mm-dd")."'";
		}
		else
		{
			$pi_date_cond= " and a.pi_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}

	if($search_type == 1)
	{
		$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and entry_form=166 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";
	}else{
		$sql= "select  a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.last_shipment_date, a.total_amount from  com_pi_master_details a, com_btb_lc_master_details b, com_btb_lc_pi c where b.id = c.com_btb_lc_master_details_id and c.pi_id = a.id and a.importer_id =$company and a.entry_form = 166 and a.supplier_id like '$cbo_supplier' and b.lc_number like '%".$txt_search_common."%' $pi_date_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.last_shipment_date, a.total_amount";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");

	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,total_amount", "",'','0,0,0,3,3,2') ;
	exit();
}


if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$book_no 			= trim(str_replace("'","",$txt_book_no));
	$book_id 			= str_replace("'","",$txt_book_id);
	$job_no 			= trim(str_replace("'","",$txt_job_no));
	$txt_internal_ref	= trim(str_replace("'","",$txt_internal_ref));
	$txt_pi_no 			= trim(str_replace("'","",$txt_pi_no));
	$hdn_pi_id 			= trim(str_replace("'","",$hdn_pi_id));
	$txt_batch_no 		= trim(str_replace("'","",$txt_batch_no));
	$cbo_booking_type 	= str_replace("'","",$cbo_booking_type);

	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_pay_mode 		= str_replace("'","",$cbo_pay_mode);
	$cbo_supplier_id 	= str_replace("'","",$cbo_supplier_id);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$cbo_floor_id 		= str_replace("'","",$cbo_floor_id);
	$cbo_room_id 		= str_replace("'","",$cbo_room_id);
	$cbo_rack_id 		= str_replace("'","",$cbo_rack_id);
	$cbo_shelf_id 		= str_replace("'","",$cbo_shelf_id);
	$cbo_box_id 		= str_replace("'","",$cbo_box_id);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);
	$cbo_value_with 	= str_replace("'","",$cbo_value_with);

	$get_upto 			= str_replace("'","",$cbo_get_upto);
	$txt_days 			= str_replace("'","",$txt_days);
	$get_upto_qnty 		= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 			= str_replace("'","",$txt_qnty);

	if($cbo_store_name > 0){
		$store_cond = " and b.store_id in ($cbo_store_name)";
		$store_cond_2 = " and c.store_id in ($cbo_store_name)";
	}

	if($cbo_floor_id > 0){
		$floor_cond = " and b.floor_id in ($cbo_floor_id)";
		$floor_cond_2 = " and c.floor_id in ($cbo_floor_id)";
	}
	if($cbo_room_id > 0){
		$room_cond = " and b.room in ($cbo_room_id)";
		$room_cond_2 = " and c.room in ($cbo_room_id)";
	}
	if($cbo_rack_id > 0){
		$rack_cond = " and b.rack in ($cbo_rack_id)";
		$rack_cond_2 = " and c.rack in ($cbo_rack_id)";
	}
	if($cbo_shelf_id > 0){
		$shelf_cond = " and b.self in ($cbo_shelf_id)";
		$shelf_cond_2 = " and c.self in ($cbo_shelf_id)";
	}
	if($cbo_box_id > 0){
		$box_cond = " and b.bin_box in ($cbo_box_id)";
		$box_cond_2 = " and c.bin_box in ($cbo_box_id)";
	}

	if($txt_batch_no)
	{
		$batch_cond = " and e.batch_no like '%$txt_batch_no%'";
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_id=$buyer_id";
	}

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(f.insert_date)=$job_year";
		if($job_year==0) $year_cond2=""; else $year_cond2=" and YEAR(insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(f.insert_date,'YYYY')=$job_year";
		if($job_year==0) $year_cond2=""; else $year_cond2=" and to_char(insert_date,'YYYY')=$job_year";
	}

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond   = " and b.transaction_date <= '$end_date'";
		$date_cond_2 = " and c.transaction_date <= '$end_date'";
	}

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$con = connect();
	$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (102)");

	//$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	if($cbo_booking_type==118 || $cbo_booking_type==108)
	{
		$bulk_booking_type= " and d.booking_type=1 and d.is_short=2 and d.entry_form=$cbo_booking_type";
	}
	else if($cbo_booking_type==88){
		$bulk_booking_type= " and d.booking_type=1 and d.is_short=1";
	}
	else if($cbo_booking_type==89){
		$bulk_booking_type= " and d.booking_type=4";
	}
	else if( $cbo_booking_type==90)
	{
		$samp_booking_type= " and booking_type=4";
	}


	if($r_id3 && $r_id6)
	{
		oci_commit($con);
	}
	$pi_no_cond="";
	if ($hdn_pi_id=="")
	{
		$pi_no_cond="";
	}
	else
	{
		$pi_no_cond=" and a.booking_id = '$hdn_pi_id' and a.receive_basis=1 ";
		$pi_no_trans_cond = " and a.id = 0";
	}
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
	if ($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and e.grouping='$txt_internal_ref'";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and d.booking_no_prefix_num='$book_no'";
	if($cbo_supplier_id ==0) $supplier_cond = ""; else $supplier_cond = " and d.supplier_id = ".$cbo_supplier_id;
	if($cbo_pay_mode ==0) $pay_mode_cond = ""; else $pay_mode_cond = " and d.pay_mode = ".$cbo_pay_mode;

	if($job_no != "" || $txt_internal_ref != "" || $book_no!="" || $cbo_supplier_id !=0 || $buyer_id!=0 || $cbo_pay_mode !=0 || $cbo_booking_type!=0)
	{
		if($cbo_booking_type !=90)
		{
			$serch_ref_sql_1 = "SELECT c.booking_no, e.id as po_id from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and f.job_no=e.job_no_mst and c.booking_type in (1,3,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $internal_ref_cond $booking_no_cond $year_cond $pay_mode_cond $supplier_cond $bulk_booking_type";
		}

		$concate="";
		if($job_no == "" && $txt_internal_ref == "" && ($cbo_booking_type ==0 || $cbo_booking_type ==90))
		{
			$concate = " union all ";
			$serch_ref_sql_2 = " SELECT d.booking_no, null as po_id from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $pay_mode_cond $supplier_cond $buyer_id_cond $year_cond2 $samp_booking_type";
		}


		if($serch_ref_sql_1 !="" && $serch_ref_sql_2 !="")
		{
			$concate = " union all ";
			$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;
		}
		else if($serch_ref_sql_1 !="")
		{
			$serch_ref_sql = $serch_ref_sql_1;
		}
		else
		{
			$serch_ref_sql = $serch_ref_sql_2;
		}

		$serch_ref_result = sql_select($serch_ref_sql);
		//echo $serch_ref_sql;die;

		foreach ($serch_ref_result as $val)
		{
			$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
			if($job_no != "" || $txt_internal_ref != "")
			{
				if($val[csf("po_id")]!=""){
					$search_po_id[$val[csf("po_id")]] = $val[csf("po_id")];
				}
			}
		}

		if(empty($search_book_arr))
		{
			echo "<p style='font-weight:bold;text-align:center;font-size:20px;'>Booking No not found</p>";
			disconnect($con);
			die;
		}
	}

	if(!empty($search_book_arr))
	{
		$search_book_nos="'".implode("','",$search_book_arr)."'";
		$search_book_arr = explode(",", $search_book_nos);

		$all_book_nos_cond=""; $bookCond="";
		if($db_type==2 && count($search_book_arr)>999)
		{
			$all_search_book_arr_chunk=array_chunk($search_book_arr,999) ;
			foreach($all_search_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bookCond.="  e.booking_no in($chunk_arr_value) or ";
			}

			$all_book_nos_cond.=" and (".chop($bookCond,'or ').")";
		}
		else
		{
			$all_book_nos_cond=" and e.booking_no in($search_book_nos)";
		}

		//fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 990, 1,$all_po_id_arr, $empty_arr);//PO ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 102, 1,$search_po_id, $empty_arr);//PO ID
	}
	//echo $all_book_nos_cond;die;

	if($report_type==2)
	{
		$rcv_select = " b.floor_id as FLOOR_ID, b.room as ROOM, b.rack as RACK, b.self as SELF, b.BIN_BOX,"; 
		$rcv_group = " b.floor_id, b.room, b.rack, b.self, b.bin_box,";
	}

	$rcv_sql = "SELECT b.id as ID, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, a.company_id as COMPANY_ID,a.receive_basis as  RECEIVE_BASIS, a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY,a.booking_id as WO_PI_PROD_ID,a.booking_no as WO_PI_PROD_NO, b.transaction_date as TRANSACTION_DATE, b.prod_id as PROD_ID, b.store_id as STORE_ID, b.floor_id as FLOOR_ID, b.room as ROOM, b.rack as RACK, b.self as SELF, b.BIN_BOX, c.body_part_id as BODY_PART_ID, c.fabric_description_id as FABRIC_DESCRIPTION_ID, f.gsm as GSM, upper(c.width) as WIDTH, f.color as COLOR_ID, b.cons_uom as CONS_UOM, c.dia_width_type as DIA_WIDTH_TYPE, d.po_breakdown_id as PO_BREAKDOWN_ID, g.grouping as GROUPING, d.quantity as PROP_QUANTITY, b.cons_quantity as CONS_QUANTITY, b.order_rate as ORDER_RATE, b.order_amount as ORDER_AMOUNT, b.pi_wo_batch_no as PI_WO_BATCH_NO, a.lc_sc_no as LC_SC_NO, e.batch_no as BATCH_NO, c.no_of_roll as NO_OF_ROLL";
	if(!empty($search_po_id))
	{
		$rcv_sql .=" FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, order_wise_pro_details d, pro_batch_create_mst e, product_details_master f, wo_po_break_down g, gbl_temp_engine h 
		WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id = f.id and c.trans_id=d.trans_id and c.id=d.dtls_id and d.entry_form=37 and d.quantity <>0 and d.po_breakdown_id=h.ref_val and h.ref_from=1 and h.user_id=$user_id and h.entry_form=102 and d.po_breakdown_id=g.id $store_cond $date_cond  $all_book_nos_cond $pi_no_cond $batch_cond $floor_cond $room_cond $rack_cond $shelf_cond $box_cond";
	}
	else
	{
		$rcv_sql .=" FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and c.id = d.dtls_id and entry_form=37 and d.quantity<>0 left join wo_po_break_down g on d.po_breakdown_id=g.id, pro_batch_create_mst e, product_details_master f
		WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id = f.id $store_cond $date_cond  $all_book_nos_cond $pi_no_cond $batch_cond $floor_cond $room_cond $rack_cond $shelf_cond $box_cond";
	}

	// echo $rcv_sql ;die;

	//echo $rcv_sql;
	$rcv_data = sql_select($rcv_sql);

	$data_array=array();
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
		$ref_str="";
		
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val["DIA_WIDTH_TYPE"])));


		if($val['PROP_QUANTITY']) 
		{
			$QUANTITY = $val['PROP_QUANTITY'];
		}
		else
		{
			$QUANTITY = $val['CONS_QUANTITY'];
		}

		if($report_type==2)
		{
			$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["FABRIC_DESCRIPTION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"]."*".$val["FLOOR_ID"]."*".$val["ROOM"]."*".$val["RACK"]."*".$val["SELF"]."*".$val["BIN_BOX"];

		}
		else
		{
			$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["FABRIC_DESCRIPTION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"];

		}

		if($transaction_date >= $date_frm)
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $QUANTITY."*".$val["ORDER_RATE"]."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*1*".$val["NO_OF_ROLL"]."__";
		}
		else
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $QUANTITY."*".$val["ORDER_RATE"]."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*2*".$val["NO_OF_ROLL"]."__";
		}
		$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

		if($val["BOOKING_WITHOUT_ORDER"] == 0)
		{
			$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
			$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
			$po_array[$val["BOOKING_NO"]][$ref_str]["grouping"] .= $val["GROUPING"].",";
		}

		$book_str = explode("-", $val["BOOKING_NO"]);
		if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
		{
			$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		}
		$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["CONS_QUANTITY"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["cons_quantity"] = $val["CONS_QUANTITY"];
	}
	unset($rcv_data);
	// echo '<pre>';
	// print_r($rate_arr_booking_and_product_wise);
	// echo '</pre>';
	

	if($report_type == 2)
	{
		$trans_in_select = " c.floor_id as FLOOR_ID, c.room as ROOM, c.rack as RACK, c.self as SELF, c.BIN_BOX,";
		$trans_in_group = " c.floor_id, c.room, c.rack, c.self, c.bin_box,";
	}

	if ($hdn_pi_id=="")
	{
		$trans_in_sql = "SELECT c.transaction_date as TRANSACTION_DATE, c.pi_wo_batch_no as PI_WO_BATCH_NO, e.batch_no as BATCH_NO, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, c.body_part_id as BODY_PART_ID, c.prod_id as PROD_ID, c.store_id as STORE_ID, $trans_in_select d.detarmination_id as DETARMINATION_ID, d.gsm as GSM, upper(d.dia_width) as WIDTH, d.color as COLOR_ID, c.cons_uom as  CONS_UOM, f.quantity as PROP_QUANTITY, c.cons_quantity as CONS_QUANTITY, c.order_rate as ORDER_RATE, c.order_amount as ORDER_AMOUNT, f.po_breakdown_id as PO_BREAKDOWN_ID, g.grouping as GROUPING, b.batch_id as BATCH_ID, b.from_store as FROM_STORE, b.from_prod_id as FROM_PROD_ID, b.no_of_roll as NO_OF_ROLL";

		if(!empty($search_po_id))
		{
			$trans_in_sql .=" from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, order_wise_pro_details f, wo_po_break_down g, product_details_master d, pro_batch_create_mst e, gbl_temp_engine h  
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.id = f.trans_id and f.trans_type=5 and f.status_active=1 and f.po_breakdown_id<>0 and f.po_breakdown_id=g.id and e.status_active=1 and a.entry_form in (14,15,306) and g.id=h.ref_val and h.ref_from=1 and h.entry_form =102 and h.user_id=$user_id $store_cond_2 $date_cond_2 $all_book_nos_cond $batch_cond $floor_cond_2 $room_cond_2 $rack_cond_2 $shelf_cond_2 $box_cond_2";
		}
		else
		{
			$trans_in_sql .=" from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type=5 and f.status_active=1 and f.po_breakdown_id<>0 left join wo_po_break_down g on f.po_breakdown_id=g.id, product_details_master d, pro_batch_create_mst e
			where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond $batch_cond $floor_cond_2 $room_cond_2 $rack_cond_2 $shelf_cond_2 $box_cond_2";
		}
		
		/* "group by c.transaction_date, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, $trans_in_group d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount, b.batch_id, b.from_store, b.from_prod_id, b.no_of_roll order by c.company_id, c.pi_wo_batch_no"; */
		// echo $trans_in_sql;die;
		$trans_in_data = sql_select($trans_in_sql);
		foreach ($trans_in_data as  $val)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
			$ref_str="";
			


			if($val['PROP_QUANTITY']) 
			{
				$QUANTITY = $val['PROP_QUANTITY'];
			}
			else
			{
				$QUANTITY = $val['CONS_QUANTITY'];
			}

			if($report_type == 2)
			{
				$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["DETARMINATION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"]."*".$val["FLOOR_ID"]."*".$val["ROOM"]."*".$val["RACK"]."*".$val["SELF"]."*".$val["BIN_BOX"];

			}
			else
			{
				$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["DETARMINATION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"];

			}

			if($transaction_date >= $date_frm)
			{
				$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $QUANTITY."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*1*".$val["NO_OF_ROLL"]."__";
			}
			else
			{
				$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $QUANTITY."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*2*".$val["NO_OF_ROLL"]."__";
			}

			$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

			if($val["BOOKING_WITHOUT_ORDER"] == 0)
			{
				$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
				$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
				$po_array[$val["BOOKING_NO"]][$ref_str]["grouping"] .= $val["GROUPING"].",";
			}

			$book_str = explode("-", $val["BOOKING_NO"]);
			if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
			{
				$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
			}
			$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
			$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

			$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val['CONS_QUANTITY'];
			$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];
			$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["cons_quantity"] = $val["CONS_QUANTITY"];

			if($rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"]*1 ==0)
			{
				$all_trans_in_batch[$val["BATCH_ID"]] = $val["BATCH_ID"];
				$trans_in_batch_prod_store[$val["BOOKING_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]] .= $val["BATCH_ID"].'*'.$val["FROM_PROD_ID"].'*'.$val["FROM_STORE"].",";
			}
		}
		unset($trans_in_data);
	}

	$all_trans_in_batch = array_filter($all_trans_in_batch);
	if(!empty($all_trans_in_batch)) 
	{
		/*if(!empty($all_trans_in_batch))
		{
			$all_trans_in_batch_nos=implode(",",$all_trans_in_batch);
			$all_trans_in_batch_nos_cond=""; $transBatchCond="";
			if($db_type==2 && count($all_po_id_arr)>999)
			{
				$all_trans_in_batch_chunk=array_chunk($all_trans_in_batch,999) ;
				foreach($all_trans_in_batch_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$transBatchCond.="  b.pi_wo_batch_no in($chunk_arr_value) or ";
				}

				$all_trans_in_batch_nos_cond.=" and (".chop($transBatchCond,'or ').")";
			}
			else
			{
				$all_trans_in_batch_nos_cond=" and b.pi_wo_batch_no in($all_trans_in_batch_nos)";
			}
		}*/

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 102, 2,$all_trans_in_batch, $empty_arr);//Batch ID

		$rcv_rate_for_transin_sql = sql_select("SELECT b.prod_id as PROD_ID, b.store_id as STORE_ID, b.pi_wo_batch_no as PI_WO_BATCH_NO, sum(b.cons_quantity) as QUANTITY,  sum(b.order_amount) as ORDER_AMOUNT from inv_receive_master a, inv_transaction b, GBL_TEMP_ENGINE c where a.id=b.mst_id  and b.pi_wo_batch_no=c.ref_val and c.user_id=$user_id and c.entry_form=102 and c.ref_from=2  and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and a.is_deleted=0 and b.is_deleted=0  group by b.prod_id, b.store_id, b.pi_wo_batch_no"); // $all_trans_in_batch_nos_cond
		foreach ($rcv_rate_for_transin_sql as $val) 
		{
			$rcv_rate_for_transin_arr[$val["PI_WO_BATCH_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]]['QUANTITY'] += $val["QUANTITY"];
			$rcv_rate_for_transin_arr[$val["PI_WO_BATCH_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]]['ORDER_AMOUNT'] += $val["ORDER_AMOUNT"];
		}
		unset($rcv_rate_for_transin_sql);

		/*echo "<pre>";
		print_r($rcv_rate_for_transin_arr);
		die;*/

		foreach ($trans_in_batch_prod_store as $transInStr => $RcvStr) 
		{
			$transInArr = explode("*", $transInStr);
			$RcvStrArr = array_unique(explode(",",chop($RcvStr,",")));
			foreach ($RcvStrArr as $val) 
			{
				$RcvStrVal = explode("*", $val);

				$rate_arr_booking_and_product_wise[$transInArr[0]][$transInArr[1]][$transInArr[2]]["quantity"] += $rcv_rate_for_transin_arr[$RcvStrVal[0].'*'.$RcvStrVal[1].'*'.$RcvStrVal[2]]['QUANTITY'];
				$rate_arr_booking_and_product_wise[$transInArr[0]][$transInArr[1]][$transInArr[2]]["amount"] += $rcv_rate_for_transin_arr[$RcvStrVal[0].'*'.$RcvStrVal[1].'*'.$RcvStrVal[2]]["ORDER_AMOUNT"];
				$rate_arr_booking_and_product_wise[$transInArr[0]][$transInArr[1]][$transInArr[2]]["cons_quantity"] = $rcv_rate_for_transin_arr[$RcvStrVal[0].'*'.$RcvStrVal[1].'*'.$RcvStrVal[2]]["CONS_QUANTITY"];
			}
		}
	}
	
	/*echo "<pre>";
	print_r($rate_arr_booking_and_product_wise2);
	die;*/
	// echo '<pre>';
	// print_r($rate_arr_booking_and_product_wise);
	// echo '</pre>';
	

	$all_po_id_arr = array_filter($all_po_id_arr);
	if(!empty($all_po_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 102, 3,$all_po_id_arr, $empty_arr);//Query PO ID

		$booking_sql = sql_select("SELECT a.body_part_id as BODY_PART_ID,c.booking_no as BOOKING_NO,a.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID, c.gmts_color_id as GMTS_COLOR_ID, c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT, f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE, f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, a.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and c.booking_type =1 and c.booking_no = d.booking_no and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=3
		union all
		select b.body_part_id as BODY_PART_ID, c.booking_no as BOOKING_NO, b.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID , c.gmts_color_id as GMTS_COLOR_ID,c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT,f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE,f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, b.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c ,  wo_booking_mst d , wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and a.fabric_description = b.id and c.booking_type in (3,4) and c.booking_no = d.booking_no  and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=3");

		foreach ($booking_sql as  $val)
		{
			$book_po_ref[$val["BOOKING_NO"]]["company_name"] 	= $val["COMPANY_NAME"];
			$book_po_ref[$val["BOOKING_NO"]]["buyer_name"] 	= $val["BUYER_NAME"];
			$book_po_ref[$val["BOOKING_NO"]]["job_no"] 		.= $val["JOB_NO"].",";
			$book_po_ref[$val["BOOKING_NO"]]["client_id"] 		= $val["CLIENT_ID"];
			$book_po_ref[$val["BOOKING_NO"]]["season"] 		.= $val["SEASON_BUYER_WISE"].",";
			$book_po_ref[$val["BOOKING_NO"]]["style_ref_no"] 	.= $val["STYLE_REF_NO"].",";
			$book_po_ref[$val["BOOKING_NO"]]["booking_no"] 	= $val["BOOKING_NO"];
			$book_po_ref[$val["BOOKING_NO"]]["booking_date"] 	= $val["BOOKING_DATE"];
			$book_po_ref[$val["BOOKING_NO"]]["pay_mode"] 		= $pay_mode[$val["PAY_MODE"]];
			if($val["PAY_MODE"] == 3 || $val["PAY_MODE"] == 5)
			{
				$book_po_ref[$val["BOOKING_NO"]]["supplier"] = $company_arr[$val["SUPPLIER_ID"]];
			}else{
				$book_po_ref[$val["BOOKING_NO"]]["supplier"] = $supplier_arr[$val["SUPPLIER_ID"]];
			}

			$job_qnty_arr[$val["JOB_NO"]]["qnty"] = $val["JOB_QUANTITY"]*$val["TOTAL_SET_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["qnty"] += $val["FIN_FAB_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["color_type"] .= $color_type[$val["COLOR_TYPE"]].",";

			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["amount"] += $val["FIN_FAB_QNTY"]*$val["RATE"];

			$bookingType="";
			if($val['BOOKING_TYPE'] == 4)
			{
				$bookingType = "Sample With Order";
			}
			else if($val['BOOKING_TYPE'] == 3)
			{
				$bookingType = "Service Booking";
			}
			else
			{
				$bookingType = $booking_type_arr[$val['ENTRY_FORM']];
			}
			$book_po_ref[$val["BOOKING_NO"]]["booking_type"] = $bookingType;
		}
		unset($booking_sql);
	}
	/*echo "<pre>";
	print_r($book_po_ref);*/

	if(!empty($all_samp_book_arr))
	{
		foreach ($all_samp_book_arr as $s_book) {
			$rID2=execute_query("insert into tmp_booking_no (userid, booking_no) values ($user_id,".$s_book.")");
		}
		if($rID2)
		{
			oci_commit($con);
		}

		//$all_samp_book_ids = implode(",", $all_samp_book_arr);
		$non_samp_sql = sql_select("select a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.gmts_color, b.fabric_color, b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_no c where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4  and a.booking_no=c.booking_no and c.userid=$user_id "); //and a.id in ($all_samp_book_ids)

		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			$book_po_ref[$val[csf("booking_no")]]["pay_mode"] 		= $pay_mode[$val[csf("pay_mode")]];
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["color_type"] .= $color_type[$val[csf("color_type_id")]].",";
		}
		unset($non_samp_sql);
	}

	$batch_id_arr = array_filter($batch_id_arr);
	//print_r($batch_id_arr );die;
	if(!empty($batch_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 102, 4,$batch_id_arr, $empty_arr);//Batch ID
	}
	oci_commit($con);

	if($report_type == 2)
	{
		$issue_return_select = " c.floor_id, c.room, c.rack, c.self, c.bin_box,";
	}

	/* if(!empty($all_po_id_arr))
	{
		$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, upper(b.width) as width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order, c.no_of_roll, h.quantity as prod_quantity, h.po_breakdown_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g, order_wise_pro_details h, GBL_TEMP_ENGINE i 
		where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 and c.id=h.trans_id and h.po_breakdown_id=i.ref_val and i.ref_from=3 and i.user_id=$user_id and i.entry_form=102";
	}
	else
	{
		$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, upper(b.width) as width, f.color as color_id,c.cons_uom, c.cons_quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order, c.no_of_roll, null as prod_quantity, null as po_breakdown_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g 
		where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2";
	} */

	if(!empty($search_po_id))
	{
		$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, f.gsm, upper(b.width) as width, f.color as color_id,c.cons_uom, c.cons_quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order, c.no_of_roll, h.quantity as prop_quantity, h.po_breakdown_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, order_wise_pro_details h, GBL_TEMP_ENGINE i, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g 
		where a.id = b.mst_id and b.trans_id=c.id and c.id=h.trans_id and h.entry_form=52 and h.po_breakdown_id=i.ref_val and i.ref_from=3 and i.user_id=$user_id and i.entry_form=102 and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2";
	}
	else
	{
		$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, f.gsm, upper(b.width) as width, f.color as color_id,c.cons_uom, c.cons_quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order, c.no_of_roll, h.quantity as prop_quantity, h.po_breakdown_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c left join order_wise_pro_details h on c.id=h.trans_id and h.entry_form=52 left join GBL_TEMP_ENGINE i on h.po_breakdown_id=i.ref_val and i.ref_from=3 and i.user_id=$user_id and i.entry_form=102, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g 
		where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2";
	}

	

	$issRtnData = sql_select($issRtnSql);

	$issue_return_data = array();

	foreach ($issRtnData as $val)
	{
		if($report_type == 2)
		{
			$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("batch_id")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")]."*".$val[csf("bin_box")];

		}
		else
		{
			$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("batch_id")]."*".$val[csf("batch_no")];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";

		if( $val[csf("prop_quantity")]!="")
		{
			$QUANTITY=$val[csf("prop_quantity")];
		}
		else
		{
			$QUANTITY=$val[csf("cons_quantity")];
		}


		if($transaction_date >= $date_frm)
		{
			if($val[csf("knit_dye_source")] == 1)
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return"] += $QUANTITY;
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_in"] += $val[csf("no_of_roll")];

				if( $val[csf("po_breakdown_id")] !="")
				{
					$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_po"] .= $val[csf("po_breakdown_id")].',';
				}
			}
			else
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return"] += $QUANTITY;
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_out"] += $val[csf("no_of_roll")];
				if( $val[csf("po_breakdown_id")] !="")
				{
					$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_po"] .= $val[csf("po_breakdown_id")].',';
				}

			}
		}
		else
		{
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening"] += $QUANTITY;
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening_amount"] +=$QUANTITY*$val[csf("order_rate")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_open"] +=$val[csf("no_of_roll")];
		}
	}
	unset($issRtnData);

	if($report_type == 2)
	{
		$issue_select = " c.floor_id, c.room, c.rack, c.self, c.bin_box,";
		$issue_group = " c.floor_id, c.room, c.rack, c.self, c.bin_box,";
	}
	
	
	/* if(!empty($all_po_id_arr))
	{
		$issue_sql = "SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, f.quantity as prod_quantity, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate, b.no_of_roll as no_of_roll, f.po_breakdown_id
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g, order_wise_pro_details f, GBL_TEMP_ENGINE h
		where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 and c.id=f.trans_id and f.entry_form=18 and f.po_breakdown_id=h.ref_val and h.ref_from=3 and h.entry_form=102 and h.user_id=$user_id";
	
	}
	else
	{
		$issue_sql = "SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, null as prod_quantity, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate, b.no_of_roll as no_of_roll, null as po_breakdown_id
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g 
		where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2";
	} */

	if(!empty($search_po_id))
	{
		$issue_sql = "SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, f.quantity as prod_quantity, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate, b.no_of_roll as no_of_roll, f.po_breakdown_id, h.ref_val
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , order_wise_pro_details f, GBL_TEMP_ENGINE h, 
		product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g 
		where a.id = b.mst_id and b.trans_id = c.id and c.id=f.trans_id and f.entry_form=18 
		and f.po_breakdown_id=h.ref_val and h.ref_from=3 and h.entry_form=102 and h.user_id=$user_id
		and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) 
		$store_cond_2 $date_cond_2 		
		and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.entry_form=18 and c.status_active=1 
		and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2";
	}
	else
	{

		$issue_sql = "SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, f.quantity as prod_quantity, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate, b.no_of_roll as no_of_roll, f.po_breakdown_id
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c left join order_wise_pro_details f on c.id=f.trans_id and f.entry_form=18 left join GBL_TEMP_ENGINE h on f.po_breakdown_id=h.ref_val and h.ref_from=3 and h.entry_form=102 and h.user_id=$user_id, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g
		where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2";
	}

	//echo $issue_sql;die;
	//$issue_sql .=" group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_group c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2), b.no_of_roll";

	$issue_sql_result = sql_select($issue_sql);
	
	$issue_data = array();
	foreach ($issue_sql_result as $val)
	{
		$issRef_str="";
		if($report_type == 2)
		{
			$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")]."*".$val[csf("bin_box")];
		}
		else
		{
			$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));

		if( $val[csf("prod_quantity")]!="")
		{
			$QUANTITY=$val[csf("prod_quantity")];
		}
		else
		{
			$QUANTITY=$val[csf("cons_quantity")];
		}
		if($transaction_date >= $date_frm)
		{
			if($val[csf("issue_purpose")] == 9)
			{
				if($val[csf("knit_dye_source")] == 1)
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside"] += $QUANTITY;
					$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_in"] += $val[csf("no_of_roll")];

					if( $val[csf("po_breakdown_id")] !="")
					{
						$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside_po"] .= $val[csf("po_breakdown_id")].',';
					}
				}
				else
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside"] += $QUANTITY;
					$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_out"] += $val[csf("no_of_roll")];

					if( $val[csf("po_breakdown_id")] !="")
					{
						$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside_po"] .= $val[csf("po_breakdown_id")].',';
					}
					
				}
			}
			else
			{
				$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue"] += $QUANTITY;
				$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_other"] += $val[csf("no_of_roll")];
				if( $val[csf("po_breakdown_id")] !="")
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue_po"] .= $val[csf("po_breakdown_id")].',';
				}

			}
			$issue_data[$val[csf("booking_no")]][$issRef_str]["issue_amount"] += $QUANTITY*$val[csf("order_rate")];
		
		}
		else
		{
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue"] += $QUANTITY;
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue_amount"] += $QUANTITY*$val[csf("order_rate")];
			$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_open"] += $val[csf("no_of_roll")];
			
		}
	}
	unset($issue_sql_result);
	/*echo "<pre>";
	print_r($issue_data);
	die;*/
	if($report_type == 2){
		$rcv_return_select = " c.floor_id, c.room, c.rack, c.self, c.bin_box,";
	}


	/* if(!empty($all_po_id_arr))
	{
		$rcvRtnSql= "SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.pi_wo_batch_no, e.batch_no, b.no_of_roll, g.quantity as prod_quantity, g.po_breakdown_id 
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE f, order_wise_pro_details g, GBL_TEMP_ENGINE h
		where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=102 and f.ref_from=4 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1 and c.id=g.trans_id and g.po_breakdown_id=h.ref_val and h.entry_form=102 and h.ref_from=3 and h.user_id=$user_id";
	}
	else
	{
		$rcvRtnSql= "SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.pi_wo_batch_no, e.batch_no, b.no_of_roll, null as prod_quantity, null as po_breakdown_id
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g 
		where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1";
	} */

	if(!empty($search_po_id))
	{
		$rcvRtnSql= "SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.pi_wo_batch_no, e.batch_no, b.no_of_roll, g.quantity as prod_quantity, g.po_breakdown_id 
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, order_wise_pro_details g, GBL_TEMP_ENGINE h, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE f
		where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.id=g.trans_id and g.entry_form=46 and g.po_breakdown_id=h.ref_val and h.entry_form=102 and h.ref_from=3 and h.user_id=$user_id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and c.prod_id=d.id and c.pi_wo_batch_no=e.id and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=102 and f.ref_from=4 and a.status_active =1 and b.status_active =1 and c.status_active =1";
	}
	else
	{
		$rcvRtnSql= "SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.pi_wo_batch_no, e.batch_no, b.no_of_roll, g.quantity as prod_quantity, g.po_breakdown_id 
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c left join order_wise_pro_details g on c.id=g.trans_id and g.entry_form=46 left join GBL_TEMP_ENGINE h on g.po_breakdown_id=h.ref_val and h.entry_form=102 and h.ref_from=3 and h.user_id=$user_id, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE f
		where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=102 and f.ref_from=4 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1";
	}

	

	$rcvRtnSql_result = sql_select($rcvRtnSql);
	$rcv_return_data =array();
	foreach ($rcvRtnSql_result as $val)
	{
		if($report_type == 2)
		{
			$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")]."*".$val[csf("bin_box")];
		}
		else
		{
			$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];
		}
		

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";

		if( $val[csf("prod_quantity")]!="")
		{
			$QUANTITY=$val[csf("prod_quantity")];
		}
		else
		{
			$QUANTITY=$val[csf("cons_quantity")];
		}

		if($transaction_date >= $date_frm)
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["qnty"] += $QUANTITY;
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["amount"] += $QUANTITY*$val[csf("order_rate")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["no_of_roll"] += $val[csf("no_of_roll")];
			if( $val[csf("po_breakdown_id")] !="")
			{
				$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["receive_ret_po"] .= $val[csf("po_breakdown_id")].',';
			}
		}
		else
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_qnty"] += $QUANTITY;
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_amount"] += $QUANTITY*$val[csf("order_rate")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["no_of_roll_open"] += $val[csf("no_of_roll")];
		}
	}
	unset($rcvRtnSql_result);

	if($report_type == 2)
	{
		$trans_out_select = " c.floor_id, c.room, c.rack, c.self, c.bin_box,";
	}


	/* if(!empty($all_po_id_arr))
	{
		$transOutSql = "SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate, b.no_of_roll, g.quantity as prod_quantity, g.po_breakdown_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE f, order_wise_pro_details g, GBL_TEMP_ENGINE h
	where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=102 and f.ref_from=4  and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and c.id=g.trans_id and g.trans_type=6 and g.po_breakdown_id=h.ref_val and h.ref_from=3 and h.entry_form=102 and h.user_id=$user_id";
	}
	else
	{
		$transOutSql = "SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate, b.no_of_roll, null as prod_quantity, null as po_breakdown_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g 
	where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=4  and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)";
	} */

	if(!empty($search_po_id))
	{
		$transOutSql = "SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate, b.no_of_roll, g.quantity as prod_quantity, g.po_breakdown_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, order_wise_pro_details g, GBL_TEMP_ENGINE h, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE f
		where a.id=b.mst_id and b.trans_id=c.id and c.id=g.trans_id and g.trans_type=6 and g.po_breakdown_id=h.ref_val and h.ref_from=3 and h.entry_form=102 and h.user_id=$user_id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=102 and f.ref_from=4  and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)";
	}
	else
	{
		$transOutSql = "SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, upper(d.dia_width) as dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate, b.no_of_roll, g.quantity as prod_quantity, g.po_breakdown_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details g on c.id=g.trans_id and g.trans_type=6 left join GBL_TEMP_ENGINE h on g.po_breakdown_id=h.ref_val and h.ref_from=3 and h.entry_form=102 and h.user_id=$user_id,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE f
		where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=102 and f.ref_from=4  and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)";
	}

	

	$transOutSql_result = sql_select($transOutSql);

	$trans_out_data = array();
	foreach ($transOutSql_result as $val)
	{
		if($report_type == 2)
		{
			$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")]."*".$val[csf("bin_box")];
		}
		else
		{
			$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";

		if( $val[csf("prod_quantity")]!="")
		{
			$QUANTITY=$val[csf("prod_quantity")];
		}
		else
		{
			$QUANTITY=$val[csf("cons_quantity")];
		}

		if($transaction_date >= $date_frm)
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["qnty"] += $QUANTITY;
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["amount"] += $QUANTITY*$val[csf("order_rate")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["no_of_roll"] += $val[csf("no_of_roll")];

			if( $val[csf("po_breakdown_id")] !="")
			{
				$trans_out_data[$val[csf("booking_no")]][$transOut_str]["trans_out_po"] .= $val[csf("po_breakdown_id")].',';
			}
		}
		else
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_qnty"] += $QUANTITY;
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_amount"] += $QUANTITY*$val[csf("order_rate")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["no_of_roll_open"] += $val[csf("no_of_roll")];
			
		}
	}
	unset($transOutSql_result);

    /*echo "<pre>";
    print_r($consumption_arr);
    die;*/

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1";
    $data_deter=sql_select($sql_deter);

    if(count($data_deter)>0)
    {
    	foreach( $data_deter as $row )
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
    unset($data_deter);

    if(!empty($all_prod_id))
    {
    	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 102, 5,$all_prod_id, $empty_arr); //Product id temp insert

    	$transaction_date_array=array();
    	// if($all_prod_id_cond!=""){
    	if(!empty($all_prod_id)){
    		$sql_date="SELECT c.booking_no, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,pro_batch_create_mst c, GBL_TEMP_ENGINE g where a.pi_wo_batch_no=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2  and a.prod_id=g.ref_val and g.user_id=$user_id and g.entry_form=102 and g.ref_from=5 group by c.booking_no,a.prod_id"; // //$all_prod_id_cond

    		$sql_date_result=sql_select($sql_date);
    		foreach( $sql_date_result as $row )
    		{
    			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
    			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
    		}
    		unset($sql_date_result);
    	}
    }

    $r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (102)");
	if($r_id3 && $r_id6)
	{
		oci_commit($con);
		disconnect($con);
	}

    $floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
    
	/*echo "<pre>";
	print_r($data_array);
	die;*/
	if($report_type == 2){
		$table_width = "6170 + 650";
		$col_span = "33";
		$summery_table_width = "1830";
	}else{
		$table_width = "5870 + 650";
		$col_span = "30";
	}
	ob_start();
	?>
	<style type="text/css">
		.word_break_wrap {
			word-break: break-all;
			word-wrap: break-word;
		}
		.grad1 {
			  background-image: linear-gradient(#e6e6e6, #b1b1cd, #e0e0eb);
			}
	</style>
	<fieldset style="width:<? echo $table_width+20;?>px;">
		<table cellpadding="0" cellspacing="0" width="2080">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:14px"><strong> 
					<? 
					if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;
					if($date_to!="") echo " To : ".change_date_format(str_replace("'","",$txt_date_to)) ;
					?>
					</strong></td>
			</tr>
		</table>

		<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">LC Company</th>
				<th width="100">Buyer</th>
				<th width="100">Buyer Client</th>
				<th width="100">Job</th>
				<th width="100">Style</th>
				<th width="100">Season</th>
				<th width="100">Booking No</th>
				<th width="100">Booking Date</th>
				<th width="100">Booking Type</th>
				<th width="100">Paymode</th>
				<th width="100">PI</th>
				<th width="100">LC/SC</th>
				<th width="100">Supplier</th>
				<th width="100">Int. Ref.</th>
				<th width="100">PO Number</th>
				<th width="100">Store Name</th>
				<? 
				if($report_type ==2)
				{ 
				?> 
					<th width="100">Floor</th>
					<th width="100">Room</th>
					<th width="100">Rack</th>
					<th width="100">Shelf</th>
					<th width="100">Box</th>
				<?
				}
				?>
				<th width="100">Batch No</th>
				<th width="100">Product ID</th>
				<th width="100">Body Part</th>
				<th width="120">F.Construction</th>
				<th width="120">F.Composition</th>
				<th width="100"><p>Fab.Dia</p></th>
				<th width="50">GSM</th>
				<th width="100">Dia Type</th>
				<th width="100">Color Type</th>
				<th width="100">F. Color</th>
				<th width="50">UOM</th>
				<th width="100">Opening Stock</th>
				<th width="50">Opening Roll</th>
				<th width="100">Receive Qty</th>
				<th width="50">Receive Roll</th>
				<th width="100"><p>Inside Issue Return</p></th>
				<th width="50">I.I.R. Roll</th>
				<th width="100"><p>Outside Issue Return</p></th>
				<th width="50">O.I.R. Roll</th>
				<th width="100">Trans In Qty</th>
				<th width="50">Trans in Roll</th>
				<th width="100">Total Rcv</th>
				<th width="50">Total rcv Roll</th>
				<th width="100">Rate ($)</th>
				<th width="100">Receive Amount($)</th>
				<th width="100"><p>Cutting Issue Inside</p></th>
				<th width="50">Cutting inside Roll</th>
				<th width="100"><p>Cutting Issue Outside</p></th>
				<th width="50">Cutting Outside Roll</th>
				<th width="100">Other Issue Qty</th>
				<th width="50">Other Issue Roll</th>
				<th width="100">Receive Rtn. Qnty</th>
				<th width="50">Rcv. Rtn. Roll</th>
				<th width="100">Trans Out Qty</th>
				<th width="50">Trans Out Roll</th>
				<th width="100">Total Issue</th>
				<th width="50">Total Issue Roll</th>
				<th width="100">Rate ($)</th>
				<th width="100">Issue Amount($)</th>
				<th width="100">Stock Qty</th>
				<th width="50">Stock Roll</th>
				<th width="100">Rate ($)</th>
			    <th width="100">Stock Rate (Tk)</th>
				<th width="100">Stock Amount($)</th>
				<th width="100">Stock Amount(TK)</th>
				<th width="100">Age (days)</th>
				<th width="100">DOH</th>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?
				// echo "<pre>";print_r($data_array);die;
				$i=1;

				$data_array_summery=array();

				foreach ($data_array as $uom => $uom_data)
				{
					$uom_total_booking_qty=$uom_total_opening_qnty=$uom_total_recv_qnty=$uom_total_inside_return=$uom_total_outside_return=$uom_total_trans_in_qty=$uom_total_tot_receive=$uom_total_total_issue=$uom_total_total_issue_amount=$uom_total_stock_qnty=$uom_total_stock_amount=$uom_total_cutting_inside_issue=$uom_total_cutting_outside_issue=$uom_total_other_issue=$uom_total_rcv_return_qnty=$uom_total_trans_out_qnty=0;$show_sub=false;
					foreach ($uom_data as $booking_no => $book_data)
					{
						foreach ($book_data as $prodStr => $row)
						{
						
							//echo $prodStr."<br>";
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$ref_qnty_arr = explode("__", $row);
							$recv_qnty=$trans_out_qty=$trans_in_qty=$opening_recv=$opening_trans=0;
							$recv_amount=$opening_recv_amount=$trans_in_amount=$opening_trans_amount=0;
							$dia_width_types="";$pi_no=""; $lc_sc_no="";

							$recv_roll=$trans_in_roll=$opening_recv_roll=$opening_trans_in_roll=0;
							foreach ($ref_qnty_arr as $ref_qnty_str)
							{
								$ref_qnty = explode("*", $ref_qnty_str);
								if($ref_qnty[6] == 1)
								{
									if($ref_qnty[7]==1){
										$recv_qnty += $ref_qnty[0];
										$recv_amount += $ref_qnty[0]*$ref_qnty[1];
										$recv_roll += $ref_qnty[8];
									}else{
										$opening_recv +=$ref_qnty[0];
										$opening_recv_amount +=$ref_qnty[0]*$ref_qnty[1];
										$opening_recv_roll += $ref_qnty[8];
									}
								}
								if($ref_qnty[6] == 5)
								{
									if($ref_qnty[7]==1){
										$trans_in_qty += $ref_qnty[0];
										$trans_in_amount += $ref_qnty[0]*$ref_qnty[1];
										$trans_in_roll += $ref_qnty[8];
									}else{
										$opening_trans +=$ref_qnty[0];
										$opening_trans_amount +=$ref_qnty[0]*$ref_qnty[1];
										$opening_trans_in_roll += $ref_qnty[8];
									}
								}
								$dia_width_types .=$ref_qnty[4].",";

								if($ref_qnty[2]==1)
								{
									$pi_no .= $ref_qnty[3].",";
								}

								$lc_sc_no .= $ref_qnty[5].",";
								//echo $recv_qnty."=";
							}

							$po_number 	= implode(",",array_unique(explode(",",chop($po_array[$booking_no][$prodStr]["po_no"],","))));
							$grouping 	= implode(",",array_unique(explode(",",chop($po_array[$booking_no][$prodStr]["grouping"],","))));
							$pi_no 	= implode(",",array_unique(explode(",",chop($pi_no,","))));
							$lc_sc_no 	= implode(",",array_unique(explode(",",chop($lc_sc_no,","))));
							$prodStr 	= explode("*", $prodStr);
							
							$company_name 	= $book_po_ref[$booking_no]["company_name"];
							$buyer_name 	= $book_po_ref[$booking_no]["buyer_name"];
							$supplier 		= $book_po_ref[$booking_no]["supplier"];
							$job_arr 		= array_unique(explode(",",chop($book_po_ref[$booking_no]["job_no"],",")));
							$job_nos = implode(",", $job_arr);

							$client_arr = array_unique(explode(",",chop($book_po_ref[$booking_no]["client_id"],",")));
							$client_nos="";
							foreach ($client_arr as $client_id)
							{
								$client_nos .= $buyer_arr[$client_id].",";
							}

							$season = array_unique(explode(",",chop($book_po_ref[$booking_no]["season"],",")));
							$season_nos="";
							foreach ($season as $s_id)
							{
								$season_nos .= $season_arr[$s_id].",";
							}

							$style_ref_no = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["style_ref_no"],","))));;
							$pay_mode_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["pay_mode"],","))));

							$booking_date = $book_po_ref[$booking_no]["booking_date"];
							$booking_type = $book_po_ref[$booking_no]["booking_type"];

							//$dia_width_type_arr = array_filter(array_unique(explode(",",chop($dia_width_types,","))));
							$dia_width_type_arr = array_unique(explode(",",chop($dia_width_types,",")));

							$dia_width_type="";
							foreach ($dia_width_type_arr as $width_type)
							{
								$dia_width_type .= $fabric_typee[$width_type].",";
							}
							$dia_width_type = chop($dia_width_type,",");

							$booking_qnty 	= $book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["qnty"];
							$booking_amount = $book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["amount"];
							if($booking_qnty >0){
								$booking_rate 	= $booking_amount/$booking_qnty;
							}else{
								$booking_rate=0;
							}

							$color_type_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["color_type"],","))));

							//$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

							if($report_type ==2)
							{
								$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9]."*".$prodStr[10]."*".$prodStr[11]."*".$prodStr[12]."*".$prodStr[13]."*".$prodStr[14];
							}
							else
							{
								$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9];
							}
							
							$inside_return 			= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return"];
							$inside_return_amount 	= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return_amount"];
							$outside_return 		= $issue_return_data[$booking_no][$issRtnRef_str]["outside_return"];
							$outside_return_amount  = $issue_return_data[$booking_no][$issRtnRef_str]["outside_return_amount"];
							$opening_iss_return 	= $issue_return_data[$booking_no][$issRtnRef_str]["opening"];
							$opening_iss_return_amount = $issue_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$tot_receive 			= $recv_qnty + $trans_in_qty + $inside_return + $outside_return;
							$tot_receive_amount 	= $recv_amount + $trans_in_amount + $inside_return_amount + $outside_return_amount;

							$tot_receive_rate=0;
							if($tot_receive>0)
							{
								$tot_receive_rate 	= $tot_receive_amount/$tot_receive;
							}
							$booking_balance_qnty 	= $booking_qnty- $tot_receive;
							$booking_balance_amount = $booking_balance_qnty*$booking_rate;

							$cutting_inside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_inside"];
							$cutting_outside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_outside"];
							$other_issue 			= $issue_data[$booking_no][$issRtnRef_str]["other_issue"];
							$issue_amount 			= $issue_data[$booking_no][$issRtnRef_str]["issue_amount"];
							$opening_issue 			= $issue_data[$booking_no][$issRtnRef_str]["opening_issue"];
							$opening_issue_amount 	= $issue_data[$booking_no][$issRtnRef_str]["opening_issue_amount"];

							$rcv_return_opening_qnty = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$rcv_return_opening_amount = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_amount"];
							$rcv_return_qnty  		= $rcv_return_data[$booking_no][$issRtnRef_str]["qnty"];
							$rcv_return_amount  	= $rcv_return_data[$booking_no][$issRtnRef_str]["amount"];

							$trans_out_amount  		= $trans_out_data[$booking_no][$issRtnRef_str]["amount"];
							$trans_out_qnty  		= $trans_out_data[$booking_no][$issRtnRef_str]["qnty"];
							$trans_out_opening_qnty = $trans_out_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$trans_out_opening_amount = $trans_out_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$total_issue  			= $cutting_inside + $cutting_outside + $other_issue + $rcv_return_qnty + $trans_out_qnty;
							/*$total_issue_amount 	= $issue_amount + $rcv_return_amount + $trans_out_amount;
							//echo $issue_amount.' + '.$rcv_return_amount.' + '.$trans_out_amount;
							$tot_issue_rate=0;
							if($total_issue>0)
							{
								$tot_issue_rate 	= $total_issue_amount/$total_issue;
							}*/

							$cutting_inside_po_ids		= implode(",",array_unique(explode(",",chop($issue_data[$booking_no][$issRtnRef_str]["cutting_inside_po"],','))));
							$cutting_outside_po_ids 	= implode(",",array_unique(explode(",",chop($issue_data[$booking_no][$issRtnRef_str]["cutting_outside_po"],','))));
							$other_issue_po_ids 	= implode(",",array_unique(explode(",",chop($issue_data[$booking_no][$issRtnRef_str]["other_issue_po"],','))));

							$inside_return_po_ids 	= implode(",",array_unique(explode(",",chop($issue_return_data[$booking_no][$issRtnRef_str]["inside_return_po"],','))));
							$outside_return_po_ids 	= implode(",",array_unique(explode(",",chop($issue_return_data[$booking_no][$issRtnRef_str]["outside_return_po"],','))));

							$outside_return_po_ids 	= implode(",",array_unique(explode(",",chop($issue_return_data[$booking_no][$issRtnRef_str]["outside_return_po"],','))));
							$receive_ret_po_ids  	= implode(",",array_unique(explode(",",chop($rcv_return_data[$booking_no][$issRtnRef_str]["receive_ret_po"],','))));
							
							$trans_out_po_ids  	= implode(",",array_unique(explode(",",chop($trans_out_data[$booking_no][$issRtnRef_str]["trans_out_po"],','))));
						
							//roll code is here 
							$inside_return_roll 		= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_in"];
							$outside_return_roll 		= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_out"];
							$opening_iss_return_roll 	= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];
							$cutting_inside_roll		= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_in"];
							$cutting_outside_roll 		= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_out"];
							$other_issue_roll			= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_other"];
							$opening_issue_roll 		= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];
							$rcv_return_opening_roll 	= $rcv_return_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];
							$rcv_return_roll 			= $rcv_return_data[$booking_no][$issRtnRef_str]["no_of_roll"];
							$trans_out_roll  			= $trans_out_data[$booking_no][$issRtnRef_str]["no_of_roll"];
							$trans_out_opening_roll 	= $trans_out_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];

							$opening_roll 	= ($opening_recv_roll + $opening_trans_in_roll + $opening_iss_return_roll) - ($opening_issue_roll + $rcv_return_opening_roll +$trans_out_opening_roll);

							$tot_receive_roll 			= $recv_roll + $trans_in_roll + $inside_return_roll + $outside_return_roll;
							$total_issue_roll  			= $cutting_inside_roll + $cutting_outside_roll + $other_issue_roll + $rcv_return_roll + $trans_out_roll;
							$stock_roll					= $opening_roll + ($tot_receive_roll - $total_issue_roll);



							$opening_title 	= "Receive:".$opening_recv ." + Transfer In:". $opening_trans ." + Issue Return:" . $opening_iss_return . "\n";
							$opening_title 	.= "Issue:".$opening_issue ." + Transfer Out:". $trans_out_opening_qnty ." + Receive Return:" . $rcv_return_opening_qnty;
							$opening_qnty 	= ($opening_recv + $opening_trans + $opening_iss_return) - ($opening_issue + $rcv_return_opening_qnty +$trans_out_opening_qnty);

							$stock_qnty 	= $opening_qnty + ($tot_receive - $total_issue);
							$stock_title 	= "Opening:".$opening_qnty ." + (Receive:". $tot_receive ."- Issue:". $total_issue.")";

							$booking_and_product_wise_quantity = $rate_arr_booking_and_product_wise[$booking_no][$prodStr[0]][$prodStr[1]]["quantity"];
							$booking_and_product_wise_amount = $rate_arr_booking_and_product_wise[$booking_no][$prodStr[0]][$prodStr[1]]["amount"];
							$booking_and_product_wise_taka = $rate_arr_booking_and_product_wise[$booking_no][$prodStr[0]][$prodStr[1]]["cons_quantity"];

							// echo $booking_and_product_wise_quantity;

							$booking_and_product_wise_rate = $booking_and_product_wise_amount/$booking_and_product_wise_quantity;

							$tot_receive_rate =$booking_and_product_wise_rate;


							$opening_amount = ($opening_recv_amount+$opening_trans_amount) -($opening_issue_amount + $rcv_return_opening_amount);

							if($opening_qnty>0)
							{
								//$opening_rate = $opening_amount/$opening_qnty;

								//$opening_rate = ($opening_recv_amount+$opening_trans_amount) / ($opening_recv + $opening_trans + $opening_iss_return);
							}

							if($tot_receive_rate ==0)
							{
								$tot_receive_rate =$opening_rate;
							}

							$tot_issue_rate = $tot_receive_rate;
							$total_issue_amount = $total_issue * $tot_issue_rate;

							if(number_format($stock_qnty,2,".","") == "-0.00")
							{
								$stock_qnty=0;
							}

							$stock_rate = $tot_receive_rate;
							$stock_amount = $stock_qnty * $stock_rate;
							$stock_amount_tk=$stock_qnty*$booking_and_product_wise_taka;

							$daysOnHand = datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['max_date'],'','',1),date("Y-m-d"));
							$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['min_date'],'','',1),date("Y-m-d"));

							//echo $recv_qnty."<br>";
							if(($consump_per_dzn/12) > 0)
							{
								$possible_cut_piece = $stock_qnty/($consump_per_dzn/12);
							}

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qnty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qnty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qnty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qnty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qnty == $txt_qnty) || $get_upto_qnty == 0))
							{
								if($stock_qnty!=0 && $cbo_value_with==2)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i;?></td>
										<td width="100"><? echo $company_arr[$company_name]?></td>
										<td width="100"><? echo $buyer_arr[$buyer_name];?></td>
										<td width="100">
											<? echo chop($client_nos,",");?>
										</td>
										<td width="100"><p class="word_break_wrap"><? echo $job_nos;?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $style_ref_no;?></p></td>
										<td width="100" class="word_break_wrap"><? echo chop($season_nos,",");?></td>
										<td width="100" class="word_break_wrap"><? echo $booking_no;?></td>
										<td width="100" class="word_break_wrap"><? echo $booking_date;?></td>
										<td width="100" class="word_break_wrap"><? echo $booking_type;?></td>
										<td width="100" class="word_break_wrap"><? echo $pay_mode_nos;?></td>
										<td width="100" class="word_break_wrap"><? echo $pi_no;?></td>
										<td width="100"><p class="word_break_wrap"><? echo $lc_sc_no;?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $supplier;?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $grouping;?></p></td>
										<td width="100" title="<? //echo $po_breakdown_id;?>"><a href="##" onClick="open_po_number('<? echo $po_number;?>','<? echo $prodStr;?>');">view</a></td>
										<td width="100" ><p class="word_break_wrap"><? echo $store_arr[$prodStr[1]];?></p></td>
										<? 
										if($report_type ==2)
										{
											?>
											<td width="100" title="floor"><? echo $floor_room_rack_arr[$prodStr[10]];?></td>
											<td width="100" title="room"><? echo $floor_room_rack_arr[$prodStr[11]];?></td>
											<td width="100" title="rack"><? echo $floor_room_rack_arr[$prodStr[12]];?></td>
											<td width="100" title="shelf"><? echo $floor_room_rack_arr[$prodStr[13]];?></td>
											<td width="100" title="Bin-Box"><? echo $floor_room_rack_arr[$prodStr[14]];?></td>
											<?
										}
										?>
										<td width="100" class="word_break_wrap" title="batch<? echo $prodStr[8];?>"><? echo $prodStr[9];?></td>
										<td width="100" class="word_break_wrap"><? echo $prodStr[0];?></td>
										<td width="100" title="<? echo $prodStr[2];?>"><p class="word_break_wrap"><? echo $body_part[$prodStr[2]]?></p></td>
										<td width="120"><p class="word_break_wrap"><? echo $constructionArr[$prodStr[3]];?></p></td>
										<td width="120"><p class="word_break_wrap"><? echo $composition_arr[$prodStr[3]];?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $prodStr[5]; ?></p></td>
										<td width="50" class="word_break_wrap"><? echo $prodStr[4]; ?></td>
										<td width="100" class="word_break_wrap"><? echo $dia_width_type;?></td>
										<td width="100" class="word_break_wrap" title="<? //echo 'ref='.$booking_no.','.$prodStr[2].','.$prodStr[3].','.$prodStr[6];?>">
											<? echo $color_type_nos;?>
										</td>
										<td width="100"><p class="word_break_wrap"><? echo $color_arr[$prodStr[6]];?></p></td>
										<td width="50"><? echo $unit_of_measurement[$prodStr[7]]; ?></td>
										
										<td width="100" class="word_break_wrap" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening_qnty,2,".","");?></td>

										<td width="50" class="word_break_wrap" align="right"><? echo $opening_roll;?></td>

										<td width="100" align="right" class="word_break_wrap">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_receive','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $po_number;?>');"><? echo number_format($recv_qnty,2,".","");?>
											</a>
										</td>
										<td width="50" align="right" class="word_break_wrap"><? echo $recv_roll;?></td>

										<td width="100" align="right" class="word_break_wrap">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_issue_rtn_in','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $inside_return_po_ids;?>');"><? echo number_format($inside_return,2,".","");?>
											</a>
										</td>
										<td width="50" align="right" class="word_break_wrap"><? echo $inside_return_roll;?></td>
										<td width="100" align="right" class="word_break_wrap">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_issue_rtn_out','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $outside_return_po_ids;?>');"><? echo number_format($outside_return,2,".","");?>
											</a>
										</td>
										<td width="50" align="right" class="word_break_wrap"><? echo $outside_return_roll;?></td>
										<td width="100" align="right" class="word_break_wrap">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_in','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $po_number;?>');"><? echo number_format($trans_in_qty,2,".","");?>
											</a>
										</td>
										<td width="50" align="right" class="word_break_wrap"><? echo $trans_in_roll;?></td>
										<td width="100" align="right" class="word_break_wrap"><? echo number_format($tot_receive,2,".","");?></td>
										<td width="50" align="right" class="word_break_wrap"><? echo $tot_receive_roll;?></td>
										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($tot_receive_rate,2,".","");?></p></td>
										<td width="100" align="right" class="word_break_wrap"><? echo number_format($tot_receive_amount,2,".","");?></td>
										<td width="100" align="right" class="word_break_wrap"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_inside','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $cutting_inside_po_ids;?>');"><? echo number_format($cutting_inside,2,".","");?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $cutting_inside_roll;?></td>

										<td width="100" align="right" class="word_break_wrap"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_outside','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $cutting_outside_po_ids;?>');"><? echo number_format($cutting_outside,2,".",""); ?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $cutting_outside_roll;?></td>

										<td width="100" align="right" class="word_break_wrap"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_other_issue','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $other_issue_po_ids;?>');"><? echo number_format($other_issue,2,".",""); ?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $other_issue_roll;?></td>

										<td width="100" align="right" class="word_break_wrap"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_recv_rtn','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $receive_ret_po_ids;?>');"><? echo number_format($rcv_return_qnty,2,".",""); ?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $rcv_return_roll;?></td>


										<td width="100" align="right" class="word_break_wrap"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_out','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $trans_out_po_ids;?>');"><? echo number_format($trans_out_qnty,2,".","");?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $trans_out_roll;?></td>

										<td width="100" align="right" class="word_break_wrap"><? echo number_format($total_issue,2,".","");?></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $total_issue_roll;?></td>

										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($tot_issue_rate,2,".","");?></p></td>
										<td width="100" align="right" class="word_break_wrap"><? echo number_format($total_issue_amount,2,".","");?></td>
										<td width="100" align="right" class="word_break_wrap" title="<? //echo $stock_title;?>"><? echo number_format($stock_qnty,2,".","");?></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $stock_roll;?></td>

										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($stock_rate,2,".","");?></p></td>
										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($booking_and_product_wise_taka,2,".","");?></p></td>
										<td width="100" align="right" class="word_break_wrap"><? echo number_format($stock_amount,2,".","");?></td>
										<td width="100" align="right" class="word_break_wrap"><? echo number_format($stock_amount_tk,2,".","");?></td>
										<td width="100" align="center" class="word_break_wrap"><? echo $ageOfDays;?></td>
										<td width="100" align="center" class="word_break_wrap"><? echo $daysOnHand ?></td>
									</tr>
									<?
									$i++;
									$uom_total_booking_qty+=$booking_qnty;
									$uom_total_opening_qnty+=$opening_qnty;
									$uom_total_recv_qnty+=$recv_qnty;
									$uom_total_inside_return+=$inside_return;
									$uom_total_outside_return+=$outside_return;
									$uom_total_trans_in_qty+=$trans_in_qty;
									$uom_total_tot_receive+=$tot_receive;
									$uom_total_cutting_inside_issue+=$cutting_inside;
									$uom_total_cutting_outside_issue+=$cutting_outside;
									$uom_total_other_issue+=$other_issue;
									$uom_total_rcv_return_qnty+=$rcv_return_qnty;
									$uom_total_trans_out_qnty+=$trans_out_qnty;
									$uom_total_total_issue+=$total_issue;
									$uom_total_total_issue_amount+=$total_issue_amount;
									$uom_total_stock_qnty+=$stock_qnty;
									$uom_total_stock_amount+=$stock_amount;
									$uom_total_stock_amount_tk+=$stock_amount_tk;

									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['opening_qnty']+=$opening_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['recv_qnty']+=$recv_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['inside_return']+=$inside_return;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['outside_return']+=$outside_return;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['trans_in_qty']+=$trans_in_qty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['tot_receive']+=$tot_receive;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['tot_receive_amount']+=$tot_receive_amount;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['cutting_inside']+=$cutting_inside;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['cutting_outside']+=$cutting_outside;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['other_issue']+=$other_issue;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['rcv_return_qnty']+=$rcv_return_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['trans_out_qnty']+=$trans_out_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['total_issue']+=$total_issue;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['stock_qnty']+=$stock_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['stock_amount']+=$stock_amount;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['stock_amount_tk']+=$stock_amount_tk;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['consid']=$prodStr[3];
									$show_sub=true;
								}
								//else if($stock_qnty>=0 && $cbo_value_with==1)
								else if( $cbo_value_with==1)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i;?></td>
										<td width="100"><? echo $company_arr[$company_name]?></td>
										<td width="100"><? echo $buyer_arr[$buyer_name];?></td>
										<td width="100">
											<? echo chop($client_nos,",");?>
										</td>
										<td width="100"><p class="word_break_wrap"><? echo $job_nos;?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $style_ref_no;?></p></td>
										<td width="100"><? echo chop($season_nos,",");?></td>
										<td width="100"><? echo $booking_no;?></td>
										<td width="100"><? echo $booking_date;?></td>
										<td width="100"><? echo $booking_type;?></td>
										<td width="100"><? echo $pay_mode_nos;?></td>
										<td width="100" ><p class="word_break_wrap"><? echo $pi_no;?></p></td>
										<td width="100" ><p class="word_break_wrap"><? echo $lc_sc_no;?></p></td>
										<td width="100" ><p class="word_break_wrap"><? echo $supplier;?></p></td>
										<td width="100" ><p class="word_break_wrap"><? echo $grouping;?></p></td>
										<td width="100" title="<? //echo $po_breakdown_id;?>"><a href="##" onClick="open_po_number('<? echo $po_number;?>','<? echo $prodStr;?>');">view</a></td>
										<td width="100" title="store"><p class="word_break_wrap"><? echo $store_arr[$prodStr[1]];?></p></td>
										<?
										if($report_type == 2)
										{
											?>
											<td width="100" title="floor"><? echo $floor_room_rack_arr[$prodStr[10]];?></td>
											<td width="100" title="room"><? echo $floor_room_rack_arr[$prodStr[11]];?></td>
											<td width="100" title="rack"><? echo $floor_room_rack_arr[$prodStr[12]];?></td>
											<td width="100" title="shelf"><? echo $floor_room_rack_arr[$prodStr[13]];?></td>
											<td width="100" title="Bin-Box"><? echo $floor_room_rack_arr[$prodStr[14]];?></td>
											<?
										}
										?>
										<td width="100" title="batch"><? echo $prodStr[9];?></td>
										<td width="100"><? echo $prodStr[0];?></td>
										<td width="100" title="<? echo $prodStr[2];?>"><p class="word_break_wrap"><? echo $body_part[$prodStr[2]]?></p></td>
										<td width="120"><p class="word_break_wrap"><? echo $constructionArr[$prodStr[3]];?></p></td>
										<td width="120"><p class="word_break_wrap"><? echo $composition_arr[$prodStr[3]];?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $prodStr[5]; ?></p></td>
										<td width="50"><? echo $prodStr[4]; ?></td>
										<td width="100"><? echo $dia_width_type;?></td>
										<td width="100"><? echo $color_type_nos;?></td>
										<td width="100"><p class="word_break_wrap"><? echo $color_arr[$prodStr[6]];?></p></td>
										<td width="50"><? echo $unit_of_measurement[$prodStr[7]]; ?></td>
										<td width="100" align="right"><? echo number_format($opening_qnty,2,".","");?></td>
										<td width="50" align="right" class="word_break_wrap"><? echo $opening_roll;?></td>
										<td width="100" align="right">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_receive','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $po_number;?>');"><? echo number_format($recv_qnty,2,".","");?>
											</a>
										</td>
										<td width="50" align="right" class="word_break_wrap"><? echo $recv_roll;?></td>
										<td width="100" align="right">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_issue_rtn_in','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $inside_return_po_ids;?>');"><? echo number_format($inside_return,2,".","");?>
											</a>
										</td>
										<td width="50" align="right" class="word_break_wrap"><? echo $inside_return_roll;?></td>

										<td width="100" align="right">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_issue_rtn_out','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $outside_return_po_ids;?>');"><? echo number_format($outside_return,2,".","");?>
											</a>
										</td>

										<td width="50" align="right" class="word_break_wrap"><? echo $outside_return_roll;?></td>

										<td width="100" align="right">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_in','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($trans_in_qty,2,".","");?>
											</a>
										</td>

										<td width="50" align="right" class="word_break_wrap"><? echo $trans_in_roll;?></td>

										<td width="100" align="right"><? echo number_format($tot_receive,2,".","")?></td>
										<td width="50" align="right" class="word_break_wrap"><? echo $tot_receive_roll;?></td>
										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($tot_receive_rate,2,".","");?></p></td>
										<td width="100" align="right"><? echo number_format($tot_receive_amount,2,".","");?></td>
										<td width="100" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_inside','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $cutting_inside_po_ids;?>');"><? echo number_format($cutting_inside,2,".","");?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $cutting_inside_roll;?></td>

										<td width="100" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_outside','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $cutting_outside_po_ids;?>');"><? echo number_format($cutting_outside,2,".",""); ?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $cutting_outside_roll;?></td>

										<td width="100" align="right" class="word_break_wrap"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_other_issue','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $other_issue_po_ids;?>');"><? echo number_format($other_issue,2,".",""); ?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $other_issue_roll;?></td>

										<td width="100" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_recv_rtn','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $receive_ret_po_ids;?>');"><? echo number_format($rcv_return_qnty,2,".",""); ?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $rcv_return_roll;?></td>

										<td width="100" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_out','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $trans_out_po_ids;?>');"><? echo number_format($trans_out_qnty,2,".","");?></a></td>

										<td width="50" align="right" class="word_break_wrap"><? echo $trans_out_roll;?></td>

										<td width="100" align="right"><? echo number_format($total_issue,2,".","");?></td>
										
										<td width="50" align="right" class="word_break_wrap"><? echo $total_issue_roll;?></td>
										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($tot_issue_rate,2,".","");?></p></td>
										<td width="100" align="right"><? echo number_format($total_issue_amount,2,".","");?></td>
										<td width="100" align="right" title="<? //echo $stock_title;?>"><? echo number_format($stock_qnty,2,".","");?></td>
									
										<td width="50" align="right" class="word_break_wrap"><? echo $stock_roll;?></td>
										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($stock_rate,2,".","");?></p></td>
										<td width="100" align="right"><p class="word_break_wrap"><? echo number_format($booking_and_product_wise_taka,2,".","");?></p></td>
										<td width="100" align="right"><? echo number_format($stock_amount,2,".","");?></td>
										<td width="100" align="right" class="word_break_wrap"><? echo number_format($stock_amount_tk,2,".","");?></td>
										<td width="100" align="center"><? echo $ageOfDays;?></td>
										<td width="100" align="center"><? echo $daysOnHand ?></td>
									</tr>
									<?
									$i++;
									$uom_total_booking_qty+=$booking_qnty;
									$uom_total_opening_qnty+=$opening_qnty;
									$uom_total_recv_qnty+=$recv_qnty;
									$uom_total_inside_return+=$inside_return;
									$uom_total_outside_return+=$outside_return;
									$uom_total_trans_in_qty+=$trans_in_qty;
									$uom_total_tot_receive+=$tot_receive;
									$uom_total_cutting_inside_issue+=$cutting_inside;
									$uom_total_cutting_outside_issue+=$cutting_outside;
									$uom_total_other_issue+=$other_issue;
									$uom_total_rcv_return_qnty+=$rcv_return_qnty;
									$uom_total_trans_out_qnty+=$trans_out_qnty;
									$uom_total_total_issue+=$total_issue;
									$uom_total_total_issue_amount+=$total_issue_amount;
									$uom_total_stock_qnty+=$stock_qnty;
									$uom_total_stock_amount+=$stock_amount;
									$uom_total_stock_amount_tk+=$stock_amount_tk;

									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['opening_qnty']+=$opening_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['recv_qnty']+=$recv_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['inside_return']+=$inside_return;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['outside_return']+=$outside_return;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['trans_in_qty']+=$trans_in_qty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['tot_receive']+=$tot_receive;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['tot_receive_amount']+=$tot_receive_amount;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['cutting_inside']+=$cutting_inside;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['cutting_outside']+=$cutting_outside;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['other_issue']+=$other_issue;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['rcv_return_qnty']+=$rcv_return_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['trans_out_qnty']+=$trans_out_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['total_issue']+=$total_issue;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['stock_qnty']+=$stock_qnty;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['stock_amount']+=$stock_amount;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['stock_amount_tk']+=$stock_amount_tk;
									$data_array_summery[$uom][$constructionArr[$prodStr[3]]]['consid']=$prodStr[3];
									$show_sub=true;
								}
							}
						}
					}
					
					if($show_sub==true)
					{
						?>
						<tr class="grad1">
							<td colspan="<? echo $col_span;?>" align="right"><strong>UOM Wise Total : </strong></td>
							<td width="100" align="right" id="value_sub_total_opening_stock">&nbsp;<strong><? echo number_format($uom_total_opening_qnty,2,".",""); ?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_rcv_qnty">&nbsp;<strong><? echo number_format($uom_total_recv_qnty,2,".",""); ?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_inside_iss_return">&nbsp;<strong><? echo number_format($uom_total_inside_return,2,".",""); ?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_out_iss_return">&nbsp;<strong><? echo number_format($uom_total_outside_return,2,".",""); ?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_trans_in">&nbsp;<strong><? echo number_format($uom_total_trans_in_qty,2,".",""); ?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_rcv">&nbsp;<strong><? echo number_format($uom_total_tot_receive,2,".",""); ?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_cutting_inside">&nbsp;<strong><? echo number_format($uom_total_cutting_inside_issue,2,".","");?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_cutting_outside">&nbsp;<strong><? echo number_format($uom_total_cutting_outside_issue,2,".","");?></strong></td>
							<td width="50">&nbsp;</td>

							<td width="100" align="right">&nbsp;<strong><? echo number_format($uom_total_other_issue,2,".","");?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right">&nbsp;<strong><? echo number_format($uom_total_rcv_return_qnty,2,".","");?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right">&nbsp;<strong><? echo number_format($uom_total_trans_out_qnty,2,".","");?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_issue">&nbsp;<strong><? echo number_format($uom_total_total_issue,2,".",""); ?></td>
							<td width="50">&nbsp;</td>
							<td width="100">&nbsp;</strong></td>
							<td width="100" align="right" id="value_sub_total_issue_amount">&nbsp;<strong><? echo number_format($uom_total_total_issue_amount,2,".",""); ?></strong></td>
							<td width="100" align="right" id="value_sub_total_stock_qnty">&nbsp;<strong><? echo number_format($uom_total_stock_qnty,2,".",""); ?></strong></td>
							<td width="50">&nbsp;</td>
							<td width="100" align="right">&nbsp;</td>
							<td width="100" align="right">&nbsp;</td>
							<td width="100" align="right" id="value_sub_total_stock_amount">&nbsp;<strong><? echo number_format($uom_total_stock_amount,2,".",""); ?></strong></td>
							<td width="100" align="right" id="value_sub_total_stock_amount_tk">&nbsp;<strong><? echo number_format($uom_total_stock_amount_tk,2,".",""); ?></strong></td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
						</tr>
						<?
					}
				}
				?>
			</table>
		</div>
		<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<?
				if($report_type == 2)
				{
					?>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<?
				}
				?>

				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_opening_stock">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_rcv_qnty">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_inside_iss_return">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_out_iss_return">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_trans_in">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_total_rcv">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>

				<th width="100" id="value_total_cutting_inside">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_total_cutting_outside">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_total_other_issue">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_total_rcv_return">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100" id="value_total_transfer_out">&nbsp;</th>
				<th width="50">&nbsp;</th>

				<th width="100" id="value_total_issue">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100" id="value_issue_amount">&nbsp;</th>
				<th width="100" id="value_stock_qnty">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100" id="value_stock_amount">&nbsp;</th>
				<th width="100" id="value_stock_amount_tk">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
			</tfoot>
		</table>

		<br>
		<table cellpadding="0" cellspacing="0" width="2080">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="7" style="font-size:18px"><strong>Finish Fabric Summary Stock Report</strong></td>
			</tr>
		</table>

		<table width="<? echo $summery_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">F.Construction</th>
				<th width="100">UOM</th>
				<th width="100">Opening Stock</th>
				<th width="100">Receive Qnty</th>
				<th width="100">Inside Issue Return</th>
				<th width="100">Outside Issue Return</th>
				<th width="100">Trans In Qty</th>
				<th width="100">Total Rcv</th>
				<th width="100">Receive Amount</th>
				<th width="100">Cutting Issue Inside</th>
				<th width="100">Cutting Issue Outside</th>
				<th width="100">Other Issue Qty</th>
				<th width="100">Receive Rtn. Qnty</th>
				<th width="100">Trans Out Qty</th>
				<th width="100">Total Issue</th>
				<th width="100">Closing Stock</th>
				<th width="100">Closing Stock Amount ($)</th>
				<th width="100">Closing Stock Amount (Tk)</th>
				
			</thead>
		</table>
		<div style="width:<? echo $summery_table_width+20;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="<? echo $summery_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?

				$i=1;
				$total_booking_qty=$total_opening_qnty=$total_recv_qnty=$total_inside_return=$total_outside_return=$total_trans_in_qty=$total_tot_receive=$total_total_issue=$total_total_issue_amount=$total_stock_qnty=$total_stock_amount=$total_cutting_inside_issue=$total_cutting_outside_issue=$total_other_issue=$total_rcv_return_qnty=$total_trans_out_qnty=0;
			
				
				foreach ($data_array_summery as $uom => $uom_data)
				{
					foreach ($uom_data as $fcons=> $row)
					{
							
						//echo "<pre>"; print_r($cons)."<br>";
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="30"><? echo $i;?></td>
							<td width="100" align="center" class="word_break_wrap" title="<? echo $row['consid'];?>"><? echo $fcons;?></td>
							<td width="100" align="center" class="word_break_wrap"><? echo $unit_of_measurement[$uom]; ?></td>
							<td width="100" align="right" class="word_break_wrap" ><? echo number_format($row['opening_qnty'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['recv_qnty'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['inside_return'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['outside_return'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['trans_in_qty'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['tot_receive'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['tot_receive_amount'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['cutting_inside'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['cutting_outside'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['other_issue'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['rcv_return_qnty'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['trans_out_qnty'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['total_issue'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['stock_qnty'],2,".","");?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['stock_amount'],2);?></td>
							<td width="100" align="right" class="word_break_wrap"><? echo number_format($row['stock_amount_tk'],2);?></td>
							
						</tr>
						<?
						$i++;
						$total_opening_qnty+=$row['opening_qnty'];
						$total_recv_qnty+=$row['recv_qnty'];
						$total_inside_return+=$row['inside_return'];
						$total_outside_return+=$row['outside_return'];
						$total_trans_in_qty+=$row['trans_in_qty'];
						$total_tot_receive+=$row['tot_receive'];
						$total_tot_receive_amount+=$row['tot_receive_amount'];
						$total_cutting_inside_issue+=$row['cutting_inside'];
						$total_cutting_outside_issue+=$row['cutting_outside'];
						$total_other_issue+=$row['other_issue'];
						$total_rcv_return_qnty+=$row['rcv_return_qnty'];
						$total_trans_out_qnty+=$row['trans_out_qnty'];
						$total_total_issue+=$row['total_issue'];
						$total_stock_qnty+=$row['stock_qnty'];
						$total_stock_amount+=$row['stock_amount'];
						$total_stock_amount_tk+=$row['stock_amount_tk'];
					}
						
				}
				?>
			</table>
		</div>
		<table width="<? echo $summery_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100"><b>Total:</b></th>
				<th width="100" align="right"><? echo number_format($total_opening_qnty,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_recv_qnty,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_inside_return,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_outside_return,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_trans_in_qty,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_tot_receive,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_tot_receive_amount,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_cutting_inside_issue,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_cutting_outside_issue,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_other_issue,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_rcv_return_qnty,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_trans_out_qnty,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_total_issue,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_stock_qnty,2,".","");?></th>
				<th width="100" align="right"><? echo number_format($total_stock_amount);?></th>
				<th width="100" align="right"><? echo number_format($total_stock_amount_tk);?></th>
			</tfoot>
		</table>
		

	</fieldset>
	<?
	//echo "Execution Time: " . (microtime(true) - $started) . "S";
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$report_type";

	exit();
}

if($action=="open_po_number")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>

	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="6">PO Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Job No</th>
						<th width="150">Style Ref no.</th>
						<th width="150">PO Number</th>
					</tr>
				</thead>
				<tbody>
					<?
					$dtlsArray = sql_select("select a.po_number, a.job_no_mst, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_no_mst = b.job_no and a.id in ($po_id)");
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
							<td width="150" ><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="150"><p><? echo $row[csf('po_number')]; ?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_receive")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="75">Receive Date</th>
						<th width="100">Receive ID</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="80">Booking No</th>
						<th width="80">Batch Date</th>
						<th width="80">Receive Qty.</th>
						<th width="80">Roll Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];
					//$from_date

					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
					if($width!='') $width_cond=" and c.width='$width'"; else $width_cond="";
					if($prod_ref[10])
					{
						$room_rack_cond = " and b.floor_id='$floor_id' and b.room='$room' and b.rack='$rack' and b.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and b.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$rcv_sql = sql_select("SELECT a.recv_number, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  b.transaction_date as receive_date, b.prod_id, sum(c.no_of_roll) as no_of_roll, sum(f.quantity) as quantity from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst e, order_wise_pro_details f WHERE a.company_id in ($companyID) and a.id = b.mst_id and b.id = c.trans_id and b.id=f.trans_id and f.trans_type=1 and f.entry_form=37 and b.transaction_type =1 and a.entry_form = 37 and a.status_active =1 and b.status_active =1 and c.status_active =1  and b.pi_wo_batch_no = e.id and f.po_breakdown_id in ($po_ids) and e.booking_no = '$booking_no' and b.prod_id='$prod_id' and b.store_id= '$store_id' and c.body_part_id= '$body_part_id' and c.gsm = '$gsm' $width_cond and b.cons_uom = '$cons_uom' $room_rack_cond and e.id = $batch_id $date_cond group by a.recv_number, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  b.transaction_date, b.prod_id"); //and c.width='$width'
					}
					else
					{
						$rcv_sql = sql_select("SELECT a.recv_number, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  b.transaction_date as receive_date, b.prod_id, sum(c.no_of_roll) as no_of_roll, sum(b.cons_quantity) as quantity from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst e  WHERE a.company_id in ($companyID) and a.id = b.mst_id and b.id = c.trans_id  and b.transaction_type =1 and a.entry_form = 37 and a.status_active =1 and b.status_active =1 and c.status_active =1  and b.pi_wo_batch_no = e.id and e.booking_no = '$booking_no' and b.prod_id='$prod_id' and b.store_id= '$store_id' and c.body_part_id= '$body_part_id' and c.gsm = '$gsm' $width_cond and b.cons_uom = '$cons_uom' $room_rack_cond and e.id = $batch_id $date_cond group by a.recv_number, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  b.transaction_date, b.prod_id"); //and c.width='$width'
					}
					
					//echo $mrr_sql;

					foreach($rcv_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('no_of_roll')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_no_of_roll+=$row[csf('no_of_roll')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? echo number_format($tot_no_of_roll,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_issue_rtn_in")
{
	echo load_html_head_contents("Return Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Issue Return Inside</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="75">Return Date</th>
						<th width="100">Return ID</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="80">Booking No</th>
						<th width="80">Batch Date</th>
						<th width="80">Return Qty.</th>
						<th width="80">Roll Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];
					$box = $prod_ref[14];
					//$from_date

					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
					if($width!='') $width_cond=" and b.width='$width'"; else $width_cond="";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self' and c.bin_box = '$box'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$issRtnSql = "SELECT a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no, c.transaction_date as receive_date, b.prod_id, sum(c.no_of_roll) as no_of_roll, sum(f.quantity) as quantity
						from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, order_wise_pro_details f where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($companyID) and e.booking_no = '$booking_no' and b.prod_id='$prod_id' and c.store_id='$store_id' and b.body_part_id='$body_part_id' and b.gsm='$gsm' $width_cond and c.cons_uom='$cons_uom' $room_rack_cond and e.id=$batch_id $date_cond and d.knit_dye_source=1 and c.id=f.trans_id and f.trans_type=4 and f.entry_form=52
						group by a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  c.transaction_date, b.prod_id";
					}
					else
					{
						$issRtnSql = "SELECT a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no, c.transaction_date as receive_date, b.prod_id, sum(c.no_of_roll) as no_of_roll, sum(c.cons_quantity) as quantity
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($companyID) and e.booking_no = '$booking_no' and b.prod_id='$prod_id' and c.store_id='$store_id' and b.body_part_id='$body_part_id' and b.gsm='$gsm' $width_cond and c.cons_uom='$cons_uom' $room_rack_cond and e.id=$batch_id $date_cond and d.knit_dye_source=1
					group by a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  c.transaction_date, b.prod_id";
					}
					
					// echo $issRtnSql;die;
					$iss_rtn_data=sql_select($issRtnSql);
					foreach($iss_rtn_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
							<td width="80"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('no_of_roll')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_no_of_roll+=$row[csf('no_of_roll')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? echo number_format($tot_no_of_roll,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_issue_rtn_out")
{
	echo load_html_head_contents("Return Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Issue Return Outside </th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="75">Return Date</th>
						<th width="100">Return ID</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="80">Booking No</th>
						<th width="80">Batch Date</th>
						<th width="80">Return Qty.</th>
						<th width="80">Roll Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];
					$box = $prod_ref[14];
					//$from_date

					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
					if($width!='') $width_cond=" and b.width='$width'"; else $width_cond="";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self' and c.bin_box = '$box'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$issRtnSql = "SELECT a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no, c.transaction_date as receive_date, b.prod_id, sum(c.no_of_roll) as no_of_roll, sum(c.cons_quantity) as quantity
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($companyID) and e.booking_no = '$booking_no' and b.prod_id='$prod_id' and c.store_id='$store_id' and b.body_part_id='$body_part_id' and b.gsm='$gsm' $width_cond and c.cons_uom='$cons_uom' $room_rack_cond and e.id=$batch_id $date_cond and d.knit_dye_source=3
					group by a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  c.transaction_date, b.prod_id";
					}
					else
					{
						$issRtnSql = "SELECT a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no, c.transaction_date as receive_date, b.prod_id, sum(c.no_of_roll) as no_of_roll, sum(c.cons_quantity) as quantity
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, order_wise_pro_details f where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($companyID) and e.booking_no = '$booking_no' and b.prod_id='$prod_id' and c.store_id='$store_id' and b.body_part_id='$body_part_id' and b.gsm='$gsm' $width_cond and c.cons_uom='$cons_uom' $room_rack_cond and e.id=$batch_id $date_cond and d.knit_dye_source=3 and c.id=f.trans_id and f.trans_type=4 and f.entry_form=52
					group by a.recv_number, d.knit_dye_source, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  c.transaction_date, b.prod_id";
					}
					// echo $issRtnSql;die;
					$iss_rtn_data=sql_select($issRtnSql);
					foreach($iss_rtn_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
							<td width="80"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('no_of_roll')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_no_of_roll+=$row[csf('no_of_roll')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? echo number_format($tot_no_of_roll,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_trans_in")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Transfer Date</th>
						<th width="100">Transfer ID</th>
						<th width="100">Booking No</th>
						<th width="80">Trans out Qty.</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!="") $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$trans_in_sql = sql_select("SELECT c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no,  c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom, sum(f.quantity) as  quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, order_wise_pro_details f  where a.id = b.mst_id and b.to_trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2  and e.booking_no = '$booking_no' and c.prod_id='$prod_id' and c.store_id= '$store_id' and c.body_part_id = '$body_part_id' and d.gsm='$gsm' $width_cond $room_rack_cond and c.cons_uom = '$cons_uom' and c.transaction_type = 5 and a.status_active =1 and b.status_active =1 and c.status_active =1  and a.entry_form in (14,15,306) and e.id = $batch_id $date_cond and c.id=f.trans_id and f.trans_type=5 and f.entry_form in (14,15,306) group by c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no, c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom"); //and d.dia_width = '$width'
					}
					else{
						$trans_in_sql = sql_select("SELECT c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no,  c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom, sum(c.cons_quantity) as  quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.to_trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2  and e.booking_no = '$booking_no' and c.prod_id='$prod_id' and c.store_id= '$store_id' and c.body_part_id = '$body_part_id' and d.gsm='$gsm' $width_cond $room_rack_cond and c.cons_uom = '$cons_uom' and c.transaction_type = 5 and a.status_active =1 and b.status_active =1 and c.status_active =1  and a.entry_form in (14,15,306) and e.id = $batch_id $date_cond group by c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no, c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom"); //and d.dia_width = '$width'
					}
					

					foreach($trans_in_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_no_of_roll+=$row[csf('no_of_roll')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right" colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_cutting_inside")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Cutting Inside Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Issue Date</th>
						<th width="100">Req No</th>
						<th width="100">Issue Id</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="100">Booking No</th>
						<th width="100">Batch Date</th>
						<th width="100">Issue Purpose</th>
						<th width="80">Issue Qty.</th>
						<th width="100">Remarks</th>

					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";

					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$issue_sql = sql_select("SELECT a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(f.quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, order_wise_pro_details f where a.id = b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.id=f.trans_id and f.TRANS_TYPE=2 and f.entry_form=18 and f.po_breakdown_id in ($po_ids) and c.company_id in ($companyID) and a.knit_dye_source =1 and a.issue_purpose=9 and c.prod_id= '$prod_id' and c.store_id= $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no= '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category=2 and c.transaction_type=2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks"); //and d.dia_width = '$width'
					}
					else
					{
						$issue_sql = sql_select("SELECT a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(c.cons_quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($companyID) and a.knit_dye_source =1 and a.issue_purpose=9 and c.prod_id= '$prod_id' and c.store_id= $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no= '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category=2 and c.transaction_type=2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks"); //and d.dia_width = '$width'
					}

					foreach($issue_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('cutt_req_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
								<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $row[csf('remarks')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_cutting_outside")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Cutting Outside Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Issue Date</th>
						<th width="100">Req No</th>
						<th width="100">Issue Id</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="100">Booking No</th>
						<th width="100">Batch Date</th>
						<th width="100">Issue Purpose</th>
						<th width="80">Issue Qty.</th>
						<th width="100">Remarks</th>

					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}
					if($po_ids!="")
					{
						$issue_sql = sql_select("select a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(f.quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, order_wise_pro_details f where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($companyID) and a.knit_dye_source =3 and a.issue_purpose=9 and c.id=f.trans_id and f.trans_type=2 and f.entry_form=18 and f.po_breakdown_id in ($po_ids) and c.prod_id = '$prod_id' and c.store_id = $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks"); //and d.dia_width = '$width'
					}
					else
					{
						$issue_sql = sql_select("select a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(c.cons_quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($companyID) and a.knit_dye_source =3 and a.issue_purpose=9 and c.prod_id = '$prod_id' and c.store_id = $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks"); //and d.dia_width = '$width'
					}

					foreach($issue_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('cutt_req_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
								<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $row[csf('remarks')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_other_issue")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Others Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Issue Date</th>
						<th width="100">Req No</th>
						<th width="100">Issue Id</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="100">Booking No</th>
						<th width="100">Batch Date</th>
						<th width="100">Issue Purpose</th>
						<th width="80">Issue Qty.</th>
						<th width="100">Remarks</th>

					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$issue_sql = sql_select("SELECT a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(f.quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, order_wise_pro_details f where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($companyID) and a.issue_purpose!=9 and c.id=f.trans_id and f.trans_type=2 and f.entry_form=18 and f.po_breakdown_id in ($po_ids) and c.prod_id = '$prod_id' and c.store_id = $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks");
					}
					else
					{
						$issue_sql = sql_select("SELECT a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(c.cons_quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($companyID) and a.issue_purpose!=9 and c.prod_id = '$prod_id' and c.store_id = $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks");
					}

					foreach($issue_sql as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('cutt_req_no')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
							<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="100"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_recv_rtn")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Receive Rtn Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Issue Date</th>
						<th width="100">Req No</th>
						<th width="100">Issue Id</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="100">Booking No</th>
						<th width="100">Batch Date</th>
						<th width="100">Issue Purpose</th>
						<th width="80">Issue Qty.</th>
						<th width="100">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";

					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$rcvRtnSql = sql_select("SELECT a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(f.quantity) as quantity 
						from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, order_wise_pro_details f 
						where a.id = b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($companyID) and c.prod_id= '$prod_id' and c.store_id= $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no= '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 46 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category=2 and c.transaction_type=3 and e.id=$batch_id $date_cond and c.id=f.trans_id and f.trans_type=3 and f.entry_form=46
						group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks");
					}
					else
					{
						$rcvRtnSql = sql_select("SELECT a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(c.cons_quantity) as quantity 
					from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e
					where a.id = b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($companyID) and c.prod_id= '$prod_id' and c.store_id= $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no= '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 46 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category=2 and c.transaction_type=3 and e.id=$batch_id $date_cond 
					group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks");
					}
					foreach($rcvRtnSql as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('cutt_req_no')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
							<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="100"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_trans_out")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Transfer Date</th>
						<th width="100">Transfer ID</th>
						<th width="100">Booking No</th>
						<th width="80">Trans out Qty.</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!="") $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					if($po_ids!="")
					{
						$trans_out_sql = sql_select("SELECT c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no,  c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom, sum(f.quantity) as  quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, order_wise_pro_details f where a.id = b.mst_id and b.trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2  and e.booking_no = '$booking_no' and c.prod_id='$prod_id' and c.store_id= '$store_id' and c.body_part_id = '$body_part_id' and d.gsm='$gsm' $width_cond $room_rack_cond and c.cons_uom = '$cons_uom' and c.transaction_type = 6 and a.status_active =1 and b.status_active =1 and c.status_active =1 and a.entry_form in (14,15,306) and e.id = $batch_id $date_cond and c.id=f.trans_id and f.trans_type=6 and f.entry_form in (14,15,306) group by c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no, c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom"); //and d.dia_width = '$width'
					}
					else
					{
						$trans_out_sql = sql_select("SELECT c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no,  c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom, sum(c.cons_quantity) as  quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2  and e.booking_no = '$booking_no' and c.prod_id='$prod_id' and c.store_id= '$store_id' and c.body_part_id = '$body_part_id' and d.gsm='$gsm' $width_cond $room_rack_cond and c.cons_uom = '$cons_uom' and c.transaction_type = 6 and a.status_active =1 and b.status_active =1 and c.status_active =1 and a.entry_form in (14,15,306) and e.id = $batch_id $date_cond group by c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no, c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom"); //and d.dia_width = '$width'
					}
					


					foreach($trans_out_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_no_of_roll+=$row[csf('no_of_roll')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right" colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

?>
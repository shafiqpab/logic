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

if($action=="load_drop_down_buyer_client")
{
	echo create_drop_down( "cbo_buyer_client_id", 120, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in ($data) and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7)) group by a.id,a.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" ); 
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

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			// alert(id);
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
			var idd = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				idd += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			idd = idd.substr( 0, idd.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			// alert(idd);
			$('#hide_job_id').val( idd );
			$('#hide_job_no').val( ddd );
		}
	</script>

	<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	<?
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") 
			{
				$buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; 
				$buyer_id_cond2=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; 
			}
			else 
			{
				$buyer_id_cond="";
				$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$buyer_name";
		$buyer_id_cond2=" and buyer_id=$buyer_name";
	}

	//$search_string="%".trim($txt_job_no)."%";
	//if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";

	if($db_type==0) $year_field_by="year(insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$cbo_year_id";
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";

	if($db_type==0)
	{
		if($cbo_year_id==0)$year_cond=""; else $year_cond="and year(insert_date)='$cbo_year_id'";
	}
	else if($db_type==2)
	{
		if($cbo_year_id==0)$year_cond=""; else $year_cond="and to_char(insert_date,'YYYY')='$cbo_year_id'";
	}
	else $year_cond="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	if($search_by==1) // Job/Style No
	{
		$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field_by from wo_po_details_master where status_active=1 and is_deleted=0 and company_name in($company_id) $buyer_id_cond $year_cond  order by job_no";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	}
	else if ($search_by==2) // Booking No
	{
		$sql= "SELECT id,booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved,pay_mode, $year_field_by from wo_booking_mst 
		where company_id in($company_id) $buyer_id_cond2 $year_cond and booking_type=1 and is_short in(1,2) and status_active=1 and is_deleted=0 order by booking_no";
		// echo $sql;

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Booking No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "company_id,buyer_id,0,0,0", $arr , "company_id,buyer_id,job_no,year,booking_no", "",'','0,0,0,0,0','',1) ;
	}
	else // Batch No
	{
		$sql ="SELECT a.id, a.batch_no,a.sales_order_no, a.extention_no, a.company_id, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id, a.is_sales, a.re_dyeing_from, $year_field_by from pro_batch_create_mst a 
		where  a.company_id in($company_id) $year_cond and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 order by a.batch_date desc";
		// echo $sql;die;

		echo create_list_view("tbl_list_search", "Company,Job No,Year,Extention No,Batch No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,batch_no", "", 1, "company_id,0,0,0,0", $arr , "company_id,sales_order_no,year,extention_no,batch_no", "",'','0,0,0,0,0','',1) ;
	}
	
	// echo $sql;
	
	exit();
}
// inventory\reports\finish_fabric_store\requires\room_rack_wise_finish_fabric_stock_report_gmts_controller.php > report_generate
/*inventory\reports\finish_fabric_store\requires\batch_wise_finish_fabric_stock_report_gmts_controller.php
$action=="report_generate"*/
if($action=="report_generate") // Note: If any change here need to adjust in report_generate_exel_only
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$cbo_client_id 		= str_replace("'","",$cbo_buyer_client_id);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_string 	= trim(str_replace("'","",$txt_search_string));
	$txt_search_str_id 	= str_replace("'","",$txt_search_str_id);
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);	
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$cbo_floor_id 		= str_replace("'","",$cbo_floor_id);
	$cbo_room_id 		= str_replace("'","",$cbo_room_id);
	$cbo_rack_id 		= str_replace("'","",$cbo_rack_id);
	$cbo_shelf_id 		= str_replace("'","",$cbo_shelf_id);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);
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
		$buyer_id_cond=" and d.buyer_id in($buyer_id)";
	}

	if($cbo_client_id!=0) $buyer_client_cond=" and f.client_id in($cbo_client_id)";


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
	//$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	//$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	if ($cbo_search_by==1) // job/style
	{
		$job_no=$txt_search_string;
		// $txt_search_str_id;
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
	}
	elseif ($cbo_search_by==2) // Booking No
	{
		$book_no=$txt_search_string;
		if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and d.booking_no_prefix_num in($book_no)";
	}
	elseif ($cbo_search_by==3) // Batch No
	{
		$txt_batch_no=$txt_search_string;
		if ($txt_search_str_id=="") 
		{
			if($txt_batch_no)
			{
				$batch_cond = " and e.batch_no like '%$txt_batch_no%'";
			}
		}
		else
		{
			$batch_id_cond = " and e.id in($txt_search_str_id)";//Batch Id
		}		
	}
	// echo $batch_id_cond;die;



	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
	$r_id2=execute_query("delete from tmp_booking_no where userid=$user_id ");
	oci_commit($con);

	if($job_no != "" || $book_no!="" || $buyer_id!=0)
	{
		$serch_ref_sql_1 = "SELECT c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and e.job_id=f.id and c.booking_type in (1,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $buyer_client_cond";
		// echo $serch_ref_sql_1;die;

		$concate="";
		if($job_no == "")
		{
			$concate = " union all ";
			$serch_ref_sql_2 = " SELECT d.booking_no from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $buyer_id_cond ";
		}
		$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;

		$serch_ref_result = sql_select($serch_ref_sql);

		foreach ($serch_ref_result as $val)
		{
			if($search_book_arr[$val[csf("booking_no")]]=="")
			{
				$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];

				$r_id2=execute_query("insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 1, '".$val[csf("booking_no")]."')");
				if($r_id2)
				{
					$r_id2=1;
				}
				else
				{
					echo "insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 1, '".$val[csf("booking_no")]."')";
					$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id ");
					$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
					oci_rollback($con);
					die;
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

	if($r_id && $r_id2)
	{
		oci_commit($con);
	}

	if(!empty($search_book_arr))
	{
		$temp_table_name = ", tmp_booking_no g";
		$temp_table_condition = " and g.booking_no=e.booking_no and g.userid=$user_id and g.type=1";
	}

	if($report_type==2)
	{
		$rcv_select = " b.floor_id as FLOOR_ID, b.room as ROOM, b.rack as RACK, b.self as SELF,"; 
		$rcv_group = " b.floor_id, b.room, b.rack, b.self,";
	}

	$rcv_sql = "SELECT b.id as ID, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, a.company_id as COMPANY_ID,a.receive_basis as  RECEIVE_BASIS, a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY,a.booking_id as WO_PI_PROD_ID,a.booking_no as WO_PI_PROD_NO, b.transaction_date as TRANSACTION_DATE, b.prod_id as PROD_ID, b.store_id as STORE_ID, $rcv_select c.NO_OF_ROLL, c.body_part_id as BODY_PART_ID, c.fabric_description_id as FABRIC_DESCRIPTION_ID, c.gsm as GSM, c.width as WIDTH, f.color as COLOR_ID, b.cons_uom as CONS_UOM,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as DIA_WIDTH_TYPE, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as PO_BREAKDOWN_ID, b.cons_quantity as QUANTITY, b.order_rate as ORDER_RATE, b.order_amount as ORDER_AMOUNT, b.pi_wo_batch_no as PI_WO_BATCH_NO, a.lc_sc_no as LC_SC_NO, e.batch_no as BATCH_NO
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and c.id = d.dtls_id and entry_form=37 and d.po_breakdown_id <>0, pro_batch_create_mst e, product_details_master f $temp_table_name 
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id = f.id $store_cond $floor_cond $room_cond $rack_cond $shelf_cond $date_cond $pi_no_cond $batch_cond $batch_id_cond $temp_table_condition
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, $rcv_group c.NO_OF_ROLL, c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color ,b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no, e.batch_no order by a.company_id, b.pi_wo_batch_no";
	//$all_book_nos_cond
	//echo $rcv_sql;die;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
		$ref_str="";
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val["DIA_WIDTH_TYPE"])));

		$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["FABRIC_DESCRIPTION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"]."*".$val["FLOOR_ID"]."*".$val["ROOM"]."*".$val["RACK"]."*".$val["SELF"];

		if($transaction_date >= $date_frm)
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*1*".$val["NO_OF_ROLL"]."__";
		}
		else
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*2*".$val["NO_OF_ROLL"]."__";
		}
		$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

		if($val["BOOKING_WITHOUT_ORDER"] == 0)
		{
			$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
			$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
		}

		$book_str = explode("-", $val["BOOKING_NO"]);
		if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
		{
			$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		}
		$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];
	}
	unset($rcv_data);
	/*echo "<pre>";
	print_r($data_array);die;*/

	if($report_type == 2)
	{
		$trans_in_select = " c.floor_id as FLOOR_ID, c.room as ROOM, c.rack as RACK, c.self as SELF,";
		$trans_in_group = " c.floor_id, c.room, c.rack, c.self,";
	}

	$trans_in_sql = "SELECT c.transaction_date as TRANSACTION_DATE, c.pi_wo_batch_no as PI_WO_BATCH_NO, e.batch_no as BATCH_NO, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, c.body_part_id as BODY_PART_ID, c.prod_id as PROD_ID, c.store_id as STORE_ID, $trans_in_select d.detarmination_id as DETARMINATION_ID, d.gsm as GSM, d.dia_width as WIDTH, d.color as COLOR_ID, c.cons_uom as  CONS_UOM, sum(c.cons_quantity) as QUANTITY, c.order_rate as ORDER_RATE, c.order_amount as ORDER_AMOUNT, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as PO_BREAKDOWN_ID, b.batch_id as BATCH_ID, b.from_store as FROM_STORE, b.from_prod_id as FROM_PROD_ID, b.NO_OF_ROLL
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type=5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e $temp_table_name
	where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.entry_form in (14,15,306) $store_cond_2 $floor_cond_2 $room_cond_2 $rack_cond_2 $shelf_cond_2 $date_cond_2 $batch_cond $batch_id_cond $temp_table_condition 
	group by c.transaction_date, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, $trans_in_group d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount, b.batch_id, b.from_store, b.from_prod_id, b.NO_OF_ROLL order by c.company_id, c.pi_wo_batch_no";
	//echo $trans_in_sql;die;//$all_book_nos_cond
	$trans_in_data = sql_select($trans_in_sql);
	foreach ($trans_in_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
		$ref_str="";

		$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["DETARMINATION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"]."*".$val["FLOOR_ID"]."*".$val["ROOM"]."*".$val["RACK"]."*".$val["SELF"];

		if($transaction_date >= $date_frm)
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*1*".$val["NO_OF_ROLL"]."__";
		}
		else
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*2*".$val["NO_OF_ROLL"]."__";
		}

		$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

		if($val["BOOKING_WITHOUT_ORDER"] == 0)
		{
			$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
			$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
		}

		$book_str = explode("-", $val["BOOKING_NO"]);
		if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
		{
			$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		}
		$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];

		if($rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"]*1 ==0)
		{
			$all_trans_in_batch[$val["BATCH_ID"]] = $val["BATCH_ID"];
			$trans_in_batch_prod_store[$val["BOOKING_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]] .= $val["BATCH_ID"].'*'.$val["FROM_PROD_ID"].'*'.$val["FROM_STORE"].",";
		}
	}
	unset($trans_in_data);

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22, 2,$all_po_id_arr, $empty_arr); // Order Id temp entry

		$booking_sql = sql_select("SELECT a.body_part_id as BODY_PART_ID,c.booking_no as BOOKING_NO,a.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID, c.gmts_color_id as GMTS_COLOR_ID, c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT, f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE, f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, a.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.job_no=c.job_no and a.id = c.pre_cost_fabric_cost_dtls_id and c.booking_type =1  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and c.booking_no = d.booking_no and c.po_break_down_id = e.id and e.job_id=f.id and e.id=g.ref_val and g.entry_form=22 and g.ref_from=2 and g.user_id=$user_id
		union all
		select b.body_part_id as BODY_PART_ID, c.booking_no as BOOKING_NO, b.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID , c.gmts_color_id as GMTS_COLOR_ID,c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT,f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE,f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, b.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.fabric_description = b.id and b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and c.booking_type =3 and c.booking_no = d.booking_no and e.job_id=f.id and  c.po_break_down_id = e.id and e.id=g.ref_val and g.entry_form=22 and g.ref_from=2 and g.user_id=$user_id");

		//$all_po_id_cond

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


			
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["qnty"] += $val["FIN_FAB_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["color_type"] .= $color_type[$val["COLOR_TYPE"]].",";

			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["amount"] += $val["FIN_FAB_QNTY"]*$val["RATE"];

			$bookingType="";
			if($val['BOOKING_TYPE'] == 4)
			{
				$bookingType = "Sample With Order";
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
		foreach ($all_samp_book_arr as $smbook) 
		{
			if($all_samp_book_arr[$smbook]=="")
			{
				$all_samp_book_arr[$smbook] = $smbook;

				$r_id4=execute_query("insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 2, ".$smbook.")");
				if($r_id4)
				{
					$r_id4=1;
				}
				else
				{
					echo "insert into tmp_booking_no (userid, type, type, booking_no) values ($user_id, 2, ".$smbook.")";
					$r_id4=execute_query("delete from tmp_booking_no where userid=$user_id ");
					$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
					oci_rollback($con);
					die;
				}
			}
		}
		oci_commit($con);

		//$all_samp_book_ids = implode(",", $all_samp_book_arr);
		$non_samp_sql = sql_select("SELECT a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.gmts_color,b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_no g where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4 and a.booking_no=g.booking_no and g.userid=$user_id and g.type=2"); //$all_samp_book_nos_cond

		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}
		}
		unset($non_samp_sql);
	}

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		$batch_ids= implode(",",$batch_id_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22, 3,$batch_id_arr, $empty_arr); // all Batch Id temp entry
	}

	if($report_type == 2)
	{
		$issue_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, c.no_of_roll, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id"; 
	//$all_batch_ids_cond
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{
		$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("batch_id")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			if($val[csf("knit_dye_source")] == 1)
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_in"] += $val[csf("no_of_roll")];
			}
			else
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_out"] += $val[csf("no_of_roll")];
			}
		}
		else
		{
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening"] += $val[csf("quantity")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening_amount"] +=$val[csf("quantity")]*$val[csf("order_rate")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_open"] +=$val[csf("no_of_roll")];
		}
	}
	unset($issRtnData);
	
	$issue_sql = sql_select("SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, b.no_of_roll as no_of_roll, c.cons_uom, c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate 
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g  
	where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 
	group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, b.no_of_roll, c.cons_uom, c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2)");

	//$all_batch_ids_cond
	
	foreach ($issue_sql as $val)
	{
		$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		
		if($transaction_date >= $date_frm)
		{
			if($val[csf("issue_purpose")] == 9)
			{
				if($val[csf("knit_dye_source")] == 1)
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside"] += $val[csf("cons_quantity")];
					$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_in"] += $val[csf("no_of_roll")];
				}
				else
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside"] += $val[csf("cons_quantity")];
					$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_out"] += $val[csf("no_of_roll")];
				}
			}
			else
			{
				$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue"] += $val[csf("cons_quantity")];
				$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_other"] += $val[csf("no_of_roll")];
			}
			$issue_data[$val[csf("booking_no")]][$issRef_str]["issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue"] += $val[csf("cons_quantity")];
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			$issue_data[$val[csf("booking_no")]][$issRef_str]["no_of_roll_open"] += $val[csf("no_of_roll")];
		}
	}
	unset($issue_sql);
	/*echo "<pre>";
	print_r($issue_data);
	die;*/
	if($report_type == 2){
		$rcv_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$rcvRtnSql = sql_select("SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, b.no_of_roll, c.pi_wo_batch_no, e.batch_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");
	//$all_batch_ids_cond

	foreach ($rcvRtnSql as $val)
	{
		$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["no_of_roll"] += $val[csf("no_of_roll")];
		}
		else
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["no_of_roll_open"] += $val[csf("no_of_roll")];
		}
	}
	unset($rcvRtnSql);

	if($report_type == 2)
	{
		$trans_out_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$transOutSql = sql_select("SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate, b.no_of_roll 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g 
	where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");
	//$all_batch_ids_cond
	foreach ($transOutSql as $val)
	{
		$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["no_of_roll"] += $val[csf("no_of_roll")];
		}
		else
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["no_of_roll_open"] += $val[csf("no_of_roll")];
		}
	}
	unset($transOutSql);

    /*echo "<pre>";
    print_r($consumption_arr);
    die;*/

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
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
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22, 4,$all_prod_id, $empty_arr); // all Prod Id temp entry

    	$transaction_date_array=array();
    	$sql_date="SELECT c.booking_no, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,pro_batch_create_mst c, GBL_TEMP_ENGINE g where a.pi_wo_batch_no=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 and a.prod_id=g.ref_val and g.ref_from=4 and g.entry_form=22 and g.user_id=$user_id   group by c.booking_no,a.prod_id";
		//$all_prod_id_cond

		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}
		unset($sql_date_result);
    	
    }

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
	execute_query("delete from tmp_booking_no where user_id=$user_id");
	oci_commit($con);
	disconnect($con);

    $floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
    
	/* echo "<pre>"; print_r($data_array); die; */

	$table_width = "2130";
	$col_span = "33";
	$summery_table_width = "2130";

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
					</strong>
				</td>
			</tr>
		</table>

		<table width="<? echo $summery_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">LC Company</th>
				<th width="100">Buyer</th>
				<th width="100">Buyer Client</th>
				<th width="100">Job</th>
				<th width="100">Style</th>
				<th width="100">Booking No</th>
				<th width="100">Body Part</th>
				<th width="100">Batch No</th>
				<th width="100">Construction</th>
				<th width="100">Composition</th>
				<th width="100">Store</th>
				<th width="100">Floor</th>
				<th width="100">Room</th>
				<th width="100">Rack</th>
				<th width="100">Shelf</th>
				<th width="100">F. Color</th>
				<th width="100">UOM</th>
				<th width="100">Closing Stock</th>
				<th width="100">No Of Roll</th>
				<th width="100">DOH</th>
				<th width="">Age (Days)</th>
			</thead>
		</table>
		<div style="width:<? echo $summery_table_width+20;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="<? echo $summery_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?
				$i=1;$total_stock_qnty=0;$tot_receive_roll=0;
				foreach ($data_array as $uom => $uom_data)
				{
					//$uom_total_stock_qnty=0;
					foreach ($uom_data as $booking_no => $book_data)
					{
						foreach ($book_data as $prodStr => $row)
						{
							//echo $prodStr."<br>";
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$recv_roll=0;$trans_in_roll=0;$opening_recv_roll=0;$opening_trans_in_roll=0;
							$ref_qnty_arr = explode("__", $row);
							$recv_qnty=$trans_out_qty=$trans_in_qty=$opening_recv=$opening_trans=0;
							$recv_amount=$opening_recv_amount=$trans_in_amount=$opening_trans_amount=0;
							$dia_width_types="";$pi_no=""; $lc_sc_no="";
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
								$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9]."*".$prodStr[10]."*".$prodStr[11]."*".$prodStr[12]."*".$prodStr[13];
							}
							else
							{
								$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9];
							}
							
							$inside_return_roll 		= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_in"];
							$outside_return_roll 		= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_out"];

							$inside_return 			= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return"];
							$inside_return_amount 	= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return_amount"];
							$outside_return 		= $issue_return_data[$booking_no][$issRtnRef_str]["outside_return"];
							$outside_return_amount  = $issue_return_data[$booking_no][$issRtnRef_str]["outside_return_amount"];
							$opening_iss_return 	= $issue_return_data[$booking_no][$issRtnRef_str]["opening"];
							$opening_iss_return_amount = $issue_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$tot_receive 			= $recv_qnty + $trans_in_qty + $inside_return + $outside_return;
							$tot_receive_amount 	= $recv_amount + $trans_in_amount + $inside_return_amount + $outside_return_amount;

							$cutting_inside_roll		= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_in"];
							$cutting_outside_roll 		= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_out"];
							$other_issue_roll			= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_other"];
							$opening_issue_roll 		= $issue_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];
							$rcv_return_opening_roll 	= $rcv_return_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];
							$rcv_return_roll 			= $rcv_return_data[$booking_no][$issRtnRef_str]["no_of_roll"];
							$trans_out_roll  			= $trans_out_data[$booking_no][$issRtnRef_str]["no_of_roll"];
							$trans_out_opening_roll 	= $trans_out_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];
							$opening_iss_return_roll 	= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_open"];

							// echo $opening_recv_roll .'+'. $opening_trans_in_roll .'+'. $opening_iss_return_roll.') - ('.$opening_issue_roll .'+'. $rcv_return_opening_roll .'+'.$trans_out_opening_roll.'<br>';
							$opening_roll 	= ($opening_recv_roll + $opening_trans_in_roll + $opening_iss_return_roll) - ($opening_issue_roll + $rcv_return_opening_roll +$trans_out_opening_roll);

							$tot_receive_roll 			= $recv_roll + $trans_in_roll + $inside_return_roll + $outside_return_roll;
							$total_issue_roll  			= $cutting_inside_roll + $cutting_outside_roll + $other_issue_roll + $rcv_return_roll + $trans_out_roll;
							$stock_roll					= $opening_roll + ($tot_receive_roll - $total_issue_roll);
							$stock_roll_title 	= "Opening:".$opening_roll ." + (Receive:". $tot_receive_roll ."- Issue:". $total_issue_roll.")";

							//$tot_receive_roll 	= $recv_roll + $trans_in_roll + $inside_return_roll + $outside_return_roll;

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

							$opening_title 	= "Receive:".$opening_recv ." + Transfer In:". $opening_trans ." + Issue Return:" . $opening_iss_return . "\n";
							$opening_title 	.= "Issue:".$opening_issue ." + Transfer Out:". $trans_out_opening_qnty ." + Receive Return:" . $rcv_return_opening_qnty;
							$opening_qnty 	= ($opening_recv + $opening_trans + $opening_iss_return) - ($opening_issue + $rcv_return_opening_qnty +$trans_out_opening_qnty);

							$stock_qnty 	= $opening_qnty + ($tot_receive - $total_issue);
							$stock_title 	= "Opening:".$opening_qnty ." + (Receive:". $tot_receive ."- Issue:". $total_issue.")";


							$opening_amount = ($opening_recv_amount+$opening_trans_amount) -($opening_issue_amount + $rcv_return_opening_amount);

							if($opening_qnty>0)
							{
								//$opening_rate = $opening_amount/$opening_qnty;

								//$opening_rate = ($opening_recv_amount+$opening_trans_amount) / ($opening_recv + $opening_trans + $opening_iss_return);
							}

							if(number_format($stock_qnty,2,".","") == "-0.00")
							{
								$stock_qnty=0;
							}

							

							$daysOnHand = datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['max_date'],'','',1),date("Y-m-d"));
							$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['min_date'],'','',1),date("Y-m-d"));

							//echo $recv_qnty."<br>";
							/*if(($consump_per_dzn/12) > 0)
							{
								$possible_cut_piece = $stock_qnty/($consump_per_dzn/12);
							}*/

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qnty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qnty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qnty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qnty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qnty == $txt_qnty) || $get_upto_qnty == 0))
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" class="word_break_wrap"><? echo $i;?></td>
									<td width="100" class="word_break_wrap"><? echo $company_arr[$company_name]; ?></td>
									<td width="100" class="word_break_wrap"><? echo $buyer_arr[$buyer_name]; ?></td>
									<td width="100" class="word_break_wrap"><? echo chop($client_nos,",");?></td>
									<td width="100" class="word_break_wrap"><? echo $job_nos;?></td>
									<td width="100" class="word_break_wrap"><? echo $style_ref_no;?></td>
									<td width="100" class="word_break_wrap"><? echo $booking_no; ?></td>
									<td width="100" class="word_break_wrap"><? echo $body_part[$prodStr[2]]?></td>
									<td width="100" class="word_break_wrap" title="batch"><? echo $prodStr[9];?></td>
									<td width="100" class="word_break_wrap"><? echo $constructionArr[$prodStr[3]];?></td>
									<td width="100" class="word_break_wrap"><? echo $composition_arr[$prodStr[3]];?></td>
									<td width="100" class="word_break_wrap" title="store"><? echo $store_arr[$prodStr[1]];?></td>
									<td width="100" class="word_break_wrap" title="floor"><? echo $floor_room_rack_arr[$prodStr[10]];?></td>
									<td width="100" class="word_break_wrap" title="room"><? echo $floor_room_rack_arr[$prodStr[11]];?></td>
									<td width="100" class="word_break_wrap" title="rack"><? echo $floor_room_rack_arr[$prodStr[12]];?></td>
									<td width="100" class="word_break_wrap" title="shelf"><? echo $floor_room_rack_arr[$prodStr[13]];?></td>
									<td width="100" class="word_break_wrap"><? echo $color_arr[$prodStr[6]];?></td>
									<td width="100" class="word_break_wrap"><? echo $unit_of_measurement[$prodStr[7]]; ?></td>
									<td width="100" class="word_break_wrap" align="right" title="<?=$stock_title;?>"><? echo number_format($stock_qnty,2,".",""); ?></td>
									<td width="100" class="word_break_wrap" align="center" title="<?=$stock_roll_title;?>"><? echo $stock_roll;//$tot_receive_roll; ?></td>
									<td width="100" class="word_break_wrap"><? echo $daysOnHand; ?></td>
									<td width="" class="word_break_wrap"><? echo $ageOfDays; ?></td>
								</tr>
								<?
								$i++;
								$total_stock_qnty+=$stock_qnty;
							}
						}						
					}
				}
				?>
			</table>
		</div>
		<table width="<? echo $summery_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100" align="right"><b>Total:</b></th>
				<th width="100" align="right"><? echo number_format($total_stock_qnty,2,".","");?></th>
				<th width="100" align="right"></th>
				<th width="100"></th>
				<th width=""></th>
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

if($action=="report_generate_exel_only") // Note: If any change here need to adjust in report_generate
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$cbo_client_id 		= str_replace("'","",$cbo_buyer_client_id);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_string 	= trim(str_replace("'","",$txt_search_string));
	$txt_search_str_id 	= str_replace("'","",$txt_search_str_id);
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);	
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$cbo_floor_id 		= str_replace("'","",$cbo_floor_id);
	$cbo_room_id 		= str_replace("'","",$cbo_room_id);
	$cbo_rack_id 		= str_replace("'","",$cbo_rack_id);
	$cbo_shelf_id 		= str_replace("'","",$cbo_shelf_id);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);
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
		$buyer_id_cond=" and d.buyer_id in($buyer_id)";
	}

	if($cbo_client_id!=0) $buyer_client_cond=" and f.client_id in($cbo_client_id)";

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
	//$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	//$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	if ($cbo_search_by==1) // job/style
	{
		$job_no=$txt_search_string;
		// $txt_search_str_id;
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
	}
	elseif ($cbo_search_by==2) // Booking No
	{
		$book_no=$txt_search_string;
		if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and d.booking_no_prefix_num in($book_no)";
	}
	elseif ($cbo_search_by==3) // Batch No
	{
		$txt_batch_no=$txt_search_string;
		if ($txt_search_str_id=="") 
		{
			if($txt_batch_no)
			{
				$batch_cond = " and e.batch_no like '%$txt_batch_no%'";
			}
		}
		else
		{
			$batch_id_cond = " and e.id in($txt_search_str_id)";//Batch Id
		}		
	}
	// echo $batch_id_cond;die;

	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
	$r_id2=execute_query("delete from tmp_booking_no where userid=$user_id ");
	oci_commit($con);

	if($job_no != "" || $book_no!="" || $buyer_id!=0)
	{
		$serch_ref_sql_1 = "SELECT c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and e.job_id=f.id and c.booking_type in (1,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $buyer_client_cond";

		$concate="";
		if($job_no == "")
		{
			$concate = " union all ";
			$serch_ref_sql_2 = " SELECT d.booking_no from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $buyer_id_cond ";
		}
		$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;

		$serch_ref_result = sql_select($serch_ref_sql);

		foreach ($serch_ref_result as $val)
		{
			if($search_book_arr[$val[csf("booking_no")]]=="")
			{
				$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];

				$r_id2=execute_query("insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 1, '".$val[csf("booking_no")]."')");
				if($r_id2)
				{
					$r_id2=1;
				}
				else
				{
					echo "insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 1, '".$val[csf("booking_no")]."')";
					$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id ");
					$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
					oci_rollback($con);
					die;
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

	if($r_id && $r_id2)
	{
		oci_commit($con);
	}

	if(!empty($search_book_arr))
	{
		$temp_table_name = ", tmp_booking_no g";
		$temp_table_condition = " and g.booking_no=e.booking_no and g.userid=$user_id and g.type=1";
	}

	if($report_type==2)
	{
		$rcv_select = " b.floor_id as FLOOR_ID, b.room as ROOM, b.rack as RACK, b.self as SELF,"; 
		$rcv_group = " b.floor_id, b.room, b.rack, b.self,";
	}

	$rcv_sql = "SELECT b.id as ID, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, a.company_id as COMPANY_ID,a.receive_basis as  RECEIVE_BASIS, a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY,a.booking_id as WO_PI_PROD_ID,a.booking_no as WO_PI_PROD_NO, b.transaction_date as TRANSACTION_DATE, b.prod_id as PROD_ID, b.store_id as STORE_ID, $rcv_select c.NO_OF_ROLL, c.body_part_id as BODY_PART_ID, c.fabric_description_id as FABRIC_DESCRIPTION_ID, c.gsm as GSM, c.width as WIDTH, f.color as COLOR_ID, b.cons_uom as CONS_UOM,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as DIA_WIDTH_TYPE, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as PO_BREAKDOWN_ID, b.cons_quantity as QUANTITY, b.order_rate as ORDER_RATE, b.order_amount as ORDER_AMOUNT, b.pi_wo_batch_no as PI_WO_BATCH_NO, a.lc_sc_no as LC_SC_NO, e.batch_no as BATCH_NO
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and c.id = d.dtls_id and entry_form=37 and d.po_breakdown_id <>0, pro_batch_create_mst e, product_details_master f $temp_table_name 
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id = f.id $store_cond $floor_cond $room_cond $rack_cond $shelf_cond $date_cond $pi_no_cond $batch_cond $batch_id_cond $temp_table_condition
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, $rcv_group c.NO_OF_ROLL, c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color ,b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no, e.batch_no order by a.company_id, b.pi_wo_batch_no";
	//$all_book_nos_cond
	//echo $rcv_sql;die;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
		$ref_str="";
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val["DIA_WIDTH_TYPE"])));

		$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["FABRIC_DESCRIPTION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"]."*".$val["FLOOR_ID"]."*".$val["ROOM"]."*".$val["RACK"]."*".$val["SELF"];

		if($transaction_date >= $date_frm)
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*1*".$val["NO_OF_ROLL"]."__";
		}
		else
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*2*".$val["NO_OF_ROLL"]."__";
		}
		$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

		if($val["BOOKING_WITHOUT_ORDER"] == 0)
		{
			$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
			$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
		}

		$book_str = explode("-", $val["BOOKING_NO"]);
		if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
		{
			$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		}
		$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];
	}
	unset($rcv_data);
	/*echo "<pre>";
	print_r($data_array);die;*/

	if($report_type == 2)
	{
		$trans_in_select = " c.floor_id as FLOOR_ID, c.room as ROOM, c.rack as RACK, c.self as SELF,";
		$trans_in_group = " c.floor_id, c.room, c.rack, c.self,";
	}

	$trans_in_sql = "SELECT c.transaction_date as TRANSACTION_DATE, c.pi_wo_batch_no as PI_WO_BATCH_NO, e.batch_no as BATCH_NO, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, c.body_part_id as BODY_PART_ID, c.prod_id as PROD_ID, c.store_id as STORE_ID, $trans_in_select d.detarmination_id as DETARMINATION_ID, d.gsm as GSM, d.dia_width as WIDTH, d.color as COLOR_ID, c.cons_uom as  CONS_UOM, sum(c.cons_quantity) as QUANTITY, c.order_rate as ORDER_RATE, c.order_amount as ORDER_AMOUNT, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as PO_BREAKDOWN_ID, b.batch_id as BATCH_ID, b.from_store as FROM_STORE, b.from_prod_id as FROM_PROD_ID, b.NO_OF_ROLL
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type=5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e $temp_table_name
	where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.entry_form in (14,15,306) $store_cond_2 $floor_cond_2 $room_cond_2 $rack_cond_2 $shelf_cond_2 $date_cond_2 $batch_cond $batch_id_cond $temp_table_condition 
	group by c.transaction_date, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, $trans_in_group d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount, b.batch_id, b.from_store, b.from_prod_id, b.NO_OF_ROLL order by c.company_id, c.pi_wo_batch_no";
	//echo $trans_in_sql;die;//$all_book_nos_cond
	$trans_in_data = sql_select($trans_in_sql);
	foreach ($trans_in_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
		$ref_str="";

		$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["DETARMINATION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"]."*".$val["FLOOR_ID"]."*".$val["ROOM"]."*".$val["RACK"]."*".$val["SELF"];

		if($transaction_date >= $date_frm)
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*1*".$val["NO_OF_ROLL"]."__";
		}
		else
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*2*".$val["NO_OF_ROLL"]."__";
		}

		$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

		if($val["BOOKING_WITHOUT_ORDER"] == 0)
		{
			$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
			$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
		}

		$book_str = explode("-", $val["BOOKING_NO"]);
		if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
		{
			$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		}
		$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];

		if($rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"]*1 ==0)
		{
			$all_trans_in_batch[$val["BATCH_ID"]] = $val["BATCH_ID"];
			$trans_in_batch_prod_store[$val["BOOKING_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]] .= $val["BATCH_ID"].'*'.$val["FROM_PROD_ID"].'*'.$val["FROM_STORE"].",";
		}
	}
	unset($trans_in_data);

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22, 2,$all_po_id_arr, $empty_arr); // Order Id temp entry

		$booking_sql = sql_select("SELECT a.body_part_id as BODY_PART_ID,c.booking_no as BOOKING_NO,a.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID, c.gmts_color_id as GMTS_COLOR_ID, c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT, f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE, f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, a.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.job_no=c.job_no and a.id = c.pre_cost_fabric_cost_dtls_id and c.booking_type =1  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and c.booking_no = d.booking_no and c.po_break_down_id = e.id and e.job_id=f.id and e.id=g.ref_val and g.entry_form=22 and g.ref_from=2 and g.user_id=$user_id
		union all
		select b.body_part_id as BODY_PART_ID, c.booking_no as BOOKING_NO, b.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID , c.gmts_color_id as GMTS_COLOR_ID,c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT,f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE,f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, b.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.fabric_description = b.id and b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and c.booking_type =3 and c.booking_no = d.booking_no and e.job_id=f.id and  c.po_break_down_id = e.id and e.id=g.ref_val and g.entry_form=22 and g.ref_from=2 and g.user_id=$user_id");

		//$all_po_id_cond

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


			
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["qnty"] += $val["FIN_FAB_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["color_type"] .= $color_type[$val["COLOR_TYPE"]].",";

			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["amount"] += $val["FIN_FAB_QNTY"]*$val["RATE"];

			$bookingType="";
			if($val['BOOKING_TYPE'] == 4)
			{
				$bookingType = "Sample With Order";
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
		foreach ($all_samp_book_arr as $smbook) 
		{
			if($all_samp_book_arr[$smbook]=="")
			{
				$all_samp_book_arr[$smbook] = $smbook;

				$r_id4=execute_query("insert into tmp_booking_no (userid, type, booking_no) values ($user_id, 2, ".$smbook.")");
				if($r_id4)
				{
					$r_id4=1;
				}
				else
				{
					echo "insert into tmp_booking_no (userid, type, type, booking_no) values ($user_id, 2, ".$smbook.")";
					$r_id4=execute_query("delete from tmp_booking_no where userid=$user_id ");
					$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
					oci_rollback($con);
					die;
				}
			}
		}
		oci_commit($con);

		//$all_samp_book_ids = implode(",", $all_samp_book_arr);
		$non_samp_sql = sql_select("SELECT a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.gmts_color,b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_no g where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4 and a.booking_no=g.booking_no and g.userid=$user_id and g.type=2"); //$all_samp_book_nos_cond

		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}
		}
		unset($non_samp_sql);
	}

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		$batch_ids= implode(",",$batch_id_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22, 3,$batch_id_arr, $empty_arr); // all Batch Id temp entry
	}

	if($report_type == 2)
	{
		$issue_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, c.no_of_roll, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id"; 
	//$all_batch_ids_cond
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{
		$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("batch_id")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			if($val[csf("knit_dye_source")] == 1)
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_in"] += $val[csf("no_of_roll")];
			}
			else
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_out"] += $val[csf("no_of_roll")];
			}
		}
		else
		{
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening"] += $val[csf("quantity")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening_amount"] +=$val[csf("quantity")]*$val[csf("order_rate")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["no_of_roll_open"] +=$val[csf("no_of_roll")];
		}
	}
	unset($issRtnData);
	
	$issue_sql = sql_select("SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2)");

	//$all_batch_ids_cond
	
	foreach ($issue_sql as $val)
	{
		$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		
		if($transaction_date >= $date_frm)
		{
			if($val[csf("issue_purpose")] == 9)
			{
				if($val[csf("knit_dye_source")] == 1)
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside"] += $val[csf("cons_quantity")];
				}
				else
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside"] += $val[csf("cons_quantity")];
				}
			}
			else
			{
				$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue"] += $val[csf("cons_quantity")];
			}
			$issue_data[$val[csf("booking_no")]][$issRef_str]["issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue"] += $val[csf("cons_quantity")];
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($issue_sql);
	/*echo "<pre>";
	print_r($issue_data);
	die;*/
	if($report_type == 2){
		$rcv_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$rcvRtnSql = sql_select("SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.pi_wo_batch_no, e.batch_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");
	//$all_batch_ids_cond

	foreach ($rcvRtnSql as $val)
	{
		$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($rcvRtnSql);

	if($report_type == 2)
	{
		$trans_out_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$transOutSql = sql_select("SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g 
	where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.ref_from=3 and g.entry_form=22 and g.user_id=$user_id and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");
	//$all_batch_ids_cond
	foreach ($transOutSql as $val)
	{
		$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($transOutSql);

    /*echo "<pre>";
    print_r($consumption_arr);
    die;*/

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
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
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22, 4,$all_prod_id, $empty_arr); // all Prod Id temp entry

    	$transaction_date_array=array();
    	$sql_date="SELECT c.booking_no, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,pro_batch_create_mst c, GBL_TEMP_ENGINE g where a.pi_wo_batch_no=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 and a.prod_id=g.ref_val and g.ref_from=4 and g.entry_form=22 and g.user_id=$user_id   group by c.booking_no,a.prod_id";
		//$all_prod_id_cond

		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}
		unset($sql_date_result);
    	
    }

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22)");
	execute_query("delete from tmp_booking_no where user_id=$user_id");
	oci_commit($con);
	disconnect($con);

    $floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
    
	/* echo "<pre>"; print_r($data_array); die; */

	$table_width = "2130";
	$col_span = "33";
	$summery_table_width = "2130";

	//ob_start();

	$html = "";
	$html .= '<table cellpadding="0" cellspacing="0" width="2080">
		<tr  class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="23" style="font-size:18px"><strong>'. $report_title .'</strong></td>
		</tr>
		<tr  class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="23" style="font-size:16px"><strong>'. $company_arr[str_replace("'","",$cbo_company_id)] .'</strong></td>
		</tr>
		<tr  class="form_caption" style="border:none;">
			<td align="center" width="100%" colspan="23" style="font-size:14px"><strong>';
				if($date_from!="") $html .= "From : ".change_date_format(str_replace("'","",$txt_date_from));
				if($date_to!="") $html .= "To : ".change_date_format(str_replace("'","",$txt_date_to));
				$html .= '<strong>
			</td>
		</tr>
	</table>

	<table width="'. $summery_table_width .'" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>
			<th>LC Company</th>
			<th>Buyer</th>
			<th>Buyer Client</th>
			<th>Job</th>
			<th>Style</th>
			<th>Booking No</th>
			<th>Body Part</th>
			<th>Batch No</th>
			<th>Construction</th>
			<th>Composition</th>
			<th>Store</th>
			<th>Floor</th>
			<th>Room</th>
			<th>Rack</th>
			<th>Shelf</th>
			<th>F. Color</th>
			<th>UOM</th>
			<th>Closing Stock</th>
			<th>No Of Roll</th>
			<th>DOH</th>
			<th>Age (Days)</th>
		</thead>
	</table>';

		$html .= '<table width="'. $summery_table_width .'" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >';
			
			$i=1;$total_stock_qnty=0;
			foreach ($data_array as $uom => $uom_data)
			{
				//$uom_total_stock_qnty=0;
				foreach ($uom_data as $booking_no => $book_data)
				{
					foreach ($book_data as $prodStr => $row)
					{
						//echo $prodStr."<br>";
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$recv_roll=0;$trans_in_roll=0;$opening_recv_roll=0;$opening_trans_in_roll=0;
						$ref_qnty_arr = explode("__", $row);
						$recv_qnty=$trans_out_qty=$trans_in_qty=$opening_recv=$opening_trans=0;
						$recv_amount=$opening_recv_amount=$trans_in_amount=$opening_trans_amount=0;
						$dia_width_types="";$pi_no=""; $lc_sc_no="";
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
							$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9]."*".$prodStr[10]."*".$prodStr[11]."*".$prodStr[12]."*".$prodStr[13];
						}
						else
						{
							$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9];
						}
						
						$inside_return_roll 		= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_in"];
						$outside_return_roll 		= $issue_return_data[$booking_no][$issRtnRef_str]["no_of_roll_out"];

						$inside_return 			= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return"];
						$inside_return_amount 	= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return_amount"];
						$outside_return 		= $issue_return_data[$booking_no][$issRtnRef_str]["outside_return"];
						$outside_return_amount  = $issue_return_data[$booking_no][$issRtnRef_str]["outside_return_amount"];
						$opening_iss_return 	= $issue_return_data[$booking_no][$issRtnRef_str]["opening"];
						$opening_iss_return_amount = $issue_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

						$tot_receive 			= $recv_qnty + $trans_in_qty + $inside_return + $outside_return;
						$tot_receive_amount 	= $recv_amount + $trans_in_amount + $inside_return_amount + $outside_return_amount;

						$tot_receive_roll 	= $recv_roll + $trans_in_roll + $inside_return_roll + $outside_return_roll;

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

						$opening_title 	= "Receive:".$opening_recv ." + Transfer In:". $opening_trans ." + Issue Return:" . $opening_iss_return . "\n";
						$opening_title 	.= "Issue:".$opening_issue ." + Transfer Out:". $trans_out_opening_qnty ." + Receive Return:" . $rcv_return_opening_qnty;
						$opening_qnty 	= ($opening_recv + $opening_trans + $opening_iss_return) - ($opening_issue + $rcv_return_opening_qnty +$trans_out_opening_qnty);

						$stock_qnty 	= $opening_qnty + ($tot_receive - $total_issue);
						$stock_title 	= "Opening:".$opening_qnty ." + (Receive:". $tot_receive ."- Issue:". $total_issue.")";


						$opening_amount = ($opening_recv_amount+$opening_trans_amount) -($opening_issue_amount + $rcv_return_opening_amount);

						if($opening_qnty>0)
						{
							//$opening_rate = $opening_amount/$opening_qnty;

							//$opening_rate = ($opening_recv_amount+$opening_trans_amount) / ($opening_recv + $opening_trans + $opening_iss_return);
						}

						if(number_format($stock_qnty,2,".","") == "-0.00")
						{
							$stock_qnty=0;
						}

						

						$daysOnHand = datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['max_date'],'','',1),date("Y-m-d"));
						$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['min_date'],'','',1),date("Y-m-d"));

						//echo $recv_qnty."<br>";
						/*if(($consump_per_dzn/12) > 0)
						{
							$possible_cut_piece = $stock_qnty/($consump_per_dzn/12);
						}*/

						if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qnty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qnty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qnty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qnty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qnty == $txt_qnty) || $get_upto_qnty == 0))
						{
							$html .='<tr id="tr'. $i.'">
								<td>'. $i .'</td>
								<td>'. $company_arr[$company_name] .'</td>
								<td>'. $buyer_arr[$buyer_name] .'</td>
								<td>'. chop($client_nos,",") .'</td>
								<td>'. $job_nos .'</td>
								<td>'. $style_ref_no .'</td>
								<td>'. $booking_no .'</td>
								<td>'. $body_part[$prodStr[2]] .'</td>
								<td>'. $prodStr[9] .'</td>
								<td>'. $constructionArr[$prodStr[3]] .'</td>
								<td>'. $composition_arr[$prodStr[3]] .'</td>
								<td>'. $store_arr[$prodStr[1]] .'</td>
								<td>'. $floor_room_rack_arr[$prodStr[10]] .'</td>
								<td>'. $floor_room_rack_arr[$prodStr[11]] .'</td>
								<td>'. $floor_room_rack_arr[$prodStr[12]] .'</td>
								<td>'. $floor_room_rack_arr[$prodStr[13]] .'</td>
								<td>'. $color_arr[$prodStr[6]] .'</td>
								<td>'. $unit_of_measurement[$prodStr[7]] .'</td>
								<td>'. number_format($stock_qnty,2,".","") .'</td>
								<td>'. $tot_receive_roll .'</td>
								<td>'. $daysOnHand .'</td>
								<td>'. $ageOfDays .'</td>
							</tr>';						
							$i++;
							$total_stock_qnty+=$stock_qnty;
						}
					}						
				}
			}
		$html .='</table>

	<table width="'. $summery_table_width .'" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<tfoot>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th align="right"><b>Total:</b></th>
			<th align="right"><? echo number_format($total_stock_qnty,2,".","");?></th>
			<th></th>
			<th></th>
			<th></th>
		</tfoot>
	</table>';

	

	foreach (glob("bwffgr_*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename="bwffgr_".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$filename";

	exit();
}
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{

	if($data!=0)
	{
	echo create_drop_down( "cbo_buyer_id", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	}
	else
	{
		echo create_drop_down( "cbo_buyer_id", 100, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

if ($action == "load_drop_down_knitting_com")
{

	$data = explode("_", $data);
	$company_id = $data[1];
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--All--", "", "load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_sales_controller', this.value, 'load_drop_down_floor', 'floor_td' );", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--All--", 0, "");
	} else {
		echo create_drop_down("cbo_knitting_company", 100, $blank_array, "", 1, "--All--", 0, "");
	}
	exit();
}

if ($action == "load_drop_down_floor")
{

	$data = explode("_", $data);
	$company_id = $data[0];
	$location_id = $data[1];
	if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and b.location_id=$location_id";

	echo create_drop_down("cbo_floor", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- All--", 0, "load_machine();", "");
	exit();
}

if ($action == "load_drop_machine")
{
	$data = explode("_", $data);
	$company_id = $data[0];
	$floor_id = $data[1];
	if ($floor_id == 0 || $floor_id == "") $floor_cond = ""; else $floor_cond = " and floor_id=$floor_id";

	echo create_drop_down("cbo_machine_name", 80, "select id, machine_no as machine_name from lib_machine_name where category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by seq_no", "id,machine_name", 1, "-- All --", 0, "", "");
	exit();
}




/*
$company_library=array();
$sql=sql_select("select id, company_short_name, company_name from lib_company");
foreach($sql as $row)
{
	$company_library[$row[csf('id')]]['short']=$row[csf('company_short_name')];
	$company_library[$row[csf('id')]]['full']=$row[csf('company_name')];
}

$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );

*/




if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_knitting_source = str_replace("'","",$cbo_knitting_source);
	$cbo_knitting_company = str_replace("'","",$cbo_knitting_company);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$cbo_year = str_replace("'","",$cbo_year);
	$txt_sales_order_no = str_replace("'","",$txt_sales_order_no);
	$txt_style_ref = str_replace("'","",$txt_style_ref);
	$txt_barcode = str_replace("'","",$txt_barcode);
	$txt_ref_no = str_replace("'","",$txt_ref_no);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_floor = str_replace("'","",$cbo_floor);
	$cbo_machine_name = str_replace("'","",$cbo_machine_name);
	$cbo_shift_name = str_replace("'","",$cbo_shift_name);
	$cbo_search_type = str_replace("'","",$cbo_search_type);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$report_type = str_replace("'","",$report_type);
	 //echo $report_type;die;

	//$knit_defect_array = array(1 => "Hole", 5 => "Loop", 10 => "Press Off", 15 => "Lycra Out", 20 => "Lycra Drop",30 => "Oil Spot", 35 => "Fly Conta", 40 => "Slub",70 => "Yarn Contra",  85 => "Oil/Ink Mark", 90 => "Set up", 95 => "Pin Hole", 100 => "Slub Hole",110 => "Miss Yarn", 115 => "Color Contra [Yarn]",140 => "Dirty Spot",150 => "Stop mark",165 => "Grease spot", 166 => "Knot", 167 => "Tara");



	/* $knit_defect_array = array(1 => "Hole", 5 => "Loop", 10 => "Press Off", 15 => "Lycra Out", 20 => "Lycra Drop", 25 => "Dust", 30 => "Oil Spot", 35 => "Fly Conta", 40 => "Slub", 45 => "Patta",50 => "Needle Break",55 => "Sinker Mark",60 => "Wheel Free",65 => "Count Mix", 70 => "Yarn Contra", 75 => "NEPS",80 => "Black Spot", 85 => "Oil/Ink Mark", 90 => "Set up", 95 => "Pin Hole", 100 => "Slub Hole",110 => "Miss Yarn", 115 => "Color Contra [Yarn]", 120 => "Color/dye spot",125 => "friction mark",130 => "Pin out",135 => "softener spot", 140 => "Dirty Spot", 145 => "Rust Stain", 150 => "Stop mark",155 => "Compacting Broken", 160 => "Insect Spot", 165 => "Grease spot", 166 => "Knot", 167 => "Tara",168 =>"Contamination",169 =>"Thick and Thin"); */

	$knit_defect_array = array(1 => "Hole", 5 => "Loop", 90 => "Set up", 21 => "Lycra Cut", 15 => "Lycra Out", 20 => "Lycra Drop", 110 => "Miss Yarn", 30 => "Oil Spot", 168 =>"Contamination", 105 => "Needle Mark", 50 => "Needle Broken", 55 => "Sinker Mark", 175 =>"Line Star", 95 => "Pin Hole", 60 => "Wheel Free", 45 => "Patta", 169 =>"Thick and Thin", 25 => "Dust", 65 => "Count Mix", 70 => "Yarn Contra", 115 => "Color Contra [Yarn]", 75 => "NEPS", 85 => "Oil/Ink Mark", 150 => "Stop mark", 166 => "Knot", 167 => "Tara", 40 => "Slub", 80 => "Black Spot", 140 => "Dirty Spot", 35 => "Fly Conta", 10 => "Press Off", 100 => "Slub Hole", 120 => "Color/dye spot", 125 => "friction mark" ,130 => "Pin out", 135 => "softener spot", 145 => "Rust Stain", 155 => "Compacting Broken", 160 => "Insect Spot", 165 => "Grease spot");

	$observation_status_arr=array(1=>"Present",2=>"Not Found", 3=>"Major",4=>"Minor",5=>"Acceptable",6=>"Good");

 	$booking_type_arr=array(118=>"Main Fabric Booking",108=>"Partial Fabric Booking",88=>"Short Fabric Booking",89=>"Sample Fabric Booking With Order",90=>"Sample Fabric Booking Without Order");
	$qc_result_arr=array(1=>'QC Pass', 2=>'Held Up', 3=>'Reject');

	$company_lib=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_lib=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
	$buyer_lib=return_library_array( "select id,short_name from lib_buyer","id","short_name");

	$machine_lib=return_library_array( "select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0","id","machine_no");

	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$operator_id_card_arr = return_library_array("select id, id_card_no from lib_employee", 'id', 'id_card_no');
	$operator_punch_card_arr = return_library_array("select id, punch_card_no from lib_employee", 'id', 'punch_card_no');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$userArr = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	$con = connect();
	$r_id1=execute_query("delete from tmp_booking_id where userid=$user_name");
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_name");
	$r_id3=execute_query("delete from tmp_prod_id where userid=$user_name");
	$r_id4=execute_query("delete from tmp_recv_dtls where userid=$user_name");

	if($r_id1 && $r_id2 && $r_id3 && $r_id4)
	{
		oci_commit($con);
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
			$qc_pass_date_cond="and e.qc_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from), '','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
			$qc_pass_date_cond="and e.qc_date between '".change_date_format(trim($txt_date_from), '','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}
	}

	if($cbo_company_name>0){
		$where_con=" and a.company_id=$cbo_company_name";
	}

	if($cbo_knitting_company>0){
		$where_con.=" and a.knitting_company=$cbo_knitting_company";
	}

	if($cbo_knitting_source>0){
		$where_con.=" and a.knitting_source=$cbo_knitting_source";
	}

	if($cbo_floor>0){
		$where_con.=" and b.FLOOR_ID=$cbo_floor";
	}

	if($cbo_buyer_id>0){
		$where_con.=" and a.BUYER_ID=$cbo_buyer_id";
	}

	if($cbo_shift_name>0){
		$where_con.=" and b.SHIFT_NAME='$cbo_shift_name'";
	}

	if($txt_sales_order_no !=""){
		$where_con.=" and (d.JOB_NO like('%$txt_sales_order_no') or c.booking_no ='$txt_sales_order_no')";
	}

	if($txt_style_ref !=""){
		$where_con.=" and d.style_ref_no ='$txt_style_ref' ";
	}

	if($cbo_machine_name>0){
		$where_con.=" and b.MACHINE_NO_ID=$cbo_machine_name";
	}


	if($cbo_year != 0){
		if($db_type==0){$where_con.=" and year(a.insert_date)=$cbo_year";}
		else if($db_type==2){$where_con.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}

	if($txt_barcode != ''){
		$where_con.=" and c.BARCODE_NO='$txt_barcode'";
	}
	// echo $where_con;
	if($txt_ref_no!='')
	{
		$int_ref_sql="SELECT a.booking_no, a.booking_mst_id , b.grouping from wo_booking_dtls a, wo_po_break_down b
		where a.po_break_down_id=b.id  and a.status_active=1 and b.status_active=1 and b.grouping='$txt_ref_no'
		group by a.booking_no, a.booking_mst_id, b.grouping";
		$int_ref_sql_result=sql_select($int_ref_sql);
		$all_booking_id='';
		foreach($int_ref_sql_result as $row)
		{
			if($all_booking_id=="") $all_booking_id=$row[csf('booking_mst_id')]; else $all_booking_id.=",".$row[csf('booking_mst_id')];
		}

		$bookingIds=chop($all_booking_id,','); $booking_cond_for_in="";
		$po_ids=count(array_unique(explode(",",$all_booking_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$booking_cond_for_in=" and (";
			$bookingIdsArr=array_chunk(explode(",",$bookingIds),999);
			foreach($bookingIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$booking_cond_for_in.=" d.booking_id in($ids) or";
			}
			$booking_cond_for_in=chop($booking_cond_for_in,'or ');
			$booking_cond_for_in.=")";
		}
		else
		{
			$booking_cond_for_in=" and d.booking_id in($bookingIds)";
		}
		$booking_order_cond=" and d.book_without_order=0";
	}
	// echo $booking_cond_for_in.'='.$booking_order_cond;die;

	$variable_settingAutoQC = sql_select("select auto_update from variable_settings_production where company_name =$cbo_company_name and variable_list in(47) and item_category_id=13 and is_deleted=0 and status_active=1");
	$autoProductionQuantityUpdatebyQC = $variable_settingAutoQC[0][csf("auto_update")];

	if ($cbo_search_type==2 && $txt_date_from!="" && $txt_date_to!="") // qc pass
	{
		$sql="SELECT a.KNITTING_SOURCE,a.KNITTING_COMPANY,a.RECEIVE_DATE,a.REMARKS,a.BOOKING_TYPE,b.GSM,b.WIDTH,b.SHIFT_NAME,b.YARN_LOT,b.BRAND_ID,b.MACHINE_DIA,b.MACHINE_GG,b.MACHINE_NO_ID,b.YARN_COUNT,b.COLOR_ID,b.STITCH_LENGTH,b.OPERATOR_NAME,b.FEBRIC_DESCRIPTION_ID,b.YARN_PROD_ID,c.BARCODE_NO,c.ROLL_NO,c.QC_PASS_QNTY,c.REJECT_QNTY,d.JOB_NO,d.SALES_BOOKING_NO,d.BOOKING_ID,d.BOOK_WITHOUT_ORDER,d.STYLE_REF_NO,d.ENTRY_FORM,d.PO_BUYER,d.BUYER_ID,A.BOOKING_NO,B.FLOOR_ID, d.CUSTOMER_BUYER, a.RECV_NUMBER
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, PRO_QC_RESULT_mst e, fabric_sales_order_mst d
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.barcode_no=e.barcode_no and c.po_breakdown_id=d.id $where_con $qc_pass_date_cond $booking_cond_for_in $booking_order_cond
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and c.IS_SALES=1 and c.ENTRY_FORM=2 and e.status_active=1 and e.is_deleted=0";
		// echo $sql;
	}
	else
	{
		//---------------------------
		$sql="SELECT a.KNITTING_SOURCE,a.KNITTING_COMPANY,a.RECEIVE_DATE,a.REMARKS,a.BOOKING_TYPE,b.GSM,b.WIDTH,b.SHIFT_NAME,b.YARN_LOT,b.BRAND_ID,b.MACHINE_DIA,b.MACHINE_GG,b.MACHINE_NO_ID,b.YARN_COUNT,b.COLOR_ID,b.STITCH_LENGTH,b.OPERATOR_NAME,b.FEBRIC_DESCRIPTION_ID,b.YARN_PROD_ID,c.BARCODE_NO,c.ROLL_NO,c.QC_PASS_QNTY,c.REJECT_QNTY,d.JOB_NO,d.SALES_BOOKING_NO,d.BOOKING_ID,d.BOOK_WITHOUT_ORDER,d.STYLE_REF_NO,d.ENTRY_FORM,d.PO_BUYER,d.BUYER_ID,A.BOOKING_NO,B.FLOOR_ID, d.CUSTOMER_BUYER, a.RECV_NUMBER, a.INSERTED_BY, a.INSERT_DATE
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,fabric_sales_order_mst d
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id $where_con $date_cond $booking_cond_for_in $booking_order_cond
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and c.IS_SALES=1 and c.ENTRY_FORM=2"; //,d.BOOKING_TYPE
	}
	// echo $sql;

	$sql_result=sql_select($sql);
	$dataArr=array();$booking_id_arr=array();
	foreach($sql_result as $rows)
	{
		$dataArr[$rows[BARCODE_NO]]=$rows;
		$fabric_des_id_arr[$rows[FEBRIC_DESCRIPTION_ID]]=$rows[FEBRIC_DESCRIPTION_ID];
		$barcode_no_arr[$rows[BARCODE_NO]]=$rows[BARCODE_NO];

		if($rows[YARN_PROD_ID])
		{
			$yarn_arr = explode(",",$rows[YARN_PROD_ID]);
			foreach ($yarn_arr as  $Yval) {
				$yarn_pro_id_arr[$Yval]=$Yval;
			}

		}

		$qc_pass_qty_summary_arr[$rows[BARCODE_NO]]+=$rows[QC_PASS_QNTY];
		$reject_qty_summary_arr[$rows[BARCODE_NO]]+=$rows[REJECT_QNTY];
		$roll_no_arr[$rows[BARCODE_NO]]+=$rows[ROLL_NO];

		if ($rows[BOOK_WITHOUT_ORDER]==0 && $rows[BOOKING_ID] !="")
		{
			$booking_id_arr[$rows[BOOKING_ID]]=$rows[BOOKING_ID];
		}
	}
	// echo "<pre>";print_r($booking_id_arr);
	if(count($booking_id_arr)>0)
	{
		/* $booking_ids_cond=" and (";
		if(count($booking_id_arr)>999 && $db_type==2)
		{
			$booking_id_chank=array_chunk($booking_id_arr,999);
			foreach($booking_id_chank as $booking_id)
			{
				$booking_ids_cond.=" a.booking_mst_id in(".implode(",",$booking_id).") or";
			}
			$booking_ids_cond=chop($booking_ids_cond,"or");
		}
		else
		{
			$booking_ids_cond.=" a.booking_mst_id in(".implode(",",$booking_id_arr).")";
		}
		$booking_ids_cond.=")"; */

		foreach ($booking_id_arr as $Bval) {
			$rID2=execute_query("insert into tmp_booking_id (userid, booking_id) values ($user_name,$Bval)");
		}
		if($rID2)
		{
		    oci_commit($con);
		}
	}
	// echo $booking_ids_cond.'=';die;

	if(!empty($barcode_no_arr))
	{
		foreach ($barcode_no_arr as $Bval) {
			$rID3=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,$Bval)");
		}
		if($rID3)
		{
		    oci_commit($con);
		}
	}

	if (count($booking_id_arr)>0)
	{
		$int_ref_sql="SELECT A.BOOKING_NO, A.BOOKING_MST_ID , B.GROUPING from wo_booking_dtls a, wo_po_break_down b, tmp_booking_id c
		where a.po_break_down_id=b.id  and a.status_active=1 and b.status_active=1 and a.booking_mst_id=c.booking_id and c.userid=$user_name
		group by a.booking_no, a.booking_mst_id, b.grouping"; //$booking_ids_cond
		$int_ref_sql_result=sql_select($int_ref_sql);
		$int_ref_arr=array();
		foreach($int_ref_sql_result as $row)
		{
			$int_ref_arr[$row[BOOKING_NO]]=$row['GROUPING'];
		}
		// echo "<pre>";print_r($int_ref_arr);
	}

	//--------------------------------

	if(!empty($fabric_des_id_arr))
	{
		foreach ($fabric_des_id_arr as $Bval) {
			$rID4=execute_query("insert into tmp_recv_dtls (userid, dtls_id) values ($user_name,$Bval)");
		}
		if($rID4)
		{
		    oci_commit($con);
		}


		$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, tmp_recv_dtls c where a.id=b.mst_id and a.id=c.dtls_id and c.userid=$user_name";
		/* $fabric_des_id_list_arr=array_chunk($fabric_des_id_arr,999);
		$p=1;
		foreach($fabric_des_id_list_arr as $fabric_des_id_process)
		{
			if($p==1) $sql_deter .="  and ( a.id in(".implode(',',$fabric_des_id_process).")";
			else  $sql_deter .=" or a.id in(".implode(',',$fabric_des_id_process).")";

			$p++;
		}
		$sql_deter .=")"; */
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row) {
			$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
			$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}
	}


	//------------------------------------------
	/* $deft_sql="SELECT a.BARCODE_NO,a.TOTAL_POINT,a.FABRIC_GRADE,a.QC_NAME,a.COMMENTS,a.ROLL_LENGTH,a.ROLL_WEIGHT,a.ACTUAL_DIA,a.ACTUAL_GSM,a.ROLL_STATUS,b.DEFECT_NAME, b.DEFECT_COUNT, b.FOUND_IN_INCH, b.FOUND_IN_INCH_POINT, b.PENALTY_POINT, b.FORM_TYPE, b.FOUND_IN_INCH, a.insert_date as INSERT_DATE from PRO_QC_RESULT_mst a,PRO_QC_RESULT_DTLS b, tmp_barcode_no c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.barcode_no=c.barcode_no and c.userid=$user_name"; */
	$deft_sql="SELECT a.BARCODE_NO,a.TOTAL_POINT,a.FABRIC_GRADE,a.QC_NAME,a.COMMENTS,a.ROLL_LENGTH,a.ROLL_WEIGHT,a.REJECT_QNTY,a.ACTUAL_DIA,a.ACTUAL_GSM,a.ROLL_STATUS, a.insert_date as INSERT_DATE, a.IS_TAB,b.DEFECT_NAME, b.DEFECT_COUNT, b.FOUND_IN_INCH, b.FOUND_IN_INCH_POINT, b.PENALTY_POINT, b.FORM_TYPE, b.FOUND_IN_INCH from PRO_QC_RESULT_mst a left join PRO_QC_RESULT_DTLS b on a.id=b.mst_id and b.status_active=1 and b.is_deleted=0, tmp_barcode_no c where  a.status_active=1 and a.is_deleted=0 and a.barcode_no=c.barcode_no and c.userid=$user_name";
	// echo $deft_sql;die;
	/* $barcode_no_list_arr=array_chunk($barcode_no_arr,999);
	$p=1;
	foreach($barcode_no_list_arr as $barcode_no_process)
	{
		if($p==1) $deft_sql .="  and ( a.BARCODE_NO in(".implode(',',$barcode_no_process).")";
		else  $deft_sql .=" or a.BARCODE_NO in(".implode(',',$barcode_no_process).")";

		$p++;
	}
	$deft_sql .=")"; */

	$deft_sql_result = sql_select($deft_sql);
	$deft_dtls_data_arr = array();
	$deft_mst_data_arr = array();
	$qc_found_barcode_arr = array();
	foreach ($deft_sql_result as $rows)
	{
		if($rows[FORM_TYPE]==2)
		{
			//$deft_mst_data_arr[$rows[BARCODE_NO]]["ovservation"][$rows[FOUND_IN_INCH]]= $observation_status_arr[$rows[FOUND_IN_INCH]];
			if($rows[FOUND_IN_INCH]>0)$deft_mst_data_arr[$rows[BARCODE_NO]]["ovservation"]+= 1;
		}
		else
		{
			$deft_dtls_data_arr[$rows[BARCODE_NO]][$rows[DEFECT_NAME]]["defect_count"] = $rows[DEFECT_COUNT];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["total_point"] = $rows[TOTAL_POINT];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["grade"] = $rows[FABRIC_GRADE];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["qc_name"] = $rows[QC_NAME];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["comments"] = $rows[COMMENTS];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["roll_length"] = $rows[ROLL_LENGTH];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["roll_weight"] = $rows[ROLL_WEIGHT];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["dia"] = $rows[ACTUAL_DIA];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["gsm"] = $rows[ACTUAL_GSM];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["qc_result"] = $rows[ROLL_STATUS];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["qc_insert_date"] = $rows[INSERT_DATE];

			$deft_dtls_summary_data_arr["defect_count"][$rows[DEFECT_NAME]][$rows[BARCODE_NO]] = $rows[DEFECT_COUNT];
			$deft_mst_summary_data_arr["total_point"][$rows[BARCODE_NO]] = $rows[TOTAL_POINT];
		}
		$qc_found_barcode_arr[$rows[BARCODE_NO]] = $rows[BARCODE_NO];
		if($rows[IS_TAB]==1) // when qc from Tab
		{
			$qc_data_arr[$rows[BARCODE_NO]]["QC_PASS_QTY"] = $rows[ROLL_WEIGHT]-$rows[REJECT_QNTY];
			$qc_data_arr[$rows[BARCODE_NO]]["REJECT_QNTY"] = $rows[REJECT_QNTY];
			$qc_data_arr[$rows[BARCODE_NO]]["PRODUCTION_WEIGHT"] = $rows[ROLL_WEIGHT];
		}
		else // when qc from QC Result page
		{
			$qc_data_arr[$rows[BARCODE_NO]]["QC_PASS_QTY"] = $rows[ROLL_WEIGHT];
			$qc_data_arr[$rows[BARCODE_NO]]["REJECT_QNTY"] = $rows[REJECT_QNTY];
			$qc_data_arr[$rows[BARCODE_NO]]["PRODUCTION_WEIGHT"] = $rows[ROLL_WEIGHT]+$rows[REJECT_QNTY];
		}
	}
	// echo "<pre>";print_r($qc_data_arr);
	//--------------------------

	//------------------------------------------
	$rcv_sql="SELECT a.barcode_no as BARCODE_NO, a.insert_date as INSERT_DATE
	from pro_roll_details a, pro_grey_prod_entry_dtls b, tmp_barcode_no c
	where a.dtls_id=b.id and b.trans_id!=0 and a.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.barcode_no=c.barcode_no and c.userid=$user_name";

	/* $barcode_no_list_arr=array_chunk($barcode_no_arr,999);
	$p=1;
	foreach($barcode_no_list_arr as $barcode_no_process)
	{
		if($p==1) $rcv_sql .="  and ( a.BARCODE_NO in(".implode(',',$barcode_no_process).")";
		else  $rcv_sql .=" or a.BARCODE_NO in(".implode(',',$barcode_no_process).")";

		$p++;
	}
	$rcv_sql .=")"; */

	$rcv_sql_result = sql_select($rcv_sql);
	foreach ($rcv_sql_result as $rows)
	{
		$rcv_data_arr[$rows[BARCODE_NO]]["insert_date"] = $rows[INSERT_DATE];
	}
	//--------------------------


	if(!empty($yarn_pro_id_arr))
	{
		foreach ($yarn_pro_id_arr as $Bval) {
			$rID5=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_name,$Bval)");
		}
		if($rID5)
		{
		    oci_commit($con);
		}
	}


	$yarn_des_sql="select a.ID,a.YARN_COMP_TYPE1ST,a.YARN_TYPE from PRODUCT_DETAILS_MASTER a, tmp_prod_id b where a.id=b.prod_id and b.userid=$user_name and a.status_active=1 and a.is_deleted=0 ";

	/* $yarn_pro_id_list_arr=array_chunk($yarn_pro_id_arr,999);
	$p=1;
	foreach($yarn_pro_id_list_arr as $yarn_pro_id_process)
	{
		if($p==1) $yarn_des_sql .="  and ( id in(".implode(',',$yarn_pro_id_process).")";
		else  $yarn_des_sql .=" or id in(".implode(',',$yarn_pro_id_process).")";

		$p++;
	}
	$yarn_des_sql .=")"; */
	$yarn_des_result = sql_select($yarn_des_sql);
	foreach ($yarn_des_result as $rows) {
		$yarn_comp_data_arr[$rows[ID]] = $composition[$rows[YARN_COMP_TYPE1ST]];
		$yarn_type_data_arr[$rows[ID]] = $yarn_type[$rows[YARN_TYPE]];
	}

	// =============================== Shift Duration Entry ==============================
	/* $sql = "select shift_name,start_time,end_time from shift_duration_entry where production_type=1 and status_active=1 and is_deleted=0";
	$sql_res = sql_select($sql);
	$shift_wise_time_array = array();
	foreach ($sql_res as $val)
	{
		$shift_wise_time_array[$val[csf('shift_name')]]['start_time'] = $val[csf('start_time')];
		$shift_wise_time_array[$val[csf('shift_name')]]['end_time'] = $val[csf('end_time')];
	} */
	// echo "<pre>";print_r($shift_wise_time_array);


	$r_id1=execute_query("delete from tmp_booking_id where userid=$user_name");
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_name");
	$r_id3=execute_query("delete from tmp_prod_id where userid=$user_name");
	$r_id4=execute_query("delete from tmp_recv_dtls where userid=$user_name");

	if($r_id1 && $r_id2 && $r_id3 && $r_id4)
	{
		oci_commit($con);
	}

	$defectArr=array();$defectIdArr=array();
	foreach($dataArr as $barcode=>$rows)
	{
	 	foreach($knit_defect_array as $defect_ids=>$defects)//array defect list
	 	{
	 		if($deft_dtls_data_arr[$barcode][$defect_ids]["defect_count"]>0)
	 		{
 				if($defectArr[$defect_ids]!=$defect_ids)
	 			{
	 				$defectIdArr[$defect_ids]=$defects;
	 				$defectArr[$defect_ids]=$defect_ids;
	 			}
	 		}
        }
	}

	//knit_defect_array=1,2,3,4
	//defectIdArr=1,4,3
	//defectIdArr=outpurt = 1,3,4 //array_intersect() after use this function
	$defectIdArr=array_intersect($knit_defect_array,$defectIdArr);
	// echo "<pre>";print_r($defectIdArr);die;
	$width=(count($defectIdArr)*60)+4670;
	$colspan=count($defectIdArr)+42;

	ob_start();
	?>
	<style>
		#table_body {
		counter-reset: serial-number;  /* Set the serial number counter to 0 */
		}

		#table_body tr:not(:first-child) td:nth-child(12)::after  {
		counter-increment: serial-number;  /* Increment the serial number counter */
		content: counter(serial-number);  /* Display the counter */
		}

		.wrd_brk{word-break: break-all;word-wrap: break-word;}

	</style>
    <fieldset style="width:<? echo $width;?>px;">
    	<table cellpadding="0" cellspacing="0" width="100%">
        	<tr>
                <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:14px"><strong><? echo $company_lib[$cbo_company_name]; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:12px"><strong></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:12px"><strong>Daily Roll wise Knitting QC Report [Details]</strong></td>
            </tr>
        </table>
        <table id="table_header_1" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" align="left" >
            <thead>
                <th width="35">SL</th>
                <th width="80" class="wrd_brk">Date</th>
                <th width="100" class="wrd_brk">Knitting Source</th>
                <th width="100" class="wrd_brk">Floor</th>
                <th width="100" class="wrd_brk">Knitting Company</th>
                <th width="60" class="wrd_brk">Knit M/C No</th>
                <th width="60" class="wrd_brk">M/C DIA</th>
                <th width="60" class="wrd_brk">M/C GAUGE</th>
                <th width="60" class="wrd_brk">SHIFT</th>
                <th width="100" class="wrd_brk">Buyer/Customer</th>
                <th width="100" class="wrd_brk">Cust Buyer</th>
				<th width="60" class="wrd_brk">Roll Row<br> Count</th>
                <th width="100" class="wrd_brk">Program No</th>
                <th width="100" class="wrd_brk">Production Id</th>
                <th width="100" class="wrd_brk">Booking No</th>
                <th width="120" class="wrd_brk">FSO No</th>
                <th width="100" class="wrd_brk">Style Ref.</th>
                <th width="100" class="wrd_brk">Int. Ref.</th>
                <th width="100" class="wrd_brk">Barcode</th>
                <th width="100" class="wrd_brk">Fabric Color</th>
                <th width="60" class="wrd_brk">YARN COUNT</th>
                <th width="100" class="wrd_brk">Yarn Composition</th>
                <th width="100" class="wrd_brk">Yarn Type</th>
                <th width="100" class="wrd_brk">YARN LOT</th>
                <th width="100" class="wrd_brk">Brand</th>
                <th width="100" class="wrd_brk">Stitch Length</th>
                <th width="100" class="wrd_brk">FABRIC Construction</th>
                <th width="200" class="wrd_brk">FABRIC Composition</th>
                <th width="60" class="wrd_brk">Dia</th>
                <th width="60" class="wrd_brk">GSM</th>
                <th width="100" class="wrd_brk">Roll No</th>
				<th width="50" class="wrd_brk">Result</th>
                <th width="60" class="wrd_brk">Qc Pass Qty</th>
                <th width="60" class="wrd_brk">QC Held Up Qty</th>
                <th width="60" class="wrd_brk">Reject Qty</th>
				<th width="60" class="wrd_brk">Production WEIGHT</th>
                <?
                //foreach($knit_defect_array as $defect_id=>$defect){
                foreach($defectIdArr as $defect_id=>$defect){
                 echo '<th width="60" class="wrd_brk">'.$defect.'</th>';
                }
                ?>
                <th width="100" class="wrd_brk">TTL POINTS</th>
                <th width="60" class="wrd_brk">GRADE</th>
                <th width="60" class="wrd_brk">DEFECT %</th>
                <th width="100" class="wrd_brk">LENGTH YDS</th>
                <th width="60" class="wrd_brk">Obser- vation</th>
                <th width="100">Operator Name</th>
                <th width="100">Operator Id</th>
                <th width="100">Operator Panch Card No</th>
                <th width="100">Prod. Insert User</th>
                <th width="100">Prod. Insert Time</th>
                <th width="100" class="wrd_brk">QC Name</th>
                <th class="wrd_brk">Comments</th>
                
                <th class="wrd_brk" width="50">QC Date</th>
                <th class="wrd_brk" width="50">QC Time</th>
                <th class="wrd_brk" width="50">Shift</th>
				<th class="wrd_brk" width="50">Receive Date</th>
                <th class="wrd_brk" width="50">Receive Time</th>
            </thead>
        </table>
		<div style="width:<? echo $width+18;?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" id="table_body" align="left" >
                <tbody >
                    <?
					$i=1;
					foreach($dataArr as $barcode=>$rows)
					{
						$company_arr=($rows[KNITTING_SOURCE]==1)?$company_lib:$supplier_lib;
						$bgcolor=($i%2==0)? "#E9F3FF" : "#FFFFFF";
						$color_name_arr=array();
						foreach(explode(',',$rows[COLOR_ID]) as $color_id){
							$color_name_arr[$color_id]=$color_lib[$color_id];
						}

						$count_name_arr=array();
						foreach(explode(',',$rows[YARN_COUNT]) as $count_id){
							$count_name_arr[$count_id]=$count_arr[$count_id];
						}

						$yarn_comp_arr=array();$yarn_type_arr=array();
						foreach(explode(',',$rows[YARN_PROD_ID]) as $pro_id){
							$yarn_comp_arr[$pro_id]=$yarn_comp_data_arr[$pro_id];
							$yarn_type_arr[$pro_id]=$yarn_type_data_arr[$pro_id];
						}


						if($rows[BOOKING_TYPE] == 4)
						{
							if($rows[csf('is_order')] == 1){
								$booking_type = "Sample With Order";
							}
							else
							{
								$booking_type = "Sample Without Order";
							}
						}
						else
						{
							$booking_type = $booking_type_arr[$rows[ENTRY_FORM]];
						}



						$defect_percent=($deft_mst_data_arr[$barcode]["total_point"]*36*100)/($rows[WIDTH]*$deft_mst_data_arr[$barcode]["roll_length"]);


						$rows[BUYER_ID]=($rows[PO_BUYER]=="")?$rows[BUYER_ID]:$rows[PO_BUYER];

						if($qc_found_barcode_arr[$barcode]!="")
						{
							$qc_pass_qnty=$rows[QC_PASS_QNTY];
						}
						else
						{
							$qc_pass_qnty=0.00;
						}

						if ($autoProductionQuantityUpdatebyQC==1)
						{
							$production_weight=$rows[QC_PASS_QNTY]+$rows[REJECT_QNTY];
						}
						else
						{
							$production_weight=$rows[QC_PASS_QNTY];
						}
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="35" class="cls"><? echo $i; ?></td>
	                        <td width="80" class="wrd_brk" align="center"><? echo change_date_format($rows[RECEIVE_DATE]);?></td>
	                        <td width="100" class="wrd_brk" align="center"><? echo $knitting_source[$rows[KNITTING_SOURCE]];?></td>
	                        <td width="100" class="wrd_brk" align="center"><? echo $floor_arr[$rows[FLOOR_ID]];?></td>
	                        <td width="100" class="wrd_brk"><? echo $company_arr[$rows[KNITTING_COMPANY]];?></td>
	                        <td width="60" class="wrd_brk"><? echo $machine_lib[$rows[MACHINE_NO_ID]*1];?></td>
	                        <td width="60" class="wrd_brk"><? echo $rows[MACHINE_DIA];?></td>
	                        <td width="60" class="wrd_brk"><? echo $rows[MACHINE_GG];?></td>
	                        <td width="60" class="wrd_brk" align="center"><p><? echo $shift_name[$rows[SHIFT_NAME]];?></p></td>
	                        <td width="100" class="wrd_brk" title="<? echo $rows[BUYER_ID];?>"><p><? echo $buyer_lib[$rows[BUYER_ID]];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $buyer_lib[$rows[CUSTOMER_BUYER]];?></p></td>
	                        <td width="60" class="wrd_brk" align="center"></td>
	                        <td width="100" class="wrd_brk"><p><? echo $rows[BOOKING_NO];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $rows[RECV_NUMBER];?></p></td>
	                        <td width="100"  align="center"><p><? echo $rows[SALES_BOOKING_NO];?></p></td>
	                        <td width="120" class="wrd_brk"><? echo $rows[JOB_NO];?></td>
	                        <td width="100" class="wrd_brk"><p><? echo $rows[STYLE_REF_NO];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $int_ref_arr[$rows[SALES_BOOKING_NO]];?></p></td>
	                        <td width="100" class="wrd_brk" align="center"><? echo $barcode;?></td>
	                        <td width="100" class="wrd_brk" align="center"><p><? echo implode(', ',$color_name_arr);?></p></td>
	                        <td width="60" class="wrd_brk" align="center"><p><? echo implode(', ',$count_name_arr);?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo implode(',',$yarn_comp_arr);?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo implode(',',$yarn_type_arr);?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $rows[YARN_LOT];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $brand_arr[$rows[BRAND_ID]];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $rows[STITCH_LENGTH];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $constructtion_arr[$rows[FEBRIC_DESCRIPTION_ID]];?></p></td>
	                        <td width="200" class="wrd_brk"><p><? echo $composition_arr[$rows[FEBRIC_DESCRIPTION_ID]];?></p></td>
	                        <td width="60" class="wrd_brk" align="center"><p><? echo number_format($rows[WIDTH]);?></p></td>
	                        <td width="60" class="wrd_brk" align="center"><p><? echo $rows[GSM];?></p></td>
	                        <td width="100" class="wrd_brk" align="right"><p><? echo $rows[ROLL_NO];?></p></td>
							<td width="50" class="wrd_brk" title="<? echo $deft_mst_data_arr[$barcode]["qc_result"];?>"><? echo $qc_result_arr[$deft_mst_data_arr[$barcode]["qc_result"]];?></td>
	                        <td width="60" class="wrd_brk" align="right">
								<p>
									<? 
										if($deft_mst_data_arr[$barcode]["qc_result"]==1)
										{
											echo number_format($qc_data_arr[$barcode]["QC_PASS_QTY"],2);$total_qc_pass_qty+=$qc_data_arr[$barcode]["QC_PASS_QTY"];//number_format($qc_pass_qnty,2);$total_qc_pass_qty+=$qc_pass_qnty;
										}
										else {echo "0.00";}
									?>
								</p>
							</td>
							<td width="60" class="wrd_brk" align="right">
								<p>
									<? 
										if($deft_mst_data_arr[$barcode]["qc_result"]==2)
										{
											echo number_format($qc_data_arr[$barcode]["QC_PASS_QTY"],2);$total_qc_held_qty+=$qc_data_arr[$barcode]["QC_PASS_QTY"];
										}
										else {echo "0.00";}
									?>
								</p>
							</td>
	                        <td width="60" class="wrd_brk" align="right"><p><? echo number_format($qc_data_arr[$barcode]["REJECT_QNTY"],2); $total_reject_qty+=$qc_data_arr[$barcode]["REJECT_QNTY"];//number_format($rows[REJECT_QNTY],2);?></p></td>
							<td width="60" class="wrd_brk" align="right"><p><? echo number_format($production_weight,2); $total_production_weight+=$production_weight;//number_format($qc_data_arr[$barcode]["PRODUCTION_WEIGHT"],2); $total_production_weight+=$qc_data_arr[$barcode]["PRODUCTION_WEIGHT"]; //number_format(($rows[QC_PASS_QNTY]+$rows[REJECT_QNTY]),2);$total_production_weight+=$rows[QC_PASS_QNTY]+$rows[REJECT_QNTY];?></p></td>
	                        <?
	                        //foreach($knit_defect_array as $defect_ids=>$defects){
	                        foreach($defectIdArr as $defect_id=>$defect){
	                        	echo '<td width="60" align="right">'.number_format($deft_dtls_data_arr[$barcode][$defect_id]["defect_count"]).'</td>';
							}


	                        /*foreach($knit_defect_array as $defect_id=>$defect){
	                         echo '<td width="60" align="right">'.number_format($deft_dtls_data_arr[$barcode][$defect_id]["defect_count"]).'</td>';
	                        }*/
	                        ?>
	                        <td width="100" class="wrd_brk" align="right"><? echo number_format($deft_mst_data_arr[$barcode]["total_point"],2);?></td>
	                        <td width="60" class="wrd_brk" align="center"><? echo $deft_mst_data_arr[$barcode]["grade"];?></td>
	                        <td width="60" class="wrd_brk" align="center"><? echo fn_number_format($defect_percent,2);?></td>
	                        <td width="100" class="wrd_brk" align="right"><? echo number_format($deft_mst_data_arr[$barcode]["roll_length"],2);?></td>
	                        <td width="60" class="wrd_brk" align="center"><p><a href="javascript:fn_observation('<? echo $barcode;?>')"> View(<? echo $deft_mst_data_arr[$barcode]["ovservation"];?>)</a></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $operator_name_arr[$rows[OPERATOR_NAME]];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $operator_id_card_arr[$rows[OPERATOR_NAME]];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $operator_punch_card_arr[$rows[OPERATOR_NAME]];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $userArr[$rows[INSERTED_BY]];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo date("h:i a", strtotime($rows[INSERT_DATE])) ;?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $deft_mst_data_arr[$barcode]["qc_name"];?></p></td>
	                        <td class="wrd_brk"><p><? echo $deft_mst_data_arr[$barcode]["comments"];?></p></td>
	                        
	                        <td width="50" class="wrd_brk">
								<?
								if($deft_mst_data_arr[$barcode]["qc_result"] ==1){
									echo change_date_format($deft_mst_data_arr[$barcode]["qc_insert_date"]);
								}
								?>
							</td>
	                        <td width="50" class="wrd_brk">
								<?
								if($deft_mst_data_arr[$barcode]["qc_result"] ==1){
									echo date("h:i A", strtotime($deft_mst_data_arr[$barcode]["qc_insert_date"]));
								}
								?>
							</td>
							<td width="50" class="wrd_brk" align="center">
							<?

								if($deft_mst_data_arr[$barcode]["qc_result"] ==1)
								{

									$start = date("H:i", strtotime($deft_mst_data_arr[$barcode]["qc_insert_date"]));

									$sql_s = "SELECT shift_name,start_time,end_time from shift_duration_entry where production_type=1 and status_active=1 and is_deleted=0";
									$sql_s_res = sql_select($sql_s);
									$shift_name_arr = array();
									foreach ($sql_s_res as $val_s)
									{
										$currentTime = strtotime($start);
										$startTime = strtotime($val_s[csf("start_time")]);
										$endTime = strtotime($val_s[csf("end_time")]);
										//echo $start.'='.$startTime.'='.$endTime;
										if(
												(
												$startTime < $endTime &&
												$currentTime >= $startTime &&
												$currentTime <= $endTime
												) ||
												(
												$startTime > $endTime && (
												$currentTime >= $startTime ||
												$currentTime <= $endTime
												)
												)
										){
											array_push($shift_name_arr,$shift_name[$val_s[csf('shift_name')]]);
											//echo $shift_name[$val_s[csf('shift_name')]];
										}
									}
									echo implode(",",array_filter(array_unique($shift_name_arr)));

									
									/*$starth = date("H", strtotime($deft_mst_data_arr[$barcode]["qc_insert_date"]));


									$first_shift_start_time = 0;
									$first_shift_end_time = 0;
									$second_shift_start_time = 0;
									$second_shift_end_time = 0;
									$third_shift_start_time = 0;
									$third_shift_end_time = 0;

									$first_shift_start_htime = $shift_wise_time_array[1]['start_time'];
									$first_shift_start_time = strtotime($shift_wise_time_array[1]['start_time']);
									$first_shift_end_time = strtotime($shift_wise_time_array[1]['end_time']);

									$second_shift_start_htime = $shift_wise_time_array[2]['start_time'];
									$second_shift_start_time = strtotime($shift_wise_time_array[2]['start_time']);
									$second_shift_end_time = strtotime($shift_wise_time_array[2]['end_time']);

									$third_shift_start_htime = $shift_wise_time_array[3]['start_time'];
									$third_shift_start_time = strtotime($shift_wise_time_array[3]['start_time']);
									$third_shift_end_time = strtotime($shift_wise_time_array[3]['end_time']);

									
									
									//echo $start.">=".$second_shift_start_time ."&&". $start."<=".$second_shift_end_time."<br>";

									$first_shift_start_htime = explode(':',$first_shift_start_htime);
									$second_shift_start_htime = explode(':',$second_shift_start_htime);
									$third_shift_start_htime = explode(':',$third_shift_start_htime);

									//echo $starth.">=".$second_shift_start_htime[0]."<br>";

									if($first_shift_start_htime[0]<12)
									{
										if($start>=$first_shift_start_time && $start<=$first_shift_end_time)
										{
											echo "A";
										}
									}
									elseif($starth>12 && $first_shift_start_htime[0]>12)
									{
										if($start>=$first_shift_start_time && $start>=$first_shift_end_time)
										{
											echo "A";
										}
									}
									else
									{
										if($start<=$first_shift_start_time && $start<=$first_shift_end_time)
										{
											echo "A";
										}
									}

									if($second_shift_start_htime[0]<12)
									{
										if($start>=$second_shift_start_time && $start<=$second_shift_end_time)
										{
											echo "B";
										}
									}
									elseif($starth>12 && $second_shift_start_htime[0]>12)
									{
										if($start>=$second_shift_start_time && $start>=$second_shift_end_time)
										{
											echo "B";
										}
									}
									else
									{
										if($start<=$second_shift_start_time && $start<=$second_shift_end_time)
										{
											echo "B";
										}
									}

									if($third_shift_start_htime[0]<12)
									{
										if($start>=$third_shift_start_time && $start<=$third_shift_end_time)
										{
											echo "C";
										}
									}
									elseif($starth>12 && $third_shift_start_htime[0]>12)
									{
										if($start>=$second_shift_start_time && $start>=$second_shift_end_time)
										{
											echo "C";
										}
									}
									else
									{
										if($start<=$third_shift_start_time && $start<=$third_shift_end_time)
										{
											echo "C";
										}
									} */

								}
								?>
							</td>
							<td width="50" class="wrd_brk">
								<?
									echo change_date_format($rcv_data_arr[$barcode]["insert_date"]);
								?>
							</td>
	                        <td width="50" class="wrd_brk">
								<?
								if($rcv_data_arr[$barcode]["insert_date"] !=""){
									echo date("h:i A", strtotime($rcv_data_arr[$barcode]["insert_date"]));
								}
								?>
							</td>
	                    </tr>
						<?
						$i++;
					}
                    ?>
                </tbody>
            </table>
            </div>
            <table class="rpt_table" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tfoot>
                    <th width="35"></th>
                    <th width="80"><!--Date--></th>
                    <th width="100"><!--Knitting Source--></th>
                    <th width="100"><!--Knitting Source--></th>
                    <th width="100"><!--Knitting Company--></th>
                    <!--<th width="60">QC M/C No</th>-->
                    <th width="60"><!--Knit M/C No--></th>
                    <th width="60"><!--M/C DIA--></th>
                    <th width="60"><!--M/C GAUGE--></th>
                    <th width="60"><!--SHIFT--></th>
                    <th width="100"><!--BUYER--></th>
                    <th width="100"><!--customer BUYER--></th>
                    <th width="60"></th>
                    <th width="100"><!--Program No--></th>
                    <th width="100"><!--Production Id--></th>
                    <th width="100"><!--Booking No--></th>
                    <th width="120"><!--FSO No--></th>
                    <th width="100"><!--Style Ref.--></th>
                    <th width="100"><!--Int Ref.--></th>
                    <!--<th width="100">Booking Type--></th>
                    <th width="100"><!--Barcode--></th>
                    <th width="100"><!--Fabric Color--></th>
                    <th width="60"><!--YARN COUNT--></th>
                    <th width="100"><!--Yarn Composition--></th>
                    <th width="100"><!--Yarn Type--></th>
                    <th width="100"><!--YARN LOT--></th>
                    <th width="100"><!--Brand--></th>
                    <th width="100"><!--Stitch Length--></th>
                    <th width="100"><!--FABRIC Construction--></th>
                    <th width="200"><!--FABRIC Composition--></th>
                    <th width="60"><!--Dia--></th>
                    <th width="60"><!--GSM--></th>
                    <th width="100"><!--Roll No--></th>
                    <th width="50"><!--Result--></th>
                    <th width="60" align="right" id="total_qc_pass_qty"><? echo number_format($total_qc_pass_qty,2); ?></th>
                    <th width="60" align="right" id="total_qc_held_qty"><? echo number_format($total_qc_held_qty,2); ?></th>
                    <th width="60" align="right" id="total_reject_qty"><? echo number_format($total_reject_qty,2);//number_format(array_sum($reject_qty_summary_arr),2);?></th>
					<th width="60" id="total_production_weight"><? echo number_format($total_production_weight,2); ?></th>
                    <?
                    //foreach($knit_defect_array as $defect_id=>$defect){
                    foreach($defectIdArr as $defect_id=>$defect){
                     echo '<th width="60" align="right">'.number_format(array_sum($deft_dtls_summary_data_arr["defect_count"][$defect_id]),2).'</th>';
                    }
                    ?>
                    <th width="100" align="right"><? echo number_format(array_sum($deft_mst_summary_data_arr["total_point"]),2);?></th>
                    <th width="60"><!--GRADE--></th>
                    <th width="60"><!--DEFECT %--></th>
                    <th width="100"><!--LENGTH YDS--></th>
                    <th width="60"><!--Observation--></th>
                    <th width="100"><!--Operator Name--></th>
                    <th width="100"><!--Operator Id--></th>
                    <th width="100"><!--Operator Panch Card--></th>
                    <th width="100"><!--Prod. Insert User--></th>
                    <th width="100"><!--Prod. Insert Time--></th>
                    <th width="100"><!--QC Name--></th>
                    <th><!--Comments--></th>
                    <th width="50"><!--QC date--></th>
                    <th width="50"><!--QC time--></th>
                    <th width="50"><!--Shift--></th>
                    <th width="50"><!--RCV date--></th>
                    <th width="50"><!--RCV time--></th>
            </tfoot>
        </table>
  	</fieldset>
	<?

	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	ob_end_clean();

	$filename="../../../ext_resource/tmp_report/".$user_name."_".time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);

	echo "$html####$filename####$report_type";

	disconnect($con);
	exit();
}


if($action=="generate_report_summary")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_knitting_source = str_replace("'","",$cbo_knitting_source);
	$cbo_knitting_company = str_replace("'","",$cbo_knitting_company);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$cbo_year = str_replace("'","",$cbo_year);
	$txt_sales_order_no = str_replace("'","",$txt_sales_order_no);
	$txt_barcode = str_replace("'","",$txt_barcode);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_floor = str_replace("'","",$cbo_floor);
	$cbo_machine_name = str_replace("'","",$cbo_machine_name);
	$cbo_shift_name = str_replace("'","",$cbo_shift_name);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);



	$knit_defect_array = array(1 => "Hole", 5 => "Loop", 10 => "Press Off", 15 => "Lycra Out", 20 => "Lycra Drop",30 => "Oil Spot", 35 => "Fly Conta", 40 => "Slub",70 => "Yarn Contra",  85 => "Oil/Ink Mark", 90 => "Set up", 95 => "Pin Hole", 100 => "Slub Hole",110 => "Miss Yarn", 115 => "Color Contra [Yarn]",140 => "Dirty Spot",150 => "Stop mark",165 => "Grease spot", 166 => "Knot", 167 => "Tara");



 	$booking_type_arr=array(118=>"Main Fabric Booking",108=>"Partial Fabric Booking",88=>"Short Fabric Booking",89=>"Sample Fabric Booking With Order",90=>"Sample Fabric Booking Without Order");


	$company_lib=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_lib=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
	$buyer_lib=return_library_array( "select id,short_name from lib_buyer","id","short_name");

	$machine_lib=return_library_array( "select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0","id","machine_no");

	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");




	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from), '','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}
	}

	if($cbo_company_name>0){
		$where_con=" and a.company_id=$cbo_company_name";
	}

	if($cbo_knitting_company>0){
		$where_con.=" and a.knitting_company=$cbo_knitting_company";
	}

	if($cbo_knitting_source>0){
		$where_con.=" and a.knitting_source=$cbo_knitting_source";
	}

	if($cbo_floor>0){
		$where_con.=" and b.FLOOR_ID=$cbo_floor";
	}

	if($cbo_buyer_id>0){
		$where_con.=" and a.BUYER_ID=$cbo_buyer_id";
	}

	if($cbo_shift_name>0){
		$where_con.=" and b.SHIFT_NAME='$cbo_shift_name'";
	}

	if($txt_sales_order_no !=""){
		$where_con.=" and d.JOB_NO like('%$txt_sales_order_no')";
	}

	if($cbo_machine_name>0){
		$where_con.=" and b.MACHINE_NO_ID=$cbo_machine_name";
	}

	if($cbo_year != 0){
		if($db_type==0){$where_con=" and year(a.insert_date)=$cbo_year";}
		else if($db_type==2){$where_con=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}


	if($txt_barcode != ''){
		$where_con=" and c.BARCODE_NO='$txt_barcode'";
	}



	//---------------------------
	$sql="select a.BUYER_ID,a.KNITTING_SOURCE,a.KNITTING_COMPANY,a.RECEIVE_DATE,a.REMARKS,a.BOOKING_TYPE,b.GSM,b.WIDTH,b.SHIFT_NAME,b.YARN_LOT,b.BRAND_ID,b.MACHINE_DIA,b.MACHINE_GG,b.MACHINE_NO_ID,b.YARN_COUNT,b.COLOR_ID,b.STITCH_LENGTH,b.OPERATOR_NAME,b.FEBRIC_DESCRIPTION_ID,b.YARN_PROD_ID,c.BARCODE_NO,c.ROLL_NO,c.QC_PASS_QNTY,c.REJECT_QNTY,c.QNTY,d.JOB_NO,d.SALES_BOOKING_NO,d.STYLE_REF_NO,d.ENTRY_FORM from inv_receive_master a,pro_grey_prod_entry_dtls b,pro_roll_details c,fabric_sales_order_mst d where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id $where_con $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.IS_SALES=1 and c.ENTRY_FORM=2"; //,d.BOOKING_TYPE
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows)
	{
		$key=$rows[YARN_PROD_ID].'*'.$rows[JOB_NO];

		$key_wise_barcode_no_arr[$key][$rows[BARCODE_NO]]=$rows[BARCODE_NO];
		$barcode_wise_key_arr[$rows[BARCODE_NO]]=$key;


		$dataArr[$key]=$rows;
		$fabric_des_id_arr[$rows[FEBRIC_DESCRIPTION_ID]]=$rows[FEBRIC_DESCRIPTION_ID];
		$barcode_no_arr[$rows[BARCODE_NO]]=$rows[BARCODE_NO];
		if($rows[YARN_PROD_ID]){$yarn_pro_id_arr[$rows[YARN_PROD_ID]]=$rows[YARN_PROD_ID];}

		$qc_pass_qty_summary_arr[$key]+=$rows[QC_PASS_QNTY];
		$reject_qty_summary_arr[$key]+=$rows[REJECT_QNTY];
		$production_qty_summary_arr[$key]+=$rows[QNTY];
	}


	//--------------------------------

	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$fabric_des_id_list_arr=array_chunk($fabric_des_id_arr,999);
	$p=1;
	foreach($fabric_des_id_list_arr as $fabric_des_id_process)
	{
		if($p==1) $sql_deter .="  and ( a.id in(".implode(',',$fabric_des_id_process).")";
		else  $sql_deter .=" or a.id in(".implode(',',$fabric_des_id_process).")";

		$p++;
	}
	$sql_deter .=")";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	//------------------------------------------
	$deft_sql="SELECT a.BARCODE_NO,a.TOTAL_POINT,a.FABRIC_GRADE,a.QC_NAME,a.COMMENTS,a.ROLL_LENGTH,a.ROLL_WEIGHT,a.ACTUAL_DIA,a.ACTUAL_GSM,b.DEFECT_NAME, b.DEFECT_COUNT, b.FOUND_IN_INCH, b.FOUND_IN_INCH_POINT, b.PENALTY_POINT,b.FORM_TYPE from PRO_QC_RESULT_mst a,PRO_QC_RESULT_DTLS b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 ";

	$barcode_no_list_arr=array_chunk($barcode_no_arr,999);
	$p=1;
	foreach($barcode_no_list_arr as $barcode_no_process)
	{
		if($p==1) $deft_sql .="  and ( a.BARCODE_NO in(".implode(',',$barcode_no_process).")";
		else  $deft_sql .=" or a.BARCODE_NO in(".implode(',',$barcode_no_process).")";

		$p++;
	}
	$deft_sql .=")";

	$deft_sql_result = sql_select($deft_sql);
	$deft_dtls_data_arr = array();
	$deft_mst_data_arr = array();$deft_mst_summary_data_arr = array();
	$tem_barcode = array();$tem_barcode2 = array();
	foreach ($deft_sql_result as $rows)
	{
		$key=$barcode_wise_key_arr[$rows[BARCODE_NO]];
		if($tem_barcode[$rows[BARCODE_NO]]=='')
		{
			if($rows[FORM_TYPE]==2 && $rows[FOUND_IN_INCH]>0)
			{
				$deft_mst_data_arr[$key]["ovservation"] += 1;
			}
			/*else
			{
				$deft_mst_data_arr[$key]["grade"] = $rows[FABRIC_GRADE];
				$deft_mst_data_arr[$key]["qc_name"] = $rows[QC_NAME];
				$deft_mst_data_arr[$key]["comments"] = $rows[COMMENTS];
				$deft_mst_data_arr[$key]["roll_length"] = $rows[ROLL_LENGTH];
				$deft_mst_data_arr[$key]["roll_weight"] = $rows[ROLL_WEIGHT];
				$deft_mst_data_arr[$key]["dia"] = $rows[ACTUAL_DIA];
				$deft_mst_data_arr[$key]["gsm"] = $rows[ACTUAL_GSM];
				$deft_mst_data_arr[$key]["total_point"] = +$rows[TOTAL_POINT];
				$deft_mst_summary_data_arr["total_point"][$key] += $rows[TOTAL_POINT];
			}*/
			$tem_barcode[$rows[BARCODE_NO]]=1;
		}

		if($rows[FORM_TYPE]!=2)
		{
			if($tem_barcode2[$rows[BARCODE_NO]]=='')
			{
				$deft_mst_data_arr[$key]["grade"] = $rows[FABRIC_GRADE];
				$deft_mst_data_arr[$key]["qc_name"] = $rows[QC_NAME];
				$deft_mst_data_arr[$key]["comments"] = $rows[COMMENTS];
				$deft_mst_data_arr[$key]["roll_length"] = $rows[ROLL_LENGTH];
				$deft_mst_data_arr[$key]["roll_weight"] = $rows[ROLL_WEIGHT];
				$deft_mst_data_arr[$key]["dia"] = $rows[ACTUAL_DIA];
				$deft_mst_data_arr[$key]["gsm"] = $rows[ACTUAL_GSM];
				$deft_mst_data_arr[$key]["total_point"] = +$rows[TOTAL_POINT];
				$deft_mst_summary_data_arr["total_point"][$key] += $rows[TOTAL_POINT];

				$tem_barcode2[$rows[BARCODE_NO]]=1;
			}

			$deft_dtls_data_arr[$key][$rows[DEFECT_NAME]]["defect_count"] += $rows[DEFECT_COUNT];
			$deft_dtls_summary_data_arr["defect_count"][$rows[DEFECT_NAME]][$key] += $rows[DEFECT_COUNT];
		}
	}
	//--------------------------

	$yarn_des_sql="select ID,YARN_COMP_TYPE1ST,YARN_TYPE from PRODUCT_DETAILS_MASTER where status_active=1 and is_deleted=0 ";

	$yarn_pro_id_list_arr=array_chunk($yarn_pro_id_arr,999);
	$p=1;
	foreach($yarn_pro_id_list_arr as $yarn_pro_id_process)
	{
		if($p==1) $yarn_des_sql .="  and ( id in(".implode(',',$yarn_pro_id_process).")";
		else  $yarn_des_sql .=" or id in(".implode(',',$yarn_pro_id_process).")";

		$p++;
	}
	$yarn_des_sql .=")";
	$yarn_des_result = sql_select($yarn_des_sql);
	foreach ($yarn_des_result as $rows) {
		$yarn_comp_data_arr[$rows[ID]] = $composition[$rows[YARN_COMP_TYPE1ST]];
		$yarn_type_data_arr[$rows[ID]] = $yarn_type[$rows[YARN_TYPE]];
	}


	$width=(count($knit_defect_array)*60)+1920;
	$colspan=count($knit_defect_array)+37;

	ob_start();
	?>
        <fieldset style="width:<? echo $width;?>px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
            	<tr>
                    <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:14px"><strong><? echo $company_lib[$cbo_company_name]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:12px"><strong></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:12px"><strong>Daily Roll wise Knitting QC Report [Summary]</strong></td>
                </tr>
            </table>
            <table id="table_header_1" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" align="left" >
                <thead>
                    <th width="35">SL</th>
                    <th width="100">Knitting Source</th>
                    <th width="100">Knitting Company</th>
                    <th width="60">MC NO</th>
                    <th width="60">M/C DIA</th>
                    <th width="60">M/C GAUGE</th>
                    <th width="100">BUYER</th>
                    <th width="100">Booking No</th>
                    <th width="120">FSO No</th>
                    <th width="100">Style Ref.</th>
                    <th width="60">Stitch Length</th>
                    <th width="100">Fab. Construction</th>
                    <th width="200">Fab. Composition</th>
                    <th width="60">Dia</th>
                    <th width="60">GSM</th>
                    <?
                        foreach($knit_defect_array as $defect_id=>$defect){
                            echo '<th width="60">'.$defect.'</th>';
                        }
                    ?>
                    <th width="60">TTL POINTS</th>
                    <th width="60">GRADE</th>
                    <th width="60">DEFECT %</th>
                    <th width="60">LENGTH (YDS)</th>
                    <th width="60">Production Qty.</th>
                    <th width="60">Qc Pass Qty</th>
                    <th width="60">Reject Qty</th>
                    <th width="60">No of Roll</th>
                    <th>Observation</th>
                </thead>
            </table>
			<div style="width:<? echo $width+18;?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" id="table_body" align="left">
                    <tbody>
                        <?
						$i=1;
						foreach($dataArr as $key=>$rows){
							$company_arr=($rows[KNITTING_SOURCE]==1)?$company_lib:$supplier_lib;
							$bgcolor=($i%2==0)? "#E9F3FF" : "#FFFFFF";

							/*
							foreach($key_wise_barcode_no_arr[$key] as $barcode){
								$deft_data_arr["total_point"][$key]+=$deft_mst_data_arr[$barcode]["total_point"];
								$deft_data_arr["grade"][$key]=$deft_mst_data_arr[$barcode]["grade"];
							}
							*/

							$yarn_des_arr=array();
							foreach(explode(',',$rows[YARN_PROD_ID]) as $pro_id){
								$yarn_comp_arr[$pro_id]=$yarn_comp_data_arr[$pro_id];
								$yarn_type_arr[$pro_id]=$yarn_type_data_arr[$pro_id];
							}



							$defect_percent=($deft_mst_data_arr[$key]["total_point"]*36*100)/($rows[WIDTH]*$deft_mst_data_arr[$key]["roll_length"]);




						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35" align="center"><? echo $i;?></td>
                            <td width="100"><p><? echo $knitting_source[$rows[KNITTING_SOURCE]];?></p></td>
                            <td width="100"><p><? echo $company_arr[$rows[KNITTING_COMPANY]];?></p></td>
                            <td width="60" align="center"><p><? echo $machine_lib[$rows[MACHINE_NO_ID]*1];?></p></td>
                            <td width="60" align="center"><p><? echo $rows[MACHINE_DIA];?></p></td>
                            <td width="60" align="center"><p><? echo $rows[MACHINE_GG];?></p></td>
                            <td width="100"><p><? echo $buyer_lib[$rows[BUYER_ID]];?></p></td>
                            <td width="100" align="center"><p><? echo $rows[SALES_BOOKING_NO];?></p></td>
                            <td width="120"><p><? echo $rows[JOB_NO];?></p></td>
                            <td width="100"><p><? echo $rows[STYLE_REF_NO];?></p></td>
                            <td width="60" align="center"><p><? echo $rows[STITCH_LENGTH];?></td>
                            <td width="100"><p><? echo $constructtion_arr[$rows[FEBRIC_DESCRIPTION_ID]];?></p></td>
                            <td width="200"><p><? echo $composition_arr[$rows[FEBRIC_DESCRIPTION_ID]];?></p></td>
                            <td width="60" align="center"><p><? echo number_format($rows[WIDTH],2);?></p></td>
                            <td width="60" align="center"><p><? echo $rows[GSM];?></p></td>
                            <?
                                foreach($knit_defect_array as $defect_id=>$defect){
									echo '<td width="60" align="right">'.number_format($deft_dtls_data_arr[$key][$defect_id]["defect_count"],2).'</td>';
                                }
                            ?>
                            <td width="60" align="right"><p><? echo number_format($deft_mst_data_arr[$key]["total_point"],2);?></p></td>
                            <td width="60" align="center"><p><? echo $deft_mst_data_arr[$key]["grade"];?></p></td>
                            <td width="60" align="center"><p><? echo fn_number_format($defect_percent,2);?></p></td>
                            <td width="60" align="right"><p><? echo number_format($deft_mst_data_arr[$key]["roll_length"],2);?></p></td>
                            <td width="60" align="right"><p><? echo number_format($production_qty_summary_arr[$key],2);?></p></td>
                            <td width="60" align="right"><p><? echo number_format($qc_pass_qty_summary_arr[$key],2);?></p></td>
                            <td width="60" align="right"><p><? echo number_format($reject_qty_summary_arr[$key],2);?></p></td>
                            <td width="60" align="center"><p><? echo $rows[ROLL_NO];?></p></td>
                            <td align="center"><a href="javascript:fn_observation('<? echo implode(',',$key_wise_barcode_no_arr[$key]);?>')"> View </a></td>
                        </tr>
						<?
						$i++;
						}

                        ?>
                    </tbody>
                </table>
                </div>
                <table class="rpt_table" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <tfoot>
                        <th width="35"><!--SL--></th>
                        <th width="100"><!--Knitting Source--></th>
                        <th width="100"><!--Knitting Company--></th>
                        <th width="60"><!--MC NO--></th>
                        <th width="60"><!--M/C DIA--></th>
                        <th width="60"><!--M/C GAUGE--></th>
                        <th width="100"><!--BUYER--></th>
                        <th width="100"><!--Booking No--></th>
                        <th width="120"><!--FSO No--></th>
                        <th width="100"><!--Style Ref.--></th>
                        <th width="60"><!--Stitch Length--></th>
                        <th width="100"><!--Fab. Construction--></th>
                        <th width="200"><!--Fab. Composition--></th>
                        <th width="60"><!--Dia--></th>
                        <th width="60"><!--GSM--></th>
                        <?
                            foreach($knit_defect_array as $defect_id=>$defect){
                                echo '<th width="60" align="right">'.number_format(array_sum($deft_dtls_summary_data_arr["defect_count"][$defect_id]),2).'</th>';
                            }
                        ?>
                        <th width="60"><? echo number_format(array_sum($deft_mst_summary_data_arr["total_point"]),2);?></th>
                        <th width="60"><!--GRADE--></th>
                        <th width="60"><!--DEFECT %--></th>
                        <th width="60"><!--LENGTH (YDS)--></th>
                        <th width="60" align="right"><? echo number_format(array_sum($production_qty_summary_arr),2);?></th>
                        <th width="60" align="right"><? echo number_format(array_sum($qc_pass_qty_summary_arr),2);?></th>
                        <th width="60"><!--Reject Qty--></th>
                        <th width="60"><!--No of Roll--></th>
                        <th><!--Observation--></th>
                	</tfoot>
            	</table>
      </fieldset>
	<?

	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	ob_end_clean();

	$filename="../../../ext_resource/tmp_report/".$user_name."_".time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);

	echo "$html####$filename";

	disconnect($con);
	exit();
}

if($action=="generate_report2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_knitting_source = str_replace("'","",$cbo_knitting_source);
	$cbo_knitting_company = str_replace("'","",$cbo_knitting_company);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$cbo_year = str_replace("'","",$cbo_year);
	$txt_sales_order_no = str_replace("'","",$txt_sales_order_no);
	$txt_style_ref = str_replace("'","",$txt_style_ref);
	$txt_barcode = str_replace("'","",$txt_barcode);
	$txt_ref_no = str_replace("'","",$txt_ref_no);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_floor = str_replace("'","",$cbo_floor);
	$cbo_machine_name = str_replace("'","",$cbo_machine_name);
	$cbo_shift_name = str_replace("'","",$cbo_shift_name);
	$cbo_search_type = str_replace("'","",$cbo_search_type);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$txt_program_no = str_replace("'","",$txt_program_no);
	// echo $txt_sales_order_no;die;

	$knit_defect_array = array(1 => "Hole", 5 => "Loop", 90 => "Set up", 21 => "Lycra Cut", 15 => "Lycra Out", 20 => "Lycra Drop", 110 => "Miss Yarn", 30 => "Oil Spot", 168 =>"Contamination", 105 => "Needle Mark", 50 => "Needle Broken", 55 => "Sinker Mark", 175 =>"Line Star", 95 => "Pin Hole", 60 => "Wheel Free", 45 => "Patta", 169 =>"Thick and Thin", 25 => "Dust", 65 => "Count Mix", 70 => "Yarn Contra", 115 => "Color Contra [Yarn]", 75 => "NEPS", 85 => "Oil/Ink Mark", 150 => "Stop mark", 166 => "Knot", 167 => "Tara", 40 => "Slub", 80 => "Black Spot", 140 => "Dirty Spot", 35 => "Fly Conta", 10 => "Press Off", 100 => "Slub Hole", 120 => "Color/dye spot", 125 => "friction mark" ,130 => "Pin out", 135 => "softener spot", 145 => "Rust Stain", 155 => "Compacting Broken", 160 => "Insect Spot", 165 => "Grease spot");

	$observation_status_arr=array(1=>"Present",2=>"Not Found", 3=>"Major",4=>"Minor",5=>"Acceptable",6=>"Good");

 	$booking_type_arr=array(118=>"Main Fabric Booking",108=>"Partial Fabric Booking",88=>"Short Fabric Booking",89=>"Sample Fabric Booking With Order",90=>"Sample Fabric Booking Without Order");
	$qc_result_arr=array(1=>'QC Pass', 2=>'Held Up', 3=>'Reject');

	$company_lib=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_lib=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
	$buyer_lib=return_library_array( "select id,short_name from lib_buyer","id","short_name");

	$machine_lib=return_library_array( "select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0","id","machine_no");

	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");

	$con = connect();
	$r_id1=execute_query("delete from tmp_booking_id where userid=$user_name");
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_name");
	$r_id3=execute_query("delete from tmp_prod_id where userid=$user_name");
	$r_id4=execute_query("delete from tmp_recv_dtls where userid=$user_name");

	if($r_id1 && $r_id2 && $r_id3 && $r_id4)
	{
		oci_commit($con);
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
			$qc_pass_date_cond="and e.qc_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from), '','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
			$qc_pass_date_cond="and e.qc_date between '".change_date_format(trim($txt_date_from), '','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}
	}

	if($cbo_company_name>0){
		$where_con=" and a.company_id=$cbo_company_name";
	}

	if($cbo_knitting_company>0){
		$where_con.=" and a.knitting_company=$cbo_knitting_company";
	}

	if($cbo_knitting_source>0){
		$where_con.=" and a.knitting_source=$cbo_knitting_source";
	}

	if($cbo_floor>0){
		$where_con.=" and b.FLOOR_ID=$cbo_floor";
	}

	if($cbo_buyer_id>0){
		$where_con.=" and a.BUYER_ID=$cbo_buyer_id";
	}

	if($cbo_shift_name>0){
		$where_con.=" and b.SHIFT_NAME='$cbo_shift_name'";
	}

	if($txt_sales_order_no !=""){
		$where_con.=" and (d.JOB_NO like('%$txt_sales_order_no') or c.booking_no ='$txt_sales_order_no')";
	}

	if($txt_style_ref !=""){
		$where_con.=" and d.style_ref_no ='$txt_style_ref' ";
	}

	if($cbo_machine_name>0){
		$where_con.=" and b.MACHINE_NO_ID=$cbo_machine_name";
	}

	if($txt_program_no !=""){
		$where_con.=" and a.booking_no ='$txt_program_no' ";
	}


	if($cbo_year != 0){
		if($db_type==0){$where_con.=" and year(a.insert_date)=$cbo_year";}
		else if($db_type==2){$where_con.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}

	if($txt_barcode != ''){
		$where_con.=" and c.BARCODE_NO=$txt_barcode";
	}
	// echo $where_con;
	if($txt_ref_no!='')
	{
		$int_ref_sql="SELECT a.booking_no, a.booking_mst_id , b.grouping from wo_booking_dtls a, wo_po_break_down b
		where a.po_break_down_id=b.id  and a.status_active=1 and b.status_active=1 and b.grouping='$txt_ref_no'
		group by a.booking_no, a.booking_mst_id, b.grouping";
		$int_ref_sql_result=sql_select($int_ref_sql);
		$all_booking_id='';
		foreach($int_ref_sql_result as $row)
		{
			if($all_booking_id=="") $all_booking_id=$row[csf('booking_mst_id')]; else $all_booking_id.=",".$row[csf('booking_mst_id')];
		}

		$bookingIds=chop($all_booking_id,','); $booking_cond_for_in="";
		$po_ids=count(array_unique(explode(",",$all_booking_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$booking_cond_for_in=" and (";
			$bookingIdsArr=array_chunk(explode(",",$bookingIds),999);
			foreach($bookingIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$booking_cond_for_in.=" d.booking_id in($ids) or";
			}
			$booking_cond_for_in=chop($booking_cond_for_in,'or ');
			$booking_cond_for_in.=")";
		}
		else
		{
			$booking_cond_for_in=" and d.booking_id in($bookingIds)";
		}
		$booking_order_cond=" and d.book_without_order=0";
	}
	// echo $booking_cond_for_in.'='.$booking_order_cond;die;
	$variable_settingAutoQC = sql_select("select auto_update from variable_settings_production where company_name =$cbo_company_name and variable_list in(47) and item_category_id=13 and is_deleted=0 and status_active=1");
	$autoProductionQuantityUpdatebyQC = $variable_settingAutoQC[0][csf("auto_update")];

	if ($cbo_search_type==2 && $txt_date_from!="" && $txt_date_to!="") // qc pass
	{
		$sql="SELECT a.KNITTING_SOURCE,a.KNITTING_COMPANY,a.RECEIVE_DATE,a.REMARKS,a.BOOKING_TYPE,b.GSM,b.WIDTH,b.SHIFT_NAME,b.YARN_LOT,b.BRAND_ID,b.MACHINE_DIA,b.MACHINE_GG,b.MACHINE_NO_ID,b.YARN_COUNT,b.COLOR_ID,b.STITCH_LENGTH,b.OPERATOR_NAME,b.FEBRIC_DESCRIPTION_ID,b.YARN_PROD_ID,c.BARCODE_NO,c.ROLL_NO,c.QC_PASS_QNTY,c.REJECT_QNTY,d.JOB_NO,d.SALES_BOOKING_NO,d.BOOKING_ID,d.BOOK_WITHOUT_ORDER,d.STYLE_REF_NO,d.ENTRY_FORM,d.PO_BUYER,d.BUYER_ID,A.BOOKING_NO,B.FLOOR_ID, d.CUSTOMER_BUYER, a.RECV_NUMBER
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, PRO_QC_RESULT_mst e, fabric_sales_order_mst d
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.barcode_no=e.barcode_no and c.po_breakdown_id=d.id $where_con $qc_pass_date_cond $booking_cond_for_in $booking_order_cond
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and c.IS_SALES=1 and c.ENTRY_FORM=2 and e.status_active=1 and e.is_deleted=0";
		// echo $sql;
	}
	else
	{
		//---------------------------
		$sql="SELECT a.KNITTING_SOURCE,a.KNITTING_COMPANY,a.RECEIVE_DATE,a.REMARKS,a.BOOKING_TYPE,b.GSM,b.WIDTH,b.SHIFT_NAME,b.YARN_LOT,b.BRAND_ID,b.MACHINE_DIA,b.MACHINE_GG,b.MACHINE_NO_ID,b.YARN_COUNT,b.COLOR_ID,b.STITCH_LENGTH,b.OPERATOR_NAME,b.FEBRIC_DESCRIPTION_ID,b.YARN_PROD_ID,c.BARCODE_NO,c.ROLL_NO,c.QC_PASS_QNTY,c.REJECT_QNTY,d.JOB_NO,d.SALES_BOOKING_NO,d.BOOKING_ID,d.BOOK_WITHOUT_ORDER,d.STYLE_REF_NO,d.ENTRY_FORM,d.PO_BUYER,d.BUYER_ID,A.BOOKING_NO,B.FLOOR_ID, d.CUSTOMER_BUYER, a.RECV_NUMBER
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,fabric_sales_order_mst d
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id $where_con $date_cond $booking_cond_for_in $booking_order_cond
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and c.IS_SALES=1 and c.ENTRY_FORM=2"; //,d.BOOKING_TYPE
	}
	//echo $sql;

	$sql_result=sql_select($sql);
	$dataArr=array();$booking_id_arr=array();
	foreach($sql_result as $rows)
	{
		$company_arr=($rows[KNITTING_SOURCE]==1)?$company_lib:$supplier_lib;
		$dataArr[$rows[BARCODE_NO]]=$rows;
		$fabric_des_id_arr[$rows[FEBRIC_DESCRIPTION_ID]]=$rows[FEBRIC_DESCRIPTION_ID];
		$barcode_no_arr[$rows[BARCODE_NO]]=$rows[BARCODE_NO];

		if($rows[YARN_PROD_ID])
		{
			$yarn_arr = explode(",",$rows[YARN_PROD_ID]);
			foreach ($yarn_arr as  $Yval) {
				$yarn_pro_id_arr[$Yval]=$Yval;
			}

		}

		$qc_pass_qty_summary_arr[$rows[BARCODE_NO]]+=$rows[QC_PASS_QNTY];
		$reject_qty_summary_arr[$rows[BARCODE_NO]]+=$rows[REJECT_QNTY];
		$roll_no_arr[$rows[BARCODE_NO]]+=$rows[ROLL_NO];

		if ($rows[BOOK_WITHOUT_ORDER]==0 && $rows[BOOKING_ID] !="")
		{
			$booking_id_arr[$rows[BOOKING_ID]]=$rows[BOOKING_ID];
		}
	}
	// echo "<pre>";print_r($booking_id_arr);
	if(count($booking_id_arr)>0)
	{
		foreach ($booking_id_arr as $Bval) {
			$rID2=execute_query("insert into tmp_booking_id (userid, booking_id) values ($user_name,$Bval)");
		}
		if($rID2)
		{
		    oci_commit($con);
		}
	}
	// echo $booking_ids_cond.'=';die;

	if(!empty($barcode_no_arr))
	{
		foreach ($barcode_no_arr as $Bval) {
			$rID3=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,$Bval)");
		}
		if($rID3)
		{
		    oci_commit($con);
		}
	}

	if (count($booking_id_arr)>0)
	{
		$int_ref_sql="SELECT A.BOOKING_NO, A.BOOKING_MST_ID , B.GROUPING from wo_booking_dtls a, wo_po_break_down b, tmp_booking_id c
		where a.po_break_down_id=b.id  and a.status_active=1 and b.status_active=1 and a.booking_mst_id=c.booking_id and c.userid=$user_name
		group by a.booking_no, a.booking_mst_id, b.grouping"; //$booking_ids_cond
		$int_ref_sql_result=sql_select($int_ref_sql);
		$int_ref_arr=array();
		foreach($int_ref_sql_result as $row)
		{
			$int_ref_arr[$row[BOOKING_NO]]=$row['GROUPING'];
		}
		// echo "<pre>";print_r($int_ref_arr);
	}

	//--------------------------------

	if(!empty($fabric_des_id_arr))
	{
		foreach ($fabric_des_id_arr as $Bval) {
			$rID4=execute_query("insert into tmp_recv_dtls (userid, dtls_id) values ($user_name,$Bval)");
		}
		if($rID4)
		{
		    oci_commit($con);
		}


		$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, tmp_recv_dtls c where a.id=b.mst_id and a.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.userid=$user_name";
		//echo $sql_deter;die;
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row) {
			$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
			$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}
	}


	//------------------------------------------

	$deft_sql="SELECT a.BARCODE_NO,a.TOTAL_POINT,a.FABRIC_GRADE,a.QC_NAME,a.COMMENTS,a.ROLL_LENGTH,a.ROLL_WEIGHT,a.REJECT_QNTY,a.ACTUAL_DIA,a.ACTUAL_GSM,a.ROLL_STATUS, a.IS_TAB,b.DEFECT_NAME, b.DEFECT_COUNT, b.FOUND_IN_INCH, b.FOUND_IN_INCH_POINT, b.PENALTY_POINT, b.FORM_TYPE, b.FOUND_IN_INCH, a.insert_date as INSERT_DATE, a.QC_DATE from PRO_QC_RESULT_MST a left join PRO_QC_RESULT_DTLS b on a.id=b.mst_id and b.status_active=1 and b.is_deleted=0, tmp_barcode_no c where  a.status_active=1 and a.is_deleted=0 and a.barcode_no=c.barcode_no and c.userid=$user_name";

	// echo $deft_sql;die;

	$deft_sql_result = sql_select($deft_sql);
	$deft_dtls_data_arr = array();
	$deft_mst_data_arr = array();
	$qc_found_barcode_arr = array();
	foreach ($deft_sql_result as $rows)
	{
		if($rows[FORM_TYPE]==2)
		{
			//$deft_mst_data_arr[$rows[BARCODE_NO]]["ovservation"][$rows[FOUND_IN_INCH]]= $observation_status_arr[$rows[FOUND_IN_INCH]];
			if($rows[FOUND_IN_INCH]>0)$deft_mst_data_arr[$rows[BARCODE_NO]]["ovservation"]+= 1;
		}
		else
		{
			$deft_dtls_data_arr[$rows[BARCODE_NO]][$rows[DEFECT_NAME]]["defect_count"] = $rows[DEFECT_COUNT];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["total_point"] = $rows[TOTAL_POINT];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["grade"] = $rows[FABRIC_GRADE];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["qc_name"] = $rows[QC_NAME];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["comments"] = $rows[COMMENTS];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["roll_length"] = $rows[ROLL_LENGTH];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["roll_weight"] = $rows[ROLL_WEIGHT];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["dia"] = $rows[ACTUAL_DIA];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["gsm"] = $rows[ACTUAL_GSM];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["qc_result"] = $rows[ROLL_STATUS];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["qc_insert_date"] = $rows[INSERT_DATE];
			$deft_mst_data_arr[$rows[BARCODE_NO]]["qc_date"] = $rows[QC_DATE];

			$deft_dtls_summary_data_arr["defect_count"][$rows[DEFECT_NAME]][$rows[BARCODE_NO]] = $rows[DEFECT_COUNT];
			$deft_mst_summary_data_arr["total_point"][$rows[BARCODE_NO]] = $rows[TOTAL_POINT];
		}
		$qc_found_barcode_arr[$rows[BARCODE_NO]] = $rows[BARCODE_NO];

		if($rows[IS_TAB]==1) // when qc from Tab
		{
			$qc_data_arr[$rows[BARCODE_NO]]["QC_PASS_QTY"] = $rows[ROLL_WEIGHT]-$rows[REJECT_QNTY];
			$qc_data_arr[$rows[BARCODE_NO]]["REJECT_QNTY"] = $rows[REJECT_QNTY];
			$qc_data_arr[$rows[BARCODE_NO]]["PRODUCTION_WEIGHT"] = $rows[ROLL_WEIGHT];
		}
		else // when qc from QC Result page
		{
			$qc_data_arr[$rows[BARCODE_NO]]["QC_PASS_QTY"] = $rows[ROLL_WEIGHT];
			$qc_data_arr[$rows[BARCODE_NO]]["REJECT_QNTY"] = $rows[REJECT_QNTY];
			$qc_data_arr[$rows[BARCODE_NO]]["PRODUCTION_WEIGHT"] = $rows[ROLL_WEIGHT]+$rows[REJECT_QNTY];
		}
	}
	// echo "<pre>";print_r($qc_data_arr);
	//--------------------------

	//------------------------------------------
	$rcv_sql="SELECT a.barcode_no as BARCODE_NO, a.insert_date as INSERT_DATE
	from pro_roll_details a, pro_grey_prod_entry_dtls b, tmp_barcode_no c
	where a.dtls_id=b.id and b.trans_id!=0 and a.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.barcode_no=c.barcode_no and c.userid=$user_name";

	$rcv_sql_result = sql_select($rcv_sql);
	foreach ($rcv_sql_result as $rows)
	{
		$rcv_data_arr[$rows[BARCODE_NO]]["insert_date"] = $rows[INSERT_DATE];
	}
	//--------------------------


	if(!empty($yarn_pro_id_arr))
	{
		foreach ($yarn_pro_id_arr as $Bval) {
			$rID5=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_name,$Bval)");
		}
		if($rID5)
		{
		    oci_commit($con);
		}
	}


	$yarn_des_sql="select a.ID,a.YARN_COMP_TYPE1ST,a.YARN_TYPE from PRODUCT_DETAILS_MASTER a, tmp_prod_id b where a.id=b.prod_id and b.userid=$user_name and a.status_active=1 and a.is_deleted=0 ";

	$yarn_des_result = sql_select($yarn_des_sql);
	foreach ($yarn_des_result as $rows) {
		$yarn_comp_data_arr[$rows[ID]] = $composition[$rows[YARN_COMP_TYPE1ST]];
		$yarn_type_data_arr[$rows[ID]] = $yarn_type[$rows[YARN_TYPE]];
	}


	$r_id1=execute_query("delete from tmp_booking_id where userid=$user_name");
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_name");
	$r_id3=execute_query("delete from tmp_prod_id where userid=$user_name");
	$r_id4=execute_query("delete from tmp_recv_dtls where userid=$user_name");

	if($r_id1 && $r_id2 && $r_id3 && $r_id4)
	{
		oci_commit($con);
	}

	$defectArr=array();$defectIdArr=array();
	foreach($dataArr as $barcode=>$rows)
	{
	 	foreach($knit_defect_array as $defect_ids=>$defects)//array defect list
	 	{
	 		if($deft_dtls_data_arr[$barcode][$defect_ids]["defect_count"]>0)
	 		{
 				if($defectArr[$defect_ids]!=$defect_ids)
	 			{
	 				$defectIdArr[$defect_ids]=$defects;
	 				$defectArr[$defect_ids]=$defect_ids;
	 			}
	 		}
        }
	}


	$defectIdArr=array_intersect($knit_defect_array,$defectIdArr);
	// echo "<pre>";print_r($defectIdArr);die;
	$width=(count($defectIdArr)*60)+1340;
	$colspan=count($defectIdArr)+9;

	ob_start();
	?>
	<style>
		#table_body {
		counter-reset: serial-number;  /* Set the serial number counter to 0 */
		}



		.wrd_brk{word-break: break-all;word-wrap: break-word;}

	</style>
    <fieldset style="width:<? echo $width;?>px;">
    	<table cellpadding="0" cellspacing="0" width="100%">
        	<tr>
                <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:14px"><strong><? echo $company_lib[$cbo_company_name]; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:12px"><strong></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="<? echo $colspan;?>" style="font-size:12px"><strong>Daily Roll and Style wise Knitting QC Report</strong></td>
            </tr>
        </table>
		<br>
		<div style="padding-bottom:100px;">
			<div style="float: left; margin-left: 15px;">
				<table width="270" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<tbody>
						<tr>
							<td bgcolor="#AFCCF2" width="100">Production Date</td>
							<td bgcolor="#FFFFFF"><? echo change_date_format($sql_result[0]["RECEIVE_DATE"]);?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Knitting company</td>
							<td bgcolor="#FFFFFF"><? echo $company_arr[$sql_result[0]["KNITTING_COMPANY"]];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2"">Knitting Source</td>
							<td bgcolor="#FFFFFF"><? echo $knitting_source[$sql_result[0]["KNITTING_SOURCE"]];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Buyer</td>
							<td bgcolor="#FFFFFF"><? echo $buyer_lib[$sql_result[0]["PO_BUYER"]];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Style No</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["STYLE_REF_NO"];?></td>
						</tr>

					</tbody>
				</table>
			</div>
			<div style="float: left; margin-left: 15px;">
				<table width="270" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<tbody>
						<tr>
							<td bgcolor="#AFCCF2" width="100">M/C No</td>
							<td bgcolor="#FFFFFF"><? echo $machine_lib[$sql_result[0]["MACHINE_NO_ID"]*1];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">M/C Dia</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["MACHINE_DIA"];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">M/C Gauge</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["MACHINE_GG"];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">GSM</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["GSM"];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">F. Dia</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["WIDTH"];?></tr>

					</tbody>
				</table>
			</div>

			<div style="float: left; margin-left: 15px;">
				<table width="270" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<tbody>
						<tr>
							<td bgcolor="#AFCCF2" width="100">Y.Lot</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["YARN_LOT"];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Y.comp</td>
							<td bgcolor="#FFFFFF">
								<?
								$yarn_comp_arr=array();$yarn_type_arr=array();
								foreach(explode(',',$sql_result[0]["YARN_PROD_ID"]) as $pro_id){
									$yarn_comp_arr[$pro_id]=$yarn_comp_data_arr[$pro_id];
									$yarn_type_arr[$pro_id]=$yarn_type_data_arr[$pro_id];
								}
								echo implode(',',$yarn_comp_arr);?>
							</td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Y.Type</td>
							<td bgcolor="#FFFFFF"><? echo implode(',',$yarn_type_arr);?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Y. Brand</td>
							<td bgcolor="#FFFFFF"><? echo $brand_arr[$sql_result[0]["BRAND_ID"]];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Y.Count</td>
							<td bgcolor="#FFFFFF">
								<?
								$count_name_arr=array();
								foreach(explode(',',$sql_result[0]["YARN_COUNT"]) as $count_id){
									$count_name_arr[$count_id]=$count_arr[$count_id];
								}
								echo implode(',',$count_name_arr);?></td>
						</tr>

					</tbody>
				</table>
			</div>
			<div style="float: left; margin-left: 15px;">
				<table width="270" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<tbody>
						<tr>
							<td bgcolor="#AFCCF2" width="100">Program No</td>
							<td bgcolor="#FFFFFF"><? echo $txt_program_no;?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Booking</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["SALES_BOOKING_NO"];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2"">Shift</td>
							<td bgcolor="#FFFFFF"><? echo $shift_name[$sql_result[0]["SHIFT_NAME"]];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">S.L</td>
							<td bgcolor="#FFFFFF"><? echo $sql_result[0]["STITCH_LENGTH"];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">Color</td>
							<td bgcolor="#FFFFFF">
								<?
								$color_name_arr=array();
								foreach(explode(',',$rows["COLOR_ID"]) as $color_id){
									$color_name_arr[$color_id]=$color_lib[$color_id];
								}
								echo implode(',',$color_name_arr);?></td>
						</tr>

					</tbody>
				</table>
			</div>
			<div style="float: left; margin-left: 15px;">
				<table width="270" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<tbody>
						<tr>
							<td bgcolor="#AFCCF2" width="100">F.Cons</td>
							<td bgcolor="#FFFFFF"><? echo $constructtion_arr[$sql_result[0]["FEBRIC_DESCRIPTION_ID"]];?></td>
						</tr>
						<tr>
							<td bgcolor="#AFCCF2">F.Comp</td>
							<td bgcolor="#FFFFFF"><? echo $composition_arr[$sql_result[0]["FEBRIC_DESCRIPTION_ID"]];?></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>

        <table id="table_header_1" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" align="left" >
            <thead>
                <th width="35">SL</th>
                <th width="100" class="wrd_brk">Barcode</th>

                <th width="60" class="wrd_brk">Roll No</th>
                <th width="70" class="wrd_brk">Qc Pass Qty</th>
                <th width="60" class="wrd_brk">Reject Qty</th>
				<th width="60" class="wrd_brk">Production WEIGHT</th>

                <?
                foreach($defectIdArr as $defect_id=>$defect){
                 echo '<th width="60" class="wrd_brk">'.$defect.'</th>';
                }
                ?>

                <th width="80" class="wrd_brk">GRADE</th>
				<th width="60" class="wrd_brk">TTL POINTS</th>
                <th width="60" class="wrd_brk">DEFECT %</th>
                <th width="100" class="wrd_brk">LENGTH YDS</th>
                <th width="100">Operator Name</th>
                <th width="100" class="wrd_brk">QC Name</th>
                <th class="wrd_brk" width="50">Result</th>
                <th class="wrd_brk" width="100">QC Date</th>
                <th class="wrd_brk">Comments</th>
            </thead>
        </table>
		<div style="width:<? echo $width+20;?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" id="table_body" align="left" >
                <tbody >
                    <?
					$i=1;
					foreach($dataArr as $barcode=>$rows)
					{

						$bgcolor=($i%2==0)? "#E9F3FF" : "#FFFFFF";
						$color_name_arr=array();
						foreach(explode(',',$rows["COLOR_ID"]) as $color_id){
							$color_name_arr[$color_id]=$color_lib[$color_id];
						}

						$count_name_arr=array();
						foreach(explode(',',$rows["YARN_COUNT"]) as $count_id){
							$count_name_arr[$count_id]=$count_arr[$count_id];
						}

						$yarn_comp_arr=array();$yarn_type_arr=array();
						foreach(explode(',',$rows["YARN_PROD_ID"]) as $pro_id){
							$yarn_comp_arr[$pro_id]=$yarn_comp_data_arr[$pro_id];
							$yarn_type_arr[$pro_id]=$yarn_type_data_arr[$pro_id];
						}


						if($rows["BOOKING_TYPE"] == 4)
						{
							if($rows[csf('is_order')] == 1){
								$booking_type = "Sample With Order";
							}
							else
							{
								$booking_type = "Sample Without Order";
							}
						}
						else
						{
							$booking_type = $booking_type_arr[$rows["ENTRY_FORM"]];
						}



						$defect_percent=($deft_mst_data_arr[$barcode]["total_point"]*36*100)/($rows["WIDTH"]*$deft_mst_data_arr[$barcode]["roll_length"]);


						$rows["BUYER_ID"]=($rows["PO_BUYER"]=="")?$rows["BUYER_ID"]:$rows["PO_BUYER"];

						if($qc_found_barcode_arr[$barcode]!="")
						{
							$qc_pass_qnty=$rows["QC_PASS_QNTY"];
						}
						else
						{
							$qc_pass_qnty=0.00;
						}

						if ($autoProductionQuantityUpdatebyQC==1)
						{
							$production_weight=$rows[QC_PASS_QNTY]+$rows[REJECT_QNTY];
						}
						else
						{
							$production_weight=$rows[QC_PASS_QNTY];
						}

						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="35" class="cls"><? echo $i; ?></td>
	                        <td width="100" class="wrd_brk" align="center"><? echo $barcode;?></td>
	                        <td width="60" class="wrd_brk" align="right"><p><? echo $rows["ROLL_NO"];?></p></td>

	                        <td width="70" class="wrd_brk" align="right"><p><? echo number_format($qc_data_arr[$barcode]["QC_PASS_QTY"],2); $total_qc_pass_qty+=$qc_data_arr[$barcode]["QC_PASS_QTY"];//number_format($qc_pass_qnty,2);$total_qc_pass_qty+=$qc_pass_qnty;?></p></td>
	                        <td width="60" class="wrd_brk" align="right"><p><? echo number_format($qc_data_arr[$barcode]["REJECT_QNTY"],2); //number_format($rows["REJECT_QNTY"],2);?></p></td>
							<td width="60" class="wrd_brk" align="right"><p><? echo number_format($production_weight,2); $total_production_weight+=$production_weight;//number_format($qc_data_arr[$rows[BARCODE_NO]]["PRODUCTION_WEIGHT"],2); $total_production_weight+=$qc_data_arr[$rows[BARCODE_NO]]["PRODUCTION_WEIGHT"];//number_format(($rows["QC_PASS_QNTY"]+$rows["REJECT_QNTY"]),2);$total_production_weight+=$rows["QC_PASS_QNTY"]+$rows["REJECT_QNTY"];?></p></td>
	                        <?
	                        //foreach($knit_defect_array as $defect_ids=>$defects){
	                        foreach($defectIdArr as $defect_id=>$defect){
	                        	echo '<td width="60" align="right">'.number_format($deft_dtls_data_arr[$barcode][$defect_id]["defect_count"]).'</td>';
							}


	                        /*foreach($knit_defect_array as $defect_id=>$defect){
	                         echo '<td width="60" align="right">'.number_format($deft_dtls_data_arr[$barcode][$defect_id]["defect_count"]).'</td>';
	                        }*/
	                        ?>
							 <td width="80" class="wrd_brk" align="center"><? echo $deft_mst_data_arr[$barcode]["grade"];?></td>
	                        <td width="60" class="wrd_brk" align="right"><? echo number_format($deft_mst_data_arr[$barcode]["total_point"],2);?></td>
	                        <td width="60" class="wrd_brk" align="center"><? echo fn_number_format($defect_percent,2);?></td>
	                        <td width="100" class="wrd_brk" align="right"><? echo number_format($deft_mst_data_arr[$barcode]["roll_length"],2);?></td>
	                        <td width="100" class="wrd_brk"><p><? echo $operator_name_arr[$rows["OPERATOR_NAME"]];?></p></td>
	                        <td width="100" class="wrd_brk"><p><? echo $deft_mst_data_arr[$barcode]["qc_name"];?></p></td>
	                        <td width="50" class="wrd_brk"><? echo $qc_result_arr[$deft_mst_data_arr[$barcode]["qc_result"]];?></td>
	                        <td width="100" class="wrd_brk"><? echo change_date_format($deft_mst_data_arr[$barcode]["qc_date"]);?></td>
	                        <td class="wrd_brk"><p><? echo $deft_mst_data_arr[$barcode]["comments"];?></p></td>

	                    </tr>
						<?
						$i++;
					}
                    ?>
                </tbody>
            </table>
            </div>
            <table class="rpt_table" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tfoot>
					<tr>
						<th width="35"></th>
						<th width="100"><!--Barcode--></th>
						<th width="60"><b>Total:</b></th>
						<th width="70" align="right"><? echo number_format($total_qc_pass_qty,2); ?></th>
						<th width="60" align="right"><? echo number_format($qc_data_arr[$barcode]["REJECT_QNTY"],2); //number_format(array_sum($reject_qty_summary_arr),2);?></th>
						<th width="60"><? echo number_format($total_production_weight,2); ?></th>
						<?
						//foreach($knit_defect_array as $defect_id=>$defect){
						foreach($defectIdArr as $defect_id=>$defect){
						echo '<th width="60" align="right">'.number_format(array_sum($deft_dtls_summary_data_arr["defect_count"][$defect_id]),2).'</th>';
						}
						?>

						<th width="80"><!--GRADE--></th>
						<th width="60" align="right"><? echo number_format(array_sum($deft_mst_summary_data_arr["total_point"]),2);?></th>
						<th width="60"><!--DEFECT %--></th>
						<th width="100"><!--LENGTH YDS--></th>
						<th width="100"><!--Operator Name--></th>
						<th width="100"><!--QC Name--></th>
						<th width="50"><!--Result--></th>
						<th width="100"><!--QC Date--></th>
						<th><!--Comments--></th>
					</tr>
					<tr>
						<th width="35"></th>
						<th width="100"><!--Barcode--></th>
						<th width="60"><!--Roll No--></th>
						<th width="70" align="right"></th>
						<th width="60" align="right"></th>
						<th width="60"></th>
						<?
						//foreach($knit_defect_array as $defect_id=>$defect){
						foreach($defectIdArr as $defect_id=>$defect){
						echo '<th width="60" align="right"></th>';
						}
						?>
						<th width="80"><b>AVG. Points</b></th>
						<th width="60" align="right">
							<?
							$total_point= array_sum($deft_mst_summary_data_arr["total_point"]);
							$total_point_count= count($deft_mst_summary_data_arr["total_point"]);
							echo number_format($total_point/$total_point_count,2);
							?>
						</th>
						<th width="60"><!--DEFECT %--></th>
						<th width="100"><!--LENGTH YDS--></th>
						<th width="100"><!--Operator Name--></th>
						<th width="100"><!--QC Name--></th>
						<th width="50"><!--Result--></th>
						<th width="100"><!--QC Date--></th>
						<th><!--Comments--></th>
					</tr>
            	</tfoot>
        </table>
  	</fieldset>
	<?

	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	ob_end_clean();

	$filename="../../../ext_resource/tmp_report/".$user_name."_".time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);

	echo "$html####$filename";

	disconnect($con);
	exit();
}


if ($action == "observation_popup") {
	echo load_html_head_contents("Order Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$observation_status_arr=array(1=>"Present",2=>"Not Found", 3=>"Major",4=>"Minor",5=>"Acceptable",6=>"Good");
	$knit_ovservation_defect_array = array(500=>"Needle Mark",501=>"Sinker Mark",502=>"Patta",503=>"Carling",504=>"Dia Mark",505=>"Oil/ink Mark",506=>"Bend Mark",507=>"Wheel Free",508=>"Belt Free",509=>"Crease Mark",510=>"Needle Broken",511=>"Double Yarn",512=>"Lot Mix",513=>"Count Mix",514=>"Date Mix",515=>"Machanical Work",516=>"Program Change",517=>"NEPS",518=>"Line Star",519=>"Lycra Cotton");


		$deft_sql="select a.BARCODE_NO,a.TOTAL_POINT,a.FABRIC_GRADE,a.QC_NAME,a.COMMENTS,a.ROLL_LENGTH,a.ROLL_WEIGHT,a.ACTUAL_DIA,a.ACTUAL_GSM,a.ROLL_STATUS,b.DEFECT_NAME, b.DEFECT_COUNT, b.FOUND_IN_INCH, b.FOUND_IN_INCH_POINT, b.PENALTY_POINT,b.FORM_TYPE,b.FOUND_IN_INCH from PRO_QC_RESULT_mst a,PRO_QC_RESULT_DTLS b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.BARCODE_NO in($barcode) and b.FORM_TYPE=2 and b.FOUND_IN_INCH >0 ";


		$deft_sql_result = sql_select($deft_sql);
		foreach ($deft_sql_result as $rows) {
			$dataArr[$rows[BARCODE_NO]][]=$rows;
		}

		?>

          <table id="table_header_1" cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" align="left" >
            <thead>
                <th width="35">SL</th>
                <th>Name</th>
                <th>Status</th>
            </thead>
            <?
		$i=1;
		foreach ($dataArr as $barcode=>$barcodeWiseDataArr) {
			echo "<tr bgcolor='#CCCCCC'>
					<td colspan='3'>Barcode: ".$barcode."</td>
				</tr>
				";
			foreach ($barcodeWiseDataArr as $rows) {
				$bgcolor=($i%2==0)? "#E9F3FF" : "#FFFFFF";
				echo "<tr bgcolor='".$bgcolor."'>
						<td align='center'>$i</td>
						<td>".$knit_ovservation_defect_array[$rows[DEFECT_NAME]]."</td>
						<td>".$observation_status_arr[$rows[FOUND_IN_INCH]]."</td>
					</tr>
					";
				$i++;
			}
		}
	?>
    	</table>
    <?



exit();
}


if($action=="sales_order_no_search_popup")
{
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(job_no)
		{
			document.getElementById('hidden_job_no').value=job_no;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:0px;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Within Group</th>
							<th>Search By</th>
							<th>Search No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
								<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">

							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", $cbo_within_group,$dd,0 );
								?>
							</td>
							<td align="center">
								<?
								$serach_type_arr=array(1=>'Sales Order No',2=>'Fab. Booking No',3=>'Program No');
								echo create_drop_down( "cbo_serach_type", 150, $serach_type_arr,"",0, "--Select--","","",0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" placeholder="Write" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('hidden_yearID').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'daily_roll_wise_knitting_qc_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
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

if($action=="create_sales_order_no_search_list")
{
	$data 			= explode('_',$data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id,short_name from lib_buyer","id","short_name");


	if($db_type==0)
	{
		if($yearID!=0) $year_cond=" and YEAR(a.insert_date)=$yearID"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($yearID!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$yearID";  else $year_cond="";
	}

	$within_group_cond  = ($within_group == 0)?"":" and a.within_group=$within_group";
	if($serach_type==1)
	{
		$sales_order_cond   = ($sales_order_no == "")?"":" and a.job_no like '%$sales_order_no%'";
	}
	else if($serach_type==2)
	{
		$sales_order_cond   = ($sales_order_no == "")?"":" and a.sales_booking_no like '%$sales_order_no%'";
	}
	else if($serach_type==3)
	{
		$sales_order_cond   = ($sales_order_no == "")?"":" and b.dtls_id like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2)? "to_char(a.insert_date,'YYYY') as year":"YEAR(a.insert_date) as year";

	if($serach_type==3)
	{
		$sql = "SELECT a.id, to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.dtls_id as program_no
		from fabric_sales_order_mst a, PPL_PLANNING_ENTRY_PLAN_DTLS b
		where a.id=b.po_id and a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond $year_cond order by a.id ";
	}
	else
	{
		$sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond $year_cond order by a.id";
	}
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="60">Sales Order ID</th>
			<th width="60" title="When Search By Program No">Program No</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="70">Booking date</th>
			<th width="40">Year</th>
			<th width="40">Within Group</th>
			<th width="110">Buyer/Unit</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:890px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1){
					$buyer=$company_arr[$row[csf('buyer_id')]];
				}else{
					$buyer=$buyer_arr[$row[csf('buyer_id')]];
				}
				if($serach_type==3)
				{
					$sales_order_no = $row[csf('program_no')];
				}
				else
				{
					$sales_order_no = $row[csf('job_no')];
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="60" align="center"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p>&nbsp;<? echo $row[csf('program_no')]; ?></p></td>
					<td width="110" align="center"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="70" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="40" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="110" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>
					<td width="110" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
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



?>

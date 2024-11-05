<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

if ($action == "load_drop_down_knitting_com") 
{
	$data = explode("_", $data);
	$company_id = $data[1];

	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "");
	} else {
		echo create_drop_down("cbo_knitting_company", 152, $blank_array, "", 1, "--Select Knit Company--", 0, "");
	}
	exit();
}
//--------------------------------------------------------------------------------------------------------------------



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "string".$type; 
	
	$company_name=str_replace("'","",$cbo_company_name);
	$type=str_replace("'","",$type);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_knitting_company=str_replace("'","",$cbo_knitting_company);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$date_drop_down=str_replace("'","",$cbo_date_drop_down);
	$txt_barcode_no=str_replace("'","",$txt_barcode_no);
	$cbo_year=str_replace("'","",$cbo_year);
	$report_title=str_replace("'","",$report_title);
	if($cbo_year!=0)
	{
		if($db_type==0) $year_cond="and year(c.insert_date)='$cbo_year'"; 
		else if($db_type==2) $year_cond="and to_char(c.insert_date,'YYYY')='$cbo_year'";	
	}
	
	
	//$txt_season="%".trim(str_replace("'","",$txt_season))."%";
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	
	$sql_cond="";
	if($cbo_knitting_company!=0) $sql_cond=" and e.knitting_company='$cbo_knitting_company'";
	if($txt_job_no!="") $sql_cond.=" and b.job_no_mst like '%$txt_job_no%'";
	if($txt_order_no!="") $sql_cond.=" and b.po_number like '%$txt_order_no%'";

	if($db_type==0) 
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$date_from=change_date_format($date_from,'','',1);
		$date_to=change_date_format($date_to,'','',1);
	}
	else  
	{
		$date_from=""; $date_to="";
	}
	if($date_from!=="" and $date_to!=="")
	{	
		if($db_type==0) 
		{
			$sql_cond.=" and a.insert_date between '$date_from' and '$date_to'";
		}else{
			//$sql_cond.=" and a.insert_date between '$date_from' and '$date_to'";
			$sql_cond.=" and a.insert_date between '$date_from' and '$date_to 11:59:59 PM'";
		}
	}
	/*if($date_drop_down==1)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$sql_cond.=" and a.insert_date between '$date_from' and '$date_to'";
		}
	}*/
	/*if($date_drop_down==3)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$sql_cond.=" and a.insert_date between '$date_from' and '$date_to'";
		}
	}*/
	

	/*if($txt_date_from!="") $sql_cond.=" and a.insert_date='$txt_date_from'";
	if($txt_date_to!="") $sql_cond.=" and a.insert_date='$txt_date_to'";*/


	$bar_code_cond="";
	if($txt_barcode_no!="") $bar_code_cond=" and a.barcode_no='$txt_barcode_no'";
	$style_ref_cond="";
	if($txt_style_ref_no!="") $style_ref_cond=" and c.style_ref_no='$txt_style_ref_no'";
	/*echo $sql_cond;
	die;*/
 
	$barcode_cond_prod="";
	if($txt_barcode_no!="") $barcode_cond_prod=" and barcode_no='$txt_barcode_no'";

 
	$machine_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
	$machine_data=array();
	foreach($machine_sql as $row)
	{
		$machine_data[$row[csf("id")]]["machine_no"]=$row[csf("machine_no")];
		$machine_data[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
		$machine_data[$row[csf("id")]]["gauge"]=$row[csf("gauge")];
	}
 
	
	$composition_arr=array();
	$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls");
	foreach( $compositionData as $row )
	{
		$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	} 

	if($cbo_buyer_name>0) $sql_cond.=" and c.buyer_name=$cbo_buyer_name";
	$sql="SELECT p.color_range_id, p.body_part_id, a.barcode_no, p.febric_description_id, p.gsm, p.width as dia, p.stitch_length, p.machine_no_id, a.id as roll_id, a.roll_no, a.entry_form, a.insert_date, a.qnty as grey_qnty, a.booking_no, b.id as po_id, b.po_number, b.file_no, b.grouping, c.id as job_id, c.buyer_name, c.job_no_prefix_num, c.job_no, c.style_ref_no, b.grouping, e.knitting_company, e.receive_basis, e.knitting_source
	from pro_grey_prod_entry_dtls p, pro_roll_details a, wo_po_break_down b, wo_po_details_master c, inv_receive_master e
	where p.id=a.dtls_id and a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and e.id = a.mst_id and a.entry_form in(2,22) and a.roll_id=0 and a.status_active=1 and a.is_deleted=0 and a.booking_without_order!=1 and c.company_name=$company_name  $sql_cond $bar_code_cond $style_ref_cond $year_cond order by c.id, b.id, a.id,e.knitting_company"; //die;

	$nameArray=sql_select( $sql);

	foreach ($nameArray as $val) 
	{
		$po_no_arr[$val[csf("po_id")]] = $val[csf("po_id")];
		$receive_basis = $val[csf("receive_basis")];
		$knitting_source = $val[csf("knitting_source")];

		$all_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		//$booking_no_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
	}
	/*echo '<pre>';
	print_r($booking_no_arr);
	echo '</pre>';die;*/
	
	$inhouse_party_sql="SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name";
	$inhousePartyArray = sql_select( $inhouse_party_sql);
	foreach ($inhousePartyArray as $row) 
	{
		$inhouse_party_arr[$row[csf("id")]] = $row[csf("company_name")]; 
	}
	

	$outBounceParty_sql="SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
	$outBouncePartyArray=sql_select( $outBounceParty_sql);
	foreach ($outBouncePartyArray as $row) 
	{
		$out_bound_party_arr[$row[csf("id")]] = $row[csf("supplier_name")]; 
	}
	



	if ($receive_basis==1) //F. Booking
	{
		$booking_sql = "SELECT b.insert_date as booking_insert_date, a.booking_no, b.booking_no
		from pro_roll_details a, wo_booking_mst b where a.booking_no=b.booking_no 
		group by b.insert_date, a.booking_no, b.booking_no";
		$booking_data=sql_select( $booking_sql);
		foreach ($booking_data as $val) 
		{
			$booking_date_arr[$val[csf("booking_no")]] = $val[csf("booking_insert_date")];
		}
	}
	if ($receive_basis==2) //F. Knit Plan
	{
		$booking_sql = "SELECT a.id, b.id as program_no, a.booking_no, e.insert_date as booking_insert_date
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_mst e
		where a.id=b.mst_id and a.booking_no=e.booking_no
		group by a.id, b.id , a.booking_no, e.insert_date";
		$booking_data=sql_select( $booking_sql);
		foreach ($booking_data as $val) 
		{
			$booking_date_arr[$val[csf("program_no")]] = $val[csf("booking_insert_date")];
		}

		/*echo '<pre>';
		print_r($booking_date_arr);
		echo '</pre>';die;*/
	}

	$qc_sql="SELECT a.id as roll_id, a.po_breakdown_id, d.id, d.barcode_no, d.fabric_grade, d.insert_date, d.update_date, d.roll_status, e.knitting_company, e.receive_basis
	FROM pro_grey_prod_entry_dtls p, pro_roll_details a, wo_po_break_down b, wo_po_details_master c, pro_qc_result_mst d, inv_receive_master e
	WHERE p.id=a.dtls_id and a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.barcode_no=d.barcode_no and e.id=a.mst_id and a.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and c.company_name=$company_name $sql_cond $bar_code_cond $style_ref_cond $year_cond
	ORDER BY c.id, b.id, a.id";
	$qc_sql_res=sql_select($qc_sql);

	$fabric_knitting_company_grade_arr = array();
	$summary_count_fabric_grade_arr = array();
	foreach ($qc_sql_res as $val) 
	{
		$qc_barcode_arr[$val[csf("barcode_no")]]["barcode_no"] = $val[csf("barcode_no")];
		$qc_fabric_grade[$val[csf("barcode_no")]] = $val[csf("fabric_grade")];
		$barcode_arr[$val[csf("roll_id")]][$val[csf("po_breakdown_id")]]["barcode_no"]=$val[csf("barcode_no")];
		$status[$val[csf("roll_id")]][$val[csf("po_breakdown_id")]]["roll_status"]=$val[csf("roll_status")]; 

		$qc_pass_insert_arr[$val[csf("barcode_no")]] = $val[csf("insert_date")];
		$rejected_insert_arr[$val[csf("barcode_no")]] = $val[csf("insert_date")];
	//	echo  $val[csf("barcode_no")].'='.$val[csf("insert_date")].'<br>';
		if($val[csf("insert_date")]!="")
		{
		$held_up_insert_arr[$val[csf("barcode_no")]] = $val[csf("insert_date")];
		}

		$qc_pass_update_arr[$val[csf("barcode_no")]] = $val[csf("update_date")];
		$rejected_update_arr[$val[csf("barcode_no")]] = $val[csf("update_date")];
		if($val[csf("update_date")]!="")
		{
		$held_up_update_arr[$val[csf("barcode_no")]] = $val[csf("update_date")];
		}
		
		$fabric_knitting_company_grade_arr[$val[csf("knitting_company")]][$val[csf("fabric_grade")]]++;  
	}
	//die;
//print_r($held_up_insert_arr);
	/*echo '<pre>';
	print_r($fabric_knitting_company_grade_arr);
	echo '</pre>';die;*/

	$challan_sql="SELECT a.mst_id, a.barcode_no, a.po_breakdown_id, a.roll_id, b.sys_number as challan_no
	from pro_roll_details a, pro_grey_prod_delivery_mst b
	where b.company_id=$company_name $bar_code_cond and a.mst_id=b.id and a.entry_form=56 and b.entry_form=56 ";
	$challan_sql_res=sql_select($challan_sql);
	foreach ($challan_sql_res as $row) 
	{
		$challan_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["challan_no"]=$row[csf("challan_no")];
	}	
	/*echo '<pre>';
	print_r($challan_arr);die;*/

	$all_barcode_nos = implode(",", array_filter(array_unique($all_barcode_arr)));
	if($all_barcode_nos=="") $all_barcode_nos=0;
	$barCond = $all_barcode_cond = ""; 
	$all_barcode_arr=explode(",",$all_barcode_nos);
	if($db_type==2 && count($all_barcode_arr)>999)
	{
		$all_barcode_chunk=array_chunk($all_barcode_arr,999) ;
		foreach($all_barcode_chunk as $chunk_arr)
		{
			$barCond.=" barcode_no in(".implode(",",$chunk_arr).") or ";	
		}
				
		$all_barcode_cond.=" and (".chop($barCond,'or ').")";			
		
	}
	else
	{ 
		$all_barcode_cond=" and barcode_no in($all_barcode_nos)";
	}


	$sqlbatch = sql_select("select  a.batch_no, a.batch_date as system_date,b.barcode_no from pro_batch_create_mst a , pro_roll_details b where a.id = b.mst_id and b.entry_form =64 and b.barcode_no<>0  and a.status_active =1  and b.status_active =1 $all_barcode_cond  order by b.insert_date desc");

	$batachData =array();
	foreach($sqlbatch as $row)
	{
		$batachData[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
	}

	$position_sql=sql_select("SELECT roll_id, po_breakdown_id, 
	max(case when entry_form=56 then roll_id else 0 end) as grey_delivery,
	max(case when entry_form=56 then insert_date else null end) as grey_delivery_date,
	max(case when entry_form=58 then roll_id else 0 end) as grey_rcv_store,
	max(case when entry_form=58 then insert_date else null end) as grey_rcv_store_date,
	max(case when entry_form=61 and is_returned=0 then roll_id else 0 end) as grey_issue_batch,
	sum(case when entry_form=61 and is_returned=0 then qnty else 0 end) as batch_issue_qnty,
	max(case when entry_form=61 and is_returned=0 then insert_date else null end) as grey_issue_batch_date,
	max(case when entry_form=62 then roll_id else 0 end) as grey_rcv_batch,
	max(case when entry_form=62 then insert_date else null end) as grey_rcv_batch_date,
	max(case when entry_form=64 then roll_id else 0 end) as batch_created,
	sum(case when entry_form=64 then qnty else 0 end) as batch_create_qnty,
	max(case when entry_form=64 then insert_date else null end) as batch_created_date,
	max(case when entry_form=66 then roll_id else 0 end) as finishion,
	max(case when entry_form=66 then insert_date else null end) as finishion_date,
	sum(case when entry_form=66 then qc_pass_qnty else 0 end) as finishion_qnty,
	max(case when entry_form=67 then roll_id else 0 end) as fin_delivery,
	max(case when entry_form=67 then insert_date else null end) as fin_delivery_date,
	max(case when entry_form=68 then roll_id else 0 end) as fin_rcv_store,
	max(case when entry_form=68 then insert_date else null end) as fin_rcv_store_date,
	max(case when entry_form=71 then roll_id else 0 end) as fin_issu_cut,
	max(case when entry_form=71 then insert_date else null end) as fin_issu_cut_date,
	max(case when entry_form=72 then roll_id else 0 end) as fin_receive_cut,
	max(case when entry_form=72 then insert_date else null end) as fin_receive_cut_date,
	max(case when entry_form=83 then roll_id else 0 end) as transfer_roll, 
	max(case when entry_form=83 and re_transfer = 0 then po_breakdown_id else null end) as trans_id_po,
	max(case when entry_form=2 then roll_id else 0 end) as production_roll_id,
	max(case when entry_form=2 then insert_date else null end) as production_date_time
	from pro_roll_details where status_active=1 and is_deleted=0 and roll_id>0 $barcode_cond_prod $all_barcode_cond  group by roll_id, po_breakdown_id");
	
	
	$batch_issue_qtny_arr=$batch_creat_qnty_arr=$roll_data_arr=array();
	foreach($position_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_delivery"]=$row[csf("grey_delivery")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_delivery_date"]=$row[csf("grey_delivery_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_store"]=$row[csf("grey_rcv_store")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_store_date"]=$row[csf("grey_rcv_store_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_issue_batch"]=$row[csf("grey_issue_batch")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_issue_batch_date"]=$row[csf("grey_issue_batch_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch"]=$row[csf("grey_rcv_batch")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch_date"]=$row[csf("grey_rcv_batch_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["batch_created"]=$row[csf("batch_created")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["batch_created_date"]=$row[csf("batch_created_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion"]=$row[csf("finishion")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion_date"]=$row[csf("finishion_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion_qnty"]=$row[csf("finishion_qnty")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_delivery"]=$row[csf("fin_delivery")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_delivery_date"]=$row[csf("fin_delivery_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_rcv_store"]=$row[csf("fin_rcv_store")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_rcv_store_date"]=$row[csf("fin_rcv_store_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_issu_cut"]=$row[csf("fin_issu_cut")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_issu_cut_date"]=$row[csf("fin_issu_cut_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_receive_cut"]=$row[csf("fin_receive_cut")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_receive_cut_date"]=$row[csf("fin_receive_cut_date")];

		$batch_issue_qtny_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]+=$row[csf("batch_issue_qnty")];
		$batch_creat_qnty_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]+=$row[csf("batch_create_qnty")];	
	}

	$knit_pro_sql=sql_select("SELECT id, po_breakdown_id,
	max(case when entry_form in(2,22) then id else 0 end) as production_roll_id,
	max(case when entry_form in(2,22) then insert_date else null end) as production_date_time
	from pro_roll_details where status_active=1 and is_deleted=0 $barcode_cond_prod $all_barcode_cond  group by id, po_breakdown_id");
	$knit_pro_arr=array();
	foreach($knit_pro_sql as $row)
	{
		$knit_pro_arr[$row[csf("id")]][$row[csf("po_breakdown_id")]]["production_roll_id"]=$row[csf("production_roll_id")];
		$knit_pro_arr[$row[csf("id")]][$row[csf("po_breakdown_id")]]["production_date_time"]=$row[csf("production_date_time")];
	}
	/*echo '<pre>';
	print_r($knit_pro_arr);	*/

function durationDaysHourMin($firstDate, $secondDate)
	{
		$seconds = strtotime($firstDate) - strtotime($secondDate);
		$days    = floor($seconds / 86400);
		$hours   = floor(($seconds - ($days * 86400)) / 3600);
		$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
		$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
		return $Duration = $days.'D: '.$hours.'H: '.$minutes.'M';
	}
	function date_time_format($date_time)
	{
		$date_format = strtotime($date_time);
		return $pro_day = date('d-M-Y H:i:s A', $date_format);
	}
	$summary_data=array(); $barcode_chk_data=array(); 
	$summary_count_barcode_arr=array(); 
	$summary_grade_barcode_arr=array(); 
	$held_up_date_summ=""; $ck=0;
	foreach($nameArray as $row)
	{
		$rollStatus = $status[$row[csf("roll_id")]][$row[csf("po_id")]]["roll_status"];
		if($rollStatus==2)
		{
			if($held_up_update_arr[$row[csf("barcode_no")]]!="" && $held_up_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
			{
				$held_up_date_summ = date_time_format($held_up_update_arr[$row[csf("barcode_no")]]);
			} 
			else{
				$held_up_date_summ = date_time_format($held_up_insert_arr[$row[csf("barcode_no")]]);
				//echo $row[csf("barcode_no")].'DF';
			}
			$held_up_date_chk_arr[$row[csf("barcode_no")]]=$held_up_date_summ;
			
		}
		if($rollStatus==1)
		{ 
			if($qc_pass_update_arr[$row[csf("barcode_no")]]!="" && $qc_pass_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
			{
				$qc_pass = date_time_format($qc_pass_update_arr[$row[csf("barcode_no")]]);
				$qc_pass_date_summ = $qc_pass_update_arr[$row[csf("barcode_no")]];
			}
			else{
				$qc_pass = date_time_format($qc_pass_insert_arr[$row[csf("barcode_no")]]);
				$qc_pass_date_summ = $qc_pass_insert_arr[$row[csf("barcode_no")]];
			}
		}
		if($rollStatus==3)
		{		
			if($rejected_update_arr[$row[csf("barcode_no")]]!="" && $rejected_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
			{ 
				$reject_up_date_summ = date_time_format($rejected_update_arr[$row[csf("barcode_no")]]); 
			}
			else{ 
				$reject_up_date_summ = date_time_format($rejected_insert_arr[$row[csf("barcode_no")]]); 
			}
		}
		if($knit_pro_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["production_roll_id"]>0)
		{
			//$production_day_summ=$knit_pro_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["production_date_time"]; 
		}
		
		
	} //Check End
	
	//print_r($held_up_date_chk_arr);
	foreach($nameArray as $row)
	{
		
		$barcode=$barcode_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["barcode_no"];
		$rollStatus = $status[$row[csf("roll_id")]][$row[csf("po_id")]]["roll_status"];
		$qc_pass_date_sum=$held_up_date_sum=$reject_up_date_sum=$Awaiting_qc_sum=$current_date_sum="";
		if($rollStatus==1)
		{ 
			if($qc_pass_update_arr[$row[csf("barcode_no")]]!="" && $qc_pass_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
			{
				//$qc_pass = date_time_format($qc_pass_update_arr[$row[csf("barcode_no")]]);
				$qc_pass_date_sum = $qc_pass_update_arr[$row[csf("barcode_no")]];
				$current_date_sum = date("d-M-Y h.m.s A", strtotime("now"));
			}
			else{
				//$qc_pass = date_time_format($qc_pass_insert_arr[$row[csf("barcode_no")]]);
				$qc_pass_date_sum = $qc_pass_insert_arr[$row[csf("barcode_no")]];
				
				$current_date_sum = date("d-M-Y h.m.s A", strtotime("now"));
			}
		}
						
		if($rollStatus==2)
		{
			if($held_up_update_arr[$row[csf("barcode_no")]]!="" && $held_up_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
			{
				$held_up_date_sum = date_time_format($held_up_update_arr[$row[csf("barcode_no")]]);
			} 
			else{
				$held_up_date_sum = date_time_format($held_up_insert_arr[$row[csf("barcode_no")]]);
				//echo $held_up_insert_arr[$row[csf("barcode_no")]].'VV';
			}
		}

		if($rollStatus==3)
		{		
			if($rejected_update_arr[$row[csf("barcode_no")]]!="" && $rejected_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
			{ 
				$reject_up_date_sum = date_time_format($rejected_update_arr[$row[csf("barcode_no")]]); 
			}
			else{ 
				$reject_up_date_sum = date_time_format($rejected_insert_arr[$row[csf("barcode_no")]]); 
			}
		}
		if($knit_pro_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["production_roll_id"]>0)
		{
			$production_day_sum=$knit_pro_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["production_date_time"]; 
			//echo $production_day_sum.'<br>';
		}
		if($held_up_date_sum!="")  
		{
			if($held_up_date_chk_arr[$row[csf("barcode_no")]]!="")
			{
				$summary_count_held_up_date_arr[$row[csf("knitting_company")]]+=1; 
				$held_up_date_sum=$held_up_date_sum;
			}
			else { 
				$summary_count_held_up_date_arr[$row[csf("knitting_company")]]+=0;
				$held_up_date_sum="";
			} 
		}
		// echo  $qc_pass_date_sum.'='.$held_up_date_sum.'='.$reject_up_date_sum.'<br>';
		if ($qc_pass_date_sum=="" && $held_up_date_sum=="" && $reject_up_date_sum=="") 
		 { 
		   
			 $current_date_time_sum = date("d-M-Y h.m.s A", strtotime("now"));
			 $Awaiting_qc_sum=durationDaysHourMin($current_date_time_sum, $production_day_sum);
			 // echo  $Awaiting_qc_sum.'DD'.$current_date_time_sum.'DD'.$production_day_sum.'<br>'; 
		 }
		//echo $row[csf("barcode_no")].'='.$Awaiting_Delivery.'<br>';				
		$summary_data[$row[csf("knitting_company")]]['knitting_company']=$row[csf("knitting_company")]; 
		$summary_data[$row[csf("knitting_company")]]['barcode_no']=$row[csf("barcode_no")]; 
		$summary_data[$row[csf("knitting_company")]]['knitting_source'] = $row[csf("knitting_source")];
		$summary_count_barcode_arr[$row[csf("knitting_company")]]++; 
		$knitting_company_fabric_grade_arr[$row[csf("knitting_company")]][$qc_fabric_grade[$row[csf("barcode_no")]]]++;
		if($Awaiting_qc_sum!="")
		{
			$summary_count_awaiting_qc_arr[$row[csf("knitting_company")]]+=1;
		}
		
		if($reject_up_date_sum!="")
		{
			$summary_count_reject_up_date_arr[$row[csf("knitting_company")]]++; 
		}
	} 
	//print_r($summary_count_awaiting_qc_arr); 
	//echo "<pre>";print_r($summary_data);die; 
	//$fabric_knitting_company_grade_arr[$summary_count_fabric_grade_arr]
	
	$div_width=2908;
	$table_width=2890;
	ob_start();
		
     if ($type == 1 || $type == 3 ) {
        
	?>
    
    <div style="width:<? echo $div_width; ?>px;">
	<fieldset style="width:<? echo $div_width; ?>px;"> 
		<!-- Summary Start -->
		<table border="0" width="<? echo $table_width; ?>" align="left">
        	<tr>
            	<td width="1150">
                	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" align="left">
                		<h2>Summary</h2>
                    	<thead>
                        	<tr>
                            	<th width="30" rowspan="2">SL</th>
                            	<th width="100" rowspan="2">Party Name</th>
                                <th width="100" rowspan="2">Total Product Number of Roll</th>
                                <th width="200" colspan="8">Fabric Grade</th>
                                <th width="80" rowspan="2">Awaiting for QC</th>
                                <th width="30" rowspan="2">%</th>
                                <th width="80" rowspan="2">Held-up</th>
                                <th width="30" rowspan="2">%</th>
                                <th width="80" rowspan="2">Rejected</th>
                                <th width="30" rowspan="2">%</th>
                            </tr>
                            <tr>
                            	<th width="50">A</th>
                                <th width="30">%</th>
                                <th width="50">B</th>
                                <th width="30">%</th>
                                <th width="50">C</th> 
                                <th width="30">%</th>
                                <th width="50">D</th> 
                                <th width="30">%</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
						$j=1;$total_summary_count_awaiting_delivery=$total_summary_count_summary_count_held_up=0;
						foreach ($summary_data as $party_id => $row)
						{
							if($row['knitting_source']==1)
							{
								$partyName = $inhouse_party_arr[$party_id];
							}else{
								$partyName = $out_bound_party_arr[$party_id];
							}
							
							$color_range_val;
							if ($j%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td><p><? echo $j; ?></p></td>
								<td><p><? echo $partyName; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $summary_count_barcode_arr[$party_id]; ?>&nbsp;</p></td>
								<!-- <td align="center"><p><? //echo $fabric_knitting_company_grade_arr[$party_id]['A']; ?>&nbsp;</p></td>
								<td align="center"><? //echo $fabric_knitting_company_grade_arr[$party_id]['B']; ?></td>
								<td align="center"><? //echo $fabric_knitting_company_grade_arr[$party_id]['C']; ?></td>
								<td align="center"><? //echo $fabric_knitting_company_grade_arr[$party_id]['D']; ?></td> -->

								<td align="right"><p><? echo $knitting_company_fabric_grade_arr[$party_id]['A']; ?>&nbsp;</p></td>
                                <td align="right"><p><? $fabric_grade_A_per = ($knitting_company_fabric_grade_arr[$party_id]['A']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_A_per,1);
								 ?>&nbsp;</p></td>
								<td align="right"><? echo $knitting_company_fabric_grade_arr[$party_id]['B']; ?></td>
                                <td align="right"><p><? $fabric_grade_B_per = ($knitting_company_fabric_grade_arr[$party_id]['B']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_B_per,1);
								 ?>&nbsp;</p></td>
								<td align="right"><? echo $knitting_company_fabric_grade_arr[$party_id]['C']; ?></td>
                                 <td align="right"><p><? $fabric_grade_C_per = ($knitting_company_fabric_grade_arr[$party_id]['C']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_C_per,1);
								 ?>&nbsp;</p></td>
								<td align="right"><? echo $knitting_company_fabric_grade_arr[$party_id]['D']; ?></td>
                                <td align="right"><p><? $fabric_grade_D_per = ($knitting_company_fabric_grade_arr[$party_id]['D']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_D_per,1);
								 ?>&nbsp;</p></td>
                                 <td align="right"><p><? 
								 $fabric_awaiting_count_per =($summary_count_awaiting_qc_arr[$party_id]/$summary_count_barcode_arr[$party_id])*100;
								 $fabric_held_up_count_per =($summary_count_held_up_date_arr[$party_id]/$summary_count_barcode_arr[$party_id])*100;
								 $fabric_reject_up_count_per =($summary_count_reject_up_date_arr[$party_id]/$summary_count_barcode_arr[$party_id])*100;
								 echo number_format($summary_count_awaiting_qc_arr[$party_id],1);
								  ?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($fabric_awaiting_count_per,1);?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($summary_count_held_up_date_arr[$party_id],1);?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($fabric_held_up_count_per,1) ?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($summary_count_reject_up_date_arr[$party_id],1);?>&nbsp;</p></td>
                                  <td align="right"><p><? echo number_format($fabric_reject_up_count_per,1) ?>&nbsp;</p></td>
							</tr>
							<? 
							$total_product += $summary_count_barcode_arr[$party_id];
							/*$total_fabric_grade_A += $fabric_knitting_company_grade_arr[$party_id]['A'];
							$total_fabric_grade_B += $fabric_knitting_company_grade_arr[$party_id]['B'];
							$total_fabric_grade_C += $fabric_knitting_company_grade_arr[$party_id]['C'];
							$total_fabric_grade_D += $fabric_knitting_company_grade_arr[$party_id]['D'];*/

							$total_fabric_grade_A += $knitting_company_fabric_grade_arr[$party_id]['A'];
							$total_fabric_grade_B += $knitting_company_fabric_grade_arr[$party_id]['B'];
							$total_fabric_grade_C += $knitting_company_fabric_grade_arr[$party_id]['C'];
							$total_fabric_grade_D += $knitting_company_fabric_grade_arr[$party_id]['D'];

							$grand_fabric_grade_A = ($total_fabric_grade_A/$total_product)*100;
							$grand_fabric_grade_B = ($total_fabric_grade_B/$total_product)*100;
							$grand_fabric_grade_C = ($total_fabric_grade_C/$total_product)*100;
							$grand_fabric_grade_D = ($total_fabric_grade_D/$total_product)*100;
							$total_summary_count_awaiting_delivery+=$summary_count_awaiting_qc_arr[$party_id];
							$total_summary_count_summary_count_held_up+=$summary_count_held_up_date_arr[$party_id];
							$total_summary_count_summary_count_reject_up+=$summary_count_reject_up_date_arr[$party_id];
							$j++;
						} 
						?>
                        </tbody>
                        <tfoot> 
                        	<tr>
                            	<th align="right" colspan="2">Total:</th>
                                <th align="right"><? echo $total_product; ?></th>
                                <th align="right"><? echo $total_fabric_grade_A; ?></th>
                                <th align="right"><? echo number_format(($total_fabric_grade_A/$total_product)*100,1); ?></th>
                                <th align="right"><? echo $total_fabric_grade_B; ?></th>
                               <th align="right"><? echo number_format(($total_fabric_grade_B/$total_product)*100,1); ?></th>
                                <th align="right"><? echo $total_fabric_grade_C; ?></th>
                                <th align="right"><? echo number_format(($total_fabric_grade_C/$total_product)*100,1); ?></th>
                                <th align="right"><? echo $total_fabric_grade_D; ?></th>
                                <th align="right"><? echo number_format(($total_fabric_grade_D/$total_product)*100,1); ?></th>
                                <th align="right"><? echo number_format($total_summary_count_awaiting_delivery,1); ?></th>
                             	<th align="right"><?  echo number_format(($total_summary_count_awaiting_delivery/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo  number_format($total_summary_count_summary_count_held_up,1);; ?></th>
                               <th align="right"><?  echo number_format(($total_summary_count_summary_count_held_up/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($total_summary_count_summary_count_reject_up,1); ?></th>
                               <th align="right"><?  echo number_format(($total_summary_count_summary_count_reject_up/$total_product)*100,1);?>&nbsp;</th>
                            </tr>
                            <tr>
                            	<th colspan="3" align="right">Grade%:</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_A,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_A/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_B,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_B/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_C,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_C/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_D,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_D/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  //echo number_format($total_summary_count_awaiting_delivery,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_summary_count_awaiting_delivery/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  //echo number_format(($total_fabric_grade_D/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_summary_count_summary_count_held_up/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  //echo number_format(($total_fabric_grade_D/$total_product)*100,1);?>&nbsp;</th>
                                 <th align="right"><?  echo number_format(($total_summary_count_summary_count_reject_up/$total_product)*100,1);?>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
                <td width="5%"></td>
                <td valign="top" width="400">
                   <canvas id="canvas3" height="250" width="400"></canvas>
                </td>
                <td></td>
            </tr>
        </table>
        <br>
        
        <!-- Summary End -->
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2890" class="rpt_table" align="left">
     		<h2></h2>
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Job No & Style Ref</th>
                    <th width="100">Order No</th>
                    <th width="100">Roll Id</th>
                    <th width="100">Color Range</th>
                    <th width="100">Body Part</th>
                    <th width="170">Fabric Description</th>
					<th width="60">GSM</th>
                    <th width="60">Dia</th>
                    <th width="60">Stitch Length</th>
                    <th width="60">Machine Dia</th>
                    <th width="60">Gauge</th>
                    <th width="80">Grey Weight</th>
                    <th width="80">Production Date and Time</th>
                    <th width="70">Duration From Booking to Produce</th>
                    <th width="70">Awaiting for QC</th>
                    <th width="70">Held-up</th>
                    <th width="70">Rejected</th>
                    <th width="70">QC passed</th>
                    <th width="70">Fabric Grade</th>
                    <th width="70">Party Name</th>
                    <th width="70">Awaiting Delivery</th>
                    <th width="70">Delivery Challan No.</th>
                    <th width="70">Delv. To Store</th>
                    <th width="70">Duration From Produce to Delivery</th> 
                    <th width="70">Recv. by Store</th>
                    <th width="70">Duration From Delivery to Recv.</th>  
                    <th width="70">Issue to Batch</th>
                    <th width="70">Duration From Recv. to Issue</th> 
                    <th width="70">Recv. by Batch</th>
                    <th width="70">Duration From Issue to Recv.</th> 
                    <th width="70">Batch Create</th>
                    <th width="70">Batch No</th>
                    <th width="70">Duration From Recv. to Batch</th> 
                    <th width="70">Duration From Produce to Batch</th> 
                    
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $div_width; ?>px; overflow-y:scroll; max-height:350px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="table_body">
				<?
					//echo $sql;
					
					$m=1;$Awaiting_Delivery="";
					$tot_grey_delivery=$tot_grey_rcv_store=$tot_grey_issue_batch=$tot_grey_rcv_batch=$tot_batch_created=$tot_fin_delivery=$tot_fin_rcv_store=$tot_fin_issu_cut=0;
					foreach ($nameArray as $row)
					{
						if ($m%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
			//secho $rollStatus.'DDDDDDDDDDDD';
						if($row['knitting_source']==1)
						{
							$partyName = $inhouse_party_arr[$row[csf("knitting_company")]];
						}else{
							$partyName = $out_bound_party_arr[$row[csf("knitting_company")]];
						}
						
						$grey_delivery_day=$grey_rcv_store_day=$grey_issue_batch_day=$grey_rcv_batch_day=$batch_created_day=$finishion_day=$fin_delivery_day=$fin_rcv_store_day=$fin_issu_cut_day="";
						
						if($row[csf("entry_form")]==2)
						{
							if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_delivery"]>0)
							{
								$grey_delivery_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_delivery_date"];
								$tot_grey_delivery+=$row[csf("grey_qnty")];
								
							}
							if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_store"]>0)
							{
								$grey_rcv_store_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_store_date"]; 
								$tot_grey_rcv_store+=$row[csf("grey_qnty")];
							}	

							
							/*echo '<pre>';
							print_r($grey_rcv_store_day);*/
						}	
						else
						{
							$grey_delivery_day=$row[csf("insert_date")];
							$tot_grey_delivery+=$row[csf("grey_qnty")];
							
							$grey_rcv_store_day=$row[csf("insert_date")]; 
							$tot_grey_rcv_store+=$row[csf("grey_qnty")];
							
						}

						if($knit_pro_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["production_roll_id"]>0)
						{
							$production_day=$knit_pro_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["production_date_time"]; 
							$tot_grey_qty+=$row[csf("grey_qnty")];
						}

						$challan_no=$challan_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["challan_no"];

						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_issue_batch"]>0)
						{
							$grey_issue_batch_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_issue_batch_date"];
							$tot_grey_issue_batch+=$row[csf("grey_qnty")];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_batch"]>0)
						{
							$grey_rcv_batch_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_batch_date"];
							$tot_grey_rcv_batch+=$row[csf("grey_qnty")];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["batch_created"]>0)
						{
							$batch_created_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["batch_created_date"];
							$tot_batch_created+=$row[csf("grey_qnty")];
						}
						/*echo '<pre>';
						print_r($roll_data_arr);*/
						//	print_r($sub_process_day);
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion"]>0)
						{
							$finishion_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_date"];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_delivery"]>0)
						{
							$fin_delivery_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_delivery_date"];
							$tot_fin_delivery+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_rcv_store"]>0)
						{
							$fin_rcv_store_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_rcv_store_date"];
							$tot_fin_rcv_store+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_issu_cut"]>0)
						{
							$fin_issu_cut_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_issu_cut_date"];
							$tot_fin_issu_cut+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						}
						
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_receive_cut"]>0)
						{
							$fin_receive_cut_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_receive_cut_date"];
							$tot_fin_receive_cut+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						} 

						$barcode=$barcode_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["barcode_no"];
						$rollStatus = $status[$row[csf("roll_id")]][$row[csf("po_id")]]["roll_status"];
						//echo $rollStatus;
						$qc_pass_date=$held_up_date=$reject_up_date=$fabric_grade=$current_date=$Awaiting_Delivery=$qc_pass="";
						//echo $rollStatus.'DDD';
						if($rollStatus==1)
						{ 
							if($qc_pass_update_arr[$row[csf("barcode_no")]]!="" && $qc_pass_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
							{
                    			$qc_pass = date_time_format($qc_pass_update_arr[$row[csf("barcode_no")]]);
                    			$qc_pass_date = $qc_pass_update_arr[$row[csf("barcode_no")]];
                    			$current_date = date("d-M-Y h.m.s A", strtotime("now"));
                    			$Awaiting_Delivery = durationDaysHourMin($current_date, $qc_pass_date);
                    		}
                    		else{
                    			$qc_pass = date_time_format($qc_pass_insert_arr[$row[csf("barcode_no")]]);
                    			$qc_pass_date = $qc_pass_insert_arr[$row[csf("barcode_no")]];
                    			$current_date = date("d-M-Y h.m.s A", strtotime("now"));
                    			$Awaiting_Delivery = durationDaysHourMin($current_date, $qc_pass_date);
                    		}
						}
						if($rollStatus==2)
						{
							if($held_up_update_arr[$row[csf("barcode_no")]]!="" && $held_up_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
							{
								$held_up_date = date_time_format($held_up_update_arr[$row[csf("barcode_no")]]);
							} 
							else{
								$held_up_date = date_time_format($held_up_insert_arr[$row[csf("barcode_no")]]);
							}
                			
						}

						if($rollStatus==3)
						{		
							if($rejected_update_arr[$row[csf("barcode_no")]]!="" && $rejected_update_arr[$row[csf("barcode_no")]]!="0000-00-00 00:00:00" )
							{ 
	                    		$reject_up_date = date_time_format($rejected_update_arr[$row[csf("barcode_no")]]); 
							}
							else{ 
	                    		$reject_up_date = date_time_format($rejected_insert_arr[$row[csf("barcode_no")]]); 
							}
						}
					//echo $Awaiting_Delivery.'='.$reject_up_date.'<br>';
	                    $booking_date = $booking_date_arr[$row[csf("booking_no")]];
						if($row[csf("entry_form")]==2) $roll_entry_form="Production"; else $roll_entry_form="Receive";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>"> 
							<td width="30" align="center"><? echo $m; ?></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $row[csf("job_no")]."<br>".$row[csf("style_ref_no")]; ?>&nbsp;</p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $row[csf("po_number")]."<br>".$row[csf("grouping")];; ?>&nbsp;</p></td>
                            
                            <td width="100" style="word-break:break-all;" align="center"><p><? echo $row[csf("barcode_no")]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $color_range[$row[csf("color_range_id")]]; ?>&nbsp;</p></td>
                            <td width="100"  style="word-break:break-all;"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>

                            <td width="170" style="word-break:break-all;"><p><? echo $composition_arr[$row[csf("febric_description_id")]]; ?>&nbsp;</p></td>
                            <td width="60" style="word-break:break-all;" align="center"><p><? echo $row[csf("gsm")]; ?>&nbsp;</p></td>
                            <td width="60" style="word-break:break-all;" align="center"><p><? echo $row[csf("dia")]; ?>&nbsp;</p></td>
                            <td width="60" style="word-break:break-all;" align="center"><p><? echo $row[csf("stitch_length")]; ?>&nbsp;</p></td>
                            <td width="60" style="word-break:break-all;" align="center"><p><? echo $machine_data[$row[csf("machine_no_id")]]["dia_width"]; ?>&nbsp;</p></td>
                            <td width="60" style="word-break:break-all;" align="center"><p><? echo $machine_data[$row[csf("machine_no_id")]]["gauge"]; ?>&nbsp;</p></td>
                            <td width="80" style="word-break:break-all;" align="center" title="<? echo $roll_entry_form."**".$row[csf("roll_id")]."**".$row[csf("po_id")]; ?>"><? echo number_format($row[csf("grey_qnty")],2); $total_grey_qnty+=$row[csf("grey_qnty")]; ?></td>

                            <td width="80" align="center" style="word-break:break-all;" valign="middle" ><a href="##" onclick="openmypage_sys_no('2','<? echo $row[csf("barcode_no")];?>')"><? echo date_time_format($production_day); ?></a></td> 
                            <? 
                            	//$booking_insert_date = $row[csf("booking_insert_date")];	
                            	$current_date_time = date("d-M-Y h.m.s A", strtotime("now"));
                            ?>
   							<td width="70" style="word-break:break-all;" align="center"><? echo durationDaysHourMin($production_day, $booking_date); //$booking_insert_date; ?></td> 
		                    <td width="70" style="word-break:break-all;"><? if ($qc_pass_date=="" && $held_up_date=="" && $reject_up_date=="") { 
		                    	echo durationDaysHourMin($current_date_time, $production_day); 
		                    } ?></td> 
		                    <td width="70" align="center" style="word-break:break-all;" valign="middle" ><? echo $held_up_date; ?>
		                	</td>	 
		                    <td width="70" align="center" style="word-break:break-all;" valign="middle" ><? echo $reject_up_date; ?>
		                	</td>
		                    <td width="70" title="<? echo $row[csf("barcode_no")].'=='.$barcode; ?>" align="center" style="word-break:break-all;" valign="middle" ><? echo $qc_pass; ?>
		                	</td>
		                    <td width="70" style="word-break:break-all;" valign="middle" align="center"><? echo $qc_fabric_grade[$row[csf("barcode_no")]]; ?></td>
		                    <td width="70" style="word-break:break-all;"><? echo $partyName; //$party_arr[$row[csf("knitting_company")]]; ?></td>
		                    <td width="70" style="word-break:break-all;" title="<? echo $current_date.'='.$qc_pass_date; ?>"><? if($Awaiting_Delivery!="") echo $Awaiting_Delivery;else echo "";	 
									?></td>
		                    <td width="70" style="word-break:break-all;"><? echo $challan_no; ?></td>

                            <td width="70" align="center" style="word-break:break-all;" valign="middle"  >

                            	<a href="##" onclick="openmypage_sys_no('56','<? echo $row[csf("barcode_no")];?>')">
                            		<? 
                            		if($grey_delivery_day!="")
                            		{
                            			echo date_time_format($grey_delivery_day) ;
                            		}
                            		?>
                            		</a>
                            </td>

                            <td width="70" style="word-break:break-all;" title="<? echo $production_day.'='.$grey_delivery_day; ?>"><? 
                            echo ($grey_delivery_day!="")? durationDaysHourMin($grey_delivery_day, $production_day):"";
                             ?></td> 
                            
                            <td width="70" align="center" style="word-break:break-all;" valign="middle" >
                            	<a href="##" onclick="openmypage_sys_no('58','<? echo $row[csf("barcode_no")];?>')">
                            	
                            	<? 
                            	if($grey_rcv_store_day!="")
                            	{
                            		echo date_time_format($grey_rcv_store_day);
                            	}
                            	
                            	?>
                            			
                            	</a>
                            </td>

                            <td width="70" style="word-break:break-all;"><? echo ($grey_delivery_day!="" && $grey_rcv_store_day!="")? durationDaysHourMin($grey_rcv_store_day ,$grey_delivery_day):""; ?></td> 
                            <?
                            	if($transfered_barcode_arr[$row[csf("barcode_no")]] == "")
                            	{
	                            	?>
		                            <td width="70" align="center" style="word-break:break-all;" valign="middle" ><a href="##" onclick="openmypage_sys_no('61','<? echo $row[csf("barcode_no")];?>')">

		                            	<? 
		                            	if($grey_issue_batch_day!="")
		                            	{
		                            		echo date_time_format($grey_issue_batch_day); 
		                            	}
		                            	?>
		                            	</a>
		                            </td>
		                            <?
	                        	}
	                        	else
	                        	{
	                        		?>
	                        		<td width="70" align="center" style="word-break:break-all;" valign="middle" ></td>
	                        		<?
	                        	}
							?>
							<td width="70" style="word-break:break-all;"><? echo ($grey_issue_batch_day!="" && $grey_rcv_store_day!="")? durationDaysHourMin($grey_issue_batch_day, $grey_rcv_store_day):""; ?></td> 
							<td width="70" align="center" style="word-break:break-all;" valign="middle" >
								<a href="##" onclick="openmypage_sys_no('62','<? echo $row[csf("barcode_no")];?>')">
									<? 
									if($grey_rcv_batch_day!="")
									{
										echo date_time_format($grey_rcv_batch_day); 
									}
									
									?>
										
								</a>
							</td>

							<td width="70" style="word-break:break-all;"><? echo ($grey_rcv_batch_day!="" && $grey_issue_batch_day!="")? durationDaysHourMin($grey_rcv_batch_day, $grey_issue_batch_day):""; ?></td> 
							
							<td width="70" align="center" style="word-break:break-all;" valign="middle">
								<a href="##" onclick="openmypage_sys_no('64','<? echo $row[csf("barcode_no")];?>')">

								<? 
								if($batch_created_day!="")
								{
									echo date_time_format($batch_created_day);
								}
								?>
									
								</a>
							</td>

							<td width="70" style="word-break:break-all;" ><? echo $batachData[$row[csf("barcode_no")]]['batch_no']; ?></td>
							<td width="70" style="word-break:break-all;"><? echo ($batch_created_day!="" && $grey_rcv_batch_day!="")? durationDaysHourMin($batch_created_day, $grey_rcv_batch_day):""; ?></td> 
							<td width="70" style="word-break:break-all;"><? echo ($batch_created_day!="" && $production_day!="")? durationDaysHourMin($batch_created_day, $production_day):""; ?></td> 
						</tr>
						<?
						$m++;
					}	
				?>
			</table> 
		</div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="rpt_table_footer"  align="left">
        	<tfoot>
            	<tr>
                	<th width="30">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th> 
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="170">&nbsp;</th>
					<th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60" align="right">Total:</th>
                    <th width="80" align="right" id="value_total_grey_qnty"><? echo number_format($total_grey_qnty,2); ?></th>

                    <th width="80"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>

                    <th width="70" align="right"><? echo number_format($tot_grey_delivery,2); ?></th>
                    <th width="70"></th>
                    <th width="70" align="right"><? echo number_format($tot_grey_rcv_store,2); ?></th>
                    <th width="70"></th>
                    <th width="70" align="right"><? echo number_format($tot_grey_issue_batch,2); ?></th>
                    <th width="70"></th>
                    <th width="70" align="right"><? echo number_format($tot_grey_rcv_batch,2); ?></th>
                    <th width="70"></th>
                    <th width="70" align="right"><? echo number_format($tot_batch_created,2); ?></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
    </div>      
 <?
	}
	else 
	{
?>
	<div style="width:<? echo $div_width; ?>px;">
	<fieldset style="width:<? echo $div_width; ?>px;"> 
		<!-- Summary Start -->
		<table border="0" width="<? echo $table_width; ?>" align="left">
        	<tr>
            	<td width="700">
                	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" align="left">
                		<h2>Summary</h2>
                    	<thead>
                        	<tr>
                            	<th width="30" rowspan="2">SL</th>
                            	<th width="100" rowspan="2">Party Name</th>
                                <th width="100" rowspan="2">Total Product Number of Roll</th>
                                <th width="200" colspan="8">Fabric Grade</th>
                                <th width="80" rowspan="2">Awaiting for QC</th>
                                <th width="30" rowspan="2">%</th>
                                <th width="80" rowspan="2">Held-up</th>
                                <th width="30" rowspan="2">%</th>
                                <th width="80" rowspan="2">Rejected</th>
                                <th width="30" rowspan="2">%</th>
                            </tr>
                            <tr>
                            	<th width="50">A</th>
                                <th width="30">%</th>
                                <th width="50">B</th>
                                <th width="30">%</th>
                                <th width="50">C</th> 
                                <th width="30">%</th>
                                <th width="50">D</th> 
                                <th width="30">%</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
						$j=1;$total_summary_count_awaiting_delivery=$total_summary_count_summary_count_held_up=0;
						foreach ($summary_data as $party_id => $row)
						{
							if($row['knitting_source']==1)
							{
								$partyName = $inhouse_party_arr[$party_id];
							}else{
								$partyName = $out_bound_party_arr[$party_id];
							}
							
							$color_range_val;
							if ($j%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td><p><? echo $j; ?></p></td>
								<td><p><? echo $partyName; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $summary_count_barcode_arr[$party_id]; ?>&nbsp;</p></td>
								<!-- <td align="center"><p><? //echo $fabric_knitting_company_grade_arr[$party_id]['A']; ?>&nbsp;</p></td>
								<td align="center"><? //echo $fabric_knitting_company_grade_arr[$party_id]['B']; ?></td>
								<td align="center"><? //echo $fabric_knitting_company_grade_arr[$party_id]['C']; ?></td>
								<td align="center"><? //echo $fabric_knitting_company_grade_arr[$party_id]['D']; ?></td> -->

								<td align="right"><p><? echo $knitting_company_fabric_grade_arr[$party_id]['A']; ?>&nbsp;</p></td>
                                <td align="right"><p><? $fabric_grade_A_per = ($knitting_company_fabric_grade_arr[$party_id]['A']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_A_per,1);
								 ?>&nbsp;</p></td>
								<td align="right"><? echo $knitting_company_fabric_grade_arr[$party_id]['B']; ?></td>
                                <td align="right"><p><? $fabric_grade_B_per = ($knitting_company_fabric_grade_arr[$party_id]['B']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_B_per,1);
								 ?>&nbsp;</p></td>
								<td align="right"><? echo $knitting_company_fabric_grade_arr[$party_id]['C']; ?></td>
                                 <td align="right"><p><? $fabric_grade_C_per = ($knitting_company_fabric_grade_arr[$party_id]['C']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_C_per,1);
								 ?>&nbsp;</p></td>
								<td align="right"><? echo $knitting_company_fabric_grade_arr[$party_id]['D']; ?></td>
                                <td align="right"><p><? $fabric_grade_D_per = ($knitting_company_fabric_grade_arr[$party_id]['D']/$summary_count_barcode_arr[$party_id])*100;
								echo number_format($fabric_grade_D_per,1);
								 ?>&nbsp;</p></td>
                                 <td align="right"><p><? 
								 $fabric_awaiting_count_per =($summary_count_awaiting_qc_arr[$party_id]/$summary_count_barcode_arr[$party_id])*100;
								 $fabric_held_up_count_per =($summary_count_held_up_date_arr[$party_id]/$summary_count_barcode_arr[$party_id])*100;
								 $fabric_reject_up_count_per =($summary_count_reject_up_date_arr[$party_id]/$summary_count_barcode_arr[$party_id])*100;
								 echo number_format($summary_count_awaiting_qc_arr[$party_id],1);
								  ?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($fabric_awaiting_count_per,1);?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($summary_count_held_up_date_arr[$party_id],1);?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($fabric_held_up_count_per,1) ?>&nbsp;</p></td>
                                 <td align="right"><p><? echo number_format($summary_count_reject_up_date_arr[$party_id],1);?>&nbsp;</p></td>
                                  <td align="right"><p><? echo number_format($fabric_reject_up_count_per,1) ?>&nbsp;</p></td>
							</tr>
							<? 
							$total_product += $summary_count_barcode_arr[$party_id];
							/*$total_fabric_grade_A += $fabric_knitting_company_grade_arr[$party_id]['A'];
							$total_fabric_grade_B += $fabric_knitting_company_grade_arr[$party_id]['B'];
							$total_fabric_grade_C += $fabric_knitting_company_grade_arr[$party_id]['C'];
							$total_fabric_grade_D += $fabric_knitting_company_grade_arr[$party_id]['D'];*/

							$total_fabric_grade_A += $knitting_company_fabric_grade_arr[$party_id]['A'];
							$total_fabric_grade_B += $knitting_company_fabric_grade_arr[$party_id]['B'];
							$total_fabric_grade_C += $knitting_company_fabric_grade_arr[$party_id]['C'];
							$total_fabric_grade_D += $knitting_company_fabric_grade_arr[$party_id]['D'];

							$grand_fabric_grade_A = ($total_fabric_grade_A/$total_product)*100;
							$grand_fabric_grade_B = ($total_fabric_grade_B/$total_product)*100;
							$grand_fabric_grade_C = ($total_fabric_grade_C/$total_product)*100;
							$grand_fabric_grade_D = ($total_fabric_grade_D/$total_product)*100;
							$total_summary_count_awaiting_delivery+=$summary_count_awaiting_qc_arr[$party_id];
							$total_summary_count_summary_count_held_up+=$summary_count_held_up_date_arr[$party_id];
							$total_summary_count_summary_count_reject_up+=$summary_count_reject_up_date_arr[$party_id];
							$j++;
						} 
						?>
                        </tbody>
                        <tfoot> 
                        	<tr>
                            	<th align="right" colspan="2">Total:</th>
                                <th align="right"><? echo $total_product; ?></th>
                                <th align="right"><? echo $total_fabric_grade_A; ?></th>
                                <th align="right"><? echo number_format(($total_fabric_grade_A/$total_product)*100,1); ?></th>
                                <th align="right"><? echo $total_fabric_grade_B; ?></th>
                               <th align="right"><? echo number_format(($total_fabric_grade_B/$total_product)*100,1); ?></th>
                                <th align="right"><? echo $total_fabric_grade_C; ?></th>
                                <th align="right"><? echo number_format(($total_fabric_grade_C/$total_product)*100,1); ?></th>
                                <th align="right"><? echo $total_fabric_grade_D; ?></th>
                                <th align="right"><? echo number_format(($total_fabric_grade_D/$total_product)*100,1); ?></th>
                                <th align="right"><? echo number_format($total_summary_count_awaiting_delivery,1); ?></th>
                             	<th align="right"><?  echo number_format(($total_summary_count_awaiting_delivery/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo  number_format($total_summary_count_summary_count_held_up,1);; ?></th>
                               <th align="right"><?  echo number_format(($total_summary_count_summary_count_held_up/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($total_summary_count_summary_count_reject_up,1); ?></th>
                               <th align="right"><?  echo number_format(($total_summary_count_summary_count_reject_up/$total_product)*100,1);?>&nbsp;</th>
                            </tr>
                            <tr>
                            	<th colspan="3" align="right">Grade%:</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_A,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_A/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_B,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_B/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_C,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_C/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_fabric_grade_D,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_fabric_grade_D/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  //echo number_format($total_summary_count_awaiting_delivery,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_summary_count_awaiting_delivery/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  //echo number_format(($total_fabric_grade_D/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  echo number_format(($total_summary_count_summary_count_held_up/$total_product)*100,1);?>&nbsp;</th>
                                <th align="right"><?  //echo number_format(($total_fabric_grade_D/$total_product)*100,1);?>&nbsp;</th>
                                 <th align="right"><?  echo number_format(($total_summary_count_summary_count_reject_up/$total_product)*100,1);?>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
                <td width="5%"></td>
                <td valign="top" width="400">
                   <canvas id="canvas3" height="250" width="400"></canvas>
                </td>
                <td></td>
            </tr>
        </table>
        </fieldset>
    </div> 

<?

	}	
	//echo "<pre>";print_r($garph_caption);
	//echo "<pre>";print_r($garph_data);die;
	$garph_caption= json_encode($garph_caption);
    $garph_data= json_encode($garph_data);
	
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
	echo "$total_data####$filename####$garph_caption####$garph_data####$type";
	
	disconnect($con);
	exit();
}


if($action == "system_no_popup")
{
	echo load_html_head_contents("Roll Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	 if ($entry_form == 61)
	 {
	 	$sql = sql_select("select a.issue_number as sys_number, a.issue_date as system_date from inv_issue_master a, pro_roll_details b where a.id = b.mst_id and b.entry_form = 61 and a.entry_form = 61 and b.is_returned =0 and b.barcode_no = '$barcode_no' and a.status_active =1 and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form == 58)
	 {
	 	$sql = sql_select("select a.recv_number as sys_number, a.receive_date as system_date from inv_receive_master a, pro_roll_details b where a.id = b.mst_id and b.entry_form = 58 and a.entry_form = 58 and b.is_returned =0 and b.barcode_no = '$barcode_no' and a.status_active =1 and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form == 56){
	 	$sql = sql_select("select a.sys_number as sys_number, a.delevery_date as system_date from pro_grey_prod_delivery_mst a, pro_roll_details b where a.id = b.mst_id and b.entry_form =56 and a.entry_form = 56 and b.barcode_no = '$barcode_no' and a.status_active =1  and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form ==62)
	 {
	 	$sql = sql_select("select  a.recv_number as sys_number, a.receive_date as system_date from inv_receive_mas_batchroll a , pro_roll_details b where a.id = b.mst_id and b.entry_form =62 and a.entry_form = 62 and b.barcode_no = '$barcode_no' and a.status_active =1  and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form ==64)
	 {
	 	$sql = sql_select("select  a.batch_no as sys_number, a.batch_date as system_date from pro_batch_create_mst a , pro_roll_details b where a.id = b.mst_id and b.entry_form =64 and b.barcode_no = '$barcode_no' and a.status_active =1  and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form ==2)
	 {
		$sql = sql_select("SELECT a.recv_number AS sys_number, a.receive_date AS system_date
		FROM inv_receive_master a, pro_roll_details b 
		WHERE a.id = b.mst_id AND b.entry_form = 2 AND b.barcode_no = '$barcode_no' and a.status_active =1  and b.status_active =1 order by b.insert_date desc");
	 }

	?>
	<br>
	<fieldset style="width:240px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="240" cellpadding="0" cellspacing="0">
            <thead>
                <th width="120">System No</th>
                <th width="100">Date</th>
            </thead>
            <tbody>
            	<? foreach($sql as $row) {?>
            	<tr>
            		<td align="center"><? echo $row[csf("sys_number")];?></td>
            		<td align="center"><? echo $row[csf("system_date")];?></td>
            	</tr>
            	<?}?>
            </tbody>
        </table>
    </fieldset>
	<?
}
?>
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
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

//--------------------------------------------------------------------------------------------------------------------



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_inter_ref=str_replace("'","",$txt_inter_ref);
	$txt_barcode_no=str_replace("'","",$txt_barcode_no);
	$txt_program_no=str_replace("'","",$txt_program_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	//$txt_season="%".trim(str_replace("'","",$txt_season))."%";
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	
	
	
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
		$date_cond=" and d.receive_date between '$date_from' and '$date_to'";	
	}
	
	
	$sql_cond="";
	if($txt_file_no!="") $sql_cond=" and b.file_no='$txt_file_no'";
	if($txt_job_no!="") $sql_cond.=" and b.job_no_mst like '%$txt_job_no%'";
	if($txt_order_no!="") $sql_cond.=" and b.po_number like '%$txt_order_no%'";
	if($txt_inter_ref!="") $sql_cond.=" and b.grouping='$txt_inter_ref'";
	$bar_code_cond="";
	if($txt_barcode_no!="") $bar_code_cond=" and a.barcode_no='$txt_barcode_no'";
	$program_cond="";
	if($txt_program_no!="") $program_cond=" and d.booking_id='$txt_program_no'";
	
	$variable_prod=sql_select("select item_category_id, fabric_roll_level, page_upto_id from variable_settings_production where company_name=$company_name and variable_list=3 and status_active=1 and is_deleted=0");
	$variable_data_arr=array();
	foreach($variable_prod as $row)
	{
		$variable_data_arr[$row[csf("item_category_id")]]["fabric_roll_level"]=$row[csf("fabric_roll_level")];
		$variable_data_arr[$row[csf("item_category_id")]]["page_upto_id"]=$row[csf("page_upto_id")];
	}
	
	if($variable_data_arr[13]["fabric_roll_level"]!=1)
	{
		echo '<span style=" font-size:18px; font-weight:bold; color:red;">Fabric In Roll Level Not Maintained</span>';
		die;
	}
	$barcode_cond_batch="";
	if($txt_barcode_no!="") $barcode_cond_batch=" and c.barcode_no='$txt_barcode_no'";
	$batch_sql=sql_select("select c.roll_id, a.color_id from pro_batch_create_mst a, pro_roll_details c where a.id=c.mst_id and a.entry_form=0 and c.entry_form=64 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_id>0 $barcode_cond_batch");
	$batch_color_data=array();
	foreach($batch_sql as $row)
	{
		$batch_color_data[$row[csf("roll_id")]]=$row[csf("color_id")];
	}
	unset($batch_sql);
	
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
	
	
	$sub_process_data=array();
	$dyeing_roll_sql=sql_select("select b.roll_id from pro_fab_subprocess a, pro_batch_create_dtls b where a.batch_id=b.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.roll_id>0 and a.status_active=1 and b.status_active=1");
	foreach($dyeing_roll_sql as $row)
	{
		$sub_process_data[$row[csf("roll_id")]][2]=$row[csf("roll_id")];
	}
	
	$sub_process_query="SELECT roll_id, 
	max(case when entry_page=30 then roll_id else 0 end) as slitting_roll,
	max(case when entry_page=30 then insert_date else null end) as slitting_roll_date,
	max(case when entry_page=31 then roll_id else 0 end) as drying_roll,
	max(case when entry_page=31 then insert_date else null end) as drying_roll_date,
	max(case when entry_page=32 then roll_id else 0 end) as heat_roll,
	max(case when entry_page=32 then insert_date else null end) as heat_roll_date,
	max(case when entry_page=33 then roll_id else 0 end) as compaction_roll,
	max(case when entry_page=33 then insert_date else null end) as compaction_roll_date,
	max(case when entry_page=34 then roll_id else 0 end) as special_finish_roll,
	max(case when entry_page=34 then insert_date else null end) as special_finish_roll_date,
	max(case when entry_page=48 then roll_id else 0 end) as stentering_roll,
	max(case when entry_page=48 then insert_date else null end) as stentering_roll_date
	from pro_fab_subprocess_dtls where entry_page in(30,31,32,33,34,48) and roll_id>0 and status_active=1 and is_deleted=0 group by roll_id";
	// echo $sub_process_query;die;
	$sub_process_sql=sql_select($sub_process_query);
	$sub_process_data=$sub_process_dateTime=array();
	foreach($sub_process_sql as $row)
	{
		$sub_process_data[$row[csf("roll_id")]][1]=$row[csf("heat_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][1]=$row[csf("heat_roll_date")];
		$sub_process_data[$row[csf("roll_id")]][3]=$row[csf("slitting_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][3]=$row[csf("slitting_roll_date")];
		$sub_process_data[$row[csf("roll_id")]][4]=$row[csf("stentering_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][4]=$row[csf("stentering_roll_date")];
		$sub_process_data[$row[csf("roll_id")]][5]=$row[csf("drying_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][5]=$row[csf("drying_roll_date")];
		$sub_process_data[$row[csf("roll_id")]][6]=$row[csf("special_finish_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][6]=$row[csf("special_finish_roll_date")];
		$sub_process_data[$row[csf("roll_id")]][7]=$row[csf("compaction_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][7]=$row[csf("compaction_roll_date")];
	}
	
	
	$composition_arr=array();
	$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls");
	foreach( $compositionData as $row )
	{
		$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	} 
	if($cbo_buyer_name>0) $sql_cond.=" and c.buyer_name=$cbo_buyer_name";
	$sql="SELECT p.color_range_id, p.body_part_id, a.barcode_no, p.febric_description_id, p.gsm, p.width as dia, p.stitch_length, p.machine_no_id, a.id as roll_id, a.roll_no, a.entry_form, a.insert_date, a.qnty as grey_qnty, b.id as po_id, b.po_number, b.file_no, b.grouping, c.id as job_id, c.buyer_name, c.job_no_prefix_num, c.job_no, c.style_ref_no, b.grouping, p.color_id, d.booking_id, d.booking_no, d.receive_basis 
	from pro_grey_prod_entry_dtls p, pro_roll_details a, wo_po_break_down b, wo_po_details_master c, inv_receive_master d  
	where p.id=a.dtls_id and a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and d.id=p.mst_id and d.knitting_source in(1,3) and a.entry_form in(2) and a.roll_id=0 and a.status_active=1 and a.is_deleted=0 and c.company_name=$company_name  $sql_cond $bar_code_cond $program_cond $date_cond   order by c.id, b.id, a.id";
	// echo $sql;die;
	$nameArray=sql_select( $sql);
	$summary_data=$garph_data=array();$all_barcode="";
	foreach($nameArray as $row)
	{
		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("dia")]]["grey_qnty"]+=$row[csf("grey_qnty")];
		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("dia")]]["batch_issue_qtny"]+=$batch_issue_qtny_arr[$row[csf("roll_id")]][$row[csf("po_id")]];
		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("dia")]]["batch_creat_qnty"]+=$batch_creat_qnty_arr[$row[csf("roll_id")]][$row[csf("po_id")]];
		
		$garph_data[1]+=$row[csf("grey_qnty")];
		$garph_caption[1]="Grey Wgt";
		
		if($row[csf("entry_form")]==2)
		{
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_delivery"]>0)
			{
				$garph_data[2]+=$row[csf("grey_qnty")];
				$garph_caption[2]="Delv. To Store";
			}
			else
			{
				$garph_data[2]+=0;
				$garph_caption[2]="Delv. To Store";
			}
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_store"]>0)
			{
				$garph_data[3]+=$row[csf("grey_qnty")];
				$garph_caption[3]="Recv. by Store";
			}
			else
			{
				$garph_data[3]+=0;
				$garph_caption[3]="Recv. by Store";
			}
		}
		else
		{
			$garph_data[2]+=$row[csf("grey_qnty")];
			$garph_caption[2]="Delv. To Store";
			$garph_data[3]+=$row[csf("grey_qnty")];
			$garph_caption[3]="Recv. by Store";
		}
		
		
		if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_issue_batch"]>0)
		{
			$garph_data[4]+=$row[csf("grey_qnty")];
			$garph_caption[4]="Issue to Batch";
		}
		else
		{
			$garph_data[4]+=0;
			$garph_caption[4]="Issue to Batch";
		}
		if($variable_data_arr[50]["fabric_roll_level"]==1)
		{
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_batch"]>0)
			{
				$garph_data[5]+=$row[csf("grey_qnty")];
				$garph_caption[5]="Recv. by Batch";
			}
			else
			{
				$garph_data[5]+=0;
				$garph_caption[5]="Recv. by Batch";
			}
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["batch_created"]>0)
			{
				$garph_data[6]+=$row[csf("grey_qnty")];
				$garph_caption[6]="Batch Create";
			}
			else
			{
				$garph_data[6]+=0;
				$garph_caption[6]="Batch Create";
			}
		}
		
		$p=6;
		//$upto_receive_batch
		if($variable_data_arr[50]["page_upto_id"]>0)
		{
			for($i=1;$i<=$variable_data_arr[50]["page_upto_id"];$i++)
			{
				if($sub_process_data[$row[csf("roll_id")]][$i]>0)
				{
					$p++;
					$garph_data[$p]+=$row[csf("grey_qnty")];
					$garph_caption[$p]="".$upto_receive_batch[$i]."";
				}
				else
				{
					$p++;
					$garph_data[$p]+=0;
					$garph_caption[$p]="".$upto_receive_batch[$i]."";
				}
				
			}
		}
		
		
		if($variable_data_arr[2]["fabric_roll_level"]==1)
		{
			//if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion"]>0)
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion"]>0)
			{
				$garph_data[$p+1]+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
				$garph_caption[$p+1]="Finish Wgt";
			}
			else
			{
				$garph_data[$p+1]+=0;
				$garph_caption[$p+1]="Finish Wgt";
			}
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_delivery"]>0)
			{
				$garph_data[$p+2]+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
				$garph_caption[$p+2]="Delv. To Store";
			}
			else
			{
				$garph_data[$p+2]+=0;
				$garph_caption[$p+2]="Delv. To Store";
			}
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_rcv_store"]>0)
			{
				$garph_data[$p+3]+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
				$garph_caption[$p+3]="Recv. by Store";
			}
			else
			{
				$garph_data[$p+3]+=0;
				$garph_caption[$p+3]="Recv. by Store";
			}
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_issu_cut"]>0)
			{
				$garph_data[$p+4]+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
				$garph_caption[$p+4]="Issue to Cut";
			}
			else
			{
				$garph_data[$p+4]+=0;
				$garph_caption[$p+4]="Issue to Cut";
			}
			if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_receive_cut"]>0)
			{
				$garph_data[$p+5]+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
				$garph_caption[$p+5]="Recv. by Cut";
			}
			else
			{
				$garph_data[$p+5]+=0;
				$garph_caption[$p+5]="Recv. by Cut";
			}
		}
		if($all_barcode=="") $all_barcode=$row[csf('barcode_no')];else $all_barcode.=",".$row[csf('barcode_no')];
	}
	$all_barcode_cond="";
	if($all_barcode)
	{
		$all_barcode_data=chop($all_barcode,','); 
		$all_barcode_arr=count(array_unique(explode(",",$all_barcode)));
		if($db_type==2 && $all_barcode_arr>1000)
		{
			$all_barcode_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$all_barcode_data),999);
			foreach($poIdsArr as $barcode)
			{
				$barcode=implode(",",$barcode);
				$all_barcode_cond.=" barcode_no in($barcode) or"; 
			}
			$all_barcode_cond=chop($all_barcode_cond,'or ');
			$all_barcode_cond.=")";
		}
		else
		{
			$all_barcode_data=implode(",",(array_unique(explode(",",$all_barcode_data))));
			$all_barcode_cond=" and barcode_no in($all_barcode_data)";
		}
	}
	//echo $all_barcode_cond;die;
	//echo "<pre>";print_r($garph_caption);
	//echo "<pre>";print_r($garph_data);die;
	//var_dump($garph_data);die;
	$position_query="SELECT roll_id, po_breakdown_id, 
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
	sum(case when entry_form=66 then qnty else 0 end) as finishion_qnty,
	max(case when entry_form=67 then roll_id else 0 end) as fin_delivery,
	max(case when entry_form=67 then insert_date else null end) as fin_delivery_date,
	max(case when entry_form=68 then roll_id else 0 end) as fin_rcv_store,
	max(case when entry_form=68 then insert_date else null end) as fin_rcv_store_date,
	max(case when entry_form=71 then roll_id else 0 end) as fin_issu_cut,
	max(case when entry_form=71 then insert_date else null end) as fin_issu_cut_date,
	max(case when entry_form=72 then roll_id else 0 end) as fin_receive_cut,
	max(case when entry_form=72 then insert_date else null end) as fin_receive_cut_date
	from pro_roll_details where status_active=1 and is_deleted=0 and roll_id>0 $barcode_cond_prod $all_barcode_cond group by roll_id, po_breakdown_id";
	// echo $position_query;die;
	$position_sql=sql_select($position_query);
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

	//$sqll_mst=sql_select("select total_penalty_point, total_point,fabric_grade from pro_qc_result_mst where status_active=1 and is_deleted=0 $all_barcode_cond");

	$fabric_grade_data_arr=return_library_array( "select barcode_no, fabric_grade from pro_qc_result_mst where status_active=1 and is_deleted=0 $all_barcode_cond", "barcode_no", "fabric_grade");

	
	$div_width=1758;
	$table_width=1740;
	$coll_span=16;
	if($variable_data_arr[50]["fabric_roll_level"]==1)
	{
		$div_width=$div_width+140;
		$table_width=$table_width+140;
		$coll_span=$coll_span+2;
	}
	if($variable_data_arr[50]["page_upto_id"]>0)
	{
		$div_width=$div_width+(70*$variable_data_arr[50]["page_upto_id"]);
		$table_width=$table_width+(70*$variable_data_arr[50]["page_upto_id"]);
		$coll_span=$coll_span+$variable_data_arr[50]["page_upto_id"];
	}
	if($variable_data_arr[2]["fabric_roll_level"]==1)
	{
		$div_width=$div_width+570;
		$table_width=$table_width+570;
		$coll_span=$coll_span+8;
	}
			
	ob_start();
	?>
    
    <div style="width:<? echo $div_width; ?>px;">
	<fieldset style="width:<? echo $div_width; ?>px;">
     
    	<p style="color:red; font-size:18px; font-weight:bold; text-align:left; padding-left:10px;">Note : Column Total Will Not Recalculate With html Filter.</p>
		<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
			<tr>
			   <td align="center" width="100%" colspan="<? echo $coll_span; ?>" class="form_caption"><? echo $report_title; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Buyer & File No</th>
					<th width="130">Job No & Style Ref</th>
                    <th width="100">Order No</th>
					<th width="60">Roll No</th>
                    <th width="100">Barcode No</th>
                    <th width="100">Program No</th>
                    <th width="100">Color Range</th>
                    <th width="100">Body Part</th>
                    <th width="100">Color</th>
                    <th width="170">Fabric Description</th>
					<th width="60">GSM</th>
                    <th width="60">Dia</th>
                    <th width="60">Stich Lenth</th>
                    <th width="60">Machine Dia</th>
                    <th width="60">Guage</th>
                    <th width="80">Grey Wgt.</th>
                    <th width="70">Delv. To Store</th>
                    <th width="70">Recv. by Store</th>
                    <th width="70">Issue to Batch</th>
                    <?
					if($variable_data_arr[50]["fabric_roll_level"]==1)
					{
						?>
                        <th width="70">Recv. by Batch</th>
                        <th width="70">Batch Create</th>
                        <?
					}
					if($variable_data_arr[50]["page_upto_id"]>0)
					{
						for($i=1;$i<=$variable_data_arr[50]["page_upto_id"];$i++)
						{
							?>
                            <th width="70"><? echo $upto_receive_batch[$i]; ?></th>
                            <?
						}
					}
					if($variable_data_arr[2]["fabric_roll_level"]==1)
					{
						?>
                        <th width="70">Finish</th>
                        <th width="70">Finish Wgt.</th>
                        <th width="70">Process Loss</th>
                        <th width="70">Delv. To Store</th>
                        <th width="70">Recv. by Store</th>
                        <th width="70">Issue to Cut</th>
                        <th width="70">Recv. by Cut</th>
                        <th>Cutted</th>
                        <?
					}
					?>
                   <th width="60">QC Result</th> 
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $div_width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="table_body">
				<?
					//echo $sql;
					
					$m=1;
					$tot_grey_delivery=$tot_grey_rcv_store=$tot_grey_issue_batch=$tot_grey_rcv_batch=$tot_batch_created=$tot_fin_delivery=$tot_fin_rcv_store=$tot_fin_issu_cut=0;
					foreach ($nameArray as $row)
					{
						if ($m%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$grey_delivery_bgcolor=$grey_rcv_store_bgcolor=$grey_issue_batch_bgcolor=$grey_rcv_batch_bgcolor=$batch_created_bgcolor=$finishion_bgcolor=$fin_delivery_bgcolor=$fin_rcv_store_bgcolor=$fin_rcv_store_bgcolor='';
						
						$grey_delivery_day=$grey_rcv_store_day=$grey_issue_batch_day=$grey_rcv_batch_day=$batch_created_day=$finishion_day=$fin_delivery_day=$fin_rcv_store_day=$fin_issu_cut_day="";
						
						if($row[csf("entry_form")]==2)
						{
							if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_delivery"]>0)
							{
								$grey_delivery_bgcolor='bgcolor="green"';
								$grey_delivery_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_delivery_date"];
								$tot_grey_delivery+=$row[csf("grey_qnty")];
								
							}
							if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_store"]>0)
							{
								$grey_rcv_store_bgcolor='bgcolor="green"'; 
								$grey_rcv_store_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_store_date"]; 
								$tot_grey_rcv_store+=$row[csf("grey_qnty")];
							}
						}
						else
						{
							$grey_delivery_bgcolor='bgcolor="green"';
							$grey_delivery_day=$row[csf("insert_date")];
							$tot_grey_delivery+=$row[csf("grey_qnty")];
							
							$grey_rcv_store_bgcolor='bgcolor="green"'; 
							$grey_rcv_store_day=$row[csf("insert_date")]; 
							$tot_grey_rcv_store+=$row[csf("grey_qnty")];
							
						}
						
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_issue_batch"]>0)
						{
							$grey_issue_batch_bgcolor='bgcolor="green"';
							$grey_issue_batch_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_issue_batch_date"];
							$tot_grey_issue_batch+=$row[csf("grey_qnty")];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_batch"]>0)
						{
							$grey_rcv_batch_bgcolor='bgcolor="green"';
							$grey_rcv_batch_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["grey_rcv_batch_date"];
							$tot_grey_rcv_batch+=$row[csf("grey_qnty")];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["batch_created"]>0)
						{
							$batch_created_bgcolor='bgcolor="green"';
							$batch_created_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["batch_created_date"];
							$tot_batch_created+=$row[csf("grey_qnty")];
						}
						$sub_process_bgcolor=$sub_process_day=array();
						if($variable_data_arr[50]["page_upto_id"]>0)
						{
							for($i=1;$i<=$variable_data_arr[50]["page_upto_id"];$i++)
							{
								if($sub_process_data[$row[csf("roll_id")]][$i]>0)
								{
									$sub_process_bgcolor[$i]='bgcolor="green"';
									$sub_process_day[$i]=$sub_process_dateTime[$row[csf("roll_id")]][$i];
									$tot_sub_process[$i]+=$row[csf("grey_qnty")];
								}
								
							}
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion"]>0)
						{
							$finishion_bgcolor='bgcolor="green"';
							$finishion_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_date"];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_delivery"]>0)
						{
							$fin_delivery_bgcolor='bgcolor="green"';
							$fin_delivery_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_delivery_date"];
							$tot_fin_delivery+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_rcv_store"]>0)
						{
							$fin_rcv_store_bgcolor='bgcolor="green"';
							$fin_rcv_store_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_rcv_store_date"];
							$tot_fin_rcv_store+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						}
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_issu_cut"]>0)
						{
							$fin_issu_cut_bgcolor='bgcolor="green"';
							$fin_issu_cut_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_issu_cut_date"];
							$tot_fin_issu_cut+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						}
						
						if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_receive_cut"]>0)
						{
							$fin_receive_cut_bgcolor='bgcolor="green"';
							$fin_receive_cut_day=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["fin_receive_cut_date"];
							$tot_fin_receive_cut+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
						}
						if($row[csf("entry_form")]==2) $roll_entry_form="Production"; else $roll_entry_form="Receive";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>"> 
							<td width="30" align="center"><? echo $m; ?></td>
                            <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]."<br>".$row[csf("file_no")]; ?>&nbsp;</p></td>
                            <td width="125"><p><? echo $row[csf("job_no")]."<br>".$row[csf("style_ref_no")]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $row[csf("po_number")]."<br>".$row[csf("grouping")];; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><a href="##" onclick="openmypage_popup('<? echo $row[csf("roll_id")]; ?>','roll_popup')"><? echo $row[csf("roll_no")]; ?></a>&nbsp;</p></td>
                            <td width="100" align="center"><p><? echo $row[csf("barcode_no")]; ?>&nbsp;</p></td>
                            <td width="100" align="center"><p><? if($row[csf("receive_basis")]==2) {echo $row[csf("booking_no")];} ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $color_range[$row[csf("color_range_id")]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $color_library[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                            <td width="170"><p><? echo $composition_arr[$row[csf("febric_description_id")]]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $row[csf("gsm")]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $row[csf("dia")]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $row[csf("stitch_length")]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $machine_data[$row[csf("machine_no_id")]]["dia_width"]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $machine_data[$row[csf("machine_no_id")]]["gauge"]; ?>&nbsp;</p></td>
                            <td width="80" align="right" title="<? echo $roll_entry_form."**".$row[csf("roll_id")]."**".$row[csf("po_id")]; ?>"><? echo number_format($row[csf("grey_qnty")],2); $total_grey_qnty+=$row[csf("grey_qnty")]; ?></td>
                            <td width="70" align="center" style="word-break:break-all;" valign="middle" <? echo $grey_delivery_bgcolor; ?>><? echo $grey_delivery_day; ?></td>
                            <td width="70" align="center" style="word-break:break-all;" valign="middle" <? echo $grey_rcv_store_bgcolor; ?>><? echo $grey_rcv_store_day; ?></td>
                            <td width="70" align="center" style="word-break:break-all;" valign="middle" <? echo $grey_issue_batch_bgcolor; ?>><? echo $grey_issue_batch_day; ?></td>
                            <?
							if($variable_data_arr[50]["fabric_roll_level"]==1)
							{
								?>
                                <td width="70" align="center" style="word-break:break-all;" valign="middle" <? echo $grey_rcv_batch_bgcolor; ?>><? echo $grey_rcv_batch_day; ?></td>
                                <td width="70" align="center" style="word-break:break-all;" valign="middle" <? echo $batch_created_bgcolor; ?>><? echo $batch_created_day; ?></td>
                                <?
							}
							
							if($variable_data_arr[50]["page_upto_id"]>0)
							{
								for($i=1;$i<=$variable_data_arr[50]["page_upto_id"];$i++)
								{
									?>
									<td width="70" style="word-break:break-all" title="<? echo $row[csf("grey_qnty")]; ?>" align="center" valign="middle"  <? echo $sub_process_bgcolor[$i]; ?> ><? echo $sub_process_day[$i]; ?></td>
									<?
								}
							}
							
							if($variable_data_arr[2]["fabric_roll_level"]==1)
							{
								?>
                                <td width="70" style="word-break:break-all" align="center" valign="middle" <? echo $finishion_bgcolor; ?>><? echo $finishion_day; ?></td>
                                <td width="70" align="right"><? if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion"]>0) echo number_format($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"],2); $total_finishing_qnty+=$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"]; ?></td>
                                <td width="70" align="right">
                                <? 
                                $processes_loss=0;
                                $processes_loss=$row[csf("grey_qnty")]-$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
                                if($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"]>0)
                                {
                                    echo number_format($processes_loss,2);
                                    $total_processes_loss += $processes_loss;
                                }
                                
                                ?>
                                </td>
                                <td width="70" align="center" valign="right" <? echo $fin_delivery_bgcolor; ?>><? echo $fin_delivery_day; ?></td>
                                <td width="70" align="center" valign="right" <? echo $fin_rcv_store_bgcolor; ?>><? echo $fin_rcv_store_day; ?></td>
                                <td width="70" align="center"  valign="right" <? echo $fin_issu_cut_bgcolor; ?>><? echo $fin_issu_cut_day; ?></td>
                                <td width="70" align="center" valign="right" <? echo $fin_receive_cut_bgcolor; ?>><? echo $fin_receive_cut_day; ?></td>
                                <td align="center" valign="right" <? echo $fin_receive_cut_bgcolor; ?>><? echo $fin_receive_cut_day; ?></td>
                                
                                <?
							}
							
							?>
                            <td width="60" align="center"><p><a href="##" onclick="openmypage_popup_qc('<? echo $row[csf("barcode_no")]; ?>','qc_popup')">QC Result</a>
                            	<br><? if ($fabric_grade_data_arr[$row[csf("barcode_no")]]!="") 
                            	{
                            		echo "Fabric Grade: ".$fabric_grade_data_arr[$row[csf("barcode_no")]];
                            	}
                            	?>&nbsp;</p></td>
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
                    <!-- <th width="30">SL</th>
					<th width="100">Bu</th>
					<th width="130">Job</th>
                    <th width="100">Or</th>
					<th width="60">Roll</th>
                    <th width="100">Bar</th>
                    <th width="100">Prog</th>
                    <th width="100">Col</th>
                    <th width="100">Bod</th>
                    <th width="100">Co</th>
                    <th width="170">Fab</th>
					<th width="60">GSM</th>
                    <th width="60">Dia</th>
                    <th width="60">Sti</th>
                    <th width="60">Mac</th> -->
                    <th width="60" align="right" colspan="16">Total:</th>
                    <th width="80" align="right" id="value_total_grey_qnty"><? echo number_format($total_grey_qnty,2); ?></th>
                    <th width="70" align="right"><? echo number_format($tot_grey_delivery,2); $pending_grey_delivery=$total_grey_qnty-$tot_grey_delivery; ?></th>
                    <th width="70" align="right"><? echo number_format($tot_grey_rcv_store,2); $pending_grey_rcv_store=$tot_grey_delivery-$tot_grey_rcv_store; ?></th>
                    <th width="70" align="right"><? echo number_format($tot_grey_issue_batch,2); $pending_grey_issue_batch=$tot_grey_rcv_store-$tot_grey_issue_batch; ?></th>
                    <?
					if($variable_data_arr[50]["fabric_roll_level"]==1)
					{
						?>
                        <th width="70" align="right"><? echo number_format($tot_grey_rcv_batch,2); $pending_grey_rcv_batch=$tot_grey_issue_batch-$tot_grey_rcv_batch; ?></th>
                        <th width="70" align="right"><? echo number_format($tot_batch_created,2); $pending_batch_created=$tot_grey_rcv_batch-$tot_batch_created; ?></th>
                        <?
					}
					if($variable_data_arr[50]["page_upto_id"]>0)
					{
						for($i=1;$i<=$variable_data_arr[50]["page_upto_id"];$i++)
						{
							?>
							<th width="70" align="right" ><? echo number_format($tot_sub_process[$i],2); ?></th>
							<?
							if($i==1)
							{
								$pending_sub_process[$i]=$tot_batch_created-$tot_sub_process[$i];
							}
							else
							{
								$pending_sub_process[$i]=$tot_sub_process[$i-1];-$tot_sub_process[$i];
							}
						}
					}
					
					if($variable_data_arr[2]["fabric_roll_level"]==1)
					{
						?>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right"><? echo number_format($total_finishing_qnty,2); ?></th>
                        <th width="70" align="right"><? echo number_format($total_processes_loss,2); ?></th>
                        <th width="70" align="right"><? echo number_format($tot_fin_delivery,2); ?></th>
                        <th width="70" align="right"><? echo number_format($tot_fin_rcv_store,2); ?></th>
                        <th width="70" align="right"><? echo number_format($tot_fin_issu_cut,2); ?></th>
                        <th width="70" align="right"><? echo number_format($tot_fin_receive_cut,2); ?></th>
                        <th align="right"><? //echo number_format($fin_issu_cut_day,2); ?></th>
                        <?
						
					}
					?>
                    <th width="60">&nbsp;</th>
                </tr>
                <tr>
					<!--/*<th width="30">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					<th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="170">&nbsp;</th>
					<th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    */-->
                    <th width="100" align="right" colspan="16">Pending:</th>
                    <th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0,2); ?></th>
                    <th width="70" align="right"><? echo number_format($pending_grey_delivery,2); ?></th>
                    <th width="70" align="right"><? echo number_format($pending_grey_rcv_store,2); ?></th>
                    <th width="70" align="right"><? echo number_format($pending_grey_issue_batch,2); ?></th>
                    <?
					if($variable_data_arr[50]["fabric_roll_level"]==1)
					{
						
						?>
                        <th width="70" align="right"><? echo number_format($pending_grey_rcv_batch,2); ?></th>
                        <th width="70" align="right"><? echo number_format($pending_batch_created,2); ?></th>
                        <?
					}
					if($variable_data_arr[50]["page_upto_id"]>0)
					{
						for($i=1;$i<=$variable_data_arr[50]["page_upto_id"];$i++)
						{
							?>
							<th width="70" align="right" ><? echo number_format($pending_sub_process[$i],2); ?></th>
							<?
						}
					}
					
					if($variable_data_arr[2]["fabric_roll_level"]==1)
					{
						?>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th align="right">&nbsp;</th>
                        <?
					}
					?>
                    <th width="60">&nbsp;</th>
                </tr>
                <tr>
                	<!--<th width="30">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					<th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="170">&nbsp;</th>
					<th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>-->
                    <th width="100" align="right" colspan="16">Pending%:</th>
                    <th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0,2); ?></th>
                    <th width="70" align="right"><? echo number_format(($pending_grey_delivery/$total_grey_qnty)*100,2); ?></th>
                    <th width="70" align="right"><? echo number_format(($pending_grey_rcv_store/$tot_grey_delivery)*100,2); ?></th>
                    <th width="70" align="right"><? echo number_format(($pending_grey_issue_batch/$tot_grey_rcv_store)*100,2); ?></th>
                    <?
					if($variable_data_arr[50]["fabric_roll_level"]==1)
					{
						?>
                        <th width="70" align="right"><? echo number_format(($pending_grey_rcv_batch/$tot_grey_issue_batch)*100,2); ?></th>
                        <th width="70" align="right"><? echo number_format(($pending_batch_created/$tot_grey_rcv_batch)*100,2); ?></th>
                        <?
					}
					if($variable_data_arr[50]["page_upto_id"]>0)
					{
						for($i=1;$i<=$variable_data_arr[50]["page_upto_id"];$i++)
						{
							if($i==1)
							{
								?>
                                <th width="70" align="right" ><? echo number_format(($pending_sub_process[$i]/$tot_batch_created)*100,2); ?></th>
                                <?
							}
							else
							{
								?>
                                <th width="70" align="right" ><? echo number_format(($pending_sub_process[$i]/$tot_sub_process[$i-1])*100,2); ?></th>
                                <?
							}
							
						}
					}
					
					if($variable_data_arr[2]["fabric_roll_level"]==1)
					{
						?>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="70" align="right">&nbsp;</th>
                        <th align="right">&nbsp;</th>
                        <?
					}
					?>
                    <th width="60">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
    </div>      
<?
	
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
	echo "$total_data####$filename####$garph_caption####$garph_data";
	
	disconnect($con);
	exit();
}


if($action=="roll_popup")
{
	echo load_html_head_contents("Roll Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$roll_id=str_replace("'","",$roll_id);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	?>
	<script>
		
		function js_set_val() 
		{
			parent.emailwindow.hide();
		}
	
    </script>
	<fieldset style="width:1080px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="100">Program No/ Booing No</th>
                <th width="110">Production ID</th>
                <th width="80">Barcode NO</th>
                <th width="150">Knitting Party Name</th>
                <th width="70">Yarn Issue Ch. No</th>
                <th width="120">Body Part</th>
                <th width="70">Stitch Length</th>
                <th width="70">Yarn Count</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Type</th>
                <th width="70">Lot</th>
                <th>Roll Qty</th>
            </thead>
                <?
                $i=1; $total_qnty=0;
				$sql="SELECT a.recv_number, a.booking_no, a.knitting_source, a.knitting_company, a.yarn_issue_challan_no, b.body_part_id, b.stitch_length, c.barcode_no, c.roll_no, c.qnty as roll_qnty, d.id as prod_id, b.yarn_lot, b.yarn_count, b.brand_id, d.yarn_type
					FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d 
					WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and c.id=$roll_id  and a.entry_form in(2) and c.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				//echo $sql; 
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
                    $total_qnty+=$row[csf('qnty')];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td><p>
						<? 
						if($row[csf('knitting_source')]==1) $knit_company=$company_arr[$row[csf('knitting_company')]];
						else   $knit_company=$supplier_arr[$row[csf('knitting_company')]];
						echo $knit_company; 
						?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('yarn_issue_challan_no')]; ?>&nbsp;</p></td>
                        <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</td>
                        <td><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
                        <td><p>
						<?
						$all_yarn_count_arr=array_unique(explode(",",$row[csf('yarn_count')]));
						$all_yarn_count="";
						foreach($all_yarn_count_arr as $y_cont_id)
						{
							$all_yarn_count.=$yarn_count_arr[$y_cont_id].",";
						}
						$all_yarn_count=chop($all_yarn_count,",");
						echo $all_yarn_count; 
						//echo $row[csf('yarn_count')];
						?>&nbsp;</p></td>
                        <td align="center"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('roll_qnty')],2,'.',''); ?>&nbsp;</td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr>
                	<td colspan="13" align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" onclick="js_set_val()" value="Close"  /></td>	
                </tr>
            </table>
	</fieldset>   
<?
 	

exit();
}

if($action=="qc_popup")
{
	echo load_html_head_contents("QC Result Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$barcode_no=str_replace("'","",$barcode_no);
	$get_qc_id_arr=return_library_array( "select id, barcode_no from pro_qc_result_mst", "barcode_no", "id");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	?>
	<script>
		
		function js_set_val() 
		{
			parent.emailwindow.hide();
		}
	
    </script>
	<fieldset style="width:600px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="100">Defect Name</th>
                <th width="110">Defect Count </th>
                <th width="80">Found in (Inch)</th>
                <th width="70">Penalty Point</th>
            </thead>
                <?
                $i=1; $total_qnty=0;
				$mst_id=$barcode_no;
				$cond_barcode=$get_qc_id_arr[$mst_id];
				if($cond_barcode!="")
				{
					$sqll_details="select b.defect_name, b.defect_count, b.found_in_inch, b.found_in_inch_point, b.penalty_point from pro_qc_result_mst a,pro_qc_result_dtls b where $cond_barcode=b.mst_id group by b.defect_name, b.defect_count, b.found_in_inch, b.found_in_inch_point, b.penalty_point";
	                $result=sql_select($sqll_details);
	                foreach($result as $row)
	                {
	                    if ($i%2==0)  
	                        $bgcolor="#E9F3FF";
	                    else
	                        $bgcolor="#FFFFFF";	
	                
	                    //$total_qnty+=$row[csf('qnty')];
	                ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                        <td align="center"><? echo $i; ?></td>
	                        <td align="center"><? echo $knit_defect_array[$row[csf('defect_name')]]; ?></td>
	                        <td align="center"><? echo $row[csf('defect_count')]; ?></td>
	                        <td align="center"><? echo $knit_defect_inchi_array[$row[csf('found_in_inch')]]; ?></td>
	                        <td align="center"><? echo $row[csf('penalty_point')]; ?></td>
	                    </tr>
	                <?
	                $i++;
	                }
	                ?>
	                <tfoot>
	                <?
	                $sqll_mst=sql_select("select a.total_penalty_point, a.total_point,a.fabric_grade from pro_qc_result_mst a where a.id=$cond_barcode"); 
	                 foreach($sqll_mst as $result)
	                {
	                ?>
	                  <tr>
	                  	<td align="right" colspan="4" bgcolor="#CCCCCC"><b>Total Penalty Point:</b></td>
	                    <td align="right" bgcolor="#CCCCCC"><? echo $result[csf('total_penalty_point')]; ?></td>
	                  </tr>
	                  <tr>
	                  	<td align="right" colspan="4" bgcolor="#CCCCCC"><b>Total Point:</b></td>
	                    <td align="right" bgcolor="#CCCCCC"><? echo $result[csf('total_point')]; ?></td>
	                  </tr>
	                  <tr>
	                  	<td align="right" colspan="4" bgcolor="#CCCCCC"><b>Fabric Grade:</b></td>
	                    <td align="right" bgcolor="#CCCCCC"><? echo $result[csf('fabric_grade')]; ?></td>
	                  </tr>
	                  <?
					}
					?> 
	                <tr>
	                	<td colspan="13" align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" onclick="js_set_val()" value="Close"  /></td>	
	                </tr>
	                </tfoot>
	               <?
				}
			   else
				{
					echo '<p style="text-align:center;"><b>'."Data is not Found"."</b></p>";
				}
				?>
            </table>
	</fieldset>   
<?
 	

exit();
}
?>
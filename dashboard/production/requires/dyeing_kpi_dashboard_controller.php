<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",trim($cbo_company_name));
	$max_height=str_replace("'","",trim($max_height));
    $txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));

    $shift_data=sql_select("SELECT shift_name, start_time, end_time, cross_date from shift_duration_entry where production_type=2 and status_active=1 order by shift_name");
    // echo "<pre>";print_r($shift_data);die;
    $fastStartTime=$shift_data[0]['START_TIME'];
    $lastEndTimeb=$shift_data[1]['END_TIME'];
    $lastEndTimec=$shift_data[2]['END_TIME'];
    if ($lastEndTimec=="") {
    	$lastEndTime=$lastEndTimeb;
    }
    else
    {
    	$lastEndTime=$lastEndTimec;
    }
    // echo $fastStartTime.'='.$lastEndTime;die;

	$shift_name="";$cross_date = 0;$shift_end_time = "";
	foreach ($shift_data as $val)
	{
		$curr_time = new DateTime('now');
		$s_time = new DateTime($val[csf('start_time')]);
		$e_time = new DateTime($val[csf('end_time')]);

		$s_time1 = new DateTime($val[csf('start_time')]);
		$e_time1 = new DateTime($val[csf('end_time')]);

		$current_time = $curr_time->format('Y-m-d H:i:s a');

		$shft_end_time = strtotime($e_time->format('Y-m-d H:i'));

		$start_time = $s_time->format('Y-m-d H:i:s a');
		$end_time = $e_time->format('Y-m-d H:i:s a');

		$start_date = $s_time1->format('Y-m-d');
		$end_date = $e_time1->format('Y-m-d');

		if($start_time > $end_time){
			$end_time = $e_time->modify('+1 day')->format('Y-m-d H:i:s a');
			$start_time = $s_time->modify('-1 day')->format('Y-m-d H:i:s a');

			$start_date = $s_time1->modify('-1 day')->format('Y-m-d');
			$end_date = $e_time1->format('Y-m-d');
		}

		if ($fast_start_date=="") 
		{
			$fast_start_date = $start_date;
		}

		// echo $fast_start_date."=".$end_date."=".$val[csf('shift_name')]."<br>";
		// echo $current_time."=".$start_time."=".$end_time."=".$val[csf('shift_name')]."<br>";
		$cross_date = $val[csf('cross_date')];
		$shift_end_time = $val[csf('end_time')];
		//echo $cross_date." ".$shift_end_time."<br />";
		if( $current_time >= $start_time && $current_time <= $end_time && $shift_name=="")
		{
			$shift_name = $val[csf('shift_name')];

			if($val[csf('cross_date')] ==1)
			{
				$cross_date = $val[csf('cross_date')];
				$shift_end_time = $val[csf('end_time')];
			}else{
				$cross_date =0;
				$shift_end_time = "";
			}
		}
		// echo $current_time."=".$start_time."=".$end_time."<br>";
		//2022-03-28 00:31:42 =  2022-03-28 22:01:00 =  2022-03-29 06:00:00
	}

	$curr_time = new DateTime('now');
	$cur_time = strtotime($curr_time->format('Y-m-d H:i'));
	$shft_end_time = $e_time->format('Y-m-d H:i:s');
	if($cur_time < $shft_end_time)
	{
		
	}

	$curr_time = new DateTime('now', new DateTimezone('Asia/Dhaka'));

	if($cross_date == 0){
		$production_date = $curr_time->format('d-m-Y');
		$end_date1 = $curr_time->format('d-m-Y');
	}
	else
	{
		$e_time = new DateTime($shift_end_time);

		$current_time = $curr_time->format('Y-m-d H:i:s a');
		$end_time = $e_time->format('Y-m-d H:i:s a');

		$end_date1 = $curr_time->format('d-m-Y');
        if($current_time > $end_time)
        {
        	$production_date = $curr_time->format('d-m-Y');
        	$end_date1 = $curr_time->format('d-m-Y');
        }else{
        	$production_date = $curr_time->modify('-1 day')->format('d-m-Y');
        }
	}

	$cbo_company_id = return_field_value('id', 'lib_company',"company_name like'%$cbo_company_name%'");

	// Dyeing Production
	$start_date_and_time=$production_date.' '.$fastStartTime.':00';
	$end_date_and_time=$end_date1.' '.$lastEndTime.':00';

	$startDate = new DateTime($start_date_and_time);
	$endDate = new DateTime($end_date_and_time);//12-11-2023=13-11-2023
	// Format dates for Oracle
	$formattedStartDate = $startDate->format('Y-m-d H:i:s');
	$formattedEndDate = $shft_end_time;//$endDate->format('Y-m-d H:i:s');

	$insert_date_cond="TO_DATE('$formattedEndDate', 'YYYY-MM-DD HH24:MI:SS')";

	if ($fast_start_date!="" &&  $end_date!="") $batch_date_cond  = "and f.PROCESS_END_DATE  between '".change_date_format($fast_start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'"; else $batch_date_cond ="";

	$date_cond=" and f.PROCESS_END_DATE = '".change_date_format($production_date,'','',1)."'";
	// echo $end_date1;die;
	$batch_date_cond=" and a.INSERT_DATE < '".change_date_format($end_date1,'','',1)."'";

	$prod_sql="SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, A.BOOKING_WITHOUT_ORDER, A.COLOR_RANGE_ID, A.EXTENTION_NO, A.TOTAL_TRIMS_WEIGHT, A.DOUBLE_DYEING, F.SHIFT_NAME, F.PRODUCTION_DATE AS PROCESS_END_DATE, F.PROCESS_END_DATE AS PRODUCTION_DATE, F.END_HOURS, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.RESULT, C.PRODUCTION_QTY, 0 AS SUB_DYING_QNTY, F.ENTRY_FORM, f.SERVICE_SOURCE, d.MACHINE_GROUP
	from pro_batch_create_mst a, pro_fab_subprocess f, pro_fab_subprocess_dtls c, lib_machine_name d
	where a.id=f.batch_id and f.id=c.mst_id and f.MACHINE_ID=d.id and a.entry_form=0
	and f.entry_form=35 and f.LOAD_UNLOAD_ID in(1,2) and c.ENTRY_PAGE=35 and c.LOAD_UNLOAD_ID in(1,2) and a.batch_against in(1,11,2,3)  and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_id $date_cond
	union all
	SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, A.BOOKING_WITHOUT_ORDER, A.COLOR_RANGE_ID, A.EXTENTION_NO, A.TOTAL_TRIMS_WEIGHT, A.DOUBLE_DYEING, F.SHIFT_NAME, F.PRODUCTION_DATE AS PROCESS_END_DATE, F.PROCESS_END_DATE AS PRODUCTION_DATE, F.END_HOURS, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.RESULT, 0 AS PRODUCTION_QTY, B.BATCH_QNTY AS SUB_DYING_QNTY, F.ENTRY_FORM, f.SERVICE_SOURCE, d.MACHINE_GROUP
	from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess f, lib_machine_name d
	where a.id=f.batch_id and f.batch_id=b.mst_id and f.MACHINE_ID=d.id and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.LOAD_UNLOAD_ID in(1,2) and a.is_sales=0 and a.batch_against in(1,2)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.company_id=$cbo_company_id $date_cond";
	// echo $prod_sql;// and f.SHIFT_NAME in(1,2) and a.BATCH_NO in('23-Stock-Batch','23-testontu','dashb2','23-23-testontu2','23-tipu vi') 
	// and a.BATCH_NO in('dashb2','dashb')
	$prod_sql_result = sql_select($prod_sql);
	$totInhouseQnty=0;$totOutboundQnty=0;$totProductionQnty=0;$shiftA_totQnty=0;$shiftB_totQnty=0;
	$single_part_dyeing=0;$double_part_dyeing=0;$bulk_totQty=0;$sample_totQty=0;$others_total_qty=array(); 
	$bulk_shiftA_mc_no_count=0;$bulk_shiftB_mc_no_count=0;$inbound_subCon_totQty=0;$totReProcess=0; 
	$totInsideRft=0;$used_bulk_total_mc_count=0;$used_sample_total_mc_count=0;
	foreach ($prod_sql_result as $key => $row) 
	{
		if ($row['LOAD_UNLOAD_ID']==2) 
		{
			if ($row['RESULT']==1) // Shade Match Only
			{
				if ($row['SERVICE_SOURCE']==1) 
				{
					$totInhouseQnty += $row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
					if ($row['ENTRY_FORM']==38)
					{
						$inbound_subCon_totQty+=$row['SUB_DYING_QNTY'];
					}
				}
				if ($row['SERVICE_SOURCE']==3) 
				{
					$totOutboundQnty += $row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
				}
				$totProductionQnty += $row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];

				if ($row['SHIFT_NAME']==1) // Shift A
				{
					$shiftA_totQnty += $row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
					$bulk_shiftA_mc_no_count++;
				}
				if ($row['SHIFT_NAME']==2) // Shift B
				{
					$shiftB_totQnty+=$row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
					$bulk_shiftB_mc_no_count++;
				}
				if ($row['DOUBLE_DYEING']!=1) // Multi Dyeing Yes
				{
					$single_part_dyeing += $row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
				}
				if ($row['DOUBLE_DYEING']==1) // Multi Dyeing No
				{
					$double_part_dyeing+=$row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
				}
				if ($row['ENTRY_FORM']!=38) 
				{
					if ($row['BOOKING_WITHOUT_ORDER']==0)
					{
						$bulk_totQty+=$row['PRODUCTION_QTY'];
					}
					if ($row['BOOKING_WITHOUT_ORDER']==1)
					{
						$sample_totQty+=$row['PRODUCTION_QTY'];
					}
				}
				if ($row['COLOR_RANGE_ID']!=4 && $row['COLOR_RANGE_ID']!=34 && $row['COLOR_RANGE_ID']!=5 && $row['COLOR_RANGE_ID']!=2 && $row['COLOR_RANGE_ID']!=1 && $row['COLOR_RANGE_ID']!=1 && $row['COLOR_RANGE_ID']!=9) 
				{
					$others_total_qty[$row['SHIFT_NAME']]+=$row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
				}
				$shift_color_range_wise_qty_arr[$row['COLOR_RANGE_ID']][$row['SHIFT_NAME']]+=$row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
			}


			if ($row['EXTENTION_NO']!="" && $row['RESULT']==1) 
			{
				$totReProcess+= $row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
			}
			if ($row['EXTENTION_NO']=="" && $row['RESULT']==1) 
			{
				$totInsideRft+= $row['PRODUCTION_QTY']+$row['SUB_DYING_QNTY'];
			}
		}
		else
		{
			if ($row['MACHINE_GROUP']=='Bulk') 
			{
				if ($bulk_machine_check_arr[$row['MACHINE_ID']]=="") 
				{
					$used_bulk_total_mc_count++;
					$bulk_machine_check_arr[$row['MACHINE_ID']]=$row['MACHINE_ID'];
				}
			}

			if ($row['MACHINE_GROUP']=='Sample') 
			{
				if ($sample_machine_check_arr[$row['MACHINE_ID']]=="") 
				{
					$used_sample_total_mc_count++;
					$sample_machine_check_arr[$row['MACHINE_ID']]=$row['MACHINE_ID'];
				}
			}
		}
		$ready_for_dyeing_check_arr[$row['BATCH_ID']]=$row['BATCH_ID'];
	}

	// Prev dyeing production
	$productionDate=change_date_format($production_date,'','',1);
	$prev_dyeing="SELECT f.BATCH_ID, f.BATCH_NO from pro_fab_subprocess f
	where f.entry_form in(35,38) and f.LOAD_UNLOAD_ID in(1,2) and f.status_active=1 and f.is_deleted=0 
	and trunc(f.INSERT_DATE) >= '01-Dec-2023'  and f.PROCESS_END_DATE < '$productionDate'";
	// echo $prev_dyeing;die;
	$prev_dyeing_result = sql_select($prev_dyeing);
	foreach ($prev_dyeing_result as $key => $row) 
	{
		$prev_ready_for_dyeing_check_arr[$row['BATCH_ID']]=$row['BATCH_ID'];
		$batch_no_check_arr[$row['BATCH_NO']]=$row['BATCH_NO'];
	}
	// echo '<pre>';print_r($batch_no_check_arr);

	// READY FOR DYEING
	$batch_sql="SELECT A.ID, a.BATCH_NO, a.INSERT_DATE, a.BATCH_DATE, B.BATCH_QNTY FROM pro_batch_create_mst a, pro_batch_create_dtls b
	WHERE  trunc(a.BATCH_DATE) >= '01-Dec-2023' and a.BATCH_DATE <= '$productionDate' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(0,36) and a.batch_against in(1,11,2,3)
	order by a.BATCH_DATE desc";
	// echo $batch_sql;
	$batch_sql_result=sql_select($batch_sql);

	$today_number_of_batch=0;$previous_number_of_batch=0;
	$today_batch_qty=0;$previous_batch_qty=0;$today='';$previous='';
	foreach ($batch_sql_result as $row) 
	{
		if ($ready_for_dyeing_check_arr[$row['ID']]=="") 
		{
			if ( strtotime($productionDate) <= strtotime($row[csf('BATCH_DATE')]) && strtotime($productionDate) >= strtotime($row[csf('BATCH_DATE')]) ) // Today
			{
				$today_batch_qty+=$row['BATCH_QNTY'];

				if ($today_batch_check_arr[$row['ID']]=="") 
				{
					$today_number_of_batch++;
					$today_batch_check_arr[$row['ID']]=$row['ID'];
					$today.='Today: '.$row['BATCH_NO'].'='.$row['ID'].', ';
				}
			}		
		}
		if ($prev_ready_for_dyeing_check_arr[$row['ID']]=="") 
		{
			if( strtotime($productionDate) > strtotime($row[csf('BATCH_DATE')]))
			{
				$previous_batch_qty+=$row['BATCH_QNTY'];
				if ($previous_batch_check_arr[$row['ID']]=="") 
				{
					$previous_number_of_batch++;
					$previous_batch_check_arr[$row['ID']]=$row['ID'];
					// echo $row['BATCH_NO'].'<br>';
					$previous.='Previous: '.$row['BATCH_NO'].'='.$row['ID'].', ';
				}
			}
		}
	}
	// echo $today_number_of_batch.'='.$previous_number_of_batch;die;

	// Machine Sql for no of bulk and sample machine-------------
	$mc_sql="SELECT ID, FLOOR_ID, MACHINE_GROUP from lib_machine_name where company_id=$cbo_company_id and MACHINE_GROUP in('Bulk','Sample') and category_id=2 and status_active=1 and is_deleted=0";
	// echo $mc_sql;
	$mc_sql_result = sql_select($mc_sql);
	$bulk_total_mc_count=0;$sample_total_mc_count=0;
	foreach ($mc_sql_result as $key => $row) 
	{
		if ($row['MACHINE_GROUP']=='Bulk') 
		{
			$bulk_total_mc_count++;
		}
		if ($row['MACHINE_GROUP']=='Sample') 
		{
			$sample_total_mc_count++;
		}
	}
	//echo $bulk_total_mc_count.'==';
	?>

	<style type="text/css">
		#secondtime table, th, td {
		    border: 4px solid black;
		    border-collapse: collapse;
		}
		.main{
			font-family: Arial, Helvetica, sans-serif;
			background-color: #262626;
			color: #fff;
			font-weight: bold;
		}
		.head {height:8%;width:100%; font-weight:bold;font-size: 2.5vw;line-height: 2;}
		.body {height:70.5%;}
		.foot {height:12%; line-height: 1.38; margin-top: 10px; padding-top: 20px;}
		.left_section{width: 75%;height: 100%;float: left;}
		.left_section_prod_info{width: 100%;height: 20%;float: left;}
		.left_section_floor_info_1{width: 100%;height: 40%;float: left;}
		.left_section_floor_info_2{width: 100%;height: 40%;float: left;}		
		.right_section{width: 25%;height: 100%;float: left; line-height: 1.25;}
		.prod_info_1{width: 33%;height: 100%;float: left;border-bottom: 4px solid black; border-right: 4px solid black; border-left: 4px solid black;}
		.prod_info_2{width: 33%;height: 100%;float: left; border-bottom: 4px solid black; border-right: 4px solid black; }
		.prod_info_3{width: 32%;height: 100%;float: left; border-bottom: 4px solid black; border-right: 4px solid black;}
		.prod_info_1_val{width: 100%;height: 70%;line-height: 1.5;font-size: 4vw;text-align: center;font-weight: bold;}
		.prod_info_1_cap{width: 100%;height: 30%;font-size: 2vw;text-align: center;font-weight: bold; color: #0FF}
		.prod_info_2_val{width: 100%;height: 70%;line-height: 1.5;font-size: 4vw;text-align: center;font-weight: bold;}
		.prod_info_2_cap{width: 100%;height: 30%;font-size: 2vw;text-align: center;font-weight: bold; color: #0FF}
		.prod_info_3_val{width: 100%;height: 70%;line-height: 1.5;font-size: 4vw;text-align: center;font-weight: bold;}
		.prod_info_3_cap{width: 100%;height: 30%;font-size: 2vw;text-align: center;font-weight: bold; color: #0FF}
		.floor_info_1_1{width: 33%;height: 100%;float: left;border-bottom: 4px solid black; border-right: 4px solid black; border-left: 4px solid black;}
		.floor_info_1_2{width: 22%;height: 100%;float: left; border-bottom: 4px solid black; border-right: 4px solid black;}
		.floor_info_1_3{width: 21.3%;height: 97.5%;float: left; border-bottom: 4px solid black; border-right: 4px solid black; padding-top: 5px;}
		.floor_info_1_floor_cap{width: 100%;height: 15%;float: left;text-align: left;font-size: 1.5vw;padding-top: 5px;}
		.floor_info_1_floor_tot{width: 100%;height: 40%;float: left;}
		.floor_total_cap{width: 100%;font-size: 1.5vw;text-align: center;}
		.floor_total_val{width: 100%;font-size: 2.5vw;text-align: center;}
		.floor_info_1_floor_shipt_val{width: 100%;height: 20%;float: left;}
		.floor_info_1_floor_shipt_cap{width: 100%;height: 20%;float: left}
		.floor_shifp_val{width: 50%;float: left;font-size: 2.5vw;}
		.floor_shifp_cap{width: 50%;float: left;font-size: 1.5vw;}
		.floor_info_2_cap{	font-size: 1.5vw;height: 10%;text-align: left;padding-top: 5px;}
		.floor_info_2_val{	font-size: 2.5vw;height: 90%;line-height: 5;text-align: center;}
		.floor_info_3_cap{font-size: 1.5vw;height: 10%;text-align: left;}
		.floor_info_3_val{	font-size: 4vw;height: 90%;line-height: 3;text-align: center;}
		.floor_info_3_sam_cap{font-size: 1.5vw;width: 50%;height: 10%;text-align: left;float: left;}
		.floor_info_3_isam_cap{font-size: 1.5vw;height: 10%;width: 50%;text-align: left;float: left;}
		.floor_info_3_isam_val{font-size: 2.5vw;height: 90%;width: 50%;text-align: center; line-height: 5;}
		.floor_info_3_sam_val{font-size: 2.5vw;height: 90%;width: 50%;text-align: center;line-height: 5;}
		.floor_info_2_1{width: 33%;height: 100%;float: left;border-bottom: 4px solid black; border-right: 4px solid black; border-left: 4px solid black;}
		.floor_info_2_2{width: 33%;height: 100%;float: left; border-bottom: 4px solid black; border-right: 4px solid black;}
		.floor_info_2_3{width: 32%;height: 100%;float: left; border-bottom: 4px solid black; border-right: 4px solid black;}
		.floor_info_2_2_cap{font-size: 1.5vw;height: 10%;text-align: left; padding-top: 5px;}
		.floor_info_2_2_shipt_val{height: 70%; width: 100%; line-height: 3;text-align: center;}
		.floor_info_2_2_shipt_cap{font-size: 1vw;height: 20%;text-align: left;}
		.floor_info_2_2_shipt_value{font-size: 3.5vw;height: 100%;line-height: 3;text-align: center;float: left; width: 50%;}		
		.floor_info_2_2_shipt_caption{font-size: 1.5vw;height: 10%;width: 50%;text-align: center;float: left;}		
		.right_section_floor_1{height: 20%; margin-top: 3px; }
		.right_section_floor_2{height: 12%;}
		.right_section_floor_total{height: 15%;}
		.right_section_floor_qcwip{height: 15%; border: 4px solid black;}
		.right_section_cap{font-size: 1.2vw;  padding: 5px 0px;}
		.right_section_floor_1_cap{width: 100%; }
		.right_section_floor_11_cap{float: left;font-size: 1vw; width: 50%; }
		.right_section_floor_12_cap{float: left;font-size: 1vw; width: 50%; }
		.right_section_floor_1_val{width: 100%;}
		.right_section_floor_11_value{width: 50%; float: left; font-size: 2.5vw; line-height: 1;}
		.right_section_floor_12_value{width: 50%;float: left; font-size: 2.5vw; line-height: 1;}
		.right_section_floor_1_caption{width: 100%;}
		.right_section_floor_11_caption{width: 50%; float: left; font-size: 1.5vw; }
		.right_section_floor_12_caption{width: 50%; float: left; font-size: 1.5vw; }
		.right_section_floor_2_val{width: 100%;}
		.right_section_floor_21_value{width: 50%; float: left; font-size: 2.5vw; line-height: 1;}
		.right_section_floor_22_value{width: 50%;float: left; font-size: 2.5vw; line-height: 1;}
		.right_section_floor_2_caption{width: 100%;}
		.right_section_floor_21_caption{width: 50%; float: left; font-size: 1.5vw; }
		.right_section_floor_22_caption{width: 50%; float: left; font-size: 1.5vw; }
		.right_section_tot_cap{width: 100%;font-size: 1.5vw; text-align: left;}
		.right_section_tot_val{width: 100%;}
		.right_section_tot_val_1{width: 50%; float: left; font-size: 2.5vw; }
		.right_section_tot_val_2{width: 50%; float: left; font-size: 2.5vw; }
		.right_section_floor_qcwip_val{width: 100%; height: 20%;}
		.right_section_floor_qcwip_val_1{width: 50%; float: left; font-size: 2.5vw; line-height: 1.5;}
		.right_section_floor_qcwip_val_2{width: 50%; float: left; font-size: 2.5vw; line-height: 1.5;}
		.right_section_floor_qcwip_cap{width: 100%; height: 20%;}
		.right_section_floor_qcwip_cap_1{width: 50%; float: left; font-size: 1.5vw; }
		.right_section_floor_qcwip_cap_2{width: 50%; float: left; font-size: 1.5vw; }
		.total_color{color: #0FF;}
		
		/*flip start*/		
		.flip {
			animation: rotate 10s infinite; 
			-webkit-animation: rotate 10s infinite;
		}
		
		@-webkit-keyframes rotate {
			100% {
				transform: rotateY(0deg);
			}
		}
		@keyframes rotate {
			100% {
				transform: rotateY(359deg);
			}
		}
		/*flip end*/
		.reject_qty_table_css{
			width: 100%; border-collapse: collapse; text-align: center;
		}
		.reject_qty_table_td_css{
			font-size: 1.5vw;line-height: 2.5;
		}
		.total_reject_qty_table_td_css{
			font-size: 1.5vw;line-height: 1.3;
		}
		.nr_font_css{
			font-size: 1.5vw;
		}
		.borderTop{
			border-top: none;
		}
		.borderBottom{
			border-bottom: none;
		}
		.color-cyan{ background-color:#0FF; color:#000000;}
	</style>
	
	<div class="main" style="width:100%; height: <? echo $max_height; ?>px; display: none;" id="firsttime">
		<div class="head">
			<div style="width:6%; height: 100%; float: left; text-align: left; border: 4px solid black;vertical-align: middle;"><span style="vertical-align: middle;">
				<img class="flip" src="../../images/logic/fakir_fashion_logo.png" id="logininfo" width="115" style="width: 70px; height: 50px;"></span>
			</div>
			<div style="width:28.6%; height: 100%; float: left; text-align: center; border-bottom: 4px solid black; border-top: 4px solid black; border-right: 4px solid black; font-size: 1.8vw; vertical-align:middle;">DYEING KPI DASHBOARD
			</div>
			<div style="width:29%; height: 100%; float: left; text-align: center; border-bottom: 4px solid black; border-top: 4px solid black; border-right: 4px solid black; font-size: 1.8vw; vertical-align:middle;">PRODUCTION DATE: <?=$production_date;?>
			</div>
			<div style="width:28%; height: 100%; float: left; text-align: center; border-bottom: 4px solid black; border-top: 4px solid black; border-right: 4px solid black; font-size: 1.8vw;"><? echo date('F d, Y G:i A'); ?>
			</div>
			<div style="width:6%; height: 100%; float: left; text-align: center; border-bottom: 4px solid black; border-top: 4px solid black; border-right: 4px solid black;">
				<span>
				<img class="flip" src="../../images/logic/logic_logo_new.png" id="logininfo" width="115"></span>
			</div>
		</div>

		<div class="body">
			<div class="left_section">
				<div class="left_section_prod_info">
					<div class="prod_info_1">
						<div class="prod_info_1_val"><? echo fn_number_format($totInhouseQnty,2,'.','','0'); ?></div>
						<div class="prod_info_1_cap">INSIDE PRODUCTION</div>
					</div>
					<div class="prod_info_2">
						<div class="prod_info_2_val"><? echo fn_number_format($totOutboundQnty,2,'.','','0'); ?></div>
						<div class="prod_info_2_cap">OUTSIDE PRODUCTION</div>
					</div>
					<div class="prod_info_3">
						<div class="prod_info_3_val"><? echo fn_number_format($totProductionQnty,2,'.','','0'); ?></div>
						<div class="prod_info_3_cap">TOTAL PRODUCTION</div>
					</div>
				</div>
				<div class="left_section_floor_info_1">
					<div class="floor_info_1_1">
						<div class="floor_info_1_floor_cap">PRODUCTION</div>
						<div class="floor_info_1_floor_cap"></div>
						<div class="floor_info_1_floor_shipt_val">
							<div class="floor_shifp_val"><? echo fn_number_format($shiftA_totQnty,2,'.','','0'); ?></div>
							<div class="floor_shifp_val"><? echo fn_number_format($shiftB_totQnty,2,'.','','0'); ?></div>
						</div>
						<div class="floor_info_1_floor_shipt_cap">
							<div class="floor_shifp_cap">SHIFT-A</div>
							<div class="floor_shifp_cap">SHIFT-B</div>
						</div>
						
					</div>
					<div class="floor_info_1_2">
						<div class="floor_info_2_cap">BULK</div>
						<div class="floor_info_2_val"><? echo fn_number_format($bulk_totQty,2,'.','','0'); ?></div>
					</div>

					<div class="floor_info_1_3">
						<div class="floor_info_3_sam_cap">SAMPLE</div>
						<div class="floor_info_3_sam_val"><? echo fn_number_format($sample_totQty,2,'.','','0'); ?></div>						
					</div>
					<div class="floor_info_1_3">
						<div class="floor_info_3_cap">INBOUND SUBCON</div>
						<div class="floor_info_3_isam_val"><? echo fn_number_format($inbound_subCon_totQty,2,'.','','0'); ?></div>
					</div>
				</div>
				<div class="left_section_floor_info_2">
					<div class="floor_info_2_1">
						<div class="floor_info_1_floor_shipt_cap" style="padding-top: 5px;">
							<div class="floor_shifp_cap">SINGLE PART DYEING</div>
							<div class="floor_shifp_cap">DOUBLE PART DYEING</div>
						</div>
						<div class="floor_info_1_floor_cap"></div>
						<div class="floor_info_1_floor_shipt_val">
							<div class="floor_shifp_val"><? echo fn_number_format($single_part_dyeing,2,'.','','0'); ?></div>
							<div class="floor_shifp_val"><? echo fn_number_format($double_part_dyeing,2,'.','','0'); ?></div>
						</div>
						
					</div>
					<div class="floor_info_2_2">
						<div class="floor_info_2_2_cap">PLANNED</div>
						<div class="floor_info_2_2_shipt_val">
							<div class="floor_info_2_2_shipt_value"></div>
							<div class="floor_info_2_2_shipt_value"></div>
						</div>
						<div class="floor_info_2_2_shipt_cap">
							<div class="floor_info_2_2_shipt_caption"></div>
							<div class="floor_info_2_2_shipt_caption"></div>
						</div>
					</div>
					<div class="floor_info_2_3">
						<div class="floor_info_1_floor_cap">AVG. EFFICIENCY</div>
						<div class="floor_info_1_floor_tot">							
							<div class="floor_total_val"></div>
							<div class="floor_total_cap"></div>
						</div>
						<div class="floor_info_1_floor_shipt_val">
							<div class="floor_shifp_val"></div>
							<div class="floor_shifp_val"></div>
						</div>
						<div class="floor_info_1_floor_shipt_cap">
							<div class="floor_shifp_cap"></div>
							<div class="floor_shifp_cap"></div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class="right_section">
				<div>
					<table style="width: 100%; border-collapse: collapse; text-align: center;">
						<tr>
							<td class="nr_font_css" style="line-height: 2.5;" colspan="2" align="center">NOT RUNNING M/C</td>
						</tr>
						<tr style="border: none;!important;">
							<th class="nr_font_css">BULK<br>NO. OF M/C (<? echo $bulk_total_mc_count;?>)</th>
							<th class="nr_font_css">SAMPLE<br>NO. OF M/C (<? echo $sample_total_mc_count;?>)</th>
						</tr>
						<tr>
							<td style="height: 178px; font-size: 2.5vw;" class="nr_font_css borderTop"><? echo $bulk_total_mc_count-$used_bulk_total_mc_count; ?></td>
							<td style="height: 178px; font-size: 2.5vw;" class="nr_font_css borderTop"><? echo $sample_total_mc_count-$used_sample_total_mc_count; ?></td>
						</tr>					  
					</table>
				</div>
				<br>
				<br>
				<div class="right_section_floor_qcwip">
					<div class="right_section_floor_qcwip_val">
						<div class="right_section_floor_qcwip_val_1"><? echo fn_number_format($totReProcess,2,'.','','0'); ?></div>
						<div class="right_section_floor_qcwip_val_2"><? echo fn_number_format($totInsideRft,2,'.','','0'); ?></div>
					</div>
					<div class="right_section_floor_qcwip_cap">
						<div class="right_section_floor_qcwip_cap_1">RE-PROCESS</div>
						<div class="right_section_floor_qcwip_cap_2">INSIDE RFT</div>
					</div>
				</div>
				<br>
				<br>
				<div style="height: 44.5%;">
					<table class="reject_qty_table_css">
						<tr>
							<td class="reject_qty_table_td_css" colspan="2" align="center">READY FOR DYEING</td>
						</tr>
					  <tr style="border: none;!important;">
					    <th class="nr_font_css">NUMBER OF<br>BATCH</th>
					    <th class="nr_font_css">BATCH QTY</th>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderBottom" style="line-height: 1.5;">PREVIOUS</td>
					    <td class="nr_font_css borderBottom">PREVIOUS</td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderTop" title="<?=$previous;?>"><? echo fn_number_format($previous_number_of_batch,2,'.','','0'); ?></td>
					    <td class="nr_font_css borderTop"><? echo fn_number_format($previous_batch_qty,2,'.','','0'); ?></td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderBottom" style="line-height: 1.5;">TODAY</td>
					    <td class="nr_font_css borderBottom">TODAY</td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderTop" title="<?=$today;?>"><? echo fn_number_format($today_number_of_batch,2,'.','','0'); ?></td>
					    <td class="nr_font_css borderTop"><? echo fn_number_format($today_batch_qty,2,'.','','0'); ?></td>
					  </tr>
					</table>
				</div>
			</div>
		</div>

		<div class="foot">
			<table width="74.7%" style="float: left;" cellpadding="0" cellspacing="0" border="1" rules="all" >
				<tbody style="width: 80%;">
				<tr>
					<th  class="color-cyan" style="width: 10%; text-align:center; font-size: 1.5vw;">Shift</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">White/RFD</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Avg Color</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Light Color</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Dark Color</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Extra Dark</th>
					<th  class="color-cyan" style="text-align:center; font-size: 1.5vw;">Others</tr>
				<tr>
					<td style="width: 10%; text-align:center; font-size: 1.5vw; font-weight: bold;">A</td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[4][1]+$shift_color_range_wise_qty_arr[34][1],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[5][1],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[2][1],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[1][1],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[9][1],2,'.','','0'); ?></td>
					<td style="text-align:center; font-size: 1.5vw; font-weight: bold;">
						<? echo fn_number_format($others_total_qty[1],2,'.','','0'); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 10%; text-align:center; font-size: 1.5vw; font-weight: bold;">B</td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[4][2]+$shift_color_range_wise_qty_arr[34][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[5][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[2][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[1][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[9][2],2,'.','','0'); ?></td>
					<td style="text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($others_total_qty[2],2,'.','','0'); ?></td>
				</tr>
				<tr>
					<td style="width: 10%; text-align:center; font-size: 1.5vw; font-weight: bold;">Total</td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[4][1]+$shift_color_range_wise_qty_arr[34][1]+$shift_color_range_wise_qty_arr[4][2]+$shift_color_range_wise_qty_arr[34][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[5][1]+$shift_color_range_wise_qty_arr[5][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[2][1]+$shift_color_range_wise_qty_arr[2][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[1][1]+$shift_color_range_wise_qty_arr[1][2],2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($shift_color_range_wise_qty_arr[9][1]+$shift_color_range_wise_qty_arr[9][2],2,'.','','0'); ?></td>
					<td style="text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($others_total_qty[1]+$others_total_qty[2],2,'.','','0'); ?></td>
				</tr>
				</tbody>
			</table>
		</div>
    </div>
    <?
	exit();
}

?>
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

    $shift_data=sql_select("SELECT shift_name, start_time, end_time, cross_date from shift_duration_entry where production_type=1 and status_active=1 order by shift_name");
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

		if ($val[csf("shift_name")]==1) // Shift A
		{
			$shiftA_start_time=$val[csf("start_time")];
			$shiftA_end_time=$val[csf("end_time")];
		}

		if ($val[csf("shift_name")]==2) // Shift A
		{
			$shiftB_start_time=$val[csf("start_time")];
			$shiftB_end_time=$val[csf("end_time")];
		}
	}

	//echo $fast_start_date."=".$end_date."<br>";die;
	// echo $current_time."=".$start_time."=".$end_time."<br>";


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
        	// $start_date2 = $curr_time->modify('-1 day')->format('d-m-Y');
        }
	}
	// echo $production_date.'='.$end_date1;die;
	// echo $cross_date."=".$production_date;die;

	$cbo_company_id = return_field_value('id', 'lib_company',"company_name like'%$cbo_company_name%'");
	$shift_name_arr=array(1 => "A", 2 => "B");

	$con = connect();
    execute_query("DELETE from tmp_barcode_no where userid=1 and entry_form=300124");
    oci_commit($con);

	// Knitting Production, PRO_ROLL_DETAILS use for barcode wise reject qnty
	$prod_sql="SELECT A.RECEIVE_DATE as PRODUCT_DATE, A.KNITTING_SOURCE, a.ENTRY_FORM, B.SHIFT_NAME, B.FEBRIC_DESCRIPTION_ID as CONS_COMP_ID, a.BOOKING_WITHOUT_ORDER, b.MACHINE_NO_ID as MACHINE_ID, b.FLOOR_ID, c.QNTY as PRODUCT_QNTY, c.REJECT_QNTY as REJECT_FABRIC_RECEIVE, c.BARCODE_NO
	from inv_receive_master a, pro_grey_prod_entry_dtls b, PRO_ROLL_DETAILS c 
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.mst_id=c.mst_id and c.entry_form=2 and a.entry_form=2 and a.item_category=13 and a.knitting_source in(1,3) and a.receive_date >= '1-Dec-2023' and a.receive_date<='$txt_date' and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	union all
	SELECT a.PRODUCT_DATE, A.KNITTING_SOURCE, a.ENTRY_FORM, B.SHIFT as SHIFT_NAME, B.CONS_COMP_ID, null as BOOKING_WITHOUT_ORDER, b.MACHINE_ID, b.FLOOR_ID, b.PRODUCT_QNTY, 0 as REJECT_FABRIC_RECEIVE, 0 as BARCODE_NO
	from SUBCON_PRODUCTION_MST a, SUBCON_PRODUCTION_DTLS b where a.id=b.mst_id and a.entry_form=159 and a.knitting_source=1 and a.PRODUCT_DATE >= '1-Dec-2023' and a.PRODUCT_DATE<='$txt_date' and a.company_id=$cbo_company_id and a.knitting_company=$cbo_company_id and b.SHIFT in('1','2') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $prod_sql;// and a.knitting_company=$cbo_company_id and b.shift_name in(1,2)
	$prod_sql_result = sql_select($prod_sql);

	foreach ($prod_sql_result as $key => $val) 
	{
		if ($val['BARCODE_NO']>0) 
		{
			$barcode_no_arr[$val['BARCODE_NO']]=$val['BARCODE_NO'];
		}		
	}
	// echo '<pre>';print_r($barcode_no_arr);die;
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
	        // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	        execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form,type) values (1,$barcode_no,300124,1)");		
		}
		oci_commit($con);

		$qc_barcode_sql=sql_select("SELECT A.BARCODE_NO from tmp_barcode_no g, PRO_QC_RESULT_MST a where g.barcode_no=a.barcode_no and g.entry_form=300124 and g.userid=1 and a.status_active=1 and a.is_deleted=0");
		$qc_barcode_arr=array();	
		foreach($qc_barcode_sql as $rows)
		{
			$qc_barcode_arr[$rows["BARCODE_NO"]]=$rows["BARCODE_NO"];
		}
		// echo '<pre>';print_r($qc_barcode_arr);die;
	}

	$totInhouseQnty=0;$totOutboundQnty=0;$totProductionQnty=0;$floor1_totQnty=0;$floor2_totQnty=0;
	$floor1_shiftA_totQnty=0;$floor1_shiftB_totQnty=0;$floor2_shiftA_totQnty=0;$floor2_shiftB_totQnty=0;
	$bulk_totQty=0;$sample_totQty=0;$tot_qc_pass_qty=0;$tot_reject_qty=0;
	$floor1_shiftA_reject_qty=0;$floor1_shiftb_reject_qty=0;
	$floor1_shiftA_mc_no_count=0;$floor1_shiftB_mc_no_count=0;$floor2_shiftA_mc_no_count=0;$floor2_shiftB_mc_no_count=0;
	$inbound_subCon_totQty=0;$prevTotProductionQnty=0;$prev_tot_qc_pass_qty=0;
	$tot_product_qty_for_qc=array();$tot_reject_qty_for_qc=array();
	foreach ($prod_sql_result as $key => $row) 
	{
		// echo strtotime($row['PRODUCT_DATE']).'>'.strtotime($txt_date).'<br>';
		
		if (strtotime($row['PRODUCT_DATE']) < strtotime($txt_date)) // current date to previous data
		{
			$prevTotProductionQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];

			if ($row['ENTRY_FORM']!=159) 
			{
				if ($qc_barcode_arr[$row["BARCODE_NO"]]!="")//which barcode is qc
				{
					$prev_tot_qc_pass_qty+=$row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
				}
				
			}
		}
		else
		{
			// echo $row['PRODUCT_DATE'].'<br>';
			if ($row['KNITTING_SOURCE']==1) 
			{
				$totInhouseQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
				if ($row['ENTRY_FORM']==159)
				{
					$inbound_subCon_totQty+=$row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
				}
			}
			if ($row['KNITTING_SOURCE']==3) 
			{
				$totOutboundQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
			}

			$totProductionQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];

			if ($row['FLOOR_ID']==10) // Knitting Floor-01
			{
				$floor1_totQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
				if ($row['SHIFT_NAME']==1) // Shift A
				{
					$floor1_shiftA_totQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
					$floor1_shiftA_reject_qty+=$row['REJECT_FABRIC_RECEIVE'];
					$floor1_shiftA_mc_no_count++;
				}
				if ($row['SHIFT_NAME']==2) // Shift B
				{
					$floor1_shiftB_totQnty+=$row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
					$floor1_shiftB_reject_qty+=$row['REJECT_FABRIC_RECEIVE'];
					$floor1_shiftB_mc_no_count++;
				}
			}
			if ($row['FLOOR_ID']==67) // Knitting Floor-02
			{
				$floor2_totQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
				if ($row['SHIFT_NAME']==1) // Shift A
				{
					$floor2_shiftA_totQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
					$floor2_shiftA_reject_qty+=$row['REJECT_FABRIC_RECEIVE'];
					$floor2_shiftA_mc_no_count++;
				}
				if ($row['SHIFT_NAME']==2) // Shift B
				{
					$floor2_shiftB_totQnty += $row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
					$floor2_shiftB_reject_qty+=$row['REJECT_FABRIC_RECEIVE'];
					$floor2_shiftB_mc_no_count++;
				}
			}
			if ($row['ENTRY_FORM']!=159) // without SubCon Knitting Production
			{
				if ($row['BOOKING_WITHOUT_ORDER']==0)
				{
					// echo $row['PRODUCT_QNTY'].'<br>';
					$bulk_totQty+=$row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
				}
				if ($row['BOOKING_WITHOUT_ORDER']==1)
				{
					$sample_totQty+=$row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
				}

				if ($qc_barcode_arr[$row["BARCODE_NO"]]!="")//which barcode is qc
				{
					$tot_product_qty_for_qc[$row['KNITTING_SOURCE']]+=$row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
					$tot_reject_qty_for_qc[$row['KNITTING_SOURCE']]+=$row['REJECT_FABRIC_RECEIVE'];
				}				
			}
			$mc_wise_qty_arr[$row['MACHINE_ID']][$row['SHIFT_NAME']]+=$row['PRODUCT_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
		}
	}
	// echo $prevTotProductionQnty.'='.$prev_tot_qc_pass_qty.'<br>';
	// echo "<pre>";print_r($tot_product_qty_for_qc);

	// Machine Sql-------------
	$mc_sql="SELECT ID, FLOOR_ID, MACHINE_GROUP from lib_machine_name where company_id=$cbo_company_id and floor_id in(10,67) and category_id=1 and status_active=1 and is_deleted=0";
	// echo $mc_sql;
	$mc_sql_result = sql_select($mc_sql);
	$floor1_total_mc_count=0;$floor2_total_mc_count=0;
	$single_jersey_shiftA_total=0;$single_jersey_shiftB_total=0;$rib_shiftA_total=0; $rib_shiftB_total=0; $fleece_shiftA_total=0; $fleece_shiftB_total=0; $interlock_shiftA_total=0; $interlock_shiftB_total=0; $auto_stripe_shiftA_total=0; $auto_stripe_shiftB_total=0; $others_shiftA_total=0; $others_shiftB_total=0; // $single_jersey=array();
	foreach ($mc_sql_result as $key => $row) 
	{
		if ($row['FLOOR_ID']==10) 
		{
			$floor1_total_mc_count++;
		}
		if ($row['FLOOR_ID']==67) 
		{
			$floor2_total_mc_count++;
		}
		// echo $mc_wise_qty_arr[$row['ID']].'<br>';
		// $single_jersey[$row['MACHINE_GROUP']]+=$mc_wise_qty_arr[$row['ID']];
		if ($row['MACHINE_GROUP']=='Single Jersey') 
		{
			$single_jersey_shiftA_total+=$mc_wise_qty_arr[$row['ID']][1];
			$single_jersey_shiftB_total+=$mc_wise_qty_arr[$row['ID']][2];
		}
		else if ($row['MACHINE_GROUP']=='Rib') 
		{
			$rib_shiftA_total+=$mc_wise_qty_arr[$row['ID']][1];
			$rib_shiftB_total+=$mc_wise_qty_arr[$row['ID']][2];
		}
		else if ($row['MACHINE_GROUP']=='Fleece') 
		{
			$fleece_shiftA_total+=$mc_wise_qty_arr[$row['ID']][1];
			$fleece_shiftB_total+=$mc_wise_qty_arr[$row['ID']][2];
		}
		else if ($row['MACHINE_GROUP']=='Interlock') 
		{
			$interlock_shiftA_total+=$mc_wise_qty_arr[$row['ID']][1];
			$interlock_shiftB_total+=$mc_wise_qty_arr[$row['ID']][2];
		}
		else if ($row['MACHINE_GROUP']=='Auto Stripe') 
		{
			$auto_stripe_shiftA_total+=$mc_wise_qty_arr[$row['ID']][1];
			$auto_stripe_shiftB_total+=$mc_wise_qty_arr[$row['ID']][2];
		}
		else
		{
			$others_shiftA_total+=$mc_wise_qty_arr[$row['ID']][1];
			$others_shiftB_total+=$mc_wise_qty_arr[$row['ID']][2];
		}
	}
	// echo $interlock_shiftA_total;
	//echo "The time is " . date("Y-m-d h:i:sa");

	$current_date_time=date('Y-m-d');
	//$current_date_time="2023-08-31 12:13 a";
	//echo date('Y-m-d h:i:s a', strtotime(date('Y-m-d h:i:s a'))).'==<br>';
	//echo date('Y-m-d h:i:s a', strtotime(date('Y-m-d'))).'==<br>';
	//echo date('Y-m-d  h:i:s a').'<br>';
	$current_strtotime=strtotime(date('Y-m-d h:i'));

	/*echo strtotime($current_date_time.$shiftA_start_time).'<br>';
	echo $shiftA_start=date('Y-m-d h:i:s a', strtotime($current_date_time.$shiftA_start_time)).'<br>';
	echo $shiftA_end=date('Y-m-d h:i:s a', strtotime($current_date_time.$shiftA_end_time)).'<br>';
	echo $shiftB_start=date('Y-m-d h:i:s a', strtotime($current_date_time.$shiftB_start_time)).'<br>';
	$next_date=date('Y-m-d', strtotime('+1 day', strtotime($current_date_time)));
	echo $shiftB_end=date('Y-m-d h:i:s a', strtotime($next_date.$shiftB_end_time)).'<br>';*/

	$shiftA_start_strtotime=strtotime($current_date_time.$shiftA_start_time);
	$shiftA_end_strtotime=strtotime($current_date_time.$shiftA_end_time);

	$shiftB_start_strtotime=strtotime($current_date_time.$shiftB_start_time);
	$next_date=date('Y-m-d', strtotime('+1 day', strtotime($current_date_time)));
	$shiftB_end_strtotime=strtotime($next_date.$shiftB_end_time);

	/*if ($shiftB_start_strtotime<=$current_strtotime && $shiftB_end_strtotime>=$current_strtotime)
	{
		echo "Shift B=".$current_strtotime.'='.$shiftB_start_strtotime;
	}
	if ($shiftA_start_strtotime<=$current_strtotime && $shiftA_end_strtotime>=$current_strtotime)
	{
		echo "Shift A=".$current_strtotime.'='.$shiftA_start_strtotime.'='.$shiftA_end_strtotime;
	}*/

	execute_query("DELETE from tmp_barcode_no where userid=1 and entry_form=300124");
    oci_commit($con);
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
		.body {height:78%;}
		.foot {height:12%;}
		.left_section{width: 75%;height: 100%;float: left;}
		.left_section_prod_info{width: 100%;height: 20%;float: left;}
		.left_section_floor_info_1{width: 100%;height: 40%;float: left;}
		.left_section_floor_info_2{width: 100%;height: 40%;float: left;}		
		.right_section{width: 25%;height: 100%;float: left;}
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
		.floor_info_1_3{width: 21.3%;height: 98%;float: left; border-bottom: 4px solid black; border-right: 4px solid black; padding-top: 5px;}
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
		.right_section_floor_qcwip{height: 12%; border: 4px solid black;}
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
		.right_section_floor_qcwip_val_1{width: 50%; float: left; font-size: 2.5vw; line-height: 1;}
		.right_section_floor_qcwip_val_2{width: 50%; float: left; font-size: 2.5vw; line-height: 1;}
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

		.qc_check_css{
			font-size: 1.5vw;line-height: 2.5; border-left: none; border-right: none;
		}
		.qc_font_css{
			font-size: 1.5vw; line-height: 1.6; 
		}
		.qc_border_left_css{
			border-left: none; width: 30px;
		}
		.qc_border_right_css{
			border-right: none; width: 60px;
		}
	</style>
	
	<div class="main" style="width:100%; height: <? echo $max_height; ?>px; display: none;" id="firsttime">
		<div class="head">
			<div style="width:6%; height: 100%; float: left; text-align: left; border: 4px solid black;vertical-align: middle;"><span style="vertical-align: middle;">
				<img class="flip" src="../../images/logic/fakir_fashion_logo.png" id="logininfo" width="115" style="width: 70px; height: 50px;"></span>
			</div>
			<div style="width:29.5%; height: 100%; float: left; text-align: center; border-bottom: 4px solid black; border-top: 4px solid black; border-right: 4px solid black; font-size: 1.8vw; vertical-align:middle;">PRODUCTION DATE: <?=$production_date;?>
			</div>
			<div style="width:25.5%; height: 100%; float: left; text-align: center; border-bottom: 4px solid black; border-top: 4px solid black; border-right: 4px solid black; font-size: 1.8vw; vertical-align:middle;">KNITTING KPI DASHBOARD
			</div>
			<div style="width:31%; height: 100%; float: left; text-align: center; border-bottom: 4px solid black; border-top: 4px solid black; border-right: 4px solid black; font-size: 1.8vw;"><? echo date('l, F d, Y G:i'); ?>
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
						<div class="floor_info_1_floor_cap">KNITTING FLOOR-1</div>
						<div class="floor_info_1_floor_shipt_val">
							<div class="floor_shifp_val"><? echo fn_number_format($floor1_shiftA_totQnty,2,'.','','0'); ?></div>
							<div class="floor_shifp_val"><? echo fn_number_format($floor1_shiftB_totQnty,2,'.','','0'); ?></div>
						</div>
						<div class="floor_info_1_floor_shipt_cap">
							<div class="floor_shifp_cap">SHIFT-A</div>
							<div class="floor_shifp_cap">SHIFT-B</div>
						</div>
						<div class="floor_info_1_floor_tot">							
							<div class="floor_total_val"><? echo fn_number_format($floor1_totQnty,2,'.','','0');?></div>
							<div class="floor_total_cap total_color">TOTAL</div>
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
						<div class="floor_info_1_floor_cap">KNITTING FLOOR-2</div>
						<div class="floor_info_1_floor_shipt_val">
							<div class="floor_shifp_val"><? echo fn_number_format($floor2_shiftA_totQnty,2,'.','','0'); ?></div>
							<div class="floor_shifp_val"><? echo fn_number_format($floor2_shiftB_totQnty,2,'.','','0'); ?></div>
						</div>
						<div class="floor_info_1_floor_shipt_cap">
							<div class="floor_shifp_cap">SHIFT-A</div>
							<div class="floor_shifp_cap">SHIFT-B</div>
						</div>
						<div class="floor_info_1_floor_tot">							
							<div class="floor_total_val"><? echo fn_number_format($floor2_totQnty,2,'.','','0');?></div>
							<div class="floor_total_cap total_color">TOTAL</div>
						</div>
					</div>
					<div class="floor_info_2_2">
						<table class="reject_qty_table_css">
							<tr>
								<td class="qc_check_css" colspan="2" align="center">QC CHECK</td>
							</tr>
						  <tr style="border: none;!important;">
						    <th class="qc_font_css qc_border_left_css">INHOUSE</th>
						    <th class="qc_font_css qc_border_right_css"><? echo fn_number_format($inhouse_tot_qc_pass_qty=$tot_product_qty_for_qc[1]-$tot_reject_qty_for_qc[1],2,'.','','0'); ?></th>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css">OUTSIDE</td>
						    <td class="qc_font_css qc_border_right_css"><? echo fn_number_format($outside_tot_qc_pass_qty=$tot_product_qty_for_qc[3]-$tot_reject_qty_for_qc[3],2,'.','','0'); ?></td>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css total_color">TOTAL</td>
						    <td class="qc_font_css qc_border_right_css total_color"><? echo fn_number_format($inhouse_tot_qc_pass_qty+$outside_tot_qc_pass_qty,2,'.','','0'); ?></td>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css">PREVIOUS</td>
						    <td class="qc_font_css qc_border_right_css">--</td>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css total_color">GRAND TOTAL</td>
						    <td class="qc_font_css qc_border_right_css total_color"><? echo fn_number_format($inhouse_tot_qc_pass_qty+$outside_tot_qc_pass_qty,2,'.','','0'); ?></td>
						  </tr>
						</table>
					</div>
					<div class="floor_info_2_3">
						<table class="reject_qty_table_css">
							<tr>
								<td class="qc_check_css" colspan="2" align="center">WIP (QC)</td>
							</tr>
						  <tr style="border: none;!important;">
						    <th class="qc_font_css qc_border_left_css">INHOUSE</th>
						    <th class="qc_font_css qc_border_right_css"><? echo fn_number_format($tot_inhouse_wip=$totInhouseQnty-$inhouse_tot_qc_pass_qty,2,'.','','0'); ?></th>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css">OUTSIDE</td>
						    <td class="qc_font_css qc_border_right_css"><? echo fn_number_format($tot_outbound_wip=$totOutboundQnty-$outside_tot_qc_pass_qty,2,'.','','0'); ?></td>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css total_color">TOTAL</td>
						    <td class="qc_font_css qc_border_right_css total_color"><? echo fn_number_format($tot_wip_qty=$tot_inhouse_wip+$tot_outbound_wip,2,'.','','0');?></td>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css">PREVIOUS</td>
						    <td class="qc_font_css qc_border_right_css"><? echo fn_number_format($tot_prev_wip_qty=$prevTotProductionQnty-$prev_tot_qc_pass_qty,2,'.','','0'); ?></td>
						  </tr>
						  <tr>
						    <td class="qc_font_css qc_border_left_css total_color">GRAND TOTAL</td>
						    <td class="qc_font_css qc_border_right_css total_color"><? echo fn_number_format($tot_wip_qty+$tot_prev_wip_qty,2,'.','','0'); ?></td>
						  </tr>
						</table>
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
					    <th class="nr_font_css">KNITTING FLOOR-1 (<? echo $floor1_total_mc_count;?>)</th>
					    <th style="font-size: 1.5vw;">KNITTING FLOOR-2 (<? echo $floor2_total_mc_count;?>)</th>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderBottom" style="line-height: 1.5;">SHIFT-A</td>
					    <td class="nr_font_css borderBottom" style="">SHIFT-A</td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderTop"><? echo $floor1_total_mc_count-$floor1_shiftA_mc_no_count; ?></td>
					    <td class="nr_font_css borderTop"><? echo $floor2_total_mc_count-$floor2_shiftA_mc_no_count; ?></td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderBottom" style="line-height: 1.5;">SHIFT-B</td>
					    <td class="nr_font_css borderBottom">SHIFT-B</td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderTop"><? 
					    	if ($shiftB_start_strtotime<=$current_strtotime && $shiftB_end_strtotime>=$current_strtotime)
							{
								echo $floor1_total_mc_count-$floor1_shiftB_mc_no_count;
							}
							else
							{
								echo "0";
							}
							?>
						</td>
					    <td class="nr_font_css borderTop"><? 
					    	if ($shiftB_start_strtotime<=$current_strtotime && $shiftB_end_strtotime>=$current_strtotime)
							{
								echo $floor1_total_mc_count-$floor2_shiftB_mc_no_count;
							} 
							else
							{
								echo "0";
							}
							?>
						</td>
					  </tr>
					  
					</table>
				</div>
				<br>
				<div class="right_section_floor_qcwip">
					<div class="right_section_floor_qcwip_val">
						<div class="right_section_floor_qcwip_val_1"></div>
						<div class="right_section_floor_qcwip_val_2"></div>
					</div>
					<div class="right_section_floor_qcwip_cap">
						<div class="right_section_floor_qcwip_cap_1">PLANNED</div>
						<div class="right_section_floor_qcwip_cap_2">Efficiency</div>
					</div>
				</div>
				<br>
				<div>
					<table class="reject_qty_table_css">
						<tr>
							<td class="reject_qty_table_td_css" colspan="2" align="center">REJECTION (KG)</td>
						</tr>
					  <tr style="border: none;!important;">
					    <th class="nr_font_css">KNITTING FLOOR-1</th>
					    <th class="nr_font_css">KNITTING FLOOR-2</th>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderBottom" style="line-height: 1.5;">SHIFT-A</td>
					    <td class="nr_font_css borderBottom">SHIFT-A</td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderTop"><? echo fn_number_format($floor1_shiftA_reject_qty,2,'.','','0'); ?></td>
					    <td class="nr_font_css borderTop"><? echo fn_number_format($floor2_shiftA_reject_qty,2,'.','','0'); ?></td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderBottom" style="line-height: 1.5;">SHIFT-B</td>
					    <td class="nr_font_css borderBottom">SHIFT-B</td>
					  </tr>
					  <tr>
					    <td class="nr_font_css borderTop"><? echo fn_number_format($floor1_shiftB_reject_qty,2,'.','','0'); ?></td>
					    <td class="nr_font_css borderTop"><? echo fn_number_format($floor2_shiftB_reject_qty,2,'.','','0'); ?></td>
					  </tr>
					  <tr>
					    <td class="total_color nr_font_css borderBottom" style="line-height: 1.5;">TOTAL</td>
					    <td class="total_color nr_font_css borderBottom">TOTAL</td>
					  </tr>
					  <tr class="color-cyan">
					    <td class="nr_font_css borderTop"><? echo fn_number_format($floor1_shiftA_reject_qty+$floor1_shiftB_reject_qty,2,'.','','0'); ?></td>
					    <td class="nr_font_css borderTop"><? echo fn_number_format($floor2_shiftA_reject_qty+$floor2_shiftB_reject_qty,2,'.','','0'); ?></td>
					  </tr>
					    <tr>
							<td class="total_reject_qty_table_td_css" colspan="2" align="center">TOTAL REJECTION<br><? echo fn_number_format(($floor1_shiftA_reject_qty+$floor1_shiftB_reject_qty)+($floor2_shiftA_reject_qty+$floor2_shiftB_reject_qty),2,'.','','0');?></td>
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
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Single Jersey</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Rib</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Fleece</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Interlock</th>
					<th  class="color-cyan" style="width: 15%; text-align:center; font-size: 1.5vw;">Auto Stripe</th>
					<th  class="color-cyan" style="text-align:center; font-size: 1.5vw;">Others</tr>
				<tr>
					<td style="width: 10%; text-align:center; font-size: 1.5vw; font-weight: bold;">A</td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($single_jersey_shiftA_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($rib_shiftA_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($fleece_shiftA_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($interlock_shiftA_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($auto_stripe_shiftA_total,2,'.','','0'); ?></td>
					<td style="text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($others_shiftA_total,2,'.','','0'); ?></td>
				</tr>
				<tr>
					<td style="width: 10%; text-align:center; font-size: 1.5vw; font-weight: bold;">B</td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($single_jersey_shiftB_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($rib_shiftB_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($fleece_shiftB_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($interlock_shiftB_total,2,'.','','0'); ?></td>
					<td style="width: 15%; text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($auto_stripe_shiftB_total,2,'.','','0'); ?></td>
					<td style="text-align:center; font-size: 1.5vw; font-weight: bold;"><? echo fn_number_format($others_shiftB_total,2,'.','','0'); ?></td>
				</tr>
				</tbody>
			</table>
		</div>
    </div>
    <?
	exit();
}

?>
<?php
header('Content-type:text/html; charset=utf-8');
session_start();

// if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
// $user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if($action=="report_generate")
{	
	extract($_REQUEST);
	$process = array( &$_POST );
	
	$company_id=str_replace("'","",$cbo_company_name);
	$txt_date=str_replace("'","",$txt_date);
	$cbo_source=str_replace("'","",$cbo_source);

	// echo $txt_date.'===A';die;

	if($db_type==2)	$txt_date=change_date_format($txt_date,'M-d-Y','',1);
	// echo $txt_date.'===B';die;

	$buyerArr = return_library_array("select id,buyer_name from lib_buyer","id","buyer_name"); 
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name"); 
	
	/*===================================================================================== /
	/								dyeing production data									/
	/===================================================================================== */
	
	
	if($cbo_source == 1){ //Inhouse == MM order --Previous query
		$prod_sql="SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, a.COLOR_ID, A.BOOKING_WITHOUT_ORDER, A.COLOR_RANGE_ID, A.EXTENTION_NO, A.TOTAL_TRIMS_WEIGHT, A.DOUBLE_DYEING, a.EXP_LOAD_HR, a.EXP_LOAD_MIN, a.DUR_REQ_HR, a.DUR_REQ_MIN, F.SHIFT_NAME, F.PRODUCTION_DATE AS PROCESS_END_DATE, F.PROCESS_END_DATE AS PRODUCTION_DATE, F.END_HOURS, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.RESULT, C.PRODUCTION_QTY, 0 AS SUB_DYING_QNTY, F.ENTRY_FORM, f.SERVICE_SOURCE, f.END_HOURS, f.END_MINUTES, d.MACHINE_NO, d.MACHINE_CAPACITY, d.PROD_CAPACITY
		from pro_batch_create_mst a, pro_fab_subprocess f, pro_fab_subprocess_dtls c, lib_machine_name d
		where a.id=f.batch_id and f.id=c.mst_id and f.MACHINE_ID=d.id and a.entry_form=0
		and f.entry_form=35 and f.LOAD_UNLOAD_ID in(1,2) and c.ENTRY_PAGE=35 and c.LOAD_UNLOAD_ID in(1,2) and a.batch_against in(1,11,2,3)  and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id and f.PROCESS_END_DATE='$txt_date'";
	}else if($cbo_source == 2){ //Inbound == Subcon order
		$prod_sql = "SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, a.COLOR_ID, A.BOOKING_WITHOUT_ORDER, A.COLOR_RANGE_ID, A.EXTENTION_NO, A.TOTAL_TRIMS_WEIGHT, A.DOUBLE_DYEING, F.SHIFT_NAME, F.PRODUCTION_DATE AS PROCESS_END_DATE, F.PROCESS_END_DATE AS PRODUCTION_DATE, F.END_HOURS, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.RESULT, 0 AS PRODUCTION_QTY, B.BATCH_QNTY AS SUB_DYING_QNTY, F.ENTRY_FORM, f.SERVICE_SOURCE, d.MACHINE_NO, d.MACHINE_CAPACITY, d.PROD_CAPACITY
		from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess f, lib_machine_name d
		where a.id=f.batch_id and f.batch_id=b.mst_id and f.MACHINE_ID=d.id and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.LOAD_UNLOAD_ID in(1,2) and a.is_sales=0 and a.batch_against in(1,2)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.company_id=$company_id and f.PROCESS_END_DATE='$txt_date'";
	}else{
		$prod_sql="SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, a.COLOR_ID, A.BOOKING_WITHOUT_ORDER, A.COLOR_RANGE_ID, A.EXTENTION_NO, A.TOTAL_TRIMS_WEIGHT, A.DOUBLE_DYEING, a.EXP_LOAD_HR, a.EXP_LOAD_MIN, a.DUR_REQ_HR, a.DUR_REQ_MIN, 

		F.PRODUCTION_DATE AS PROCESS_END_DATE, F.PROCESS_END_DATE AS PRODUCTION_DATE, F.END_HOURS, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.RESULT, C.PRODUCTION_QTY, 0 AS SUB_DYING_QNTY, F.ENTRY_FORM, f.SERVICE_SOURCE, d.MACHINE_NO, d.MACHINE_CAPACITY, d.PROD_CAPACITY
		from pro_batch_create_mst a, pro_fab_subprocess f, pro_fab_subprocess_dtls c, lib_machine_name d
		where a.id=f.batch_id and f.id=c.mst_id and f.MACHINE_ID=d.id and a.entry_form=0
		and f.entry_form=35 and f.LOAD_UNLOAD_ID in(1,2) and c.ENTRY_PAGE=35 and c.LOAD_UNLOAD_ID in(1,2) and a.batch_against in(1,11,2,3)  and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id and f.PROCESS_END_DATE='$txt_date'
		
		union all

		SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, a.COLOR_ID, A.BOOKING_WITHOUT_ORDER, A.COLOR_RANGE_ID, A.EXTENTION_NO, A.TOTAL_TRIMS_WEIGHT, A.DOUBLE_DYEING, a.EXP_LOAD_HR, a.EXP_LOAD_MIN, a.DUR_REQ_HR, a.DUR_REQ_MIN,
		 F.PRODUCTION_DATE AS PROCESS_END_DATE, F.PROCESS_END_DATE AS PRODUCTION_DATE, F.END_HOURS, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.RESULT, 0 AS PRODUCTION_QTY, B.BATCH_QNTY AS SUB_DYING_QNTY, F.ENTRY_FORM, f.SERVICE_SOURCE, d.MACHINE_NO, d.MACHINE_CAPACITY, d.PROD_CAPACITY
		from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess f, lib_machine_name d
		where a.id=f.batch_id and f.batch_id=b.mst_id and f.MACHINE_ID=d.id and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.LOAD_UNLOAD_ID in(1,2) and a.is_sales=0 and a.batch_against in(1,2)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.company_id=$company_id and f.PROCESS_END_DATE='$txt_date'
		";
	}
	// and A.BATCH_NO='Batch Hub 3'
	//echo $prod_sql;die;
	$prod_sql_result = sql_select($prod_sql);

	$sql_resqlt=sql_select($sql);
	foreach ($prod_sql_result as $key => $row) 
	{
		if ($row['LOAD_UNLOAD_ID']==1)
		{
			$data_arr[$row['BATCH_ID']]['MACHINE_NO']=$row['MACHINE_NO'];
			$data_arr[$row['BATCH_ID']]['MACHINE_CAPACITY']+=$row['MACHINE_CAPACITY'];
			$data_arr[$row['BATCH_ID']]['PROD_CAPACITY']+=$row['PROD_CAPACITY'];
			$data_arr[$row['BATCH_ID']]['COLOR_ID']=$row['COLOR_ID'];
			$data_arr[$row['BATCH_ID']]['BATCH_NO']=$row['BATCH_NO'];
			$data_arr[$row['BATCH_ID']]['EXT_NO']=$row['EXTENTION_NO'];
			$data_arr[$row['BATCH_ID']]['PRODUCTION_QTY']+=$row['PRODUCTION_QTY'];
			$data_arr[$row['BATCH_ID']]['TOTAL_TRIMS_WEIGHT']+=$row['TOTAL_TRIMS_WEIGHT'];
			$data_arr[$row['BATCH_ID']]['LOAD_BATCH_DATE']=$row['PRODUCTION_DATE'];
			$data_arr[$row['BATCH_ID']]['EXP_LOAD_HR']=$row['EXP_LOAD_HR'];
			$data_arr[$row['BATCH_ID']]['EXP_LOAD_MIN']=$row['EXP_LOAD_MIN'];
			$data_arr[$row['BATCH_ID']]['ACTUAL_LOAD_HOURS']=$row['END_HOURS'];
			$data_arr[$row['BATCH_ID']]['ACTUAL_LOAD_MIN']=$row['END_MINUTES'];
			$data_arr[$row['BATCH_ID']]['DUR_REQ_HR']=$row['DUR_REQ_HR'];
			$data_arr[$row['BATCH_ID']]['DUR_REQ_MIN']=$row['DUR_REQ_MIN'];
			$data_arr[$row['BATCH_ID']]['LOAD_DATE']=$row['PRODUCTION_DATE'];

			$load_status_arr[$row['BATCH_ID']]='Running';
		}
		if ($row['LOAD_UNLOAD_ID']==2)
		{
			$data_arr[$row['BATCH_ID']]['UNLOAD_DATE']=$row['PRODUCTION_DATE'];	
			$data_arr[$row['BATCH_ID']]['ACTUAL_UNLOAD_HOURS']=$row['END_HOURS'];
			$data_arr[$row['BATCH_ID']]['ACTUAL_UNLOAD_MIN']=$row['END_MINUTES'];

			if ($row['RESULT']==1) 
			{
				$unload_status_arr[$row['BATCH_ID']]='Complete';
			}
			elseif ($row['RESULT']!=1) 
			{
				$unload_status_arr[$row['BATCH_ID']]=$dyeing_result[$row['RESULT']];
			}
		}

		$ready_for_dyeing_check_arr[$row['BATCH_ID']]=$row['BATCH_ID'];

		$all_batch_id_arr[$row['BATCH_ID']]=$row['BATCH_ID'];
	}
	// echo "<pre>";print_r($load_status_arr);die;

	// READY FOR DYEING
	$batch_sql="SELECT A.ID as BATCHID, a.BATCH_NO, a.BATCH_DATE, B.BATCH_QNTY, a.COLOR_ID, A.EXTENTION_NO, A.TOTAL_TRIMS_WEIGHT, a.EXP_LOAD_HR, a.EXP_LOAD_MIN, a.DUR_REQ_HR, a.DUR_REQ_MIN, b.PO_ID, d.MACHINE_NO, d.MACHINE_CAPACITY, d.PROD_CAPACITY 
	FROM pro_batch_create_mst a, pro_batch_create_dtls b, lib_machine_name d
	WHERE a.id=b.mst_id and a.dyeing_machine=d.id and a.BATCH_DATE='$txt_date' and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(0,36) and a.batch_against in(1,11,2,3) and a.is_sales=0";
	// echo $batch_sql;die;
	$batch_sql_result=sql_select($batch_sql);

	$today_number_of_batch=0;$previous_number_of_batch=0;
	$today_batch_qty=0;$previous_batch_qty=0;$today='';$previous='';
	foreach ($batch_sql_result as $row) 
	{
		if ($ready_for_dyeing_check_arr[$row['BATCHID']]=="") 
		{
			// echo $row['BATCH_NO'].'<br>';
			// $data_arr[$row['BATCHID']]['RESULT']='Ready for Load';
			$data_arr[$row['BATCHID']]['MACHINE_NO']=$row['MACHINE_NO'];
			$data_arr[$row['BATCHID']]['MACHINE_CAPACITY']+=$row['MACHINE_CAPACITY'];
			$data_arr[$row['BATCHID']]['PROD_CAPACITY']+=$row['PROD_CAPACITY'];
			$data_arr[$row['BATCHID']]['COLOR_ID']=$row['COLOR_ID'];
			$data_arr[$row['BATCHID']]['BATCH_NO']=$row['BATCH_NO'];
			$data_arr[$row['BATCHID']]['EXT_NO']=$row['EXTENTION_NO'];
			$data_arr[$row['BATCHID']]['PRODUCTION_QTY']+=$row['BATCH_QNTY'];
			$data_arr[$row['BATCHID']]['TOTAL_TRIMS_WEIGHT']+=$row['TOTAL_TRIMS_WEIGHT'];
			$data_arr[$row['BATCHID']]['LOAD_BATCH_DATE']=$row['BATCH_DATE'];
			$data_arr[$row['BATCHID']]['BATCH_QNTY']+=$row['BATCH_QNTY'];
			$data_arr[$row['BATCHID']]['EXP_LOAD_HR']=$row['EXP_LOAD_HR'];
			$data_arr[$row['BATCHID']]['EXP_LOAD_MIN']=$row['EXP_LOAD_MIN'];
			$data_arr[$row['BATCHID']]['DUR_REQ_HR']=$row['DUR_REQ_HR'];
			$data_arr[$row['BATCHID']]['DUR_REQ_MIN']=$row['DUR_REQ_MIN'];
			
			$all_batch_id_arr[$row['BATCHID']]=$row['BATCHID'];

			$ready_status_arr[$row['BATCHID']]='Ready for Load';
		}
	}
	// echo '<pre>';print_r($ready_status_arr);die;
	// echo "<pre>";print_r($load_status_arr); echo "<b>"; echo "<pre>";print_r($unload_status_arr);die;

	$batch_id_cond=where_con_using_array($all_batch_id_arr,0,"C.MST_ID");
	$result_job = sql_select("SELECT C.MST_ID, A.JOB_NO_MST, B.BUYER_NAME, b.STYLE_REF_NO
	from pro_batch_create_dtls c, wo_po_break_down a, wo_po_details_master b 
	where c.po_id=a.id and a.JOB_ID=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_id_cond
	group by c.mst_id, a.job_no_mst, b.buyer_name, b.STYLE_REF_NO");
	foreach ($result_job as $row) 
	{
		$job_arr[$row['MST_ID']]['JOB_NO_MST']=$row['JOB_NO_MST'];
		$job_arr[$row['MST_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
		$job_arr[$row['MST_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
	}
	// echo '<pre>';print_r($job_arr);die;

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$tbl_width = 1460;
	$tr_height = 30;

	ob_start();
    ?>

    <!-- <style type="text/css">
		#secondtime table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
	</style> -->

    <style type="text/css">
		td div{font-weight: bold;font-size: 14px;vertical-align: middle;}
		#new_style div { position: relative; }
		td#new_style div::before { position: absolute; left: 0; top: 0; width: 100%; height: 50%; background: #ffe800;z-index: 99999; }
		#new_style div { box-shadow: inset 0px 7px 0px #ffe800; }
		.rpt_table tfoot th, td,td p{font-weight: bold;vertical-align: middle;color: #FFFFFF;text-shadow: rgb(0, 0, 0) 1px 0px 0px, rgb(0, 0, 0) 0.540302px 0.841471px 0px, rgb(0, 0, 0) -0.416147px 0.909297px 0px, rgb(0, 0, 0) -0.989993px 0.14112px 0px, rgb(0, 0, 0) -0.653644px -0.756803px 0px, rgb(0, 0, 0) 0.283662px -0.958924px 0px, rgb(0, 0, 0) 0.96017px -0.279416px 0px;
			}
			/* text-shadow: rgb(0, 0, 0) 1px 0px 0px, rgb(0, 0, 0) 0.540302px 0.841471px 0px, rgb(0, 0, 0) -0.416147px 0.909297px 0px, rgb(0, 0, 0) -0.989993px 0.14112px 0px, rgb(0, 0, 0) -0.653644px -0.756803px 0px, rgb(0, 0, 0) 0.283662px -0.958924px 0px, rgb(0, 0, 0) 0.96017px -0.279416px 0px; */
		#table_body thead th,#table_body tfoot th{background: #191A19;}
		#table_body thead th{color: #FFFFFF;font-weight: bold;font-size: 16px;}
		#table_body tfoot th{font-weight: bold;font-size: 16px;}
		#table_body  tr{border-bottom: .001em solid #444;}
		#table_body th,#table_body td{border-right: .001em solid #444 ; padding: 0 .5px 0 .5px;}
		.rpt_info tr td{color: #000000;font-weight: bold;font-size: 16px;}
		#table_body tbody tr th{font-size: 20px; color:red;}
		
		td div:hover span {
			bottom: 50px;
			visibility: visible;
			opacity: 1;
			z-index: 999999;
			display: block;
			text-shadow: none;
		}

		td.parentCell, div.block_div
		{
			position: relative;
		}
		th div.block_div
		{
			position: relative;
			height: 100%;
			width: 100%;
		}

		span.tooltips{
			display: none;
			position: absolute;
			z-index: 100;
			background: white;
			padding: 3px;
			color: #000000;
			top: 20px;
			left: 20px;
			font-size: 14px;
			font-weight: bold;
			text-shadow: none;
			width: 150px;
		}
		div.block_div span.tooltips{width: 80px;}
		td.parentCell:hover span.tooltips,div.block_div:hover span.tooltips{display:block;}
	</style>

    <div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="firsttime">
		<div style="width:<? echo $tbl_width+20;?>px">
	        <table width="<? echo $tbl_width;?>" cellpadding="1" cellspacing="0" border="0" rules="all" id="table_body" align="left" style="border-collapse:seperate;border:.001em solid #444;">
	            <thead>
	            	<tr>
	            		<th colspan="20" align="left" style="color: red;">Inhouse Production</th>
	            	</tr>
					<tr height="<?=$tr_height;?>">
						<th width="60"><p>SL</p></th>
						<th width="60"><p>M/C No</p></th>
						<th width="60"><p>M/C Capacity</p></th>
						<th width="60"><p>Production Capacity</p></th>
						<th width="60"><p>Buyer</p></th>
						<th width="60"><p>Style</p></th>
						<th width="60"><p>Job No</p></th>
						<th width="60"><p>Color Name</p></th>
						<th width="60"><p>Batch No</p></th>
						<th width="60"><p>Ext. No</p></th>

						<th width="60"><p>Batch/ Dyeing Qty.</p></th>
						<th width="60"><p>Batch/ Trims Wgt.</p></th>
						<th width="60"><p>Status</p></th>
						<th width="60"><p>Batch Prepare Date</p></th>
						<th width="60"><p>Load/Batch Date</p></th>
						<th width="60"><p>Expected Load Time</p></th>
						<th width="60"><p>Actual Load Time</p></th>
						<th width="60"><p>UnLoad Date</p></th>
						<th width="60"><p>Expected UnLoad Time</p></th>
						<th width="60"><p>Actual UnLoad Time</p></th>
						<th width="60"><p>Expected  Time Use</p></th>
						<th width="60"><p>Actual Total Time Used</p></th>
					</tr>
				</thead>
	        </table>
	        <div style="width:<?= $tbl_width+20;?>px; max-height:<?=$page_height;?>px; overflow-y:auto;" id="scroll_body">
	            <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left" style="border-collapse:seperate;border:.001em solid #444;">
	                <tbody>
		            	<?
		            	$i=1;$status="";
		            	foreach($data_arr as $batch_id=>$row) 
		            	{
							$bgcolor=($i%2==0)?"#000000":"#000000";

							if ($unload_status_arr[$batch_id]!="")
							{
								$status=$unload_status_arr[$batch_id];
							}
							elseif ($unload_status_arr[$batch_id]=="" && $ready_status_arr[$batch_id]=="") 
							{
								$status='Running';
							}
							elseif ($ready_status_arr[$batch_id]!="") 
							{
								$status='Ready For Load';
							}

							$job_no_mst=$job_arr[$batch_id]['JOB_NO_MST'];
							$buyer_name=$job_arr[$batch_id]['BUYER_NAME'];
							$style_ref_no=$job_arr[$batch_id]['STYLE_REF_NO'];

							// Expected UnLoad Time Calculation
							$exp_load_hr_min = $txt_date.' '.$row['EXP_LOAD_HR'].':'.$row['EXP_LOAD_MIN'];
							$dur_req_hr_min = $row['DUR_REQ_HR'].':'.$row['DUR_REQ_MIN'];
							// Parse the first datetime
							$firstTimestamp = strtotime($exp_load_hr_min);
							// Extract hours and minutes from the second string
							list($secondHours, $secondMinutes) = explode(':', $dur_req_hr_min);

							// Add the hours and minutes to the first timestamp
							$secondTimestamp = strtotime("+$secondHours hours +$secondMinutes minutes", $firstTimestamp);
							// Format the result
							$exp_unload_t = date('H:i', $secondTimestamp);
							// Display the result
							// echo "Sum of Datetimes: $exp_unload_t<br>";
							// Expected UnLoad Time Calculation End

							// Expected  Time Use							
	                        $exp_load_t=$row['EXP_LOAD_HR'].':'.$row['EXP_LOAD_MIN'];
							$firstDatetime = ($txt_date.' '.$exp_load_t.':'.'00');
	                        $secondDatetime = ($txt_date.' '.$exp_unload_t.':'.'00');
							// Convert strings to DateTime objects
							$firstDate = new DateTime($firstDatetime);
							$secondDate = new DateTime($secondDatetime);

							// Calculate the time difference
							$difference = $firstDate->diff($secondDate);

							// Format and display the result
							$expected_time_use="{$difference->format('%h : %i')}";  

							// Actual Total Time Used
							$actual_load_hr = $row['ACTUAL_LOAD_HOURS'];
							$actual_load_min = $row['ACTUAL_LOAD_MIN'];
							$actual_unload_hours = $row['ACTUAL_UNLOAD_HOURS'];
							$actual_unload_min = $row['ACTUAL_UNLOAD_MIN'];
							// Create DateTime objects for the two times
							$firstTime = new DateTime(sprintf('%02d:%02d', $actual_load_hr, $actual_load_min));
							$secondTime = new DateTime(sprintf('%02d:%02d', $actual_unload_hours, $actual_unload_min));
							// Calculate the difference
							$difference = $firstTime->diff($secondTime);
							// Output the difference in a readable format (12-hour format)
							$actual_total_time_used=$difference->format('%h:%i');
							//echo "Time Duration: " . $actual_total_time_used;
							// Actual Total Time Used End

							$color="";
							if($actual_total_time_used == $expected_time_use)
							{
							    $color="green";
							}
							if($actual_total_time_used > $expected_time_use)
							{
							    $color="red";
							}
							if($actual_total_time_used < $expected_time_use)
							{
							    $color="black";
							} 

		                	?>
		                	<tr height="<?=$tr_height;?>" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
		                        <td width="60"><p><?=$i;?></p></td>
			                    <td width="60"><p><?=$row['MACHINE_NO'];?></p></td>
			                    <td width="60"><p><?=$row['MACHINE_CAPACITY'];?></p></td>
			                    <td width="60"><p><?=$row['PROD_CAPACITY'];?></p></td>
								<td width="60"><p><?=$buyer_arr[$buyer_name];?></p></td>
								<td width="60"><p><?=$style_ref_no;?></p></td>
								<td width="60"><p><?=$job_no_mst;?></p></td>
			                    <td width="60" title="<?=$row['COLOR_ID'];?>"><p><?=$color_arr[$row['COLOR_ID']];?></p></td>
			                    <td width="60" title="<?=$batch_id;?>"><p><?=$row['BATCH_NO'];?></p></td>
			                    <td width="60"><p><?=$row['EXT_NO'];?></p></td>
			                    
			                    <td width="60" align="center"><p><?=fn_number_format($row['PRODUCTION_QTY'],2,'.','','0');?></p></td>
			                    <td width="60" align="center"><p><?=fn_number_format($row['TOTAL_TRIMS_WEIGHT'],2,'.','','0');?></p></td>
			                    <td width="60" align="center"><p><?=$status;//$row['RESULT'];?></p></td>
			                    <td width="60" align="center"><p><?=$row['LOAD_BATCH_DATE'];//$row['RESULT'];?></p></td>
			                    <td width="60" align="center"><p><?=$row['LOAD_BATCH_DATE'];?></p></td>
			                    <td width="60" align="center" title="From new field of Batch creation page"><p><?=$row['EXP_LOAD_HR'].':'.$row['EXP_LOAD_MIN'];?></p></td>
			                    <td width="60" align="center" title="Process Start Time form dying production"><p><?=$row['ACTUAL_LOAD_HOURS'].':'.$row['ACTUAL_LOAD_MIN'];?></p></td>
			                    <td width="60" align="center"><p><?=$row['UNLOAD_DATE'];?></p></td>
			                    <td width="60" align="center" title="expected load time + duration requered"><p><?=$exp_unload_t; ?></p></td>
			                    <td width="60" align="center" title="dying production > Process End Time"><p><?=$row['ACTUAL_UNLOAD_HOURS'].':'.$row['ACTUAL_UNLOAD_MIN'];?></p></td>
			                    <td style="background-color: <?=$color;?>;" width="60" align="center" title="Expected Load Time - Expected UnLoad Time"><p><?=$expected_time_use;?></p></td>
			                    <td width="60" align="center" title="actual load time - actual unload time"><p><?=$actual_total_time_used;?></p></td>
		                    </tr>
		                    <?
		                    $i++;
		                    $tot_qnty+=$row['PRODUCTION_QTY'];
		                    $tot_trims_qnty+=$row['TOTAL_TRIMS_WEIGHT'];
				        }
			            ?>
			        </tbody>
	            </table>
			</div>
			<table class="rpt_table" width="<?= $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tfoot>
	           		<tr style="background: #1F1D36;text-align: right;">
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;">Grand Total</th>

	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"><?=fn_number_format($tot_qnty,2,'.','','0');?></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"><?=fn_number_format($tot_trims_qnty,2,'.','','0');?></th>
					    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
					    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                </tr>
	            </tfoot>
	        </table>
		</div>
	</div>

	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="secondtime">
		<div style="width:<? echo $tbl_width+20;?>px">
	        <table width="<? echo $tbl_width;?>" cellpadding="1" cellspacing="0" border="0" rules="all" id="table_body" align="left" style="border-collapse:seperate;border:.001em solid #444;">
	            <thead>
	            	<tr>
	            		<th colspan="20" align="left" style="color: red;">InBound Production</th>
	            	</tr>
					<tr height="<?=$tr_height;?>">
						<th width="60"><p>SL</p></th>
						<th width="60"><p>M/C No</p></th>
						<th width="60"><p>M/C Capacity</p></th>
						<th width="60"><p>Production Capacity</p></th>
						<th width="60"><p>Buyer</p></th>
						<th width="60"><p>Style</p></th>
						<th width="60"><p>Job No</p></th>
						<th width="60"><p>Color Name</p></th>
						<th width="60"><p>Batch No</p></th>
						<th width="60"><p>Ext. No</p></th>

						<th width="60"><p>Batch/ Dyeing Qty.</p></th>
						<th width="60"><p>Batch/ Trims Wgt.</p></th>
						<th width="60"><p>Status</p></th>
						<th width="60"><p>Batch Prepared Date</p></th>
						<th width="60"><p>Load/Batch Date</p></th>
						<th width="60"><p>Expected Load Time</p></th>
						<th width="60"><p>Actual Load Time</p></th>
						<th width="60"><p>UnLoad Date</p></th>
						<th width="60"><p>Expected UnLoad Time</p></th>
						<th width="60"><p>Actual UnLoad Time</p></th>
						<th width="60"><p>Expected  Time Use</p></th>
						<th width="60"><p>Actual Total Time Used</p></th>
					</tr>
				</thead>
	        </table>
	        <div style="width:<?= $tbl_width+20;?>px; max-height:<?=$page_height;?>px; overflow-y:auto;" id="scroll_body">
	            <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left" style="border-collapse:seperate;border:.001em solid #444;">
	                <tbody>
		            	<?
		            	$i=1;$tot_qnty=0;$tot_trims_qnty=0;
		            	foreach($subCondata_arr as $batch_id=>$row) 
		            	{
							$bgcolor=($i%2==0)?"#000000":"#000000";

							$job_no_mst=$job_arr[$batch_id]['JOB_NO_MST'];
							$buyer_name=$job_arr[$batch_id]['BUYER_NAME'];
							$style_ref_no=$job_arr[$batch_id]['STYLE_REF_NO'];

							// Expected UnLoad Time Calculation
							$exp_load_hr_min = $txt_date.' '.$row['EXP_LOAD_HR'].':'.$row['EXP_LOAD_MIN'];
							$dur_req_hr_min = $row['DUR_REQ_HR'].':'.$row['DUR_REQ_MIN'];
							// Parse the first datetime
							$firstTimestamp = strtotime($exp_load_hr_min);
							// Extract hours and minutes from the second string
							list($secondHours, $secondMinutes) = explode(':', $dur_req_hr_min);

							// Add the hours and minutes to the first timestamp
							$secondTimestamp = strtotime("+$secondHours hours +$secondMinutes minutes", $firstTimestamp);
							// Format the result
							$exp_unload_t = date('H:i', $secondTimestamp);
							// Display the result
							// echo "Sum of Datetimes: $exp_unload_t<br>";
							// Expected UnLoad Time Calculation End

							// Expected  Time Use							
	                        $exp_load_t=$row['EXP_LOAD_HR'].':'.$row['EXP_LOAD_MIN'];
							$firstDatetime = ($txt_date.' '.$exp_load_t.':'.'00');
	                        $secondDatetime = ($txt_date.' '.$exp_unload_t.':'.'00');
							// Convert strings to DateTime objects
							$firstDate = new DateTime($firstDatetime);
							$secondDate = new DateTime($secondDatetime);

							// Calculate the time difference
							$difference = $firstDate->diff($secondDate);

							// Format and display the result
							$expected_time_use="{$difference->format('%h : %i')}";  

							// Actual Total Time Used
							$actual_load_hr = $row['ACTUAL_LOAD_HOURS'];
							$actual_load_min = $row['ACTUAL_LOAD_MIN'];
							$actual_unload_hours = $row['ACTUAL_UNLOAD_HOURS'];
							$actual_unload_min = $row['ACTUAL_UNLOAD_MIN'];
							// Create DateTime objects for the two times
							$firstTime = new DateTime(sprintf('%02d:%02d', $actual_load_hr, $actual_load_min));
							$secondTime = new DateTime(sprintf('%02d:%02d', $actual_unload_hours, $actual_unload_min));
							// Calculate the difference
							$difference = $firstTime->diff($secondTime);
							// Output the difference in a readable format (12-hour format)
							$actual_total_time_used=$difference->format('%h:%i');
							//echo "Time Duration: " . $actual_total_time_used;
							// Actual Total Time Used End

							$color="";
							if($actual_total_time_used == $expected_time_use)
							{
							    $color="green";
							}
							if($actual_total_time_used > $expected_time_use)
							{
							    $color="red";
							}
							if($actual_total_time_used < $expected_time_use)
							{
							    $color="black";
							} 

		                	?>
		                	<tr height="<?=$tr_height;?>" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
		                        <td width="60"><p><?=$i;?></p></td>
			                    <td width="60"><p><?=$row['MACHINE_NO'];?></p></td>
			                    <td width="60"><p><?=$row['MACHINE_CAPACITY'];?></p></td>
			                    <td width="60"><p><?=$row['PROD_CAPACITY'];?></p></td>
								<td width="60"><p><?=$buyer_arr[$buyer_name];?></p></td>
								<td width="60"><p><?=$style_ref_no;?></p></td>
								<td width="60"><p><?=$job_no_mst;?></p></td>
			                    <td width="60" title="<?=$row['COLOR_ID'];?>"><p><?=$color_arr[$row['COLOR_ID']];?></p></td>
			                    <td width="60"><p><?=$row['BATCH_NO'];?></p></td>
			                    <td width="60"><p><?=$row['EXTENTION_NO'];?></p></td>
			                    
			                    <td width="60" align="center"><p><?=fn_number_format($row['PRODUCTION_QTY'],2,'.','','0');?></p></td>
			                    <td width="60" align="center"><p><?=fn_number_format($row['TOTAL_TRIMS_WEIGHT'],2,'.','','0');?></p></td>
			                    <td width="60" align="center"><p><?=$row['RESULT'];?></p></td>
			                    <td width="60" align="center"><p><?=$row['LOAD_BATCH_DATE'];?></p></td>
			                    <td width="60" align="center"><p><?=$row['LOAD_BATCH_DATE'];?></p></td>
			                    <td width="60" align="center" title="From new field of Batch creation page"><p><?=$row['EXP_LOAD_HR'].':'.$row['EXP_LOAD_MIN'];?></p></td>
			                    <td width="60" align="center" title="Process Start Time form dying production"><p><?=$row['ACTUAL_LOAD_HOURS'].':'.$row['ACTUAL_LOAD_MIN'];?></p></td>
			                    <td width="60" align="center"><p><?=$row['UNLOAD_DATE'];?></p></td>
			                    <td width="60" align="center" title="expected load time + duration requered"><p><?=$exp_unload_t; ?></p></td>
			                    <td width="60" align="center" title="dying production > Process End Time"><p><?=$row['ACTUAL_UNLOAD_HOURS'].':'.$row['ACTUAL_UNLOAD_MIN'];?></p></td>
			                    <td style="background-color: <?=$color;?>;" width="60" align="center" title="Expected Load Time - Expected UnLoad Time"><p><?=$expected_time_use;?></p></td>
			                    <td width="60" align="center" title="actual load time - actual unload time"><p><?=$actual_total_time_used;?></p></td>
		                    </tr>
		                    <?
		                    $i++;
		                    $tot_qnty+=$row['PRODUCTION_QTY'];
		                    $tot_trims_qnty+=$row['TOTAL_TRIMS_WEIGHT'];
				        }
			            ?>
			        </tbody>
	            </table>
			</div>
			<table class="rpt_table" width="<?= $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tfoot>
	           		<tr style="background: #1F1D36;text-align: right;">
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;">Grand Total</th>

	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"><?=fn_number_format($tot_qnty,2,'.','','0');?></th>
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"><?=fn_number_format($tot_trims_qnty,2,'.','','0');?></th>
					    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th>
					    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                    <th width="60" style="background: #191A19;text-align: right; border-color: #444;"></th> 
	                </tr>
	            </tfoot>
	        </table>
		</div>
	</div>

	<?    
	$user_id=($user_id=='')?1000000000000:$user_id;

	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	disconnect($con);
	exit(); 
}
?>
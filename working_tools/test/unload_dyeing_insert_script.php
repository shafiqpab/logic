<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
if ($db_type == 0) {
			mysql_query("BEGIN");
		}
	die;	
 $bidArr=array(47686,53272,61157,63011,22271,71032,75355,78383,83715,83412,84652,84236,85563,89481,69810,89130,89768,93271,91030);

$un_load_arr=array();
$un_load_main_dyeing_mst="select id,company_id,system_no,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,batch_no,batch_id,batch_ext_no,process_id,ltb_btb_id,water_flow_meter,process_end_date,end_hours,end_minutes,machine_id,floor_id,load_unload_id,entry_form,multi_dyeing_id,remarks,dyeing_type_id,hour_load_meter,multi_batch_load_id,inserted_by,insert_date from pro_fab_subprocess   where   entry_form=35  and load_unload_id=1  and status_active=1 and   batch_id in(47686,53272,61157,63011,22271,71032,75355,78383,83715,83412,84652,84236,85563,89481,69810,89130,89768,93271,91030) order by  batch_id  asc";

//$un_load_main_dyeing_sql_dtls="select b.batch_id,b.batch_no,b.load_unload_id,a.company_id from pro_fab_subprocess b, pro_fab_subprocess_dtls a where b.id=a.mst_id and b.entry_form=35  and b.load_unload_id=1 and a.status_active=1 and b.status_active=1 and  b.batch_id in(21542,46518,47686,53272,61157,63011,22271,71032,75355,78383,83715,83412,84652,84236,85563,89481,69810,89130,89768,93271,91030) order by b.batch_id  asc";

$un_load_main_dyeingMst=sql_select($un_load_main_dyeing_mst);
foreach($un_load_main_dyeingMst as $row)
{
	$un_load_arr[$row[csf("batch_id")]]['company_id']=$row[csf("company_id")];
	$un_load_arr[$row[csf("batch_id")]]['system_no']=$row[csf("system_no")];
	$un_load_arr[$row[csf("batch_id")]]['service_source']=$row[csf("service_source")];
	$un_load_arr[$row[csf("batch_id")]]['service_company']=$row[csf("service_company")];
	$un_load_arr[$row[csf("batch_id")]]['received_chalan']=$row[csf("received_chalan")];
	$un_load_arr[$row[csf("batch_id")]]['issue_chalan']=$row[csf("issue_chalan")];
	$un_load_arr[$row[csf("batch_id")]]['issue_challan_mst_id']=$row[csf("issue_challan_mst_id")];
	$un_load_arr[$row[csf("batch_id")]]['batch_no']=$row[csf("batch_no")];
	$un_load_arr[$row[csf("batch_id")]]['batch_ext_no']=$row[csf("batch_ext_no")];
	$un_load_arr[$row[csf("batch_id")]]['process_id']=$row[csf("process_id")];
	$un_load_arr[$row[csf("batch_id")]]['ltb_btb_id']=$row[csf("ltb_btb_id")];
	$un_load_arr[$row[csf("batch_id")]]['water_flow_meter']=$row[csf("water_flow_meter")];
	$un_load_arr[$row[csf("batch_id")]]['process_end_date']=$row[csf("process_end_date")];
	$un_load_arr[$row[csf("batch_id")]]['end_hours']=$row[csf("end_hours")];
	$un_load_arr[$row[csf("batch_id")]]['end_minutes']=$row[csf("end_minutes")];
	$un_load_arr[$row[csf("batch_id")]]['machine_id']=$row[csf("machine_id")];
	$un_load_arr[$row[csf("batch_id")]]['floor_id']=$row[csf("floor_id")];
	$un_load_arr[$row[csf("batch_id")]]['load_unload_id']=$row[csf("load_unload_id")];
	$un_load_arr[$row[csf("batch_id")]]['entry_form']=$row[csf("entry_form")];
	$un_load_arr[$row[csf("batch_id")]]['multi_dyeing_id']=$row[csf("multi_dyeing_id")];
	$un_load_arr[$row[csf("batch_id")]]['remarks']=$row[csf("remarks")];
	$un_load_arr[$row[csf("batch_id")]]['dyeing_type_id']=$row[csf("dyeing_type_id")];
	$un_load_arr[$row[csf("batch_id")]]['hour_load_meter']=$row[csf("hour_load_meter")];
	$un_load_arr[$row[csf("batch_id")]]['multi_batch_load_id']=$row[csf("multi_batch_load_id")];
	$un_load_arr[$row[csf("batch_id")]]['inserted_by']=$row[csf("inserted_by")];
	$un_load_arr[$row[csf("batch_id")]]['insert_date']=$row[csf("insert_date")];
	//inserted_by,insert_date
	
}
$m=0;

 $field_array = "id,company_id,system_no,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,batch_no,batch_id,batch_ext_no,process_id,ltb_btb_id,water_flow_meter,process_end_date,end_hours,end_minutes,machine_id,floor_id,load_unload_id,result,entry_form,multi_dyeing_id,remarks,dyeing_type_id,hour_unload_meter,shift_name,fabric_type,production_date,booking_no,responsibility_id,inserted_by,insert_date ,shade_matched";
$id_mst = return_next_id("id", "pro_fab_subprocess", 1);
$id_dtls = return_next_id("id", "pro_fab_subprocess_dtls", 1);
foreach($un_load_arr as $batch_id=>$row)
{
	$system_no=$row['system_no'];
	$cbo_company_id=$row['company_id'];
	$cbo_service_source=$row['service_source'];
	$cbo_service_company=$row['service_company'];
	
	if($row['issue_chalan']=='') $row['issue_chalan']='';
	if($row['issue_challan_mst_id']=='') $row['issue_challan_mst_id']='';
	if($row['batch_ext_no']=='') $row['batch_ext_no']='';
	if($row['remarks']=='') $row['remarks']='';
	if($row['hour_load_meter']=='') $row['hour_load_meter']='';
	
	$txt_recevied_chalan=$row['received_chalan'];
	if($txt_recevied_chalan=='') $txt_recevied_chalan='';
	$txt_issue_chalan=$row['issue_chalan'];
	$txt_issue_mst_id=$row['issue_challan_mst_id'];
	$txt_batch_no=$row['batch_no'];
	$txt_batch_ID=$batch_id;
	$txt_ext_id=$row['batch_ext_no'];
	$txt_process_id=$row['process_id'];
	$cbo_ltb_btb=$row['ltb_btb_id'];
	$txt_water_flow=$row['water_flow_meter'];
	$txt_process_end_date=$row['process_end_date'];
	$txt_process_date=$row['process_end_date'];
	$txt_end_hours=$row['end_hours'];
	$txt_end_minutes=$row['end_minutes']+10;
	$cbo_machine_name=$row['machine_id'];
	$cbo_floor=$row['floor_id'];
	$cbo_load_unload=2;
	$cbo_floor=$row['floor_id'];
	$cbo_result_name=1;
	$hidden_double_dyeing=$row['multi_dyeing_id'];
	$txt_remarks=$row['remarks'];
	$cbo_dyeing_type=$row['dyeing_type_id'];
	$txt_unload_meter=$row['hour_load_meter'];
	$cbo_shift_name=1;
	$cbo_fabric_type=0;
	$txt_booking_no='';
	$cbo_fabric_type=0;
	$cbo_responsibility=0;
	$inserted_by=$row['inserted_by'];
	$insert_date=$row['insert_date'];
	$cbo_responsibility=0; 
	
	//$id = return_next_id("id", "pro_fab_subprocess", 1);
	
	 if ($data_array != "") $data_array .= ",";
			$data_array .= "(" . $id_mst . "," . $cbo_company_id . "," . $system_no . "," . $cbo_service_source . "," . $cbo_service_company . ",'" . $txt_recevied_chalan . "','" . $txt_issue_chalan . "','" . $txt_issue_mst_id . "','" . $txt_batch_no . "'," . $txt_batch_ID . ",'" . $txt_ext_id . "'," . $txt_process_id . "," . $cbo_ltb_btb . ",'" . $txt_water_flow . "','" . $txt_process_end_date . "'," . $txt_end_hours . "," . $txt_end_minutes . "," . $cbo_machine_name . "," . $cbo_floor . "," . $cbo_load_unload . "," . $cbo_result_name . ",35," . $hidden_double_dyeing . ",'" . $txt_remarks . "'," . $cbo_dyeing_type . ",'" . $txt_unload_meter . "'," . $cbo_shift_name . "," . $cbo_fabric_type . ",'" . $txt_process_date . "','" . $txt_booking_no . "'," . $cbo_responsibility . "," . $inserted_by. ",'" . $insert_date . "',1)";
			
		$field_array_dtls = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,no_of_roll, load_unload_id, production_qty, rate, amount, currency_id,exchange_rate,remarks ,inserted_by,insert_date";
		
			$un_load_main_dyeing_sql_dtls="select a.entry_page,a.prod_id,a.const_composition,a.gsm,a.dia_width,a.width_dia_type,a.batch_qty,a.no_of_roll,a.load_unload_id,a.production_qty,a.remarks,a.inserted_by,a.insert_date from pro_fab_subprocess b, pro_fab_subprocess_dtls a where b.id=a.mst_id and b.entry_form=35  and b.load_unload_id=1 and a.status_active=1 and b.status_active=1 and  b.batch_id in(47686,53272,61157,63011,22271,71032,75355,78383,83715,83412,84652,84236,85563,89481,69810,89130,89768,93271,91030) and b.batch_id=".$txt_batch_ID."  order by b.batch_id  asc";
			
			$un_load_main_dyeingDtls=sql_select($un_load_main_dyeing_sql_dtls);
			
			foreach($un_load_main_dyeingDtls as $row)
			{
				$Itemprod_id=$row[csf('prod_id')];
				$const_composition=$row[csf('const_composition')];
				$gsm=$row[csf('gsm')];
				$dia_width=$row[csf('dia_width')];
				$width_dia_type=$row[csf('width_dia_type')];
				$batch_qty=$row[csf('batch_qty')];
				$no_of_roll=$row[csf('no_of_roll')];
				$load_unload_id=$row[csf('load_unload_id')];
				$production_qty=$row[csf('production_qty')];
				if($row[csf('remarks')]=='') $row[csf('remarks')]='';
				$remarks=$row[csf('remarks')];
				$inserted_by=$row[csf('inserted_by')];
				$insert_date=$row[csf('insert_date')];
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $id_dtls . "," . $id_mst . ",35," . $Itemprod_id . ",'" . $const_composition . "','" . $gsm . "','" . $dia_width . "'," . $width_dia_type . "," . $batch_qty . ",'" . $no_of_roll . "',2," . $production_qty . ",'','','','','" . $remarks . "'," . $inserted_by. ",'" . $insert_date . "')";
					$id_dtls = $id_dtls + 1;
			}

			
	
	//$field_array_dtls = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,no_of_roll,load_unload_id,production_qty,remarks,inserted_by,insert_date";
	$id_mst = $id_mst + 1;
	$m++;
	
	//$un_load_dyeing_compamy_array[$row[csf("batch_id")]]=$row[csf("company_id")];
}
	//echo "10**insert into pro_fab_subprocess($field_array) values".$data_array;die;
	$rID = sql_insert("pro_fab_subprocess", $field_array, $data_array, 0);
	if ($rID) $flag = 1; else $flag = 0;
	// echo "10**=".$rID;die;
	
	 //echo "10**=insert into pro_fab_subprocess_dtls ($field_array_dtls) values".$data_array_dtls;die;;
	$rID2 = sql_insert("pro_fab_subprocess_dtls", $field_array_dtls, $data_array_dtls, 0);
	if ($flag == 1) {
		if ($rID2) $flag = 1; else $flag = 0;
	 }
	//echo "10**=".$rID.'='.$flag;die;

 
 	if($db_type==0)
		{
			if($unload_mst){
				mysql_query("COMMIT");  
				echo "<b>Success</b>";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "Failed";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "<b>Success</b>"; 
			}
			else{
				oci_rollback($con); 
				echo "<b>Failed</b>";
			}
		}

//print_r($un_load_dyeing_compamy_array);
//After run Complete; Plz stop this page- use die top of page;
?>
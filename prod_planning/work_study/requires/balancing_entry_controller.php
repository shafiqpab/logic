<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

function format($number) {
    return preg_replace(
        '~\.[0-9]0+$~',
        null,
        sprintf('%.2f', $number)
    );
}

if($action=="populate_data_from_breakdown")
{
	$sql= "SELECT id, buyer_id, style_ref, gmts_item_id, working_hour FROM ppl_gsd_entry_mst where id=$data and is_deleted=0";
	$data_array=sql_select($sql);
 	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_style_ref_bl').value			= '".$row[csf("style_ref")]."';\n";
		echo "document.getElementById('cbo_buyer_bl').value 			= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_gmt_item_bl').value 			= '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('txt_working_hour_bl').value 		= '".$row[csf("working_hour")]."';\n";
		echo "document.getElementById('breakdown_id').value 			= '".$row[csf("id")]."';\n";
		
		$balanceData=sql_select("select id, allocated_mp, line_no, pitch_time, target, efficiency,learning_cub_method from ppl_balancing_mst_entry where gsd_mst_id='".$row[csf("id")]."' and balancing_page=1 and status_active=1 and is_deleted=0");
		echo "document.getElementById('txt_allocated_mp').value			= '".$balanceData[0][csf("allocated_mp")]."';\n";
		echo "document.getElementById('txt_line_no').value 				= '".$balanceData[0][csf("line_no")]."';\n";
		echo "document.getElementById('txt_pitch_time').value 			= '".$balanceData[0][csf("pitch_time")]."';\n";
		echo "document.getElementById('txt_target').value 				= '".$balanceData[0][csf("target")]."';\n";
		echo "document.getElementById('txt_efficiency_bl').value 		= '".$balanceData[0][csf("efficiency")]."';\n";
		echo "document.getElementById('bl_update_id').value 			= '".$balanceData[0][csf("id")]."';\n";
		echo "document.getElementById('cbo_learning_cub_method_bl').value = '".$balanceData[0][csf("learning_cub_method")]."';\n";

		if($balanceData[0][csf("id")]>0)
		{
			echo "set_button_status(1, '".$permission."', 'fnc_balancing_entry',2);\n"; 
		}
		else
		{
			echo "set_button_status(0, '".$permission."', 'fnc_balancing_entry',2);\n"; 
		}
		
		exit();
	}
}

if($action=="details_list_view")
{
	$data=explode("**",$data);
	$update_id=$data[0];
	$bl_update_id=$data[1];

	$balanceDataArray=array();
	if($bl_update_id>0)
	{
        $blData="select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id  and is_deleted=0";
       // echo $blData;die;

		$blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
		foreach($blData as $row)
		{
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
		}
	}
	
	$operation_arr=return_library_array("select id,operation_name from lib_sewing_operation_entry", "id","operation_name");
	
    $sqlDtls="SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc, b.target_on_effi_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$update_id and b.is_deleted=0 order by b.row_sequence_no asc";
    // echo $sqlDtls;die;

	$data_array_dtls=sql_select($sqlDtls);

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and process_id={$data_array_dtls[0]['PROCESS_ID']}  order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );



	//echo $sqlDtls;

	$i=1; $tot_smv=0;
	foreach($data_array_dtls as $slectResult)
	{
		if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
		 
		/*if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
		{
			$smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
			$cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
			$perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
		}
		else
		{
			$smv=$slectResult[csf('total_smv')];
			$cycleTime=$slectResult[csf('total_smv')]*60;
			$perc=$slectResult[csf('target_on_full_perc')];
		}*/
		 
		 $smv=$slectResult[csf('total_smv')];
		 $cycleTime=$slectResult[csf('total_smv')]*60;
		 $perc=$slectResult[csf('target_on_full_perc')];
		
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
            <td><? echo $slectResult[csf('row_sequence_no')]; ?>
            	<input type="hidden" name="seqNoB[]" id="seqNoB_<? echo $i; ?>" value="<? echo $slectResult[csf('row_sequence_no')]; ?>">
                <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $slectResult[csf('id')]; ?>">
            </td>
            
            <td><p><? echo $body_part[$slectResult[csf('body_part_id')]]; ?></p><input type="hidden" name="bodyPart[]" id="bodyPart_<? echo $i; ?>" value="<? echo $slectResult[csf('body_part_id')]; ?>"></td>

            <td><p><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></p><input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>" value="<? echo $slectResult[csf('lib_sewing_id')]; ?>"></td>
            <td><p><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></p><input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" value="<? echo $slectResult[csf('resource_gsd')]; ?>"></td>
            <td align="right" id="totalSmv_<? echo $i; ?>"><? echo number_format($smv,2,'.',''); ?></td>
            <td align="right" id="tgtPerc_<? echo $i; ?>"><? echo $perc; ?></td>
            <td align="right" id="cycleTime_<? echo $i; ?>"><? echo number_format($cycleTime,2,'.',''); ?></td>

            <td align="center"><input type="text" name="txtTheoriticalMp[]" id="txtTheoriticalMp_<? echo $i; ?>" value="<? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?>" style="width:65px" class="text_boxes_numeric"></td>
            
            <td align="center"><input type="text" name="txtlayOut[]" id="txtlayOut_<? echo $i; ?>"value="<? echo $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?>" onKeyUp="calculate_total();" style="width:50px" class="text_boxes_numeric"></td>

            
            <td align="center"><input type="text" name="workLoad[]" id="workLoad_<? echo $i; ?>"value="<? echo $balanceDataArray[$slectResult[csf('id')]]['work_load']; ?>" style="width:50px" class="text_boxes_numeric"></td>
            <td align="center"><input type="text" name="weight[]" id="weight_<? echo $i; ?>"value="<? echo $balanceDataArray[$slectResult[csf('id')]]['weight']; ?>" style="width:50px" class="text_boxes_numeric"></td>
            <td align="center"><input type="text" name="workerTracking[]" id="workerTracking_<? echo $i; ?>"value="<? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?>" style="width:50px" class="text_boxes"></td>
        </tr>
	<?	
		$tot_smv+=$smv;
		$i++;
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$approved=0;
	$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$breakdown_id");
	foreach($sql as $row){
		$approved=$row[csf('approved')];
	}
	if($approved==3) $approved=1; else $approved=$approved;
	
	if($approved==1){
		echo "approved**".str_replace("'","",$breakdown_id);
		die;
	}
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		} 
		
		$id=return_next_id( "id", "ppl_balancing_mst_entry", 1 );
		$field_array="id,gsd_mst_id,allocated_mp,line_no,pitch_time,target,efficiency,balancing_page,learning_cub_method,inserted_by,insert_date,entry_form";
		$data_array="(".$id.",".$breakdown_id.",".$txt_allocated_mp.",".$txt_line_no.",".$txt_pitch_time.",".$txt_target.",".$txt_efficiency_bl.",1,".$cbo_learning_cub_method_bl.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1)";
		
		$field_array_dtls="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, smv, target_hundred_perc, cycle_time, theoritical_mp, layout_mp, work_load, weight, worker_tracking,entry_form";
		$dtls_id = return_next_id( "id", "ppl_balancing_dtls_entry", 1 );
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$seqNo="seqNo".$j;
			$gsdDtlsId="dtlsId".$j;
			$bodyPart="bodyPart".$j;
			$sewingId="sewingId".$j;
			$rescId="rescId".$j;
			$smv="smv".$j;
			$tgtPerc="tgtPerc".$j;
			$cycleTime="cycleTime".$j;
			$txtTheoriticalMp="txtTheoriticalMp".$j;
			$layOut="layOut".$j;
			$workLoad="workLoad".$j;
			$weight="weight".$j;
			$workerTracking="workerTracking".$j;

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$breakdown_id.",'".$$gsdDtlsId."',".$id.",'".$$seqNo."','".$$bodyPart."','".$$sewingId."','".$$rescId."','".$$smv."','".$$tgtPerc."','".$$cycleTime."','".$$txtTheoriticalMp."','".$$layOut."','".$$workLoad."','".$$weight."','".$$workerTracking."',1)";
			$dtls_id = $dtls_id+1;
		}

		//echo "10**insert into ppl_balancing_dtls_entry (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID = sql_insert("ppl_balancing_mst_entry", $field_array, $data_array,0);
		$rID2 = sql_insert("ppl_balancing_dtls_entry", $field_array_dtls, $data_array_dtls,1);
		//echo "10**".$rID."&&".$rID2;die;

		if($rID && $rID2)
        {
            oci_commit($con);  
            echo "0**".$id;
        }
        else
        {
            oci_rollback($con);
            echo "5**0**0";
        }
		disconnect($con);
		die;
	}
	elseif($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type == 0)
		{
			mysql_query("BEGIN");
		}  
		$field_array = "allocated_mp*line_no*pitch_time*learning_cub_method*target*efficiency*updated_by*update_date";
		$data_array=$txt_allocated_mp."*".$txt_line_no."*".$txt_pitch_time."*".$cbo_learning_cub_method_bl."*".$txt_target."*".$txt_efficiency_bl."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, smv, target_hundred_perc, cycle_time, theoritical_mp, layout_mp, work_load, weight, worker_tracking,entry_form";
		$dtls_id = return_next_id( "id", "ppl_balancing_dtls_entry", 1 );
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$seqNo="seqNo".$j;
			$gsdDtlsId="dtlsId".$j;
			$bodyPart="bodyPart".$j;
			$sewingId="sewingId".$j;
			$rescId="rescId".$j;
			$smv="smv".$j;
			$tgtPerc="tgtPerc".$j;
			$cycleTime="cycleTime".$j;
			$txtTheoriticalMp="txtTheoriticalMp".$j;
			$layOut="layOut".$j;
			$workLoad="workLoad".$j;
			$weight="weight".$j;
			$workerTracking="workerTracking".$j;
 
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$breakdown_id.",'".$$gsdDtlsId."',".$bl_update_id.",'".$$seqNo."','".$$bodyPart."','".$$sewingId."','".$$rescId."','".$$smv."','".$$tgtPerc."','".$$cycleTime."','".$$txtTheoriticalMp."','".$$layOut."','".$$workLoad."','".$$weight."','".$$workerTracking."',1)";
			$dtls_id = $dtls_id+1;
		}
		// lauyout = ppl_gsd_entry_dtls 

		$rID=sql_update("ppl_balancing_mst_entry",$field_array,$data_array,"id",$bl_update_id,0);
		$rID2=execute_query( "delete from ppl_balancing_dtls_entry where mst_id=$bl_update_id",0);
		$rID3=sql_insert("ppl_balancing_dtls_entry",$field_array_dtls,$data_array_dtls,1);
		//echo "10**".$rID."&&".$rID2."&&".$rID3;die;
		
		if($rID && $rID2 && $rID3)
        {
            oci_commit($con);  
            echo "1**".str_replace("'", '', $bl_update_id);
        }
        else
        {
            oci_rollback($con);
            echo "6**".str_replace("'", '', $bl_update_id);
        }
		disconnect($con);
		die;
	}
    elseif($operation==2)   // Delete Here
	{
		$con = connect();
 
        $sql = "SELECT id, buyer_id, style_ref, gmts_item_id FROM ppl_gsd_entry_mst where id=$breakdown_id";
        $data_array = sql_select($sql);
        foreach ($data_array as $row) {
            $balanceData = sql_select("select id, line_shape, no_of_work_st, layout_date from ppl_balancing_mst_entry where gsd_mst_id='" . $row[csf("id")] . "' and balancing_page=3 and status_active=1 and is_deleted=0");
            if ($balanceData[0][csf("id")] > 0) {
                echo "exit**";die;
            }
        }

        $breakdown_id =  str_replace("'", '', $breakdown_id);
 
        $mstSql = "UPDATE ppl_balancing_mst_entry SET UPDATED_BY = $user_name, UPDATE_DATE = '$pc_date_time', STATUS_ACTIVE = 0, IS_DELETED = 1 WHERE gsd_mst_id = $breakdown_id";
        $rID = execute_query($mstSql);

        $dtlsSql = "UPDATE ppl_balancing_dtls_entry  SET DELETED_BY=$user_name, DELETE_DATE='$pc_date_time', STATUS_ACTIVE = 0, IS_DELETED = 1 WHERE gsd_mst_id = $breakdown_id";
        $rID2 = execute_query($dtlsSql);

        if($rID && $rID2)
        {
            oci_commit($con);    
            echo "2**".$rID;
        }
        else
        {
            oci_rollback($con);
            echo "10**".$rID;
        }
        disconnect($con);
		die;
        // $field_array="updated_by*update_date*status_active*is_deleted";
        // $data_array=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"; echo $data_array;die;
        // $rID=sql_update("ppl_balancing_mst_entry", $field_array, $data_array,"id",str_replace("'", '', $bl_update_id),0);
        // $field_array_delts="status_active*is_deleted";
		// $data_array_delts="0*1";
        //$rID=sql_update("ppl_balancing_mst_entry", $field_array, $data_array, "id", "".$bl_update_id."",1);
        // $rID2=sql_update("ppl_balancing_dtls_entry",$field_array_delts,$data_array_delts,"mst_id","".$bl_update_id."",1);
	}
}

if($action=="load_graph_data")
{
    $data = explode("_", $data);

 //   print_r($data);die;
 
    $mast_id = $data[0];
    $bl_update_id = $data[1];

    //$sql = "SELECT a.PROCESS_ID,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
   //echo $sql;die;

    $mstDataArray = sql_select("SELECT a.PROCESS_ID,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

   
    //echo "<pre>";
    ///print_r($mstDataArray);die;
    // "</pre>";
    //die;

   
    $balanceDataArray=array();
    if($bl_update_id>0)
    {
        $blData=sql_select("SELECT gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
        foreach($blData as $row)
        {
            $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
        }
    }
    
    $operation_arr=return_library_array("SELECT id,operation_name from lib_sewing_operation_entry", "id","operation_name");
    
    $sqlDtls="SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc, b.target_on_effi_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$mast_id and b.is_deleted=0 order by b.row_sequence_no asc";

	$data_array_dtls=sql_select($sqlDtls);

    $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array();
	foreach($data_array_dtls as $row)
	{ 
        $ucl = number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
        $lcl = number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
        
        $seqNosArr[] = $row['ROW_SEQUENCE_NO'];
        $weightsArr[] = number_format($balanceDataArray[$row['ID']]['weight'],2,'.','');
        $pitchTimesArr[] = $mstDataArray[0][csf('pitch_time')];
        $uclsArr[] = $ucl;
        $lclsArr[] = $lcl;
    }

    // echo json_encode($seqNosArr).'**'.json_encode($weightsArr)."**".json_encode($pitchTimesArr)."**".json_encode($uclsArr)."**".json_encode($lclsArr);
	// exit();


    echo json_encode($seqNosArr).'**'.json_encode($weightsArr)."**".json_encode($pitchTimesArr)."**".json_encode($uclsArr)."**".json_encode($lclsArr);
	exit();

    // $attach_id=return_library_array( "SELECT id,attachment_name from lib_attachment",'id','attachment_name');
    // $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mast_id."' and is_deleted=0 group by id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc order by row_sequence_no asc";

    // print_r( $weightsArr); die;
    //echo $sqlDtls;die;
    // $data_array_dtls=sql_select($sqlDtls);
    // $data_array = array();
    // foreach ($data_array_dtls as $v) 
    // {
    //     $data_array[$v['BODY_PART_ID']][$v['ID']]['row_sequence_no'] = $v['ROW_SEQUENCE_NO'];
    //     //$data_array[$v['ID']]['row_sequence_no'] = $v['ROW_SEQUENCE_NO'];
    //     //$data_array[$v['ID']]['id'] = $v['ID'];
    // }

    // $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array();
    // foreach($data_array as $bp_id=>$bp_data)
    // {
    //     foreach($bp_data as $rid=>$r_data)
    //     {
    //         $count_body_part[$bp_id]++;
    //     }
    // }
    // echo "<pre>";
    // print_r($data_array);
    // echo "</pre>";
    // die;

    // foreach($data_array as $bp_id=>$bp_data)
    // {
    //    //foreach($bp_data as $rid=>$r)
    //    //{ 
    //         // $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
    //         // $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');

    //         // $seqNosArr[]=$r['row_sequence_no'];
    //         // $weightsArr[]=number_format($balanceDataArray[$rid]['weight'],2,'.','');
    //         // $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
    //         // $uclsArr[]=$ucl;
    //         // $lclsArr[]=$lcl;
    //    //}

    //    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
    //    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
       
    //    $seqNosArr[]=$r['row_sequence_no'];
    //    $weightsArr[]=number_format($balanceDataArray[$bp_data['ID']]['weight'],2,'.','');
    //    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
    //    $uclsArr[]=$ucl;
    //    $lclsArr[]=$lcl;


    // }
    // print_r( $weightsArr); die;

    // echo json_encode($seqNosArr).'**'.json_encode($weightsArr)."**".json_encode($pitchTimesArr)."**".json_encode($uclsArr)."**".json_encode($lclsArr);
	// exit();
}





if($action=="balancing_print")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	$breakdown_id=$data[2];
    // print_r($data);die; 
    // seam_length
	$buyer_library = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library = return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
    
    $mst_sql = "SELECT a.PROCESS_ID,a.id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour, a.tot_mc_smv,a.applicable_period, a.bulletin_type, a.total_smv, a.color_type, a.extention_no, a.system_no, b.inserted_by, a.prod_description, b.insert_date, b.updated_by, b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency FROM ppl_gsd_entry_mst a, ppl_balancing_mst_entry b WHERE a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$mstDataArray = sql_select($mst_sql);

     

    $gsd_entry_dtls_sql = "SELECT a.id FROM ppl_gsd_entry_dtls a WHERE mst_id = '$breakdown_id' and a.operator_smv IS NOT NULL";
	$gsd_entry_dtls_res = sql_select($gsd_entry_dtls_sql);
	$no_of_machine = count($gsd_entry_dtls_res); 
    
 

	$balancing_mst_entry_sql = "SELECT a.id, a.target FROM ppl_balancing_mst_entry a WHERE gsd_mst_id = '$breakdown_id'";
   // echo $balancing_mst_entry_sql;die;
	$balancing_mst_entry_res = sql_select($balancing_mst_entry_sql);
	$target = $balancing_mst_entry_res['0']['TARGET'];
	$tot_mc_smv = $mstDataArray[0]['TOT_MC_SMV'];
	$working_hour = $mstDataArray[0]['WORKING_HOUR'];
    // print_r($working_hour);die;

    // (138*14.6)/(43*1*60)
	// calculation of (Target*Total MC SMV)/(No of Machine*Working Hour*60)
	$target_eff = ($target*$tot_mc_smv)/($no_of_machine*$working_hour*60);

    $production_resource_arr=return_library_array("SELECT RESOURCE_ID,RESOURCE_NAME FROM LIB_OPERATION_RESOURCE WHERE is_deleted=0 and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} ORDER BY RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME");
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="100%" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Operation Balancing Sheet</u></strong></td>
            </tr>
            <tr>
                <td width="110"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="190"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="170"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="110"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Working Hour</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><?= $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Allocated MP</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><?= $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('line_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Efficiency</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><?= $mstDataArray[0][csf('efficiency')]; ?></td>
                <td><strong>Pitch Time</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><?= $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Target</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><?= $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            <tr>
                <td><strong>SMV</strong></td>
                <td><strong>:</strong></td>
                <td><?= number_format($mstDataArray[0][csf('total_smv')],2); ?></td>
                <td><strong>Custom Style</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('custom_style')]; ?></td>
                <td><strong>Bulletin Type</strong></td>
                <td><strong>:</strong></td>
                <td><?= $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Insert By</strong></td>
                <td><strong>:</strong></td>
                <td><?= $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                <td><strong>Insert Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('insert_date')]; ?></td>
                <td><strong>Color Type</strong></td>
                <td><strong>:</strong></td>
                <td><?= $color_type[$mstDataArray[0][csf('color_type')]]; ?></td>
            
            </tr>
            <tr>
                <td><strong>Update By</strong></td>
                <td><strong>:</strong></td>
                <td><?= $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                <td><strong>Update Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('update_date')]; ?></td>
                <td><strong>System ID</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('system_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Fabric Type</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('fabric_type')]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td><strong>:</strong></td>
                <td><?= change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                <td><strong>Extention No</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('extention_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Product Des.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('prod_description')]; ?></td>
                <td><strong>Remarks</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('remarks')]; ?></td>
                <td><strong>Target (eff.): </strong></td>
                <td width="10"><strong>:</strong></td>
                <td><?= number_format($target_eff, 2);?><td>
            </tr>
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th width="50">Seq. No</th>
                <th width="100">Body Part</th>
                <th width="200">Operation</th>
                <th width="100">Resource</th>
                <th width="70">Seam Length</th>
                <th width="40">SMV</th>
                <th width="60">Target (100%)</th>
                <th width="40">Effi.</th>
                <th width="60">Cycle Time(s)</th>
                <th width="50">Theo. MP</th>
                <th width="50">Lay. MP</th>
                <th width="60">W. Load %</th>
                <th width="50">Weight</th>
                <th width="185">W. Track</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData = sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv'] = $row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc'] = number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time'] = $row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp'] = $row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp'] = $row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load'] = $row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight'] = $row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking'] = $row[csf('worker_tracking')];
                    }
                }
                
                $operation_length_arr = return_library_array( "select id,seam_length from lib_sewing_operation_entry", "id","seam_length"  );
                $operation_arr = return_library_array("SELECT id, operation_name FROM lib_sewing_operation_entry", "id","operation_name");
                $sqlDtls = "SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc FROM ppl_gsd_entry_dtls WHERE mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";
                $data_array_dtls = sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm = array();
                
                $seqNosArr = array(); $weightsArr = array(); $pitchTimesArr = array(); $uclsArr = array(); $lclsArr = array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    /* if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                        $cycleTime=$slectResult[csf('total_smv')]*60;
                        $perc=$slectResult[csf('target_on_full_perc')];
                    } */
                    $smv=$slectResult[csf('total_smv')];
                    $cycleTime=$slectResult[csf('total_smv')]*60;
                    $perc=$slectResult[csf('target_on_full_perc')];
                    
                    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                    }
					else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
					else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        
                        $mpSumm[$rescId]+= $layOut;
                    }
                    $weight=fn_number_format(($smv*1)/($layOut*1),2);
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    //$weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $weightsArr[] = number_format($weight,2,'.','');
                    $pitchTimesArr[] = $mstDataArray[0][csf('pitch_time')];
                    $uclsArr[] = $ucl;
                    $lclsArr[] = $lcl;
					
					$tot_th_mp += $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
                    
					
                ?>
                    <tr>
                        <td align="center"><?= $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><?= $body_part[$slectResult[csf('body_part_id')]]; ?></td>
                        <td><?= $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center"><?= $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
                        <td><?= $operation_length_arr[$slectResult[csf('lib_sewing_id')]];?></td>
                        <td align="right"><?= number_format($smv,2,'.',''); ?></td>
                        <td align="center"><?= $perc; ?></td>
                        <td align="center"><?= number_format($slectResult[csf('target_on_effi_perc')],2,'.',''); ?></td>
                        <td align="center"><?= number_format($cycleTime,2,'.',''); ?></td>
                        <td align="right"><?= $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        <td align="right"><?= $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?></td>
                        <td align="center"><?= $balanceDataArray[$slectResult[csf('id')]]['work_load']; ?></td>
                        <td align="center"><?= $weight;?></td>
                        <td align="center"><?= $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                $seqNos = json_encode($seqNosArr);
                $weights = json_encode($weightsArr); 
                $pitchTimes = json_encode($pitchTimesArr); 
                $ucls = json_encode($uclsArr); 
                $lcls = json_encode($lclsArr);
				
				if(strpos($tot_mp,".") != "")
				{
					$tot_mp = number_format($tot_mp,2,'.','');
				}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
                <th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_th_mp,2,'.',''); ?></th>
				<th align="right"><? echo $tot_mp; ?></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="20%" valign="top">
                	<b>SMV Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">Assistant Operator</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($imSmv,2,'.',''); ?></td>
                        </tr>
                        
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="20%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						if(strpos($imMp,".")!="")
						{
							$imMp=number_format($imMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">Assistant Operator</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp,2,'.',','); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
                        </tr>
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="20%" valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="150"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td  valign="top">
                	<b>Image</b>
                    <table border="1" rules="all" class="rpt_table" width="100%">
                    <td align="center">
					<? 
                        $image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
                        
						if(count($image_location_arr)==1){$h=200;}
						elseif(count($image_location_arr)==2){$h=130;}
						elseif(count($image_location_arr)==3){$h=100;}
						else{$h=80;}
						
						foreach($image_location_arr as $image_path){
                            echo '<img src="../../../'.$image_path.'" height="'.$h.'" style="margin:3px;border:1px solid #BBB; border-radius:3px;" /> ';
                        }
                    ?>
                    </td>
                    </table>
                </td>
            </tr>
        </table>
        <div style="width:100%; margin-top:10px; height:220px; border:solid 1px" align="center">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="50">LCL</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Weight</td>
                </tr>
            </table>
           <canvas id="canvas" height="200" width="890"></canvas>
        </div>
        
        
    </div>
    <script>
		var lineChartData = {
            labels : <? echo $seqNos; ?>,
            datasets : [
				{
					
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $weights; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $ucls; ?>
				}
				,
				{
					//label: "My Third dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#4A7EBB",
					pointColor : "#4A7EBB",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#4A7EBB",
					data : <? echo $pitchTimes; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $lcls; ?>
				}
			]
        }
		
		window.onload = function()
		{
			var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                responsive : true
        	}); 
        }
	</script>
	<?
	exit();
}

if($action=="balancing_print9")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$machine_category = $machine_category;

	$mstDataArray=sql_select("select a.process_id,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,a.product_dept,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency,sum(d.po_quantity*c.total_set_qnty) as po_quantity from ppl_balancing_mst_entry b,ppl_gsd_entry_mst a left join  wo_po_details_master c on c.style_ref_no = a.style_ref and c.status_active=1 left join wo_po_break_down d on c.id = d.job_id and d.status_active=1 where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.process_id,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,a.product_dept,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency");
    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
    ob_start();
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <?php
        $lastID = sql_select("SELECT max(ID) as id FROM common_photo_library WHERE form_name = 'group_logo'");
        $imageGroupLogo = return_library_array("select id, image_location from common_photo_library where id='".$lastID[0]['ID']."' and form_name='group_logo'", "id", "image_location");
        ?> 

        <table cellpadding="0" cellspacing="0" width="100%">
            <tr> 
                <td> 
                    <table width="100%" border="0">
                        <tr>
                            <td align="center" colspan="9" style="font-size:24px"><strong><u><? echo $buyer_library[$mstDataArray[0][csf('company_id')]]; ?></u></strong></td>
                        </tr>
                        <?php
                        $lastID = sql_select("SELECT max(ID) as id FROM common_photo_library WHERE form_name = 'group_logo'");
                        $imageGroupLogo = return_library_array("select id, image_location from common_photo_library where id='".$lastID[0]['ID']."' and form_name='group_logo'", "id", "image_location");
                        ?>
                        <tr>
                            <td width="190">
                                <?php
                                foreach($imageGroupLogo as $image_path){
                                    echo '<img src="../../../'.$image_path.'" style="width:80px;height:80px;margin:3px;border:1px solid #BBB; border-radius:3px;" /> ';
                                }
                                ?>
                            </td>
                            <td align="center" colspan="9" style="font-size:24px"><strong><u>Operation Bulletin Report</u></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Style Ref.</strong></td>
                            <td><strong>: </strong><?= $mstDataArray[0][csf('style_ref')]; ?></td>
                            <td><strong>Buyer Name</strong></td>
                            <td><strong>: </strong><?= $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Garments Item</strong></td>
                            <td><strong>: </strong><?= $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
                            <td><strong>Applicable Period</strong></td>
                            <td><strong>: </strong><?= change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Prod. Dept</strong></td>
                            <td><strong>: </strong><?= $product_dept[$mstDataArray[0][csf('product_dept')]]; ?></td>
                            <td><strong>Insert Date Time</strong></td>
                            <td><strong>: </strong><?= $mstDataArray[0][csf('insert_date')]; ?></td>
                             
                        </tr>
                        <tr>
                            <td><strong>Bulletin Type</strong></td>
                            <td><strong>: </strong><?= $machine_category[$mstDataArray[0][csf('process_id')]]; ?></td>
                            <td><strong>Insert By</strong></td>
                            <td><strong>: </strong><?= $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Order Qty.</strong></td>
                            <td><strong>: </strong><?= $mstDataArray[0][csf('po_quantity')]; ?></td>
                            <td><strong>Update Date Time</strong></td>
                            <td><strong>: </strong><?= $mstDataArray[0][csf('update_date')]; ?></td>
                        </tr>
                        <tr>
                            <td><strong>System ID</strong></td>
                            <td><strong>: </strong><?= $mstDataArray[0][csf('system_no')]; ?></td>
                            <td><strong>Update By</strong></td>
                            <td><strong>: </strong><?= $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Remarks</strong></td>
                            <td><strong>: </strong><? echo $mstDataArray[0][csf('remarks')]; ?></td>
                        </tr>        
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                    <td  valign="top">
                        
                        <table border="0" rules="all" class="rpt_table" width="100%">
                            <td align="center">
                            <? 
                                $image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
                            // print_r($mstDataArray[0][csf('id')]);exit;
                                if(count($image_location_arr)==1){$h=240;$w=240;}
                                elseif(count($image_location_arr)==2){$h=130;$w=130;}
                                elseif(count($image_location_arr)==3){$h=100;$w=100;}
                                else{$h=80;$w=80;}
                                
                                foreach($image_location_arr as $image_path){
                                    echo '<img src="../../../'.$image_path.'" height="'.$h.'" width="'.$w.'" style="margin:3px;border:1px solid #BBB; border-radius:3px;" /> ';
                                }
                            ?>
                            </td>
                        </table>
                    </td>
                </td>
            </tr> 
	    </table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="70%" valign="top">
                    <table width="100%" align="left" cellspacing="0"  border="1" rules="all">
                        <thead bgcolor="#dddddd" align="center">
                            <th width="25px">SL</th>
                            <th width="50px">Category</th>
                            <th width="240">OPERATION</th>
                            <th width="100">M/C</th>
                            <th width="40">S.V.M</th>
                            <th width="60">TGT (100%)</th>
                            <th width="50">MAN REQ</th>
                            <th width="50">MAN ALCT</th>
                            <th width="90">PLAN WORK STATION</th>
                            <th width="95">REMARKS</th>
                        </thead>
                        <tfoot> <tr> <td id="spacer" style="height:0px;"> </td> </tr> </tfoot>
                        <?
                            $balanceDataArray=array();
                            if($bl_update_id>0)
                            {
                                $blData=sql_select("select a.gsd_dtls_id,a.smv,a.target_hundred_perc,a.cycle_time,a.theoritical_mp,a.layout_mp,a.work_load,a.weight,a.worker_tracking,b.pitch_time,b.target from ppl_balancing_dtls_entry a, ppl_balancing_mst_entry b where a.gsd_mst_id=b.gsd_mst_id and a.mst_id= b.id and b.id=$bl_update_id and a.is_deleted=0 and b.is_deleted=0");
                                
                                foreach($blData as $row)
                                {
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['pitch_time']=$row[csf('pitch_time')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['target']=$row[csf('target')];
                                }
                            }
                        // echo "<pre>";
                        // print_r($balanceDataArray);
                        // echo "</pre>";
                        // exit;
                        $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
                        $sqlDtls="SELECT body_part_id from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 group by  body_part_id";
                    
                        $data_arr_dtls=sql_select($sqlDtls);
                            
                        $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0; $sewSmv=0;   
                        $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                        $s=1;
                        $tot_tmp =0;
                        $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $chk_arr=array();          
                            foreach($data_arr_dtls as $selectResult)
                            {
                                $ttt = 0;
                                $gmts_item=explode(',',$selectResult[csf('body_part_id')]);
                                for($c=0;$c<count($gmts_item); $c++)
                                {
                                        $sqlBodyDtls="SELECT id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and body_part_id in($gmts_item[$c]) and is_deleted=0 group by id,body_part_id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc order by body_part_id,row_sequence_no";
                                        $data_array_dtls=sql_select($sqlBodyDtls);
                                       
                                        $rowspan = count($data_array_dtls);

                                        ?>
                                        <tr>
                                            <td style="border:1px solid black; text-align:center;" colspan="<? echo count($nameArray_size)+9;?>"><strong><? echo strtoupper($body_part[$gmts_item[$c]]); ?></strong></td>
                                        </tr>
                                         
                                    <? 
                                   
                                    foreach($data_array_dtls as $key=>$slectResult)
                                    {
                                        if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                                        {
                                            $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                                            $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                                            $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                                            $tot_perc+=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                                        }
                                        else
                                        {
                                            $smv=$slectResult[csf('total_smv')];
                                            $cycleTime=$slectResult[csf('total_smv')]*60;
                                            $perc=$slectResult[csf('target_on_full_perc')];
                                            $tot_perc+=$slectResult[csf('target_on_full_perc')];
                                        }
                                        $attachment=$slectResult[csf('attachment_id')];
                                        $rescId=$slectResult[csf('resource_gsd')];
                                        $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                                        $pitch_time=$balanceDataArray[$slectResult[csf('id')]]['pitch_time'];
                                        $target=$balanceDataArray[$slectResult[csf('id')]]['target'];
                                        
                                        $bgcolor="#FFFFFF";
                                        if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==70 || $rescId==147)
                                        {
                                            $helperSmv=$helperSmv+$smv;
                                            $helperMp=$helperMp+$layOut;
                                            $bgcolor="#90EE90";
                                        }
                                        else if($rescId==53)
                                        {
                                            $fIMSmv=$fIMSmv+$smv;
                                            $fImMp=$fImMp+$layOut;
                                        }
                                        else if($rescId==69)
                                        {
                                            $sewSmv=$sewSmv+$smv;
                                            $sewMp= $sewMp+$layOut;
                                            $bgcolor="#90EE90";
                                        }
                                        else if($rescId==54)
                                        {
                                            $fQISmv=$fQISmv+$smv;
                                            $fQiMp=$fQiMp+$layOut;
                                        }
                                        else if($rescId==55)
                                        {
                                            $polyHelperSmv=$polyHelperSmv+$smv;
                                            $polyHelperMp=$polyHelperMp+$layOut;
                                            $bgcolor="#90EE90";
                                        }
                                        else if($rescId==56)
                                        {
                                            $pkSmv=$pkSmv+$smv;
                                            $pkMp=$pkMp+$layOut;
                                        }
                                        else if($rescId==90)
                                        {
                                            $htSmv=$htSmv+$smv;
                                            $htMp=$htMp+$layOut;
                                        }
                                        else if($rescId==176)
                                        {
                                            $imSmv=$imSmv+$smv;
                                            $imMp=$imMp+$layOut;
                                        }
                                        else
                                        {
                                            $machineSmv=$machineSmv+$smv;
                                            $machineMp=$machineMp+$layOut;
                                            
                                            $mpSumm[$rescId]+= $layOut;
                                        }
                                        
                                        $ucl=number_format($pitch_time+($pitch_time*0.10),2,'.','');
                                        $lcl=number_format($pitch_time-($pitch_time*0.10),2,'.','');

                                    
                                        
                                        $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                                        $weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                                        
                                        $tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];?>

                                    
                                    <td width="25px" align="center"><?=$s++;?></td>
                                    <td width="50px"><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"><? echo strtoupper($production_resource_arr[$slectResult[csf('resource_gsd')]]); ?></td>
                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"><? echo number_format($smv,2,'.','');$tot_smv+=$smv; ?></td>
                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"><? echo $perc; ?></td>
                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center">
                                    <?php
                                    echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; 
                                    $tot_tmp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; 
                                    ?>
                                    </td>
                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?></td>

                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center">
                                        <? echo round($balanceDataArray[$slectResult[csf('id')]]['layout_mp']);?>
                                        <? //if($balanceDataArray[$slectResult[csf('id')]]['layout_mp']>1.49){ echo "2";}else{ echo "1";} ?>
                                    </td>
                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                                    <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"></td>
                                </tr>
                                <? } ?>
                            <?	
                                //$tot_smv+=$smv;
                                //$tot_tmp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
                            // $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                            }
                        }
                            
                            $seqNos= json_encode($seqNosArr);
                            $weights= json_encode($weightsArr); 
                            $pitchTimes= json_encode($pitchTimesArr); 
                            $ucls= json_encode($uclsArr); 
                            $lcls= json_encode($lclsArr);
                            
                            if(strpos($tot_mp,".")!="")
                            {
                                $tot_mp=number_format($tot_mp,2,'.','');
                            }
                        ?>
                        <tfoot style="position:relative; bottom: 0;">
                        <?php
                                $total_smv = $helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv;
                                $tot_tmp = $tot_smv/($total_smv/$mstDataArray[0][csf('allocated_mp')]);
                                
                            ?>
                            <th align="right" colspan="3">Total</th>
                            <th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
                            <th>&nbsp;</th>
                            <th><? echo number_format($tot_tmp,2,'.',''); ?></th>
                            <th><? echo number_format($tot_mp,2,'.',''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tfoot>
                        
                    </table>
                     
                    
                </td>
            <td width="1%" valign="top"></td>
            <td width="20%" valign="top">
                	<table border="1" rules="all" class="rpt_table" width="100%">
                        <tr bgcolor="#FFFFFF">
                        	<td align="center" width="115">INLINE</td>
                            <td align="center" style="padding-right:5px">S.M.V</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>SEWING</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                    	<tr bgcolor="#FFFFFF">
                        	<td >HELPER</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>IRON</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($sewSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                    <br><br>
                    <table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">MAN LEVEL</td>
                            <td align="right" style="padding-right:5px"><? echo number_format($tot_mp,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>TACT TIME</td>
                            <td align="right" style="padding-right:5px"><? echo number_format($pitch_time,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>UCL (10%)</td>
                            <td align="right" style="padding-right:5px"><? echo number_format($ucl,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>LCL (10%)</td>
                            <?php
                                $total_smv = $helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv;
                                $tot_smv = $tot_smv/($total_smv/$mstDataArray[0][csf('allocated_mp')]);
                                
                            ?>
                            <td align="right" style="padding-right:5px"><? echo number_format($lcl,2,'.',''); ?></td>

                            
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>100% TGT/H</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($target,0,'.',''); ?></td>
                        </tr>  
                        <tr bgcolor="#FFFFFF">
                        	<td>95% TGT/H</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($target*.95,0,'.',''); ?></td>
                        </tr>  
                        <!-- <tr bgcolor="#FFFFFF">
                        	<td>80% TGT/H</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format( $target*.80,0,'.',''); ?></td>
                        </tr>                 -->
                    </table>
                    <br><br>
                    <table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
                        <tr bgcolor="#FFFFFF">
                        	<td align="center">MACHINE TYPE</td>
                            <td align="center" style="padding-right:5px">REQ</td>
                        </tr> 
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="150"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                    <br><br>
                    <?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp+$sewMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}

                        if(strpos($sewMp,".")!="")
						{
							$sewMp=number_format($sewMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						if(strpos($imMp,".")!="")
						{
							$imMp=number_format($imMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	
                	<table border="1" rules="all" class="rpt_table" width="100%">
                        <tr bgcolor="#FFFFFF">
                        	<td align="center">MANPOWER</td>
                            <td align="center" style="padding-right:5px">REQ</td>
                        </tr> 
                        <tr bgcolor="#E9F3FF">
                        	<td>OPERATOR</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>IRON MAN</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $sewMp; ?></td>
                        </tr>
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">HELPER</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>                              
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </table>
        <br/>
        <br/>
        <br/>
        <br/>
        <div>
            CENTRAL .IE<br>
            -------------------<br/>
            PREPARED BY
        </div>
    
        
    </div>
  
    <br>
	<?

	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo $html;
	?>
		<script type="text/javascript">
            
            setTimeout(alertAfter3Seconds, 3000);

            function alertAfter3Seconds()
            {
                window.location.href = '<?php echo $filename;?>';
            }
		</script>
	<?php

    exit();
    // fclose($filename);
    // header('Content-type: text/csv');
    // header('Content-disposition:attachment; filename="'.$filename.'"');
    // readfile($filename);
	
}

if($action=="balancing_print10")
{
    $data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library  = return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	 
    $mstDataArray = sql_select("SELECT a.process_id,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,a.product_dept,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency,sum(d.po_quantity*c.total_set_qnty) as po_quantity FROM ppl_balancing_mst_entry b,ppl_gsd_entry_mst a left join  wo_po_details_master c on c.style_ref_no = a.style_ref and c.status_active=1 left join wo_po_break_down d on c.id = d.job_id and d.status_active=1 where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.process_id,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,a.product_dept,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency");

    $production_resource_arr = return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
    ob_start();
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:1300px">
 
        <?php
        $lastID = sql_select("SELECT max(ID) as id FROM common_photo_library WHERE form_name = 'group_logo'");
        $imageGroupLogo = return_library_array("select id, image_location from common_photo_library where id='".$lastID[0]['ID']."' and form_name='group_logo'", "id", "image_location");
        ?>
        <table cellspacing="0" cellpadding="0" width="1300">
            <tr> 
                <td> 
                    <table width="100%" border="0">
                        <tr>
                            <td align="left">
                                <?php
                                foreach($imageGroupLogo as $image_path){
                                    echo '<img src="../../../'.$image_path.'" style="width:80px;height:80px;margin:3px;border:1px solid #BBB; border-radius:3px;" /> ';
                                }
                                ?>
                            </td>
                            <td colspan="4" align="center">
                                <strong style="font-size:xx-large;">Operation Bulletin Report</strong><br> 
                            </td>
                        </tr>
                        <tr>
                            <td align="left" width='180'><strong>Style Ref.</strong></td>
                            <td align="left" width='2'><strong>:</strong></td>
                            <td align="left" width='400'><?= $mstDataArray[0][csf('style_ref')]; ?></td>            
                            <td align="left" width='180'><strong>Buyer Name</strong></td>
                            <td align="left" width='2'><strong>:</strong></td>
                            <td align="left"><?= $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                        </tr>
                        <tr>
                            <td align="left" width='180'><strong>Garments Item</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left" width='400'><?= $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
                            <td align="left" width='180'><strong>Applicable Period</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left"><?= change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                        </tr>
                  
                        <tr>
                            <td align="left" width='180'><strong>Prod. Dept</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left" width='400'><?= $product_dept[$mstDataArray[0][csf('product_dept')]]; ?></td>
                            <td align="left" width='180'><strong>Insert Date Time</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left"><?= $mstDataArray[0][csf('insert_date')]; ?></td>
                        </tr>
                        
 
                        <tr>
                            <td align="left" width='180' valign="top"><strong>Bulletin Type</strong></td>
                            <td align="left" width='10' valign="top"><strong>:</strong></td>
                            <td align="left" width='400'valign="top"><?= $machine_category[$mstDataArray[0][csf('process_id')]]; ?></td>           
                            <td align="left" width='180'><strong>Insert By</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left"><?= $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                        </tr>
                        <tr>
                            <td align="left" width='180'><strong>Order Qty.</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left" width='400'><?= $mstDataArray[0][csf('po_quantity')]; ?></td>            
                            <td align="left" width='180'><strong>Update Date Time</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left"><?= $mstDataArray[0][csf('update_date')]; ?></td>
                        </tr>
                        <tr>
                            <td align="left" width='180'><strong>System ID</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left" width='400'><?= $mstDataArray[0][csf('system_no')]; ?></td>
                            <td align="left" width='180'><strong>Update By</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left"><?= $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                        </tr>
                        <tr>
                            <td align="left" width='180'><strong>Remarks</strong></td>
                            <td align="left" width='10'><strong>:</strong></td>
                            <td align="left" width='400'><? echo $mstDataArray[0][csf('remarks')]; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                    <td valign="top">
                        <table border="0" rules="all" class="rpt_table" width="100%">
                            <td align="center" style="border: 0;">
                                <? 
                                $image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
                                if(count($image_location_arr)==1){$h=240;$w=240;}
                                elseif(count($image_location_arr)==2){$h=100;$w=170;}
                                elseif(count($image_location_arr)==3){$h=150;$w=130;}
                                else{$h=100;$w=80;}
                                ?>

                                <tr style="border:none">
                                    <?php
                                    $i = 0;
                                    foreach($image_location_arr as $key1=>$image_path){
                                    $img_path= "../../../".$image_path;
                                    ?>
                                    <td style="border:none;text-align: end;">
                                    <?php 
                                    if($i ==0){
                                        echo '<p align="center">Front Part</p>';
                                        } 
                                        else{
                                            echo '<p align="center">Back Part</p> ';
                                        }
                                        ?>
                                        <img src="<?php echo $img_path; ?>" height="210" width="170" style="margin:3px;border:1px solid #BBB; border-radius:3px;" ></td>
                                    <?php 
                                    $i++;
                                    }
                                    ?>
                                </tr>
                            </td>
                        </table>
                    </td>
                </td>
            </tr>
        </table>
 

        <table cellpadding="0" cellspacing="0" width="100%">
            <tr> 
                <td width="70%" valign="top">
                    <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
                        <thead bgcolor="#dddddd" align="center">
                            <th width="25px">Category</th>
                            <th width="25px">SL</th>
                            <th width="300">OPERATION</th>
                            <th width="100">M/C</th>
                            <th width="40">S.V.M</th>
                            <th width="60">TGT (100%)</th>
                            <th width="50">MAN REQ</th>
                            <th width="50">MAN ALCT</th>
                            <th width="50">PLAN. WS</th>
                            <th width="80">RMK.</th>
                        </thead>
                        <tfoot> <tr> <td id="spacer" style="height:0px;"> </td> </tr> </tfoot>
                        <?
                            $balanceDataArray=array();
                            if($bl_update_id>0)
                            {
                                $blData=sql_select("select a.gsd_dtls_id,a.smv,a.target_hundred_perc,a.cycle_time,a.theoritical_mp,a.layout_mp,a.work_load,a.weight,a.worker_tracking,b.pitch_time,b.target from ppl_balancing_dtls_entry a, ppl_balancing_mst_entry b where a.gsd_mst_id=b.gsd_mst_id and a.mst_id= b.id and b.id=$bl_update_id and a.is_deleted=0 and b.is_deleted=0");
                               // echo $blData;die;
                                foreach($blData as $row)
                                {
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['pitch_time']=$row[csf('pitch_time')];
                                    $balanceDataArray[$row[csf('gsd_dtls_id')]]['target']=$row[csf('target')];
                                }
                            }

                            //print_r( $balanceDataArray);die;
                            
                            $operation_arr=return_library_array( "SELECT id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
                            $attach_id=return_library_array( "SELECT id,attachment_name from lib_attachment",'id','attachment_name');
                            


                            $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 group by id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc order by row_sequence_no";

                            // echo $sqlDtls;die;


                            $data_array_dtls=sql_select($sqlDtls);

                            $data_array = array();
                            foreach ($data_array_dtls as $v) 
                            {
                                // echo $v['BODY_PART_ID']."<br>";
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['row_sequence_no'] = $v['ROW_SEQUENCE_NO'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['mst_id'] = $v['MST_ID'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['lib_sewing_id'] = $v['LIB_SEWING_ID'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['resource_gsd'] = $v['RESOURCE_GSD'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['attachment_id'] = $v['ATTACHMENT_ID'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['efficiency'] = $v['EFFICIENCY'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['total_smv'] = $v['TOTAL_SMV'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['target_on_full_perc'] = $v['TARGET_ON_FULL_PERC'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['target_on_effi_perc'] = $v['TARGET_ON_EFFI_PERC'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['body_part_id'] = $v['BODY_PART_ID'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['pitch_time'] = $v['pitch_time'];
                                $data_array[$v['BODY_PART_ID']][$v['ID']]['target'] = $v['target'];
                            }

                           // print_r($data_array);die;
                            
                            $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0; $sewSmv=0;   
                            $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                            $s=1;
                            //$tot_tmp =0;
                            $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $chk_arr=array();
                            foreach($data_array as $bp_id=>$bp_data)
                            {
                                foreach($bp_data as $rid=>$r_data)
                                {
                                    $count_body_part[$bp_id]++;
                                }
                            }
                            $tot_tmp =0;
                            foreach($data_array as $bp_id=>$bp_data)
                            {
                                $bp=0;
                                foreach($bp_data as $rid=>$r)
                                {
                                
                                    if($balanceDataArray[$rid]['smv']>0)	
                                    {
                                        $smv=$balanceDataArray[$rid]['smv'];
                                        $cycleTime=$balanceDataArray[$rid]['cycle_time'];
                                        $perc=$balanceDataArray[$rid]['perc'];
                                        $tot_perc+=$balanceDataArray[$v[csf('id')]]['perc'];
                                    }
                                    else
                                    {
                                        $smv=$r['total_smv'];
                                        $cycleTime=$r['total_smv']*60;
                                        $perc=$r['target_on_full_perc'];
                                        $tot_perc+=$r[csf('target_on_full_perc')];
                                    }
                                    $attachment=$r['attachment_id'];
                                    $rescId=$r['resource_gsd'];
                                    $layOut=$balanceDataArray[$rid]['layout_mp'];
                                    $target=$balanceDataArray[$rid]['target'];
                                    $pitch_time=$balanceDataArray[0]['PITCH_TIME'];
                                    $pitch_time2= $mstDataArray[0][csf('pitch_time')];
                                    // $pitch_time=$balanceDataArray[$r[csf('id')]]['pitch_time'];
                                    
                                    $bgcolor="#FFFFFF";
                                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==70 || $rescId==147)
                                    {
                                        $helperSmv=$helperSmv+$smv;
                                        $helperMp=$helperMp+$layOut;
                                        $bgcolor="#90EE90";
                                    }
                                    else if($rescId==53)
                                    {
                                        $fIMSmv=$fIMSmv+$smv;
                                        $fImMp=$fImMp+$layOut;
                                    }
                                    else if($rescId==69)
                                    {
                                        $sewSmv=$sewSmv+$smv;
                                        $sewMp= $sewMp+$layOut;
                                        $bgcolor="#90EE90";
                                    }
                                    else if($rescId==54)
                                    {
                                        $fQISmv=$fQISmv+$smv;
                                        $fQiMp=$fQiMp+$layOut;
                                    }
                                    else if($rescId==55)
                                    {
                                        $polyHelperSmv=$polyHelperSmv+$smv;
                                        $polyHelperMp=$polyHelperMp+$layOut;
                                        $bgcolor="#90EE90";
                                    }
                                    else if($rescId==56)
                                    {
                                        $pkSmv=$pkSmv+$smv;
                                        $pkMp=$pkMp+$layOut;
                                    }
                                    else if($rescId==90)
                                    {
                                        $htSmv=$htSmv+$smv;
                                        $htMp=$htMp+$layOut;
                                    }
                                    else if($rescId==176)
                                    {
                                        $imSmv=$imSmv+$smv;
                                        $imMp=$imMp+$layOut;
                                    }
                                    else
                                    {
                                        $machineSmv=$machineSmv+$smv;
                                        $machineMp=$machineMp+$layOut;
                                        
                                        $mpSumm[$rescId]+= $layOut;
                                    }
                                    
                                    $ucl=number_format($pitch_time+($pitch_time*0.10),2,'.','');
                                    $lcl=number_format($pitch_time-($pitch_time*0.10),2,'.','');
                                    $seqNosArr[]=$r[csf('row_sequence_no')];
                                    $weightsArr[]=number_format($balanceDataArray[$r[csf('id')]]['weight'],2,'.','');
                                    $tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
                                    $rowspan= $count_body_part[$bp_id];
                                    ?>
                                    <tr>
                                        <?
                                        if($bp==0)
                                        {
                                            ?>
                                            <td rowspan="<? echo $rowspan;?>"><? echo $body_part[$bp_id]; ?></td>
                                            <?
                                            $bp++;
                                        }
                                        ?>
                                        <td><?=$s++;?></td>
                                        <td><? echo $operation_arr[$r['lib_sewing_id']]; ?></td>
                                        <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center">
                                            <? echo $production_resource_arr[$r['resource_gsd']]; ?>
                                        </td>
                                        <td align="right" style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;">
                                            <? echo number_format($smv,2,'.',''); ?>
                                        </td>
                                        <td align="right" style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $perc; ?></td>
                                        <td align="right" style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;">
                                            <?php
                                            $smv =$smv;
                                            $pitch_time2 = $pitch_time2;
                                            $total_tmp = $smv/$pitch_time2;
                                            echo $total_tmp2 = number_format(floor($total_tmp*100)/100,2, '.', '');
                                            $tot_tmp += $total_tmp2;
                                            ?>
                                        </td>
                                        <td align="center" style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;">
                                            <? echo $balanceDataArray[$rid]['layout_mp']; ?>
                                        </td>
                                        <td align="center"><? echo $balanceDataArray[$rid]['layout_mp'];?></td>
                                        <td align="center"><? echo $balanceDataArray[$r[csf('id')]]['worker_tracking']; ?></td>
                                    </tr>
                                    <?	
                                    $tot_smv+=$smv;
                                    $tot_mp+=$balanceDataArray[$rid]['layout_mp'];
                                    $i++;
                                }
                            }
                            // end loop 
                            
                            $seqNos= json_encode($seqNosArr);
                            $weights= json_encode($weightsArr); 
                            $pitchTimes= json_encode($pitchTimesArr); 
                            $ucls= json_encode($uclsArr); 
                            $lcls= json_encode($lclsArr);
                            
                            if(strpos($tot_mp,".")!="")
                            {
                                $tot_mp=number_format($tot_mp, 2, '.' ,'');
                            }
                        ?>
                        <tfoot style="position:relative; bottom: 0;">
                            <?php
                            $total_smv = $helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv;
                            // $tot_tmp = $tot_smv/($total_smv/$mstDataArray[0][csf('allocated_mp')]);
                            ?>
                            <th align="right" colspan="3">Total</th>
                            <th></th>
                            <th align="right"><? echo number_format($tot_smv, 2, '.', ''); ?></th>
                            <th></th>
                            <th align="right"><?= number_format($tot_tmp,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_mp, 2, '.' ,''); ?></th>
                            <th align="right"><? echo number_format($tot_mp, 2, '.' ,''); ?></th>
                            <th></th>
                        </tfoot> 
                    </table>
                </td>

                <?php
               // print_r($pitch_time);die;
                ?>

                <td width="1%" valign="top"></td>

                <td width="20%" valign="top">
                    <table border="1" rules="all" class="rpt_table" width="100%">
                        <tr bgcolor="#FFFFFF">
                            <td align="center" width="115">INLINE</td>
                            <td align="center" style="padding-right:5px">S.M.V</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>SEWING</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv, 2 ,'.', ''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td >HELPER</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv, 2, '.', ''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>IRON</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($sewSmv, 2, '.', ''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                    <br><br>
                    <table border="1" rules="all" class="rpt_table" width="100%">
                        <tr bgcolor="#FFFFFF">
                            <td width="115">MAN LEVEL</td>
                            <td align="right" style="padding-right:5px"><? echo number_format($tot_mp, 2, '.', ''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>TACT TIME</td>
                            <td align="right" style="padding-right:5px"><? echo number_format($mstDataArray[0]['PITCH_TIME'],2);   // number_format($pitch_time, 2, '.' ,''); ?></td>
                        </tr>
                        <?php
                        $count_ucl =  $mstDataArray[0]['PITCH_TIME']*10/100;
 
                        $ucl = $mstDataArray[0]['PITCH_TIME']+$count_ucl;
                        $lcl = $mstDataArray[0]['PITCH_TIME']-$count_ucl;
                        
                        ?>
                        <tr bgcolor="#E9F3FF">
                            <td>UCL (10%)</td>
                            <td align="right" style="padding-right:5px"><? echo number_format($ucl, 2, '.' ,''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                            <td>LCL (10%)</td>
                            <?php
                            $total_smv = $helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv;
                            $tot_smv = $tot_smv/($total_smv/$mstDataArray[0][csf('allocated_mp')]);
                            ?>
                            <td align="right" style="padding-right:5px"><? echo number_format($lcl,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>100% TGT/H</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($target,0,'.',''); ?></td>
                        </tr>  
                        <tr bgcolor="#FFFFFF">
                            <td>95% TGT/H</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($target*.95,0,'.',''); ?></td>
                        </tr>
                    </table>
                    <br><br>
                    <table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
                        <tr bgcolor="#FFFFFF">
                            <td align="center">MACHINE TYPE</td>
                            <td align="center" style="padding-right:5px">REQ</td>
                        </tr> 
                        <?
                        $x=1; $totatMp=0;
                        foreach($mpSumm as $key=>$mp)
                        {
                            if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
                            
                            if(strpos($mp,".")!="")
                            {
                                $mp=number_format($mp,2,'.','');
                            }
                        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td width="150"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
                            $totatMp+=$mp;
                            $x++;	
                        }
                        
                        if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
                        
                        if(strpos($totatMp,".")!="")
                        {
                            $totatMp=number_format($totatMp,2,'.','');
                        }
                       ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                    <br><br>
                    <?
                        $totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp+$sewMp;
                        
                        if(strpos($helperMp,".")!="")
                        {
                            $helperMp=number_format($helperMp,2,'.','');
                        }
                        
                        if(strpos($machineMp,".")!="")
                        {
                            $machineMp=number_format($machineMp,2,'.','');
                        }

                        if(strpos($sewMp,".")!="")
                        {
                            $sewMp=number_format($sewMp,2,'.','');
                        }
                        
                        if(strpos($sQiMp,".")!="")
                        {
                            $sQiMp=number_format($sQiMp,2,'.','');
                        }
                        
                        if(strpos($totatMp,".")!="")
                        {
                            $fImMp=number_format($fImMp,2,'.','');
                        }
                        
                        if(strpos($fQiMp,".")!="")
                        {
                            $fQiMp=number_format($fQiMp,2,'.','');
                        }
                        
                        if(strpos($polyHelperMp,".")!="")
                        {
                            $polyHelperMp=number_format($polyHelperMp,2,'.','');
                        }
                        
                        if(strpos($pkMp,".")!="")
                        {
                            $pkMp=number_format($pkMp,2,'.','');
                        }
                        
                        if(strpos($htMp,".")!="")
                        {
                            $htMp=number_format($htMp,2,'.','');
                        }
                        if(strpos($imMp,".")!="")
                        {
                            $imMp=number_format($imMp,2,'.','');
                        }
                        
                        if(strpos($totMpSumm,".")!="")
                        {
                            $totMpSumm=number_format($totMpSumm,2,'.','');
                        }
                    ?>
                    <table border="1" rules="all" class="rpt_table" width="100%">
                        <tr bgcolor="#FFFFFF">
                            <td align="center">MANPOWER</td>
                            <td align="center" style="padding-right:5px">REQ</td>
                        </tr> 
                        <tr bgcolor="#E9F3FF">
                            <td>OPERATOR</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>IRON MAN</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $sewMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td width="115">HELPER</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>                              
                        <tr bgcolor="#FFFFFF">
                            <td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>

            </tr>
        </table>

        <br/>
        <br/>
        <br/>
        <br/>
        <div>
            CENTRAL .IE<br>
            -------------------<br/>
            PREPARED BY
        </div>
         
    </div>
      
	<?

	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo $html;
	?>
		<script type="text/javascript">
            setTimeout(alertAfter3Seconds, 3000);

            function alertAfter3Seconds()
            {
                window.location.href = '<?php echo $filename;?>';
            }
		</script>
	<?php

	exit();
}
 
  
if($action=="balancing_print8")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	$mstDataArray=sql_select("SELECT a.PROCESS_ID,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
    ob_start();
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="100%" border="0">
            <tr>
                <td align="center" colspan="9" style="font-size:24px"><strong><u>Operation Bulletin Report</u></strong></td>
            </tr>
            <tr>
                <td width="110"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="190"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="170"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="110"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Working Hour</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Allocated MP</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Extention No</strong></td>
                <td><strong>:</strong></td>
                <td colspan="7"><? echo $mstDataArray[0][csf('extention_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Efficiency</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('efficiency')]; ?></td>
                <td><strong>Pitch Time</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Target</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            
            
            <tr>
                <td><strong>SAM</strong></td>
                <td><strong>:</strong></td>
                <td><? echo number_format($mstDataArray[0][csf('total_smv')],2); ?></td>
                <td><strong>Insert Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('insert_date')]; ?></td>
                <td><strong>Bulletin Type</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
            </tr>
            
            
            <tr>
                <td><strong>Insert By</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                <td><strong>Update Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('update_date')]; ?></td>
                <td><strong>System ID</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('system_no')]; ?></td>        
            </tr>
            
            <tr>
                <td><strong>Update By</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                <td><strong>Product Des.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="7"><? echo $mstDataArray[0][csf('prod_description')]; ?></td>
            </tr>
            
            <tr>
                <td><strong>Fabric Type</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Remarks</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="7"><? echo $mstDataArray[0][csf('remarks')]; ?></td>
            </tr>
            
            
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <!-- <th width="50">Seq. No</th> -->
                <th width="100">Category</th>
                <th width="25">SL</th>
                <th width="240">Operation</th>
                <th width="100">Machine / Non-Machine</th>
                <th width="40">SAM</th>
                <th width="60">Target (100%)</th>
                <th width="60">Cycle Time(s)</th>
                <th width="50">Theo. MP</th>
                <th width="50">Lay. MP</th>
                <th width="50">Weight</th>
                <th width="90">Balancing Target</th>
                <th width="90">Attachment</th>
                <th width="95">Operation Remarks</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData=sql_select("SELECT gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
                $operation_arr=return_library_array( "SELECT id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
                $attach_id=return_library_array( "SELECT id,attachment_name from lib_attachment",'id','attachment_name');

            

               //  $sqlDtls="SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc, b.target_on_effi_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id='".$mstDataArray[0][csf('id')]."' and b.is_deleted=0 order by b.row_sequence_no asc";
                
            $sqlDtls = "SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 group by id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc order by row_sequence_no asc";
            $data_array_dtls=sql_select($sqlDtls);

            $data_array = array();
            foreach ($data_array_dtls as $v)  
            {
                // echo $v['BODY_PART_ID']."<br>";
                $data_array[$v['BODY_PART_ID']][$v['ID']]['row_sequence_no'] = $v['ROW_SEQUENCE_NO'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['mst_id'] = $v['MST_ID'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['lib_sewing_id'] = $v['LIB_SEWING_ID'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['resource_gsd'] = $v['RESOURCE_GSD'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['attachment_id'] = $v['ATTACHMENT_ID'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['efficiency'] = $v['EFFICIENCY'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['total_smv'] = $v['TOTAL_SMV'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['target_on_full_perc'] = $v['TARGET_ON_FULL_PERC'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['target_on_effi_perc'] = $v['TARGET_ON_EFFI_PERC'];
                $data_array[$v['BODY_PART_ID']][$v['ID']]['body_part_id'] = $v['BODY_PART_ID'];
            }
            // echo"<pre>";print_r($data_array);die;

				
            $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0; $sewSmv=0;   
            $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
            $s=1;
            //$seqNosArr=array(); 
           $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $chk_arr=array();
            foreach($data_array as $bp_id=>$bp_data)
            {
                foreach($bp_data as $rid=>$r_data)
                {
                    $count_body_part[$bp_id]++;
                }
            }
                
            foreach($data_array as $bp_id=>$bp_data)
            {
                $bp=0;
                foreach($bp_data as $rid=>$r)
                {
                
                    if($balanceDataArray[$rid]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$rid]['smv'];
                        $cycleTime=$balanceDataArray[$rid]['cycle_time'];
                        $perc=$balanceDataArray[$rid]['perc'];
                    }
                    else
                    {
                        $smv=$r['total_smv'];
                        $cycleTime=$r['total_smv']*60;
                        $perc=$r['target_on_full_perc'];
                    }
                    $attachment=$r['attachment_id'];
                    $rescId=$r['resource_gsd'];
                    $layOut=$balanceDataArray[$rid]['layout_mp'];
                    
                    $bgcolor="#FFFFFF";
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                        $bgcolor="#90EE90";
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==69)
                    {
                        $sewSmv=$sewSmv+$smv;
                        $sewMp= $sewMp+$layOut;
                        $bgcolor="#90EE90";
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                        $bgcolor="#90EE90";
                    }
                    else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
                    else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
                    else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl = number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl = number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    
                    //$seqNosArr[] = $r['row_sequence_no'];
                   // $weightsArr[] = number_format($balanceDataArray[$rid]['weight'],2,'.','');
                   // $pitchTimesArr[] = $mstDataArray[0][csf('pitch_time')];
                   // $uclsArr[] = $ucl;
                   // $lclsArr[] = $lcl;
                    
                    $tot_th_mp += $balanceDataArray[$rid]['theoritical_mp'];
                    $rowspan = $count_body_part[$bp_id];
                    ?>
                    <tr>
                        <!-- <td align="center"><? //echo $r['row_sequence_no')]; ?></td> -->
                        <?
                        if($bp==0)
                        {
                            ?>
                                <td rowspan="<? echo $rowspan;?>"><? echo $body_part[$bp_id]; ?></td>
                            <?
                            $bp++;
                        }
                        ?>
                        <td><?=$s++;?></td>
                        <td><? echo $operation_arr[$r['lib_sewing_id']]; ?></td>
                        <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"><? echo $production_resource_arr[$r['resource_gsd']]; ?></td>

                        <td align="right"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="center" style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $perc; ?></td>
                        <td align="center"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo number_format($cycleTime,2,'.',''); ?></td>
                        <td align="right"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $balanceDataArray[$rid]['theoritical_mp']; ?></td>
                        <td align="right"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $balanceDataArray[$rid]['layout_mp']; ?></td>
                        <td align="center"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $balanceDataArray[$rid]['weight']; ?></td>
                        <td align="center"><?echo $perc*$balanceDataArray[$rid]['layout_mp'];?></td>
                        <td align="center"><? echo $attach_id[$attachment]; ?></td>
                        <td align="center"><? echo $balanceDataArray[$rid]['worker_tracking']; ?></td>
                    </tr>
                    <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$rid]['layout_mp'];
                    $i++;
                }
            }
 
            
            $sqlDtls="SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc, b.target_on_effi_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id='".$mstDataArray[0][csf('id')]."' and b.is_deleted=0 order by b.row_sequence_no asc";
            $data_array_dtls=sql_select($sqlDtls);
            $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array();
            foreach($data_array_dtls as $row)
            { 
                $ucl = number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                $lcl = number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                
                $seqNosArr[] = $row['ROW_SEQUENCE_NO'];
                $weightsArr[] = number_format($balanceDataArray[$row['ID']]['weight'],2,'.','');
                $pitchTimesArr[] = $mstDataArray[0][csf('pitch_time')];
                $uclsArr[] = $ucl;
                $lclsArr[] = $lcl;
            }
            // end loop  
            $seqNos= json_encode($seqNosArr);
            $weights= json_encode($weightsArr); 
            $pitchTimes= json_encode($pitchTimesArr); 
            $ucls= json_encode($uclsArr); 
            $lcls= json_encode($lclsArr);
            
            if(strpos($tot_mp,".")!="")
            {
                $tot_mp=number_format($tot_mp,2,'.','');
            }
			?>
			<tfoot>
				
				<th align="right" colspan="4">Total</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_th_mp,2,'.',''); ?></th>
				<th align="right"><? echo $tot_mp; ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="20%" valign="top">
                	<b>SAM Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">Sewing Helper</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Iron</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($sewSmv,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv,2,'.',''); ?></td>
                        </tr>                  
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="20%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp+$sewMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}

                        if(strpos($sewMp,".")!="")
						{
							$sewMp=number_format($sewMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						if(strpos($imMp,".")!="")
						{
							$imMp=number_format($imMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">Sewing Helper</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Iron</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $sewMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp,2,'.',','); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
                        </tr>
                        
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="20%" valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="150"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td  valign="top">
                	
                    <table border="0" rules="all" class="rpt_table" width="100%">
                    <td align="center">
					<? 
                        $image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
                        
						if(count($image_location_arr)==1){$h=240;}
						elseif(count($image_location_arr)==2){$h=130;}
						elseif(count($image_location_arr)==3){$h=100;}
						else{$h=80;}
						
						foreach($image_location_arr as $image_path){
                            echo '<img src="../../../'.$image_path.'" height="'.$h.'" style="margin:3px;border:1px solid #BBB; border-radius:3px;" /> ';
                        }
                    ?>
                    </td>
                    </table>
                </td>
            </tr>
        </table>


        <div style="width:100%; margin-top:10px; height:220px; border:solid 1px" align="center">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="50">LCL</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Weight</td>
                </tr>
            </table>
           <canvas id="canvas" height="200" width="890"></canvas>
        </div>
        
        
    </div>
    <script>
		var lineChartData = {
            labels : <? echo $seqNos; ?>,
            datasets : [
				{
					//label: "My First dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $weights; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $ucls; ?>
				}
				,
				{
					//label: "My Third dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#4A7EBB",
					pointColor : "#4A7EBB",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#4A7EBB",
					data : <? echo $pitchTimes; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $lcls; ?>
				}
			]
        }

        var ctx = document.getElementById("canvas").getContext("2d");
        window.myLine = new Chart(ctx).Line(lineChartData, {
            responsive : true
        }); 
		
        // not working this function ---alhassan----
		// window.onload = function()
		// {
		// 	var ctx = document.getElementById("canvas").getContext("2d");
        //     window.myLine = new Chart(ctx).Line(lineChartData, {
        //         responsive : true
        // 	}); 
        // }
	</script>
    <br>
    <br>
    <br>
    <br>
    <br>
    <table id="signatureTblId" width="901.5" style="padding-top:70px;">
 
		<tr>
			<td style="text-align: center; font-size:18px; border-top:1px solid;width: 70px;"><strong>Line IE</strong></td>
            <td width="60"></td>
            <td style="text-align: center; font-size:18px; border-top:1px solid;width: 120px;"><strong>Technical Manager</strong></td>
            <td width="60"></td>
            <td style="text-align: center; font-size:18px; border-top:1px solid;width: 100px;"><strong>Prepared By</strong></td>
            <td width="60"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;width: 100px;"><strong>Head of IE</strong></td>
		</tr>
	</table>

    <? 
			//There is no company in layout entry for this reason max company selected;
			// echo signature_table(110,"(select max(company_id) as company_id from variable_settings_signature where report_id=110)", "900px");
		?>
	<?

	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo $html;
	?>
		<script type="text/javascript">
			window.location.href = '<?php echo $filename;?>';
		</script>
	<?php

	exit();
}

  
if($action=="balancing_print8_bk") // 6/7/2023
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	$mstDataArray=sql_select("SELECT a.PROCESS_ID,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
    ob_start();
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="100%" border="0">
            <tr>
                <td align="center" colspan="9" style="font-size:24px"><strong><u>Operation Bulletin Report</u></strong></td>
            </tr>
            <tr>
                <td width="110"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="190"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="170"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="110"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Working Hour</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Allocated MP</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Extention No</strong></td>
                <td><strong>:</strong></td>
                <td colspan="7"><? echo $mstDataArray[0][csf('extention_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Efficiency</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('efficiency')]; ?></td>
                <td><strong>Pitch Time</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Target</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            
            
            <tr>
                <td><strong>SAM</strong></td>
                <td><strong>:</strong></td>
                <td><? echo number_format($mstDataArray[0][csf('total_smv')],2); ?></td>
                <td><strong>Insert Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('insert_date')]; ?></td>
                <td><strong>Bulletin Type</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
            </tr>
            
            
            <tr>
                <td><strong>Insert By</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                <td><strong>Update Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('update_date')]; ?></td>
                <td><strong>System ID</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('system_no')]; ?></td>        
            </tr>
            
            <tr>
                <td><strong>Update By</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                <td><strong>Product Des.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="7"><? echo $mstDataArray[0][csf('prod_description')]; ?></td>
            </tr>
            
            <tr>
                <td><strong>Fabric Type</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Remarks</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="7"><? echo $mstDataArray[0][csf('remarks')]; ?></td>
            </tr>
            
            
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <!-- <th width="50">Seq. No</th> -->
                <th width="100">Category</th>
                <th width="25">SL</th>
                <th width="240">Operation</th>
                <th width="100">Machine / Non-Machine</th>
                <th width="40">SAM</th>
                <th width="60">Target (100%)</th>
                <th width="60">Cycle Time(s)</th>
                <th width="50">Theo. MP</th>
                <th width="50">Lay. MP</th>
                <th width="50">Weight</th>
                <th width="90">Balancing Target</th>
                <th width="90">Attachment</th>
                <th width="95">Operation Remarks</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData=sql_select("SELECT gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
                $operation_arr=return_library_array( "SELECT id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
                $attach_id=return_library_array( "SELECT id,attachment_name from lib_attachment",'id','attachment_name');
                
                $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 group by id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc order by body_part_id,row_sequence_no";
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0; $sewSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                $s=1;
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $chk_arr=array();
                foreach($data_array_dtls as $slectResult)
                {

                    $count_body_part[$slectResult[csf('body_part_id')]]++;
                }
                 
                foreach($data_array_dtls as $slectResult)
                {
                    
                    if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                        $cycleTime=$slectResult[csf('total_smv')]*60;
                        $perc=$slectResult[csf('target_on_full_perc')];
                    }
                    $attachment=$slectResult[csf('attachment_id')];
                    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    $bgcolor="#FFFFFF";
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                        $bgcolor="#90EE90";
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==69)
                    {
                        $sewSmv=$sewSmv+$smv;
                        $sewMp= $sewMp+$layOut;
                        $bgcolor="#90EE90";
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                        $bgcolor="#90EE90";
                    }
					else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
					else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    $weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
                    $uclsArr[]=$ucl;
                    $lclsArr[]=$lcl;
					
					$tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];

 
    
                    $rowspan= $count_body_part[$slectResult[csf('body_part_id')]];
					
                ?>
                    <tr>
                        <!-- <td align="center"><? //echo $slectResult[csf('row_sequence_no')]; ?></td> -->
                        <?
                        if(!in_array($slectResult[csf('body_part_id')],$chk_arr)){
                            $chk_arr[]=$slectResult[csf('body_part_id')];
                            ?>
                                 <td rowspan="<? echo $rowspan;?>"><? echo $body_part[$slectResult[csf('body_part_id')]]; ?></td>
                            <?
                        }
                        ?>
                       <td><?=$s++;?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;" align="center"><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>

                        <td align="right"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="center" style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $perc; ?></td>
                        <td align="center"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo number_format($cycleTime,2,'.',''); ?></td>
                        <td align="right"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        <td align="right"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?></td>
                        <td align="center"  style="background-color:<? echo $bgcolor ;?>;print-color-adjust:exact;"><? echo $balanceDataArray[$slectResult[csf('id')]]['weight']; ?></td>
                        <td align="center"><?echo $perc*$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];?></td>
                        <td align="center"><? echo $attach_id[$attachment]; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                $seqNos= json_encode($seqNosArr);
                $weights= json_encode($weightsArr); 
                $pitchTimes= json_encode($pitchTimesArr); 
                $ucls= json_encode($uclsArr); 
                $lcls= json_encode($lclsArr);
				
				if(strpos($tot_mp,".")!="")
				{
					$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
			<tfoot>
				
				<th align="right" colspan="4">Total</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_th_mp,2,'.',''); ?></th>
				<th align="right"><? echo $tot_mp; ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="20%" valign="top">
                	<b>SAM Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">Sewing Helper</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Iron</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($sewSmv,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv,2,'.',''); ?></td>
                        </tr>                  
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv+$sewSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="20%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp+$sewMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}

                        if(strpos($sewMp,".")!="")
						{
							$sewMp=number_format($sewMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						if(strpos($imMp,".")!="")
						{
							$imMp=number_format($imMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="115">Sewing Helper</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Iron</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $sewMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp,2,'.',','); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
                        </tr>
                        
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="20%" valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="150"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td  valign="top">
                	
                    <table border="0" rules="all" class="rpt_table" width="100%">
                    <td align="center">
					<? 
                        $image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
                        
						if(count($image_location_arr)==1){$h=240;}
						elseif(count($image_location_arr)==2){$h=130;}
						elseif(count($image_location_arr)==3){$h=100;}
						else{$h=80;}
						
						foreach($image_location_arr as $image_path){
                            echo '<img src="../../../'.$image_path.'" height="'.$h.'" style="margin:3px;border:1px solid #BBB; border-radius:3px;" /> ';
                        }
                    ?>
                    </td>
                    </table>
                </td>
            </tr>
        </table>
        <div style="width:100%; margin-top:10px; height:220px; border:solid 1px" align="center">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="50">LCL</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Weight</td>
                </tr>
            </table>
           <canvas id="canvas" height="200" width="890"></canvas>
        </div>
        
        
    </div>
    <script>
		var lineChartData = {
            labels : <? echo $seqNos; ?>,
            datasets : [
				{
					//label: "My First dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $weights; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $ucls; ?>
				}
				,
				{
					//label: "My Third dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#4A7EBB",
					pointColor : "#4A7EBB",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#4A7EBB",
					data : <? echo $pitchTimes; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $lcls; ?>
				}
			]
        }
		
		window.onload = function()
		{
			var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                responsive : true
        	}); 
        }
	</script>
    <br>
    <br>
    <br>
    <br>
    <br>
    <table id="signatureTblId" width="901.5" style="padding-top:70px;">
 
		<tr>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Line IE</strong></td>
            <td width="75"></td>
            <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Technical Manager</strong></td>
            <td width="75"></td>
            <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Prepared By</strong></td>
            <td width="75"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>IE Manager</strong></td>
		</tr>
	</table>

    <? 
			//There is no company in layout entry for this reason max company selected;
			// echo signature_table(110,"(select max(company_id) as company_id from variable_settings_signature where report_id=110)", "900px");
		?>
	<?

	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo $html;
	?>
		<script type="text/javascript">
			//window.location.href = '<?php echo $filename;?>';
		</script>
	<?php

	exit();
}

if($action=="balancing_print2")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	
	$mstDataArray=sql_select("select a.PROCESS_ID,a.id, a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.bulletin_type,a.applicable_period,a.internal_ref,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
	
	//print_r($mstDataArray);
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="870" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Operation Balancing Sheet</u></strong></td>
            </tr>
            <tr>
                <td width="130"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="120"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="130"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Working Hour</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Allocated MP</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Efficiency</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('efficiency')]; ?></td>
                <td><strong>Pitch Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Target</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            <tr>
                <td><strong>Insert By</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                <td><strong>Insert Date Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="4" valign="top"><? echo $mstDataArray[0][csf('insert_date')]; ?></td>
                
            </tr>
            <tr>
                <td><strong>Update By</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                <td><strong>Update Date Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="4" valign="top"><? echo $mstDataArray[0][csf('update_date')]; ?></td>
            </tr>
            <tr>
                <td><strong>Bulletin Type</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                
                
                <td><strong>Internal Ref</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('internal_ref')];?></td>
                
                
            </tr>
            
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th width="60">Seq. No</th>
                <th width="290">Operation</th>
                <th width="100">Resource</th>
                <th width="60">SMV</th>
                <th width="70">Target (100%)</th>
                <th width="70">Efficiency</th>
                <th width="70">Cycle Time(s)</th>
                <th width="80">Theoretical MP</th>
                <th width="70">Layout MP</th>
                <th width="70">W. Load %</th>
                <th width="70">Weight</th>
                <th>W. Track</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
                $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );

				 $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $bodyPartArr=array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    /* if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                        $cycleTime=$slectResult[csf('total_smv')]*60;
                        $perc=$slectResult[csf('target_on_full_perc')];
                    } */
                    $smv=$slectResult[csf('total_smv')];
                    $cycleTime=$slectResult[csf('total_smv')]*60;
                    $perc=$slectResult[csf('target_on_full_perc')];
                    
                    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                    }
					else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
					else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    $weight=fn_number_format(($smv*1)/($layOut*1),2);
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    //$weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $weightsArr[]=number_format($weight,2,'.','');
                    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
                    $uclsArr[]=$ucl;
                    $lclsArr[]=$lcl;
					
					$tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
					
					/*if(!in_array($slectResult[csf('body_part_id')],$bodyPartArr))
					{
						echo '<tr><td colspan="13"><b>'.$body_part[$slectResult[csf('body_part_id')]].'</b></td></tr>';
						$bodyPartArr[]=$slectResult[csf('body_part_id')];
					}*/
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center"><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
                        <td align="right"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="center"><? echo $perc; ?></td>
                        <td align="center"><? echo number_format($slectResult[csf('target_on_effi_perc')],2,'.',''); ?></td>
                        <td align="center"><? echo number_format($cycleTime,2,'.',''); ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['work_load']; ?></td>
                        <td align="center"><? echo $weight; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                $seqNos= json_encode($seqNosArr);
                $weights= json_encode($weightsArr); 
                $pitchTimes= json_encode($pitchTimesArr); 
                $ucls= json_encode($uclsArr); 
                $lcls= json_encode($lclsArr);
				
				if(strpos($tot_mp,".")!="")
				{
					$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
                <th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_th_mp,2,'.',''); ?></th>
				<th align="right"><? echo $tot_mp; ?></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="32%" valign="top">
                	<b>SMV Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($imSmv,2,'.',''); ?></td>
                        </tr>
                        
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="32%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
                        </tr>
                        
                        
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="170"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style="width:900px; margin-top:10px; height:220px; border:solid 1px" align="center">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="50">LCL</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Weight</td>
                </tr>
            </table>
           <canvas id="canvas" height="200" width="890"></canvas>
        </div>
        
        <? 
			$image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
			foreach($image_location_arr as $image_path){
				echo '<img src="../../../'.$image_path.'" height="100" style="margin:3px 3px 3px 0;" />';
			}
		?>
        
    </div>
    <script>
		var lineChartData = {
            labels : <? echo $seqNos; ?>,
            datasets : [
				{
					//label: "My First dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $weights; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $ucls; ?>
				}
				,
				{
					//label: "My Third dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#4A7EBB",
					pointColor : "#4A7EBB",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#4A7EBB",
					data : <? echo $pitchTimes; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $lcls; ?>
				}
			]
        }
		
		window.onload = function()
		{
			var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                responsive : true
        	}); 
        }
	</script>
	<?
	exit();
}


if($action=="balancing_print6")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	$show_item=$data[2];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	
	$mstDataArray=sql_select("select a.PROCESS_ID,a.id, a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.bulletin_type,a.applicable_period,a.internal_ref,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
	//print_r($mstDataArray);
	/*if($show_item==1)
	{
		$width=1150;
		$colspan=5;
	}
	else
	{
		$width=1150;
		$colspan=5;
		$colspan2=2;
	}*/
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="870" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Operation Balancing Sheet</u></strong></td>
            </tr>
            <tr>
                <td width="130"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="120"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="130"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Working Hour</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Allocated MP</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Efficiency</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('efficiency')]; ?></td>
                <td><strong>Pitch Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Target</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            <tr>
                <td><strong>Insert By</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                <td><strong>Insert Date Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="4" valign="top"><? echo $mstDataArray[0][csf('insert_date')]; ?></td>
                
            </tr>
            <tr>
                <td><strong>Update By</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                <td><strong>Update Date Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="4" valign="top"><? echo $mstDataArray[0][csf('update_date')]; ?></td>
            </tr>
            <tr>
                <td><strong>Bulletin Type</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                
                
                <td><strong>Internal Ref</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('internal_ref')];?></td>
                
                
            </tr>
            
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th width="60">Seq. No</th>
                <th width="290">Operation</th>
                <th width="100">Resource</th>
                <th width="60">SMV</th>
                <th width="70">Target (100%)</th>
                <th width="70">Target [Eff.]</th>
                <th width="70">Cycle Time(s)</th>
                <th width="80">Theoretical MP</th>
                <th width="70">Layout MP</th>
                <?
                if($show_item==1)
				{
                ?>
                <th width="70">W. Load %</th>
                <th width="70">Weight</th>
                <?
            	}
                ?>
                <th>W. Track</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
                $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );

				 $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $bodyPartArr=array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                        $cycleTime=$slectResult[csf('total_smv')]*60;
                        $perc=$slectResult[csf('target_on_full_perc')];
                    }
                    
                    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                    }
					else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
					else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    $weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
                    $uclsArr[]=$ucl;
                    $lclsArr[]=$lcl;
					
					$tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
					
					/*if(!in_array($slectResult[csf('body_part_id')],$bodyPartArr))
					{
						echo '<tr><td colspan="13"><b>'.$body_part[$slectResult[csf('body_part_id')]].'</b></td></tr>';
						$bodyPartArr[]=$slectResult[csf('body_part_id')];
					}*/
					$layout_mp=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center"><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
                        <td align="right"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="center"><? echo $perc*$layout_mp; ?></td>
                        <td align="center"><? echo number_format($slectResult[csf('target_on_effi_perc')]*$layout_mp,2,'.',''); ?></td>
                        <td align="center"><? echo number_format($cycleTime,2,'.',''); ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?></td>
                        <?
		                if($show_item==1)
						{
		                ?>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['work_load']; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['weight']; ?></td>
                        <?
                    	}
                        ?>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                $seqNos= json_encode($seqNosArr);
                $weights= json_encode($weightsArr); 
                $pitchTimes= json_encode($pitchTimesArr); 
                $ucls= json_encode($uclsArr); 
                $lcls= json_encode($lclsArr);
				
				if(strpos($tot_mp,".")!="")
				{
					$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
                <th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_th_mp,2,'.',''); ?></th>
				<th align="right"><? echo $tot_mp; ?></th>
				<?
                if($show_item==1)
				{
                ?>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<?
				}
				?>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="32%" valign="top">
                	<b>SMV Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($imSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="32%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="170"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style="width:900px; margin-top:10px; height:220px; border:solid 1px" align="center">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="50">LCL</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Weight</td>
                </tr>
            </table>
           <canvas id="canvas" height="200" width="890"></canvas>
        </div>
        
        <? 
			$image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
			foreach($image_location_arr as $image_path){
				echo '<img src="../../../'.$image_path.'" height="100" style="margin:3px 3px 3px 0;" />';
			}
		?>
        
    </div>
    <script>
		var lineChartData = {
            labels : <? echo $seqNos; ?>,
            datasets : [
				{
					//label: "My First dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $weights; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $ucls; ?>
				}
				,
				{
					//label: "My Third dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#4A7EBB",
					pointColor : "#4A7EBB",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#4A7EBB",
					data : <? echo $pitchTimes; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $lcls; ?>
				}
			]
        }
		
		window.onload = function()
		{
			var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                responsive : true
        	}); 
        }
	</script>
	<?
	exit();
}


if($action=="balancing_print3")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$lib_company=return_library_array( "select id, COMPANY_NAME from LIB_COMPANY", "id", "COMPANY_NAME"  );
	
	
	
	$mstDataArray=sql_select("select a.PROCESS_ID,a.PO_JOB_NO,a.id, a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.bulletin_type,a.applicable_period,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency,a.REMARKS from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );

	
	$jobWiseCompanyArr=return_library_array("select JOB_NO,COMPANY_NAME from WO_PO_DETAILS_MASTER where JOB_NO = '".$mstDataArray[0]['PO_JOB_NO']."'", "JOB_NO", "COMPANY_NAME"  );
	
	
	?>

    <div style="width:990px">
        <?php ob_start(); ?>
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th width="60">Seq. No</th>
                <th width="240">Operation</th>
                <th width="100">Machine Type/Manual</th>
                <th width="60" title="SMV">SAM</th>
                <th width="70" title="Cycle Time(s)">SAS</th>
                <th width="70" title="Target (100%)">Stnadard TGT/Hr</th>
                <th width="80" title="Theoretical MP">Manpower Required (TML)</th>
                <th width="70" title="Layout MP">Manpower Allocated (AML)</th>
                <th width="70" title="Efficiency">Balanced Target PDN/Hr</th>
                <th width="70">W. Load %</th>
                <th title="W. Track">Note</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
                $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );

				 $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $bodyPartArr=array();
				$target_on_effi_perc_arr=array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    /* if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                        $cycleTime=$slectResult[csf('total_smv')]*60;
                        $perc=$slectResult[csf('target_on_full_perc')];
                    } */
                    $smv=$slectResult[csf('total_smv')];
                    $cycleTime=$slectResult[csf('total_smv')]*60;
                    $perc=$slectResult[csf('target_on_full_perc')];
                    
                    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    $manual_operation=array(40,41,43,44,48,68,53,54,55,56,70);
					if(in_array($rescId,$manual_operation)){
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
						$totalHelper+=$layOut;
                    }
					else if($rescId==69)
                    {
                        $helperSmv=$helperSmv+$smv;
						
					    $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
						$totalIronMan+=$layOut;
                    }

                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    //$weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $weight=fn_number_format(($smv*1)/($layOut*1),2);
                    $weightsArr[]=number_format($weight,2,'.','');
                    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
                    //$uclsArr[]=$ucl;
                    //$lclsArr[]=$lcl;
					
					$tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
					$target_on_effi_perc_arr[]=$slectResult[csf('target_on_effi_perc')];
					
					//.'='.$slectResult[csf('resource_gsd')]
					
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center"><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
                        <td align="center"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="center"><? echo number_format($cycleTime,2,'.',''); ?></td>
                        <td align="center"><? echo $perc; ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?></td>
                        <td align="center"><? echo number_format($slectResult[csf('target_on_effi_perc')],2,'.',''); ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['work_load']; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                /*  $seqNos= json_encode($seqNosArr);
                $weights= json_encode($weightsArr); 
                $pitchTimes= json_encode($pitchTimesArr); 
                $ucls= json_encode($uclsArr); 
                $lcls= json_encode($lclsArr);*/				
				if(strpos($tot_mp,".")!="")
				{
					$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_th_mp,2,'.',''); ?></th>
				<th align="right"><? echo $tot_mp; ?></th>
				<th>&nbsp;</th>
                <th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        
		<?
			$part2 = ob_get_contents();
			ob_end_clean();
			ob_start();
        ?>
        
        <table width="870" border="1" rules="all">
            <tr>
                <td colspan="3"><strong style="font-size:22px;">Operation Bulletin</strong></td>
                <td colspan="3" align="right"><strong>Updated On:</strong></td>
                <td colspan="2"><? echo $mstDataArray[0][csf('update_date')]; ?></td>
            </tr>
            <tr>
                <td colspan="3"><strong style="font-size:22px;">
                	<?= ($jobWiseCompanyArr[$mstDataArray[0][PO_JOB_NO]])?$lib_company[$jobWiseCompanyArr[$mstDataArray[0][PO_JOB_NO]]]:implode(',',$lib_company);?> <!--Meghna Denims Ltd.-->
               </strong></td>
                <td colspan="3" align="right"><strong><!--Revised No:--></strong></td>
                <td colspan="2"></td>
            </tr>
            
            <tr>
                <td colspan="2"><strong style="color:#609;">Style Info</strong></td>
                <td colspan="2"><strong style="color:#609;">SMV Details</strong></td>
                <td colspan="2"><strong style="color:#609;">Manpower Profile</strong></td>
                <td colspan="2"><strong style="color:#609;">Target & Eff. Profile</strong></td>
            </tr>
            
            <tr>
                <td><strong>Buyer</strong></td>
                <td><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td><strong>Total SMV</strong></td>
                <td><? echo number_format($tot_smv,2,'.',''); ?></td>
                <td><strong>Operator</strong></td>
                <td><?= $tot_mp-($totalHelper+$totalIronMan); ?></td>
                <td><strong>Target/Hr @100% Eff.</strong></td>
                <td><?= round(($tot_mp*60)/$tot_smv); ?></td>
            </tr>
            <tr>
                <td><strong>Style</strong></td>
                <td><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td><strong>Machine SMV</strong></td>
                <td><?= $machineSmv; ?></td>
                <td><strong>Helper</strong></td>
                <td><?= $totalHelper; ?></td>
                <td><strong>Target Efficiency</strong></td>
                <td><?= number_format(($tot_smv*min($target_on_effi_perc_arr))/$tot_mp/60,2); ?></td>
            </tr>
            
            <tr>
                <td><strong>Item</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
                <td><strong>Manual SMV</strong></td>
                <td><?= $helperSmv; ?></td>
                <td><strong>Iron Man</strong></td>
                <td><?= $totalIronMan; ?></td>
                <td><strong>Expected Target (Pcs)/Hr</strong></td>
                <td><?= min($target_on_effi_perc_arr); ?> Pcs</td>
            </tr>
            <tr>
                <td><strong>Quantity</strong></td>
                <td><? echo $mstDataArray[0][csf('REMARKS')];// Qty show from remarks. Client Req. ?></td>
                <td><strong>TAKT Time</strong></td>
                <td><?= number_format($tot_smv/$tot_mp,2); ?></td>
                <td><strong>TTL MP</strong></td>
                <td><?= $tot_mp; ?></td>
                <td><strong>Balance Loss</strong></td>
                <td><?= number_format(100-($tot_th_mp/$tot_mp),2); ?></td>
            </tr>
            <tr>
                <td><strong>Line</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>Labor Productivity/Man/Hr	</strong></td>
                <td><?= number_format(min($target_on_effi_perc_arr)/$tot_mp,2); ?></td>
            </tr>
			</table>        

		<?
			$part1 = ob_get_contents();
			ob_end_clean();
			
			echo $part1;
			echo "<br>";
			echo $part2;
			
        ?>
        


        
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="60%" valign="top">
                <b>Profile Picture</b><br />
                	
					<? 
                        $image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
                        foreach($image_location_arr as $image_path){
                            echo '<img src="../../../'.$image_path.'" height="100" style="margin:3px 3px 3px 0;" />';
                        }
                    ?>                    
                    
                </td>

                <td></td>
                <td valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="170"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        
      
        
    </div>
    
	<?
	exit();
}



if($action=="balancing_print4")
{

	list($job_no,$item_id,$gsd_id,$bl_update_id)=explode("***",$data);
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$operation_resource_img_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME = 'operation_resource' and FILE_TYPE = 1 and IS_DELETED = 0", "MASTER_TBLE_ID", "IMAGE_LOCATION"  );
	
	$sql="select 
	
	listagg(cast(d.FILE_NO as varchar2(4000)),',') within group (order by d.FILE_NO) as FILE_NO,
	listagg(cast(d.GROUPING as varchar2(4000)),',') within group (order by d.GROUPING) as GROUPING,
	a.PROCESS_ID,a.BULLETIN_TYPE,a.COLOR_TYPE,a.TOTAL_SMV,a.fabric_type,a.extention_no,a.id,a.approved, a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.bulletin_type,a.applicable_period,a.internal_ref,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency,sum(d.PO_QUANTITY*c.TOTAL_SET_QNTY) as PO_QUANTITY from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b,WO_PO_DETAILS_MASTER c, WO_PO_BREAK_DOWN d where a.id=b.gsd_mst_id and c.id=d.job_id and c.job_no='$job_no' and c.STYLE_REF_NO=a.style_ref and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	
     group by a.id, a.PROCESS_ID,a.BULLETIN_TYPE,a.COLOR_TYPE,a.TOTAL_SMV,a.fabric_type,a.buyer_id,a.extention_no, a.style_ref, a.gmts_item_id,a.approved, a.working_hour,a.bulletin_type, a.applicable_period,a.internal_ref,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency	
	";
	$mstDataArray=sql_select($sql);
	
	$imgSql="select IMAGE_LOCATION from COMMON_PHOTO_LIBRARY  where FORM_NAME='gsd_entry' and MASTER_TBLE_ID='$gsd_id'";
	$imgSqlResult=sql_select($imgSql);

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );

	// print_r($production_resource_arr);
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="870" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Operation Balancing Sheet</u></strong></td>
            </tr>
            <tr>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="110"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="150"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td width="110"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>File No</strong></td>
                <td><strong>:</strong></td>
                <td><?= implode(',',array_unique(explode(',',$mstDataArray[0][FILE_NO]))); ?></td>
                
                <td><strong>Ref No</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('GROUPING')]; ?></td>
                
                <td><strong>Order Qty.</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][PO_QUANTITY]; ?></td>
            </tr>
            <tr>
                <td><strong>Efficiency</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('efficiency')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
                <td><strong>Target</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            <tr>
                <td><strong>SMV</strong></td>
                <td><strong>:</strong></td>
                <td><?=$mstDataArray[0][TOTAL_SMV]; ?></td>
                <td><strong>Color Type</strong></td>
                <td><strong>:</strong></td>
                <td><?=$color_type[$mstDataArray[0][COLOR_TYPE]]; ?></td>
                <td><strong>Bulletin Type</strong></td>
                <td><strong>:</strong></td>
                <td><?=$bulletin_type_arr[$mstDataArray[0][BULLETIN_TYPE]]; ?></td>
            </tr>
            
            <tr>
                <td><strong>Fabric Type</strong></td>
                <td><strong>:</strong></td>
                <td><?=$mstDataArray[0][csf('fabric_type')]; ?></td>
                <td><strong>Pitch Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>System ID</strong></td>
                <td><strong>:</strong></td>
                <td><?=$gsd_id; ?></td>
            </tr>
            <tr>
                <td><strong>Applicable Period</strong></td>
                <td><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                <td><strong>Bulletin Status</strong></td>
                <td><strong>:</strong></td>
                <td><? echo ($mstDataArray[0][csf('approved')]==1)?'Approved':''; ?></td>
                <td><strong>Extention</strong></td>
                <td><strong>:</strong></td>
                <td><? echo ($mstDataArray[0][csf('extention_no')])?'Amendment-'.$mstDataArray[0][csf('extention_no')]:''; ?></td>
            </tr>
        </table>
        <br />
        
        
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th width="35" align="center">SL</th>
                <th width="290">Operation</th>
                <th width="100">Resource</th>
                <th width="70">Cycle Time(s)</th>
                <th width="60">SMV</th>
                <th width="80">Theoretical MP</th>
                <th width="70">Layout Operators</th>
                <th width="70">Associate Operators</th>
                <th width="70">Helpers</th>
                <th width="70">Target/ Operation </th>
                <th width="70">Process Target </th>
                <th width="80">Share </th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
					    $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
					   
					   
					   
					   
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
				
				
				
				
                $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
              

				 $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0;  $shMp=0;$mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $bodyPartArr=array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
						$smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $cycleTime=$slectResult[csf('total_smv')]*60;
						$smv=$slectResult[csf('total_smv')];
                        $perc=$slectResult[csf('target_on_full_perc')];
                    }
                    
                   
				   
				    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                    }
					else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
					else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
					else if($rescId==147)
                    {
                        $shSmv=$shSmv+$smv;
                        $shMp=$shMp+$layOut;
                    }					
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }					
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    $weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
                    $uclsArr[]=$ucl;
                    $lclsArr[]=$lcl;
					
					$tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
					
					
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center" title="<?= $production_resource_arr[$slectResult[csf('resource_gsd')]];?>">
						<? //echo $production_resource[$slectResult[csf('resource_gsd')]]; ?>
                        <?
							$manualArr=array(41,43,44,48,68,69,53,54,55,56,70,90,129,147,176);
							// if($slectResult[csf('resource_gsd')]==40){
							// 	echo ($slectResult[csf('resource_gsd')]==40)?'<img src="../../../home_css/logo/man.gif" height="30" />':$production_resource_arr[$slectResult[csf('resource_gsd')]];
							// }
							// else{
							// 	echo (in_array($slectResult[csf('resource_gsd')],$manualArr))?'<img src="../../../home_css/logo/man2.gif" height="30" />':$production_resource_arr[$slectResult[csf('resource_gsd')]];
							// }
                           echo '<img src="../../../'.$operation_resource_img_arr[$slectResult[csf('resource_gsd')]].'" height="30" />';
                           echo "<br><small style='font-size:10px;'>".$production_resource_arr[$slectResult[csf('resource_gsd')]]."</small>";

                       

						
							$layout_H=$layout_AH=$layout_O='';$multyplayMP=0;
							if (in_array($slectResult[csf('resource_gsd')],$manualArr)){
								$layout_H=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
								$multyplayMP=$layout_H;
							}
							else if ($slectResult[csf('resource_gsd')]==40){
								$layout_AH=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
								$multyplayMP=$layout_AH;
							}
							else{
								$layout_O=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
								$multyplayMP=$layout_O;
							}
							
							$totalData[cycle_time]+=$cycleTime;
							$totalData[smv]+=$smv;
							$totalData[theroMP]+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
							$totalData[layout_O]+=$layout_O;
							$totalData[layout_AH]+=$layout_AH;
							$totalData[layout_H]+=$layout_H;
						
						?>
                        </td>
                        <td align="right"><? echo number_format($cycleTime,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        
                        <td align="center"><? echo $layout_O; ?></td>
                        
                        
                        <td align="center"><? echo $layout_AH; ?></td>
                        <td align="center"><? echo $layout_H; ?></td>
                        <td align="center"><? echo $perc; ?></td>
                        
                        
                        <td align="center"><? echo number_format(($multyplayMP*$perc)); ?></td>
                        
                        
                        
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                $seqNos= json_encode($seqNosArr);
                $weights= json_encode($weightsArr); 
                $pitchTimes= json_encode($pitchTimesArr); 
                $ucls= json_encode($uclsArr); 
                $lcls= json_encode($lclsArr);
				
				if(strpos($tot_mp,".")!="")
				{
					$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
            	<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right"><?=$totalData[cycle_time]; ?></th>
				<th align="right"><?=number_format($totalData[smv],2); ?></th>
				<th align="right"><?=number_format($totalData[theroMP],2); ?></th>
                <th><?=$totalData[layout_O]; ?></th>
                <th><?=$totalData[layout_AH]; ?></th>
                <th><?=$totalData[layout_H]; ?></th>
				<th align="right"></th>
				<th>&nbsp;</th>
			</tfoot>

		</table>
        
        
        
        
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="24%" valign="top" align="center">
                	<b>SMV Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv,2,'.',''); ?></td>
                        </tr>
                        
                         <tr bgcolor="#E9F3FF">
                        	<td>Helper</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($shSmv,2,'.',''); ?></td>
                        </tr>                       
                        
                         <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($imSmv,2,'.',''); ?></td>
                        </tr>                       
                        
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$shSmv+$imSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="24%" valign="top" align="center">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$shMp+$imMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						
						if(strpos($imMp,".")!="")
						{
							$imMp=number_format($imMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        
                         <tr bgcolor="#E9F3FF">
                        	<td>Helper</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $shMp; ?></td>
                        </tr>                       

                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
                        </tr>
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td valign="top" width="24%" align="center">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="170"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
                
                <td width="1%" valign="top"></td>
                <td valign="top" align="center">
                	<b>Image</b>
                    <table width="100%">
                    	<tr><td>
                        <? foreach($imgSqlResult as $rows){
							$width=(100/count($imgSqlResult));
							echo '<img src="../../../'.$rows[IMAGE_LOCATION].'" width="'.$width.'%" />';
						}
                        
                        ?>
                        
                        </td></tr>
                    </table>
                	
                </td>
                
            </tr>
        </table>
        <div style="width:900px; margin-top:10px; height:220px; border:solid 1px" align="center">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="50">LCL</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Weight</td>
                </tr>
            </table>
           <canvas id="canvas" height="200" width="890"></canvas>
        </div>
        
    </div>
    <script>
		var lineChartData = {
            labels : <? echo $seqNos; ?>,
            datasets : [
				{
					//label: "My First dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $weights; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $ucls; ?>
				}
				,
				{
					//label: "My Third dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#4A7EBB",
					pointColor : "#4A7EBB",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#4A7EBB",
					data : <? echo $pitchTimes; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $lcls; ?>
				}
			]
        }
		
		window.onload = function()
		{
			var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                responsive : true
        	}); 
        }
	</script>
	<?
	exit();

}


if($action=="balancing_print5")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	
	$mstDataArray=sql_select("select a.PROCESS_ID,a.id, a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.bulletin_type,a.applicable_period,a.internal_ref,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
	
	//print_r($mstDataArray);
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="990" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Operation Balancing Sheet</u></strong></td>
                <td rowspan="7" valign="top">
                    <table border="1" rules="all">
                    	<tbody bgcolor="#CCCCCC"><th>Effi.%</th><th>Target</th></tbody>
                    	<?
						$effiPerArr=array(60,65,70,75,80,85,90);
						$target =($mstDataArray[0][csf('target')]/$mstDataArray[0][csf('efficiency')])*100;
						foreach($effiPerArr as $effiPar){
							echo '<tr style="font-size:12px;"><td>'.$effiPar.'%</td><td align="right">'.decimal_format((($target/100)*$effiPar),7).'</td></tr>';	
						}
						?>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="130"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="120"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="130"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Working Hour</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Allocated MP</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Efficiency</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('efficiency')]; ?></td>
                <td><strong>Pitch Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Target</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            <tr>
                <td><strong>Insert By</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                <td><strong>Insert Date Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="4" valign="top"><? echo $mstDataArray[0][csf('insert_date')]; ?></td>
            </tr>
            <tr>
                <td><strong>Update By</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                <td><strong>Update Date Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="4" valign="top"><? echo $mstDataArray[0][csf('update_date')]; ?></td>
            </tr>
            <tr>
                <td><strong>Bulletin Type</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                <td><strong>Internal Ref</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('internal_ref')];?></td>
            </tr>
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th width="60">Seq. No</th>
                <th width="290">Operation</th>
                <th>Operator</th>
                <th>Asst. Operator</th>
                <th width="100">Resource</th>
                <th width="60">SMV</th>
                <th width="70">Target (100%)</th>
                <th width="70">Effi- ciency</th>
                <th width="70">Cycle Time(s)</th>
                <th width="80">Theor- etical MP</th>
                <th width="70">Layout MP</th>
                <th width="70">W. Load %</th>
                <th width="70">Weight</th>
                <th>W. Track</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($bl_update_id>0)
                {
                    $blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
                $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );

				 $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $bodyPartArr=array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                        $cycleTime=$slectResult[csf('total_smv')]*60;
                        $perc=$slectResult[csf('target_on_full_perc')];
                    }
                    
                    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                    }
					else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
					else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    $weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
                    $uclsArr[]=$ucl;
                    $lclsArr[]=$lcl;
					$tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td></td>
                        <td></td>
                        <td align="center"><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
                        <td align="right"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="center"><? echo $perc; ?></td>
                        <td align="center"><? echo number_format($slectResult[csf('target_on_effi_perc')],2,'.',''); ?></td>
                        <td align="center"><? echo number_format($cycleTime,2,'.',''); ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        <td align="right"><? echo $balanceDataArray[$slectResult[csf('id')]]['layout_mp']; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['work_load']; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['weight']; ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                $seqNos= json_encode($seqNosArr);
                $weights= json_encode($weightsArr); 
                $pitchTimes= json_encode($pitchTimesArr); 
                $ucls= json_encode($uclsArr); 
                $lcls= json_encode($lclsArr);
				
				if(strpos($tot_mp,".")!="")
				{
					$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
                <th>&nbsp;</th>
				<th align="right"><? echo number_format($tot_th_mp,2,'.',''); ?></th>
				<th align="right"><? echo $tot_mp; ?></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="32%" valign="top">
                	<b>SMV Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($imSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv+$imSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="32%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp+$imMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}
						
						if(strpos($sQiMp,".")!="")
						{
							$sQiMp=number_format($sQiMp,2,'.','');
						}
						
						if(strpos($totatMp,".")!="")
						{
							$fImMp=number_format($fImMp,2,'.','');
						}
						
						if(strpos($fQiMp,".")!="")
						{
							$fQiMp=number_format($fQiMp,2,'.','');
						}
						
						if(strpos($polyHelperMp,".")!="")
						{
							$polyHelperMp=number_format($polyHelperMp,2,'.','');
						}
						
						if(strpos($pkMp,".")!="")
						{
							$pkMp=number_format($pkMp,2,'.','');
						}
						
						if(strpos($htMp,".")!="")
						{
							$htMp=number_format($htMp,2,'.','');
						}
						
						if(strpos($imMp,".")!="")
						{
							$imMp=number_format($imMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
                        </tr>
                        
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td width="170"><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right" style="padding-right:5px"><? echo $mp; ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style="width:900px; margin-top:10px; height:220px; border:solid 1px" align="center">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="50">LCL</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Weight</td>
                </tr>
            </table>
           <canvas id="canvas" height="200" width="890"></canvas>
        </div>
        
        <? 
			$image_location_arr=return_library_array( "select id,image_location from common_photo_library where master_tble_id='".$mstDataArray[0][csf('id')]."' and form_name='gsd_entry'", "id", "image_location"  );
			foreach($image_location_arr as $image_path){
				echo '<img src="../../../'.$image_path.'" height="100" style="margin:3px 3px 3px 0;" />';
			}
		?>
        
    </div>
    <script>
		var lineChartData = {
            labels : <? echo $seqNos; ?>,
            datasets : [
				{
					//label: "My First dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $weights; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $ucls; ?>
				}
				,
				{
					//label: "My Third dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#4A7EBB",
					pointColor : "#4A7EBB",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#4A7EBB",
					data : <? echo $pitchTimes; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $lcls; ?>
				}
			]
        }
		
		window.onload = function()
		{
			var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                responsive : true
        	}); 
        }
	</script>
	<?
	exit();
}

if($action=="balancing_print7")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	
	$mstDataArray=sql_select("select a.PROCESS_ID,a.id, a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.bulletin_type,a.applicable_period,a.internal_ref,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
	
	$balanceDataArray=array();
	if($bl_update_id>0)
	{
		$blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
		foreach($blData as $row)
		{
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
		}
	}

	
	
	$sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
	$data_array_dtls=sql_select($sqlDtls);
	foreach($data_array_dtls as $row){
		if($balanceDataArray[$row[csf('id')]]['smv']>0)	
		{
			$smv=$balanceDataArray[$row[csf('id')]]['smv'];
		}
		else
		{
			$smv=$row[csf('total_smv')];
		}
		
		
		 
		$rescId=$row[csf('resource_gsd')];
		
		if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 ||  $rescId==70 ||  $rescId==147 )
		{
			$helperSmv=$helperSmv+$smv;
		}
		else if($rescId==53)
		{
		}
		else if($rescId==54)
		{
		}
		else if($rescId==55)
		{
			
		}
		else if($rescId==56)
		{
			
		}
		else if($rescId==90)
		{
		}
		else if($rescId==176)
		{
			$imSmv=$imSmv+$smv;
		}
		else
		{
			$machineSmv=$machineSmv+$smv;
		}
	}
	

	
	//print_r($mstDataArray);
	?>
    <div style="width:990px">
        <table width="990" border="0">
            <tr>
                <td align="center" colspan="6"><strong><u>Operation Balancing </u></strong></td>
                <td rowspan="6" valign="top" align="right">
                    <table border="1" rules="all">
                    	<tbody bgcolor="#CCCCCC"><th>Effi.%</th><th title="(Allocated MP*Effi*60)/Total SMV ">Target</th></tbody>
                    	<?
						$effiPerArr=array(100,90,80,75,60,65,60);
						foreach($effiPerArr as $effiPar){
							//$target =($mstDataArray[0][csf('target')]/$mstDataArray[0][csf('efficiency')])*$effiPar;;
							$target =(($mstDataArray[0][csf('allocated_mp')]*60)/($helperSmv+$machineSmv+$imSmv)/100)*$effiPar;
							echo '<tr style="font-size:13px;"><td>'.$effiPar.'%</td><td align="right">'.decimal_format($target,7).'</td></tr>';	
						}
						?>
                    </table>
                </td>
            </tr>
            
            
            <tr>
                <td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="180"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="130"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Allocated MP</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Pitch Time</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Target</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('target')]; ?></td>
            </tr>
            <tr>
                <td><strong>Insert Date Time</strong></td>
                <td><strong>:</strong></td>
                <td valign="top"><? echo $mstDataArray[0][csf('insert_date')]; ?></td>
                <td><strong>Internal Ref</strong></td>
                <td><strong>:</strong></td>
                <td><?= $mstDataArray[0][csf('internal_ref')];?></td>
            </tr>
            
            <tr>
                <td><strong>Update Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('update_date')]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
            </tr>
            
            
            
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th width="60">Seq. No</th>
                <th width="290">Operation</th>
                <th width="100">Resource</th>
                <th width="60">SMV</th>
                <th width="70">Target/Hour</th>
                <th width="70">Target Required/Hour</th>
                <th width="70">Theoretical MP Calculation</th>
                <th width="80">No of Operator required</th>
                <th width="70">No of Assistant Op. Required</th>
                <th>W. Track</th>
            </thead>
            <?
                
                $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );

				 /*$sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
                $data_array_dtls=sql_select($sqlDtls);*/
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;$imSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $bodyPartArr=array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                    }
                    
                    
                   	$layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    $rescId=$slectResult[csf('resource_gsd')];
					
					if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
						$operatorReq=0;
						$assistantOpReq=$layOut;
                    }
                    else if($rescId==53)
                    {
						$operatorReq=$layOut;
						$assistantOpReq=0;
                    }
                    else if($rescId==54)
                    {
						$operatorReq=$layOut;
						$assistantOpReq=0;
                    }
                    else if($rescId==55)
                    {
						$operatorReq=$layOut;
						$assistantOpReq=0;
						
                    }
					else if($rescId==56)
                    {
						$operatorReq=$layOut;
						$assistantOpReq=0;
						
                    }
					else if($rescId==90)
                    {
						$operatorReq=$layOut;
						$assistantOpReq=0;
                    }
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
						$operatorReq=0;
						$assistantOpReq=$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                       	$mpSumm[$rescId]+= $layOut;
                       	$mpSmvSumm[$rescId]+= $smv;
					   
						$operatorReq=$layOut;
						$assistantOpReq=0;
					   
                    }
                    
					
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center" title="<?=$rescId;?>"><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
                        <td align="right"><? echo number_format($smv,2,'.',''); ?></td>
                        <td align="center"><? echo number_format(60/$smv,2); ?></td>
                        <td align="center"><? echo number_format($mstDataArray[0][csf('target')],2,'.',''); ?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['theoritical_mp']; ?></td>
                        <td align="right"><?=$operatorReq;?></td>
                        <td align="right"><?=$assistantOpReq;?></td>
                        <td align="center"><? echo $balanceDataArray[$slectResult[csf('id')]]['worker_tracking']; ?></td>
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    //$tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $totOperatorReq+=$operatorReq;
                    $totAssistantOpReq+=$assistantOpReq;
					
					$i++;
                }
                
				
				if(strpos($tot_mp,".")!="")
				{
					//$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right"><? echo number_format($tot_smv,2,'.',''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
                <th align="right"><?=$totOperatorReq;?></th>
				<th align="right"><?=$totAssistantOpReq;?></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <br />
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="32%" valign="top">
                	<b>SMV Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                        <tr bgcolor="#E9F3FF">
                        	<td width="150">Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Assistant Operator</td>
                            <td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv,2,'.',''); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="im" align="right" style="padding-right:5px"><? echo number_format($imSmv,2,'.',''); ?></td>
                        </tr>
                        
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$imSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="32%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$imMp;
						
						if(strpos($helperMp,".")!="")
						{
							$helperMp=number_format($helperMp,2,'.','');
						}
						
						if(strpos($machineMp,".")!="")
						{
							$machineMp=number_format($machineMp,2,'.','');
						}
						
						if(strpos($imMp,".")!="")
						{
							$imMp=number_format($imMp,2,'.','');
						}
						
						if(strpos($totMpSumm,".")!="")
						{
							$totMpSumm=number_format($totMpSumm,2,'.','');
						}
					?>
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                        <tr bgcolor="#E9F3FF">
                        	<td width="150">Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
                        </tr>
                    	<tr bgcolor="#FFFFFF">
                        	<td>Assistant Operator</td>
                            <td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="imm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
                        </tr>

                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
                        <tr bgcolor="#CCC">
                            <td>Resource</td>
                            <td width="80">M/C MAN</td>
                            <td width="80">M/C SMV</td>
                        </tr>
					
					<?
						$x=1; $totatMp=0;
                    	foreach($mpSumm as $key=>$mp)
						{
							if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
							
							if(strpos($mp,".")!="")
							{
								$mp=number_format($mp,2,'.','');
							}
						?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                            	<td><? echo $production_resource_arr[$key]; ?></td>
                                <td align="right"><? echo number_format($mp,2); ?></td>
                                <td align="right"><? echo number_format($mpSmvSumm[$key],2); ?></td>
                            </tr>
                        <?
							$totatMp+=$mp;
							$totatMCSmv+=$mpSmvSumm[$key];
							$x++;	
						}
						
						if($x%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						
						if(strpos($totatMp,".")!="")
						{
							$totatMp=number_format($totatMp,2,'.','');
						}
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>">
                        	<td align="right"><b>Total</b></td>
                            <td align="right"><? echo number_format($totatMp,2); ?></td>
                            <td align="right"><? echo number_format($totatMCSmv,2); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

       
        
    </div>
    
	<?
	exit();
}



if($action=='job_list_by_style_popup'){
	echo load_html_head_contents("Job","../../../", 1, 1, $unicode);
	?>
    	<script>
	  function js_set_value(data_string)
	  { 
		  document.getElementById('data_string').value=data_string;
		  parent.emailwindow.hide();
	  }
	</script>  


   <?
	extract($_REQUEST); 

	$sql="select a.ID,a.JOB_NO,a.STYLE_REF_NO,b.GMTS_ITEM_ID,c.id as GST_MST_ID,sum(d.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY,max(SHIPING_STATUS) as SHIPING_STATUS from WO_PO_DETAILS_MASTER a,WO_PO_DETAILS_MAS_SET_DETAILS b, ppl_gsd_entry_mst c,WO_PO_BREAK_DOWN d where a.id=b.job_id and b.job_id=d.job_id and c.style_ref=a.STYLE_REF_NO and b.gmts_item_id=c.gmts_item_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.STYLE_REF_NO='$txt_style_ref_bl' and c.id='$update_id' group by a.ID,a.JOB_NO,a.STYLE_REF_NO,b.GMTS_ITEM_ID,c.id";
	// echo $sql;
	$sqlResult=sql_select($sql);
	
	?>
    <input type="hidden" id="data_string" readonly="readonly">
    <table border="1" rules="all" class="rpt_table" width="100%">
        <thead>
            <th>SL</th>
            <th>JOB</th>
            <th>Ref No</th>
            <th>Item</th>
            <th>PO Qty</th>
            <th>Shipment Status</th>
        </thead>
        <tbody>
        <?
		$i=1; 
		
		foreach($sqlResult as $row){
			
			 $bgcolor=($i%2==0)?"#FFFFFF":"#E9F3FF";
			?>
            <tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" style="cursor:pointer" onClick="js_set_value('<?=$row['JOB_NO'].'***'.$row['GMTS_ITEM_ID'].'***'.$row['GST_MST_ID'];?>')" onDblClick="js_set_save_value(0)" class="tr_<?=$row['ID'];?>">
                <td align="center"><?=$i;?></td>
                <td title="WorkStudy:<?=$row['GST_MST_ID'];?>"><?=$row[JOB_NO];?></td>
                <td><?=$row[STYLE_REF_NO];?></td>
                <td><?=$garments_item[$row['GMTS_ITEM_ID']];?></td>
                <td><?=$row['PO_QUANTITY'];?></td>
                <td><?=($row['SHIPING_STATUS']==1)?"Full Shiped":"Pending";?></td>
            </tr>
            <? $i++;} ?>
        </tbody>
    </table>
    
    <?
	
}




?>
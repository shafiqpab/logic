<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

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
		$blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id  and is_deleted=0");
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
	$sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id=$update_id and is_deleted=0 order by row_sequence_no asc";
	//echo $sqlDtls;
	$data_array_dtls=sql_select($sqlDtls);
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
            <td><p><? echo $production_resource[$slectResult[csf('resource_gsd')]]; ?></p><input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" value="<? echo $slectResult[csf('resource_gsd')]; ?>"></td>
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

	// echo "save=".$operation;die;
    
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "ppl_balancing_mst_entry", 1 ) ;
		$field_array="id,gsd_mst_id,allocated_mp,line_no,pitch_time,target,efficiency,balancing_page,learning_cub_method,inserted_by,insert_date,entry_form";
		$data_array="(".$id.",".$breakdown_id.",".$txt_allocated_mp.",".$txt_line_no.",".$txt_pitch_time.",".$txt_target.",".$txt_efficiency_bl.",1,".$cbo_learning_cub_method_bl.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 2)";

       //  echo $data_array;die;

		
		$field_array_dtls="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, smv, target_hundred_perc, cycle_time, theoritical_mp, layout_mp, work_load, weight, worker_tracking, entry_form";
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
			$data_array_dtls.="(".$dtls_id.",".$breakdown_id.",'".$$gsdDtlsId."',".$id.",'".$$seqNo."','".$$bodyPart."','".$$sewingId."','".$$rescId."','".$$smv."','".$$tgtPerc."','".$$cycleTime."','".$$txtTheoriticalMp."','".$$layOut."','".$$workLoad."','".$$weight."','".$$workerTracking."', 2)";
			$dtls_id = $dtls_id+1;
		}

		//echo "10**insert into ppl_balancing_dtls_entry (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("ppl_balancing_mst_entry",$field_array,$data_array,0);
		$rID2=sql_insert("ppl_balancing_dtls_entry",$field_array_dtls,$data_array_dtls,1);
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
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="allocated_mp*line_no*pitch_time*learning_cub_method*target*efficiency*updated_by*update_date";
		$data_array=$txt_allocated_mp."*".$txt_line_no."*".$txt_pitch_time."*".$cbo_learning_cub_method_bl."*".$txt_target."*".$txt_efficiency_bl."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, smv, target_hundred_perc, cycle_time, theoritical_mp, layout_mp, work_load, weight, worker_tracking";
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
			$data_array_dtls.="(".$dtls_id.",".$breakdown_id.",'".$$gsdDtlsId."',".$bl_update_id.",'".$$seqNo."','".$$bodyPart."','".$$sewingId."','".$$rescId."','".$$smv."','".$$tgtPerc."','".$$cycleTime."','".$$txtTheoriticalMp."','".$$layOut."','".$$workLoad."','".$$weight."','".$$workerTracking."')";
			$dtls_id = $dtls_id+1;
		}
		
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
}

if($action=="load_graph_data")
{
	$seqNos=''; $weights=''; $pitchTimes=''; $ucls=''; $lcls='';
	$sql="select a.pitch_time,b.row_sequence_no,b.weight from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and b.mst_id=$data and a.balancing_page=1 and a.is_deleted=0 and b.is_deleted=0";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$seqNos.=$row[csf('row_sequence_no')].",";
		$weights.=number_format($row[csf('weight')],2,'.','').",";
		$pitchTimes.=$row[csf('pitch_time')].",";
		
		$ucl=number_format(($row[csf('pitch_time')]/0.85),2,'.','');
		$ucls.=$ucl.",";
		
		$lcl=number_format((($row[csf('pitch_time')]*2)-$ucl),2,'.','');
		$lcls.=$lcl.",";
	}
	echo "[".substr($seqNos, 0, -1)."]**[".substr($weights, 0, -1)."]**[".substr($ucls, 0, -1)."]**[".substr($pitchTimes, 0, -1)."]**[".substr($lcls, 0, -1)."]";
	exit();	
}

if($action=="balancing_print")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	$mstDataArray=sql_select("select a.id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.tot_mc_smv,a.color_type,a.extention_no,a.system_no,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
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
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Allocated MP</strong></td>
                <td><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Line No.</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('line_no')]; ?></td>
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
                <td><strong>SMV</strong></td>
                <td><strong>:</strong></td>
                <td><? echo number_format($mstDataArray[0][csf('tot_mc_smv')],2); ?></td>
                <td><strong>Custom Style</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('custom_style')]; ?></td>
                
                <td><strong>Bulletin Type</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
            </tr>
            
            
            <tr>
                <td><strong>Insert By</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('inserted_by')]]; ?></td>
                <td><strong>Insert Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('insert_date')]; ?></td>
                <td><strong>Color Type</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $color_type[$mstDataArray[0][csf('color_type')]]; ?></td>
            
            </tr>
            
            <tr>
                <td><strong>Update By</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $user_library[$mstDataArray[0][csf('updated_by')]]; ?></td>
                <td><strong>Update Date Time</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('update_date')]; ?></td>
                <td><strong>System ID</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('system_no')]; ?></td>
            
            </tr>
            
            <tr>
                <td><strong>Fabric Type</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>
                <td><strong>Applicable Period</strong></td>
                <td><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
                <td><strong>Extention No</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('extention_no')]; ?></td>
            
            </tr>

            <tr>
                <td><strong>Product Des.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td colspan="7"><? echo $mstDataArray[0][csf('prod_description')]; ?></td>
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
                <th width="50">Seq. No</th>
                <th width="100">Body Part</th>
                <th width="200">Operation</th>
                <th width="100">Resource</th>
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
                $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0;   
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array();
                 
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
                     
                    if($rescId==129 || $rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==176)
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
                        <td><? echo $body_part[$slectResult[csf('body_part_id')]]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center"><? echo $production_resource[$slectResult[csf('resource_gsd')]]; ?></td>
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
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="20%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp;
						
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
                            	<td width="150"><? echo $production_resource[$key]; ?></td>
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

if($action=="balancing_print2")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	
	$mstDataArray=sql_select("select a.id, a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.bulletin_type,a.applicable_period,b.inserted_by,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
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
                <td colspan="4"><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
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
                     
                    if($rescId==129 || $rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==176)
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
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center"><? echo $production_resource[$slectResult[csf('resource_gsd')]]; ?></td>
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
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td align="right" style="padding-right:5px"><? echo number_format($helperSmv+$machineSmv+$sQISmv+$fIMSmv+$fQISmv+$polyHelperSmv+$pkSmv+$htSmv,2,'.',''); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="32%" valign="top">
                	<?
						$totMpSumm=$helperMp+$machineMp+$sQiMp+$fImMp+$fQiMp+$polyHelperMp+$pkMp+$htMp;
						
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
                            	<td width="170"><? echo $production_resource[$key]; ?></td>
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
?>
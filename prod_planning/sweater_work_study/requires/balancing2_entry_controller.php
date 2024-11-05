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
	$sql= "SELECT id, buyer_id, style_ref, gmts_item_id, working_hour FROM ppl_gsd_entry_mst where id=$data";
	$data_array=sql_select($sql);
 	foreach ($data_array as $row)
	{
		$tot_smv=return_field_value("sum(total_smv) as smv","ppl_gsd_entry_dtls","mst_id='".$row[csf("id")]."' and status_active=1 and is_deleted=0","smv");
		echo "document.getElementById('txt_tot_smv2').value 			= '".number_format($tot_smv,2,'.','')."';\n";
		echo "document.getElementById('cbo_gmt_item_bl2').value 		= '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('txt_working_hour_bl2').value 	= '".$row[csf("working_hour")]."';\n";
		echo "document.getElementById('breakdown_id2').value 			= '".$row[csf("id")]."';\n";
		
		$balanceData=sql_select("select id, allocated_mp, pitch_time, target, efficiency, max_work_load, min_work_load,learning_cub_method from ppl_balancing_mst_entry where gsd_mst_id='".$row[csf("id")]."' and balancing_page=2 and status_active=1 and is_deleted=0");
		echo "document.getElementById('txt_worker').value				= '".$balanceData[0][csf("allocated_mp")]."';\n";
		echo "document.getElementById('txt_min_wl').value				= '".$balanceData[0][csf("min_work_load")]."';\n";
		echo "document.getElementById('txt_max_wl').value 				= '".$balanceData[0][csf("max_work_load")]."';\n";
		echo "document.getElementById('txt_pitch_time2').value 			= '".$balanceData[0][csf("pitch_time")]."';\n";
		echo "document.getElementById('txt_tgt_per_day').value 			= '".$balanceData[0][csf("target")]."';\n";
		echo "document.getElementById('txt_efficiency_bl2').value 		= '".$balanceData[0][csf("efficiency")]."';\n";
		echo "document.getElementById('bl2_update_id').value 			= '".$balanceData[0][csf("id")]."';\n";
		echo "document.getElementById('cbo_learning_cub_method_bl2').value = '".$balanceData[0][csf("learning_cub_method")]."';\n";
		
		if($balanceData[0][csf("id")]>0)
		{
			echo "set_button_status(1, '".$permission."', 'fnc_balancing2_entry',3);\n"; 
		}
		else
		{
			echo "set_button_status(0, '".$permission."', 'fnc_balancing2_entry',3);\n"; 
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
		$blData=sql_select("select gsd_dtls_id,smv from ppl_balancing2_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
		foreach($blData as $row)
		{
			$balanceDataArray[$row[csf('gsd_dtls_id')]].=$row[csf('smv')].",";
		}
		
		$mp=return_field_value("allocated_mp","ppl_balancing_mst_entry","id=$bl_update_id");
	}
	else
	{
		$mp=0;
	}

	$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
	$sqlDtls="SELECT id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, total_smv from ppl_gsd_entry_dtls where mst_id=$update_id and is_deleted=0 order by row_sequence_no asc";
	//echo $order_sql;die;
	$data_array_dtls=sql_select($sqlDtls);
	$i=1;
	foreach($data_array_dtls as $slectResult)
	{
		 if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="trBl2_<? echo $i; ?>">
            <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?><input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>" value="<? echo $slectResult[csf('lib_sewing_id')]; ?>"></td>
            <td style="padding-left:2px"><? echo $slectResult[csf('row_sequence_no')]; ?>
            	<input type="hidden" name="seqNoBl[]" id="seqNoBl_<? echo $i; ?>" value="<? echo $slectResult[csf('row_sequence_no')]; ?>">
                <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $slectResult[csf('id')]; ?>">
            </td>
            <td><? echo $production_resource[$slectResult[csf('resource_gsd')]]; ?><input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" value="<? echo $slectResult[csf('resource_gsd')]; ?>"></td>
            <td align="right" id="totalSmv2_<? echo $i; ?>"><? echo number_format($slectResult[csf('total_smv')],2,'.',''); ?></td>
            <?
			if($balanceDataArray[$slectResult[csf('id')]]!="")
			{
				$smvData=explode(",",substr($balanceDataArray[$slectResult[csf('id')]],0,-1));
				$z=1;
				foreach($smvData as $smv)
				{
				?>
                	<td align="center"><input type="text" name="wSmv[]" id="wSmv_<? echo $i."_".$z; ?>" value="<? echo $smv; ?>" class="text_boxes_numeric" style="width:40px" onkeyup="calculate_smv(<? echo $i.",".$z; ?>)"/></td>
                <?
					$z++;
				}
			}
			else
			{
				for($z=1;$z<=$mp;$z++)
				{
				?>
                	<td align="center"><input type="text" name="wSmv[]" id="wSmv_<? echo $i."_".$z; ?>" value="" class="text_boxes_numeric" style="width:40px" onkeyup="calculate_smv(<? echo $i.",".$z; ?>)"/></td>
                <?
				}
			}
			?>
        </tr>
	<?	
		$i++;
	}
	exit();
}

if($action=="details_list_view_header")
{
	$i=1; $header=''; $smvData=''; $targetData=''; $wlData=''; $tfoot='';
	$blData=sql_select("select smv,target,work_load from ppl_bl_wk_dtls_entry where mst_id=$data and is_deleted=0");
	foreach($blData as $row)
	{
		$header.='<th>W.'.$i.'</th>';
		$smvData.='<th><input type="text" name="smv[]" id="smv_2_'.$i.'" value="'.$row[csf('smv')].'" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
		$targetData.='<th><input type="text" name="tgt[]" id="tgt_3_'.$i.'" value="'.$row[csf('target')].'" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
		$wlData.='<th><input type="text" name="wl[]" id="wl_4_'.$i.'" value="'.$row[csf('work_load')].'" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
		$tfoot.='<th id="td_f_'.$i.'">'.$row[csf('smv')].'</th>';
		$i++;
	}
	echo $header."__".$smvData."__".$targetData."__".$wlData."__".$tfoot;
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$approved=0;
	$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$breakdown_id2");
	foreach($sql as $row){
		$approved=$row[csf('approved')];
	}
	if($approved==3) $approved=1; else $approved=$approved;
	
	if($approved==1){
		echo "approved**".str_replace("'","",$breakdown_id2);
		die;
	}
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "ppl_balancing_mst_entry", 1 ) ;
		$field_array="id,gsd_mst_id,allocated_mp,pitch_time,target,efficiency,max_work_load,min_work_load,tot_smv,balancing_page,learning_cub_method,inserted_by,insert_date,entry_form";
		$data_array="(".$id.",".$breakdown_id2.",".$txt_worker.",".$txt_pitch_time2.",".$txt_tgt_per_day.",".$txt_efficiency_bl2.",".$txt_max_wl.",".$txt_min_wl.",".$txt_tot_smv2.",2,".$cbo_learning_cub_method_bl2.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 2)";
		
		$smvString=explode("_",$smvString);
		$tgtString=explode("_",$tgtString);
		$wlString=explode("_",$wlString);
		
		$field_array_dtls_wl="id, gsd_mst_id, mst_id, smv, target, work_load, entry_form";
		$dtls_id_wk = return_next_id( "id", "ppl_bl_wk_dtls_entry", 1 );
		for($j=0;$j<str_replace("'", '', $txt_worker);$j++)
		{
			$smv=$smvString[$j];
			$target=$tgtString[$j];
			$work_load=$wlString[$j];
			if($data_array_dtls_wk!="") $data_array_dtls_wk.=",";
			$data_array_dtls_wk.="(".$dtls_id_wk.",".$breakdown_id2.",".$id.",'".$smv."','".$target."','".$work_load."', 2)";
			$dtls_id_wk = $dtls_id_wk+1;
		}
		
		$field_array_dtls="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, smv, entry_form";
		$dtls_id = return_next_id( "id", "ppl_balancing2_dtls_entry", 1 );
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$seqNo="seqNo".$j;
			$gsdDtlsId="dtlsId".$j;
			$sewingId="sewingId".$j;
			$rescId="rescId".$j;
			
			for($k=1;$k<=str_replace("'", '', $txt_worker);$k++)
			{
				$smv="wSmv_".$j."_".$k;
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$breakdown_id2.",'".$$gsdDtlsId."',".$id.",'".$$seqNo."','".$$sewingId."','".$$rescId."','".str_replace("'", '',$$smv)."', 2)";
				$dtls_id = $dtls_id+1;
			}
		}

		//echo "10**insert into ppl_balancing2_dtls_entry (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("ppl_balancing_mst_entry",$field_array,$data_array,0);
		$rID2=sql_insert("ppl_bl_wk_dtls_entry",$field_array_dtls_wl,$data_array_dtls_wk,1);
		$rID3=sql_insert("ppl_balancing2_dtls_entry",$field_array_dtls,$data_array_dtls,1);
		 //echo "10**".$rID."&&".$rID2."&&".$rID3.$data_array_dtls;die;

		if($rID && $rID2 && $rID3)
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
		
		$field_array="allocated_mp*pitch_time*learning_cub_method*target*efficiency*max_work_load*min_work_load*tot_smv*updated_by*update_date";
		$data_array=$txt_worker."*".$txt_pitch_time2."*".$cbo_learning_cub_method_bl2."*".$txt_tgt_per_day."*".$txt_efficiency_bl2."*".$txt_max_wl."*".$txt_min_wl."*".$txt_tot_smv2."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$smvString=explode("_",$smvString);
		$tgtString=explode("_",$tgtString);
		$wlString=explode("_",$wlString);
		
		$field_array_dtls_wl="id, gsd_mst_id, mst_id, smv, target, work_load";
		$dtls_id_wk = return_next_id( "id", "ppl_bl_wk_dtls_entry", 1 );
		for($j=0;$j<str_replace("'", '', $txt_worker);$j++)
		{
			$smv=$smvString[$j];
			$target=$tgtString[$j];
			$work_load=$wlString[$j];
			if($data_array_dtls_wk!="") $data_array_dtls_wk.=",";
			$data_array_dtls_wk.="(".$dtls_id_wk.",".$breakdown_id2.",".$bl2_update_id.",'".$smv."','".$target."','".$work_load."')";
			$dtls_id_wk = $dtls_id_wk+1;
		}
		
		$field_array_dtls="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, smv";
		$dtls_id = return_next_id( "id", "ppl_balancing2_dtls_entry", 1 );
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$seqNo="seqNo".$j;
			$gsdDtlsId="dtlsId".$j;
			$sewingId="sewingId".$j;
			$rescId="rescId".$j;
			
			for($k=1;$k<=str_replace("'", '', $txt_worker);$k++)
			{
				$smv="wSmv_".$j."_".$k;
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$breakdown_id2.",'".$$gsdDtlsId."',".$bl2_update_id.",'".$$seqNo."','".$$sewingId."','".$$rescId."','".str_replace("'", '',$$smv)."')";
				$dtls_id = $dtls_id+1;
			}
		}
		//echo "10**insert into ppl_bl_wk_dtls_entry (".$field_array_dtls_wl.") values ".$data_array_dtls_wk;die;
		$rID=sql_update("ppl_balancing_mst_entry",$field_array,$data_array,"id",$bl2_update_id,0);
		$rID2=execute_query( "delete from ppl_bl_wk_dtls_entry where mst_id=$bl2_update_id",0);
		$rID3=execute_query( "delete from ppl_balancing2_dtls_entry where mst_id=$bl2_update_id",0);
		$rID4=sql_insert("ppl_bl_wk_dtls_entry",$field_array_dtls_wl,$data_array_dtls_wk,1);
		$rID5=sql_insert("ppl_balancing2_dtls_entry",$field_array_dtls,$data_array_dtls,1);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5;die;

		if($rID && $rID2 && $rID3 && $rID4 && $rID5)
		{
			oci_commit($con);  
			echo "1**".str_replace("'", '', $bl2_update_id);
		}
		else
		{
			oci_rollback($con);
			echo "6**".str_replace("'", '', $bl2_update_id);
		}
		disconnect($con);
		die;
	}
}

if($action=="load_graph_data")
{
	$tittles=''; $maxWl=''; $minWl=''; $wls=''; $i=1;
	$sql="select a.max_work_load, a.min_work_load, b.target, b.work_load from ppl_balancing_mst_entry a, ppl_bl_wk_dtls_entry b where a.id=b.mst_id and b.mst_id=$data and a.balancing_page=2";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$tittles.="'W.".$i."',";
		$wls.=number_format($row[csf('work_load')],2,'.','').",";
		$maxWl.=$row[csf('max_work_load')].",";
		$minWl.=$row[csf('min_work_load')].",";
		$i++;
	}
	echo "[".substr($tittles, 0, -1)."]**[".substr($wls, 0, -1)."]**[".substr($maxWl, 0, -1)."]**[".substr($minWl, 0, -1)."]";
	exit();	
}

if($action=="balancing_print")
{
	$data=explode("**",$data);
	$bl_update_id=$data[0];
	$report_title=$data[1];
	
	$mstDataArray=sql_select("select a.id, a.gmts_item_id, a.working_hour, b.allocated_mp, b.pitch_time, b.target, b.efficiency, b.max_work_load, b.min_work_load, b.tot_smv from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='".$bl_update_id."' and b.balancing_page=2 and b.status_active=1 and b.is_deleted=0");
	
	$i=1; $header=''; $smvData=''; $targetData=''; $wlData=''; $tfoot=''; $tittles_arr=''; $maxWl_arr=''; $minWl_arr=''; $wls_arr='';
	$blData=sql_select("select smv,target,work_load from ppl_bl_wk_dtls_entry where mst_id=$bl_update_id and  is_deleted=0");
	foreach($blData as $row)
	{
		$header.='<th width="50">W.'.$i.'</th>';
		$smvData.='<td align="right">'.$row[csf('smv')].'</td>';
		$targetData.='<td align="right">'.$row[csf('target')].'</td>';
		$wlData.='<td align="right">'.$row[csf('work_load')].'</td>';
		$tfoot.='<td align="right">'.$row[csf('smv')].'</td>';
		
		$tittles_arr[]="W.".$i;
		$maxWl_arr[]=$mstDataArray[0][csf('max_work_load')];
		$minWl_arr[]=$mstDataArray[0][csf('min_work_load')];
		$wls_arr[]=number_format($row[csf('work_load')],2,'.','');
		$i++;
	}
	
	$tittles= json_encode($tittles_arr);
	$maxWl= json_encode($maxWl_arr); 
	$minWl= json_encode($minWl_arr); 
	$wls= json_encode($wls_arr);
	
	$tableWidth=($i*50)+285;
?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:<? echo $tableWidth;?>px">
        <table width="890" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Operation Balancing2 Sheet</u></strong></td>
            </tr>
            <tr>
                <td width="140"><strong>No. Of Worker</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="110"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td width="160"><strong>Efficiency</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="110"><? echo $mstDataArray[0][csf('efficiency')]; ?></td>
                <td width="110"><strong>Working Hour</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
            </tr>
            <tr>
                <td><strong>Max Work Load %</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('max_work_load')]; ?></td>
                <td><strong>Min Work Load %</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('min_work_load')]; ?></td>
                <td><strong>Total SMV</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo number_format($mstDataArray[0][csf('tot_smv')],2,'.',''); ?></td>
            </tr>
            <tr>
                <td><strong>Target Qty. per day</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('target')]; ?></td>
                <td><strong>Cycle Time/Pitch Time</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('pitch_time')]; ?></td>
                <td><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead>
            	<tr bgcolor="#dddddd" align="center">
                    <th width="160" rowspan="4">Operation</th>
                    <th width="50" rowspan="4">Seq. No</th>
                    <th width="105" rowspan="4">Resource</th>
                    <th width="70">Worker</th>
                    <? echo $header; ?>
                </tr>
                <tr>
                	<th>SMV</th>
                    <? echo $smvData; ?>
                </tr>
                <tr>
                	<th>Target</th>
                    <? echo $targetData; ?>
                </tr>
                <tr>
                	<th>W. Load</th>
                    <? echo $wlData; ?>
                </tr>
            </thead>
            <tbody>
            <?
                $balanceDataArray=array();
				if($bl_update_id>0)
				{
					$blData=sql_select("select gsd_dtls_id,smv from ppl_balancing2_dtls_entry where mst_id=$bl_update_id and is_deleted=0");
					foreach($blData as $row)
					{
						$balanceDataArray[$row[csf('gsd_dtls_id')]].=$row[csf('smv')].",";
					}
				}
			
				$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
				$sqlDtls="SELECT id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, total_smv from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";
				 //echo $order_sql;die;
				$data_array_dtls=sql_select($sqlDtls);
				$i=1;
				foreach($data_array_dtls as $slectResult)
				{
					 //if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
				?>
					<tr bgcolor="<? //echo $bgcolor; ?>">
						<td style="word-wrap:break-word"><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
						<td style="padding-left:2px"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
						<td style="word-wrap:break-word"><? echo $production_resource[$slectResult[csf('resource_gsd')]]; ?></td>
						<td align="right"><? echo number_format($slectResult[csf('total_smv')],2,'.',''); ?></td>
						<?
						if($balanceDataArray[$slectResult[csf('id')]]!="")
						{
							$smvData=explode(",",substr($balanceDataArray[$slectResult[csf('id')]],0,-1));
							foreach($smvData as $smv)
							{
							?>
								<td align="right"><? echo $smv; ?></td>
							<?
							}
						}
						else
						{
							for($z=1;$z<=$mstDataArray[0][csf('allocated_mp')];$z++)
							{
							?>
								<td align="right">&nbsp;</td>
							<?
							}
						}
						?>
					</tr>
				<?	
					$i++;
				}
			?>
            </tbody>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right">&nbsp;</th>
				<? echo $tfoot; ?>
			</tfoot>
		</table>
        <table cellpadding="0" cellspacing="0" width="100%">
        	<tr> 
            	<td width="100%"></td>
            </tr>
        </table>
        <div style="width:900px; margin-top:10px; height:220px; border:solid 1px" align="center">
            <table style="margin-left:5px; font-size:12px">
                <tr>
                    <td><b>Work Load Curve</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="90">Max Work Load</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="90">Min Work Load</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Work Load</td>
                </tr>
            </table>
            <canvas id="canvas" height="200" width="890"></canvas>
        </div>
    </div>
    
    <script>
		var lineChartData = {
            labels : <? echo $tittles; ?>,
           datasets : [
				{
					//label: "My First dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#7D60A0",
					pointColor : "#7D60A0",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#7D60A0",
					data : <? echo $wls; ?>
				},
				{
					//label: "My Fourth dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#BE4B48",
					pointColor : "#BE4B48",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#BE4B48",
					data : <? echo $maxWl; ?>
				},
				{
					//label: "My Second dataset",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "#98B954",
					pointColor : "#98B954",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "#98B954",
					data : <? echo $minWl; ?>
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
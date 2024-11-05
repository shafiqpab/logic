<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];




if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/dhu_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/dhu_report_controller' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/dhu_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}
if ($action=="load_drop_down_line")
{
    $explode_data = explode("_",$data);
	//echo  $explode_data
    $prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
    $txt_date = $explode_data[3];
    
    $cond="";
    if($prod_reso_allo==1)
    {
        $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
        $line_array=array();
        
        if($txt_date=="")
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
            $line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
        }
        else
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
         if($db_type==0)    $data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
         if($db_type==2)    $data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";

             $line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }
        
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }

        echo create_drop_down( "cbo_line_id", 100,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
    }
    else
    {
        if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
        if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

        echo create_drop_down( "cbo_line_id", 100, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0);
    }
    exit();
	
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	// echo "<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc( $process ));	
	$company_library=return_library_array( "SELECT id,company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("SELECT id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
	//$line_arr =return_library_array("SELECTselect id, line_name from lib_sewing_line",'id','line_name');
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
			$line_n=$lRow[csf('line_name')];
		}
	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_location=str_replace("'","", $cbo_location);
	$cbo_floor=str_replace("'","", $cbo_floor);
	$cbo_line_id=str_replace("'","", $cbo_line_id);
	$txt_int_ref=str_replace("'","", $txt_int_ref);
	$txt_style_ref=str_replace("'","", $txt_style_ref);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	//echo $cbo_line_id;die;

	$all_cond="";
	if($cbo_company_name!="")$all_cond.=" and a.company_name='$cbo_company_name'";
	if($txt_style_ref)$all_cond.=" and a.style_ref_no like '%$txt_style_ref%'";
	if($cbo_location!="")$all_cond.=" and a.location_name='$cbo_location'";
	if($cbo_floor!="")$all_cond.=" and c.floor_id='$cbo_floor'";
	if($cbo_line_id)$all_cond.=" and c.sewing_line='$cbo_line_id'";
	if($txt_int_ref)$all_cond.=" and b.grouping='$txt_int_ref'";
	if($txt_date_from !="" && $txt_date_to !="" )
	{
		$all_cond.=" and c.production_date between'$txt_date_from' and '$txt_date_to'";
	}
	
	if($report_type==1) 
	{
		   $sql="SELECT a.company_name,a.style_ref_no,a.location_name,b.grouping,c.floor_id,c.production_date,to_char(c.production_hour,'HH24') as prod_hour,c.sewing_line,d.defect_point_id,d.defect_type_id,d.defect_qty,c.production_quantity as check_qty,c.spot_qnty,c.alter_qnty,c.reject_qnty,c.prod_reso_allo from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c, pro_gmts_prod_dft d where a.id=b.job_id and b.id=c.po_break_down_id  and c.id=d.mst_id  and c.production_type=5  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0  and d.status_active = 1 and d.is_deleted = 0  $all_cond ";
		  //echo $sql;die;

		   $data_array=array();
		   $defect_type_arr=array();
		   foreach (sql_select($sql) as $row) 
		   {
			
			$sewing_line='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_ids=$prod_reso_arr[$row["SEWING_LINE"]];
				$sl_ids_arr = explode(",", $sewing_line_ids);
				foreach($sl_ids_arr as $val)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
				}
			}
			else
			{
				$sewing_line=$lineArr[$row["SEWING_LINE"]];
			}
			$sewing_line_serial='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_ids=$prod_reso_arr[$row["SEWING_LINE"]];
				
				foreach($sl_ids_arr as $val)
				{
					if($sewing_line_serial=='') $sewing_line_serial=$lineSerialArr[$val]; else $sewing_line_serial=$lineSerialArr[$val];
				}
			}
			else
			{
				$sewing_line_serial=$lineSerialArr[$row["SEWING_LINE"]];
			}
			$prodHour = explode(":",$row["PROD_HOUR"]);
					
				$data_array[$sewing_line_serial][$sewing_line][$row["SEWING_LINE"]][$prodHour[0]*1]['spot_qnty']+=$row["SPOT_QNTY"];
				$data_array[$sewing_line_serial][$sewing_line][$row["SEWING_LINE"]][$prodHour[0]*1]['alter_qnty']+=$row["ALTER_QNTY"];
				$data_array[$sewing_line_serial][$sewing_line][$row["SEWING_LINE"]][$prodHour[0]*1]['check_qty']+=$row["CHECK_QTY"];
				$data_array[$sewing_line_serial][$sewing_line][$row["SEWING_LINE"]][$prodHour[0]*1]['reject_qnty']+=$row["REJECT_QNTY"];
				
				$data_array[$sewing_line_serial][$sewing_line][$row["SEWING_LINE"]][$prodHour[0]*1]['production_date']=$row["PRODUCTION_DATE"];	


				$data_array[$sewing_line_serial][$sewing_line][$row["SEWING_LINE"]][$prodHour[0]*1]['defect_qty']+=$row["DEFECT_QTY"];	
				$data_array[$sewing_line_serial][$sewing_line][$row["SEWING_LINE"]][$prodHour[0]*1][$row["DEFECT_TYPE_ID"]][$row["DEFECT_POINT_ID"]]['defect_qty']+=$row["DEFECT_QTY"];	
				if($row["DEFECT_QTY"] > 0)
				{
					$defect_type_arr[$row["DEFECT_TYPE_ID"]][$row["DEFECT_POINT_ID"]]=$row["DEFECT_POINT_ID"];
				}
				
			

				
		   }
		// echo "<pre>";print_r($defect_type_arr);die;
		   $tbl_width = 370+(count($defect_type_arr[2])*50)+(count($defect_type_arr[3])*50)+(count($defect_type_arr[4])*50);
		   $rowspanArr = array();
		   foreach ($data_array as $slno_key => $slno_val)
		   {
			   
			   foreach ($slno_val as $line_name_key => $line_name_val)
			   {
				   foreach ($line_name_val as $sewing_line_key => $line_val)
				   {
					foreach ($line_val as $prod_hour_key => $r)
					{
						$rowspanArr[$sewing_line_key]++;
					}
				   }
				}
			}
			//  echo "<pre>";print_r($rowspanArr);die;
			 ob_start();
			 ?>
			 <style>
				.block_div {
				width: auto;
				height: auto;
				text-wrap: normal;
				vertical-align: bottom;
				display: block;
				position: !important;
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
				}
			 </style>
		 	<table width="1200" cellspacing="0" style="margin-top: 25px" >

			 <tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong><? echo $company_library[$cbo_company_name];?></strong>
		 			</td>
		 		</tr>
		 		<tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong>Daily Line Wise Data analysis of Sewing DHU for the Month &nbsp;<?=change_date_format( str_replace("'","",trim($txt_date_from)));?> &nbsp; TO  &nbsp;<?=change_date_format( str_replace("'","",trim($txt_date_to)));?></strong>
		 			</td>
		 		</tr>
		 		
		 		<tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong> </strong>
		 			</td>
		 		</tr>
				<tr>
					
				</tr>
		 	</table>
			<?
			

			foreach ($data_array as $slno_key => $slno_val)
			{
				
				foreach ($slno_val as $line_name_key => $line_name_val)
				{ 
					$total_chk_qty=0;
					$total_defective_pcs=0;
					$total_pass_pcs=0;
					$ttl_defect_found=0;
					$ttl_dhu=0;
					foreach ($line_name_val as $sewing_line_key => $line_val)
					{	
						ksort($line_val);
						$defect_sum_array = array();
						?>	<table>
							<tr>
							<td  style="border:none;font-size:16px; font-weight:bold;margin:0 auto" ><b>Line:<?=rtrim($line_name_key,",") ;?></b></td>
							</tr>
						  		<?												
								?>	
							</table>
							
							<div style="max-height:425px; width:720px;">
									
								<table cellspacing="0" cellpadding="0"  border="1" rules="all"   width="<?=$tbl_width;?>"  class="rpt_table" style="margin: 0 auto;"  >
									<thead>
										<tr height="100">											
											<th class="alignment_css" width="100"><div class="block_div">Date</div></th>
											<th class="alignment_css" width="60"><div class="block_div">Hour</div></th>
											<th class="alignment_css" width="60"><div class="block_div">Check qty </div></th>
											<th class="alignment_css" width="60"><div class="block_div">Defective Pcs</div></th>
											<th class="alignment_css" width="60"><div class="block_div">Total Pass Pcs</div></th>
											<th class="alignment_css" width="60"><div class="block_div">Total Defects found.</div></th>
											<th class="alignment_css" width="60"><div class="block_div">DHU</div></th>
											
											<?
											foreach ($defect_type_arr[2] as $reject_point_id) 
											{
												?>
												<th width="50"><div class="block_div"><?=$sew_fin_reject_type_arr[$reject_point_id];?></div></th>
												<?
												
											}
											?>
											<?
											foreach ($defect_type_arr[3] as $alter_point_id) 
											{
												?>
												<th width="50"><div class="block_div"><?=$sew_fin_alter_defect_type[$alter_point_id];?></div></th>
												<?
												
											}
											?>
												
											<?
											foreach ($defect_type_arr[4] as $spot_point_id) 
											{
												?>
												<th width="50"><div class="block_div"><?=$sew_fin_spot_defect_type[$spot_point_id];?></div ></th>
												<?
												
											}
											?>
											
											
										</tr>
									</thead>	
									<tbody>											
												
											<?
											$l=0;
											foreach ($line_val as $prod_hour_key => $r)
											{	
												$defective_pcs=$r['spot_qnty'] + $r['alter_qnty'] + $r['reject_qnty'];
												$pass_pcs= $r['check_qty'] - $defective_pcs ;
												$dhu=(($r['defect_qty']*100) / $r['check_qty']);
													?>
												
													<?
													if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>  
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
															<?
															if($l==0)
															{

															
															?>
														<td rowspan="<?=$rowspanArr[$sewing_line_key];?>" width="100" style="font-size:14px; font-weight:bold;"><b><?=change_date_format( str_replace("'","",trim($txt_date_from)));?><br> - <br> <?=change_date_format( str_replace("'","",trim($txt_date_to)));?></b></td>
														<?$l++;}?>

														<td align="center" width="70"><?=date('h:00 a',strtotime($prod_hour_key.":00"));?></td>
														<td align="right" width="60"><?=$r['check_qty'];?></td>
														<td align="right" width="60"><?=$defective_pcs;?></td>
														<td align="right" width="60"><?=$pass_pcs;?></td>
														<td align="right" width="60"><?=$r['defect_qty'];?></td>
														<td align="right" width="60"><?=number_format($dhu,2);?></td>
														<?
														
														foreach ($defect_type_arr[2] as $reject_point_id) 
														{ 
															
															?>
															<td width="50" align="right"><?=$r[2][$reject_point_id]['defect_qty'];?></td>
															<?
															$defect_sum_array[2][$reject_point_id]+=$r[2][$reject_point_id]['defect_qty'];
															
														}
														
														?>
														<?
														foreach ($defect_type_arr[3] as $alter_point_id) 
														{
															?>
															<td width="50" align="right"><?=$r[3][$alter_point_id]['defect_qty'];?></td>
															<?
															$defect_sum_array[3][$alter_point_id]+=$r[3][$alter_point_id]['defect_qty'];
															
														}
														?>
														<?
														foreach ($defect_type_arr[4] as $spot_point_id) 
														{
															?>
															<td width="50" align="right"><?=$r[4][$spot_point_id]['defect_qty'];?></td>
															<?
															$defect_sum_array[4][$spot_point_id]+=$r[4][$spot_point_id]['defect_qty'];
															
														}
														?>
														<?
														$total_chk_qty +=$r['check_qty'];
														$total_defective_pcs +=$defective_pcs;
														$total_pass_pcs      +=$pass_pcs;
														$ttl_defect_found    +=$r['defect_qty'];
														$ttl_dhu             +=$dhu;
														
														?>
													</tr>
																				<?														

											}
											?>
									</tbody>									
										<tfoot>
											<tr>
												<th width="60"  rowspan="2"></th>
												<th align="center" width="70" rowspan="2">Total</th>
												<th align="right" width="60"  rowspan="2"><?=$total_chk_qty;?></th>
												<th align="right" width="60"  rowspan="2"><?=$total_defective_pcs;?></th>
												<th align="right" width="60"  rowspan="2"><?=$total_pass_pcs;?></th>
												<th align="right" width="60"  rowspan="2"><?=$ttl_defect_found;?></th>
												<th align="right" width="60"  rowspan="2"><?=number_format($ttl_dhu,2);?></th>
												<?
												foreach ($defect_type_arr[2] as $reject_point_id) 
												{
													?>
													<th align="right" width="50"><?=$defect_sum_array[2][$reject_point_id];?></th>
													
													<?
													
												}
												?>
												<?
												foreach ($defect_type_arr[3] as $alter_point_id) 
												{
													?>
													<th  align="right" width="50"><?=$defect_sum_array[3][$alter_point_id];?></th>
													<?
													
												}
												?>
												<?
												foreach ($defect_type_arr[4] as $spot_point_id) 
												{
													?>
													<th align="right" width="50"><?=$defect_sum_array[4][$spot_point_id];?></th>
													<?
													
												}
												?>

											</tr>
											
											<tr>
											<?
												foreach ($defect_type_arr[2] as $reject_point_id) 
												{
													?>
													<th align="right" width="50"><?=number_format(($defect_sum_array[2][$reject_point_id] /$ttl_defect_found)*100,2);?>%</th>
													
													<?
													
												}
												?>
												<?
												foreach ($defect_type_arr[3] as $alter_point_id) 
												{
													?>
													<th  align="right" width="50"><?=number_format(($defect_sum_array[3][$alter_point_id] / $ttl_defect_found)*100,2);?>%</th>
													<?
													
												}
												?>
												<?
												foreach ($defect_type_arr[4] as $spot_point_id) 
												{
													?>
												<th  align="right" width="50"><?=number_format(($defect_sum_array[4][$spot_point_id] / $ttl_defect_found)*100,2);?>%</th>
													<?
													
												}
												?>

											</tr>
										</tfoot>
									    
								</table>																		  
							</div>
							<?
					}
				}
			}	
		
			?>
				<br>
				<br>
				<br>
				<div align="left" style="width:100%;">
			<div align="center" style="width:2850px; height:500px;  margin-left:20px; border:solid 1px">
				<table style="margin-left:60px; font-size:12px" align="left">
				<tr>
					<td align="left" bgcolor="red" width="10"></td>
					<td> Sewing Defect Comparison-Hourly Basis  TOP 3 Defects Analysis</td>
				</tr>
				</table>
				<canvas id="canvas" height="400" width="2800"></canvas>
			</div>
		</div>
		<?
			//$reject_qty_type=array(0=>"Reject");
			$bar_arr=array();$val_arr=array();
			foreach($defect_type_arr[2] as $kk=>$vv)
			{
				$bar_arr[]=$sew_fin_reject_type_arr[$vv];
				$val_arr[]=number_format(($defect_sum_array[2][$reject_point_id] /$ttl_defect_found)*100,2);
			}
			
			foreach($defect_type_arr[3] as $key=>$val)
			{
				$bar_arr[]=$sew_fin_alter_defect_type[$val];
				$val_arr[]=number_format(($defect_sum_array[3][$val] / $ttl_defect_found)*100,2);
			}
			
			foreach($defect_type_arr[3]as $keyr=>$valr)
			{
				$bar_arr[]=$sew_fin_alter_defect_type[$valr];
				$val_arr[]=number_format(($defect_sum_array[3][$valr] / $ttl_defect_found)*100,2);
			}
			
			$bar_arr= json_encode($bar_arr);
			$val_arr= json_encode($val_arr);
			?>
		   <script>
				var barChartData = {
				labels : <? echo $bar_arr; ?>,
				datasets : [
						{
							fillColor : "blue",
							strokeColor : "rgba(220,220,220,0.8)",
							highlightFill: "rgb(255,99,71)",
							highlightStroke: "rgba(220,220,220,1)",
							data : <? echo $val_arr; ?>
						}
					]
				}
				
				var ctx = document.getElementById("canvas").getContext("2d");
				window.myBar = new Chart(ctx).Bar(barChartData, {
				responsive : true
				});
		   </script>
			<?
		


	}
	$html = ob_get_contents();
    ob_clean();
     foreach (glob("$user_id*.xls") as $filename) {
     @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$type";

	exit();
}


 
?>
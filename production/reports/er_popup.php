<? 
header('Content-type:text/html; charset=utf-8');
session_start();
error_reporting(0);
require_once('../../includes/common.php');
echo load_html_head_contents("Efficiency Report V2", "../../", 1, 1,$unicode,1,1);

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
	 
$process = array( &$_POST );
extract(check_magic_quote_gpc( $process )); 

if($action=="details_popup")
{
	echo load_html_head_contents("Efficiency Report V2", "../../../", 1, 1,$unicode,1,1);
	extract($_REQUEST);
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	list($comapny_id,$location_id,$floor_id) = explode("__",$search_string);

	$date_from 		= date('01-M-y');
	$date_to 		= date('d-M-y');
	
	if($location_id==0) $rLocationCond=""; else $rLocationCond="and a.location_id in ($location_id)";
	if($floor_id==0) $rFloorCond=""; else $rFloorCond="and a.floor_id in ($floor_id)";
	
	$proddatecond="";
	
	$resource_sql="SELECT a.id as ID, a.company_id as COMPANY_ID, a.location_id as LOCATION_ID, a.floor_id as FLOOR_ID, a.line_number as LINEID,
	b.target_per_hour as TARGET_HOUR, b.man_power as MAN_POWER, b.working_hour as WORKING_HOUR, b.line_chief as PMAPM_NAME, b.capacity as CAPACITY, (c.smv_adjust/60) as SMVADJUST,
	c.pr_date as PROD_DATE,
	d.po_id as POID, d.gmts_item_id as GMTS_ITEM, d.actual_smv as ACTUAL_SMV, e.FLOOR_SERIAL_NO
	from lib_prod_floor e, prod_resource_mst a, prod_resource_dtls_mast b left join prod_resource_color_size d on b.id=d.dtls_id and d.status_active=1 and 
	d.is_deleted=0, prod_resource_dtls c  where e.id=a.floor_id and a.id=b.mst_id and b.id=c.mast_dtl_id 
	and a.company_id in (".$comapny_id.") and c.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and e.production_process=5 $rLocationCond $rFloorCond order by e.floor_serial_no";
	
	// echo $resource_sql; die;
	$dataArray_sql=sql_select($resource_sql);
	//$currentDate=date("d-m-y", strtotime($date_to));
	$poidArr=array(); $nooflinearray=array(); $floorarr=array(); $datewiseprodarr=array();
	foreach($dataArray_sql as $porow)
	{
		if($porow['POID']!="")
			$poidArr[$porow['POID']]=$porow['POID'];
		$floorarr[$porow['FLOOR_ID']]=$porow['FLOOR_ID'];
	}
	
	$sewingSql="SELECT po_break_down_id as POID, item_number_id as GITEMID, floor_id as FLOORID,SEWING_LINE, production_date as PRODDATE, production_quantity as PRODQTY from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type = '5'  and production_date between '$date_from' and '$date_to'";
	$sewingArray_sql=sql_select($sewingSql); 
	foreach($sewingArray_sql as $srow)
	{
		$poidArr[$srow['POID']]=$srow['POID'];
	}
	$poidcond = where_con_using_array($poidArr,0,"a.id");
	$poitemsmvarr=array();
	$posql="SELECT a.id as ID, b.gmts_item_id as GMTSITEMID, b.smv_pcs as SMVPCS from wo_po_break_down a, wo_po_details_mas_set_details b where a.job_id=b.job_id and a.status_active=1 and a.is_deleted=0 $poidcond group by a.id, b.gmts_item_id, b.smv_pcs";
	//echo $posql;
	$poArray_sql=sql_select($posql);
	
	foreach($poArray_sql as $prow)
	{
		$poitemsmvarr[$prow['ID']][$prow['GMTSITEMID']]=$prow['SMVPCS'];
	}
	unset($poArray_sql);
	
	$productionarr=array(); $totProducehour=0;$a=0;
	foreach($sewingArray_sql as $srow)
	{
		$dayproducehour=0;
		$dayproducehour=($srow['PRODQTY']*$poitemsmvarr[$srow['POID']][$srow['GITEMID']])/60;
		$prodDate=date("d-M-y", strtotime($srow['PRODDATE']));
		//echo strtotime($date_to).'='.strtotime($srow['PRODDATE']).'<br>';
		if(strtotime($date_to)==strtotime($srow['PRODDATE']))
		{
			$productionarr[$srow['FLOORID']][$srow['SEWING_LINE']]['todayproduction']+=$srow['PRODQTY'];
			$productionarr[$srow['FLOORID']][$srow['SEWING_LINE']]['todayproducehour']+=$dayproducehour;
			
		}
			
		$productionarr[$srow['FLOORID']][$srow['SEWING_LINE']]['productionqty']+=$srow['PRODQTY'];
		$productionarr[$srow['FLOORID']][$srow['SEWING_LINE']]['producehour']+=$dayproducehour;
		$totProducehour+=$dayproducehour;
		// $a+=$totProducehour;
		// echo $a.'<br>';
		$datewiseprodarr[$srow['FLOORID']][$srow['SEWING_LINE']][$prodDate]['producehour']+=$dayproducehour;
	}
	unset($sewingArray_sql);
	//print_r($productionarr);
	
	$resourceDataArr=array(); $totcuavahour=0; $floordatewisearr=array();
	foreach($dataArray_sql as $row)
	{
		$itemSmv=$cumPlanHour=$cuAvai_hour=0;
		$itemSmv=$poitemsmvarr[$row['POID']][$row['GMTS_ITEM']];
		$cumPlanHour=($row['TARGET_HOUR']*$itemSmv/60);
		$cuAvai_hour=($row['MAN_POWER']*$row['WORKING_HOUR'])+$row['SMVADJUST'];
		//echo $currentDate.'--'.$row['PROD_DATE'].'<br>';
		
		$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['pmapm'].='*'.$row['PMAPM_NAME'];
		$resDate=date("d-M-y", strtotime($row['PROD_DATE']));
		if(strtotime($date_to)==strtotime($row['PROD_DATE']))
		{
			$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['todaycapacity']+=$row['CAPACITY']*$row['WORKING_HOUR'];
			$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['daywh']+=$row['WORKING_HOUR'];
			$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['daymp']+=$row['MAN_POWER'];
			$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['dayworkinghour']+=$row['MAN_POWER']*$row['WORKING_HOUR'];
			$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['line'].=','.$row['ID'];
			
			//$floordatewisearr[$row['FLOOR_ID']][$row['ID']][$resDate]['wh']+=$row['WORKING_HOUR'];
		}
		$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['capacity']+=$row['CAPACITY']*$row['WORKING_HOUR'];
		$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['cu_plan_hour']+=$cumPlanHour;
		$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['cu_avail_hour']+=$cuAvai_hour;
		
		$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['wh']+=$row['WORKING_HOUR'];
		$resourceDataArr[$row['FLOOR_ID']][$row['ID']]['mp']+=$row['MAN_POWER'];
		$totcuavahour+=$cuAvai_hour;
		$floordatewisearr[$row['FLOOR_ID']][$row['ID']]['dayworkinghour']+=$row['MAN_POWER']*$row['WORKING_HOUR'];
	}
	unset($dataArray_sql);
	ob_start();
	?> 
	<fieldset style="width:300px;margin:0 auto;">
		<div style="width:300px;"> 
			<table width="300" cellspacing="0" style="margin: 20px 0"> 
				<tr style="border:none;">
					<td align="center" style="border:none; font-size:14px;font-weight: bold;" colspan="4">                        
						Date <?=date('d-m-Y');?>                      
					</td>
				</tr>
			</table>
			<div>
				<table width="300" cellspacing="0" border="1" class="rpt_table" rules="all">
					<thead> 	 	 	 	 	 	
						<tr>
							<th width="20">SL.</th>  
							<th width="90">Sewing Floor</th>  
							<th width="70">Sewing Line</th>  
							<th width="60">Day Efficiency</th>
							<th width="60"><p>Day Produced Pcs</p></th>
						</tr>
					</thead>
				</table>
				<div style="max-height:400px; overflow-y:auto; width:300px" id="scroll_body">
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="300" rules="all" id="table_body" >
					<? $i=1;
					foreach($resourceDataArr as $flid=>$floordata)
					{
						foreach ($floordata as $lkey => $ldata) 
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$todayproduction=$cuprodqty=$dayproducehour=$producehour=$dayworkinghour=$daymanpower=$cuplaneff=$dayeff=$flooravgeff=$avgeff=0;
							$todayproduction=$productionarr[$flid][$lkey]['todayproduction'];
							$cuprodqty=$productionarr[$flid][$lkey]['productionqty'];
							$dayproducehour=$productionarr[$flid][$lkey]['todayproducehour'];
							$producehour=$productionarr[$flid][$lkey]['producehour'];
							$dayworkinghour=$ldata['daywh']/(count(array_filter(array_unique(explode(",",$ldata['line'])))));
							$daymanpower=$resourceDataArr[$flid][$lkey]['dayworkinghour'];//*$ldata['daywh'];
							$cuplaneff=($ldata['cu_plan_hour']/$ldata['cu_avail_hour'])*100;
							$dayeff=($daymanpower) ? ($dayproducehour/$daymanpower)*100 : 0;
							// echo "(".$dayproducehour."/".$daymanpower.")*100<br>";
							$flooravgeff=($producehour/$ldata['cu_avail_hour'])*100;
							$avgeff=$flooravgeff;//($producehour/$ldata['cu_avail_hour'])*100;
							$floorspan=count($floorarr);

							$sewing_line = "";
							$sewing_line_ids=$prod_reso_arr[$lkey];
							$sl_ids_arr = explode(",", $sewing_line_ids);
							foreach($sl_ids_arr as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}

							$bg_color = ($dayeff<72) ? "red" : "";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_1nd<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_1nd<?=$i; ?>" >
								<td width="20" align="center"><?=$i; ?></td>  
								<td style="word-wrap: break-word;word-break: break-all;" width="90" title="<?=$flid; ?>">
									<?=$floor_library[$flid]; ?>
								</td>  
								<td width="70"><p><?=$sewing_line;?></p></td>
								<td bgcolor="<?=$bg_color;?>" width="60" align="right" title="<?=$dayproducehour.'/'.$daymanpower; ?>"><?=number_format($dayeff,2); ?>%</td>
								<td width="60" align="right"><?=number_format($todayproduction,0); ?></td>
							</tr>
							<?
							$i++;
							$gdayplanpcs+=$ldata['todaycapacity'];
							$gcuplanpcs+=$ldata['capacity'];
							$gcuplanprohour+=$ldata['cu_plan_hour'];
							$gcuavaihour+=$ldata['cu_avail_hour'];
							$gtodayproduction+=$todayproduction;
							$gcuproduction+=$cuprodqty;
							$gdayProdhour+=$dayproducehour;
							$gproducehour+=$producehour;
							
							$totdayworkinghour+=$dayworkinghour;
							
							$gdaymanpower+=$daymanpower;
							$gactcuavahour+=$ldata['cu_avail_hour'];
						}
					}
					//echo $totdayworkinghour.'='.count($floorarr);
					$gdayworkinghour=($totdayworkinghour/count($floorarr));
					$gcuplaneff=($gcuplanprohour/$gcuavaihour)*100;
					$gdayeff=($gdayProdhour/$gdaymanpower)*100;
					$gflooravgeff=($gproducehour/$gactcuavahour)*100;
					$gavgEff=$gflooravgeff;
					$gfloorEff=$gflooravgeff;
					?> 
					</table>
				</div>
				<table width="300" cellspacing="0" border="1" class="rpt_table" rules="all" >
					<tfoot> 	 	 	 	 	 	
						<tr>
							<th width="20">&nbsp;</th>  
							<th width="90"></th>
							<th width="70">TOTAL :</th>
							<th width="60" align="right"><?=number_format($gdayeff,2); ?></th>
							<th width="60"><?=number_format($gtodayproduction,0);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<br> <? $datediff = datediff( 'd', $date_from,$date_to); 
		$tblwidth=300;
		?>
		
		<table width="<?=$tblwidth; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead> 	 	 	 	 	 	
				<tr>
					<th style="word-wrap: break-word;word-break: break-all;" width="20">SL.</th>  
					<th style="word-wrap: break-word;word-break: break-all;" width="90">Sewing Floor</th>  
					<th style="word-wrap: break-word;word-break: break-all;" width="70">Sewing Line</th>  
					<th style="word-wrap: break-word;word-break: break-all;" width="60">MTD Avg. Eff%</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="60">MTD P PCS</th>
				</tr>
			</thead>
			<? $n=1;
				foreach($floordatewisearr as $flrid=>$fldata)
				{
					foreach ($fldata as $lkey => $ldata) 
					{
						$sewing_line = "";
						$sewing_line_ids=$prod_reso_arr[$lkey];
						$sl_ids_arr = explode(",", $sewing_line_ids);
						foreach($sl_ids_arr as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
						if($n%2==0) $nbgcolor="#E9F3FF"; else $nbgcolor="#FFFFFF";
						$mtdavgeff=($resourceDataArr[$flrid][$lkey]['cu_avail_hour']) ? ($productionarr[$flrid][$lkey]['producehour']/($resourceDataArr[$flrid][$lkey]['cu_avail_hour']))*100 : 0;
						$productionqty = $productionarr[$flrid][$lkey]['productionqty'];
						$bg_color = ($mtdavgeff<72) ? "red" : "";
						?>
						<tr bgcolor="<?=$nbgcolor; ?>" onClick="change_color('tr_<?=$n; ?>','<?=$nbgcolor; ?>');" id="tr_1nd<?=$n; ?>" >
							<td width="20" align="center"><?=$n; ?></td>
							<td width="90"><p><?=$floor_library[$flrid]; ?></p></td>
							<td width="90"><p><?=$sewing_line; ?></p></td>
							<td bgcolor="<?=$bg_color;?>" width="90" align="right"><?=number_format($mtdavgeff,2); ?></td>
							<td width="90" align="right"><?=number_format($productionqty,1); ?></td>
						</tr>
						<?	
						$n++;
					}
				}
			?>
		</table>
	</fieldset>
	<?
}
?>
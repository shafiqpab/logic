<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );     	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo $datediff;
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$cbo_company";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.serving_company=$cbo_working_company";
	
	$company_library=return_library_array( "select id,company_name from lib_company where company_id=$cbo_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	
	
        
	if($type==3){
		if($db_type==0)
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
		}
			
		$sql_floor=sql_select("Select a.id, a.floor_name from  lib_prod_floor a where a.location_id=$cbo_location $cbo_company_cond and a.status_active=1 and a.is_deleted=0 order by a.floor_name ");
      $sql_floor_cutting=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=1 and a.status_active=1 and a.is_deleted=0 $production_date    group by a.floor_id order by  a.floor_id ");

        $sql_floor_sewing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=5 and a.status_active=1 and a.is_deleted=0 $production_date   group by a.floor_id order by  a.floor_id ");

        $sql_floor_finishing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=8 and a.status_active=1 and a.is_deleted=0  $production_date   group by a.floor_id order by  a.floor_id ");
			
			
			
			
			
			$count_data=count($sql_floor_cutting)+count($sql_floor_finishing)+count($sql_floor_sewing);
			//echo $count_data;die;
	
			 //$count_hd=count($sql_floor)+1;
			$count_hd=count($sql_floor_cutting)+count($sql_floor_finishing)+count($sql_floor_sewing)+1;
			$width_hd=$count_hd*80;
			$count_cutting=count($sql_floor_cutting)+1;
			$width_cutting=$count_cutting*80;
	
			$count_sewing=count($sql_floor_sewing)+1;
			$width_sewing=$count_sewing*80;
	
			$count_finishing=count($sql_floor_finishing)+1;
			$width_finishing=$count_finishing*80;
	
			$table_width=90+($count_data*100);
		ob_start();	
		//$table_width=90+($datediff*160);
	?>
		<div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<tr>
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">
				   <? 
				   if ($cbo_company!=0){echo ' Company Name:' .$company_library[$cbo_company];} else{echo ' Working Company Name:' .$company_library[$cbo_working_company];}
				   ?>
					</strong>
				   </td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
				</tr>
				<tr>  
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
				</tr>  
			</table>
			<?
			
			if ($cbo_location==0) $location_id =""; else $location_id =" and a.location=$cbo_location ";
			
			
	
			$sql_result="SELECT a.id, a.production_type, a.floor_id, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,5,8) and  b.production_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond $company_working_cond $location_id $production_date";
			//echo $sql_result;	
			$sql_dtls=sql_select($sql_result);
			$floor_qnty=array();
			
			foreach ( $sql_dtls as $row )
			{
				$floor_qnty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]][$row[csf("production_type")]] +=$row[csf("production_qnty")];
			}
			$head_arr=array(1=>"Cutting Production",5=>"Sewing Production",8=>"Finishing Production");
			$unit_wise_total_array=array();
			$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
			?>
			<div align="center" style="height:auto;">
			<table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th style="word-break: break-all;word-wrap: break-word;" width="90" rowspan="2" >Date</th>
						<?
						 foreach ( $head_arr as $head=>$headval )
						 {
							if($head==1)
							{
	
	
							?>
								<th  style="word-break: break-all;word-wrap: break-word;"  width="<? echo $width_cutting; ?>" colspan="<? echo $count_cutting; ?>"><? echo $headval; ?></th>
							<?
							 }
	
							else if($head==5)
							{
	
	
							?>
								<th  style="word-break: break-all;word-wrap: break-word;"  width="<? echo $width_sewing; ?>" colspan="<? echo $count_sewing; ?>"><? echo $headval; ?></th>
							<?
							 }
	
							 else
							{
	
	
							?>
								<th  style="word-break: break-all;word-wrap: break-word;"   width="<? echo $width_finishing; ?>" colspan="<? echo $count_finishing; ?>"><? echo $headval; ?></th>
							<?
							 }
						 }
						?>
					</tr>
				   <tr>
						<?
						foreach ( $head_arr as $head=>$headval )
						{
							if($head==1)
							{
								foreach ( $sql_floor_cutting as $rows )
								{
									?>
										<th  style="word-break: break-all;word-wrap: break-word;"  width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
									<?
								}
							}
	
							else if($head==5)
							{
								foreach ( $sql_floor_sewing as $rows )
								{
									?>
										<th  style="word-break: break-all;word-wrap: break-word;"  width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
									<?
								}
							}
	
	
							else if($head==8)
							{
								foreach ( $sql_floor_finishing as $rows )
								{
									?>
										<th  style="word-break: break-all;word-wrap: break-word;"  width="80" colspan=""><? echo $floor_arr[$rows[csf("floor_id")]]; ?></th>
									<?
								}
							}
	
	
							?>
							<th width="80"  style="word-break: break-all;word-wrap: break-word;"   colspan="">Total</th>
						<?
						}
					   ?>
				   </tr>
				</thead>
			</table>
			</div>
			<div align="center" style="max-height:300px" id="scroll_body2">
			<table align="center" cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
				<?
				$value_define=array();
				for($j=0;$j<$datediff;$j++)
				{
					if ($j%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					$date_data_array=array();
					$newdate =add_date(str_replace("'","",$txt_date_from),$j);
					$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
					$date_data_array[$j]=$newdate;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
					<td  style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo change_date_format($newdate); ?></td>
					<?
					foreach ( $head_arr as $head=>$headval )
					{
						$site_tot_qnty='';
						if($head==1)
						{
							foreach ( $sql_floor_cutting as $rows )
							{ 
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
								<?
								 $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
								 $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
								 if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;
								 
							}
							?>
								<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
							<?
						}
						else if($head==5)
						{
							foreach ( $sql_floor_sewing as $rows )
							{ 
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"   width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
								<?
								 $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
								 $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
								 if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;
								 
							}
							?>
								<td  style="word-break: break-all;word-wrap: break-word;"   width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
							<?
						}
	
						else if($head==8)
						{
							foreach ( $sql_floor_finishing as $rows )
							{ 
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"   width="80" align="right"><?  echo number_format($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head],0); ?></td>
								<?
								 $site_tot_qnty+=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
								 $unit_wise_total_array[$head][$rows[csf("floor_id")]] +=$floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head];
								 if($floor_qnty[$date_data_array[$j]][$rows[csf("floor_id")]][$head]!="")   $value_define[$head][$rows[csf("floor_id")]] +=1;
								 
							}
							?>
								<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format($site_tot_qnty,0); ?></strong></td>
							<?
						}
	 
					}
					?>
				</tr>
				<?
				}
				?>
				<tr bgcolor="#dddddd">
					<td  style="word-break: break-all;word-wrap: break-word;"  align="center" width="90"><strong>Total : </strong></td>
					<?
					foreach ( $head_arr as $head=>$headval )
					{
						$grand_total_tot='';
						if($head==1)
						{
							foreach ( $sql_floor_cutting as $rows )
							{
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
								<?
								$grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
							}
						}
	
					   else if($head==5)
						{
							foreach ( $sql_floor_sewing as $rows )
							{
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
								<?
								$grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
							}
						}
	
	
						else if($head==8)
						{
							foreach ( $sql_floor_finishing as $rows )
							{
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format($unit_wise_total_array[$head][$rows[csf("floor_id")]],0); ?></strong></td>
								<?
								$grand_total_tot+=$unit_wise_total_array[$head][$rows[csf("floor_id")]];
							}
						}
						?>
						<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format($grand_total_tot,0); ?></strong></td>
						<?
					}
					?>
				</tr>
				 
				<tr bgcolor="#dddddd">
					<td  style="word-break: break-all;word-wrap: break-word;"  align="center" width="90"><strong>Avg : </strong></td>
					<?
					$m=1;
					foreach ( $head_arr as $head=>$headval )
					{
						$avg='';
						$avg_tot='';
						if($head==1)
						{
							foreach ( $sql_floor_cutting as $rows )
							{
								$avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
								//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
								<?
								$avg_tot+=$avg;
							}
						}
	
						else if($head==5)
						{
							foreach ( $sql_floor_sewing as $rows )
							{
								$avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
								//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
								<?
								$avg_tot+=$avg;
							}
						}
	
	
						else if($head==8)
						{
							foreach ( $sql_floor_finishing as $rows )
							{
								$avg=$unit_wise_total_array[$head][$rows[csf("floor_id")]]/ $value_define[$head][$rows[csf("floor_id")]];
								//$avg=$unit_wise_total_array[$head][$rows[csf("id")]]/$value_define[$head][$rows[csf("id")]];
								?>
									<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><? echo number_format( $avg,2); ?></strong></td>
								<?
								$avg_tot+=$avg;
							}
						}
						?>
						<td  style="word-break: break-all;word-wrap: break-word;"  width="80" align="right"><strong><?  echo number_format($avg_tot,2); ?></strong></td>
					<?
					}
					?>
				</tr> 
			</table>
		</div>
		</div>
	<?    
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
		echo "$total_data####$filename";
		exit();
		}
	elseif($type==4){
			
		
		/*$sql_floor_sewing=sql_select("SELECT a.floor_id,b.floor_name from  pro_garments_production_mst a,lib_prod_floor b where b.id=a.floor_id and a.location=$cbo_location $cbo_company_cond and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and a.floor_id !=0 group by a.floor_id,b.floor_name order by b.floor_name ");*/
			
			
			
			if ($cbo_location==0){
				$location_id =""; 
				$location_con =""; 
			}else {
				$location_id =" and a.location=$cbo_location ";
				$location_con =" and  location_id=$cbo_location ";
			}
			if($db_type==0)
			{
				if( $date_from==0 && $date_to==0 ){
					$production_date="";$applying_period_date="";
				}
				else {
					$production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
					$applying_period_date= " and DATE_FORMAT(applying_period_date,'%m-%Y') between '".change_date_format($date_from,'yyyy-mm')."' and '".change_date_format($date_to,'yyyy-mm')."'";
					$actual_resource_date= " and b.pr_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
				}
			}
			else
			{
				if( $date_from==0 && $date_to==0 ){
					$production_date="";$applying_period_date="";
				}
				else{
					$production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
					$applying_period_date= " and TO_CHAR(applying_period_date,'Mon-YYYY') between '".date("M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("M-Y",strtotime(str_replace("'","",$date_to)))."'";
					$actual_resource_date= " and b.pr_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
				}
			}
			
			
			$sql_result="Select a.id, a.floor_id, a.production_date, b.production_qnty,(b.production_qnty*c.order_rate) as production_val from pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=a.po_break_down_id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_cond $company_working_cond $location_id $production_date";
			$sql_dtls=sql_select($sql_result);
			$pro_qty=array();
			foreach ( $sql_dtls as $row )
			{
				$pro_qty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]]['val'] +=$row[csf("production_val")];
				$pro_qty[change_date_format($row[csf("production_date")],'','',1)][$row[csf("floor_id")]]['qty'] +=$row[csf("production_qnty")];
			}
			
		
		$company_id=($cbo_company)?$cbo_company:$cbo_working_company;
		
		
			
		$sql_cost_per_minute_arr = sql_select("select applying_period_date,cost_per_minute from  lib_standard_cm_entry where company_id=$company_id $applying_period_date and status_active=1 and is_deleted=0");
		foreach ( $sql_cost_per_minute_arr as $row )
		{
			$key=date("m-Y",strtotime(str_replace("'","",$row[csf("applying_period_date")])));
			$cost_per_minute_arr[$key]=$row[csf("cost_per_minute")];

		}

		
		$target_data_arr=sql_select("select b.pr_date,b.target_per_hour,b.working_hour,a.floor_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.location_id=$cbo_location  $actual_resource_date and a.is_deleted=0 and b.is_deleted=0");
		$target_qty=array();
		foreach ( $target_data_arr as $row )
		{
			$target_qty[change_date_format($row[csf("pr_date")],'','',1)][$row[csf("floor_id")]] +=($row[csf("target_per_hour")]*$row[csf("working_hour")]);
		}
		
		$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor where company_id=$company_id and production_process=5 $location_con and is_deleted=0 and is_deleted=0","id","floor_name");
		
		$table_width=(count($floor_arr)*320)+80+320;
		$colspan_hd=count($floor_arr)+2;
		
			
		ob_start();	
	?>
		
        <div style="width:<? echo $table_width+25; ?>; margin:0 auto; padding:0 5px;">
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
				<tr>
				   <td align="center" width="100%" colspan="<? echo $colspan_hd; ?>" class="form_caption" >
                   <strong style="font-size:18px">
				   <? echo $company_library[$cbo_company?$cbo_company:$cbo_working_company]; ?>
					</strong>
				   </td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px">Floor Wise Production Summary Report</strong></td>
				</tr>
				<tr>  
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
				</tr>  
			</table>
			<?
			
			if ($cbo_location==0) $location_id =""; else $location_id =" and a.location=$cbo_location ";
			if($db_type==0)
			{
				if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				if( $date_from==0 && $date_to==0 ) $production_date=""; else $production_date= " and a.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
			}
			
			?>

			<table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th width="80" rowspan="2" >Date</th>
						<?
						 foreach ( $floor_arr as $floor_id=>$floor_name )
						 {
							?>
								<th colspan="4"><? echo $floor_name; ?></th>
							<?
	
						 }
						?>
                        <th colspan="4">ALL Unit Total</th>
					</tr>
					<tr>
						<?
						 foreach ( $floor_arr as $floor_id=>$floor_name )
						 {
							?>
								<th width="80">Target in pcs</th>
								<th width="80">Production in pcs</th>
								<th width="80">Total CM Value</th>
								<th width="80">Total Production Value </th>
							<?
	
						 }
						?>
                            <th width="80">Target in pcs</th>
                            <th width="80">Production in pcs</th>
                            <th width="80">Total CM Value</th>
                            <th width="80">Total Production Value </th>
					</tr>
				</thead>
			</table>
            <div style="max-height:300px; float:left; overflow-y:scroll; width:<? echo $table_width+20; ?>px;">
			<table cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
				<?
				$value_define=array();
				for($j=0;$j<$datediff;$j++)
				{
					$bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";  
						
					$date_data_array=array();
					$newdate =add_date(str_replace("'","",$txt_date_from),$j);
					$newdate=date("d-M-Y",strtotime(str_replace("'","",$newdate)));
					$monthYearDate=date("m-Y",strtotime(str_replace("'","",$newdate)));
					$date_data_array[$j]=$newdate;
					
					
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
					<td width="79" align="center"><? echo change_date_format($newdate); ?></td>
					<?
					foreach ( $floor_arr as $floor_id=>$floor_name )
					{
					
					$TotalCMValue=$pro_qty[$newdate][$floor_id]['qty']*$cost_per_minute_arr[$monthYearDate];
					//Date wise total-----------------------------------------
						$DateTargetInPcs[$newdate]+=$target_qty[$newdate][$floor_id];
						$DateProductionInPcs[$newdate]+=$pro_qty[$newdate][$floor_id]['qty'];
						$DateTotalCMValue[$newdate]+=$TotalCMValue;
						$DateTotalProductionValue[$newdate]+=$pro_qty[$newdate][$floor_id]['val'];
					//Floor wise total-----------------------------------------
						$FloorTargetInPcs[$floor_id]+=$target_qty[$newdate][$floor_id];
						$FloorProductionInPcs[$floor_id]+=$pro_qty[$newdate][$floor_id]['qty'];
						$FloorTotalCMValue[$floor_id]+=$TotalCMValue;
						$FloorTotalProductionValue[$floor_id]+=$pro_qty[$newdate][$floor_id]['val'];
						
						?>
						<td width="80" align="right"><? echo number_format($target_qty[$newdate][$floor_id],0); ?></td>
						<td width="80" align="right"><? echo number_format($pro_qty[$newdate][$floor_id]['qty'],0); ?></td>
						<td width="80" align="right"><? echo number_format($TotalCMValue,2) ; ?></td>
						<td width="80" align="right"><? echo number_format($pro_qty[$newdate][$floor_id]['val'],0); ?></td>
						<?
					}
					?>
						<td width="80" align="right"><? echo number_format($DateTargetInPcs[$newdate],0); ?></td>
						<td width="80" align="right"><? echo number_format($DateProductionInPcs[$newdate],0); ?></td>
						<td width="80" align="right"><? echo number_format($DateTotalCMValue[$newdate],2); ?></td>
						<td width="80" align="right"><? echo number_format($DateTotalProductionValue[$newdate],0); ?></td>
                    
				</tr>
				<?
				}
				?>
			</table>
		</div>
			<table cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
                <tfoot>
                    <tr>
						
                        <th width="80">&nbsp;</th>
						<?
						 foreach ( $floor_arr as $floor_id=>$floor_name )
						 {
							?>
                            <th width="80" align="right"><? echo number_format($FloorTargetInPcs[$floor_id],0); ?></th>
                            <th width="80" align="right"><? echo number_format($FloorProductionInPcs[$floor_id],0); ?></th>
                            <th width="80" align="right"><? echo number_format($FloorTotalCMValue[$floor_id],2); ?></th>
                            <th width="80" align="right"><? echo number_format($FloorTotalProductionValue[$floor_id],0); ?></th>
							<?
	
						 }
						?>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                    </tr>
                </tfoot>
			</table>
        
	</div>
        
        
	<?    
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
		echo "$total_data####$filename";
		exit();
	}
}
?>

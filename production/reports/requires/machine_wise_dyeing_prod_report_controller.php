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
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_wise_dyeing_prod_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";
	
	echo create_drop_down( "cbo_floor_id", 140, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_service_company=str_replace("'","",$cbo_service_company_id);
	if($cbo_company!=0) $company_cond=" and a.company_id=$cbo_company"; else $company_cond="";
	if($cbo_service_company!=0) $company_service_cond=" and a.service_company=$cbo_service_company"; else $company_service_cond="";
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
		
	$table_width=425+($datediff*70);
	ob_start();	
?>
	<div>
        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>
        </table>
        <?
	
	$machine_name=str_replace("'","",$txt_machine_id);
	$floor_name=str_replace("'","",$cbo_floor_id);
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	//if (str_replace("'","",$cbo_location_id)==0) $location_id=""; else $location_id=" and location_id=$cbo_location_id";
	if (str_replace("'","",$txt_machine_id)==0 || str_replace("'","",$txt_machine_id)=='') $machine_id=""; else $machine_id=" and id in ( $machine_name )";
	if ($floor_name==0 || $floor_name=='') $floor_id=""; else $floor_id=" and floor_id in ( $floor_name )";
	

	//var_dump ($machin_arr);
	
	$machineArr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	?>
	<div>
	<table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
		<thead>
			<tr>
				<th width="35">SL</th>
				<th width="80">Machine No</th>
				<th width="70">Floor</th>
				<th width="70">Capacity</th>
				<?
				//$date_data_array=array();
				for($j=0;$j<$datediff;$j++)
				{
					$newDate =add_date(str_replace("'","",$txt_date_from),$j);
					//$full_date=change_date_format($newdate);
					$days_months=explode('-',$newDate);
				?>
					<th width="70"><p><? echo date("d-M",strtotime($newDate)); ?></p></th>
				<?
				}  
				?>
				 <th width="80">Total</th>
				 <th width="80">Avg/Day</th>
		   </tr>
		</thead>
	</table>
    <div style="width:<? echo $table_width+17; ?>px; overflow-y:scroll; max-height:300px;" id="scroll_body">
	<table align="center" cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
	<? 
	//$cbo_location=str_replace("'","",$cbo_location_id);
	$machine=str_replace("'","",$txt_machine_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$floor_name=str_replace("'","",$cbo_floor_id);
	$result_id=str_replace("'","",$cbo_result_id);
	//if ($cbo_location==0) $location_id =""; else $location_id =" and a.location_id=$cbo_location ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and a.machine_id in ( $machine ) ";
	//if ($machine=="") $machine_no_sub =""; else $machine_no_sub =" and b.machine_id in ( $machine ) ";
	if( $date_from==0 && $date_to==0 ) $production_date_cond=""; else $production_date_cond= " and a.process_end_date between ".$txt_date_from." and ".$txt_date_to."";
	//if( $date_from==0 && $date_to==0 ) $receive_date_sub=""; else $receive_date_sub= " and a.process_end_date between ".$txt_date_from." and ".$txt_date_to."";

	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and a.floor_id=$floor_name";
	if ($result_id==0 || $result_id=='') $result_id_cond=""; else $result_id_cond=" and a.result=$result_id";
	
	/*$sql_result="Select a.receive_date, b.machine_no_id, b.floor_id, sum(b.grey_receive_qnty) as grey_receive_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_no_id!=0 and a.company_id=$cbo_company $location_id $machine_no $receive_date group by a.receive_date, b.machine_no_id, b.floor_id ";
	//echo $sql_result;
	$sql_dtls=sql_select($sql_result);
	$date_data_arr=array();
	$date_total_arr=array();
	foreach ($sql_dtls as $row)
	{
		$date_data_arr[$row[csf('machine_no_id')]][change_date_format($row[csf('receive_date')],'','',1)]=$row[csf('grey_receive_qnty')];
		$date_data_arr[$row[csf('machine_no_id')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
		$date_total_arr[change_date_format($row[csf('receive_date')],'','',1)]+=$row[csf('grey_receive_qnty')];
	}*/
	
	$sql_result="Select a.company_id,a.process_end_date, a.machine_id, a.result,a.incomplete_result, a.floor_id, sum(c.batch_qnty) as production_qty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id  and a.load_unload_id=2 and a.entry_form in (35,38) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $company_cond $company_service_cond $floor_id_cond $production_date_cond $machine_cond $result_id_cond group by a.company_id,a.process_end_date, a.machine_id, a.result, a.floor_id, a.incomplete_result order by a.process_end_date, a.machine_id,a.result";
	//echo $sql_result;
	$sql_dtls=sql_select($sql_result);
	$date_data_arr=array();
	$date_total_arr=array();
	$company_arr=array();
	foreach ($sql_dtls as $row)
	{
		
		 
		$date_data_arr[$row[csf('result')]][$row[csf('machine_id')]][change_date_format($row[csf('process_end_date')],'','',1)]+=$row[csf('production_qty')];
		$date_data_arr[$row[csf('result')]][$row[csf('machine_id')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
		$date_total_arr[$row[csf('result')]][change_date_format($row[csf('process_end_date')],'','',1)]+=$row[csf('production_qty')];
		$dyeing_result_arr[]=$row[csf('result')];
			
		
		$company_arr[$row[csf('company_id')]]=$row[csf('company_id')];
	}
	$dyeing_result_uni=array_unique($dyeing_result_arr);
	
	
	
		$sql_machine_dtls="Select id, machine_no, brand, origin, prod_capacity, dia_width, floor_id from lib_machine_name where category_id=2 and company_id in (".implode(",",$company_arr).") and status_active=1 and is_deleted=0 $location_id $floor_id $machine_id order by seq_no";
	//echo $sql_machine_dtls;
	$sql_machine=sql_select($sql_machine_dtls);
	$count_data=count($sql_machine);
	//echo $count_data;
	$machin_arr=array();
	$machine_dtls_array=array();
	
	foreach ( $sql_machine as $row )
	{
		$machin_arr[$row[csf('id')]]=$row[csf('id')];
		$machine_dtls_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
		$machine_dtls_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
		$machine_dtls_array[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
		$machine_dtls_array[$row[csf('id')]]['prod_capacity']=$row[csf('prod_capacity')];
	}
	
	$idle_machine_array=array();
	$idol_sql="select id, machine_entry_tbl_id, machine_no, from_date, from_hour, from_minute, to_date, to_hour, to_minute, machine_idle_cause, remarks from pro_cause_of_machine_idle where status_active=1 and is_deleted=0 and machine_idle_cause in (1,2,3,6,7,8)";
	$idol_sql_result=sql_select($idol_sql); $timeDiffstart='';
	foreach ($idol_sql_result as $row)
	{
		$from_date=change_date_format($row[csf('from_date')],'','',1);	$from_hour=$row[csf('from_hour')]; $from_minute=$row[csf('from_minute')];
		$to_date=change_date_format($row[csf('to_date')],'','',1);	$to_hour=$row[csf('to_hour')]; $to_minute=$row[csf('to_minute')];
		
		$start_time='';
		$start_time=$from_hour.':'.$from_minute.':'.'00';
		
		$end_time='';
		$end_time=$to_hour.':'.$to_minute.':'.'00';
		$datediff_n = datediff( 'd', $from_date, $to_date);
		if ($datediff_n==1)
		{
			$p_time=$end_time;
			$timeDiffstart=datediff(n,$start_time,$p_time);
			$timeDiffend=datediff(n,$p_time,$start_time);
			//echo $timeDiffstart.'kk<br>';
		}
		else
		{
			$p_time="00:00:00";
			$timeDiffstart=datediff(n,$p_time,$start_time);
			$timeDiffend=datediff(n,$p_time,$end_time);
		}
		if ($datediff_n==1)
		{
			$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$from_date]=$timeDiffstart;
		}
		else
		{	
			for($k=0; $k<$datediff_n; $k++)
			{
				$newdate_n =change_date_format(add_date(str_replace("'","",$from_date),$k),'','',1);
				//echo $to_date.'=='.$newdate_n.'<br>';
				if($from_date==$newdate_n)
				{
					$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$newdate_n]=$timeDiffstart;
				}
				else if($to_date!=$newdate_n)
				{
					$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$newdate_n]=1440;
				}
				else if($to_date==$newdate_n)
				{
					$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$newdate_n]=$timeDiffend;
				}
			}
		}
	}
	//var_dump($idle_machine_array);

	$i=1; $k=1;
	$avg_capacity=0;
	$avg_date_capacity=array();
	$total_qty_date=array();
	$date_total_arr_all=array();
	$tot_count=0;
	$machine_wise_capacity_tot=array();
	$machine_wise_capacity=array();
	$total_capacity_loss=array();
	$result_arr=array();
	foreach($dyeing_result_uni as $res_id)
	{
		if (!in_array($res_id,$result_arr) )
		{
			if($k!=1)
			{ 	
			?>
                <tr class="tbl_bottom">
                    <td colspan="3" align="right"><strong>Sub. Production Total</strong></td>
                    <td>&nbsp;</td>
                    <?
                    for($j=0;$j<$datediff;$j++)
                    {
                        $newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
                        //$date_data_array[$newdate]=$newdate;
                        ?>
                        <td align="right"><? echo number_format($date_total_arr_all[$newdate],2); ?>&nbsp;</td>
                        <?
                    }
                    ?>
                    <td align="right"><strong><? echo number_format($grand_tot_qnty,2); ?></strong>&nbsp;</td>
                    <td align="right"><strong><? //echo number_format($tot_avg_day,2); ?></strong>&nbsp;</td>
                </tr>               
				<?
				for($j=0;$j<$datediff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
					unset($date_total_arr_all[$newdate]);
				}
				unset($grand_tot_qnty);
				unset($tot_lc_noncash);
			}
			?>
			<tr bgcolor="#EFEFEF">
				<td colspan="<? echo $j+6; ?>" align="left" ><b>Result : <? echo $dyeing_result[$res_id]; ?></b></td>
			</tr>
			<?
			$result_arr[]=$res_id;            
			$k++;
		}		
		
		foreach ($sql_machine as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$machine_id=$row[csf('id')];
			$machine_no=$row[csf('machine_no')];
			$machine_dia=$row[csf('dia_width')];
			$machine_capacity=$row[csf('prod_capacity')];
			$machine_floor=$row[csf('floor_id')];
			//echo $machine_no;
			$date_data_array=array();
			$tot_quenty=0;
			$avg_production=0;
			$avg_day=0;
			$newdate =add_date(str_replace("'","",$txt_date_from),$j);
			//$date_data_array[$j]=$newdate;
			//asort($machine_no);
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="height:35px;" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="35"><? echo $i; ?></td>
				<td width="80"><p><? echo $machine_no;?></p></td>
				<td width="70"><p><? echo $floor_library[$machine_floor]; ?></p></td>
				<td width="70" align="right"><? echo $machine_capacity; $tot_capacity+=$machine_capacity; if($machine_capacity>0) $avg_capacity++; ?>&nbsp;</td>
				<?
				for($j=0;$j<$datediff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
					if ($date_data_arr[$res_id][$machine_id][$newdate]=="")
						$tdcolor="#FF000B";
					else if ($date_data_arr[$res_id][$machine_id][$newdate]<$machine_capacity)
						$tdcolor="#FFF000";
					else if ($date_data_arr[$res_id][$machine_id][$newdate]>=$machine_capacity)
						$tdcolor="#009933";
					
						$total_qty_date[$res_id][$machine_id][$newdate]=$date_data_arr[$res_id][$machine_id][$newdate];
					
					?>
						<td width="70" bgcolor="<? echo $tdcolor; ?>" align="right" id="date_data_arr" ><a href="##" onclick="openmypage_idle('<? echo $machine_id; ?>','<? echo $newdate;?>','idle_for');" >
						<? 
						$machine_wise_capacity[$machine_id][$newdate]=(($machine_capacity/24)/60)*($idle_machine_array[$machine_id][$newdate]);
						$machine_wise_capacity_tot[$machine_id][$newdate]=$machine_capacity-$machine_wise_capacity[$machine_id][$newdate];
						
						if ($total_qty_date[$res_id][$machine_id][$newdate]<$machine_capacity && $total_qty_date[$res_id][$machine_id][$newdate]!='' )
						{
							echo number_format($total_qty_date[$res_id][$machine_id][$newdate],2).'&nbsp;<br><i>'.number_format((($total_qty_date[$res_id][$machine_id][$newdate]/$machine_capacity)*100),2).'%</i>'; //.'=='.$machine_wise_capacity_tot[$machine_id][$newdate]
						}
						else 
						{
							echo number_format($total_qty_date[$res_id][$machine_id][$newdate],2).'&nbsp;<br><i>'.number_format((($total_qty_date[$res_id][$machine_id][$newdate]/$machine_capacity)*100),2).'%</i>'; 
						}
						$date_total_arr_all[$newdate]+=$total_qty_date[$res_id][$machine_id][$newdate];
						$grand_total_arr_all[$newdate]+=$total_qty_date[$res_id][$machine_id][$newdate];
						 if ($total_qty_date[$res_id][$machine_id][$newdate]>0) $avg_production++; ?>&nbsp;</a></td>
					<?
					$tot_quenty+=$total_qty_date[$res_id][$machine_id][$newdate];
					$avg_day=$tot_quenty/$avg_production;
					$total_capacity_loss[$newdate]+=$machine_wise_capacity_tot[$machine_id][$newdate];
	
					if ($avg_day=="")
						$tdcolor="#FF0000";
					else if ($avg_day<$machine_capacity)
						$tdcolor="#FFF000";
					else if ($avg_day>=$machine_capacity)
						$tdcolor="#009933";
						
				 if ($total_qty_date[$machine_id][$newdate]>0)$avg_date_capacity[$newdate]++;  
				} 
				?>
				<td width="80" align="right"><? echo number_format($tot_quenty,2); if ($tot_quenty>0) $tot_count++; ?>&nbsp;</td>
				<td width="80" align="right" bgcolor="<? echo $tdcolor; ?>" ><? echo number_format($avg_day,2).'&nbsp;<br><i>'.number_format((($avg_day/$machine_capacity)*100),2).'%</i>'; $tot_avg_day+=$avg_day; ?>&nbsp;</td>
			</tr>
			 <?
			$i++;
			 $grand_tot_qnty+=$tot_quenty;
		}
	}
	?>
    <tfoot>
        <tr class="tbl_bottom">
        	<td colspan="3" align="right"><strong>Sub. Production Total</strong></td>
            <td align="right">&nbsp;</td>
			<?
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				//$date_data_array[$newdate]=$newdate;
				?>
				<td align="right"><? echo number_format($date_total_arr_all[$newdate],2); ?>&nbsp;</td>
                <?
			}
			?>
            <td align="right"><strong><? echo number_format($grand_tot_qnty,2); ?></strong>&nbsp;</td>
            <td align="right"><strong><? //echo number_format($tot_avg_day,2); ?></strong>&nbsp;</td>
        </tr>     
    	<tr>
        	<td colspan="3" align="right"><strong>Production Total</strong></td>
            <td align="right">&nbsp;</td>
			<?
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				//$date_data_array[$newdate]=$newdate;
				?>
				<td align="right"><? echo number_format($grand_total_arr_all[$newdate],2); ?>&nbsp;</td>
                <?
			}
			?>
            <td align="right"><strong><? echo number_format($grand_tot_qnty,2); ?></strong>&nbsp;</td>
            <td align="right"><strong><? //echo number_format($tot_avg_day,2); ?></strong>&nbsp;</td>
        </tr>
        <?
		if(str_replace("'","",$cbo_result_id)==1)
		{
		?>
        <tr>
        	<td colspan="3" align="right"><strong>Capacity Total</strong></td>
            <td align="right"><? echo number_format($tot_capacity,0); ?>&nbsp;</td>
			<?
			$day_wise_capacity_tot=array();
			$day_wise_capacity=array();
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				//$date_data_array[$newdate]=$newdate;
				$date_total_arr_all[$newdate]=$date_total_arr[$newdate];
				
				$day_wise_capacity[$newdate]=(($tot_capacity/24)/60)*($idle_machine_array[$newdate]);
				$day_wise_capacity_tot[$newdate]=$tot_capacity-$day_wise_capacity[$newdate];
				?>
				<td align="right"><? echo number_format($total_capacity_loss[$newdate],2); $total_capacity+=$total_capacity_loss[$newdate]; ?>&nbsp;</td>
                <?
			}
			?>
            <td align="right"><strong><? echo number_format($total_capacity,2); ?></strong>&nbsp;</td>
            <td align="right"><strong><? //echo number_format($tot_avg_day,2); ?></strong>&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3" align="right"><strong>Achievement % On Day Capacity</strong></td>
            <td align="right"><strong><? //echo number_format($tot_capacity/$avg_capacity,2); ?></strong></td>
			<?
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				$date_data_array[$newdate]=$newdate;
				if (($grand_total_arr_all[$newdate]/$avg_date_capacity[$newdate])=="")
					$tdcolor="#FF0000";
				else if (($grand_total_arr_all[$newdate]/$avg_date_capacity[$newdate])<($tot_capacity/$avg_capacity))
					$tdcolor="#FFF000";
				else if (($grand_total_arr_all[$newdate]/$avg_date_capacity[$newdate])>=($tot_capacity/$avg_capacity))
					$tdcolor="#009933";
				?>
				<td align="right" bgcolor="<? echo $tdcolor; ?>"><? $ach_day_capacity=($grand_total_arr_all[$newdate]/$total_capacity_loss[$newdate])*100; echo number_format($ach_day_capacity,2).' %'; ?>&nbsp;</td>
                <?
			}
			?>
            <td align="right" bgcolor="<? echo $tdcolor;?>"><strong><? $tot_per=($grand_tot_qnty/$total_capacity)*100; echo number_format($tot_per,2).'%';  ?></strong>&nbsp;</td>
            <td align="right"><strong><? //$tot_per=($tot_avg_day/$tot_capacity)*100; echo number_format($tot_per,2).'%'; ?></strong>&nbsp;</td>
        </tr>
	   	<tr>
        	<td colspan="3" align="right"><strong>Opportunity (Loss)/Gain</strong></td>
            <td align="right"><strong><? //echo number_format($tot_capacity/$avg_capacity,2); ?></strong></td>
			<?
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				//$date_data_array[$newdate]=$newdate;
				if (($grand_total_arr_all[$newdate]/$avg_date_capacity[$newdate])=="")
					$tdcolor="#FF0000";
				else if (($grand_total_arr_all[$newdate]/$avg_date_capacity[$newdate])<($tot_capacity/$avg_capacity))
					$tdcolor="#FFF000";
				else if (($grand_total_arr_all[$newdate]/$avg_date_capacity[$newdate])>=($tot_capacity/$avg_capacity))
					$tdcolor="#009933";
				?>
				<td align="right" bgcolor="<? echo $tdcolor;?>">
				<? $day_capacity=($grand_total_arr_all[$newdate]/$total_capacity_loss[$newdate])*100;
				   $day_cap_per=$day_capacity-100;
				    
				if ($day_capacity<100)
				{ 
					$exp_cap=preg_replace("#\-\#", "() ", $day_cap_per);
					$less_greter='('.$exp_cap[0].')';
				} 
				else 
				{
					$less_greter=$day_cap_per;
				}
				   echo number_format($day_cap_per,2).' %'; 
				?>&nbsp;</td>
                <?
			}
			?>
            <td align="right" bgcolor="<? echo $tdcolor;?>"><strong><? echo number_format($tot_per-100,2).'%'; ?></strong>&nbsp;</td>
            <td align="right"><strong>&nbsp;</strong>&nbsp;</td>
        </tr>
        <?
		}
		?>
     </tfoot>
    </table>
        <div style="font-style:italic; color:#FF0000; font-size:18px; margin-left:20px" ><p><i><b>Note:</b>Machine Capacity has been exploded from total capacity due to production hampered for fowlling reasons: </i></p>
        	<ul>
                <li>Disorder</li>
                <li>Routine Maintenance</li>
                <li>Job Not Available</li>
                <li>Worker Unrest</li>
                <li>Off-Day</li>
                <li>Material Not Available</li>
            </ul>
        </div>
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
	//echo "$total_data####$filename";
	exit();      
}

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	//print_r ($im_data);
?>
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hid_machine_id').val( id );
			$('#hid_machine_name').val( name );
		}
		
		function hidden_field_reset()
		{
			$('#hid_machine_id').val('');
			$('#hid_machine_name').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
    </script>
</head>
<input type="hidden" name="hid_machine_id" id="hid_machine_id" />
<input type="hidden" name="hid_machine_name" id="hid_machine_name" />
<?	
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.company_id=$im_data[0] and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
	//echo  $sql;
	
	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;
	
   exit(); 
}

if($action=="idle_for")
{
	echo load_html_head_contents("Cause of Machine Idle Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	if($db_type==0)
	{
		$cng_date=change_date_format($date,"yyyy-mm-dd", "-",1);
	}
	elseif($db_type==2)
	{
		$cng_date=$date;
	}
	
	$sql= "SELECT id, machine_entry_tbl_id, machine_no, from_date, from_hour, from_minute, to_date, to_hour, to_minute, machine_idle_cause, remarks from  pro_cause_of_machine_idle where machine_entry_tbl_id='$machine_id' and '$cng_date' between from_date and to_date and is_deleted=0 and status_active=1";
	//echo $sql;
	
?>
	<fieldset style="width:550px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">From Date and Time</th>
                <th width="120">To Date and Time</th>
                <th width="100">Cause of Machine Idle</th>
                <th width="140">Remarks</th>
            </thead>
            <tbody>
                <?
                $i=1; $total_qnty=0;
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$from_date=date("Y-m-d",strtotime($row[csf('from_date')]));	$from_hour=$row[csf('from_hour')]; $from_minute=$row[csf('from_minute')];
					$to_date=date("Y-m-d",strtotime($row[csf('to_date')]));	$to_hour=$row[csf('to_hour')]; $to_minute=$row[csf('to_minute')];
					
					$start_time='';
					$start_time=$from_hour.':'.$from_minute;
					
					$end_time='';
					$end_time=$to_hour.':'.$to_minute;
					
					$start_date=change_date_format($from_date)." - ".$start_time;
					$end_date=change_date_format($to_date)." - ".$end_time;
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="120"><p><? echo $start_date; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $end_date; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $cause_type[$row[csf('machine_idle_cause')]]; ?>&nbsp;</p></td>
                        <td width="140"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                    </tr>
                <?
                $i++;
                }
                ?>
                </tbody>
            </table>
	</fieldset>   
	<?	
	exit();
}

?>
<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_lbuyer_id", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company =$data $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
  exit();	 
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_wise_kniting_prod_report_v2_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";
	
	echo create_drop_down( "cbo_floor_id", 140, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_lbuyer_id=str_replace("'","",$cbo_lbuyer_id);
	//$cbo_company='1,2,3,4,5,6,7,8';
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$table_width=585+($datediff*70);
	ob_start();	
	?>
	<div>
    <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
        <tr>
           <td align="center" width="100%" colspan="8" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
        </tr> 
        <tr>  
           <td align="center" width="100%" colspan="8" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo "; From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
        </tr>
    </table>
    <?

	$machine_name=str_replace("'","",$txt_machine_id);
	$floor_name=str_replace("'","",$cbo_floor_id);
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
	if (str_replace("'","",$cbo_location_id)==0) $location_cond=""; else $location_cond=" and location_id=$cbo_location_id";
	if (str_replace("'","",$txt_machine_id)==0 || str_replace("'","",$txt_machine_id)=='') $machine_cond=""; else $machine_cond=" and id in ( $machine_name )";
	if ($floor_name==0 || $floor_name=='') $floor_cond=""; else $floor_cond=" and floor_id in ( $floor_name )";
	

	
	$machineArr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	?>
	<div>
	<table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
		<thead>
			<tr>
				<th width="35">SL</th>
				<th width="70">Floor</th>
				<th width="80">Machine No</th>
				<th >Group</th>
				<th width="60">Mach.Dia/ Gauge</th>
				<th width="70">Capacity</th>
				<?
				for($j=0;$j<$datediff;$j++)
				{
					$newDate =add_date(str_replace("'","",$txt_date_from),$j);
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
	$cbo_location=str_replace("'","",$cbo_location_id);
	$machine=str_replace("'","",$txt_machine_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$floor_name=str_replace("'","",$cbo_floor_id);
	if ($cbo_lbuyer_id==0) $lbuyer_con =""; else $lbuyer_con =" and a.BUYER_ID=$cbo_lbuyer_id ";
	if ($cbo_location==0) $location_id =""; else $location_id =" and a.location_id=$cbo_location ";
	
	if ($machine=="") $machine_no =""; else $machine_no =" and b.machine_no_id in ( $machine ) ";
	if ($machine=="") $machine_no_sub =""; else $machine_no_sub =" and b.machine_id in ( $machine ) ";
	if( $date_from==0 && $date_to==0 ) $receive_date=""; else $receive_date= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
	if( $date_from==0 && $date_to==0 ) $receive_date_sub=""; else $receive_date_sub= " and a.product_date between ".$txt_date_from." and ".$txt_date_to."";

	if ($floor_name==0 || $floor_name=='') $floor_id_val=""; else $floor_id_val=" and b.floor_id=$floor_name";
	
	$sql_result="SELECT a.receive_date, b.machine_no_id, b.floor_id, sum(b.grey_receive_qnty) as grey_receive_qnty, sum(b.grey_receive_qnty_pcs) as grey_receive_qnty_pcs from inv_receive_master a, pro_grey_prod_entry_dtls b 
	where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_no_id!=0 and a.knitting_company in($cbo_company) $lbuyer_con $location_id $machine_no $receive_date group by a.receive_date, b.machine_no_id, b.floor_id ";
	 //echo $sql_result;
	$sql_dtls=sql_select($sql_result);
	$date_data_arr=array();$date_data_arr_pcs=array();
	$date_total_arr=array();
	foreach ($sql_dtls as $row)
	{
		$date_data_arr[$row[csf('machine_no_id')]][change_date_format($row[csf('receive_date')],'','',1)]+=$row[csf('grey_receive_qnty')];
		$date_data_arr_pcs[$row[csf('machine_no_id')]][change_date_format($row[csf('receive_date')],'','',1)]+=$row[csf('grey_receive_qnty_pcs')];
		$date_data_arr[$row[csf('machine_no_id')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
		$date_total_arr[change_date_format($row[csf('receive_date')],'','',1)]+=$row[csf('grey_receive_qnty')];
	}
	$sql_result_subcon="Select a.product_date, b.machine_id, sum(b.product_qnty) as product_qnty from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_id!=0 and a.company_id in($cbo_company) $location_id $machine_no_sub $receive_date_sub group by a.product_date, b.machine_id ";
	
	$sql_dtls_sub=sql_select($sql_result_subcon);
	$date_data_sub_arr=array();
	$date_total_sub_arr=array();
	
	foreach ($sql_dtls_sub as $row)
	{
		$date_data_sub_arr[$row[csf('machine_id')]][change_date_format($row[csf('product_date')],'','',1)]=$row[csf('product_qnty')];
		$date_total_sub_arr[change_date_format($row[csf('product_date')],'','',1)]+=$row[csf('product_qnty')];
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
			$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$from_date]=$timeDiffstart;
		}
		else
		{	
			for($k=0; $k<$datediff_n; $k++)
			{
				$newdate_n =change_date_format(add_date(str_replace("'","",$from_date),$k),'','',1);
				if($from_date==$newdate_n)
				{
					$p_time="23:59:59";
					$timeDiffstart=datediff(n,$p_time,$start_time);
					$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$newdate_n]=$timeDiffstart;
				}
				else if($to_date!=$newdate_n)
				{
					$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$newdate_n]=1440;
				}
				else if($to_date==$newdate_n)
				{
					$p_time="00:00:00";
					$timeDiffend=datediff(n,$p_time,$end_time);
					$idle_machine_array[$row[csf('machine_entry_tbl_id')]][$newdate_n]=$timeDiffend;
				}
			}
		}
	}

	$i=1;
	$avg_capacity=0;
	$avg_date_capacity=array();
	$total_qty_date=array();$total_qty_pcs_date=array();
	$date_total_arr_all=array();$date_total_pcs_arr_all=array();
	$tot_count=0;
	$machine_wise_capacity_tot=array();
	$machine_wise_capacity=array();
	$total_capacity_loss=array();
	$sql_machine_dtls="Select id, MACHINE_GROUP,GAUGE,machine_no, brand, origin, prod_capacity, dia_width, floor_id from lib_machine_name where category_id=1 and company_id in($cbo_company) and status_active=1 and is_deleted=0 $location_cond $floor_cond $machine_cond order by seq_no";
	 //echo $sql_machine_dtls;
	$sql_machine=sql_select($sql_machine_dtls);
	foreach($sql_machine as $row)//$machine_id=>$machine_val 
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
		$machine_id=$row[csf('id')];
		$machine_no=$row[csf('machine_no')];//$machine_dtls_array[$machine_val]['machine_no'];
		$machine_dia=$row[csf('dia_width')];//$machine_dtls_array[$machine_val]['dia_width'];
		$machine_capacity=$row[csf('prod_capacity')];//$machine_dtls_array[$machine_val]['prod_capacity'];
		$machine_floor=$row[csf('floor_id')];//$machine_dtls_array[$machine_val]['floor_id'];
		//echo $machine_no;
		$date_data_array=array();
		$tot_quenty=0;$tot_quenty_pcs=0;
		$avg_production=0;
		$avg_day=0;$avg_day_pcs=0;
		$newdate =add_date(str_replace("'","",$txt_date_from),$j);
		//$date_data_array[$j]=$newdate;
		//asort($machine_no);
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="height:35px;" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			<td width="35"><? echo $i; ?></td>
			<td width="70"><p><? echo $floor_library[$machine_floor]; ?></p></td>
			<td width="80"><p><? echo $machine_no;?></p></td>
			<td><p><? echo $row[MACHINE_GROUP]; ?></p></td>
			<td width="60" title="GAUGE:<?= $row[GAUGE];?>"><p><? echo $machine_dia.', '.$row[GAUGE]; ?></p></td>
			<td width="70" align="right"><? echo $machine_capacity; $tot_capacity+=$machine_capacity; if($machine_capacity>0) $avg_capacity++; ?></td>
			<?

			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				if (($date_data_arr[$machine_id][$newdate]+$date_data_sub_arr[$machine_id][$newdate])=="")
					$tdcolor="#FFA07A";//#FF000B
				else if ($date_data_arr[$machine_id][$newdate]+$date_data_sub_arr[$machine_id][$newdate]<$machine_capacity)
					$tdcolor="#ffff99";//#FFF000
				else if ($date_data_arr[$machine_id][$newdate]+$date_data_sub_arr[$machine_id][$newdate]>=$machine_capacity)
					$tdcolor="#99c299";//#009933
					
				$total_qty_date[$machine_id][$newdate]=$date_data_arr[$machine_id][$newdate]+$date_data_sub_arr[$machine_id][$newdate];
				$total_qty_pcs_date[$machine_id][$newdate]=$date_data_arr_pcs[$machine_id][$newdate];
				?>
					<td width="70" bgcolor="<? echo $tdcolor; ?>" align="right" id="date_data_arr" ><a href="##" onclick="openmypage_idle('<? echo $machine_id; ?>','<? echo $newdate;?>','idle_for');" >
					<? 
					$machine_wise_capacity[$machine_id][$newdate]=(($machine_capacity/24)/60)*($idle_machine_array[$machine_id][$newdate]);
					$machine_wise_capacity_tot[$machine_id][$newdate]=$machine_capacity-$machine_wise_capacity[$machine_id][$newdate];
					
					if ($total_qty_date[$machine_id][$newdate]<$machine_capacity && $total_qty_date[$machine_id][$newdate]!='' )
					{
						echo number_format($total_qty_date[$machine_id][$newdate],2).'<br>'.number_format($total_qty_pcs_date[$machine_id][$newdate],2).'(pcs)<br><i>'.number_format((($total_qty_date[$machine_id][$newdate]/$machine_capacity)*100),2).'%</i>'; //.'=='.$machine_wise_capacity_tot[$machine_id][$newdate]
					}
					else 
					{
						echo number_format($total_qty_date[$machine_id][$newdate],2).'<br>'.number_format($total_qty_pcs_date[$machine_id][$newdate],2).'(pcs)<br><i>'.number_format((($total_qty_date[$machine_id][$newdate]/$machine_capacity)*100),2).'%</i>'; 
					}
					$date_total_arr_all[$newdate]+=$total_qty_date[$machine_id][$newdate];
					$date_total_pcs_arr_all[$newdate]+=$total_qty_pcs_date[$machine_id][$newdate];
					 if ($total_qty_date[$machine_id][$newdate]>0) $avg_production++; ?></a></td>
				<?
				$tot_quenty+=$total_qty_date[$machine_id][$newdate];
				$tot_quenty_pcs+=$total_qty_pcs_date[$machine_id][$newdate];
				$avg_day=$tot_quenty/$avg_production;
				$avg_day_pcs=$tot_quenty_pcs/$avg_production;
				$total_capacity_loss[$newdate]+=$machine_wise_capacity_tot[$machine_id][$newdate];

				if ($avg_day=="")
					$tdcolor="#FFA07A";//#FF000B
				else if ($avg_day<$machine_capacity)
					$tdcolor="#ffff99";//#FFF000
				else if ($avg_day>=$machine_capacity)
					$tdcolor="#99c299";#009933
					
			 	if ($total_qty_date[$machine_id][$newdate]>0)$avg_date_capacity[$newdate]++;  
			} 
			
			$machineQtyArr[$machine_no]+=$tot_quenty;
			$machineQtyPcsArr[$machine_no]+=$tot_quenty_pcs;
			
			?>
            <td width="80" align="right"><? 
            echo number_format($tot_quenty,2).'<br>'.number_format($tot_quenty_pcs,2); 
            if ($tot_quenty>0) $tot_count++; 
            ?></td>
            <td width="80" align="right" bgcolor="<? echo $tdcolor; ?>" ><? echo number_format($avg_day,2).'<br>'.number_format($avg_day_pcs,2).'<br><i>'.number_format((($avg_day/$machine_capacity)*100),2).'%</i>'; $tot_avg_day+=$avg_day; ?></td>
		</tr>
		 <?
		$i++;
		$grand_tot_qnty+=$tot_quenty;
		$grand_tot_qnty_pcs+=$tot_quenty_pcs;
	}
	?>
    <tfoot>
    	<tr>
        	<td colspan="5" align="right"><strong>Production Total</strong></td>
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
        	<td colspan="5" align="right"><strong>Total In Pcs</strong></td>
            <td align="right">&nbsp;</td>
			<?			
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				?>
				<td align="right"><? echo number_format($date_total_pcs_arr_all[$newdate],2); ?>&nbsp;</td>
                <?
			}
			?>
            <td align="right"><strong><? echo number_format($grand_tot_qnty_pcs,2); ?></strong>&nbsp;</td>
            <td align="right"><strong></strong>&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="5" align="right"><strong>Capacity Total</strong></td>
            <td align="right"><? echo number_format($tot_capacity,0); ?>&nbsp;</td>
			<?
			$day_wise_capacity_tot=array();
			$day_wise_capacity=array();
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				//$date_data_array[$newdate]=$newdate;
				$date_total_arr_all[$newdate]=$date_total_arr[$newdate]+$date_total_sub_arr[$newdate];
				
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
        	<td colspan="5" align="right"><strong>Achievement % On Day Capacity</strong></td>
            <td align="right"><strong><? //echo number_format($tot_capacity/$avg_capacity,2); ?></strong></td>
			<?
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				$date_data_array[$newdate]=$newdate;
				if (($date_total_arr_all[$newdate]/$avg_date_capacity[$newdate])=="")
					$tdcolor="#FFA07A";//#FF0000
				else if (($date_total_arr_all[$newdate]/$avg_date_capacity[$newdate])<($tot_capacity/$avg_capacity))
					$tdcolor="#ffff99";//#FFF000
				else if (($date_total_arr_all[$newdate]/$avg_date_capacity[$newdate])>=($tot_capacity/$avg_capacity))
					$tdcolor="#99c299";//#009933
				?>
				<td align="right" bgcolor="<? echo $tdcolor; ?>"><? $ach_day_capacity=($date_total_arr_all[$newdate]/$total_capacity_loss[$newdate])*100; echo number_format($ach_day_capacity,2).' %'; ?>&nbsp;</td>
                <?
			}
			?>
            <td align="right" bgcolor="<? echo $tdcolor;?>"><strong><? $tot_per=($grand_tot_qnty/$total_capacity)*100; echo number_format($tot_per,2).'%';  ?></strong>&nbsp;</td>
            <td align="right"><strong><? //$tot_per=($tot_avg_day/$tot_capacity)*100; echo number_format($tot_per,2).'%'; ?></strong>&nbsp;</td>
        </tr>
	   	<tr>
        	<td colspan="5" align="right"><strong>Opportunity (Loss)/Gain</strong></td>
            <td align="right"><strong><? //echo number_format($tot_capacity/$avg_capacity,2); ?></strong></td>
			<?
			for($j=0;$j<$datediff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
				//$date_data_array[$newdate]=$newdate;
				if (($date_total_arr_all[$newdate]/$avg_date_capacity[$newdate])=="")
					$tdcolor="#FFA07A";//#FF0000
				else if (($date_total_arr_all[$newdate]/$avg_date_capacity[$newdate])<($tot_capacity/$avg_capacity))
					$tdcolor="#ffff99";//#FFF000
				else if (($date_total_arr_all[$newdate]/$avg_date_capacity[$newdate])>=($tot_capacity/$avg_capacity))
					$tdcolor="#99c299";//#009933
				?>
				<td align="right" bgcolor="<? echo $tdcolor;?>">
				<? $day_capacity=($date_total_arr_all[$newdate]/$total_capacity_loss[$newdate])*100;
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
	//echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);

	?>
	<div id="chartdiv" style="height:500px; width:<?= $table_width; ?>px; background-color:#FFF"></div>	
	<script src="../../home_graph/ext_resource/hschart/hschart.js"></script>
	<script>
		var msg="Total Qty"
		var uom="";

			
			$('#chartdiv').highcharts({

				chart: {
					type: 'column'
				},
		
				title: {
					text: 'Machine Wise Monthy Knitting Production  Qnty Graph'
				},
		
				xAxis: {
					categories:['<? echo implode("','",array_keys($machineQtyArr));?>'],
					labels: {
					rotation: 90,
					style: {color: 'black',}
					}
				},
		
				yAxis: {
					allowDecimals: false,
					min: 0,
					title: {
						text: msg
					}
				},
		
				tooltip: {
					formatter: function () {
						return '<b>' + this.x + '</b> ' +
							 ': ' + this.y + uom +'<br/>' ;
							//+ 'Total: ' + this.point.stackTotal;  this.series.name + ': ' + this.y + uom +'<br/>' ;
					}
				},
		
				plotOptions: {
					column: {
						stacking: false //'normal'
					}
				},
			
				series: [{ name: 'Machine', data:[<? echo implode(',',$machineQtyArr);?>], stack: 'none'}]
			});
	</script>
	<?	
	
	$total_data="";
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$total_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_doc,$total_data);
	
	$filename=$user_id."_".$name.".xls";
	echo $total_data.'****'.$filename;

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
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=1 and a.company_id=$im_data[0] and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
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
	//echo $sql;
	//echo "<br />". create_list_view ( "list_view", "Order No,Order Qnty,Pub Shipment Date", "200,120,220","540","220",1, "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*a.total_set_qnty as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.is_deleted=0 and a.status_active=1", "", "","", 1, '0,0,0', $arr, "po_number,po_quantity,pub_shipment_date","../requires/date_wise_production_report_controller", '','0,1,3');
	
	exit();
}

?>
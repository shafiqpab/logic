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
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_wise_daily_dyeing_prod_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	 
}

if ($action == "load_drop_down_buyer") 
{
	$lode_data=explode('_',$data);
	//print_r($lode_data[0]); // [0] == type and [1] == company
	if ($lode_data[0]==0 || $lode_data[0]==1 || $lode_data[0]==3) // Self and Sample
	{
		echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$lode_data[1]' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
		exit();
	}
	if ($lode_data[0]==2) // Sub Party
	{
		echo create_drop_down("cbo_buyer_name", 130, "select sup.id, sup.supplier_name from lib_supplier sup, lib_supplier_tag_company b  where sup.status_active =1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$lode_data[1]' and sup.id in (select supplier_id from lib_supplier_party_type where party_type in (1,3,21,90)) order by supplier_name", "id,supplier_name", 1, "-- All Buyer --", $selected, "");
		exit();
	}
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
	$machine_name=str_replace("'","",$txt_machine_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$order_type=str_replace("'","",$cbo_order_type);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$booking_no=str_replace("'","",$txt_booking_no);
	$order_no=str_replace("'","",$txt_order_no);
	$batch=str_replace("'","",$txt_batch);
	$batch_color=str_replace("'","",$hidden_color_id);
	$color_range=str_replace("'","",$txt_color_range);
	$floor_name=str_replace("'","",$cbo_floor_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	
	if ($batch=="") $batch_cond =""; else $batch_cond =" and a.batch_no='$batch' ";
	if ($order_type==0) $order_type_cond =""; else $order_type_cond =" and a.order_type=$order_type ";
	if ($cbo_location==0 || $cbo_location=='') $location_id =""; else $location_id =" and c.location_id=$cbo_location ";
	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and c.floor_id=$cbo_floor_id";
	if ($machine_name=="") $machine_cond=""; else $machine_cond =" and c.id in ($machine_name) ";
	if ($buyer_name==0  || $buyer_name=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$buyer_name ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and q.booking_no=$txt_booking_no ";
	if ($batch_color=="") $batch_color_cond =""; else $batch_color_cond =" and q.color_id=$batch_color ";
	if ($color_range==0 || $color_range=="") $color_range_cond =""; else $color_range_cond =" and q.color_range_id=$color_range ";
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$machineArr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	ob_start();	

	?>
	<div>
        <table width="1760" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>
        </table>
    <?
    if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$mc_idle = "SELECT a.machine_entry_tbl_id, a.machine_no, a.from_date, a.from_hour, a.from_minute, a.to_date, a.to_hour, a.to_minute, a.machine_idle_cause, a.remarks, b.batch_id
	from pro_cause_of_machine_idle a, pro_fab_subprocess b 
	where a.machine_entry_tbl_id=b.machine_id and a.status_active=1 and a.is_deleted=0 $idle_sql_cond";

	//echo $mc_idle;die;

	$mc_idle_result=sql_select($mc_idle);
	$idle_data_arr=array();
	foreach ($mc_idle_result as $rows) 
	{
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine"]=$rows[csf("machine_entry_tbl_id")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_no"]=$rows[csf("machine_no")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_date"]=$rows[csf("from_date")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_hour"]=$rows[csf("from_hour")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_minute"]=$rows[csf("from_minute")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_date"]=$rows[csf("to_date")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_hour"]=$rows[csf("to_hour")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_minute"]=$rows[csf("to_minute")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_idle_cause"]=$rows[csf("machine_idle_cause")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["remarks"]=$rows[csf("remarks")];
	}
	/*echo '<pre>';
  	print_r($idle_data_arr);die;*/		

	if ($order_type==0 || $order_type==1) // Self Order
	{
		?>
		<div>
			<table width="518" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th colspan="5">Self Order</th>
					</tr>
				</thead>
			</table>
		<table width="1740" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
			<thead>
				<tr>
	                <th rowspan="2" width="30">SL No</th>
	                <th rowspan="2" width="80">M/C & Capacity</th>
	                <th rowspan="2" width="80">Buyer Name</th>
	                <th rowspan="2" width="80">Batch No.</th>
	                <th rowspan="2" width="80">Batch Color</th>
	                <th rowspan="2" width="80">Booking No.</th>
	                <th rowspan="2" width="80">Color Range</th>
	                <th colspan="5" width="80">Total Production Qty in Kg</th>
	                <th rowspan="2" width="80">Water/kg in Ltr</th>
	                <th rowspan="2" width="80">M/C UT%y</th>
	                <th rowspan="2" width="80">Loading Time</th>
	                <th rowspan="2" width="80">Unloading Time</th>
	                <th rowspan="2" width="80">Total Time (Hour)</th>
	                <th rowspan="2" width="120">Fabric Construction</th>
	                <th rowspan="2" width="80">Result</th>
	                <th rowspan="2">Remarks</th>
	            </tr>
	            <tr>
	                <th width="80" title="Data is showing, according to [Color Range] - Average Color, from batch creation page.">Color</th>
	                <th width="80" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
	                <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
	                <th width="80">Re-Process</th>
	                <th width="80">Trims Weight</th>
	            </tr> 
			</thead>
		</table>
	    <div style="width:1760px; overflow-y:scroll; max-height:300px;" id="scroll_body">
		<table align="center" cellspacing="0" width="1740"  border="1" rules="all" class="rpt_table" >
			
			<? 
			$sqls="SELECT  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.remarks 
			from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond  
			and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 
			group by  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.remarks ";

			$load_time_array=array();
			foreach(sql_select($sqls) as $vals)
			{
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
			}
		  	/*echo '<pre>';
		  	print_r($load_time_array);die;*/

		  	// Main query	
			$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date, 
			 a.water_flow_meter, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity, 
			 p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
			 from wo_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			 where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
			 and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2 
			 and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order!=1
			 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			 order by a.production_date, c.machine_no";

			//echo $sql_result;die;

			$sql_dtls=sql_select($sql_result);
			//print_r($sql_dtls);
			$date_data_arr=array();
			foreach ($sql_dtls as $row)
			{
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('production_date')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];			
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];			
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];	
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition']=$row[csf('const_composition')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];				
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];				
			}		
			/*echo "<pre>";
			print_r($date_data_arr);die;*/

			$color_range=array(1=>"Dark Color",2=>"Light Color",3=>"Black Color",4=>"White Color",5=>"Average Color",6=>"Melange",7=>"Wash",8=>"Scouring",9=>"Extra Dark",10=>"Medium Color",11=>"Super Dark");


			$rowspan_arr=array();
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					foreach ($machine_data as $batch_id => $row) 
					{
						if (isset($rowspan_arr[$p_date][$machine_id])) 
						{
							$rowspan_arr[$p_date][$machine_id]++;
						}
						else
						{
							$rowspan_arr[$p_date][$machine_id]=1;
						}					
					}
				}
			}
			/*echo '<pre>';
			print_r($rowspan_arr);*/

			$i=1;
			$other_grnd_total=$white_grnd_total=$wash_grnd_total=$pro_qty_grnd_total=0;
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$pro_qty_date_wise_total=0; 
				?>
				<tr>
					<td colspan="20" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
				</tr>
				<?
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					$other_mcw_wise_total=$white_mcw_wise_total=$wash_mcw_wise_total=$total_pro_qty=0; 
					$r=0;
					foreach ($machine_data as $batch_id => $row) 
					{
						//echo $row['batch_ext_no'];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr id="<? echo 'v_'.$i; ?>" onclick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
							<?							
								if ($r==0) 
								{
									?>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="30" valign="middle"><?echo $i;?></td>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="80" valign="middle"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
									<?
								} $r++;
							?>
							<td width="80"><?echo $buyer_library[$row['buyer_id']];?></td>
							<td width="80"><?echo $row['batch_no'];?></td>
							<td width="80"><p><?echo $color_library[$row['color_id']]?></p></td>
							<td width="80"><?echo $row['booking_no'];?></td>
							<td width="80"><?echo $color_range[$row['color_range_id']];?></td>
							<? 
							$avarage_batch_qty=$white_batch_qty=$wash_batch_qty=$pro_qty=0; 
								if ($row['color_range_id']==5 && $row['result']==1)
								{
									$avarage_batch_qty = $row['batch_qty']; 
								} 
							?>
							<td width="80" align="right"><? echo $avarage_batch_qty; $other_mcw_wise_total+=$avarage_batch_qty;?></td> 
							<? 
								if ($row['color_range_id']==4 && $row['result']==1)
								{
									$white_batch_qty = $row['batch_qty']; 
								} 
							?>
							<td width="80" align="right"><? echo $white_batch_qty; $white_mcw_wise_total+=$white_batch_qty;?></td>
							<? 
								if ($row['color_range_id']==7 && $row['result']==1)
								{
									$wash_batch_qty = $row['batch_qty']; 
								} 
							?>
							<td width="80" align="right"><? echo $wash_batch_qty; $wash_mcw_wise_total+=$wash_batch_qty;?></td>
							<? 
								if ($row['result']==2) 
								{
									$pro_qty = $row['batch_qty']; 
								}
							?>
							<td width="80" align="right"><?echo $pro_qty; $total_pro_qty+=$pro_qty;?></td>
							<td width="80" align="right"><?echo $row['total_trims_weight'];?></td>
							<td width="80"><?echo $row['water_flow_meter'];?></td>
							<?
								$pro_qty = $row['batch_qty'];
								$mc_capacity = $row['prod_capacity'];
								$mc_ut = ($pro_qty/$mc_capacity)*100;
							?>
							<td width="80"><? echo number_format($mc_ut,2,'.','');?></td>
							<?
								$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
								$start_time = $load_hour.'.'.$load_minut; 
								$load_hour_minut = date('h:i A', strtotime($start_time));
							?>
							<td width="80"><?echo $load_hour_minut;?></td>
							<?
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$end_time = $unload_hour.'.'.$unload_minut; 
								$unload_hour_minut = date('h:i A', strtotime($end_time));
							?>
							<td width="80"><?echo $unload_hour_minut; ?></td>
							<?
							    $diff = (strtotime($unload_hour_minut) - strtotime($load_hour_minut));
							    $total = $diff/60;
							    $total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
							?>
							<td width="80"><? echo $total_time; ?></td>
							<td width="120"><?echo $row['const_composition'];?></td>
							<td width="80"><?echo $dyeing_result[$row['result']];?></td>
							<?
								$unload_remarks = $row['remarks'];
								$load_remarks = $load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
								if ($unload_remarks=="") 
								{
									?><td><?echo $load_remarks;?></td><?	
								}
								else
								{
									?><td><?echo $row['remarks'];?></td><?							
								}							
							?>					
						</tr>											
						<?						
						//$i++;
						//$other_mcw_wise_total+=$other_pro_qty;
						//$white_mcw_wise_total+=$white_pro_qty;
						
					}
					if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					{
						?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$idle_data_arr[$p_date][$machine_id]['machine_idle_cause']];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr[$p_date][$machine_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr[$p_date][$machine_id]['from_hour'];
									$idle_start_minut = $idle_data_arr[$p_date][$machine_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut; 
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr[$p_date][$machine_id]['to_hour'];
									$idle_end_minut = $idle_data_arr[$p_date][$machine_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut; 
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr[$p_date][$machine_id]['from_date'];
									$to_date = $idle_data_arr[$p_date][$machine_id]['to_date'];
									 
									$before = strtotime($from_date . " " . $start_hour_minut);
									$after = strtotime($to_date . " " . $end_hour_minut);
									$diff = $after - $before;
									//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments
									 
									// $diff is in seconds
									$hours = floor($diff / 3600);
									$minutes = floor(($diff - $hours * 3600) / 60);
									$seconds = $diff - $hours * 3600 - $minutes * 60;
									echo $hours.'.'.$minutes.'H';
									?>
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						<?	
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Total:</b></td>
						<td><strong><? echo $other_mcw_wise_total;?></strong></td>
						<td><strong><? echo $white_mcw_wise_total;?><strong></td>
						<td><strong><? echo $wash_mcw_wise_total;?><strong></td>
						<td><strong><? echo $total_pro_qty;?><strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$i++;
					$other_date_wise_total+=$other_mcw_wise_total;
					$white_date_wise_total+=$white_mcw_wise_total;
					$wash_date_wise_total+=$wash_mcw_wise_total;
					$pro_qty_date_wise_total+=$total_pro_qty;
				}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Date wise Total:</b></td>
						<td><strong><? echo $other_date_wise_total;?></strong></td>
						<td><strong><? echo $white_date_wise_total;?></strong></td>
						<td><strong><? echo $wash_date_wise_total;?></strong></td>
						<td><strong><? echo $pro_qty_date_wise_total;?></strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$other_grnd_total+=$other_date_wise_total;
					$white_grnd_total+=$white_date_wise_total;
					$wash_grnd_total+=$wash_date_wise_total;
					$pro_qty_grnd_total+=$pro_qty_date_wise_total;
			}
		?>
	    <tfoot>
	        <tr class="tbl_bottom">
	        	<td width="30"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80" align="right"><strong>Grand Total :</strong></td>
	            <td width="80"><strong><? echo $other_grnd_total;?></strong>&nbsp;</td>
	            <td width="80"><strong><? echo $white_grnd_total;?></strong></td>
	            <td width="80"><strong><? echo $wash_grnd_total;?></strong></td>
	            <td width="80"><strong><? echo $pro_qty_grnd_total;?></strong></td>
	            <td colspan="9"></td>
	        </tr> 
	     </tfoot>
	    </table>
	    </div>
	    </div>
	    <br>
	    <?
	}

	if ($order_type==0 || $order_type==2) // Subcontract Order
	{
		?>
		<div>
			<table width="518" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th colspan="5">Subcontract Order</th>
					</tr>
				</thead>
			</table>
		<table width="1740" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
			<thead>
				<tr>
	                <th rowspan="2" width="30">SL No</th>
	                <th rowspan="2" width="80">M/C & Capacity</th>
	                <th rowspan="2" width="80">Party Name</th>
	                <th rowspan="2" width="80">Batch No.</th>
	                <th rowspan="2" width="80">Batch Color</th>
	                <th rowspan="2" width="80">Order No.</th>
	                <th rowspan="2" width="80">Color Range</th>
	                <th colspan="5" width="80">Total Production Qty in Kg</th>
	                <th rowspan="2" width="80">Water/kg in Ltr</th>
	                <th rowspan="2" width="80">M/C UT%y</th>
	                <th rowspan="2" width="80">Loading Time</th>
	                <th rowspan="2" width="80">Unloading Time</th>
	                <th rowspan="2" width="80">Total Time (Hour)</th>
	                <th rowspan="2" width="120">Fabric Construction</th>
	                <th rowspan="2" width="80">Result</th>
	                <th rowspan="2">Remarks</th>
	            </tr>
	            <tr>
	                <th width="80" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
	                <th width="80" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
	                <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
	                <th width="80">Re-Process</th>
	                <th width="80">Trims Weight</th>
	            </tr> 
			</thead>
		</table>
	    <div style="width:1760px; overflow-y:scroll; max-height:300px;" id="scroll_body">
		<table align="center" cellspacing="0" width="1740"  border="1" rules="all" class="rpt_table" >
			
			<? 
			$sqls=" SELECT a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.remarks 
			from pro_fab_subprocess a, lib_machine_name c 
			where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond 
			and a.machine_id=c.id and a.entry_form=38 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 
 			group by a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.remarks";

		  $load_time_array=array();
		  foreach(sql_select($sqls) as $vals)
		  {
		  	$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
		  	$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
		  	$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
		  }
		  	/*echo '<pre>';
		  	print_r($load_time_array);die;*/
		  	
		  		
		  	if ($buyer_name==0  || $buyer_name=='') $party_name_cond =""; else $party_name_cond =" and f.party_id=$buyer_name ";
			if ($order_no=="") $order_no_cond =""; else $order_no_cond =" and p.order_no=$txt_order_no ";

		  	// Main query	
			$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date, a.water_flow_meter, b.batch_qnty, b.item_description, c.machine_no, c.prod_capacity, f.party_id, p.order_no, q.color_id, q.color_range_id, q.total_trims_weight, q.process_id 
			from subcon_ord_dtls p, subcon_ord_mst f, pro_batch_create_mst q, pro_fab_subprocess a, pro_batch_create_dtls b, lib_machine_name c 
			where a.service_company=$cbo_company_id $order_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $party_name_cond $batch_color_cond $color_range_cond and a.machine_id=c.id and a.entry_form=38 and q.entry_form=36 and a.load_unload_id=2 and a.batch_id=q.id and b.po_id=p.id  and p.job_no_mst= f.subcon_job and q.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.production_date, c.machine_no";

			//echo $sql_result;die;

			$sql_dtls=sql_select($sql_result);
			//print_r($sql_dtls);
			$date_data_arr=array();
			foreach ($sql_dtls as $row)
			{
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('production_date')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];			
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qnty']+=$row[csf('batch_qnty')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];			
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];	
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['item_description']=$row[csf('item_description')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['party_id']=$row[csf('party_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['order_no']=$row[csf('order_no')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];				
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];				
			}
		
			/*echo "<pre>";
			print_r($date_data_arr);*/
			$color_range=array(1=>"Dark Color",2=>"Light Color",3=>"Black Color",4=>"White Color",5=>"Average Color",6=>"Melange",7=>"Wash",8=>"Scouring",9=>"Extra Dark",10=>"Medium Color",11=>"Super Dark");


			$rowspan_arr=array();
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					foreach ($machine_data as $batch_id => $row) 
					{
						if (isset($rowspan_arr[$p_date][$machine_id])) 
						{
							$rowspan_arr[$p_date][$machine_id]+=1;
						}
						else
						{
							$rowspan_arr[$p_date][$machine_id]=1;
						}
					}
				}
			}
			//echo '<pre>';
			//print_r($rowspan_arr);

			$i=1;
			$subcon_other_grnd_total=$subcon_white_grnd_total=$subcon_wash_grnd_total=$subcon_pro_qty_grnd_total=0;
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				$subcon_other_date_wise_total=$subcon_white_date_wise_total=$subcon_wash_date_wise_total=$subcon_pro_qty_date_wise_total=0;
				?>
				<tr>
					<td colspan="20" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
				</tr>
				<?
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					$subcon_other_mcw_wise_total=$subcon_white_mcw_wise_total=$subcon_wash_mcw_wise_total=$subcon_pro_qty_total=0;
					$r=0;
					foreach ($machine_data as $batch_id => $row) 
					{
						//echo $row['batch_ext_no'];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr id="<? echo 'v_'.$i; ?>" onclick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
							<?							
								if ($r==0) 
								{
									?>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="30" valign="middle"><?echo $i;?></td>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="80" valign="middle"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
									<?
								} $r++;
							?>
							<td width="80"><?echo $buyer_library[$row['party_id']];?></td>
							<td width="80"><?echo $row['batch_no'];?></td>
							<td width="80"><p><?echo $color_library[$row['color_id']]?></p></td>
							<td width="80"><?echo $row['order_no'];?></td>
							<td width="80"><?echo $color_range[$row['color_range_id']];?></td>
							<?
							$subcon_other_pro_qty=$subcon_white_pro_qty=$subcon_wash_pro_qty=$pro_qty=0; 
							if ($row['color_range_id']==5 && $row['result']==1)
							{
								$subcon_other_pro_qty = $row['batch_qnty'];
							} 
							?>
							<td width="80" align="right"><? echo $subcon_other_pro_qty; $subcon_other_mcw_wise_total+=$subcon_other_pro_qty;?></td>	

							<? 
								if ($row['color_range_id']==4 && $row['result']==1)
								{
									$subcon_white_pro_qty = $row['batch_qnty']; 
								} 
							?> 
							<td width="80" align="right"><? echo $subcon_white_pro_qty; $subcon_white_mcw_wise_total+=$subcon_white_pro_qty;?></td>
							<? 
								if ($row['color_range_id']==7 && $row['result']==1)
								{
									$subcon_wash_pro_qty = $row['batch_qnty'];
								} 
							?>
							<td width="80" align="right"><? echo $subcon_wash_pro_qty; $subcon_wash_mcw_wise_total+=$subcon_wash_pro_qty;?></td> 							
							<?
								if ($row['result']==2) 
								{
									$pro_qty = $row['batch_qnty']; 
								}
							?>
							<td width="80" align="right"><?echo $pro_qty; $subcon_pro_qty_total+=$pro_qty;?></td>
							<td width="80" align="right"><?echo $row['total_trims_weight'];?></td>
							<td width="80"><?echo $row['water_flow_meter'];?></td>
							<?
								$pro_qty = $row['batch_qnty'];
								$mc_capacity = $row['prod_capacity'];
								$mc_ut = ($pro_qty/$mc_capacity)*100;
							?>
							<td width="80"><? echo number_format($mc_ut,2,'.','');?></td>
							<?
								$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
								$start_time = $load_hour.':'.$load_minut; 
								$load_hour_minut = date('h:i a', strtotime($start_time));
							?>
							<td width="80"><?echo $load_hour_minut;?></td>
							<?
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$end_time = $unload_hour.':'.$unload_minut; 
								$unload_hour_minut = date('h:i a', strtotime($end_time));
							?>
							<td width="80"><?echo $unload_hour_minut; ?></td>
							<?
							    $diff = (strtotime($unload_hour_minut) - strtotime($load_hour_minut));
							    $total = $diff/60;
							    $total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
							?>
							<td width="80"><? echo $total_time; ?></td>
							<td width="120"><?echo $row['item_description'];?></td>
							<td width="80"><?echo $dyeing_result[$row['result']];?></td>
							<?
								$unload_remarks = $row['remarks'];
								$load_remarks = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["remarks"];
								if ($unload_remarks=="") 
								{
									?><td><?echo $load_remarks;?></td><?	
								}
								else
								{
									?><td><?echo $row['remarks'];?></td><?							
								}							
							?>						
						</tr>					
						<?
						//$i++;
						//$subcon_other_mcw_wise_total+=$subcon_other_pro_qty;
						//$subcon_white_mcw_wise_total+=$subcon_white_pro_qty;
					}
					if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					{
						?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$idle_data_arr[$p_date][$machine_id]['machine_idle_cause']];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr[$p_date][$machine_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr[$p_date][$machine_id]['from_hour'];
									$idle_start_minut = $idle_data_arr[$p_date][$machine_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut; 
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr[$p_date][$machine_id]['to_hour'];
									$idle_end_minut = $idle_data_arr[$p_date][$machine_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut; 
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr[$p_date][$machine_id]['from_date'];
									$to_date = $idle_data_arr[$p_date][$machine_id]['to_date'];
									 
									$before = strtotime($from_date . " " . $start_hour_minut);
									$after = strtotime($to_date . " " . $end_hour_minut);
									$diff = $after - $before;
									//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments
									 
									// $diff is in seconds
									$hours = floor($diff / 3600);
									$minutes = floor(($diff - $hours * 3600) / 60);
									$seconds = $diff - $hours * 3600 - $minutes * 60;
									echo $hours.'.'.$minutes.'H';
									?>
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						<?	
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Total:</b></td>
						<td><strong><? echo $subcon_other_mcw_wise_total;?></strong></td>
						<td><strong><? echo $subcon_white_mcw_wise_total;?><strong></td>
						<td><strong><? echo $subcon_wash_mcw_wise_total;?><strong></td>
						<td><strong><? echo $subcon_pro_qty_total;?><strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$i++;
					$subcon_other_date_wise_total+=$subcon_other_mcw_wise_total;
					$subcon_white_date_wise_total+=$subcon_white_mcw_wise_total;
					$subcon_wash_date_wise_total+=$subcon_wash_mcw_wise_total;
					$subcon_pro_qty_date_wise_total+=$subcon_pro_qty_total;
				}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Date wise Total:</b></td>
						<td><strong><? echo $subcon_other_date_wise_total;?></strong></td>
						<td><strong><? echo $subcon_white_date_wise_total;?></strong></td>
						<td><strong><? echo $subcon_wash_date_wise_total;?></strong></td>
						<td><strong><? echo $subcon_pro_qty_date_wise_total;?></strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$subcon_other_grnd_total+=$subcon_other_date_wise_total;
					$subcon_white_grnd_total+=$subcon_white_date_wise_total;
					$subcon_wash_grnd_total+=$subcon_wash_date_wise_total;
					$subcon_pro_qty_grnd_total+=$subcon_pro_qty_date_wise_total;
			}
		?>
	    <tfoot>
	        <tr class="tbl_bottom">
	        	<td width="30"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80" align="right"><strong>Grand Total :</strong></td>
	            <td width="80"><strong><? echo $subcon_other_grnd_total;?></strong>&nbsp;</td>
	            <td width="80"><strong><? echo $subcon_white_grnd_total;?></strong></td>
	            <td width="80"><strong><? echo $subcon_wash_grnd_total;?></strong></td>
	            <td width="80"><strong><? echo $subcon_pro_qty_grnd_total;?></strong></td>
	            <td colspan="9" width="80"></td>
	        </tr> 
	     </tfoot>
	    </table>
	    </div>
	    </div>
	    <?
	}	

	if ($order_type==0 || $order_type==3) // Sample Without Order
	{
		?>
		<div>
			<table width="518" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th colspan="5">Sample Without Order</th>
					</tr>
				</thead>
			</table>
		<table width="1740" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
			<thead>
				<tr>
	                <th rowspan="2" width="30">M/C No</th>
	                <th rowspan="2" width="80">M/C & Capacity</th>
	                <th rowspan="2" width="80">Buyer Name</th>
	                <th rowspan="2" width="80">Batch No.</th>
	                <th rowspan="2" width="80">Batch Color</th>
	                <th rowspan="2" width="80">Booking No.</th>
	                <th rowspan="2" width="80">Color Range</th>
	                <th colspan="5" width="80">Total Production Qty in Kg</th>
	                <th rowspan="2" width="80">Water/kg in Ltr</th>
	                <th rowspan="2" width="80">M/C UT%y</th>
	                <th rowspan="2" width="80">Loading Time</th>
	                <th rowspan="2" width="80">Unloading Time</th>
	                <th rowspan="2" width="80">Total Time (Hour)</th>
	                <th rowspan="2" width="120">Fabric Construction</th>
	                <th rowspan="2" width="80">Result</th>
	                <th rowspan="2">Remarks</th>
	            </tr>
	            <tr>
	                <th width="80" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
	                <th width="80" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
	                <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
	                <th width="80">Re-Process</th>
	                <th width="80">Trims Weight</th>
	            </tr> 
			</thead>
		</table>
	    <div style="width:1760px; overflow-y:scroll; max-height:300px;" id="scroll_body">
		<table align="center" cellspacing="0" width="1740"  border="1" rules="all" class="rpt_table" >
			
			<? 
			$sqls="SELECT  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.remarks 
			from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond  
			and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 
			group by  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.remarks ";

			$load_time_array=array();
			foreach(sql_select($sqls) as $vals)
			{
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
			}
		  	/*echo '<pre>';
		  	print_r($load_time_array);die;*/

		  	// Main query	
			$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date, 
			 a.water_flow_meter, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity, 
			 p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
			 from wo_non_ord_samp_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			 where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
			 and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2 
			 and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order=1
			 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			 order by a.production_date, c.machine_no";

			//echo $sql_result;die;

			$sql_dtls=sql_select($sql_result);
			//print_r($sql_dtls);
			$date_data_arr=array();
			foreach ($sql_dtls as $row)
			{
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('production_date')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];			
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];			
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];	
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition']=$row[csf('const_composition')];		
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];				
				$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];				
			}		
			/*echo "<pre>";
			print_r($date_data_arr);die;*/

			$color_range=array(1=>"Dark Color",2=>"Light Color",3=>"Black Color",4=>"White Color",5=>"Average Color",6=>"Melange",7=>"Wash",8=>"Scouring",9=>"Extra Dark",10=>"Medium Color",11=>"Super Dark");


			$rowspan_arr=array();
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					foreach ($machine_data as $batch_id => $row) 
					{
						if (isset($rowspan_arr[$p_date][$machine_id])) 
						{
							$rowspan_arr[$p_date][$machine_id]++;
						}
						else
						{
							$rowspan_arr[$p_date][$machine_id]=1;
						}					
					}
				}
			}
			/*echo '<pre>';
			print_r($rowspan_arr);*/

			$i=1;
			$other_grnd_total=$white_grnd_total=$wash_grnd_total=$wo_pro_qty_grnd_total=0;
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$wo_pro_qty_date_wise_total=0;
				?>
				<tr>
					<td colspan="20" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
				</tr>
				<?
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					$other_mcw_wise_total=$white_mcw_wise_total=$wo_wash_mcw_wise_total=$wo_pro_qty_total=0;
					$r=0;
					foreach ($machine_data as $batch_id => $row) 
					{
						//echo $row['batch_ext_no'];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr id="<? echo 'v_'.$i; ?>" onclick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
							<?							
								if ($r==0) 
								{
									?>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="30" valign="middle"><?echo $i;?></td>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="80" valign="middle"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
									<?
								} $r++;
							?>
							<td width="80"><?echo $buyer_library[$row['buyer_id']];?></td>
							<td width="80"><?echo $row['batch_no'];?></td>
							<td width="80"><p><?echo $color_library[$row['color_id']]?></p></td>
							<td width="80"><?echo $row['booking_no'];?></td>
							<td width="80"><?echo $color_range[$row['color_range_id']];?></td>
							<?

							$other_pro_qty=$white_pro_qty=$wo_wash_pro_qty=$pro_qty=0; 
								if ($row['color_range_id']==5 && $row['result']==1)
								{
									$other_pro_qty = $row['batch_qty'];
								} 
							?>
							<td width="80" align="right"><? echo $other_pro_qty; $other_mcw_wise_total+=$other_pro_qty;?></td>	

							<? 
								if ($row['color_range_id']==4 && $row['result']==1)
								{
									$white_pro_qty = $row['batch_qty']; 
								} 
							?> 
							<td width="80" align="right"><? echo $white_pro_qty; $white_mcw_wise_total+=$white_pro_qty;?></td>
							<? 
								if ($row['color_range_id']==7 && $row['result']==1)
								{
									$wo_wash_pro_qty = $row['batch_qty'];
								} 
							?>
							<td width="80" align="right"><? echo $wo_wash_pro_qty; $wo_wash_mcw_wise_total+=$wo_wash_pro_qty;?></td>						
							<? 
								if ($row['result']==2) 
								{
									$pro_qty = $row['batch_qty']; 
								}
							?>
							<td width="80" align="right"><?echo $pro_qty; $wo_pro_qty_total+=$pro_qty;?></td>
							<td width="80"><?echo $row['total_trims_weight'];?></td>
							<td width="80"><?echo $row['water_flow_meter'];?></td>
							<?
								$pro_qty = $row['batch_qty'];
								$mc_capacity = $row['prod_capacity'];
								$mc_ut = ($pro_qty/$mc_capacity)*100;
							?>
							<td width="80"><? echo number_format($mc_ut,2,'.','');?></td>
							<?
								$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
								$start_time = $load_hour.'.'.$load_minut; 
								$load_hour_minut = date('h:i A', strtotime($start_time));
							?>
							<td width="80"><?echo $load_hour_minut;?></td>
							<?
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$end_time = $unload_hour.'.'.$unload_minut; 
								$unload_hour_minut = date('h:i A', strtotime($end_time));
							?>
							<td width="80"><?echo $unload_hour_minut; ?></td>
							<?
							    $diff = (strtotime($unload_hour_minut) - strtotime($load_hour_minut));
							    $total = $diff/60;
							    $total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
							?>
							<td width="80"><? echo $total_time; ?></td>
							<td width="120"><?echo $row['const_composition'];?></td>
							<td width="80"><?echo $dyeing_result[$row['result']];?></td>
							<?
								$unload_remarks = $row['remarks'];
								$load_remarks = $load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
								if ($unload_remarks=="") 
								{
									?><td><?echo $load_remarks;?></td><?	
								}
								else
								{
									?><td><?echo $row['remarks'];?></td><?							
								}							
							?>					
						</tr>											
						<?						
						//$i++;
						//$other_mcw_wise_total+=$other_pro_qty;
						//$white_mcw_wise_total+=$white_pro_qty;
						
					}
					if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					{
						?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$idle_data_arr[$p_date][$machine_id]['machine_idle_cause']];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr[$p_date][$machine_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr[$p_date][$machine_id]['from_hour'];
									$idle_start_minut = $idle_data_arr[$p_date][$machine_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut; 
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr[$p_date][$machine_id]['to_hour'];
									$idle_end_minut = $idle_data_arr[$p_date][$machine_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut; 
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr[$p_date][$machine_id]['from_date'];
									$to_date = $idle_data_arr[$p_date][$machine_id]['to_date'];
									 
									$before = strtotime($from_date . " " . $start_hour_minut);
									$after = strtotime($to_date . " " . $end_hour_minut);
									$diff = $after - $before;
									//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments
									 
									// $diff is in seconds
									$hours = floor($diff / 3600);
									$minutes = floor(($diff - $hours * 3600) / 60);
									$seconds = $diff - $hours * 3600 - $minutes * 60;
									echo $hours.'.'.$minutes.'H';
									?>
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						<?	
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Total:</b></td>
						<td><strong><? echo $other_mcw_wise_total;?></strong></td>
						<td><strong><? echo $white_mcw_wise_total;?><strong></td>
						<td><strong><? echo $wo_wash_mcw_wise_total;?><strong></td>
						<td><strong><? echo $wo_pro_qty_total;?><strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$i++;
					$other_date_wise_total+=$other_mcw_wise_total;
					$white_date_wise_total+=$white_mcw_wise_total;
					$wash_date_wise_total+=$wo_wash_mcw_wise_total;
					$wo_pro_qty_date_wise_total+=$wo_pro_qty_total;
				}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Date wise Total:</b></td>
						<td><strong><? echo $other_date_wise_total;?></strong></td>
						<td><strong><? echo $white_date_wise_total;?></strong></td>
						<td><strong><? echo $wash_date_wise_total;?></strong></td>
						<td><strong><? echo $wo_pro_qty_date_wise_total;?></strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$other_grnd_total+=$other_date_wise_total;
					$white_grnd_total+=$white_date_wise_total;
					$wash_grnd_total+=$wash_date_wise_total;
					$wo_pro_qty_grnd_total+=$wo_pro_qty_date_wise_total;
			}
		?>
	    <tfoot>
	        <tr class="tbl_bottom">
	        	<td width="30"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80"></td>
	        	<td width="80" align="right"><strong>Grand Total :</strong></td>
	            <td width="80"><strong><? echo $other_grnd_total;?></strong>&nbsp;</td>
	            <td width="80"><strong><? echo $white_grnd_total;?></strong></td>
	            <td width="80"><strong><? echo $wash_grnd_total;?></strong></td>
	            <td width="80"><strong><? echo $wo_pro_qty_grnd_total;?></strong></td>
	            <td colspan="19"></td>
	        </tr> 
	     </tfoot>
	    </table>
	    </div>
	    </div>
	    <br>
	    <?
	}
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
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) 
		{
			
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

if($action=="color_name_suggestion")
{
	$search_color = $_POST['color_name'];
	$color_sql = "SELECT id, color_name from lib_color where color_name like '$search_color%' and status_active=1 and is_deleted=0 order by color_name"; 
	$search_color_arr=sql_select($color_sql);
	echo '<ul>';
	foreach ($search_color_arr as $value) 
	{
		$color_name = $value[csf('color_name')];
		$color_name2 = "'$color_name'";
		?>
			<li onclick="set_color_id(<? echo $value[csf('id')];?>,<? echo $color_name2; ?>)"><? echo $value[csf('color_name')];?></li>
		<?
	}
	echo '</ul>';
}

if ($action == "fabricBooking_popup") 
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$im_data=explode('_',$data);
	//print_r ($im_data);

	$width = 1055;
	?>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][98]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function () {
			set_field_level_access( <? echo $cbo_company_id; ?> );
		}
		function js_set_value(id, booking_no, type) 
		{
			//alert(id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_booking_id').val(id);
			$('#booking_without_order').val(type);
			parent.emailwindow.hide();
		}

		function company_validation()
		{
			var cbo_lc_company_id = $('#cbo_lc_company_id').val();
			if (cbo_lc_company_id==0) 
			{
				if (form_validation('cbo_lc_company_id','Company')==false)
				{
					alert('Please Select LC Company');
					return;
				}
			}
			else 
			{
				 show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_lc_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'machine_wise_daily_dyeing_prod_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);'); 
			}
		}

	</script>

	</head>

	<body>
		<div align="center" style="width:<? echo $width; ?>px;">
			<form name="searchwofrm" id="searchwofrm" autocomplete=off>
				<fieldset style="width:<? echo $width - 5; ?>px; margin-left:2px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="750" class="rpt_table">
						<thead>
							<th>LC Company</th>
							<th>Year</th>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="200">Enter Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
								value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes"
								value="">
								<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes"
								value="">
								<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes"
								value="">
							</th>
						</thead>
						<tr>
							<td> 
								<?
									echo create_drop_down( "cbo_lc_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
							    ?>
							</td>
							<td align="center">
								<?
									echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
								?>
							</td>
							<td align="center">
								<?
									echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", '', "", $disable);
								?>
							</td>
							<td align="center">
								<?
									$search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Booking Date", 5 => "Internal Ref", 6 => "File No");
									$dd = "change_search_event(this.value, '0*0*0*3*0*0', '0*0*0*2*0*0', '../../') ";
									$selected = 1;
									$disable = 0;
									echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", $selected, $dd, 0); 
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" 
								onClick="company_validation();"
								style="width:100px;"/>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	</html>
	<?
}

if ($action == "create_booking_search_list_view") 
{
	$data = explode("_", $data);
	//print_r($data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$booking_year = $data[4];

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	if ($buyer_id == 0) $buyer_id = "%%";

		if (trim($data[0]) != "") 
		{
			if ($search_by == 1) 
			{
				$search_field_cond = "and a.booking_no like '$search_string'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) 
			{
				$search_field_cond = "and b.po_number like '$search_string'";
				$search_field_cond_sample = "";
			} 
			else if ($search_by == 3) 
			{
				$search_field_cond = "and b.job_no_mst like '$search_string'";
				$search_field_cond_sample = "";
			} 
			else if ($search_by == 5) 
			{
				$search_field_cond = "and b.grouping like '$search_string'";
				$search_field_cond_sample = "";
			} 
			else if ($search_by == 6) 
			{
				$search_field_cond = "and b.file_no like '$search_string'";
				$search_field_cond_sample = "";
			} 
			else 
			{
				if ($db_type == 0) {
					$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
					$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
				} else {
					$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
					$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
				}
			}
		} 
		else 
		{
			$search_field_cond = "";
			$search_field_cond_sample = "";
		}
		$po_arr = array();
		$po_data = sql_select("select b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($po_data as $row) 
		{
			$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
		}
		$year_cond = "";
		$year_cond_non_order = "";
		$booking_year_condition="";
		$booking_year_non_order_condition="";

		if ($db_type == 0) 
		{
			$year_cond = "YEAR(a.insert_date) as year";
			$year_cond_non_order = "YEAR(s.insert_date) as year";
			if($booking_year>0)
			{
				$booking_year_condition=" and YEAR(a.insert_date)=$booking_year";
				$booking_year_non_order_condition=" and YEAR(s.insert_date)=$booking_year";
			}
		} 
		else if ($db_type == 2) 
		{
			$year_cond = "to_char(a.insert_date,'YYYY') as year";
			$year_cond_non_order = "to_char(s.insert_date,'YYYY') as year";
			if($booking_year>0)
			{
				$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$booking_year";
				$booking_year_non_order_condition=" and to_char(s.insert_date,'YYYY')=$booking_year";
			}
		}

 		// check variable settings if allocation is available or not
		/*$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company_id and variable_list=18 and item_category_id = 1");
		$booking_type_cond = ($variable_set_allocation==1)?" and a.booking_type not in(1,4)":"";*/
		if (trim($data[0]) != "" && ($search_by == 2 || $search_by == 3 || $search_by == 5 || $search_by == 6)) 
		{
			$sql = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id,c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,a.booking_type, a.is_short, $year_cond 
			from wo_booking_mst a,wo_booking_dtls c, wo_po_break_down b 
			where a.booking_no=c.booking_no and c.po_break_down_id=b.id and a.company_id=$company_id and a.buyer_id like '$buyer_id' and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $booking_year_condition 
			group by a.id,a.booking_no, a.booking_date, a.buyer_id,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num, a.booking_type,a.is_short 
			order by a.id";
		} 
		else 
		{
			$sql = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id,c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,a.booking_type, a.is_short, $year_cond 
			from wo_booking_mst a,wo_booking_dtls c, wo_po_break_down b 
			where a.booking_no=c.booking_no and c.po_break_down_id=b.id and a.company_id=$company_id and a.buyer_id like '$buyer_id' and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $booking_year_condition group by a.id,a.booking_no, a.booking_date, a.buyer_id,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num,a.booking_type, a.is_short
			union all
			SELECT s.id, s.booking_no,s.booking_no_prefix_num, s.booking_date, s.buyer_id, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type, 0 as booking_type, 0 as is_short, $year_cond_non_order 
			FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
			WHERE s.booking_no=t.booking_no and s.company_id=$company_id and s.buyer_id like '$buyer_id' and s.status_active =1 and s.is_deleted=0 and s.item_category=2 and (s.fabric_source=1 OR t.fabric_source=1) $search_field_cond_sample $booking_year_non_order_condition
			group by s.id, s.booking_no, s.booking_no_prefix_num, s.booking_date, s.buyer_id, s.item_category, s.delivery_date, s.insert_date
			order by id,type desc";
		}
		//echo $sql;
		$result = sql_select($sql);
		$po_id_arr = $booking_arr = array();
		$job_ids = "";
		foreach ($result  as $value) 
		{
			$po_id_arr[$value[csf("booking_no")]] .= $value[csf("po_break_down_id")] . ",";
			$booking_arr[$value[csf("booking_no")]] = $value[csf("id")] . "**" . $value[csf("booking_no")] . "**" . $value[csf("booking_no_prefix_num")] . "**" . $value[csf("booking_date")] . "**" . $value[csf("buyer_id")] . "**" . $value[csf("item_category")] . "**" . $value[csf("delivery_date")] . "**" . $value[csf("job_no_mst")] . "**" . $value[csf("type")] . "**" . $value[csf("booking_type")] . "**" . $value[csf("is_short")] . "**" . $value[csf("year")];
		}
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Booking No</th>
				<th width="70">Type</th>
				<th width="50">Year</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="88">Item Category</th>
				<th width="75">Delivary date</th>
				<th width="80">Job No</th>
				<th width="70">Order Qnty</th>
				<th width="75">Shipment Date</th>
				<th width="130">Order No</th>
				<th width="70">Internal Ref</th>
				<th>File No</th>
			</thead>
		</table>
		<div style="width:1050px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			foreach ($booking_arr as $row) 
			{
				$data = explode("**",$row);
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$booking_type = '';
				if ($data[9] == 0) {
					$booking_type = 'Sample Without Order';
				} else if ($data[9] == 4) {
					$booking_type = 'Sample';
				} else {
					if ($data[10] == 1) $booking_type = 'Short'; else $booking_type = 'Main';
				}

				$po_qnty_in_pcs = '';
				$po_no = '';
				$min_shipment_date = '';
				$internal_ref = '';
				$file_nos = '';
				if ($data[1] != "" && $data[8] == 0) 
				{
					$po_id = explode(",", rtrim($po_id_arr[$data[1]],","));
					foreach ($po_id as $id) {
						$po_data = explode("**", $po_arr[$id]);
						$po_number = $po_data[0];
						$pub_shipment_date = $po_data[1];
						$po_qnty = $po_data[2];
						$poQntyPcs = $po_data[3];
						$grouping = $po_data[4];
						$file_no = $po_data[5];

						if ($po_no == "") $po_no = $po_number; else $po_no .= "," . $po_number;
						if ($grouping != "") {
							if ($internal_ref == "") $internal_ref = $grouping; else $internal_ref .= "," . $grouping;
						}
						if ($file_no != "") {
							if ($file_nos == "") $file_nos = $file_no; else $file_nos .= "," . $file_no;
						}

						if ($min_shipment_date == '') {
							$min_shipment_date = $pub_shipment_date;
						} else {
							if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
						}

						$po_qnty_in_pcs += $poQntyPcs;
					}
				}

				$internal_ref = implode(",", array_unique(explode(",", $internal_ref)));
				$file_nos = implode(",", array_unique(explode(",", $file_nos)));
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value(<? echo $data[0]; ?>,'<? echo $data[1]; ?>','<? echo $data[8]; ?>','');">
					<td width="30"><? echo $i; ?></td>
					<td width="60" align="left"><p><? echo $data[2]; ?></p>
					</td>
					<td width="70" align="center"><p><? echo $booking_type; ?></p></td>
					<td width="50" align="center"><p><? echo $data[11]; ?></p></td>
					<td width="75" align="center"><? echo change_date_format($data[3]); ?></td>
					<td width="60"><p><? echo $buyer_arr[$data[4]]; ?></p></td>
					<td width="88"><p><? echo $item_category[$data[5]]; ?></p></td>
					<td width="75" align="center"><? echo change_date_format($data[6]); ?></td>
					<td width="80"><p><? echo $data[7]; ?></p></td>
					<td width="70" align="right"><? echo $po_qnty_in_pcs; ?></td>
					<td width="75" align="center"><? echo change_date_format($min_shipment_date); ?></td>
					<td width="130"><p><? echo $po_no; ?></p></td>
					<td width="70"><p><? echo $internal_ref; ?></p></td>
					<td><p><? echo $file_nos; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	   </div>
		<?	
} 
?>

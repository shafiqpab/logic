<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_idle_breakdown_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";
	
	echo create_drop_down( "cbo_floor_id", 120, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id in(1,2) and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
	exit();	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$cbo_company=str_replace("'","",$cbo_company_id);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

	if ($RptType==1) // Show
	{
		$table_width=450+($datediff*225);
		$col_span=($datediff+5)*2;
		
		function convertMinutes2Hours($Minutes)
		{
			if ($Minutes < 0)
			{
				$Min = Abs($Minutes);
			}
			else
			{
				$Min = $Minutes;
			}
			$iHours = Floor($Min / 60);
			$Minutes = ($Min - ($iHours * 60)) / 100;
			$tHours = $iHours + $Minutes;
			if ($Minutes < 0)
			{
				$tHours = $tHours * (-1);
			}
			$aHours = explode(".", $tHours);
			$iHours = $aHours[0];
			if (empty($aHours[1]))
			{
				$aHours[1] = "00";
			}
			$Minutes = $aHours[1];
			if (strlen($Minutes) < 2)
			{
				$Minutes = $Minutes ."0";
			}
			$Minutes = substr($Minutes, 0, 2);
			
			$tHours = $iHours .":". $Minutes;
			return $tHours;
		}
		//echo convertMinutes2Hours(datediff(n,'10-Feb-2019 6:15:00','10-Feb-2019 7:35:00'));

		ob_start();	
		?>
		<div>
	        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" colspan="<? echo $col_span; ?>" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" colspan="<? echo $col_span; ?>" class="form_caption" ><strong style="font-size:16px"><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>  
	               <td align="center"colspan="<? echo $col_span; ?>" class="form_caption" ><strong style="font-size:13px"><? echo "Date From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>
	        </table>
	        <?
			$machine_name=str_replace("'","",$txt_machine_id);
			$floor_name=str_replace("'","",$cbo_floor_id);
			$machine_type=str_replace("'","",$cbo_machine_type);
			
			if (str_replace("'","",$cbo_location_id)==0) $location_id=""; else $location_id=" and location_id=$cbo_location_id";
			if (str_replace("'","",$txt_machine_id)==0 || str_replace("'","",$txt_machine_id)=='') $machine_id=""; else $machine_id=" and id in ( $machine_name )";
			if ($floor_name==0) $floor_id=""; else $floor_id=" and floor_id in ( $floor_name )";
			if ($machine_type==0) $machine_type_cond=""; else $machine_type_cond=" and category_id in ( $machine_type )";
			
			$sql_machine_dtls="Select id, machine_no, brand, origin, prod_capacity, floor_id from lib_machine_name where company_id=$cbo_company_id and status_active=1 and is_deleted=0 $location_id $floor_id $machine_type_cond $machine_id order by id";
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
				$machine_dtls_array[$row[csf('id')]]['brand']=$row[csf('brand')];
				$machine_dtls_array[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$machine_dtls_array[$row[csf('id')]]['prod_capacity']=$row[csf('prod_capacity')];
			}
			
			$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name");
			$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
			?>
	        <table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
	            <thead>
	            	<tr>
	                	<th width="30" rowspan="2">SL</th>
	                    <th colspan="4">Particulars</th>
	                    <?
	                    //$date_data_array=array();
	                    for($j=0;$j<$datediff;$j++)
	                    {
	                        $newDate =add_date(str_replace("'","",$txt_date_from),$j);
	                        //$full_date=change_date_format($newdate);
	                        $days_months=explode('-',$newDate);
	                    ?>
	                        <th colspan="2"><? echo date("d-M",strtotime($newDate)); ?></th>
	                    <?
	                    }  
	                    ?>
	                    <th rowspan="2">Total Idle Duration</th>
	                </tr>
	                <tr>
	                    <th width="80">Machine No</th>
	                    <th width="100">Brand Name</th>
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
	                    	<th width="160">Reason & Remaks</th>
	                        <th width="60">Idle Dur.</th>
	                    <?
	                    }  
	                    ?>
	               </tr>
	            </thead>
	        </table>
	        <div style="width:<? echo $table_width+17; ?>px; overflow-y:scroll; max-height:300px;" id="scroll_body">
	        <table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
				<? 
	            $idle_machine_array=array(); $idle_machine_cause_arr=array();
	            $idol_sql="select id, machine_entry_tbl_id, machine_no, from_date, from_hour, from_minute, to_date, to_hour, to_minute,reporting_date, machine_idle_cause, remarks from pro_cause_of_machine_idle where status_active=1 and is_deleted=0";// and machine_idle_cause in (1,2,3,6,7,8)
	            $idol_sql_result=sql_select($idol_sql); $timeDiffstart='';
	            foreach ($idol_sql_result as $row)
	            {
	                $reporting_date=change_date_format($row[csf('reporting_date')],'','',1);
					$reporting_date_arr[$row[csf('machine_entry_tbl_id')]][$reporting_date][]=array(
						'from_date'=>$row[csf('from_date')],
						'to_date'=>$row[csf('to_date')],
						'from_hour'=>$row[csf('from_hour')],
						'from_minute'=>$row[csf('from_minute')],
						'to_hour'=>$row[csf('to_hour')],
						'to_minute'=>$row[csf('to_minute')],
						'reporting_date'=>$row[csf('reporting_date')]
					);
					
					$reporting_date=change_date_format($row[csf('reporting_date')],'','',1);	
					$from_date=change_date_format($row[csf('from_date')],'','',1);	
					$from_hour=$row[csf('from_hour')]; $from_minute=$row[csf('from_minute')];
	                $to_date=change_date_format($row[csf('to_date')],'','',1);	
					$to_hour=$row[csf('to_hour')]; 
					$to_minute=$row[csf('to_minute')];
	                
	                $start_time='';
	                $start_time=$from_hour.':'.$from_minute.':'.'00';
	                
	                $end_time='';
	                $end_time=$to_hour.':'.$to_minute.':'.'00';
	                $datediff_n = datediff( 'd', $from_date, $to_date);
	               
				   
	                if ($datediff_n==1)
	                {
						$p_time=$end_time;
	                    //$timeDiffstart=datediff(n,$start_time,$p_time);
						
	                   // $idle_machine_array[$row[csf('machine_entry_tbl_id')]][$from_date]+=$timeDiffstart;
	                    $idle_machine_cause_arr[$row[csf('machine_entry_tbl_id')]][$reporting_date]['cause'].=$row[csf('machine_idle_cause')].'****';
	                    $idle_machine_cause_arr[$row[csf('machine_entry_tbl_id')]][$reporting_date]['remarks'].=$row[csf('remarks')].'****';
	                }
	                else
	                {	
	                    for($k=0; $k<$datediff_n; $k++)
	                    {
							/*$p_time="23:59:59";
							$p_endtime="00:00:00";
							$timeDiffstart=datediff(n,$p_time,$start_time);
							$timeDiffend=datediff(n,$p_endtime,$end_time);*/
	                        $newdate_n =change_date_format(add_date(str_replace("'","",$reporting_date),$k),'','',1);
	                        //echo $to_date.'=='.$newdate_n.'<br>'; 
	                        $idle_machine_cause_arr[$row[csf('machine_entry_tbl_id')]][$newdate_n]['cause'].=$row[csf('machine_idle_cause')].'****';
	                        $idle_machine_cause_arr[$row[csf('machine_entry_tbl_id')]][$newdate_n]['remarks'].=$row[csf('remarks')].'****';
	                    }
	                }
	            }
				
				
				
				$i=1;
	            $date_total_duration_arr=array();
	        	$idle_duration_arr=array();
	            foreach ( $machin_arr as $machine_id=>$machine_val )
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                
	                $machine_no=$machine_dtls_array[$machine_val]['machine_no'];
	                $brand=$machine_dtls_array[$machine_val]['brand'];
	                $machine_capacity=$machine_dtls_array[$machine_val]['prod_capacity'];
	                $machine_floor=$machine_dtls_array[$machine_val]['floor_id'];
	               
				   
				    ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" style="height:35px;" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="80"><p><? echo $machine_no;?></p></td>
	                    <td width="100"><p><? echo $brand;?></p></td>
	                    <td width="70"><p><? echo $floor_library[$machine_floor]; ?></p></td>
	                    <td width="70" align="right"><? echo $machine_capacity; ?>&nbsp;</td>
	                    <?
	                    $idle_duration=0;
	                    for($j=0;$j<$datediff;$j++)
	                    {
	                        $newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
	                        $idle_duration=$idle_machine_array[$machine_id][$newdate];
	                        
	                        $idle_duration_arr[$machine_id]+=$idle_machine_array[$machine_id][$newdate];
	                        //$date_total_duration_arr[$newdate]+=$idle_machine_array[$machine_id][$newdate];
							$total_duration+=$idle_machine_array[$machine_id][$newdate];
							$idle_cause="";
							$idle_cause_ex=array_filter(array_unique(explode('****',$idle_machine_cause_arr[$machine_id][$newdate]['cause'])));
							
							
							
						   //-------------------------
							$idle_duration_on_reporting_date=0;
							foreach($reporting_date_arr[$machine_id][$newdate] as $dRows){
								$fromHM=$dRows[from_date].' '.$dRows[from_hour].':'.$dRows[from_minute];
								$toHM=$dRows[to_date].' '.$dRows[to_hour].':'.$dRows[to_minute];
								$idle_duration_on_reporting_date+=datediff(n,$fromHM,$toHM);
								$newArr[$machine_id][$newdate][$dRows[from]]=$dRows[from];	
								$newArr[$machine_id][$newdate][$dRows[to]]=$dRows[to];	
							}
							$date_total_duration_arr[$newdate]+=$idle_duration_on_reporting_date;
							//---------------------------
							
							
							foreach ($idle_cause_ex as $val)
							{
								if($idle_cause=="") $idle_cause=$cause_type[$val]; else $idle_cause.=', '.$cause_type[$val];
							}
							
							$idle_remarks="";
							$idle_remarks_ex=array_filter(array_unique(explode('****',$idle_machine_cause_arr[$machine_id][$newdate]['remarks'])));
							foreach ($idle_remarks_ex as $val)
							{
								if($idle_remarks=="") $idle_remarks=$val; else $idle_remarks.=', '.$val;
							}
	                        ?>
	                        <td width="160"><p><? echo $idle_cause.'<br>'.$idle_remarks; ?></p></td>
	                        <td width="60" align="right">
	                        
	                        <? if($idle_duration_on_reporting_date!=0){?>
	                        <a href="##" onclick="openmypage_idle('<? echo $machine_val; ?>','<? echo $newdate;?>','idle_for');" >
							<?  
							//if($idle_duration!=0) echo convertMinutes2Hours($idle_duration); ?>
	                        <?  echo convertMinutes2Hours($idle_duration_on_reporting_date);?>
	                        </a>
	                        <? } ?>
	                        </td>
	                    <?
	                    }
	                    ?>
	                    <td align="right"><? if($idle_duration_arr[$machine_id]!=0) echo convertMinutes2Hours($idle_duration_arr[$machine_id]); ?></td>
	                </tr>
	                <?
	                $i++;
	            }
				//var_dump($idle_duration_arr);
	            ?>
	        </table>
	        </div>
	        <table cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>" border="1" rules="all" class="tbl_bottom" >
	            <tr>
	            	<td width="30">&nbsp;</td>
	                <td width="80">&nbsp;</p></td>
	                <td width="100">&nbsp;</p></td>
	                <td width="70">&nbsp;</p></td>
	                <td width="70"><strong>Total :</strong></td>
					<?
	                for($j=0; $j<$datediff; $j++)
	                {
	                    $newdate =change_date_format(add_date(str_replace("'","",$txt_date_from),$j),'','',1);
	                    //$date_data_array[$newdate]=$newdate;
	                    ?>
	                    <td width="160">&nbsp;</td>
	                    <td width="60" align="right"><? if ($date_total_duration_arr[$newdate]!=0) echo convertMinutes2Hours($date_total_duration_arr[$newdate]); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><strong><? echo convertMinutes2Hours($total_duration); ?></strong>&nbsp;</td>
	            </tr>
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
		//echo "$total_data####$filename";
	}

	else  // Show 2
	{
		
		function convertMinutesHours($from_date,$from_hour,$from_minute,$to_date,$to_hour,$to_minute)
		{
			//$Minutes=($to_hour-$from_hour);//+$from_minute+$to_minute;
			$start_date_time=$from_date.'.'.$from_hour.'.'.$from_minute; 
			$idle_start_date_time=strtotime($start_date_time);

			$end_date_time=$to_date.'.'.$to_hour.'.'.$to_minute; 
			$idle_end_date_time=strtotime($end_date_time);

			$diff = ($idle_end_date_time - $idle_start_date_time);
			$total = $diff/60;
		    return $total_idle_time = sprintf("%02d:%02d", floor($total/60), $total%60);
		}

        function secToHR($seconds) 
        {
		  $hours = floor($seconds / 3600);
		  $minutes = floor(($seconds / 60) % 60);
		  return "$hours:$minutes";
		}
			
		
		ob_start();	
		$table_width=1030;
		?>
		<div style="width:1030px; margin: 0 auto;">
	        <table width="<? echo $table_width; ?>" colspan="14" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" class="form_caption"  colspan="14"><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" class="form_caption" colspan="14" ><strong style="font-size:16px"><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>  
	               <td align="center" class="form_caption" colspan="14" ><strong style="font-size:13px"><? echo "Date From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>
	        </table>
	        <?
			$machine_name=str_replace("'","",$txt_machine_id);
			$floor_name=str_replace("'","",$cbo_floor_id);
			$machine_type=str_replace("'","",$cbo_machine_type);
			$cbo_search_by=str_replace("'","",$cbo_search_by);
			//echo $cbo_search_by;die;
			$search_by_arr=array(1=>"Reporting Date",2=>"From Date");
			
			if (str_replace("'","",$cbo_location_id)==0) $location_id=""; else $location_id=" and a.location_id=$cbo_location_id";
			if (str_replace("'","",$txt_machine_id)==0 || str_replace("'","",$txt_machine_id)=='') $machine_id=""; else $machine_id=" and a.id in ( $machine_name )";
			if ($floor_name==0) $floor_id=""; else $floor_id=" and a.floor_id in ( $floor_name )";
			if ($machine_type==0) $machine_type_cond=""; else $machine_type_cond=" and a.category_id in ( $machine_type )";

			//$str_cond=" and b.from_date between $txt_date_from and  $txt_date_to";
			//$str_cond1=" and b.reporting_date between $txt_date_from and  $txt_date_to";
			
			if($cbo_search_by==1)
			{
				
				$str_cond=" and b.reporting_date between ".$txt_date_from." and ".  $txt_date_to;
				
			}else{
				$str_cond =" and b.from_date between ".$txt_date_from ." and ".  $txt_date_to;
				

			}	
			
			$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name");
			$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
			$main_sql = "SELECT a.id, a.brand, a.origin, a.prod_capacity, a.location_id, a.floor_id,
			b.machine_entry_tbl_id, b.machine_no, b.from_date, b.from_hour, b.from_minute, b.to_date, b.to_hour, b.to_minute, b.reporting_date, b.machine_idle_cause, b.remarks 
			from lib_machine_name a, pro_cause_of_machine_idle b
			where a.id=b.machine_entry_tbl_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $machine_type_cond $location_id $floor_id $machine_id $str_cond  order by a.id";
			//echo $main_sql;die;

			$sql_machine=sql_select($main_sql);
			$count_data=count($sql_machine);

			$machin_arr=array();
			$machine_dtls_array=array();
			
			foreach ( $sql_machine as $row )
			{
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['brand']=$row[csf('brand')];
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['origin']=$row[csf('origin')];
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['prod_capacity']=$row[csf('prod_capacity')];
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['to_date']=$row[csf('to_date')];
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['to_hour']=$row[csf('to_hour')];
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['to_minute']=$row[csf('to_minute')];
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['machine_idle_cause']=$row[csf('machine_idle_cause')];
				$machin_arr[$row[csf('from_date')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('from_hour')]][$row[csf('from_minute')]]['remarks']=$row[csf('remarks')];
			}
			/*echo "<pre>";
			print_r($machin_arr);*/

			?>
	        <table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
	            <thead>
	            	<tr>
	                	<th width="30">SL</th>
	                    <th width="80">Date</th>
	                    <th width="80">Machine No</th>
	                    <th width="100">Brand Name</th>
	                    <th width="70">Floor</th>
	                    <th width="70">Capacity</th>
	                    <th width="70">Idle Hour</th>
	                    <th width="70">Idel Minutes</th>
	                    <th width="80">Idle To Date</th>
	                    <th width="60">Idle To Hour</th>
	                    <th width="60">Idel To Minutes</th>
	                    <th width="60">Idle Durration</th>
                    	<th width="100">Reason</th>               
						<th width="100">Based On</th>         
                    	<th width="100">Remaks</th>
	               </tr>
	            </thead>
	        </table>
	        <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:300px;" id="scroll_body">
	        	<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?> "  border="1" rules="all" class="rpt_table" >
					<?			
					function familyName($fname) 
					{
					    echo "$fname Refsnes.<br>";
					}		
					$i=1;
		            $date_total_duration_arr=array();
		        	$idle_duration_arr=array();
		        	$grand_total_idle=$time_seconds=0;
		            foreach ($machin_arr as $from_date_key=>$from_date )
		            {
		            	foreach ($from_date as $location_id=>$location_val )
		            	{
		            		foreach ($location_val as $floor_id=>$floor_val )
		            		{
		            			foreach ($floor_val as $machine_no_key=>$machine_no_val )
			            		{
			            			foreach ($machine_no_val as $from_hour_key=>$from_hour_val )
				            		{
				            			foreach ($from_hour_val as $from_minute_key=>$row )
					            		{

							                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										    ?>
							                <tr bgcolor="<? echo $bgcolor; ?>" style="height:35px;" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							                    <td width="30"><? echo $i; ?></td>
							                    <td width="80"><p><? echo change_date_format($from_date_key); ?></p></td>
							                    <td width="80"><p><? echo $machine_no_key;?></p></td>
							                    <td width="100"><p><? echo $row['brand'];?></p></td>
							                    <td width="70"><p><? echo $floor_library[$floor_id]; ?></p></td>
							                    <td width="70" align="right"><? echo $row['prod_capacity']; ?></td>
							                    <td width="70" align="right"><? echo $from_hour_key; ?></td>
							                    <td width="70" align="right"><? echo $from_minute_key; ?></td>
							                    <td width="80" align="right"><? echo change_date_format($row['to_date']); ?></td>
							                    <td width="60" align="right"><? echo $row['to_hour']; ?></td>
							                    <td width="60" align="right"><? echo $row['to_minute']; ?></td>
							                    <td width="60" align="right"><? echo $total_idle=convertMinutesHours( $from_date_key,$from_hour_key,$from_minute_key,$row['to_date'],$row['to_hour'],$row['to_minute'] ); ?></td>
						                        <td width="100"><p><? echo $cause_type[$row['machine_idle_cause']]; ?></p></td>
												 <td width="100"><p><? echo $search_by_arr[$cbo_search_by];?></p></td>
												
						                        <td width="100"><p><? echo $row['remarks']; ?></p></td>
							                </tr>
							                <?
							                $i++;
							                //echo $total_idle;
							                //$str_time = "2:50";
											sscanf($total_idle, "%d:%d:%d", $hours, $minutes, $seconds);
											$time_seconds = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
											// $grand_total_idle+=$total_idle;
							                $grand_total_idle+=$time_seconds;
							                
		                				}
				            		}
			            		}
		            		}
		            	}
		            }
		            ?>
	        	</table>
		        <table cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>" border="1" rules="all" class="tbl_bottom" >
		            <tr>
		            	<td width="30"></td>
		                <td width="80"></td>
		                <td width="80"></td>
		                <td width="100"></td>
		                <td width="70"></td>
		                <td width="70"><strong>Total :</strong></td>
		                <td width="70"></td>
		                <td width="70"></td>
	                    <td width="80"></td>
	                    <td width="60" align="right"></td>
	                    <td width="60" align="right"></td>
	                    <td width="60" align="right"><? echo secToHR($grand_total_idle); ?></td>
	                    <td width="100" align="right"><?  ?></td>
						<td width="100" align="right"><?  ?></td>
		                <td width="100" align="right"><strong></strong></td>
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
		//echo "$total_data####$filename";
	}
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
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id='$im_data[2]' and a.company_id=$im_data[0] and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
	//echo  $sql;
	
	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "180,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;
	
   exit(); 
}

if($action=="idle_for")
{
	echo load_html_head_contents("Cause of Machine Idle Pop Up", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	
	list($reporting_date)=explode('*',$date);
	
	if($db_type==0)
	{
		$reporting_date=change_date_format($reporting_date,"yyyy-mm-dd", "-",1);
		//$from_date=change_date_format($from,"yyyy-mm-dd", "-",1);
		//$to_date=change_date_format($to,"yyyy-mm-dd", "-",1);
	}
	elseif($db_type==2)
	{
		$reporting_date=$reporting_date;
		//$from_date=$from;
		//$to_date=$to;
	}
	
	
	  //$datCon=" and from_date >='$from' and to_date<='$reporting_date'";
	  $datCon=" and reporting_date ='$reporting_date'";
	
	
	
	//$sql= "SELECT id, machine_entry_tbl_id, machine_no, from_date, from_hour, from_minute, to_date, to_hour, to_minute, machine_idle_cause, remarks from  pro_cause_of_machine_idle where machine_entry_tbl_id='$machine_id' and '$cng_date' between from_date and to_date and is_deleted=0 and status_active=1";
	
	
	$sql= "SELECT id, machine_entry_tbl_id, machine_no, from_date, from_hour, from_minute, to_date, to_hour, to_minute, machine_idle_cause, remarks from  pro_cause_of_machine_idle where machine_entry_tbl_id='$machine_id' $datCon  and is_deleted=0 and status_active=1";
	
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
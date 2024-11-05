<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );		 
}

if ($action=="load_php_dtls_form")
{
	$data=explode('_',$data);
	//echo $data;
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $data[1], $data[0]);
	/*echo "<pre>";
	print_r($daysinmonth);
	echo "<pre>";*/
	$c_date="$data[0]-$data[1]-01";
	$k=0; $kk=1; 
	for( $i = 1; $i <= $daysinmonth; $i++ ) 
	{  

		$day_txt=$i<10?'0'.$i:$i;
		$month_txt=$data[1]<10?'0'.$data[1]:$data[1];
		$date=$day_txt.'-'.$month_txt.'-'.$data[0];
		$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		$day_name = date('D', strtotime($date));
		
		if($day_name=='Fri' && $mdr[$i]['day_status']==""){$style='style="color: red;"';$status_select=2;} 
		else if($mdr[$i]['day_status']==2){$style='style="color: red;"';$status_select=2;}
		else{$style='';$status_select=1;}


	?>
		<tr align="center" <?php echo $style; ?>>
			<td>
            	<input type="hidden" id="update_id_dtls_<? echo $i; ?>" name="update_id_dtls_<? echo $i; ?>" value="" />
				<input type="text" name="txt_date_<? echo $i; ?>" id="txt_date_<? echo $i; ?>" class="datepicker" style="width:67px" value="<? echo  change_date_format(add_date($c_date, $k));  ?>" readonly />
			</td>
			 <td width="40" id="tdDay_<? echo $i; ?>"><?php echo $day_name; ?></td>
			<td>
				<?
				$day_select=1;
				if ($day_name=='Fri') {
					$day_select=2;
					
				}
					$day_status=array(1=>"Open",2=>"Closed");
					echo create_drop_down( "cbo_day_status_$i", 72,$day_status,"", 0, "-- Select --", $day_select,"open_close(this.value,$i,$kk)" );  //open_close(this.value,$i,$kk)
				?>
			</td>
			<td>
				<input type="text" name="txt_no_of_line_<? echo $i; ?>" id="txt_no_of_line_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onblur="calculate_capacity_min_pcs(this.value,<? echo $i; ?>);" />
			</td>
			<td>
				<input type="text" name="txt_capacity_min_<? echo $i; ?>" id="txt_capacity_min_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" readonly="readonly" />
			</td>
			<td>
				<input type="text" name="txt_capacity_pcs_<? echo $i; ?>" id="txt_capacity_pcs_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" readonly="readonly" />
			</td>
		</tr>
	 <? 
	 $k++;	
	 $kk++; 
	 } 	
}

if ($action=="load_php_dtls_form_update_val")
{
	$data=explode('_',$data);
	// cbo_location_id
	$mc_per_line=$data[5];
	$smv_basic=$data[6];
	$smv_basic_cond="";$machine_line_cond="";
	//if($smv_basic) $smv_basic_cond="and a.basic_smv=$smv_basic";
	//if($mc_per_line) $machine_line_cond="and a.avg_machine_line=$mc_per_line";
	$sql="SELECT a.id, b.id as bid, b.month_id, b.date_calc, b.day_status, b.no_of_line, b.capacity_min, b.capacity_pcs, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and a.capacity_source=$data[4] and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $smv_basic_cond $machine_line_cond  order by b.date_calc";
	//    $sql="SELECT a.id, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and a.capacity_source=$data[4] and b.WORKING_DAY is not null and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $smv_basic_cond $machine_line_cond";
	//echo "SELECT a.id, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and a.capacity_source=$data[4] and b.WORKING_DAY is not null and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $smv_basic_cond $machine_line_cond";
 
	//echo $sql;die;

	$sql_result = sql_select($sql);
	//echo "<pre>";
	//print_r($sql_result);die;
//
	$i=0;
	$data_count=count($sql_result);
	foreach ($sql_result as $inf)
	{
		$mstIdarr[$inf[csf("id")]]=$inf[csf("id")];
		$i++;
		if ($i==$data_count)
		{
			
			echo "document.getElementById('txt_avg_mch_line').value 	= ".$inf[csf("avg_machine_line")].";\n";
			echo "document.getElementById('txt_basic_smv').value 	= ".$inf[csf("basic_smv")].";\n";
			echo "document.getElementById('txt_efficiency_per').value 	= ".$inf[csf("effi_percent")].";\n";
			echo "document.getElementById('update_id').value= '".$inf[csf("id")]."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
			
			$mstid=$inf[csf("id")];
		}
	}
if(count($mstIdarr)>0)
{
	$mstId_cond=" and a.id in(".implode(",",$mstIdarr).")";
}
//else $mstId_cond=" and a.id in(0)";
	$sqlDtls="SELECT b.id as bid, b.month_id, b.date_calc, b.day_status, b.no_of_line, b.capacity_min, b.capacity_pcs from lib_capacity_calc_mst a join lib_capacity_calc_dtls b on a.id=b.mst_id where a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and a.capacity_source=$data[4]   and b.status_active=1 and b.is_deleted=0  order by b.date_calc";
	 // echo $sqlDtls; 

	$sqlDtlsResult=sql_select($sqlDtls); $k=0; $capacity_min_sum=$capacity_pcs_sum=0;
	foreach ($sqlDtlsResult as $row)
	{
		$capacity_min=$capacity_pcs=0;
		$capacity_min=$row[csf('capacity_min')];
		$capacity_min_sum += $capacity_min;
		
		$capacity_pcs=$row[csf('capacity_pcs')];
		$capacity_pcs_sum += $capacity_pcs;
		$k++;
		echo "document.getElementById('cbo_day_status_".$k."').value 	= '".$row[csf("day_status")]."';\n";
		echo "document.getElementById('txt_no_of_line_".$k."').value 	= '".$row[csf("no_of_line")]."';\n";
		echo "document.getElementById('line_id').value					= '".$row[csf("no_of_line")]."';\n";
		echo "document.getElementById('txt_capacity_min_".$k."').value 	= '".$row[csf("capacity_min")]."';\n";
		echo "document.getElementById('txt_capacity_pcs_".$k."').value 	= '".$row[csf("capacity_pcs")]."';\n";
		echo "document.getElementById('update_id_dtls_".$k."').value 	= '".$row[csf("bid")]."';\n";
		
		if ($row[csf("day_status")]==2)
		{
			echo "document.getElementById('txt_capacity_min_".$k."').value 			= '';\n";
			echo "document.getElementById('txt_capacity_pcs_".$k."').value 			= '';\n";
			echo "disable_enable_fields( 'txt_capacity_min_".$k."*txt_capacity_pcs_".$k."', 1, '', '');\n";
		}
	}
	echo "document.getElementById('total_min').value 							= '".$capacity_min_sum."';\n";
	echo "document.getElementById('total_pcs').value 							= '".$capacity_pcs_sum."';\n";
	
	if($data_count==0)
	{
		echo "document.getElementById('txt_avg_mch_line').value 	= '';\n";
		echo "document.getElementById('txt_basic_smv').value 	= '';\n";
		echo "document.getElementById('txt_efficiency_per').value 	= '';\n";
		echo "document.getElementById('update_id').value= '';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
	}
	 
	exit;
}

if ($action=="load_php_dtls_form_update")
{
	$data=explode('_',$data);
	$mon_id=$data[4];
	$basic_smv=$data[5];
	$mc_line=$data[6];
	$mon_id_cond="";$basic_smv_cond="";$mc_line_cond="";
	if($mon_id) $mon_id_cond="and b.month_id=$mon_id";
	if($basic_smv) $basic_smv_cond="and c.basic_smv=$basic_smv";
	if($mc_line) $mc_line_cond="and c.avg_mch_line=$mc_line";

	if($mon_id>0){
		$sql_main=sql_select("SELECT a.id, a.comapny_id, a.capacity_source, a.year, a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b WHERE a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.capacity_source=$data[3] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $mon_id_cond group by a.id, a.comapny_id, a.capacity_source, a.year, a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent order by a.id asc");
		foreach($sql_main as $row){
			$mstIdarr[$row[csf("id")]]=$row[csf("id")];
				echo "document.getElementById('txt_avg_mch_line').value= '".$row[csf("avg_machine_line")]."';\n";
				echo "document.getElementById('txt_basic_smv').value= '".$row[csf("basic_smv")]."';\n";
				echo "document.getElementById('txt_efficiency_per').value= '".$row[csf("effi_percent")]."';\n";
		}
	}
	
	if(count($mstIdarr)>0)
	{
		$mstId_cond=" and a.id in(".implode(",",$mstIdarr).")";
	}
	//else $mstId_cond=" and a.id in(0)";

	$sql_res=sql_select("SELECT a.id, a.comapny_id, a.capacity_source, a.year, a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent, c.avg_rate, c.id as year_id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs, c.basic_smv as smv2, c.efficiency_per, c.avg_mch_line FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c WHERE a.id=c.mst_id and a.id=b.mst_id and  b.mst_id=c.mst_id  and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.capacity_source=$data[3] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $mon_id_cond $basic_smv_cond $mc_line_cond group by a.id, a.comapny_id, a.capacity_source, a.year, a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent, c.avg_rate, c.id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs, c.basic_smv, c.efficiency_per, c.avg_mch_line order by c.month_id asc");//and c.working_day is not null
	 
	 

	// echo $sql_res;die; 

	$i=0;
	$month_count=count($sql_res);

	foreach ($sql_res as $row)
	{
		$working_day=$row[csf('working_day')];
		$working_day_sum += $working_day;

		$capacity_month_min=$row[csf('capacity_month_min')];
		$capacity_month_min_sum += $capacity_month_min;

		$capacity_month_pcs=$row[csf('capacity_month_pcs')];
		$capacity_month_pcs_sum += $capacity_month_pcs;
		
		$i++;
		if ($i==$month_count)
		{
			/* echo "document.getElementById('txt_avg_mch_line').value= '".$row[csf("avg_machine_line")]."';\n";
			echo "document.getElementById('txt_basic_smv').value= '".$row[csf("basic_smv")]."';\n";
			echo "document.getElementById('txt_efficiency_per').value= '".$row[csf("effi_percent")]."';\n"; */
			//echo "document.getElementById('txt_avg_rate').value= '".$row[csf("avg_rate")]."';\n";

			echo "document.getElementById('txt_working_day_total').value= '".$working_day_sum."';\n";
			echo "document.getElementById('txt_capacity_min_total').value= '".$capacity_month_min_sum."';\n";
			echo "document.getElementById('txt_capacity_pcs_total').value= '".$capacity_month_pcs_sum."';\n";
			//echo "document.getElementById('update_id').value= '".$row[csf("id")]."';\n";
			if ($row[csf("id")]!="")
			{
				echo "disable_enable_fields( 'cbo_company_id*cbo_capacity_source*cbo_location_id*cbo_year', 1, '', '');\n";
			}
		}
		echo "document.getElementById('txt_sl_no_".$row[csf("month_id")]."').value = '".$row[csf("month_id")]."';\n";
		echo "document.getElementById('txt_month_".$row[csf("month_id")]."').value = '".$months[$row[csf("month_id")]]."';\n";
		echo "document.getElementById('txt_month_id_".$row[csf("month_id")]."').value = '".$row[csf("month_id")]."';\n";
		echo "document.getElementById('txt_working_day_".$row[csf("month_id")]."').value = '".$row[csf("working_day")]."';\n";
		echo "document.getElementById('txt_year_capacity_min_".$row[csf("month_id")]."').value = '".$row[csf("capacity_month_min")]."';\n";
		echo "document.getElementById('txt_year_capacity_pcs_".$row[csf("month_id")]."').value = '".$row[csf("capacity_month_pcs")]."';\n";
		echo "document.getElementById('update_id_year_dtls_".$row[csf("month_id")]."').value = '".$row[csf("year_id")]."';\n";
	
	
		echo "document.getElementById('avg_rate_".$row[csf("month_id")]."').value = '".$row[csf("avg_rate")]."';\n";	
	}
	exit;
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  
	
	if ($operation==0)  // Insert Here==================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$location_id=str_replace("'","",$cbo_location_id);
		$company_id=str_replace("'","",$cbo_company_id);
		$year_id=str_replace("'","",$cbo_year);
		$capacity_source_id=str_replace("'","",$cbo_capacity_source);
		$month_id=str_replace("'","",$cbo_month);
		$basic_smv=str_replace("'","",$txt_basic_smv);
		$efficiency_per=str_replace("'","",$txt_efficiency_per);
		 $basic_smv_cond=""; $efficiency_per_cond="";
		
		if($basic_smv) $basic_smv_cond="and a.basic_smv=$basic_smv";
		if($efficiency_per) $efficiency_per_cond="and a.effi_percent=$efficiency_per";
		
		// $sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where  a.id=b.mst_id and a.comapny_id=$company_id and a.location_id=$location_id and a.year=$year_id and b.month_id=$month_id and a.capacity_source=$capacity_source_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $basic_smv_cond $efficiency_per_cond order by b.date_calc");
		$sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b,lib_capacity_year_dtls c where  a.id=b.mst_id  and a.id=c.mst_id and b.mst_id=c.mst_id  and c.month_id=b.month_id and c.year=a.year and a.comapny_id=$company_id and a.location_id=$location_id and a.year=$year_id and b.month_id=$month_id and a.capacity_source=$capacity_source_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $basic_smv_cond $efficiency_per_cond order by b.date_calc");
		$tot_row=count($sql_result);
		if($tot_row>0)
		{
			$msg="Duplicate Entry found in Same Following Item Heads:Company,Source,Location,Year,Month, Basic SAM,Efficency";
			echo "11**".$msg;
			disconnect($con);
			die;
		}
		 $update_id=str_replace("'",'',$update_id);
		if($update_id=="")
		{
			$mst_id=return_next_id( "id", " lib_capacity_calc_mst", 1 ) ; 
			$field_array_mst="id,comapny_id,capacity_source,year,location_id,avg_machine_line,basic_smv,effi_percent,avg_rate,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array_mst="(".$mst_id.",".$cbo_company_id.",".$cbo_capacity_source.",".$cbo_year.",".$cbo_location_id.",".$txt_avg_mch_line.",".$txt_basic_smv.",".$txt_efficiency_per.",".$txt_avg_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		}
		else
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_mst="capacity_source*year*location_id*avg_machine_line*basic_smv*effi_percent*avg_rate*updated_by*update_date*status_active*is_deleted";
			
			$data_array_mst="".$cbo_capacity_source."*".$cbo_year."*".$cbo_location_id."*".$txt_avg_mch_line."*".$txt_basic_smv."*".$txt_efficiency_per."*".$txt_avg_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update2("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		$data_array_dtls="";
		$dtls_id=return_next_id( "id", "lib_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,date_calc,day_status,no_of_line,capacity_min,capacity_pcs";
		for($i=1; $i<=$tot_row_date; $i++)
		{
			$txt_date= "txt_date_".$i;
			$cbo_day_status="cbo_day_status_".$i;
			$txt_no_of_line="txt_no_of_line_".$i;
			$txt_capacity_min="txt_capacity_min_".$i;
			$txt_capacity_pcs="txt_capacity_pcs_".$i;
			$update_id_dtls="update_id_dtls_".$i;

			if(str_replace("'",'',$$update_id_dtls)=="")
			{
				if ($i!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$mst_id.",".$cbo_month.",".$$txt_date.",".$$cbo_day_status.",".$$txt_no_of_line.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
				$dtls_id=$dtls_id+1;
			}
		}
			
		$dtls_year_id=return_next_id( "id", "lib_capacity_year_dtls", 1 ); 
		$field_array_year="id,mst_id,month_id,working_day,capacity_month_min,capacity_month_pcs,avg_rate,efficiency_per,basic_smv,avg_mch_line";
		
			for($i=1; $i<=$tot_row_year; $i++)
			{
				$txt_month= "txt_month_id_".$i;
				$txt_working_day="txt_working_day_".$i;
				$txt_year_capacity_min="txt_year_capacity_min_".$i;
				$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
				$update_id_year_dtls="update_id_year_dtls_".$i;
				$avg_rate="avg_rate_".$i;
				$txt_basic_smv="txt_basic_smv_".$i;
				$txt_efficiency_per="txt_efficiency_per_".$i;
				$txt_avg_mch_line="txt_avg_mch_line_".$i;
				//echo "10**=A".'='.str_replace("'",'',$$txt_year_capacity_min).'='.str_replace("'",'',$$update_id_year_dtls);
				$year_capacity_min=str_replace("'",'',$$txt_year_capacity_min);

				if(str_replace("'",'',$$update_id_year_dtls)=="")
				{
					if ($i!=1) $data_array_year .=",";
					$data_array_year.="(".$dtls_year_id.",".$mst_id.",".$$txt_month.",".$$txt_working_day.",".$$txt_year_capacity_min.",".$$txt_year_capacity_pcs.",".$$avg_rate.",".$$txt_efficiency_per.",".$$txt_basic_smv.",".$$txt_avg_mch_line.")";
					$dtls_year_id=$dtls_year_id+1;
					
				}
				else
				{
					//echo "10**=B";
					$id_arr_year=array();
					$data_array_year=array();
					$field_array_year_update="working_day*capacity_month_min*capacity_month_pcs*avg_rate*basic_smv*efficiency_per*avg_mch_line";
					for($i=1; $i<=12; $i++)
					{
						$txt_month= "txt_month_id_".$i;
						$txt_working_day="txt_working_day_".$i;
						$txt_year_capacity_min="txt_year_capacity_min_".$i;
						$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
						$update_id_year_dtls="update_id_year_dtls_".$i;
						$avg_rate="avg_rate_".$i;
						$txt_basic_smv="txt_basic_smv_".$i;
						$txt_efficiency_per="txt_efficiency_per_".$i;
						$txt_avg_mch_line="txt_avg_mch_line_".$i;

						if(str_replace("'",'',$$update_id_year_dtls)!="" && str_replace("'",'',$$txt_month)==str_replace("'",'',$cbo_month))
						{
							$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
							$data_array_year_update[str_replace("'",'',$$update_id_year_dtls)] =explode("*",("".$$txt_working_day."*".$$txt_year_capacity_min."*".$$txt_year_capacity_pcs."*".$$avg_rate."*".$$txt_efficiency_per."*".$$txt_basic_smv."*".$$txt_avg_mch_line.""));
						}
					}
				}
			}
		 if(str_replace("'",'',$update_id)=="")
		{
			$rID=sql_insert("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,0);	
		}
		else
		{
			$rID=sql_update("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		  //echo "10**INSERT INTO lib_capacity_year_dtls ($field_array_year) values $data_array_year"; die;
		$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		if($data_array_year !="")
		{
			$rID2=sql_insert("lib_capacity_year_dtls",$field_array_year,$data_array_year,1);
		}
		if(count($data_array_year_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "lib_capacity_year_dtls", "id", $field_array_year_update, $data_array_year_update, $id_arr_year ),1);

		}
	 	
		 // echo "10**". $rID .'&&'. $rID1 .'&&'. $rID2;oci_rollback($con); die;
		
		
		if($db_type==0)
		{
			if( $rID && $rID1 && $rID2 )
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$mst_id);
			}
		}
		if($db_type==2 || $db_type==1 )
			{
			if( $rID && $rID1 && $rID2 )
				{
					oci_commit($con); 
					echo "0**".str_replace("'",'',$mst_id);
				}
			else
				{
					oci_rollback($con); 
					echo "10**".str_replace("'",'',$mst_id);
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$location_id=str_replace("'","",$cbo_location_id);
		$updateid=str_replace("'",'',$update_id);
		$company_id=str_replace("'","",$cbo_company_id);
		$year_id=str_replace("'","",$cbo_year);
		$capacity_source_id=str_replace("'","",$cbo_capacity_source);
		$month_id=str_replace("'","",$cbo_month);
		$basic_smv=str_replace("'","",$txt_basic_smv);
		$efficiency_per=str_replace("'","",$txt_efficiency_per);
		$basic_smv_cond=""; $efficiency_per_cond="";
		
		if($basic_smv) $basic_smv_cond="and a.basic_smv=$basic_smv";
		if($efficiency_per) $efficiency_per_cond="and a.effi_percent=$efficiency_per";
		
		// $sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$company_id and a.location_id=$location_id and a.year=$year_id and b.month_id=$month_id and a.capacity_source=$capacity_source_id and a.id!=$updateid and  a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0  $basic_smv_cond $efficiency_per_cond order by b.date_calc");
		$sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b,lib_capacity_year_dtls c where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id  and c.month_id=b.month_id  and c.year=a.year and a.comapny_id=$company_id and a.location_id=$location_id and a.year=$year_id and b.month_id=$month_id and a.capacity_source=$capacity_source_id and a.id!=$updateid and  a.status_active=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  and  b.status_active=1 and b.is_deleted=0  $basic_smv_cond $efficiency_per_cond order by b.date_calc");
		$tot_row=count($sql_result);
		if($tot_row>0)
		{
			$msg="Duplicate Entry found in Same Following Item Heads:Company,Source,Location,Year,Month, Basic SAM,Efficency";
			echo "11**".$msg;
			disconnect($con);
			die;
		}
		
		$field_array_mst="capacity_source*year*location_id*avg_machine_line*basic_smv*effi_percent*avg_rate*updated_by*update_date*status_active*is_deleted";
		
		$data_array_mst="".$cbo_capacity_source."*".$cbo_year."*".$cbo_location_id."*".$txt_avg_mch_line."*".$txt_basic_smv."*".$txt_efficiency_per."*".$txt_avg_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

		$id_arr=array();
		$data_array_dtls_up=array();
		$field_array_dtls_up="day_status*no_of_line*capacity_min*capacity_pcs";
		
		$dtls_id=return_next_id( "id", "lib_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,date_calc,day_status,no_of_line,capacity_min,capacity_pcs";
		
		for($i=1; $i<=$tot_row_date; $i++)
		{
			$txt_date= "txt_date_".$i;
			$cbo_day_status="cbo_day_status_".$i;
			$txt_no_of_line="txt_no_of_line_".$i;
			$txt_capacity_min="txt_capacity_min_".$i;
			$txt_capacity_pcs="txt_capacity_pcs_".$i;
			$update_id_dtls="update_id_dtls_".$i;			
			
			if(str_replace("'",'',$$update_id_dtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_dtls_up[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$cbo_day_status.",".$$txt_no_of_line.",".$$txt_capacity_min.",".$$txt_capacity_pcs.""));
			}
			else 
			{
				if ($i!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$cbo_month.",".$$txt_date.",".$$cbo_day_status.",".$$txt_no_of_line.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
				$dtls_id=$dtls_id+1;
			}
		}
		$id_arr_year=array();
		$data_array_year_update=array();
		$field_array_year_update="working_day*capacity_month_min*capacity_month_pcs*avg_rate*basic_smv*efficiency_per*avg_mch_line";
		$dtls_year_id=return_next_id( "id", "lib_capacity_year_dtls", 1 ); 
		$field_array_year="id,mst_id,month_id,working_day,capacity_month_min,capacity_month_pcs,avg_rate,efficiency_per,basic_smv,avg_mch_line";
		for($i=1; $i<=12; $i++)
		{
			$txt_month= "txt_month_id_".$i;
			$txt_working_day="txt_working_day_".$i;
			$txt_year_capacity_min="txt_year_capacity_min_".$i;
			$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
			$update_id_year_dtls="update_id_year_dtls_".$i;
			$avg_rate="avg_rate_".$i;
			$txt_basic_smv="txt_basic_smv_".$i;
			$txt_efficiency_per="txt_efficiency_per_".$i;
			$txt_avg_mch_line="txt_avg_mch_line_".$i;			
		
			if(str_replace("'",'',$$update_id_year_dtls)=="")
			{
				if ($i!=1) $data_array_year .=",";
				$data_array_year.="(".$dtls_year_id.",".$mst_id.",".$$txt_month.",".$$txt_working_day.",".$$txt_year_capacity_min.",".$$txt_year_capacity_pcs.",".$$avg_rate.",".$$txt_efficiency_per.",".$$txt_basic_smv.",".$$txt_avg_mch_line.")";
				$dtls_year_id=$dtls_year_id+1;
			}
			if(str_replace("'",'',$$update_id_year_dtls)!=""  && str_replace("'",'',$$txt_month)==str_replace("'",'',$cbo_month))
			{
				$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
				$data_array_year_update[str_replace("'",'',$$update_id_year_dtls)] =explode("*",("".$$txt_working_day."*".$$txt_year_capacity_min."*".$$txt_year_capacity_pcs."*".$$avg_rate."*".$$txt_efficiency_per."*".$$txt_basic_smv."*".$$txt_avg_mch_line.""));
			}
		}
		$rID=sql_update("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		$rID1=execute_query(bulk_update_sql_statement( "lib_capacity_calc_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $id_arr ),0);
		
		if(str_replace("'",'',$data_array_dtls)!="")
		{
			$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		if($data_array_year !="")
		{
			$rID2=sql_insert("lib_capacity_year_dtls",$field_array_year,$data_array_year,1);
		}
		if(count($data_array_year_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "lib_capacity_year_dtls", "id", $field_array_year_update, $data_array_year_update, $id_arr_year ),1);

		}
		
		if($db_type==0)
		{
			if( $rID && $rID1 && $rID2 )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
			{
			   if( $rID && $rID1 && $rID2 )
					{
						oci_commit($con);  
						echo "1**".str_replace("'",'',$update_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".str_replace("'",'',$update_id);
					}
			}
		disconnect($con);
		die;
	}
}

if($action=="load_php_dtls_form_return_id_date")
{
	$qry_result=sql_select( "select id,mst_id from lib_capacity_calc_dtls where mst_id='$data' and status_active=1");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		
	}
	echo $id;
}

if($action=="load_php_dtls_form_return_id_year")
{
	$qry_res=sql_select( "select id,mst_id from lib_capacity_year_dtls where mst_id='$data' and status_active=1");
	foreach ($qry_res as $inf)
	{
		if($id_year=="") $id_year=$inf[csf("id")]; else $id_year.="*".$inf[csf("id")];
		
	}
	echo $id_year;
}

if($action=="working_hour")
{
	$data_ex=explode("_",$data);
	//echo $data_ex[1];
	//applying_period_date 	
	if($db_type==0) 
	{
		$applying_period_date=change_date_format($data_ex[1],'yyyy-mm-dd');
		//$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$applying_period_date=change_date_format($data_ex[1],'','',1);
		//$to_date=change_date_format($to_date,'','',1);
	}
	$working_hour=0;
	$qry_working_hour=sql_select( "select working_hour from  lib_standard_cm_entry where company_id='$data_ex[0]' and '$applying_period_date' between applying_period_date and applying_period_to_date and is_deleted=0 and  status_active=1");
//	echo "select working_hour from  lib_standard_cm_entry where company_id='$data_ex[0]' and '$applying_period_date' between applying_period_date and applying_period_to_date and is_deleted=0 and  status_active=1";
	foreach ($qry_working_hour as $row)
	{
		$working_hour=$row[csf("working_hour")];
	}
	echo trim($working_hour);
	exit();
}
?>
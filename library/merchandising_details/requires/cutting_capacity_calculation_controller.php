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
	$manpower = $data[4];
	$wo_hour = $data[5];
	$cm = $data[6];
	$capacity_min = $manpower*$wo_hour*60;
	$capacity_pcs = $capacity_min/$cm;
	// echo $capacity_min."/".$cm."<br>";

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
				<input type="text" name="txt_sew_capacity_min_<? echo $i; ?>" id="txt_sew_capacity_min_<? echo $i; ?>" class="text_boxes" class="text_boxes_numeric" style="width:80px" readonly="readonly" value="<?=$capacity_min;?>"/>
			</td>
			<td>
				<input type="text" name="txt_sew_capacity_pcs_<? echo $i; ?>" id="txt_sew_capacity_pcs_<? echo $i; ?>" class="text_boxes" class="text_boxes_numeric" style="width:80px" readonly="readonly" value="<?=$capacity_pcs;?>"/>
			</td>
			<td>
				<input type="text" name="txt_percentage_<? echo $i; ?>" id="txt_percentage_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onblur="calculate_capacity_min_pcs(this.value,<? echo $i; ?>);" />
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
	$manpower = $data[5];
	$wo_hour = $data[6];
	$cm = $data[7];
	$capacity_min = $manpower*$wo_hour*60;


	//$save_id=$data[4];
	
	$sql_check=sql_select("SELECT a.id, b.month_id, c.id as year_id from lib_cutt_capacity_calc_mst a, lib_cutt_capacity_calc_dtls b, lib_cutt_capacity_year_dtls c where a.id=c.mst_id and a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.status_active=1 and a.is_deleted=0 and b.month_id=$data[3] group by  a.id, b.month_id, c.id order by b.month_id");

	if(count($sql_check)>0)
	{

		$sew_sql_result=sql_select("SELECT a.id, b.id as bid, b.month_id, b.date_calc,b.day_status, b.capacity_min, b.capacity_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.date_calc");

		$sew_capacity_val_arr=array();
		foreach($sew_sql_result as $val)
		{
			$sew_capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['sew_capacity_min']+= $val[csf('capacity_min')];
			$sew_capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['sew_capacity_pcs']+= $val[csf('capacity_pcs')];
		}

		// echo "<pre>";
		// print_r($sew_capacity_val_arr); die;



		$sql_result=sql_select("SELECT a.id, a.year, b.id as bid, b.month_id, b.date_calc,a.sewing_id, b.percentage,b.day_status, b.capacity_min, b.capacity_pcs,b.sew_capacity_min, b.sew_capacity_pcs from lib_cutt_capacity_calc_mst a, lib_cutt_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.date_calc");

		$capacity_val_arr=array();
		foreach($sql_result as $val){
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['id']= $val[csf('id')];
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['bid']= $val[csf('bid')];
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['percentage']= $val[csf('percentage')];
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['year']= $val[csf('year')];
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['month_id']= $val[csf('month_id')];
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['day_status']= $val[csf('day_status')];
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['capacity_min']+= $val[csf('capacity_min')];
			$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['capacity_pcs']+= $val[csf('capacity_pcs')];
			//$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['sew_capacity_min']+= $val[csf('sew_capacity_min')];
			//$capacity_val_arr[$val[csf('month_id')]][$val[csf('date_calc')]]['sew_capacity_pcs']+= $val[csf('sew_capacity_pcs')];
			
		}

		// echo "<pre>";
		// print_r($capacity_val_arr); die;


		$i=0;
		$data_count=count($sql_result);
		
		foreach ($capacity_val_arr as $month_id => $month_data_arr)
		{
		
			foreach ($month_data_arr as $date_calc => $inf)
			{
				$capacity_min=$inf['capacity_min'];
				$capacity_min_sum += $capacity_min;
				
				$capacity_pcs=$inf['capacity_pcs'];
				$capacity_pcs_sum += $capacity_pcs;

				$sew_capacity_min=$sew_capacity_val_arr[$month_id][$date_calc]['sew_capacity_min'];
				$sew_capacity_min_sum += $sew_capacity_min;
				
				$sew_capacity_pcs=$sew_capacity_val_arr[$month_id][$date_calc]['sew_capacity_pcs'];
				$sew_capacity_pcs_sum += $sew_capacity_pcs;


				$i++;
				if ($i==$data_count)
				{
					echo "document.getElementById('total_min').value 							= '".$capacity_min_sum."';\n";
					echo "document.getElementById('total_pcs').value 							= '".$capacity_pcs_sum."';\n";

					echo "document.getElementById('sew_total_min').value 							= '".$sew_capacity_min_sum."';\n";
					echo "document.getElementById('sew_total_pcs').value 							= '".$sew_capacity_pcs_sum."';\n";
					
					echo "document.getElementById('update_id').value= '".$inf["id"]."';\n";
					echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
				}
				echo "document.getElementById('cbo_day_status_".$i."').value 	= '".$inf["day_status"]."';\n";
				echo "document.getElementById('txt_percentage_".$i."').value 	= '".$inf["percentage"]."';\n";
				echo "document.getElementById('sewing_id').value					= '".$sewing_id."';\n";
				echo "document.getElementById('txt_capacity_min_".$i."').value 	= '".$inf["capacity_min"]."';\n";
				echo "document.getElementById('txt_sew_capacity_min_".$i."').value 	= '".$sew_capacity_min."';\n";
				echo "document.getElementById('txt_capacity_pcs_".$i."').value 	= '".$inf["capacity_pcs"]."';\n";
				echo "document.getElementById('txt_sew_capacity_pcs_".$i."').value 	= '".$sew_capacity_pcs."';\n";
				echo "document.getElementById('update_id_dtls_".$i."').value 	= '".$inf["bid"]."';\n";
				
				if ($inf["day_status"]==2)
				{
					echo "document.getElementById('txt_capacity_min_".$i."').value 			= '';\n";
					echo "document.getElementById('txt_sew_capacity_min_".$i."').value 			= '';\n";
					echo "document.getElementById('txt_capacity_pcs_".$i."').value 			= '';\n";
					echo "document.getElementById('txt_sew_capacity_pcs_".$i."').value 			= '';\n";
					echo "disable_enable_fields( 'txt_capacity_min_".$i."*txt_capacity_pcs_".$i."', 1, '', '');\n";
					echo "disable_enable_fields( 'txt_sew_capacity_min_".$i."*txt_sew_capacity_pcs_".$i."', 1, '', '');\n";
				}

				if($data_count==0)
				{
					
					echo "document.getElementById('update_id').value= '';\n";
					echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
				}

			}
		}
		
	}
	else
	{

	
		$sql_result=sql_select("SELECT a.id, b.id as bid, b.month_id, b.date_calc, b.day_status, b.capacity_min, b.capacity_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.date_calc");


		

		$i=0;
		$data_count=count($sql_result);
		foreach ($sql_result as $inf)
		{
			// $capacity_min=$inf[csf('capacity_min')];
			$capacity_min_sum += $capacity_min;
			
			$capacity_pcs=$inf[csf('capacity_pcs')];
			$capacity_pcs_sum += $capacity_pcs;

			

			$i++;
			if ($i==$data_count)
			{
				echo "document.getElementById('total_min').value 							= '".$capacity_min_sum."';\n";
				echo "document.getElementById('total_pcs').value 							= '".$capacity_pcs_sum."';\n";

				echo "document.getElementById('sew_total_min').value 							= '".$capacity_min_sum."';\n";
				echo "document.getElementById('sew_total_pcs').value 							= '".$capacity_pcs_sum."';\n";

				//echo "document.getElementById('update_id').value= '".$inf[csf("id")]."';\n";
				echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
			}
			echo "document.getElementById('cbo_day_status_".$i."').value 	= '".$inf[csf("day_status")]."';\n";
			
			echo "document.getElementById('sewing_id').value					= '".$inf[csf("id")]."';\n";
			echo "document.getElementById('txt_capacity_min_".$i."').value 	= '".$capacity_min."';\n";
			echo "document.getElementById('txt_sew_capacity_min_".$i."').value 	= '".$capacity_min."';\n";
			echo "document.getElementById('txt_capacity_pcs_".$i."').value 	= '".$inf[csf("capacity_pcs")]."';\n";
			echo "document.getElementById('txt_sew_capacity_pcs_".$i."').value 	= '".$inf[csf("capacity_pcs")]."';\n";
			
			
			if ($inf[csf("day_status")]==2)
			{
				echo "document.getElementById('txt_capacity_min_".$i."').value 			= '';\n";
				echo "document.getElementById('txt_sew_capacity_min_".$i."').value 			= '';\n";
				echo "document.getElementById('txt_capacity_pcs_".$i."').value 			= '';\n";
				echo "document.getElementById('txt_sew_capacity_pcs_".$i."').value 			= '';\n";
				echo "disable_enable_fields( 'txt_capacity_min_".$i."*txt_capacity_pcs_".$i."', 1, '', '');\n";
				echo "disable_enable_fields( 'txt_sew_capacity_min_".$i."*txt_sew_capacity_pcs_".$i."', 1, '', '');\n";
			}
		}
		if($data_count==0)
		{
			
			echo "document.getElementById('update_id').value= '';\n";
			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
		}

	}

	exit;
}



if ($action=="load_php_dtls_form_update")
{
	$data=explode('_',$data);
	$mon_id=$data[3];
	
	$mon_id_cond="";
	if($mon_id) $mon_id_cond="and b.month_id=$mon_id";
	//if($txt_table_no) $table_no_cond="and a.table_no=$txt_table_no";
	
	

	$sql_res=sql_select("select a.id, a.comapny_id, a.year, a.location_id, c.id as year_id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs from lib_cutt_capacity_calc_mst a, lib_cutt_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] AND c.location_id=$data[1] and a.year=$data[2] and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $mon_id_cond group by a.id, a.comapny_id, a.year, a.location_id, c.id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs order by c.month_id");

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
			

			echo "document.getElementById('txt_working_day_total').value= '".$working_day_sum."';\n";
			echo "document.getElementById('txt_capacity_min_total').value= '".$capacity_month_min_sum."';\n";
			echo "document.getElementById('txt_capacity_pcs_total').value= '".$capacity_month_pcs_sum."';\n";
			echo "document.getElementById('update_id').value= '".$row[csf("id")]."';\n";
			if ($row[csf("id")]!="")
			{
				echo "disable_enable_fields( 'cbo_company_id*cbo_location_id*cbo_year', 1, '', '');\n";
			}
		}
		echo "document.getElementById('txt_sl_no_".$i."').value = '".$i."';\n";
		echo "document.getElementById('txt_month_".$i."').value = '".$months[$row[csf("month_id")]]."';\n";
		echo "document.getElementById('txt_month_id_".$i."').value = '".$row[csf("month_id")]."';\n";
		echo "document.getElementById('txt_working_day_".$i."').value = '".$row[csf("working_day")]."';\n";
		echo "document.getElementById('txt_year_capacity_min_".$i."').value = '".$row[csf("capacity_month_min")]."';\n";
		echo "document.getElementById('txt_year_capacity_pcs_".$i."').value = '".$row[csf("capacity_month_pcs")]."';\n";
		echo "document.getElementById('update_id_year_dtls_".$i."').value = '".$row[csf("year_id")]."';\n";

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
		//$capacity_source_id=str_replace("'","",$cbo_capacity_source);
		$month_id=str_replace("'","",$cbo_month);
		$sewing_id=str_replace("'","",$sewing_id);
		$table_no_cond="";
		
		if($txt_table_no) $table_no_cond="and a.table_no=$txt_table_no";
		
		
		$sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.percentage,b.capacity_min,b.capacity_pcs from lib_cutt_capacity_calc_mst a,lib_cutt_capacity_calc_dtls b where  a.id=b.mst_id and a.comapny_id=$company_id and a.location_id=$location_id and a.year=$year_id and b.month_id=$month_id  and a.status_active=1 and a.is_deleted=0  $table_no_cond and  b.status_active=1 and b.is_deleted=0 order by b.date_calc");
		$tot_row=count($sql_result);
		if($tot_row>0)
		{
			$msg="Duplicate Entry found in Same Following Item Heads:Company,Location,Year,Month";
			echo "11**".$msg;
			disconnect($con);
			die;
		}
		$update_id=str_replace("'",'',$update_id);
		if($update_id=="")
		{
			$mst_id=return_next_id( "id", " lib_cutt_capacity_calc_mst", 1 ) ; 
			$field_array_mst="id,comapny_id,year,location_id,table_no,sewing_id,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array_mst="(".$mst_id.",".$cbo_company_id.",".$cbo_year.",".$cbo_location_id.",".$txt_table_no.",".$sewing_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		}
		else
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_mst="year*location_id*table_no*updated_by*update_date*status_active*is_deleted";
			
			$data_array_mst="".$cbo_year."*".$cbo_location_id."*".$txt_table_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update("lib_cutt_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		$data_array_dtls="";
		$dtls_id=return_next_id( "id", "lib_cutt_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,location_id,date_calc,day_status,percentage,sew_capacity_min,sew_capacity_pcs,capacity_min,capacity_pcs";
		for($i=1; $i<=$tot_row_date; $i++)
		{
			//txt_sew_capacity_min_'+i+'*txt_sew_capacity_pcs_'+i

			$txt_date= "txt_date_".$i;
			$cbo_day_status="cbo_day_status_".$i;
			$txt_percentage="txt_percentage_".$i;
			$txt_sew_capacity_min="txt_sew_capacity_min_".$i;
			$txt_sew_capacity_pcs="txt_sew_capacity_pcs_".$i;
			$txt_capacity_min="txt_capacity_min_".$i;
			$txt_capacity_pcs="txt_capacity_pcs_".$i;
			$update_id_dtls="update_id_dtls_".$i;

			if(str_replace("'",'',$$update_id_dtls)=="")
			{
				if ($i!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$mst_id.",".$cbo_month.",".$cbo_location_id.",".$$txt_date.",".$$cbo_day_status.",".$$txt_percentage.",".$$txt_sew_capacity_min.",".$$txt_sew_capacity_pcs.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
				$dtls_id=$dtls_id+1;
			}
		}
			
		$dtls_year_id=return_next_id( "id", "lib_cutt_capacity_year_dtls", 1 ); 
		$field_array_year="id,mst_id,month_id,location_id,working_day,capacity_month_min,capacity_month_pcs";
		
			for($i=1; $i<=$tot_row_year; $i++)
			{
				$txt_month= "txt_month_id_".$i;
				$txt_working_day="txt_working_day_".$i;
				$txt_year_capacity_min="txt_year_capacity_min_".$i;
				$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
				$update_id_year_dtls="update_id_year_dtls_".$i;
				

				if(str_replace("'",'',$$update_id_year_dtls)=="")
				{
					if ($i!=1) $data_array_year .=",";
					$data_array_year.="(".$dtls_year_id.",".$mst_id.",".$$txt_month.",".$cbo_location_id.",".$$txt_working_day.",".$$txt_year_capacity_min.",".$$txt_year_capacity_pcs.")";
					$dtls_year_id=$dtls_year_id+1;
				}
				else
				{
					$id_arr_year=array();
					$data_array_year=array();
					$field_array_year_update="working_day*capacity_month_min*capacity_month_pcs";
					for($i=1; $i<=12; $i++)
					{
						$txt_month= "txt_month_id_".$i;
						$txt_working_day="txt_working_day_".$i;
						$txt_year_capacity_min="txt_year_capacity_min_".$i;
						$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
						$update_id_year_dtls="update_id_year_dtls_".$i;
						

						if(str_replace("'",'',$$update_id_year_dtls)!="" && str_replace("'",'',$$txt_month)==str_replace("'",'',$cbo_month))
						{
							$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
							$data_array_year_update[str_replace("'",'',$$update_id_year_dtls)] =explode("*",("".$$txt_working_day."*".$$txt_year_capacity_min."*".$$txt_year_capacity_pcs.""));
						}
					}
				}
			}

			
		if(str_replace("'",'',$update_id)=="")
		{
			$rID=sql_insert("lib_cutt_capacity_calc_mst",$field_array_mst,$data_array_mst,0);	
		}
		else
		{
			$rID=sql_update("lib_cutt_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		//echo "10**INSERT INTO lib_cutt_capacity_calc_dtls $field_array_dtls values $data_array_dtls"; die;
		$rID1=sql_insert("lib_cutt_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		if($data_array_year !="")
		{
			$rID2=sql_insert("lib_cutt_capacity_year_dtls",$field_array_year,$data_array_year,1);
		}
		if(count($data_array_year_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "lib_cutt_capacity_year_dtls", "id", $field_array_year_update, $data_array_year_update, $id_arr_year ),1);

		}
	 	
		//echo "10**". $rID .'&&'. $rID1 .'&&'. $rID2;oci_rollback($con); die;
		
		
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
		$company_id=str_replace("'","",$cbo_company_id);
		$year_id=str_replace("'","",$cbo_year);
		$updateid=str_replace("'",'',$update_id);
		$month_id=str_replace("'","",$cbo_month);
		$sewing_id=str_replace("'","",$sewing_id);
		$table_no_cond="";
		
		if($txt_table_no) $table_no_cond="and a.table_no=$txt_table_no";


		$sql_process_ac=sql_select("select a.id, a.company_id, a.process_year, a.process_month, a.proces_type from lib_process_ac_head_standard_mst a,lib_process_ac_head_standard_dtls b where  a.id=b.mst_id and a.company_id=$cbo_company_id and a.proces_type=1 and a.process_year=$cbo_year and a.process_month=$cbo_month  and a.status_active=1 and a.is_deleted=0 order by a.id");
		$sql_process_result=count($sql_process_ac);

		//echo $sql_process_result; die;
		if($sql_process_result>0)
		{
			$msg="Data Update And Deleted Restricted. This Data Already Save Other Page";
			echo "500**".$msg;
			disconnect($con);
			die;
		}
		
		
		$sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.percentage,b.capacity_min,b.capacity_pcs from lib_cutt_capacity_calc_mst a,lib_cutt_capacity_calc_dtls b where  a.id=b.mst_id and a.comapny_id=$company_id and a.location_id=$location_id and a.year=$year_id and b.month_id=$month_id and a.id!=$updateid  and a.status_active=1 and a.is_deleted=0  $table_no_cond and  b.status_active=1 and b.is_deleted=0 order by b.date_calc");
		$tot_row=count($sql_result);
		
		if($tot_row>0)
		{
			$msg="Duplicate Entry found in Same Following Item Heads:Company,Location,Year,Month";
			echo "11**".$msg;
			disconnect($con);
			die;
		}

		$field_array_mst="year*location_id*table_no*updated_by*update_date*status_active*is_deleted";
			
		$data_array_mst="".$cbo_year."*".$cbo_location_id."*".$txt_table_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

		$id_arr=array();
		$data_array_dtls_up=array();
		$field_array_dtls_up="day_status*percentage*capacity_min*capacity_pcs";
		
		$dtls_id=return_next_id( "id", "lib_cutt_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,location_id,date_calc,day_status,percentage,capacity_min,capacity_pcs";
		
		for($i=1; $i<=$tot_row_date; $i++)
		{
			$txt_date= "txt_date_".$i;
			$cbo_day_status="cbo_day_status_".$i;
			$txt_percentage="txt_percentage_".$i;
			$txt_capacity_min="txt_capacity_min_".$i;
			$txt_capacity_pcs="txt_capacity_pcs_".$i;
			$update_id_dtls="update_id_dtls_".$i;			
			
			if(str_replace("'",'',$$update_id_dtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_dtls_up[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$cbo_day_status.",".$$txt_percentage.",".$$txt_capacity_min.",".$$txt_capacity_pcs.""));
			}
			else 
			{
				if ($i!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$cbo_month.",".$cbo_location_id.",".$$txt_date.",".$$cbo_day_status.",".$$txt_percentage.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
				$dtls_id=$dtls_id+1;
			}
		}
		$id_arr_year=array();
		$data_array_year_update=array();
		$field_array_year_update="working_day*capacity_month_min*capacity_month_pcs";
		$dtls_year_id=return_next_id( "id", "lib_cutt_capacity_year_dtls", 1 ); 
		$field_array_year="id,mst_id,month_id,location_id,working_day,capacity_month_min,capacity_month_pcs";
		for($i=1; $i<=12; $i++)
		{
			$txt_month= "txt_month_id_".$i;
			$txt_working_day="txt_working_day_".$i;
			$txt_year_capacity_min="txt_year_capacity_min_".$i;
			$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
			$update_id_year_dtls="update_id_year_dtls_".$i;
						
		
			if(str_replace("'",'',$$update_id_year_dtls)=="")
			{
				if ($i!=1) $data_array_year .=",";
				$data_array_year.="(".$dtls_year_id.",".$mst_id.",".$$txt_month.",".$cbo_location_id.",".$$txt_working_day.",".$$txt_year_capacity_min.",".$$txt_year_capacity_pcs.")";
				$dtls_year_id=$dtls_year_id+1;
			}
			if(str_replace("'",'',$$update_id_year_dtls)!=""  && str_replace("'",'',$$txt_month)==str_replace("'",'',$cbo_month))
			{
				$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
				$data_array_year_update[str_replace("'",'',$$update_id_year_dtls)] =explode("*",("".$$txt_working_day."*".$$txt_year_capacity_min."*".$$txt_year_capacity_pcs.""));
			}
		}
		$rID=sql_update("lib_cutt_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		$rID1=execute_query(bulk_update_sql_statement( "lib_cutt_capacity_calc_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $id_arr ),0);
		
		if(str_replace("'",'',$data_array_dtls)!="")
		{
			$rID1=sql_insert("lib_cutt_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		if($data_array_year !="")
		{
			$rID2=sql_insert("lib_cutt_capacity_year_dtls",$field_array_year,$data_array_year,1);
		}
		if(count($data_array_year_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "lib_cutt_capacity_year_dtls", "id", $field_array_year_update, $data_array_year_update, $id_arr_year ),1);

		}


		//echo "10**". $rID .'&&'. $rID1 .'&&'. $rID2;oci_rollback($con); die;
		
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
	$qry_result=sql_select( "select id,mst_id from lib_cutt_capacity_calc_dtls where mst_id='$data' and status_active=1");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		
	}
	echo $id;
}

if($action=="load_php_dtls_form_return_id_year")
{
	$qry_res=sql_select( "select id,mst_id from lib_cutt_capacity_year_dtls where mst_id='$data' and status_active=1");
	foreach ($qry_res as $inf)
	{
		if($id_year=="") $id_year=$inf[csf("id")]; else $id_year.="*".$inf[csf("id")];
		
	}
	echo $id_year;
}


?>
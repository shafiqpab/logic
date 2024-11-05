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
	list($year,$month,$company,$location,$fin_type)=explode('_',$data);
	$day_status=array(1=>"Open",2=>"Closed");
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $month, $year);

	$sql_res=sql_select("select b.id, b.mst_id,b.day_id, b.month_id, b.date_calc, b.day_status,b.manpower, b.capacity_mint, b.capacity_pcs 
	from 
		lib_fin_gmts_capacity_cal_mst a,
		lib_fin_gmts_capacity_cal_dtls b
	where 
		a.company_id=$company 
		and a.year=$year 
		and a.fin_type=$fin_type 
		and a.status_active=1 
		and a.is_deleted=0 
		and a.id=b.mst_id 
		and b.month_id=$month
	order by b.day_id");
	foreach($sql_res as $rows){
		$mdr[$rows[csf('day_id')]]['id']=$rows[csf('id')];	
		$mdr[$rows[csf('day_id')]]['day_status']=$rows[csf('day_status')];	
		$mdr[$rows[csf('day_id')]]['manpower']=$rows[csf('manpower')];	
		$mdr[$rows[csf('day_id')]]['capacity_mint']=$rows[csf('capacity_mint')];	
		$mdr[$rows[csf('day_id')]]['capacity_pcs']=$rows[csf('capacity_pcs')];	
	}
	
	
	
	
	for( $i = 1; $i <= $daysinmonth; $i++ ) 
	{  
		$day_txt=$i<10?'0'.$i:$i;
		$month_txt=$month<10?'0'.$month:$month;
		$date=$day_txt.'-'.$month_txt.'-'.$year;
		$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		$day_name = date('D', strtotime($date));
		
		if($day_name=='Fri' && $mdr[$i]['day_status']==""){$style='style="color: red;"';$status_select=2;} 
		else if($mdr[$i]['day_status']==2){$style='style="color: red;"';$status_select=2;}
		else{$style='';$status_select=1;}
	
	
	?>
		
        <tr bgcolor="<? echo $bgcolor; ?>" id="days_tr_<?php echo $i;?>" <?php echo $style; ?>>
            <td width="60" id="tdDate_<? echo $i; ?>" align="center"><? echo $date; ?></td>
            <td width="40" id="tdDay_<? echo $i; ?>"><?php echo $day_name; ?></td>
            <td width="80" align="center">
               <input type="hidden" id="updateIdDay_<? echo $i; ?>" value="<? echo $mdr[$i]['id'];?>" />
                <?
					echo create_drop_down( "dayStatus_".$i, 72,$day_status,"", 0, "-- Select --", $status_select,"fn_calculate_capacity()" );
                ?>
            </td>
            <td width="80" align="center"><input type="text" id="manpower_<? echo $i; ?>" name="manpower_<? echo $i; ?>" style="width:55px;" class="text_boxes_numeric" onkeyup="copy_manpower(<? echo $i; ?>);" value="<? echo $mdr[$i]['manpower'];?>"></td>
            <td width="100" id="dayCapacityMint_<? echo $i; ?>" align="right"><? echo $mdr[$i]['capacity_mint'];?></td>
            <td id="dayCapacityPcs_<? echo $i; ?>" align="right"><? echo $mdr[$i]['capacity_pcs'];?></td>
        </tr>
        
	 <? 
	 } 

	exit();	
}


	
if ($action=="load_php_dtls_form_update")
{
	list($year,$month,$company,$location,$fin_type)=explode('_',$data);
	
	
	$sql_res=sql_select("select a.id,a.company_id,a.fin_type,a.year,a.location_id,a.wo_hrs,a.effi_percent,a.basic_smv,b.id as year_dtls_id,b.month_id,b.working_day,b.capacity_month_mint,b.capacity_month_pcs 
	from 
		lib_fin_gmts_capacity_cal_mst a,
		lib_fin_gmts_capa_year_dtls b
	where 
		a.company_id=$company 
		and a.year=$year 
		and a.fin_type=$fin_type
		and a.location_id=$location 
		and a.status_active=1 
		and a.is_deleted=0 
		and a.id=b.mst_id 
	order by b.month_id");

	$update_id="";$manpower="";$wo_hrs="";$effi_precent="";$basic_smv="";$location_id="";
	foreach ($sql_res as $row)
	{
		$working_day=$row[csf('working_day')];
		$working_day_sum += $working_day;

		$capacity_month_mint=$row[csf('capacity_month_mint')];
		$capacity_month_mint_sum += $capacity_month_mint;

		$capacity_month_pcs=$row[csf('capacity_month_pcs')];
		$capacity_month_pcs_sum += $capacity_month_pcs;
		
		$month_id=$row[csf("month_id")];
		if($update_id==""){
			$update_id=$row[csf("id")];
			$manpower=$row[csf("manpower")];
			$wo_hrs=$row[csf("wo_hrs")];
			$effi_percent=$row[csf("effi_percent")];
			$basic_smv=$row[csf("basic_smv")];
			$location_id=$row[csf("location_id")];
		}
		
		echo "document.getElementById('monthId_".$month_id."').innerHTML	= '".$month_id."';\n";
		echo "document.getElementById('monthName_".$month_id."').innerHTML	= '".$months[$month_id]."';\n";
		echo "document.getElementById('workingDays_".$month_id."').innerHTML	= '".$row[csf("working_day")]."';\n";
		echo "document.getElementById('monthCapMint_".$month_id."').innerHTML	= '".$row[csf("capacity_month_mint")]."';\n";
		echo "document.getElementById('monthCapPcs_".$month_id."').innerHTML 	= '".$row[csf("capacity_month_pcs")]."';\n";
		
		echo "document.getElementById('update_id_year_dtls_".$month_id."').value 	= '".$row[csf("year_dtls_id")]."';\n";
	}
	
	if ($update_id!=""){
		echo "document.getElementById('totMonthWorkignDays').innerHTML	= '".$working_day_sum."';\n";
		echo "document.getElementById('totMonthCapMint').innerHTML	= '".$capacity_month_mint_sum."';\n";
		echo "document.getElementById('totMonthCapPcs').innerHTML	= '".$capacity_month_pcs_sum."';\n";
		echo "document.getElementById('update_id').value	= '".$update_id."';\n";
		
		echo "document.getElementById('cbo_location_id').value	= '".$location_id."';\n";
		echo "document.getElementById('txt_wo_hrs').value	= '".$wo_hrs."';\n";
		echo "document.getElementById('txt_efficiency_per').value	= '".$effi_percent."';\n";
		echo "document.getElementById('txt_smv').value	= '".$basic_smv."';\n";
		
		echo "disable_enable_fields( 'cbo_company_id*cbo_fin_type*cbo_year*txt_wo_hrs*txt_efficiency_per*txt_smv', 1, '', '');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_finish_gmts_capacity_calculation',1);\n";
	}
	
	if ($update_id==""){
		for($i=1;$i<=12;$i++){
			echo "document.getElementById('workingDays_".$i."').innerHTML		= '0';\n";
			echo "document.getElementById('monthCapMint_".$i."').innerHTML		= '0';\n";
			echo "document.getElementById('monthCapPcs_".$i."').innerHTML 		= '0';\n";
			echo "document.getElementById('update_id_year_dtls_".$i."').value	= '';\n";
		}
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
		if(str_replace("'",'',$update_id)=="")
		{
			
			//mst part-------------------------------
			$mst_id=return_next_id( "id", " lib_fin_gmts_capacity_cal_mst", 1 ) ; 
			$field_array_mst="id, company_id, location_id, fin_type, year, wo_hrs, effi_percent, basic_smv, inserted_by, insert_date, status_active, is_deleted";
			
			$data_array_mst="(".$mst_id.",".$cbo_company_id.",".$cbo_location_id.",".$cbo_fin_type.",".$cbo_year.",".$txt_wo_hrs.",".$txt_efficiency_per.",".$txt_smv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			//Dtls part--------------------------------
			
			$dtls_id=return_next_id( "id", "lib_fin_gmts_capacity_cal_dtls", 1 ); 
			$field_array_dtls="id, mst_id,day_id, month_id, date_calc, day_status,manpower, capacity_mint, capacity_pcs";
				
				
				$month_data_arr=explode('__',$day_data);
				$data_array_dtls='';$set_comma='';
				foreach($month_data_arr as $month_data_val)
				{
					list($txt_date,$txt_day_id,$cbo_day_status,$manpower,$txt_capacity_min,$txt_capacity_pcs)=explode('**',$month_data_val);
	
					
					if($db_type==0)
					{
						$txt_date=change_date_format(trim($txt_date),"yyyy-mm-dd");
					}
					else
					{
						$txt_date=change_date_format($txt_date,'','',1);	
					}
					
					if ($data_array_dtls!=''){$set_comma =",";}
					$data_array_dtls.="$set_comma (".$dtls_id.",".$mst_id.",".$txt_day_id.",".$cbo_month.",'".$txt_date."',".$cbo_day_status.",".$manpower.",".$txt_capacity_min.",".$txt_capacity_pcs.")";
					$dtls_id=$dtls_id+1;
				}
		
			//Year Dtls part--------------------------------
			$dtls_year_id=return_next_id( "id", "lib_fin_gmts_capa_year_dtls", 1 ); 
			$field_array_year="id, mst_id, month_id, working_day, capacity_month_mint, capacity_month_pcs";
		
				
				$data_array_year='';$set_comma='';
				list($cbo_month,$workingDays,$monthCapMint,$monthCapPcs)=explode('**',$month_data);
				for($m=1;$m<=12;$m++)
				{
					if($cbo_month==$m){
						$txt_workingDays=$workingDays;
						$txt_monthCapMint=$monthCapMint;
						$txt_monthCapPcs=$monthCapPcs;	
					}
					else{
						$txt_workingDays=0;
						$txt_monthCapMint=0;
						$txt_monthCapPcs=0;	
					}
					if ($data_array_year!=''){$set_comma =",";}
						$data_array_year.="$set_comma (".$dtls_year_id.",".$mst_id.",".$m.",".$txt_workingDays.",".$txt_monthCapMint.",".$txt_monthCapPcs.")";
					$dtls_year_id=$dtls_year_id+1;
		
				}
		
			//echo "10** insert into lib_fin_gmts_capa_year_dtls ($field_array_year) values $data_array_year";
			
			
			$rID=sql_insert("lib_fin_gmts_capacity_cal_mst",$field_array_mst,$data_array_mst,0);		
			$rID1=sql_insert("lib_fin_gmts_capacity_cal_dtls",$field_array_dtls,$data_array_dtls,0);		
			$rID2=sql_insert("lib_fin_gmts_capa_year_dtls",$field_array_year,$data_array_year,0);		
	 	
		
		//echo "10**".$rID.'='.$rID1.'='.$rID2;die;
		
		
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
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_year="working_day*capacity_month_mint*capacity_month_pcs";
		list($cbo_month,$workingDays,$monthCapMint,$monthCapPcs,$update_id_year_dtls)=explode('**',$month_data);
		if(str_replace("'",'',$update_id_year_dtls)!="")
		{
			$data_array_year="".$workingDays."*".$monthCapMint."*".$monthCapPcs."";
		}
		
		
		
		$dtls_id=return_next_id( "id", "lib_fin_gmts_capacity_cal_dtls", 1 ); 
		$field_array_dtls="id, mst_id,day_id,month_id, date_calc, day_status,manpower, capacity_mint, capacity_pcs";
		$field_array_dtls_update="month_id*date_calc*day_status*manpower*capacity_mint*capacity_pcs";
			$month_data_arr=explode('__',$day_data);
			$data_array_dtls='';
			foreach($month_data_arr as $month_data_val)
			{
				list($txt_date,$txt_day_id,$cbo_day_status,$manpower,$txt_capacity_min,$txt_capacity_pcs,$updateIdDay)=explode('**',$month_data_val);

				if($db_type==0)
				{
					$txt_date=change_date_format(trim($txt_date),"yyyy-mm-dd");
				}
				else
				{
					$txt_date=change_date_format($txt_date,'','',1);	
				}
				
				
				if($updateIdDay==""){
					if ($data_array_dtls!=''){$set_comma =",";}
					$data_array_dtls.="$set_comma (".$dtls_id.",".$update_id.",".$txt_day_id.",".$cbo_month.",'".$txt_date."',".$cbo_day_status.",".$manpower.",".$txt_capacity_min.",".$txt_capacity_pcs.")";
					$dtls_id=$dtls_id+1;
				}
				else
				{
					$id_arr_dtls[]=str_replace("'",'',$updateIdDay);
					$data_array_dtls_update[str_replace("'",'',$updateIdDay)] =explode(",",("".$cbo_month.",'".$txt_date."',".$cbo_day_status.",".$manpower.",".$txt_capacity_min.",".$txt_capacity_pcs.""));
				}
				
				
			}
	
		
		$rID=sql_update("lib_fin_gmts_capa_year_dtls",$field_array_year,$data_array_year,"id",$update_id_year_dtls,0);
		
		if($data_array_dtls!="")
		{
			$rID1=sql_insert("lib_fin_gmts_capacity_cal_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		else{
		$rID1=execute_query(bulk_update_sql_statement( "lib_fin_gmts_capacity_cal_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr_dtls ),1);
		}
		
		 //echo "10** insert into lib_fin_gmts_capacity_cal_dtls ($field_array_dtls) values $data_array_dtls";die;
		 //echo "10**".$data_array_dtls;die;
		 //echo "10**".$rID.'='.$rID1;die;
		
				
		if($db_type==0)
		{
			if( $rID && $rID1)
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
			   if( $rID && $rID1)
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






//=====================================================================================================



if ($action=="load_php_dtls_form_update_val")
{
	$data=explode('_',$data);
	
	
	
	//$sql_result=sql_select("select a.id,b.id as uid,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2]  and b.month_id=$data[3] and a.capacity_source=$data[4] and  a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id");

	$sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2]  and b.month_id=$data[3] and a.capacity_source=$data[4] and  a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id");
	$i=0;
	$data_count=count($sql_result);
	foreach ($sql_result as $inf)
	{
		$capacity_min=$inf[csf('capacity_min')];
		$capacity_min_sum += $capacity_min;
		
		$capacity_pcs=$inf[csf('capacity_pcs')];
		$capacity_pcs_sum += $capacity_pcs;

		$i++;
	if ($i==$data_count)
		{
			echo "document.getElementById('total_min').value 							= '".$capacity_min_sum."';\n";
			echo "document.getElementById('total_pcs').value 							= '".$capacity_pcs_sum."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
		}
		echo "document.getElementById('cbo_day_status_".$i."').value 			= '".$inf[csf("day_status")]."';\n";
		echo "document.getElementById('txt_no_of_line_".$i."').value 			= '".$inf[csf("no_of_line")]."';\n";
		echo "document.getElementById('line_id').value 			= '".$inf[csf("no_of_line")]."';\n";
		echo "document.getElementById('txt_capacity_min_".$i."').value 			= '".$inf[csf("capacity_min")]."';\n";
		echo "document.getElementById('txt_capacity_pcs_".$i."').value 			= '".$inf[csf("capacity_pcs")]."';\n";
		echo "document.getElementById('update_id_dtls_".$i."').value 			= '".$inf[csf("bid")]."';\n";
		
		if ($inf[csf("day_status")]==2)
		{
			echo "document.getElementById('txt_capacity_min_".$i."').value 			= '';\n";
			echo "document.getElementById('txt_capacity_pcs_".$i."').value 			= '';\n";
			echo "disable_enable_fields( 'txt_capacity_min_".$i."*txt_capacity_pcs_".$i."', 1, '', '');\n";
		}
	}
	exit;
}




if($action=="load_php_dtls_form_return_id_date")
{
	$qry_result=sql_select( "select id,mst_id from lib_capacity_calc_dtls where mst_id='$data'");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		
	}
	echo $id;
}

if($action=="load_php_dtls_form_return_id_year")
{
	$qry_res=sql_select( "select id,mst_id from lib_capacity_year_dtls where mst_id='$data'");
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
	foreach ($qry_working_hour as $row)
	{
		$working_hour=$row[csf("working_hour")];
	}
	echo trim($working_hour);
	exit();
}
?>
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
	echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "check_unique_id();","","","","","",3 );
	exit();		 
}


if ($action=="load_month_in_days")
{
	list($year,$month,$comapny,$location,$section,$sub_section)=explode('_',$data);
	
	$sql="select c.mst_id, c.dtls_id,  c.month_id, c.capacity_date,  c.day_status , c.daily_capacity_tk,c.daily_capacity_usd from trims_capacity_calculation_mst a, trims_capacity_cal_dtls b,trims_capacity_cal_day_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.mst_id=c.mst_id and b.month_id=c.month_id and b.month_id=$month and c.month_id=$month and a.company_id=$comapny and a.location_id=$location and a.capacity_year=$year and a.section_id=$section and a.sub_section_id=$sub_section and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0"; 
	 
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach ($sql_result as $inf)
	{
		$key=date("Y-m-d",strtotime($inf[csf('capacity_date')]));
		$dataArr[$key]=array(
			capacity_date=>$inf[csf('capacity_date')],
			day_status=>$inf[csf('day_status')],
			daily_capacity_tk=>$inf[csf('daily_capacity_tk')],
			daily_capacity_usd=>$inf[csf('daily_capacity_usd')]
		);
	}
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN,$month,$year);

	for( $i = 1; $i <= $daysinmonth; $i++ ) 
	{  
	 $date=add_date($year.'-'.$month.'-'.$i);
	
		if($dataArr[$date][day_status]==2){$bgcolor="#F00";}else{$bgcolor="#000";}
	
	?>
        <tr id="days_tr_<? echo $i;?>" style="color:<? echo $bgcolor;?>">
            <td id="capacity_date_<? echo $i;?>" align="center"><? echo  change_date_format($date);  ?></td>
            <td align="center"><? echo  date('D',strtotime($date)); ?></td>
            <td align="center">
                <?
                    $day_status=array(1=>"Open",2=>"Closed");
                    echo create_drop_down( "cbo_day_status_".$i, 72,$day_status,"", 0, "-- Select --", $dataArr[$date][day_status],"open_close($month)","","" );
                ?>
            </td>
            <td>
                <input type="text" name="txt_day_capacity_tk_<? echo $i;?>" id="txt_day_capacity_tk_<? echo $i;?>" class="text_boxes_numeric" style="width:100px" value="<? echo $dataArr[$date][daily_capacity_tk];?>" readonly/>
            </td>
            <td>
                <input type="text" name="txt_day_capacity_usd_<? echo $i;?>" id="txt_day_capacity_usd_<? echo $i;?>" class="text_boxes_numeric" style="width:100px" value="<? echo $dataArr[$date][daily_capacity_usd];?>" readonly/>
            </td>
        </tr>        
	 <? 
	 } 	
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
		
		if(str_replace("'","",$update_id)==""){	
			$mst_id=return_next_id( "id", " trims_capacity_calculation_mst", 1 ) ; 
			$field_array_mst="id,company_id,location_id,section_id,sub_section_id,capacity_year,yearly_capacity_tk,yearly_capacity_usd,inserted_by,insert_date,status_active,is_deleted";
			$data_array_mst="(".$mst_id.",".$cbo_company_id.",".$cbo_location_id.",".$cbo_section.",".$cbo_sub_section.",".$cbo_year.",".$txt_year_capacity_in_tk.",".$txt_year_capacity_in_usd.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			
			//month detels..........................................................
			$dtls_id=return_next_id( "id", "trims_capacity_cal_dtls", 1 ); 
			$field_array_dtls="id,mst_id,month_id,working_day,monthly_capacity_tk,conversion_rate,monthly_capacity_usd,inserted_by,insert_date,status_active,is_deleted";
			$dtls_id_arr=array();
			for($i=1; $i<=12; $i++)
			{
				$update_id_dtls="update_id_dtls_".$i;
				$working_days="workingDays_".$i;
				$month_capacity_tk="txt_month_capacity_tk_".$i;
				$conversion_rate="txt_conversion_rate_".$i;
				$month_capacity_usd="txt_month_capacity_usd_".$i;
				$month=$i;
				
				if(str_replace("'",'',$$update_id_dtls)=="")
				{
					$dtls_id_arr[$month]=$dtls_id;
					if ($i!=1) $data_array_dtls .=",";
					$data_array_dtls.="(".$dtls_id.",".$mst_id.",".$month.",".$$working_days.",".$$month_capacity_tk.",".$$conversion_rate.",".$$month_capacity_usd.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$dtls_id=$dtls_id+1;
				}
			}
	
	
			//day detels..........................................................	
			$day_dtls_id=return_next_id( "id", "trims_capacity_cal_day_dtls", 1 ); 
			$field_array_day="id,mst_id,dtls_id,month_id,capacity_date,day_status,daily_capacity_tk,daily_capacity_usd,inserted_by,insert_date,status_active,is_deleted";
			
			for($i=1; $i<=$tot_day_row; $i++)
			{
			
				$capacity_date="capacity_date_".$i;
				$day_status="cbo_day_status_".$i;
				$day_capacity_tk="txt_day_capacity_tk_".$i;
				$day_capacity_usd="txt_day_capacity_usd_".$i;
				$selected_month_id=str_replace("'",'',$selected_month_id);
				
				if(str_replace("'",'',$$update_id_dtls)=="")
				{
					
					if($db_type==0) 
					{
						$capacity_date=change_date_format($$capacity_date,'yyyy-mm-dd');
					}
					else if($db_type==2) 
					{
						$capacity_date=change_date_format($$capacity_date,'','',1);
					}
					
					if ($i!=1) $data_array_day .=",";
					$data_array_day.="(".$day_dtls_id.",".$mst_id.",".$dtls_id_arr[$selected_month_id].",".$selected_month_id.",'".$capacity_date."',".$$day_status.",".$$day_capacity_tk.",".$$day_capacity_usd.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$day_dtls_id=$day_dtls_id+1;
				}
			
			}
				
			$rID=sql_insert("trims_capacity_calculation_mst",$field_array_mst,$data_array_mst,0);	
			$rID1=sql_insert("trims_capacity_cal_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID2=sql_insert("trims_capacity_cal_day_dtls",$field_array_day,$data_array_day,1);
			
			//echo "10**x".$data_array_mst.'**'.$rID1.'**'.$rID2;oci_rollback($con); die;
			
		}
		 
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
		
		
		
		$field_array_mst="company_id*location_id*section_id*sub_section_id*capacity_year*yearly_capacity_tk*yearly_capacity_usd*updated_by*update_date*status_active*is_deleted";
		$data_array_mst="".$cbo_company_id."*".$cbo_location_id."*".$cbo_section."*".$cbo_sub_section."*".$cbo_year."*".$txt_year_capacity_in_tk."*".$txt_year_capacity_in_usd."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		
		
		//month...............
		$field_array_dtls_up="working_day*monthly_capacity_tk*conversion_rate*monthly_capacity_usd*updated_by*update_date*status_active*is_deleted";
		for($i=1; $i<=12; $i++)
		{
			$update_id_dtls="update_id_dtls_".$i;
			$working_days="workingDays_".$i;
			$month_capacity_tk="txt_month_capacity_tk_".$i;
			$conversion_rate="txt_conversion_rate_".$i;
			$month_capacity_usd="txt_month_capacity_usd_".$i;
			$month=$i;
			$dtls_id_arr[$month]=str_replace("'",'',$$update_id_dtls);
			
			if(str_replace("'",'',$$update_id_dtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_dtls_up[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$working_days.",".$$month_capacity_tk.",".$$conversion_rate.",".$$month_capacity_usd.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0"));
				
			}
		
		}
	
	  //echo "10**".$month_update_sql;oci_rollback($con);die;
		
		//day detels..........................................................	
			$day_dtls_id=return_next_id( "id", "trims_capacity_cal_day_dtls", 1 ); 
			$field_array_day="id,mst_id,dtls_id,month_id,capacity_date,day_status,daily_capacity_tk,daily_capacity_usd,inserted_by,insert_date,status_active,is_deleted";
			
			for($i=1; $i<=$tot_day_row; $i++)
			{
			
				$capacity_date="capacity_date_".$i;
				$day_status="cbo_day_status_".$i;
				$day_capacity_tk="txt_day_capacity_tk_".$i;
				$day_capacity_usd="txt_day_capacity_usd_".$i;
				$selected_month_id=str_replace("'",'',$selected_month_id);
				
				
				if($db_type==0) 
				{
					$capacity_date=change_date_format($$capacity_date,'yyyy-mm-dd');
				}
				else if($db_type==2) 
				{
					$capacity_date=change_date_format($$capacity_date,'','',1);
				}
				
				if ($i!=1) $data_array_day .=",";
				$data_array_day.="(".$day_dtls_id.",".$update_id.",".$dtls_id_arr[$selected_month_id].",".$selected_month_id.",'".$capacity_date."',".$$day_status.",".$$day_capacity_tk.",".$$day_capacity_usd.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$day_dtls_id=$day_dtls_id+1;
			
			}

		
		
		if($data_array_day){
			$field_array_day_update="updated_by*update_date*status_active*is_deleted";
			$data_array_day_update="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID3=sql_update("trims_capacity_cal_day_dtls",$field_array_day_update,$data_array_day_update,"mst_id*month_id",$update_id.'*'.$selected_month_id,0);
		}
		
		
		$rID=sql_update("trims_capacity_calculation_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		$rID1=execute_query(bulk_update_sql_statement( "trims_capacity_cal_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $id_arr ),0);
		$rID2=sql_insert("trims_capacity_cal_day_dtls",$field_array_day,$data_array_day,1);
		
		
		
		
		//echo"10**".str_replace("'",'',$update_id).'**'.$rID.'**'.$rID1.'*'.$rID2.'*'.$rID3  .'=='. $update_id; oci_rollback($con);die;
		
		
				
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


if ($action=="load_php_dtls_form_update_val")
{
	list($year,$comapny,$location,$section,$sub_section)=explode('_',$data);
	$sql="select a.id,b.id as dtls_id,a.yearly_capacity_tk,a.yearly_capacity_usd,b.month_id,b.conversion_rate,b.working_day,b.monthly_capacity_tk,b.monthly_capacity_usd from trims_capacity_calculation_mst a, trims_capacity_cal_dtls b where a.id=b.mst_id and a.company_id=$comapny and a.location_id=$location and a.capacity_year=$year and a.section_id=$section and a.sub_section_id=$sub_section and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	 
	$sql_result=sql_select($sql);
	foreach ($sql_result as $inf)
	{
		if($inf[csf("month_id")]==1){
			echo "document.getElementById('update_id').value	= '".$inf[csf("id")]."';\n";
			echo "document.getElementById('txt_year_capacity_in_tk').value	= '".$inf[csf("yearly_capacity_tk")]."';\n";
			echo "document.getElementById('txt_year_capacity_in_usd').value	= '".$inf[csf("yearly_capacity_usd")]."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
		}

		
		echo "document.getElementById('update_id_dtls_".$inf[csf("month_id")]."').value	= '".$inf[csf("dtls_id")]."';\n";
		echo "document.getElementById('txt_month_capacity_tk_".$inf[csf("month_id")]."').value	= '".$inf[csf("monthly_capacity_tk")]."';\n";
		echo "document.getElementById('txt_conversion_rate_".$inf[csf("month_id")]."').value	= '".$inf[csf("conversion_rate")]."';\n";
		echo "document.getElementById('txt_month_capacity_usd_".$inf[csf("month_id")]."').value	= '".$inf[csf("monthly_capacity_usd")]."';\n";
		echo "document.getElementById('workingDays_".$inf[csf("month_id")]."').innerHTML = '".$inf[csf("working_day")]."';\n";
		
	}
	
	exit;
}













/*
if ($action=="load_php_dtls_form_update")
{
	$data=explode('_',$data);

	//$sql_res=sql_select("select a.id,a.comapny_id,a.capacity_source,a.year,a.location_id,a.avg_machine_line,a.basic_smv,a.effi_percent,c.id as year_id,c.month_id,c.working_day,c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b,lib_capacity_year_dtls c where a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.capacity_source=$data[3] and  a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id group by c.month_id");
	
	
	$sql_res=sql_select("select a.id, a.comapny_id, a.capacity_source, a.year, a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent, a.prod_category_id, c.id as year_id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where  a.id=c.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.capacity_source=$data[3] and a.prod_category_id='$data[4]' and  a.status_active=1 and a.is_deleted=0 group by a.id, a.comapny_id, a.capacity_source, a.year, a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent, a.prod_category_id, c.id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs order by c.month_id");

	$i=0;
	$month_count=count($sql_res);
	foreach ($sql_res as $row)
	{
		$i++;
		
		$working_day=$row[csf('working_day')];
		$working_day_sum += $working_day;

		$capacity_month_min=$row[csf('capacity_month_min')];
		$capacity_month_min_sum += $capacity_month_min;

		$capacity_month_pcs=$row[csf('capacity_month_pcs')];
		$capacity_month_pcs_sum += $capacity_month_pcs;
		
		echo "document.getElementById('txt_sl_no_".$i."').value 			= '".$i."';\n";
		echo "document.getElementById('txt_month_".$i."').value 			= '".$months[$row[csf("month_id")]]."';\n";
		echo "document.getElementById('txt_month_id_".$i."').value 			= '".$row[csf("month_id")]."';\n";
		echo "document.getElementById('txt_working_day_".$i."').value 			= '".$row[csf("working_day")]."';\n";
		echo "document.getElementById('txt_year_capacity_min_".$i."').value 	= '".$row[csf("capacity_month_min")]."';\n";
		echo "document.getElementById('txt_year_capacity_pcs_".$i."').value 	= '".$row[csf("capacity_month_pcs")]."';\n";
		echo "document.getElementById('update_id_year_dtls_".$i."').value 		= '".$row[csf("year_id")]."';\n";
		if ($i==$month_count)
		{
			echo "document.getElementById('txt_avg_mch_line').value 			= '".$row[csf("avg_machine_line")]."';\n";
			echo "document.getElementById('txt_basic_smv').value 				= '".$row[csf("basic_smv")]."';\n";
			echo "document.getElementById('txt_efficiency_per').value 			= '".$row[csf("effi_percent")]."';\n";
			echo "document.getElementById('cbo_product_category').value 		= '".$row[csf("prod_category_id")]."';\n";
			echo "document.getElementById('txt_working_day_total').value 		= '".$working_day_sum."';\n";
			echo "document.getElementById('txt_capacity_min_total').value 		= '".$capacity_month_min_sum."';\n";
			echo "document.getElementById('txt_capacity_pcs_total').value 		= '".$capacity_month_pcs_sum."';\n";
			echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
			if ($row[csf("id")]!="")
			{
				echo "disable_enable_fields( 'cbo_company_id*cbo_capacity_source*cbo_location_id*cbo_year*txt_avg_mch_line*txt_basic_smv*cbo_product_category*txt_efficiency_per', 1, '', '');\n";
			}
		}
		
	}
	exit;
}


if($action=="load_php_dtls_form_return_id_date")
{
	$qry_result=sql_select( "select id,mst_id from lib_capacity_calc_dtls where mst_id='$data' order by id ASC");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		
	}
	echo $id;
}

if($action=="load_php_dtls_form_return_id_year")
{
	$qry_res=sql_select( "select id,mst_id from lib_capacity_year_dtls where mst_id='$data' order by id ASC");
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
*/


?>
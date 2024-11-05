<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

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
	$c_date="$data[0]-$data[1]-01";
	$k=0; $kk=1; 
	for( $i = 1; $i <= $daysinmonth; $i++ ) 
	{  
		$s_date=change_date_format(add_date($c_date, $k));
		$s_date=explode("-", $s_date);
		$f_date="$s_date[2]-$s_date[1]-$s_date[0]";
 		$parts = explode("-", $f_date);
		$dayForDate = date("l", mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]));
	 ?>
		<tr align="center">
			<td>
            	<input type="hidden" id="update_id_dtls_<? echo $i; ?>" name="update_id_dtls_<? echo $i; ?>" value="" />
				<input type="text" name="txt_date_<? echo $i; ?>" id="txt_date_<? echo $i; ?>" class="datepicker" style="width:77px" value="<? echo  change_date_format(add_date($c_date, $k));  ?>" readonly />
			</td>
			<td>
                <input type="text" name="txt_day_name_<? echo $i; ?>" id="txt_day_name_<? echo $i; ?>" class="text_boxes" style="width:67px;" readonly value="<? echo $dayForDate; ?>"/>
             </td>


			<td>
				<?
					$day_status=array(1=>"Open",2=>"Closed");
					echo create_drop_down( "cbo_day_status_$i", 80,$day_status,"", 0, "-- Select --", 1,"open_close(this.value,$i,$kk)" );
				?>
			</td>
 
			<td>
				<input type="text" name="txt_machine_manpower_<? echo $i; ?>" id="txt_machine_manpower_<? echo $i; ?>" class="text_boxes_numeric" style="width:107px" onblur="calculate_capacity_min_pcs(this.value,<? echo $i; ?>);" />
			</td>
			<td>
				<input type="text" name="txt_capacity_min_<? echo $i; ?>" id="txt_capacity_min_<? echo $i; ?>" class="text_boxes_numeric" readonly="readonly" />
			</td>
			<td>
				<input type="text" name="txt_capacity_pcs_<? echo $i; ?>" id="txt_capacity_pcs_<? echo $i; ?>" class="text_boxes_numeric"  readonly="readonly" />
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
 	$sql_result=sql_select("select a.id,b.id as bid,b.month_id,b.date_calc,b.day_status,b.machine_manpow,b.capacity_min,b.capacity_pcs from sample_capacity_calc_mst a,sample_capacity_calc_dtls b where a.company_id=$data[0] and a.location_id=$data[1] and a.year=$data[2]  and b.month_id=$data[3] and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id");
	$i=0;
	$data_count=count($sql_result);
	foreach ($sql_result as $inf)
	{
		$capacity_min=$inf[csf('capacity_min')];
		$capacity_min_sum += $capacity_min;
 
		$i++;
	if ($i==$data_count)
		{
			echo "document.getElementById('total_min').value 							= '".$capacity_min_sum."';\n";
 			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
		}
		echo "document.getElementById('cbo_day_status_".$i."').value 	= '".$inf[csf("day_status")]."';\n";
		echo "document.getElementById('txt_machine_manpower_".$i."').value 	= '".$inf[csf("machine_manpow")]."';\n";
		echo "document.getElementById('line_id').value					= '".$inf[csf("machine_manpow")]."';\n";
		echo "document.getElementById('txt_capacity_min_".$i."').value 	= '".$inf[csf("capacity_min")]."';\n";
		echo "document.getElementById('txt_capacity_pcs_".$i."').value 	= '".$inf[csf("capacity_pcs")]."';\n";
 		echo "document.getElementById('update_id_dtls_".$i."').value 	= '".$inf[csf("bid")]."';\n";
		
		if ($inf[csf("day_status")]==2)
		{
			echo "document.getElementById('txt_capacity_min_".$i."').value 			= '';\n";
 			echo "disable_enable_fields( 'txt_capacity_min_".$i."', 1, '', '');\n";
		}
	}
	exit;
}

if ($action=="load_php_dtls_form_update")
{
	$data=explode('_',$data);

 	$sql_res=sql_select("select a.id,a.company_id,a.year,a.location_id,a.effi_percent,a.working_hours,c.id as year_id,c.month_id,c.working_day,
c.capacity_month_min,a.basic_smv from sample_capacity_calc_mst a,sample_capacity_calc_dtls b,sample_capacity_year_dtls c where a.company_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id group by
  a.id,a.company_id,a.working_hours,a.year,a.location_id,a.effi_percent,c.id,c.month_id,c.working_day,c.capacity_month_min,a.basic_smv order by c.month_id");

	$i=0;
 	$month_count=count($sql_res);
 	foreach ($sql_res as $row)
	{
		$working_day=$row[csf('working_day')];
		$working_day_sum += $working_day;

		$capacity_month_min=$row[csf('capacity_month_min')];
		$capacity_month_min_sum += $capacity_month_min;
 		
		$i++;

 		if ($i==$month_count)
		{
 			echo "document.getElementById('txt_efficiency_per').value 						= '".$row[csf("effi_percent")]."';\n";
			 echo "document.getElementById('txt_basic_smv').value 						= '".$row[csf("basic_smv")]."';\n";
			echo "document.getElementById('txt_working_hours').value 						= '".$row[csf("working_hours")]."';\n";

			echo "document.getElementById('txt_working_day_total').value 							= '".$working_day_sum."';\n";
			echo "document.getElementById('txt_capacity_min_total').value 							= '".$capacity_month_min_sum."';\n";
 			echo "document.getElementById('update_id').value 							= '".$row[csf("id")]."';\n";
			if ($row[csf("id")]!="")
			{
				echo "disable_enable_fields( 'cbo_company_id*cbo_location_id*cbo_year', 1, '', '');\n";
			}
		}
		echo "document.getElementById('txt_sl_no_".$i."').value 			= '".$i."';\n";
		echo "document.getElementById('txt_month_".$i."').value 			= '".$months[$row[csf("month_id")]]."';\n";
		echo "document.getElementById('txt_month_id_".$i."').value 			= '".$row[csf("month_id")]."';\n";
		echo "document.getElementById('txt_working_day_".$i."').value 			= '".$row[csf("working_day")]."';\n";
		echo "document.getElementById('txt_year_capacity_min_".$i."').value 	= '".$row[csf("capacity_month_min")]."';\n";
 		echo "document.getElementById('update_id_year_dtls_".$i."').value 		= '".$row[csf("year_id")]."';\n";
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
			$mst_id=return_next_id( "id", "sample_capacity_calc_mst", 1 ) ; 
			$field_array_mst="id,company_id,year,location_id,effi_percent,working_hours,basic_smv,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array_mst="(".$mst_id.",".$cbo_company_id.",".$cbo_year.",".$cbo_location_id.",".$txt_efficiency_per.",".$txt_working_hours.",".$txt_basic_smv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		 //echo "insert into sample_capacity_calc_mst (".$field_array_mst.") values ".$data_array_mst;die;
			//$rID=sql_insert("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,0);
		}
		else
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_mst="year*location_id*effi_percent*basic_smv*updated_by*update_date*status_active*is_deleted";
			
			$data_array_mst="".$cbo_year."*".$cbo_location_id."*".$txt_efficiency_per."*".$txt_basic_smv."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update("sample_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		
		$dtls_id=return_next_id( "id", "sample_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,date_calc,day_name,day_status,machine_manpow,capacity_min,capacity_pcs";
			for($i=1; $i<=$tot_row_date; $i++)
			{
				$txt_date= "txt_date_".$i;
				$txt_day_name="txt_day_name_".$i;
				$cbo_day_status="cbo_day_status_".$i;
 				$txt_machine_manpower="txt_machine_manpower_".$i;
				$txt_capacity_min="txt_capacity_min_".$i;
				$txt_capacity_pcs="txt_capacity_pcs_".$i;
				
				$update_id_dtls="update_id_dtls_".$i;

				if(str_replace("'",'',$$update_id_dtls)=="")
				{
					if ($i!=1) $data_array_dtls .=",";
					$data_array_dtls.="(".$dtls_id.",".$mst_id.",".$cbo_month.",".$$txt_date.",".$$txt_day_name.",".$$cbo_day_status.",".$$txt_machine_manpower.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
					$dtls_id=$dtls_id+1;
				}
			}
			//echo "0**"."insert into lib_capacity_calc_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;	 
			//$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
			
		$dtls_year_id=return_next_id( "id", "sample_capacity_year_dtls", 1 ); 
		$field_array_year="id,mst_id,month_id,working_day,capacity_month_min";
		
			for($i=1; $i<=$tot_row_year; $i++)
			{
				$txt_month= "txt_month_id_".$i;
				$txt_working_day="txt_working_day_".$i;
				$txt_year_capacity_min="txt_year_capacity_min_".$i;
 				$update_id_year_dtls="update_id_year_dtls_".$i;

				if(str_replace("'",'',$$update_id_year_dtls)=="")
				{
					if ($i!=1) $data_array_year .=",";
					$data_array_year.="(".$dtls_year_id.",".$mst_id.",".$$txt_month.",".$$txt_working_day.",".$$txt_year_capacity_min.")";
					$dtls_year_id=$dtls_year_id+1;
				}
				else
				{
					$id_arr_year=array();
					$data_array_year=array();
					$field_array_year="working_day*capacity_month_min";
					for($i=1; $i<=12; $i++)
					{
						$txt_month= "txt_month_id_".$i;
						$txt_working_day="txt_working_day_".$i;
						$txt_year_capacity_min="txt_year_capacity_min_".$i;
 						$update_id_year_dtls="update_id_year_dtls_".$i;
						
						if(str_replace("'",'',$$update_id_year_dtls)!="")
						{
							$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
							$data_array_year[str_replace("'",'',$$update_id_year_dtls)] =explode(",",("".$$txt_working_day.",".$$txt_year_capacity_min.""));
						}
					}
					//echo bulk_update_sql_statement( 'lib_capacity_year_dtls', 'id', $field_array_year, $data_array_year, $id_arr_year ); die;	 
					//$rID2=execute_query(bulk_update_sql_statement( "lib_capacity_year_dtls", "id", $field_array_year, $data_array_year, $id_arr_year ),1);
				}
			}
			//echo "0**"."insert into lib_capacity_year_dtls (".$field_array_year.") values ".$data_array_year; die;
		 if(str_replace("'",'',$update_id)=="")
			{
		    	$rID=sql_insert("sample_capacity_calc_mst",$field_array_mst,$data_array_mst,0);	
			}
		else
			{
				$rID=sql_update("sample_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
			}
		$rID1=sql_insert("sample_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		if(str_replace("'",'',$$update_id_year_dtls)=="")
			{
				$rID2=sql_insert("sample_capacity_year_dtls",$field_array_year,$data_array_year,1);
			}
		else
			{
				$rID2=execute_query(bulk_update_sql_statement( "sample_capacity_year_dtls", "id", $field_array_year, $data_array_year, $id_arr_year ),1);

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
		

		$field_array_mst="year*location_id*effi_percent*basic_smv*updated_by*update_date*status_active*is_deleted";
		
		$data_array_mst="".$cbo_year."*".$cbo_location_id."*".$txt_efficiency_per."*".$txt_basic_smv."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		
		//$rID=sql_update("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);

		$id_arr=array();
		$data_array_dtls_up=array();
		$field_array_dtls_up="day_status*day_name*machine_manpow*capacity_min*capacity_pcs";
		
		$dtls_id=return_next_id( "id", "sample_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,date_calc,day_name,day_status,machine_manpow,capacity_min,capacity_pcs";
		
		for($i=1; $i<=$tot_row_date; $i++)
		{
			$txt_date= "txt_date_".$i;
			$cbo_day_status="cbo_day_status_".$i;
			$txt_day_name="txt_day_name_".$i;
			$txt_machine_manpower="txt_machine_manpower_".$i;
			$txt_capacity_min="txt_capacity_min_".$i;
			$txt_capacity_pcs="txt_capacity_pcs_".$i;
 			$update_id_dtls="update_id_dtls_".$i;
			//print_r ($$update_id_dtls);die;
			if(str_replace("'",'',$$update_id_dtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_dtls_up[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$cbo_day_status.",".$$txt_day_name.",".$$txt_machine_manpower.",".$$txt_capacity_min.",".$$txt_capacity_pcs.""));
			}
			else 
			{
				if ($i!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$cbo_month.",".$$txt_date.",".$$txt_day_name.",".$$cbo_day_status.",".$$txt_machine_manpower.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
				$dtls_id=$dtls_id+1;
			}
		}
	/*	$rID1=execute_query(bulk_update_sql_statement( "lib_capacity_calc_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $id_arr ),0);
		
		if(str_replace("'",'',$data_array_dtls)!="")
		{
			$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		}*/

		$id_arr_year=array();
		$data_array_year=array();
		$field_array_year="working_day*capacity_month_min";
		for($i=1; $i<=12; $i++)
		{
			$txt_month= "txt_month_id_".$i;
			$txt_working_day="txt_working_day_".$i;
			$txt_year_capacity_min="txt_year_capacity_min_".$i;
			$update_id_year_dtls="update_id_year_dtls_".$i;
			
			if(str_replace("'",'',$$update_id_year_dtls)!="")
			{
				$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
				$data_array_year[str_replace("'",'',$$update_id_year_dtls)] =explode(",",("".$$txt_working_day.",".$$txt_year_capacity_min.""));
			}
		}
		//echo bulk_update_sql_statement( 'lib_capacity_year_dtls', 'id', $field_array_year, $data_array_year, $id_arr_year ); die;	 
		$rID=sql_update("sample_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		$rID1=execute_query(bulk_update_sql_statement( "sample_capacity_calc_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $id_arr ),0);
		
		if(str_replace("'",'',$data_array_dtls)!="")
		{
			$rID1=sql_insert("sample_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		$rID2=execute_query(bulk_update_sql_statement( "sample_capacity_year_dtls", "id", $field_array_year, $data_array_year, $id_arr_year ),1);
				
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
	list($mst_id,$month)=explode("**",$data);
	$qry_result=sql_select( "select id,mst_id from sample_capacity_calc_dtls where mst_id='$mst_id' and month_id=$month");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		
	}
	echo $id;
}

if($action=="load_php_dtls_form_return_id_year")
{
	list($mst_id,$month)=explode("**",$data);
	$qry_res=sql_select( "select id,mst_id from sample_capacity_year_dtls where mst_id='$mst_id' and month_id=$month");
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
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$comp=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
	$arr=array (0=>$comp,6=>$row_status);
	echo  create_list_view ( "list_view", "Company Name,Period From,Period To,BEP CM %,Asking Profit %,Asking CM %,Status", "150,80,80,80,80,80,60","700","220",0, "select  company_id,applying_period_date,applying_period_to_date,bep_cm,asking_profit,asking_cm,status_active,id from  lib_standard_cm_entry where is_deleted=0 order by id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,0,0,0,0,0,status_active", $arr , "company_id,applying_period_date,applying_period_to_date,bep_cm,asking_profit,asking_cm,status_active", "../merchandising_details/requires/standard_cm_entry_controller", 'setFilterGrid("list_view",-1);','0,3,3,2,2,2,0' ) ;
	
}
if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "" );
	exit();
}
if ($action=="check_capacity_calculation")
{
	$data_date=explode("_",$data);
	$company_id=$data_date[0];
	$cal_dates=$data_date[1];
	$location_id=$data_date[2];
	if($location_id>0) $location_cond="and a.location_id=$location_id ";else $location_cond="";
	/*if($db_type==0)
	{
		$start_date=change_date_format($cal_dates,'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$end_date=change_date_format($cal_dates,'','-',1);
    }
	$date_cond="and b.date_calc between '$start_date' and  '$end_date'";*/
	$year = date('Y',strtotime($cal_dates));
	 $month_id = date('m',strtotime($cal_dates));
	 $monthid=ltrim($month_id,'0');
	// echo $monthid.'dd'.$year;
	 	//echo $month_id.'dd';;
	$sql="select min(c.working_day) as working_day, min(b.date_calc) as date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($company_id) and a.year=$year and  c.month_id=$monthid  $location_cond ";
	 //echo $sql;
	$sql_data_calc=sql_select($sql);
	if(count($sql_data_calc)>0)
	{
		$working_day=$sql_data_calc[0][csf('working_day')];
		echo $working_day;
	}
	else
	{
	 	echo '';
	}
	
	
	
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select company_id,location_id,applying_period_date,applying_period_to_date,bep_cm,asking_profit,asking_cm,monthly_cm_expense,no_factory_machine,working_hour, status_active,	cost_per_minute,asking_avg_rate,id,actual_cm,max_profit,depreciation_amorti,interest_expense,income_tax,operating_expn from  lib_standard_cm_entry where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";   
		echo "load_drop_down('requires/standard_cm_entry_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location_td' );\n"; 
		echo "document.getElementById('cbo_location_id').value = '".($inf[csf("location_id")])."';\n";   
		echo "document.getElementById('txt_applying_period_date').value  = '".change_date_format(($inf[csf("applying_period_date")]),'dd-mm-yyyy','-')."';\n"; 
		echo "document.getElementById('txt_applying_period_to_date').value  = '".change_date_format(($inf[csf("applying_period_to_date")]),'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_bep_cm').value  = '".($inf[csf("bep_cm")])."';\n"; 
		echo "document.getElementById('txt_asking_cm').value  = '".($inf[csf("asking_cm")])."';\n"; 
		echo "document.getElementById('txt_asking_profit').value  = '".($inf[csf("asking_profit")])."';\n"; 

		echo "document.getElementById('txt_monthly_cm').value  = '".($inf[csf("monthly_cm_expense")])."';\n"; 
		echo "document.getElementById('txt_number_machine').value  = '".($inf[csf("no_factory_machine")])."';\n"; 
		echo "document.getElementById('txt_working_hour').value  = '".($inf[csf("working_hour")])."';\n"; 
		
		echo "document.getElementById('txt_asking_avg_rate').value  = '".($inf[csf("asking_avg_rate")])."';\n";
		
		echo "document.getElementById('txt_actual_cm').value  = '".($inf[csf("actual_cm")])."';\n";
		echo "document.getElementById('txt_max_profit').value  = '".($inf[csf("max_profit")])."';\n";
		
		echo "calculate_date()\n";
		echo "caculate_cost_per_minute()\n";
		echo "document.getElementById('txt_cost_per_minute').value  = '".($inf[csf("cost_per_minute")])."';\n"; 
		echo "document.getElementById('txt_depr_amort').value  = '".($inf[csf("depreciation_amorti")])."';\n";
		echo "document.getElementById('txt_interest_expn').value  = '".($inf[csf("interest_expense")])."';\n";
		echo "document.getElementById('txt_income_tax').value  = '".($inf[csf("income_tax")])."';\n";
		echo "document.getElementById('txt_operating_expn').value  = '".($inf[csf("operating_expn")])."';\n";
		
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_standard_cm',1);\n";  

	}
}

if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_applying_period_date=str_replace("'","",$txt_applying_period_date);
	$txt_applying_period_to_date=str_replace("'","",$txt_applying_period_to_date);
	$location_id=str_replace("'","",$cbo_location_id);
	if($db_type==0)
	{
		$txt_applying_period_date=change_date_format($txt_applying_period_date,"yyyy-mm-dd","-");
		$txt_applying_period_to_date=change_date_format($txt_applying_period_to_date,"yyyy-mm-dd","-");
	}
	else
	{
		$txt_applying_period_date=change_date_format($txt_applying_period_date, "d-M-y", "-",1);
		$txt_applying_period_to_date=change_date_format($txt_applying_period_to_date, "d-M-y", "-",1);
	}
	if($location_id>0) $location_cond="and location_id=$location_id";else $location_cond="";
	if ($operation==0)  // Insert Here	
	{
		
		if (is_duplicate_field( "id", "lib_standard_cm_entry", "company_id=$cbo_company_name and applying_period_date='$txt_applying_period_date' and status_active=1 and is_deleted=0 $location_cond" ) == 1)
		{
			echo "11**0"; die;
		}
        else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_standard_cm_entry", 1 ) ; //txt_max_profit 	status_active,is_deleted,inserted_by,insert_date,updated_by,update_date,is_locked
			$field_array="id,company_id,location_id,applying_period_date,applying_period_to_date,bep_cm,asking_cm,asking_profit,monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,asking_avg_rate,status_active,is_deleted,inserted_by,insert_date,is_locked,actual_cm,max_profit,depreciation_amorti,interest_expense,income_tax,operating_expn";
			$data_array="(".$id.",".trim($cbo_company_name).",".trim($cbo_location_id).",'".trim($txt_applying_period_date)."','".trim($txt_applying_period_to_date)."',".trim($txt_bep_cm).",".trim($txt_asking_cm).",".trim($txt_asking_profit).",".trim($txt_monthly_cm).",".trim($txt_number_machine).",".trim($txt_working_hour).",".trim($txt_cost_per_minute).",".trim($txt_asking_avg_rate).",".trim($cbo_status).",'0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',".trim($txt_actual_cm).",".trim($txt_max_profit).",".trim($txt_depr_amort).",".trim($txt_interest_expn).",".trim($txt_income_tax).",".trim($txt_operating_expn).")";
			$rID=sql_insert("lib_standard_cm_entry",$field_array,$data_array,1);
			// echo "11**0".$rID; die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
			if($rID )
			    {
					oci_commit($con);   
					echo "0**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	else if ($operation==1)   // Update Here
	{
		$duplicate_id=return_field_value("id","lib_standard_cm_entry","company_id=$cbo_company_name and applying_period_date='$txt_applying_period_date' and status_active=1 and is_deleted=0 $location_cond");
		
		if($duplicate_id=="")  // Duplicate
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_standard_cm_entry", 1 ) ; // 	status_active,is_deleted,inserted_by,insert_date,updated_by,update_date,is_locked
			$field_array="id,company_id,location_id,applying_period_date,applying_period_to_date,bep_cm,asking_cm,asking_profit,monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,asking_avg_rate,status_active,is_deleted,inserted_by,insert_date,is_locked,actual_cm,max_profit,depreciation_amorti,interest_expense,income_tax,operating_expn";
			$data_array="(".$id.",".trim($cbo_company_name).",".trim($cbo_location_id).",'".trim($txt_applying_period_date)."','".trim($txt_applying_period_to_date)."',".trim($txt_bep_cm).",".trim($txt_asking_cm).",".trim($txt_asking_profit).",".trim($txt_monthly_cm).",".trim($txt_number_machine).",".trim($txt_working_hour).",".trim($txt_cost_per_minute).",".trim($txt_asking_avg_rate).",".trim($cbo_status).",'0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',".trim($txt_actual_cm).",".trim($txt_max_profit).",".trim($txt_depr_amort).",".trim($txt_interest_expn).",".trim($txt_income_tax).",".trim($txt_operating_expn).")";
			$rID=sql_insert("lib_standard_cm_entry",$field_array,$data_array,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
			 if($rID )
			    {
					oci_commit($con);   
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
		else
		{
			$update_id=str_replace("'","",$update_id);
			
			if($duplicate_id!=$update_id)
			{
				echo "11**0"; die;
			}
			else
			{
				
				$con = connect();
			
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				
				//str_replace("'","",$wo_id)
			
				$field_array="company_id*location_id*applying_period_date*applying_period_to_date*bep_cm*asking_cm*asking_profit*monthly_cm_expense*no_factory_machine*working_hour*cost_per_minute*asking_avg_rate*status_active*updated_by*update_date*actual_cm*max_profit*depreciation_amorti*interest_expense*income_tax*operating_expn";
				$data_array="".$cbo_company_name."*".$cbo_location_id."*'".$txt_applying_period_date."'*'".$txt_applying_period_to_date."'*".$txt_bep_cm."*".$txt_asking_cm."*".$txt_asking_profit."*".trim($txt_monthly_cm)."*".trim($txt_number_machine)."*".trim($txt_working_hour)."*".trim($txt_cost_per_minute)."*".trim($txt_asking_avg_rate)."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".trim($txt_actual_cm)."*".trim($txt_max_profit)."*".trim($txt_depr_amort)."*".trim($txt_interest_expn)."*".trim($txt_income_tax)."*".trim($txt_operating_expn)."";
				
				 //echo "10**".$field_array.'_'.$data_array;die;
				
				$rID2=sql_update("lib_standard_cm_entry",$field_array,$data_array,"id","".$update_id."",1);
				//echo $rID; die;
				if($db_type==0)
				{
					if( $rID2 ){
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($rID2)
					{
					    oci_commit($con);  
					    echo "1**".$rID;
					}
				else{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
			
				disconnect($con);
			
			}
		}
		
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_standard_cm_entry",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		   if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
	}
}


?>